<?php
/**
 * Fallback template — blog index / home posts list.
 *
 * @package ZonePlay
 */

get_header();
?>
<section class="pt-40 pb-20 px-4 sm:px-6 lg:px-8">
	<div class="max-w-4xl mx-auto">
		<h1 class="text-4xl md:text-5xl font-display font-bold text-zp-darkblue mb-12">
			<?php echo esc_html( is_home() && ! is_front_page() ? get_the_title( get_option( 'page_for_posts' ) ) : __( 'Latest News', 'zoneplay' ) ); ?>
		</h1>

		<?php if ( have_posts() ) : ?>
			<div class="space-y-10">
				<?php while ( have_posts() ) : the_post(); ?>
					<article <?php post_class( 'bg-white rounded-3xl border-4 border-slate-100 p-8 shadow-sm' ); ?>>
						<h2 class="text-2xl font-display font-bold text-zp-darkblue mb-3">
							<a href="<?php the_permalink(); ?>" class="hover:text-zp-red transition-colors"><?php the_title(); ?></a>
						</h2>
						<p class="text-slate-500 font-medium text-sm mb-4"><?php echo esc_html( get_the_date() ); ?></p>
						<div class="text-slate-700 leading-relaxed"><?php the_excerpt(); ?></div>
						<a href="<?php the_permalink(); ?>" class="inline-block mt-4 font-display font-bold text-zp-blue hover:text-zp-red transition-colors">
							<?php esc_html_e( 'Read more', 'zoneplay' ); ?> &rarr;
						</a>
					</article>
				<?php endwhile; ?>
			</div>

			<div class="mt-14 font-display font-bold text-zp-darkblue">
				<?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => __( '&larr; Newer', 'zoneplay' ), 'next_text' => __( 'Older &rarr;', 'zoneplay' ) ) ); ?>
			</div>
		<?php else : ?>
			<p class="text-lg text-slate-600"><?php esc_html_e( 'Nothing here yet.', 'zoneplay' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
