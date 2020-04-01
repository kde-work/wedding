<?php
// Sending Wish email
add_action('wp_ajax_wl_email', 'wl_email_callback');
//add_action('wp_ajax_nopriv_wl_email', 'wl_email_callback');
function wl_email_callback() {
	if ($_POST['email'] AND wp_verify_nonce($_POST['wl_email'], 'wl_email_action')) {
		$email = addslashes($_POST['email']);
		ShoutOut::push_event( 18 );
		wb_mail( $email ); // ./mail.php
	}
	die;
}

// Saved Wish List
add_action('wp_ajax_wl_save', 'wl_save_callback');
add_action('wp_ajax_nopriv_wl_save', 'wl_save_callback');
function wl_save_callback() {
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
			OR $key == 'notes'
		) {
			$data[$id][$key] = wb_clean_str(addslashes($val));
		} else {
			$data[$id][$key] = addslashes($val);
		}
	}
	ShoutOut::push_event( 19 );
	update_user_meta($user_id, 'wl_save', base64_encode(serialize($data)));
	die;
}