<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://www.message-business.com/
 * @since      1.0.0
 *
 * @package    Message_Business
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete all the Message Business options
delete_option('MESSAGE_BUSINESS_ACCOUNTID');
delete_option('MESSAGE_BUSINESS_APIKEY');
delete_option('MESSAGE_BUSINESS_FORMBUILDEROPTIONS');
delete_option('MESSAGE_BUSINESS_WIDGETFORMHTML');
delete_option('MESSAGE_BUSINESS_INPUTSUBMITBUTTONTEXT');
// WooCommerce options
delete_option('MESSAGE_BUSINESS_SHOP_URL');
delete_option('MESSAGE_BUSINESS_CONSUMER_KEY');
delete_option('MESSAGE_BUSINESS_CONSUMER_SECRET');
delete_option('MESSAGE_BUSINESS_START_DATE_LAST_IMPORT_CUSTOMERS');
delete_option('MESSAGE_BUSINESS_END_DATE_LAST_IMPORT_CUSTOMERS');
delete_option('MESSAGE_BUSINESS_LAST_PAGE_CUSTOMERS');
delete_option('MESSAGE_BUSINESS_IMPORT_CUSTOMERS_FREQUENCY');

// Delete all the Message Business WP Cron jobs
wp_clear_scheduled_hook('message_business_import_contacts');