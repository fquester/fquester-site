(function () {
	'use strict';

	var header = document.getElementById('page-header');
	var content = document.getElementById('page-content');

	if (!header || !content) {
		return;
	}

	function isSameOriginInternalLink(link) {
		if (!link || link.target === '_blank' || link.hasAttribute('download')) {
			return false;
		}
		if (link.origin !== window.location.origin) {
			return false;
		}
		if (link.hasAttribute('rel') && link.getAttribute('rel').indexOf('external') !== -1) {
			return false;
		}
		return true;
	}

	function swap(html, url, pushState) {
		var doc = new DOMParser().parseFromString(html, 'text/html');
		var newHeader = doc.getElementById('page-header');
		var newContent = doc.getElementById('page-content');

		if (!newHeader || !newContent) {
			window.location.href = url;
			return;
		}

		var apply = function () {
			header.innerHTML = newHeader.innerHTML;
			content.innerHTML = newContent.innerHTML;
			document.title = doc.title;
		};

		if (document.startViewTransition) {
			document.startViewTransition(apply);
		} else {
			apply();
		}

		if (pushState) {
			window.history.pushState({ ajaxNav: true }, doc.title, url);
		}

		window.scrollTo({ top: 0, behavior: 'auto' });
	}

	function navigate(url, pushState) {
		fetch(url, { headers: { 'X-Requested-With': 'ajax-nav' } })
			.then(function (res) {
				if (!res.ok) {
					throw new Error('bad status ' + res.status);
				}
				return res.text();
			})
			.then(function (html) {
				swap(html, url, pushState);
			})
			.catch(function () {
				window.location.href = url;
			});
	}

	document.addEventListener('click', function (e) {
		var link = e.target.closest('a');

		if (!link || !isSameOriginInternalLink(link)) {
			return;
		}

		if (!header.contains(link) && !content.contains(link)) {
			return;
		}

		if (link.hasAttribute('download') || link.hostname !== window.location.hostname) {
			return;
		}

		e.preventDefault();
		navigate(link.href, true);
	});

	window.addEventListener('popstate', function () {
		navigate(window.location.href, false);
	});
})();
