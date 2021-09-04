<?php
// custom pagination

function dd_pagination($query) {
    $big = 999999999; 
    $paged = '';
    
    if ( is_singular() ) {
        $paged = get_query_var('page');
    } else {
        $paged = get_query_var('paged');
    }
        
    echo paginate_links( array(
        'base'    => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
        'format'  => '',
        'current' => max( 1, $paged ),
        'total'   => $query->max_num_pages,
        'prev_next' => false,
    ) );
} 
