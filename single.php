<?php
/**
 * Single post. Kept simple and on-brand — the site is page-driven; posts
 * are a secondary content type.
 *
 * @package ZonePlay
 */

get_header();
?>
<article <?php post_class( 'pt-40 pb-20 px-4 sm:px-6 lg:px-8' ); ?>>
	<div class="max-w-3xl mx-auto">
		<?php while ( have_posts() ) : the_post(); ?>
			<h1 class="text-4xl md:text-5xl font-display font-bold text-zp-darkblue mb-6"><?php the_title(); ?></h1>
			<p class="text-slate-500 font-medium mb-10"><?php echo esc_html( get_the_date() ); ?></p>
			<div class="zp-entry space-y-6 text-lg text-slate-700 leading-relaxed">
				<?php the_content(); ?>
			</div>
			<?php
			wp_link_pages(
				array(
					'before' => '<nav class="mt-10 font-display font-bold text-zp-blue">' . esc_html__( 'Pages:', 'zoneplay' ),
					'after'  => '</nav>',
				)
			);
			?>
		<?php endwhile; ?>
		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	</div>
</article>
<?php
get_footer();
