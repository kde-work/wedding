<?php
add_shortcode('wedding_todo', 'wb_todo_shortcode');
function wb_todo_shortcode ($atts) {
	wb_scripts__todo ();
	$empty_settings = 0;
	extract( shortcode_atts( array(
			'empty_settings' => 'Sorry, you need to fill in the settings.'
		), $atts )
	);
	ob_start();
	if ( is_user_logged_in() ) {
		$assigned_list = WeddingToDoClass::get_assigned_list();
		$user_id = wp_get_current_user()->ID;
		$wb_bride = get_user_meta($user_id, 'wb_bride', 1);
		$wb_groom = get_user_meta($user_id, 'wb_groom', 1);
		$wb_date = get_user_meta($user_id, 'wb_date', 1);
		$wb_total = get_user_meta($user_id, 'wb_total_input', 1);
//		$wb_number_of_guests = get_user_meta($user_id, 'wb_number_of_guests', 1);
//		$wb_img1 = get_user_meta($user_id, 'wb_img1', 1);
//		$wb_img2 = get_user_meta($user_id, 'wb_img2', 1);
//		delete_user_meta($user_id, 'wb_todo');
		if (!$wb_bride OR !$wb_groom OR !$wb_date OR !$wb_total) {
			return $empty_settings;
		}
		WeddingToDoClass::pre_install();
		$items = unserialize(base64_decode(get_user_meta($user_id, 'wb_todo', 1)));
		datepicker_js();
		?>
		<table class="wb-todo-table">
			<tr>
				<td class="wb-todo-table__td wb-todo-table__td--left">
					<div class="wb-todo-filter">
						<div class="wb-todo-filter__box">
							<div class="wb-todo-filter__title">Filter list</div>
							<div class="wb-todo-filter__group wb-todo-group wb-todo-group--checkbox wb-todo-group--1">
								<div class="wb-todo-group__title">Categories</div>
								<div class="wb-todo-group__body">
								<?php
								$list_of_groups = WeddingToDoClass::get_list_of_groups('ToDoGroups', $items);
								foreach ($list_of_groups as $group) {
									?>
									<input type="checkbox" id="wb-todo-checkbox--<?php echo $group['term_id']; ?>" class="wb-todo-filter__click wb-todo-group__checkbox wb-todo-group__checkbox--<?php echo $group['term_id']; ?>" data-type="category" data-id="<?php echo $group['term_id']; ?>" autocomplete="off">
									<label for="wb-todo-checkbox--<?php echo $group['term_id']; ?>" class="wb-todo-group__label wb-todo-group__label--<?php echo $group['term_id']; ?> ns ll"><?php echo $group['name']; ?></label>
                                    <br>
									<?php
								}
								?>
								</div>
							</div>
							<div class="wb-todo-filter__group wb-todo-group wb-todo-group--list wb-todo-group--2">
								<div class="wb-todo-group__title">Expire when</div>
								<div class="wb-todo-group__body">
                                    <div class="wb-todo-group__filter-item">
                                        <input type="radio" name="wb-todo-filter__expire" class="wb-todo-filter__click wb-todo-group__expire wb-todo-group__expire--10-d" data-type="expire" data-id="10-d" id="wb-todo-group__expire--10-d" autocomplete="off">
                                        <label for="wb-todo-group__expire--10-d" class="ll ns">Next 10 days</label>
                                    </div>
                                    <div class="wb-todo-group__filter-item">
                                        <input type="radio" name="wb-todo-filter__expire" class="wb-todo-filter__click wb-todo-group__expire wb-todo-group__expire--30-d" data-type="expire" data-id="30-d" id="wb-todo-group__expire--30-d" autocomplete="off">
                                        <label for="wb-todo-group__expire--30-d" class="ll ns">Next 30 days</label>
                                    </div>
                                    <div class="wb-todo-group__filter-item">
                                        <input type="radio" name="wb-todo-filter__expire" class="wb-todo-filter__click wb-todo-group__expire wb-todo-group__expire--show-all" data-type="expire" data-id="show-all" id="wb-todo-group__expire--show-all" checked="checked" autocomplete="off">
                                        <label for="wb-todo-group__expire--show-all" class="ll ns">Show all</label>
                                    </div>
                                    <div class="wb-todo-group__filter-item">
                                        <input type="radio" name="wb-todo-filter__expire" class="wb-todo-filter__click wb-todo-group__expire wb-todo-group__expire--show-expired" data-type="expire" data-id="show-expired" id="wb-todo-group__expire--show-expired" autocomplete="off">
                                        <label for="wb-todo-group__expire--show-expired" class="ll ns">Show only expired</label>
                                    </div>
								</div>
							</div>
							<div class="wb-todo-filter__group wb-todo-group wb-todo-group--checkbox wb-todo-group--3">
								<div class="wb-todo-group__title">Assigned to</div>
								<div class="wb-todo-group__body">
									<?php
									foreach ($assigned_list as $assigned) {
//                                        if ($assigned!='bride' && $assigned!='groom') {
                                        ?>
                                        <input
                                            type="checkbox"
                                            id="wb-todo-group__assigned--<?php echo str_replace( ' ', '-', $assigned ); ?>"
                                            class="wb-todo-filter__click wb-todo-group__assigned wb-todo-group__assigned--<?php echo str_replace( ' ', '-', $assigned ); ?>"
                                            autocomplete="off"
                                            data-type="assigned"
                                            data-id="<?php echo str_replace( ' ', '-', $assigned ); ?>">
                                        <label
                                            for="wb-todo-group__assigned--<?php echo str_replace( ' ', '-', $assigned ); ?>"
                                            class="wb-todo-group__label ns ll">
                                            <?php echo $assigned; ?>
                                        </label><br>
                                        <?php
//                                        }
									}
									?>
								</div>
							</div>
                            <div class="wb-todo-filter__group wb-todo-group wb-todo-group--checkbox wb-todo-group--4">
                                <div class="wb-todo-group__body">
                                    <input type="checkbox" id="wb-todo__hide-done" checked class="wb-todo-filter__click wb-todo-group__checkbox wb-todo__hide-empty" data-type="done" data-id="1" autocomplete="off">
                                    <label for="wb-todo__hide-done" class="wb-todo-group__label ns ll"><b>Show completed items</b></label>
                                </div>
                            </div>
						</div>
					</div>
				</td>
				<td class="wb-todo-table__td wb-todo-table__td--right">
                    <div class="wb-search-input">
                        <input type="text" id="wb-search-input" autocomplete="off" class="wb-search__input" name="search-input" placeholder="Search for task ...">
                        <div class="wb__loading"></div>
                    </div>
                    <div class="wb-todo-ajax-box">
	                    <?php
	                    echo wb_todo_content_template($items);
	                    ?>
                    </div>
				</td>
			</tr>
		</table>
		<?php
	}
	$output = ob_get_clean();
	return $output;
}

function wb_todo_content_template($items) {
	ob_start();

	if (current_user_can('administrator')) {
	    echo "<a href='.?reset-todo'>Reset TODO data for admin</a>";
    }

	?>
    <table class="wb-todo">
        <thead>
        <tr class="wb__tr">
            <th class="wb__th wb__th--done">Done</th>
            <th class="wb__th wb__th--name">Name</th>
            <th class="wb__th wb__th--due-date">Due date</th>
            <th class="wb__th wb__th--details">Details</th>
            <th class="wb__th wb__th--assigned-to">Assigned to</th>
            <th class="wb__th wb__th--in-budget">In budget</th>
        </tr>
        </thead>
        <tbody>
		<?php
		$html = array();
		foreach ($items as $item) {
			if (isset($item['status']) AND $item['status']=='delete') {
				continue;
			}
			$end_time = $item['end_time'];
			$comparing_dates = WeddingToDoClass::comparing_dates($end_time, time());
			$expires = round($comparing_dates/24/60/60) + 1;
			if (!is_array($item['categories']) AND !count($item['categories'])) {
				$cats = implode(',', WeddingToDoClass::get_id_array_of_cats($item['ID']));
			} else {
				$cats = implode(',', $item['categories']);
			}
			ob_start();
			?>
            <tr class="wb__tr wb__tr--item wb-todo__tr <?php
                echo ($expires < 0) ? 'wb__tr--expire ' : '';
                echo WeddingToDoClass::get_group_classes ($item['ID'], 'wb-todo__tr-category', $cats), ' ';
                echo WeddingToDoClass::get_assigned_classes ($item['assigned'], 'wb-todo__tr-assigned'), ' ';
                echo ($item['done']) ? 'wb-todo__tr-done--1' : 'wb-todo__tr-done--0';
                ?>" data-id="<?php echo $item['ID']; ?>" data-expire="<?php echo $expires; ?>">
                <td class="wb__td wb__td--done" data-id="<?php echo $item['ID']; ?>">
                    <div class="wb-todo-table__done-box">
                        <input type="checkbox" name="wb-todo-table__done" class="wb-todo-table__done" id="wb-todo-table__done--<?php echo $item['ID']; ?>" <?php echo ($item['done']) ? 'checked' : ''; ?> autocomplete="off" data-id="<?php echo $item['ID']; ?>">
                        <label for="wb-todo-table__done--<?php echo $item['ID']; ?>" class="wb-todo-table__done-label wb-todo-table__done-label--<?php echo ($item['done']) ? '1' : '0'; ?>" title="Done!"></label>
                    </div>
                </td>
                <td class="wb__td wb__td--name" data-id="<?php echo $item['ID']; ?>">
                    <div class="wb-todo-table__title"><?php echo $item['name']; ?></div>
					<?php
					if ($expires < 0) {
						?>
                        <div class="wb-todo-table__date wb-todo-table__date--red">Date over due!
                        </div>
						<?php
					} else {
						?>
                        <div class="wb-todo-table__date">Expires in  <?php echo $expires; ?> day<?php echo ($comparing_dates > 1)?'s':''; ?></div>
						<?php
					}
					?>
                </td>
                <td class="wb__td wb__td--due-date" data-id="<?php echo $item['ID']; ?>">
					<?php /*echo WeddingToDoClass::get_date($item['date']['start_time'], $item['date']['number_days'], 'd. F, Y');*/ ?>
					<?php echo date('d. F, Y', $end_time); ?>
                </td>
                <td class="wb__td wb__td--details" data-id="<?php echo $item['ID']; ?>">
                    <div class="wb__detail wb__detail--plus" data-id="<?php echo $item['ID']; ?>"></div>
                </td>
                <td class="wb__td wb__td--assigned-to" data-id="<?php echo $item['ID']; ?>">
					<?php echo $item['assigned']; ?>
                </td>
                <td class="wb__td wb__td--in-budget" data-id="<?php echo $item['ID']; ?>">
					<?php
					if ($item['in_budget'] AND $item['in_budget'] != 'empty') {
						?>
                        <div class="wb-todo-table__coin wb-todo-table__coin--gold" data-budget="<?php echo $item['in_budget']; ?>"></div>
						<?php
					} else {
						?>
                        <div class="wb-todo-table__coin wb-todo-table__coin--gray"></div>
						<?php
					}
					?>
                </td>
            </tr>
            <tr class="wb-todo-table__settings wb-todo-table__settings--<?php echo $item['ID']; ?>">
                <td colspan="6">
                    <div class="td-add-new td-add-new--settings">
						<?php
						/*$date = date('Y-m-d', $end_time);
						$assigned = str_replace(array(' ', ','), array('-', ''), $item['assigned']);
						echo do_shortcode(
							"[wb_todo_settings 
                                                    id='{$item['ID']}' 
                                                    note='{$item['notes']}' 
                                                    name='{$item['name']}' 
                                                    date='{$date}' 
                                                    assigned='{$assigned}' 
                                                    category='$cats' 
                                                    wedding_budget='{$item['in_budget']}'
                                                ]"
						);*/
						?>
                    </div>
                </td>
            </tr>
			<?php
			$output = ob_get_clean();
			$ym = date( 'Ym', $end_time );
			if (!isset($html[$ym])) {
				$html[$ym] = array();
			}
			array_push($html[$ym], array(
				'time' => $end_time,
				'val' => $item['name'],
				'html' => $output,
				'expires' => $expires
			));
		}
		ksort($html);
		foreach ($html as $key => $val) {
			usort($html[$key], function($a, $b){
				if($a['expires'] === $b['expires'])
					return 0;
				return $a['expires'] > $b['expires'] ? 1 : -1;
			});
		}
		//
		$val = '';
		foreach ($html as $items) {
			$html_i = '';
			foreach ($items as $tr) {
				$val .= $tr['val'] . ' ';
				$html_i .= $tr['html'];
			}
			$date_item = date( 'F Y', $items[0]['time'] );
			?>
            <tr class="wb__tr wb-todo__tr wb__tr--month">
                <td colspan="6" class="wb__month">
                    <div class="wb__date-title"
                         data-val="<?php echo str_replace( '"', "'", $val ); ?>"><?php echo $date_item; ?></div>
                </td>
            </tr>
			<?php
			$val = '';
			echo $html_i;
		}
		?>
        </tbody>
    </table>
    <div class="td-add-new-btn">
        <div class="wb-add-new-btn__btn _wb-button wb-add-new__button">Add a new task</div>
    </div>
    <div class="td-add-new wb-todo-table__new">
        <h4>Add a new task</h4>
		<?php
		echo do_shortcode("[wb_todo_settings note='' name='' date='' assigned='' category='' wedding_budget='']");
		?>
    </div>
	<?php
    $output = ob_get_clean();
    return $output;
}
