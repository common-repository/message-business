<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

include_once MESSAGE_BUSINESS_PLUGIN_DIR . 'woocommerce/message-business-woocommerce.php';
global $woocommerce;
?>

<div class="wrap">

    <!-- Title -->
    <img src="<?php echo esc_url( plugin_dir_url( dirname( __FILE__ ) ) . 'images/logo.png' ); ?>" alt="Message Business" width="311">

    <?php
        $active_tab = isset($_GET['tab']) ? sanitize_text_field( $_GET['tab'] ) : 'settings';
    ?>

    <!-- Tabs -->
    <h2 class="nav-tab-wrapper" style="margin-bottom: 10px;">
        <a href="?page=messagebusiness&tab=settings" class="nav-tab <?php echo esc_attr( $active_tab == 'settings' ? 'nav-tab-active' : '' ); ?>"><?php echo esc_html( __( 'Settings', 'message-business' ) ); ?></a>
        <a href="?page=messagebusiness&tab=form-settings" class="nav-tab <?php echo esc_attr( $active_tab == 'form-settings' ? 'nav-tab-active' : '' ); ?>"><?php echo esc_html( __( 'Form settings', 'message-business' ) ); ?></a>
        <a href="?page=messagebusiness&tab=woocommerce" class="nav-tab <?php echo esc_attr($active_tab == 'woocommerce' ? 'nav-tab-active' : '' ); ?>"><?php echo esc_html( __( 'WooCommerce settings', 'message-business' ) ); ?></a>
    </h2>

    <!-- Shows error/success messages -->
    <?php if ( $active_tab == 'settings' ) {
            if( isset( $_POST['MESSAGE_BUSINESS_ACCOUNTID'] ) && isset( $_POST['MESSAGE_BUSINESS_APIKEY'] )) {
                // check if the accountId and apiKey are correct
                $accountId = intval( $_POST['MESSAGE_BUSINESS_ACCOUNTID'] );
                if( !$accountId ) {
                    $accountId = '';
                }
                $apiKey = sanitize_key( $_POST['MESSAGE_BUSINESS_APIKEY'] );

                if( empty( $accountId ) ) {
                    
                    add_settings_error( 'message_business_messages', 'message_business_message_account_id', __( 'Please specify an account number.', 'message-business' ), 'error' );
                } elseif( empty( $apiKey ) ) {
                    
                    add_settings_error( 'message_business_messages', 'message_business_message_api_key', __( 'Please specify an API key.', 'message-business' ), 'error' );
                } else {
                    
                    $moduleApi = new Swagger\Client\Api\Message_Business_ModulesApi();
                    $this->message_business_initApi($moduleApi->getApiClient()->getConfig(), $accountId, $apiKey);
                    try {
                        $moduleTest = $moduleApi->modulesGetConnectionTest();
                        // save the accountId and apiKey and show a success message
                        update_option( 'MESSAGE_BUSINESS_ACCOUNTID', $accountId );
                        update_option( 'MESSAGE_BUSINESS_APIKEY', $apiKey );
                        add_settings_error( 'message_business_messages', 'message_business_message', __( 'Settings Saved', 'message-business' ), 'updated' );
                        
                        // save Module details to M.B.
                        $modulesParameters = new Swagger\Client\Model\Message_Business_ModulesParameters();
                        $mb_url = get_bloginfo('url');
                        $params = array(
                            "moduleUrl" => $mb_url
                        );
                        $legacy_params = array();
                        $modulesParameters->setName('woocommerce');
                        $modulesParameters->setVersion($woocommerce->version);
                        $modulesParameters->setModuleVersion(MESSAGE_BUSINESS_VERSION);
                        $modulesParameters->setParameters($params);
                        $modulesParameters->setLegacyParamters($legacy_params);
                        $moduleApi->modulesPostModuleParams($modulesParameters);

                        // if WooCommerce is installed and activated, we create all the WooCoommerce fields needed for the plugin
                        message_business_create_woocommerce_custom_fields();
                    } catch(Exception $e) {
                        add_settings_error( 'message_business_messages', 'message_business_message_api_key', __( 'Please verify your Account number and Api key.', 'message-business' ), 'error' );
                    }
                }
            }
    ?>

    <!-- Configuration steps -->
    <div style="background-color: #ffffff; width=75%; padding: 10px;">
        <fieldset>
            <legend><?php echo esc_html( __( 'Configuration steps', 'message-business' ) ); ?></legend>
            <?php
                $subscribeMbURL = 'https://services.message-business.com/v3/signup/default.aspx';
                $formSettingsURL =  get_admin_url() . '/plugins.php?page=messagebusiness&tab=form-settings';
                $widgetsPageURL = get_admin_url() . '/widgets.php';
            ?>
            <ul class="custom-counter">
                <li><?php echo sprintf( __( 'If it has not been done, create a new Message Business account <a href="%s" target="_blank">here</a>.', 'message-business' ), esc_url( $subscribeMbURL ) ); ?></li>
                <li><?php echo esc_html( __( 'Generate a new API key on your Message Business account (More > Settings > Connectors, API) to get access to the Message Business API and fill the form below with your account number and API key.', 'message-business' ) ); ?></li>
                <li><?php echo sprintf( __( '<a href="%s">Edit your subscription form</a> by selecting the appropriate fields.', 'message-business' ), esc_url( $formSettingsURL ) ); ?></li>
                <li><?php echo sprintf( __( '<a href="%s">Publish the subscription form</a> on your web site structure.', 'message-business' ), esc_url( $widgetsPageURL ) ); ?></li>
                <li><?php echo esc_html( __( 'Configure your emailing SMTP with Message Business SMTP configuration, see below.', 'message-business' ) ); ?></li>
            </ul>
        </fieldset>
    </div>

    <br>

    <!-- API Configuration -->
    <div style="background-color: #ffffff; width=75%; padding: 10px;">
        <fieldset>
            <legend><?php echo esc_html( __('Credentials of your account on Message Business', 'message-business' ) ); ?></legend>
            <form action="" method="post">
                
                <?php settings_fields( 'message-business-plugin-settings-group' ); ?>
                <?php do_settings_sections( 'message-business-plugin-settings-group' ); ?>

                <table>
                    <tr>
                        <td valign="top"><label style="text-align:right; white-space: nowrap;" ><?php echo esc_html( __( 'Account number', 'message-business' ) ); ?></label></td>
                        <td valign="top"><input style="width:250px;" type="text" name="MESSAGE_BUSINESS_ACCOUNTID" value="<?php echo esc_attr( get_option('MESSAGE_BUSINESS_ACCOUNTID') ); ?>" required></td>
                    </tr>
                    <tr>
                        <td valign="top"><label style="text-align:right; white-space: nowrap;" ><?php echo esc_html( __( 'Account API key', 'message-business' ) ); ?></label></td>
                        <td valign="top"><input style="width:250px;" type="text" name="MESSAGE_BUSINESS_APIKEY" value="<?php echo esc_attr( get_option('MESSAGE_BUSINESS_APIKEY') ); ?>" required></td>
                    </tr>
                </table>
                <?php
                    // show error/update messages
                    settings_errors( 'message_business_messages' );
                    submit_button(__( 'Save settings', 'message-business' ));
                ?>
            </form>
        </fieldset>
    </div> 

    <br>
    
    <!-- SMTP Configuration -->
    <?php
        $smtpConfigURL = 'https://www.message-business.com/campus/email-transactionnel/wordpress-woocommerce-email-transactionnel-smtp/';
    ?>
    <div style="background-color: #ffffff; width=75%; padding: 10px;">
        <fieldset>
            <legend><?php echo esc_html( __( 'SMTP Configuration', 'message-business' ) ); ?></legend>
            <p><?php echo sprintf( __( 'Please check <a href="%s" target="_blank">here</a> how to configure your SMTP settings.', 'message-business' ), esc_url( $smtpConfigURL ) ); ?></p>
        </fieldset>
    </div>

    <?php 
    } elseif( $active_tab == 'form-settings' ) {

        include_once( sprintf( "%s/message-business-admin-form-settings.php", dirname( __FILE__ ) ) );
    } elseif( $active_tab == 'woocommerce' ) {

        if( message_business_is_woocommerce_activated() ) {

            include_once( MESSAGE_BUSINESS_PLUGIN_DIR . 'woocommerce/partials/message-business-woocommerce.php' );
        } else {

            include_once( MESSAGE_BUSINESS_PLUGIN_DIR . 'woocommerce/partials/message-business-woocommerce-not-activated.php' );
        }
    }
    ?>

    <br>

    <!-- Footer -->
    <div style="background-color: #ffffff; width=75%; padding: 10px;">
        <fieldset>
            <legend><?php echo esc_html( __('At your service', 'message-business') ); ?></legend>
            <form action="" method="POST" style="float:left;" >
                <table>
                    <tr>
                        <td valign="top"><label style="text-align:right;" ></label></td>
                        <td valign="top">
                        <?php
                            $contactUSURL = 'https://www.message-business.com/campus/';
                            $registerMbURL = 'https://www.message-business.com/tarifs-emailing-services/#offre-decouverte';
                            echo sprintf( __( 'Please <a href="%s">contact us</a> if you need additionnal information on this module.', 'message-business' ), esc_url( $contactUSURL ) );
                        ?>
                        <br><br>
                        <?php
                            echo sprintf( __( 'If you have not registered yet to Message Business, <a href="%s">create an account now ... It\'s free and without obligation !</a>', 'message-business' ), esc_url( $registerMbURL ) );
                        ?>
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>
    </div>

</div>