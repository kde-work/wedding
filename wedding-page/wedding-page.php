<?php

/**
 * Wedding Page.
 *
 * @author   Wedding
 * @category Admin
 * @package  Wedding/Classes
 * @version  0.0.1
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WeddingPage {

	/**
	 * ID of templates.
	 *
	 * @var $template
	 */
	public static $template = [444214];

	/*
	 * Get list of user pages
	 */
	public function get_pages() {
		$query = array(
			'author' => get_current_user_id(),
			'post_type' => 'WeddingPage',
			'posts_per_page' => '1',
		);
		return new WP_Query( $query );
	}

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'init_components' ), 5 );
	}
	
	/**
	 * initial components.
	 */
	public static function init_components() {
		if( current_user_can( 'administrator' ) ) {
			$show_in_menu = true;
			$capabilities = [];
		} else {
			$show_in_menu = true;
			$capabilities = [
				'create_posts' => true,
				'edit_post' => true,
				'publish_posts' => true,
				'edit_others_posts' => false,
			];
		}
		
		register_post_type( 'WeddingPage',
			array(
				'labels' => array(
					'name' => 'WeddingPages',
					'singular_name' => 'WeddingPages'
				),
				'public' => true,
				'show_in_menu' => $show_in_menu,
				'capabilities' => $capabilities,
				'has_archive' => false,
				'exclude_from_search' => false,
				'show_in_nav_menus' => false,
				'rewrite' => array( 'slug' => 'WeddingPage' ),
				'supports'  => array( 'title', 'revisions', 'editor', 'author' ),
			)
		);
	}

	/**
	 * initial components.
	 *
	 * @param  int $id_template
	 * @return string
	 */
	public static function get_template_content( $id_template = 1 ) {
		switch ( $id_template ) {
			case 1 :
				return get_post( self::$template[0] )->post_content;
		}
		return get_the_content( self::$template[0] );
	}
}
WeddingPage::init();