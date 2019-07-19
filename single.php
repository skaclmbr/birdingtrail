<?php

/**
 * Template Name: birdingtrail
 * @package BirdingTrail
 * @since BirdingTrail 1.0
 *
 * 
 *
 * The template for single posts full-width.
 *
*/

get_header(); ?>

<div class="container content">
	<div class = "col-md-12">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<div class="row blog-post">
			<h3 class="cover-heading display-4 font-italic"><?php the_title() ?></h3>
			<p class="blog-post-meta"><?php the_date() ?> by <?php the_author() ?></p>
		</div>
		<div class="row">
			<div class="col-md-9"><?php the_content() ?></div>
			<div class="col-md-3 sidebar"><?php get_sidebar(); ?></div>
		</div>
	<?php endwhile; endif; ?>
		<nav class="blog-pagination">
	    	
	    	<div class="btn btn-outline-secondary"><?php previous_post_link() ?></div>
	    	<div class="btn btn-outline-secondary"><?php next_post_link() ?></div>
		</nav>
	</div>
</div>


<?php get_footer(); ?>