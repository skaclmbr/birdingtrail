

<?php
/**
 * @package WordPress
 * @subpackage Birding Trail Theme
 * @since Birding Trail 0.5
 */
 get_header(); ?>

<main role="main" class="container">
    <div class="row">
      <div class="col-md-8 blog-main">
        <h3 class="pb-3 mb-4 font-italic border-bottom">From the Feeder</h3>


        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
          <div class="blog-post">
            <!-- <article <?php post_class(); ?> id="post-<?php the_ID(); ?>"> - interesting old article code -->
            <h2 class="blog-post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

            <?php /*posted_on(); not working...*/ ?>

            <div class="entry">
              <?php the_content(); ?>
            </div>

            <footer class="postmetadata">
              <?php the_tags(__('Tags: ','birdingtrail'), ', ', '<br />'); ?>
              <?php _e('Posted in ','birdingtrail'); ?><?php the_category(', '); ?> | 
              <?php comments_popup_link(__('No Comments &#187;','birdingtrail'), __('1 Comment &#187;','birdingtrail'), __('% Comments &#187;','birdingtrail')); ?>
            </footer>

          </div> <!-- blog-post -->

        <?php endwhile; ?>

        <?php the_post_navigation(); ?>

        <?php else : ?>

          <h2><?php _e('Nothing Found','birdingtrail'); ?></h2>

        <?php endif; ?>
      </div> <!-- blog-main col -->
    </div> <!-- row -->
</main> <!-- main container -->
<?php get_sidebar(); ?>

<?php get_footer(); ?>
