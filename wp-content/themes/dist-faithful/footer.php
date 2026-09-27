<?php if ( ! is_page( 'admin-panel' ) ) : ?>
<footer class="footer">
	<div class="footer-logo"><img src="<?php echo esc_url( dist_faithful_asset( 'images/company/company-01.jpeg' ) ); ?>" alt="PEW Training Center" style="display:block; width:120px; height:98px; object-fit:contain;"></div>
	<div class="ourAddress"><h4>Our Address</h4><address>PEW Training Center</address><p>Chotobongram (Baro rasta more), Chandrima, Rajshahi.<br><strong>Tel:&nbsp;</strong>01342-846300, 01342-846301, 01342-846302<br><strong>Email:&nbsp;</strong><a href="mailto:info.pewtc@gmail.com">info.pewtc@gmail.com</a><br><strong>Web:&nbsp;</strong><a href="https://www.pewtc.com/" target="_blank" rel="noopener noreferrer">www.pewtc.com</a><br></p></div>
	<ul class="menu"><li><a href="<?php echo esc_url( dist_faithful_page_url( 'electrical-1' ) ); ?>"><span>Electrical</span></a></li><li class="course-hidden"><a href="<?php echo esc_url( dist_faithful_page_url( 'engineering-1' ) ); ?>"><span>ইলেক্ট্রনিক্স ইঞ্জিনিয়ারিং বিভাগ</span></a></li><li><a href="<?php echo esc_url( dist_faithful_page_url( 'mechanical-1' ) ); ?>"><span>Welding</span></a></li><li class="course-hidden"><a href="<?php echo esc_url( dist_faithful_page_url( 'computer-1' ) ); ?>"><span>কম্পিউটার ইঞ্জিনিয়ারিং বিভাগ</span></a></li><li class="course-hidden"><a href="#"><span>ফ্যাশান ডিজাইন ইঞ্জিনিয়ারিং বিভাগ</span></a></li><li class="course-hidden"><a href="<?php echo esc_url( dist_faithful_page_url( 'textile-1' ) ); ?>"><span>টেক্সটাইল ইঞ্জিনিয়ারিং বিভাগ</span></a></li><li class="course-hidden"><a href="<?php echo esc_url( dist_faithful_page_url( 'civil-1' ) ); ?>"><span>সিভিল ইঞ্জিনিয়ারিং বিভাগ</span></a></li></ul>
	<a href="http://tech-plexus.com/" class="ftrLogo" target="_blank"><img src="<?php echo esc_url( dist_faithful_asset( 'uploads/ftrlogo.png' ) ); ?>" alt="TechPlexus Ltd."></a>
</footer>

<?php endif; ?>
<script>
jQuery(document).ready(function($) {
	if (typeof $.fn.fancybox === 'function') {
		$('.fancybox').fancybox({
			openEffect: 'elastic',
			closeEffect: 'elastic',
			helpers: {
				title: { type: 'inside' }
			}
		});
	}
});
</script>
<?php wp_footer(); ?>
</body>
</html>
