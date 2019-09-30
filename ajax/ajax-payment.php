<?php
add_action('wp_ajax_wp-buy', 'wp_buy_callback');
//add_action('wp_ajax_nopriv_wp-buy', 'wp_buy_callback');
function wp_buy_callback() {
//	$role = WeddingPayment::check_role();
//	if ( $role['slug'] == WeddingPayment::$paid AND $role['is_expired'] == 1 )
	$bambora = new WeddingPaymentBambora();
	$response = $bambora->request( WB_VIP_STATUS_COST );
//	$request = $response['request'];
	$session = $response['session'];

	$html = '';
//	ob_start();
//	print_r($data);
//	$html = ob_get_clean();
	if ( empty( $response ) ) {
		echo json_encode(array(
			'ErrorMessage' => 'Server error',
			'response' => $session,
			'test' => $html
		));
	}

	if ( isset( $session->meta ) AND isset( $session->token ) AND $session->meta->result ) {
		echo json_encode(array(
			'token' => $session->token,
			'response' => $session,
			'ErrorMessage' => '',
			'test' => $html
		));
	} else {
		echo json_encode(array(
			'ErrorMessage' => $session->meta->message->enduser,
			'response' => $session,
			'test' => $html
		));
	}
	die;
}