<?php get_header(); ?>
<section class="content">
	<div class="leftCon">
		<div class="nodes promoted">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<div id="node-<?php the_ID(); ?>" <?php post_class( 'node' ); ?>><h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1><div class="node-body"><?php the_excerpt(); ?></div></div>
			<?php endwhile; else : ?><div class="node"><h1>কোন তথ্য পাওয়া যায়নি</h1></div><?php endif; ?>
		</div>
	</div>
	<div class="sidebar"></div>
</section>
<?php get_footer(); ?>
