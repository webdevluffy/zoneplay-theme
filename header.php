<?php
/**
 * Site header — head, GTM, skip link, and the fixed navigation bar.
 *
 * The logo comes from the Customizer "Site Identity" logo and the links
 * from the "primary" menu location ("Main Menu"); see inc/navigation.php.
 * Design is unchanged from the previous static header.
 *
 * @package ZonePlay
 */
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
	})(window,document,'script','dataLayer','<?php echo esc_js( zp_gtm_id() ); ?>');</script>
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
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr( zp_gtm_id() ); ?>"
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
				<?php zp_the_logo( 'h-24 md:h-28 w-auto drop-shadow-md', 'hover:scale-105 transition-transform' ); ?>
			</div>

			<?php
			zp_menu(
				'primary',
				array(
					'aria_label'     => __( 'Primary', 'zoneplay' ),
					'nav_class'      => 'hidden lg:flex space-x-4 xl:space-x-8 items-center mt-2',
					'base_class'     => 'font-display font-bold transition-all text-base xl:text-[1.1rem] hover:-translate-y-1 border-b-4 pb-1',
					'active_class'   => 'text-zp-red border-zp-red',
					'inactive_class' => 'text-zp-darkblue hover:text-zp-red border-transparent',
				)
			);
			?>

			<?php
			zp_nav_button(
				'bg-zp-yellow hover:bg-zp-orange text-zp-darkblue font-display font-bold py-3 px-6 xl:px-8 rounded-full shadow-[0_4px_0_0_#D97706] hover:shadow-[0_2px_0_0_#D97706] hover:translate-y-[2px] transition-all text-lg xl:text-xl whitespace-nowrap active:shadow-none active:translate-y-[4px]',
				'hidden lg:flex mt-2'
			);
			?>

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
			<?php
			zp_menu(
				'primary',
				array(
					'base_class'     => 'block px-4 py-3 rounded-2xl text-xl font-display font-bold transition-colors border-2',
					'active_class'   => 'bg-zp-red/10 text-zp-red border-zp-red/20',
					'inactive_class' => 'text-zp-darkblue hover:bg-zp-blue/10 hover:text-zp-blue border-transparent',
				)
			);
			?>
			<?php
			zp_nav_button(
				'flex justify-center items-center w-full bg-zp-yellow hover:bg-zp-orange text-zp-darkblue font-display font-bold py-4 px-6 rounded-2xl shadow-[0_4px_0_0_#D97706] active:shadow-none active:translate-y-[4px] transition-all text-2xl',
				'pt-4'
			);
			?>
		</div>
	</div>
</header>

<main id="main" class="flex-grow w-full relative z-10">
