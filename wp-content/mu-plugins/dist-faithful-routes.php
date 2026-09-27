<?php
/**
 * Keep the faithful port's page URLs aligned with the live /page/<slug> paths
 * and keep WordPress canonical URLs correct behind a public HTTPS tunnel.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cloudflare terminates HTTPS before forwarding the request to the local PHP
 * server. Tell WordPress about the original request before it builds URLs or
 * runs canonical redirects.
 */
function dist_faithful_normalize_forwarded_request() {
	$forwarded_proto = isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] )
		? strtolower( trim( explode( ',', (string) wp_unslash( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) )[0] ) )
		: '';

	if ( 'https' !== $forwarded_proto ) {
		return;
	}

	// Only trust these forwarding headers when the request has Cloudflare
	// markers. Direct local requests should continue to use their local URL.
	if ( empty( $_SERVER['HTTP_CF_RAY'] ) && empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
		return;
	}

	$_SERVER['HTTPS']       = 'on';
	$_SERVER['SERVER_PORT'] = '443';
}

dist_faithful_normalize_forwarded_request();

/**
 * Return the public origin for a trusted Cloudflare-forwarded request.
 *
 * Quick Tunnels use the standard public HTTPS port. If the local request's
 * host contains the PHP port, remove it so it can never leak into a public
 * redirect.
 */
function dist_faithful_forwarded_origin() {
	$forwarded_proto = isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] )
		? strtolower( trim( explode( ',', (string) wp_unslash( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) )[0] ) )
		: '';

	if ( ! in_array( $forwarded_proto, array( 'http', 'https' ), true ) ) {
		return '';
	}

	if ( empty( $_SERVER['HTTP_CF_RAY'] ) && empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
		return '';
	}

	$host_header = ! empty( $_SERVER['HTTP_X_FORWARDED_HOST'] )
		? (string) wp_unslash( $_SERVER['HTTP_X_FORWARDED_HOST'] )
		: ( isset( $_SERVER['HTTP_HOST'] ) ? (string) wp_unslash( $_SERVER['HTTP_HOST'] ) : '' );
	$host = trim( explode( ',', $host_header )[0] );

	if ( '' === $host || ! preg_match( '/^(?:[A-Za-z0-9-]+\.)*[A-Za-z0-9-]+(?::[0-9]+)?$/', $host ) ) {
		return '';
	}

	// The Quick Tunnel is HTTPS on the public side; do not expose the local
	// PHP port if Cloudflare passed the public host through HTTP_HOST.
	if ( 'https' === $forwarded_proto ) {
		$host = preg_replace( '/:[0-9]+$/', '', $host );
	}

	return $forwarded_proto . '://' . $host;
}

/**
 * Replace WordPress-generated origins with the public forwarded origin.
 */
function dist_faithful_forwarded_url( $url, $path = '' ) {
	$origin = dist_faithful_forwarded_origin();

	if ( '' === $origin ) {
		return $url;
	}

	if ( '' === (string) $path ) {
		return $origin;
	}

	return trailingslashit( $origin ) . ltrim( (string) $path, '/' );
}
add_filter( 'home_url', 'dist_faithful_forwarded_url', 10, 4 );
add_filter( 'site_url', 'dist_faithful_forwarded_url', 10, 4 );

/**
 * Repair a canonical redirect already assembled with the local server port.
 */
function dist_faithful_forwarded_canonical_redirect( $redirect_url, $requested_url ) {
	$origin = dist_faithful_forwarded_origin();

	if ( '' === $origin || ! $redirect_url ) {
		return $redirect_url;
	}

	$parsed = wp_parse_url( $redirect_url );
	$path   = isset( $parsed['path'] ) && '' !== $parsed['path'] ? $parsed['path'] : '/';
	$query  = isset( $parsed['query'] ) ? '?' . $parsed['query'] : '';

	return $origin . $path . $query;
}
add_filter( 'redirect_canonical', 'dist_faithful_forwarded_canonical_redirect', 10, 2 );

function dist_faithful_page_routes() {
	if ( 'dist-faithful' === get_template() ) {
		add_rewrite_rule( '^page/([^/]+)/?$', 'index.php?pagename=$matches[1]', 'top' );
	}
}
add_action( 'init', 'dist_faithful_page_routes' );

function dist_faithful_page_routes_link( $link, $post_id, $sample ) {
	if ( 'dist-faithful' === get_template() && 'page' === get_post_type( $post_id ) && (int) $post_id !== (int) get_option( 'page_on_front' ) ) {
		return home_url( '/page/' . get_post_field( 'post_name', $post_id ) . '/' );
	}
	return $link;
}
add_filter( 'page_link', 'dist_faithful_page_routes_link', 10, 3 );
