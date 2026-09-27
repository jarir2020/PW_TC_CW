<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="description" content="PEW Training Center - Skills for Industry Competitiveness and Innovation Program (SICIP)">
	<meta name="keywords" content="PEW Training Center, SICIP, technical training, Rajshahi">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php if ( ! is_page( 'admin-panel' ) ) : ?>
<div class="fixedHdr">
	<div class="fxdFormCon">
		<form action="<?php echo esc_url( home_url( '/' ) ); ?>" id="CommentPromotedForm" method="post" accept-charset="utf-8">
			<div class="fleft user_comment"><input name="comment_name" value="নাম :" class="txtBox2" type="text"><input name="comment_email" value="ইমেল :" class="txtBox2" type="text"><input name="comment_phone" value="ফোন :" class="txtBox2" type="text"></div>
			<div class="fleft commnet_textarea"><textarea name="comment_body" class="txtBox2" cols="30" rows="6">মন্তব্য :</textarea><input class="subBtn2 submit_comment" type="submit" value="পাঠান"></div>
		</form>
		<?php if ( ! is_page( 'admin-panel' ) ) : ?><form action="<?php echo esc_url( wp_login_url( home_url( '/dashboard/' ) ) ); ?>" id="MiniUserLoginForm" class="fxHdrForm2" method="post" accept-charset="utf-8">
			<div class="fleft required"><input name="log" value="ব্যবহারকারী :" class="txtBox2" maxlength="60" type="text"></div><div class="fleft required"><input name="pwd" value="গোপন নং  :" class="txtBox2" type="password"></div><input class="subBtn2" type="submit" value="পাঠান">
		</form><?php endif; ?>
	</div>
	<?php if ( ! is_page( 'admin-panel' ) ) : ?><div class="fx_hdr_wrap"><div class="fright"><a href="#" class="btn1">মন্তব্য </a><a href="#" class="btn1">লগ ইন</a></div></div><?php endif; ?>
</div>

<header class="header">
	<div class="hdr_wrap">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" id="logo"><img src="<?php echo esc_url( dist_faithful_asset( 'uploads/new-logo.jpeg' ) ); ?>" alt="PEW Training Center"></a>
		<div class="hdrRgt">
			<div id="block-8" class="block block-search hdrForm"><div class="block-body"><form id="searchform" class="hdrForm" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>"><input name="s" class="txtBox1" size="10" type="text" id="q"><input class="subBtn1" type="submit" value="Submit"></form></div></div>
			<div id="block-16" class="block block-social_icon hdrIcon"><div class="block-body"><ul class="menu"><li><a href="<?php echo esc_url( home_url( '/feed/' ) ); ?>" class="icon5"><span>Feed</span></a></li><li><a href="https://www.linkedin.com" class="icon4" target="_blank"><span>Linkedin</span></a></li><li><a href="https://www.youtube.com" class="icon3" target="_blank"><span>Youtube</span></a></li><li><a href="https://www.twitter.com" class="icon2" target="_blank"><span>Twitter</span></a></li><li><a href="https://www.facebook.com/pewtc" class="icon1" target="_blank" rel="noopener noreferrer"><span>Facebook</span></a></li></ul></div></div>
		</div>
		<div id="menu"><?php dist_faithful_nav(); ?></div>
		<div class="newsTicker"><div id="block-14" class="block block-news_headline"><div class="block-body"><ul id="js-news" class="js-hidden">
		<?php $ticker_posts = dist_faithful_posts( 'pew_notice', 5 ); foreach ( $ticker_posts as $ticker_post ) : ?><li class="news-item"><a href="<?php echo esc_url( dist_faithful_post_url( $ticker_post->ID ) ); ?>"><?php echo esc_html( get_the_title( $ticker_post->ID ) ); ?></a></li><?php endforeach; ?>
		</ul></div></div></div>
	</div>
</header>
<?php endif; ?>

