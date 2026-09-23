/**
 * AminoLabs Pro 2 – Theme-Skripte (ohne jQuery, ohne externe Abhängigkeiten).
 *  1. Laufleiste (wechselnde Hinweise)
 *  2. Such-Overlay
 *  3. Sticky-Kaufleiste auf Produktseiten
 *  4. „Versand heute“-Countdown
 *  5. Bewegung: Einblenden beim Scrollen, hochzählende Laborwerte, Chromatogramm
 */
(function () {
	'use strict';

	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* 1. Laufleiste ------------------------------------------------------ */
	function initAnnouncement() {
		var items = document.querySelectorAll('.alp-announce__item');
		if (items.length < 2 || reduceMotion) return;
		var i = 0;
		setInterval(function () {
			if (document.hidden) return;
			items[i].classList.remove('is-active');
			i = (i + 1) % items.length;
			items[i].classList.add('is-active');
		}, 4500);
	}

	/* 2. Such-Overlay ---------------------------------------------------- */
	function initSearch() {
		var sheet = document.getElementById('alp-search');
		if (!sheet) return;
		var input = sheet.querySelector('input[type="search"]');
		var lastFocus = null;

		function open(e) {
			if (e) e.preventDefault();
			lastFocus = document.activeElement;
			sheet.hidden = false;
			document.documentElement.classList.add('alp-lock');
			requestAnimationFrame(function () {
				sheet.classList.add('is-open');
				if (input) input.focus();
			});
		}
		function close() {
			sheet.classList.remove('is-open');
			document.documentElement.classList.remove('alp-lock');
			setTimeout(function () { sheet.hidden = true; }, reduceMotion ? 0 : 200);
			if (lastFocus) lastFocus.focus();
		}

		document.addEventListener('click', function (e) {
			if (e.target.closest('[data-alp-open-search]')) open(e);
			else if (e.target.closest('[data-alp-close-search]')) close();
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && !sheet.hidden) close();
		});
	}

	/* 3. Sticky-Kaufleiste ----------------------------------------------- */
	function initBuyBar() {
		var bar = document.querySelector('[data-alp-buybar]');
		var form = document.querySelector('form.cart');
		if (!bar || !form || !('IntersectionObserver' in window)) return;
		var realBtn = form.querySelector('[type="submit"], .single_add_to_cart_button');
		var btn = bar.querySelector('[data-alp-buybar-btn]');

		var io = new IntersectionObserver(function (entries) {
			var visible = entries[0].isIntersecting || entries[0].boundingClientRect.top > 0;
			bar.classList.toggle('is-visible', !visible);
			bar.setAttribute('aria-hidden', visible ? 'true' : 'false');
			btn.tabIndex = visible ? -1 : 0;
			document.body.classList.toggle('alp-buybar-on', !visible);
		}, { rootMargin: '0px 0px -40px 0px' });
		io.observe(form);

		btn.addEventListener('click', function () {
			// Variable Produkte: zur Auswahl scrollen. Einfache Produkte: echten Button auslösen.
			var needsChoice = form.classList.contains('variations_form') && realBtn && realBtn.classList.contains('disabled');
			if (needsChoice || !realBtn) {
				form.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'center' });
				return;
			}
			realBtn.click();
		});
	}

	/* 4. Versand-Countdown (Mo–Fr bis Bestellschluss, Zeitzone Berlin) ---- */
	function initShipping() {
		var el = document.querySelector('[data-alp-cutoff]');
		if (!el) return;
		var cutoff = parseInt(el.getAttribute('data-alp-cutoff'), 10) || 14;
		var text = el.querySelector('.alp-ship__text');
		var names = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];

		function berlinNow() {
			try {
				var parts = new Intl.DateTimeFormat('de-DE', {
					timeZone: 'Europe/Berlin', hour12: false, weekday: 'short',
					hour: '2-digit', minute: '2-digit'
				}).formatToParts(new Date());
				var map = {};
				parts.forEach(function (p) { map[p.type] = p.value; });
				var days = { 'So': 0, 'Mo': 1, 'Di': 2, 'Mi': 3, 'Do': 4, 'Fr': 5, 'Sa': 6 };
				return { day: days[map.weekday.replace('.', '')], h: parseInt(map.hour, 10) % 24, m: parseInt(map.minute, 10) };
			} catch (e) {
				var d = new Date();
				return { day: d.getDay(), h: d.getHours(), m: d.getMinutes() };
			}
		}

		function render() {
			var n = berlinNow();
			var workday = n.day >= 1 && n.day <= 5;
			var minsLeft = cutoff * 60 - (n.h * 60 + n.m);
			if (workday && minsLeft > 0) {
				var h = Math.floor(minsLeft / 60), m = minsLeft % 60;
				text.innerHTML = 'Bestelle innerhalb von <strong>' + (h ? h + ' Std. ' : '') + m + ' Min.</strong> – Versand noch heute.';
			} else {
				var next = n.day;
				do { next = (next + 1) % 7; } while (next === 0 || next === 6);
				var label = (next === (n.day + 1) % 7) ? 'morgen' : 'am ' + names[next];
				text.innerHTML = 'Jetzt bestellen – Versand <strong>' + label + '</strong>.';
			}
			el.hidden = false;
		}
		render();
		setInterval(render, 60000);
	}

	/* 5. Bewegung ---------------------------------------------------------- */
	// Elemente, die beim Scrollen sanft erscheinen (Geschwister nacheinander).
	var REVEAL = [
		'.alp-section__head', '.alp-trust__item', '.alp-cat', '.products .product-small',
		'.alp-coa-teaser__copy', '.alp-coa-teaser__list', '.alp-process__step', '.alp-know__card',
		'.alp-faq__head', '.alp-faq__item', '.alp-cta', '.alp-coa-card', '.alp-coa-explain',
		'.alp-pcoa', '.alp-ptab-coa__doc', '.alp-ptab-coa__info', '.alp-shop-intro',
		'.wis-card', '.box-blog-post', '.gl-card', '.ship-card', '.recon-card', '.alp-usp-card',
		'#alp-scope .alp-card', '#alp-scope .alp-section', '.alp3-flag-col',
		'.alp-desc__tile', '.alp-desc__panel', '.alp-desc__dark', '.alp-desc__tiles'
	].join(',');

	// Laborwerte, die hochzählen (erste Zahl im Text, z. B. „≥ 98 %“, „10,44 mg“).
	var COUNT = '.alp-hero__stats dd, .alp-pcoa__num, .alp-coa-card__v, .alp-ptab-coa__figures strong, .alp-coa-teaser__val, .alp-cert__data dd';

	function countUp(el) {
		var node = el.firstChild;
		while (node && node.nodeType !== 3) node = node.nextSibling;
		if (!node) return;
		var m = node.nodeValue.match(/^(\D*?)(\d+(?:,\d+)?)(.*)$/);
		if (!m || /^\s*[–-]\s*\d/.test(m[3])) return; // Bereiche wie „2–4 Tage“ bleiben stehen
		var target = parseFloat(m[2].replace(',', '.'));
		var decimals = (m[2].split(',')[1] || '').length;
		var start = null;
		var duration = 1100;
		function frame(t) {
			if (start === null) start = t;
			var p = Math.min(1, (t - start) / duration);
			var eased = 1 - Math.pow(1 - p, 3);
			node.nodeValue = m[1] + (target * eased).toFixed(decimals).replace('.', ',') + m[3];
			if (p < 1) requestAnimationFrame(frame);
		}
		node.nodeValue = m[1] + (0).toFixed(decimals).replace('.', ',') + m[3];
		requestAnimationFrame(frame);
	}

	function initMotion() {
		if (reduceMotion || !document.body.classList.contains('alp-motion') || !('IntersectionObserver' in window)) return;

		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) return;
				var el = entry.target;
				el.classList.add('is-in');
				if (el.matches(COUNT)) {
					countUp(el);
				} else {
					el.querySelectorAll(COUNT).forEach(function (c) {
						if (!c.closest('.alp-reveal:not(.is-in)')) countUp(c);
					});
				}
				io.unobserve(el);
			});
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });

		document.querySelectorAll(REVEAL).forEach(function (el) {
			if (el.parentElement && el.parentElement.closest('.alp-reveal')) return; // nicht doppelt verschachteln
			var i = Array.prototype.indexOf.call(el.parentElement.children, el);
			el.style.setProperty('--alp-delay', Math.min(i, 6) * 70 + 'ms');
			el.classList.add('alp-reveal');
			io.observe(el);
		});
		// Zahlen außerhalb von einblendenden Elementen (z. B. Hero) separat beobachten.
		document.querySelectorAll(COUNT).forEach(function (el) {
			if (!el.closest('.alp-reveal')) io.observe(el);
		});
		document.querySelectorAll('.alp-chroma').forEach(function (el) { io.observe(el); });
	}

	function ready(fn) {
		if (document.readyState !== 'loading') fn();
		else document.addEventListener('DOMContentLoaded', fn);
	}

	ready(function () {
		initAnnouncement();
		initSearch();
		initBuyBar();
		initShipping();
		initMotion();
	});
})();
