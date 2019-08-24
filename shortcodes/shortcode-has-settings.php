<?php
function wb_has_settings_shortcode( $atts, $content = null ) {
	if ( is_user_logged_in() && !is_null( $content ) && !is_feed() ) {
		$is_settings_made = 0;
		$message = '';
		extract( shortcode_atts( array(
				'is_settings_made' => '1',
				'message' => ''
			), $atts )
		);

		$user_id = wp_get_current_user()->ID;
		$wb_bride = get_user_meta($user_id, 'wb_bride', 1);
		$wb_groom = get_user_meta($user_id, 'wb_groom', 1);
		$wb_date = get_user_meta($user_id, 'wb_date', 1);
		$wb_total = get_user_meta($user_id, 'wb_total_input', 1);

		if (!$is_settings_made) {
			if (!$wb_bride OR !$wb_groom OR !$wb_date OR !$wb_total) {
				return do_shortcode($content);
			} else {
				return $message;
			}
		} else {
			if ($wb_bride AND $wb_groom AND $wb_date AND $wb_total) {
				return do_shortcode($content);
			} else {
				return $message;
			}
		}
	}
}
add_shortcode( 'wb_has_settings', 'wb_has_settings_shortcode' );

function wb_settings_redirect_shortcode( $atts, $content = null ) {
	$url = 0;
	extract( shortcode_atts( array(
			'url' => ''
		), $atts )
	);

	$user_id = wp_get_current_user()->ID;
	$wb_bride = get_user_meta($user_id, 'wb_bride', 1);
	$wb_groom = get_user_meta($user_id, 'wb_groom', 1);
	$wb_date = get_user_meta($user_id, 'wb_date', 1);
	$wb_total = get_user_meta($user_id, 'wb_total_input', 1);

	if ((!$wb_bride OR !$wb_groom OR !$wb_date OR !$wb_total) AND $url) {
		wp_safe_redirect($url);
	}
	return '';
}
add_shortcode( 'wb_has_settings', 'wb_has_settings_shortcode' );