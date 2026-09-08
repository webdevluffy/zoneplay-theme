<?php
/**
 * Site footer — brand blurb, opening times, contact, legal row.
 *
 * Static for now (mirrors the Astro build); a widgets pass comes later.
 * Opening times are the $zp_hours array below.
 *
 * @package ZonePlay
 */

$zp_email     = 'info@zoneplaycardiff.co.uk';
$zp_tel       = '+442920239777';
$zp_tel_label = '02920 239777';
$zp_facebook  = 'https://www.facebook.com/zoneplaycardiff';
$zp_instagram = 'https://www.instagram.com/zoneplaycardiff/';

$zp_hours = array(
	array( 'day' => 'Monday', 'hours' => 'Closed', 'closed' => true ),
	array( 'day' => 'Tuesday – Saturday', 'hours' => '10am – 5:30pm', 'closed' => false ),
	array( 'day' => 'Sunday', 'hours' => '11am – 4pm', 'closed' => false ),
);
?>
</main>

<footer class="bg-zp-darkblue text-white pt-20 pb-10">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">

			<div class="lg:col-span-2">
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/logo.webp' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) . ' logo' ); ?>" width="200" height="133" loading="lazy" decoding="async" class="h-20 w-auto mb-6 drop-shadow-lg" />
				<h2 class="font-display font-bold text-xl text-white mb-3">Indoor Soft Play in Cardiff</h2>
				<p class="text-blue-200 font-medium text-base max-w-md mb-8 leading-relaxed">
					Zone Play Cardiff is an indoor soft play centre at Unit 5, Stadium Close (off Penarth Road),
					perfect for babies, toddlers, and young children. With free parking, a caf&eacute;, birthday parties,
					and plenty of seating for parents, we&rsquo;re one of Cardiff&rsquo;s favourite family-friendly rainy-day activities.
				</p>
				<div class="flex gap-3">
					<a href="<?php echo esc_url( $zp_facebook ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' on Facebook' ); ?>" class="w-11 h-11 rounded-full bg-white/10 flex items-center justify-center hover:bg-[#1877F2] transition-colors">
						<svg class="w-5 h-5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" /></svg>
					</a>
					<a href="<?php echo esc_url( $zp_instagram ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' on Instagram' ); ?>" class="w-11 h-11 rounded-full bg-white/10 flex items-center justify-center hover:bg-zp-pink transition-colors">
						<svg class="w-5 h-5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="20" x="2" y="2" rx="5" ry="5" /><path d="M16 11.37A4 4 0 1 1 12.63 8A4 4 0 0 1 16 11.37m1.5-4.87h.01" /></svg>
					</a>
				</div>
			</div>

			<div>
				<h2 class="font-display font-bold text-xl mb-6 text-zp-yellow">Opening Times</h2>
				<ul class="space-y-1 mb-4">
					<?php foreach ( $zp_hours as $i => $row ) : ?>
						<li class="py-2.5 border-b border-white/10 last:border-0">
							<span class="block text-blue-200 font-medium text-sm"><?php echo esc_html( $row['day'] ); ?></span>
							<span class="font-bold text-base <?php echo $row['closed'] ? 'text-red-400' : 'text-white'; ?>"><?php echo esc_html( $row['hours'] ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
				<p class="text-blue-300 text-sm font-medium leading-relaxed">Open daily during school holidays &amp; Bank Holidays</p>
			</div>

			<div>
				<h2 class="font-display font-bold text-xl mb-6 text-zp-green">Find Us</h2>
				<ul class="space-y-4 text-blue-200 font-medium text-sm mb-8">
					<li class="flex items-start gap-3">
						<svg class="w-5 h-5 text-zp-yellow shrink-0 mt-0.5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0" /><circle cx="12" cy="10" r="3" /></svg>
						<address class="not-italic leading-relaxed">Unit 5, Stadium Cl<br />Cardiff, CF11 8TS</address>
					</li>
					<li>
						<a href="mailto:<?php echo esc_attr( $zp_email ); ?>" class="flex items-center gap-3 hover:text-white transition-colors break-all">
							<svg class="w-5 h-5 text-zp-yellow shrink-0" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m22 7l-8.991 5.727a2 2 0 0 1-2.009 0L2 7" /><rect width="20" height="16" x="2" y="4" rx="2" /></svg>
							<?php echo esc_html( $zp_email ); ?>
						</a>
					</li>
					<li>
						<a href="tel:<?php echo esc_attr( $zp_tel ); ?>" class="flex items-center gap-3 hover:text-white transition-colors">
							<svg class="w-5 h-5 text-zp-yellow shrink-0" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233a14 14 0 0 0 6.392 6.384" /></svg>
							<?php echo esc_html( $zp_tel_label ); ?>
						</a>
					</li>
				</ul>
			</div>

		</div>

		<div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center text-blue-300 font-medium space-y-4 md:space-y-0 text-sm">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>. All Rights Reserved.</p>
			<nav class="flex gap-6" aria-label="<?php esc_attr_e( 'Legal', 'zoneplay' ); ?>">
				<a href="<?php echo esc_url( home_url( '/advertisers/' ) ); ?>" class="hover:text-white transition-colors">Advertisers</a>
				<a href="<?php echo esc_url( home_url( '/rules-of-play/' ) ); ?>" class="hover:text-white transition-colors">Rules of Play</a>
				<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>" class="hover:text-white transition-colors">Privacy Policy</a>
			</nav>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
