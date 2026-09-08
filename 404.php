<?php
/**
 * 404 — ported from the Astro build.
 *
 * @package ZonePlay
 */

get_header();

$zp_links = array(
	'/about-us/'   => 'About Us',
	'/cafe/'       => 'Cafe',
	'/parties/'    => 'Parties',
	'/events/'     => 'Events',
	'/membership/' => 'Membership',
	'/contact/'    => 'Contact',
);
?>
<section class="pt-48 pb-28 px-4 sm:px-6 lg:px-8 text-center relative overflow-hidden bg-zp-blue">
	<div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_2px_2px,white_1px,transparent_0)] bg-[size:24px_24px] pointer-events-none"></div>
	<div class="relative z-10 max-w-3xl mx-auto">
		<div class="text-7xl mb-6" aria-hidden="true">&#128584;</div>
		<h1 class="text-6xl md:text-8xl font-display font-bold text-white mb-4 drop-shadow-xl">404</h1>
		<p class="text-2xl md:text-3xl text-white/90 font-medium drop-shadow-lg mb-10">Oops! This page has wandered off to play.</p>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex items-center bg-zp-yellow hover:bg-zp-orange text-zp-darkblue font-display font-bold py-4 px-10 rounded-full shadow-[0_6px_0_0_#D97706] hover:shadow-[0_2px_0_0_#D97706] hover:translate-y-[4px] transition-all text-xl group active:shadow-none active:translate-y-[6px]">
			Back to Home
			<svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-7-7l7 7l-7 7" /></svg>
		</a>
	</div>
</section>

<section class="py-20 bg-slate-50">
	<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
		<h2 class="text-2xl font-display font-bold text-zp-darkblue mb-8">Or try one of these pages</h2>
		<div class="flex flex-wrap justify-center gap-3">
			<?php foreach ( $zp_links as $href => $label ) : ?>
				<a href="<?php echo esc_url( home_url( $href ) ); ?>" class="bg-white border-4 border-slate-200 hover:border-zp-blue text-zp-darkblue font-display font-bold px-6 py-3 rounded-full shadow-sm transition-colors"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php
get_footer();
