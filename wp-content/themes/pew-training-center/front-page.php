<?php
get_header();
$programs = array(
	array( 'title' => 'কম্পিউটার সায়েন্স', 'meta' => 'Computer Science & Technology', 'class' => 'violet', 'icon' => 'book' ),
	array( 'title' => 'সিভিল ইঞ্জিনিয়ারিং', 'meta' => 'Civil Technology', 'class' => 'orange', 'icon' => 'building' ),
	array( 'title' => 'ইলেকট্রিক্যাল ইঞ্জিনিয়ারিং', 'meta' => 'Electrical Technology', 'class' => 'blue', 'icon' => 'arrow' ),
	array( 'title' => 'মেকানিক্যাল ইঞ্জিনিয়ারিং', 'meta' => 'Mechanical Technology', 'class' => 'green', 'icon' => 'building' ),
	array( 'title' => 'ইলেকট্রনিক্স ইঞ্জিনিয়ারিং', 'meta' => 'Electronics Technology', 'class' => 'pink', 'icon' => 'arrow' ),
	array( 'title' => 'টেক্সটাইল ইঞ্জিনিয়ারিং', 'meta' => 'Textile Technology', 'class' => 'yellow', 'icon' => 'book' ),
);
$notices = array( 'উপবৃত্তির জন্য তথ্য গ্রহণ সংক্রান্ত জরুরি বিজ্ঞপ্তি', '১ম, ৩য়, ৫ম ও ৭ম পর্বের ব্যবহারিক পরীক্ষার সময়সূচি', 'ডিপ্লোমা ইন ইঞ্জিনিয়ারিং শিক্ষাক্রমের পরীক্ষার রুটিন', 'শিক্ষার্থীদের ইউনিক আইডি ফরম পূরণের নির্দেশনা' );
?>
<main id="main-content">
	<section class="hero">
		<div class="hero__grid container">
			<div class="hero__content">
				<div class="eyebrow"><span class="eyebrow__line"></span> দিনাজপুর, বাংলাদেশ <span class="eyebrow__dot"></span> EST. 2009</div>
				<h1>শিক্ষার আলোয়<br><em>সম্ভাবনার পথ</em></h1>
				<p>প্রযুক্তি ও দক্ষতাভিত্তিক শিক্ষার মাধ্যমে আগামীর বাংলাদেশ গড়ার প্রত্যয়ে আমরা নিরলসভাবে কাজ করে চলেছি।</p>
				<div class="hero__actions"><a class="button button--primary" href="#programs">আমাদের প্রোগ্রাম <span>↗</span></a><a class="text-link" href="#history">আমাদের সম্পর্কে জানুন <span>→</span></a></div>
				<div class="hero__trust"><div class="trust-avatars"><span>শি</span><span>দক</span><span>প্র</span><span>+</span></div><span>২,০০০+ শিক্ষার্থীর আস্থা</span></div>
			</div>
			<div class="hero__visual">
				<div class="hero__orb hero__orb--one"></div><div class="hero__orb hero__orb--two"></div>
				<div class="hero__card hero__card--main"><div class="hero__photo-placeholder"><span>প্রযুক্তির ভবিষ্যৎ<br><b>এখান থেকেই শুরু</b></span></div><div class="hero__card-label"><span>DINajpur Institute</span><strong>Science & Technology</strong></div></div>
				<div class="hero__floating hero__floating--stat"><strong>১৫+</strong><span>বছরের<br>অভিজ্ঞতা</span></div><div class="hero__floating hero__floating--badge">শিখুন<br><b>নতুন কিছু</b></div>
			</div>
		</div>
		<div class="hero__scroll"><span>SCROLL TO EXPLORE</span><i></i></div>
	</section>

	<section class="announcement"><div class="container announcement__inner"><span class="announcement__label">সর্বশেষ ঘোষণা</span><div class="announcement__track"><span>ভর্তি সংক্রান্ত সকল তথ্য ও নোটিশ নিয়মিতভাবে এখানে প্রকাশ করা হয়</span><span>•</span><span>পরবর্তী সেমিস্টারের ক্লাস রুটিন শীঘ্রই প্রকাশিত হবে</span></div><a href="#notices">সব নোটিশ দেখুন <span>↗</span></a></div></section>

	<section class="section section--programs" id="programs"><div class="container">
		<div class="section-heading"><div><span class="kicker">আমাদের একাডেমিক প্রোগ্রাম</span><h2>আপনার স্বপ্নের<br><em>বিষয়টি বেছে নিন</em></h2></div><p>শিক্ষার্থীদের আগ্রহ, দক্ষতা এবং ভবিষ্যৎ কর্মজীবনের কথা মাথায় রেখে আমাদের প্রতিটি টেকনোলজি সাজানো হয়েছে।</p></div>
		<div class="program-grid">
			<?php foreach ( $programs as $index => $program ) : ?><a class="program-card program-card--<?php echo esc_attr( $program['class'] ); ?>" href="#contact"><span class="program-card__number">0<?php echo esc_html( $index + 1 ); ?></span><span class="program-card__icon"><?php echo pew_icon( $program['icon'] ); ?></span><h3><?php echo esc_html( $program['title'] ); ?></h3><span class="program-card__meta"><?php echo esc_html( $program['meta'] ); ?></span><span class="program-card__arrow">↗</span></a><?php endforeach; ?>
		</div>
	</div></section>

	<section class="section section--story" id="history"><div class="container story-grid"><div class="story-media"><div class="story-photo"><span>EST.<br><b>2009</b></span></div><div class="story-stamp">DIST<br><small>Learning • Skill • Future</small></div></div><div class="story-content"><span class="kicker">আমাদের সংক্ষিপ্ত ইতিহাস</span><h2>শুধু চাকরি নয়,<br><em>উদ্যোক্তা হওয়ার শিক্ষা</em></h2><p>উত্তরের জেলা দিনাজপুরের কৃষি নির্ভর জনগোষ্ঠীর জীবনমান উন্নয়নে কারিগরি শিক্ষার প্রয়োজনীয়তা বিবেচনা করে ২০০৯ সালে দিনাজপুর ইনস্টিটিউট অব সাইন্স এন্ড টেকনোলজি প্রতিষ্ঠিত হয়।</p><p>আজ আমরা প্রযুক্তি, দক্ষতা ও মানবিক মূল্যবোধে সমৃদ্ধ একটি প্রজন্ম গড়ে তুলতে কাজ করছি।</p><a class="text-link" href="#contact">প্রতিষ্ঠান সম্পর্কে আরও জানুন <span>→</span></a><div class="story-stats"><div><strong>১৫+</strong><span>বছরের যাত্রা</span></div><div><strong>৮</strong><span>টেকনোলজি</span></div><div><strong>২k+</strong><span>শিক্ষার্থী</span></div></div></div></div></section>

	<section class="section section--content" id="notices"><div class="container content-grid">
		<div class="content-main"><div class="section-heading section-heading--compact"><div><span class="kicker">জেনে রাখুন</span><h2>নোটিশ ও <em>আপডেট</em></h2></div><a class="text-link" href="#">সবগুলো দেখুন <span>↗</span></a></div><div class="notice-list"><?php foreach ( $notices as $index => $notice ) : ?><article class="notice-item"><span class="notice-date"><b><?php echo esc_html( 12 - $index ); ?></b><small>এপ্রিল<br>২০২২</small></span><div><span class="notice-type">জরুরি বিজ্ঞপ্তি</span><h3><a href="#"><?php echo esc_html( $notice ); ?></a></h3></div><a class="notice-arrow" href="#" aria-label="নোটিশ পড়ুন">↗</a></article><?php endforeach; ?></div></div>
		<div class="content-side" id="routines"><div class="side-card side-card--dark"><div class="side-card__top"><span class="kicker">একাডেমিক ক্যালেন্ডার</span><span class="side-icon"><?php echo pew_icon( 'calendar' ); ?></span></div><h3>সময়কে সাথে নিয়ে<br><em>এগিয়ে চলুন</em></h3><div class="mini-calendar"><div class="mini-calendar__head"><b>সেপ্টেম্বর ২০২৬</b><span>‹ &nbsp; ›</span></div><div class="mini-calendar__week"><span>শো</span><span>ম</span><span>বু</span><span>বি</span><span>শু</span><span>শ</span><span>র</span></div><div class="mini-calendar__days"><span></span><span>১</span><span>২</span><span>৩</span><span>৪</span><span>৫</span><span>৬</span><span>৭</span><span>৮</span><span>৯</span><span>১০</span><span class="is-active">১১</span><span>১২</span><span>১৩</span><span>১৪</span><span>১৫</span><span>১৬</span><span>১৭</span><span>১৮</span><span>১৯</span><span>২০</span></div></div></div><div class="side-card side-card--light"><span class="kicker">আজকের উপস্থিতি</span><div class="attendance-number">৮৬<small>%</small></div><div class="attendance-bar"><span style="width:86%"></span></div><div class="attendance-meta"><span>মোট শিক্ষার্থী ২,০০০</span><span>আজ, ১১ সেপ্টেম্বর</span></div></div></div>
	</div></section>

	<section class="section section--principal"><div class="container principal-grid"><div><span class="kicker">অধ্যক্ষের কিছু কথা</span><blockquote>“শিক্ষা ব্যবস্থাকে আধুনিক ও গতিশীল করার জন্য তথ্য ও প্রযুক্তির ব্যবহার অনস্বীকার্য। আমাদের প্রতিটি শিক্ষার্থী যেন নিজের সম্ভাবনাকে কাজে লাগাতে পারে—এটাই আমাদের বিশ্বাস।”</blockquote><div class="principal-sign"><span class="principal-avatar">অ</span><div><strong>অধ্যক্ষ</strong><small>Dinajpur Institute of Science & Technology</small></div></div></div><div class="principal-visual"><div class="principal-line"></div><span>Future<br>Ready<br>Learning</span></div></div></section>

	<section class="section section--links" id="gallery"><div class="container"><div class="section-heading section-heading--compact"><div><span class="kicker">প্রয়োজনীয় লিংক</span><h2>আপনার প্রয়োজনীয় <em>তথ্য</em></h2></div><a class="button button--outline" href="#contact">সব লিংক <span>↗</span></a></div><div class="link-grid"><a href="#"><span class="link-grid__icon">শি</span><span><b>শিক্ষা মন্ত্রণালয়</b><small>moedu.gov.bd</small></span><span>↗</span></a><a href="#"><span class="link-grid__icon">বা</span><span><b>বাংলাদেশ কারিগরি শিক্ষা বোর্ড</b><small>bteb.gov.bd</small></span><span>↗</span></a><a href="#"><span class="link-grid__icon">প্র</span><span><b>বাংলাদেশ জাতীয় তথ্য বাতায়ন</b><small>bangladesh.gov.bd</small></span><span>↗</span></a><a href="#"><span class="link-grid__icon">জে</span><span><b>জেলা প্রশাসন, দিনাজপুর</b><small>dinajpur.gov.bd</small></span><span>↗</span></a></div></div></section>

	<section class="section section--cta"><div class="container cta-box"><div><span class="kicker">আপনার যাত্রা শুরু হোক</span><h2>আগামীর জন্য<br><em>আজই প্রস্তুত হন</em></h2></div><a class="button button--light" href="#contact">যোগাযোগ করুন <span>↗</span></a></div></section>
</main>
<?php get_footer(); ?>
