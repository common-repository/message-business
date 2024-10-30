<fieldset style="background-color:#eeeeee;">
    <legend><?php echo esc_html( __('Form settings', 'message-business') ); ?></legend>

    <?php

    // after submitting the form we format the fields object array
    if( isset( $_POST['MESSAGE_BUSINESS_FORMBUILDEROPTIONS'] ) ) {

        if( isset( $_POST['MESSAGE_BUSINESS_INPUTSUBMITBUTTONTEXT'] ) ) {

            $inputSubmitButtonText = sanitize_text_field( $_POST['MESSAGE_BUSINESS_INPUTSUBMITBUTTONTEXT'] );

            // Update the form's submit button text value
            if( !empty( $inputSubmitButtonText ) ) {
                update_option( 'MESSAGE_BUSINESS_INPUTSUBMITBUTTONTEXT', esc_attr( $inputSubmitButtonText ) );
            }
        }

        if( isset( $_POST['MESSAGE_BUSINESS_HIDEFORMAFTERSUBMIT'] ) ) {
            update_option( 'MESSAGE_BUSINESS_HIDEFORMAFTERSUBMIT', true );
        } else {          
            update_option( 'MESSAGE_BUSINESS_HIDEFORMAFTERSUBMIT', false );            
        }

        // update option MESSAGE_BUSINESS_FORMBUILDEROPTIONS to save the changes
        $formBuilderOptionsPosted = json_decode( wp_unslash( $_POST['MESSAGE_BUSINESS_FORMBUILDEROPTIONS'] ) );
        $atLeastOneFieldRequired = false;
        $labelIsIncorrect = false;
        $isJsonIncorrect = false;
        foreach($formBuilderOptionsPosted as $formOptionPosted) {
            if( !array_key_exists( 'id', $formOptionPosted ) && !is_int( (int)$formOptionPosted->id ) ) {
                $isJsonIncorrect = true;
                break;
            }

            if( array_key_exists( 'name', $formOptionPosted ) ) {
                $formOptionPosted->name = sanitize_text_field( $formOptionPosted->name );
            } else {
                $isJsonIncorrect = true;
                break;
            }

            if( array_key_exists( 'shortcode', $formOptionPosted ) ) {
                $formOptionPosted->shortcode = sanitize_text_field( $formOptionPosted->shortcode );
            } else {
                $isJsonIncorrect = true;
                break;
            }

            if( array_key_exists( 'label', $formOptionPosted ) ) {
                if( empty($formOptionPosted->label) || is_null($formOptionPosted->label) ) {
                    $labelIsIncorrect = true;
                    break;
                } else {
                    $formOptionPosted->label = sanitize_text_field( $formOptionPosted->label );
                }
            } else {
                $isJsonIncorrect = true;
                break;
            }

            if( array_key_exists( 'optional', $formOptionPosted ) ) {
                if( is_bool( $formOptionPosted->optional ) ) {
                    if( $formOptionPosted->optional === false ) {
                        $atLeastOneFieldRequired = true;
                    }
                }
            } else {
                $isJsonIncorrect = true;
                break;
            }

            if( !array_key_exists( 'position', $formOptionPosted ) || !is_int( (int)$formOptionPosted->position ) ) {
                $isJsonIncorrect = true;
                break;
            }
        }

        if( $atLeastOneFieldRequired && !$labelIsIncorrect && !$isJsonIncorrect ) {

            update_option( 'MESSAGE_BUSINESS_FORMBUILDEROPTIONS', $formBuilderOptionsPosted );
            
            // generate an HTML code and save it as a wp option
            $json = get_option( 'MESSAGE_BUSINESS_FORMBUILDEROPTIONS' );

            // sort form fields by position attribute
            usort($json, 'message_business_sortFieldsByPosition');
            $html = message_business_generateHTMLFromJSON($json);
            update_option( 'MESSAGE_BUSINESS_WIDGETFORMHTML', $html );
            ?>
            <div class="notice notice-success is-dismissible"><p><strong><?php echo esc_html( __('Settings Saved', 'message-business') ); ?></strong></p></div>
            <?php
        } elseif( !$atLeastOneFieldRequired ) {
        ?>
            <div class="notice notice-error is-dismissible"><p><strong><?php echo esc_html( __('At least one field must be required', 'message-business') ); ?></strong></p></div>
        <?php 
        } elseif( $labelIsIncorrect ) {
        ?>
            <div class="notice notice-error is-dismissible"><p><strong><?php echo esc_html( __('Please fill the label(s)', 'message-business') ); ?></strong></p></div>
        <?php  
        }
        
    }

    ?>

    <form id="messagebusiness-form-settings-form" action="" method="POST">

        <?php settings_fields( 'message-business-plugin-form-settings-group' ); ?>
        <?php do_settings_sections( 'message-business-plugin-form-settings-group' ); ?>

        <hr>

        <!-- dropzone -->
        <div class="dropzone">
            <div id="dragndroptext">
                <span><?php echo esc_html( __( 'Choose an item and drag it here.', 'message-business' ) ); ?></span>
            </div>
            <ul id="dropzoneItems" class="items">

            <?php
            $formBuilderOptions = get_option('MESSAGE_BUSINESS_FORMBUILDEROPTIONS');

            // sort form fields by position attribute
            usort( $formBuilderOptions, 'message_business_sortFieldsByPosition' );

            if( !empty( $formBuilderOptions ) ) {
                // display all items dropped here
                foreach($formBuilderOptions as $formOption) {
                    ?>

                        <li id="<?php echo esc_attr( $formOption->id ); ?>" class="item-text drag"
                            item-id="<?php echo esc_attr( $formOption->id ); ?>" item-shortcode="<?php echo esc_attr( $formOption->shortcode ); ?>" item-name="<?php echo esc_attr( $formOption->name ); ?>" item-unicitykey="<?php echo $formOption->unicityKey; ?>">
                            <div class="fieldholder">
                                <div class="fieldbar">
                                    <span><?php echo esc_html( __( $formOption->label, 'message-business' ) ); ?></span>
                                    <div class="item-options">
                                        <i class="material-icons expand-item">expand_more</i>
                                        <?php if( !$formOption->unicityKey ) { ?>
                                            <i class="material-icons remove-item">close</i>
                                        <?php }?>
                                    </div>
                                </div>
                                <div class="field-content">
                                    <!-- input label -->
                                    <label for="input-label-<?php echo esc_attr( $formOption->id ); ?>"><?php echo esc_html( __('Label', 'message-business') ); ?>:</label>
                                    <input type="text" id="input-label-<?php echo esc_attr( $formOption->id ); ?>" name="input-label-<?php echo esc_attr( $formOption->id ); ?>" value="<?php echo esc_attr( __( $formOption->label, 'message-business' ) ); ?>">
                                    <!-- <br> -->
                                    <!-- input value -->
                                    <div class="block-value-<?php echo esc_attr( $formOption->id ); ?>" <?php if( (isset($formOption->hidden) && $formOption->hidden == false) || is_null($formOption->hidden) ) { echo esc_attr( "hidden" ); } ?>>
                                        <label for="input-value-<?php echo esc_attr( $formOption->id ); ?>"><?php echo esc_html( __('Value', 'message-business') ); ?>:</label>
                                        <input type="text" id="input-value-<?php echo esc_attr( $formOption->id ); ?>" name="input-value-<?php echo esc_attr( $formOption->id ); ?>" value="<?php echo esc_attr( __( $formOption->value, 'message-business' ) ); ?>">
                                    </div>

                                    <hr class="mb-line">

                                    <!-- optional option -->
                                    <div>
                                    <input type="checkbox" id="input-optional-<?php echo esc_attr( $formOption->id ); ?>" name="input-optional-<?php echo esc_attr( $formOption->id ); ?>" <?php if ($formOption->optional) echo esc_attr( 'checked' ); ?> >
                                    <label for="input-optional-<?php echo esc_attr( $formOption->id ); ?>"><?php echo esc_html( __( 'Optional?', 'message-business' ) ); ?></label>
                                    </div>

                                    <!-- hidden option -->
                                    <div>
                                    <input type="checkbox" id="input-hidden-<?php echo esc_attr( $formOption->id ); ?>" name="input-hidden-<?php echo esc_attr( $formOption->id ); ?>" <?php if ($formOption->hidden) echo esc_attr( 'checked' ); ?> >
                                    <label for="input-hidden-<?php echo esc_attr( $formOption->id ); ?>"><?php echo esc_html( __( 'Hidden?', 'message-business' ) ); ?></label>
                                    </div>
                                </div>
                            </div>
                        </li>

                    <?php
                }
            }
            ?>

            </ul>
            
        </div>

        <!-- list of available items draggable -->
        <div class="listItems">
            <ul id="availableItems" class="items">
                <?php

                $json_string = file_get_contents(MESSAGE_BUSINESS_PLUGIN_DIR. 'includes/fields.json');
                $fields = json_decode($json_string);
                
                if( !empty( $fields ) ) {
                    foreach( $fields->fields as $key => $field ) {
                        ?>
        
                        <li id="<?php echo esc_attr( $key ); ?>" class="item-text drag"
                            item-id="<?php echo esc_attr( $key );?>" item-shortcode="<?php echo esc_attr( $field->shortcode ); ?>" item-name="<?php echo esc_attr( $field->name ); ?>"  item-unicitykey="<?php echo esc_attr( $field->unicityKey ); ?>">
                            <div class="fieldholder">
                                <div class="fieldbar">
                                    <span><?php echo esc_html( __( $field->label, 'message-business' ) ); ?></span>
                                    <div class="item-options">
                                        <i class="material-icons drag-item">drag_handle</i>
                                    </div>
                                </div>
                                <div class="field-content">
                                    <!-- input label -->
                                    <label for="input-label-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( __('Label', 'message-business') ); ?>:</label>
                                    <input type="text" id="input-label-<?php echo esc_attr( $key ); ?>" name="input-label-<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( __( $field->label, 'message-business' ) ); ?>">
                                    <!-- <br> -->
                                    <!-- input value -->
                                    <div class="block-value-<?php echo esc_attr( $key ); ?>"  <?php if( (isset($field->hidden) && $field->hidden == false) || is_null($field->hidden) ) { echo esc_attr( "hidden" ); } ?>>
                                        <label for="input-value-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( __('Value', 'message-business') ); ?>:</label>
                                        <input type="text" id="input-value-<?php echo esc_attr( $key ); ?>" name="input-value-<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( __( $field->value, 'message-business' ) ); ?>">
                                    </div>
        
                                    <hr class="mb-line">
        
                                    <!-- optional option -->
                                    <input type="checkbox" id="input-optional-<?php echo esc_attr( $key ); ?>" name="input-optional-<?php echo esc_attr( $key ); ?>" <?php if ($field->optional) echo esc_attr( 'checked' ) ?> > <label for="input-optional-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( __( 'Optional?', 'message-business' ) );?></label><br>
        
                                    <!-- hidden option -->
                                    <input type="checkbox" id="input-hidden-<?php echo esc_attr( $key ); ?>" name="input-hidden-<?php echo esc_attr( $key ); ?>" <?php if ($field->hidden) echo esc_attr( 'checked' ) ?> > <label for="input-hidden-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( __( 'Hidden?', 'message-business' ) ); ?></label><br>
        
                                </div>
                            </div>
                        </li>
                    <?php
                    }
                }
                ?>
            </ul>
        </div>

        <?php
        // set dynamically the MESSAGE_BUSINESS_FORMBUILDEROPTIONS value
        ?>
        <input type="hidden" id="MESSAGE_BUSINESS_FORMBUILDEROPTIONS" name="MESSAGE_BUSINESS_FORMBUILDEROPTIONS" value='<?php echo json_encode( get_option('MESSAGE_BUSINESS_FORMBUILDEROPTIONS') ); ?>'>


        <div class="right-block">
            <!-- checkbox to show or hide the form after submiting -->
            <!-- <div>
                <?php
                    $hideFormIsChecked = get_option( 'MESSAGE_BUSINESS_HIDEFORMAFTERSUBMIT' );
                ?>
                <input type="checkbox" name="MESSAGE_BUSINESS_HIDEFORMAFTERSUBMIT" id="checkbox-hide-form" <?php if( $hideFormIsChecked ) echo esc_attr( 'checked' ); ?>>
                <label for="checkbox-hide-form"><?php echo esc_html( __( 'Do you want to hide the form after a successful submit?', 'message-business' ) ); ?></label>
            </div> -->

            <!-- input for submit form button text value -->
            <label for="input-label-submit"><strong><?php echo esc_html( __( 'Submit button text', 'message-business' ) ); ?> :</strong></label>
            <?php
                $inputSubmitButtonText = get_option( 'MESSAGE_BUSINESS_INPUTSUBMITBUTTONTEXT' );
            ?>
            <input type="text" name="MESSAGE_BUSINESS_INPUTSUBMITBUTTONTEXT" id="input-label-submit" value="<?php echo __( wp_unslash( esc_attr($inputSubmitButtonText) ), 'message-business' ); ?>">
            
            <!-- submit button to save the changes -->
            <div>
                <?php
                // show error/update messages
                settings_errors( 'message_business_messages' );
                submit_button(__( 'Save changes', 'message-business' ));
                ?>
            </div>
        </div>

    </form>
</fieldset>