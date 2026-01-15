<?php 
global $sfplugin;
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

?>
<!-- SFPlugin by topdevs.net -->
<!-- Page Plugin Code START -->
<div
	class="<?php echo esc_attr( $like_box_classes ); ?>"
	data-sfp-embed="1"
	data-sfp-url="<?php echo esc_url( $url ); ?>"
	data-sfp-width="<?php echo esc_attr( $width ); ?>"
	data-sfp-height="<?php echo esc_attr( $height ); ?>"
	data-sfp-hide-cover="<?php echo esc_attr( ( $hide_cover ) ? 'true' : 'false'); ?>"
	data-sfp-show-facepile="<?php echo esc_attr( ( $show_facepile ) ? 'true' : 'false'); ?>"
	data-sfp-small-header="<?php echo esc_attr( ( $small_header ) ? 'true' : 'false'); ?>"
	data-sfp-tabs="<?php echo esc_attr( implode( ",", $tabs ) ); ?>"
	data-sfp-locale="<?php echo esc_attr( $locale ); ?>"
	data-sfp-click-to-load="<?php echo esc_attr( $click_to_load ? '1' : '0' ); ?>"
	data-sfp-lazy="<?php echo esc_attr( $lazy_load ? '1' : '0' ); ?>"
>
	<button type="button" class="sfp-placeholder"<?php echo $placeholder_style_attr; ?>>
		<?php echo esc_html( $placeholder_text ); ?>
	</button>
	<div class="sfp-embed" aria-live="polite"></div>
</div>
<!-- Page Plugin Code END -->
