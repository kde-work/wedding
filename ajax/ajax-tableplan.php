<?php
// Saved Table Plan
add_action('wp_ajax_wb-tableplan-save', 'wb_tableplan_save');
//add_action('wp_ajax_nopriv_gl_save', 'gl_save_callback');
function wb_tableplan_save() {
	$user_id = wp_get_current_user()->ID;
	$data = $_POST['data'];

	if (!$data['Id'] OR $data['Id'] == -1) {
		$data['Id'] = time();
	}

	$server_data = unserialize(base64_decode(get_user_meta($user_id, 'wb_tableplan', 1)));
	if (is_array($server_data)) {
		$server_data = array();
	}
	$server_data[$data['Id']] = array(
		'data' => $data
	);

	update_user_meta($user_id, 'wb_tableplan', base64_encode(serialize($server_data)));

	ob_start();
	print_r($data);
	$html = ob_get_clean();
	echo json_encode(array(
		'ErrorMessage' => '',
		'PlanID' => $data['Id'],
		'test' => $html
	));
	die;
}

// Delete Table Plan
add_action('wp_ajax_wb-tableplan-delete', 'wb_tableplan_delete');
//add_action('wp_ajax_nopriv_gl_save', 'gl_save_callback');
function wb_tableplan_delete() {
	$user_id = wp_get_current_user()->ID;
	$id = intval($_POST['id']);

	$server_data = unserialize(base64_decode(get_user_meta($user_id, 'wb_tableplan', 1)));

	if (isset($server_data[$id])) {
		unset($server_data[$id]);
		update_user_meta($user_id, 'wb_tableplan', base64_encode(serialize($server_data)));
	}

	echo json_encode(array(
		'ErrorMessage' => '',
		'Message' => 'Success Removing',
	));
	die;
}