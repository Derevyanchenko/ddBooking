jQuery(document).ready(function($) {

    $(".ddbooking_add_to_wishlist").on("click", function(e) {
        e.preventDefault();

        var property_id = $(this).data('property-id');

        var ddbooking_add_to_wishlist = {
            success: function() {
                $( '#post-' + property_id + ' .ddbooking_add_to_wishlist').hide(0, function() {
                    $('#post-' + property_id + ' .succesfull_added').delay(700).show();
                })
            },
            error: function(error) {
                console.log(error);
            }
        }

        $('#ddbooking_add_to_wishlist_form_' + property_id).ajaxSubmit(ddbooking_add_to_wishlist);
    });

    $(".ddbooking_remove_property").on("click", function(e) {
        e.preventDefault();

        var property_id = $(this).data('property-id');

        $.ajax({
            url: ddbooking_bookingform_var.ajaxurl,
            type: 'POST',
            data: {
                dd_property_id: property_id,
                dd_user_id: $(this).data('user-id'),
                action: 'ddbooking_remove_to_wishlist',
            },
            success: function(data) {
                $('#post-' + property_id).hide();
            },
            error: function(error, data) {
                console.log(data);
            }
        })
    });


});
