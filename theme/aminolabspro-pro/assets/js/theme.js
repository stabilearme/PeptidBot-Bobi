/**
 * AminoLabs Pro 2 – Theme-Skripte (ohne jQuery, ohne externe Abhängigkeiten).
 *  1. Laufleiste (wechselnde Hinweise)
 *  2. Such-Overlay
 *  3. Sticky-Kaufleiste auf Produktseiten
 *  4. „Versand heute“-Countdown
 *  5. Bewegung: Einblenden beim Scrollen, hochzählende Laborwerte, Chromatogramm
 *  6. Umschalter „Neu im Shop“ / „Angebote“ auf der Startseite
 *  7. Countdown (Aktionsprodukt)
 *  8. Newsletter-Anmeldung (Brevo)
 *  9. Bilder-Absicherung (Platzhalter mit data-src)
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
		'.alp-section__head', '.alp-stats__panel', '.alp-trust__item', '.alp-cat', '.products .product-small',
		'.alp-coa-teaser__copy', '.alp-coa-teaser__list', '.alp-process__step', '.alp-know__card',
		'.alp-faq__head', '.alp-faq__item', '.alp-cta', '.alp-soon', '.alp-coa-card', '.alp-coa-explain',
		'.alp-check-card', '.alp-article', '.alp-pcoa', '.alp-ptab-coa__doc', '.alp-ptab-coa__info', '.alp-shop-intro',
		'.wis-card', '.box-blog-post', '.gl-card', '.ship-card', '.recon-card', '.alp-usp-card',
		'#alp-scope .alp-card', '#alp-scope .alp-section', '.alp3-flag-col',
		'.alp-desc__tile', '.alp-desc__panel', '.alp-desc__dark', '.alp-desc__tiles'
	].join(',');

	// Laborwerte, die hochzählen (erste Zahl im Text, z. B. „≥ 98 %“, „10,44 mg“).
	var COUNT = '.alp-hero__stats dd, .alp-stats__value, .alp-hero__proof-val strong, .alp-pcoa__num, .alp-coa-card__v, .alp-ptab-coa__figures strong, .alp-coa-teaser__val, .alp-cert__data dd';

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
		// Läuft auch bei „Bewegung reduzieren“: motion.css zeigt dann nur Überblendungen ohne Bewegung.
		if (!document.body.classList.contains('alp-motion') || !('IntersectionObserver' in window)) return;

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

	/* 6. Umschalter (Tabs) --------------------------------------------------- */
	function initTabs() {
		document.addEventListener('click', function (e) {
			var tab = e.target.closest('[data-alp-tab]');
			if (!tab) return;
			var group = tab.parentElement;
			group.querySelectorAll('[data-alp-tab]').forEach(function (t) {
				var on = t === tab;
				t.classList.toggle('is-active', on);
				t.setAttribute('aria-selected', on ? 'true' : 'false');
				var panel = document.getElementById(t.getAttribute('data-alp-tab'));
				if (panel) panel.hidden = !on;
			});
			// Kacheln im neu gezeigten Bereich sofort einblenden
			var shown = document.getElementById(tab.getAttribute('data-alp-tab'));
			if (shown) shown.querySelectorAll('.alp-reveal').forEach(function (el) { el.classList.add('is-in'); });
		});
	}

	/* 7. Countdown (Aktionsprodukt) -------------------------------------- */
	function initCountdown() {
		document.querySelectorAll('[data-alp-countdown]').forEach(function (el) {
			var end = parseInt(el.getAttribute('data-alp-countdown'), 10) * 1000;
			var cells = {};
			el.querySelectorAll('[data-unit]').forEach(function (c) { cells[c.getAttribute('data-unit')] = c; });
			function pad(n) { return n < 10 ? '0' + n : String(n); }
			function tick() {
				var left = Math.max(0, Math.floor((end - Date.now()) / 1000));
				if (!left) {
					if (el.hidden) return;
					el.hidden = true;
					// Wochenangebot abgelaufen: Seite ohne Cache neu laden, dann erscheint das neue Angebot.
					if (el.hasAttribute('data-alp-reload') && location.search.indexOf('alp_week=') === -1) {
						setTimeout(function () {
							location.replace(location.pathname + (location.search ? location.search + '&' : '?') + 'alp_week=' + Math.floor(end / 1000) + '#aktion');
						}, 4000);
					}
					return;
				}
				cells.d.textContent = pad(Math.floor(left / 86400));
				cells.h.textContent = pad(Math.floor(left % 86400 / 3600));
				cells.m.textContent = pad(Math.floor(left % 3600 / 60));
				cells.s.textContent = pad(left % 60);
			}
			tick();
			setInterval(tick, 1000);
		});
	}

	/* 8. Newsletter-Anmeldung (Brevo) ------------------------------------
	 * Sendet im Hintergrund an Brevo und leitet dann zur Danke-Seite weiter
	 * (wie bisher auf aminolabspro.com). Ohne JavaScript geht das Formular direkt an Brevo. */
	function initNewsletter() {
		document.querySelectorAll('form[data-alp-newsletter]').forEach(function (form) {
			form.addEventListener('submit', function (e) {
				if (!window.fetch || !window.FormData) return;
				e.preventDefault();
				var trap = form.querySelector('[name="email_address_check"]');
				if (trap && trap.value) return;
				var btn = form.querySelector('button[type="submit"]');
				if (btn) { btn.disabled = true; btn.classList.add('is-loading'); }
				offerStore('s');
				var done = function () { window.location.href = form.getAttribute('data-thanks') || '/'; };
				fetch(form.action, { method: 'POST', body: new FormData(form), mode: 'no-cors' }).then(done, done);
			});
		});
	}

	/* 8b. 15-%-Einblendung ------------------------------------------------
	 * Erscheint nach data-delay Sekunden oder am PC, wenn die Maus oben aus der Seite geht.
	 * Gespeichert im Browser: 's' = angemeldet (nie wieder), sonst Zeitpunkt des Schließens. */
	function offerStore(value) {
		try {
			if (value === undefined) return window.localStorage.getItem('alp_offer');
			window.localStorage.setItem('alp_offer', value);
		} catch (e) { return null; }
	}

	function initOffer() {
		var box = document.getElementById('alp-offer');
		if (!box) return;
		var state = offerStore();
		var snooze = (parseInt(box.getAttribute('data-snooze'), 10) || 30) * 864e5;
		if (state === 's' || (state && Date.now() - parseInt(state, 10) < snooze)) return;
		if (location.hash === '#newsletter') return;

		var shown = false, timer = null;
		var inView = function (el) {
			if (!el) return false;
			var r = el.getBoundingClientRect();
			return r.top < window.innerHeight && r.bottom > 0;
		};
		var show = function () {
			if (shown) return;
			// Nicht über dem Newsletter-Abschnitt und nicht über der Kaufleiste auf Produktseiten.
			if (inView(document.getElementById('newsletter')) || document.body.classList.contains('alp-buybar-on')) {
				timer = setTimeout(show, 10000);
				return;
			}
			shown = true;
			box.hidden = false;
			document.body.classList.add('alp-offer-on');
			requestAnimationFrame(function () { requestAnimationFrame(function () { box.classList.add('is-open'); }); });
			document.removeEventListener('mouseout', onLeave);
		};
		var close = function () {
			offerStore(String(Date.now()));
			box.classList.remove('is-open');
			document.body.classList.remove('alp-offer-on');
			setTimeout(function () { box.hidden = true; }, 300);
		};
		var onLeave = function (e) {
			if (!e.relatedTarget && e.clientY <= 0) show();
		};

		timer = setTimeout(show, (parseInt(box.getAttribute('data-delay'), 10) || 30) * 1000);
		if (window.matchMedia('(hover: hover) and (min-width: 850px)').matches) {
			setTimeout(function () { if (!shown) document.addEventListener('mouseout', onLeave); }, 5000);
		}
		box.querySelector('[data-alp-offer-close]').addEventListener('click', function () { clearTimeout(timer); close(); });
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && shown && !box.hidden) close();
		});
	}

	/* 9. Bilder-Absicherung ------------------------------------------------
	 * Falls ein Plugin/Flatsome Bilder als Platzhalter mit data-src ausgibt und dessen
	 * Nachlade-Skript nicht läuft, bleiben Produktbilder leer. Hier werden sie direkt
	 * eingesetzt; der Browser lädt sie dank loading="lazy" trotzdem erst beim Scrollen. */
	function initImageFallback() {
		document.querySelectorAll('img[data-src]').forEach(function (img) {
			var src = img.getAttribute('src') || '';
			if (src && src.indexOf('data:') !== 0) return;
			if (!img.hasAttribute('loading')) img.setAttribute('loading', 'lazy');
			if (img.dataset.srcset) img.setAttribute('srcset', img.dataset.srcset);
			if (img.dataset.sizes) img.setAttribute('sizes', img.dataset.sizes);
			img.setAttribute('src', img.dataset.src);
			img.classList.remove('lazy-load');
			img.classList.add('lazy-load-active');
		});
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
		initTabs();
		initCountdown();
		initNewsletter();
		initOffer();
		initImageFallback();
	});
})();
