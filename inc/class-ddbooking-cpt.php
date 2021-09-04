<?php 

if( ! class_exists('ddBookingCpt') ) {

    class ddBookingCpt {

        public function register() {
            add_action('init', [$this, 'custom_post_type']);

            add_action('add_meta_boxes', [$this, 'add_meta_box_property']);
            add_action('save_post', [$this, 'save_metabox'], 10, 2);

            add_action('manage_property_posts_columns', [$this, 'custom_columns_for_property']);
            add_action('manage_property_posts_custom_column', [$this, 'custom_property_columns_data'], 10, 2);
            add_filter('manage_edit-property_sortable_columns', [$this, 'custom_property_columns_sort']);
            add_action('pre_get_posts', [$this, 'custom_property_order']);
        }

        public function add_meta_box_property() {
            add_meta_box(
                'ddbooking_settings',
                'Property Settings',
                [$this, 'metabox_property_html'],
                'property',
                'normal',
                'default'
            );
        }

        public function save_metabox($post_id, $post) {

            if ( ! isset($_POST['_ddbooking']) || ! wp_verify_nonce( $_POST['_ddbooking'], 'ddbookingfields' ) ) {
                return $post_id;
            }

            if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
                return $post_id;
            }

            if ( $post->post_type != 'property' ) {
                return $post_id;
            }

            $post_type = get_post_type_object( $post->post_type );
            if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
                return $post_id;
            }

            
            if ( is_null($_POST['ddbooking_price']) ) {
                delete_post_meta($post_id, 'ddbooking_price');
            } else {
                update_post_meta($post_id, 'ddbooking_price', sanitize_text_field( intval($_POST['ddbooking_price']) ));
            }

            if ( is_null($_POST['ddbooking_period']) ) {
                delete_post_meta($post_id, 'ddbooking_period');
            } else {
                update_post_meta($post_id, 'ddbooking_period', sanitize_text_field( $_POST['ddbooking_period'] ));
            }

            if ( is_null($_POST['ddbooking_type']) ) {
                delete_post_meta($post_id, 'ddbooking_type');
            } else {
                update_post_meta($post_id, 'ddbooking_type', sanitize_text_field( $_POST['ddbooking_type'] ));
            }

            if ( is_null($_POST['ddbooking_agent']) ) {
                delete_post_meta($post_id, 'ddbooking_agent');
            } else {
                update_post_meta($post_id, 'ddbooking_agent', sanitize_text_field( $_POST['ddbooking_agent'] ));
            }

            return $post_id;

        }

        public function metabox_property_html($post) {

            wp_nonce_field('ddbookingfields', '_ddbooking');

            $price = get_post_meta($post->ID, 'ddbooking_price', true);
            $period = get_post_meta($post->ID, 'ddbooking_period', true);
            $type = get_post_meta($post->ID, 'ddbooking_type', true);
            $agent_meta = get_post_meta($post->ID, 'ddbooking_agent', true);

            echo '
            <p>
                <label for="ddbooking_price">Price</label>
                <input type="number" id="ddbooking_price" name="ddbooking_price" value="' . esc_html($price) . '">
            </p>

            <p>
                <label for="ddbooking_period">Period</label>
                <input type="text" id="ddbooking_period" name="ddbooking_period" value="' . esc_html($period) . '">
            </p>

            <p>
                <label for="ddbooking_type">Type</label>
                <select id="ddbooking_type" name="ddbooking_type">
                    <option value="">Select type</option>
                    <option value="sale"' . selected('sale', $type, false) . '>For Sale</option>
                    <option value="rent"' . selected('rent', $type, false) . '>For Rent</option>
                    <option value="sold"' . selected('sold', $type, false) . '>Sold</option>
                </select>
            </p>
            ';

            $agents = get_posts(array('post_type' => 'agent', 'post_per_page' => -1));
            
            if ( $agents ) {

                echo '
                    <p>
                    <label for="ddbooking_agent">Agent</label>
                        <select id="ddbooking_agent" name="ddbooking_agent">
                        <option value="">Select Agent</option>
                ';

                foreach ( $agents as $agent ) { ?>
                    <option value="<?php echo esc_html( $agent->ID ); ?>" <?php if ( $agent->ID == $agent_meta ) { echo 'selected'; } ?> >
                        <?php echo esc_html( $agent->post_title ); ?>
                    </option>
                <?php }

                echo '
                        </select>
                    </p>
                ';
            }
        }

        public function custom_post_type() {

            register_post_type('property', 
            array(
                'public' => true,
                'has_archive' => true,
                'rewrite' => array('slug' => 'properties'),
                'label' => 'Property',
                'supports' => array('title', 'editor', 'thumbnail'),
                // 'show_in_rest' => true
            ));
        
            register_post_type('agent', 
            array(
                'public' => true,
                'has_archive' => true,
                'rewrite' => array('slug' => 'agents'),
                'label' => 'Agents',
                'supports' => array('title', 'editor', 'thumbnail'),
                // 'show_in_rest' => true
            ));

            // register taxonomy

            $labels = array(
                'name'              => esc_html_x( 'Locations', 'taxonomy general name', 'ddbooking'),
                'singular_name'     => esc_html_x( 'Location', 'taxonomy general name', 'ddbooking'),
                'search_items'      => esc_html( 'Search Location', 'ddbooking'),
                'all_items'         => esc_html( 'All Locations', 'ddbooking'),
                'view_item '        => esc_html( 'View Location', 'ddbooking'),
                'parent_item'       => esc_html( 'Parent Location', 'ddbooking'),
                'parent_item_colon' => esc_html( 'Parent Location:', 'ddbooking'),
                'edit_item'         => esc_html( 'Edit Location', 'ddbooking'),
                'update_item'       => esc_html( 'Update Location', 'ddbooking'),
                'add_new_item'      => esc_html( 'Add New Location', 'ddbooking'),
                'new_item_name'     => esc_html( 'New Location Name', 'ddbooking'),
                'menu_name'         => esc_html( 'Location', 'ddbooking'),
            );

            $args = array(
                'hierarchical' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => array('slug' => 'properties/location'),
                'labels' => $labels
            );

            register_taxonomy('location', 'property', $args);

            unset($args);
            unset($labels);

            $labels = array(
                'name'              => esc_html_x( 'Types', 'taxonomy general name', 'ddbooking'),
                'singular_name'     => esc_html_x( 'Type', 'taxonomy general name', 'ddbooking'),
                'search_items'      => esc_html( 'Search Type', 'ddbooking'),
                'all_items'         => esc_html( 'All Types', 'ddbooking'),
                'view_item '        => esc_html( 'View Type', 'ddbooking'),
                'parent_item'       => esc_html( 'Parent Type', 'ddbooking'),
                'parent_item_colon' => esc_html( 'Parent Type:', 'ddbooking'),
                'edit_item'         => esc_html( 'Edit Type', 'ddbooking'),
                'update_item'       => esc_html( 'Update Type', 'ddbooking'),
                'add_new_item'      => esc_html( 'Add New Type', 'ddbooking'),
                'new_item_name'     => esc_html( 'New Type Name', 'ddbooking'),
                'menu_name'         => esc_html( 'Type', 'ddbooking'),
            );

            $args = array(
                'hierarchical' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => array('slug' => 'properties/types'),
                'labels' => $labels
            );

            register_taxonomy('property-type', 'property', $args);

        }

        public function custom_columns_for_property($columns) {

            $title = $columns['title'];
            $date = $columns['date'];
            $location = $columns['taxonomy-location'];
            $type = $columns['taxonomy-property-type'];

            $columns['title'] = $title;
            $columns['date'] = $date;
            $columns['taxonomy-location'] = $location;
            $columns['taxonomy-property-type'] = $type;
            
            // custom columns
            $columns['price'] = esc_html__('Price', 'ddbooking');
            $columns['offer'] = esc_html__('Offer', 'ddbooking');
            $columns['agent'] = esc_html__('Agent', 'ddbooking');

            return $columns;
        }

        public function custom_property_columns_data($column, $post_id) {

            $price = get_post_meta( $post_id, 'ddbooking_price', true);
            $offer = get_post_meta( $post_id, 'ddbooking_type', true);
            $agent_id = get_post_meta( $post_id, 'ddbooking_agent', true);
            if ($agent_id ) {
                $agent = get_the_title($agent_id);
            } else {
                $agent = 'No agent';
            }

            switch( $column ) {
                case 'price': 
                    echo esc_html($price);
                    break;

                case 'offer':
                    echo esc_html($offer);
                    break;

                case 'agent':
                    echo esc_html($agent);
                    break;
            }

        }

        public function custom_property_columns_sort($columns){

            $columns['price'] = 'price';
            $columns['offer'] = 'offer';

            return $columns;
        }

        public function custom_property_order($query) {

            if ( ! is_admin() ) {
                return;
            }

            $orderby = $query->get('orderby');

            if ( 'price' == $orderby ) {
                $query->set('meta_key', 'ddbooking_price');
                $query->set('orderby', 'meta_value_num');
            }

            if ( 'offer' == $orderby ) {
                $query->set('meta_key', 'ddbooking_type');
                $query->set('orderby', 'meta_value');
            }

        }

    }

    if( class_exists('ddBookingCpt') ) {
        $ddBookingCpt = new ddBookingCpt();
        $ddBookingCpt->register();
    }

}