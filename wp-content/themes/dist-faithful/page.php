<?php get_header(); ?>
<?php if ( is_page( 'admin-panel' ) ) : ?>
<main class="pew-admin-page-shell">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php the_content(); ?>
	<?php endwhile; ?>
</main>
<?php else : ?>
<section class="content">
	<div class="leftCon">
		<div class="nodes promoted">
			<?php while ( have_posts() ) : the_post(); ?>
				<div id="node-<?php the_ID(); ?>" <?php post_class( 'node node-type-page' ); ?>><h1><?php the_title(); ?></h1><div class="node-body"><?php the_content(); ?></div></div>
			<?php endwhile; ?>
		</div>
	</div>
	<div class="sidebar"></div>
</section>
<?php endif; ?>
<?php get_footer(); ?>