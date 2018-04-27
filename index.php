

<?php
/**
 * @package WordPress
 * @subpackage Birding Trail Theme
 * @since Birding Trail 0.5
 */
 get_header(); ?>

<!-- <h4><?php echo $pagename;?></h4> pagename troubleshooting -->

<main role="main" class="container-fluid">
    <div class="row"><div class="col-12">  <h3 class="pb-3 mb-4 font-italic border-bottom">From the Feeder</h3></div></div>
    <div class="row">
      <div class="col-9 blog-main">

        <?php if (have_posts()) : while (have_posts()) : the_post(); ?> <!-- loop through posts -->
          <div class="blog-post card flex-md-row mb-4 box-shadow h-md-250"> <!-- blog post container -->
            <div class="card-body d-flex flex-column align-items-start"> <!-- blog post text-->
              <h3 class="mb-0">
                <a class="" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
              </h3>
              <!-- 
              <article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
              <h2 class="blog-post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
              <?php /*posted_on(); not working...*/ ?>
              <div class="entry">
                <?php the_content(); ?>
              </div>
               - interesting old article code -->
              
              <div class="mb-1 text-black-50">Nov 12</div> <!-- POST DATE -->
              <p class="card-text mb-auto"><?php the_content(); ?></p>
              <a href="<?php the_permalink(); ?>">Continue reading</a>


              <!-- old footer info
              <footer class="postmetadata">
                <?php the_tags(__('Tags: ','birdingtrail'), ', ', '<br />'); ?>
                <?php _e('Posted in ','birdingtrail'); ?><?php the_category(', '); ?> | 
                <?php comments_popup_link(__('No Comments &#187;','birdingtrail'), __('1 Comment &#187;','birdingtrail'), __('% Comments &#187;','birdingtrail')); ?>
              </footer>
              end footer info -->


            </div> <!-- blog post text -->
            <img class="card-img-right flex-auto d-none d-md-block img-thumbnail" src="/wp-content/uploads/2018/04/ncbt_logo_grayscale.png" alt="Card image cap">

          </div> <!-- blog post container -->

        <?php endwhile; ?>

        <?php else : ?> <!-- runs if no posts found -->

          <h2><?php _e('Nothing Found','birdingtrail'); ?></h2>

        <?php endif; ?> <!-- end loop -->
      </div> <!-- blog-main col -->
      <div class="col-3"><?php get_sidebar(); ?></div>
    </div> <!-- row -->
</main> <!-- main container -->

<?php get_footer(); ?>
