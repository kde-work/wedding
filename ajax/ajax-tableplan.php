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

	// Save Table information
	$server_data = unserialize(base64_decode(get_user_meta($user_id, 'wb_tableplan', 1)));
	if (!is_array($server_data)) {
		$server_data = array();
	}
	$server_data[$data['Id']] = array(
		'data' => $data
	);
	update_user_meta($user_id, 'wb_tableplan', base64_encode(serialize($server_data)));

	ShoutOut::push_event( 7 );

	// Save Guests information
	if (!empty($data['Guests'])) {
		$server_guests = unserialize(base64_decode(get_user_meta($user_id, 'gl_save', 1)));
		foreach ($data['Guests'] as $Guest) {
			if (isset($server_guests[$Guest['Id']])) {
				$server_table_data = unserialize(base64_decode($server_guests[$Guest['Id']]['table_data']));
				if (!is_array($server_table_data)) {
					$server_table_data = array();
				}
				$server_table_data[$data['Id']] = $Guest;
				$server_guests[$Guest['Id']]['table_data'] = base64_encode(serialize($server_table_data));
			}
//			echo "tablePlan.AddNewGuest('{$id}', '{$Guest['name']} {$Guest['family']}', {$table_data['Type']}, {$table_data['Meal']}, {$table_data['RSVP']}, '{$table_data['TableID']}', '{$table_data['SeatID']}');\n";
		}
		update_user_meta($user_id, 'gl_save', base64_encode(serialize($server_guests)));
	}

//	ob_start();
//	print_r($data);
//	$html = ob_get_clean();
	echo json_encode(array(
		'ErrorMessage' => '',
		'PlanID' => $data['Id'],
//		'test' => $html
	));
	die;
}

// Delete Table Plan
add_action('wp_ajax_wb-tableplan-delete', 'wb_tableplan_delete');
//add_action('wp_ajax_nopriv_gl_save', 'gl_save_callback');
function wb_tableplan_delete() {
	$user_id = wp_get_current_user()->ID;
	$id = intval($_POST['id']);

	ob_start();
	$server_data = unserialize(base64_decode(get_user_meta($user_id, 'wb_tableplan', 1)));
//	print_r($server_data);

	ShoutOut::push_event( 8 );

	if (isset($server_data[$id])) {
		unset($server_data[$id]);
		update_user_meta($user_id, 'wb_tableplan', base64_encode(serialize($server_data)));
	}
	print_r($server_data);
	$html = ob_get_clean();

	echo json_encode(array(
		'ErrorMessage' => '',
		'Message' => 'Success Removing',
		'test' => $html,
	));
	die;
}

// Duplicate Table Plan
add_action('wp_ajax_wb-tableplan-duplicate', 'wb_tableplan_duplicate');
//add_action('wp_ajax_nopriv_gl_save', 'gl_save_callback');
function wb_tableplan_duplicate() {
	$user_id = wp_get_current_user()->ID;
	$id = intval($_POST['id']);

	$server_data = unserialize(base64_decode(get_user_meta($user_id, 'wb_tableplan', 1)));

	$html = '';
	if (isset($server_data[$id])) {
		$new_id = time();
		$html = wb_tableplan_table_line($server_data[$id]['data']['Name'] . ' New', $new_id);
		$server_data[$new_id] = $server_data[$id];
		update_user_meta($user_id, 'wb_tableplan', base64_encode(serialize($server_data)));
	}

	ShoutOut::push_event( 9 );

	echo json_encode(array(
		'ErrorMessage' => '',
		'Message' => 'Success Duplicate',
		'HTML' => $html,
	));
	die;
}

// Print Table Plan
add_action('wp_ajax_wb-tableplan-print', 'wb_tableplan_print');
//add_action('wp_ajax_nopriv_gl_save', 'gl_save_callback');
function wb_tableplan_print() {
	print_r($_POST);
	die;
}