<?php
/**
 * The template for displaying all single posts and attachments.
 *
 * @package BirdingTrail
 * @since BirdingTrail 1.0
 */

get_header();
?>


<div class="container">

	<div class="row">
		<div class="page-title col-sm-12">
			<h1><?php single_post_title(); ?></h1>
		</div>
	</div>
	<div class="row">
		<div class = "col-sm-12">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) : the_post();
			the_content();
		endwhile;
	endif;
	?>
</div>
</div>
</div>

<?php get_footer(); ?>
