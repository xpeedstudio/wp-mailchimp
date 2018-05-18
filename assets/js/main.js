(function ($) {
    'use strict';

    $('.xs-newsletter').on('submit', function(e){
        e.preventDefault();
        $(".xs_mailchimp_submit").val('Wait..');
        var email = $('.xs-newsletter-email', this).val();
        var name = $('.xs-newsletter-name', this).val();
        var xs_list_id = $('.xs_list_id', this).val();
        $.ajax({
            url : xs_check_obj.ajaxurl,
            type : 'post',
            data : {
                action : 'user_xs_subscribe_form',
                email : email,
                name : name,
                xs_list_id : xs_list_id,
                xs_security : xs_check_obj.ajax_nonce,
            },
            success : function( response ) {
                if(response === 'success'){
                    console.log(response);
                }else{
                    $(".xs_mailchimp_submit").val('Wait..');
                    console.log(response);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log("Error: " + errorThrown);
            }
        });
    });
})(jQuery)