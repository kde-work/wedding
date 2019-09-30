<?php
/**
 * Payment system.
 *
 * @author   Wedding
 * @category Admin
 * @package  Wedding/Classes
 * @version  0.0.1
 */
if (!defined('ABSPATH')) {
	exit;
}
class WeddingPayment {
	public static $trial_period = WB_TRIAL_PERIOD;
	public static $paid_period = WB_VIP_PERIOD;
	public static $trial = 'trial';
	public static $paid = 'paid';

	/**
	 * Hooks.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'init_components' ), 10 );
		add_action( 'init', array( __CLASS__, 'set_trial' ), 20 );
		add_action( 'init', array( __CLASS__, 'payment_accept' ), 30 );
	}

	/**
	 * initial components.
	 */
	public static function init_components() {
		add_role( self::$trial, 'Trial period',
			array(
				'read'         => true,
				'edit_posts'   => true,
				'upload_files' => true,
			)
		);
		add_role( self::$paid, 'Paid period',
			array(
				'read'         => true,
				'edit_posts'   => true,
				'upload_files' => true,
			)
		);
	}

	/**
	 * Check role for Wedding tools.
	 *
	 * @param  string $role
	 * @param  int $time
	 * @return bool
	 */
	public static function set_role( $role, $time = 0 ) {
		$current_user = wp_get_current_user();
		if ( !$time ) $time = time();
		switch ( $role ) {
			case self::$trial :
				$meta_trial = get_user_meta( $current_user->ID, 'wb-trial' );
				if ( $meta_trial ) {
					if ( $meta_trial > ( $time - self::$trial_period ) ) {
						return true;
					} else {
						return false;
					}
				}
				if ( !current_user_can( 'administrator' ) ) {
					$current_user->set_role( self::$trial );
                }
				add_user_meta( $current_user->ID, 'wb-trial', $time, 1 );
				return true;
			case self::$paid :
//				$meta_paid = get_user_meta( $current_user->ID, 'wb-paid' );
//				if ( $meta_paid ) {
//					if ( $meta_paid > ( $time - self::$paid_period ) ) {
//						return true;
//					} else {
//						return false;
//					}
//				}
				if ( !current_user_can( 'administrator' ) ) {
					$current_user->set_role( self::$paid );
				}
				add_user_meta( $current_user->ID, 'wb-paid', $time, 1 );
				return true;
		}
		return false;
	}

	/**
	 * Check role for Wedding tools.
	 *
	 * @return array
	 */
	public static function check_role() {
		if ( current_user_can( 'administrator' ) )
			return array(
				'name' => 'Administrator',
				'role' => 'administrator',
				'time' => 0,
				'date' => 0,
				'is_expired' => 0,
			);

		$current_user = wp_get_current_user();
		$user_roles = $current_user->roles;

//		self::set_role( self::$trial );

		// Is Trial period
		if ( in_array( self::$trial, ( array )$user_roles ) ) {
			$meta_trial = get_user_meta( $current_user->ID, 'wb-trial', 1 );
			$role = array(
				'name' => 'Trial',
				'role' => self::$trial,
			);

			if ( $meta_trial ) {
                $role += array(
	                'time' => $meta_trial,
	                'date' => date( 'Y-m-d', $meta_trial ),
                );
				if ( $meta_trial > ( time() - self::$trial_period ) ) {
					return $role + ['is_expired' => 0];
				} else {
					return $role + ['is_expired' => 1];
				}
			}

			self::set_role( self::$trial );
			return $role + array(
				'time' => time(),
				'date' => date( 'Y-m-d', time() ),
				'is_expired' => 0
			    );
		}

		// Is Paid period
		if ( in_array( self::$paid, ( array )$user_roles ) ) {
			$meta_paid = get_user_meta( $current_user->ID, 'wb-paid', 1 );
			$role = array(
				'name' => 'VIP',
				'role' => self::$paid,
			);

			if ( $meta_paid ) {
				$role += array(
					'time' => $meta_paid,
					'date' => date( 'Y-m-d', $meta_paid ),
				);

				if ( $meta_paid > ( time() - self::$paid_period ) ) {
					return $role + ['is_expired' => 0];
				} else {
					return $role + ['is_expired' => 1];
				}
			}

			self::set_role( self::$paid );
			return $role + array(
					'time' => time(),
					'date' => date( 'Y-m-d', time() ),
					'is_expired' => 0
				);
		}

		return array(
			'name' => 'Without access',
			'role' => $current_user->roles[0],
			'time' => 0,
			'date' => 0,
			'is_expired' => 1,
		);
	}

	/**
	 * Has user trial.
	 *
	 * @return bool
	 */
	public static function has_trial() {
		if ( get_user_meta( wp_get_current_user()->ID, 'wb-trial', 1 ) )
			return true;
		return false;
	}

	/**
	 * Set trial by _GET.
	 */
	public static function set_trial() {
		if ( isset( $_GET['wb-p'] ) AND $_GET['wb-p'] == self::$trial ) {
			self::set_role( self::$trial );
		}
	}

	/**
	 * Set trial by _GET.
	 */
	public static function payment_accept() {
		if ( isset( $_GET['wb-payment'] ) ) {
            if ( $_GET['wb-payment'] == 'callbacks' ) {
//	            TB::m($_GET);
	            if ( isset( $_GET['feeid'] ) AND $_GET['feeid'] ) {
		            global $wpdb;

		            $b = new WeddingPaymentBambora();
		            $check_transaction = $b->check_transaction( $_GET );
		            if ( $check_transaction === true ) {
//			            TB::m( $b->check_transaction( $_GET ) );
			            $wpdb->query( $wpdb->prepare(
				            "UPDATE `{$wpdb->prefix}wb_payment` SET 
                                `date_complete` = '%s',
                                `cardno` = '%s',
                                `request` = '%s',
                                `status` = 'complete'
                             WHERE `id` = '%d'
                            ",
				            time(),
				            $_GET['cardno'],
				            base64_encode( serialize( $_GET ) ),
				            $_GET['order_id']
			            ) );

			            self::set_role( self::$paid );
                    }
                }
	            die;
            }
		}
	}

	/**
	 * Return expire time.
	 *
     * @param  array $role
	 * @return string
	 */
	public static function get_expire_time( $role = [] ) {
        if ( empty( ( array )$role ) ) {
	        $role = self::check_role();
        }

        switch ( $role['role'] ) {
            case self::$paid:
	            return self::check_role()['time'] + self::$paid_period;
            case self::$trial:
	            return self::check_role()['time'] + self::$trial_period;
        }
        return '';
	}

	/**
	 * Return expire date.
	 *
	 * @param  array $role
	 * @return string
	 */
	public static function get_expire_date( $role = [] ) {
		if ( empty( ( array )$role ) ) {
			$role = self::check_role();
		}

		return date( 'Y-m-d', self::get_expire_time( $role ) );
	}

	/**
	 * Return HTML information about account.
	 *
	 * @return string
	 */
	public static function get_payment_information() {
		$role = self::check_role();
		ob_start();
//		$r = new WeddingPaymentBambora();
//        print_r( $r->request(10)['session'] );
		?>
		<div class="wb-payment-information">
			<h2 class="wb-settings__title">Account information</h2>
			<div class="wb-payment-information__item wb-payment-information__item--status">
				<div class="wb-payment-information__title">Account Status:</div>
				<div class="wb-payment-information__info"><?php echo $role['name']; echo ( $role['is_expired'] ) ? '. <b class="red">Expired!</b>' : ''; ?></div>
			</div>
			<?php if( $role['date'] ) : ?>
				<div class="wb-payment-information__item wb-payment-information__item--date">
					<div class="wb-payment-information__title">Expiry Date:</div>
					<div class="wb-payment-information__info"><?php echo self::get_expire_date( $role ); ?></div>
				</div>
			<?php endif; ?>
            <?php
            if ( $role['role'] != self::$paid OR $role['is_expired'] == 1 ) : ?>
                <div class="wb-payment-information__item wb-payment-information__item--button">
                    <?php if ( !self::has_trial() ) : ?>
	                    <?php echo wb_payment_trial_button( 'Get trial' ); ?>
                    <?php endif; ?>
	                <?php echo wb_payment_paid_button( 'Buy full access for 2 ears' ); ?>
                </div>
            <?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Create Bambora Online Checkout payment HTML
     *
	 * @return string
	 */
	public static function create_bambora_online_checkout_payment_html() {
		$html  = '<script src="https://static.bambora.com/checkout-sdk-web/latest/checkout-sdk-web.min.js"></script>';
		$html .= '<div class="wb-payment-loader"><div class="wb-payment-loader__spinner"></div><div class="wb-payment-loader__bg"></div></div>';
		return $html;
	}
}
WeddingPayment::init();