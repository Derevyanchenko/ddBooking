<?php get_header(); ?>

<div class="wrapper archive_agent">
    <div class="container">
        <?php
            // if is archive page, get_query_var = paged
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; 
            // is if static page (frontpage, etc..), get_query_var = page
            // $paged = (get_query_var('page')) ? get_query_var('page') : 1; 
            $agents = new WP_Query(array(
                'post_type' => 'agent',
                'post_per_page' => 2,
                'paged' => $paged,

            ));

            if ( $agents->have_posts() ) {

                // Load posts loop.
                while ( $agents->have_posts() ) {
                    $agents->the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <?php if ( get_the_post_thumbnail(get_the_ID(), 'large') ) {
                            echo get_the_post_thumbnail(get_the_ID(), 'large');
                        } ?>
                        <h2><?php the_title(); ?></h2>
                        <div class="description"><?php the_excerpt(); ?></div>
                        <a href="<?php the_permalink(); ?>">Open this Agent</a>
                    </article>
            <?php } 
            
            // Pagination
            dd_pagination($agents);

            }
            else {
                echo '<p>' . esc_html__( 'Properties not found.',  'ddbooking') . '</p>'; 
            }
        ?>
    </div>
</div>

<?php get_footer(); ?>