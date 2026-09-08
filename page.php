<?php
/**
 * Page template. Page bodies are authored with the Editable HTML Block
 * plugin, so the entry content is rendered full-width with no theme chrome
 * around it — each block carries its own section layout and top spacing to
 * clear the fixed header (as in the Astro build).
 *
 * @package ZonePlay
 */

get_header();

while ( have_posts() ) :
	the_post();
	the_content();
endwhile;

get_footer();
