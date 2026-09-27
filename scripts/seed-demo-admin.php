<?php
/**
 * Create or refresh the local PEW Training Center administrator.
 *
 * The username and password are read from the ignored .env file:
 *   PEW_ADMIN_USERNAME / WP_USERNAME
 *   PEW_ADMIN_PASSWORD / WP_PASSWORD
 *
 * Run from the project root:
 *   php scripts/seed-demo-admin.php
 */

$root = dirname( __DIR__ );
$env_file = $root . '/.env';
if ( is_readable( $env_file ) ) {
    foreach ( file( $env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) as $line ) {
        $line = trim( $line );
        if ( '' === $line || 0 === strpos( $line, '#' ) || false === strpos( $line, '=' ) ) {
            continue;
        }
        list( $key, $value ) = explode( '=', $line, 2 );
        $key   = trim( $key );
        $value = trim( $value );
        if ( strlen( $value ) >= 2 && ( ( '"' === $value[0] && '"' === substr( $value, -1 ) ) || ( "'" === $value[0] && "'" === substr( $value, -1 ) ) ) ) {
            $value = substr( $value, 1, -1 );
        }
        putenv( $key . '=' . $value );
    }
}

require_once $root . '/wp-load.php';

$username = getenv( 'PEW_ADMIN_USERNAME' ) ?: getenv( 'WP_USERNAME' ) ?: 'pew_admin';
$password = getenv( 'PEW_ADMIN_PASSWORD' ) ?: getenv( 'WP_PASSWORD' ) ?: '';
$email    = getenv( 'PEW_ADMIN_EMAIL' ) ?: 'admin@pewtc.local';

if ( '' === $password ) {
    fwrite( STDERR, "Set PEW_ADMIN_PASSWORD or WP_PASSWORD in the ignored .env file first.\n" );
    exit( 1 );
}

$user = get_user_by( 'login', $username );
if ( $user ) {
    $user_id = wp_update_user(
        array(
            'ID'           => $user->ID,
            'display_name' => 'PEW Training Center Admin',
            'role'         => 'administrator',
        )
    );
    if ( ! is_wp_error( $user_id ) ) {
        wp_set_password( $password, $user->ID );
        $user_id = $user->ID;
    }
} else {
    $user_id = wp_create_user( $username, $password, sanitize_email( $email ) );
    if ( ! is_wp_error( $user_id ) ) {
        $user = new WP_User( $user_id );
        $user->set_role( 'administrator' );
        wp_update_user( array( 'ID' => $user_id, 'display_name' => 'PEW Training Center Admin' ) );
    }
}

if ( is_wp_error( $user_id ) ) {
    fwrite( STDERR, 'Could not seed the administrator: ' . $user_id->get_error_message() . "\n" );
    exit( 1 );
}

printf( "Demo administrator is ready (user ID %d). Use the local .env credentials at /wp-login.php, then open /admin-panel/.\n", (int) $user_id );