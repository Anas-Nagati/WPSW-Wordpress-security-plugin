<?php
/**
 * @package  WPSW
 */
/*
Plugin Name: WPSW
Description: This is a simple security plugin, It outputs all active plugins in an API and send an email to plugin author to report any security risks.
Version: 1.0.0
Author: Anas Nagati
Author URI: anasnagati@gmail.com
License: GPLv2 or later
Text Domain: WPSW
*/


defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You silly human!' );
function WPSW_plugin_activate() {

    add_option( 'Activated_Plugin', 'WPSW' );

    /* Send site info to plugin admin */
    $admin_email = get_option('admin_email');
    $site_url = get_site_url().'/wp-json/WPSW/v2/plugins ';
    $current_theme = wp_get_theme();
    $to = 'anasnagati@gmail.com';
    $subject = 'New website is registered';
    $message = 'A new website has just joined us, the URL is: '.$site_url . 'The used theme is: ' . $current_theme .', and the admin email is: '.$admin_email  ;
    wp_mail($to, $subject, $message);

}
register_activation_hook( __FILE__, 'WPSW_plugin_activate' );

function WPSW_get_plugin_info() {

    // Get all plugins
    include_once( 'wp-admin/includes/plugin.php' );
    $all_plugins = get_plugins();

    // Get active plugins
    $active_plugins = get_option('active_plugins');

    // Assemble array of name, version, and whether plugin is active (boolean)
    foreach ( $all_plugins as $key => $value ) {
        $is_active = ( in_array( $key, $active_plugins ) ) ? true : false;
        $plugins[ $key ] = array(
            'name'    => $value['Name'],
            'version' => $value['Version'],
            'active'  => $is_active,
        );
    }

    return $plugins;
}

// register rest route
add_action( 'rest_api_init', function (){
    register_rest_route('WPSW/v2', 'plugins', [
        'methods' => 'GET',
        'callback' => 'WPSW_get_plugin_info',
    ]);
} );
