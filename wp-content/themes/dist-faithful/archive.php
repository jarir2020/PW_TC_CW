<?php get_header(); ?>
<section class="content">
	<div class="leftCon">
		<div class="nodes promoted">
			<h1><?php the_archive_title(); ?></h1>
			<?php while ( have_posts() ) : the_post(); ?>
				<div id="node-<?php the_ID(); ?>" <?php post_class( 'node' ); ?>><h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1><div class="node-body"><?php the_excerpt(); ?></div></div>
			<?php endwhile; ?>
		</div>
	</div>
	<div class="sidebar"></div>
</section>
<?php get_footer(); ?>
