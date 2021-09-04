<?php get_header(); ?>

<?php $ddBooking_Template_Loader->get_template_part('parts/filter'); ?>

<div class="wrapper archive_property">
    <div class="container">
        <?php

        if ( ! empty( $_POST['submit'] ) ) {
            
            $args = array(
                'post_type' => 'property',
                'post_per_page' => -1,
                'meta_query' => array(
                    'relation' => 'AND'
                ),
                'tax_query' => array(
                    'relation' => 'AND'
                ),

            );

            if ( isset( $_POST['ddbooking_type'] ) && $_POST['ddbooking_type'] != '' ) {
                array_push( $args['meta_query'], array(
                    'key' => 'ddbooking_type',
                    'value' => esc_attr($_POST['ddbooking_type']),
                ) );
            }

            if ( isset( $_POST['ddbooking_price'] ) && $_POST['ddbooking_price'] != '' ) {
                array_push( $args['meta_query'], array(
                    'key' => 'ddbooking_price',
                    'value' => esc_attr($_POST['ddbooking_price']),
                    'type' => 'numeric',
                    'compare' =>  '<=',
                ) );
            }

            if ( isset( $_POST['ddbooking_agent'] ) && $_POST['ddbooking_agent'] != '' ) {
                array_push( $args['meta_query'], array(
                    'key' => 'ddbooking_agent',
                    'value' => esc_attr($_POST['ddbooking_agent']),
                ) );
            }

            if ( isset( $_POST['ddbooking_location'] ) && $_POST['ddbooking_location'] != '' ) {
                array_push( $args['tax_query'], array(
                    'taxonomy' => 'location',
                    'terms' => $_POST['ddbooking_location'],
                ));
            }

            if ( isset( $_POST['ddbooking_property-type'] ) && $_POST['ddbooking_property-type'] != '' ) {
                array_push( $args['tax_query'], array(
                    'taxonomy' => 'property-type',
                    'terms' => $_POST['ddbooking_property-type'],
                ));
            }

            $properties = new WP_Query($args);

            if ( $properties->have_posts() ) {

                while ( $properties->have_posts() ) {
                    $properties->the_post(); 
                    $ddBooking_Template_Loader->get_template_part('parts/content');
                } 
            }
            else {
                echo '<p>' . esc_html__( 'Properties not found.',  'ddbooking') . '</p>'; 
            }

        } else {

            if ( have_posts() ) {

                while ( have_posts() ) {
                    the_post(); 
                    $ddBooking_Template_Loader->get_template_part('parts/content');
            } 
            
            // Pagination

            posts_nav_link();
            }
            else {
                echo '<p>' . esc_html__( 'Properties not found.',  'ddbooking') . '</p>'; 
            }
        }

        ?>
    </div>
</div>

<?php get_footer(); ?>