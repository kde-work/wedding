<?php
/**
 * Payment system.
 *
 * @author   Wedding
 * @category Admin
 * @package  Wedding/Classes
 * @version  0.0.2
 */
if (!defined('ABSPATH')) {
	exit;
}
class WeddingPayment {

	/**
     * Actual info from `pmpro_memberships_users` about user.
	 * @var array
	 */
    protected $user_info = [];

	/**
     * All info from `pmpro_memberships_users` about user.
	 * @var array
	 */
    protected $user_info_all = [];

	/**
     * Info from `pmpro_membership_levels`.
	 * @var array
	 */
    protected $levels = [];

	/**
	 * Return HTML information about account.
	 *
	 * @return string
	 */
	public function get_payment_information() {
		ob_start();
		$role = $this->check_role();
		?>
        <div class="wb-payment-information">
            <h2 class="wb-settings__title">Account information</h2>
            <div class="wb-payment-information__item wb-payment-information__item--status">
                <div class="wb-payment-information__title">Account Status:</div>
                <div class="wb-payment-information__info"><?php echo $role['name']; echo ( $role['is_expired'] ) ? '. <b class="red">Expired!</b>' : ''; ?></div>
            </div>
			<?php if( $role['enddate'] ) : ?>
                <div class="wb-payment-information__item wb-payment-information__item--date">
                    <div class="wb-payment-information__title">Expiry Date:</div>
                    <div class="wb-payment-information__info"><?php echo $this->get_expire_date(); ?></div>
                </div>
			<?php endif; ?>
			<?php
			if ( $role['is_expired'] != 1 ) : ?>
                <div class="wb-payment-information__item wb-payment-information__item--button">
                    <a class="wb-payment__button wp-buy wb-button-regular" href='velg-medlemskap' target='_blank'>Go to Online Bryllupsplanlegger</a>
					<?php
//                    echo "<a class=\"wb-payment__button wp-buy wb-button-regular\" href='/velg-medlemskap/' target='_blank'>Go to Online Bryllupsplanlegger</a>";
//					wb_payment_trial_button( 'Get trial' );
                    /* if ( $this->user_info['status'] === false ) : ?>
						<?php echo wb_payment_trial_button( 'Get trial' ); ?>
					<?php endif; ?>
					<?php echo wb_payment_paid_button( 'Buy full access for 2 ears' ); */
                    ?>
                </div>
			<?php endif; ?>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Check role for Wedding tools.
	 *
	 * @return array
	 */
	public function check_role() {
		if ( current_user_can( 'administrator' ) )
			return array(
				'name' => 'Administrator',
				'role' => 'administrator',
				'endtime' => 0,
				'enddate' => 0,
				'is_expired' => 0,
			);

		if ( $this->user_info['status'] === false ) {
			return array(
				'name' => 'Without access',
				'role' => 'trial',
				'time' => 0,
				'enddate' => 0,
				'is_expired' => 1,
			);
		}

		$role_name = $this->role_by_id( $this->user_info['membership_id'] );
		$role = array(
			'name' => $this->levels[$role_name]['name'],
			'role' => $role_name,
			'endtime' => strtotime( $this->user_info['enddate'] ),
			'enddate' => $this->user_info['enddate'],
		);

		if ( $this->user_info['status'] == 'active' ) {
			$role = $role + ['is_expired' => 0];
		} else {
			$role = $role + ['is_expired' => 1];
		}

		return $role;
	}

	/**
	 * Setup class.
     *
     * @param  int|bool $id
	 */
	public function __construct( $id = false ) {
		$this->init_user_info( $id );
		$this->init_levels_info();
	}

	/**
	 * Return cost by type.
     *
     * @param  string $type
     * @return string|bool
	 */
	public function get_cost_by_type( $type ) {
		return ( isset( $this->levels[$type] ) ) ? (int)$this->levels[$type]['initial_payment'] . '00' : false;
	}

	/**
	 * Init levels information.
	 */
	protected function init_levels_info() {
		global $wpdb;

		if ( !empty( $this->levels ) ) {
			return;
        }

		$levels = $wpdb->get_results(
		    "SELECT * FROM `{$wpdb->prefix}pmpro_membership_levels`",
			ARRAY_A
		);
		$this->levels['trial'] = $levels[0];
		$this->levels['vip'] = $levels[1];
		$this->levels['standard'] = $levels[2];

		foreach ( $this->levels as $key => $level ) {
			switch ( $level['expiration_period'] ) {
                case 'Day':
			        $m = 60*60*24;
			        break;
                case 'Month':
                default:
			        $m = 60*60*24*31;
            }
			$this->levels[$key]['time'] = $m * $level['expiration_number'];
		}
	}

	/**
	 * Init user information.
	 *
	 * @param  int|bool $id
	 */
	protected function init_user_info( $id = false ) {
		global $wpdb;

		if ( !$id ) {
			$id = wp_get_current_user()->ID;
        }

		$pmpro_user = $wpdb->get_results(
		    $wpdb->prepare(
                "SELECT * FROM `{$wpdb->prefix}pmpro_memberships_users` as users
                 INNER JOIN `{$wpdb->prefix}pmpro_membership_levels` as levels 
                 ON users.`membership_id` = levels.`id`
                 WHERE users.`user_id` = '%d'
                    AND users.`status` != 'admin_changed'
                ",
                $id
		    ),
			ARRAY_A
		);

		if ( !empty( $pmpro_user ) ) {
			$this->user_info_all = $pmpro_user;
			$this->user_info = array_pop( $pmpro_user );
			unset( $this->user_info['id'] );
        } else {
			$this->user_info = [
                'user_id' => $id,
                'status' => false,
            ];
        }
		/*
    [user_id] => 6
    [membership_id] => 2
    [code_id] => 0
    [initial_payment] => 299.00000000
    [billing_amount] => 0.00000000
    [cycle_number] => 0
    [cycle_period] =>
    [billing_limit] => 0
    [trial_amount] => 0.00000000
    [trial_limit] => 0
    [status] => active
    [startdate] => 2019-08-29 11:50:56
    [enddate] => 0000-00-00 00:00:00
    [modified] => 2019-08-29 12:50:56
    [name] => VIP
    [description] => VIP-medlemskap
    [confirmation] =>
    [allow_signups] => 1
    [expiration_number] => 12
    [expiration_period] => Month
		*/
	}

	/**
	 * Hooks.
	 */
	public function init() {
		add_action( 'init', array( __CLASS__, 'set_trial' ), 20 );
		add_action( 'init', array( __CLASS__, 'payment_accept' ), 30 );
	}

	/**
	 * Adds time to the new subscription, if the old one is still active.
     *
     * @return int
	 */
	public function additional_time() {
        if ( $this->user_info['status'] == 'active' ) {
            return strtotime( $this->user_info['enddate'] ) - time();
        }
        return 0;
	}

	/**
	 * Set up role for Wedding tools.
	 *
	 * @param  string $role
	 * @param  int $time
	 * @return bool
	 */
	public function set_role( $role, $time = 0 ) {
		global $wpdb;

		if ( !$time ) $time = time();
		$date = date( 'Y-m-d H:i:s', $time );
		$membership_id = false;
		$additional_time = 0;

		switch ( $role ) {
            case 'trial' : // id:1
                if ( $this->user_info['status'] === false ) {
                    $membership_id = 1;
	                $level = 'trial';
                    break;
                }
				return false;
			case 'vip' : // id:2
                $membership_id = 2;

                // add time if there was VIP before
                if ( $this->user_info['membership_id'] == 2 ) {
	                $additional_time = $this->additional_time();
                }
				$level = 'vip';
                break;
            case 'Standard' : // id:3
			case 'standard' :
                $membership_id = 3;
			    $additional_time = $this->additional_time();
			    $level = 'standard';
                break;
		}

		if ( $membership_id !== false ) {
			$wpdb->query( $wpdb->prepare(
				"UPDATE `{$wpdb->prefix}pmpro_memberships_users` SET
                            `status` = 'admin_changed'
                 WHERE `status` = 'active' AND `user_id` = '%d'
                        ",
				$this->user_info['user_id']
			) );

			$t = $wpdb->query( $wpdb->prepare(
				"INSERT INTO `{$wpdb->prefix}pmpro_memberships_users` SET 
                            `user_id` = '%d',
                            `membership_id` = '%d',
                            `code_id` = '0',
                            `initial_payment` = '0',
                            `billing_amount` = '0',
                            `cycle_number` = '0',
                            `cycle_period` = '0',
                            `billing_limit` = '0',
                            `trial_amount` = '0',
                            `trial_limit` = '0',
                            `status` = 'active',
                            `startdate` = '%s',
                            `enddate` = '%s',
                            `modified` = '%s'
                        ",
				$this->user_info['user_id'],
				$membership_id,
				$date,
				date( 'Y-m-d H:i:s', $time + $additional_time + $this->levels[$level]['time'] ),
				$date
			) );

			return $t;
        }
		return false;
	}

	/**
	 * Return role name by id.
	 *
     * @param  int $id
	 * @return string
	 */
	public function role_by_id( $id ) {
		$a = [1 => 'trial', 2 => 'vip', 3 => 'standard'];
		return isset( $a[$id] ) ? $a[$id] : '';
	}

	/**
	 * Has user trial.
	 *
	 * @return bool
	 */
	public function has_trial() {
		if ( $this->user_info['status'] == 'active' AND $this->user_info['membership_id'] == 1 )
			return true;
		return false;
	}

	/**
	 * Set trial by _GET.
	 */
	public static function set_trial() {
		if ( isset( $_GET['wb-p'] ) AND $_GET['wb-p'] == 'trial' ) {
			$wedding_payment = new WeddingPayment();
			$wedding_payment->set_role( 'trial' );
		}
	}

	/**
	 * Payment accept.
	 */
	public static function payment_accept() {
		if ( isset( $_GET['wb-payment'] ) ) {
            if ( $_GET['wb-payment'] == 'callbacks' ) {
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

			            $wb_payment = $wpdb->get_row(
				            $wpdb->prepare(
					            "SELECT `type`, `user_id` FROM `{$wpdb->prefix}wb_payment`
                                 WHERE `id` = '%d'
                                ",
					            $_GET['order_id']
				            ), ARRAY_A
			            );
			            $type = ( isset( $wb_payment['type'] ) AND $wb_payment['type'] ) ? $wb_payment['type'] : 'standard';
                        if ( !isset( $wb_payment['user_id'] ) OR !$wb_payment['user_id'] ) {
	                        return false;
                        }

			            $wedding_payment = new WeddingPayment( $wb_payment['user_id'] );
			            $wedding_payment->set_role( $type );
                    }
                }
	            die;
            }
		}
	}

	/**
	 * Return expire time.
	 *
	 * @return string
	 */
	public function get_expire_time() {
        return strtotime( $this->user_info['enddate'] );
	}

	/**
	 * Return expire date.
	 *
	 * @return string
	 */
	public function get_expire_date() {
		return date( 'd-m-Y', strtotime( $this->user_info['enddate'] ) );
	}

	/**
	 * Create Bambora Online Checkout payment HTML
     *
	 * @return string
	 */
	public function create_bambora_online_checkout_payment_html() {
		$html  = '<script src="https://static.bambora.com/checkout-sdk-web/latest/checkout-sdk-web.min.js"></script>';
		$html .= '<div class="wb-payment-loader"><div class="wb-payment-loader__spinner"></div><div class="wb-payment-loader__bg"></div></div>';
		return $html;
	}
}