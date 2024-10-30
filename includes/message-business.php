<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.message-business.com/
 * @since      1.0.0
 *
 * @package    Message_Business
 * @subpackage Message_Business/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Message_Business
 * @subpackage Message_Business/includes
 * @author     Message Business
 */
class Message_Business {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Message_Business_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'MESSAGE_BUSINESS_VERSION' ) ) {
			$this->version = MESSAGE_BUSINESS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'message-business';

		$this->message_business_load_dependencies();
		$this->message_business_set_locale();
		$this->message_business_define_admin_hooks();
		$this->message_business_define_public_hooks();
		$this->message_business_define_woocommerce_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Message_Business_Loader. Orchestrates the hooks of the plugin.
	 * - Message_Business_i18n. Defines internationalization functionality.
	 * - Message_Business_Admin. Defines all hooks for the admin area.
	 * - Message_Business_Public. Defines all hooks for the public side of the site.
     * - Message_Business_Widget. Defines the Message Business widget which contains public and admin part of the widget.
     * - Field. Defines field class with its properties.
     * - functions.php Defines all others functions used by the plugin such as the convert from json to HTML function.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function message_business_load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/message-business-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/message-business-i18n.php';

		/**
		 * The class Field responsible for defining the form field of the widget
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Message_Business_Field.php';

		/**
		 * The functions.php file contains some functionnalities of the plugin
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/functions.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/message-business-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/message-business-public.php';

		/**
		 * The class responsible for defining all actions that occur in the widget area of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'widget/message-business-newsletter-widget.php';

		/**
		 * The classes responsible for defining all actions that occur in the WooCommerce area of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'woocommerce/message-business-woocommerce.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'woocommerce/message-business-billing.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'woocommerce/message-business-shipping.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'woocommerce/message-business-customer.php';

		$this->loader = new Message_Business_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Message_Business_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function message_business_set_locale() {

		$plugin_i18n = new Message_Business_i18n();

		$this->loader->message_business_add_action( 'plugins_loaded', $plugin_i18n, 'message_business_load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function message_business_define_admin_hooks() {

		$plugin_admin = new Message_Business_Admin( $this->message_business_get_plugin_name(), $this->message_business_get_version() );
		$mb_woocommerce = new MESSAGE_BUSINESS_WooCommerce();
		$mb_newsletter_widget = new Message_Business_Newsletter_Widget();

		$this->loader->message_business_add_action( 'admin_enqueue_scripts', $plugin_admin, 'message_business_enqueue_styles' );
		$this->loader->message_business_add_action( 'admin_enqueue_scripts', $plugin_admin, 'message_business_enqueue_scripts' );
		$this->loader->message_business_add_action( 'admin_menu', $plugin_admin, 'message_business_register_plugin_menu_page' );
		$this->loader->message_business_add_action( 'admin_init', $plugin_admin, 'message_business_register_plugin_settings' );

		$this->loader->message_business_add_action( 'widgets_init', $mb_newsletter_widget, 'message_business_register_widget' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function message_business_define_public_hooks() {

		$plugin_public = new Message_Business_Public( $this->message_business_get_plugin_name(), $this->message_business_get_version() );

		$this->loader->message_business_add_action( 'wp_enqueue_scripts', $plugin_public, 'message_business_enqueue_styles' );
		$this->loader->message_business_add_action( 'wp_enqueue_scripts', $plugin_public, 'message_business_enqueue_scripts' );
	}
	
	/**
	 * Register all of the hooks related to the WooCommerce functionality
	 * of the plugin
	 * 
	 * @since    1.1.0
	 * @access   private
	 */
	private function message_business_define_woocommerce_hooks() {

		$plugin_woocommerce = new MESSAGE_BUSINESS_WooCommerce();
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function message_business_run() {
		$this->loader->message_business_run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function message_business_get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Message_Business_Loader    Orchestrates the hooks of the plugin.
	 */
	public function message_business_get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function message_business_get_version() {
		return $this->version;
	}

}
