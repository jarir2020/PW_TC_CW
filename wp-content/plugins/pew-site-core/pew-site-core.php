<?php
/**
 * Plugin Name: Pew Site Core
 * Description: Content types, roles, shortcodes and private portal behavior for Pew Training Center.
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 8.0
 * Author: Pew Training Center
 * Text Domain: pew-site-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function pew_core_post_types() {
	return array(
		'pew_notice'   => array( 'নোটিশ', 'নোটিশ', 'dashicons-megaphone', true ),
		'pew_course'   => array( 'টেকনোলজি', 'টেকনোলজি', 'dashicons-welcome-learn-more', true ),
		'pew_staff'    => array( 'শিক্ষক ও কর্মী', 'শিক্ষক ও কর্মী', 'dashicons-groups', true ),
		'pew_result'   => array( 'ফলাফল', 'ফলাফল', 'dashicons-chart-bar', true ),
		'pew_document' => array( 'ডকুমেন্ট', 'ডকুমেন্ট', 'dashicons-media-document', true ),
		'pew_gallery'     => array( 'গ্যালারি', 'গ্যালারি', 'dashicons-format-gallery', true ),
		'pew_event'       => array( 'ইভেন্ট', 'ইভেন্ট', 'dashicons-calendar-alt', true ),
		'pew_certificate' => array( 'সনদপত্র', 'সনদপত্র', 'dashicons-awards', true ),
	);
}

function pew_register_content_types() {
	$types = pew_core_post_types();
	foreach ( $types as $type => $data ) {
		register_post_type(
			$type,
			array(
				'labels' => array(
					'name'          => $data[0],
					'singular_name' => $data[1],
					'add_new_item'  => $data[1] . ' যোগ করুন',
					'edit_item'     => $data[1] . ' সম্পাদনা করুন',
				),
				'public'       => true,
				'show_in_rest' => true,
				'menu_icon'    => $data[2],
				'has_archive'  => true,
				'rewrite'      => array( 'slug' => str_replace( 'pew_', '', $type ) ),
				'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
			)
		);
	}

	register_taxonomy(
		'pew_department',
		array( 'pew_course', 'pew_staff', 'pew_result' ),
		array(
			'label'             => 'বিভাগ',
			'public'            => true,
			'show_in_rest'      => true,
			'hierarchical'      => true,
			'rewrite'           => array( 'slug' => 'department' ),
			'show_admin_column' => true,
		)
	);
}
add_action( 'init', 'pew_register_content_types' );

function pew_register_roles() {
	add_role(
		'pew_student',
		'শিক্ষার্থী',
		array(
			'read' => true,
		)
	);
	add_role(
		'pew_teacher',
		'শিক্ষক / স্টাফ',
		array(
			'read'         => true,
			'upload_files' => true,
			'edit_posts'   => true,
		)
	);
}

function pew_site_core_activate() {
	pew_register_roles();
	pew_register_content_types();
	flush_rewrite_rules();

	$page = get_page_by_path( 'dashboard' );
	if ( ! $page ) {
		wp_insert_post(
			array(
				'post_title'   => 'ড্যাশবোর্ড',
				'post_name'    => 'dashboard',
				'post_content' => '[pew_user_dashboard]',
				'post_status'  => 'publish',
				'post_type'    => 'page',
			)
		);
	}
}
register_activation_hook( __FILE__, 'pew_site_core_activate' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

function pew_login_styles() {
	$css = '.login { background:#f1f3ee; font-family:Arial,sans-serif; }.login h1 a { width:90px; height:90px; margin-bottom:12px; background:#ee7440; border-radius:50% 50% 50% 8px; color:#fff; font-size:22px; line-height:90px; text-indent:0; text-align:center; text-decoration:none; }.login h1 a:after { content:"DIST"; }.login form { border:0; border-radius:16px; box-shadow:0 20px 60px rgba(23,32,37,.1); }.login #wp-submit { background:#ee7440; border-color:#ee7440; }.login a { color:#bd4f28; }';
	wp_add_inline_style( 'login', $css );
}
add_action( 'login_enqueue_scripts', 'pew_login_styles' );

function pew_hide_private_admin( $show ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}
	return $show;
}
add_filter( 'show_admin_bar', 'pew_hide_private_admin' );

function pew_redirect_limited_users() {
	if ( wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || wp_doing_cron() ) {
		return;
	}
	if ( is_admin() && is_user_logged_in() && ! current_user_can( 'manage_options' ) && ! wp_is_json_request() ) {
		wp_safe_redirect( home_url( '/dashboard/' ) );
		exit;
	}
}
add_action( 'admin_init', 'pew_redirect_limited_users' );

function pew_login_redirect( $redirect_to, $requested_redirect_to, $user ) {
	if ( $user instanceof WP_User && ! user_can( $user, 'manage_options' ) ) {
		return home_url( '/dashboard/' );
	}
	return $redirect_to;
}
add_filter( 'login_redirect', 'pew_login_redirect', 10, 3 );

function pew_dashboard_shortcode() {
	if ( ! is_user_logged_in() ) {
		return '<section class="dashboard-gate"><h2>আপনার ড্যাশবোর্ডে প্রবেশ করুন</h2><p>শিক্ষার্থী, শিক্ষক এবং স্টাফদের জন্য ব্যক্তিগত তথ্য এখানে দেখা যাবে।</p><a class="button button--primary" href="' . esc_url( wp_login_url( get_permalink() ) ) . '">লগ ইন করুন <span>↗</span></a></section>';
	}

	$user       = wp_get_current_user();
	$role_label = 'ব্যবহারকারী';
	if ( in_array( 'pew_student', (array) $user->roles, true ) ) {
		$role_label = 'শিক্ষার্থী';
	} elseif ( in_array( 'pew_teacher', (array) $user->roles, true ) ) {
		$role_label = 'শিক্ষক / স্টাফ';
	} elseif ( in_array( 'administrator', (array) $user->roles, true ) ) {
		$role_label = 'অ্যাডমিনিস্ট্রেটর';
	}

	$cards = array(
		array( 'label' => 'নোটিশ', 'value' => wp_count_posts( 'pew_notice' )->publish ?? 0, 'url' => get_post_type_archive_link( 'pew_notice' ) ),
		array( 'label' => 'প্রোগ্রাম', 'value' => wp_count_posts( 'pew_course' )->publish ?? 0, 'url' => get_post_type_archive_link( 'pew_course' ) ),
		array( 'label' => 'ফলাফল', 'value' => wp_count_posts( 'pew_result' )->publish ?? 0, 'url' => get_post_type_archive_link( 'pew_result' ) ),
	);
	ob_start();
	?>
	<section class="dashboard-shell">
		<div class="dashboard-head"><div><span class="kicker">ব্যক্তিগত পোর্টাল</span><h2>স্বাগতম, <?php echo esc_html( $user->display_name ); ?></h2><p><?php echo esc_html( $role_label ); ?> · আপনার প্রয়োজনীয় তথ্য এক জায়গায়।</p></div><a class="text-link" href="<?php echo esc_url( function_exists( 'pew_admin_panel_logout_url' ) ? pew_admin_panel_logout_url() : wp_logout_url( home_url( '/' ) ) ); ?>">লগ আউট <span>↗</span></a></div>
		<div class="dashboard-cards"><?php foreach ( $cards as $card ) : ?><a href="<?php echo esc_url( $card['url'] ?: home_url( '/' ) ); ?>"><strong><?php echo esc_html( $card['value'] ); ?></strong><span><?php echo esc_html( $card['label'] ); ?></span><i>↗</i></a><?php endforeach; ?></div>
		<div class="dashboard-message"><span class="side-icon"><?php echo pew_dashboard_icon(); ?></span><div><h3>আপনার তথ্য নিয়মিত আপডেট করুন</h3><p>প্রোফাইল, একাডেমিক তথ্য ও যোগাযোগের বিবরণ সঠিক রাখলে প্রতিষ্ঠান থেকে প্রয়োজনীয় আপডেট সহজে পাওয়া যাবে।</p></div></div>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'pew_user_dashboard', 'pew_dashboard_shortcode' );

function pew_dashboard_icon() {
	return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v18M3 12h18" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.7"/></svg>';
}

function pew_gallery_shortcode() {
	$items = get_posts(
		array(
			'post_type'      => 'pew_gallery',
			'post_status'    => 'publish',
			'posts_per_page' => 60,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'no_found_rows'  => true,
		)
	);
	ob_start();
	?>
	<div class="pew-gallery-intro"><p>Explore moments from PEW Training Center training, practical sessions, and community activities.</p></div>
	<?php if ( $items ) : ?>
		<ul class="galleryIn pew-gallery-managed">
			<?php foreach ( $items as $item ) : ?>
				<?php
				$image_id = get_post_thumbnail_id( $item->ID );
				$full_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
				$image    = $image_id ? wp_get_attachment_image( $image_id, 'large', false, array( 'alt' => get_the_title( $item->ID ), 'loading' => 'lazy' ) ) : '';
				if ( ! $full_url || ! $image ) {
					continue;
				}
				$caption = get_the_excerpt( $item->ID );
				?>
				<li>
					<a class="fancybox" rel="pew-gallery" href="<?php echo esc_url( $full_url ); ?>" title="<?php echo esc_attr( get_the_title( $item->ID ) ); ?>"><?php echo wp_kses_post( $image ); ?><span aria-hidden="true"></span></a>
					<div class="pew-gallery-caption"><strong><?php echo esc_html( get_the_title( $item->ID ) ); ?></strong><?php if ( $caption ) : ?><small><?php echo esc_html( $caption ); ?></small><?php endif; ?></div>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<p class="pew-gallery-empty">Gallery images will be published here by the PEW Training Center administration.</p>
	<?php endif; ?>
	<?php
	return ob_get_clean();
}
add_shortcode( 'pew_gallery', 'pew_gallery_shortcode' );

function pew_gallery_page_content( $content ) {
	if ( is_page( 'albums' ) && in_the_loop() && is_main_query() && false === strpos( $content, 'pew_gallery' ) ) {
		$content .= pew_gallery_shortcode();
	}
	return $content;
}
add_filter( 'the_content', 'pew_gallery_page_content', 20 );

function pew_register_meta_boxes() {
	add_meta_box( 'pew_resource_details', 'রিসোর্সের তথ্য', 'pew_resource_meta_box', array( 'pew_document', 'pew_result', 'pew_event' ), 'normal', 'default' );
}
add_action( 'add_meta_boxes', 'pew_register_meta_boxes' );

function pew_resource_meta_box( $post ) {
	wp_nonce_field( 'pew_save_resource_details', 'pew_resource_nonce' );
	$url   = get_post_meta( $post->ID, '_pew_resource_url', true );
	$label = get_post_meta( $post->ID, '_pew_resource_label', true );
	?>
	<p><label for="pew_resource_url">ডাউনলোড / বিস্তারিত লিংক</label><input class="widefat" id="pew_resource_url" name="pew_resource_url" type="url" value="<?php echo esc_attr( $url ); ?>"></p>
	<p><label for="pew_resource_label">লিংকের লেবেল</label><input class="widefat" id="pew_resource_label" name="pew_resource_label" type="text" value="<?php echo esc_attr( $label ); ?>" placeholder="ডাউনলোড করুন"></p>
	<?php
}

function pew_save_resource_details( $post_id ) {
	if ( ! isset( $_POST['pew_resource_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pew_resource_nonce'] ) ), 'pew_save_resource_details' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( array( 'pew_resource_url' => '_pew_resource_url', 'pew_resource_label' => '_pew_resource_label' ) as $field => $meta_key ) {
		$value = isset( $_POST[ $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : '';
		if ( 'pew_resource_url' === $field ) {
			$value = esc_url_raw( $value );
		}
		update_post_meta( $post_id, $meta_key, $value );
	}
}
add_action( 'save_post', 'pew_save_resource_details' );

require_once __DIR__ . '/includes/admin-panel.php';
require_once __DIR__ . '/includes/certificates.php';
