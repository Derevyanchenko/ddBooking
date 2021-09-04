<?php 

if ( ! class_exists('ddBooking_Shortcodes') ) {

    class ddBooking_Shortcodes {

        public $ddBooking;
        public $agents;

        public function register() {
            add_action('init', [$this, 'register_shortcode']);
        }

        public function register_shortcode() {
            add_shortcode( 'ddbooking_filter', [$this, 'filter_shortcode'] );
        }

        public function filter_shortcode($atts = array()) {

            // [ddbooking_filter location='1' offer='1' price='1' agent='1' type='1']
            // [ddbooking_filter location='1' type='1' price='1' offer='1' agent='1']

            extract(shortcode_atts( array(
                'location' => 0,
                'offer' => 0,
                'price' => 0,
                'agent' => 0,
                'type' => 0,
            ), $atts));

            $this->ddBooking = new ddBooking();

            $this->agents = get_posts(array( 'post_type' => 'agent', 'numberposts' => -1));
                
            $agents_list = '';
            foreach ($this->agents as $agent_single) {
                $agents_list .= '<option value="' . $agent_single->ID . '">' . $agent_single->post_title . '</option>';
            }

            $output = '';
            $output .= '<div class="wrapper filter-form">';
            $output .= '<div class="container">';
            $output .= '<form method="post" action="'. get_post_type_archive_link('property') .'">';

            if ( $location == 1 ) {
                $output .= '
                    <select name="ddbooking_location">
                        <option value="">Select Location</option>
                        ' . ddBooking::get_terms_hierarchical('location', '') . ' 
                    </select>
                ';
            }

            if ( $type == 1 ) {    
                $output .= '
                    <select name="ddbooking_property-type">
                        <option value="">Select property type</option>
                        ' . ddBooking::get_terms_hierarchical('property-type', '') . ' 
                    </select>
                ';
            }

            if ( $price == 1 ) {  
                $output .= '
                    <input 
                        type="text" 
                        placeholder="Max Price" 
                        name="ddbooking_price" 
                        value=""
                    />
                ';
            }

            if ( $offer == 1 ) {  
                $output .= '
                    <select name="ddbooking_type">
                        <option value="">Select Offer</option>
                        <option value="sale">For Sale</option>
                        <option value="rent">For Rent</option>
                        <option value="sold">For Sold</option>
                    </select>
                ';
            }

            if ( $agent == 1 ) {
                $output .= '
                    <select name="ddbooking_agent">
                        <option value="">Select Agent</option>
                        ' . $agents_list . '
                    </select>
                ';
            }

            $output .= '<input type="submit" name="submit" value="Filter" />';



            $output .= '</form>';
            $output .= '</div>';
            $output .= '</div>';

            return $output;
        }

    }

    $ddBooking_Shortcodes = new ddBooking_Shortcodes();
    $ddBooking_Shortcodes->register();

}