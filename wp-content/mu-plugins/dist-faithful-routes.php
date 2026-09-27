<?php
/**
 * Keep the faithful port's page URLs aligned with the live /page/<slug> paths.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

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
