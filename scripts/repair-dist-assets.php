<?php
/**
 * Repair copied content URLs after switching from the boilerplate theme.
 */

if ( ! function_exists( 'get_posts' ) ) {
    fwrite( STDERR, "WordPress is not loaded.\n" );
    exit( 1 );
}

$old_url = home_url( '/wp-content/themes/pew-training-center/assets/uploads/dist.jpg' );
$new_url = home_url( '/wp-content/themes/dist-faithful/assets/uploads/dist.jpg' );
$pages   = get_posts( array( 'post_type' => 'page', 'post_status' => 'any', 'posts_per_page' => -1 ) );
$fixed   = 0;
foreach ( $pages as $page ) {
    if ( false === strpos( $page->post_content, $old_url ) ) {
        continue;
    }
    wp_update_post(
        wp_slash(
            array(
                'ID'           => $page->ID,
                'post_content' => str_replace( $old_url, $new_url, $page->post_content ),
            )
        )
    );
    $fixed++;
}
printf( "Repaired %d page asset URL(s).\n", $fixed );
