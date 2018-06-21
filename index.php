

<?php
/**
 * @package WordPress
 * @subpackage Birding Trail Theme
 * @since Birding Trail 0.5
 */
 get_header(); ?>

<!-- <h4><?php echo $pagename;?></h4> pagename troubleshooting -->

<div class="container">
    <?php 
      $sticky = get_option('sticky_posts');
      $featuredID = get_cat_ID('featured');
      $topargs = array(
        'p' => $sticky[0]
      );
      $querytop = new WP_Query($topargs);

    if ($querytop->have_posts()) : while ($querytop->have_posts()) : $querytop->the_post(); 
      ?>

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


<!-- This code is for secondary posts to highlihgt. Could be the two most recent with thumbnails-->
<div class="row mb-2"> <!-- second row with secondary posts, only two featured here -->
  <?php 
    $featuredargs = array(
        'post__not_in' => array($sticky[0]),
        'cat' => $featuredID,
        'posts_per_page' => 2
      );
    $queryfeatured = new WP_Query($featuredargs);
    if ($queryfeatured->have_posts()) : while ($queryfeatured->have_posts()) : $queryfeatured->the_post();
  ?>

  <div class="col-md-6">
    <div class="card mb-4 box-shadow lede-card">
      <div class="card-body d-flex flex-column align-items-start">
        <!-- <strong class="d-inline-block mb-2 text-primary">World</strong> -->
        <h3 class="mb-0">
          <a class="text-featured" href="#"><?php the_title() ?></a>
        </h3>
        <div class="mb-1 text-muted"><?php the_date(); ?></div>
        <p class="card-text mb-auto"><?php the_excerpt(); ?></p>
        <a href="<?php the_permalink(); ?>">Continue reading</a>
      </div>
      <?php if ( has_post_thumbnail() ) : the_post_thumbnail('jumbo-thumb',['class'=>'featured-img']); endif;?>
<!--       <img class="card-img-right flex-auto d-none d-md-block" data-src="holder.js/200x250?theme=thumb" alt="Card image cap"> -->
    </div>
  </div>
<?php 
  endwhile;
  wp_reset_postdata();
  endif;
?>
</div>

    <main role="main" class="container">
      <div class="row">
        <div class="col-md-9 blog-main">
          <h3 class="pb-3 mb-4 font-italic border-bottom">From the Feeder</h3>
      <?php 
        $paged = ( get_query_var('paged')) ? get_query_var( 'paged') :1;
        $mainargs = array(
          'post__not_in' => array($sticky[0]),
          'category__not_in' => array($featuredID),
          'posts_per_page' => 4,
          'paged' => $paged          
        );

        $querymain = new WP_Query($mainargs);

        if ($querymain->have_posts()) : while($querymain->have_posts()) : $querymain->the_post(); 
      ?>
          <div class="blog-post">
            <h2 class="blog-post-title"><?php the_title() ?></h2>
            <p class="blog-post-meta"><?php the_date() ?> by <a href="#"><?php the_author() ?></a></p>

            <?php the_excerpt(); ?>

          </div><!-- /.blog-post -->
        <?php endwhile; ?>

<!--           <nav class="blog-pagination">
            <div class="btn btn-outline-secondary"><?php echo get_previous_posts_link(); ?></div>
            <div class="btn btn-outline-secondary"><?php echo get_next_posts_link(); ?></div>
          </nav>
 -->
      <?php endif; wp_reset_postdata();?>
        </div><!-- /.blog-main -->
      <div class="col-md-3 sidebar"><?php get_sidebar(); ?></div>
      </div> <!-- main blog row container -->
    </main>





<?php get_footer(); ?>
