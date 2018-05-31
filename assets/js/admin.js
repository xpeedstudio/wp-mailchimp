(function ($) {
    'use strict';

    $('.xs-mail-list').on('click', function(e){
        e.preventDefault();
        $.ajax({
            url : xs_admin_check_obj.ajaxurl,
            type : 'post',
            data : {
                action : 'xs_load_maillist',
                xs_admin_security : xs_admin_check_obj.ajax_nonce,
            },
            success : function( response ) {
                console.log(response);
                // if(response === 'success'){
                //     console.log(response);
                // }else{
                //     $(".xs_mailchimp_submit").val('Wait..');
                //     console.log(response);
                // }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log("Error: " + errorThrown);
            }
        });
    });
})(jQuery)