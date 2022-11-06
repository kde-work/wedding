<?php
add_action('wp_ajax_wpb-save', 'wpb_save_callback');
function wpb_save_callback() {
	if ( !empty( $_POST ) AND wp_verify_nonce( $_POST['wpb'], 'wpb_action' ) ) {
		$return = array();
		$data = $_POST;
		unset( $data['_wp_http_referer'], $data['action'], $data['wpb'] );

		$return['query'] = $data;
		$return['answer'] = update_user_meta( wp_get_current_user()->ID, 'wpb_save', $data );

		$template = ( intval( $data['template'] ) ) ? intval( $data['template'] ) : 1;
		$wedding_page = new WeddingPage();
		$return['is_change_template'] = $wedding_page->change_template( $template, $data['page-url'] );
		$return['is-page-url'] = $wedding_page->save_page_name( sanitize_title( $data['page-url'] ) );
		$return['is-password'] = $wedding_page->save_password( $data['password'] );
		$return['url'] = get_the_permalink( WeddingBudgetClass::get_option( 'wedding-page-id' ) );

//		ShoutOut::push_event( 13 );
		echo json_encode( $return );
		die;
	}
	die;
}

add_action('wp_ajax_wb-creator', 'wb_creator_callback');
function wb_creator_callback() {
	if ( count( $_POST ) ) {
		$return = array();
		$query = $_POST['query'];

		if ( $query == 'img_url_by_ID' ) {
			$id = intval( $_POST['id'] );
			$size = addslashes( $_POST['size'] );
			$return['url'] = wp_get_attachment_image_src( $id, $size, true )[0];
			$return['size'] = $size;
			$return['id'] = $id;
		}

		echo json_encode( $return );
		die;
	}
	die;
}

add_action('wp_ajax_wp-create-webp', 'wb_create_webp_callback');
function wb_create_webp_callback() {
	$template = ( intval( $_POST['template'] ) ) ? intval( $_POST['template'] ) : 1;
	$name = ( htmlspecialchars( $_POST['name'] ) ) ? htmlspecialchars( $_POST['name'] ) : '';

	$post_id = WeddingPage::create_page( $template, $name );

	ShoutOut::push_event( 14 );
	if ( $post_id ) {
		echo json_encode(array(
			'html' => wedding_webp_shortcode( ['is_short' => true], null ),
			'ErrorMessage' => '',
		));
	} else {
		echo json_encode(array(
			'ErrorMessage' => 'Empty page id',
			'test' => ''
		));
	}
	die;
}

// Check Page name
add_action('wp_ajax_wp-check-name', 'wp_check_name_callback');
function wp_check_name_callback() {
	$name = ( htmlspecialchars( $_POST['name'] ) ) ? htmlspecialchars( $_POST['name'] ) : '';
	echo json_encode(array(
		'answer' => WeddingPage::check_page_name( $name ),
		'ErrorMessage' => '',
	));
	die;
}

// Save Password
add_action('wp_ajax_wp-save-password', 'wp_save_password_callback');
function wp_save_password_callback() {
	$wedding_page = new WeddingPage();
	echo json_encode(array(
		'answer' => $wedding_page->save_password( $_POST['password'] ),
		'ErrorMessage' => '',
	));
	die;
}

// Save Page name
add_action('wp_ajax_wp-save-name', 'wp_save_name_callback');
function wp_save_name_callback() {
	$name = ( htmlspecialchars( $_POST['name'] ) ) ? htmlspecialchars( $_POST['name'] ) : '';
	$wedding_page = new WeddingPage();

//	ShoutOut::push_event( 15 );
	echo json_encode(array(
		'answer' => $wedding_page->save_page_name( $name ),
//		'html' => wedding_webp_shortcode( ['is_short' => true], null ),
		'value' => $name,
		'href' => "https://bryllupshjemmeside.no/{$name}",
		'ErrorMessage' => '',
	));
	die;
}

// Change Template of Wedding Page
add_action('wp_ajax_wp-change-template', 'wp_change_template_callback');
function wp_change_template_callback() {
	$template = ( htmlspecialchars( $_POST['template'] ) ) ? htmlspecialchars( $_POST['template'] ) : 1;
	$wedding_page = new WeddingPage();
	echo json_encode(array(
		'answer' => $wedding_page->change_template( $template ),
		'html' => wedding_webp_shortcode( ['is_short' => true], null ),
		'ErrorMessage' => '',
	));
	die;
}