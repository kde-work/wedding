<?phpadd_action('wp_ajax_wb_todo_delete', 'wb_todo_delete_callback');add_action('wp_ajax_nopriv_wb_todo_delete', 'wb_todo_delete_callback');function wb_todo_delete_callback() {	$user_id = wp_get_current_user()->ID;	$id = addslashes($_POST['id']);	$budget = addslashes($_POST['budget']);	if ($id) {		$items = unserialize(base64_decode(get_user_meta($user_id, 'wb_todo', 1)));		// Delete from Budget		if ($budget) {			$wb_saves = get_user_meta(wp_get_current_user()->ID, 'wb_save', 1);			$new_budget_items = array();			foreach ($wb_saves as $wb_save) {				if ($wb_save['id'] == 'clear-line' OR !$wb_save['id']) {					$budget_id_i = wb_clean_str($wb_save['name']);				} else {					$budget_id_i = $wb_save['id'];				}				if ($budget_id_i != $budget) {					array_push($new_budget_items, $wb_save);				} else {					array_push($new_budget_items, array(						'id' => $budget,						'option' => 'remove-line',						'group_id' => $wb_save['group_id']					));				}			}			update_user_meta($user_id, 'wb_save', ($new_budget_items));		}		// Delete from TO-DO		$new_items = array();		foreach ($items as $item) {			if ($item['ID'] != $id) {				array_push($new_items, $item);			} else {				array_push($new_items, array(					'ID' => $id,					'status' => 'delete'				));			}		}		update_user_meta($user_id, 'wb_todo', base64_encode(serialize($new_items)));//		ShoutOut::push_event( 12 );		echo json_encode(array(			'html' => wb_todo_content_template($new_items)		));	}	die;}add_action('wp_ajax_wb_todo_new', 'wb_todo_new_callback');add_action('wp_ajax_nopriv_wb_todo_new', 'wb_todo_new_callback');function wb_todo_new_callback() {	if (wp_verify_nonce($_POST['wb_todo_field'], 'wb_todo')) {		$user_id = wp_get_current_user()->ID;		$id = addslashes($_POST['id']);		$budget = addslashes($_POST['budget']);		$assigned = addslashes($_POST['assigned']);		$date = addslashes($_POST['date']);		$name = wb_clean_str(addslashes($_POST['name']));		$note = wb_clean_str(htmlspecialchars(addslashes($_POST['note'])));		$done = intval($_POST['done']);		$category = array();		if (!$name OR !$date) {			echo 0;			die;		}		foreach ($_POST as $key => $val) {			if (strpos($key, 'category') !== false) {				array_push($category, $val);			}		}		$items = unserialize(base64_decode(get_user_meta($user_id, 'wb_todo', 1)));		// if New item		if (!$id OR $id == 'new') {			$id = str_replace(' ', '-', $name);			array_push($items, array(				'ID' => $id,				'assigned' => $assigned,				'categories' => $category,				'end_time' => strtotime($date),				'in_budget' => $budget,				'name' => $name,				'done' => $done,				'end_time_by_email' => 0,				'notes' => $note			));			ShoutOut::push_event( 10, $user_id );		} else {			$new_items = array();			foreach ($items as $item) {				if ($item['ID'] == $id) {					array_push($new_items, array(						'ID' => $id,						'assigned' => $assigned,						'categories' => $category,						'end_time' => strtotime($date),						'in_budget' => $budget,						'name' => $name,						'done' => $done,						'end_time_by_email' => 0,						'notes' => $note					));				} else {					array_push($new_items, $item);				}			}			ShoutOut::push_event( 11, $user_id );			$items = $new_items;		}//		print_r(json_encode(serialize($items)));		update_user_meta($user_id, 'wb_todo', base64_encode(serialize($items)));//		print_r(json_encode(unserialize(get_user_meta($user_id, 'wb_todo', 1))));		echo json_encode(array(			'html' => wb_todo_content_template($items)		));	}	die;}function wb_clean_str($str) {	return str_replace(array("'", '"', ",", "[", "]"), array('&#39;', '&#34;', '&#44;', '&#91;', '&#93;'), $str);}