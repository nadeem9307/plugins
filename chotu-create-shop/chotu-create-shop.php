<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              chotu.com
 * @since             1.1
 * @package           Chotu
 *
 * @wordpress-plugin
 * Plugin Name:       Chotu Create Bulk Shop
 * Plugin URI:        chotu.com
 * Description:       Chotu Create Bulk Shop
 * Version:           1.0.0
 * Author:            Mohd Nadeem
 * Author URI:        chotu.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       chotu
 * Domain Path:       /languages
 */

add_action('rest_api_init', 'chotu_create_shop_rest_api_init');
function chotu_create_shop_rest_api_init()
{
    register_rest_route('api/v3', '/createshop/', array(
        'methods' => 'post',
        'callback' => 'chotu_snippet_api_createshop',
        'permission_callback' => 'chotu_snippet_get_permission_callback'
    ));
	register_rest_route('api/v3', '/updatetrial/', array(
        'methods' => 'post',
        'callback' => 'chotu_snippet_api_trial_update',
        'permission_callback' => 'chotu_snippet_get_permission_callback'
    ));
}

/**
 * chotu_snippet_api_createshop
 *
 * @param  mixed $request
 * @return void
 */
function chotu_snippet_api_createshop($request){
	$file = $request->get_file_params();

	if (empty($file['file'])) {
		return new WP_Error('invalid_file', 'No file uploaded.', array('status' => 400));
	}
	$csv_file = $file['file'];
	if (($handle = fopen($csv_file['tmp_name'], 'r')) !== false) {
		$header = fgetcsv($handle, 1000, ',');
		$errors = array();
		$success = array();
		while (($data = fgetcsv($handle, 1000, ',')) !== false) {
			$user_data = array();
			foreach ($header as $index => $column) {
				$user_data[$column] = sanitize_text_field($data[$index]);
			}
			$product = wc_get_product( $user_data['variation_id'] );
			if($product){
				$parent_id = $product->get_parent_id();
				switch_to_blog(get_main_site_id());
				$user_id = chotu_snippet_create_captain($user_data);
				restore_current_blog();
				if(is_numeric($user_id)){
					$plan_rootshop_id   = get_post_meta($parent_id,'plan_rootshop_id',true); 
					$order_id = chotu_snippet_create_order($user_id, $parent_id, $user_data['variation_id'],$user_data['captain_language']);
					if($order_id){
						chotu_snippet_create_subscription($order_id, $user_data['variation_id']);
						switch_to_blog(get_main_site_id());
						chotu_snippet_attach_rootshop($user_id, $plan_rootshop_id, $user_data['captain_shop_name']);
						chotu_snippet_attach_language($user_id, $user_data['captain_language']);
						restore_current_blog();
						$success[] = __('User created by '.$data['captain_mobile_number']. ' user_id '.$user_id);
					}
				}else{
					$errors[] = __($user_id . ' ' .$user_data['captain_mobile_number']);
				}
			}else{
				$errors[] = __('User not created by '.$user_data['variation_id'] . ' variation id not found');
			}
		}
		return new WP_REST_Response(
			array(
                'success' => $success,
                'errors' => $errors,
				'status' => 200,
			)
		);
	}
}
/**
 * chotu_snippet_create_order
 *
 * @param  mixed $product_id
 * @param  mixed $user_id
 * @return void
 */
function chotu_snippet_create_order($user_id, $product_id, $variation_id, $captain_language){
	$pa_language = false;
	$order = wc_create_order();
	$order->set_customer_id( $user_id );
	$order->add_product( wc_get_product( $variation_id ), 1 );
	$order->set_status( 'wc-processing', 'Order is created programmatically' );
	// Get the order items
	$order_items = $order->get_items();
	// Get the variation object by ID
	$variation = new WC_Product_Variation($variation_id);

	if ($variation) {
		// Get the variation's attributes
		$variation_attributes = $variation->get_variation_attributes();
		if (array_key_exists('attribute_pa_language', $variation_attributes)) {
			$pa_language = true;
		}
	}
	// Set the price of each item to 0
	foreach ($order_items as $item_id => $item) {
		if($pa_language){
			$item->add_meta_data('pa_language', $captain_language, true);
		}
		$item->set_subtotal(0);
		$item->set_total(0);
		$item->set_subtotal_tax(0);
		$item->set_total_tax(0);
	}
	$order->calculate_totals();
	$order->save();
	return $order->get_id();
}
/**
 * chotu_snippet_create_subscription
 *
 * @param  mixed $order
 * @param  mixed $product_id
 * @return void
 */
function chotu_snippet_create_subscription($order, $product_id){
	if ( ! is_object( $order ) ) {
		$order = new WC_Order( $order );
	}
	$subscription = wcs_create_subscription(array(
		'order_id' => $order->get_id(),
		'billing_period' => WC_Subscriptions_Product::get_period( $product_id ),
		'billing_interval' => WC_Subscriptions_Product::get_interval( $product_id )
	));
	if( is_wp_error( $subscription ) ){
		return false;
	}
	$start_date = gmdate( 'Y-m-d H:i:s' );
	$note = ! empty( $note ) ? $note : __( 'Programmatically added order and subscription.' );
	$product = wc_get_product( $product_id );
	$item_id = $subscription->add_product(
		$product,
		1,
		array(
			'variation' => ( method_exists( $product, 'get_variation_attributes' ) ) ? $product->get_variation_attributes() : array(),
			'totals'    => array(
				'subtotal'     => $product->get_price(),
				'subtotal_tax' => 0,
				'total'        => $product->get_price(),
				'tax'          => 0,
				'tax_data'     => array(
					'subtotal' => array(),
					'total'    => array(),
				),
			),
		)
	);

	if ( ! $item_id ) {
		throw new Exception( __( 'Error: Unable to add product to created subscription. Please try again.', 'woocommerce-subscriptions' ) );
	}
	$language           = wc_get_order_item_meta($item_id,'pa_language',true);
	// WC_Subscriptions_Manager::create_pending_subscription_for_order($order, $variation_id, $args = array() );
	$dates = array(
		'trial_end'    => WC_Subscriptions_Product::get_trial_expiration_date( $product_id, $start_date ),
		'next_payment' => WC_Subscriptions_Product::get_first_renewal_payment_date( $product_id, $start_date ),
		'end'          => WC_Subscriptions_Product::get_expiration_date( $product_id, $start_date ),
	);
	$subscription->update_dates( $dates );
	$subscription->update_status( 'active', $note, true );
	$subscription->set_total( 0, 'tax' );
	$subscription->set_total( $product->get_price(), 'total' );
	return $language;
}
/**
 * chotu_snippet_create_captain
 *
 * @param  mixed $data
 * @return void
 */
function chotu_snippet_create_captain($data){
	$mobile_number = substr($data['captain_mobile_number'], -10, 10);
	$userdata = array(
		'user_login' 	=> $mobile_number,
		'user_email' 	=> $mobile_number . '@chotu.com',
		'user_nicename' => $mobile_number,
		'user_pass' 	=> wp_generate_password(12, false, false),
		'user_url' 		=> get_site_url(1,'/','https').$mobile_number,
		'role'			=> 'captain'
	);
	$user_id = wp_insert_user($userdata);
	flush_rewrite_rules();
	if (!is_wp_error($user_id)) {
            $user = get_user_by('id', $user_id);
            // add user too site 2 with captain user role
            add_user_to_blog( 2,  $user_id , 'captain');
			return $user_id;
	}else{
		// echo 'user '.$mobile_number . ' already exist., ';
		return $user_id->get_error_message();
	}
} 
 /**
  * chotu_snippet_attach_rootshop
  *
  * @param  mixed $user_id
  * @param  mixed $plan_rootshop_id
  * @param  mixed $captain_shop_name
  * @return void
  */
 function chotu_snippet_attach_rootshop($user_id, $plan_rootshop_id,$captain_shop_name){
	$rootshop = chotu_get_cpost($plan_rootshop_id, 'rootshop');
	if( is_null($rootshop) ){
		 return false;
	}
	 //$captain_shop_name = $this->captain_shop_name($this->first_name, $rootshop->post_title);
	wp_update_user(array('ID' => $user_id, 'display_name' => $captain_shop_name));
	$user_object = new Captain_User($user_id);
	$metas = $user_object->prepare_item_for_user_meta($rootshop);
	foreach($metas as $key => $value) {
		update_user_meta( $user_id, $key, $value );
	}
	return true;
 }
/**
 * chotu_snippet_attach_language
 *
 * @param  mixed $user_id
 * @param  mixed $language
 * @return void
 */
function chotu_snippet_attach_language($user_id, $language){
	if (!chotu_snippet_validate_language($language)) {
		return false;
	}
	update_user_meta($user_id, 'captain_language', $language);
} 
/**
 * chotu_snippet_validate_language
 *
 * @param  mixed $language
 * @return void
 */
function chotu_snippet_validate_language($language){
        $chotu_languages = array('as', 'bn', 'bho', 'doi', 'en', 'hi', 'mr', 'te', 'ta', 'gu', 'ur', 'kn', 'ml', 'pa', 'kok', 'mai', 'mni', 'lus', 'ne', 'or', 'sa', 'sd');
        if ($language == '' || !in_array($language, $chotu_languages)) {
            return false;
        }
        return true;
}

/**
 * chotu_snippet_api_trial_update
 * update trial of subscriptin by given subscription id ex. id is coming from csv file
 * @param  mixed $request
 * @return void
 */
function chotu_snippet_api_trial_update($request){
	$file = $request->get_file_params();

	if (empty($file['file'])) {
		return new WP_Error('invalid_file', 'No file uploaded.', array('status' => 400));
	}
	$errors = array();
	$success = array();
	$csv_file = $file['file'];
	if (($handle = fopen($csv_file['tmp_name'], 'r')) !== false) {
		$header = fgetcsv($handle, 1000, ',');
		while (($data = fgetcsv($handle, 1000, ',')) !== false) {
			$user_data = array();
			foreach ($header as $index => $column) {
				$user_data[$column] = sanitize_text_field($data[$index]);
			}
			if(isset($user_data['captain_mobile_number'])){
				$mobile_number = substr($user_data['captain_mobile_number'], -10, 10);
				if (username_exists($mobile_number)) {
					$result = chotu_snippet_extend_subscription_trial($user_data['captain_mobile_number'],$user_data['trial_extended']);
					if(is_wp_error( $result )){
						$errors[] = $result->get_error_message();
					}else{
						$success[] = $user_data['captain_mobile_number'];
					}
				}else{
					$errors[] = __('User not found.','error');
				}
			}
		}
		return new WP_REST_Response(
			array(
                'success' => $success,
                'errors' => $errors,
				'status' => 200,
			)
		);
	}
	
}
/**
 * chotu_snippet_extend_subscription_trial
 * update trial and next payment date by subscription id and trial extend days
 * @param  mixed $subscription_id
 * @param  mixed $trial_extended
 * @return void
 */
function chotu_snippet_extend_subscription_trial($mobile_number, $trial_extended){
	// Load WooCommerce Subscriptions functions
	if (class_exists('WC_Subscriptions') && is_numeric($trial_extended)) {
		// Load the subscription object
		if(function_exists('chotu_get_captain_subscription')){
			$user = get_user_by( 'login', $mobile_number );
			$subs = chotu_get_captain_subscription($user->ID);
			if($subs){
				$subscription = wcs_get_subscription($subs->ID);

				if ($subscription) {
					// Calculate the new trial end date (e.g., extend by 30 days)
		
					// $new_trial_end_date = date('Y-m-d H:i:s', strtotime('+'.$trial_extended.' days', strtotime($subscription->get_date('trial_end'))));
					// $new_next_payment = date('Y-m-d H:i:s', strtotime('+'.$trial_extended.' days', strtotime($subscription->get_date('next_payment'))));
		
					$new_trial_end_date = date('Y-m-d H:i:s', strtotime('+'.$trial_extended.' days'));
					$new_next_payment = date('Y-m-d H:i:s', strtotime('+'.$trial_extended.' days'));
		
		
					// Update the trial end date
					$subscription->update_dates(array('trial_end' => $new_trial_end_date,'next_payment' => $new_next_payment));
					$subscription->save();
					return true;
				} else {
					return new WP_Error('invalid_subscription_id', 'Invalid subscription id: '.$subs->ID);
					// Output an error message if the subscription is not found
				}
			}else{
				return new WP_Error('invalid_subscription_id', 'subscription not found '.$mobile_number);
			}
		}
		
	}
}
/**
 * check permission for request have authenticated or not
 * 
 * get_my_query_permission_callback
 * */
function chotu_snippet_get_permission_callback($request) {
    $chotu_captain_api_key = $request->get_header('chotu_captain_api_key');
	switch_to_blog(get_main_site_id());
	$key = chotu_get_option('chotu_captain_api_key');
	restore_current_blog();
	if ( $key != trim($chotu_captain_api_key) ) {
		return new WP_Error(
			'rest_user_cannot_view',
			__( '❌ You are not authorized to access this API.' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}
	return true;
}