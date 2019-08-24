<?php

add_action('wp_ajax_wb_settings_delete_img', 'wb_settings_delete_img_callback');
add_action('wp_ajax_nopriv_wb_settings_delete_img', 'wb_settings_delete_img_callback');
function wb_settings_delete_img_callback() {
	$user_id = wp_get_current_user()->ID;
	$id = intval($_POST['id']);
	update_user_meta($user_id, "wb_img{$id}", '');
	echo $id;
	die;
}

add_action('wp_ajax_wb_settings', 'wb_settings_callback');
add_action('wp_ajax_nopriv_wb_settings', 'wb_settings_callback');
function wb_settings_callback() {
//    print_r(json_encode($_POST));
//	echo 12;
//    print_r($_FILES);
//	die;

	if (wp_verify_nonce($_POST['wb_settings_field'], 'wb_settings')) {
		$user_id = wp_get_current_user()->ID;

		$bride = addslashes($_POST['bride']);
		$groom = addslashes($_POST['groom']);
		$date = addslashes($_POST['date']);
		$total_input = addslashes($_POST['total-input']);
		$number_of_guests = intval($_POST['number_of_guests']);

//		print_r($_FILES);
//		var_dump($_FILES['files_1']['name']);
//		var_dump($_FILES['files_2']['name']);
//		die;
//		ob_start();
//		print_r($_FILES);
//		$output = ob_get_contents(); // $output == "blabla"
//		ob_end_clean(); // втихую отбрасывает содержимое буфера

//		echo json_encode(
//			array(
//				'status' => 1,
//				'img1' => $output
//			)
//		);
//		die;

		if ($_FILES['files_1']['name'] AND preg_match('/(\.gif$)|(\.jpg$)|(\.png$)|(\.jpeg$)/i', $_FILES['files_1']['name'], $matches)) {
			$thumbnail_id_1 = wb_adding_thumbnail ($_FILES['files_1']['tmp_name'], $bride);
			update_user_meta($user_id, 'wb_img1', $thumbnail_id_1);
		} else {
			$thumbnail_id_1 = get_user_meta($user_id, 'wb_img1', 1);
		}

		if ($_FILES['files_2']['name'] AND preg_match('/(\.gif$)|(\.jpg$)|(\.png$)|(\.jpeg$)/i', $_FILES['files_2']['name'], $matches)) {
			$thumbnail_id_2 = wb_adding_thumbnail ($_FILES['files_2']['tmp_name'], $groom);
			update_user_meta($user_id, 'wb_img2', $thumbnail_id_2);
		} else {
			$thumbnail_id_2 = get_user_meta($user_id, 'wb_img2', 1);
		}

		if (!$bride or !$groom or !$date or !$total_input) {
			echo json_encode(
				array(
					'status' => 'Error! Fields is empty'
				)
			);
			die;
		}

		update_user_meta($user_id, 'wb_bride', $bride);
		update_user_meta($user_id, 'wb_groom', $groom);
		update_user_meta($user_id, 'wb_date', $date);
		update_user_meta($user_id, 'wb_total_input', $total_input);
		update_user_meta($user_id, 'wb_number_of_guests', $number_of_guests);

		echo json_encode(
			array(
				'status' => 1,
				'img1' => ($thumbnail_id_1)?wp_get_attachment_image_src($thumbnail_id_1, 'thumbnail', true )[0]:'',
				'img2' => ($thumbnail_id_2)?wp_get_attachment_image_src($thumbnail_id_2, 'thumbnail', true )[0]:''
			)
		);
	}
	die;
}

function wb_adding_thumbnail ($img_path, $post_name, $post_id = false) {
	if (!empty($img_path) AND file_exists($img_path)) {
		require_once(ABSPATH .'wp-admin/includes/image.php');
		require_once(ABSPATH .'wp-admin/includes/file.php');
		require_once(ABSPATH .'wp-admin/includes/media.php');

		$iso9_table = array(
			'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Ѓ' => 'G`',
			'Ґ' => 'G`', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Є' => 'YE',
			'Ж' => 'ZH', 'З' => 'Z', 'Ѕ' => 'Z', 'И' => 'I', 'Й' => 'Y',
			'Ј' => 'J', 'І' => 'I', 'Ї' => 'YI', 'К' => 'K', 'Ќ' => 'K',
			'Л' => 'L', 'Љ' => 'L', 'М' => 'M', 'Н' => 'N', 'Њ' => 'N',
			'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
			'У' => 'U', 'Ў' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'TS',
			'Ч' => 'CH', 'Џ' => 'DH', 'Ш' => 'SH', 'Щ' => 'SH', 'Ъ' => '',
			'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA',
			'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'ѓ' => 'g',
			'ґ' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'є' => 'ye',
			'ж' => 'zh', 'з' => 'z', 'ѕ' => 'z', 'и' => 'i', 'й' => 'y',
			'ј' => 'j', 'і' => 'i', 'ї' => 'yi', 'к' => 'k', 'ќ' => 'k',
			'л' => 'l', 'љ' => 'l', 'м' => 'm', 'н' => 'n', 'њ' => 'n',
			'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
			'у' => 'u', 'ў' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts',
			'ч' => 'ch', 'џ' => 'dh', 'ш' => 'sh', 'щ' => 'sh', 'ь' => '',
			'ы' => 'y', 'ъ' => "", 'э' => 'e', 'ю' => 'yu', 'я' => 'ya'
		);
		$rep = Array (' ','/','.',',','+','*');

		$title = substr(strtr($post_name, $iso9_table), 0, 40);
		$title = str_replace($rep,'-',$title);

		$file_array['name'] = strtolower(rawurlencode($title).'.jpg');
		$file_array['tmp_name'] = $img_path;
		$thumbnail_id = media_handle_sideload($file_array, $post_id, $file_array['name']);
		if (!is_wp_error($thumbnail_id)) {
//			set_post_thumbnail($post_id, $thumbnail_id);
			update_post_meta($thumbnail_id, '_wp_attachment_image_alt', $post_name, true);
			return $thumbnail_id;
		}
	}
	return false;
}