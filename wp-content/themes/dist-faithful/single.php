<?php get_header(); ?>
<section class="content">
	<div class="leftCon">
		<div class="nodes promoted">
			<?php while ( have_posts() ) : the_post(); ?>
				<div id="node-<?php the_ID(); ?>" <?php post_class( 'node' ); ?>><h1><?php the_title(); ?></h1><div class="node-body"><?php the_content(); ?></div></div>
			<?php endwhile; ?>
		</div>
	</div>
	<div class="sidebar"></div>
</section>
<?php get_footer(); ?>
