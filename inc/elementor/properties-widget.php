<?php

class Elementor_Properties_Widget extends \Elementor\Widget_Base {

    // init custom var for templeate loader
    protected $ddBooking_Template_Loader;

    // protected $ddLocations = array();

    protected function generate_locations_array_for_select() {
        $ddLocations = array('' => 'Select Location...');
        $temp_locations = get_terms('location');

        foreach ( $temp_locations as  $location ) {
            $ddLocations[$location->term_id] = $location->name;
        }
        
        return $ddLocations;
    }

    // elementor code
	public function get_name() {
		return 'ddproperies';
	}

	public function get_title() {
		return esc_html__( 'Properties List', 'ddbooking' );
	}

	public function get_icon() {
		return 'fa fa-code';
	}

	public function get_categories() {
		return [ 'ddbooking' ];
	}

	protected function _register_controls() {

        $ddLocations = $this->generate_locations_array_for_select();

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'ddbooking' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'count',
			[
				'label' => esc_html__( 'Posts Count', 'ddbooking' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 1,
			]
		);

        $this->add_control(
			'offer',
			[
				'label' => __( 'Offer', 'ddbooking' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
                    '' => 'Select offer...',
					'sale'  => esc_html__( 'For Sale', 'ddbooking' ),
					'rent' => esc_html__( 'For Rent', 'ddbooking' ),
					'sold' => esc_html__( 'Sold', 'ddbooking' ),
				],
			]
		);

        $this->add_control(
			'location',
			[
				'label' => __( 'Locations', 'ddbooking' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => $ddLocations,
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

        // $settings['offer'];

        $args = array(
            'post_type' => 'property',
            "posts_per_page" => $settings['count'],
            'meta_query' => array(
                'relation' => 'AND'
            ),
            'tax_query' => array(
                'relation' => 'AND'
            ),
        );

        if ( isset($settings['offer']) && $settings['offer'] != '' ) {
            array_push( $args['meta_query'], array(
                'key' => 'ddbooking_type',
                'value' => esc_attr($settings['offer']),
            ) );
        }

        if ( isset($settings['location']) && $settings['location'] != '' ) {
            array_push( $args['tax_query'], array(
                'taxonomy' => 'location',
                'terms' => $settings['location'],
            ));
        }

        $properties = new WP_Query($args);

        $this->ddBooking_Template_Loader = new ddBooking_Template_Loader();
        

        // loop
        if ( $properties->have_posts() ) {
			echo '<div class="wrapper archive_property">';

            while ( $properties->have_posts() ) {
                $properties->the_post(); 

                $this->ddBooking_Template_Loader->get_template_part('parts/content');
            } 

			echo '</div>';
        } else {
            echo '<p>' . esc_html__( 'Properties not found.',  'ddbooking') . '</p>'; 
        }

        wp_reset_postdata();

	}

}