<?php
add_action( 'init', 'wb_shout_out_activation' );
function wb_shout_out_activation() {
	if( !wp_next_scheduled( 'wb_shout_out' ) ) {
		wp_clear_scheduled_hook( 'wb_shout_out' );
		wp_schedule_event( time(), 'daily', 'wb_shout_out' );
	}
}

if( defined('DOING_CRON') && DOING_CRON ) {
	add_action( 'wb_shout_out', 'wb_shout_out_handler' );
}

function wb_shout_out_handler() {
	global $wpdb;

	$total = $wpdb->get_var( "SELECT COUNT(*) FROM `{$wpdb->prefix}wb_event_list`" );
	if ( $total > 80 ) {
		$total = $total - 80;
		return $wpdb->query( "DELETE FROM `{$wpdb->prefix}wb_event_list` LIMIT $total" );
	}
	return false;
}

add_action( 'init', 'wb_shout_out' );
function wb_shout_out() {
	if (isset($_GET['test_wb_shout_out'])) {
		wb_shout_out_handler();
		die;
	}
}