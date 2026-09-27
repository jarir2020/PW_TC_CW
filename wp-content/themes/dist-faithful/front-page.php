<?php
get_header();

if ( ! function_exists( 'dist_faithful_home_accordion' ) ) {
	function dist_faithful_home_accordion( $id, $class, $title, $post_type, $group, $archive_path, $open = false ) {
		$posts = dist_faithful_posts( $post_type, 5, $group );
		?>
		<div id="<?php echo esc_attr( $id ); ?>" class="block <?php echo esc_attr( $class ); ?> accordianIn">
			<h3><?php echo esc_html( $title ); ?></h3>
			<a href="javascript:void(0);" class="btnOpen<?php echo $open ? ' btnClose' : ''; ?>">&nbsp;</a>
			<div class="block-body accordianOpen">
				<ul>
				<?php foreach ( $posts as $post ) : ?><li><a href="<?php echo esc_url( dist_faithful_post_url( $post->ID ) ); ?>"><?php echo dist_faithful_date_box( $post->ID ); ?><?php echo esc_html( get_the_title( $post->ID ) ); ?></a></li><?php endforeach; ?>
				</ul>
				<?php if ( $archive_path ) : ?><div class="lwrLink"><a href="<?php echo esc_url( dist_faithful_url( $archive_path ) ); ?>">সবগুলো জানতে »</a></div><?php endif; ?>
			</div>
		</div>
		<?php
	}
}

$history_page   = get_page_by_path( 'college_histroy' );
$principal_page = get_page_by_path( 'principal' );
$history_body   = $history_page ? $history_page->post_content : '<h2 style="text-align: center;"><span style="color: #0000ff;">Dinajpur Institute of Science and Technology (DIST) (Institute Code: 13145)</span></h2><h2 style="text-align: center;"><span style="color: #0000ff;"><strong>Approved By:</strong></span> <img style="display: block; margin-left: auto; margin-right: auto;" src="' . esc_url( dist_faithful_asset( 'uploads/dist.jpg' ) ) . '" alt="" width="487" height="173"></h2><p style="text-align: justify;">উত্তরের জেলা দিনাজপুরের কৃষি নির্ভর অনগ্রসর জনগোষ্ঠির একটা বড় অংশ যাদের জীবন মান উন্নয়নে শুধু চাকুরী নয় ব্যক্তি উদ্যাক্তা হিসেবে গড়ে তুলতে কারিগরি শিক্ষার কোন বিকল্প নেই। কারিগরি শিক্ষার প্রয়োজনীয়তা বিবেচনায় রেখেই ২০০৯ সালে বাংলাদেশ কারিগরি শিক্ষাবোর্ডের অনুমোদন নিয়ে ব্যাক্তি উদ্যোগে দিনাজপুর শহরের প্রাণ কেন্দ্রে দিনাজপুর ইনস্টিটিউট অব সাইন্স এ্যান্ড টেকনোলজি (DIST) প্রতিষ্ঠিত হয়। ২০১০-১১ শিক্ষাবর্ষে চার বছর মেয়াদী ডিপ্লোমা-ইন-ইঞ্জিনিয়ারং কোর্সে দুটি টেকনোলজিতে ৭৫ জন শিক্ষার্থী নিয়ে পথ চলা শুরু করে। বর্তমানে এই প্রতিষ্ঠানে মোট টেকনোলজির সংখ্যা ৮টি এবং শিক্ষার্থীর সংখ্যা প্রায় ২০০০ জন। গণপ্রজাতন্ত্রী বাংলাদেশ সরকারের শিক্ষা মন্ত্রণালয় এর কারিগরি ও মাদ্রাসা শিক্ষা বিভাগের অধীন কারিগরি শিক্ষা অধিদপ্তর এর তত্তাবধানে প্রতিষ্ঠানটি পরিচালিত হয়ে থাকে এবং প্রতিষ্ঠানটির একাডেমিক দিকটি বাংলাদেশ কারিগরি শিক্ষা বোর্ড কর্তৃক নিয়ন্ত্রিত হয়ে থাকে।</p>';
$principal_body = $principal_page ? $principal_page->post_content : '<p style="text-align: justify;">Dinajpur Institute Of Science &amp; Technology<br><br>দিনাজপুর ইন্সটিটিউট অফ সাইন্স এন্ড টেকনোলজি(DIST)<br><br>বর্তমান যুগ তথ্য ও প্রযুক্তির যুগ। সমাজ তথা রাষ্ট্রীয় জীবনের প্রতিটি ক্ষেত্রে উন্নয়নের লক্ষ্যে তথ্য ও প্রযুক্তির বিকল্প নেই।শিক্ষা প্রতিষ্ঠানের কার্যক্রমকে আধুনিক ও গতিশীল করার জন্য তথ্য ও প্রযুক্তির ব্যবহার অনস্বীকার্য। শিক্ষা বিষয়ক কার্যক্রমকে গতিশীল ও স্বচ্ছ করার প্রয়োজনে অনলাইন (online) কার্যক্রমের সফল বাস্তবায়নের লক্ষ্যে প্রতিষ্ঠানের&nbsp; ওয়েব সাইট স্থাপন ও ব্যবহার যেমন শিক্ষা ব্যবস্থাকে গতিশীল করে তুলবে তেমনি ডিজিটাল বাংলাদেশ গড়ার ক্ষেত্রে ও গুরুত্বপূর্ণ ভূমিকা পালন করবে এবং ভিশন, ২০২১ বাস্তবায়নে সূদুর প্রসারী প্রভাব রাখবে বলে আমি সর্বান্তকরনে বিশ্বাস করি ।</p><address style="text-align: right;">অধ্যক্ষ</address><address style="text-align: right;">Dinajpur Institute Of Science &amp; Technology<br></address><address style="text-align: right;">দিনাজপুর ইন্সটিটিউট অফ সাইন্স এন্ড টেকনোলজি(DIST)</address>';
?>

<section class="content">
	<div class="leftCon">
		<div class="slider_theame">
			<div id="slider" class="nivoSlider">
				<?php
				$slider_images = array( '88ce0a0baeb96656bcc55542faf46995.jpg', 'af0af28ce12202d45dc06df1ec4d1c9e.jpg', '1993a3e6ed486d87fa7f3e656aa49772.jpg', 'c47c350c036fb51eb71e99e0ac1b842f.jpg', '4533466e77d9353d69088103e7b4dec2.jpg', '3f1c9d077a0b80a542c9f5d7c4cf6335.jpg', '59cdf6a5c4109ec66807e1274a1a3eed.jpg', 'bbc2fe5a6342d81c10f420b04cc716f7.jpg', '454624074e4a1d5a1288fb414c9f1290.jpg', '89e3cf69a42ab579b1719d32de8cf2ff.jpg', '649f4105f179a384d65b64014061f5eb.jpg', '66b8c5ecb12659aac4f5c3e1412622fe.jpg' );
				foreach ( $slider_images as $index => $slider_image ) :
					?><img src="<?php echo esc_url( dist_faithful_asset( 'images/gallery/large/' . $slider_image ) ); ?>" title="<?php echo 0 === $index ? 'DIST' : ''; ?>" alt=""><?php
				endforeach;
				?>
			</div>
		</div><!-- End of slider_theame -->

		<div class="boxcontainer">
			<p><a href="<?php echo esc_url( dist_faithful_page_url( 'civil-1' ) ); ?>">civil</a><a href="<?php echo esc_url( dist_faithful_page_url( 'electrical-1' ) ); ?>">electrical</a><a href="<?php echo esc_url( dist_faithful_page_url( 'engineering-1' ) ); ?>">engineering</a><a href="<?php echo esc_url( dist_faithful_page_url( 'mechanical-1' ) ); ?>">mechanical</a><a href="<?php echo esc_url( dist_faithful_page_url( 'computer-1' ) ); ?>">computer</a><a href="#"></a><a href="<?php echo esc_url( dist_faithful_page_url( 'textile-1' ) ); ?>" style="margin-right:0px">textile</a></p>
		</div>

		<div class="nodes promoted">
			<div id="node-349" class="node node-type-page"><h1><a href="<?php echo esc_url( dist_faithful_page_url( 'college_histroy' ) ); ?>">কলেজ এর সংক্ষিপ্ত ইতিহাস</a></h1><div class="node-body"><?php echo wp_kses_post( $history_body ); ?></div></div>
			<div id="node-302" class="node node-type-page"><h1><a href="<?php echo esc_url( dist_faithful_page_url( 'principal' ) ); ?>">অধ্যক্ষের কিছু কথা </a></h1><div class="node-body"><?php echo wp_kses_post( $principal_body ); ?></div></div>
			<div class="paging"></div>
		</div>
	</div>

	<div class="sidebar">
		<div class="accordian">
			<?php dist_faithful_home_accordion( 'block-17', 'block-notice', 'নোটিশ বোর্ড', 'pew_notice', '', 'notice', true ); ?>
			<?php dist_faithful_home_accordion( 'block-21', 'block-class_routine', 'ক্লাস রুটিন', 'pew_document', 'class_routine', 'class_routine' ); ?>
			<?php dist_faithful_home_accordion( 'block-20', 'block-syllabus', 'সিলেবাস', 'pew_document', 'syllabus', '' ); ?>
			<?php dist_faithful_home_accordion( 'block-18', 'block-exam_routine', 'পরীক্ষার রুটিন', 'pew_document', 'exam_routine', 'exam_routine' ); ?>
			<?php dist_faithful_home_accordion( 'block-19', 'block-result', 'রেজাল্ট', 'pew_result', 'result', 'result' ); ?>
		</div>

		<div class="sidebarComn"><h2>দিনপুঞ্জি</h2><?php dist_faithful_calendar(); ?></div>
		<div class="sidebarComn attChart" style="display:none;"><h2>Attendence</h2><div class="upperDiv"><div style="float: left; width:50%"><p>Total Student <b> 25 </b></p><p>Today Present <b> 0 </b></p><p>Yesterday Present <b> 0 </b></p></br></br><a href="<?php echo esc_url( dist_faithful_url( 'attendences/student_attendence' ) ); ?>">Student Attendence</a></br><a href="<?php echo esc_url( dist_faithful_url( 'attendences/school_attendence' ) ); ?>">Ins. Attendence</a></div><div id="chartContainer" style="height: 250px; width: 50%; float: right;"></div><div class="clear">&nbsp;</div></div></div>

		<div class="sidebarComn"><h2>গুরুত্বপূর্ণ লিঙ্ক</h2><div id="block-6" class="block block-blogroll"><div class="block-body"><ul class="menu">
			<li><a href="http://www.moedu.gov.bd/" id="link-88" target="_blank"><span>শিক্ষা মন্ত্রনালয়</span></a></li><li><a href="http://www.teachers.gov.bd/" id="link-89" target="_blank"><span>শিক্ষক বাতায়ন</span></a></li><li><a href="https://techedu.gov.bd/" id="link-67" target="_blank"><span>কারিগরি শিক্ষা অধিদপ্তর</span></a></li><li><a href="http://www.nctb.gov.bd" id="link-101" target="_blank"><span>জাতীয় শিক্ষাক্রম ও পাঠ্যপুস্তক বোর্ড</span></a></li><li><a href="http://www.bangladesh.gov.bd" id="link-102" target="_blank"><span>বাংলাদেশ জাতীয় তথ্য বাতায়ন</span></a></li><li><a href="<?php echo esc_url( dist_faithful_page_url( 'student-results' ) ); ?>" id="link-106" target="_blank"><span>দিনাজপুর ইন্সটিটিউট অফ সাইন্স এন্ড টেকনোলজির সকল রেজাল্ট অনুসন্ধান</span></a></li><li><a href="http://www.bteb.gov.bd/" id="link-107" target="_blank"><span>কারিগরি শিক্ষা বোর্ড, ঢাকা।</span></a></li><li><a href="https://www.facebook.com/distdinajpur" id="link-132" target="_blank"><span>দিনাজপুর ইন্সটিটিউট অফ সাইন্স এন্ড টেকনোলজি এর অফিসিয়াল ফেসবুক পেজ</span></a></li><li><a href="http://mmc.e-service.gov.bd/" id="link-133" target="_blank"><span>মাল্টিমিডিয়া ক্লাসরুম ম্যানেজমেন্ট সিস্টেম</span></a></li><li><a href="http://step-dte.gov.bd/" id="link-149"><span>STEP</span></a></li><li><a href="http://www.dinajpur.gov.bd/" id="link-134" target="_blank"><span>জেলা প্রশাসন , দিনাজপুর।</span></a></li>
		</ul></div></div></div>

		<div class="sidebarComn" style=" width:68%; height:160px; margin-bottom:10px; overflow:hidden; text-align:center; float:left"><iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d28775.298209072233!2d88.61895911737165!3d25.64103684513969!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39fb4d67ac34f183%3A0x55200e9fc0060baf!2sDinajpur+Institute+of+Science+%26+Technology+(DIST)!5e0!3m2!1sen!2sbd!4v1471166625411" width="280" height="200" frameborder="0" style="border:0" allowfullscreen></iframe></div>
		<div class="sidebarComn" style="height:160px; width:28%;margin-bottom:10px; text-align:center; float:right"><a href="http://www.hitwebcounter.com" target="_blank"><img src="http://hitwebcounter.com/counter/counter.php?page=6465876&amp;style=0006&amp;nbdigits=7&amp;type=page&amp;initCount=50" title="install tracking codes" alt="install tracking codes" border="0"></a><div style="font-size:.8em; color:#666666; text-align:left; margin-left:17px">Total Hits</div></div>
	</div>
</section>

<?php get_footer(); ?>
