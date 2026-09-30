<?php
declare(strict_types=1);

/**
 * Migration: 2026_09_30_000002_ensure_wordpress_options.php
 *
 * Ensures WordPress core options (blogname, blogdescription, admin_email) are aligned.
 */

return function (\PDO $pdo, string $projectRoot, string $siteUrl): void {
    $options = [
        'blogname'        => 'PEW Training Center',
        'blogdescription' => 'Skills for Industry Competitiveness and Innovation Program (SICIP)',
        'admin_email'     => 'admin@pewtc.com',
    ];

    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM pew_options WHERE option_name = ?");
    $updateStmt = $pdo->prepare("UPDATE pew_options SET option_value = ? WHERE option_name = ?");
    $insertStmt = $pdo->prepare("INSERT INTO pew_options (option_name, option_value, autoload) VALUES (?, ?, 'yes')");

    foreach ($options as $name => $val) {
        $checkStmt->execute([$name]);
        if ($checkStmt->fetchColumn() > 0) {
            $updateStmt->execute([$val, $name]);
        } else {
            $insertStmt->execute([$name, $val]);
        }
    }
};
