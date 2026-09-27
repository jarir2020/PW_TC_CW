<?php
/**
 * Seed the supplied PEW Training Center company photos into the editable gallery.
 *
 * Run from the project root:
 *   php scripts/seed-demo-gallery.php
 */

$root = dirname( __DIR__ );
require_once $root . '/wp-load.php';

if ( ! post_type_exists( 'pew_gallery' ) ) {
    fwrite( STDERR, "The pew_gallery post type is not registered. Is Pew Site Core active?\n" );
    exit( 1 );
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$files = glob( $root . '/wp-content/themes/dist-faithful/assets/images/company/company-*.jpeg' );
sort( $files );

foreach ( $files as $source ) {
    $basename = basename( $source );
    $slug     = 'company-photo-' . sanitize_title( pathinfo( $basename, PATHINFO_FILENAME ) );
    $post     = get_page_by_path( $slug, OBJECT, 'pew_gallery' );
    if ( ! $post ) {
        $post_id = wp_insert_post(
            array(
                'post_type'    => 'pew_gallery',
                'post_status'  => 'publish',
                'post_title'   => 'PEW Training Center - ' . pathinfo( $basename, PATHINFO_FILENAME ),
                'post_excerpt' => 'Training center photo',
                'post_name'    => $slug,
            ),
            true
        );
        if ( is_wp_error( $post_id ) ) {
            fwrite( STDERR, "Could not create gallery item for {$basename}: {$post_id->get_error_message()}\n" );
            continue;
        }
    } else {
        $post_id = $post->ID;
    }

    if ( get_post_thumbnail_id( $post_id ) ) {
        printf( "KEPT gallery image #%d %s\n", (int) $post_id, $basename );
        continue;
    }

    $upload = wp_upload_bits( $basename, null, file_get_contents( $source ) );
    if ( ! empty( $upload['error'] ) ) {
        fwrite( STDERR, "Could not upload {$basename}: {$upload['error']}\n" );
        continue;
    }
    $filetype = wp_check_filetype( $upload['file'], null );
    $attachment_id = wp_insert_attachment(
        array(
            'post_mime_type' => $filetype['type'],
            'post_title'     => sanitize_text_field( pathinfo( $basename, PATHINFO_FILENAME ) ),
            'post_content'   => '',
            'post_status'    => 'inherit',
        ),
        $upload['file'],
        $post_id,
        true
    );
    if ( is_wp_error( $attachment_id ) ) {
        fwrite( STDERR, "Could not create attachment for {$basename}: {$attachment_id->get_error_message()}\n" );
        continue;
    }
    $metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
    wp_update_attachment_metadata( $attachment_id, $metadata );
    set_post_thumbnail( $post_id, $attachment_id );
    printf( "SEEDED gallery image #%d %s\n", (int) $post_id, $basename );
}
