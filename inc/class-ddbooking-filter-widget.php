<?php 

if ( ! class_exists('ddBooking_Filter_Widget') ) {

    class ddBooking_Filter_Widget extends WP_Widget {

        function __construct() {
            parent::__construct('ddbooking_filter_widget', esc_html__('filter', 'ddbooking'), array(
                'description' => 'Filter Form'
            ));
        }

        public function widget($args, $instance) {
            extract($args);

            $title = apply_filters('widget_title', $instance['title']);

            echo $before_widget;

            if ( $title ) {
                echo $before_widget . esc_html($title) . $after_widget;
            }

            $fields = '';

            if ( $location ) {
                $fields .= ' location="1" ';
            }

            if ( $type ) {
                $fields .= ' type="1" ';
            }

            if ( $price ) {
                $fields .= ' price="1" ';
            }

            if ( $offer ) {
                $fields .= ' offer="1" ';
            }

            if ( $agent ) {
                $fields .= ' agent="1" ';
            }

            echo do_shortcode('[ddbooking_filter ' . $fields . ' ]');

            echo $after_widget;

        }

        public function form($instance) {

            if ( isset($instance['title']) ) {
                $title = $instance['title'];
            } else {
                $title = '';
            }

            if ( isset($instance['location']) ) {
                $location = $instance['location'];
            } else {
                $location = '';
            }

            if ( isset($instance['type']) ) {
                $type = $instance['type'];
            } else {
                $type = '';
            }

            if ( isset($instance['price']) ) {
                $price = $instance['price'];
            } else {
                $price = '';
            }

            if ( isset($instance['offer']) ) {
                $offer = $instance['offer'];
            } else {
                $offer = '';
            }

            if ( isset($instance['agent']) ) {
                $agent = $instance['agent'];
            } else {
                $agent = '';
            }

            ?>

            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>">Title</label>
                <input 
                    type="text" 
                    id="<?php echo $this->get_field_id('title'); ?>" 
                    name="<?php echo $this->get_field_id('title'); ?>" 
                    value="<?php echo esc_attr($title); ?>"
                    class="widefat"
                />
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('location'); ?>">Show Location Field</label>
                <input 
                    type="checkbox" 
                    id="<?php echo $this->get_field_id('location'); ?>" 
                    name="<?php echo $this->get_field_id('location'); ?>" 
                    <?php checked($location, 'on'); ?>
                />
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('type'); ?>">Show Type Field</label>
                <input 
                    type="checkbox" 
                    id="<?php echo $this->get_field_id('type'); ?>" 
                    name="<?php echo $this->get_field_id('type'); ?>" 
                    <?php checked($type, 'on'); ?>
                />
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('price'); ?>">Show Price Field</label>
                <input 
                    type="checkbox" 
                    id="<?php echo $this->get_field_id('price'); ?>" 
                    name="<?php echo $this->get_field_id('price'); ?>" 
                    <?php checked($price, 'on'); ?>
                />
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('offer'); ?>">Show Offer Field</label>
                <input 
                    type="checkbox" 
                    id="<?php echo $this->get_field_id('offer'); ?>" 
                    name="<?php echo $this->get_field_id('offer'); ?>" 
                    <?php checked($offer, 'on'); ?>
                />
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('agent'); ?>">Show Agent Field</label>
                <input 
                    type="checkbox" 
                    id="<?php echo $this->get_field_id('agent'); ?>" 
                    name="<?php echo $this->get_field_id('agent'); ?>" 
                    <?php checked($agent, 'on'); ?>
                />
            </p>

            <?php 
        }

        public function update($new_instance, $old_instance ) {
            
            $instance = $old_instance;

            $instance['title'] = strip_tags($new_instance['title']);
            $instance['location'] = strip_tags($new_instance['location']);
            $instance['type'] = strip_tags($new_instance['type']);
            $instance['price'] = strip_tags($new_instance['price']);
            $instance['offer'] = strip_tags($new_instance['offer']);
            $instance['agent'] = strip_tags($new_instance['agent']);

            return $instance;
        }

    }

    // $ddBooking_Shortcodes = new ddBooking_Shortcodes();
    // $ddBooking_Shortcodes->register();

}