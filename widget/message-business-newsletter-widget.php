<?php

class Message_Business_Newsletter_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'message_business_newsletter_widget',
			'description' => 'Newsletter form by Message Business',
		);
        parent::__construct( 'message_business_newsletter_widget', 'Message Business Newsletter', $widget_ops );
		add_action('wp_enqueue_scripts', array(&$this, 'message_business_load_css'));
		add_action('wp_enqueue_scripts', array(&$this, 'message_business_load_js'));

		add_action( 'wp_ajax_message_business_post_form', array( &$this, 'message_business_post_form' ) );
		add_action( 'wp_ajax_nopriv_message_business_post_form', array( &$this, 'message_business_post_form' ) );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
        
        // outputs the content of the widget
		echo $args['before_widget'];
		
        if ( !empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		// get the Html code for the widget form and display it
		$widgetHtml = '';
		$widgetHtml = html_entity_decode( get_option( 'MESSAGE_BUSINESS_WIDGETFORMHTML' ) );
		echo $widgetHtml;
		
		?>
        
        <?php
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Widget title', 'message-business' );
		?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title', 'message-business' ); ?>:</label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
		<p class="help"></p>
        <?php
    }

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        return $instance;
    }
    
    /**
	 * Register widget
	 *
	 */
    public function message_business_register_widget() {
		register_widget( get_class() );
    }

    public function message_business_load_css() {

        if ( is_active_widget( false, false, $this->id_base, true ) ) {

			wp_enqueue_style( 'message_business_newsletter_widget', plugin_dir_url( __FILE__ ) . 'css/message-business-widget.css', array(), false, 'all' );
			wp_enqueue_style( 'message_business_international_tel_input', plugin_dir_url( __FILE__ ) . 'css/intlTelInput.css', array(), false, 'all' );
        }
	}

	public function message_business_load_js() {

		if ( is_active_widget( false, false, $this->id_base, true ) ) {

			// wp_register_script( 'message_business_ajax_script', plugin_dir_url( __FILE__ ) . 'js/message-business-widget.js', array('jquery') );
			wp_enqueue_script( 'message_business_newsletter_widget', plugin_dir_url( __FILE__ ) . 'js/message-business-widget.js', array('jquery'), false, true );
			wp_enqueue_script( 'message_business_jquery_validate', plugin_dir_url( __FILE__ ) . 'js/jquery.validate.min.js', array(), false, true );
			wp_enqueue_script( 'message_business_international_tel_input', plugin_dir_url( __FILE__ ) . 'js/intlTelInput.js', array(), false, true );

			wp_localize_script( 'message_business_newsletter_widget', 'message_business_ajax_object',
			array( 
				'message_business_ajax_url' => admin_url( 'admin-ajax.php' ),
				'message_business_nonce' => wp_create_nonce( 'message_business_widget_form' )	
			) );

		}
	}

	public function message_business_post_form() {

		if ( is_active_widget( false, false, $this->id_base, true ) ) {

			if ( !wp_verify_nonce( $_REQUEST['nonce'], "message_business_widget_form")) {
				exit("Security error!");
			}

			$data = $_REQUEST['data'];
			$attributes = array();
			$json['id'] = 0;
			$json['contactKey'] = 'email';
			$json['attributes'] = array();
			$emailPattern = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';
			$response = array();

			$fields = array();
			$fieldsJSON = json_decode( file_get_contents( MESSAGE_BUSINESS_PLUGIN_DIR . 'includes/fields.json' ) );
			foreach( $fieldsJSON->fields as $field ) {
				array_push( $fields, $field->name );
			}

			foreach($data as $key => $value) {
				$isOptional = json_decode( $value['optional'] );
				// check that the field id is correct
				if( isset( $value['id'] ) &&  in_array( $value['id'], $fields ) ) {
					
					// check that all the required fields are filled
					if( ( isset( $isOptional ) && $isOptional === false ) ) {
						
						if( !empty( $value['fieldValue'] ) || !is_null( $value['fieldValue'] ) ) {

							if( $value['id'] === 'email') {
								
								if( !preg_match( $emailPattern, $value['fieldValue'] ) ) {
									$response['status'] = 'error';
									$response['message'] = esc_html( __( 'Invalid format', 'message-business' ) );
								} else {
									$json['contactKey'] = $value['fieldValue'];
								
									// force emailoptin = yes
									$emailoptin['id'] = 'emailoptin';
									$emailoptin['fieldName'] = 'emailoptin';
									$emailoptin['fieldValue'] = 'true';
									array_push( $attributes, $emailoptin );
								}
							}
						} else {

							$response['status'] = 'error';
							$response['message'] = esc_html( __( 'Please fill the required fields.', 'message-business' ) );
						}
					}
					
					//$contact = new Swagger\Client\Model\Message_Business_ContactData();
					//$contact->setAttributes($value);

					array_push( $attributes, $value );
				}
			}

			if( $response['status'] !== 'error' ) {
				
				$json['attributes'] = $attributes;
				$json = json_encode( $json );
				$pluginUrl = esc_url( admin_url( 'plugins.php?page=messagebusiness' ) );

				// API call
				$contactApi = new Swagger\Client\Api\Message_Business_ContactApi();
				$moduleApi = new Swagger\Client\Api\Message_Business_ModulesApi();
				try {
					$moduleTest = $moduleApi->modulesGetConnectionTest();
					try {
						$resultApi =  $contactApi->contactPostContactAttributeKey( $json );
						
						if( $resultApi == '"1 contact(s) affected."' ) {
							$response['status'] = 'success';
							$response['message'] = esc_html( __( 'The form has been submitted successfully.', 'message-business' ) );
							if( get_option( 'MESSAGE_BUSINESS_HIDEFORMAFTERSUBMIT' ) ) {
								$response['hideform'] = true;
							} else {
								$response['hideform'] = false;
							}
						} else {
							$response['status'] = 'error';
							$response['message'] = esc_html( __( 'An error has occurred! Please try again.', 'message-business' ) );
						}
					} catch( Exception $e ) {
						$response['status'] = 'error';
						$response['message'] = esc_html( __( 'This email address is already registered.', 'message-business' ) );
					}
					
				} catch(Exception $e) {
					$response['status'] = 'error';

					if( current_user_can( 'manage_options' ) ) {
						$response['message'] = __( 'Please set your Account number and Api key to link your form to Message Business.' . sprintf( ' <a href="%s">%s</a>', $pluginUrl, 'Click here to set your Account number and Api key' ) );    
					} else {
						$response['message'] = esc_html( __( 'An error has occurred! Please try again.', 'message-business' ) );
					}
				}
				
			}

			header('Content-Type: application/json');
			echo json_encode( $response );
		}
		wp_die();
	}
}

?>