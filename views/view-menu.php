<?php
global $wp_settings_sections, $wp_settings_fields;

$page = 'sfp_plugin';
$tabs = array(
	'general' => __( 'General', 'simple-like-page-plugin' ),
	'popup' => __( 'Popup Embed', 'simple-like-page-plugin' ),
	'float' => __( 'Floating Panel', 'simple-like-page-plugin' ),
	'analytics' => __( 'Analytics', 'simple-like-page-plugin' ),
);

$active_tab = isset( $_GET['sfp_tab'] ) ? sanitize_key( wp_unslash( $_GET['sfp_tab'] ) ) : 'general';
if ( ! isset( $tabs[ $active_tab ] ) ) {
	$active_tab = 'general';
}

$upgrade_url = add_query_arg( array(
	'utm_source'   => 'wp-admin',
	'utm_medium'   => 'settings-page',
	'utm_campaign' => 'sfp-upgrade',
), apply_filters( 'sfp_pro_upgrade_url', 'https://topdevs.net/simple-social-pro/' ) );

if ( ! function_exists( 'sfp_render_settings_section_table' ) ) {
	function sfp_render_settings_section_table( $page, $section_id, $show_title = true ) {
		global $wp_settings_sections, $wp_settings_fields;

		if ( empty( $wp_settings_sections[ $page ][ $section_id ] ) ) {
			return false;
		}

		$section = $wp_settings_sections[ $page ][ $section_id ];
		$icon_class = 'dashicons-admin-generic';
		if ( 'sfp_performance_privacy' === $section_id ) {
			$icon_class = 'dashicons-performance';
		} elseif ( 'sfp_placeholder' === $section_id ) {
			$icon_class = 'dashicons-format-image';
		} elseif ( 'sfp_locale' === $section_id ) {
			$icon_class = 'dashicons-translation';
		} elseif ( 'ssp_pro_display_rules' === $section_id ) {
			$icon_class = 'dashicons-filter';
		} elseif ( 'ssp_pro_popup' === $section_id ) {
			$icon_class = 'dashicons-welcome-view-site';
		} elseif ( 'ssp_pro_float' === $section_id ) {
			$icon_class = 'dashicons-share';
		} elseif ( 'ssp_pro_analytics' === $section_id ) {
			$icon_class = 'dashicons-chart-bar';
		}

		echo '<div class="sfp-settings-section sfp-settings-section--' . esc_attr( sanitize_html_class( $section_id ) ) . '">';

		if ( $show_title && ! empty( $section['title'] ) ) {
			echo '<h2 class="sfp-section-title"><span class="dashicons ' . esc_attr( $icon_class ) . '" aria-hidden="true"></span><span>' . esc_html( $section['title'] ) . '</span></h2>';
		}

		if ( ! empty( $section['callback'] ) ) {
			call_user_func( $section['callback'], $section );
		}

		if ( empty( $wp_settings_fields[ $page ][ $section_id ] ) ) {
			echo '</div>';
			return true;
		}

		echo '<table class="form-table" role="presentation">';
		do_settings_fields( $page, $section_id );
		echo '</table>';
		echo '</div>';

		return true;
	}
}

if ( ! function_exists( 'sfp_render_pro_placeholder_settings' ) ) {
	function sfp_render_pro_placeholder_settings( $context, $upgrade_url, $show_title = true ) {
		$upgrade_url = add_query_arg( 'utm_content', str_replace( '_', '-', $context ), $upgrade_url );
		$rows = array();
		$title = __( 'Pro Settings', 'simple-like-page-plugin' );
		$description = __( 'Additional placement controls are available in Pro.', 'simple-like-page-plugin' );
		$pro_enable_badge_contexts = array( 'popup', 'float', 'analytics' );
		$icon_class = 'dashicons-admin-generic';

		if ( 'display_rules' === $context ) {
			$title = __( 'Display Rules', 'simple-like-page-plugin' );
			$description = __( 'Show or hide embeds by post type, post ID, and device.', 'simple-like-page-plugin' );
			$icon_class = 'dashicons-filter';
			$rows = array(
				array( __( 'Enable display rules', 'simple-like-page-plugin' ), 'checkbox', '', true ),
				array( __( 'Allow post types', 'simple-like-page-plugin' ), 'text', 'page, post' ),
				array( __( 'Include only post IDs', 'simple-like-page-plugin' ), 'text', '12,45,89' ),
				array( __( 'Exclude post IDs', 'simple-like-page-plugin' ), 'text', '21,34' ),
				array( __( 'Device', 'simple-like-page-plugin' ), 'select' ),
			);
		} elseif ( 'popup' === $context ) {
			$title = __( 'Popup', 'simple-like-page-plugin' );
			$description = __( 'Configure modal display behavior for the Page embed.', 'simple-like-page-plugin' );
			$icon_class = 'dashicons-welcome-view-site';
			$rows = array(
				array( __( 'Enable popup embed', 'simple-like-page-plugin' ), 'checkbox', '', true ),
				array( __( 'Popup page URL', 'simple-like-page-plugin' ), 'text', 'https://www.facebook.com/YourPage' ),
				array( __( 'Trigger', 'simple-like-page-plugin' ), 'select' ),
				array( __( 'Delay (seconds)', 'simple-like-page-plugin' ), 'number', '7' ),
				array( __( 'Scroll trigger (%)', 'simple-like-page-plugin' ), 'number', '50' ),
				array( __( 'Show once per browser session', 'simple-like-page-plugin' ), 'checkbox' ),
				array( __( 'Headline', 'simple-like-page-plugin' ), 'text', __( 'Stay connected with us', 'simple-like-page-plugin' ) ),
				array( __( 'Sub headline', 'simple-like-page-plugin' ), 'text', __( 'Follow our latest updates on Facebook.', 'simple-like-page-plugin' ) ),
			);
		} elseif ( 'float' === $context ) {
			$title = __( 'Float', 'simple-like-page-plugin' );
			$description = __( 'Configure the floating follow panel placement.', 'simple-like-page-plugin' );
			$icon_class = 'dashicons-share';
			$rows = array(
				array( __( 'Enable floating panel', 'simple-like-page-plugin' ), 'checkbox', '', true ),
				array( __( 'Floating page URL', 'simple-like-page-plugin' ), 'text', 'https://www.facebook.com/YourPage' ),
				array( __( 'Label', 'simple-like-page-plugin' ), 'text', __( 'Follow us', 'simple-like-page-plugin' ) ),
				array( __( 'Show Facebook icon before label', 'simple-like-page-plugin' ), 'checkbox' ),
				array( __( 'Button background color', 'simple-like-page-plugin' ), 'color', '#1877f2' ),
				array( __( 'Button text color', 'simple-like-page-plugin' ), 'color', '#ffffff' ),
				array( __( 'Position', 'simple-like-page-plugin' ), 'select' ),
			);
		} elseif ( 'analytics' === $context ) {
			$title = __( 'Analytics', 'simple-like-page-plugin' );
			$description = __( 'Track engagement events from popup and floating embeds.', 'simple-like-page-plugin' );
			$icon_class = 'dashicons-chart-bar';
			$rows = array(
				array( __( 'Enable tracking', 'simple-like-page-plugin' ), 'checkbox', '', true ),
				array( __( 'Popup opens', 'simple-like-page-plugin' ), 'number', '0' ),
				array( __( 'Floating opens', 'simple-like-page-plugin' ), 'number', '0' ),
				array( __( 'Embed loads', 'simple-like-page-plugin' ), 'number', '0' ),
				array( __( 'Placeholder clicks', 'simple-like-page-plugin' ), 'number', '0' ),
			);
		}
		?>
		<div class="sfp-settings-section sfp-settings-section--placeholder sfp-settings-section--<?php echo esc_attr( $context ); ?>">
			<?php if ( $show_title ) : ?>
				<h2 class="sfp-section-title sfp-pro-heading"><span class="dashicons <?php echo esc_attr( $icon_class ); ?>" aria-hidden="true"></span><span><?php echo esc_html( $title ); ?></span><span class="sfp-pro-badge sfp-pro-badge--heading">PRO</span></h2>
			<?php endif; ?>
			<p><?php echo esc_html( $description ); ?></p>
		<table class="form-table" role="presentation">
			<?php foreach ( $rows as $row ) : ?>
				<tr>
					<th scope="row" class="sfp-setting-heading">
						<span class="sfp-setting-label">
							<?php echo esc_html( $row[0] ); ?>
							<?php if ( in_array( $context, $pro_enable_badge_contexts, true ) && ! empty( $row[3] ) ) : ?>
								<span class="sfp-pro-badge sfp-pro-badge--inline">PRO</span>
							<?php endif; ?>
						</span>
					</th>
					<td>
						<?php if ( 'checkbox' === $row[1] ) : ?>
							<label><input type="checkbox" disabled="disabled" /></label>
							<?php if ( ! empty( $row[3] ) ) : ?>
								<a class="sfp-learn-more-link" href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html__( 'Learn more', 'simple-like-page-plugin' ); ?></a>
							<?php endif; ?>
						<?php elseif ( 'select' === $row[1] ) : ?>
							<select disabled="disabled">
								<option><?php echo esc_html__( 'Option 1', 'simple-like-page-plugin' ); ?></option>
							</select>
						<?php elseif ( 'color' === $row[1] ) : ?>
							<input type="text" class="sfp-color-field" value="<?php echo isset( $row[2] ) ? esc_attr( $row[2] ) : ''; ?>" data-default-color="<?php echo isset( $row[2] ) ? esc_attr( $row[2] ) : ''; ?>" disabled="disabled" />
						<?php elseif ( 'number' === $row[1] ) : ?>
							<input type="number" class="small-text" value="<?php echo isset( $row[2] ) ? esc_attr( $row[2] ) : ''; ?>" disabled="disabled" />
						<?php else : ?>
							<input type="text" class="regular-text" value="<?php echo isset( $row[2] ) ? esc_attr( $row[2] ) : ''; ?>" disabled="disabled" />
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		</div>
		<?php
	}
}
?>
<div class="wrap">
	<h2><?php echo esc_html__( 'Simple Like Page', 'simple-like-page-plugin' ); ?></h2>

	<h2 class="nav-tab-wrapper sfp-tabs" style="margin-bottom:16px;">
		<?php foreach ( $tabs as $tab_key => $tab_label ) : ?>
			<?php
			$tab_url = add_query_arg(
				array(
					'page' => 'sfp_plugin',
					'sfp_tab' => $tab_key,
				),
				admin_url( 'options-general.php' )
			);
			?>
			<a href="<?php echo esc_url( $tab_url ); ?>" class="nav-tab <?php echo ( $active_tab === $tab_key ) ? 'nav-tab-active' : ''; ?>">
				<?php
				$tab_icon_class = 'dashicons-admin-generic';
				if ( 'general' === $tab_key ) {
					$tab_icon_class = 'dashicons-admin-settings';
				} elseif ( 'popup' === $tab_key ) {
					$tab_icon_class = 'dashicons-welcome-view-site';
				} elseif ( 'float' === $tab_key ) {
					$tab_icon_class = 'dashicons-share';
				} elseif ( 'analytics' === $tab_key ) {
					$tab_icon_class = 'dashicons-chart-bar';
				}
				?>
				<span class="sfp-tab-label"><span class="dashicons <?php echo esc_attr( $tab_icon_class ); ?>" aria-hidden="true"></span><span><?php echo esc_html( $tab_label ); ?></span></span>
			</a>
		<?php endforeach; ?>
	</h2>

	<form method="post" action="options.php">
		<?php settings_fields( 'sfp_options' ); ?>

		<?php if ( 'general' === $active_tab ) : ?>
			<?php sfp_render_settings_section_table( $page, 'sfp_performance_privacy' ); ?>
			<?php sfp_render_settings_section_table( $page, 'sfp_placeholder' ); ?>
			<?php sfp_render_settings_section_table( $page, 'sfp_locale' ); ?>

			<?php if ( ! sfp_render_settings_section_table( $page, 'ssp_pro_display_rules' ) ) : ?>
				<?php sfp_render_pro_placeholder_settings( 'display_rules', $upgrade_url ); ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( 'popup' === $active_tab ) : ?>
			<?php if ( ! sfp_render_settings_section_table( $page, 'ssp_pro_popup', false ) ) : ?>
				<?php sfp_render_pro_placeholder_settings( 'popup', $upgrade_url, false ); ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( 'float' === $active_tab ) : ?>
			<?php if ( ! sfp_render_settings_section_table( $page, 'ssp_pro_float', false ) ) : ?>
				<?php sfp_render_pro_placeholder_settings( 'float', $upgrade_url, false ); ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( 'analytics' === $active_tab ) : ?>
			<?php if ( ! sfp_render_settings_section_table( $page, 'ssp_pro_analytics', false ) ) : ?>
				<?php sfp_render_pro_placeholder_settings( 'analytics', $upgrade_url, false ); ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php submit_button( __( 'Save Changes', 'simple-like-page-plugin' ) ); ?>
	</form>
</div>
