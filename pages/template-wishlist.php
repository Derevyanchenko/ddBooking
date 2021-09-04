<?php

/*
Template Name: Template Wishlist
*/ 

get_header();
?>

<div class="wrapper archive_property">
    <div class="container">
    
        <?php
        if ( have_posts() ) {

            while ( have_posts() ) {
                the_post(); 

                the_content();
            } 
        }
        ?>

        <?php 
        if ( is_user_logged_in() ) {
            global $current_user;
            $user_id = get_current_user_id();
            $wishlist_items = get_user_meta($user_id, 'ddbooking_wishlist_properties');
            if ( count($wishlist_items) > 0 ) {

                $args = array(
                    'post_type' => 'property',
                    'post_per_page' => -1,
                    'post__in' => $wishlist_items,
                    'orderby' => 'post__in'
                );

                $properties = new WP_Query($args);

                if ( $properties->have_posts() ) {

                    while ( $properties->have_posts() ) {
                        $properties->the_post(); 
                        
                        $ddBooking_Template_Loader->get_template_part('parts/content');
                    } 
                }
            } else {
                echo 'Mo properties in Wishlist';
            }

        }
        ?>

    </div>
</div>

<?php
get_footer();