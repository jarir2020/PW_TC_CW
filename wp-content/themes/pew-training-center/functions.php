<?php
/**
 * Pew Training Center theme setup.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PEW_THEME_VERSION', '1.0.0' );

function pew_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 80, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'responsive-embeds' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'pew-training-center' ),
			'footer'  => __( 'Footer Menu', 'pew-training-center' ),
		)
	);

	add_image_size( 'pew-hero', 1440, 720, true );
	add_image_size( 'pew-card', 720, 480, true );
	add_image_size( 'pew-square', 640, 640, true );
}
add_action( 'after_setup_theme', 'pew_theme_setup' );

function pew_enqueue_assets() {
	wp_enqueue_style( 'pew-fonts', 'https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap', array(), null );
	wp_enqueue_style( 'pew-theme', get_template_directory_uri() . '/assets/css/main.css', array(), PEW_THEME_VERSION );
	wp_enqueue_script( 'pew-theme', get_template_directory_uri() . '/assets/js/theme.js', array(), PEW_THEME_VERSION, true );
	wp_localize_script( 'pew-theme', 'pewTheme', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ) );
}
add_action( 'wp_enqueue_scripts', 'pew_enqueue_assets' );

function pew_admin_assets() {
	wp_enqueue_style( 'pew-admin', get_template_directory_uri() . '/assets/css/admin.css', array(), PEW_THEME_VERSION );
}
add_action( 'admin_enqueue_scripts', 'pew_admin_assets' );

function pew_body_classes( $classes ) {
	if ( is_front_page() ) {
		$classes[] = 'is-home';
	}
	return $classes;
}
add_filter( 'body_class', 'pew_body_classes' );

function pew_primary_menu_fallback() {
	?>
	<ul class="menu menu--fallback">
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">হোম</a></li>
		<li class="menu-item-has-children"><a href="#institute">প্রাতিষ্ঠানিক কার্যক্রম</a><button class="menu-toggle" aria-expanded="false" aria-label="সাবমেনু খুলুন">+</button>
			<ul class="sub-menu"><li><a href="#history">কলেজ ইতিহাস</a></li><li><a href="#notices">নোটিশ বোর্ড</a></li><li><a href="#routines">পাঠ্যক্রম ও রুটিন</a></li></ul>
		</li>
		<li class="menu-item-has-children"><a href="#programs">টেকনোলজি</a><button class="menu-toggle" aria-expanded="false" aria-label="সাবমেনু খুলুন">+</button>
			<ul class="sub-menu"><li><a href="#programs">সিভিল ইঞ্জিনিয়ারিং</a></li><li><a href="#programs">কম্পিউটার সায়েন্স</a></li><li><a href="#programs">ইলেকট্রিক্যাল ইঞ্জিনিয়ারিং</a></li></ul>
		</li>
		<li><a href="#gallery">গ্যালারি</a></li>
		<li><a href="#contact">যোগাযোগ</a></li>
	</ul>
	<?php
}

function pew_excerpt( $post_id = 0, $length = 18 ) {
	$post_id = $post_id ?: get_the_ID();
	$text    = wp_strip_all_tags( get_the_excerpt( $post_id ) );
	$words   = preg_split( '/\s+/u', trim( $text ) );
	if ( count( $words ) <= $length ) {
		return $text;
	}
	return implode( ' ', array_slice( $words, 0, $length ) ) . '…';
}

function pew_icon( $name ) {
	$icons = array(
		'arrow'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h13m-6-6 6 6-6 6"/></svg>',
		'building' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 21V4l8-2 8 2v17M8 8h1m6 0h1M8 12h1m6 0h1M8 16h1m6 0h1M10 21v-4h4v4"/></svg>',
		'book'     => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16ZM4 18.5A2.5 2.5 0 0 1 6.5 16H20"/></svg>',
		'calendar' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3v3m12-3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Z"/><path d="M8 13h.01M12 13h.01M16 13h.01M8 17h.01M12 17h.01"/></svg>',
		'download' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 20h14"/></svg>',
		'users'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3"/><path d="M3 20c.4-3.5 2.4-5 6-5s5.6 1.5 6 5M16 5.5a3 3 0 0 1 0 5.8M17 15c2.4.3 3.7 2 4 5"/></svg>',
	);
	return isset( $icons[ $name ] ) ? $icons[ $name ] : $icons['arrow'];
}

