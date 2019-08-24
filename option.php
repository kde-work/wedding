<?php
// Settings and Statistic Page in the Admin Panel
function wb_option() {
	global $wpdb;

	$stats = $wpdb->get_results(
		"SELECT `meta_value` FROM `$wpdb->usermeta`
			 WHERE 
			    `meta_key`='wb_save'
	        ",
		ARRAY_A
	);

	$wb_total__count = $wpdb->get_var(
		"SELECT COUNT(`meta_value`) FROM `$wpdb->usermeta`
			 WHERE 
			    `meta_key`='wb_total_input'
			    AND `meta_value` > 5000
	        "
	);
	$wb_total__avg = $wpdb->get_var(
		"SELECT AVG(`meta_value`) FROM `$wpdb->usermeta`
			 WHERE 
			    `meta_key`='wb_total_input'
			    AND `meta_value` > 5000
	        "
	);

	$new_line = array();
	$data = array();
	$deleted = array();

	foreach ($stats as $stat) {
		$user_datas = unserialize($stat['meta_value']);

		foreach ($user_datas as $user_data) {
			if ($user_data['id'] == 'clear-line') {
				if (!is_array($new_line[$user_data['name']])) {
					$new_line[$user_data['name']] = array();
					$new_line[$user_data['name']]['real']['count'] = 0;
					$new_line[$user_data['name']]['real']['sum'] = 0;
					$new_line[$user_data['name']]['estimate']['count'] = 0;
					$new_line[$user_data['name']]['estimate']['sum'] = 0;
				}
				if ($user_data['real']) {
					$new_line[$user_data['name']]['real']['count'] += 1;
					$new_line[$user_data['name']]['real']['sum'] += $user_data['real'];
				}
				if ($user_data['estimate']) {
					$new_line[$user_data['name']]['estimate']['count'] += 1;
					$new_line[$user_data['name']]['estimate']['sum'] += $user_data['estimate'];
				}
			} elseif ($user_data['option'] == 'remove-line') {
				$deleted[$user_data['id']] += 1;
			} else {
				if (!is_array($data[$user_data['id']])) {
					$data[$user_data['id']] = array();
					$data[$user_data['id']]['real']['count'] = 0;
					$data[$user_data['id']]['real']['sum'] = 0;
					$data[$user_data['id']]['estimate']['count'] = 0;
					$data[$user_data['id']]['estimate']['sum'] = 0;
				}
				if ($user_data['real']) {
					$data[$user_data['id']]['real']['count'] += 1;
					$data[$user_data['id']]['real']['sum'] += $user_data['real'];
				}
				if ($user_data['estimate']) {
					$data[$user_data['id']]['estimate']['count'] += 1;
					$data[$user_data['id']]['estimate']['sum'] += $user_data['estimate'];
				}
			}
		}
	}

	?>
	<h1>Statistics of filling in the fields of the plugin Wedding Budget</h1>

    <br>
    <p>Total average Wedding Budget: <b><?php echo round($wb_total__avg); ?></b> (<b><?php echo $wb_total__count; ?></b> created budgets)</p>

	<table width="100%" class="wb__option">
		<tr>
			<th colspan="7" class="wb__title"><h3>Waste for preset positions</h3></th>
		</tr>
		<tr>
			<th><b>Title</b></th>
			<th>Count of <b>ESTIMATE</b></th>
			<th>Total amount of <b>ESTIMATE</b></th>
			<th>Average of <b>ESTIMATE</b></th>
			<th>Count of <b>REAL</b></th>
			<th>Total amount of <b>REAL</b></th>
			<th>Average of <b>REAL</b></th>
		</tr>
		<?php
		foreach ($data as $key => $item) {
		?>
			<tr>
				<td><b><?php echo get_the_title($key); ?></b></td>
				<td><?php echo $item['estimate']['count']; ?></td>
				<td><?php echo $item['estimate']['sum']; ?></td>
				<td><b><?php echo $item['estimate']['sum'] / $item['estimate']['count']; ?></b></td>
				<td><?php echo $item['real']['count']; ?></td>
				<td><?php echo $item['real']['sum']; ?></td>
				<td><b><?php echo $item['real']['sum'] / $item['estimate']['count']; ?></b></td>
			</tr>
		<?php
		}
		?>
	</table>

	<table width="100%" class="wb__option">
		<tr>
			<th colspan="7" class="wb__title"><h3>Waste for custom (front-end) positions</h3></th>
		</tr>
		<tr>
			<th><b>Title</b></th>
			<th>Count of <b>ESTIMATE</b></th>
			<th>Total amount of <b>ESTIMATE</b></th>
			<th>Average of <b>ESTIMATE</b></th>
			<th>Count of <b>REAL</b></th>
			<th>Total amount of <b>REAL</b></th>
			<th>Average of <b>REAL</b></th>
		</tr>
		<?php
		foreach ($new_line as $key => $item) {
		?>
			<tr>
				<td><b><?php echo $key; ?></b></td>
				<td><?php echo $item['estimate']['count']; ?></td>
				<td><?php echo $item['estimate']['sum']; ?></td>
				<td><b><?php echo $item['estimate']['sum'] / $item['estimate']['count']; ?></b></td>
				<td><?php echo $item['real']['count']; ?></td>
				<td><?php echo $item['real']['sum']; ?></td>
				<td><b><?php echo $item['real']['sum'] / $item['estimate']['count']; ?></b></td>
			</tr>
		<?php
		}
		?>
	</table>

	<table width="100%" class="wb__option">
		<tr>
			<th colspan="2" class="wb__title"><h3>Number of times the line was deleted</h3></th>
		</tr>
		<tr>
			<th><b>Title</b></th>
			<th>Count of removes</th>
		</tr>
		<?php
		foreach ($deleted as $key => $item) {
			?>
			<tr>
				<td><b><?php echo get_the_title($key); ?></b></td>
				<td><?php echo $item; ?></td>
			</tr>
			<?php
		}
		?>
	</table>
	<style>
		.wb__option {
            border: 1px solid #ccc;
			margin: 3em 0;
		}
		.wb__option th {
			font-weight: normal !important;
			border-bottom: 1px solid #ccc;
		}
        .wb__option h3 {
        }
        .wb__title {
            background-color: #ccc;
            padding: 0;
        }
	</style>
<?php
}