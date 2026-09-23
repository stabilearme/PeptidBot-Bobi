/**
 * AminoLabs Pro 2 – Theme-Skripte (ohne jQuery, ohne externe Abhängigkeiten).
 *  1. Laufleiste (wechselnde Hinweise)
 *  2. Such-Overlay
 *  3. Sticky-Kaufleiste auf Produktseiten
 *  4. „Versand heute“-Countdown
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

	function ready(fn) {
		if (document.readyState !== 'loading') fn();
		else document.addEventListener('DOMContentLoaded', fn);
	}

	ready(function () {
		initAnnouncement();
		initSearch();
		initBuyBar();
		initShipping();
	});
})();
