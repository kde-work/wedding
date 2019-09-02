<?php
add_shortcode('tableplan', 'wb_tableplan_shortcode');
function wb_tableplan_shortcode ($atts) {
	if ( is_user_logged_in() ) {
		wb_scripts__tableplan();
		$user_id = wp_get_current_user()->ID;
		$items = unserialize(base64_decode(get_user_meta($user_id, 'tp_save', 1)));
		$params = shortcode_atts( array(
			'without-table' => 'Without table',
		), $atts );
		ob_start();
		?>
		<div class="tp">
			<div class="wb__loading"></div>
			<div class="gl__guest-titel">Table plan</div>
            <div class="tableplan">
                <div class="tableplan__tables">
                    <div class="tp-tables">
                        <div class="tp-table tp-table--square"></div>
                        <div class="tp-table tp-table--rectangle"></div>
                        <div class="tp-table tp-table--round"></div>
                        <div class="tp-table tp-table--oval"></div>
                    </div>
                </div>
                <div class="tableplan__body">
                    <div class="tableplan__guests">
                        <div class="tp-guests">
                            <?php
                            $guests = unserialize(base64_decode(get_user_meta($user_id, 'gl_save', 1)));
                            $i = 0;
                            $guests_tables = array();
                            foreach ($guests as $guest) {
                                $i++;
                                ob_start();
                                ?>
                                <div class="tp-guest tp-guest--member tp-guest--<?php echo $i; ?>">
                                    <div class="tp-guest__status <?php echo ($guest['status'])?'tp-guest__status--checked':''; ?>"></div>
                                    <div class="tp-guest__name"><?php echo $guest['name']; ?></div>
                                    <div class="tp-guest__family"><?php echo $guest['family']; ?></div>
                                    <div class="tp-guest__role"><?php echo $guest['role']; ?></div>
                                </div>
                                <?php
                                $html = ob_get_clean();
                                if (!isset($guest['table']) OR $guest['table']) {
                                    $guest['table'] = '__default__';
                                }
                                if (!isset($guests_tables[$guest['table']])) {
                                    $guests_tables[$guest['table']] = array();
                                }
                                array_push($guests_tables[$guest['table']], array('html' => $html));
                            }
                            usort($guests_tables, function ($a, $b){
                                if ($a == '__default__') return 1;
                                if ($b == '__default__') return -1;
                                if ($a == $b) {
                                    return 0;
                                }
                                return ($a > $b) ? +1 : -1;
                            });
                            foreach ($guests_tables as $table_name => $guests_of_table) {
                                if ($table_name == '__default__') {
                                    $table_name = $params['without-table'];
                                }
                                echo "<div class=\"tp-guests__table\">{$table_name}</div>";
                                foreach ($guests_of_table as $guest_of_table) {
                                    echo $guest_of_table['html'];
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div class="tableplan__field"></div>
                </div>
            </div>
		</div>
		<?php
	}
	return ob_get_clean();
}