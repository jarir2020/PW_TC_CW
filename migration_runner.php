<?php
declare(strict_types=1);

/**
 * migration_runner.php
 *
 * Idempotent migration runner for PEW Training Center.
 * It is called after each deployment from deploy.sh or directly via HTTP / CLI.
 * Only unapplied migrations from database/migrations/ are executed.
 *
 * Usage:
 *   - Visit https://pewtc.com/migration_runner.php, OR
 *   - curl -k -X POST https://pewtc.com/migration_runner.php
 *   - CLI: php migration_runner.php [migrate|status|rollback]
 */

error_reporting(E_ALL);
ini_set('display_errors', PHP_SAPI === 'cli' ? '1' : '0');

$ROOT = __DIR__;

// Detect project root
$candidateRoots = [
    $ROOT,
    dirname($ROOT),
];

$projectRoot = null;
foreach ($candidateRoots as $c) {
    if ((is_file($c . '/wp-config.php') || is_file($c . '/wp-load.php')) && is_dir($c . '/database/migrations')) {
        $projectRoot = $c;
        break;
    }
}

if ($projectRoot === null) {
    http_response_code(500);
    die("❌ Could not find project root. Make sure wp-config.php and database/migrations exist.\n");
}

// Load .env
if (is_file($projectRoot . '/.env')) {
    foreach (file($projectRoot . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value, " \t\"'");
        if (getenv($key) === false || getenv($key) === '') {
            putenv("$key=$value");
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
        }
    }
}

// Optional Token Gate
$requiredToken = getenv('DEPLOY_TOKEN') ?: (getenv('MIGRATION_TOKEN') ?: '');
if ($requiredToken !== '' && PHP_SAPI !== 'cli') {
    $provided = $_GET['token'] ?? $_POST['token'] ?? ($_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? '');
    if (!hash_equals($requiredToken, (string) $provided)) {
        http_response_code(403);
        die("❌ Forbidden: Invalid or missing token.\n");
    }
}

// Resolve Database Configuration
$dbHost  = getenv('WP_DB_HOST') ?: (getenv('DB_HOST') ?: 'localhost');
$dbPort  = getenv('MYSQL_PORT') ?: (getenv('DB_PORT') ?: '3306');
$dbName  = getenv('MYSQL_DATABASE') ?: (getenv('DB_NAME') ?: 'pew_training_center');
$dbUser  = getenv('MYSQL_USER') ?: (getenv('DB_USER') ?: 'root');
$dbPass  = getenv('MYSQL_PASSWORD') ?: (getenv('DB_PASSWORD') ?: '');
$siteUrl = getenv('SITE_URL') ?: (getenv('WP_HOME') ?: (getenv('WP_SITEURL') ?: 'https://pewtc.com'));
$siteUrl = rtrim($siteUrl, '/');

if (str_contains($dbHost, ':')) {
    [$hostOnly, $portOnly] = explode(':', $dbHost, 2);
    $dbHost = $hostOnly;
    if ($portOnly) {
        $dbPort = $portOnly;
    }
}

// Establish PDO connection
try {
    if ($dbHost === 'localhost') {
        $dsn = "mysql:host=localhost;dbname={$dbName};charset=utf8mb4";
    } else {
        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
    }

    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
    ]);
} catch (\Throwable $e) {
    if (PHP_SAPI !== 'cli') {
        http_response_code(500);
    }
    die("❌ Database connection failed ({$dbHost}:{$dbPort}/{$dbName}): " . $e->getMessage() . "\n");
}

if (PHP_SAPI !== 'cli') {
    header('Content-Type: text/plain; charset=utf-8');
}

echo "============================================================\n";
echo "  PEW Training Center — Migration Runner\n";
echo "============================================================\n\n";
echo "Project root:  $projectRoot\n";
echo "Database:      $dbName\n";
echo "DB Host:       $dbHost\n";
echo "Site URL:      $siteUrl\n";
echo "Timestamp:     " . date('Y-m-d H:i:s T') . "\n\n";

$migrationsDir = $projectRoot . '/database/migrations';
if (!is_dir($migrationsDir)) {
    @mkdir($migrationsDir, 0755, true);
}

// Concurrency lock
$lockDirectory = $projectRoot . '/database';
$lockPath = $lockDirectory . '/migration_runner.lock';
$lockHandle = @fopen($lockPath, 'c');
if ($lockHandle === false || !flock($lockHandle, LOCK_EX)) {
    if (is_resource($lockHandle)) fclose($lockHandle);
    if (PHP_SAPI !== 'cli') {
        http_response_code(500);
    }
    die("❌ Could not acquire the migration lock.\n");
}

// Ensure migration tracking table exists
$pdo->exec("
    CREATE TABLE IF NOT EXISTS `pew_migrations` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `migration` VARCHAR(255) NOT NULL UNIQUE,
        `batch` INT NOT NULL,
        `applied_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

$requestedAction = $_GET['action'] ?? $_POST['action'] ?? ($argv[1] ?? 'migrate');
$allowedActions = ['status', 'migrate', 'rollback', 'seed'];
$action = in_array($requestedAction, $allowedActions, true) ? $requestedAction : 'migrate';

if (isset($_GET['rollback']) && $_GET['rollback'] === '1') {
    $action = 'rollback';
}

try {
    switch ($action) {
        case 'status':
            echo "ℹ Migration status:\n\n";
            $stmt = $pdo->query("SELECT migration FROM pew_migrations ORDER BY id ASC");
            $applied = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "Applied migrations: " . count($applied) . "\n";

            $files = scandir($migrationsDir) ?: [];
            $allMigrations = [];
            foreach ($files as $f) {
                if ($f === '.' || $f === '..') continue;
                if (str_ends_with($f, '.sql') || str_ends_with($f, '.php')) {
                    $allMigrations[] = $f;
                }
            }
            sort($allMigrations);

            $pending = 0;
            foreach ($allMigrations as $f) {
                if (!in_array($f, $applied, true)) {
                    $pending++;
                    echo "  ⏳ pending: $f\n";
                } else {
                    echo "  ✅ applied: $f\n";
                }
            }
            echo "\nPending: $pending\n";
            break;

        case 'migrate':
            echo "▶ Running pending migrations...\n\n";

            $stmt = $pdo->query("SELECT migration FROM pew_migrations");
            $applied = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $files = scandir($migrationsDir) ?: [];
            $pendingMigrations = [];
            foreach ($files as $f) {
                if ($f === '.' || $f === '..') continue;
                if ((str_ends_with($f, '.sql') || str_ends_with($f, '.php')) && !in_array($f, $applied, true)) {
                    $pendingMigrations[] = $f;
                }
            }
            sort($pendingMigrations);

            if (empty($pendingMigrations)) {
                echo "  ℹ Nothing to migrate. Database is up to date.\n";
            } else {
                $batchStmt = $pdo->query("SELECT COALESCE(MAX(batch), 0) + 1 FROM pew_migrations");
                $batch = (int) $batchStmt->fetchColumn();

                foreach ($pendingMigrations as $migrationFile) {
                    $fullPath = $migrationsDir . '/' . $migrationFile;
                    echo "  ⏳ Applying: $migrationFile... ";
                    $start = microtime(true);

                    if (str_ends_with($migrationFile, '.sql')) {
                        $sql = file_get_contents($fullPath);
                        if ($sql === false) {
                            throw new \RuntimeException("Could not read migration file: $migrationFile");
                        }

                        // Replace local dev hostnames / URLs with target $siteUrl
                        $sql = str_replace(
                            ['http://127.0.0.1:8090', 'http://localhost:8090', '{{SITE_URL}}'],
                            [$siteUrl, $siteUrl, $siteUrl],
                            $sql
                        );

                        $pdo->exec($sql);
                    } elseif (str_ends_with($migrationFile, '.php')) {
                        $migrationResult = require $fullPath;
                        if (is_callable($migrationResult)) {
                            $migrationResult($pdo, $projectRoot, $siteUrl);
                        } elseif (is_object($migrationResult) && method_exists($migrationResult, 'up')) {
                            $migrationResult->up($pdo, $projectRoot, $siteUrl);
                        }
                    }

                    $recordStmt = $pdo->prepare("INSERT INTO pew_migrations (migration, batch) VALUES (?, ?)");
                    $recordStmt->execute([$migrationFile, $batch]);

                    $duration = round(microtime(true) - $start, 3);
                    echo "Done ({$duration}s)\n";
                }
            }

            // Post-migration synchronization: Ensure WordPress options point to the target URL
            $hasOptions = $pdo->query("SHOW TABLES LIKE 'pew_options'")->fetch();
            if ($hasOptions) {
                $syncOpt = $pdo->prepare("UPDATE pew_options SET option_value = ? WHERE option_name IN ('siteurl', 'home')");
                $syncOpt->execute([$siteUrl]);

                // Ensure theme is set to dist-faithful
                $themeOpt = $pdo->prepare("UPDATE pew_options SET option_value = 'dist-faithful' WHERE option_name IN ('template', 'stylesheet')");
                $themeOpt->execute();
            }

            // Ensure posts URLs point to target site URL
            $hasPosts = $pdo->query("SHOW TABLES LIKE 'pew_posts'")->fetch();
            if ($hasPosts) {
                $syncPosts = $pdo->prepare("
                    UPDATE pew_posts 
                    SET guid = REPLACE(guid, 'http://127.0.0.1:8090', ?),
                        post_content = REPLACE(post_content, 'http://127.0.0.1:8090', ?)
                    WHERE guid LIKE '%127.0.0.1:8090%' OR post_content LIKE '%127.0.0.1:8090%'
                ");
                $syncPosts->execute([$siteUrl, $siteUrl]);
            }

            echo "\n✅ Migrations applied successfully.\n";
            break;

        case 'rollback':
            echo "◀ Rolling back last batch...\n\n";

            $lastBatchStmt = $pdo->query("SELECT MAX(batch) FROM pew_migrations");
            $lastBatch = $lastBatchStmt->fetchColumn();

            if (!$lastBatch) {
                echo "  ℹ No migrations to roll back.\n";
                break;
            }

            $stmt = $pdo->prepare("SELECT migration FROM pew_migrations WHERE batch = ? ORDER BY id DESC");
            $stmt->execute([$lastBatch]);
            $toRollback = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($toRollback as $migrationFile) {
                echo "  ⏳ Rolling back: $migrationFile... ";
                $fullPath = $migrationsDir . '/' . $migrationFile;
                if (is_file($fullPath) && str_ends_with($migrationFile, '.php')) {
                    $migrationObj = require $fullPath;
                    if (is_object($migrationObj) && method_exists($migrationObj, 'down')) {
                        $migrationObj->down($pdo, $projectRoot, $siteUrl);
                    }
                }

                $delStmt = $pdo->prepare("DELETE FROM pew_migrations WHERE migration = ?");
                $delStmt->execute([$migrationFile]);
                echo "Done\n";
            }

            echo "\n✅ Rolled back batch $lastBatch.\n";
            break;

        case 'seed':
            echo "▶ Seeding reference content...\n\n";
            if (is_file($projectRoot . '/wp-load.php')) {
                require_once $projectRoot . '/wp-load.php';
                if (is_file($projectRoot . '/scripts/import-reference-content.php')) {
                    require_once $projectRoot . '/scripts/import-reference-content.php';
                    echo "  ✅ Reference content imported.\n";
                }
                if (is_file($projectRoot . '/scripts/seed-demo-admin.php')) {
                    require_once $projectRoot . '/scripts/seed-demo-admin.php';
                    echo "  ✅ Demo admin verified.\n";
                }
            } else {
                echo "  ⚠ wp-load.php not found; skipped WordPress seeders.\n";
            }
            break;
    }
} catch (\Throwable $e) {
    flock($lockHandle, LOCK_UN);
    fclose($lockHandle);
    if (PHP_SAPI !== 'cli') {
        http_response_code(500);
    }
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    echo "  in " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}

flock($lockHandle, LOCK_UN);
fclose($lockHandle);

echo "\n✅ Done.\n";
echo "The migration runner is idempotent and serialized against concurrent calls.\n";
