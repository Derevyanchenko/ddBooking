<?php
/*
* Template Name: Add Property
*/ 

function ddbooking_image_validation($file_name) {

    $valid_extentions = array('jpg', 'jpeg', 'gif', 'png');
    $exploded_array = explode('.', $file_name);
    if ( ! empty($exploded_array) && is_array($exploded_array) ) {
        $ext = array_pop($exploded_array);
        return in_array($ext, $valid_extentions);
    } else {
        return false;
    }

}

function ddbooking_insert_attachment($file_handler, $post_id, $setthumb = false) {

    if ( $_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK ) __return_false();

    require_once( ABSPATH . 'wp-admin' . '/includes/image.php' );
    require_once( ABSPATH . 'wp-admin' . '/includes/file.php' );
    require_once( ABSPATH . 'wp-admin' . '/includes/media.php' );

    $attach_id = media_handle_upload($file_handler, $post_id);

    if ( $setthumb ) {
        update_post_meta( $post_id, '_thumbnail_id', $attach_id );
    }

    return $attach_id;

}

$success = '';

if ( isset($_POST['action']) && is_user_logged_in() ) {
    if ( wp_verify_nonce($_POST['property_nonce'], 'submit_property') ) {

        $ddbooking_item = array();

        $ddbooking_item['post_title'] = sanitize_text_field($_POST['property_title']);
        $ddbooking_item['post_type'] = 'property';
        $ddbooking_item['post_content'] = sanitize_textarea_field($_POST['property_description']);

        global $current_user; wp_get_current_user();
        $ddbooking_item['post_author'] = $current_user->ID;

        $ddbooking_action = $_POST['action'];
        if ( $ddbooking_action == 'ddbooking_add_property' ) {
            $ddbooking_item['post_status'] = 'pending';

            $ddbooking_item_id = wp_insert_post($ddbooking_item);

            if ( $ddbooking_item_id > 0 ) {
                do_action('wp_insert_post', 'wp_insert_post');
                $success = 'Property Successful Submit';
            }
        } elseif ( $ddbooking_action == 'ddbooking_edit_property' ) {
            $ddbooking_item['post_status'] = 'pending';
            $ddbooking_item['ID'] = intval($_POST['property_id']);
            $ddbooking_item_id = wp_update_post($ddbooking_item);

            $success = 'Property Successful Updated';
        }

        // add other fields - metaboxes, taxonomy, image 

        if ( $ddbooking_item_id > 0 ) {
            
            if ( isset($_POST['property_offer']) && $_POST['property_offer'] != '' ) {
                update_post_meta( $ddbooking_item_id, 'ddbooking_type', trim($_POST['property_offer']) );
            }
            if ( isset($_POST['property_price']) ) {
                update_post_meta( $ddbooking_item_id, 'ddbooking_price', trim($_POST['property_price']) );
            }
            if ( isset($_POST['property_period']) ) {
                update_post_meta( $ddbooking_item_id, 'ddbooking_period', trim($_POST['property_period']) );
            }
            if ( isset($_POST['property_agent']) ) {
                update_post_meta( $ddbooking_item_id, 'ddbooking_agent', trim($_POST['property_agent']) );
            }

        }

        // taxonomy
        if ( isset($_POST['property_location']) ) {
            wp_set_object_terms( $ddbooking_item_id, intval($_POST['property_location']), 'location' );
        }
        if ( isset($_POST['property_type']) ) {
            wp_set_object_terms( $ddbooking_item_id, intval($_POST['property_type']), 'property-type' );
        }

        // image
        if ( $_FILES ) {
            foreach ( $_FILES as $submitted_file => $file_array ) {
                if ( ddbooking_image_validation($_FILES[$submitted_file]['name']) ) {
                    
                    $size = intval($_FILES[$submitted_file]['size']);

                    if ( $size > 0 ) {
                        ddbooking_insert_attachment($submitted_file, $ddbooking_item_id, true);
                    }

                }
            }
        }

    }
}

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
                        <div class="description"><?php the_content(); ?></div>
                    </article>

                    <?php if ( is_user_logged_in() ) { 
                        
                        if ( !empty($success) ) {
                            echo esc_html( $success );
                        } else {

                            if ( isset($_GET['edit']) && !empty($_GET['edit']) ) {

                                $property_id_edit = intval(trim($_GET['edit']));
                                $dd_edit_property = get_post($property_id_edit);

                                if ( ! empty($dd_edit_property) && $dd_edit_property->post_type == 'property' ) {

                                    global $current_user;
                                    wp_get_current_user()
                                    
                                    if ( $dd_edit_property->post_author == $current_user->ID ) {
                                        $dd_matadata = get_post_custom( $dd_edit_property->ID );
                                        ?>

                                            <h2>Edit Property</h2>
                                             <div class="add_form">
                                                    <form method="POST" id="add_property" enctype="multipart/form-data">
                                                        <p>
                                                            <label for="property_title">Title</label>
                                                            <input type="text" name="property_title" id="property_title" placeholder="Add the Title" value="<?php echo $dd_edit_property->post_title; ?>" required tabindex="1" /> 
                                                        </p>
                                                        <p>
                                                            <label for="property_description">Description</label>
                                                            <textarea name="property_description" id="property_description" placeholder="Add the Description" required tabindex="2">
                                                                <?php echo $dd_edit_property->post_content; ?>
                                                            </textarea>
                                                        </p>
                                                        <p>
                                                        <label for="property_image">Featured Image</label>
                                                            <input type="file" name="property_image" id="property_image" tabindex="3" required>
                                                        </p>
                                                        <p>
                                                            <label for="property_location">Select Location</label>
                                                            <select name="property_location" id="property_location"  tabindex="4" >
                                                                <?php

                                                                $current_term_id = 0;
                                                                $tax_terms = get_the_terms($dd_edit_property->ID, 'location');

                                                                if ( ! empty($tax_terms) ) {
                                                                    foreach ( $tax_terms as $tax_term ) {
                                                                        $current_term_id = $tax_term->term_id;
                                                                        break;
                                                                    }
                                                                }
                                                                $current_term_id = intval($current_term_id);

                                                                $locations = get_terms('location', array(
                                                                        'hide_empty' => false
                                                                ));
                                                                
                                                                if ( ! empty($locations) ) {
                                                                    foreach ($locations as $location) {

                                                                        $selected = '';
                                                                        if ( $current_term_id == $location->term_id ) { $selected = 'selected'; }

                                                                        echo '<option' . $selected . ' value="'. $location->term_id .'">' . $location->name . '</option>';
                                                                    }
                                                                }

                                                                ?>
                                                            </select>
                                                        </p>
                                                        <p>
                                                            <label for="property_type">Select Type</label>
                                                            <select name="property_type" id="property_type"  tabindex="5" >
                                                                <?php
                                                                $current_term_id = 0;
                                                                $tax_terms = get_the_terms($dd_edit_property->ID, 'property-type');

                                                                if ( ! empty($tax_terms) ) {
                                                                    foreach ( $tax_terms as $tax_term ) {
                                                                        $current_term_id = $tax_term->term_id;
                                                                        break;
                                                                    }
                                                                }
                                                                $current_term_id = intval($current_term_id);

                                                                $types = get_terms('property-type', array(
                                                                        'hide_empty' => false
                                                                ));
                                                                
                                                                if ( ! empty($types) ) {
                                                                    foreach ($types as $type) {
                                                                        
                                                                        $selected = '';
                                                                        if ( $current_term_id == $type->term_id ) { $selected = 'selected'; }

                                                                        echo '<option' . $selected . ' value="'. $type->term_id .'">' . $type->name . '</option>';
                                                                    }
                                                                }

                                                                ?>
                                                            </select>
                                                        </p>
                                                        <p>
                                                            <label for="property_offer">Select Offer Type</label>
                                                            <select name="property_offer" id="property_offer"  tabindex="6" >
                                                                <option selected value="">Not selected</option>
                                                                <option value="sale" <?php if ( get_post_meta($dd_edit_property->ID, 'ddbooking_type', true) == 'sale' ) { echo 'selected'; } ?> >For Sale</option>
                                                                <option value="sold" <?php if ( get_post_meta($dd_edit_property->ID, 'ddbooking_type', true) == 'sold' ) { echo 'selected'; } ?>>For Sold</option>
                                                                <option value="rent" <?php if ( get_post_meta($dd_edit_property->ID, 'ddbooking_type', true) == 'rent' ) { echo 'selected'; } ?>>For Rent</option>
                                                            </select>
                                                        </p>
                                                        <p>
                                                            <label for="property_price">Price</label>
                                                            <input type="text" name="property_price" id="property_price" value="<?php get_post_meta($dd_edit_property->ID, 'ddbooking_price', true); ?>" required tabindex="7" /> 
                                                        </p>
                                                        <p>
                                                            <label for="property_period">Period</label>
                                                            <input type="text" name="property_period" id="property_period" value=""<?php get_post_meta($dd_edit_property->ID, 'ddbooking_period', true); ?>" required tabindex="8" /> 
                                                        </p>
                                                        <p>
                                                            <?php  
                                                                global $current_user;
                                                                wp_get_current_user();
                                                            ?>
                                                            <label for="property_agent">Agent</label>
                                                            <input type="text" name="property_agent" id="property_agent" value="<?php echo $current_user->ID; ?>" required tabindex="9" /> 
                                                        </p>
                                                        <p>
                                                            <?php wp_nonce_field('submit_property', 'property_nonce'); ?>
                                                            <input type="submit" name="submit" tabindex="10" valur="Edit Property" />
                                                            <input type="hidden" name="action" value="ddbooking_edit_property" />
                                                            <input type="hidden" name="property_id" value="<?php echo $dd_edit_property->ID; ?>" />
                                                        </p>
                                                    </form>
                                                </div>

                                        <?php
                                    }

                                }

                            } else {
                        ?>

                            <div class="add_form">
                                <form method="POST" id="add_property" enctype="multipart/form-data">
                                    <p>
                                        <label for="property_title">Title</label>
                                        <input type="text" name="property_title" id="property_title" placeholder="Add the Title" value="" required tabindex="1" /> 
                                    </p>
                                    <p>
                                        <label for="property_description">Description</label>
                                        <textarea name="property_description" id="property_description" placeholder="Add the Description" required tabindex="2"></textarea>
                                    </p>
                                    <p>
                                    <label for="property_image">Featured Image</label>
                                        <input type="file" name="property_image" id="property_image" tabindex="3" required>
                                    </p>
                                    <p>
                                        <label for="property_location">Select Location</label>
                                        <select name="property_location" id="property_location"  tabindex="4" >
                                            <?php
                                            $locations = get_terms('location', array(
                                                    'hide_empty' => false
                                            ));
                                            
                                            if ( ! empty($locations) ) {
                                                foreach ($locations as $location) {
                                                    echo '<option value="'. $location->term_id .'">' . $location->name . '</option>';
                                                }
                                            }

                                            ?>
                                        </select>
                                    </p>
                                    <p>
                                        <label for="property_type">Select Type</label>
                                        <select name="property_type" id="property_type"  tabindex="5" >
                                            <?php
                                            $types = get_terms('property-type', array(
                                                    'hide_empty' => false
                                            ));
                                            
                                            if ( ! empty($types) ) {
                                                foreach ($types as $type) {
                                                    echo '<option value="'. $type->term_id .'">' . $type->name . '</option>';
                                                }
                                            }

                                            ?>
                                        </select>
                                    </p>
                                    <p>
                                        <label for="property_offer">Select Offer Type</label>
                                        <select name="property_offer" id="property_offer"  tabindex="6" >
                                            <option selected value="">Not selected</option>
                                            <option value="sale">For Sale</option>
                                            <option value="sold">For Sold</option>
                                            <option value="rent">For Rent</option>
                                        </select>
                                    </p>
                                    <p>
                                        <label for="property_price">Price</label>
                                        <input type="text" name="property_price" id="property_price" value="" required tabindex="7" /> 
                                    </p>
                                    <p>
                                        <label for="property_period">Period</label>
                                        <input type="text" name="property_period" id="property_period" value="" required tabindex="8" /> 
                                    </p>
                                    <p>
                                        <?php  
                                            global $current_user;
                                            wp_get_current_user();
                                        ?>
                                        <label for="property_agent">Agent</label>
                                        <input type="text" name="property_agent" id="property_agent" value="<?php echo $current_user->ID; ?>" required tabindex="9" /> 
                                    </p>
                                    <p>
                                        <?php wp_nonce_field('submit_property', 'property_nonce'); ?>
                                        <input type="submit" name="submit" tabindex="10" valur="Add New Property" />
                                        <input type="hidden" name="action" value="ddbooking_add_property" />
                                    </p>
                                </form>
                            </div>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>

            <?php } 
            }  
        ?>
        </div>
    </div>

<?php
get_footer();