<?php
/**
 * Search results.
 *
 * @package ZonePlay
 */

get_header();
?>
<section class="pt-40 pb-20 px-4 sm:px-6 lg:px-8">
	<div class="max-w-4xl mx-auto">
		<h1 class="text-3xl md:text-4xl font-display font-bold text-zp-darkblue mb-10">
			<?php
			/* translators: %s: search query. */
			printf( esc_html__( 'Search results for &ldquo;%s&rdquo;', 'zoneplay' ), '<span class="text-zp-red">' . esc_html( get_search_query() ) . '</span>' );
			?>
		</h1>

		<?php if ( have_posts() ) : ?>
			<ul class="space-y-6">
				<?php while ( have_posts() ) : the_post(); ?>
					<li class="bg-white rounded-2xl border-4 border-slate-100 p-6 shadow-sm">
						<a href="<?php the_permalink(); ?>" class="font-display font-bold text-xl text-zp-darkblue hover:text-zp-red transition-colors"><?php the_title(); ?></a>
						<div class="text-slate-600 mt-2 leading-relaxed"><?php the_excerpt(); ?></div>
					</li>
				<?php endwhile; ?>
			</ul>
			<div class="mt-12 font-display font-bold text-zp-darkblue"><?php the_posts_pagination(); ?></div>
		<?php else : ?>
			<p class="text-lg text-slate-600"><?php esc_html_e( 'No results. Try a different search.', 'zoneplay' ); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
