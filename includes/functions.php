<?php

/**
 * generate HTML code from JSON fields (Field) object
 *
 * @param [type] $json
 * @return string
 */
function message_business_generateHTMLFromJSON($json) {

    // set Html code to empty
    $html = '';

    $html .= '<form name="message-business-newsletter-form" method="POST" action="' . admin_url('admin-ajax.php') . '">';
    $html .= '<label class="response"></label>';
    $html .= '<div class="subscribe">';

    foreach($json as $formOption) {

        if( isset($formOption->hidden) && $formOption->hidden === true ) {
            $html .= '<input type="hidden" id="' . $formOption->name . '"';
            $html .= ' name="' . $formOption->name . '"';
            $html .= ' value="' . $formOption->value . '"';
            $html .= ' >';
        } else {
            $type = 'text';
            if( $formOption->name == 'email' ) {
                $type = 'email';
            }
            if( $formOption->name == 'mobile' || $formOption->name == 'phone' || $formOption->name == 'fax' ) {
                $type= 'tel';
            }
            $html .= '<label for="' . $formOption->name . '">' . __( $formOption->label, 'message-business' ) . '</label>';
            $html .= '<input type="' . $type . '" id="' . $formOption->name . '"';
            $html .= ' name="' . $formOption->name . '"';
            if ( $type !== 'tel' ) {
                $html .= ' placeholder="' . __( $formOption->label, 'message-business' ) . '"';
            }
            if(!$formOption->optional) $html .= 'required';
            if( $type == 'email' ) {
                $html .= ' data-msg="' . __( 'Please enter a valid email address.', 'message-business' ) . '"';
            } elseif( $type == 'tel' ) {
                $html .= ' data-msg="' . __( 'Please enter a valid phone number.', 'message-business' ) . '"';
            } else {
                $html .= ' data-msg="' . __( 'This field is required.', 'message-business' ) . '"';
            }
            $html .= ' >';
        }
    }

    $html .= '<span class="message-business-loading"></span><button type="submit" class="button subscribesubmitbutton">' . esc_html( __( wp_unslash( get_option( 'MESSAGE_BUSINESS_INPUTSUBMITBUTTONTEXT', 'message-business' ) ) ), 'message-business' ) . '</button>';
    $html .= '</div>';
    $html .= '</form>';

    return htmlentities($html);
}

/**
 * sort two Field object by their position
 *
 * @param [Field] $a
 * @param [Field] $b
 * @return integer
 */
function message_business_sortFieldsByPosition($a, $b) {
	if($a->position == $b->position){ return 0 ; }
	return ($a->position < $b->position) ? -1 : 1;
}

function message_business_importCustomersToMessageBusiness() {
    // Import all contacts to M.B.
    $mb_woocommerce = new MESSAGE_BUSINESS_WooCommerce();
    $shop_url = get_option('MESSAGE_BUSINESS_SHOP_URL');
    $consumer_key = get_option('MESSSAGE_BUSINESS_CONSUMER_KEY');
    $consumer_secret = get_option('MESSAGE_BUSINESS_CONSUMER_SECRET');
    $mb_client = $mb_woocommerce->set_client($shop_url, $consumer_key, $consumer_secret);

    try {
        $page = 1;
        $condition = true;
        $status = true;
        
        while( $condition && $status ) {
            $page++;
            $customers = $mb_client->get('customers', ['page' => $page, 'per_page' => 100]);
            $contactsData = array();
    
            foreach( $customers as $customer ) {
                $mb_billing = new Message_Business_Billing(
                    $customer->billing->first_name,
                    $customer->billing->last_name,
                    $customer->billing->email,
                    $customer->billing->company
                );
                $mb_shipping = new Message_Business_Shipping(
                    $customer->shipping->first_name,
                    $customer->shipping->last_name,
                    $customer->shipping->company
                );
                $mb_customer = new Message_Business_Customer(
                    $customer->first_name,
                    $customer->last_name,
                    $customer->email,
                    $mb_billing,
                    $mb_shipping
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
                
                $contact = new Swagger\Client\Model\Message_Business_ContactData();
                $contact->setId(0);
                $contact->setContactKey('email');
                $contact->setAttributes($attributes);
                $contactsData[] = $contact;
            }
            
            // API call
            $contactsApi = new Swagger\Client\Api\Message_Business_ContactsApi();
            $moduleApi = new Swagger\Client\Api\Message_Business_ModulesApi();
            try {
                $moduleTest = $moduleApi->modulesGetConnectionTest();
                try {
                    $resultApi =  $contactsApi->contactsPostContactsAttributeKey( $contactsData );
                    
                    if( $resultApi == '"100 contact(s) affected."' ) {
                        $status = true;
                        // $response['status'] = 'success';
                        // $response['message'] = esc_html( __( 'The form has been submitted successfully.', 'message-business' ) );
                    } else {
                        $status = false;
                        // $response['status'] = 'error';
                        // $response['message'] = esc_html( __( 'An error has occurred! Please try again.', 'message-business' ) );
                    }
                } catch( Exception $e ) {
                    $status = false;
                    // $response['status'] = 'error';
                    // $response['message'] = esc_html( __( 'This email address is already registered.', 'message-business' ) );
                }
                
            } catch(Exception $e) {
                $status = false;
                // $response['status'] = 'error';
                // if( current_user_can( 'manage_options' ) ) {
                //     $response['message'] = __( 'Please set your Account number and Api key to link your form to Message Business.' . sprintf( ' <a href="%s">%s</a>', $pluginUrl, 'Click here to set your Account number and Api key' ) );    
                // } else {
                //     $response['message'] = esc_html( __( 'An error has occurred! Please try again.', 'message-business' ) );
                // }
            }
            if( sizeof($customers) !== 100 ) {
                $condition = false;
            }
        }
        // If something went wrong we save the last page from the list of customers to continue the import on the next job scheduled
        if( !$status ) {
            update_option('MESSAGE_BUSINESS_LAST_PAGE_CUSTOMERS', $page);
        } else {
            // otherwise we remove the option 'MESSAGE_BUSINESS_LAST_PAGE_CUSTOMERS'
            delete_option('MESSAGE_BUSINESS_LAST_PAGE_CUSTOMERS');
        }
        return $status;
        // add_settings_error( 'message_business_messages', 'message_business_message_success', __( $response['message'], 'message-business' ), $response['status'] );
    } catch ( Automattic\WooCommerce\HttpClient\HttpClientException $e ) {
        return false;
        // add_settings_error( 'message_business_messages', 'message_business_message_error', __( $e->getMessage(), 'message-business' ), 'error' );
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
function message_business_init_app($config, $accountId, $apiKey) {
    $config->setUsername($accountId);
    $config->setPassword($apiKey);
    $config->setCurlTimeout(120);
    $config->setSSLVerification(false);
    return true;
}

function message_business_gmt_to_local_timestamp( $gmt_timestamp ) {
	return get_date_from_gmt( date( 'Y-m-d H:i:s', $gmt_timestamp ), 'U' );
}

function message_business_is_woocommerce_activated() {
    if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
}

function message_business_create_woocommerce_custom_fields() {
    if( message_business_is_woocommerce_activated() ) {
        $accountApi = new Swagger\Client\Api\Message_Business_AccountApi();

        // contact fields
        $json_string = file_get_contents(MESSAGE_BUSINESS_PLUGIN_DIR. 'includes/woocommerce-fields.json');
        $woo_fields = json_decode($json_string);

        if( !empty($woo_fields) ) {
            foreach( $woo_fields as $woo_field ) {
                $contact_field = new Swagger\Client\Model\Message_Business_ContactField();
                $contact_field->setName( $woo_field->name );
                $contact_field->setFormat( $woo_field->format );
                try {
                    $accountApi->accountPostContactAttribute( $contact_field );
                } catch( Exception $e ) {
                    $e->getMessage();
                }
            }
        }
    }
}

function message_business_get_custom_fields_object() {
    $accountApi = new Swagger\Client\Api\Message_Business_AccountApi();
    try {
        $contacts_list = $accountApi->accountGetAllContactAttribute();
        if( !empty( $contacts_list ) ) {
            return $contacts_list;
        }
    } catch( Exception $e ) {
        error_log( $e->getMessage() );
    }
    return null;
}

function message_business_get_contact_field_id( &$custom_fields_object, &$field_description ) {

    if( !is_null( $custom_fields_object ) && !empty( $field_description ) ) {
        foreach( $custom_fields_object as $custom_field ) {
            if( $custom_field['description'] === $field_description ) {
                return $custom_field['id'];
            }
        }
    }
    return null;
}

function message_business_get_cancelled_carts_json($customer_id) {

    $currency = get_woocommerce_currency();
    $currency_symbol = get_woocommerce_currency_symbol();
    global $wpdb;
    $array = $wpdb->get_results("select session_value, session_expiry from ".$wpdb->prefix."woocommerce_sessions");
    $cart_array = array();

    set_time_limit(0);
    foreach( $array as $session ) {
        $session_value = unserialize( $session->session_value );
        $session_expiry = $session->session_expiry;
        $session_expiry_date = date( 'Y-m-d H:i:s', $session_expiry );
        $woo_updated_cart = get_user_meta($customer_id, '_woocommerce_updated_cart_' . get_current_blog_id(), true);
        $cart_last_updated_date = '';
        if( $woo_updated_cart ) {
            $cart_last_updated_date = $woo_updated_cart['cart_updated'];
        }
        $customer = unserialize( $session_value['customer']);
        $cart_customer_id = $customer['id'];
        if( $cart_customer_id === $customer_id ) {
            $cart = unserialize( $session_value['cart']);
            $cart_totals = unserialize( $session_value['cart_totals'] );
            $cart_subtotal = $cart_totals['subtotal'];
            $cart_subtotal_tax = $cart_totals['subtotal_tax'];
            $cart_shipping_total = $cart_totals['shipping_total'];
            $cart_shipping_tax = $cart_totals['shipping_tax'];
            $cart_total = $cart_totals['total'];

            $cart_array = array( 
                "session_expiry" => $session_expiry_date,
                "last_updated_date" => $cart_last_updated_date,
                "currency" => $currency,
                "currency_symbol" => $currency_symbol,
                "cart_subtotal" => $cart_subtotal,
                "cart_subtotal_tax" => $cart_subtotal_tax,
                "cart_shipping_total" => $cart_shipping_total,
                "cart_shipping_tax" => $cart_shipping_tax,
                "cart_total" => $cart_total,
                "cart_url" => wc_get_cart_url(),
                "cart_items" => array()
            );

            foreach( $cart as $cart_item ) {
                $product_id = $cart_item['product_id'];
                $product_url = get_permalink( $product_id );
                $product = wc_get_product( $product_id );
                $product_title = $product->get_title();
                $short_description = $product->get_short_description(); 
                $long_description = $product->get_description();
                $image = $product->get_image();
                $image_url = get_the_post_thumbnail_url($product_id);
                if( is_null( $image_url ) ) {
                    $image_url = '';
                }
                $price = $product->get_price();
                $item_quantity = $cart_item['quantity'];
                $item_subtotal = $cart_item['line_subtotal'];
                $item_subtotal_tax = $cart_item['line_subtotal_tax'];
                $item_total = $cart_item['line_total'];

                $items_array = array(
                    "item_title" => $product_title,
                    "item_url" => $product_url,
                    "item_short_description" => $short_description,
                    "item_long_description" => $long_description,
                    "item_image" => $image_url,
                    "item_price" => $price,
                    "item_quantity" => $item_quantity,
                    "item_subtotal" => $item_subtotal,
                    "item_subtotal_tax" => $item_subtotal_tax,
                    "item_total" => $item_total
                );

                array_push( $cart_array['cart_items'], $items_array );
            }
        }
    }
    $json_string = '';
    if( !empty( $cart_array ) )  {
        $json_string = json_encode( $cart_array, JSON_UNESCAPED_SLASHES );
    }
    return $json_string;    
}

function message_business_set_last_login($login) {
    $user = get_user_by('login', $login);
    update_user_meta( $user->ID, 'last_login', current_time('mysql') );
}
add_action('wp_login', 'message_business_set_last_login');

function message_business_custom_import_frequencies($schedules) {
	$schedules['three_hours'] = array(
		'interval' => 10800,
		'display' => __('Every three hours')
	);
	$schedules['six_hours'] = array(
		'interval' => 21600,
		'display' => __('Every six hours')
    );
    $schedules['nine_hours'] = array(
		'interval' => 32400,
		'display' => __('Every nine hours')
	);
	$schedules['twice_a_day'] = array(
		'interval' => 43200,
		'display' => __('Twice a day')
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'message_business_custom_import_frequencies');