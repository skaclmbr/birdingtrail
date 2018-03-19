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

    <div class="cover-container d-flex h-100 p-3 mx-auto flex-column">
      <header class="masthead mb-auto">
        <div class="inner">

          <h3 class="masthead-brand"><?php bloginfo('name'); ?></h3>
          <?php wp_nav_menu( array( 
            'theme_location' => 'header-menu', 
            'menu_class' => 'nav nav-masthead justify-content-center list-inline' 
          ) ); ?>
          <!-- STOCK BOOTSTRAP NAV
          <nav class="nav nav-masthead justify-content-center">
            <a class="nav-link active" href="#">Home</a>
            <a class="nav-link" href="#">Features</a>
            <a class="nav-link" href="#">Contact</a>
          </nav>
        -->
        </div>
      </header>
