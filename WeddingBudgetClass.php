<?php/** * Installation related functions and actions. * * @author   Wedding * @category Admin * @package  Wedding/Classes * @version  0.0.1 */if (!defined('ABSPATH')) {	exit;}class WeddingBudgetClass {	/**	 * Return option.	 *	 * @param  string $type	 * @param  string $def	 * @param  bool $is_user_page	 * @return string	 */	public static function get_option( $type, $def = '', $is_user_page = false ) {		if ( $is_user_page ) {			$user_id = get_user_id_by_current_url();		} else {			$user_id = wp_get_current_user()->ID;		}		switch ( $type ) {			case 'bride' :				$data = get_user_meta( $user_id, 'wb_bride', 1 );				break;			case 'groom' :				$data = get_user_meta( $user_id, 'wb_groom', 1 );				break;			case 'date' :				$data = get_user_meta( $user_id, 'wb_date', 1 );				break;			case 'number_of_guests' :				$data = get_user_meta( $user_id, 'wb_number_of_guests', 1 );				break;			case 'img1' :				$data = get_user_meta( $user_id, 'wb_img1', 1 );				break;			case 'img2' :				$data = get_user_meta( $user_id, 'wb_img2', 1 );				break;			case 'email' :				$data = get_userdata( $user_id )->get('user_email');				break;			case 'template' :				$data = get_user_meta( $user_id, 'wb_template', 1 );				break;			case 'wedding-page-id' :			case 'wb_page_id' :				$data = get_user_meta( $user_id, 'wb_page_id', 1 );				break;			default:				$data = $def;		}		return $data;	}	/**	 * Hook in tabs.	 */	public static function init() {		add_action('init', array(__CLASS__, 'init_components'), 5);	}	/**	 * initial components.	 */	public static function init_components() {		if(current_user_can('administrator')) {			$show_in_menu = true;		} else {			$show_in_menu = false;		}		// Wedding Budget taxonomy		register_taxonomy('WeddingBudgetGroups', array('WeddingBudget'),			array(				'labels' => array(					'name' => __('Wedding Budget Groups'),					'singular_name' => __('Wedding Budget Group'),				),				'public' => true,				'hierarchical' => true,			)		);		register_post_type('WeddingBudget',			array(				'labels' => array(					'name' => __('WeddingBudgets'),					'singular_name' => __('WeddingBudget')				),				'public' => true,				'show_in_menu' => $show_in_menu,				'has_archive' => true,				'exclude_from_search' => false,				'show_in_nav_menus' => false,				'rewrite' => array('slug' => 'WeddingBudget'),				'supports'  => array('title', 'revisions'),				'taxonomies' => array('WeddingBudgetGroups')			)		);	}	public static function is_in_todo ($id, $name) {		$user_id = wp_get_current_user()->ID;		$items = unserialize(base64_decode(get_user_meta($user_id, 'wb_todo', 1)));		foreach ($items as $item) {			if ( isset( $item['status'] ) AND $item['status'] == 'delete' ) {				continue;			}			if ( $id == 'clear-line' OR $id == '_clear-line' ) {				$budget_id = str_replace(' ', '-', $name);			} else {				$budget_id = $id;			}			if ($item['in_budget'] == $budget_id) {				return true;			}		}		return false;	}	public static function get_list_of_groups ($taxonomy = 'WeddingBudgetGroups') {		global $wpdb;		return $wpdb->get_results(			"SELECT terms.`term_id`, terms.`name`, term_taxonomy.`taxonomy` FROM `$wpdb->terms` as terms			 INNER JOIN `$wpdb->term_taxonomy` as term_taxonomy			    ON terms.`term_id` = term_taxonomy.`term_id`			 WHERE 			    term_taxonomy.`taxonomy`='$taxonomy'	        ",			ARRAY_A		);	}	public static function get_list_of_posts ($term_id) {		global $wpdb;		return $wpdb->get_results(			"SELECT DISTINCT posts.* FROM `$wpdb->posts` as posts			 INNER JOIN `$wpdb->term_relationships` as term_relationships			    ON posts.`ID` = term_relationships.`object_id`			 WHERE 			    term_relationships.`term_taxonomy_id`='$term_id'			    AND posts.`post_status` = 'publish'	        "		);	}}WeddingBudgetClass::init();