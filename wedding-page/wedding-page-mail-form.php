<?php
function wedding_page_form_shortcode( $atts ) {
	$params = shortcode_atts( array(
		'title' => 'Send us a message',
		'button' => 'send',
		'success' => 'success message',
		'error' => 'error message'
	), $atts );

	wb_scripts__webp_form();

	ob_start();
	?>
	<form class="wb-form">
		<?php if ($params['title']) : ?><h3><?php echo $params['title']; ?></h3><?php endif; ?>
		<div class="wb-form__loading"></div>
		<div class="wb-form__message wb-form__message--success"><div><?php echo $params['success']; ?></div></div>
		<div class="wb-form__message wb-form__message--error"><div><?php echo $params['error']; ?></div></div>
		<input type="text" name="name" placeholder="Name" class="wb-input" required title="Your name">
		<input type="text" name="phone" placeholder="Phone" class="wb-input" required title="Your phone">
		<input type="email" name="email" placeholder="Email" class="wb-input" required title="Email">
		<textarea name="message" cols="30" rows="10" placeholder="message" title="Message"></textarea>
		<input type="submit" class="wb-form__button">
		<input type="hidden" name="action" value="webp_form">
		<?php wp_nonce_field( 'webp_form_action', 'webp_form' ); ?>
	</form>
	<?php
	return ob_get_clean();
}
add_shortcode( 'wedding_page_form', 'wedding_page_form_shortcode' );