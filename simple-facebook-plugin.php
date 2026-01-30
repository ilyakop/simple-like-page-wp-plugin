<?php
/**
 * Plugin Name: Simple Like Page Plugin
 * Plugin URI: https://topdevs.net/simple-like-page-plugin/
 * Description: A lightweight, privacy-friendly way to embed Facebook Page feeds on WordPress with performance and consent in mind.
 * Version: 2.0.0
 * Requires at least: 5.8
 * Requires PHP: 7.2
 * Author: topdevs.net
 * Author URI: https://topdevs.net
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: simple-like-page-plugin
 *
 * This plugin is not affiliated with, endorsed by, or sponsored by Meta Platforms, Inc.
 * All trademarks belong to their respective owners.
 */

define( "SFP_VERSION", '2.0' );

/**
* Main SF Plugin Class
*
* Contains the main functions for SF and stores variables
*
* @since SF Plugin 1.2
* @author Ilya K.
*/

// Check if class already exist
if ( !class_exists( 'SFPlugin' ) ) {

	// Main plugin class
	class SFPlugin {
		
		public $pluginPath;
		public $pluginUrl;
		public $pluginName;
		public $optionName;
		public $frontendAssetsEnqueued = false;

		public $facebookLocalesUrl = "http://www.facebook.com/translations/FacebookLocales.xml";
		public $locales;

		/**
		* SF Plugin Constructor
		*
		* Gets things started
		*/
		public function __construct( ) {
			$this->pluginPath 	= plugin_dir_path(__FILE__);
			$this->pluginUrl 	= plugin_dir_url(__FILE__);

			$this->optionName	= "simple_facebook_plugin_options";
			
			$this->locales 		= $this->parseLocales($this->facebookLocalesUrl);

			$this->loadFiles();
			$this->addActions();
			$this->addShortcodes();
		}
		
		/**
		 * Load all the required files.
		 */
		protected function loadFiles() {
			// Include social plugins files
			require_once( $this->pluginPath . 'lib/sfp-page-plugin.php' );

			// Allow addons load files
			do_action('sfp_load_files');
		}
		
		/**
		* Add all the required actions.
		*/
		protected function addActions() {
			
			add_action( 'admin_menu', 	array( $this, 'pluginMenu') );
			add_action( 'admin_notices',    array( $this, 'adminNotice') );
			add_action( 'widgets_init', 	array( $this, 'addWidgets') );
			//add_action( 'wp_footer',		array( $this, 'addJavaScriptSDK') );
			add_action( 'admin_init', 		array( $this, 'saveOptions' ) );
			add_action( 'admin_init', 		array( $this, 'ignoreNotices' ) );
			add_action( 'admin_init', 		array( $this, 'registerSettings' ) );
			add_action( 'admin_enqueue_scripts',	array( $this, 'enqueueScriptsAdmin') );
			add_action( 'init', array( $this, 'registerBlock' ) );

			// Add settings links on Plugins page
			$plugin = plugin_basename( __FILE__ );
			add_filter( "plugin_action_links_$plugin", array( $this, 'pluginSettingsLink') );
			add_filter( 'plugin_row_meta', array( $this, 'pluginRowMeta' ), 10, 2 );

			// Allow addons add actions
			do_action( 'sfp_add_actions', $this );
		}
		
		/**
		* Register all widgets
		*/
		public function addWidgets() {
			
			register_widget('SFPPagePluginWidget');
		
			// Allow addons add widgets
			do_action('sfp_add_widgets');
		}
		
		/**
		* Register all shortcodes
		*/
		public function addShortcodes() {
		
			add_shortcode('sfp-page-plugin', 'sfp_page_plugin_shortcode');
			
			// Allow addons add shortcodes
			do_action('sfp_add_shortcodes');
		}

		/**
		 * Get remote XML file by URL
		 * 
		 * @param  string $url 
		 * @return array
		 * @since 1.3
		 */
		public function parseLocales ( $url = "" ) {

			if ( file_exists( $url ) && function_exists( "simplexml_load_file" ) ) {
				
				$locales 	= array();
				$xml 		= simplexml_load_file( $url );
				
				foreach ( $xml as $key => $locale ) {
					
					$name = (array) $locale->englishName;
					$name = $name[0];

					$code = (array) $locale->codes->code->standard->representation;
					$code = $code[0];

					$locales[$code] = $name; 
				};
			}
			else 
				$locales = array( 
					"af_ZA"=>   "Afrikaans",
					"ar_AR"=>   "Arabic",
					"az_AZ"=>	"Azerbaijani",
					"be_BY"=>   "Belarusian",
					"bg_BG"=>   "Bulgarian",
					"bn_IN"=>   "Bengali",
					"bs_BA"=>   "Bosnian",
					"ca_ES"=>   "Catalan",
					"cs_CZ"=>   "Czech",
					"cy_GB"=>   "Welsh",
					"da_DK"=>   "Danish",
					"de_DE"=>   "German",
					"el_GR"=>   "Greek",
					"en_GB"=>   "English (UK)",
					"en_PI"=>   "English (Pirate)",
					"en_UD"=>   "English (Upside Down)",
					"en_US"=>   "English (US)",
					"eo_EO"=>   "Esperanto",
					"es_ES"=>   "Spanish (Spain)",
					"es_LA"=>   "Spanish",
					"et_EE"=>   "Estonian",
					"eu_ES"=>   "Basque",
					"fa_IR"=>   "Persian",
					"fb_LT"=>   "Leet Speak",
					"fi_FI"=>   "Finnish",
					"fo_FO"=>   "Faroese",
					"fr_CA"=>   "French (Canada)",
					"fr_FR"=>   "French (France)",
					"fy_NL"=>   "Frisian",
					"ga_IE"=>   "Irish",
					"gl_ES"=>   "Galician",
					"he_IL"=>   "Hebrew",
					"hi_IN"=>   "Hindi",
					"hr_HR"=>   "Croatian",
					"hu_HU"=>   "Hungarian",
					"hy_AM"=>   "Armenian",
					"id_ID"=>   "Indonesian",
					"is_IS"=>   "Icelandic",
					"it_IT"=>   "Italian",
					"ja_JP"=>   "Japanese",
					"ka_GE"=>   "Georgian",
					"km_KH"=>   "Khmer",
					"ko_KR"=>   "Korean",
					"ku_TR"=>   "Kurdish",
					"la_VA"=>   "Latin",
					"lt_LT"=>   "Lithuanian",
					"lv_LV"=>   "Latvian",
					"mk_MK"=>   "Macedonian",
					"ml_IN"=>   "Malayalam",
					"ms_MY"=>   "Malay",
					"nb_NO"=>   "Norwegian (bokmal)",
					"ne_NP"=>   "Nepali",
					"nl_NL"=>   "Dutch",
					"nn_NO"=>   "Norwegian (nynorsk)",
					"pa_IN"=>   "Punjabi",
					"pl_PL"=>   "Polish",
					"ps_AF"=>   "Pashto",
					"pt_BR"=>   "Portuguese (Brazil)",
					"pt_PT"=>   "Portuguese (Portugal)",
					"ro_RO"=>   "Romanian",
					"ru_RU"=>   "Russian",
					"sk_SK"=>   "Slovak",
					"sl_SI"=>   "Slovenian",
					"sq_AL"=>   "Albanian",
					"sr_RS"=>   "Serbian",
					"sv_SE"=>   "Swedish",
					"sw_KE"=>   "Swahili",
					"ta_IN"=>   "Tamil",
					"te_IN"=>   "Telugu",
					"th_TH"=>   "Thai",
					"tl_PH"=>   "Filipino",
					"tr_TR"=>   "Turkish",
					"uk_UA"=>   "Ukrainian",
					"vi_VN"=>   "Vietnamese",
					"zh_CN"=>   "Simplified Chinese (China)",
					"zh_HK"=>   "Traditional Chinese (Hong Kong)",
					"zh_TW"=>   "Traditional Chinese (Taiwan)" 
				);

			return $locales;
		}

		/**
		 * Load styles for dashboard
		 *
		 * @since 1.3.1
		 */

		static function enqueueScriptsAdmin() {
			
			// add custom css
			wp_register_style( 'sfp-admin-style', plugin_dir_url(__FILE__) . '/lib/css/sfp-admin-style.css' );
			wp_enqueue_style( 'sfp-admin-style' );

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_add_inline_script(
				'wp-color-picker',
				'jQuery(function($){function initSfpColors(context){$(context).find(".sfp-color-field").wpColorPicker();}initSfpColors(document);$(document).on("widget-added widget-updated",function(e,widget){initSfpColors(widget);});});'
			);
		}

		/**
		 * Load Facebook JavaScript SDK
		 *
		 * @since 1.3
		 */
		
		public function addJavaScriptSDK() { 
			$this->enqueueFrontendAssets();
		}

		/**
		 * Add Dashboard > Plugins Menu Page
		 *
		 * @since 1.3
		 */

		public function pluginMenu() {
			
			add_options_page( 'Simple Like Page Plugin', 'Simple Like Page Plugin', 'manage_options', 'sfp_plugin', array( $this, "pluginMenuView" ) );
		}

		/**
		 * Show Menu Page View
		 *
		 * @since 1.3
		 */

		public function pluginMenuView() {

			$options = $this->getPluginOptions();
			
			// include Like Box view
			include( $this->pluginPath . 'views/view-menu.php' );

		}

		/**
		* Show admin notice
		* 
		* @since 1.3
		*/

		public function adminNotice() {

			// don't show any notices for now
			return;

			global $current_user;
			$user_id = $current_user->ID;

			/* Check that the user hasn't already clicked to ignore the message */
			if ( ! get_user_meta( $user_id, 'sfp_ignore_notice_4') ) {

				echo '<div class="updated"><p>';

				printf( __('Thanks for using our <strong>Simple Like Page Plugin</strong>! We have some other great WordPress plugins <a href="http://codecanyon.net/user/topdevs/portfolio?ref=topdevs">View Portfolio</a> | <a href="%1$s">Hide this</a>'), '?sfp_ignore_4=0');

				echo "</p></div>";

			}
		}

		public function ignoreNotices() {
			
			global $current_user;
			$user_id = $current_user->ID;
			
			/* If user clicks to ignore the notice, add that to their user meta */
			if ( isset( $_GET['sfp_ignore_4'] ) && '0' == $_GET['sfp_ignore_4'] ) {
				add_user_meta( $user_id, 'sfp_ignore_notice_4', 'true', true);
			}
		}

		/**
		* Add status link on plugins page
		*
		* @since 1.3
		*/

		public function pluginSettingsLink ( $links ) {

			$settings_link = '<a href="' . menu_page_url( "sfp_plugin", false ) . '">Settings</a>'; 

			$inserted = false;
			$updated_links = array();
			foreach ( $links as $link ) {
				$updated_links[] = $link;
				if ( strpos( $link, 'Visit plugin site' ) !== false ) {
					$updated_links[] = $settings_link;
					$inserted = true;
				}
			}

			if ( ! $inserted ) {
				$updated_links[] = $settings_link;
			}

			return $updated_links; 
		}

		public function pluginRowMeta( $links, $file ) {

			if ( $file !== plugin_basename( __FILE__ ) ) {
				return $links;
			}

			$settings_link = '<a href="' . menu_page_url( "sfp_plugin", false ) . '">Settings</a>';

			$inserted = false;
			$updated_links = array();
			foreach ( $links as $link ) {
				$updated_links[] = $link;
				if ( stripos( $link, 'Visit plugin site' ) !== false ) {
					$updated_links[] = $settings_link;
					$inserted = true;
				}
			}

			if ( ! $inserted ) {
				$updated_links[] = $settings_link;
			}

			return $updated_links;
		}

		/**
		* Get plugin options
		* 
		* @since 1.3
		*/

		public function getPluginOptions() {

			$defaults = array(
				'url' => 'https://www.facebook.com/WordPress/',
				'locale' => "en_US",
				'click_to_load' => 0,
				'lazy_load' => 1,
				'placeholder_text' => 'Click to load Facebook content',
				'placeholder_bg_color' => '#e7f3ff',
				'placeholder_text_color' => '#1877f2'
			);

			$defaults = apply_filters( "sfp_default_options", $defaults );

			$options = get_option( $this->optionName, array() );
			$options = wp_parse_args( $options, $defaults );

			return $options;
		}

		/**
		* Save plugin options
		* 
		* @since 1.3
		*/

		public function savePluginOptions( $options = array() ) {

			update_option( $this->optionName, $options );
		}

		/**
		* Trigger when settings page form submitted
		*
		* @since 1.3
		*/

		public function saveOptions() {

			//delete_option( $this->optionName );

			// If submit button pressed
			if ( isset( $_POST['sfp_options_saved'] ) ) {

				$options = $this->getPluginOptions();

				if ( isset( $_POST['locale'] ) && !empty( $_POST['locale'] ) ) {

					$options['locale'] = $_POST['locale'];
				}

				if ( isset( $_POST['url'] ) && ! empty( $_POST['url'] ) ) {
					$options['url'] = esc_url_raw( $_POST['url'] );
				}

				if ( isset( $_POST['click_to_load'] ) ) {
					$options['click_to_load'] = (int) (bool) $_POST['click_to_load'];
				}

				if ( isset( $_POST['lazy_load'] ) ) {
					$options['lazy_load'] = (int) (bool) $_POST['lazy_load'];
				}

				if ( isset( $_POST['placeholder_text'] ) ) {
					$options['placeholder_text'] = sanitize_text_field( $_POST['placeholder_text'] );
				}

				if ( isset( $_POST['placeholder_bg_color'] ) ) {
					$options['placeholder_bg_color'] = sanitize_hex_color( $_POST['placeholder_bg_color'] );
				}

				if ( isset( $_POST['placeholder_text_color'] ) ) {
					$options['placeholder_text_color'] = sanitize_hex_color( $_POST['placeholder_text_color'] );
				}

				$this->savePluginOptions( $options );
			}
		}

		public function registerSettings() {

			register_setting(
				'sfp_options',
				$this->optionName,
				array( $this, 'sanitizeOptions' )
			);

			add_settings_section(
				'sfp_performance_privacy',
				'Performance & Privacy',
				array( $this, 'renderPerformancePrivacySection' ),
				'sfp_plugin'
			);

			add_settings_field(
				'sfp_click_to_load',
				'Enable click-to-load',
				array( $this, 'renderClickToLoadField' ),
				'sfp_plugin',
				'sfp_performance_privacy'
			);

			add_settings_field(
				'sfp_lazy_load',
				'Enable lazy loading',
				array( $this, 'renderLazyLoadField' ),
				'sfp_plugin',
				'sfp_performance_privacy'
			);

			add_settings_field(
				'sfp_placeholder_text',
				'Placeholder text',
				array( $this, 'renderPlaceholderTextField' ),
				'sfp_plugin',
				'sfp_performance_privacy'
			);

			add_settings_field(
				'sfp_placeholder_bg_color',
				'Placeholder background color',
				array( $this, 'renderPlaceholderBgField' ),
				'sfp_plugin',
				'sfp_performance_privacy'
			);

			add_settings_field(
				'sfp_placeholder_text_color',
				'Placeholder text color',
				array( $this, 'renderPlaceholderTextColorField' ),
				'sfp_plugin',
				'sfp_performance_privacy'
			);

			add_settings_section(
				'sfp_locale',
				'Localization',
				array( $this, 'renderLocaleSection' ),
				'sfp_plugin'
			);

			add_settings_field(
				'sfp_url_field',
				'Default Facebook Page URL',
				array( $this, 'renderUrlField' ),
				'sfp_plugin',
				'sfp_locale'
			);

			add_settings_field(
				'sfp_locale_field',
				'Language',
				array( $this, 'renderLocaleField' ),
				'sfp_plugin',
				'sfp_locale'
			);
		}

		public function sanitizeOptions( $options ) {

			$defaults = $this->getPluginOptions();

			$clean = array();
			$clean['url'] = isset( $options['url'] ) ? esc_url_raw( $options['url'] ) : $defaults['url'];
			$clean['locale'] = isset( $options['locale'] ) ? sanitize_text_field( $options['locale'] ) : $defaults['locale'];
			$clean['click_to_load'] = ! empty( $options['click_to_load'] ) ? 1 : 0;
			$clean['lazy_load'] = ! empty( $options['lazy_load'] ) ? 1 : 0;
			$clean['placeholder_text'] = isset( $options['placeholder_text'] ) ? sanitize_text_field( $options['placeholder_text'] ) : $defaults['placeholder_text'];
			$clean['placeholder_bg_color'] = isset( $options['placeholder_bg_color'] ) ? sanitize_hex_color( $options['placeholder_bg_color'] ) : $defaults['placeholder_bg_color'];
			$clean['placeholder_text_color'] = isset( $options['placeholder_text_color'] ) ? sanitize_hex_color( $options['placeholder_text_color'] ) : $defaults['placeholder_text_color'];

			return $clean;
		}

		public function renderPerformancePrivacySection() {
			echo '<p>Delays Facebook loading until interaction or when the embed is in view to reduce unnecessary third-party requests.</p>';
		}

		public function renderLocaleSection() {
			echo '<p>Select the Facebook SDK locale used when loading embeds.</p>';
		}

		public function renderUrlField() {
			$options = $this->getPluginOptions();
			?>
			<input type="url" class="regular-text" name="<?php echo esc_attr( $this->optionName ); ?>[url]" value="<?php echo esc_attr( $options['url'] ); ?>" />
			<p class="description">Used when a widget, shortcode, or block does not specify a URL.</p>
			<?php
		}

		public function renderClickToLoadField() {
			$options = $this->getPluginOptions();
			?>
			<label>
				<input type="checkbox" name="<?php echo esc_attr( $this->optionName ); ?>[click_to_load]" value="1" <?php checked( 1, (int) $options['click_to_load'] ); ?> />
				<span>Requires a click before Facebook loads.</span>
			</label>
			<?php
		}

		public function renderLazyLoadField() {
			$options = $this->getPluginOptions();
			?>
			<label>
				<input type="checkbox" name="<?php echo esc_attr( $this->optionName ); ?>[lazy_load]" value="1" <?php checked( 1, (int) $options['lazy_load'] ); ?> />
				<span>Loads the embed only when it enters the viewport.</span>
			</label>
			<?php
		}

		public function renderPlaceholderTextField() {
			$options = $this->getPluginOptions();
			?>
			<input type="text" class="regular-text" name="<?php echo esc_attr( $this->optionName ); ?>[placeholder_text]" value="<?php echo esc_attr( $options['placeholder_text'] ); ?>" />
			<p class="description">Displayed before the embed loads.</p>
			<?php
		}

		public function renderPlaceholderBgField() {
			$options = $this->getPluginOptions();
			?>
			<input type="text" class="sfp-color-field" name="<?php echo esc_attr( $this->optionName ); ?>[placeholder_bg_color]" value="<?php echo esc_attr( $options['placeholder_bg_color'] ); ?>" data-default-color="#e7f3ff" />
			<p class="description">Background color for the placeholder.</p>
			<?php
		}

		public function renderPlaceholderTextColorField() {
			$options = $this->getPluginOptions();
			?>
			<input type="text" class="sfp-color-field" name="<?php echo esc_attr( $this->optionName ); ?>[placeholder_text_color]" value="<?php echo esc_attr( $options['placeholder_text_color'] ); ?>" data-default-color="#1877f2" />
			<p class="description">Text color for the placeholder.</p>
			<?php
		}

		public function renderLocaleField() {
			$options = $this->getPluginOptions();
			?>
			<select name="<?php echo esc_attr( $this->optionName ); ?>[locale]">
			<?php foreach ( $this->locales as $code => $name ) : ?>
				<option <?php selected( ( $options['locale'] == $code ) ? 1 : 0 ); ?> value="<?php echo esc_attr( $code ); ?>" ><?php echo esc_html( $name ); ?></option>
			<?php endforeach; ?>
			</select>
			<?php
		}

		public function enqueueFrontendAssets() {

			if ( $this->frontendAssetsEnqueued ) {
				return;
			}

			$this->frontendAssetsEnqueued = true;

			$options = $this->getPluginOptions();
			$should_load = apply_filters( 'sfp_should_load', true );
			$has_consent = apply_filters( 'sfp_has_consent', true );

			// Hooks for future Pro add-on consent logic.
			do_action( 'sfp_before_load', $should_load, $has_consent );

			wp_enqueue_style(
				'sfp-frontend-style',
				$this->pluginUrl . 'assets/css/simple-facebook-plugin.css',
				array(),
				SFP_VERSION
			);

			wp_enqueue_script(
				'sfp-frontend-script',
				$this->pluginUrl . 'assets/js/simple-facebook-plugin.js',
				array(),
				SFP_VERSION,
				true
			);

			$settings = array(
				'locale' => isset( $options['locale'] ) ? $options['locale'] : 'en_US',
				'clickToLoadEnabled' => (bool) $options['click_to_load'],
				'lazyLoadEnabled' => (bool) $options['lazy_load'],
				'placeholderText' => isset( $options['placeholder_text'] ) ? $options['placeholder_text'] : 'Click to load Facebook content',
				'shouldLoad' => (bool) $should_load,
				'hasConsent' => (bool) $has_consent,
			);

			$settings_json = wp_json_encode( $settings );

			wp_add_inline_script(
				'sfp-frontend-script',
				'window.sfpSettings = window.sfpSettings || {}; window.sfpSettings = Object.assign(window.sfpSettings, ' . $settings_json . ');',
				'before'
			);

			// Hook for future Pro add-on to react after loader setup.
			do_action( 'sfp_after_load', $should_load, $has_consent );
		}

		public function registerBlock() {

			if ( ! function_exists( 'register_block_type' ) ) {
				return;
			}

			$defaults = function_exists( 'sfp_get_page_plugin_defaults' )
				? sfp_get_page_plugin_defaults()
				: array();

			$url_default = isset( $defaults['url'] ) ? $defaults['url'] : '';
			$width_default = isset( $defaults['width'] ) ? $defaults['width'] : '';
			$height_default = isset( $defaults['height'] ) ? $defaults['height'] : '';
			$hide_cover_default = isset( $defaults['hide_cover'] ) ? (bool) $defaults['hide_cover'] : false;
			$show_facepile_default = isset( $defaults['show_facepile'] ) ? (bool) $defaults['show_facepile'] : true;
			$small_header_default = isset( $defaults['small_header'] ) ? (bool) $defaults['small_header'] : false;
			$timeline_default = isset( $defaults['timeline'] ) ? (bool) $defaults['timeline'] : false;
			$events_default = isset( $defaults['events'] ) ? (bool) $defaults['events'] : false;
			$messages_default = isset( $defaults['messages'] ) ? (bool) $defaults['messages'] : false;
			$locale_default = isset( $defaults['locale'] ) ? $defaults['locale'] : 'en_US';
			$placeholder_default = isset( $defaults['placeholder_text'] ) ? $defaults['placeholder_text'] : '';
			$placeholder_bg_default = isset( $defaults['placeholder_bg_color'] ) ? $defaults['placeholder_bg_color'] : '';
			$placeholder_text_color_default = isset( $defaults['placeholder_text_color'] ) ? $defaults['placeholder_text_color'] : '';

			wp_register_script(
				'sfp-block-editor',
				$this->pluginUrl . 'assets/js/sfp-block.js',
				array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n' ),
				SFP_VERSION,
				true
			);

			$block_defaults = array(
				'url' => $url_default,
				'width' => $width_default,
				'height' => $height_default,
				'hideCover' => $hide_cover_default,
				'showFacepile' => $show_facepile_default,
				'smallHeader' => $small_header_default,
				'timeline' => $timeline_default,
				'events' => $events_default,
				'messages' => $messages_default,
				'locale' => $locale_default,
				'placeholderText' => $placeholder_default,
				'placeholderBgColor' => $placeholder_bg_default,
				'placeholderTextColor' => $placeholder_text_color_default,
			);

			wp_add_inline_script(
				'sfp-block-editor',
				'window.sfpBlockDefaults = ' . wp_json_encode( $block_defaults ) . ';',
				'before'
			);

			register_block_type( 'simple-facebook-plugin/page', array(
				'editor_script' => 'sfp-block-editor',
				'render_callback' => array( $this, 'renderBlock' ),
				'attributes' => array(
					'url' => array( 'type' => 'string', 'default' => $url_default ),
					'width' => array( 'type' => 'string', 'default' => $width_default ),
					'height' => array( 'type' => 'string', 'default' => $height_default ),
					'hideCover' => array( 'type' => 'boolean', 'default' => $hide_cover_default ),
					'showFacepile' => array( 'type' => 'boolean', 'default' => $show_facepile_default ),
					'smallHeader' => array( 'type' => 'boolean', 'default' => $small_header_default ),
					'timeline' => array( 'type' => 'boolean', 'default' => $timeline_default ),
					'events' => array( 'type' => 'boolean', 'default' => $events_default ),
					'messages' => array( 'type' => 'boolean', 'default' => $messages_default ),
					'locale' => array( 'type' => 'string', 'default' => $locale_default ),
					'clickToLoad' => array( 'type' => 'boolean' ),
					'placeholderText' => array( 'type' => 'string', 'default' => $placeholder_default ),
					'placeholderBgColor' => array( 'type' => 'string', 'default' => $placeholder_bg_default ),
					'placeholderTextColor' => array( 'type' => 'string', 'default' => $placeholder_text_color_default ),
				),
			) );
		}

		public function renderBlock( $attributes ) {

			$instance = array(
				'url' => isset( $attributes['url'] ) ? $attributes['url'] : '',
				'width' => isset( $attributes['width'] ) ? $attributes['width'] : '',
				'height' => isset( $attributes['height'] ) ? $attributes['height'] : '',
				'hide_cover' => isset( $attributes['hideCover'] ) ? (bool) $attributes['hideCover'] : false,
				'show_facepile' => isset( $attributes['showFacepile'] ) ? (bool) $attributes['showFacepile'] : true,
				'small_header' => isset( $attributes['smallHeader'] ) ? (bool) $attributes['smallHeader'] : false,
				'timeline' => isset( $attributes['timeline'] ) ? (bool) $attributes['timeline'] : false,
				'events' => isset( $attributes['events'] ) ? (bool) $attributes['events'] : false,
				'messages' => isset( $attributes['messages'] ) ? (bool) $attributes['messages'] : false,
				'locale' => isset( $attributes['locale'] ) ? $attributes['locale'] : 'en_US',
			);

			if ( array_key_exists( 'clickToLoad', $attributes ) ) {
				$instance['click_to_load'] = (bool) $attributes['clickToLoad'];
			}

			if ( ! empty( $attributes['placeholderText'] ) ) {
				$instance['placeholder_text'] = $attributes['placeholderText'];
			}

			if ( ! empty( $attributes['placeholderBgColor'] ) ) {
				$instance['placeholder_bg_color'] = $attributes['placeholderBgColor'];
			}

			if ( ! empty( $attributes['placeholderTextColor'] ) ) {
				$instance['placeholder_text_color'] = $attributes['placeholderTextColor'];
			}

			if ( function_exists( 'sfp_render_page_plugin_html' ) ) {
				return sfp_render_page_plugin_html( $instance );
			}

			return '';
		}
		
	} // end SFPlugin class

} // end if !class_exists

// Create new SFPlugin instance
$GLOBALS["sfplugin"] = new SFPlugin();

?>
