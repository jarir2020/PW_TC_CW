<?php
/**
 * Seed the first content set after WordPress installation.
 * Run with: wp eval-file scripts/seed-content.php
 */

if ( ! function_exists( 'wp_insert_post' ) ) {
	fwrite( STDERR, "WordPress is not loaded. Run this through WP-CLI after installation.\n" );
	exit( 1 );
}

$records = array(
	array(
		'post_type'    => 'pew_notice',
		'post_title'   => 'উপবৃত্তির জন্য তথ্য গ্রহণ সংক্রান্ত জরুরি বিজ্ঞপ্তি',
		'post_content' => 'শিক্ষার্থীদের প্রয়োজনীয় তথ্য নির্ধারিত সময়ের মধ্যে জমা দেওয়ার জন্য অনুরোধ করা হলো।',
	),
	array(
		'post_type'    => 'pew_notice',
		'post_title'   => 'পর্বমধ্য পরীক্ষার সময়সূচি প্রকাশিত হয়েছে',
		'post_content' => 'সকল বিভাগের শিক্ষার্থীদের পরীক্ষার সময়সূচি নোটিশ বোর্ড থেকে সংগ্রহ করতে হবে।',
	),
	array(
		'post_type'    => 'pew_notice',
		'post_title'   => 'শিক্ষার্থীদের ইউনিক আইডি ফরম পূরণের নির্দেশনা',
		'post_content' => 'সঠিক তথ্য দিয়ে অনলাইন ফরম পূরণ করে নির্ধারিত সময়ে বিভাগীয় অফিসে জমা দিন।',
	),
	array(
		'post_type'    => 'pew_course',
		'post_title'   => 'কম্পিউটার সায়েন্স এন্ড টেকনোলজি',
		'post_content' => 'কম্পিউটার সায়েন্স, সফটওয়্যার, নেটওয়ার্কিং ও আধুনিক প্রযুক্তির ব্যবহারিক শিক্ষা।',
	),
	array(
		'post_type'    => 'pew_course',
		'post_title'   => 'সিভিল টেকনোলজি',
		'post_content' => 'নির্মাণ, অবকাঠামো, সার্ভেয়িং ও পরিবেশভিত্তিক প্রকৌশল দক্ষতার পূর্ণাঙ্গ প্রোগ্রাম।',
	),
	array(
		'post_type'    => 'pew_course',
		'post_title'   => 'ইলেকট্রিক্যাল টেকনোলজি',
		'post_content' => 'বিদ্যুৎ, নিয়ন্ত্রণ ব্যবস্থা, মেশিন ও শিল্প-প্রযুক্তির ব্যবহারিক প্রশিক্ষণ।',
	),
	array(
		'post_type'    => 'pew_course',
		'post_title'   => 'মেকানিক্যাল টেকনোলজি',
		'post_content' => 'মেশিন ডিজাইন, উৎপাদন, রক্ষণাবেক্ষণ ও আধুনিক যন্ত্রপাতির ব্যবহারিক শিক্ষা।',
	),
	array(
		'post_type'    => 'pew_course',
		'post_title'   => 'ইলেকট্রনিক্স টেকনোলজি',
		'post_content' => 'ইলেকট্রনিক সার্কিট, ডিজিটাল সিস্টেম ও যোগাযোগ প্রযুক্তির হাতে-কলমে প্রশিক্ষণ।',
	),
	array(
		'post_type'    => 'pew_course',
		'post_title'   => 'টেক্সটাইল টেকনোলজি',
		'post_content' => 'টেক্সটাইল উৎপাদন, ফ্যাব্রিক, গার্মেন্টস ও মান নিয়ন্ত্রণ বিষয়ে কর্মমুখী শিক্ষা।',
	),
);

$created = 0;
$skipped = 0;
foreach ( $records as $record ) {
	$existing = get_page_by_title( $record['post_title'], OBJECT, $record['post_type'] );
	if ( $existing ) {
		$skipped++;
		continue;
	}
	$record['post_status'] = 'publish';
	if ( wp_insert_post( wp_slash( $record ), true ) ) {
		$created++;
	}
}

printf( "Seed complete: %d created, %d already present.\n", $created, $skipped );

