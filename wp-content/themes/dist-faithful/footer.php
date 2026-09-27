<?php if ( ! is_page( 'admin-panel' ) ) : ?>
<footer class="footer">
	<div class="footer-logo"><img src="<?php echo esc_url( dist_faithful_asset( 'images/company/company-01.jpeg' ) ); ?>" alt="PEW Training Center" style="display:block; width:120px; height:98px; object-fit:contain;"></div>
	<div class="ourAddress"><h4>Our Address</h4><address>PEW Training Center</address><p>Chotobongram (Baro rasta more), Chandrima, Rajshahi.<br><strong>Tel:&nbsp;</strong>01342-846300, 01342-846301, 01342-846302<br><strong>Email:&nbsp;</strong><a href="mailto:info.pewtc@gmail.com">info.pewtc@gmail.com</a><br><strong>Web:&nbsp;</strong><a href="https://www.pewtc.com/" target="_blank" rel="noopener noreferrer">www.pewtc.com</a><br></p></div>
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
