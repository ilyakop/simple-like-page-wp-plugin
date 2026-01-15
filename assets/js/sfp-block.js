(function(wp) {
	if (!wp || !wp.blocks || !wp.element) {
		return;
	}

	var el = wp.element.createElement;
	var __ = wp.i18n && wp.i18n.__ ? wp.i18n.__ : function(text) { return text; };
	var InspectorControls = wp.blockEditor ? wp.blockEditor.InspectorControls : wp.editor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var ToggleControl = wp.components.ToggleControl;
	var Placeholder = wp.components.Placeholder;
	var ColorPalette = wp.components.ColorPalette;
	var defaults = window.sfpBlockDefaults || {};

	wp.blocks.registerBlockType('simple-facebook-plugin/page', {
		title: __('Facebook Page Plugin', 'simple-facebook-plugin'),
		icon: 'facebook',
		category: 'widgets',
		attributes: {
			url: { type: 'string', default: defaults.url || 'https://www.facebook.com/WordPress/' },
			width: { type: 'string', default: defaults.width || '' },
			height: { type: 'string', default: defaults.height || '' },
			hideCover: { type: 'boolean', default: typeof defaults.hideCover === 'boolean' ? defaults.hideCover : false },
			showFacepile: { type: 'boolean', default: typeof defaults.showFacepile === 'boolean' ? defaults.showFacepile : true },
			smallHeader: { type: 'boolean', default: typeof defaults.smallHeader === 'boolean' ? defaults.smallHeader : false },
			timeline: { type: 'boolean', default: typeof defaults.timeline === 'boolean' ? defaults.timeline : false },
			events: { type: 'boolean', default: typeof defaults.events === 'boolean' ? defaults.events : false },
			messages: { type: 'boolean', default: typeof defaults.messages === 'boolean' ? defaults.messages : false },
			locale: { type: 'string', default: defaults.locale || 'en_US' },
			clickToLoad: { type: 'boolean' },
			placeholderText: { type: 'string', default: defaults.placeholderText || '' },
			placeholderBgColor: { type: 'string', default: defaults.placeholderBgColor || '' },
			placeholderTextColor: { type: 'string', default: defaults.placeholderTextColor || '' }
		},
		edit: function(props) {
			var attrs = props.attributes;
			var placeholderText = attrs.placeholderText || __('Click to load Facebook content', 'simple-facebook-plugin');
			var previewStyle = {};
			if (attrs.placeholderBgColor) {
				var tint = (function(hex, ratio) {
					var clean = hex.replace('#', '');
					if (clean.length !== 6) {
						return hex;
					}
					var r = parseInt(clean.substring(0, 2), 16);
					var g = parseInt(clean.substring(2, 4), 16);
					var b = parseInt(clean.substring(4, 6), 16);
					r = Math.round(r + (255 - r) * ratio);
					g = Math.round(g + (255 - g) * ratio);
					b = Math.round(b + (255 - b) * ratio);
					return '#' + [r, g, b].map(function(value) {
						var hexValue = value.toString(16);
						return hexValue.length === 1 ? '0' + hexValue : hexValue;
					}).join('');
				})(attrs.placeholderBgColor, 0.6);
				previewStyle.background = 'linear-gradient(135deg, ' + attrs.placeholderBgColor + ' 0%, ' + tint + ' 100%)';
			}
			if (attrs.placeholderTextColor) {
				previewStyle.color = attrs.placeholderTextColor;
			}

			return [
				el(InspectorControls, { key: 'controls' },
					el(PanelBody, { title: __('Content', 'simple-facebook-plugin'), initialOpen: true },
						el(TextControl, {
							label: __('Facebook Page URL', 'simple-facebook-plugin'),
							value: attrs.url,
							onChange: function(value) { props.setAttributes({ url: value }); }
						}),
						el(TextControl, {
							label: __('Width', 'simple-facebook-plugin'),
							value: attrs.width,
							onChange: function(value) { props.setAttributes({ width: value }); }
						}),
						el(TextControl, {
							label: __('Height', 'simple-facebook-plugin'),
							value: attrs.height,
							onChange: function(value) { props.setAttributes({ height: value }); }
						})
					),
					el(PanelBody, { title: __('Display', 'simple-facebook-plugin'), initialOpen: false },
						el(ToggleControl, {
							label: __('Hide cover photo', 'simple-facebook-plugin'),
							checked: !!attrs.hideCover,
							onChange: function(value) { props.setAttributes({ hideCover: value }); }
						}),
						el(ToggleControl, {
							label: __('Show friend faces', 'simple-facebook-plugin'),
							checked: !!attrs.showFacepile,
							onChange: function(value) { props.setAttributes({ showFacepile: value }); }
						}),
						el(ToggleControl, {
							label: __('Small header', 'simple-facebook-plugin'),
							checked: !!attrs.smallHeader,
							onChange: function(value) { props.setAttributes({ smallHeader: value }); }
						}),
						el(ToggleControl, {
							label: __('Show timeline tab', 'simple-facebook-plugin'),
							checked: !!attrs.timeline,
							onChange: function(value) { props.setAttributes({ timeline: value }); }
						}),
						el(ToggleControl, {
							label: __('Show events tab', 'simple-facebook-plugin'),
							checked: !!attrs.events,
							onChange: function(value) { props.setAttributes({ events: value }); }
						}),
						el(ToggleControl, {
							label: __('Show messages tab', 'simple-facebook-plugin'),
							checked: !!attrs.messages,
							onChange: function(value) { props.setAttributes({ messages: value }); }
						})
					),
					el(PanelBody, { title: __('Performance & Privacy', 'simple-facebook-plugin'), initialOpen: false },
						el(ToggleControl, {
							label: __('Enable click-to-load', 'simple-facebook-plugin'),
							checked: !!attrs.clickToLoad,
							onChange: function(value) { props.setAttributes({ clickToLoad: value }); }
						}),
						el(TextControl, {
							label: __('Placeholder text', 'simple-facebook-plugin'),
							value: attrs.placeholderText,
							onChange: function(value) { props.setAttributes({ placeholderText: value }); }
						})
					)
					,
					el(PanelBody, { title: __('Placeholder Style', 'simple-facebook-plugin'), initialOpen: false },
						el('p', {}, __('Background color', 'simple-facebook-plugin')),
						el(ColorPalette, {
							value: attrs.placeholderBgColor,
							onChange: function(value) { props.setAttributes({ placeholderBgColor: value }); }
						}),
						el('p', {}, __('Text color', 'simple-facebook-plugin')),
						el(ColorPalette, {
							value: attrs.placeholderTextColor,
							onChange: function(value) { props.setAttributes({ placeholderTextColor: value }); }
						})
					)
				),
				el(Placeholder, {
					key: 'preview',
					label: __('Facebook Page Plugin', 'simple-facebook-plugin'),
					instructions: attrs.url ? __('Embed loads on the front end.', 'simple-facebook-plugin') : __('Add a Facebook Page URL to preview.', 'simple-facebook-plugin')
				},
					el('div', { className: 'sfp-block-preview', style: previewStyle }, placeholderText)
				)
			];
		},
		save: function() {
			return null;
		}
	});
})(window.wp);
