<?php
/**
 * Site header — head, GTM, skip link, and the fixed navigation bar.
 *
 * The nav is intentionally static (mirrors the Astro build); it will be
 * swapped for a registered menu location in a later pass.
 *
 * @package ZonePlay
 */

$zp_nav = array(
	'/'            => 'Home',
	'/about-us/'   => 'About Us',
	'/cafe/'       => 'Cafe',
	'/parties/'    => 'Parties',
	'/events/'     => 'Events',
	'/membership/' => 'Membership',
	'/contact/'    => 'Contact',
);

// WP-native current request path, no leading/trailing slash ('' on the
// front page, 'about-us' for /about-us/, 'blog/hello' for a nested URL).
$zp_req = isset( $GLOBALS['wp']->request ) ? trim( (string) $GLOBALS['wp']->request, '/' ) : '';

/**
 * True when the nav item at $href is the current page. Home matches only
 * the front page; every other item also matches its descendants.
 */
function zp_nav_is_active( $href, $req ) {
	$slug = trim( (string) wp_parse_url( $href, PHP_URL_PATH ), '/' );
	if ( '' === $slug ) {
		return '' === $req;
	}
	return $req === $slug || 0 === strpos( $req, $slug . '/' );
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<?php if ( zp_load_gtm() ) : ?>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','<?php echo esc_js( ZP_GTM_ID ); ?>');</script>
	<!-- End Google Tag Manager -->
	<?php endif; ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="theme-color" content="#0e355d" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'min-h-screen flex flex-col bg-slate-50 overflow-x-hidden' ); ?>>
<?php wp_body_open(); ?>
<?php if ( zp_load_gtm() ) : ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr( ZP_GTM_ID ); ?>"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?php endif; ?>

<a class="sr-only focus:not-sr-only focus:absolute focus:z-[100] focus:top-2 focus:left-2 focus:bg-white focus:text-zp-darkblue focus:px-4 focus:py-2 focus:rounded-lg focus:shadow-lg" href="#main">
	<?php esc_html_e( 'Skip to content', 'zoneplay' ); ?>
</a>

<header class="fixed w-full z-50 bg-white/95 backdrop-blur-md border-b-4 border-zp-red shadow-sm">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="flex justify-between items-center h-22">

			<div class="flex-shrink-0 flex items-center">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:scale-105 transition-transform" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' home' ); ?>">
					<img
						src="<?php echo esc_url( get_theme_file_uri( 'assets/images/logo.webp' ) ); ?>"
						alt="<?php echo esc_attr( get_bloginfo( 'name' ) . ' logo' ); ?>"
						width="200" height="133" fetchpriority="high" decoding="async"
						class="h-24 md:h-28 w-auto drop-shadow-md"
					/>
				</a>
			</div>

			<nav class="hidden lg:flex space-x-4 xl:space-x-8 items-center mt-2" aria-label="<?php esc_attr_e( 'Primary', 'zoneplay' ); ?>">
				<?php foreach ( $zp_nav as $href => $label ) : $active = zp_nav_is_active( $href, $zp_req ); ?>
					<a
						href="<?php echo esc_url( home_url( $href ) ); ?>"
						<?php echo $active ? 'aria-current="page"' : ''; ?>
						class="font-display font-bold transition-all text-base xl:text-[1.1rem] hover:-translate-y-1 border-b-4 pb-1 <?php echo $active ? 'text-zp-red border-zp-red' : 'text-zp-darkblue hover:text-zp-red border-transparent'; ?>"
					><?php echo esc_html( $label ); ?></a>
				<?php endforeach; ?>
			</nav>

			<div class="hidden lg:flex mt-2">
				<a
					href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
					class="bg-zp-yellow hover:bg-zp-orange text-zp-darkblue font-display font-bold py-3 px-6 xl:px-8 rounded-full shadow-[0_4px_0_0_#D97706] hover:shadow-[0_2px_0_0_#D97706] hover:translate-y-[2px] transition-all text-lg xl:text-xl whitespace-nowrap active:shadow-none active:translate-y-[4px]"
				><?php esc_html_e( 'Book Soft Play', 'zoneplay' ); ?></a>
			</div>

			<div class="lg:hidden flex items-center mt-2">
				<button
					id="menu-toggle"
					type="button"
					class="text-zp-darkblue hover:text-zp-red p-2"
					aria-label="<?php esc_attr_e( 'Open menu', 'zoneplay' ); ?>"
					aria-expanded="false"
					aria-controls="mobile-menu"
				>
					<svg class="h-10 w-10 menu-icon-open" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5h16M4 12h16M4 19h16" /></svg>
					<svg class="h-10 w-10 menu-icon-close hidden" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6L6 18M6 6l12 12" /></svg>
				</button>
			</div>

		</div>
	</div>

	<div id="mobile-menu" class="lg:hidden bg-white border-b-4 border-zp-blue absolute w-full max-h-[80vh] overflow-y-auto hidden">
		<div class="px-4 pt-4 pb-8 space-y-3">
			<?php foreach ( $zp_nav as $href => $label ) : $active = zp_nav_is_active( $href, $zp_req ); ?>
				<a
					href="<?php echo esc_url( home_url( $href ) ); ?>"
					<?php echo $active ? 'aria-current="page"' : ''; ?>
					class="block px-4 py-3 rounded-2xl text-xl font-display font-bold transition-colors border-2 <?php echo $active ? 'bg-zp-red/10 text-zp-red border-zp-red/20' : 'text-zp-darkblue hover:bg-zp-blue/10 hover:text-zp-blue border-transparent'; ?>"
				><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
			<div class="pt-4">
				<a
					href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
					class="flex justify-center items-center w-full bg-zp-yellow hover:bg-zp-orange text-zp-darkblue font-display font-bold py-4 px-6 rounded-2xl shadow-[0_4px_0_0_#D97706] active:shadow-none active:translate-y-[4px] transition-all text-2xl"
				><?php esc_html_e( 'Book Soft Play', 'zoneplay' ); ?></a>
			</div>
		</div>
	</div>
</header>

<main id="main" class="flex-grow w-full relative z-10">
