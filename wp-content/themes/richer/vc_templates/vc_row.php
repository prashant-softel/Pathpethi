<?php
/** @var $this WPBakeryShortCode_VC_Row */
$output = $el_class = $bg_image = $bg_color = $bg_image_repeat = $font_color = $padding = $margin_bottom = $css = $full_width = $el_id = $parallax_image = $parallax = '';
$poster = $mp4 = $m4v = $webm = $ogv = $overlay = $extra_class = $video_open = $extra_class = '';
extract( shortcode_atts( array(
	'el_class' => '',
	'bg_image' => '',
	'bg_color' => '',
	'bg_image_repeat' => '',
	'font_color' => '',
	'padding' => '',
	'margin_bottom' => '',
	'full_width' => false,
	'parallax' => false,
	'parallax_image' => false,
	'css' => '',
	'el_id' => '',

	'poster'    => '',
    'mp4'     => '',
    'm4v'     => '',
    'webm'      => '',
    'ogv'     => '',
    'overlay'   => ''
), $atts ) );

$parallax_image_id = '';
$parallax_image_src = '';
$full_width = $full_width != '' ? 'stretch' : '';
$full_width_class = $full_width != '' ? 'vc_row_fullwidth ' : '';
// wp_enqueue_style( 'js_composer_front' );
wp_enqueue_script( 'wpb_composer_front_js' );
// wp_enqueue_style('js_composer_custom_css');

if($mp4 != '' || $m4v != '' || $webm != '' || $ogv != ''){
    $poster = wp_get_attachment_url($poster) ? wp_get_attachment_url($poster) : $poster;
    $overlay = wp_get_attachment_url($overlay) ? wp_get_attachment_url($overlay) : $overlay;
    if($poster != ''){
        $video_bg = 'background-image: url(' . $poster . ');';
    }
    if($overlay != '') {
        $overlay = 'background:url('.$overlay.') repeat;';
    }
    $extra_class = 'videosection ';
    //$video_open = '[videosection poster="'.$poster.'" mp4="'.$mp4.'" m4v="'.$m4v.'" webm="'.$webm.'" ogv="'.$ogv.'" text_color="'.$font_color.'" pad_top="0" pad_bottom="0" overlay="'.$overlay.'"]';
    //$video_close = '[/videosection]';
    $video_open = '<div class="video-wrap">
          <video id="videoId" width="1920" height="800" preload="auto" poster="'.$poster.'" autoplay="autoplay" loop="loop" muted="muted">
            <source src="' . esc_url($webm) . '" type="video/webm">
            <source src="' . esc_url($mp4) . '" type="video/mp4">
            <source src="' . esc_url($m4v) . '" type="video/m4v">
            <source src="' . esc_url($ogv) . '" type="video/ogg">
          </video>
      </div>
      <div class="video-poster" style="' . $video_bg . '"></div>
      <div class="video-overlay" style="' . $overlay . '"></div>';
}

$el_class = $this->getExtraClass( $el_class );

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'vc_row wpb_row '.$extra_class.$full_width_class . ( $this->settings( 'base' ) === 'vc_row_inner' ? 'vc_inner ' : '' ) . get_row_css_class() . $el_class . vc_shortcode_custom_css_class( $css, ' ' ), $this->settings['base'], $atts );

$style = $this->buildStyle( $bg_image, $bg_color, $bg_image_repeat, $font_color, $padding, $margin_bottom );
?>
	<div <?php echo isset( $el_id ) && ! empty( $el_id ) ? "id='" . esc_attr( $el_id ) . "'" : ""; ?> <?php
?>class="<?php echo esc_attr( $css_class ); ?><?php if ( ! empty( $parallax ) ): echo ' vc_general vc_parallax vc_parallax-' . $parallax; endif; ?><?php if ( ! empty( $parallax ) && strpos( $parallax, 'fade' ) ): echo ' js-vc_parallax-o-fade'; endif; ?><?php if ( ! empty( $parallax ) && strpos( $parallax, 'fixed' ) ): echo ' js-vc_parallax-o-fixed'; endif; ?>"
<?php
// parallax bg values

$bgSpeed = 1.5;
?>
<?php
if ( $parallax ) {
	wp_enqueue_script( 'vc_jquery_skrollr_js' );

	echo '
            data-vc-parallax="' . $bgSpeed . '"
        ';
}
if ( strpos( $parallax, 'fade' ) ) {
	echo '
            data-vc-parallax-o-fade="on"
        ';
}
if ( $parallax_image ) {
	$parallax_image_id = preg_replace( '/[^\d]/', '', $parallax_image );
	$parallax_image_src = wp_get_attachment_image_src( $parallax_image_id, 'full' );
	if ( ! empty( $parallax_image_src[0] ) ) {
		$parallax_image_src = $parallax_image_src[0];
	}
	echo '
                data-vc-parallax-image="' . $parallax_image_src . '"
            ';
}
?>
<?php echo $style; ?>><?php if ( $full_width != 'stretch' ) echo '<div class="container">';
echo wpb_js_remove_wpautop( $content );
if ( $full_width != 'stretch' ) echo '</div>';
echo $video_open;
?></div><?php echo $this->endBlockComment( 'row' );
/*if ( ! empty( $full_width ) ) {
	echo '<div class="vc_row-full-width"></div>';
}*/
