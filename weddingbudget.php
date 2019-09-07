<?php/*Plugin Name: Wedding BudgetDescription: Use shortcode [weddingbudget]Version: 1.0Author: DmitryAuthor URI: http://kutalo.com/*/if (!defined('ABSPATH')) {	exit;}require 'WeddingBudgetClass.php';require 'WeddingToDoClass.php';require 'shortcodes.php';require 'ajax.php';require 'option.php';require 'cron-email.php';require 'mail.php';global $wb_file;$wb_file = __FILE__;// action function for above hookfunction wb_updater_add_pages() {    add_options_page('Wedding Budget', 'Wedding Budget', 'manage_options', 'statistic', 'wb_option');}add_action('admin_menu', 'wb_updater_add_pages');function wb_scripts__weddingbudget () {	$v = '1.004';    wp_register_script('weddingbudget', plugins_url('/html/js/weddingbudget.js', __FILE__), array(), $v);    wp_enqueue_script('weddingbudget');    wp_register_style('weddingbudget', plugins_url('/html/css/weddingbudget.css', __FILE__), array(), $v);    wp_enqueue_style('weddingbudget');}function wb_scripts__settings () {	$v = '1.004';	wp_register_script('wb-settings', plugins_url('/html/js/settings.js', __FILE__), array(), $v);	wp_enqueue_script('wb-settings');    wp_register_style('weddingbudget', plugins_url('/html/css/weddingbudget.css', __FILE__), array(), $v);    wp_enqueue_style('weddingbudget');}function wb_scripts__todo () {	$v = '1.004';	wp_register_script('wb-todo', plugins_url('/html/js/todo.js', __FILE__), array(), $v);	wp_enqueue_script('wb-todo');    wp_register_style('weddingbudget', plugins_url('/html/css/weddingbudget.css', __FILE__), array(), $v);    wp_enqueue_style('weddingbudget');}function wb_scripts__helptext () {	$v = '1.004';	wp_register_script('wb-helptext', plugins_url('/html/js/helptext.js', __FILE__), array(), $v);	wp_enqueue_script('wb-helptext');    wp_register_style('weddingbudget', plugins_url('/html/css/weddingbudget.css', __FILE__), array(), $v);    wp_enqueue_style('weddingbudget');}function wb_scripts__guestlist () {	$v = '1.004';	wp_register_script('wb-guestlist', plugins_url('/html/js/guestlist.js', __FILE__), array(), $v);	wp_enqueue_script('wb-guestlist');    wp_register_style('weddingbudget', plugins_url('/html/css/weddingbudget.css', __FILE__), array(), $v);    wp_enqueue_style('weddingbudget');}function wb_scripts__wishlist () {	$v = '1.004';	wp_register_script('wb-wishlist', plugins_url('/html/js/wishlist.js', __FILE__), array(), $v);	wp_enqueue_script('wb-wishlist');    wp_register_style('weddingbudget', plugins_url('/html/css/weddingbudget.css', __FILE__), array(), $v);    wp_enqueue_style('weddingbudget');}function wb_scripts__tableplan () {	$v = '1.004';	wp_enqueue_script('wbtp-ga', plugins_url('/html/js/tableplan/ga.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-watch', plugins_url('/html/js/tableplan/watch.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-kinetic', plugins_url('/html/js/tableplan/kinetic.min.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-utils', plugins_url('/html/js/tableplan/utils.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-lib', plugins_url('/html/js/tableplan/lib.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-IndexFunctions', plugins_url('/html/js/tableplan/IndexFunctions.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-params', plugins_url('/html/js/tableplan/params.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-DragManager', plugins_url('/html/js/tableplan/DragManager.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-Guest', plugins_url('/html/js/tableplan/Guest.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-TablePlan', plugins_url('/html/js/tableplan/TablePlan.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-Table', plugins_url('/html/js/tableplan/Table.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-Seat', plugins_url('/html/js/tableplan/Seat.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-GuestAvatar', plugins_url('/html/js/tableplan/GuestAvatar.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-PlanOutput', plugins_url('/html/js/tableplan/PlanOutput.js', __FILE__), array(), $v);	wp_enqueue_script('wbtp-RectObject', plugins_url('/html/js/tableplan/RectObject.js', __FILE__), array(), $v);	wp_enqueue_script('wb-tableplan', plugins_url('/html/js/tableplan.js', __FILE__), array(), $v);//    wp_enqueue_style('wbtps-styles', plugins_url('/html/css/tableplan/styles.css', __FILE__), array(), $v);//    wp_enqueue_style('wbtps-green-blue', plugins_url('/html/css/tableplan/green-blue.css', __FILE__), array(), $v);//    wp_enqueue_style('wbtps-general', plugins_url('/html/css/tableplan/general.css', __FILE__), array(), $v);    wp_enqueue_style('wbtps-planner', plugins_url('/html/css/tableplan/planner.css', __FILE__), array(), $v);    wp_enqueue_style('wbtps-jquery', plugins_url('/html/css/tableplan/jquery.css', __FILE__), array(), $v);    wp_enqueue_style('wbtps-jquery-ui', plugins_url('/html/css/tableplan/jquery-ui.css', __FILE__), array(), $v);    wp_enqueue_style('weddingbudget', plugins_url('/html/css/weddingbudget.css', __FILE__), array(), $v);}function wb_ajaxurl_scripts () {    wp_localize_script('jquery', 'wedding_budget',        array(            'url' => admin_url('admin-ajax.php')        ));	wp_localize_script('jquery', 'sources',		array(			'anti_clock_rotation' => plugins_url('/html/img/planner/anti_clock_rotation.png', __FILE__),			'clock_rotation' => plugins_url('/html/img/planner/clock_rotation.png', __FILE__)		));}add_action('wp_enqueue_scripts', 'wb_ajaxurl_scripts', 40);function datepicker_js(){	wp_enqueue_script('jquery-ui-datepicker');	wp_register_script('time-picker', plugins_url('/html/js/time-picker.js', __FILE__));	wp_enqueue_script('time-picker');	wp_enqueue_style('jqueryui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css', false, null );}