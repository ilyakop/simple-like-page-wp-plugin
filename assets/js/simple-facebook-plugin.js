(function() {
	var settings = window.sfpSettings || {};
	var api = window.sfpPlugin || {};
	var sdkPromise = null;
	var sdkLoaded = false;

	function getSetting(value, fallback) {
		return typeof value === 'undefined' ? fallback : value;
	}

	function getLocale(container) {
		if (settings.locale) {
			return settings.locale;
		}
		if (container && container.dataset && container.dataset.sfpLocale) {
			return container.dataset.sfpLocale;
		}
		return 'en_US';
	}

	function shouldLoadSdk() {
		return getSetting(settings.shouldLoad, true) && getSetting(settings.hasConsent, true);
	}

	function ensureFbRoot() {
		if (!document.getElementById('fb-root')) {
			var fbRoot = document.createElement('div');
			fbRoot.id = 'fb-root';
			document.body.appendChild(fbRoot);
		}
	}

	function loadSdk(container) {
		if (sdkLoaded) {
			return Promise.resolve();
		}
		if (sdkPromise) {
			return sdkPromise;
		}

		sdkPromise = new Promise(function(resolve, reject) {
			if (!shouldLoadSdk()) {
				reject(new Error('Facebook SDK blocked by filters.'));
				return;
			}

			ensureFbRoot();

			var js = document.createElement('script');
			js.id = 'facebook-jssdk';
			js.async = true;
			js.defer = true;
			js.src = 'https://connect.facebook.net/' + getLocale(container) + '/sdk.js#xfbml=1&version=v19.0';
			js.onload = function() {
				sdkLoaded = true;
				resolve();
			};
			js.onerror = function() {
				reject(new Error('Facebook SDK failed to load.'));
			};

			document.head.appendChild(js);
		});

		return sdkPromise;
	}

	function buildEmbed(container) {
		if (!container || container.dataset.sfpLoaded === '1') {
			return;
		}

		if (!shouldLoadSdk()) {
			return;
		}

		container.dataset.sfpLoaded = '1';

		var target = container.querySelector('.sfp-embed');
		if (!target) {
			return;
		}

		var placeholder = container.querySelector('.sfp-placeholder');
		if (placeholder) {
			placeholder.setAttribute('hidden', 'hidden');
			placeholder.setAttribute('aria-hidden', 'true');
			placeholder.style.display = 'none';
			placeholder.disabled = true;
		}

		var fbPage = document.createElement('div');
		fbPage.className = 'fb-page';
		fbPage.setAttribute('data-href', container.dataset.sfpUrl || '');
		fbPage.setAttribute('data-width', container.dataset.sfpWidth || '');
		fbPage.setAttribute('data-height', container.dataset.sfpHeight || '');
		fbPage.setAttribute('data-hide-cover', container.dataset.sfpHideCover || 'false');
		fbPage.setAttribute('data-show-facepile', container.dataset.sfpShowFacepile || 'true');
		fbPage.setAttribute('data-small-header', container.dataset.sfpSmallHeader || 'false');
		fbPage.setAttribute('data-tabs', container.dataset.sfpTabs || '');

		target.appendChild(fbPage);

		loadSdk(container).then(function() {
			if (window.FB && window.FB.XFBML && typeof window.FB.XFBML.parse === 'function') {
				window.FB.XFBML.parse(container);
			}
		}).catch(function() {
			container.dataset.sfpLoaded = '0';
			if (placeholder) {
				placeholder.removeAttribute('hidden');
				placeholder.removeAttribute('aria-hidden');
				placeholder.style.display = '';
				placeholder.disabled = false;
			}
			if (fbPage && fbPage.parentNode) {
				fbPage.parentNode.removeChild(fbPage);
			}
		});
	}

	function isInView(element) {
		var rect = element.getBoundingClientRect();
		return rect.top <= (window.innerHeight || document.documentElement.clientHeight) && rect.bottom >= 0;
	}

function setupLazyFallback(containers) {
	var pending = Array.prototype.slice.call(containers);

		function check() {
			pending = pending.filter(function(container) {
				if (container.dataset.sfpLoaded === '1') {
					return false;
				}

				if (!isInView(container)) {
					return true;
				}

		buildEmbed(container);

				return false;
			});

			if (!pending.length) {
				window.removeEventListener('scroll', check);
				window.removeEventListener('resize', check);
				window.removeEventListener('orientationchange', check);
			}
		}

		window.addEventListener('scroll', check);
		window.addEventListener('resize', check);
		window.addEventListener('orientationchange', check);
		check();
	}

	function initContainers() {
		var containers = document.querySelectorAll('[data-sfp-embed="1"]');
		if (!containers.length) {
			return;
		}

		var observer = null;
		if ('IntersectionObserver' in window) {
			observer = new IntersectionObserver(function(entries) {
				entries.forEach(function(entry) {
					if (!entry.isIntersecting) {
						return;
					}

					var container = entry.target;
					observer.unobserve(container);
					if (container.dataset.sfpLoaded === '1') {
						return;
					}

					if (container.dataset.sfpLazy === '1' && container.dataset.sfpClickToLoad === '0') {
						buildEmbed(container);
					}
				});
			});
		}

		containers.forEach(function(container) {
			var placeholder = container.querySelector('.sfp-placeholder');
			var clickEnabled = container.dataset.sfpClickToLoad === '1';
			var lazyEnabled = container.dataset.sfpLazy === '1';

			if (placeholder) {
				placeholder.addEventListener('click', function() {
					buildEmbed(container);
				});

				if (!clickEnabled) {
					placeholder.classList.add('sfp-placeholder--auto');
				}
			}

			if (lazyEnabled && observer) {
				observer.observe(container);
			}

			if (!lazyEnabled && !clickEnabled) {
				buildEmbed(container);
			}
		});

	if (!observer) {
		var lazyFallbackTargets = Array.prototype.filter.call(containers, function(container) {
			return container.dataset.sfpLazy === '1' && container.dataset.sfpClickToLoad === '0';
		});

		if (lazyFallbackTargets.length) {
			setupLazyFallback(lazyFallbackTargets);
		}
		}
	}

	api.loadEmbed = function(container) {
		buildEmbed(container);
	};

	api.loadAll = function() {
		var containers = document.querySelectorAll('[data-sfp-embed="1"]');
		Array.prototype.forEach.call(containers, function(container) {
			if (container.dataset.sfpLoaded !== '1') {
				buildEmbed(container);
			}
		});
	};

	window.sfpPlugin = api;

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initContainers);
	} else {
		initContainers();
	}
})();
