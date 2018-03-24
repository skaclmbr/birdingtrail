<!doctype html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <?php wp_head(); ?>
  </head>

  <body <?php body_class(); ?>>

    <div class="bt-container d-flex h-100 mx-auto flex-column">
    <!--<div class="cover-container d-flex h-100 p-3 mx-auto flex-column">-->
      <header class="masthead" id="bt-header">
        <!--<div class="inner">-->
          <div class="masthead-brand">
            <?php
                if (!is_page_template('front-page.php') and has_custom_logo() ) {
                  the_custom_logo();
                } else {
                  echo '<h3>';
                  echo bloginfo('name');
                  echo '</h3>';
                };
            ?>
          </div>
          <?php wp_nav_menu( array( 
            'theme_location' => 'header-menu', 
            'menu_class' => 'nav nav-masthead justify-content-center list-inline' 
          ) ); ?>

        <!-- </div> -->
      </header>
