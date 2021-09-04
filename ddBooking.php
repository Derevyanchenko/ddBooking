<?php 
/*
Plugin Name: ddBooking
Plugin URI: #
Description: Booking plugin for practice plugin development
Version: 1.0
Author: Danil Derevyanchenko
Author URI: #
Licence: GPLv2 or later
Text Domain: ddbooking
Domain Path: /lang
*/ 

if( ! defined('ABSPATH') ) {
    die;
}

define('DDBOOKING_PATH', plugin_dir_path(__FILE__));

if ( ! class_exists('ddBookingCpt') ) {
    require DDBOOKING_PATH . 'inc/class-ddbooking-cpt.php';
}

if ( ! class_exists('Gamajo_Template_Loader') ) {
    require DDBOOKING_PATH . 'inc/class-gamajo-template-loader.php';
}

require DDBOOKING_PATH . 'inc/class-ddbooking-template-loader.php';
require DDBOOKING_PATH . 'inc/class-ddbooking-shortcodes.php';
require DDBOOKING_PATH . 'inc/class-ddbooking-filter-widget.php';
require DDBOOKING_PATH . 'inc/class-ddbooking-booking-form.php';
require DDBOOKING_PATH . 'inc/class-ddbooking-elementor.php';
require DDBOOKING_PATH . 'inc/class-ddbooking-wpbakery.php';
require DDBOOKING_PATH . 'inc/class-ddbooking-wishlist.php';

class ddBooking {

    // register all methods
    function register() {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend']);
        add_action('widgets_init', [$this, 'register_widget']);
        add_action('admin_menu', [$this, 'add_menu_item']);

        add_filter('plugin_action_links_'.plugin_basename(__FILE__), [$this, 'add_plugin_settings_link']  );

        add_action('admin_init', [$this, 'settings_init']);
    }

    public function settings_init() {

        register_setting('ddbooking_settings', 'ddbooking_settings_options');

        add_settings_section(
            'ddbooking_settings_section', 
            esc_html__('Settings', 'ddbooking'), 
            [$this, 'ddbooking_settings_section_html'],
            'ddbooking_settings'
        );

        add_settings_field(
            'filter_title', 
            esc_html__('Title for Filter', 'ddbooking'),
            [$this, 'filter_title_html'],
            'ddbooking_settings',
            'ddbooking_settings_section'
        );

        add_settings_field(
            'archive_title', 
            esc_html__('Title for Archive Page', 'ddbooking'),
            [$this, 'archive_title_html'],
            'ddbooking_settings',
            'ddbooking_settings_section'
        );

    }

    public function filter_title_html() {
        $options = get_option('ddbooking_settings_options');
        ?>

        <input type="text" name="ddbooking_settings_options[filter_title]" value="<?php echo isset( $options['filter_title'] ) ? $options['filter_title'] : ''; ?>">
        <?php
    }

    public function archive_title_html() {
        $options = get_option('ddbooking_settings_options');
        ?>

        <input type="text" name="ddbooking_settings_options[archive_title]" value="<?php echo isset( $options['archive_title'] ) ? $options['archive_title'] : ''; ?>">
        <?php
    }

    public function ddbooking_settings_section_html() {
        esc_html_e('Settings for ddBooking Plugin', 'ddbooking');
    }

    // register widget method
    public function register_widget() {
        register_widget('ddbooking_filter_widget');
    }

    public function add_plugin_settings_link($link) {

        $ddbooking_link = '<a href="admin.php?page=ddbooking_settings">' . esc_html__('Settings Page', 'ddbooking') . '</a>';
        array_push($link, $ddbooking_link);

        return $link;
    }

    public function add_menu_item() {
        add_menu_page(
            esc_html__('ddBooking Settings Page', 'ddbooking'),
            esc_html__('ddBooking', 'ddbooking'),
            'manage_options',
            'ddbooking_settings',
            [$this, 'main_admin_page'],
            'dashicons-admin-plugins',
            100
        );
    }

    public function main_admin_page() {
        require_once DDBOOKING_PATH . 'admin/welcome.php';
    }

    // get hierarchical terms method
    public static function get_terms_hierarchical($tax_name, $current_term) {

        $taxonomy_terms = get_terms($tax_name, array(
            'hide_empty' => false,
            'parent' => 0
        ));

        $html = '';

        if ( ! empty($taxonomy_terms) ) {
            foreach ( $taxonomy_terms as $term ) {
                if ( $current_term  == $term->term_id ) {
                    $html .= '<option value="' . $term->term_id . '" selected>' . $term->name . '</option>';
                } else {
                    $html .= '<option value="' . $term->term_id . '">' . $term->name . '</option>';
                }

                $child_terms = get_terms($tax_name, array(
                    'hide_empty' => false,
                    'parent' => $term->term_id
                ));

                if ( ! empty($child_terms) ) {
                    foreach ( $child_terms as $child ) {
                        if ( $current_term  == $child->term_id ) {
                            $html .= '<option value="' . $child->term_id . '" selected> -- ' . $child->name . '</option>';
                        } else {
                            $html .= '<option value="' . $child->term_id . '"> -- ' . $child->name . '</option>';
                        }
                    }
                }
            }
        }

        return $html;

    }

     // enqueue admin styles and scripts method
    public function enqueue_admin() {
        wp_enqueue_style('ddBooking_style_admin', plugins_url( '/assets/css/admin/style.css', __FILE__ ));
        wp_enqueue_script('ddBooking_script_admin', plugins_url( '/assets/js/admin/scripts.js', __FILE__ ), array('jquery'), '1.0', true);
    }

    // enqueue frontend styles and scripts method
    public function enqueue_frontend() {
        wp_enqueue_style('ddBooking_style_frontend', plugins_url( '/assets/css/frontend/style.css', __FILE__ ));
        wp_enqueue_script('ddBooking_script_frontend', plugins_url( '/assets/js/frontend/scripts.js', __FILE__ ), array('jquery'), '1.0', true);
        wp_enqueue_script('jquery-form');
    }

    static function activation() {
         flush_rewrite_rules();
    }

    static function deactivation() {
        flush_rewrite_rules();
    }
}

// instahce
if( class_exists('ddBooking') ) {
    $ddBooking = new ddBooking();
    $ddBooking->register();
} 

register_activation_hook( __FILE__, array( $ddBooking, 'activation' ) );
register_deactivation_hook( __FILE__, array( $ddBooking, 'deactivation' ) );