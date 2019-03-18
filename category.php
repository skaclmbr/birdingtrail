

<?php
/**
 * Template Name: Category
 *
 * The template for blog posts category - will be used to discuss the banner content.
 *   LATER: we can modify this to be customized for a specific category (e.g., category-birdwatching.php)
 * It contains header, footer and 100% content width.
 *
 * @package birdingtrail
 * @since birdingtrail
 * @author Scott Anderson
 *
 */
 get_header(); ?>

<!-- <h4><?php echo $pagename;?></h4> pagename troubleshooting -->

<div class="card-columns">
    <?php 

    if (have_posts()) : while (have_posts()) : the_post(); 
      ?>
    <!-- 
      ADD if statement to format cards based on tags of post:
        - quote (short post with no image) - card text-center
        - picture (nice picture, with title overlaid - no other text)
        - tidbit (short info with photo) - card
        - fullpost (excerpt with photo, link to full blog post) - card
    -->
    <a href="<?php the_permalink(); ?>">
<!--       <div class="col-lg-4 col-sm-6 mb-4"> -->
<!--         <div class="card bg-secondary text-white"> -->
        <div class="card bg-secondary text-white mb-3">
          <!--<img slass="card-img-top" src="" alt=""> -->
            <h5 class="card-title"><?php the_title(); ?></h5>
          <?php if ( has_post_thumbnail() ) : the_post_thumbnail('jumbo-thumb',['class' => 'card-img-top']); endif;?><!--put thumbnail here-->
          <div class="card-body"> 
            <p class="card-text"><?php the_excerpt(); ?></p>
          </div>
        </div>        
<!--       </div> -->
    </a>

    <?php endwhile; ?>
  <?php endif; ?>

</div> <!-- end card columns -->

</main>





<?php get_footer(); ?>
