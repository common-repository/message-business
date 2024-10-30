<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.message-business.com/
 * @since      1.0.0
 *
 * @package    Message_Business
 * @subpackage Message_Business/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Message_Business
 * @subpackage Message_Business/includes
 * @author     Message Business
 */
class Message_Business_Activator {

	/**
	 *
	 * create the form builder option with email field as field by default
     * and generate an html code for the form
     * then we save both of them as options
	 *
	 * @since    1.0.0
	 */
	public static function message_business_activate() {

		$json = file_get_contents( MESSAGE_BUSINESS_PLUGIN_DIR .'includes/fields.json');
		$fields = json_decode($json);
		$formBuilderOptions = array();
		foreach( $fields->fields as $field ) {
			if( isset( $field->unicityKey ) ) {
				if( $field->unicityKey === true ) {
					$defaultField = new Message_Business_Field();
					$defaultField->id = (string)$field->id;
					$defaultField->shortcode = $field->shortcode;
					$defaultField->name = $field->name;
					$defaultField->label = $field->label;
					$defaultField->optional = $field->optional;
					$defaultField->position = 0;
					$defaultField->unicityKey = true;
					array_push($formBuilderOptions, $defaultField );
				}
			}
		}

		// create/update MESSAGE_BUSINESS_FORMBUILDEROPTIONS and MESSAGE_BUSINESS_INPUTSUBMITBUTTONTEXT options for the form builder
		update_option( 'MESSAGE_BUSINESS_FORMBUILDEROPTIONS', $formBuilderOptions );
		update_option( 'MESSAGE_BUSINESS_INPUTSUBMITBUTTONTEXT', 'Subscribe' );

		// generate the HTML code for the default form
		$json = get_option( 'MESSAGE_BUSINESS_FORMBUILDEROPTIONS' );
		$html = message_business_generateHTMLFromJSON($json);
		update_option( 'MESSAGE_BUSINESS_WIDGETFORMHTML', $html );
	}

}