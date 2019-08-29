<?php
add_shortcode('tableplan', 'wb_tableplan_shortcode');
function wb_tableplan_shortcode ($atts) {
	if ( is_user_logged_in() ) {
		wb_scripts__tableplan();
		$user_id = wp_get_current_user()->ID;
		$items = unserialize(base64_decode(get_user_meta($user_id, 'tp_save', 1)));
		$params = shortcode_atts( array(
			'title' => '',
		), $atts );
		ob_start();
		?>
		<div class="tp">
			<div class="wb__loading"></div>
			<div class="gl__guest-titel">Table plan</div>
            <div class="tableplan">

            </div>
		</div>
		<?php
	}
	return ob_get_clean();
}