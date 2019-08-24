<?php

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
//                                print_r($list_of_groups);
								foreach ($list_of_groups as $group) {
//									$list_of_posts = WeddingToDoClass::get_list_of_posts($group['term_id']);
//									if (!count($list_of_posts)) {
//										continue;
//									}
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
					<?php
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
                                <tr class="wb__tr wb-todo__tr
                                          <?php echo WeddingToDoClass::get_group_classes ($item['ID'], 'wb-todo__tr-category', $cats); ?>
                                          <?php echo WeddingToDoClass::get_assigned_classes ($item['assigned'], 'wb-todo__tr-assigned'); ?>
                                          <?php echo ($item['done']) ? 'wb-todo__tr-done--1' : 'wb-todo__tr-done--0'; ?>
                                " data-id="<?php echo $item['ID']; ?>" data-expire="<?php echo $expires; ?>">
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
                                        <div class="wb-todo-table__coin wb-todo-table__coin--gold" data-budget="<?php echo $item['in_budget']; ?>">
                                            <img
                                                src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDU4LjAwNyA1OC4wMDciIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDU4LjAwNyA1OC4wMDc7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNTEycHgiIGhlaWdodD0iNTEycHgiPgo8ZyBpZD0iWE1MSURfMjJfIj4KCTxwYXRoIGlkPSJYTUxJRF8xNDNfIiBzdHlsZT0iZmlsbDojRkNDNjJEOyIgZD0iTTUyLjAwMywyOS4yMTF2MS4yMzhjMCw2LjkzMi0xMS42NDEsMTIuNTUxLTI2LDEyLjU1MSAgIGMtNy45MjYsMC0xNS4wMTktMS43MTQtMTkuNzg4LTQuNDE0QzcuODIzLDQ0LjgzMSwxOC43NTEsNTAsMzIuMDAzLDUwYzE0LjM1OSwwLDI2LTYuMDY4LDI2LTEzICAgQzU4LjAwMywzMy45NTIsNTUuNzQ3LDMxLjI2Niw1Mi4wMDMsMjkuMjExIi8+Cgk8cGF0aCBpZD0iWE1MSURfMTQyXyIgc3R5bGU9ImZpbGw6I0U0QUYxODsiIGQ9Ik0zMi4wMDMsNTBjLTE0LjM1OSwwLTI2LTYuMDY4LTI2LTEzdjguNDQ4YzAsNi45MzIsMTEuNjQxLDEyLjU1MiwyNiwxMi41NTIgICBzMjYtNS42MiwyNi0xMi41NTJWMzdDNTguMDAzLDQzLjkzMiw0Ni4zNjIsNTAsMzIuMDAzLDUwIi8+Cgk8cGF0aCBpZD0iWE1MSURfMTQxXyIgc3R5bGU9ImZpbGw6I0NFOTkxMjsiIGQ9Ik05LjAwMyw1MS4zNDljMC41OSwwLjUzOSwxLjI1OSwxLjA1NSwyLDEuNTQ1di04LjI5MSAgIGMtMC43NDEtMC41MS0xLjQxLTEuMDQ1LTItMS42MDNWNTEuMzQ5eiIvPgoJPHBhdGggaWQ9IlhNTElEXzE0MF8iIHN0eWxlPSJmaWxsOiNDRTk5MTI7IiBkPSJNNTMuMDAzLDQ0LjYwM3Y4LjI5MWMwLjc0MS0wLjQ4OSwxLjQxLTEuMDA2LDItMS41NDVWNDMgICBDNTQuNDE0LDQzLjU1OCw1My43NDUsNDQuMDkzLDUzLjAwMyw0NC42MDMiLz4KCTxwYXRoIGlkPSJYTUxJRF8xMzlfIiBzdHlsZT0iZmlsbDojQ0U5OTEyOyIgZD0iTTEzLjAwMyw1NC4wNmMwLjYzMiwwLjMyNywxLjMsMC42MzYsMiwwLjkyOXYtOC4xOTMgICBjLTAuNy0wLjMwOC0xLjM2OC0wLjYzMy0yLTAuOTc1VjU0LjA2eiIvPgoJPHBhdGggaWQ9IlhNTElEXzEzOF8iIHN0eWxlPSJmaWxsOiNDRTk5MTI7IiBkPSJNNDkuMDAzLDU0Ljk4OGMwLjctMC4yOTIsMS4zNjgtMC42MDIsMi0wLjkyOVY0NS44MiAgIGMtMC42MzIsMC4zNDItMS4zLDAuNjY4LTIsMC45NzVWNTQuOTg4eiIvPgoJPHBhdGggaWQ9IlhNTElEXzEzN18iIHN0eWxlPSJmaWxsOiNDRTk5MTI7IiBkPSJNMTcuMDAzLDU1Ljc0NWMwLjY0NiwwLjIyMSwxLjMxMywwLjQyNywyLDAuNjE5VjQ4LjI1ICAgYy0wLjY4Ny0wLjIwNC0xLjM1NC0wLjQyMy0yLTAuNjU2VjU1Ljc0NXoiLz4KCTxwYXRoIGlkPSJYTUxJRF8xMzZfIiBzdHlsZT0iZmlsbDojQ0U5OTEyOyIgZD0iTTQ1LjAwMyw1Ni4zNjRjMC42ODctMC4xOTIsMS4zNTQtMC4zOTgsMi0wLjYxOXYtOC4xNTEgICBjLTAuNjQ2LDAuMjMzLTEuMzEzLDAuNDUyLTIsMC42NTZWNTYuMzY0eiIvPgoJPHBhdGggaWQ9IlhNTElEXzEzNV8iIHN0eWxlPSJmaWxsOiNDRTk5MTI7IiBkPSJNMjIuMDAzLDU3LjA4NGMwLjY1MywwLjEzMiwxLjMyMSwwLjI1LDIsMC4zNTV2LTguMDQ0ICAgYy0wLjY3OS0wLjExMy0xLjM0Ny0wLjIzOS0yLTAuMzc5VjU3LjA4NHoiLz4KCTxwYXRoIGlkPSJYTUxJRF8xMzRfIiBzdHlsZT0iZmlsbDojQ0U5OTEyOyIgZD0iTTQwLjAwMyw1Ny40MzljMC42NzktMC4xMDYsMS4zNDctMC4yMjQsMi0wLjM1NXYtOC4wNjkgICBjLTAuNjUzLDAuMTQtMS4zMjEsMC4yNjYtMiwwLjM4VjU3LjQzOXoiLz4KCTxwYXRoIGlkPSJYTUxJRF8xMzNfIiBzdHlsZT0iZmlsbDojQ0U5OTEyOyIgZD0iTTI4LjAwMyw1Ny44OTljMC42NTgsMC4wNDksMS4zMjYsMC4wODMsMiwwLjEwN3YtOC4wMDMgICBjLTAuNjc0LTAuMDI2LTEuMzQyLTAuMDYyLTItMC4xMTVWNTcuODk5eiIvPgoJPHBhdGggaWQ9IlhNTElEXzEzMl8iIHN0eWxlPSJmaWxsOiNDRTk5MTI7IiBkPSJNMzQuMDAzLDU4LjAwN2MwLjY3NC0wLjAyNSwxLjM0Mi0wLjA1OCwyLTAuMTA3di04LjAxMiAgIGMtMC42NTgsMC4wNTMtMS4zMjYsMC4wODktMiwwLjExNlY1OC4wMDd6Ii8+Cgk8cGF0aCBpZD0iWE1MSURfMTMxXyIgc3R5bGU9ImZpbGw6I0U0QUYxODsiIGQ9Ik0yNi4wMDMsMzQuOTkzYy0xNC4zNTksMC0yNi02LjA2OC0yNi0xM3Y4LjQ0OGMwLDYuOTMyLDExLjY0MSwxMi41NTIsMjYsMTIuNTUyICAgczI2LTUuNjIsMjYtMTIuNTUydi04LjQ0OEM1Mi4wMDMsMjguOTI1LDQwLjM2MiwzNC45OTMsMjYuMDAzLDM0Ljk5MyIvPgoJPHBhdGggaWQ9IlhNTElEXzEzMF8iIHN0eWxlPSJmaWxsOiNDRTk5MTI7IiBkPSJNMy4wMDMsMzYuMzQyYzAuNTksMC41MzksMS4yNTksMS4wNTUsMiwxLjU0NXYtOC4yOTEgICBjLTAuNzQxLTAuNTEtMS40MS0xLjA0NS0yLTEuNjAyVjM2LjM0MnoiLz4KCTxwYXRoIGlkPSJYTUxJRF8xMjlfIiBzdHlsZT0iZmlsbDojQ0U5OTEyOyIgZD0iTTQ3LjAwMywyOS41OTZ2OC4yOTFjMC43NDEtMC40ODksMS40MS0xLjAwNiwyLTEuNTQ1di04LjM0OSAgIEM0OC40MTQsMjguNTUxLDQ3Ljc0NSwyOS4wODYsNDcuMDAzLDI5LjU5NiIvPgoJPHBhdGggaWQ9IlhNTElEXzEyOF8iIHN0eWxlPSJmaWxsOiNDRTk5MTI7IiBkPSJNNy4wMDMsMzkuMDUzYzAuNjMyLDAuMzI3LDEuMywwLjYzNiwyLDAuOTI5di04LjE5MyAgIGMtMC43LTAuMzA4LTEuMzY4LTAuNjMzLTItMC45NzVWMzkuMDUzeiIvPgoJPHBhdGggaWQ9IlhNTElEXzEyN18iIHN0eWxlPSJmaWxsOiNDRTk5MTI7IiBkPSJNNDMuMDAzLDM5Ljk4MWMwLjctMC4yOTIsMS4zNjgtMC42MDEsMi0wLjkyOXYtOC4yMzkgICBjLTAuNjMyLDAuMzQyLTEuMywwLjY2OC0yLDAuOTc1VjM5Ljk4MXoiLz4KCTxwYXRoIGlkPSJYTUxJRF8xMjZfIiBzdHlsZT0iZmlsbDojQ0U5OTEyOyIgZD0iTTExLjAwMyw0MC43MzhjMC42NDYsMC4yMjEsMS4zMTMsMC40MjcsMiwwLjYxOXYtOC4xMTQgICBjLTAuNjg3LTAuMjA0LTEuMzU0LTAuNDIzLTItMC42NTZWNDAuNzM4eiIvPgoJPHBhdGggaWQ9IlhNTElEXzEyNV8iIHN0eWxlPSJmaWxsOiNDRTk5MTI7IiBkPSJNMzkuMDAzLDQxLjM1N2MwLjY4Ny0wLjE5MiwxLjM1NC0wLjM5OCwyLTAuNjE5di04LjE1MSAgIGMtMC42NDYsMC4yMzMtMS4zMTMsMC40NTItMiwwLjY1NlY0MS4zNTd6Ii8+Cgk8cGF0aCBpZD0iWE1MSURfMTI0XyIgc3R5bGU9ImZpbGw6I0NFOTkxMjsiIGQ9Ik0xNi4wMDMsNDIuMDc3YzAuNjUzLDAuMTMyLDEuMzIxLDAuMjUsMiwwLjM1NXYtOC4wNDQgICBjLTAuNjc5LTAuMTEzLTEuMzQ3LTAuMjM5LTItMC4zNzlWNDIuMDc3eiIvPgoJPHBhdGggaWQ9IlhNTElEXzEyM18iIHN0eWxlPSJmaWxsOiNDRTk5MTI7IiBkPSJNMzQuMDAzLDQyLjQzM2MwLjY3OS0wLjEwNiwxLjM0Ny0wLjIyNCwyLTAuMzU1di04LjA2OSAgIGMtMC42NTMsMC4xNC0xLjMyMSwwLjI2Ni0yLDAuMzhWNDIuNDMzeiIvPgoJPHBhdGggaWQ9IlhNTElEXzEyMl8iIHN0eWxlPSJmaWxsOiNDRTk5MTI7IiBkPSJNMjIuMDAzLDQyLjg5M2MwLjY1OCwwLjA0OSwxLjMyNiwwLjA4MywyLDAuMTA3di04LjAwMyAgIGMtMC42NzQtMC4wMjYtMS4zNDItMC4wNjItMi0wLjExNVY0Mi44OTN6Ii8+Cgk8cGF0aCBpZD0iWE1MSURfMTIxXyIgc3R5bGU9ImZpbGw6I0NFOTkxMjsiIGQ9Ik0yOC4wMDMsNDNjMC42NzQtMC4wMjUsMS4zNDItMC4wNTgsMi0wLjEwN3YtOC4wMTIgICBjLTAuNjU4LDAuMDUzLTEuMzI2LDAuMDg5LTIsMC4xMTZWNDN6Ii8+Cgk8cGF0aCBpZD0iWE1MSURfMTIwXyIgc3R5bGU9ImZpbGw6I0ZGRDk0OTsiIGQ9Ik01MS4zMiwzMy4zMDJDNDguNjQzLDM4Ljg1OCwzOC4zMjksNDMsMjYuMDAzLDQzYy00LjYwNCwwLTguOTI2LTAuNTgtMTIuNjc3LTEuNTkzICAgYzMuNjI4LDIuNDYzLDEwLjA4NSw0LjU1OSwxOC42NzcsNC41NTljMTMuNjgyLDAsMjItNS4zMTEsMjItOC45NjZDNTQuMDAzLDM1Ljc4LDUzLjA2NCwzNC40ODYsNTEuMzIsMzMuMzAyIi8+Cgk8cGF0aCBpZD0iWE1MSURfMTE5XyIgc3R5bGU9ImZpbGw6I0ZGRDk0OTsiIGQ9Ik0zMS4wMDMsMzNjLTE0LjM1OSwwLTI2LTUuNjItMjYtMTIuNTUydi01LjY1MmMtMy4xNDEsMS45NjktNSw0LjQzOC01LDcuMjA0ICAgYzAsNi45MzIsMTEuNjQxLDEzLDI2LDEzYzYuOTE0LDAsMTMuMTkyLTEuNDA5LDE3Ljg0OS0zLjY0MkM0MC4wNjEsMzIuNDAxLDM1LjY3OCwzMywzMS4wMDMsMzMiLz4KCTxwYXRoIGlkPSJYTUxJRF8xMThfIiBzdHlsZT0iZmlsbDojRTRBRjE4OyIgZD0iTTMxLjAwMywyNC45OTNjLTE0LjM1OSwwLTI2LTYuMDY4LTI2LTEzdjguNDQ4YzAsNi45MzIsMTEuNjQxLDEyLjU1MiwyNiwxMi41NTIgICBjMTQuMzU5LDAsMjYtNS42MiwyNi0xMi41NTJ2LTguNDQ4QzU3LjAwMywxOC45MjUsNDUuMzYyLDI0Ljk5MywzMS4wMDMsMjQuOTkzIi8+Cgk8cGF0aCBpZD0iWE1MSURfMTE3XyIgc3R5bGU9ImZpbGw6I0NFOTkxMjsiIGQ9Ik04LjAwMywyNi4zNDJjMC41OSwwLjUzOSwxLjI1OSwxLjA1NSwyLDEuNTQ1di04LjI5MSAgIGMtMC43NDEtMC41MS0xLjQxLTEuMDQ1LTItMS42MDJWMjYuMzQyeiIvPgoJPHBhdGggaWQ9IlhNTElEXzExNl8iIHN0eWxlPSJmaWxsOiNDRTk5MTI7IiBkPSJNNTIuMDAzLDE5LjU5NnY4LjI5MWMwLjc0MS0wLjQ4OSwxLjQxLTEuMDA2LDItMS41NDV2LTguMzQ5ICAgQzUzLjQxNCwxOC41NTEsNTIuNzQ1LDE5LjA4Niw1Mi4wMDMsMTkuNTk2Ii8+Cgk8cGF0aCBpZD0iWE1MSURfMTE1XyIgc3R5bGU9ImZpbGw6I0NFOTkxMjsiIGQ9Ik0xMi4wMDMsMjkuMDUzYzAuNjMyLDAuMzI3LDEuMywwLjYzNiwyLDAuOTI5di04LjE5MyAgIGMtMC43LTAuMzA4LTEuMzY4LTAuNjMzLTItMC45NzVWMjkuMDUzeiIvPgoJPHBhdGggaWQ9IlhNTElEXzExNF8iIHN0eWxlPSJmaWxsOiNDRTk5MTI7IiBkPSJNNDguMDAzLDI5Ljk4MWMwLjctMC4yOTIsMS4zNjgtMC42MDEsMi0wLjkyOXYtOC4yMzkgICBjLTAuNjMyLDAuMzQyLTEuMywwLjY2OC0yLDAuOTc1VjI5Ljk4MXoiLz4KCTxwYXRoIGlkPSJYTUxJRF8xMTNfIiBzdHlsZT0iZmlsbDojQ0U5OTEyOyIgZD0iTTE2LjAwMywzMC43MzhjMC42NDYsMC4yMjEsMS4zMTMsMC40MjcsMiwwLjYxOXYtOC4xMTQgICBjLTAuNjg3LTAuMjA0LTEuMzU0LTAuNDIzLTItMC42NTZWMzAuNzM4eiIvPgoJPHBhdGggaWQ9IlhNTElEXzExMl8iIHN0eWxlPSJmaWxsOiNDRTk5MTI7IiBkPSJNNDQuMDAzLDMxLjM1N2MwLjY4Ny0wLjE5MiwxLjM1NC0wLjM5OCwyLTAuNjE5di04LjE1MSAgIGMtMC42NDYsMC4yMzMtMS4zMTMsMC40NTItMiwwLjY1NlYzMS4zNTd6Ii8+Cgk8cGF0aCBpZD0iWE1MSURfMTExXyIgc3R5bGU9ImZpbGw6I0NFOTkxMjsiIGQ9Ik0yMS4wMDMsMzIuMDc3YzAuNjUzLDAuMTMyLDEuMzIxLDAuMjUsMiwwLjM1NXYtOC4wNDQgICBjLTAuNjc5LTAuMTEzLTEuMzQ3LTAuMjM5LTItMC4zNzlWMzIuMDc3eiIvPgoJPHBhdGggaWQ9IlhNTElEXzExMF8iIHN0eWxlPSJmaWxsOiNDRTk5MTI7IiBkPSJNMzkuMDAzLDMyLjQzM2MwLjY3OS0wLjEwNiwxLjM0Ny0wLjIyNCwyLTAuMzU1di04LjA2OSAgIGMtMC42NTMsMC4xNC0xLjMyMSwwLjI2Ni0yLDAuMzhWMzIuNDMzeiIvPgoJPHBhdGggaWQ9IlhNTElEXzEwOV8iIHN0eWxlPSJmaWxsOiNDRTk5MTI7IiBkPSJNMjcuMDAzLDMyLjg5M2MwLjY1OCwwLjA0OSwxLjMyNiwwLjA4MywyLDAuMTA3di04LjAwMyAgIGMtMC42NzQtMC4wMjYtMS4zNDItMC4wNjItMi0wLjExNVYzMi44OTN6Ii8+Cgk8cGF0aCBpZD0iWE1MSURfMTA4XyIgc3R5bGU9ImZpbGw6I0NFOTkxMjsiIGQ9Ik0zMy4wMDMsMzNjMC42NzQtMC4wMjUsMS4zNDItMC4wNTgsMi0wLjEwN3YtOC4wMTIgICBjLTAuNjU4LDAuMDUzLTEuMzI2LDAuMDg5LTIsMC4xMTZWMzN6Ii8+Cgk8cGF0aCBpZD0iWE1MSURfMTA3XyIgc3R5bGU9ImZpbGw6I0ZDQzYyRDsiIGQ9Ik01Ny4wMDMsMTJjMCw2LjkzMi0xMS42NDEsMTMtMjYsMTNjLTE0LjM1OSwwLTI2LTYuMDY4LTI2LTEzICAgYzAtNi45MzIsMTEuNjQxLTEyLDI2LTEyQzQ1LjM2MiwwLDU3LjAwMyw1LjA2OCw1Ny4wMDMsMTIiLz4KCTxwYXRoIGlkPSJYTUxJRF8xMDZfIiBzdHlsZT0iZmlsbDojRkZEOTQ5OyIgZD0iTTMxLjAwMywyMC45NjZjLTEzLjY4MiwwLTIyLTUuMzEtMjItOC45NjZjMC0zLjY1NSw4LjMxOC05LDIyLTkgICBjMTMuNjgyLDAsMjIsNS4zNDUsMjIsOUM1My4wMDMsMTUuNjU2LDQ0LjY4NSwyMC45NjYsMzEuMDAzLDIwLjk2NiIvPgoJPHBhdGggaWQ9IlhNTElEXzEwNV8iIHN0eWxlPSJmaWxsOiNGMEM0MUI7IiBkPSJNMzcuNzg0LDEzLjM1OWMwLjgyLTAuMTc0LDEuMzAxLTAuNjgsMS4xMjQtMS4yNTdsLTAuMTktMC42MTYgICBjLTAuMTM5LTAuNDUzLTAuNjgtMC44MzEtMS4zNDMtMS4wMTljLTAuOTAyLTEuOTI3LTMuMjAyLTIuNjI1LTYuNjE4LTIuNjI1Yy0wLjE2MiwwLTAuMzIsMC4wMDQtMC40NzYsMC4wMSAgIGMtMS4xNjYsMC4wNDgtMi4zMjktMC4wNjQtMy4yMzYtMC4zOTZjLTAuMzctMC4xMzUtMC42NzEtMC4yNzYtMC44Ny0wLjQyM2MtMC4wOS0wLjA2Ni0wLjMwOC0wLjAyNC0wLjMwOCwwLjA2ICAgYzAsMC4xNDYsMC4wMTEsMC4zMTksMC4wNDMsMC41MTFjMC4xMjQsMC43NDctMC4wMjQsMC42MzUtMC43NjIsMS4zOGMtMC40MjgsMC40MzQtMC43NjksMC45MzQtMS4wMTYsMS40ODYgICBjLTAuNjU5LDAuMTg5LTEuMTk3LDAuNTY2LTEuMzM2LDEuMDE3bC0wLjE5LDAuNjE2Yy0wLjE3NywwLjU3NywwLjMwNCwxLjA4MywxLjEyNCwxLjI1N2MwLjIzNCwxLjQ3NiwxLjIzNywyLjc3MiwzLjQyNCwzLjQ4NiAgIGwtMC4xMiwxLjAzN2MtMC4wMzIsMC4yNzctMC4yNzEsMC41MzMtMC42MTksMC42NTlsLTQuNDExLDEuNTkyQzI0LjU4LDIwLjY3NCwyNy41MTYsMjEsMzAuNzU3LDIxICAgYzMuMTk2LDAsNi4wOTUtMC4zMTcsOC42NDUtMC44NDJsLTQuMzE1LTEuNjEzYy0wLjM0MS0wLjEyOC0wLjU3NS0wLjM4Mi0wLjYwNi0wLjY1NGwtMC4xMjEtMS4wNDUgICBDMzYuNTQ3LDE2LjEzMiwzNy41NSwxNC44MzUsMzcuNzg0LDEzLjM1OSIvPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo="
                                            width="28"/>
                                        </div>
                                        <?php
                                        } else {
                                        ?>
                                        <div class="wb-todo-table__coin wb-todo-table__coin--gray">
                                            <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTguMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDYwIDYwIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA2MCA2MDsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSIzMnB4IiBoZWlnaHQ9IjMycHgiPgo8Zz4KCTxwYXRoIGQ9Ik0xOS42MDMsMjEuMjk3bC0wLjI1NywwLjA5M2wzLjQ1LDAuNzIyQzI1LjYwNiwyMi43MDIsMjguNjIxLDIzLDMxLjc1MywyM2MxLjYxNSwwLDMuMTk3LTAuMDg0LDQuNzM3LTAuMjQxICAgYzIuNjM4LTAuMjM4LDUuMDY0LTAuNjc3LDcuMjM1LTEuMjY3bDAuMjczLTAuMDU2bC0wLjAzNC0wLjAxM0M1MC43ODMsMTkuNTE4LDU1LDE2LjE0Myw1NSwxM2MwLTQuNDI0LTguNjAzLTktMjMtOVM5LDguNTc2LDksMTMgICBDOSwxNi4wNzYsMTMuMDQsMTkuMzcyLDE5LjYwMywyMS4yOTd6IE0yOC40NiwxNi44OTVjLTEuNjIzLTAuNTI5LTIuNTQ2LTEuNDM1LTIuNzQ2LTIuNjkybC0wLjEwOC0wLjY3OWwtMC42NzMtMC4xNDMgICBjLTAuMTYxLTAuMDM0LTAuMjY5LTAuMDgyLTAuMzMzLTAuMTJsMC4xNDEtMC40NjJjMC4wMzYtMC4wNjYsMC4yNTMtMC4yNSwwLjY2My0wLjM2OGwwLjQ0OS0wLjEyOGwwLjE5LTAuNDI2ICAgYzAuMTk4LTAuNDQ0LDAuNDcyLTAuODQ1LDAuODEzLTEuMTljMC4xNS0wLjE1MiwwLjI3NC0wLjI2NSwwLjM3NC0wLjM1NmMwLjMyOS0wLjMsMC41NDQtMC41NTEsMC42NDYtMC44NzUgICBjMS4xOSwwLjM5MywyLjUyMSwwLjQzNCwzLjQ0MywwLjM5NWMwLjE0My0wLjAwNiwwLjI4OC0wLjAwOSwwLjQzNi0wLjAwOWM0LjI2MSwwLDUuMzAxLDEuMTY5LDUuNzEyLDIuMDQ4bDAuMTkzLDAuNDE0ICAgbDAuNDM5LDAuMTI1YzAuNDEyLDAuMTE3LDAuNjMxLDAuMzAyLDAuNjYsMC4zNTJsMC4xNDgsMC40ODFjLTAuMDY0LDAuMDM4LTAuMTcyLDAuMDg1LTAuMzMzLDAuMTE5TDM3LjksMTMuNTIzbC0wLjEwOCwwLjY4ICAgYy0wLjIsMS4yNTctMS4xMjQsMi4xNjMtMi43NDYsMi42OTJsLTAuNzc3LDAuMjUzbDAuMjE0LDEuODU4YzAuMDc2LDAuNjUsMC41NTQsMS4yMTUsMS4yNDksMS40NzVsMC43MzQsMC4yNzUgICBjLTAuMjQsMC4wMjYtMC40ODksMC4wMzYtMC43MzIsMC4wNTdjLTEuMTkzLDAuMDkxLTIuNDI2LDAuMTUzLTMuNzM1LDAuMTUzYy0xLjc0LDAtMy4zNzYtMC4wODktNC45MTEtMC4yNDVsMC42NjEtMC4yMzkgICBjMC43MDktMC4yNTYsMS4xOTctMC44MjUsMS4yNzMtMS40ODRsMC4yMTMtMS44NUwyOC40NiwxNi44OTV6IE0zMiw2YzEzLjk4MiwwLDIxLDQuNDg4LDIxLDdjMCwyLjA2LTQuMzE2LDUuNi0xMi4zNzksNy4xNzEgICBsLTQuMTctMS41NjJsLTAuMDEyLTAuMTA0YzIuMDA1LTAuODY4LDIuODU0LTIuMjAzLDMuMTkxLTMuMzcxYzAuNDctMC4yMDYsMC44MzctMC41MTUsMS4wNjQtMC45ICAgYzAuMjU1LTAuNDM0LDAuMzE0LTAuOTQsMC4xNjUtMS40MjRsLTAuMTg5LTAuNjE2Yy0wLjIwMS0wLjY1NC0wLjc4Ni0xLjIxMS0xLjYtMS41NDJjLTEuNDc2LTIuNTA3LTQuODU0LTIuODA5LTcuMzE3LTIuODA5ICAgYy0wLjE3NSwwLTAuMzQ4LDAuMDA0LTAuNTE4LDAuMDExYy0xLjExOCwwLjA0NS0yLjEzLTAuMDc0LTIuODUyLTAuMzM3Yy0wLjQwNy0wLjE0OC0wLjU2OS0wLjI1MS0wLjYxOS0wLjI4OCAgIGMtMC40MTYtMC4zMDgtMC45NTItMC4yNjgtMS4yOTktMC4wOTNsLTAuNjAzLDAuMzQydjAuNjE2YzAsMC4xOTMsMC4wMTQsMC40MjMsMC4wNTcsMC42NzVjMC4wMDIsMC4wMTUsMC4wMDUsMC4wMywwLjAwNywwLjA0NCAgIGMtMC4wMTUsMC4wMTMtMC4wMzEsMC4wMjctMC4wNDcsMC4wNDJjLTAuMTE5LDAuMTA5LTAuMjY3LDAuMjQ0LTAuNDQ2LDAuNDI1Yy0wLjQwNywwLjQxMi0wLjc1LDAuODc1LTEuMDIyLDEuMzgyICAgYy0wLjgwMSwwLjMzMi0xLjM3NiwwLjg4NC0xLjU3NiwxLjUzMWwtMC4xODksMC42MTdjLTAuMTQ4LDAuNDg0LTAuMDksMC45OSwwLjE2NiwxLjQyNGMwLjIyNywwLjM4NSwwLjU5NCwwLjY5NCwxLjA2MywwLjkgICBjMC4zMzcsMS4xNjgsMS4xODcsMi41MDMsMy4xOTEsMy4zNzFsLTAuMDEyLDAuMTAzbC00LjExMiwxLjQ4NEMxNS4xNTUsMTguNDgxLDExLDE1LjAyNCwxMSwxM0MxMSwxMC40ODgsMTguMDE4LDYsMzIsNnoiIGZpbGw9IiNkNmQ2ZDYiLz4KCTxwYXRoIGQ9Ik01NCwyOS42NDd2LTAuMjUyYzMuMTU1LTIuMjE5LDUtNC45NTQsNS03Ljk0N1YxM0M1OSw1LjU4OSw0Ny4zOTMsMCwzMiwwUzUsNS41ODksNSwxM3YyLjI3QzEuNzM4LDE3LjQ1NiwwLDIwLjExNiwwLDIzICAgdjguNDQ4YzAsMy4yOTcsMi4yMzUsNi4yODMsNiw4LjYwOXY2LjM5MUM2LDU0LjA0NywxNy44Niw2MCwzMyw2MHMyNy01Ljk1MywyNy0xMy41NTJWMzhDNjAsMzQuODE1LDU3LjkxNCwzMS45NDEsNTQsMjkuNjQ3eiAgICBNNTgsMzhjMCw2LjM5My0xMS42ODIsMTItMjUsMTJjLTAuNTc1LDAtMS4xNDYtMC4wMTUtMS43MTQtMC4wMzVjLTAuMTY5LTAuMDA2LTAuMzM4LTAuMDEyLTAuNTA3LTAuMDIgICBjLTAuNDk1LTAuMDIyLTAuOTg3LTAuMDUyLTEuNDc1LTAuMDljLTAuMzE4LTAuMDI1LTAuNjMzLTAuMDU1LTAuOTQ4LTAuMDg2Yy0wLjEzOS0wLjAxMy0wLjI3OC0wLjAyOC0wLjQxNi0wLjA0MiAgIGMtOC4yMjctMC44ODUtMTUuMjI1LTMuODUyLTE4LjI4Ni03LjZjLTAuMDQtMC4wNS0wLjA4My0wLjA5OS0wLjEyMi0wLjE1Yy0wLjA0Ni0wLjA1OS0wLjA4Ny0wLjExOS0wLjEzMi0wLjE3OSAgIGMxLjQwNywwLjYsMi45MzgsMS4xMyw0LjU4MSwxLjU3NUMxOC40Miw0Ni4yOTMsMjUuMzI3LDQ3Ljk2NSwzMyw0Ny45NjVjMTMuNTU0LDAsMjMtNS4yNTIsMjMtOS45NjUgICBjMC0xLjM4OS0wLjg0NC0yLjc3Mi0yLjQ0OC00LjAzMmMwLjAyNi0wLjA3MSwwLjA0NC0wLjE0NCwwLjA2OC0wLjIxNmMwLjAzNC0wLjEwNCwwLjA2OS0wLjIwNywwLjA5OC0wLjMxMSAgIGMwLjAzNC0wLjEyMSwwLjA2Mi0wLjI0MiwwLjA5LTAuMzY0YzAuMDI0LTAuMTA1LDAuMDQ5LTAuMjEsMC4wNjktMC4zMTZjMC4wMjMtMC4xMjMsMC4wMzgtMC4yNDcsMC4wNTQtMC4zNyAgIGMwLjAxNC0wLjEwNywwLjAyOS0wLjIxMywwLjAzOS0wLjMyYzAuMDAzLTAuMDMxLDAuMDA5LTAuMDYyLDAuMDExLTAuMDkzQzU2LjA1LDMzLjM3Nyw1OCwzNS4zOSw1OCwzOHogTTM3LjEzMSw1MS44MzYgICBjMC4wMy0wLjAwMiwwLjA1OS0wLjAwNiwwLjA4OS0wLjAwOGMwLjU3NC0wLjA0NywxLjEzOS0wLjEwNiwxLjctMC4xNzFjMC4xNDUtMC4wMTcsMC4yODktMC4wMzQsMC40MzMtMC4wNTIgICBjMC41NTUtMC4wNywxLjEwNi0wLjE0NSwxLjY0Ny0wLjIzMnY1Ljk5MmMtMS4yOTMsMC4yMDktMi42MjksMC4zNzEtNCwwLjQ3OHYtNS45OTdDMzcuMDQzLDUxLjg0MiwzNy4wODcsNTEuODQsMzcuMTMxLDUxLjgzNnogICAgTTQzLDUxLjAwNWMwLjA1Ny0wLjAxMiwwLjExLTAuMDI2LDAuMTY2LTAuMDM4YzAuNTM4LTAuMTE0LDEuMDctMC4yMzQsMS41OS0wLjM2NWMwLjA2OC0wLjAxNywwLjEzNC0wLjAzNiwwLjIwMi0wLjA1NCAgIGMwLjM1MS0wLjA5LDAuNjk5LTAuMTg0LDEuMDQxLTAuMjgxdjUuOTg5Yy0wLjk2MSwwLjI3Ny0xLjk2MywwLjUyNC0zLDAuNzRWNTEuMDA1eiBNNDgsNDkuNjM0ICAgYzAuMDU0LTAuMDE5LDAuMTA3LTAuMDM3LDAuMTYtMC4wNTZjMC4wNzYtMC4wMjcsMC4xNDgtMC4wNTUsMC4yMjQtMC4wODJjMC40MTMtMC4xNDksMC44MTctMC4zMDUsMS4yMTQtMC40NjUgICBjMC4xMTMtMC4wNDYsMC4yMjYtMC4wOTEsMC4zMzctMC4xMzdjMC4wMjItMC4wMDksMC4wNDQtMC4wMTgsMC4wNjYtMC4wMjd2NS45NjNjLTAuNjM4LDAuMjc3LTEuMzA0LDAuNTQtMiwwLjc4NlY0OS42MzR6ICAgIE01Miw0Ny45MzZjMC4xNzItMC4wODgsMC4zMzgtMC4xOCwwLjUwNS0wLjI3YzAuMDk3LTAuMDUyLDAuMTk0LTAuMTA0LDAuMjg5LTAuMTU3YzAuMzg2LTAuMjE2LDAuNzYxLTAuNDM4LDEuMTItMC42NjYgICBjMC4wMjgtMC4wMTgsMC4wNTctMC4wMzUsMC4wODUtMC4wNTN2NS44MjVjLTAuNjA4LDAuNDM0LTEuMjc2LDAuODUtMiwxLjI0M1Y0Ny45MzZ6IE0xMiw0Ni43OWMwLjYyMywwLjM5OSwxLjI5OCwwLjc3NCwyLDEuMTM1ICAgdjUuOTMzYy0wLjcyNC0wLjM5NC0xLjM5Mi0wLjgwOS0yLTEuMjQzVjQ2Ljc5eiBNNiwzMS43OWMwLjAyOCwwLjAxOCwwLjA1NywwLjAzNSwwLjA4NSwwLjA1M2MwLjM2LDAuMjI4LDAuNzM0LDAuNDUsMS4xMiwwLjY2NiAgIGMwLjA5NSwwLjA1MywwLjE5MywwLjEwNSwwLjI4OSwwLjE1N2MwLjE2NywwLjA5MSwwLjMzMywwLjE4MiwwLjUwNSwwLjI3VjM4SDYuNTYyQzYuMzY5LDM3Ljg3Myw2LjE4MiwzNy43NDUsNiwzNy42MTVWMzEuNzl6ICAgIE0xMC40MDMsMzQuMDNjMC4zOTYsMC4xNjEsMC44LDAuMzE2LDEuMjE0LDAuNDY1YzAuMDc1LDAuMDI3LDAuMTQ4LDAuMDU2LDAuMjI0LDAuMDgyYzAuMDUzLDAuMDE5LDAuMTA3LDAuMDM3LDAuMTYsMC4wNTZ2NS45ODEgICBjLTAuNjk2LTAuMjQ2LTEuMzYyLTAuNTA5LTItMC43ODZ2LTUuOTYzYzAuMDIyLDAuMDA5LDAuMDQ0LDAuMDE4LDAuMDY2LDAuMDI3QzEwLjE3NywzMy45NCwxMC4yOSwzMy45ODUsMTAuNDAzLDM0LjAzeiAgICBNMTUuMjQzLDM1LjYwMWMwLjUyLDAuMTMxLDEuMDUyLDAuMjUxLDEuNTksMC4zNjVjMC4wNTYsMC4wMTIsMC4xMSwwLjAyNiwwLjE2NiwwLjAzOHY1Ljk5MWMtMC4wNTctMC4wMTItMC4xMTQtMC4wMjQtMC4xNy0wLjAzNiAgIGMtMC41MzctMC4xMTMtMS4wNjUtMC4yMzUtMS41ODQtMC4zNjZjLTAuNDIzLTAuMTA3LTAuODM3LTAuMjIxLTEuMjQ1LTAuMzM4di01Ljk4OWMwLjM0MiwwLjA5OCwwLjY5LDAuMTkxLDEuMDQxLDAuMjgxICAgQzE1LjEwOSwzNS41NjUsMTUuMTc1LDM1LjU4NCwxNS4yNDMsMzUuNjAxeiBNMjEuMDc5LDM2LjY1N2MwLjU2MSwwLjA2NSwxLjEyNywwLjEyNCwxLjcsMC4xNzEgICBjMC4wMywwLjAwMiwwLjA1OSwwLjAwNiwwLjA4OSwwLjAwOGMwLjA0MywwLjAwMywwLjA4NywwLjAwNiwwLjEzMSwwLjAwOXY1Ljk4NGMtMC42ODgtMC4wNTMtMS4zNzMtMC4xMTItMi4wNDgtMC4xOTEgICBjLTAuMzI2LTAuMDM4LTAuNjQ3LTAuMDg1LTAuOTY5LTAuMTI5Yy0wLjMzMi0wLjA0Ni0wLjY1NS0wLjEwNC0wLjk4My0wLjE1NnYtNS45OGMwLjU0MSwwLjA4NywxLjA5MiwwLjE2MywxLjY0NywwLjIzMiAgIEMyMC43OTEsMzYuNjIzLDIwLjkzNSwzNi42NCwyMS4wNzksMzYuNjU3eiBNMjcsMzdjMC42NzEsMCwxLjMzNi0wLjAyMywyLTAuMDQ5djYuMDA3Yy0wLjAyMSwwLjAwMS0wLjA0MiwwLjAwMi0wLjA2MywwLjAwMiAgIEMyOC4yOTUsNDIuOTg0LDI3LjY1MSw0MywyNyw0M2MtMC42NywwLTEuMzM0LTAuMDM2LTItMC4wNjJWMzYuOTZjMC4wNDQsMC4wMDIsMC4wOSwwLjAwMSwwLjEzNCwwLjAwMyAgIEMyNS43NTEsMzYuOTg1LDI2LjM3MiwzNywyNywzN3ogTTcsMTguMjg5YzAuMDE2LDAuMDIsMC4wMzUsMC4wMzksMC4wNTEsMC4wNThjMC4yMjIsMC4yNzcsMC40NjEsMC41NSwwLjcxNSwwLjgxOCAgIGMwLjA1NywwLjA2LDAuMTE3LDAuMTE4LDAuMTc1LDAuMTc3YzAuMjI2LDAuMjI5LDAuNDYzLDAuNDU0LDAuNzExLDAuNjc2YzAuMDU5LDAuMDUzLDAuMTE1LDAuMTA2LDAuMTc2LDAuMTU4ICAgQzguODg2LDIwLjIyNiw4Ljk0MiwyMC4yNzUsOSwyMC4zMjN2NS41NzFjLTEuMjg1LTEuMzc2LTItMi44ODEtMi00LjQ0NlYxOC4yODl6IE0xMSwyMS43OWMwLjAyOCwwLjAxOCwwLjA1NywwLjAzNSwwLjA4NSwwLjA1MyAgIGMwLjM2LDAuMjI4LDAuNzM0LDAuNDUsMS4xMiwwLjY2NmMwLjA5NSwwLjA1MywwLjE5MywwLjEwNSwwLjI4OSwwLjE1N2MwLjE2NywwLjA5MSwwLjMzMywwLjE4MiwwLjUwNSwwLjI3djUuOTIyICAgYy0wLjcyNC0wLjM5NC0xLjM5Mi0wLjgwOS0yLTEuMjQzVjIxLjc5eiBNMTUsMjMuODY2YzAuMDIyLDAuMDA5LDAuMDQ0LDAuMDE4LDAuMDY2LDAuMDI3YzAuMTExLDAuMDQ3LDAuMjI0LDAuMDkyLDAuMzM3LDAuMTM3ICAgYzAuMzk2LDAuMTYxLDAuOCwwLjMxNiwxLjIxNCwwLjQ2NWMwLjA3NSwwLjAyNywwLjE0OCwwLjA1NiwwLjIyNCwwLjA4MmMwLjA1MywwLjAxOSwwLjEwNywwLjAzNywwLjE2LDAuMDU2djUuOTgxICAgYy0wLjY5Ni0wLjI0Ni0xLjM2Mi0wLjUwOS0yLTAuNzg2VjIzLjg2NnogTTE5LDI1LjI2NmMwLjM0MiwwLjA5OCwwLjY5LDAuMTkxLDEuMDQxLDAuMjgxYzAuMDY4LDAuMDE4LDAuMTM0LDAuMDM3LDAuMjAyLDAuMDU0ICAgYzAuNTIsMC4xMzEsMS4wNTIsMC4yNTEsMS41OSwwLjM2NWMwLjA1NiwwLjAxMiwwLjExLDAuMDI2LDAuMTY2LDAuMDM4djUuOTkxYy0xLjAzNy0wLjIxNi0yLjAzOS0wLjQ2My0zLTAuNzRWMjUuMjY2eiBNMjQsMjYuMzcyICAgYzAuNTQxLDAuMDg3LDEuMDkyLDAuMTYzLDEuNjQ3LDAuMjMyYzAuMTQ0LDAuMDE4LDAuMjg4LDAuMDM1LDAuNDMzLDAuMDUyYzAuNTYxLDAuMDY1LDEuMTI3LDAuMTI0LDEuNywwLjE3MSAgIGMwLjAzLDAuMDAyLDAuMDU5LDAuMDA2LDAuMDg5LDAuMDA4YzAuMDQzLDAuMDAzLDAuMDg3LDAuMDA2LDAuMTMxLDAuMDA5djUuOTk3Yy0xLjM3MS0wLjEwNy0yLjcwNy0wLjI2OC00LTAuNDc4VjI2LjM3MnogTTMyLDI3ICAgYzAuNjI4LDAsMS4yNDktMC4wMTUsMS44NjYtMC4wMzdjMC4wNDQtMC4wMDIsMC4wOS0wLjAwMSwwLjEzNC0wLjAwM3Y1Ljk3N0MzMy4zMzQsMzIuOTYzLDMyLjY3MSwzMywzMiwzMyAgIGMtMC42NzMsMC0xLjMzOS0wLjAxNy0yLTAuMDQyVjI2Ljk2YzAuMDQ0LDAuMDAyLDAuMDksMC4wMDEsMC4xMzQsMC4wMDNDMzAuNzUxLDI2Ljk4NSwzMS4zNzIsMjcsMzIsMjd6IE0zNi4yMiwyNi44MjggICBjMC41NzQtMC4wNDcsMS4xMzktMC4xMDYsMS43LTAuMTcxYzAuMTQ1LTAuMDE3LDAuMjg5LTAuMDM0LDAuNDMzLTAuMDUyYzAuNTU1LTAuMDcsMS4xMDYtMC4xNDUsMS42NDctMC4yMzJ2NS45NzkgICBjLTAuMzc2LDAuMDYtMC43NDQsMC4xMy0xLjEyNiwwLjE4MmMtMC4yLDAuMDI3LTAuMzk4LDAuMDU2LTAuNiwwLjA4MWMtMC43NDksMC4wOTItMS41MSwwLjE1Ny0yLjI3NCwwLjIxNnYtNS45ODQgICBjMC4wNDMtMC4wMDMsMC4wODctMC4wMDYsMC4xMzEtMC4wMDlDMzYuMTYxLDI2LjgzNCwzNi4xOSwyNi44MywzNi4yMiwyNi44Mjh6IE00My43NTcsMjUuNjAxICAgYzAuMDY4LTAuMDE3LDAuMTM0LTAuMDM2LDAuMjAyLTAuMDU0YzAuMzUxLTAuMDksMC42OTktMC4xODQsMS4wNDEtMC4yODF2NS45ODljLTAuMTI4LDAuMDM3LTAuMjU3LDAuMDczLTAuMzg2LDAuMTA4ICAgYy0wLjAwMSwwLTAuMDAzLDAtMC4wMDQsMC4wMDFjLTAuMjI0LDAuMDYyLTAuNDUsMC4xMjQtMC42NzgsMC4xODNjLTAuNTUxLDAuMTQyLTEuMTExLDAuMjczLTEuNjgxLDAuMzk1ICAgYy0wLjA4NCwwLjAxOC0wLjE2NywwLjAzNi0wLjI1MSwwLjA1M3YtNS45OTFjMC4wNTctMC4wMTIsMC4xMS0wLjAyNiwwLjE2Ni0wLjAzOEM0Mi43MDUsMjUuODUyLDQzLjIzNywyNS43MzIsNDMuNzU3LDI1LjYwMXogICAgTTQ3LjM4NCwyNC40OTZjMC40MTMtMC4xNDksMC44MTctMC4zMDUsMS4yMTQtMC40NjVjMC4xMTMtMC4wNDYsMC4yMjYtMC4wOTEsMC4zMzctMC4xMzdjMC4wMjItMC4wMDksMC4wNDQtMC4wMTgsMC4wNjYtMC4wMjcgICB2NS45NjNjLTAuNjM4LDAuMjc3LTEuMzA0LDAuNTQtMiwwLjc4NnYtNS45ODFjMC4wNTQtMC4wMTksMC4xMDctMC4wMzcsMC4xNi0wLjA1NkM0Ny4yMzYsMjQuNTUxLDQ3LjMwOSwyNC41MjMsNDcuMzg0LDI0LjQ5NnogICAgTTUxLjc5NCwyMi41MDhjMC4zODYtMC4yMTYsMC43NjEtMC40MzgsMS4xMi0wLjY2NmMwLjAyOC0wLjAxOCwwLjA1Ny0wLjAzNSwwLjA4NS0wLjA1M3Y1LjgyNWMtMC42MDgsMC40MzQtMS4yNzYsMC44NS0yLDEuMjQzICAgdi01LjkyMmMwLjE3Mi0wLjA4OCwwLjMzOC0wLjE4LDAuNTA1LTAuMjdDNTEuNjAyLDIyLjYxMyw1MS42OTksMjIuNTYxLDUxLjc5NCwyMi41MDh6IE0yNC41MjcsNDQuOTQzICAgYzAuMDM4LDAuMDAyLDAuMDc2LDAuMDAzLDAuMTEzLDAuMDA0YzAuMDQyLDAuMDAyLDAuMDg1LDAuMDAzLDAuMTI4LDAuMDA1YzAuMDg0LDAuMDAzLDAuMTY4LDAuMDA2LDAuMjUyLDAuMDA5ICAgQzI1LjY3NCw0NC45ODQsMjYuMzMyLDQ1LDI3LDQ1YzAuNDY4LDAsMC45MzItMC4wMDYsMS4zOTMtMC4wMTdjMC4xNTEtMC4wMDQsMC4yOTktMC4wMTEsMC40NDktMC4wMTYgICBjMC4wNjItMC4wMDIsMC4xMjQtMC4wMDUsMC4xODctMC4wMDdjMC4wOTEtMC4wMDMsMC4xODMtMC4wMDYsMC4yNzQtMC4wMWMwLjE1NS0wLjAwNiwwLjMxMS0wLjAxLDAuNDY1LTAuMDE4ICAgYzAuMTgtMC4wMDksMC4zNTgtMC4wMjIsMC41MzctMC4wMzNjMC4yNzEtMC4wMTYsMC41NDMtMC4wMywwLjgxMS0wLjA1YzAuMTk3LTAuMDE1LDAuMzkxLTAuMDMzLDAuNTg3LTAuMDUgICBjMC4yNDgtMC4wMjEsMC40OTctMC4wNDEsMC43NDItMC4wNjVjMC4xOTYtMC4wMTksMC4zODgtMC4wNDMsMC41ODItMC4wNjRjMC4yNDEtMC4wMjcsMC40ODQtMC4wNTIsMC43MjMtMC4wODIgICBjMC4xOTYtMC4wMjQsMC4zODgtMC4wNTMsMC41ODItMC4wNzljMC4yMzItMC4wMzIsMC40NjQtMC4wNjIsMC42OTMtMC4wOTdjMC4xOTUtMC4wMjksMC4zODYtMC4wNjIsMC41NzgtMC4wOTQgICBjMC4yMjUtMC4wMzcsMC40NS0wLjA3MiwwLjY3Mi0wLjExMmMwLjE5Mi0wLjAzNCwwLjM4LTAuMDcyLDAuNTctMC4xMDhjMC4yMTYtMC4wNDEsMC40MzQtMC4wODIsMC42NDctMC4xMjYgICBjMC4xOS0wLjAzOSwwLjM3Ny0wLjA4MiwwLjU2NS0wLjEyM2MwLjIwOS0wLjA0NiwwLjQxOC0wLjA5MSwwLjYyNC0wLjEzOWMwLjE4NS0wLjA0NCwwLjM2Ny0wLjA5LDAuNTQ5LTAuMTM2ICAgYzAuMjA0LTAuMDUxLDAuNDA4LTAuMTAxLDAuNjA4LTAuMTU0YzAuMTc4LTAuMDQ3LDAuMzUyLTAuMDk3LDAuNTI4LTAuMTQ2YzAuMTk5LTAuMDU2LDAuMzk5LTAuMTExLDAuNTk1LTAuMTcgICBjMC4xNzQtMC4wNTIsMC4zNDUtMC4xMDYsMC41MTYtMC4xNmMwLjE5MS0wLjA2LDAuMzgxLTAuMTE5LDAuNTY4LTAuMTgxYzAuMTctMC4wNTYsMC4zMzctMC4xMTUsMC41MDQtMC4xNzMgICBjMC4xODMtMC4wNjQsMC4zNjYtMC4xMjcsMC41NDUtMC4xOTNjMC4xNjUtMC4wNiwwLjMyNi0wLjEyMywwLjQ4Ny0wLjE4NWMwLjE3Ni0wLjA2OCwwLjM1Mi0wLjEzNiwwLjUyNC0wLjIwNiAgIGMwLjE1Ny0wLjA2NCwwLjMxMS0wLjEzLDAuNDY2LTAuMTk1YzAuMTctMC4wNzMsMC4zNDEtMC4xNDUsMC41MDctMC4yMmMwLjE1LTAuMDY3LDAuMjk3LTAuMTM2LDAuNDQ0LTAuMjA1ICAgYzAuMTYzLTAuMDc2LDAuMzI1LTAuMTUzLDAuNDg1LTAuMjMyYzAuMTQ0LTAuMDcxLDAuMjg2LTAuMTQ0LDAuNDI3LTAuMjE3YzAuMTU0LTAuMDgsMC4zMDgtMC4xNiwwLjQ1OC0wLjI0MiAgIGMwLjEzOC0wLjA3NSwwLjI3My0wLjE1MSwwLjQwNy0wLjIyN2MwLjE0Ny0wLjA4NCwwLjI5My0wLjE2OCwwLjQzNi0wLjI1M2MwLjEzLTAuMDc4LDAuMjU3LTAuMTU2LDAuMzgzLTAuMjM2ICAgYzAuMTQtMC4wODgsMC4yNzgtMC4xNzYsMC40MTMtMC4yNjVjMC4xMjItMC4wODEsMC4yNDItMC4xNjMsMC4zNjEtMC4yNDVjMC4xMzEtMC4wOTEsMC4yNjEtMC4xODMsMC4zODgtMC4yNzYgICBjMC4xMTQtMC4wODMsMC4yMjYtMC4xNjgsMC4zMzYtMC4yNTNjMC4xMjMtMC4wOTUsMC4yNDQtMC4xOSwwLjM2My0wLjI4N2MwLjEwNi0wLjA4NiwwLjIxLTAuMTczLDAuMzEzLTAuMjYxICAgYzAuMTE0LTAuMDk4LDAuMjI2LTAuMTk3LDAuMzM2LTAuMjk2YzAuMDk4LTAuMDg5LDAuMTk0LTAuMTc4LDAuMjg4LTAuMjY5YzAuMTA1LTAuMTAxLDAuMjA3LTAuMjAzLDAuMzA4LTAuMzA1ICAgYzAuMDktMC4wOTIsMC4xNzgtMC4xODQsMC4yNjQtMC4yNzdjMC4wOTUtMC4xMDQsMC4xODctMC4yMDgsMC4yNzgtMC4zMTNjMC4wODEtMC4wOTUsMC4xNjItMC4xOSwwLjIzOS0wLjI4NiAgIGMwLjA4Ni0wLjEwNiwwLjE2OC0wLjIxNCwwLjI0OC0wLjMyMmMwLjA3Mi0wLjA5NywwLjE0NC0wLjE5NCwwLjIxMi0wLjI5MmMwLjA1NS0wLjA3OSwwLjEwOS0wLjE1OCwwLjE2MS0wLjIzOCAgIEM1My41MSwzNi41NjMsNTQsMzcuMzM5LDU0LDM4YzAsMi43MDQtNy40MTIsNy45NjUtMjEsNy45NjVjLTQuMTE1LDAtNy40ODQtMC40NjktMTAuMTc5LTEuMTIgICBDMjMuMzgzLDQ0Ljg4OCwyMy45NTMsNDQuOTE4LDI0LjUyNyw0NC45NDN6IE00Miw0MC42MTV2LTYuMDIyYzAuMDY0LTAuMDIzLDAuMTI3LTAuMDQ2LDAuMTkxLTAuMDY5ICAgYzAuMzY3LTAuMTMxLDAuNzQyLTAuMjU1LDEuMDk3LTAuMzk2YzAuMjQ0LTAuMDk3LDAuNDcyLTAuMjA2LDAuNzExLTAuMzA4djYuMDA5QzQzLjM2Miw0MC4xMDYsNDIuNjk2LDQwLjM2OSw0Miw0MC42MTV6ICAgIE0zNy43ODMsMzUuODAzYzAuNDE5LTAuMDk2LDAuODQ0LTAuMTg1LDEuMjUzLTAuMjkyYzAuMzI4LTAuMDg2LDAuNjQxLTAuMTg4LDAuOTYzLTAuMjgxdjYuMDI1Yy0wLjk2MSwwLjI3Ny0xLjk2MywwLjUyNC0zLDAuNzQgICB2LTYuMDFDMzcuMjY1LDM1LjkyOSwzNy41MjIsMzUuODYzLDM3Ljc4MywzNS44MDN6IE00NiwzOC44NTh2LTUuNzY0YzAuNjg5LTAuMjA2LDEuMzU0LTAuNDI4LDItMC42NjN2NS4xODQgICBDNDcuMzkyLDM4LjA0OSw0Ni43MjQsMzguNDY0LDQ2LDM4Ljg1OHogTTMxLjk4NiwzNi43NTZjMC4zMy0wLjAzMywwLjY1Mi0wLjA4MiwwLjk3OS0wLjEyMWMwLjQ4Mi0wLjA1NywwLjk2OC0wLjEwOCwxLjQ0My0wLjE4ICAgYzAuMi0wLjAzLDAuMzkzLTAuMDcxLDAuNTkyLTAuMTA0djYuMDE0Yy0wLjA4NCwwLjAxNC0wLjE2OSwwLjAyNy0wLjI1MywwLjA0MWMtMC4yOTksMC4wNDYtMC42LDAuMDkxLTAuOTA0LDAuMTMyICAgYy0wLjIzNSwwLjAzMi0wLjQ2OSwwLjA2Ni0wLjcwNywwLjA5NWMtMC41MTcsMC4wNjItMS4wNCwwLjExNi0xLjU2OCwwLjE2MmMtMC4xNzUsMC4wMTYtMC4zNSwwLjAzMS0wLjUyNiwwLjA0NSAgIGMtMC4wMTQsMC4wMDEtMC4wMjksMC4wMDItMC4wNDMsMC4wMDN2LTYuMDEzQzMxLjMyOCwzNi44MDMsMzEuNjYsMzYuNzg4LDMxLjk4NiwzNi43NTZ6IE01MiwzMS40NDggICBjMCwwLjc2MS0wLjE3MiwxLjUwNy0wLjQ5MywyLjIzMWwtMC4wOTEsMC4xODhjLTAuMjk3LDAuNjE3LTAuNzA5LDEuMjEyLTEuMjA2LDEuNzg2Yy0wLjA2LDAuMDY4LTAuMTE1LDAuMTM3LTAuMTc4LDAuMjA0ICAgYy0wLjAxLDAuMDExLTAuMDIyLDAuMDIxLTAuMDMyLDAuMDMydi00LjI2M2MwLjctMC4zMTEsMS4zNjktMC42MzksMi0wLjk4NVYzMS40NDh6IE0xNiw0OC44NjRjMC42NDMsMC4yNzEsMS4zMSwwLjUyNywyLDAuNzY3ICAgdjUuOTg0Yy0wLjY5Ni0wLjI0Ni0xLjM2Mi0wLjUwOS0yLTAuNzg2VjQ4Ljg2NHogTTIwLDUwLjI1OWMwLjk2OCwwLjI3NywxLjk2NCwwLjUzMSwzLDAuNzQ2djUuOTkxICAgYy0xLjAzNy0wLjIxNi0yLjAzOS0wLjQ2My0zLTAuNzRWNTAuMjU5eiBNMjUsNTEuMzcxYzAuMTU2LDAuMDI1LDAuMzE4LDAuMDQ0LDAuNDc2LDAuMDY4YzAuMjM5LDAuMDM2LDAuNDgsMC4wNywwLjcyMSwwLjEwMiAgIGMwLjM3NywwLjA1MSwwLjc1NSwwLjA5OSwxLjEzOCwwLjE0MmMwLjM4MSwwLjA0MywwLjc2NCwwLjA4MSwxLjE1LDAuMTE2YzAuMTcyLDAuMDE1LDAuMzQyLDAuMDMyLDAuNTE1LDAuMDQ2djUuOTk5ICAgYy0xLjM3MS0wLjEwNy0yLjcwNy0wLjI2OC00LTAuNDc4VjUxLjM3MXogTTU3LDE4LjI4OXYzLjE2YzAsMS41NjUtMC43MTUsMy4wNy0yLDQuNDQ2di01LjU3MSAgIGMwLjA1OC0wLjA0OCwwLjExNC0wLjA5NywwLjE3MS0wLjE0NmMwLjA2LTAuMDUyLDAuMTE3LTAuMTA2LDAuMTc2LTAuMTU4YzAuMjQ5LTAuMjIyLDAuNDg2LTAuNDQ3LDAuNzExLTAuNjc2ICAgYzAuMDU4LTAuMDU5LDAuMTE5LTAuMTE4LDAuMTc1LTAuMTc3YzAuMjU1LTAuMjY4LDAuNDkzLTAuNTQxLDAuNzE1LTAuODE4QzU2Ljk2NSwxOC4zMjcsNTYuOTg0LDE4LjMwOCw1NywxOC4yODl6IE0zMiwyICAgYzE0LjAxOSwwLDI1LDQuODMyLDI1LDExYzAsNi4zOTMtMTEuNjgyLDEyLTI1LDEyUzcsMTkuMzkzLDcsMTNDNyw2LjgzMiwxNy45ODEsMiwzMiwyeiBNNSwxNy43MzR2My43MTQgICBjMCwwLjIzMSwwLjAxMiwwLjQ2LDAuMDM0LDAuNjg4YzAuMDA3LDAuMDc2LDAuMDIyLDAuMTUxLDAuMDMxLDAuMjI3YzAuMDE5LDAuMTUxLDAuMDM4LDAuMzAyLDAuMDY2LDAuNDUyICAgYzAuMDE3LDAuMDg5LDAuMDQxLDAuMTc3LDAuMDYyLDAuMjY2YzAuMDMxLDAuMTM0LDAuMDYxLDAuMjY5LDAuMDk5LDAuNDAyYzAuMDI3LDAuMDk0LDAuMDYxLDAuMTg3LDAuMDkyLDAuMjgxICAgYzAuMDQxLDAuMTI1LDAuMDgyLDAuMjUsMC4xMywwLjM3NGMwLjAzNywwLjA5NywwLjA4LDAuMTkyLDAuMTIxLDAuMjg4YzAuMDUxLDAuMTE5LDAuMTAzLDAuMjM5LDAuMTYsMC4zNTcgICBjMC4wNDcsMC4wOTYsMC4wOTgsMC4xOTEsMC4xNDksMC4yODZjMC4wNjIsMC4xMTYsMC4xMjQsMC4yMzEsMC4xOTEsMC4zNDVjMC4wNTYsMC4wOTUsMC4xMTYsMC4xOSwwLjE3NiwwLjI4NCAgIGMwLjA3MSwwLjExMiwwLjE0NCwwLjIyMywwLjIyLDAuMzM0YzAuMDY1LDAuMDk0LDAuMTM0LDAuMTg4LDAuMjAzLDAuMjgxYzAuMDgxLDAuMTA4LDAuMTYzLDAuMjE2LDAuMjQ5LDAuMzIzICAgYzAuMDc0LDAuMDkyLDAuMTUxLDAuMTg0LDAuMjI5LDAuMjc2YzAuMDksMC4xMDYsMC4xODMsMC4yMSwwLjI3OSwwLjMxNGMwLjA4MiwwLjA5LDAuMTY2LDAuMTc5LDAuMjUyLDAuMjY4ICAgYzAuMSwwLjEwMywwLjIwNCwwLjIwNSwwLjMwOSwwLjMwN2MwLjA5LDAuMDg3LDAuMTgsMC4xNzQsMC4yNzQsMC4yNTljMC4xMSwwLjEsMC4yMjMsMC4yLDAuMzM4LDAuMjk4ICAgYzAuMDk4LDAuMDg0LDAuMTk1LDAuMTY5LDAuMjk2LDAuMjUyYzAuMTE5LDAuMDk4LDAuMjQ0LDAuMTk0LDAuMzY4LDAuMjkxYzAuMTA0LDAuMDgxLDAuMjA3LDAuMTYyLDAuMzE1LDAuMjQxICAgYzAuMTMsMC4wOTYsMC4yNjUsMC4xOSwwLjQsMC4yODRjMC4xMSwwLjA3NywwLjIxNywwLjE1NCwwLjMzLDAuMjI5YzAuMTQyLDAuMDk1LDAuMjksMC4xODgsMC40MzgsMC4yODEgICBjMC4xMTMsMC4wNzEsMC4yMjMsMC4xNDQsMC4zMzksMC4yMTRjMC4xNTksMC4wOTcsMC4zMjUsMC4xOSwwLjQ5LDAuMjg1YzAuMTExLDAuMDYzLDAuMjE4LDAuMTI4LDAuMzMxLDAuMTkgICBjMC4yMDUsMC4xMTMsMC40MTcsMC4yMjIsMC42MjksMC4zMzFjMC4wNzksMC4wNDEsMC4xNTUsMC4wODMsMC4yMzUsMC4xMjNjMC4yOTMsMC4xNDcsMC41OTMsMC4yOSwwLjg5OSwwLjQzICAgYzAuMTA2LDAuMDQ4LDAuMjE3LDAuMDk0LDAuMzI1LDAuMTQyYzAuMjA2LDAuMDkxLDAuNDExLDAuMTgyLDAuNjIzLDAuMjdjMC4xMzEsMC4wNTQsMC4yNjcsMC4xMDYsMC40LDAuMTU5ICAgYzAuMTk0LDAuMDc3LDAuMzg3LDAuMTU1LDAuNTg2LDAuMjNjMC4xNDYsMC4wNTUsMC4yOTUsMC4xMDcsMC40NDMsMC4xNmMwLjE5MywwLjA2OSwwLjM4NSwwLjEzOSwwLjU4MSwwLjIwNiAgIGMwLjE1NSwwLjA1MywwLjMxMywwLjEwMywwLjQ3MSwwLjE1NGMwLjE5NiwwLjA2NCwwLjM5MSwwLjEyNywwLjU5LDAuMTg4YzAuMTYzLDAuMDUsMC4zMjksMC4wOTgsMC40OTQsMC4xNDYgICBjMC4xOTksMC4wNTgsMC4zOTksMC4xMTYsMC42MDEsMC4xNzJjMC4xNywwLjA0NywwLjM0MSwwLjA5MSwwLjUxMywwLjEzNmMwLjIwNCwwLjA1MywwLjQwOCwwLjEwNiwwLjYxNSwwLjE1NiAgIGMwLjE3NSwwLjA0MywwLjM1MiwwLjA4NCwwLjUzLDAuMTI1YzAuMjEsMC4wNDgsMC40MjEsMC4wOTYsMC42MzQsMC4xNDJjMC4xOCwwLjAzOSwwLjM2MSwwLjA3NiwwLjU0MiwwLjExMyAgIGMwLjIxNiwwLjA0NCwwLjQzMiwwLjA4NiwwLjY1MSwwLjEyN2MwLjE4MywwLjAzNCwwLjM2NywwLjA2OCwwLjU1MiwwLjFjMC4yMjMsMC4wMzksMC40NDksMC4wNzYsMC42NzUsMC4xMTIgICBjMC4xODUsMC4wMywwLjM3MSwwLjA1OSwwLjU1OCwwLjA4N2MwLjIzLDAuMDM0LDAuNDYzLDAuMDY2LDAuNjk2LDAuMDk3YzAuMTg3LDAuMDI1LDAuMzc0LDAuMDUsMC41NjMsMC4wNzMgICBjMC4yNCwwLjAyOSwwLjQ4MiwwLjA1NiwwLjcyNCwwLjA4MmMwLjE4NiwwLjAyLDAuMzcxLDAuMDQxLDAuNTU4LDAuMDU5YzAuMjUsMC4wMjQsMC41MDIsMC4wNDUsMC43NTQsMC4wNjYgICBjMC4xODYsMC4wMTUsMC4zNywwLjAzMiwwLjU1NywwLjA0NWMwLjI2MywwLjAxOSwwLjUyOSwwLjAzNCwwLjc5NSwwLjA0OWMwLjE3NywwLjAxLDAuMzUzLDAuMDIyLDAuNTMyLDAuMDMxICAgYzAuMDM4LDAuMDAyLDAuMDc1LDAuMDA1LDAuMTEzLDAuMDA3QzI4LjYxMywzNC45OCwyNy44MDksMzUsMjcsMzVDMTMuNjgyLDM1LDIsMjkuMzkzLDIsMjNDMiwyMC43OTcsMy40MTUsMTkuMDI3LDUsMTcuNzM0eiAgICBNMiwzMS40NDh2LTMuMTZjMC4wMTYsMC4wMiwwLjAzNSwwLjAzOSwwLjA1MSwwLjA1OGMwLjIyMiwwLjI3NywwLjQ2MSwwLjU1LDAuNzE1LDAuODE4YzAuMDU3LDAuMDYsMC4xMTcsMC4xMTgsMC4xNzUsMC4xNzcgICBjMC4yMjYsMC4yMjksMC40NjMsMC40NTQsMC43MTEsMC42NzZjMC4wNTksMC4wNTMsMC4xMTUsMC4xMDYsMC4xNzYsMC4xNThDMy44ODYsMzAuMjI2LDMuOTQyLDMwLjI3NSw0LDMwLjMyM3Y1LjU3MSAgIEMyLjcxNSwzNC41MTgsMiwzMy4wMTMsMiwzMS40NDh6IE04LDQ2LjQ0OHYtMy4xNmMwLjU1OCwwLjcwNywxLjIzNSwxLjM4MiwyLDIuMDI2djUuNTc5QzguNzE1LDQ5LjUxOCw4LDQ4LjAxMyw4LDQ2LjQ0OHogICAgTTMxLDU3Ljk1OFY1MS45NmMwLjA0OCwwLjAwMiwwLjA5NywwLjAwMSwwLjE0NSwwLjAwM0MzMS43NTksNTEuOTg1LDMyLjM3Nyw1MiwzMyw1MmMwLjYyOCwwLDEuMjQ5LTAuMDE1LDEuODY2LTAuMDM3ICAgYzAuMDQ0LTAuMDAyLDAuMDktMC4wMDEsMC4xMzQtMC4wMDN2NS45OThDMzQuMzM5LDU3Ljk4MywzMy42NzMsNTgsMzMsNThTMzEuNjYxLDU3Ljk4MywzMSw1Ny45NTh6IE01OCw0My4yODl2My4xNiAgIGMwLDEuNTY1LTAuNzE1LDMuMDctMiw0LjQ0NnYtNS41NzFjMC4wNTgtMC4wNDgsMC4xMTQtMC4wOTcsMC4xNzEtMC4xNDZjMC4wNi0wLjA1MiwwLjExNy0wLjEwNiwwLjE3Ni0wLjE1OCAgIGMwLjI0OS0wLjIyMiwwLjQ4Ni0wLjQ0NywwLjcxMS0wLjY3NmMwLjA1OC0wLjA1OSwwLjExOS0wLjExOCwwLjE3NS0wLjE3N2MwLjI1NS0wLjI2OCwwLjQ5My0wLjU0MSwwLjcxNS0wLjgxOCAgIEM1Ny45NjUsNDMuMzI3LDU3Ljk4NCw0My4zMDgsNTgsNDMuMjg5eiIgZmlsbD0iI2Q2ZDZkNiIvPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+CjxnPgo8L2c+Cjwvc3ZnPgo=" width="28" />
                                        </div>
                                        <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr class="wb-todo-table__settings wb-todo-table__settings--<?php echo $item['ID']; ?>">
                                    <td colspan="6">
                                        <div class="td-add-new td-add-new--settings">
                                            <?php
                                            $date = date('Y-m-d', $end_time);
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
                                            );
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                $output = ob_get_contents(); ob_end_clean();
                                array_push($html, array('html'=>$output, 'expires'=>$expires));
                            }
                            usort($html, function($a, $b){
                                if($a['expires'] === $b['expires'])
                                    return 0;

                                return $a['expires'] > $b['expires'] ? 1 : -1;
                            });
                            foreach ($html as $tr) {
                                echo $tr['html'];
                            }
                            ?>
                        </tbody>
					</table>
                    <div class="td-add-new-btn">
                        <div class="wb-add-new-btn__btn besocial-button">Add a new task</div>
                    </div>
                    <div class="td-add-new wb-todo-table__new">
                        <h4>Add a new task</h4>
                        <?php
                        echo do_shortcode("[wb_todo_settings note='' name='' date='' assigned='' category='' wedding_budget='']");
                        ?>
                    </div>
				</td>
			</tr>
		</table>
		<?php
	}
	$output = ob_get_contents(); ob_end_clean();
	return $output;
}
add_shortcode('wedding_todo', 'wb_todo_shortcode');
