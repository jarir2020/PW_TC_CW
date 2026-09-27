<?php
/**
 * Rewrite legacy local theme asset URLs embedded in imported page content.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function dist_faithful_rewrite_local_asset_urls( $html ) {
	if ( 'dist-faithful' !== get_template() || false === strpos( $html, '/wp-content/themes/dist-faithful/' ) ) {
		return $html;
	}

	return preg_replace(
		'~https?://(?:127\.0\.0\.1|localhost)(?::[0-9]+)?(?=/wp-content/themes/dist-faithful/)~i',
		'',
		$html
	);
}

function dist_faithful_start_asset_url_rewrite() {
	if ( is_admin() || is_feed() || ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	ob_start( 'dist_faithful_rewrite_local_asset_urls' );
}
add_action( 'template_redirect', 'dist_faithful_start_asset_url_rewrite', 0 );
