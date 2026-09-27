<?php
/**
 * Import the reference homepage structure into the PEW Training Center content types.
 *
 * Run from the project root:
 *   php -r 'require getcwd()."/wp-load.php"; require getcwd()."/scripts/import-reference-content.php";'
 */

if ( ! function_exists( 'wp_insert_post' ) ) {
    fwrite( STDERR, "WordPress is not loaded.\n" );
    exit( 1 );
}

update_option( 'blogname', 'PEW Training Center' );
update_option( 'blogdescription', 'Skills for Industry Competitiveness and Innovation Program (SICIP)' );

function dist_import_upsert( $record ) {
    $post_type = $record['post_type'];
    $slug      = $record['post_name'] ?? '';
    $existing  = $slug ? get_page_by_path( $slug, OBJECT, $post_type ) : false;
    if ( ! $existing && ! empty( $record['post_title'] ) ) {
        $existing = get_page_by_title( $record['post_title'], OBJECT, $post_type );
    }

    $record['post_status'] = 'publish';
    $post_id               = $existing ? $existing->ID : wp_insert_post( wp_slash( $record ), true );
    if ( is_wp_error( $post_id ) ) {
        printf( "ERROR %s: %s\n", $record['post_title'] ?? $slug, $post_id->get_error_message() );
        return 0;
    }
    if ( $existing ) {
        $record['ID'] = $post_id;
        unset( $record['post_name'] );
        wp_update_post( wp_slash( $record ) );
    }
    foreach ( $record['meta'] ?? array() as $key => $value ) {
        update_post_meta( $post_id, $key, $value );
    }
    printf( "%s %s #%d\n", $existing ? 'UPDATED' : 'CREATED', $record['post_type'], $post_id );
    return $post_id;
}

$history_html = '<h2 style="text-align: center;">PEW Training Center</h2><p style="text-align: justify;"><strong>Skills for Industry Competitiveness and Innovation Program (SICIP)</strong></p><p style="text-align: justify;">PEW Training Center provides a platform for skills development and technical training. Program details, notices, and learner resources will be published here.</p>';
$principal_html = '<p style="text-align: justify;"><strong>Welcome to PEW Training Center.</strong></p><p style="text-align: justify;">Skills for Industry Competitiveness and Innovation Program (SICIP)</p><p style="text-align: justify;">Training center announcements and program updates will be shared through this website.</p>';

$pages = array(
    array( 'post_type' => 'page', 'post_name' => 'college_histroy', 'post_title' => 'কলেজ এর সংক্ষিপ্ত ইতিহাস', 'post_content' => $history_html ),
    array( 'post_type' => 'page', 'post_name' => 'history', 'post_title' => 'কলেজ ইতিহাস', 'post_content' => $history_html ),
    array( 'post_type' => 'page', 'post_name' => 'principal', 'post_title' => 'অধ্যক্ষের কিছু কথা', 'post_content' => $principal_html ),
    array( 'post_type' => 'page', 'post_name' => 'anual_activities', 'post_title' => 'বাৎসরিক কার্যক্রম', 'post_content' => '<p>প্রতিষ্ঠানের বাৎসরিক কার্যক্রম, একাডেমিক পরিকল্পনা এবং প্রকাশিত কার্যক্রম এখানে সংরক্ষিত হবে।</p>' ),
    array( 'post_type' => 'page', 'post_name' => 'courses', 'post_title' => 'পাঠ্যক্রম', 'post_content' => '<p>PEW Training Center-এর অনুমোদিত পাঠ্যক্রমের তথ্য।</p>' ),
    array( 'post_type' => 'page', 'post_name' => 'school_courses', 'post_title' => 'কোর্স সমূহ', 'post_content' => '<p>প্রতিষ্ঠানে পরিচালিত ডিপ্লোমা-ইন-ইঞ্জিনিয়ারিং কোর্সসমূহ।</p>' ),
    array( 'post_type' => 'page', 'post_name' => 'exam_result', 'post_title' => 'পরীক্ষার ফল', 'post_content' => '<p>পরীক্ষার ফলাফল ও প্রকাশিত ফল সংক্রান্ত তথ্য।</p>' ),
    array( 'post_type' => 'page', 'post_name' => 'documentaries', 'post_title' => 'ডকুমেন্টারি', 'post_content' => '<p>প্রতিষ্ঠানের ডকুমেন্টারি ও ভিডিও তথ্য।</p>' ),
    array( 'post_type' => 'page', 'post_name' => 'discipline', 'post_title' => 'প্রতিষ্ঠানের নিয়ম কানুন', 'post_content' => '<p>প্রতিষ্ঠানের নিয়ম কানুন ও শিক্ষার্থীদের জন্য আচরণবিধি।</p>' ),
    array( 'post_type' => 'page', 'post_name' => 'library', 'post_title' => 'পাঠাগার', 'post_content' => '<p>প্রতিষ্ঠানের পাঠাগার সম্পর্কিত তথ্য।</p>' ),
    array( 'post_type' => 'page', 'post_name' => 'dormitory', 'post_title' => 'ছাত্রাবাস', 'post_content' => '<p>ছাত্রাবাস সম্পর্কিত তথ্য।</p>' ),
    array( 'post_type' => 'page', 'post_name' => 'ডাউনলোড', 'post_title' => 'প্রয়োজনীয় ডাউনলোড', 'post_content' => '<p>প্রয়োজনীয় ফরম, নির্দেশনা ও ডাউনলোড লিংক।</p>' ),
    array( 'post_type' => 'page', 'post_name' => 'library2', 'post_title' => 'লাইব্রেরী', 'post_content' => '<p>লাইব্রেরী সেবা ও সংগ্রহের তথ্য।</p>' ),
    array( 'post_type' => 'page', 'post_name' => 'contact', 'post_title' => 'যোগাযোগ', 'post_content' => '<p><strong>PEW Training Center</strong><br>Chotobongram (Baro rasta more), Chandrima, Rajshahi.</p><p><strong>Tel:</strong> 01342-846300, 01342-846301, 01342-846302</p><p><strong>Email:</strong> <a href="mailto:info.pewtc@gmail.com">info.pewtc@gmail.com</a></p><p><strong>Web:</strong> <a href="https://www.pewtc.com/">www.pewtc.com</a></p>' ),
    array( 'post_type' => 'page', 'post_name' => 'albums', 'post_title' => 'গ্যালারি', 'post_content' => '<p>প্রতিষ্ঠানের ছবি ও গ্যালারি।</p>' ),
    array( 'post_type' => 'page', 'post_name' => 'student-results', 'post_title' => 'রেজাল্ট অনুসন্ধান', 'post_content' => '<p>শিক্ষার্থীর ফলাফল অনুসন্ধান।</p>' ),
    array( 'post_type' => 'page', 'post_name' => 'student-id', 'post_title' => 'স্টুডেন্ট আইডি অনুসন্ধান', 'post_content' => '<p>স্টুডেন্ট আইডি অনুসন্ধান।</p>' ),
    array( 'post_type' => 'page', 'post_name' => 'job-placement', 'post_title' => 'জব প্লেসমেন্ট', 'post_content' => '<p>জব প্লেসমেন্ট সংক্রান্ত তথ্য।</p>' ),
);
foreach ( $pages as $page ) {
    dist_import_upsert( $page );
}

$technologies = array(
    'civil-1' => 'Diploma-in-Civil Engineering',
    'electrical-1' => 'Diploma-in-Electrical Engineering',
    'engineering-1' => 'Diploma-in-Electronics Engineering',
    'mechanical-1' => 'Diploma-in-Mechanical Engineering',
    'computer-1' => 'Diploma-in-Computer Engineering',
    'Diploma-in-Computer-Science' => 'Diploma-in-Computer-Science',
    'textile-1' => 'Diploma-in-Textile Engineering',
);
foreach ( $technologies as $slug => $title ) {
    dist_import_upsert( array( 'post_type' => 'page', 'post_name' => $slug, 'post_title' => $title, 'post_content' => '<p>' . esc_html( $title ) . '</p>' ) );
}

$notices = array(
    array( 'post_name' => 'STIPEND INFORMATION SESSION 2021-22', 'post_title' => 'উপবৃত্তির জন্য তথ্য গ্রহণ - ১২-০৪-২০২২ ইং', 'day' => '১২', 'month' => 'এপ্রিল', 'year' => '২০২২' ),
    array( 'post_name' => 'unique-id-form', 'post_title' => 'Unique ID Form', 'day' => '১২', 'month' => 'ফেব্রুয়ারী', 'year' => '২০২২' ),
    array( 'post_name' => 'textile-board-challenge-2021', 'post_title' => 'Textile Board Challenge -2021', 'day' => '১৭', 'month' => 'মে', 'year' => '২০২১' ),
    array( 'post_name' => 'Textile Result and Board Challange 2021', 'post_title' => 'টেক্সটাইল ইঞ্জিনিয়ারিং সমাপনী পরীক্ষার ফলাফল এবং বোর্ড চ্যালেঞ্জ নোটিশ- ২০২১', 'day' => '১১', 'month' => 'মে', 'year' => '২০২১' ),
    array( 'post_name' => 'today05-10-19(3)', 'post_title' => 'বিজ্ঞপ্তি', 'day' => '০৫', 'month' => 'অক্টোবর', 'year' => '২০১৯' ),
);
foreach ( $notices as $item ) {
    dist_import_upsert( array( 'post_type' => 'pew_notice', 'post_name' => $item['post_name'], 'post_title' => $item['post_title'], 'post_content' => '<p>' . esc_html( $item['post_title'] ) . '</p>', 'meta' => array( '_dist_date_day' => $item['day'], '_dist_date_month' => $item['month'], '_dist_date_year' => $item['year'] ) ) );
}

$documents = array(
    array( 'group' => 'class_routine', 'post_name' => 'sdfd', 'post_title' => 'Class Routine-2019', 'day' => '০৬', 'month' => 'অক্টোবর', 'year' => '২০১৯' ),
    array( 'group' => 'class_routine', 'post_name' => 'c', 'post_title' => 'Class Routine 2017 (2nd,4th,6th,7th) all Department.', 'day' => '১১', 'month' => 'ফেব্রুয়ারী', 'year' => '২০১৭' ),
    array( 'group' => 'exam_routine', 'post_name' => 'mid-term-exam-routine-2019', 'post_title' => 'Mid Term Exam Routine-2019', 'day' => '১৩', 'month' => 'অক্টোবর', 'year' => '২০১৯' ),
    array( 'group' => 'exam_routine', 'post_name' => 'ডিপ্লোমা ইন ইঞ্জিনিয়ারিং শিক্ষাক্রমের পরীক্ষার সময়সূচী-২০১৭ ইং', 'post_title' => 'ডিপ্লোমা ইন ইঞ্জিনিয়ারিং শিক্ষাক্রমের পরীক্ষার সময়সূচী-২০১৭ ইং', 'day' => '১০', 'month' => 'নভেম্বর', 'year' => '২০১৭' ),
    array( 'group' => 'exam_routine', 'post_name' => 'Mid Term Exam 2017', 'post_title' => 'মধ্য পর্ব পরীক্ষা ২০১৭ ইং', 'day' => '২৭', 'month' => 'সেপ্টেম্বর', 'year' => '২০১৭' ),
    array( 'group' => 'exam_routine', 'post_name' => 'Exam Routine', 'post_title' => 'ডিপ্লোমা-ইন-ইঞ্জিনিয়ারিং এবং ডিপ্লোমা-ইন-টেক্সটাইল ইঞ্জিনিয়ারিং শিক্ষাক্রমের পর্ব মধ্য পরীক্ষার সময়সূচী', 'day' => '০৪', 'month' => 'এপ্রিল', 'year' => '২০১৭' ),
    array( 'group' => 'exam_routine', 'post_name' => 'পরীক্ষার রুটিন-২০১৬', 'post_title' => '২০১৬ সনের ডিপ্লোমা-ইন-ইঞ্জিনিয়ারিং শিক্ষাক্রমের পরীক্ষার সময়সূচী', 'day' => '১২', 'month' => 'ডিসেম্বর', 'year' => '২০১৬' ),
    array( 'group' => 'syllabus', 'post_name' => 'syllabus', 'post_title' => 'সিলেবাস', 'day' => '', 'month' => '', 'year' => '' ),
);
foreach ( $documents as $item ) {
    dist_import_upsert( array( 'post_type' => 'pew_document', 'post_name' => $item['post_name'], 'post_title' => $item['post_title'], 'post_content' => '<p>' . esc_html( $item['post_title'] ) . '</p>', 'meta' => array( '_dist_group' => $item['group'], '_dist_date_day' => $item['day'], '_dist_date_month' => $item['month'], '_dist_date_year' => $item['year'] ) ) );
}

$results = array(
    array( 'post_name' => 'টেক্সটাইল ৭ম সেমিস্টার এর ফলাফল প্রকাশ হয়েছে - ০৮-১০-২০১৭ ইং', 'post_title' => 'টেক্সটাইল ৭ম সেমিস্টার এর ফলাফল প্রকাশ হয়েছে - ০৮-১০-২০১৭ ইং' ),
    array( 'post_name' => 'টেক্সটাইল ৬ষ্ঠ সেমিস্টার এর ফলাফল প্রকাশ হয়েছে - ০৮-১০-২০১৭ ইং', 'post_title' => 'টেক্সটাইল ৬ষ্ঠ সেমিস্টার এর ফলাফল প্রকাশ হয়েছে - ০৮-১০-২০১৭ ইং' ),
    array( 'post_name' => 'টেক্সটাইল ৪র্থ সেমিস্টার এর ফলাফল প্রকাশ হয়েছে - ০৮-১০-২০১৭ ইং', 'post_title' => 'টেক্সটাইল ৪র্থ সেমিস্টার এর ফলাফল প্রকাশ হয়েছে - ০৮-১০-২০১৭ ইং' ),
    array( 'post_name' => '8th semister result publish 2017', 'post_title' => '৮ম সেমিস্টার এর ফলাফল প্রকাশ হয়েছে - ০১-১০-২০১৭ ইং' ),
    array( 'post_name' => '7th semister result publish 2017', 'post_title' => '৭ম সেমিস্টার এর ফলাফল প্রকাশ হয়েছে - ০১-১০-২০১৭ ইং' ),
);
foreach ( $results as $item ) {
    dist_import_upsert( array( 'post_type' => 'pew_result', 'post_name' => $item['post_name'], 'post_title' => $item['post_title'], 'post_content' => '<p>' . esc_html( $item['post_title'] ) . '</p>', 'meta' => array( '_dist_group' => 'result', '_dist_date_day' => '০৮', '_dist_date_month' => 'অক্টোবর', '_dist_date_year' => '২০১৭' ) ) );
}

flush_rewrite_rules();
echo "Reference content import complete.\n";
