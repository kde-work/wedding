<?php
/*
Plugin Name: Wedding Budget
Description: Use shortcode [weddingbudget]
Version: 1.0
Author: Dmitry
Author URI: http://kutalo.com/
*/

if (!defined('ABSPATH')) {
	exit;
}

require 'WeddingBudgetClass.php';
require 'WeddingToDoClass.php';
require 'shortcodes.php';
require 'ajax.php';
require 'option.php';
require 'cron-email.php';

// action function for above hook
function wb_updater_add_pages() {
    add_options_page('Wedding Budget', 'Wedding Budget', 'manage_options', 'statistic', 'wb_option');
}
add_action('admin_menu', 'wb_updater_add_pages');

function wb_scripts__weddingbudget () {
    wp_register_script('weddingbudget', plugins_url('/html/js/weddingbudget.js', __FILE__));
    wp_enqueue_script('weddingbudget');

    wp_register_style('weddingbudget', plugins_url('/html/css/weddingbudget.css', __FILE__));
    wp_enqueue_style('weddingbudget');
}

function wb_scripts__settings () {
	wp_register_script('wb-settings', plugins_url('/html/js/settings.js', __FILE__));
	wp_enqueue_script('wb-settings');

    wp_register_style('weddingbudget', plugins_url('/html/css/weddingbudget.css', __FILE__));
    wp_enqueue_style('weddingbudget');
}

function wb_scripts__todo () {
	wp_register_script('wb-todo', plugins_url('/html/js/todo.js', __FILE__));
	wp_enqueue_script('wb-todo');

    wp_register_style('weddingbudget', plugins_url('/html/css/weddingbudget.css', __FILE__));
    wp_enqueue_style('weddingbudget');
}

function wb_scripts__helptext () {
	wp_register_script('wb-helptext', plugins_url('/html/js/helptext.js', __FILE__));
	wp_enqueue_script('wb-helptext');

    wp_register_style('weddingbudget', plugins_url('/html/css/weddingbudget.css', __FILE__));
    wp_enqueue_style('weddingbudget');
}

function wb_scripts__guestlist () {
	wp_register_script('wb-guestlist', plugins_url('/html/js/guestlist.js', __FILE__));
	wp_enqueue_script('wb-guestlist');

    wp_register_style('weddingbudget', plugins_url('/html/css/weddingbudget.css', __FILE__));
    wp_enqueue_style('weddingbudget');
}

function wb_ajaxurl_scripts () {
    wp_localize_script('jquery', 'wedding_budget',
        array(
            'url' => admin_url('admin-ajax.php')
        ));
}
add_action('wp_enqueue_scripts', 'wb_ajaxurl_scripts', 40);

function datepicker_js(){
	wp_enqueue_script('jquery-ui-datepicker');

	wp_register_script('time-picker', plugins_url('/html/js/time-picker.js', __FILE__));
	wp_enqueue_script('time-picker');

	wp_enqueue_style('jqueryui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css', false, null );
}