(function( $ ) {
    'use strict';
    
/**
    * All of the JavaScript code for the Message Business Newsletter widget
    * should reside in this file.
*/

$( document ).ready(function() {

    // hideForm();

    function hideForm() {
        if( typeof sessionStorage != 'undefined' ) {
            if('hideform' in sessionStorage) {
                $('form[name="message-business-newsletter-form"] > :not(.response)').hide();
            } else {
                // delete session hideform
                sessionStorage.removeItem('hideform');
                $('form[name="message-business-newsletter-form"]').show();
            }
        }
    }

    var _this = this;

    this.config = {
        "phoneFieldsDefaultCountry":"fr",
        "phoneFieldsPreferredCountries":["fr"],
        "phoneFieldsOnlyCountries":[],
    };

    $('input[type="tel"]').intlTelInput({
        initialCountry: 'auto',
        geoIpLookup: function(callback) {
            $.get('https://ipinfo.io', function() {}, "jsonp").always(function(resp) {
                var countryCode = (resp && resp.country) ? resp.country : "";
                callback(countryCode);
            });
        },
        preferredCountries: _this.config.phoneFieldsPreferredCountries,
        onlyCountries: _this.config.phoneFieldsOnlyCountries,
        utilsScript: "https://public.message-business.com/Javascript/form/intlTelInput.utils.min.js"
    });

    $.validator.methods.tel = function( value, element ) {
        var self = $(element);

        if( this.optional( element ) ) {
            return this.optional( element );
        }
        
        if(  $.trim( value ) ) {
            if( self.intlTelInput("isValidNumber") ) {
                return true;
            } else {
                return false;
            }
        }
    };

    $('form[name="message-business-newsletter-form"]').each(function() {
        $(this).validate({
            errorPlacement: function(error, element) {
                if ( element.attr("type") == "tel" ) {
                    var intlTelInputDiv = element.parents('.intl-tel-input');
                    error.insertAfter(intlTelInputDiv);
                } else {
                    error.insertAfter(element);
                }
            }
        });
    });
    

    $( document ).on( 'submit', 'form[name="message-business-newsletter-form"]', function(e) {
        
        e.preventDefault();
        var errorMessage = '';
        var hasError = false;
        var $data = [];
        var $nonce = message_business_ajax_object.message_business_nonce;
        var $url = message_business_ajax_object.message_business_ajax_url;
        var $form = $(this);

        $form.find( 'input' ).each(function() {
            var inputId = $(this).attr('id');
            var inputValue = $(this).val();
            var inputOptional = !$(this).prop('required');
            $data.push({id: inputId, fieldValue: inputValue, optional: inputOptional});
        });
            
        // Ajax call to subscribe a new contact
        $.ajax({
            type: "POST",
            url: $url,
            data: {
                action: 'message_business_post_form',
                data: $data,
                nonce: $nonce
            },
            beforeSend: function(jqXHR) {
                $form.find('.message-business-loading').show();
            },
            success: function(data) {
                $form.find('.message-business-loading').hide();
                if( data['status'] === 'error' ) {

                    // here show red color on the fields ?
                    if( data['message'] ) {

                        // here append response message to the form
                        $form.find('.response').addClass('error').html(data['message']).css('display', 'block');
                    }
                } else if( data['status'] === 'success' ) {

                    if( data['message'] ) {

                        // here append response message to the form
                        $form.find('.response').addClass('success').html(data['message']).show();
                        $form[0].reset();
                        if( data['hideform'] === true ) {
                            // sessionStorage.setItem('hideform', true);
                            // hideForm();
                        } else {
                            if( sessionStorage.getItem('hideform') ) {
                                sessionStorage.removeItem('hideform');
                            }
                        }
                    }
                }
            },
            error: function(error) {
                $form.find('.message-business-loading').hide();
                $form.find('.response').addClass('error').html('Error server!').show();
            },
            dataType: 'json'
        });
    });
});


})( jQuery );