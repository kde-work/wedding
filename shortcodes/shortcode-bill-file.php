<?php
function wb_bill_file_shortcode( $atts ) {
	global $wpdb;

	$atts = shortcode_atts( array(
			'text' => 'Bill file',
		), $atts
	);
	$user_id = wp_get_current_user()->ID;

	if ( $user_id ) {
		$folder = wp_upload_dir()['baseurl'] . "/bill-pdf";
		$bill_file = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT `bill_file` FROM `{$wpdb->prefix}wb_payment` 
                 WHERE `user_id` = '%d' AND `status` = 'complete' ORDER BY `id` DESC LIMIT 1
                ", $user_id
			)
		);
		return "<a href='{$folder}/{$bill_file}'>{$atts['text']}</a>";
	}
	return '';
}
add_shortcode( 'wb_bill_file', 'wb_bill_file_shortcode' );