<?php
/**
 * Installation related functions and actions.
 *
 * @author   Wedding
 * @category Admin
 * @package  Wedding/Classes
 * @version  0.0.1
 */
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class WeddingToDoClass {

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action('init', array(__CLASS__, 'init_components'), 5);
	}

	/**
	 * initial components.
	 */
	public static function init_components() {
		if(current_user_can('administrator')) {
			$show_in_menu = true;
		} else {
			$show_in_menu = false;
		}

		// TO-DO taxonomy
		register_taxonomy('ToDoGroups', array('ToDo'),
			array(
				'labels' => array(
					'name' => __('To-do Groups'),
					'singular_name' => __('To-do'),
				),
				'public' => true,
				'hierarchical' => true,
			)
		);
		register_post_type('ToDo',
			array(
				'labels' => array(
					'name' => __('ToDo List'),
					'singular_name' => __('ToDo')
				),
				'public' => true,
				'show_in_menu' => $show_in_menu,
				'has_archive' => true,
				'exclude_from_search' => false,
				'show_in_nav_menus' => false,
				'publicly_queryable' => false,
				'rewrite' => array('slug' => 'ToDo'),
				'supports'  => array('title', 'revisions'),
				'taxonomies' => array('ToDoGroups')
			)
		);
	}

	public static function get_group_classes ($post_id, $class = 'wb-todo__tr', $cat_list = false) {
		if ($cat_list === false) {
			$cats = WeddingToDoClass::get_id_array_of_cats($post_id);
		} else {
			$cats = explode(',', $cat_list);
		}
		$str = '';
		foreach ($cats as $cat) {
			$str .= "$class--{$cat} ";
		}
		return $str;
	}

	public static function get_assigned_classes ($list, $class = 'wb-todo__tr') {
		$cats = explode(',', $list);
		$str = '';
		foreach ($cats as $cat) {
			$cat = str_replace(" ", '-', $cat);
			$cat = str_replace(array("'", '"', ",", "[", "]"), '', $cat);
			$str .= "$class--{$cat} ";
		}
		return $str;
	}

	public static function comparing_dates ($time1, $time2) {
		return ($time1 - $time2)?($time1 - $time2):false;
	}

	public static function get_date ($time, $days, $format = 'd. F, Y') {
		return date($format, $time + $days*24*60*60);
	}

	public static function array_of_cats_by_item ($item) {
		if (!is_array($item['categories']) AND !count($item['categories'])) {
			return WeddingToDoClass::get_id_array_of_cats($item['ID']);
		} else {
			return $item['categories'];
		}
	}

	public static function get_list_of_groups ($taxonomy = 'ToDoGroups', $posts = array()) {
		global $wpdb;
		if (!$posts OR !count($posts)) {
			return $wpdb->get_results(
				"SELECT terms.`term_id`, terms.`name`, term_taxonomy.`taxonomy` FROM `$wpdb->terms` as terms
				 INNER JOIN `$wpdb->term_taxonomy` as term_taxonomy
				    ON terms.`term_id` = term_taxonomy.`term_id`
				 WHERE 
			    term_taxonomy.`taxonomy`='$taxonomy'
	        ",
				ARRAY_A
			);
		} else {
			$cats = array();
			foreach ($posts as $item) {
				$cats_i = WeddingToDoClass::array_of_cats_by_item($item);
				foreach ($cats_i as $cat_i) {
					$flag = true;
					foreach ($cats as $cat) {
						if ($cat['term_id'] == $cat_i) {
							$flag = false;
							break;
						}
					}
					if ($flag) {
						array_push($cats, array('term_id' => $cat_i, 'name' => get_term($cat_i)->name));
					}
				}
			}
			return $cats;
		}
	}

	public static function get_list_of_posts ($term_id) {
		global $wpdb;
		return $wpdb->get_results(
			"SELECT DISTINCT posts.* FROM `$wpdb->posts` as posts
			 INNER JOIN `$wpdb->term_relationships` as term_relationships
			    ON posts.`ID` = term_relationships.`object_id`
			 WHERE 
			    term_relationships.`term_taxonomy_id`='$term_id'
			    AND posts.`post_status` = 'publish'
	        "
		);
	}

	public static function get_list_of_posts_by_taxonomy ($taxonomy = 'ToDoGroups') {
		global $wpdb;
		return $wpdb->get_results(
			"SELECT DISTINCT posts.* FROM `$wpdb->posts` as posts
			 INNER JOIN `$wpdb->term_relationships` as term_relationships
			    ON posts.`ID` = term_relationships.`object_id`
		     INNER JOIN `$wpdb->term_taxonomy` as term_taxonomy
			    ON term_relationships.`term_taxonomy_id` = term_taxonomy.`term_id`
			 WHERE 
			    term_taxonomy.`taxonomy`='$taxonomy' 
			    AND posts.`post_status` = 'publish'
	        "
		);
	}

	public static function get_id_array_of_cats ($post_id) {
		global $wpdb;
		$list_of_groups = $wpdb->get_results(
			"SELECT `term_taxonomy_id` FROM `$wpdb->term_relationships` as term_relationships
			 WHERE term_relationships.`object_id`='$post_id'
	        ",
			ARRAY_A
		);
		$cat_array = array();
		foreach ($list_of_groups as $group) {
			array_push($cat_array, $group['term_taxonomy_id']);
		}
		return $cat_array;
	}

	private static function find_elem_by_id ($array, $id) {
		$i = 0;
		foreach ($array as $item) {
			if ($item['ID'] == $id AND $id) {
				return $i;
			}
			$i++;
		}
		return false;
	}

	public static function pre_install () {
		$user_id = wp_get_current_user()->ID;
		$to_do = unserialize(base64_decode(get_user_meta($user_id, 'wb_todo', 1)));
		$wb_date = strtotime(get_user_meta($user_id, 'wb_date', 1));
		if (count($to_do) AND $to_do) {
			/*$to_do_array = array();
			$items = WeddingToDoClass::get_list_of_posts_by_taxonomy('ToDoGroups');
			foreach ($items as $item) {
				$item_pos = WeddingToDoClass::find_elem_by_id ($to_do, $item->ID);
				if ($item_pos !== false) {
					array_push($to_do_array, $to_do[$item_pos]);
				} else {
					$number_days = get_field('number_days', $item->ID);
					$wedding_budget_group = get_field('wedding_budget_group', $item->ID);
					array_push($to_do_array, array(
						'ID' => $item->ID,
						'name' => $item->post_title,
						'end_time' => ($wb_date - $number_days*24*60*60),
						'assigned' => 'both',
						'in_budget' => (is_object($wedding_budget_group))?$wedding_budget_group->ID:null,
						'notes' => '',
						'categories' => WeddingToDoClass::get_id_array_of_cats($item->ID)
					));
				}
			}
			update_user_meta($user_id, 'wb_todo', base64_encode(serialize($to_do_array)));*/
		} else {
			$to_do_array = array();
			$items = WeddingToDoClass::get_list_of_posts_by_taxonomy('ToDoGroups');
			foreach ($items as $item) {
				$number_days = get_field('number_days', $item->ID);
				$wedding_budget_groups = get_field('wedding_budget_group', $item->ID);
				$wedding_budget_group_ids = [];
				if ($wedding_budget_groups) {
					foreach ( $wedding_budget_groups as $wedding_budget_group ) {
						$wedding_budget_group_ids[] = $wedding_budget_group->ID;
					}
					$wedding_budget_group_ids = implode(",", $wedding_budget_group_ids);
				}
//				print_r($wedding_budget_group_ids); die;
				array_push($to_do_array, array(
					'ID' => $item->ID,
					'name' => $item->post_title,
					'end_time' => ($wb_date - $number_days*24*60*60),
					'assigned' => 'Begge',
					'in_budget' => $wedding_budget_group_ids,
					'notes' => '',
					'categories' => WeddingToDoClass::get_id_array_of_cats($item->ID)
				));
			}
			update_user_meta($user_id, 'wb_todo', base64_encode(serialize($to_do_array)));
		}
	}

	public static function get_assigned_list ($user_id = false) {
		if ($user_id === false) {
			$user_id = wp_get_current_user()->ID;
		}
		$to_do = unserialize(base64_decode(get_user_meta($user_id, 'wb_todo', 1)));
		$wb_bride = get_user_meta($user_id, 'wb_bride', 1);
		$wb_groom = get_user_meta($user_id, 'wb_groom', 1);
		$assigned_list = array(
			'Begge',
			($wb_bride)?$wb_bride:'bride',
			($wb_groom)?$wb_groom:'groom'
		);
		foreach ($to_do as $item) {
			$is_find = false;
			foreach ($assigned_list as $assigned) {
				if ($item['assigned'] == 'bride' && $wb_bride) {
					$item['assigned'] = $wb_bride;
				}
				if ($item['assigned'] == 'groom' && $wb_groom) {
					$item['assigned'] = $wb_groom;
				}
				if ($assigned == $item['assigned']) {
					$is_find = true;
					break;
				}
			}
			if (!$is_find and $item['assigned'])  {
				array_push($assigned_list, $item['assigned']);
			}
		}
		return $assigned_list;
	}
}
WeddingToDoClass::init();