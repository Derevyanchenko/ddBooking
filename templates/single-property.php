<?php get_header(); ?>

<div class="wrapper single_property">
    <div class="container">
        <?php
            if ( have_posts() ) {

                // Load posts loop.
                while ( have_posts() ) {
                    the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <?php if ( get_the_post_thumbnail(get_the_ID(), 'large') ) {
                            echo get_the_post_thumbnail(get_the_ID(), 'large');
                        } ?>

                        <?php
                            echo do_shortcode('[ddbooking_booking]');
                        ?>

                        <h2><?php the_title(); ?></h2>
                        <div class="description"><?php the_content(); ?></div>
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
                                echo ' ' . get_post_meta(get_the_ID(), 'ddbooking_property-type', true); ?>
                            </span>
                            <span class="price">
                                <?php esc_html_e( 'Price:', 'ddbooking' ); 
                                echo ' ' . get_post_meta(get_the_ID(), 'ddbooking_price', true); ?>
                            </span>
                            <span class="offer">
                                <?php esc_html_e( 'Offer:', 'ddbooking' );
                                echo ' ' . get_post_meta(get_the_ID(), 'ddbooking_type', true); ?>
                            </span>
                            <span class="agent">
                                <?php esc_html_e( 'Agent:', 'ddbooking' ); 
                                $agent_id = get_post_meta(get_the_ID(), 'ddbooking_agent', true);
                                $agent = get_post($agent_id);
                                if ( $agent ) {
                                    echo ' ' .esc_html($agent->post_title);
                                }
                                ?>
                            </span>
                        </div>
                    </article>
            <?php } 
            }  
        ?>
    </div>
</div>

<?php get_footer(); ?>