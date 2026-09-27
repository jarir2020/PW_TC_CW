<?php
/**
 * One-time copy of the public page bodies from the live DIST site.
 *
 * This is intentionally an importer, not a runtime dependency. It fetches
 * the public HTML once and stores the node-body markup in WordPress pages.
 */

if ( ! function_exists( 'wp_remote_get' ) ) {
    fwrite( STDERR, "WordPress is not loaded.\n" );
    exit( 1 );
}

$pages = array(
    'courses' => 'courses',
    'school_courses' => 'school_courses',
    'documentaries' => 'documentaries',
    'discipline' => 'discipline',
    'library' => 'library',
    'dormitory' => 'dormitory',
    'ডাউনলোড' => 'ডাউনলোড',
    'civil-1' => 'civil-1',
    'electrical-1' => 'electrical-1',
    'engineering-1' => 'engineering-1',
    'mechanical-1' => 'mechanical-1',
    'Diploma-in-Computer-Science' => 'Diploma-in-Computer-Science',
    'textile-1' => 'textile-1',
);

foreach ( $pages as $slug => $remote_slug ) {
    $response = wp_remote_get( 'https://distdinajpur.edu.bd/page/' . rawurlencode( $remote_slug ), array( 'timeout' => 20 ) );
    if ( is_wp_error( $response ) ) {
        printf( "ERROR %s: %s\n", $slug, $response->get_error_message() );
        continue;
    }
    $status = wp_remote_retrieve_response_code( $response );
    $html   = wp_remote_retrieve_body( $response );
    if ( 200 !== $status || ! $html ) {
        printf( "SKIPPED %s: HTTP %d\n", $slug, $status );
        continue;
    }

    libxml_use_internal_errors( true );
    $document = new DOMDocument();
    $document->loadHTML( '<?xml encoding="UTF-8">' . $html );
    $xpath = new DOMXPath( $document );
    $nodes = $xpath->query( "//*[contains(concat(' ', normalize-space(@class), ' '), ' node-body ')]" );
    if ( ! $nodes || ! $nodes->length ) {
        printf( "SKIPPED %s: node-body not found\n", $slug );
        continue;
    }

    $body = '';
    foreach ( $nodes->item( 0 )->childNodes as $child ) {
        $body .= $document->saveHTML( $child );
    }
    $page = get_page_by_path( $slug, OBJECT, 'page' );
    if ( ! $page ) {
        printf( "SKIPPED %s: local page not found\n", $slug );
        continue;
    }
    wp_update_post(
        wp_slash(
            array(
                'ID'           => $page->ID,
                'post_content' => $body,
            )
        )
    );
    printf( "UPDATED page %s #%d from live HTML\n", $slug, $page->ID );
}

echo "Live page-body import complete.\n";
