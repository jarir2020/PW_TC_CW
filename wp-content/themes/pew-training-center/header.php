<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main-content">মূল কনটেন্টে যান</a>
<div class="site-shell">
	<div class="topbar">
		<div class="container topbar__inner">
			<div class="topbar__message">দিনাজপুরের অন্যতম আধুনিক কারিগরি শিক্ষা প্রতিষ্ঠান</div>
			<div class="topbar__contacts"><a href="tel:+88053166080">☎ ০৫৩১-৬৬০৮০</a><a href="mailto:info@distdinajpur.edu.bd">✉ info@distdinajpur.edu.bd</a></div>
		</div>
	</div>
	<header class="site-header">
		<div class="container site-header__inner">
			<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?>">
				<span class="brand__mark">DIST</span>
				<span class="brand__copy"><strong>দিনাজপুর ইনস্টিটিউট</strong><small>অফ সাইন্স এন্ড টেকনোলজি</small></span>
			</a>
			<button class="mobile-toggle" type="button" aria-expanded="false" aria-controls="site-navigation"><span></span><span></span><span></span><em>মেনু</em></button>
			<nav id="site-navigation" class="site-nav" aria-label="প্রধান মেনু">
				<?php if ( has_nav_menu( 'primary' ) ) { wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'menu' ) ); } else { pew_primary_menu_fallback(); } ?>
			</nav>
			<a class="header-login" href="<?php echo esc_url( wp_login_url( home_url( '/dashboard/' ) ) ); ?>">লগ ইন <span>↗</span></a>
		</div>
	</header>
