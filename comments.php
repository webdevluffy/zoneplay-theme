<?php
/**
 * Comments area — minimal, on-brand. Only rendered by single.php when
 * comments are open.
 *
 * @package ZonePlay
 */

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="mt-16 pt-10 border-t-4 border-slate-100">
	<?php if ( have_comments() ) : ?>
		<h2 class="text-2xl font-display font-bold text-zp-darkblue mb-8">
			<?php
			$zp_count = get_comments_number();
			printf(
				esc_html( _n( '%s Comment', '%s Comments', $zp_count, 'zoneplay' ) ),
				esc_html( number_format_i18n( $zp_count ) )
			);
			?>
		</h2>

		<ol class="space-y-6">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>

		<?php the_comments_pagination(); ?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="text-slate-500 font-medium"><?php esc_html_e( 'Comments are closed.', 'zoneplay' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'class_form'   => 'mt-10 space-y-4',
			'class_submit' => 'bg-zp-yellow hover:bg-zp-orange text-zp-darkblue font-display font-bold px-8 py-3 rounded-full transition-colors',
		)
	);
	?>
</section>
