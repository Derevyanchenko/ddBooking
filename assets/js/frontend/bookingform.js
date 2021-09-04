jQuery(document).ready(function($) {

    console.log('booking_form loaded');

    $("#ddbooking_booling_submit").on("click", function(e) {
        e.preventDefault();

        $.ajax({
            url: ddbooking_bookingform_var.ajaxurl,
            type: 'POST',
            data: {
                action: 'booking_form',
                nonce: ddbooking_bookingform_var.nonce,
                name: $('#ddbooking_name').val(),
                email: $('#ddbooking_email').val(),
                phone: $('#ddbooking_phone').val(),
            },
            success: function(data) {
                $('#ddbooking_result').html(data);
            }, 
            error: function(error) {
                console.log(error);
            }
        })
    });

});