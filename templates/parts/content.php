<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php if ( get_the_post_thumbnail(get_the_ID(), 'large') ) {
        echo get_the_post_thumbnail(get_the_ID(), 'large');
    } ?>
    <h2><?php the_title(); ?></h2>
    <div class="description"><?php the_excerpt(); ?></div>
    <div class="property_info">
        <span class="location">
            <?php esc_html_e( 'Location:', 'ddbooking' ) . ' '; 
                $locations = get_the_terms(get_the_ID(), 'location'); 
                foreach ( $locations as $location) {
                    echo  $location->name . ', ';
                }
            ?>
        </span>
        <span class="type">
            <?php esc_html_e( 'Type:', 'ddbooking' ); 
            echo ' ' . get_post_meta(get_the_ID(), 'ddbooking_type', true); ?>
        </span>
        <span class="price">
            <?php esc_html_e( 'Price:', 'ddbooking' ); 
            echo ' ' . get_post_meta(get_the_ID(), 'ddbooking_price', true); ?>
        </span>
        <span class="offer">
            <?php esc_html_e( 'Offer:', 'ddbooking' );
            echo ' ' . get_post_meta(get_the_ID(), 'ddbooking_offer', true); ?>
        </span>
        <span class="agent">
            <?php esc_html_e( 'Agent:', 'ddbooking' ); 
            $agent_id = get_post_meta(get_the_ID(), 'ddbooking_agent', true);
            $agent = get_post($agent_id);

            echo ' ' .esc_html($agent->post_title);
            ?>
        </span>
    </div>
    <a href="<?php the_permalink(); ?>">Open this Property</a>

    <?php if ( is_user_logged_in() ) { 
        $property_id = get_the_ID();
        $user_id = get_current_user_id();
        
        if( ddBooking_Wishlist::ddbooking_check_in_wishlist($user_id, $property_id) ) {
            if ( is_page_template('pages/template-wishlist.php') ) { ?>
                <a href="<?php echo admin_url('admin-ajax.php'); ?>" class="ddbooking_remove_property" data-property-id="<?php echo $property_id; ?>" data-user-id="<?php echo $user_id; ?>" style="display: inline-block;">Remove from Wishlist</a>
            <?php } else {
                echo '<p>Already Added to Wishlist</p>';
            }
        } else { ?>
            <form action="<?php echo admin_url('admin-ajax.php') ?>" method="POST" id="ddbooking_add_to_wishlist_form_<?php echo $property_id; ?>">
                <input type="hidden" name="dd_user_id" value="<?php echo $user_id; ?>">
                <input type="hidden" name="dd_property_id" value="<?php echo $property_id; ?>">
                <input type="hidden" name="action" value="ddbooking_add_to_wishlist">
                <a href="#" data-property-id="<?php echo $property_id; ?>" class="ddbooking_add_to_wishlist">Add to Wishlist</a>
                <span class="succesfull_added" style="display: none;">Added to Wishlist</span>
            </form>
        <?php } ?>
        
    <?php } ?>

</article>