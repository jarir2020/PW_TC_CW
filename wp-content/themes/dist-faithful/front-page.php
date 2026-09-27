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
$history_body   = $history_page ? $history_page->post_content : '<h2 style="text-align: center;">PEW Training Center</h2><p style="text-align: justify;"><strong>Skills for Industry Competitiveness and Innovation Program (SICIP)</strong></p><p style="text-align: justify;">PEW Training Center provides a platform for skills development and technical training. Program details, notices, and learner resources will be published here.</p>';
$principal_body = $principal_page ? $principal_page->post_content : '<p style="text-align: justify;"><strong>Welcome to PEW Training Center.</strong></p><p style="text-align: justify;">Skills for Industry Competitiveness and Innovation Program (SICIP)</p><p style="text-align: justify;">Training center announcements and program updates will be shared through this website.</p>';
?>

<section class="content">
	<div class="leftCon">
		<div class="slider_theame">
			<div id="slider" class="nivoSlider">
				<?php
				$slider_images = array( 'company/company-01.jpeg', 'company/company-02.jpeg', 'company/company-03.jpeg', 'company/company-04.jpeg', 'company/company-05.jpeg', 'company/company-06.jpeg', 'company/company-07.jpeg', 'company/company-08.jpeg', 'company/company-09.jpeg', 'company/company-10.jpeg' );
				foreach ( $slider_images as $index => $slider_image ) :
					?><img src="<?php echo esc_url( dist_faithful_asset( 'images/' . $slider_image ) ); ?>" title="<?php echo 0 === $index ? 'Pew Training Center' : ''; ?>" alt="Pew Training Center"><?php
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
			<li><a href="http://www.moedu.gov.bd/" id="link-88" target="_blank"><span>শিক্ষা মন্ত্রনালয়</span></a></li><li><a href="http://www.teachers.gov.bd/" id="link-89" target="_blank"><span>শিক্ষক বাতায়ন</span></a></li><li><a href="https://techedu.gov.bd/" id="link-67" target="_blank"><span>কারিগরি শিক্ষা অধিদপ্তর</span></a></li><li><a href="http://www.nctb.gov.bd" id="link-101" target="_blank"><span>জাতীয় শিক্ষাক্রম ও পাঠ্যপুস্তক বোর্ড</span></a></li><li><a href="http://www.bangladesh.gov.bd" id="link-102" target="_blank"><span>বাংলাদেশ জাতীয় তথ্য বাতায়ন</span></a></li><li><a href="<?php echo esc_url( dist_faithful_page_url( 'student-results' ) ); ?>" id="link-106" target="_blank"><span>PEW Training Center-এর সকল ফলাফল অনুসন্ধান</span></a></li><li><a href="http://www.bteb.gov.bd/" id="link-107" target="_blank"><span>কারিগরি শিক্ষা বোর্ড, ঢাকা।</span></a></li><li><a href="https://www.facebook.com/" id="link-132" target="_blank"><span>PEW Training Center-এর অফিসিয়াল ফেসবুক পেজ</span></a></li><li><a href="http://mmc.e-service.gov.bd/" id="link-133" target="_blank"><span>মাল্টিমিডিয়া ক্লাসরুম ম্যানেজমেন্ট সিস্টেম</span></a></li><li><a href="http://step-dte.gov.bd/" id="link-149"><span>STEP</span></a></li><li><a href="https://rajshahi.gov.bd/" id="link-134" target="_blank"><span>জেলা প্রশাসন, রাজশাহী।</span></a></li>
		</ul></div></div></div>

		<div class="sidebarComn" style=" width:68%; height:160px; margin-bottom:10px; overflow:hidden; text-align:center; float:left"><iframe src="https://www.google.com/maps?q=PEW+Training+Center,+Chotobongram,+Chandrima,+Rajshahi&output=embed" width="280" height="200" frameborder="0" style="border:0" allowfullscreen></iframe></div>
		<div class="sidebarComn" style="height:160px; width:28%;margin-bottom:10px; text-align:center; float:right"><a href="http://www.hitwebcounter.com" target="_blank"><img src="http://hitwebcounter.com/counter/counter.php?page=6465876&amp;style=0006&amp;nbdigits=7&amp;type=page&amp;initCount=50" title="install tracking codes" alt="install tracking codes" border="0"></a><div style="font-size:.8em; color:#666666; text-align:left; margin-left:17px">Total Hits</div></div>
	</div>
</section>

<?php get_footer(); ?>
