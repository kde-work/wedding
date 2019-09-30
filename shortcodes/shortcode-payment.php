<?php
function wb_payment_shortcode( $atts, $content = null ) {
	if ( !is_feed() ) {
		if ( !is_user_logged_in() ) {
			$atts = shortcode_atts( array(
			    ), $atts
			);
			$check_role = WeddingPayment::check_role();

            // if don't have access
            if ( $check_role['is_expired'] ) {
//	            wb_scripts__payment();
	            ob_start();
	            ?>
                <div class="wb-payment">
                    <div class="wb-payment__title">Getting access to the tools</div>
                    <div class="wb-payment__body">
                        <?php
                        if ( !WeddingPayment::has_trial() ) {
                            ?>
                            <div class="wb-payment__block wb-payment__block--trial">
                                <div class="wb-payment__block-title">Trial</div>
                                <p class="wb-payment__text">Get the trial version for 14 days. Full access.</p>
	                            <?php echo wb_payment_trial_button( 'Get trial' ); ?>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="wb-payment__block wb-payment__block--buy">
                            <div class="wb-payment__block-title">Buy</div>
                            <p class="wb-payment__text">Full access for 2 ears.</p>
                            <?php echo wb_payment_paid_button(); ?>
                        </div>
                    </div>
                </div>
	            <?php
	            return ob_get_clean();
            } else {
                return do_shortcode($content);
            }
		} else {
			wb_scripts__payment();
			ob_start();
			?>
			<div class="wb-no-auth">
				<div class="wb-no-auth__title">You need to login or register to access the tools.</div>
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
	$html  = "<div class=\"wb-payment__button wp-buy wb-button-regular\">$title</div>";
	$html .= WeddingPayment::create_bambora_online_checkout_payment_html();
	return $html;
}
function wb_payment_trial_button( $title = 'Get trial' ) {
	return "<a href=\"" . get_the_permalink( 443891 ) . "?wb-p=trial\" class=\"wb-payment__button wp-trial wb-button-regular\">$title</a>";
}