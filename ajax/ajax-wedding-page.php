<?php
add_action('wp_ajax_wp-create-webp', 'wb_create_webp_callback');
function wb_create_webp_callback() {
	$template = ( intval( $_POST['template'] ) ) ? intval( $_POST['template'] ) : 1;

	$user_id = wp_get_current_user()->ID;
	$wb_bride = get_user_meta( $user_id, 'wb_bride', 1 );
	$wb_groom = get_user_meta( $user_id, 'wb_groom', 1 );
	$wb_date = get_user_meta( $user_id, 'wb_date', 1 );

	$title = "{$wb_bride}-{$wb_groom}-{$wb_date}";

	//Create post
	$post = array(
		'post_title' => $title,
		'post_content' => WeddingPage::get_template_content( $template ),
		'post_author' => $user_id,
		'post_status' => 'publish',
		'comment_status' => 'closed',
		'post_type' => 'WeddingPage'
	);
	$post_id = wp_insert_post( $post );

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