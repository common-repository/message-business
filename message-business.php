<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.message-business.com/
 * @since             1.0.0
 * @package           Message_Business
 *
 * @wordpress-plugin
 * Plugin Name:       Marketing automation, Email and SMS for Woocommerce and Wordpress
 * Plugin URI:        https://wordpress.org/plugins/message-business
 * Description:       Synchronize your Woocommerce clients and Wordpress visitors with Message Business application for Marketing automation, email marketing, sms marketing, webform, abandoned cart etc.  100% Compliant GDPR.
 * Version:           1.1.1
 * Author:            Message Business
 * Author URI:        https://www.message-business.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       message-business
 * Domain Path:       /languages
 * WC requires at least: 2.6.0
 * WC tested up to: 3.2.5
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define( 'MESSAGE_BUSINESS_VERSION', '1.1.1' );
define( 'MESSAGE_BUSINESS_PLUGIN_DIR', dirname( __FILE__ ) . '/' );

require_once ( MESSAGE_BUSINESS_PLUGIN_DIR . 'autoload.php');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/message-business-activator.php
 */
function message_business_activate_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/message-business-activator.php';
	Message_Business_Activator::message_business_activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/message-business-deactivator.php
 */
function message_business_deactivate_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/message-business-deactivator.php';
	Message_Business_Deactivator::message_business_deactivate();
}

register_activation_hook( __FILE__, 'message_business_activate_plugin' );
register_deactivation_hook( __FILE__, 'message_business_deactivate_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/message-business.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function message_business_run_plugin() {

	$plugin = new Message_Business();
	if(isset($plugin)) {
		// Add the settings link to the plugins page
		function plugin_settings_link($links) { 
			$settings_link = '<a href="plugins.php?page=messagebusiness">' . __( 'Settings', 'message-business' ) . '</a>';
			array_unshift($links, $settings_link);
			return $links;
		}
	
		add_filter("plugin_action_links_" . plugin_basename(__FILE__), 'plugin_settings_link');
	}
	$plugin->message_business_run();

}
message_business_run_plugin();
