<?php

/**
 * Template Name: Front Page
 *
 * The template for the page builder full-width.
 *
*/

get_header(); ?>


<main role="main" class="container">

	<div class="row brand row-pad">
		<div class="col-md-12 center-block"><a href="<?php home_url() ?>"><img class="front-page-logo" src="/wp-content/uploads/2018/04/ncbt_front_page.png"></a></div>
	</div>	
	<div class="row row-pad">
		<div class="col-md-12  lead">
			From our vast coastline to our high mountain peaks, North Carolina is home to diverse people, birds, and natural areas. Our unique place along the Atlantic Coast provides a variety of outdoor experiences.
		</div>
	</div>
	<div class="row row-pad">
		<div class="col-md-12  lead">
			<a href="/sites" class="btn btn-lg btn-secondary">Explore North Carolina</a>
		</div>
	</div>
	<div class="learn-more rounded">
		<div class="row">
			<div class="col-12 lead lead-label">Learn More</div>
		</div>
		<div class="row row-pad">
			<div class="col center-block fp-topic"><a href="/birdwatching"><i class="fp-topic-icon fa fa-binoculars fa-2x"></i></a><p class="fp-label">Birdwatching</p></div>
			<div class="col center-block fp-topic"><a href="/economy"><i class="fp-topic-icon fa fa-usd fa-2x"></i></a><p class="fp-label">Economy</p></div>
			<div class="col center-block fp-topic"><a href="/education"><i class="fp-topic-icon fa fa-leaf fa-2x"></i></a><p class="fp-label">Conservation</p></div>
		</div>
	</div>
</main>

<?php get_footer(); ?>