<?php
function wb_get_info_shortcode ($atts) {
	$filter = $empty_settings = $y_template = $d_template = 0;
	$template = '';
	extract( shortcode_atts( array(
			'filter' => 'bride',
			'template' => '%attending% of %total% guest is attending',
			'y_template' => '%years% år og ',
			'd_template' => '%days% dager igjen til',
			'empty_settings' => 'Sorry, you need to fill in the settings.'
		), $atts )
	);
	ob_start();
	$user_id = wp_get_current_user()->ID;
	$wb_bride = get_user_meta($user_id, 'wb_bride', 1);
	$wb_groom = get_user_meta($user_id, 'wb_groom', 1);
	$wb_date = get_user_meta($user_id, 'wb_date', 1);
	$wb_total = get_user_meta($user_id, 'wb_total_input', 1);
	if (!$wb_bride OR !$wb_groom OR !$wb_date OR !$wb_total) {
		return $empty_settings;
	}
//16 of 78 guest is attending
	switch ($filter) {
		case 'Role':
			$wp = new WeddingPayment();
			$role = $wp->check_role();
			return $role['name'];
		case 'Spent of budget':
			return '<span class="wb-rest__real"></span>';
		case 'Rest of budget':
			return '<span class="wb-total-spent__real"></span>';
		case 'Estimated spent on budget':
			return '<span class="wb-wedding-total-budget__val"></span>';

		case 'How many total tasks':
			return '<span class="wb-settings--total-tasks"></span>';
		case 'How many overdue':
			return '<span class="wb-settings--overdue"></span>';
		case 'How many future coming tasks':
			return '<span class="wb-settings--coming-tasks"></span>';
		case 'How many completed':
			return '<span class="wb-settings--completed"></span>';

		case 'How many total guests':
			return '<span class="wb-settings--total-guests"></span>';
		case 'How many attending Dinner':
			return '<span class="wb-settings--dinner"></span>';
		case 'How many attending Full day':
			return '<span class="wb-settings--full-day"></span>';
		case 'How many attending coffee':
			return '<span class="wb-settings--coffee"></span>';

		case 'How many Wishes':
			return '<span class="wb-settings--wishes"></span>';

		case 'How many total persons':
			$items = count(unserialize(base64_decode(get_user_meta($user_id, 'gl_save', 1))));
			return "<span class=\"wb-settings--total-persons\">{$items}</span>";
		case 'How many Women':
		case 'How many Men':
			$items = unserialize(base64_decode(get_user_meta($user_id, 'gl_save', 1)));
			$women = 0;
			$men = 0;
			foreach ($items as $guest_id => $val) {
				if ($val['gender'] == 'Dame') {
					$women++;
				} else {
					$men++;
				}
			}
			return ($filter == 'How many Women') ? "<span class=\"wb-settings--women\">$women</span>" : "<span class=\"wb-settings--men\">$men</span>";

		case 'bride':
			return $wb_bride;
		case 'groom':
			return $wb_groom;
		case 'Total wedding budget':
			return $wb_total;
		case 'Number of guests total':
			$wb_number_of_guests = get_user_meta($user_id, 'wb_number_of_guests', 1);
			return $wb_number_of_guests;
		case 'Guest is attending':
			$items = unserialize(base64_decode(get_user_meta($user_id, 'gl_save', 1)));
			$total = count($items);
			$attending = 0;
			foreach ($items as $item) {
				if ($item['status']) {
					$attending++;
				}
			}
			return str_replace(array('%attending%', '%total%'), array($attending, $total), $template);
		case 'Image 1 ID':
			$wb_img1 = get_user_meta($user_id, 'wb_img1', 1);
			if ($wb_img1) {
				return $wb_img1;
			} else {
				return 1530;
			}
		case 'Image 1 URL':
			$wb_img1 = get_user_meta($user_id, 'wb_img1', 1);
			if ($wb_img1) {
				return wp_get_attachment_image_src($wb_img1, 'large', true )[0];
			} else {
				return wp_get_attachment_image_src(1530, 'large', true )[0];
			}
		case 'Image 2 ID':
			$wb_img2 = get_user_meta($user_id, 'wb_img2', 1);
			if ($wb_img2) {
				return $wb_img2;
			} else {
				return 1529;
			}
		case 'Image 2 URL':
			$wb_img2 = get_user_meta($user_id, 'wb_img2', 1);
			if ($wb_img2) {
				return wp_get_attachment_image_src($wb_img2, 'large', true )[0];
			} else {
				return wp_get_attachment_image_src(1529, 'large', true )[0];
			}
		case 'Date D.M - Y':
			$month = array(
				'January' => 'Januar',
				'February' => 'Februar',
				'March' => 'Mars',
				'April' => 'April',
				'May' => 'Mai',
				'June' => 'Juni',
				'July' => 'Juli',
				'August' => 'August',
				'September' => 'September',
				'October' => 'Oktober',
				'November' => 'November',
				'December' => 'Desember'
			);
			return strtr(date('d. F, Y', strtotime($wb_date)), $month);
		case 'Date beauty':
			$date = strtotime($wb_date) - time();
			$year = 365*24*60*60;
			$years = floor($date / $year);
			$days = floor(($date - $year*$years)/60/60/24);
			$str = '';
			if ($years) {
//				$str = "$years år og ";
				$str = str_replace("%years%", $years, $y_template);
			}
//			$str .= "$days dager igjen til";
			$str .= str_replace("%days%", $days, $d_template);
			return $str;
		case 'Done/All':
			$items = unserialize(base64_decode(get_user_meta($user_id, 'wb_todo', 1)));
			$done = $all = 0;
			foreach ($items as $item) {
				if ($item['status'] != 'delete') {
					if ($item['done']) {
						$done++;
					}
					$all++;
				}
			}
			return "$done/$all";
		case 'Left Budget Estimate':
			$wb_saves = (get_user_meta(wp_get_current_user()->ID, 'wb_save', 1));
			$sum = 0;
			if ($wb_saves) {
				foreach ($wb_saves as $wb_save) {
					if ($wb_save['option'] != 'remove-line' AND $wb_save['estimate'] > 0) {
						$sum += $wb_save['estimate'];
					}
				}
			} else {
				return '—';
			}
			return number_format($wb_total - $sum, 1, '.', ',');
		case 'Left Budget Real':
			$wb_saves = (get_user_meta(wp_get_current_user()->ID, 'wb_save', 1));
			$sum = 0;
			if ($wb_saves) {
				foreach ($wb_saves as $wb_save) {
					if ($wb_save['option'] != 'remove-line' AND $wb_save['real'] > 0) {
						$sum += $wb_save['real'];
					}
				}
			} else {
				return '—';
			}
			return number_format($wb_total - $sum, 1, '.', ',');
	}
	return '';
}
add_shortcode('wb_get_info', 'wb_get_info_shortcode');