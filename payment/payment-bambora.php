<?php
/**
 * Payment Bambora.
 *
 * @author   Wedding
 * @category Admin
 * @package  Wedding/Classes
 * @version  0.0.1
 */
if (!defined('ABSPATH')) {
	exit;
}
class WeddingPaymentBambora {

	protected $merchant = 'T291916901';
	// OLD protected $merchant = 'T415488101';
	protected $accesstoken = 'wy2X9CVkXxdxp4Sxre0L';
	protected $secrettoken = '1c5crTturv1M2nTVRY0KpIdTkwMDbay5djhuS0FF';
	protected $currency = "NOK";

	/**
	 * Get the Bambora Api Key
	 *
	 * @param  string $amount
	 * @param  string $type
	 * @return array
	 */
	public function request( $amount, $type ) {
		$order_id = $this->get_order_id( $type );

		if ( $order_id === false OR $amount === false )
			return [];

		$request = array(
			"order" => array(
				"id" => $order_id,
			    "amount" => $amount,
			    "currency" => $this->currency
			),
			"url" => array(
				"accept" => $this->get_url( 'accept', $order_id ),
			    "cancel" => $this->get_url( 'cancel', $order_id ),
			    "callbacks" => [
			    	["url" => $this->get_url( 'callbacks', $order_id )]
			    ],
			),
		);

		$session = $this->set_session( $request );

		if ( isset( $session->token ) ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare(
				"UPDATE `{$wpdb->prefix}wb_payment` SET 
                            `session` = '%s',
                            `amount` = '%s',
                            `currency` = '%s',
                    		`status` = 'set_session'
                     WHERE `id` = '%d' AND
                           `user_id` = '%d'
                    ",
				$session->token,
				$amount,
				$this->currency,
				$order_id,
				wp_get_current_user()->ID
			) );
		}

		return [
			'request' => $request,
			'session' => $session,
		];
	}

	/**
	 * Get Bambora response.
	 *
	 * @param array $request
	 * @return mixed
	 */
	public function set_session( $request ) {
		$service_url = Payment_Endpoints::get_checkout_api_endpoint() . '/sessions' ;
		$json_data = wp_json_encode( $request );
		$checkout_response = $this->call_rest_service( $service_url, $json_data, 'POST' );

		return json_decode( $checkout_response );
	}

	/**
	 * Get the Bambora Api Key
	 */
	protected function get_api_key() {
		return $this->generate_api_key( $this->merchant, $this->accesstoken, $this->secrettoken );
	}

	/**
	 * Generate Bambora API key
	 *
	 * @param string $merchant
	 * @param string $accesstoken
	 * @param string $secrettoken
	 * @return string
	 */
	public function generate_api_key( $merchant, $accesstoken, $secrettoken ) {
		$combined = $accesstoken . '@' . $merchant . ':' . $secrettoken;
		$encoded_key = base64_encode( $combined );
		$api_key = 'Basic ' . $encoded_key;

		return $api_key;
	}

	/**
	 * Get the Bambora Api Key
	 *
	 * @param  string $type
	 * @return int | bool
	 */
	protected function get_order_id( $type ) {
		global $wpdb;

		if (!wp_get_current_user()->ID) return false;

		$time = time();
		$user_id = wp_get_current_user()->ID;

		$t = $wpdb->query( $wpdb->prepare(
			"INSERT INTO `{$wpdb->prefix}wb_payment` SET 
                    `date_start` = '%d',
                    `user_id` = '%d',
                    `type` = '%s',
                    `status` = 'new'
                ",
			$time,
			$user_id,
			$type
		) );

		if ( $t ) {
			$result = $wpdb->get_row(
				$wpdb->prepare( "SELECT `id` FROM {$wpdb->prefix}wb_payment
								WHERE `date_start` = '%d'
									AND `user_id` = '%d'",
					$time,
					$user_id
				),
				ARRAY_A );

			if ( isset( $result['id'] ) ) {
				return $result['id'];
			} else {
				return false;
			}
		}

		return false;
	}

	/**
	 * Get the Bambora Api Key
	 *
	 * @param  string $type
	 * @param  int $order_id
	 * @return string
	 */
	protected function get_url( $type, $order_id ) {
		$args = array( 'wb-payment' => $type, 'order_id' => $order_id);
//		return add_query_arg( $args , 'https://example.org' );
		return add_query_arg( $args , site_url( '/bekreftelse-pa-medlemskap/' ) );
	}

	/**
	 * Get the Bambora Api Key
	 *
	 * @param  array $getParameteres
	 * @return string | bool
	 */
	public function check_transaction( $getParameteres ) {
		// Check exists txnid!
		if ( empty( $getParameteres["txnid"] ) ) {
			return( "No GET(txnid) was supplied to the system!" );
		}
		// Check exists orderid!
		if ( empty( $getParameteres["orderid"] ) ) {
			return( "No GET(orderid) was supplied to the system!" );
		}
		// Check exists hash!
		if ( empty( $getParameteres["hash"] ) ) {
			return( "No GET(hash) was supplied to the system!" );
		}

		$service_url = Payment_Endpoints::get_merchant_endpoint() . '/transactions/' . $getParameteres["txnid"];
		$checkout_response = $this->call_rest_service( $service_url, wp_json_encode( [] ), 'GET' );
		$response = json_decode( $checkout_response );

		if( $response->meta->result == false ) {
			return( $response->meta->message->merchant );
		}

//		$transaction = $response->transaction;
		return true;
	}
	
	/**
	 * Call the rest service at the specified Url
	 *
	 * @param string $service_url
	 * @param mixed  $json_data
	 * @param string $method
	 * @return mixed
	 */
	private function call_rest_service( $service_url, $json_data, $method ) {

		$content_length = isset( $json_data ) ? strlen( $json_data ) : 0;
		$headers = array(
			'Content-Type: application/json',
			'Content-Length: ' . $content_length,
			'Accept: application/json',
			'Authorization: ' . $this->get_api_key(),
			'X-EPay-System: ' . "PHP/" . phpversion(),
		);

		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $method );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $json_data );
		curl_setopt( $curl, CURLOPT_URL, $service_url );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $curl, CURLOPT_FAILONERROR, false );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );

		$result = curl_exec( $curl );
		return $result;
	}

}