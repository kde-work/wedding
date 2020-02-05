<?php
add_action('wp_ajax_wp-create-webp', 'wb_create_webp_callback');
function wb_create_webp_callback() {
	$template = ( intval( $_POST['template'] ) ) ? intval( $_POST['template'] ) : 1;
	$name = ( htmlspecialchars( $_POST['name'] ) ) ? htmlspecialchars( $_POST['name'] ) : '';

	$post_id = WeddingPage::create_page( $template, $name );

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

// Save Page name
add_action('wp_ajax_wp-save-name', 'wp_save_name_callback');
function wp_save_name_callback() {
	$name = ( htmlspecialchars( $_POST['name'] ) ) ? htmlspecialchars( $_POST['name'] ) : '';
	$wedding_page = new WeddingPage();
	echo json_encode(array(
		'answer' => $wedding_page->save_page_name( $name ),
		'html' => wedding_webp_shortcode( ['is_short' => true], null ),
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