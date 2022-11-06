<?php
function wb_payment_shortcode( $atts, $content = null ) {
	if ( !is_feed() ) {
		if ( is_user_logged_in() ) {
			$atts = shortcode_atts( array(
			    ), $atts
			);
			$wedding_payment = new WeddingPayment();
			$check_role = $wedding_payment->check_role();

            // if don't have access
            if ( $check_role['is_expired'] ) {
//	            wb_scripts__payment();
	            ob_start();
	            ?>
                <div class="wb-payment">
                    <div class="wb-payment__title">Få tilgang til planleggingsverktøyene</div>
                    <div class="wb-payment__body">
                        <?php
                        if ( !$wedding_payment->has_trial() ) {
                            ?>
                            <div class="wb-payment__block wb-payment__block--trial">
                                <div class="wb-payment__block-title">Prøveperiode</div>
                                <p class="wb-payment__text">Test verktøyene i 14 dager. Full tilgang.</p>
	                            <?php echo wb_payment_trial_button( 'Get trial' ); ?>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="wb-payment__block wb-payment__block--buy">
                            <div class="wb-payment__block-title">Kjøp</div>
                            <p class="wb-payment__text">Full tilgang i 12 måneder.</p>
                            <?php echo wb_payment_paid_button(); ?>
                        </div>
                    </div>
                </div>
	            <?php
	            return ob_get_clean();
            } else {
                return do_shortcode( $content );
            }
		} else {
			wb_scripts__payment();
			ob_start();
			?>
			<div class="wb-no-auth">
				<div class="wb-no-auth__title">Du må logge inn eller registrere deg for å kunne bruke planleggingsverktøyene.</div>
				<div class="wb-no-auth__body">
					<div class="wb-no-auth__block kleo-show-login"><a title="Login" href="/wp-login.php" data-title="Login">Login</a></div>
					<div class="wb-no-auth__block"><a title="Register" href="http://wedding.ld/registrer/" data-title="Register">Register</a></div>
				</div>
			</div>
			<?php
			return ob_get_clean();
		}
	}
	return '';
}
add_shortcode( 'wb_payment', 'wb_payment_shortcode' );

function wb_payment_paid_button( $title = 'Buy' ) {
	wb_scripts__payment();
	$wedding_payment = new WeddingPayment();
	$html  = "<div class=\"wb-payment__button wp-buy wb-button-regular\">$title</div>";
	$html .= $wedding_payment->create_bambora_online_checkout_payment_html();
	return $html;
}
function wb_payment_trial_button( $title = 'Get trial' ) {
	return "<a href=\"" . get_the_permalink( 443891 ) . "?wb-p=trial\" class=\"wb-payment__button wp-trial wb-button-regular\">$title</a>";
}