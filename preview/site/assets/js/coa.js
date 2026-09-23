/**
 * Chargen-Prüfer + Filter der COA-Übersicht.
 * Daten kommen aus /data/coa-batches.php (über wp_localize_script als window.ALP_COA).
 */
(function () {
	'use strict';

	var DATA = window.ALP_COA || { batches: {}, coaUrl: '/coa/' };

	function esc(s) {
		return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
		});
	}

	function normalize(v) {
		return String(v || '').toUpperCase().replace(/\s+/g, '').replace(/[–—_]/g, '-');
	}

	var ICON_CHECK = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>';

	function renderResult(id, b) {
		if (!b) {
			return '<div class="alp-lookup__card is-error"><p><strong>Charge „' + esc(id) + '“ nicht gefunden.</strong></p>' +
				'<p>Prüf die Schreibweise auf dem Etikett (Format z. B. BPC-0726-01) oder <a href="' + esc(DATA.coaUrl) + '">sieh dir alle Zertifikate an</a>.</p></div>';
		}
		if (!b.done) {
			return '<div class="alp-lookup__card is-pending"><p class="alp-lookup__product">' + esc(b.product) + ' <span class="alp-mono">' + esc(id) + '</span></p>' +
				'<p>Diese Charge wird gerade im Labor analysiert. Das Zertifikat erscheint ' + esc(b.expected || 'in Kürze') + '.</p></div>';
		}
		var html = '<div class="alp-lookup__card is-ok">' +
			'<div class="alp-lookup__top"><p class="alp-lookup__product">' + esc(b.product) + ' <span class="alp-mono">' + esc(id) + '</span></p>' +
			'<span class="alp-status alp-status--ok">' + ICON_CHECK + ' Verifiziert</span></div>' +
			'<dl class="alp-lookup__data">' +
			'<div><dt>Reinheit (HPLC)</dt><dd class="is-accent">' + esc(b.purity) + ' %</dd></div>' +
			'<div><dt>Wirkstoffgehalt</dt><dd>' + esc(b.content) + '</dd></div>' +
			'<div><dt>Prüflabor</dt><dd>' + esc(b.lab) + '</dd></div>' +
			'<div><dt>Getestet</dt><dd>' + esc(b.tested) + '</dd></div>' +
			'</dl><div class="alp-lookup__actions">';
		if (b.file) html += '<a class="alp-btn alp-btn--primary alp-btn--sm" href="' + esc(b.file) + '" target="_blank" rel="noopener">Zertifikat ansehen' + (b.cert ? ' (Nr. ' + esc(b.cert) + ')' : '') + '</a>';
		if (b.url) html += '<a class="alp-link" href="' + esc(b.url) + '">Zum Produkt →</a>';
		return html + '</div></div>';
	}

	function initLookup(root) {
		var form = root.querySelector('form');
		var input = root.querySelector('.alp-lookup__input');
		var out = root.querySelector('.alp-lookup__result');

		function check(value) {
			var id = normalize(value);
			if (!id) { input.focus(); return; }
			out.innerHTML = renderResult(id, DATA.batches[id]);
			out.classList.add('is-filled');
		}

		form.addEventListener('submit', function (e) {
			e.preventDefault();
			check(input.value);
		});
		root.querySelectorAll('.alp-lookup__example').forEach(function (btn) {
			btn.addEventListener('click', function () {
				input.value = btn.getAttribute('data-batch');
				check(input.value);
			});
		});
	}

	function initFilter(root) {
		var input = root.querySelector('[data-alp-filter-input]');
		var cards = root.querySelectorAll('.alp-coa-card');
		var empty = root.querySelector('.alp-coa-grid-empty');
		if (!input) return;
		input.addEventListener('input', function () {
			var q = input.value.trim().toLowerCase();
			var shown = 0;
			cards.forEach(function (c) {
				var hit = !q || c.getAttribute('data-product').indexOf(q) > -1 || c.id.indexOf(q) > -1;
				c.hidden = !hit;
				if (hit) shown++;
			});
			if (empty) empty.hidden = shown > 0;
		});
	}

	function highlightHash() {
		if (!location.hash || location.hash.indexOf('#charge-') !== 0) return;
		var el = document.getElementById(location.hash.slice(1));
		if (el) el.classList.add('is-highlight');
	}

	function ready(fn) {
		if (document.readyState !== 'loading') fn();
		else document.addEventListener('DOMContentLoaded', fn);
	}

	ready(function () {
		document.querySelectorAll('[data-alp-lookup]').forEach(initLookup);
		document.querySelectorAll('[data-alp-coa-filter]').forEach(initFilter);
		highlightHash();
	});
})();
