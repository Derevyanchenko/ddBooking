<?php

class ddBooking_Wishlist {

    public function __construct() {
        add_action( 'wp_ajax_ddbooking_add_to_wishlist', [$this, 'ddbooking_add_to_wishlist'] );
        add_action( 'wp_ajax_nopriv_ddbooking_add_to_wishlist', [$this, 'ddbooking_add_to_wishlist'] );

        add_action( 'wp_ajax_ddbooking_remove_to_wishlist', [$this, 'ddbooking_remove_to_wishlist'] );
        add_action( 'wp_ajax_nopriv_ddbooking_remove_to_wishlist', [$this, 'ddbooking_remove_to_wishlist'] );
    }

    public function ddbooking_add_to_wishlist()
    {
        if ( isset($_POST['dd_property_id']) && isset($_POST['dd_user_id']) ) {
            $property_id = intval($_POST['dd_property_id']);
            $user_id = intval($_POST['dd_user_id']);

            if ( $property_id > 0 && $user_id > 0 ) {
                if ( add_user_meta($user_id, 'ddbooking_wishlist_properties', $property_id) ) {
                    echo 'Succesful added to wishlist';
                } else {
                    echo 'Failed';
                }
            }
        }
    }

    public function ddbooking_remove_to_wishlist()
    {
        if ( isset($_POST['dd_property_id']) && isset($_POST['dd_user_id']) ) {

            $property_id = intval($_POST['dd_property_id']);
            $user_id = intval($_POST['dd_user_id']);

            if ( $property_id > 0 && $user_id > 0 ) {
                if ( delete_user_meta($user_id, 'ddbooking_wishlist_properties', $property_id) ) {
                    echo 'Succesful added to wishlist';
                } else {
                    echo 'Failed';
                }
            }
        } else{ 
        
        }
    }
    
    public static function ddbooking_check_in_wishlist($user_id, $property_id)
    {
        global $wpdb;
        $result = $wpdb->get_results("SELECT * FROM $wpdb->usermeta WHERE meta_key='ddbooking_wishlist_properties' AND meta_value=". $property_id ." AND user_id=". $user_id);

        if ( isset( $result[0]->meta_value) && $result[0]->meta_value == $property_id ) {
            return true;
        } else {
            return false;
        }
    }

}

$ddBooking_Wishlist = new ddBooking_Wishlist();
