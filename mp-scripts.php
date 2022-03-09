<?php
add_action( 'admin_enqueue_scripts', 'our_function_name' );

function our_function_name( $hook ){
	$supercoin_value = get_option( 'set_mpwscoin' );
	wp_register_script( 'sd_my_cool_script', plugins_url( 'assets/mp-dashboard.js', __FILE__ ), array( 'jquery' ), '1.0', true );
	wp_enqueue_script( 'sd_my_cool_script' );
	wp_localize_script( 'sd_my_cool_script', 'admin_object',
        array( 
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'set_mpwscoin' => $supercoin_value,
            'abc' => 'xyz'
        )
    );
}
