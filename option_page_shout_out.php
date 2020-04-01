<?php

// Shout Out Page in the Admin Panel
function wb_shout_out_page() {
	wp_check_post_shout_out();
	?>
	<h1>Shout Out Options</h1>
	<form action="" method="post">
		<label for="shout_out_enable">Is Shout Out enable?</label>
		<input type="checkbox" name="enable" value="1" id="shout_out_enable" autocomplete="off" <?php echo ( get_option( 'wp_shout_out_enable' ) ) ? 'checked' : ''; ?>>
		<input type="hidden" name="action" value="shout_out_enable">
		<input type="submit">
		<?php wp_nonce_field( 'shout_out_enable_action', 'shout_out_enable' ); ?>
	</form><br><hr><br>
	<form action="" method="post">
		<label for="commercial_line">Commercial line for each X probability</label><br>
		<textarea name="data" id="commercial_line" autocomplete="off" style="width: 100%;"><?php echo stripslashes( get_option( 'wp_commercial_line_text' ) ); ?></textarea>
		<br><br>
		<label for="probability_for_reaped">Probability for reaped in %</label><br>
		<input type="text" name="probability_for_reaped" value="<?php echo get_option( 'wp_commercial_line_probability' ); ?>" id="probability_for_reaped" autocomplete="off" placeholder="2">
		<input type="hidden" name="action" value="commercial_line">
		<br><br>
		<input type="submit">
		<?php wp_nonce_field( 'commercial_line_action', 'commercial_line' ); ?>
	</form><br><hr><br>
	<h2>Translate events and nicknames</h2>
	<p>You can edit nicknames <a href="/db-wb/?username=wmpntwbrnj&db=wmpntwbrnj&select=wp_wb_event_nicknames">here</a>.</p>
	<p>You can edit events <a href="/db-wb/?username=wmpntwbrnj&db=wmpntwbrnj&select=wp_wb_events">here</a>.</p>
	<p>User: <code><?php echo DB_USER; ?></code></p>
	<p>Password: <code><?php echo DB_PASSWORD; ?></code></p>
	<?php
}

function wp_check_post_shout_out() {
	if ( !empty( $_POST ) AND isset( $_POST['action'] ) AND $_POST['action'] ) {
		if ( $_POST['action'] == 'shout_out_enable' AND wp_verify_nonce( $_POST['shout_out_enable'], 'shout_out_enable_action' ) ) {
			update_option( 'wp_shout_out_enable', (int)$_POST['enable'] );
		}
		if ( $_POST['action'] == 'commercial_line' AND wp_verify_nonce( $_POST['commercial_line'], 'commercial_line_action' ) ) {
			update_option( 'wp_commercial_line_text', $_POST['data'] );
			update_option( 'wp_commercial_line_probability', (int)$_POST['probability_for_reaped'] );
		}
	}
}
