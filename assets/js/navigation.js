/*
 * Mobile menu toggle — ported from the Astro Header component.
 */
(function () {
	var toggle = document.getElementById('menu-toggle');
	var menu = document.getElementById('mobile-menu');
	if (!toggle || !menu) return;

	var iconOpen = document.querySelector('.menu-icon-open');
	var iconClose = document.querySelector('.menu-icon-close');

	toggle.addEventListener('click', function () {
		var open = menu.classList.toggle('hidden') === false;
		toggle.setAttribute('aria-expanded', String(open));
		toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
		if (iconOpen) iconOpen.classList.toggle('hidden', open);
		if (iconClose) iconClose.classList.toggle('hidden', !open);
	});

	// Close on navigation / resize back to desktop.
	menu.addEventListener('click', function (e) {
		if (e.target.closest('a')) {
			menu.classList.add('hidden');
			toggle.setAttribute('aria-expanded', 'false');
			toggle.setAttribute('aria-label', 'Open menu');
			if (iconOpen) iconOpen.classList.remove('hidden');
			if (iconClose) iconClose.classList.add('hidden');
		}
	});
})();
