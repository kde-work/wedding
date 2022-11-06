<?php/*Plugin Name: Wedding BudgetDescription: Use shortcode [weddingbudget]Version: 1.1Author: DmitryAuthor URI: http://kutalo.com/*/if (!defined('ABSPATH')) {	exit;}require 'WeddingBudgetClass.php';require 'WeddingToDoClass.php';require 'shortcodes.php';require 'ajax.php';require 'option_page_budget.php';require 'option_page_shout_out.php';require 'cron.php';require 'mail.php';require 'payment/payment.php';require 'payment/payment-bambora.php';require 'payment/payment-endpoints.php';require 'wedding-page/wedding-page.php';require 'wedding-page/wedding-page-mail-form.php';require 'wedding-page/editor.php';require 'wedding-page/editor-components.php';require 'wedding-page/editor-default-values.php';require 'shout-out/shout-out.php';require 'pdf-report/dompdf/autoload.inc.php';require 'pdf-report/report.php';global $wb_file;$wb_file = __FILE__;// Plugin activation.function wb_install () {	global $wpdb;	$wpdb->query(		"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}wb_payment` (		  `id` int(11) NOT NULL AUTO_INCREMENT,		  `user_id` int(11) NOT NULL,		  `session` varchar(123) NOT NULL DEFAULT '',		  `type` varchar(123) NOT NULL DEFAULT '',		  `amount` varchar(63) NOT NULL DEFAULT '',		  `currency` varchar(63) NOT NULL DEFAULT '',		  `date_start` varchar(123) NOT NULL DEFAULT '',		  `date_start_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,		  `date_complete` varchar(123) NOT NULL DEFAULT '',		  `cardno` varchar(255) NOT NULL DEFAULT '',		  `request` text NOT NULL,		  `status` varchar(123) NOT NULL DEFAULT '',		  PRIMARY KEY (`id`)		) ENGINE=InnoDB DEFAULT CHARSET=utf8;"	);	$wpdb->query(		"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}wb_events` (		  `id` int(11) NOT NULL AUTO_INCREMENT,		  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,		  `value_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,		  `limit_seconds` int(11) NOT NULL DEFAULT '0' COMMENT 'event limit in time',		  `is_available` int(11) NOT NULL DEFAULT '1',		  PRIMARY KEY (`id`)		) ENGINE=InnoDB DEFAULT CHARSET=utf8;"	);	$wpdb->query(		"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}wb_event_list` (		  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,		  `event_id` int NOT NULL,		  `user_id` int NOT NULL,		  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP		) ENGINE=InnoDB DEFAULT CHARSET=utf8;"	);	$wpdb->query(		"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}wb_event_nicknames` (		  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,		  `value` varchar(255) NOT NULL		) ENGINE=InnoDB DEFAULT CHARSET=utf8;"	);}register_activation_hook(__FILE__, 'wb_install');function wb_deactivation(){	wp_clear_scheduled_hook( 'wb_cron' );}register_deactivation_hook( __FILE__, 'wb_deactivation' );// Init.function wb_init () {	$wedding_payment = new WeddingPayment();	$wedding_payment->init();	$shout_out = new ShoutOut();	$shout_out->init();	if (isset($_GET['bill-test'])) {		phpinfo();//		WeddingPayment::generate_pdf_file([//			'amount' => 479,//			'role' => 'vip',//			'order_id' => 36,//			'card_no' => '415421XXXXXX0001',    'time' => '17-04-2020 12:49:23',    'user_id' => '6',    'user_name' => 'Dmitry',    'user_mail' => 'omigos99@yandex.ru',    'end_date' => '14-05-2024 12:39:28'//		]);		die;	}}add_action( 'init', 'wb_init' );// action function for above hookfunction wb_updater_add_pages() {    add_options_page('Wedding Budget', 'Wedding Budget', 'manage_options', 'statistic', 'wb_option');    add_options_page('Shout Out', 'Shout Out', 'manage_options', 'shout_out', 'wb_shout_out_page');}add_action('admin_menu', 'wb_updater_add_pages');function wb_scripts__weddingbudget() {	$v = '1.015';    wp_register_script('fontawesome', 'https://use.fontawesome.com/98f42fcd2c.js', array(), $v);    wp_enqueue_script('fontawesome');    wp_register_script('weddingbudget', plugins_url('/html/js/weddingbudget.js', __FILE__), array('jquery'), $v);    wp_enqueue_script('weddingbudget');    wp_register_style('weddingbudget', plugins_url('/html/css/weddingbudget.css', __FILE__), array(), $v);    wp_enqueue_style('weddingbudget');}function wb_scripts__settings() {	$v = '1.015';	wp_register_script('wb-settings', plugins_url('/html/js/settings.js', __FILE__), array(), $v);	wp_enqueue_script('wb-settings');    wp_register_style('weddingbudget', plugins_url('/html/css/weddingbudget.css', __FILE__), array(), $v);    wp_enqueue_style('weddingbudget');}function wb_scripts__shout_out() {	$v = '1.015';	wp_register_script('shout_out-js', plugins_url('/html/js/shout_out.js', __FILE__), array(), $v);	wp_enqueue_script('shout_out-js');    wp_register_style('shout_out', plugins_url('/html/css/shout_out.css', __FILE__), array(), $v);    wp_enqueue_style('shout_out');}function wb_scripts__todo() {	$v = '1.015';	wp_register_script('wb-todo', plugins_url('/html/js/todo.js', __FILE__), array(), $v);	wp_enqueue_script('wb-todo');    wp_register_style('weddingbudget', plugins_url('/html/css/weddingbudget.css', __FILE__), array(), $v);    wp_enqueue_style('weddingbudget');}function wb_scripts__helptext() {	$v = '1.015';	wp_register_script('wb-helptext', plugins_url('/html/js/helptext.js', __FILE__), array(), $v);	wp_enqueue_script('wb-helptext');    wp_register_style('weddingbudget', plugins_url('/html/css/weddingbudget.css', __FILE__), array(), $v);    wp_enqueue_style('weddingbudget');}function wb_scripts__guestlist() {	$v = '1.015';	wp_register_script('wb-guestlist', plugins_url('/html/js/guestlist.js', __FILE__), array(), $v);	wp_enqueue_script('wb-guestlist');    wp_register_style('weddingbudget', plugins_url('/html/css/weddingbudget.css', __FILE__), array(), $v);    wp_enqueue_style('weddingbudget');}function wb_scripts__wishlist() {	$v = '1.015';	wp_register_script('wb-wishlist', plugins_url('/html/js/wishlist.js', __FILE__), array(), $v);	wp_enqueue_script('wb-wishlist');    wp_register_style('weddingbudget', plugins_url('/html/css/weddingbudget.css', __FILE__), array(), $v);    wp_enqueue_style('weddingbudget');}function wb_scripts__payment() {	$v = '1.015';	wp_register_script('wb-payment', plugins_url('/html/js/payment.js', __FILE__), array(), $v);	wp_enqueue_script('wb-payment');	wp_register_style('weddingbudget', plugins_url('/html/css/weddingbudget.css', __FILE__), array(), $v);	wp_enqueue_style('weddingbudget');}function wb_scripts__webp() {	$v = '1.015';	wp_enqueue_media();	wp_register_script('wb-wedding-page', plugins_url('/html/js/wedding-page.js', __FILE__), array(), $v);	wp_enqueue_script('wb-wedding-page');	wp_register_script('wb-builder', plugins_url('/html/js/builder.js', __FILE__), array(), $v);	wp_enqueue_script('wb-builder');	wp_register_style('weddingbudget', plugins_url('/html/css/weddingbudget.css', __FILE__), array(), $v);	wp_enqueue_style('weddingbudget');	wp_register_style('wb-css-builder', plugins_url('/html/css/builder.css', __FILE__), array(), $v);	wp_enqueue_style('wb-css-builder');}function wb_scripts__webp_form() {	$v = '1.015';	wp_register_script('wb-wedding-page-form', plugins_url('/html/js/wedding-page-form.js', __FILE__), array(), $v);	wp_enqueue_script('wb-wedding-page-form');}function wb_scripts__tableplan () {	$v = '1.015';	wp_enqueue_script('wbtp-ga', plugins_url('/html/js/tableplan/ga.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-watch', plugins_url('/html/js/tableplan/watch.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-kinetic', plugins_url('/html/js/tableplan/kinetic.min.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-utils', plugins_url('/html/js/tableplan/utils.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-lib', plugins_url('/html/js/tableplan/lib.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-IndexFunctions', plugins_url('/html/js/tableplan/IndexFunctions.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-params', plugins_url('/html/js/tableplan/params.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-DragManager', plugins_url('/html/js/tableplan/DragManager.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-Guest', plugins_url('/html/js/tableplan/Guest.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-TablePlan', plugins_url('/html/js/tableplan/TablePlan.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-Table', plugins_url('/html/js/tableplan/Table.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-Seat', plugins_url('/html/js/tableplan/Seat.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-GuestAvatar', plugins_url('/html/js/tableplan/GuestAvatar.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-PlanOutput', plugins_url('/html/js/tableplan/PlanOutput.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-RectObject', plugins_url('/html/js/tableplan/RectObject.js', __FILE__), array(), $v);	wp_enqueue_script('wb-tableplan', plugins_url('/html/js/tableplan.js', __FILE__), array(), $v);//    wp_enqueue_style('wbtps-styles', plugins_url('/html/css/tableplan/styles.css', __FILE__), array(), $v);//    wp_enqueue_style('wbtps-green-blue', plugins_url('/html/css/tableplan/green-blue.css', __FILE__), array(), $v);//    wp_enqueue_style('wbtps-general', plugins_url('/html/css/tableplan/general.css', __FILE__), array(), $v);    wp_enqueue_style('wbtps-planner', plugins_url('/html/css/tableplan/planner.css', __FILE__), array(), $v);    wp_enqueue_style('wbtps-jquery', plugins_url('/html/css/tableplan/jquery.css', __FILE__), array(), $v);    wp_enqueue_style('wbtps-jquery-ui', plugins_url('/html/css/tableplan/jquery-ui.css', __FILE__), array(), $v);    wp_enqueue_style('weddingbudget', plugins_url('/html/css/weddingbudget.css', __FILE__), array(), $v);}function wb_ajaxurl_scripts () {    wp_localize_script('jquery', 'wedding_budget',        array(            'url' => admin_url('admin-ajax.php'),            'nonce' => wp_create_nonce('wedding-nonce')        ));	wp_localize_script('jquery', 'sources',		array(			'anti_clock_rotation' => plugins_url('/html/img/planner/anti_clock_rotation.png', __FILE__),			'clock_rotation' => plugins_url('/html/img/planner/clock_rotation.png', __FILE__)		));}add_action('wp_enqueue_scripts', 'wb_ajaxurl_scripts', 40);function datepicker_js(){	wp_enqueue_script('jquery-ui-datepicker');	wp_register_script('time-picker', plugins_url('/html/js/time-picker.js', __FILE__));	wp_enqueue_script('time-picker');	wp_enqueue_style('jqueryui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css', false, null );}