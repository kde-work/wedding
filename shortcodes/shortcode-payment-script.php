<?php
function wb_payment_script_shortcode() {
	wb_scripts__payment();
	$wedding_payment = new WeddingPayment();
	ob_start();
	echo $wedding_payment->create_bambora_online_checkout_payment_html();
	$role = $wedding_payment->check_role();
	if ( !$role['is_expired'] ) {
		echo "<style>";
		switch ( $role['role'] ) {
			case 'trial':
			case 'standard':
				echo ".wb-payment-button--trial{display:none;}";
				break;
			case 'vip':
				echo ".wb-payment-button--trial{display:none;}";
				echo ".wb-payment-button--standard{display:none;}";
				break;
		}
		echo "</style>";
	}
	return ob_get_clean();
}
add_shortcode( 'wb_payment_script', 'wb_payment_script_shortcode' );
