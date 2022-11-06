<?php
add_action('wp_ajax_wp-buy', 'wp_buy_callback');
//add_action('wp_ajax_nopriv_wp-buy', 'wp_buy_callback');
function wp_buy_callback() {
//	if( ! wp_verify_nonce( $_POST['nonce_code'], 'wedding-nonce' ) ) die( 'Stop!' );
	$type = $_POST['type'];
	$bambora = new WeddingPaymentBambora();
	$wedding_payment = new WeddingPayment();
	$response = $bambora->request( $wedding_payment->get_cost_by_type( $type ), $type );
//	$request = $response['request'];
	$session = $response['session'];

	switch ( $type ) {
		case 'VIP':
		case 'vip':
			ShoutOut::push_event( 4 );
			break;
		case 'standard':
			ShoutOut::push_event( 5 );
			break;
	}
	$html = '';
//	ob_start();
//	print_r($data);
//	$html = ob_get_clean();
	if ( empty( $response ) ) {
		echo json_encode(array(
			'ErrorMessage' => 'Server error',
			'response' => $session,
			'test' => '2'
		));
		die;
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
			'response' => $response,
			'session' => $session,
			'test' => $html
		));
	}
	die;
}