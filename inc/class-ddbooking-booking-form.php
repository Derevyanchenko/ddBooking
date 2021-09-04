<?php

class ddBooking_booking_form {

    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue']);
        add_action('init', [$this, 'ddbooking_booking_shortcode']);

        add_action('wp_ajax_booking_form', [$this, 'booking_form']);
        add_action('wp_ajax_nopriv_booking_form', [$this, 'booking_form']);
    }

    public function enqueue() {
        wp_enqueue_script( 
            'ddbooking_bookingform', 
            plugins_url('ddbooking/assets/js/frontend/bookingform.js'),
            array('jquery'),
            '1.0',
            true
        );

        wp_localize_script('ddbooking_bookingform', 'ddbooking_bookingform_var', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('_wpnonce'),
            'title' => esc_html__('Booking Form', 'ddbooking'),
        ));
    }

    public function ddbooking_booking_shortcode() {
        add_shortcode( 'ddbooking_booking', [$this, 'booking_form_html']);
    }

    public function booking_form_html($atts, $content) {
        echo '<div id="ddbooking_result"></div>';
        echo '<form method="POST">
            <p>
                <input type="text" name="name" id="ddbooking_name" />
            </p>
            <p>
                <input type="text" name="email"  id="ddbooking_email" />
            </p>
            <p>
                <input type="text" name="phone"  id="ddbooking_phone" />
            </p>
            <p>
                <input type="submit" name="submit" id="ddbooking_booling_submit" />
            </p>
        </form>';
    }

     function booking_form() {
        
        check_ajax_referer('_wpnonce', 'nonce');

        if ( ! empty($_POST) ) {

            if ( isset( $_POST['name'] ) ) {
                $name = sanitize_text_field($_POST['name']);
            }
            if ( isset( $_POST['email'] ) ) {
                $email = sanitize_text_field($_POST['email']);
            }
            if ( isset( $_POST['phone'] ) ) {
                $phone = sanitize_text_field($_POST['phone']);
            }

            // email Admin
            $data_message = '';
            $data_message .= 'Name: ' . esc_html($name) . '<br>';
            $data_message .= 'Email: ' . esc_html($email) . '<br>';
            $data_message .= 'Phone: ' . esc_html($phone) . '<br>';

            $result_admin = wp_mail( get_option('admin_email'), 'New Reservation', $data_message );

            // email Client
            $message = 'Thank you for you reservation!';
            $result_admin = wp_mail($email, esc_html__('Booking', 'ddbooking') , $message);

        }
        
        wp_die();
    }

}

$ddBooking_booking_form = new ddBooking_booking_form();