<?php
/**
 * Router for PHP's built-in server.
 * Existing files are served directly; all other requests go through WordPress.
 */

$request_path = parse_url( $_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH ) ?: '/';
$file_path    = realpath( __DIR__ . '/..' . $request_path );
$document_root = realpath( __DIR__ . '/..' );

if ( $file_path && $document_root && str_starts_with( $file_path, $document_root ) && is_file( $file_path ) ) {
	return false;
}

require $document_root . '/index.php';

