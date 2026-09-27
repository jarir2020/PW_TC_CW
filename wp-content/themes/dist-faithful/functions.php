<?php
/**
 * Faithful WordPress port of the public DIST frontend.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DIST_FAITHFUL_VERSION', '1.0.0' );

function dist_faithful_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
}
add_action( 'after_setup_theme', 'dist_faithful_setup' );

function dist_faithful_asset( $path ) {
	return trailingslashit( get_template_directory_uri() ) . 'assets/' . ltrim( $path, '/' );
}

function dist_faithful_enqueue_styles() {
	$styles = array( 'reset', '960', 'ie', 'slider', 'theme', 'ticker-style', 'slider-theame', 'fonts', 'jquery.fancybox', 'jquery.fancybox-buttons', 'jquery.fancybox-thumbs', 'style', 'menu', 'prettyCheckable', 'result' );
	foreach ( $styles as $style ) {
		wp_enqueue_style( 'dist-' . sanitize_key( $style ), dist_faithful_asset( 'css/' . $style . '.css' ), array(), DIST_FAITHFUL_VERSION );
	}
	wp_enqueue_style( 'dist-jquery-ui', dist_faithful_asset( 'css/ui-themes/smoothness/jquery-ui.css' ), array(), DIST_FAITHFUL_VERSION );
	wp_enqueue_style( 'dist-wordpress-compat', dist_faithful_asset( 'css/wordpress-compat.css' ), array( 'dist-style' ), DIST_FAITHFUL_VERSION );
}
add_action( 'wp_enqueue_scripts', 'dist_faithful_enqueue_styles', 5 );

function dist_faithful_enqueue_scripts() {
	wp_deregister_script( 'jquery' );
	wp_register_script( 'jquery', dist_faithful_asset( 'js/jquery/jquery.min.js' ), array(), '1.8.3', false );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'dist-jquery-ui', dist_faithful_asset( 'js/jquery/jquery-ui.min.js' ), array( 'jquery' ), DIST_FAITHFUL_VERSION, false );
	wp_enqueue_script( 'dist-canvasjs', dist_faithful_asset( 'js/canvasjs.min.js' ), array(), DIST_FAITHFUL_VERSION, false );
	wp_enqueue_script( 'dist-hover-intent', dist_faithful_asset( 'js/jquery/jquery.hoverIntent.minified.js' ), array( 'jquery' ), DIST_FAITHFUL_VERSION, false );
	wp_enqueue_script( 'dist-superfish', dist_faithful_asset( 'js/jquery/superfish.js' ), array( 'jquery' ), DIST_FAITHFUL_VERSION, false );
	wp_enqueue_script( 'dist-supersubs', dist_faithful_asset( 'js/jquery/supersubs.js' ), array( 'jquery' ), DIST_FAITHFUL_VERSION, false );
	wp_enqueue_script( 'dist-theme', dist_faithful_asset( 'js/theme.js' ), array( 'jquery', 'dist-superfish', 'dist-supersubs' ), DIST_FAITHFUL_VERSION, false );
	wp_enqueue_script( 'dist-ie6-png-fix', dist_faithful_asset( 'js/scms/ie6PngFix.js' ), array( 'jquery' ), DIST_FAITHFUL_VERSION, false );
	wp_enqueue_script( 'dist-nivo', dist_faithful_asset( 'js/scms/jquery.nivo.slider.js' ), array( 'jquery' ), DIST_FAITHFUL_VERSION, false );
	wp_enqueue_script( 'dist-ticker', dist_faithful_asset( 'js/scms/jquery.ticker.js' ), array( 'jquery' ), DIST_FAITHFUL_VERSION, false );
	wp_enqueue_script( 'dist-easing', dist_faithful_asset( 'js/scms/jqueryeasing.js' ), array( 'jquery' ), DIST_FAITHFUL_VERSION, false );
	wp_enqueue_script( 'dist-fancybox', dist_faithful_asset( 'js/jquery.fancybox.js' ), array( 'jquery' ), DIST_FAITHFUL_VERSION, false );
	wp_enqueue_script( 'dist-fancybox-buttons', dist_faithful_asset( 'js/jquery.fancybox-buttons.js' ), array( 'jquery', 'dist-fancybox' ), DIST_FAITHFUL_VERSION, false );
	wp_enqueue_script( 'dist-pretty-checkable', dist_faithful_asset( 'js/scms/prettyCheckable.js' ), array( 'jquery' ), DIST_FAITHFUL_VERSION, false );
	wp_enqueue_script( 'dist-menu', dist_faithful_asset( 'js/scms/menu.js' ), array( 'jquery' ), DIST_FAITHFUL_VERSION, false );
	wp_enqueue_script( 'dist-site-js', dist_faithful_asset( 'js/scms/js.js' ), array( 'jquery', 'dist-nivo', 'dist-ticker', 'dist-pretty-checkable' ), DIST_FAITHFUL_VERSION, false );
}
add_action( 'wp_enqueue_scripts', 'dist_faithful_enqueue_scripts', 20 );

function dist_faithful_body_classes( $classes ) {
	$classes[] = 'dist-faithful-port';
	return $classes;
}
add_filter( 'body_class', 'dist_faithful_body_classes' );

function dist_faithful_url( $path = '/' ) {
	return home_url( '/' . ltrim( $path, '/' ) );
}

function dist_faithful_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page->ID ) : dist_faithful_url( $slug . '/' );
}

function dist_faithful_posts( $post_type, $limit = 5, $group = '' ) {
	$args = array( 'post_type' => $post_type, 'post_status' => 'publish', 'posts_per_page' => $limit, 'orderby' => 'date', 'order' => 'DESC', 'no_found_rows' => true, 'ignore_sticky_posts' => true );
	if ( $group ) {
		$args['meta_key']   = '_dist_group';
		$args['meta_value'] = $group;
	}
	return get_posts( $args );
}

function dist_faithful_date_parts( $post_id ) {
	$day   = get_post_meta( $post_id, '_dist_date_day', true ) ?: get_the_date( 'j', $post_id );
	$year  = get_post_meta( $post_id, '_dist_date_year', true ) ?: get_the_date( 'Y', $post_id );
	$month = get_post_meta( $post_id, '_dist_date_month', true ) ?: get_the_date( 'F', $post_id );
	return array( $day . $month, $year );
}

function dist_faithful_date_box( $post_id ) {
	$parts = dist_faithful_date_parts( $post_id );
	return '<span class="acc_title"><span>' . esc_html( $parts[0] ) . '</span>' . esc_html( $parts[1] ) . '</span>';
}

function dist_faithful_post_url( $post_id ) {
	return get_permalink( $post_id );
}

function dist_faithful_bengali_number( $number ) {
	return strtr( (string) $number, array( '0' => '০', '1' => '১', '2' => '২', '3' => '৩', '4' => '৪', '5' => '৫', '6' => '৬', '7' => '৭', '8' => '৮', '9' => '৯' ) );
}

function dist_faithful_calendar() {
	$timestamp     = current_time( 'timestamp' );
	$year          = (int) wp_date( 'Y', $timestamp );
	$month         = (int) wp_date( 'n', $timestamp );
	$today         = wp_date( 'Y-m-d', $timestamp );
	$month_names   = array( 1 => 'জানুয়ারী', 2 => 'ফেব্রুয়ারী', 3 => 'মার্চ', 4 => 'এপ্রিল', 5 => 'মে', 6 => 'জুন', 7 => 'জুলাই', 8 => 'আগস্ট', 9 => 'সেপ্টেম্বর', 10 => 'অক্টোবর', 11 => 'নভেম্বর', 12 => 'ডিসেম্বর' );
	$english_month = array( 1 => 'january', 2 => 'february', 3 => 'march', 4 => 'april', 5 => 'may', 6 => 'june', 7 => 'july', 8 => 'august', 9 => 'september', 10 => 'october', 11 => 'november', 12 => 'december' );
	$weekdays      = array( 'সোম', 'মঙ্গল', 'বুধ', 'বৃহ:', 'শুক্র', 'শনি', 'রবি' );
	$days_in_month = (int) wp_date( 't', $timestamp );
	$first_weekday = (int) wp_date( 'N', strtotime( sprintf( '%04d-%02d-01', $year, $month ) ) ) - 1;
	$previous_month = 1 === $month ? 12 : $month - 1;
	$next_month     = 12 === $month ? 1 : $month + 1;
	$previous_year  = 1 === $month ? $year - 1 : $year;
	$next_year      = 12 === $month ? $year + 1 : $year;
	$previous       = $previous_year . '/' . $english_month[ $previous_month ];
	$next           = $next_year . '/' . $english_month[ $next_month ];
	?>
	<div id="calendarCont" class="calendarCon">
		<div class="cal_hdr"><?php echo esc_html( $month_names[ $month ] . ' ' . dist_faithful_bengali_number( $year ) ); ?><img src="<?php echo esc_url( dist_faithful_asset( 'img/ajax/loadingAnimation.gif' ) ); ?>" style="display:none;position:absolute;left:0px;top:3px;" id="load_calendar" alt=""><a href="<?php echo esc_url( dist_faithful_url( 'calendar/' . $previous ) ); ?>" id="link-calendar-prev"><img src="<?php echo esc_url( dist_faithful_asset( 'img/bullet5.gif' ) ); ?>" alt=""></a><a href="<?php echo esc_url( dist_faithful_url( 'calendar/' . $next ) ); ?>" class="next" id="link-calendar-next"><img src="<?php echo esc_url( dist_faithful_asset( 'img/bullet7.gif' ) ); ?>" alt=""></a></div>
		<table border="1" cellpadding="0" cellspacing="2" width="100%" id="eventTable" class="cal_table"><thead><tr><?php foreach ( $weekdays as $weekday ) : ?><th class="cell-header"><?php echo esc_html( $weekday ); ?></th><?php endforeach; ?></tr></thead><tbody><tr>
		<?php for ( $blank = 0; $blank < $first_weekday; $blank++ ) : ?><td><div class="cell-data">&nbsp;</div></td><?php endfor; ?>
		<?php for ( $day = 1; $day <= $days_in_month; $day++ ) : $date = sprintf( '%04d-%02d-%02d', $year, $month, $day ); $weekday = (int) wp_date( 'N', strtotime( $date ) ); $classes = array(); if ( 5 === $weekday ) { $classes[] = 'wndTD'; } if ( $date === $today ) { $classes[] = 'tdyTD'; } ?><td title="<?php echo esc_attr( $date . ( 5 === $weekday ? '-Weekend' : '' ) . ( $date === $today ? '-Today' : '' ) ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"><div class="cell-number"><?php echo esc_html( dist_faithful_bengali_number( $day ) ); ?></div></td><?php if ( 0 === ( $first_weekday + $day ) % 7 && $day !== $days_in_month ) : ?></tr><tr><?php endif; ?><?php endfor; ?>
		<?php $total_cells = $first_weekday + $days_in_month; $end_cells = (int) ceil( $total_cells / 7 ) * 7; for ( $blank = $total_cells; $blank < $end_cells; $blank++ ) : $weekday = $blank % 7; ?><td class="<?php echo 4 === $weekday ? 'wndTD' : ''; ?>"><div class="cell-data">&nbsp;</div></td><?php if ( 6 === $weekday && $blank + 1 < $end_cells ) : ?></tr><tr><?php endif; ?><?php endfor; ?></tr></tbody></table>
	</div>
	<?php
}

function dist_faithful_nav() {
	$items = array(
		array( 'label' => 'হোম পেজ', 'url' => home_url( '/' ) ),
		array( 'label' => 'প্রাতিষ্ঠানিক কার্যক্রম', 'children' => array( array( 'label' => 'বাৎসরিক কার্যক্রম', 'slug' => 'anual_activities' ), array( 'label' => 'পাঠ্যক্রম', 'slug' => 'courses' ), array( 'label' => 'কোর্স সমুহ', 'slug' => 'school_courses' ), array( 'label' => 'পরীক্ষার ফল', 'slug' => 'exam_result' ), array( 'label' => 'ডকুমেন্টারি', 'slug' => 'documentaries' ) ) ),
		array( 'label' => 'গ্যালারি', 'url' => dist_faithful_page_url( 'albums' ) ),
		array( 'label' => 'অন্যান্য তথ্য', 'children' => array( array( 'label' => 'কলেজ ইতিহাস', 'slug' => 'history' ), array( 'label' => 'নিয়ম কানুন', 'slug' => 'discipline' ), array( 'label' => 'পাঠাগার', 'slug' => 'library' ), array( 'label' => 'ছাত্রাবাস', 'slug' => 'dormitory' ), array( 'label' => 'প্রয়োজনীয় ডাউনলোড', 'slug' => 'ডাউনলোড' ), array( 'label' => 'লাইব্রেরী', 'slug' => 'library2' ) ) ),
		array( 'label' => 'যোগাযোগ', 'url' => dist_faithful_page_url( 'contact' ) ),
		array( 'label' => 'ভর্তি তথ্য' ),
		array( 'label' => 'টেকনোলজি', 'children' => array( array( 'label' => 'Diploma-in-Civil Engg.', 'slug' => 'civil-1' ), array( 'label' => 'Diploma-in-Electical Engg.', 'slug' => 'electrical-1' ), array( 'label' => 'Diploma-in-Computer-Science', 'slug' => 'Diploma-in-Computer-Science' ), array( 'label' => 'Diploma-in-Electronics Engg.', 'slug' => 'engineering-1' ), array( 'label' => 'Diploma-in-Mechanical Engg.', 'slug' => 'mechanical-1' ), array( 'label' => 'Diploma-in-Textial Engg.', 'slug' => 'textile-1' ) ) ),
		array( 'label' => 'কলেজ প্রশাসন', 'children' => array( array( 'label' => 'অধ্যক্ষ', 'slug' => 'principal' ), array( 'label' => 'উপাধ্যক্ষ', 'slug' => 'viceprincipal' ), array( 'label' => 'শিক্ষক বৃন্দ', 'slug' => 'asssistant-teacher' ), array( 'label' => 'কমর্কর্তা-কর্মচারী', 'slug' => 'staff' ), array( 'label' => 'পিটিএ' ), array( 'label' => 'পরিচালনা পরিষদ', 'slug' => 'porichalona_porishad' ), array( 'label' => 'প্রাক্তন অধ্যক্ষবৃন্দ' ) ) ),
		array( 'label' => 'রেজাল্ট অনুসন্ধান', 'url' => dist_faithful_page_url( 'student-results' ) ),
		array( 'label' => 'স্টুডেন্ট আইডি অনুসন্ধান', 'url' => dist_faithful_page_url( 'student-id' ) ),
		array( 'label' => 'জব প্লেসমেন্ট', 'url' => dist_faithful_page_url( 'job-placement' ) ),
		array( 'label' => 'ব্লগ', 'url' => home_url( '/blog/' ) ),
	);
	?>
	<ul class="menu">
		<?php foreach ( $items as $index => $item ) : ?><li class="<?php echo 0 === $index ? 'current' : ''; ?>"><a href="<?php echo esc_url( $item['url'] ?? '#' ); ?>" id="link-<?php echo esc_attr( 56 + $index ); ?>" class="<?php echo 0 === $index ? 'selected' : ''; ?>"><span><?php echo esc_html( $item['label'] ); ?></span></a>
		<?php if ( ! empty( $item['children'] ) ) : ?><div><ul><?php foreach ( $item['children'] as $child ) : ?><li><a href="<?php echo esc_url( ! empty( $child['slug'] ) ? dist_faithful_page_url( $child['slug'] ) : '#' ); ?>" id="link-<?php echo esc_attr( 59 + $index ); ?>"><span><?php echo esc_html( $child['label'] ); ?></span></a></li><?php endforeach; ?></ul></div><?php endif; ?></li><?php endforeach; ?>
	</ul>
	<?php
}
