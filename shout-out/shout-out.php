<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * ShoutOut class
 *
 * @class       ShoutOut
 * @version     0.0.1
 * @category    Admin
 * @author      Dmitry
 */
class ShoutOut {

	/**
	 * Setup class.
	 */
	public function __construct() {
	}

	/**
	 * Init actions.
	 */
	public function init() {
		add_action( 'wp_login', array( $this, 'wp_login' ), 10, 2 );
		add_action( 'user_register', array( $this, 'user_register' ), 100, 1 );
		add_action( 'bbp_new_topic', array( $this, 'bbp_new_topic' ), 10, 1 );
		add_action( 'bbp_new_reply', array( $this, 'bbp_new_reply' ), 10, 1 );
	}

	/**
	 * wp_login action.
	 *
	 * @param  string $user_login
	 * @param  object $user
	 */
	public function wp_login( $user_login, $user ) {
		self::push_event( 1, $user->ID );
	}

	/**
	 * wp_login action.
	 *
	 * @param  string $user_id
	 */
	public function user_register( $user_id ) {
		self::push_event( 2, $user_id );
	}

	/**
	 * wp_login action.
	 *
	 * @param  int $topic_id
	 */
	public function bbp_new_topic( $topic_id ) {
		self::push_event( 20, wp_get_current_user()->ID, $topic_id );
	}

	/**
	 * wp_login action.
	 *
	 * @param  int $reply_id
	 */
	public function bbp_new_reply( $reply_id ) {
		self::push_event( 21, wp_get_current_user()->ID, $reply_id );
	}

	/**
	 * Push event in the DB.
	 *
	 * @param  int $event_id
	 * @param  int|bool $user_id
	 * @param  mixed $data
	 * @return int|bool
	 */
	public static function push_event( $event_id, $user_id = false, $data = '' ) {
		global $wpdb;

		// Enable/Disable Shout Out
		if ( !get_option( 'wp_shout_out_enable' ) ) return false;

		if ( !$user_id ) $user_id = wp_get_current_user()->ID;
		$event_id = intval( $event_id );

		return $wpdb->query( $wpdb->prepare(
			"INSERT INTO `{$wpdb->prefix}wb_event_list` SET
                `event_id` = '%d',
                `user_id` = '%d',
                `data` = '%s'
                ",
			$event_id,
			$user_id,
			$data
		) );
	}

	/**
	 * Get user nickname.
	 *
	 * @param  int|bool $user_id
	 * @param  bool $with_out_placeholder
	 * @return array
	 */
	public static function get_nickname( $user_id = false, $with_out_placeholder = false ) {
		global $wpdb;

		if ( !$user_id ) $user_id = wp_get_current_user()->ID;
		$nickname = get_user_meta( $user_id, 'wb_nickname', 1 );
		$nickname = ( $nickname ) ? $nickname : get_userdata( $user_id )->first_name;

		if ( $with_out_placeholder ) {
			return [
				'nickname' => $nickname
			];
		}

		$names = $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}wb_event_nicknames`", ARRAY_A );
		$placeholder = $names[rand( 0, count( $names ) - 1 )]['value'];

		return [
			'nickname' => $nickname,
			'placeholder' => $placeholder
		];
	}

	/**
	 * Get Topic by ID.
	 *
	 * @param  int $topic_id
	 * @return string
	 */
	public static function get_topic( $topic_id ) {
		if ( $topic_id ) {
			return "<a href='". bbp_get_topic_permalink( $topic_id ) ."'>". get_post_field( 'post_title', $topic_id, 'raw' ) ."</a>";
		}
		return '';
	}

	/**
	 * Get Reply by ID.
	 *
	 * @param  int $reply_id
	 * @return string
	 */
	public static function get_reply( $reply_id ) {
		if ( $reply_id ) {
			return "<a href='". esc_url( bbp_get_reply_url( $reply_id ) ) ."'>". bbp_get_reply_topic_title( $reply_id ) ."</a>";
		}
		return '';
	}

	/**
	 * Get event value.
	 *
	 * @param  int $count
	 * @return array
	 */
	public function get_events( $count = 8 ) {
		global $wpdb;

		$data = $wpdb->get_results( $wpdb->prepare(
			"SELECT *, 
				(SELECT `meta_value` FROM `{$wpdb->usermeta}` as usermeta WHERE usermeta.`meta_key` = 'wb_nickname' AND usermeta.`user_id` = list.`user_id`) as nickname,
				(SELECT `value` FROM `{$wpdb->prefix}wb_events` as events WHERE events.`id` = list.`event_id`) as event_text
			 FROM `{$wpdb->prefix}wb_event_list` as list
             ORDER BY list.`id` DESC LIMIT %d",
			intval( $count )
		), ARRAY_A );
		for ( $i = 0; $i < count( $data ); ++$i ) {
			if ( !$data[$i]['nickname'] AND $data[$i]['user_id'] ) {
				$data[$i]['nickname'] = self::get_nickname( $data[$i]['user_id'], true )['nickname'];
				$data[$i]['nickname'] = "<a href='/members/{$data[$i]['nickname']}/profile'>{$data[$i]['nickname']}</a>";
			}
			$data[$i]['event_text'] = str_replace( '%username%', $data[$i]['nickname'], $data[$i]['event_text'] );

			if ( $data[$i]['data'] ) {
				if ( strpos( $data[$i]['event_text'], '%topic%' ) !== false ) {
					$data[$i]['event_text'] = str_replace( '%topic%', self::get_topic( $data[$i]['data'] ), $data[$i]['event_text'] );
				}
				if ( strpos( $data[$i]['event_text'], '%reply%' ) !== false ) {
					$data[$i]['event_text'] = str_replace( '%reply%', self::get_reply( $data[$i]['data'] ), $data[$i]['event_text'] );
				}
			}
		}
		return $data;
	}
}