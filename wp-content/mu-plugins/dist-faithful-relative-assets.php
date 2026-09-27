<?php
/**
 * Keep copied DIST theme assets same-origin when the local site is exposed
 * through a temporary public tunnel.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function dist_faithful_relative_asset_source( $src, $handle ) {
	if ( 'dist-faithful' !== get_template() || false === strpos( $src, '/wp-content/themes/dist-faithful/' ) ) {
		return $src;
	}

	return wp_make_link_relative( $src );
}
add_filter( 'style_loader_src', 'dist_faithful_relative_asset_source', 100, 2 );
add_filter( 'script_loader_src', 'dist_faithful_relative_asset_source', 100, 2 );
