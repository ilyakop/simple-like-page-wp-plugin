<?php 
global $sfplugin;
if ( ! apply_filters( 'sfp_should_render_embed', true, isset( $instance ) ? $instance : array() ) ) {
	return;
}
// Check if shortcode return string and not bool 
if ( is_string( $hide_cover ) ) 	$hide_cover 	= ( $hide_cover 	== 'true') ? true : false;
if ( is_string( $show_facepile 	) ) $show_facepile 	= ( $show_facepile 	== 'true') ? true : false;
if ( is_string( $small_header 	) ) $small_header 	= ( $small_header 	== 'true') ? true : false;
if ( is_string( $timeline ) ) 		$timeline 		= ( $timeline 		== 'true') ? true : false;
if ( is_string( $events ) ) 		$events 		= ( $events 		== 'true') ? true : false;
if ( is_string( $messages ) ) 		$messages 		= ( $messages 		== 'true') ? true : false;

$like_box_classes = array( "sfp-container" );
$like_box_classes = apply_filters( "sfp_like_box_classes", $like_box_classes, $instance );
$like_box_classes = implode( " ", $like_box_classes );

$tabs = array();

if ( $timeline === true ) 	$tabs[] = "timeline";
if ( $events === true ) 	$tabs[] = "events";
if ( $messages === true ) 	$tabs[] = "messages";   

$options = $sfplugin->getPluginOptions();
$click_to_load = isset( $instance['click_to_load'] ) ? $instance['click_to_load'] : $options['click_to_load'];
$lazy_load = isset( $instance['lazy_load'] ) ? $instance['lazy_load'] : $options['lazy_load'];
$placeholder_text = isset( $instance['placeholder_text'] ) && $instance['placeholder_text'] !== '' ? $instance['placeholder_text'] : $options['placeholder_text'];
$placeholder_text = $placeholder_text ? $placeholder_text : 'Click to load Facebook content';
$placeholder_bg_color = isset( $instance['placeholder_bg_color'] ) && $instance['placeholder_bg_color'] !== '' ? $instance['placeholder_bg_color'] : $options['placeholder_bg_color'];
$placeholder_text_color = isset( $instance['placeholder_text_color'] ) && $instance['placeholder_text_color'] !== '' ? $instance['placeholder_text_color'] : $options['placeholder_text_color'];

if ( is_string( $click_to_load ) ) 	$click_to_load 	= ( $click_to_load 	== 'true') ? true : false;
if ( is_string( $lazy_load ) ) 		$lazy_load 		= ( $lazy_load 		== 'true') ? true : false;
$click_to_load = (bool) $click_to_load;
$lazy_load = (bool) $lazy_load;

$placeholder_styles = array();
$placeholder_bg_color = sanitize_hex_color( $placeholder_bg_color );
$placeholder_text_color = sanitize_hex_color( $placeholder_text_color );
$placeholder_bg_tint = $placeholder_bg_color;
if ( $placeholder_bg_color ) {
	$hex = ltrim( $placeholder_bg_color, '#' );
	if ( strlen( $hex ) === 6 ) {
		$rgb = array(
			hexdec( substr( $hex, 0, 2 ) ),
			hexdec( substr( $hex, 2, 2 ) ),
			hexdec( substr( $hex, 4, 2 ) )
		);
		$tint_ratio = 0.6;
		$rgb = array_map(
			function( $value ) use ( $tint_ratio ) {
				return (int) round( $value + ( 255 - $value ) * $tint_ratio );
			},
			$rgb
		);
		$placeholder_bg_tint = sprintf( '#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2] );
	}
}
if ( $placeholder_bg_color ) {
	$placeholder_styles[] = 'background: linear-gradient(135deg, ' . $placeholder_bg_color . ' 0%, ' . $placeholder_bg_tint . ' 100%)';
}
if ( $placeholder_text_color ) {
	$placeholder_styles[] = 'color: ' . $placeholder_text_color;
}
$placeholder_style_attr = $placeholder_styles ? ' style="' . esc_attr( implode( '; ', $placeholder_styles ) ) . '"' : '';

$sfplugin->enqueueFrontendAssets();

$container_attributes = array(
	'class' => $like_box_classes,
	'data-sfp-embed' => '1',
	'data-sfp-url' => esc_url( $url ),
	'data-sfp-width' => $width,
	'data-sfp-height' => $height,
	'data-sfp-hide-cover' => ( $hide_cover ) ? 'true' : 'false',
	'data-sfp-show-facepile' => ( $show_facepile ) ? 'true' : 'false',
	'data-sfp-small-header' => ( $small_header ) ? 'true' : 'false',
	'data-sfp-tabs' => implode( ",", $tabs ),
	'data-sfp-locale' => $locale,
	'data-sfp-click-to-load' => $click_to_load ? '1' : '0',
	'data-sfp-lazy' => $lazy_load ? '1' : '0',
);
$container_attributes = apply_filters( 'sfp_container_attributes', $container_attributes, $instance );

?>
<!-- SFPlugin by topdevs.net -->
<!-- Page Plugin Code START -->
<div<?php foreach ( $container_attributes as $attr_name => $attr_value ) : ?> <?php echo esc_attr( $attr_name ); ?>="<?php echo esc_attr( $attr_value ); ?>"<?php endforeach; ?>>
	<button
		type="button"
		class="sfp-placeholder"
		<?php echo $placeholder_style_attr; ?>
		<?php echo $click_to_load ? '' : 'aria-label="' . esc_attr( $placeholder_text ) . '"'; ?>
	>
		<?php echo $click_to_load ? esc_html( $placeholder_text ) : ''; ?>
	</button>
	<div class="sfp-embed" aria-live="polite"></div>
</div>
<!-- Page Plugin Code END -->
