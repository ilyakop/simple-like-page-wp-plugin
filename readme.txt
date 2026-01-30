=== Simple Like Page Plugin – Fast & Privacy-Friendly Page Embeds ===

Contributors: topdevs
Tags: facebook, embeds, social, privacy, performance
Requires at least: 5.8
Tested up to: 6.9
Requires PHP: 7.2
Stable tag: 2.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Embed Meta™ Page content without slowing down your site or loading third-party scripts before user interaction.

== Description ==

Embedding social Page content often slows down WordPress sites and loads third-party scripts before visitors interact.

Simple Like Page Plugin helps you embed Meta™ Page content without hurting performance, by delaying script loading until interaction or visibility.

This reduces unnecessary requests, improves Core Web Vitals, and gives you control over when external scripts load.

This plugin is not affiliated with, endorsed by, or sponsored by Meta Platforms, Inc.
All trademarks belong to their respective owners.

== Key Features ==

= Privacy-First Loading =

* Delays Facebook script loading until user interaction
* Optional lazy loading when the embed enters the viewport
* Helps reduce third-party requests on initial page load

= Performance Focused =

* Lightweight HTML placeholder before activation
* No render-blocking Facebook SDK on initial page load

= Multiple Integration Options =

* Gutenberg block
* Shortcode
* Classic widget
* Theme template tag

= Extensible =

* Hooks and filters for add-ons
* Architecture designed for future consent-based extensions

== How It Works ==

1. Renders a placeholder instead of the Facebook embed
2. Loads Facebook scripts only after user interaction (or when in view, if enabled)
3. Renders the Page Plugin once scripts are available

This approach is designed to improve performance and reduce unnecessary third-party requests.

== Usage ==

You can add the Facebook Page Plugin in four different ways.

= 1. Gutenberg Block (Recommended) =

1. Edit any post or page
2. Click **Add Block (+)**
3. Search for **Facebook Page Plugin**
4. Insert the block
5. Paste your Facebook Page URL
6. Adjust layout and privacy options in the block sidebar

= 2. Shortcode =

Use the shortcode inside any post or page:

`sfp-page-plugin`

Shortcode parameters:

* `url`
* `width`
* `height`
* `hide_cover`
* `show_facepile`
* `small_header`
* `timeline`
* `events`
* `messages`
* `locale`
* `click_to_load`
* `lazy_load`
* `placeholder_text`
* `placeholder_bg_color`
* `placeholder_text_color`

Example:

`[sfp-page-plugin url="https://www.facebook.com/WordPress" width="320" timeline="true"]`

= 3. Widget =

1. Go to **Appearance -> Widgets**
2. Add **SFP - Like Page Plugin**
3. Enter your Facebook Page URL
4. Configure display options
5. Save

= 4. Template Tag (Advanced) =

For theme developers:

```
<?php if ( function_exists( 'sfp_page_plugin' ) ) {
	$args = array(
		'url' => 'https://www.facebook.com/WordPress/',
		'width' => '300',
		'timeline' => true,
		'locale' => 'en_US'
	);
	sfp_page_plugin( $args );
} ?>
```

== Performance & Privacy ==

By default, the plugin delays Facebook loading until interaction. If you enable lazy loading, scripts are loaded when the embed is in view.

Facebook may set cookies or process data once the embed loads. You are responsible for updating your privacy policy as needed.

== Settings ==

The plugin includes a **Performance & Privacy** section where you can control:

* Click-to-load (default off)
* Lazy loading (default on)
* Placeholder text
* Placeholder colors

You can also set a default Facebook Page URL and locale in the settings screen.

== Compatibility ==

* Works with modern WordPress themes
* Compatible with caching and performance plugins
* Supports Gutenberg and Classic Editor

== Frequently Asked Questions ==

= Does this plugin load Facebook automatically? =
No. Facebook scripts load only after interaction by default. You can also enable lazy loading.

= Is this plugin GDPR compliant? =
The plugin helps control when Facebook scripts load, but compliance depends on your site configuration and privacy policy.

= Is this an official Facebook plugin? =
No. This is a third-party plugin that uses Facebook's Page Plugin embed.

== Installation ==

1. Upload the plugin to the `/wp-content/plugins/` directory
2. Activate the plugin through the WordPress admin
3. Add the block, shortcode, widget, or template tag

== Changelog ==

= 2.0.0 =
* Privacy-first loading with click-to-load and lazy loading
* Centralized script loading and consent-ready hooks
* Gutenberg block with performance controls
* New settings screen for performance and placeholder options

= 1.5.2 =
* Teseted up to WordPress 6.3.2
* PHP8.x Compatibility check
* Security fixes for minor Authenticated (Contributor+) XSS vulnerability

= 1.5.1 =
* Teseted up to WordPress 4.9.6

= 1.5 =
* Added Events and Messages tabs
* Added Small Header Option
* Fixed PHP7 Warning and Notice messages
* Removed deprecated Like Box widget

= 1.4.1 =
* Redirect issue fixed

= 1.4 =
* Deprecated "Like Box" replaced with new Facebook "Page Plugin"

= 1.3 =
* Add-on support added

= 1.2.2 =
* Option to show Like Box with no border changed to native Facebook data-show-border=false

= 1.2.1 =
* Added option to show Like Box with no border
* Added Norwegian(bokmal) locale to widget

= 1.2 =
* Plugin structure reorganized. Shortcode and template tag functionality added

= 1.1 =
* More than 20 Facebook Locales added

== Support ==

Found a bug or have a feature request? Please use the **Support** tab on WordPress.org.

