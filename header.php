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

    <div class="cover-container d-flex h-100 mx-auto flex-column">
    <!--<div class="cover-container d-flex h-100 p-3 mx-auto flex-column">-->
      <header class="masthead mb-auto">
        <div class="inner">

          <a href="../"><h3 class="masthead-brand"><?php bloginfo('name');?></h3></a>
          <!--<a href="<?php get_bloginfo('url'); ?>"><img class="header_logo" src="/wp-content/themes/birdingtrail/img/header_logo.png"></a>-->
          <?php wp_nav_menu( array( 
            'theme_location' => 'header-menu', 
            'menu_class' => 'nav nav-masthead justify-content-center list-inline' 
          ) ); ?>

        </div>
      </header>
