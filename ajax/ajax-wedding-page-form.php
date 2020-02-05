<?php

// Sending email. For user email form
add_action('wp_ajax_webp_form', 'webp_form_callback');
add_action('wp_ajax_nopriv_webp_form', 'webp_form_callback');
function webp_form_callback() {
	if ($_POST['email'] AND wp_verify_nonce($_POST['webp_form'], 'webp_form_action')) {
		$name = addslashes($_POST['name']);
		$phone = addslashes($_POST['phone']);
		$email = addslashes($_POST['email']);
		$message = addslashes($_POST['message']);
		wb_form_mail( $name, $email, $phone, $message ); // ../mail.php
	}
	die;
}