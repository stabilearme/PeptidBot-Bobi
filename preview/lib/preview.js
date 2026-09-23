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
		var thumb = e.target.closest('[data-pv-thumb]');
		if (thumb) {
			e.preventDefault();
			var i = thumb.getAttribute('data-pv-thumb');
			document.querySelectorAll('[data-pv-slide]').forEach(function (s) { s.hidden = s.getAttribute('data-pv-slide') !== i; });
			document.querySelectorAll('.product-thumbnails .col').forEach(function (c) { c.classList.toggle('is-nav-selected', c.contains(thumb)); });
		}
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
		};
		pills.forEach(function (p) {
			if (p.getAttribute('href') === 'shop.html') {
				p.addEventListener('click', function (e) { e.preventDefault(); history.pushState(null, '', 'shop.html'); applyFilter(); });
			}
		});
		window.addEventListener('hashchange', applyFilter);
		applyFilter();
	}
})();
