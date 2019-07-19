<?php

/**
 * Template Name: Front Page
 *
 * The template for the page builder full-width.
 *
*/

get_header(); ?>


<main role="main" class="content container">

	<div class="row brand row-pad">
		<div class="col-md-12 center-block"><img class="front-page-logo" src="/wp-content/uploads/2019/07/ncbt_logo_web_front_page.png"></div>
	</div>	
	<div class="row row-pad">
		<div class="col-md-12  lead">
			<a href="/sites" id="explore-button" class="btn btn-lg btn-secondary">Explore North Carolina</a>
		</div>
	</div>
	<div class="row row-pad">
		<div class="col-md-12  lead">
			From vast coastlines to high mountain peaks, North Carolina is home to diverse people, birds, natural areas, and outdoor experiences.
		</div>
	</div>
	<div class="learn-more rounded">
		<div class="row">
			<div class="col-12 lead lead-label">Learn More</div>
		</div>
		<div class="row row-pad">
			<div class="col center-block fp-topic"><a href="/birdwatching"><i class="fp-topic-icon fa fa-binoculars fa-2x"></i></a><p class="fp-label">Birdwatching</p></div>
			<!-- <div class="col center-block fp-topic"><a href="/economy"><i class="fp-topic-icon fa fa-usd fa-2x"></i></a><p class="fp-label">Economy</p></div> -->
			<div class="col center-block fp-topic"><a href="/conservation"><i class="fp-topic-icon fa fa-leaf fa-2x"></i></a><p class="fp-label">Conservation</p></div>
		</div>
	</div>
</main>



<?php get_footer(); ?>

