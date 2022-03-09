<?php 
add_shortcode( 'mpwscoin', 'wpdocs_baztag_func' );
function wpdocs_baztag_func( $atts, $content = "" ) {

    ob_start();

    if ( is_user_logged_in() ) {
        global $wpdb;
        $customer_id = get_current_user_id();
        $tablename = $wpdb->prefix.'mpwscoin';
        $row_selected = $wpdb->get_results( "SELECT * FROM `wp_mpwscoin` WHERE user_id = $customer_id" );
        $total_coin = 0;
        foreach ($row_selected as $row){
            $total_coin = $total_coin + $row->coin_purchased;
        }
        //echo "<pre>sddssdds"; print_r($row_selected  );
        echo "<h2> Total Coins available here is:  " . $total_coin ." </h2>";
    } else {
        echo 'Hello visitor!';
    }

    

    return ob_get_clean();
}