<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!-- WooCommerce API Configuration -->

<div class="message-business-import-customers-notice"></div>
<div style="background-color: #ffffff; width=75%; padding: 10px;">
    <fieldset>
        <legend><?php echo esc_html( __('Import all your contacts to your Message Business account', 'message-business' ) ); ?></legend>
        <form name="message-business-woocommerce-import-contacts-form" action="" method="post">

            <?php settings_fields( 'message-business-woocommerce-settings-group' ); ?>
            <?php do_settings_sections( 'message-business-woocommerce-settings-group' ); ?>

            <table style="width:75%;float:left">
                <tr>
                    <td valign="top"><label for="MESSAGE_BUSINESS_SHOP_URL" style="text-align:right; white-space: nowrap;" ><?php echo esc_html( __( 'Shop URL', 'message-business' ) ); ?></label></td>
                    <?php $shop_url = ( !get_option('MESSAGE_BUSINESS_SHOP_URL') ) ? get_bloginfo('url') : get_option('MESSAGE_BUSINESS_SHOP_URL') ; ?>
                    <td valign="top"><input style="width:300px;" type="text" id="MESSAGE_BUSINESS_SHOP_URL" name="MESSAGE_BUSINESS_SHOP_URL" value="<?php echo esc_url( $shop_url ); ?>" required></td>
                </tr>
                <tr>
                    <td valign="top"><label for="MESSAGE_BUSINESS_CONSUMER_KEY" style="text-align:right; white-space: nowrap;" ><?php echo esc_html( __( 'Consumer key', 'message-business' ) ); ?></label></td>
                    <td valign="top">
                        <input style="width:300px;" type="text" id="MESSAGE_BUSINESS_CONSUMER_KEY" name="MESSAGE_BUSINESS_CONSUMER_KEY" value="<?php echo esc_attr( get_option('MESSAGE_BUSINESS_CONSUMER_KEY') ); ?>" required>
                        <div class="help-tip">
                            <p><?php echo __( 'To obtain a consumer key, go to your WooCommerce extension > Settings > API > keys and apps > Add key > Name your description > Change the permissions to "Read Write" > Generate API key.', 'message-business' ); ?></p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td valign="top"><label for="MESSAGE_BUSINESS_CONSUMER_SECRET" style="text-align:right; white-space: nowrap;" ><?php echo esc_html( __( 'Consumer secret', 'message-business' ) ); ?></label></td>
                    <td valign="top">
                        <input style="width:300px;" type="text" id="MESSAGE_BUSINESS_CONSUMER_SECRET" name="MESSAGE_BUSINESS_CONSUMER_SECRET" value="<?php echo esc_attr( get_option('MESSAGE_BUSINESS_CONSUMER_SECRET') ); ?>" required>
                        <div class="help-tip">
                            <p><?php echo __( 'To obtain a consumer secret, go to your WooCommerce extension > Settings > API > keys and apps > Add key > Name your description > Change the permissions to "Read Write" > Generate API key.', 'message-business' ); ?></p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p>
                            <?php echo __( 'Last import', 'message-business' ) . ' : <span id="last-import-date" class="message-business-import-date">';
                            if( get_option( 'MESSAGE_BUSINESS_END_DATE_LAST_IMPORT_CUSTOMERS' ) ) {
                                $last_import_date = date( 'd/m/Y H:i:s', message_business_gmt_to_local_timestamp( strtotime( get_option( 'MESSAGE_BUSINESS_END_DATE_LAST_IMPORT_CUSTOMERS' ) ) ) );
                            } else {
                                $last_import_date = '-';
                            }
                            echo $last_import_date;
                            ?>
                            </span>
                        </p>
                    </td>
                    <td>
                        <p>
                            <?php echo __( 'Last import duration', 'message-business' ) . ' : '; ?>
                            <span id="last-import-duration" class="message-business-import-date">
                            <?php
                            $last_import_end_date_timestamp = strtotime( get_option( 'MESSAGE_BUSINESS_END_DATE_LAST_IMPORT_CUSTOMERS' ) );
                            $last_import_start_date_timestamp = strtotime( get_option( 'MESSAGE_BUSINESS_START_DATE_LAST_IMPORT_CUSTOMERS' ) );
                            if( $last_import_end_date_timestamp > $last_import_start_date_timestamp ) {
                                $last_import_duration = date("H:i:s", ( $last_import_end_date_timestamp - $last_import_start_date_timestamp ) );
                                echo $last_import_duration;
                            } elseif( $last_import_end_date_timestamp < $last_import_start_date_timestamp ) {
                                echo __( 'Import in progress', 'message-business' ) . '...';
                            } else {
                                echo '00:00:00';
                            }
                            ?>
                            </span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td><p><?php echo __( 'Next import', 'message-business' ) . ' : <span id="next-import-date" class="message-business-import-date">';
                    if ( wp_next_scheduled( 'message_business_import_contacts' ) ) {
                        $next_import_date = date( 'd/m/Y H:i:s', message_business_gmt_to_local_timestamp( wp_next_scheduled( 'message_business_import_contacts' ) ) );
                    } else {
                        $next_import_date = __( 'Not scheduled.', 'message-business' );
                    }
                    echo $next_import_date; ?></span></p></td>
                </tr>
                <tr>
                    <td valign="top">
                        <label for="MESSAGE_BUSINESS_IMPORT_CUSTOMERS_FREQUENCY" style="text-align:right; white-space: nowrap;" >
                            <?php echo esc_html( __( 'Import my contacts every', 'message-business' ) . ' :' ); ?>
                        </label>
                    </td>
                    <td valign="top">
                        <?php
                        if( get_option( 'MESSAGE_BUSINESS_IMPORT_CUSTOMERS_FREQUENCY' ) ) {
                            $mb_selected_frequency = get_option( 'MESSAGE_BUSINESS_IMPORT_CUSTOMERS_FREQUENCY' );
                        } else {
                            $mb_selected_frequency = 0;
                        }
                        $frequencies = array( 0, 1, 3, 6, 9, 12, 24 );
                        ?>
                        <select
                            name="MESSAGE_BUSINESS_IMPORT_CUSTOMERS_FREQUENCY"
                            id="MESSAGE_BUSINESS_IMPORT_CUSTOMERS_FREQUENCY"
                            required>
                            <?php
                                foreach( $frequencies as $frequency ) {
                                    $selected_frequency = false;
                                    if( $frequency == $mb_selected_frequency ) {
                                        $selected_frequency = true;
                                    }
                                    echo '<option value="' . $frequency . '"';
                                        if($selected_frequency) {
                                            echo ' selected="selected"';
                                        }
                                    echo '>';
                                    if( $frequency == 0 ) {
                                        echo __( 'None' );
                                    } elseif( $frequency == 1 ) {
                                        echo $frequency . ' ' . __( 'hour', 'message-business' );
                                    } else {
                                        echo $frequency . ' ' . __( 'hours', 'message-business' );
                                    }
                                    echo '</option>';
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="message-business-import-in-progress">
                            <span class="message-business-loading"></span>
                            <div class="message-business-import-in-progress-label"><?php echo __('Import in progress', 'message-business'); ?>...</div>
                        </div>
                        <?php
                        // show error/update messages
                        settings_errors( 'message_business_messages' );
                        submit_button(__( 'Import my contacts', 'message-business' ));
                        ?>
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>