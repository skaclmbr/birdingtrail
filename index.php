

<?php
/**
 * @package WordPress
 * @subpackage Birding Trail Theme
 * @since Birding Trail 0.5
 */
 get_header(); ?>

<div class="content">
  <div class="card-columns">
<?php 

  
  if (have_posts()) :
?>

    <!-- 
      ADD if statement to format cards based on tags of post:
        - quote (short post with no image) - card text-center
        - picture (nice picture, with title overlaid - no other text)
        - tidbit (short info with photo) - card
        - fullpost (excerpt with photo, link to full blog post) - card
    -->
  <?php
    while (have_posts()) : the_post();
  ?>
      <a href="<?php the_permalink(); ?>">
          <div class="card card-white mb-3">
            <?php if ( has_post_thumbnail() ) : the_post_thumbnail('jumbo-thumb',['class' => 'card-img-top']); endif;?><!--put thumbnail here-->
              <div class="card-title"><?php the_title(); ?></div>
            <div class="card-body"> 
              <p class="card-text"><?php the_excerpt(); ?></p>
            </div>
            <div class="blog-card-category">Category: <?php the_category('&bull;'); ?>  </div>
          </div>
      </a>

  <?php endwhile; ?>

</div> <!-- end card columns -->
<div class="blog-nav"><?php posts_nav_link(); ?></div>    
</div> <!-- end main content-->
<?php else:  ?>
  <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
<?php endif; ?>



<?php get_footer(); ?>
