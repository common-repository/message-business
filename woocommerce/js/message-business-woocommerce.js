(function( $ ) {
    'use strict';
    
/**
    * All of the JavaScript code for the Message Business WooCommerce area
    * should reside in this file.
*/

$( document ).ready(function() {

    var SecondsTohhmmss = function(totalSeconds) {
        var hours   = Math.floor(totalSeconds / 3600);
        var minutes = Math.floor((totalSeconds - (hours * 3600)) / 60);
        var seconds = totalSeconds - (hours * 3600) - (minutes * 60);
      
        // round seconds
        seconds = Math.round(seconds * 100) / 100
      
        var result = (hours < 10 ? "0" + hours : hours);
            result += ":" + (minutes < 10 ? "0" + minutes : minutes);
            result += ":" + (seconds  < 10 ? "0" + seconds : seconds);
        return result;
    }

    $( document ).on( 'submit', 'form[name="message-business-woocommerce-import-contacts-form"]', function(e) {
        e.preventDefault();
        var $form = $(this);
        
        // Ajax call to import all contacts to the Message Business account
        var $nonce = message_business_woocommerce_ajax_object.message_business_woocommerce_nonce;
        var $url = message_business_woocommerce_ajax_object.message_business_woocommerce_ajax_url;
        var $shop_url = $('input[name="MESSAGE_BUSINESS_SHOP_URL"]').val();
        var $consumer_key = $('input[name="MESSAGE_BUSINESS_CONSUMER_KEY"]').val();
        var $consumer_secret = $('input[name="MESSAGE_BUSINESS_CONSUMER_SECRET"]').val();
        var $frequency = $('#MESSAGE_BUSINESS_IMPORT_CUSTOMERS_FREQUENCY').val();

        $.ajax({
            type: "POST",
            url: $url,
            data: {
                action: 'message_business_woocommerce_import_contacts',
                nonce: $nonce,
                MESSAGE_BUSINESS_SHOP_URL: $shop_url,
                MESSAGE_BUSINESS_CONSUMER_KEY: $consumer_key,
                MESSAGE_BUSINESS_CONSUMER_SECRET: $consumer_secret,
                MESSAGE_BUSINESS_IMPORT_CUSTOMERS_FREQUENCY: $frequency
            },
            dataType: 'json',
            beforeSend: function(jqXHR) {
                $form.find('.message-business-import-in-progress').show();
            },
            success: function(data) {
                $form.find('.message-business-import-in-progress').hide();
                if( data['status'] === 'success' ) {
                    $('.message-business-import-customers-notice')
                    .empty()
                    .removeClass( 'updated error notice' )
                    .addClass( 'updated notice' )
                    .show();
                    $( '#last-import-date' ).html( data['last-import-date'] );
                    $( '#next-import-date' ).html( data['next-import-date'] );
                    $( '#last-import-duration' ).html( data['last-import-duration'] );
                } else if( data['status'] === 'error' ) {
                    $('.message-business-import-customers-notice')
                    .empty()
                    .removeClass( 'updated error notice' )
                    .addClass( 'error notice' )
                    .show();
                }
                $( '.message-business-import-customers-notice' ).html( '<p>' + data['message'] + '</p>' );
            },
            error: function(error) {
                $form.find('.message-business-import-in-progress').hide();
                $('.message-business-import-customers-notice')
                .empty()
                .removeClass( 'updated error notice' )
                .addClass( 'error notice' )
                .show();
                $( '.message-business-import-customers-notice' ).html( '<p>' + data['message'] + '</p>' );
            }
        });
    });
});


})( jQuery );