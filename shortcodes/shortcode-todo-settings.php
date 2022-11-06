<?php
function wb_todo_settings_shortcode ($atts) {
	$id = $note = $name = $date = $assigned = $category = $wedding_budget = $done = 0;
	extract( shortcode_atts( array(
			'id' => 'new',
			'note' => '',
			'name' => '',
			'date' => '',
			'assigned' => '',
			'category' => '',
			'done' => 0,
			'wedding_budget' => '',
		), $atts )
	);
	if ( is_user_logged_in() ) {
		ob_start();
		?>
		<form class="td-add-new__form" data-id="<?php echo $id; ?>">
			<div class="td-add-new__item td-add-new__item--3 td-add-new__item--note">
                <?php
                if (function_exists('get_field') AND get_field('additional_html_into_task_header', $id)) {
	                echo get_field('additional_html_into_task_header', $id);
                }
                ?>
				<label for="td-add-new__note-<?php echo $id; ?>">Some notes</label>
				<textarea name="note" class="td-add-new__note <?php if (!$note) echo 'td-add-new__note--empty'; ?>" id="td-add-new__note-<?php echo $id; ?>" autocomplete="off" cols="30" rows="10"><?php
                    if ($note) {
                        echo $note;
                    } else {
                        if (function_exists('get_field') AND get_field('custom_note', $id)) {
                            echo get_field('custom_note', $id);
                        } else {
	                        ?>Here some notes...
Need to call Jane / 999 99 999 99
Must be purchased from "name of vendor"
Should check more from blah-blah<?php
                        }
                    } ?></textarea>
			</div>
			<table border="0">
				<tr>
					<td width="50%">
						<div class="td-add-new__item td-add-new__item--1 td-add-new__item--input">
							<label for="td-add-new__name-<?php echo $id; ?>">Name</label>
							<input type="text" name="name" class="td-add-new__name" id="td-add-new__name-<?php echo $id; ?>" autocomplete="off" value="<?php echo $name; ?>" required>
						</div>
					</td>
					<td width="50%">
						<div class="td-add-new__item td-add-new__item--2 td-add-new__item--input">
							<label for="td-add-new__date-<?php echo $id; ?>">Due date</label>
							<input type="text" name="date" class="td-add-new__date datepicker" id="td-add-new__date-<?php echo $id; ?>" autocomplete="off" value="<?php echo $date; ?>" required>
						</div>
					</td>
				</tr>
			</table>
			<table>
				<tr>
					<td width="33.33%">
						<div class="td-add-new__item td-add-new__item--4 td-add-new__item--checkbox-list">
							<div class="td-add-new__title">Assigned to</div>
							<div class="td-checkbox-list">
								<div class="td-checkbox-list__padding">
									<?php
									$assigned_list = WeddingToDoClass::get_assigned_list();
									$members = explode(',', $assigned);
									foreach ($assigned_list as $assigned_item) {
										$assigned_id = str_replace(' ', '-', $assigned_item);
										?>
										<div class="td-checkbox-list__item">
											<input type="radio"
											       name="assigned"
											       class="td-add-new__assigned td-checkbox-list__input"
											       id="td-add-new__assigned--<?php echo $assigned_id."-$id"; ?>"
											       value="<?php echo $assigned_item; ?>"
												<?php
												foreach ($members as $member)
													if ($member == $assigned_id) echo 'checked="checked" ' ?>
												   autocomplete="off">
											<label for="td-add-new__assigned--<?php echo $assigned_id."-$id"; ?>" class="td-checkbox-list__label ns"><?php echo $assigned_item; ?></label>
										</div>
										<?php
									}
									?>
									<div class="td-checkbox-list__item td-checkbox-list__item--new">
										<input type="radio"
										       name="assigned"
										       class="td-add-new__assigned td-add-new__assigned--new td-checkbox-list__input"
										       id="td-add-new__assigned--new-item-<?php echo "-$id"; ?>"
										       value="New"
											   autocomplete="off">
										<label for="td-add-new__assigned--new-item-<?php echo "-$id"; ?>" class="td-checkbox-list__label ns">New</label>
										<input type="text" class="td-add-new__new-assigned" placeholder="Ny ansvarlig">
									</div>
								</div>
							</div>
						</div>
					</td>
					<td width="33.33%">
						<div class="td-add-new__item td-add-new__item--5 td-add-new__item--checkbox-list">
							<div class="td-add-new__title">Add/Remove from categories</div>
							<div class="td-checkbox-list">
								<div class="td-checkbox-list__padding">
									<?php
									$list_of_groups = WeddingToDoClass::get_list_of_groups('ToDoGroups');
									$categories_init = explode(',', $category);
									foreach ($list_of_groups as $group) {
										?>
										<div class="td-checkbox-list__item">
											<input type="checkbox"
											       name="category--<?php echo $group['term_id']."-$id"; ?>"
											       class="td-add-new__group td-checkbox-list__input"
											       id="td-add-new__group--<?php echo $group['term_id']."-$id"; ?>"
												<?php
												foreach ($categories_init as $category_init)
													if ($group['term_id'] == $category_init) echo 'checked="checked" ' ?>
												   value="<?php echo $group['term_id']; ?>"
												   autocomplete="off">
											<label for="td-add-new__group--<?php echo $group['term_id']."-$id"; ?>" class="td-checkbox-list__label ns"><?php echo $group['name']; ?></label>
										</div>
										<?php
									}
									?>
								</div>
							</div>
						</div>
					</td>
					<td width="33.33%">
						<div class="td-add-new__item td-add-new__item--6 td-add-new__item--checkbox-list">
							<div class="td-add-new__title">Link to an item from the wedding budget</div>
							<div class="td-checkbox-list">
								<div class="td-checkbox-list__padding">
									<div class="td-checkbox-list__item">
										<input type="radio"
										       name="budget"
										       class="td-add-new__budget td-checkbox-list__input"
										       id="td-add-new__budget--empty<?php echo "-$id"; ?>"
										       value="empty" autocomplete="off">
										<label
											for="td-add-new__budget--empty<?php echo "-$id"; ?>"
											class="td-checkbox-list__label ns">Do not bind</label>
									</div>
									<?php
									$budgets = get_user_meta(wp_get_current_user()->ID, 'wb_save', 1);
									if (count($budgets) AND is_array($budgets)) {
										foreach ( $budgets as $budget ) {
											if ( isset( $budget['option'] ) AND $budget['option'] == 'remove-line' ) {
												continue;
											}
											if ( $budget['id'] == 'clear-line' ) {
												$budget_id = str_replace( ' ', '-', $budget['name'] );
											} else {
												$budget_id = $budget['id'];
											}
											$wedding_budgets_init = explode(',', $wedding_budget);
											?>
											<div class="td-checkbox-list__item">
												<input type="radio"
												       name="budget"
												       class="td-add-new__budget td-checkbox-list__input"
												       id="td-add-new__budget--<?php echo $budget_id."-$id"; ?>"
													<?php
													foreach ($wedding_budgets_init as $wedding_budget_init)
														if ($budget_id == $wedding_budget_init) echo 'checked="checked" ' ?>
													   value="<?php echo $budget_id; ?>"
													   autocomplete="off">
												<label
													for="td-add-new__budget--<?php echo $budget_id."-$id"; ?>"
													class="td-checkbox-list__label ns"><?php echo $budget['name']; ?></label>
											</div>
											<?php
										}
									} else {
										$budgets = WeddingToDoClass::get_list_of_posts_by_taxonomy('WeddingBudgetGroups');
										foreach ( $budgets as $budget ) {
											$wedding_budgets_init = explode(',', $wedding_budget);
											?>
											<div class="td-checkbox-list__item">
												<input type="radio"
												       name="budget"
												       class="td-add-new__budget td-checkbox-list__input"
												       id="td-add-new__budget--<?php echo $budget->ID."-$id"; ?>"
													<?php
													foreach ($wedding_budgets_init as $wedding_budget_init)
														if ($budget->ID == $wedding_budget_init) echo 'checked="checked" ' ?>
													   value="<?php echo $budget->ID; ?>" autocomplete="off">
												<label
													for="td-add-new__budget--<?php echo $budget->ID."-$id"; ?>"
													class="td-checkbox-list__label ns"><?php echo $budget->post_title; ?></label>
											</div>
											<?php
										}
									}
									?>
								</div>
							</div>
						</div>
					</td>
				</tr>
			</table>
			<div class="td-add-new__item td-add-new__item--7 td-add-new__item--btns">
				<input class="td-add-new__save" value="Save" type="submit">
				<?php
				if ($id AND $id != 'new') {
					?>
					<div class="td-add-new__delete ll ll--red" data-id="<?php echo $id; ?>" data-budget="<?php echo $wedding_budget; ?>">Delete</div>
					<?php
				}
				?>
			</div>
			<input type="hidden" name="done" value="<?php echo $done*1; ?>" autocomplete="off" class="td-add-new__done--<?php echo $id; ?>">
			<input type="hidden" name="id" value="<?php echo $id; ?>">
			<input type="hidden" name="action" value="wb_todo_new">
			<?php wp_nonce_field('wb_todo','wb_todo_field'); ?>
		</form>
		<?php
	}
	$output = ob_get_contents(); ob_end_clean();
	return $output;
}
add_shortcode('wb_todo_settings', 'wb_todo_settings_shortcode');