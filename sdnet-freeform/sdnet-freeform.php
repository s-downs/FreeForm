<?php

/**
 * Plugin Name: FreeForm from SDowns.net
 * Plugin URI: https://sdowns.net/wp-plugins/freeform
 * Description: FreeForm allows the simple (and free) creation of forms on your WordPress site, with the option to send a copy of the completed form to the visitor and checking the validity of their email domain, and reCAPTCHA.
 * Version: 0.0.1
 * Author: SDowns.net
 * Author URI: https://sdowns.net/
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'SDNET_FREEFORM_BASE', plugin_dir_url( __FILE__ ) );

include __DIR__ . '/shortcode/shortcode.php';

add_action('admin_init', 'sdnet_freeform_admin_init');
function sdnet_freeform_admin_init () {
    ob_start();
}

add_action('admin_menu', 'sdnet_freeform_admin_menu');
function sdnet_freeform_admin_menu () {
    add_menu_page(
        'FreeForm Summary',
        'FreeForm',
        'manage_options',
        'sdnet_freeform_dash',
        'sdnet_freeform_page_dash',
        "data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3e%3cpath fill-rule='evenodd' d='M5 2a.5.5 0 0 1 .5-.5c.862 0 1.573.287 2.06.566.174.099.321.198.44.286.119-.088.266-.187.44-.286A4.165 4.165 0 0 1 10.5 1.5a.5.5 0 0 1 0 1c-.638 0-1.177.213-1.564.434a3.49 3.49 0 0 0-.436.294V7.5H9a.5.5 0 0 1 0 1h-.5v4.272c.1.08.248.187.436.294.387.221.926.434 1.564.434a.5.5 0 0 1 0 1 4.165 4.165 0 0 1-2.06-.566A4.561 4.561 0 0 1 8 13.65a4.561 4.561 0 0 1-.44.285 4.165 4.165 0 0 1-2.06.566.5.5 0 0 1 0-1c.638 0 1.177-.213 1.564-.434.188-.107.335-.214.436-.294V8.5H7a.5.5 0 0 1 0-1h.5V3.228a3.49 3.49 0 0 0-.436-.294A3.166 3.166 0 0 0 5.5 2.5.5.5 0 0 1 5 2z'/%3e%3cpath d='M10 5h4a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1h-4v1h4a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-4v1zM6 5V4H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v-1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h4z'/%3e%3c/svg%3e",
        6
    );
    add_submenu_page('sdnet_freeform_dash', 'FreeForm Forms', 'Forms', 'manage_options', 'sdnet_freeform_forms', 'sdnet_freeform_page_forms');
    add_submenu_page('sdnet_freeform_dash', 'FreeForm Responses', 'Responses', 'manage_options', 'sdnet_freeform_responses', 'sdnet_freeform_page_responses');
    add_submenu_page('sdnet_freeform_dash', 'FreeForm Settings', 'Settings', 'manage_options', 'sdnet_freeform_settings', 'sdnet_freeform_page_settings');
}

function sdnet_freeform_page_dash () {
    include __DIR__ . '/admin-pages/dash.php';
}

function sdnet_freeform_page_forms () {
    include __DIR__ . '/admin-pages/forms.php';
}

function sdnet_freeform_page_responses () {
    include __DIR__ . '/admin-pages/responses.php';
}

function sdnet_freeform_page_settings () {
    include __DIR__ . '/admin-pages/options.php';
}






add_action( 'init', 'sdnet_freeform_init' );
register_activation_hook( __FILE__, 'sdnet_freeform_activationhooks' );
register_deactivation_hook( __FILE__, 'sdnet_freeform_deactivationhooks' );
function sdnet_freeform_init () {
    sdnet_freeform_registerposttypes();
    add_shortcode( 'freeform', 'sdnet_freeform_shortcode' );
}
function sdnet_freeform_activationhooks () {
    sdnet_freeform_registerposttypes();
    add_shortcode( 'freeform', 'sdnet_freeform_shortcode' );
}
function sdnet_freeform_deactivationhooks () {
    unregister_post_type( 'sdnet_freeform_form' );
    unregister_post_type( 'sdnet_freeform_res' );
}


// Util
function sdnet_freeform_registerposttypes () {
    register_post_type( 'sdnet_freeform_form', [
        'labels' => [
            'name' => __( 'Forms', 'textdomain'),
            'singular_name' => __( 'Form', 'textdomain' ),
        ],
        'public' => false,
        'has_archive' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
//        'show_ui' => true,
        'supports' => [ 'title', 'slug' ],
        'hierarchical' => false,
    ] );
    register_post_type( 'sdnet_freeform_res', [
        'labels' => [
            'name' => __( 'Responses', 'textdomain'),
            'singular_name' => __( 'Response', 'textdomain' ),
        ],
        'public' => false,
        'has_archive' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
//        'show_ui' => true,
        'supports' => [ 'title' ],
        'hierarchical' => false,
    ] );
}
