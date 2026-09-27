<?php
/**
 * Import the reference homepage structure into the PEW Training Center content types.
 *
 * Run from the project root:
 *   php -r 'require getcwd()."/wp-load.php"; require getcwd()."/scripts/import-reference-content.php";'
 */

if ( ! function_exists( 'wp_insert_post' ) ) {
    $wp_load = dirname( __DIR__ ) . '/wp-load.php';
    if ( file_exists( $wp_load ) ) {
        require_once $wp_load;
    }
}

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

$history_html = '<div class="pew-certificate-card">'
    . '<h2>পিইডব্লিউ ট্রেনিং সেন্টার (PEW Training Center)</h2>'
    . '<p>পিইডব্লিউ ট্রেনিং সেন্টার (PEWTC) রাজশাহীর একটি শীর্ষস্থানীয় কারিগরি ও বৃত্তিমূলক প্রশিক্ষণ প্রতিষ্ঠান। প্রতিষ্ঠানটি ছোট বনগ্রাম (বার রাস্তার মোড়), চন্দ্রিমা, সপুরা, রাজশাহী-এ অবস্থিত। বাংলাদেশের দ্রুত বর্ধনশীল শিল্প খাতের চাহিদা অনুযায়ী দক্ষ, আধুনিক ও কর্মমুখী মানবসম্পদ তৈরি করার দৃঢ় অঙ্গীকার নিয়ে প্রতিষ্ঠানটি পরিচালিত হয়ে আসছে।</p>'
    . '<p>প্রতিষ্ঠানটি গণপ্রজাতন্ত্রী বাংলাদেশ সরকারের অর্থ বিভাগ, অর্থ মন্ত্রণালয়-এর সহযোগিতায় <strong>Skills for Industry Competitiveness and Innovation Program (SICIP)</strong>-এর আওতায় এবং <strong>বাংলাদেশ ইঞ্জিনিয়ারিং ইন্ডাস্ট্রি ওনার্স এসোসিয়েশন (BEIOA)</strong>-এর তত্ত্বাবধানে চার মাস মেয়াদী স্ট্যান্ডার্ডাইজড কারিগরি কোর্স সফলভাবে পরিচালনা করে আসছে। এছাড়াও প্রতিষ্ঠানটি জাতীয় দক্ষতা উন্নয়ন কর্তৃপক্ষ (NSDA) এবং কনস্ট্রাকশন ইন্ডাস্ট্রি স্কিলস কাউন্সিল (CISC)-এর সাথে সমন্বয় রেখে প্রি-লার্নিং রিকগনিশন (RPL) ও ইন্ডাস্ট্রি স্ট্যান্ডার্ড কারিগরি প্রশিক্ষণ প্রদান করে।</p>'
    . '<p><strong>আমাদের লক্ষ্য ও উদ্দেশ্য:</strong> আধুনিক ওয়ার্কশপ ল্যাবরেটরি, আধুনিক যন্ত্রপাতি ও বাস্তবমুখী ব্যবহারিক শিক্ষার মাধ্যমে তরুণ-তরুণী, বিশেষ করে নারী ও অনগ্রসর জনগোষ্ঠীকে কারিগরি দক্ষতায় সাবলম্বী করে তোলা এবং দেশি-বিদেশি শিল্প প্রতিষ্ঠানে তাদের কর্মসংস্থানের টেকসই পথ সুগম করা।</p>'
    . '</div>';

$principal_html = '<div class="pew-certificate-card">'
    . '<h2>স্বাগত বার্তা — পিইডব্লিউ ট্রেনিং সেন্টার</h2>'
    . '<p><strong>পিইডব্লিউ ট্রেনিং সেন্টারে আপনাদের আন্তরিক স্বাগতম।</strong></p>'
    . '<p>একবিংশ শতাব্দীর চ্যালেঞ্জ মোকাবিলায় এবং চতুর্থ শিল্প বিপ্লবের যুগে সনাতন শিক্ষার পাশাপাশি প্রায়োগিক ও কারিগরি শিক্ষার গুরুত্ব অপরিসীম। দক্ষ জনশক্তি ছাড়া কোনো দেশ বা জাতির অর্থনৈতিক সমৃদ্ধি সম্ভব নয়। পিইডব্লিউ ট্রেনিং সেন্টার সেই লক্ষ্যেই মানসম্পন্ন, বাস্তবমুখী ও কর্মসংস্থানমূলক প্রশিক্ষণ প্রদানে কাজ করে যাচ্ছে।</p>'
    . '<p>আমাদের প্রশিক্ষণ কেন্দ্রে রয়েছে আধুনিক সরঞ্জামসজ্জিত ল্যাব, দক্ষ প্রশিক্ষকমণ্ডলী এবং সম্পূর্ণ নিরাপদ ও শিক্ষাবান্ধব পরিবেশ। আমরা প্রতিটি শিক্ষার্থীকে শুধু প্রশিক্ষণই দিই না, তাদের মধ্যে পেশাদারিত্ব, সততা ও কর্মস্পৃহা গড়ে তোলার জন্য কাজ করি। প্রশিক্ষণ শেষে শিক্ষার্থীদের চাকরি প্রাপ্তি এবং আত্মকর্মসংস্থানে আমরা প্রত্যক্ষ ভূমিকা রাখি। আসুন, কারিগরি শিক্ষা গ্রহণ করে নিজেকে দক্ষ মানবসম্পদে রূপান্তরিত করি।</p>'
    . '<p><strong>— সেন্টার ইনচার্জ / পরিচালক</strong><br>পিইডব্লিউ ট্রেনিং সেন্টার, রাজশাহী।</p>'
    . '</div>';

$admission_html = '<div class="pew-admission-page">'
    . '<div class="pew-admission-hero">'
    . '<span class="pew-admission-badge">ভর্তি বিজ্ঞপ্তি ২০২৪-২০২৫</span>'
    . '<h2>সরকারি বৃত্তিতে সম্পূর্ণ বিনামূল্যে ৪ মাস মেয়াদী ট্রেড কোর্সে ভর্তি চলছে!</h2>'
    . '<p>বাংলাদেশ সরকারের অগ্রাধিকার শিল্প খাত হিসেবে ঘোষিত লাইট ইঞ্জিনিয়ারিং শিল্পসহ দেশের সকল শিল্পের জন্য দক্ষ জনশক্তি গড়ে তোলার লক্ষ্যে অর্থ বিভাগ, অর্থ মন্ত্রণালয়ের সহযোগিতায় Skills for Industry Competitiveness and Innovation Program (SICIP) প্রকল্পের আওতায় বাংলাদেশ ইঞ্জিনিয়ারিং ইন্ডাস্ট্রি ওনার্স এসোসিয়েশন (BEIOA)-এর তত্ত্বাবধানে পিইডব্লিউ ট্রেনিং সেন্টার (PEWTC)-এ সম্পূর্ণ বিনামূল্যে কারিগরি প্রশিক্ষণের সুযোগ।</p>'
    . '</div>'
    . '<div class="pew-course-cards">'
    . '<div class="pew-course-box"><h3>১. ইলেকট্রিক্যাল ইনস্টলেশন এন্ড মেনটেনেন্স</h3>'
    . '<ul><li><strong>কোর্সের মেয়াদ:</strong> ৪ মাস</li><li><strong>শিক্ষাগত যোগ্যতা:</strong> কমপক্ষে ৮ম শ্রেণি / জেএসসি পাস</li><li><strong>বয়সসীমা:</strong> ১৮ থেকে ৪৫ বছর</li><li><strong>শিখন ক্ষেত্র:</strong> হাউস ওয়্যারিং, ইন্ডাস্ট্রিয়াল ওয়্যারিং, মোটর কন্ট্রোল ও মেইনটেনেন্স, সার্কিট ব্রেকার, সেফটি ও ট্রাবলশুটিং।</li></ul></div>'
    . '<div class="pew-course-box"><h3>২. ওয়েল্ডিং (Welding)</h3>'
    . '<ul><li><strong>কোর্সের মেয়াদ:</strong> ৪ মাস</li><li><strong>শিক্ষাগত যোগ্যতা:</strong> কমপক্ষে ৮ম শ্রেণি / জেএসসি পাস</li><li><strong>বয়সসীমা:</strong> ১৮ থেকে ৪৫ বছর</li><li><strong>শিখন ক্ষেত্র:</strong> আর্ক ওয়েল্ডিং (SMAW), গ্যাস মেটাল আর্ক ওয়েল্ডিং (MIG), মেটাল কাটিং, জয়েন্ট প্রিপারেশন, গ্রাইন্ডিং ও সেফটি।</li></ul></div>'
    . '</div>'
    . '<div class="pew-facility-list"><h3>প্রশিক্ষণের বৈশিষ্ট্য ও বিশেষ সুবিধাসমূহ:</h3>'
    . '<ol>'
    . '<li><strong>১০০% বিনামূল্যে প্রশিক্ষণ:</strong> কোনো প্রকার ভর্তি ফি বা কোর্স ফি নেই।</li>'
    . '<li><strong>দৈনিক ভাতা প্রদান:</strong> নিয়মিত ক্লাসে উপস্থিতির ভিত্তিতে দৈনিক ১০০ টাকা যাতায়াত ভাতা এবং ৫০ টাকা রিফ্রেশমেন্ট ভাতা (দৈনিক ১৫০ টাকা) প্রদান।</li>'
    . '<li><strong>অগ্রাধিকার:</strong> নারী, ক্ষুদ্র নৃ-গোষ্ঠী, প্রতিবন্ধী ও সুবিধাবঞ্চিত প্রার্থীদের বিশেষ অগ্রাধিকার।</li>'
    . '<li><strong>সনদপত্র:</strong> কোর্স সফলভাবে সম্পন্ন করার পর সরকারি ও জাতীয়ভাবে স্বীকৃত সার্টিফিকেট প্রদান।</li>'
    . '<li><strong>চাকরি সহায়তা:</strong> প্রশিক্ষণ শেষে শিক্ষার্থীদের দেশ-বিদেশে চাকরি প্রাপ্তিতে সর্বাত্মক সহায়তা প্রদান।</li>'
    . '</ol></div>'
    . '<div class="pew-facility-list" style="background:#fff7eb; border-color:#fadcb3;"><h3>ভর্তির জন্য প্রয়োজনীয় কাগজপত্র:</h3>'
    . '<ol>'
    . '<li>সদ্য তোলা পাসপোর্ট সাইজের রঙিন ছবি - ২ কপি।</li>'
    . '<li>জাতীয় পরিচয়পত্র (NID) অথবা অনলাইন জন্মনিবন্ধন সনদের ফটোকপি - ১ কপি।</li>'
    . '<li>সর্বশেষ শিক্ষাগত যোগ্যতার সনদের ফটোকপি - ১ কপি।</li>'
    . '</ol></div>'
    . '<div class="pew-cert-contact-card" style="margin-top:20px;">'
    . '<h4>ভর্তি ও বিস্তারিত তথ্যের জন্য যোগাযোগ:</h4>'
    . '<p><strong>PEW Training Center</strong><br>ছোট বনগ্রাম (বার রাস্তার মোড়), চন্দ্রিমা, সপুরা, রাজশাহী ৬২০৩।<br>'
    . '<strong>হটলাইন:</strong> 01342-846300 (WhatsApp) | <strong>মোবাইল:</strong> 01342-846301, 01342-846302<br>'
    . '<strong>ইমেইল:</strong> <a href="mailto:info.pewtc@gmail.com">info.pewtc@gmail.com</a> | <strong>ওয়েবসাইট:</strong> <a href="https://www.pewtc.com/">www.pewtc.com</a> | <strong>ফেসবুক:</strong> <a href="https://www.facebook.com/pewtc" target="_blank" rel="noopener">facebook.com/pewtc</a></p>'
    . '</div>'
    . '</div>';

$activities_html = '<div class="pew-certificate-card">'
    . '<h2>বাৎসরিক কার্যক্রম ও একাডেমিক ক্যালেন্ডার</h2>'
    . '<p>পিইডব্লিউ ট্রেনিং সেন্টার প্রতি বছর তিনটি নিয়মিত সেশনে ৪ মাস মেয়াদী কারিগরি ট্রেড কোর্স পরিচালনা করে থাকে। প্রতিটি সেশনের কার্যক্রম সুনির্দিষ্ট পরিকল্পনা অনুযায়ী পরিচালিত হয়:</p>'
    . '<table class="pew-table-clean"><thead><tr><th>সেশন / ব্যাচ</th><th>কার্যক্রমের মেয়াদ</th><th>প্রধান কার্যক্রম</th></tr></thead><tbody>'
    . '<tr><td><strong>১ম ব্যাচ (জানুয়ারি – এপ্রিল)</strong></td><td>জানুয়ারি থেকে এপ্রিল</td><td>ভর্তি কার্যক্রম, ওরিয়েন্টেশন, বেসিক ও অ্যাডভান্সড ব্যবহারিক প্রশিক্ষণ, মিড-টার্ম পরীক্ষা, চূড়ান্ত মূল্যায়ন ও সনদপত্র বিতরণ।</td></tr>'
    . '<tr><td><strong>২য় ব্যাচ (মে – আগস্ট)</strong></td><td>মে থেকে আগস্ট</td><td>ভর্তি বিজ্ঞপ্তি, ওয়ার্কশপ নিরাপত্তা সপ্তাহ, হ্যান্ডস-অন ল্যাব প্র্যাকটিস, ইন্ডাস্ট্রিয়াল ভিজিট ও চূড়ান্ত এসেসমেন্ট।</td></tr>'
    . '<tr><td><strong>৩য় ব্যাচ (সেপ্টেম্বর – ডিসেম্বর)</strong></td><td>সেপ্টেম্বর থেকে ডিসেম্বর</td><td>ভর্তি কার্যক্রম, কারিগরি প্রশিক্ষণ, জব ফেয়ার প্রস্তুতি, সনদপত্র প্রদান ও কর্মসংস্থান সহায়তা।</td></tr>'
    . '</tbody></table>'
    . '<p><strong>বিশেষ ইভেন্ট ও কার্যক্রম:</strong> ফায়ার সেফটি অ্যান্ড ইমার্জেন্সি ড্রিল, জাতীয় দক্ষতা উন্নয়ন সপ্তাহ উদযাপন, ইন্ডাস্ট্রিয়াল অ্যাটাচমেন্ট এবং চাকরি মেলা।</p>'
    . '</div>';

$courses_html = '<div class="pew-certificate-card">'
    . '<h2>পিইডব্লিউ ট্রেনিং সেন্টার-এর অনুমোদিত পাঠ্যক্রম</h2>'
    . '<p>আমাদের পাঠ্যক্রম বাংলাদেশ সরকারের অর্থ মন্ত্রণালয় পরিচালিত SICIP প্রকল্প এবং BEIOA ও NSDA-এর স্ট্যান্ডার্ড অনুযায়ী প্রণীত। প্রতিটি কোর্সে তাত্ত্বিক ক্লাসের পাশাপাশি ৮০% সময় ব্যবহারিক ল্যাবে হাতে-কলমে প্রশিক্ষণ দেওয়া হয়।</p>'
    . '<h3>১. ইলেকট্রিক্যাল ইনস্টলেশন এন্ড মেনটেনেন্স (Electrical Installation & Maintenance)</h3>'
    . '<ul><li>পেশাগত স্বাস্থ্য ও সুরক্ষা (Occupational Health & Safety)</li><li>ইলেকট্রিক্যাল হ্যান্ড টুলস এবং মেজারিং ইনস্ট্রুমেন্টের সঠিক ব্যবহার</li><li>আবাসিক ও বাণিজ্যিক ওয়্যারিং ডিজাইন এবং ইনস্টলেশন</li><li>ডিস্ট্রিবিউশন বোর্ড, সার্কিট ব্রেকার, ডিবি ও এসডিবি ওয়্যারিং</li><li>বৈদ্যুতিক মোটর, ম্যাগনেটিক কন্টাক্টর এবং কন্ট্রোল সার্কিট</li><li>আর্থিং, গ্রাউন্ডিং এবং লাইটনিং প্রটেকশন সিস্টেম</li><li>ফল্ট ফাইন্ডিং, টেস্ট ও মেইনটেনেন্স মেথডোলজি</li></ul>'
    . '<h3>২. ওয়েল্ডিং (Welding Technology)</h3>'
    . '<ul><li>ওয়ার্কশপ সুরক্ষা ও ব্যক্তিগত নিরাপত্তা সরঞ্জাম (PPE) ব্যবহার</li><li>শিল্ডেড মেটাল আর্ক ওয়েল্ডিং (SMAW / Stick Welding)</li><li>গ্যাস মেটাল আর্ক ওয়েল্ডিং (GMAW / MIG Welding)</li><li>মেটাল কাটিং, জয়েন্ট প্রিপারেশন (Butt, Lap, Tee, Corner)</li><li>১জি থেকে ৪জি পজিশন ওয়েল্ডিং প্র্যাকটিস</li><li>ওয়েল্ড ডিফেক্ট শনাক্তকরণ, কোয়ালিটি চেকিং ও ফিনিশিং</li></ul>'
    . '</div>';

$discipline_html = '<div class="pew-certificate-card">'
    . '<h2>প্রতিষ্ঠানের নিয়ম কানুন ও আচরণবিধি</h2>'
    . '<p>একটি সুষ্ঠু, শৃঙ্খলাবদ্ধ ও নিরাপদ প্রশিক্ষণ পরিবেশ নিশ্চিত করতে সকল শিক্ষার্থীকে নিম্নলিখিত নিয়মাবলি যথাযথভাবে মেনে চলতে হবে:</p>'
    . '<ol>'
    . '<li><strong>উপস্থিতি:</strong> দৈনিক ভাতা ও কোর্স সম্পন্নতার জন্য ক্লাসে নূন্যতম ৮০% উপস্থিতি বাধ্যতামূলক। অনুপস্থিতির ক্ষেত্রে যথাযথ কারণসহ লিখিত আবেদন করতে হবে।</li>'
    . '<li><strong>সময়নিষ্ঠতা:</strong> প্রতিদিন সকাল ৯:০০ টার মধ্যে প্রতিষ্ঠানে উপস্থিত হতে হবে এবং নির্ধারিত সময় পর্যন্ত ক্লাসে অংশগ্রহণ করতে হবে।</li>'
    . '<li><strong>নিরাপত্তা ও পোশাকবিধি (PPE):</strong> ওয়ার্কশপে প্রবেশের সময় অ্যাপ্রন, সেফটি বুট, নিরাপত্তা চশমা ও প্রয়োজনীয় সুরক্ষামূলক সরঞ্জাম পরিধান বাধ্যতামূলক।</li>'
    . '<li><strong>যন্ত্রপাতির যত্ন:</strong> ল্যাবরেটরি ও ওয়ার্কশপের যন্ত্রপাতি সতর্কতার সাথে ব্যবহার করতে হবে এবং কাজ শেষে নিজ দায়িত্বে গুছিয়ে রাখতে হবে।</li>'
    . '<li><strong>শৃঙ্খলামূলক আচরণ:</strong> প্রতিষ্ঠানে ধূমপান, অসদাচরণ এবং বিশৃঙ্খলা সম্পূর্ণ নিষিদ্ধ। প্রতিষ্ঠানের মর্যাদা অক্ষুণ্ণ রাখা প্রতিটি শিক্ষার্থীর নৈতিক দায়িত্ব।</li>'
    . '</ol>'
    . '</div>';

$documentaries_html = '<div class="pew-certificate-card">'
    . '<h2>ডকুমেন্টারি ও ব্যবহারিক কার্যক্রম</h2>'
    . '<p>পিইডব্লিউ ট্রেনিং সেন্টারের আধুনিক ওয়ার্কশপ, বাস্তবমুখী শিখন পদ্ধতি এবং শিক্ষার্থীদের ব্যবহারিক দক্ষতার চিত্র তুলে ধরা হয়েছে। আমাদের প্রতিটি ট্রেড ল্যাবে আধুনিক যন্ত্রপাতি ও সেফটি ইকুইপমেন্ট সজ্জিত রয়েছে।</p>'
    . '<p><strong>ভিডিও ও অডিও-ভিজ্যুয়াল ল্যাব:</strong> মাল্টিমিডিয়া ক্লাসরুমের মাধ্যমে তত্ত্বীয় জ্ঞান প্রদানের পর সরাসরি ওয়ার্কশপে হাতে-কলমে প্র্যাকটিস করানো হয়। বিস্তারিত ভিডিও ও ডকুমেন্টারি আপডেট প্রতিষ্ঠানের ফেসবুক পেজে নিয়মিত প্রকাশ করা হয়।</p>'
    . '<p>অফিসিয়াল ডকুমেন্টারি ও ভিডিও দেখতে ভিজিট করুন আমাদের <a href="https://www.facebook.com/pewtc" target="_blank" rel="noopener noreferrer">ফেসবুক পেজ</a>।</p>'
    . '</div>';

$library_html = '<div class="pew-certificate-card">'
    . '<h2>প্রযুক্তিগত পাঠাগার ও রিসোর্স সেন্টার</h2>'
    . '<p>শিক্ষার্থীদের আত্মউন্নয়ন ও তাত্ত্বিক জ্ঞান সমৃদ্ধ করার জন্য প্রতিষ্ঠানে একটি সুসজ্জিত প্রযুক্তিগত পাঠাগার রয়েছে। এখানে কারিগরি ট্রেড সহায়িকা, সার্কিট ডায়াগ্রাম বই, জাতীয় বিল্ডিং কোড (BNBC), সেফটি স্ট্যান্ডার্ড নির্দেশিকা এবং আধুনিক প্রযুক্তির রেফারেন্স সামগ্রী সংরক্ষিত রয়েছে।</p>'
    . '<p>শিক্ষার্থীরা ক্লাসের ফাঁকে অথবা ছুটির পর পাঠাগারে অধ্যয়ন ও নোট তৈরি করতে পারেন। এছাড়াও ডিজিটাল রিসোর্স ও অনলাইন কারিগরি কন্টেন্ট পড়ার সুযোগ রয়েছে।</p>'
    . '</div>';

$dormitory_html = '<div class="pew-certificate-card">'
    . '<h2>আবাসন ও হোস্টেল সহায়তা</h2>'
    . '<p>রাজশাহী জেলার প্রত্যন্ত উপজেলা এবং পার্শ্ববর্তী জেলাগুলো (নাটোর, নওগাঁ, চাঁপাইনবাবগঞ্জ, পাবনা প্রভৃতি) থেকে আগত প্রশিক্ষণার্থীদের জন্য প্রতিষ্ঠানের পক্ষ থেকে নিরাপদ ও মানসম্মত আবাসন/মেস খুঁজে পেতে সর্বাত্মক সহায়তা প্রদান করা হয়।</p>'
    . '<p>ছোট বনগ্রাম, চন্দ্রিমা ও সপুরা এলাকায় প্রতিষ্ঠানের ক্যাম্পাসের সন্নিকটেই নিরাপদ আবাসিক মেস ও হোস্টেল সুবিধা বিদ্যমান। আবাসন সহায়তার জন্য ভর্তি অফিসে যোগাযোগ করার পরামর্শ দেওয়া হলো।</p>'
    . '</div>';

$download_html = '<div class="pew-certificate-card">'
    . '<h2>প্রয়োজনীয় ফরম ও ডাউনলোড</h2>'
    . '<p>পিইডব্লিউ ট্রেনিং সেন্টারের ভর্তি ফরম, নির্দেশিকা ও প্রয়োজনীয় নথিপত্র এখান থেকে সংগ্রহ করতে পারবেন:</p>'
    . '<ul>'
    . '<li><strong>ভর্তি আবেদন ফরম (SICIP Trade Course Admission Form):</strong> <a href="' . esc_url( dist_faithful_page_url( 'admission' ) ) . '">ভর্তি ফরম ও নির্দেশনা দেখুন ↗</a></li>'
    . '<li><strong>ট্রেড কোর্স সিলেবাস ও পাঠ্যক্রম সারসংক্ষেপ:</strong> <a href="' . esc_url( dist_faithful_page_url( 'courses' ) ) . '">কোর্স পাঠ্যক্রম দেখুন ↗</a></li>'
    . '<li><strong>অনলাইন সনদ যাচাই নির্দেশিকা:</strong> <a href="' . esc_url( dist_faithful_page_url( 'certificate-verification' ) ) . '">সনদ যাচাইকরণ পোর্টালে যান ↗</a></li>'
    . '</ul>'
    . '<p class="pew-certificate-contact">যেকোনো ফরম সরাসরি অফিস থেকেও বিনামূল্যে সংগ্রহ করা যাবে।</p>'
    . '</div>';

$contact_html = '<div class="pew-certificate-card">'
    . '<h2>যোগাযোগ — পিইডব্লিউ ট্রেনিং সেন্টার</h2>'
    . '<p><strong>PEW Training Center</strong><br>'
    . 'ছোট বনগ্রাম (বার রাস্তার মোড়), চন্দ্রিমা, সপুরা, রাজশাহী ৬২০৩।</p>'
    . '<p><strong>হটলাইন / হোয়াটসঅ্যাপ:</strong> 01342-846300<br>'
    . '<strong>মোবাইল নম্বর:</strong> 01342-846301, 01342-846302<br>'
    . '<strong>ইমেইল:</strong> <a href="mailto:info.pewtc@gmail.com">info.pewtc@gmail.com</a><br>'
    . '<strong>অফিসিয়াল ওয়েবসাইট:</strong> <a href="https://www.pewtc.com/">www.pewtc.com</a><br>'
    . '<strong>অফিসিয়াল ফেসবুক পেজ:</strong> <a href="https://www.facebook.com/pewtc" target="_blank" rel="noopener noreferrer">facebook.com/pewtc</a></p>'
    . '<p><strong>অফিস সময়সূচী:</strong> শনিবার থেকে বৃহস্পতিবার, সকাল ৯:০০ টা থেকে বিকাল ৫:০০ টা পর্যন্ত (শুক্রবার ও সরকারি ছুটির দিন বন্ধ)।</p>'
    . '<p><strong>যাতায়াত নির্দেশনা:</strong> রাজশাহী রেলওয়ে স্টেশন অথবা শিরোইল বাস টার্মিনাল থেকে অটো/ইজিবাইকযোগে মাত্র ১০-১৫ মিনিটে ছোট বনগ্রাম বার রাস্তার মোড়ে আসা যায়।</p>'
    . '</div>';

$pages = array(
    array( 'post_type' => 'page', 'post_name' => 'college_histroy', 'post_title' => 'প্রতিষ্ঠানের সংক্ষিপ্ত ইতিহাস', 'post_content' => $history_html ),
    array( 'post_type' => 'page', 'post_name' => 'history', 'post_title' => 'প্রতিষ্ঠানের ইতিহাস', 'post_content' => $history_html ),
    array( 'post_type' => 'page', 'post_name' => 'principal', 'post_title' => 'অধ্যক্ষের কিছু কথা', 'post_content' => $principal_html ),
    array( 'post_type' => 'page', 'post_name' => 'admission', 'post_title' => 'ভর্তি তথ্য', 'post_content' => $admission_html ),
    array( 'post_type' => 'page', 'post_name' => 'anual_activities', 'post_title' => 'বাৎসরিক কার্যক্রম', 'post_content' => $activities_html ),
    array( 'post_type' => 'page', 'post_name' => 'courses', 'post_title' => 'পাঠ্যক্রম', 'post_content' => $courses_html ),
    array( 'post_type' => 'page', 'post_name' => 'school_courses', 'post_title' => 'কোর্স সমূহ', 'post_content' => $courses_html ),
    array( 'post_type' => 'page', 'post_name' => 'exam_result', 'post_title' => 'পরীক্ষার ফল', 'post_content' => '<div class="pew-certificate-card"><h2>পরীক্ষার ফলাফল ও মূল্যায়ন নির্দেশিকা</h2><p>পিইডব্লিউ ট্রেনিং সেন্টারের তাত্ত্বিক ও ব্যবহারিক পরীক্ষার মূল্যায়ন এনটিভিকিউএফ (NTVQF) ও এসআইসিআইপি (SICIP) এসেসমেন্ট স্ট্যান্ডার্ড অনুযায়ী সম্পন্ন হয়। শিক্ষার্থীরা তাদের অর্জিত স্কিল অনুযায়ী Competent (উত্তীর্ণ) গ্রেড লাভ করেন।</p><p>শিক্ষার্থীদের সনদ যাচাই করতে ভিজিট করুন আমাদের <a href="' . esc_url( dist_faithful_page_url( 'certificate-verification' ) ) . '">অনলাইন সনদ যাচাই পোর্টাল ↗</a>।</p></div>' ),
    array( 'post_type' => 'page', 'post_name' => 'documentaries', 'post_title' => 'ডকুমেন্টারি', 'post_content' => $documentaries_html ),
    array( 'post_type' => 'page', 'post_name' => 'discipline', 'post_title' => 'প্রতিষ্ঠানের নিয়ম কানুন', 'post_content' => $discipline_html ),
    array( 'post_type' => 'page', 'post_name' => 'library', 'post_title' => 'পাঠাগার', 'post_content' => $library_html ),
    array( 'post_type' => 'page', 'post_name' => 'dormitory', 'post_title' => 'ছাত্রাবাস', 'post_content' => $dormitory_html ),
    array( 'post_type' => 'page', 'post_name' => 'ডাউনলোড', 'post_title' => 'প্রয়োজনীয় ডাউনলোড', 'post_content' => $download_html ),
    array( 'post_type' => 'page', 'post_name' => 'library2', 'post_title' => 'লাইব্রেরী', 'post_content' => $library_html ),
    array( 'post_type' => 'page', 'post_name' => 'contact', 'post_title' => 'যোগাযোগ', 'post_content' => $contact_html ),
    array( 'post_type' => 'page', 'post_name' => 'albums', 'post_title' => 'গ্যালারি', 'post_content' => '<p>PEW Training Center training, practical sessions, and activities.</p>' ),
    array( 'post_type' => 'page', 'post_name' => 'certificate-verification', 'post_title' => 'সনদ যাচাই (Certificate Verification)', 'post_content' => '[pew_certificate_verification]' ),
);
foreach ( $pages as $page ) {
    dist_import_upsert( $page );
}

$course_details_image = esc_url( get_template_directory_uri() . '/assets/images/courses/course-details.jpeg' );
$course_poster_html = '<figure class="pew-course-poster" style="margin: 0 0 24px; text-align: center;"><img src="' . $course_details_image . '" alt="PEW Training Center course details poster" style="display: block; width: 100%; max-width: 854px; height: auto; margin: 0 auto;"><figcaption>Course information poster supplied by PEW Training Center.</figcaption></figure>';
$course_facts_html = '<h3>Course details from the supplied poster</h3><table><thead><tr><th>Course</th><th>Duration</th><th>Minimum education</th><th>Age</th></tr></thead><tbody><tr><td>Electrical Installation and Maintenance</td><td>4 months</td><td>Grade 8 pass</td><td>18–45</td></tr><tr><td>Welding</td><td>4 months</td><td>Grade 8 pass</td><td>18–45</td></tr></tbody></table><ul><li>Training is presented as free under the Skills for Industry Competitiveness and Innovation Program (SICIP).</li><li>The poster identifies the Bangladesh Engineering Industry Owners Association (BEIOA) and PEW Training Center, Rajshahi.</li><li>Women, ethnic minorities, persons with disabilities, and disadvantaged applicants receive priority.</li><li>Daily attendance is used for the stated 100 taka travel allowance and 50 taka refreshment allowance.</li><li>Certificates and assistance with employment are provided after training.</li></ul>';
$course_application_html = '<h3>Application documents shown on the poster</h3><ol><li>Two recent passport-size photographs.</li><li>One photocopy of the National ID card or online birth-registration certificate.</li><li>One photocopy of the educational qualification certificate.</li></ol><p><strong>Training center:</strong> Chotobongram (Baro rasta more), Chandrima, Rajshahi.<br><strong>Cell:</strong> 01342-846300, 01342-846301, 01342-846302<br><strong>Email:</strong> <a href="mailto:info.pewtc@gmail.com">info.pewtc@gmail.com</a><br><strong>Web:</strong> <a href="https://www.pewtc.com/">www.pewtc.com</a></p>';
$electrical_html = '<div class="pew-course-page">' . $course_poster_html . '<h2>Electrical Installation and Maintenance</h2><p>This four-month trade course builds practical skills for installing, testing, maintaining, and repairing electrical systems. Electrical skills are important because homes, businesses, factories, agriculture, healthcare, communications, and public services all depend on safe and reliable electricity.</p>' . $course_facts_html . '<h3>Why this course is important</h3><ul><li>It develops hands-on ability with wiring, electrical safety, tools, measurements, fault finding, and maintenance.</li><li>It supports entry-level work in construction, building services, factories, workshops, utilities, and service businesses.</li><li>It creates a foundation for further training in power, electronics, automation, renewable energy, and electrical engineering.</li></ul><h3>Potential job sectors</h3><ul><li>Electrical installation and building maintenance.</li><li>Factories, production plants, workshops, and facility operations.</li><li>Power, utility, generator, solar, and renewable-energy service providers.</li><li>Construction companies, engineering contractors, repair services, and self-employment.</li></ul><h3>Higher study and overseas pathways</h3><p>After gaining practical experience, learners may progress to certificates, diplomas, or degree-level study in electrical engineering, electrical and electronic engineering, power systems, industrial automation, mechatronics, or renewable energy. Overseas admission and employment depend on qualification recognition, English-language ability, work experience, practical assessments, and visa rules, so each institution or employer must be checked before applying.</p>' . $course_application_html . '</div>';
$welding_html = '<div class="pew-course-page">' . $course_poster_html . '<h2>Welding</h2><p>This four-month trade course develops practical skills for joining, fabricating, repairing, and finishing metal components. Welding is important across construction and manufacturing because strong, accurate joints are needed for buildings, machinery, vehicles, structures, workshops, and industrial equipment.</p>' . $course_facts_html . '<h3>Why this course is important</h3><ul><li>It builds a practical foundation in welding safety, equipment handling, joint preparation, metal fabrication, and quality checking.</li><li>It can help learners enter fabrication, construction, manufacturing, maintenance, and repair work.</li><li>It creates a foundation for advanced welding processes, fabrication technology, metallurgy, inspection, and industrial safety.</li></ul><h3>Potential job sectors</h3><ul><li>Metal fabrication shops, structural steel, and construction projects.</li><li>Manufacturing plants, machinery workshops, and industrial maintenance.</li><li>Automotive and vehicle-body repair, agricultural equipment, and general repair services.</li><li>Shipbuilding, heavy engineering, pipeline, and industrial fabrication work where additional certification is required.</li></ul><h3>Higher study and overseas pathways</h3><p>Learners may progress to advanced welding, fabrication, materials, non-destructive testing, industrial safety, or mechanical-technology certificates and diplomas. Overseas study and employment opportunities depend on recognized credentials, practical welding tests, language ability, work experience, safety certification, and the requirements of each country, institution, or employer.</p>' . $course_application_html . '</div>';
$technologies = array(
    'civil-1' => array( 'title' => 'Diploma-in-Civil Engineering', 'content' => '<p>Diploma-in-Civil Engineering</p>' ),
    'electrical-1' => array( 'title' => 'Electrical Installation and Maintenance', 'content' => $electrical_html ),
    'engineering-1' => array( 'title' => 'Diploma-in-Electronics Engineering', 'content' => '<p>Diploma-in-Electronics Engineering</p>' ),
    'mechanical-1' => array( 'title' => 'Welding', 'content' => $welding_html ),
    'computer-1' => array( 'title' => 'Diploma-in-Computer Engineering', 'content' => '<p>Diploma-in-Computer Engineering</p>' ),
    'Diploma-in-Computer-Science' => array( 'title' => 'Diploma-in-Computer-Science', 'content' => '<p>Diploma-in-Computer-Science</p>' ),
    'textile-1' => array( 'title' => 'Diploma-in-Textile Engineering', 'content' => '<p>Diploma-in-Textile Engineering</p>' ),
);
foreach ( $technologies as $slug => $course ) {
    dist_import_upsert( array( 'post_type' => 'page', 'post_name' => $slug, 'post_title' => $course['title'], 'post_content' => $course['content'] ) );
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
