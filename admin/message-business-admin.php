<?php


require_once ( __DIR__ . '/../autoload.php' );

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.message-business.com/
 * @since      1.0.0
 *
 * @package    Message_Business
 * @subpackage Message_Business/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Message_Business
 * @subpackage Message_Business/admin
 * @author     Message Business
 */
class Message_Business_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Check if the Message Business API has been initialised or not.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      boolean    $isApiInitialised    True if the MB API has been initialised.
	 */
	private $isApiInitialised;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		if ( get_option('MESSAGE_BUSINESS_ACCOUNTID') !== null && get_option( 'MESSAGE_BUSINESS_APIKEY' ) !== null ) {

			$moduleApi = new Swagger\Client\Api\Message_Business_ModulesApi();
			$accountId = get_option( 'MESSAGE_BUSINESS_ACCOUNTID' );
			$apiKey = get_option( 'MESSAGE_BUSINESS_APIKEY' );
			$this->isApiInitialised = $this->message_business_initApi($moduleApi->getApiClient()->getConfig(), $accountId, $apiKey);
		} else {
			$this->isApiInitialised = false;
		}
	}

	/**
	 * Initialise Message Business API
	 *
	 * @param [Message_Business_ModulesApi] $config
	 * @param [string] $accountId
	 * @param [string] $apiKey
	 * @return boolean
	 */
	private function message_business_initApi($config, $accountId, $apiKey) {
		$config->setUsername($accountId);
        $config->setPassword($apiKey);
        $config->setCurlTimeout(120);
        $config->setSSLVerification(false);
        return true;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function message_business_enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/message-business-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'message_business_material_icons', plugin_dir_url( __FILE__ ) . 'css/material-icons-font.css', array(), false, 'all' );
		// wp_enqueue_style( 'message_business_pace', plugin_dir_url( __FILE__ ) . 'css/pace.css', array(), false, 'all' );
		wp_register_style('jquery-ui', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
  		wp_enqueue_style( 'jquery-ui' );  
		wp_enqueue_style( 'message_business_woocommerce', plugin_dir_url( __DIR__ ) . 'woocommerce/css/message-business-woocommerce.css', array(), false, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function message_business_enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/message-business-admin.js', array( 
			'jquery', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable' ), $this->version, false );
		// wp_enqueue_script( 'message_business_pace', plugin_dir_url( __FILE__ ) . 'js/pace.js', array(), $this->version, false );
		wp_enqueue_script( 'message_business_woocommerce', plugin_dir_url( __DIR__ ) . 'woocommerce/js/message-business-woocommerce.js', array('jquery', 'jquery-ui-progressbar'), $this->version, false );
		wp_localize_script( 'message_business_woocommerce', 'message_business_woocommerce_ajax_object',
			array( 
				'message_business_woocommerce_ajax_url' => admin_url( 'admin-ajax.php' ),
				'message_business_woocommerce_nonce' => wp_create_nonce( 'message_business_woocommerce_import_contacts_form' )	
			)
		);
	}

	/**
	 * Register the plugin menu for the admin area.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function message_business_register_plugin_menu_page() {

		add_plugins_page(
			__('Message Business','message-business'),
			__('Message Business','message-business'),
			'manage_options',
			'messagebusiness',
			array( $this, 'message_business_settings_page' )
		);
	}

	/**
	 * Register the plugin settings for the admin area.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function message_business_register_plugin_settings() {

		// Message Business general settings
		register_setting( 'message-business-plugin-settings-group', 'MESSAGE_BUSINESS_ACCOUNTID' );
		register_setting( 'message-business-plugin-settings-group', 'MESSAGE_BUSINESS_APIKEY' );

		// Form builder settings
		register_setting( 'message-business-plugin-form-settings-group', 'MESSAGE_BUSINESS_FORMBUILDEROPTIONS' );
		register_setting( 'message-business-plugin-form-settings-group', 'MESSAGE_BUSINESS_INPUTSUBMITBUTTONTEXT' );
	}

	/**
	 * Displays the page content for the Message Business Settings submenu
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function message_business_settings_page() {

		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.', 'message-business') );
		}
		
		include(sprintf("%s/partials/message-business-admin-settings.php", dirname(__FILE__) ));
	}

	/**
	 * Validate input and return validated output settings
	 *
	 * @since 1.0.0
	 * @param [type] $input
	 * @return array
	 */
	public function message_business_validate_input_settings($input) {
		
		   // Create our array for storing the validated options
		   $output = array();
			
		   // Loop through each of the incoming options
		   foreach( $input as $key => $value ) {
				
			   // Check to see if the current option has a value. If so, process it.
			   if( isset( $input[$key] ) ) {
				
				   // Strip all HTML and PHP tags and properly handle quoted strings
				   $output[$key] = strip_tags( stripslashes( $input[ $key ] ) );
					
			   }
		   }
			
		   // Return the array processing any additional functions filtered by this action
		   return apply_filters( 'message_business_validate_inputs', $output, $input );
		
	}
	
}