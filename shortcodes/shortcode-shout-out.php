<?php
function shout_out_shortcode( $atts, $content = null ) {
	$atts = shortcode_atts( array(
		'count' => 8,
	), $atts );

	wb_scripts__shout_out();
	$shout_out = new ShoutOut();
	$events = $shout_out->get_events( $atts['count'] );
	ob_start();
	echo "<div class='wed-shout-out'>";
	foreach ( $events as $event ) {
		?>
		<div class="wed-shout-out__item wed-shout-out__item--regular"><?php echo $event['event_text']; ?></div>
		<?php
	}
	$commercial_lines = get_option( 'wp_commercial_line_text' );
    $i = 0;
	foreach ( $commercial_lines as $commercial_line ) {
        ++$i;
        if ( $commercial_line ) {
	        echo "<div class='wed-shout-out__item--$i wed-shout-out__item--ads wed-shout-out__item--hide'><div class='wed-shout-out__item wed-shout-out__item--commercial-line' data-probability='" . get_option( 'wp_commercial_line_probability' ) . "'>" . stripslashes( $commercial_line ) . "</div></div>";
        }
	}
		echo "<div class='wed-shout-out__grad'></div>";
	echo "</div>";
	return ob_get_clean();
}
add_shortcode( 'shout_out', 'shout_out_shortcode' );