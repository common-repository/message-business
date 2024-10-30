<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require MESSAGE_BUSINESS_PLUGIN_DIR . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;

class MESSAGE_BUSINESS_WooCommerce {

    /**
     * WooCommerce module's name
	 * @var string
	 */
	public $plugin_name = "message-business";
    
    /**
     * WooCommerce module's version
     * @var string
     */
    public $version = "1.1.0";

    /**
     * WooCommerce API client
     * @var Client
     */
    private $client;

    public function __construct() {
        if( $this->is_activated() ) {
            add_action( 'wp_ajax_message_business_woocommerce_import_contacts', array($this, 'message_business_import_contacts_to_mb_post') );
            add_action( 'message_business_import_contacts', array( $this, 'message_business_import_contacts_to_mb') );
            add_action( 'woocommerce_cart_updated', array( $this, 'message_business_cart_updated' ) );
        }
    }

    /**
	 * Register the stylesheets for the WooCommerce side of the site.
	 *
	 * @since    1.1.0
	 */
	public function message_business_enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/message-business-woocommerce.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the WooCommerce side of the site.
	 *
	 * @since    1.1.0
	 */
	public function message_business_enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/message-business-woocommerce.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( 'message_business_woocommerce', plugin_dir_url( __FILE__ ) . 'js/message-business-woocommerce.js', array('jquery'), false, true );
		wp_localize_script( 'message_business_woocommerce', 'message_business_woocommerce_ajax_object',
			array( 
				'message_business_woocommerce_ajax_url' => admin_url( 'admin-ajax.php' ),
				'message_business__woocommerce_nonce' => wp_create_nonce( 'message_business_woocommerce_import_contacts_form' )	
            )
        );
        
	}

    /**
     * Check if WooCommerce is installed and activated
	 * @return boolean
	 */
	public function is_activated() {

		return in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
    }

    public function message_business_cart_updated($user_id) {
        update_user_meta( wp_get_current_user()->ID, '_woocommerce_updated_cart_' . get_current_blog_id(), array(
            'cart_updated' => date('Y-m-d H:i:s', time()),
        ) );
    }

    /**
     * Set WooCommerce API Client
     *
     * @param [string] $shop_url
     * @param [string] $consumer_key
     * @param [string] $consumer_secret
     * @return Client
     */
    public function set_client($shop_url, $consumer_key, $consumer_secret) {

        $options = array( 
            'wp_api' => true,
            'version' => 'wc/v2'
        );

        $this->client = new Client(
            $shop_url, 
            $consumer_key, 
            $consumer_secret,
            $options
        );
        return $this->client;
    }

    /**
     * Get current WooCommerce API Client
     *
     * @return Client
     */
    public function get_client() {
        return $this->client;
    }
    
    private function add_contact($mb_client, $page, &$custom_fields_object) {
        $customers = $mb_client->get('customers', ['page' => $page, 'per_page' => 50]);
        $contactsData = array();
        $condition = true;
        foreach( $customers as $customer ) {
            //$orders = $mb_client->get('orders', ['per_page' => 100, 'status' => 'completed', 'customer' => $customer->id]);          
            $orders = wc_get_orders( array( 'status' => 'completed', 'customer_id' => $customer->id ) );
            $mb_turnover = 0;
            if( !empty( $orders ) ) {
                foreach( $orders as $order ) {
                    $mb_turnover = $mb_turnover + floatval( $order->get_total() );
                }
            }

            $mb_billing = new Message_Business_Billing(
                $customer->billing->first_name,
                $customer->billing->last_name,
                $customer->billing->email,
                $customer->billing->company,
                $customer->billing->address_1,
                $customer->billing->address_2,
                $customer->billing->postcode,
                $customer->billing->city,
                $customer->billing->country
            );
            $mb_shipping = new Message_Business_Shipping(
                $customer->shipping->first_name,
                $customer->shipping->last_name,
                $customer->shipping->company,
                $customer->shipping->address_1,
                $customer->shipping->address_2,
                $customer->shipping->postcode,
                $customer->shipping->city,
                $customer->shipping->country
            );
            $mb_customer = new Message_Business_Customer(
                $customer->first_name,
                $customer->last_name,
                $customer->email,
                $mb_billing,
                $mb_shipping,
                $mb_turnover
            );                

            /* Firstname attribute */
            $firstname = new Swagger\Client\Model\Message_Business_ContactAttribute();
            $firstname->setId('firstname');
            $firstname->setFieldName('firstname');
            $firstname->setFieldValue( $mb_customer->getFirstName() );
            $attributes[] = $firstname;

            /* Lastname attribute */
            $lastname = new Swagger\Client\Model\Message_Business_ContactAttribute();
            $lastname->setId('lastname');
            $lastname->setFieldName('lastname');
            $lastname->setFieldValue( $mb_customer->getLastName() );
            $attributes[] = $lastname;

            /* Email attribute */
            $email = new Swagger\Client\Model\Message_Business_ContactAttribute();
            $email->setId('email');
            $email->setFieldName('email');
            $email->setFieldValue( $mb_customer->getEmail() );
            $attributes[] = $email;

            /* Company name attribute */
            $companyname = new Swagger\Client\Model\Message_Business_ContactAttribute();
            $companyname->setId('companyname');
            $companyname->setFieldName('companyname');
            $companyname->setFieldValue( $mb_customer->getCompanyName() );
            $attributes[] = $companyname;

            /* Address attributes */
            $address1 = new Swagger\Client\Model\Message_Business_ContactAttribute();
            $address1->setId('address1');
            $address1->setFieldName('address1');
            $address1->setFieldValue( $mb_customer->getAddress1() );
            $attributes[] = $address1;

            $address2 = new Swagger\Client\Model\Message_Business_ContactAttribute();
            $address2->setId('address2');
            $address2->setFieldName('address2');
            $address2->setFieldValue( $mb_customer->getAddress2() );
            $attributes[] = $address2;

            $zipcode = new Swagger\Client\Model\Message_Business_ContactAttribute();
            $zipcode->setId('zipcode');
            $zipcode->setFieldName('zipcode');
            $zipcode->setFieldValue( $mb_customer->getZipCode() );
            $attributes[] = $zipcode;

            $city = new Swagger\Client\Model\Message_Business_ContactAttribute();
            $city->setId('city');
            $city->setFieldName('city');
            $city->setFieldValue( $mb_customer->getCity() );
            $attributes[] = $city;

            $country = new Swagger\Client\Model\Message_Business_ContactAttribute();
            $country->setId('country');
            $country->setFieldName('country');
            $country->setFieldValue( $mb_customer->getCountry() );
            $attributes[] = $country;

            /* WooCommerce contact attributes */

            // get turnover field ID
            $woo_orders_total_description = "woo_orderstotal";
            $woo_orders_total_id = message_business_get_contact_field_id( $custom_fields_object, $woo_orders_total_description );

            if( !is_null( $woo_orders_total_id ) ) {
                $turnover = new Swagger\Client\Model\Message_Business_ContactAttribute();
                $turnover->setId( $woo_orders_total_id );
                $turnover->setFieldName( $woo_orders_total_id );
                $turnover->setFieldValue( $mb_customer->getTurnover() );
                $attributes[] = $turnover;
            }

            // get cancelled carts field ID
            $woo_cancelled_carts_description = "woo_cancelledcarts";
            $woo_cancelled_carts_id = message_business_get_contact_field_id( $custom_fields_object, $woo_cancelled_carts_description );

            if( !is_null( $woo_cancelled_carts_id ) ) {
                // get cancelled carts
                $json = message_business_get_cancelled_carts_json($customer->id);
                $cancelled_carts = new Swagger\Client\Model\Message_Business_ContactAttribute();
                $cancelled_carts->setId( $woo_cancelled_carts_id );
                $cancelled_carts->setFieldName( $woo_cancelled_carts_id );
                $cancelled_carts->setFieldValue( $json );
                $attributes[] = $cancelled_carts;
            }

            // get cancelled cart date field ID
            $woo_last_cancelled_cart_date_description = "woo_lastcancelledcartdate";
            $woo_last_cancelled_cart_date_id = message_business_get_contact_field_id( $custom_fields_object, $woo_last_cancelled_cart_date_description );

            if( !is_null( $woo_last_cancelled_cart_date_id ) ) {
                // get last cancelled cart date
                $cart = json_decode( $json, true );
                $woo_last_cancelled_cart_date = $cart['last_updated_date'];
                $last_cancelled_cart_date = new Swagger\Client\Model\Message_Business_ContactAttribute();
                $last_cancelled_cart_date->setId( $woo_last_cancelled_cart_date_id );
                $last_cancelled_cart_date->setFieldName( $woo_last_cancelled_cart_date_id );
                $last_cancelled_cart_date->setFieldValue( $woo_last_cancelled_cart_date );
                $attributes[] = $last_cancelled_cart_date;
            }

            // get last order date field ID
            $woo_last_order_date_description = "woo_lastorderdate";
            $woo_last_order_date_id = message_business_get_contact_field_id( $custom_fields_object, $woo_last_order_date_description );
            
            if( !is_null( $woo_last_order_date_id ) ) {
                // get last order date value
                $order = wc_get_orders( array( 'limit' => 1, 'orderby' => 'date', 'order' => 'DESC', 'status' => array( 'pending', 'on-hold', 'processing', 'completed' ), 'customer_id' => $customer->id ) );
                if( !empty( $order ) ) {
                    $order_data = $order[0]->get_data();
                    $last_order_date_value = $order_data['date_created']->date('Y-m-d H:i:s');
                    $last_order_date = new Swagger\Client\Model\Message_Business_ContactAttribute();
                    $last_order_date->setId( $woo_last_order_date_id );
                    $last_order_date->setFieldName( $woo_last_order_date_id );
                    $last_order_date->setFieldValue( $last_order_date_value );
                    $attributes[] = $last_order_date;
                }
            }

            // get last customer connection date field ID
            $woo_last_connection_description = 'woo_lastconnection';
            $woo_last_connection_id = message_business_get_contact_field_id( $custom_fields_object, $woo_last_connection_description );

            if( !is_null( $woo_last_connection_id ) ) {
                // get last connection date
                $last_login_date =  get_user_meta($customer->id, 'last_login', true);
                $last_connection = new Swagger\Client\Model\Message_Business_ContactAttribute();
                $last_connection->setId( $woo_last_connection_id );
                $last_connection->setFieldName( $woo_last_connection_id );
                $last_connection->setFieldValue( $last_login_date );
                $attributes[] = $last_connection;
            }

            // get registration date field ID
            $woo_date_add_description = 'woo_dateadd';
            $woo_date_add_id = message_business_get_contact_field_id( $custom_fields_object, $woo_date_add_description );

            if( !is_null( $woo_date_add_id ) ) {
                // get customer registration date
                $registration_date = date( 'Y-m-d H:i:s', strtotime( $customer->date_created_gmt ) );
                // $user_data = get_userdata( $customer->id );
                // $registration_date = date( 'Y-m-d H:i:s', strtotime( $user_data->user_registered ) );
                $woo_date_add = new Swagger\Client\Model\Message_Business_ContactAttribute();
                $woo_date_add->setId( $woo_date_add_id );
                $woo_date_add->setFieldName( $woo_date_add_id );
                $woo_date_add->setFieldValue( $registration_date );
                $attributes[] = $woo_date_add;
            }

            // get first order date field ID
            $woo_first_order_date_description = 'woo_firstorderdate';
            $woo_first_order_date_id = message_business_get_contact_field_id( $custom_fields_object, $woo_first_order_date_description );

            if( !is_null( $woo_first_order_date_id ) ) {
                // get first order date
                $order = wc_get_orders( array( 'limit' => 1, 'orderby' => 'date', 'order' => 'ASC', 'status' => array( 'pending', 'on-hold', 'processing', 'completed' ), 'customer_id' => $customer->id ) );
                if( !empty( $order ) ) {
                    $order_data = $order[0]->get_data();
                    $first_order_date_value = $order_data['date_created']->date('Y-m-d H:i:s');
                    $first_order_date = new Swagger\Client\Model\Message_Business_ContactAttribute();
                    $first_order_date->setId( $woo_first_order_date_id );
                    $first_order_date->setFieldName( $woo_first_order_date_id );
                    $first_order_date->setFieldValue( $first_order_date_value );
                    $attributes[] = $first_order_date;
                }
            }

            // get last order total field ID
            $woo_last_order_total_description = 'woo_lastordertotal';
            $woo_last_order_total_id = message_business_get_contact_field_id( $custom_fields_object, $woo_last_order_total_description );

            if( !is_null( $woo_last_order_total_id ) ) {
                // get last order total value
                $order = wc_get_orders( array( 'limit' => 1, 'orderby' => 'date', 'order' => 'DESC', 'status' => 'completed', 'customer_id' => $customer->id ) );
                if( !empty( $order ) ) {
                    $order_data = $order[0]->get_data();
                    $last_order_total_value = wc_format_decimal( $order_data['total'], 2 );
                    $last_order_total = new Swagger\Client\Model\Message_Business_ContactAttribute();
                    $last_order_total->setId( $woo_last_order_total_id );
                    $last_order_total->setFieldName( $woo_last_order_total_id );
                    $last_order_total->setFieldValue( $last_order_total_value );
                    $attributes[] = $last_order_total;
                }
            }

            // get orders count field ID
            $woo_orders_count_description = 'woo_orderscount';
            $woo_orders_count_id = message_business_get_contact_field_id( $custom_fields_object, $woo_orders_count_description );

            if( !is_null( $woo_orders_count_id ) ) {
                // get orders count value
                $args = array( 
                    'status' => array( 'pending', 'on-hold', 'processing', 'completed' ),
                    'customer_id' => $customer->id
                );
                $orders = wc_get_orders( $args );
                $orders_count = sizeof( $orders );
                // $orders_count = $customer->orders_count;
                $woo_orders_count = new Swagger\Client\Model\Message_Business_ContactAttribute();
                $woo_orders_count->setId( $woo_orders_count_id );
                $woo_orders_count->setFieldName( $woo_orders_count_id );
                $woo_orders_count->setFieldValue( $orders_count );
                $attributes[] = $woo_orders_count;
            }

            /* End WooCommerce contact attributes */

            $contact = new Swagger\Client\Model\Message_Business_ContactData();
            $contact->setId(0);
            $contact->setContactKey($mb_customer->getEmail());
            $contact->setAttributes($attributes);
            $contactsData[] = $contact;
        }
        
        // API call
        $contactsApi = new Swagger\Client\Api\Message_Business_ContactsApi();
        $moduleApi = new Swagger\Client\Api\Message_Business_ModulesApi();
        if( sizeof($contactsData) > 0 ) {
            $resultApi =  $contactsApi->contactsPostContactsAttributeKey( $contactsData );
            $countContacts = sizeof( $contactsData );

            if( $resultApi == '"' . $countContacts . ' contact(s) affected."' ) {
                $status = true;
            } else {
                $status = false;
            }
        }
        if( sizeof($customers) !== 50 ) {
            $condition = false;
        }
        return $condition;
    }

    public function message_business_import_contacts_to_mb() {

        // ini_set('max_execution_time', 300);

        // Import all contacts to M.B.
        $shop_url = get_option('MESSAGE_BUSINESS_SHOP_URL');
        $consumer_key = get_option('MESSAGE_BUSINESS_CONSUMER_KEY');
        $consumer_secret = get_option('MESSAGE_BUSINESS_CONSUMER_SECRET');
        $mb_client = $this->set_client($shop_url, $consumer_key, $consumer_secret);

        $page = 1;
        $status = true;
        // we save the start date of last import
        date_default_timezone_set('UTC');
        update_option( 'MESSAGE_BUSINESS_START_DATE_LAST_IMPORT_CUSTOMERS', date('Y-m-d H:i:s', time() ) );

        // get custom fields from M.B.
        $custom_fields_object = message_business_get_custom_fields_object();

        set_time_limit(0);
        while( $this->add_contact($mb_client, $page, $custom_fields_object) ) {
            $page++;
        }

        if( !$status ) {
            update_option('MESSAGE_BUSINESS_LAST_PAGE_CUSTOMERS', $page);
        } else {
            // otherwise we remove the option 'MESSAGE_BUSINESS_LAST_PAGE_CUSTOMERS'
            delete_option('MESSAGE_BUSINESS_LAST_PAGE_CUSTOMERS');
            // we save the end date of last import
            update_option( 'MESSAGE_BUSINESS_END_DATE_LAST_IMPORT_CUSTOMERS', date( 'Y-m-d H:i:s', time() ) );
        }

        return $status;
    }

    /**
     * Import all contacts from WooCommerce to Message Business
     *
     * @return boolean
     */
    public function message_business_import_contacts_to_mb_post() {
        if ( !wp_verify_nonce( $_REQUEST['nonce'], "message_business_woocommerce_import_contacts_form")) {
            exit("Security error!");
        }

        // ini_set('max_execution_time', 300);

        // if WooCommerce is installed and activated, we create all the WooCoommerce fields needed for the plugin
        message_business_create_woocommerce_custom_fields();

        $shop_url = ( !empty( $_POST['MESSAGE_BUSINESS_SHOP_URL'] ) ? $_POST['MESSAGE_BUSINESS_SHOP_URL'] : '' );
        $consumer_key = sanitize_key( $_POST['MESSAGE_BUSINESS_CONSUMER_KEY'] );
        $consumer_secret = sanitize_key( $_POST['MESSAGE_BUSINESS_CONSUMER_SECRET'] );
        $frequency = ( $_POST['MESSAGE_BUSINESS_IMPORT_CUSTOMERS_FREQUENCY'] !== '' ? $_POST['MESSAGE_BUSINESS_IMPORT_CUSTOMERS_FREQUENCY'] : '' );
        $frequencies = array( 0, 1, 3, 6, 9, 12, 24 );
        switch ($frequency) {
            case 0:
                $frenquency_time = 'once';
                $frequency_name = 'once';
                break;
            case 1:
                $frenquency_time = 3600;
                $frequency_name = 'hourly';
                break;
            case 3:
                $frenquency_time = 10800;
                $frequency_name = 'three_hours';
                break;
            case 6:
                $frenquency_time = 21600;
                $frequency_name = 'six_hours';
                break;
            case 9:
                $frenquency_time = 32400;
                $frequency_name = 'nine_hours';
                break;
            case 12:
                $frenquency_time = 43200;
                $frequency_name = 'twice_a_day';
                break;
            case 24:
                $frenquency_time = 86400;
                $frequency_name = 'daily';
                break;
        }
        $json = array();

        if( empty( $shop_url ) && esc_url( $shop_url ) ) {
            $json['status'] = 'error';
            $json['message'] = __( 'Please specify your shop url.', 'message-business' );
        } elseif( empty( $consumer_key ) ) {
            $json['status'] = 'error';
            $json['message'] = __( 'Please specify a consumer key.', 'message-business' );
        } elseif( empty( $consumer_secret ) ) {
            $json['status'] = 'error';
            $json['message'] = __( 'Please specify a consumer secret.', 'message-business' );
        } elseif( !in_array( $frequency, $frequencies ) ) {
            $json['status'] = 'error';
            $json['message'] = __( 'Please select a valid frequency.', 'message-business' );
        } else {
            // save the shop url and consumer keys then show a success message
            update_option( 'MESSAGE_BUSINESS_SHOP_URL', $shop_url );
            update_option( 'MESSAGE_BUSINESS_CONSUMER_KEY', $consumer_key );
            update_option( 'MESSAGE_BUSINESS_CONSUMER_SECRET', $consumer_secret );
            // we save the selected import frequency
            update_option( 'MESSAGE_BUSINESS_IMPORT_CUSTOMERS_FREQUENCY', $frequency );

            try {
                $status = $this->message_business_import_contacts_to_mb();
                // If something went wrong we save the last page from the list of customers to continue the import on the next job scheduled
                if( !$status ) {
                    $json['status'] = 'error';
                    $json['message'] = __( 'An error has occurred during the process. Please try again.', 'message-business' );
                } else {
                    $json['status'] = 'success';
                    $json['message'] = __( 'Customers data have been successfully synchronized.', 'message-business' );
                    $json['last-import-date'] = date( 'd/m/Y H:i:s', message_business_gmt_to_local_timestamp( strtotime( get_option( 'MESSAGE_BUSINESS_END_DATE_LAST_IMPORT_CUSTOMERS' ) ) ) );
                    if( strtotime( get_option( 'MESSAGE_BUSINESS_END_DATE_LAST_IMPORT_CUSTOMERS' ) ) > strtotime( get_option( 'MESSAGE_BUSINESS_START_DATE_LAST_IMPORT_CUSTOMERS' ) ) ) {
                        $last_import_duration = gmdate("H:i:s", ( strtotime( get_option( 'MESSAGE_BUSINESS_END_DATE_LAST_IMPORT_CUSTOMERS' ) ) - strtotime( get_option( 'MESSAGE_BUSINESS_START_DATE_LAST_IMPORT_CUSTOMERS' ) ) ) );
                        $json['last-import-duration'] = $last_import_duration;
                    }

                    // we schedule a WP cron job
                    if( wp_next_scheduled( 'message_business_import_contacts' ) ) {
                        $next_import_timestamp = wp_next_scheduled( 'message_business_import_contacts' );
                        wp_unschedule_event( $next_import_timestamp, 'message_business_import_contacts' );
                    }
                    if( $frequency !== 0 ) {
                        if( !wp_next_scheduled( 'message_business_import_contacts' ) ) {
                            wp_schedule_event( time() + $frenquency_time, $frequency_name, 'message_business_import_contacts' );
                        }
                    }

                    // get next import date
                    if ( wp_next_scheduled( 'message_business_import_contacts' ) ) {
                        $next_import_date = date( 'd/m/Y H:i:s', message_business_gmt_to_local_timestamp( wp_next_scheduled( 'message_business_import_contacts' ) ) );
                        $json['next-import-date'] = $next_import_date; 
                    } else {
                        $json['next-import-date'] = __( 'Not scheduled.', 'message-business' );
                    }
                }
            } catch ( Automattic\WooCommerce\HttpClient\HttpClientException $e ) {
                $json['status'] = 'error';
                $json['message'] = $e->getMessage();
            }
        }
        header('Content-Type: application/json');
        echo json_encode( $json );
        die();
    }
}