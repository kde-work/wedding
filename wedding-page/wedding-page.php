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
	public static $template = [444464, 444524, 444664];

	/**
	 * WordPress pages.
	 *
	 * @var $template
	 */
	public static $pages;

	/*
	 * Get list of user pages.
	 */
	public function get_pages() {
		$query = array(
			'author' => get_current_user_id(),
//			'post_type' => 'WeddingPage',
			'post_type' => 'page',
			'posts_per_page' => '1',
			'meta_key' => '_wp_page_template',
			'meta_value' => 'weddingpage-page.php',
		);
		self::$pages = new WP_Query( $query );
		return self::$pages;
	}

	/*
	 * Get id of user Wedding page.
	 *
	 * @return int|bool
	 */
	public function get_page_id() {
		$pages = $this->get_pages();
		if ( !empty( $pages->posts ) ) {
			return $pages->posts[0]->ID;
		} else {
			return false;
		}
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
		$id = $id_template - 1;
		if ( isset( self::$template[$id] ) ) {
			return get_post( self::$template[$id] )->post_content;
		} else {
			return get_the_content( self::$template[0] );
		}
	}

	/**
	 * Generate Wedding page name.
	 *
	 * @return string
	 */
	public static function get_page_name() {
		if ( !empty( self::$pages->posts ) ) {
			return self::$pages->posts[0]->post_name;
		}

		$user_id = wp_get_current_user()->ID;
		$wb_bride = get_user_meta( $user_id, 'wb_bride', 1 );
		$wb_groom = get_user_meta( $user_id, 'wb_groom', 1 );
		$wb_date = get_user_meta( $user_id, 'wb_date', 1 );

		switch ( true ) {
			case self::check_page_name( $title = "{$wb_bride}-and-{$wb_groom}" ) :
				return $title;
			case self::check_page_name( $title = "{$wb_bride}-{$wb_groom}" ) :
				return $title;
			case self::check_page_name( $title = "{$wb_groom}-and-{$wb_bride}" ) :
				return $title;
			case self::check_page_name( $title = "{$wb_groom}-{$wb_bride}" ) :
				return $title;
			case self::check_page_name( $title = "{$wb_bride}-{$wb_groom}-{$wb_date}" ) :
			default:
				return $title;
		}
	}

	/**
	 * Save Wedding page name.
	 *
	 * @param  string $title
	 * @return bool
	 */
	public function save_page_name( $title ) {
		$id = $this->get_page_id();
		if ( !$id ) {
			return false;
		}

		wp_update_post( wp_slash( array(
			'ID' => $id,
			'post_name' => $title,
			'post_title' => $title,
		) ) );
		return true;
	}

	/**
	 * Change Template of Wedding Page.
	 *
	 * @param  int $template
	 * @return bool
	 */
	public function change_template( $template = 1 ) {
		$id = $this->get_page_id();
		if ( !$id ) {
			return false;
		}

		wp_update_post( wp_slash( array(
			'ID' => $id,
			'post_content' => self::get_template_content( $template ),
		) ) );
		return true;
	}

	/**
	 * Generate Wedding page name.
	 *
	 * @param  string $title
	 * @return bool
	 */
	public static function check_page_name( $title ) {
		$query = array(
			'author' => get_current_user_id(),
			'post_type' => 'page',
			'posts_per_page' => '1',
			'meta_key' => '_wp_page_template',
			'meta_value' => 'weddingpage-page.php',
		);
		$pages = new WP_Query( $query );
		if ( !empty( $pages->posts ) AND $pages->posts[0]->post_title == $title ) {
			return 'self';
		}

		return ( empty( get_page_by_path( $title, ARRAY_N ) ) ) ? true : false;
	}

	/**
	 * Create Wedding page name.
	 *
	 * @param  int $template
	 * @param  string $title
	 * @return int
	 */
	public static function create_page( $template = 1, $title = '' ) {
		$user_id = wp_get_current_user()->ID;
		$wb_bride = get_user_meta( $user_id, 'wb_bride', 1 );
		$wb_groom = get_user_meta( $user_id, 'wb_groom', 1 );
		$wb_date = get_user_meta( $user_id, 'wb_date', 1 );

		if ( !$title )
			$title = self::get_page_name();

		//Create post
		$post = array(
			'post_title' => $title,
			'post_content' => WeddingPage::get_template_content( $template ),
			'post_author' => $user_id,
			'post_status' => 'publish',
			'comment_status' => 'closed',
			'post_type' => 'page',
//		'post_type' => 'WeddingPage'
		);
		$post_id = wp_insert_post( $post );
		update_post_meta( $post_id, '_wp_page_template', 'weddingpage-page.php', 1 );
		update_post_meta( $post_id, '_wedding_template', $template, 1 );

		return $post_id;
	}
}
WeddingPage::init();