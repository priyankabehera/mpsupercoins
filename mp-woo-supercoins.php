<?php
/**
 * Plugin Name: MP WooCommerce SuperCoins
 * Description: MP WooCommerce SuperCoins - Points and Rewards
 * Version: 1.0
 * Author: Priyankabehera, Manoranjan 
 * Text Domain: mpwscoin 
 * 
 * @package MP WooCommerce SuperCoins
 */

register_activation_hook( __FILE__, 'mpwscoin_activate' );

function mpwscoin_activate(){

    global $wpdb;

    $attributes = wc_get_attribute_taxonomies();

    

        $args = array(
            'slug'    => 'mpwscoin',
            'name'   => __( 'SuperCoins', 'mpwscoin' ),
            'type'    => 'text',
            'orderby' => 'menu_order',
            'has_archives'  => false,
        );

       // $result = wc_create_attribute( $args );
       /// wp_insert_term( '1', 'pa_mpwscoin', array( 'slug' => "1" ) );
        
       $wordpress_page = array(
        'post_title'    => 'MPSuperCoin',
        'post_content'  => '<!-- wp:shortcode -->[mpwscoin]<!-- /wp:shortcode -->',
        'post_status'   => 'publish',
        'post_author'   => 1,
        'post_type' => 'page'
         );
       wp_insert_post( $wordpress_page );


       
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE `{$wpdb->base_prefix}mpwscoin` (
        id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id bigint(20) UNSIGNED NOT NULL,
        order_id bigint(20) UNSIGNED NOT NULL,
        coin_purchased bigint(20) UNSIGNED NOT NULL,
        coin_purchased_at datetime NOT NULL,
        expiry_at datetime NOT NULL,
        PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

    

}

//add_filter('woocommerce_product_get_attributes', 'mpwscoin_edit_additional_informations', 20, 2);

function mpwscoin_edit_additional_informations( $attributes, $product ){

    $attribute = $attributes['pa_mpwscoin'];

    echo "<pre>";
    print_r($attributes);exit;
}

// Display Fields using WooCommerce Action Hook
add_action( 'woocommerce_product_options_general_product_data', 'woocom_general_product_data_custom_field' );

function woocom_general_product_data_custom_field() {
    // Create a custom text field
   
  
    // Number Field
    woocommerce_wp_text_input( 
      array( 
        'id' => 'mpwscoin', 
        'label' => __( 'Get SuperCoins', 'woocommerce' ), 
        'placeholder' => '', 
        'description' => __( 'These coins will linked with this product', 'woocommerce' ),
        'type' => 'number', 
        'custom_attributes' => array(
           'step' => 'any',
           'max' => '15'
        ) 
      )
    );

    // Number Field
    woocommerce_wp_text_input( 
        array( 
          'id' => 'mpwscoin_use', 
          'label' => __( 'Use Wallet Supercoin', 'woocommerce' ), 
          'placeholder' => '', 
          'description' => __( 'These coins will linked with this product', 'woocommerce' ),
          'type' => 'number', 
          'custom_attributes' => array(
             'step' => 'any',
             'max' => '15'
          ) 
        )
      );
  
      // Number Field
    woocommerce_wp_text_input( 
        array( 
          'id' => '_bestprice', 
          'label' => __( 'Price after supercoin', 'woocommerce' ), 
          'placeholder' => '', 
          'description' => __( 'Price calculated based on supercoin used while purchasing the pruduct.', 'woocommerce' ),
          'type' => 'number', 
          'custom_attributes' => array(
             'step' => 'any',
             'max' => '15'
          ) 
        )
    );

  }

  // Hook to save the data value from the custom fields
add_action( 'woocommerce_process_product_meta', 'woocom_save_general_proddata_custom_field' );

/** Hook callback function to save custom fields information */
function woocom_save_general_proddata_custom_field( $post_id ) {
    // Save Text Field
    
     
    // Save Number Field
    $number_field = $_POST['mpwscoin'];
    if( ! empty( $number_field ) ) {
       update_post_meta( $post_id, 'mpwscoin', esc_attr( $number_field ) );
    }

    // Save Number Field
    $number_field = $_POST['mpwscoin_use'];
    if( ! empty( $number_field ) ) {
       update_post_meta( $post_id, 'mpwscoin_use', esc_attr( $number_field ) );
    }

    if( isset( $_POST['_bestprice'] ) ){
        update_post_meta( $post_id, '_bestprice', sanitize_text_field( $_POST['_bestprice'] ) );
    }
    
}


add_filter( 'woocommerce_product_tabs', 'woo_custom_product_tabs' );
function woo_custom_product_tabs( $tabs ) {


    //Attribute Description tab
    $tabs['attrib_desc_tab'] = array(
        'title'     => __( 'SuperCoins', 'woocommerce' ),
        'priority'  => 100,
        'callback'  => 'woo_attrib_desc_tab_content'
    );
    return $tabs;

}

// New Tab contents

function woo_attrib_desc_tab_content() {

    global $post;

    $mpwscoin = get_post_meta( $post->ID, 'mpwscoin', true );


    echo '<h2> No of Super Coins after order completion: '.$mpwscoin.' </h2>';
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
        echo '<h2> No of Super Coins in your supercoin wallet: '.$total_coin.' </h2>';
    }
    
}

//add_filter( 'woocommerce_get_price_html', 'njengah_text_after_price', 999, 2 );


function njengah_text_after_price($price,$product){

    global $post;
    $id = $product->get_id();

    $mpwscoin = get_post_meta( $id, 'mpwscoin', true );

    $text_to_add_after_price  = ' + ' . $mpwscoin . ' Super coins'; //change text in bracket to your preferred text 
		  
	return $price .   $text_to_add_after_price;
		  
} 

//add_filter( 'woocommerce_cart_item_price', 'njengah_text_after_price_cart', 999, 2 );
function njengah_text_after_price_cart($price,$product){

    global $post;
    $id = $product['product_id'];
    $quantity = $product['quantity'];
    $mpwscoin = get_post_meta( $id, 'mpwscoin', true );

    $total_supercoin = $mpwscoin*$quantity;

    //echo "<pre>";print_r($total_supercoin);

    

    $text_to_add_after_price  = ' + ' . $total_supercoin . ' Total Super coins'; //change text in bracket to your preferred text 
		  
	return $price .   $text_to_add_after_price;
		  
} 

// Note the low hook priority, this should give to your other plugins the time to add their own items...
add_filter( 'woocommerce_account_menu_items', 'add_my_menu_items', 99, 1 );

function add_my_menu_items( $items ) {
    $my_items = array(
    //  endpoint   => label
        'mpsupercoin' => __( 'supercoin Wallet', 'my_plugin' ),
    );

    $my_items = array_slice( $items, 0, 1, true ) +
        $my_items +
        array_slice( $items, 1, count( $items ), true );

    return $my_items;
}


add_action( 'woocommerce_order_status_completed', 'wc_send_order_to_mypage', 1 );
function wc_send_order_to_mypage( $order_id ) {
    $order = wc_get_order( $order_id );
    $supercoin_array = array();


    //echo "<pre>"; print_r($order->get_items());exit;

    

    if ( count( $order->get_items() ) > 0 ) {
        
		foreach ( $order->get_items() as $item ) {            
			$product = $item->get_product();
            $product_id = $product->get_id();
            $product_qty =  $item->get_quantity();
            $mpwscoin = get_post_meta( $product_id, 'mpwscoin', true ); 
            $all_mpwscoin = $mpwscoin*$product_qty;  
            $supercoin_array[] = $all_mpwscoin;
		}

        global $wpdb;
        $tablename = $wpdb->prefix.'mpwscoin';

        $user_id = $order->get_user_id(); 
        $order_date = $order->order_date;

        $total_coins = array_sum($supercoin_array);

        //echo "<pre>"; print_r($total_coins);exit;

        $inserid = $wpdb->insert( $tablename, 
            array(
                'id' => '',
                'user_id' => $user_id, 
                'order_id' => $order_id,
                'coin_purchased' => $total_coins, 
                'coin_purchased_at' => $order_date,
                'expiry_at' => $order_date,                    
            ),
            array( '%d', '%d', '%d', '%d', '%s', '%s' ),
        );

	}

    // global $wp_filter;
    // print_r($wp_filter);
    //exit;

}

include_once dirname( __FILE__ ) . '/mp-scripts.php';
include_once dirname( __FILE__ ) . '/mp-shortcode.php';


has_action( 'woocommerce_order_status_completed', 'wc_send_order_to_mypage' );


/**
 * Create the section beneath the products tab
 **/
add_filter( 'woocommerce_get_sections_products', 'wcslider_add_section' );
function wcslider_add_section( $sections ) {
	
	$sections['mpwscoin'] = __( 'MP SuperCoins', 'text-domain' );
	return $sections;
	
}

/**
 * Add settings to the specific section we created before
 */
add_filter( 'woocommerce_get_settings_products', 'wcslider_all_settings', 10, 2 );
function wcslider_all_settings( $settings, $current_section ) {
	/**
	 * Check the current section is what we want
	 **/
	if ( $current_section == 'mpwscoin' ) {
		$settings_slider = array();
		// Add second text field option
		$settings_slider[] = array(
			'name'     => __( 'Set Price per Supercoin', 'text-domain' ),
			'desc_tip' => __( 'This will add a title to your slider', 'text-domain' ),
			'id'       => 'set_mpwscoin',
			'type'     => 'number',
			'desc'     => __( 'Any title you want can be added to your slider with this option!', 'text-domain' ),
		);
		
		$settings_slider[] = array( 'type' => 'sectionend', 'id' => 'mpwscoin' );
		return $settings_slider;
	
	/**
	 * If not, return the standard settings
	 **/
	} else {
		return $settings;
	}
}


function cw_change_product_price_display( $price ) {

    global $post;

    $mpwscoin_use = get_post_meta( $post->ID, 'mpwscoin_use', true );
    $supercoin_value = get_option( 'set_mpwscoin' );

    $product = wc_get_product( $post->ID );
    $product_price = $product->get_price();

    //echo "<pre>";print_r(); 
    $price_supercoin = $mpwscoin_use * $supercoin_value;
    $total_price_diff_supercoin = $product_price-$price_supercoin;
    $price .= ' OR Pay: ' . $total_price_diff_supercoin .' + '. $mpwscoin_use . ' Super coin';
    return $price;
}
add_filter( 'woocommerce_get_price_html', 'cw_change_product_price_display' );
//add_filter( 'woocommerce_cart_item_price', 'cw_change_product_price_display' );


add_action( 'woocommerce_after_add_to_cart_button', 'additional_single_product_button', 20 );
function additional_single_product_button() {
    global $product;

    // Output
    //echo '<br><a rel="no-follow" class="'.$class.'" style="'.$style.'">'.$name.'</a>';

   // echo '<button> User Spercoin </button>';
   
}

// Add a 'Get better price' additional button and a hidden field below single add to cart button
add_action( 'woocommerce_before_add_to_cart_button', 'before_add_to_cart_button' );
function before_add_to_cart_button() {
    global $product;

    // Get your product 'bestpprice' custom field
    $bestprice = get_post_meta( $product->get_id(), '_bestprice', true);

    if( ! empty($bestprice) ):

    $bestprice = wc_get_price_to_display( $product, array( 'price' => $bestprice ) );
    $reg_price = wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) );
    $range = wc_format_sale_price( $reg_price, $bestprice );
    ?>
    <!-- The button and hidden field --> 
    <div class="bestprice-wrapper"><br>
        <a href="" class="get_bestprice button alt" id="get_bestprice"><?php _e('Use supercoin'); ?></a>
        <input type="hidden" name="bestprice" id="bestprice" class="bestprice" value="" />
    </div>
    <!-- The jQuery code --> 
    <script type="text/javascript">
        (function($){
            var b = '<?php echo $bestprice; ?>',
                i = 'input[name=bestprice]',
                p = 'p.price',
                r = '<?php echo $range; ?>',
                t = 'a#get_bestprice'
                u = true;
            $(t).click( function(e){
                e.preventDefault();
                if(u){
                    $(p).html(r);  // Replacing price with the range
                    $(i).val(b);  // Set the best price in hidden input field
                    u = false;   // Disable button
                    $(t).text('Better Price active'); // change button text
                    $(t).removeClass('alt'); // Remove button 'alt' class for styling
                }
            });
        })(jQuery);
    </script>
    <?php
    endif;
}

// Add custom fields data to cart items
add_filter( 'woocommerce_add_cart_item_data', 'custom_add_cart_item_data', 20, 2 );
function custom_add_cart_item_data( $cart_item, $product_id ){

    if( ! isset( $_POST['bestprice'] ) )
        return $cart_item;

    if( ! empty( $_POST['bestprice'] ) ){
        $cart_item['custom_data']['bestprice'] =  (float) esc_attr( $_POST['bestprice'] );
        $new_supercoin_array = array('supercoin' => '1');

        array_merge($cart_item,$new_supercoin_array);

    }
    return $cart_item;
}

// Replacing cart item price with 'bestprice'
add_action( 'woocommerce_before_calculate_totals', 'set_cart_item_bestprice', 20, 1 );
function set_cart_item_bestprice( $cart ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
        return;

    // Loop through cart items
    foreach ( $cart->get_cart() as $cart_item ){
        if( isset( $cart_item['custom_data']['bestprice'] ) ){
            // Set the calculated item price (if there is one)
            $cart_item['data']->set_price( (float) $cart_item['custom_data']['bestprice'] );
        }
    }
}

// Change add to cart link by a link to the product in Shop and archives pages for bestprice enabled option
add_filter( 'woocommerce_loop_add_to_cart_link', 'bestprice_loop_add_to_cart_button', 10, 2 );
function bestprice_loop_add_to_cart_button( $button, $product  ) {

    // Get your product 'bestpprice' custom field
    $bestprice = get_post_meta( $product->get_id(), '_bestprice', true);

    // Only for enabled "bestprice" option price.
    if( ! empty( $bestprice ) ){
        $button_text = __( "View product", "woocommerce" );
        $button = '<a class="button" href="' . $product->get_permalink() . '">' . $button_text . '</a>';
    }
    return $button;
}

add_filter( 'woocommerce_cart_item_price', 'supercoin_product_price_display', 999, 2 );
function supercoin_product_price_display($price,$product){

    global $post;
    $id = $product['product_id'];
    $quantity = $product['quantity'];
    $mpwscoin = get_post_meta( $id, 'mpwscoin_use', true );
    $total_supercoin = $mpwscoin*$quantity;
    $text_to_add_after_price  = ' + ' . $mpwscoin . ' Total Super coins'; //change text in bracket to your preferred text 
		  
	return $price .   $text_to_add_after_price;

}

add_action('woocommerce_thankyou', 'enroll_student', 10, 1);
function enroll_student( $order_id ) {
    if ( ! $order_id )
        return;

    // Allow code execution only once 
    //if( ! get_post_meta( $order_id, '_thankyou_action_done', true ) ) {

        // Get an instance of the WC_Order object
        $order = wc_get_order( $order_id );

        //echo "<pre>"; print_r($order);

        // Get the order key
        $order_key = $order->get_order_key();

        // Get the order number
        $order_key = $order->get_order_number();

       

        // Loop through order items
        foreach ( $order->get_items() as $item_id => $item ) {

            echo "<pre>"; print_r( $item);

            // Get the product object
            $product = $item->get_product();

            // Get the product Id
            $product_id = $product->get_id();

            // Get the product name
            $product_id = $item->get_name();
        }

        // Output some data
        echo '<p>Order ID: '. $order_id . ' — Order Status: ' . $order->get_status() . ' — Order is paid: ' . $paid . '</p>';

        // Flag the action as done (to avoid repetitions on reload for example)
        $order->update_meta_data( '_thankyou_action_done', true );
        $order->save();
    //}
}
