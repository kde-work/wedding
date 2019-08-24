<?php
function get_clear_id(&$key) {
	if (strpos($key, '--') !== false) {
		$id = substr($key, strpos($key, '--')+2);
		$key = substr($key, 0, strpos($key, '--'));
		return (int)$id;
	} else {
		return false;
	}
}

// Saved Guest List
add_action('wp_ajax_gl_save', 'gl_save_callback');
add_action('wp_ajax_nopriv_gl_save', 'gl_save_callback');
function gl_save_callback() {
	$user_id = wp_get_current_user()->ID;

	$data = array();

	foreach ($_POST as $key => $val) {
		if (!$val OR strpos($key, '--empty_line')) {
			continue;
		}

		$id = get_clear_id($key);
		if ($id === false) {
			continue;
		}

		if (!is_array($data[$id])) {
			$data[$id] = array();
		}

		if (
			$key == 'name'
			OR $key == 'family'
			OR $key == 'notes'
		) {
			$data[$id][$key] = wb_clean_str(addslashes($val));
		} else {
			$data[$id][$key] = addslashes($val);
		}
	}

	update_user_meta($user_id, 'gl_save', base64_encode(serialize($data)));
//	print_r($data);
	die;
}