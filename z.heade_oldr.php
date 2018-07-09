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

    <!--
    <div id="page-container" class="">
    <div class="cover-container d-flex h-100 p-3 mx-auto flex-column">-->
      <header class="container-fluid h-100 mx-auto">
        <!--<div class="inner">-->
          <div class = "row masthead">
            <div class="masthead-brand col-md"> <!-- either logo or name, float left or center -->
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
          <!-- insert menu div -->
          <?php wp_nav_menu( array( 
            'theme_location' => 'header-menu', 
            'menu_class' => 'nav nav-masthead',
            'container_class' => 'menu-main-container col-md'
          ) ); ?>
          <!-- <a class="btn btn-xs btn-outline-secondary" href="#">Sign up</a> FIGURE OUT how to add this button to right -->

        <!-- </div> -->
      </div>
      </header> <!-- end of bs row -->
