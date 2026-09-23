/* Nur Vorschau: ersetzt, was im Live-Shop Flatsome/WooCommerce erledigen. */
(function () {
	'use strict';

	function toast(msg) {
		var t = document.querySelector('.pv-toast');
		if (!t) {
			t = document.createElement('div');
			t.className = 'pv-toast';
			t.setAttribute('role', 'status');
			document.body.appendChild(t);
		}
		t.textContent = msg;
		t.classList.add('is-on');
		clearTimeout(t._h);
		t._h = setTimeout(function () { t.classList.remove('is-on'); }, 2600);
	}

	/* Umschalter Handy-/Desktop-Ansicht in der Vorschau-Leiste */
	var bar = document.querySelector('.pv-bar');
	if (bar) {
		var file = location.pathname.split('/').pop() || 'index.html';
		if (!/\.html$/.test(file)) file = 'index.html';
		var inDesktop = false;
		try { inDesktop = window.parent !== window && window.parent.PV_DESKTOP === true; } catch (e) {}
		var sw = document.createElement('a');
		sw.className = 'pv-bar__switch';
		if (inDesktop) {
			// Die Desktop-Ansicht hat ihre eigene, gut lesbare Leiste.
			bar.hidden = true;
		} else {
			sw.textContent = 'Desktop-Ansicht';
			sw.href = 'desktop.html#' + file;
		}
		var tag = bar.querySelector('.pv-bar__tag');
		bar.insertBefore(sw, tag ? tag.nextSibling : bar.firstChild);
	}

	/* Lightbox für Zertifikate & Produktbilder (im Shop: Flatsome-Lightbox) */
	function openLightbox(src) {
		var box = document.createElement('div');
		box.className = 'pv-lightbox';
		box.innerHTML = '<img alt=""><button type="button" aria-label="Schließen">×</button>';
		box.querySelector('img').src = src;
		function close() { box.remove(); document.removeEventListener('keydown', onKey); }
		function onKey(e) { if (e.key === 'Escape') close(); }
		box.addEventListener('click', close);
		document.addEventListener('keydown', onKey);
		document.body.appendChild(box);
	}

	document.addEventListener('click', function (e) {
		var lb = e.target.closest('a.lightbox, .alp-lookup__actions a[href^="img/"]');
		if (lb && /\.(webp|png|jpe?g)$/i.test(lb.getAttribute('href'))) {
			e.preventDefault();
			openLightbox(lb.getAttribute('href'));
			return;
		}
		if (e.target.closest('[data-pv-cart]')) {
			e.preventDefault();
			toast('Vorschau – der Warenkorb ist nur im Live-Shop aktiv.');
		}
		var tab = e.target.closest('[data-pv-tab]');
		if (tab) {
			e.preventDefault();
			var id = tab.getAttribute('href').slice(1);
			document.querySelectorAll('.product-tabs li').forEach(function (li) { li.classList.toggle('active', li.contains(tab)); });
			document.querySelectorAll('.tab-panels > .panel').forEach(function (p) { p.hidden = p.id !== id; p.classList.toggle('active', p.id === id); });
		}
		var thumb = e.target.closest('[data-pv-thumb]');
		if (thumb) {
			e.preventDefault();
			var i = thumb.getAttribute('data-pv-thumb');
			document.querySelectorAll('[data-pv-slide]').forEach(function (s) { s.hidden = s.getAttribute('data-pv-slide') !== i; });
			document.querySelectorAll('.product-thumbnails .col').forEach(function (c) { c.classList.toggle('is-nav-selected', c.contains(thumb)); });
		}
	});

	/* Formulare (Kontakt, Newsletter, Login, Kasse …) werden in der Vorschau nicht abgeschickt */
	document.addEventListener('submit', function (e) {
		if (e.defaultPrevented) return; // z. B. Chargen-Prüfer, eigene Seitenskripte
		e.preventDefault();
		toast(e.target.hasAttribute('data-pv-checkout')
			? 'Vorschau – im Live-Shop wird die Bestellung jetzt abgeschickt.'
			: 'Vorschau – das Formular wird nur im Live-Shop gesendet.');
	});

	/* Produktseite: Menge +/- und „In den Warenkorb“ */
	var form = document.querySelector('[data-pv-cart-form]');
	if (form) {
		var qty = form.querySelector('.qty');
		form.querySelector('.minus').addEventListener('click', function () { qty.value = Math.max(1, (+qty.value || 1) - 1); });
		form.querySelector('.plus').addEventListener('click', function () { qty.value = (+qty.value || 0) + 1; });
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			toast('Vorschau – ' + qty.value + '× würde jetzt in den Warenkorb gelegt.');
		});
	}

	/* Mobiles Menü */
	var menu = document.getElementById('main-menu');
	if (menu) {
		document.addEventListener('click', function (e) {
			if (e.target.closest('[data-pv-menu]')) { e.preventDefault(); menu.hidden = false; document.documentElement.classList.add('alp-lock'); }
			else if (e.target.closest('[data-pv-menu-close]')) { menu.hidden = true; document.documentElement.classList.remove('alp-lock'); }
		});
		document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !menu.hidden) { menu.hidden = true; document.documentElement.classList.remove('alp-lock'); } });
	}

	/* Shop: Kategorie-Leiste filtert per #kategorie-slug */
	var shop = document.querySelector('[data-pv-shop]');
	if (shop) {
		var pills = document.querySelectorAll('.alp-cat-pills .alp-pill');
		var count = document.querySelector('.woocommerce-result-count');
		var applyFilter = function () {
			var cat = location.hash.indexOf('#kategorie-') === 0 ? location.hash.slice(1) : '';
			var n = 0;
			shop.querySelectorAll('.product-small.col').forEach(function (c) {
				var hit = !cat || (' ' + c.getAttribute('data-cats') + ' ').indexOf(' ' + cat + ' ') > -1;
				c.hidden = !hit;
				if (hit) n++;
			});
			pills.forEach(function (p) {
				var href = p.getAttribute('href');
				var on = cat ? href.indexOf('#' + cat) > -1 : href === 'shop.html';
				p.classList.toggle('is-active', on);
				if (on) p.setAttribute('aria-current', 'page'); else p.removeAttribute('aria-current');
			});
			if (count) count.textContent = n + (n === 1 ? ' Ergebnis' : ' Ergebnisse');
			var title = document.querySelector('[data-pv-shop-title]');
			var activePill = document.querySelector('.alp-cat-pills .alp-pill.is-active');
			if (title) title.textContent = cat && activePill ? activePill.firstChild.textContent.trim() : 'Alle Produkte';
			var crumb = document.querySelector('.shop-page-title .breadcrumbs');
			if (crumb) crumb.innerHTML = '<a href="index.html">Startseite</a> <span class="divider">/</span> ' + (cat ? '<a href="shop.html">Shop</a> <span class="divider">/</span> ' + title.textContent : 'Shop');
		};
		var order = document.querySelector('[data-pv-orderby]');
		var grid = shop.querySelector('.products');
		if (order && grid) {
			order.addEventListener('change', function () {
				var cards = Array.prototype.slice.call(grid.children);
				var key = order.value;
				cards.sort(function (a, b) {
					if (key === 'price') return a.dataset.price - b.dataset.price;
					if (key === 'price-desc') return b.dataset.price - a.dataset.price;
					if (key === 'date') return b.dataset.id - a.dataset.id;
					return b.dataset.sales - a.dataset.sales;
				});
				cards.forEach(function (c) { grid.appendChild(c); });
			});
		}
		pills.forEach(function (p) {
			if (p.getAttribute('href') === 'shop.html') {
				p.addEventListener('click', function (e) { e.preventDefault(); history.pushState(null, '', 'shop.html'); applyFilter(); });
			}
		});
		window.addEventListener('hashchange', applyFilter);
		applyFilter();
	}
})();
