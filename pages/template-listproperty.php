<?php
/*
Template Name: List Personal Properies
*/ 

get_header();
?>

<div class="wrapper">
    <div class="container">
        <?php
        if ( have_posts() ) {

            // Load posts loop.
            while ( have_posts() ) {
                the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <h2><?php the_title(); ?></h2>
                    <div class="description"><?php the_content(); ?></div>
                </article>

            <?php } ?>
        <?php } ?>

        <div class="list">
            <?php 
                if ( is_user_logged_in() ) {

                    global $current_user;
                    wp_get_current_user(); 

                    $edit_property = get_site_url() . '/add-property/';

                    $args = array(
                        'post_type' => 'property',
                        'post_per_page' => -1,
                        'post_status' => array('publish', 'pending', 'draft', 'future'),
                        'author' => $current_user->ID,
                    );

                    $listing = new WP_Query($args);

                    if ( $listing->have_posts() ): 
                        while ( $listing->have_posts() ) :

                            if ( $edit_property ) {
                                $new_edit_property = add_query_arg('edit', $post->ID, $edit_property);
                            }

                            $listing->the_post(); ?>

                        <div class="property">
                            <h3><?php the_title(); ?></h3>
                            <a href="<?php echo $new_edit_property; ?>">Edit property</a>
                            <a href="<?php the_permalink(); ?>">Read more</a>
                        </div>

                        <?php endwhile; ?>
                    <?php endif;
                }
            ?>
        </div>

    </div>
</div>

<?php
get_footer();