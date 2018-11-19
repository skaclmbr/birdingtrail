

<?php
/**
 * Template Name: Banner
 *
 * The template for blog posts that discuss the banner content.
 *
 * It contains header, footer and 100% content width.
 *
 * @package birdingtrail
 * @since birdingtrail
 * @author Scott Anderson
 *
 * ADD ENQUEUE SCRIPTS HERE (GMAPS, NCBT ADD DATA)
 */
 get_header(); ?>

<!-- <h4><?php echo $pagename;?></h4> pagename troubleshooting -->

<div class="card-columns">
    <?php 
      $bannerargs = array(
        'category_name' => 'banner',
      );
      $querytop = new WP_Query($topargs);

    if ($querytop->have_posts()) : while ($querytop->have_posts()) : $querytop->the_post(); 
      ?>
    <!-- 
      ADD if statement to format cards based on tags of post:
        - quote (short post with no image)
        - picture (nice picture, with title overlaid - no other text)
        - tidbit (short info with photo)
        - fullpost (excerpt with photo, link to full blog post)
    -->
    <div class="card">
      <img slass="card-img-top" src="" alt="">
      <div class="card-body"> 
        <h5 class="card-title"></h5>
        <p class="card-text">
          
        </p>

      </div>


    </div>
  <div class="jumbotron text-white border rounded bg-dark lede-card row">
    <div class="col-md-6 jumbo-text">

          <h1 class="display-4 font-italic"><?php the_title(); ?></h1>
          <p class="lead my-3"><?php the_excerpt(); ?></p>
          <p class="lead mb-0"><a href="<?php the_permalink(); ?>" class="text-white font-weight-bold">Continue reading...</a></p>
    </div>

    <div class="col-md-6 px-0 thumb-div">
    <?php if ( has_post_thumbnail() ) : the_post_thumbnail('jumbo-thumb',['class' => 'thumb-img']); endif;?><!--put thumbnail here-->
    </div>        
  </div>
    <?php endwhile; ?>
    <?php wp_reset_postdata(); ?>
  <?php endif; ?>


  </div> <!-- main blog row container -->
</main>





<?php get_footer(); ?>
