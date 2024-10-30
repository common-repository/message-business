<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.message-business.com/
 * @since      1.0.0
 *
 * @package    Message_Business
 * @subpackage Message_Business/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Message_Business
 * @subpackage Message_Business/includes
 * @author     Message Business
 */
class Message_Business_Deactivator {

	/**
	 * Fired during plugin deactivation 
	 * 
	 * @since    1.0.0
	 */
	public static function message_business_deactivate() {
		// Delete all the Message Business WP Cron jobs
		wp_clear_scheduled_hook('message_business_import_contacts');

		delete_option('MESSAGE_BUSINESS_START_DATE_LAST_IMPORT_CUSTOMERS');
		delete_option('MESSAGE_BUSINESS_END_DATE_LAST_IMPORT_CUSTOMERS');
		delete_option('MESSAGE_BUSINESS_LAST_PAGE_CUSTOMERS');
		delete_option('MESSAGE_BUSINESS_IMPORT_CUSTOMERS_FREQUENCY');
	}

}
