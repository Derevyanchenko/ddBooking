<?php

class WPBakery_ddBooking_List {

    // init custom var for templeate loader
    protected $ddBooking_Template_Loader;

    function __construct() {
        add_action('init', [$this, 'create_shortcode']);
        add_shortcode('ddbooking_list', [$this, 'render_shortcode']);
    }

    public function create_shortcode() {
        if ( function_exists('vc_map') ) {

            vc_map(array(
                'name' => 'Filter',
                'base' => 'ddbooking_list',
                'description' => 'First shortcode',
                'category' => 'ddBooking',
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'heading' => 'Title',
                        'param_name' => 'title',
                        'value' => '',
                        'description' => 'Insert the title',
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => 'Count',
                        'param_name' => 'count',
                        'value' => '',
                        'description' => 'Insert the count',
                    ),
                ),
            ));

        }
    }

    public function render_shortcode($atts, $content, $tag) {
        $atts = (shortcode_atts(array(
            'title' => '',
            'count' => '2',
        ), 
        $atts));

        $args = array(
            'post_type' => 'property',
            "posts_per_page" => $atts['count'],
        );
        $properties = new WP_Query($args);

        $this->ddBooking_Template_Loader = new ddBooking_Template_Loader();

        echo '<div class="wrapper archive_property">';

        if ( $properties->have_posts() ) {
            while ( $properties->have_posts() ) {
                $properties->the_post(); 

                $this->ddBooking_Template_Loader->get_template_part('parts/content');
            } 
        } else {
            echo '<p>' . esc_html__( 'Properties not found.',  'ddbooking') . '</p>'; 
        }

        echo '</div>';

    }

}

new WPBakery_ddBooking_List();