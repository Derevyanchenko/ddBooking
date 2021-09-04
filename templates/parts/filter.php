<div class="wrapper filter-form">

    <?php 
        $options = get_option('ddbooking_settings_options'); 
        if ( isset( $options['filter_title'] ) ) {
            echo $options['filter_title'];
        }
    ?>

    <div class="container">
        <form action="<?php get_post_type_archive_link('property'); ?>" method="post">
            <select name="ddbooking_location">
                <option value="">Select Location</option>
                <?php echo ddBooking::get_terms_hierarchical('location', $_POST['ddbooking_location']); ?> 
            </select>

            <select name="ddbooking_property-type">
                <option value="">Select property type</option>
                <?php echo ddBooking::get_terms_hierarchical('property-type', $_POST['ddbooking_property-type']); ?> 
            </select>

            <input 
                type="text" 
                placeholder="Max Price" 
                name="ddbooking_price" 
                value="<?php if ( isset($_POST['ddbooking_price'])  ) { echo esc_attr( $_POST['ddbooking_price'] ); } ?>"
            />

            <select name="ddbooking_type">
                <option value="">Select Offer</option>
                <option value="sale" 
                    <?php if( isset($_POST['ddbooking_type']) && $_POST['ddbooking_type'] == 'sale') { echo 'selected'; } ?> >
                    For Sale
                </option>
                <option value="rent" 
                    <?php if( isset($_POST['ddbooking_type']) && $_POST['ddbooking_type'] == 'rent') { echo 'selected'; } ?> >
                    For Rent
                </option>
                <option value="sold" 
                    <?php if( isset($_POST['ddbooking_type']) && $_POST['ddbooking_type'] == 'sold') { echo 'selected'; } ?> >
                    For Sold
                </option>
            </select>

            <select name="ddbooking_agent">
                <option value="">Select Agent</option>
                <?php                  
                    $agents = get_posts(
                        array(
                            'post_type' => 'agent',
                            'numberposts' => -1
                        )
                    );

                    $selected = '';
                    if ( isset($_POST['ddbooking_agent']) ) {
                        $agent_id = $_POST['ddbooking_agent'];
                    }
                    
                    foreach ($agents as $agent) {
                        echo '<option value="' . $agent->ID . '" ' . selected($agent->ID, $agent_id, false) . '>' . $agent->post_title . '</option>';
                    }

                ?>
            </select>

            <input type="submit" name="submit" value="Filter" />

        </form>

    </div>
</div>