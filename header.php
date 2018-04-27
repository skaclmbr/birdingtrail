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

  <div class="container-fluid mx-auto"> <!-- header container -->
      <div class = "row masthead"> <!-- row container -->
        <div class="masthead-brand col-xs-6"> <!-- either logo or name, float left or center -->
          <?php
              if (!is_page_template('front-page.php') and has_custom_logo() ) {
                the_custom_logo();
              } else {
                echo '<h4>';
                echo bloginfo('name');
                echo '</h4>';
              };
          ?>
        </div>
      <!-- insert menu div -->
      <?php wp_nav_menu( array( 
        'theme_location' => 'header-menu', 
        'menu_class' => 'nav nav-masthead',
        'container_class' => 'menu-main-container col-xs-6'
      ) ); ?>

      </div>
  </div> <!-- header container -->
