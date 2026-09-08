<?php
/**
 * @package ZonePlay
 */
?>
<form role="search" method="get" class="mt-8 flex gap-2 max-w-md" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="sr-only" for="zp-search"><?php esc_html_e( 'Search', 'zoneplay' ); ?></label>
	<input type="search" id="zp-search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>"
		placeholder="<?php esc_attr_e( 'Search&hellip;', 'zoneplay' ); ?>"
		class="flex-1 rounded-full border-4 border-slate-200 focus:border-zp-blue focus:outline-none px-5 py-2.5 font-medium" />
	<button type="submit" class="bg-zp-yellow hover:bg-zp-orange text-zp-darkblue font-display font-bold px-6 py-2.5 rounded-full transition-colors">
		<?php esc_html_e( 'Go', 'zoneplay' ); ?>
	</button>
</form>
