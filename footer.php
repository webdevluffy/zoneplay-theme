<?php
/**
 * Site footer — brand blurb, opening times, contact, legal row.
 *
 * The brand blurb, social links, opening times and Find Us details are all
 * editable in the Customizer under "Footer Content" (see inc/customizer.php);
 * this template only renders them. The footer logo reuses the Site Identity
 * logo and the legal-row links come from the "legal" menu location
 * ("Footer Menu").
 *
 * @package ZonePlay
 */

$zp_heading    = trim( zp_footer_mod( 'zp_footer_heading' ) );
$zp_blurb      = trim( zp_footer_mod( 'zp_footer_blurb' ) );
$zp_facebook   = trim( zp_footer_mod( 'zp_footer_facebook' ) );
$zp_instagram  = trim( zp_footer_mod( 'zp_footer_instagram' ) );
$zp_hours_note = trim( zp_footer_mod( 'zp_footer_hours_note' ) );
$zp_address    = trim( zp_footer_mod( 'zp_footer_address' ) );
$zp_email      = trim( zp_footer_mod( 'zp_footer_email' ) );
$zp_phone      = trim( zp_footer_mod( 'zp_footer_phone' ) );
$zp_tel        = preg_replace( '/[^0-9+]/', '', $zp_phone );

// Opening times: one "Day | Hours" row per line; "closed" hours render red.
$zp_hours = array();
foreach ( preg_split( '/\r\n|\r|\n/', zp_footer_mod( 'zp_footer_hours' ) ) as $zp_line ) {
	$zp_line = trim( $zp_line );
	if ( '' === $zp_line ) {
		continue;
	}
	$zp_parts   = array_map( 'trim', explode( '|', $zp_line, 2 ) );
	$zp_row_hrs = isset( $zp_parts[1] ) ? $zp_parts[1] : '';
	$zp_hours[] = array(
		'day'    => $zp_parts[0],
		'hours'  => $zp_row_hrs,
		'closed' => ( '' !== $zp_row_hrs && false !== stripos( $zp_row_hrs, 'closed' ) ),
	);
}
?>
</main>

<footer class="bg-zp-darkblue text-white pt-20 pb-10">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">

			<div class="lg:col-span-2">
				<?php zp_logo_img( 'h-20 w-auto mb-6 drop-shadow-lg', array( 'loading' => 'lazy' ) ); ?>
				<?php if ( '' !== $zp_heading ) : ?>
					<h2 class="font-display font-bold text-xl text-white mb-3"><?php echo esc_html( $zp_heading ); ?></h2>
				<?php endif; ?>
				<?php if ( '' !== $zp_blurb ) : ?>
					<p class="text-blue-200 font-medium text-base max-w-md mb-8 leading-relaxed"><?php echo nl2br( esc_html( $zp_blurb ) ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== $zp_facebook || '' !== $zp_instagram ) : ?>
					<div class="flex gap-3">
						<?php if ( '' !== $zp_facebook ) : ?>
							<a href="<?php echo esc_url( $zp_facebook ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' on Facebook' ); ?>" class="w-11 h-11 rounded-full bg-white/10 flex items-center justify-center hover:bg-[#1877F2] transition-colors">
								<svg class="w-5 h-5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" /></svg>
							</a>
						<?php endif; ?>
						<?php if ( '' !== $zp_instagram ) : ?>
							<a href="<?php echo esc_url( $zp_instagram ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' on Instagram' ); ?>" class="w-11 h-11 rounded-full bg-white/10 flex items-center justify-center hover:bg-zp-pink transition-colors">
								<svg class="w-5 h-5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="20" x="2" y="2" rx="5" ry="5" /><path d="M16 11.37A4 4 0 1 1 12.63 8A4 4 0 0 1 16 11.37m1.5-4.87h.01" /></svg>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<div>
				<h2 class="font-display font-bold text-xl mb-6 text-zp-yellow">Opening Times</h2>
				<?php if ( ! empty( $zp_hours ) ) : ?>
					<ul class="space-y-1 mb-4">
						<?php foreach ( $zp_hours as $row ) : ?>
							<li class="py-2.5 border-b border-white/10 last:border-0">
								<span class="block text-blue-200 font-medium text-sm"><?php echo esc_html( $row['day'] ); ?></span>
								<span class="font-bold text-base <?php echo $row['closed'] ? 'text-red-400' : 'text-white'; ?>"><?php echo esc_html( $row['hours'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php if ( '' !== $zp_hours_note ) : ?>
					<p class="text-blue-300 text-sm font-medium leading-relaxed"><?php echo esc_html( $zp_hours_note ); ?></p>
				<?php endif; ?>
			</div>

			<div>
				<h2 class="font-display font-bold text-xl mb-6 text-zp-green">Find Us</h2>
				<ul class="space-y-4 text-blue-200 font-medium text-sm mb-8">
					<?php if ( '' !== $zp_address ) : ?>
						<li class="flex items-start gap-3">
							<svg class="w-5 h-5 text-zp-yellow shrink-0 mt-0.5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0" /><circle cx="12" cy="10" r="3" /></svg>
							<address class="not-italic leading-relaxed"><?php echo nl2br( esc_html( $zp_address ) ); ?></address>
						</li>
					<?php endif; ?>
					<?php if ( '' !== $zp_email ) : ?>
						<li>
							<a href="mailto:<?php echo esc_attr( $zp_email ); ?>" class="flex items-center gap-3 hover:text-white transition-colors break-all">
								<svg class="w-5 h-5 text-zp-yellow shrink-0" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m22 7l-8.991 5.727a2 2 0 0 1-2.009 0L2 7" /><rect width="20" height="16" x="2" y="4" rx="2" /></svg>
								<?php echo esc_html( $zp_email ); ?>
							</a>
						</li>
					<?php endif; ?>
					<?php if ( '' !== $zp_phone ) : ?>
						<li>
							<a href="tel:<?php echo esc_attr( $zp_tel ); ?>" class="flex items-center gap-3 hover:text-white transition-colors">
								<svg class="w-5 h-5 text-zp-yellow shrink-0" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233a14 14 0 0 0 6.392 6.384" /></svg>
								<?php echo esc_html( $zp_phone ); ?>
							</a>
						</li>
					<?php endif; ?>
				</ul>
			</div>

		</div>

		<div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center text-blue-300 font-medium space-y-4 md:space-y-0 text-sm">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>. All Rights Reserved.</p>
			<?php
			zp_menu(
				'legal',
				array(
					'aria_label' => __( 'Legal', 'zoneplay' ),
					'nav_class'  => 'flex gap-6',
					'base_class' => 'hover:text-white transition-colors',
				)
			);
			?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
