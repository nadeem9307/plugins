<?php
/**
 * 
 */
class Chotu_Start_Checkout
{
		
	/**
	 * __construct
	 *
	 * @return void
	 */
	public function init()
	{
        add_filter( 'woocommerce_checkout_fields', array( $this, 'chotu_modified_checkout_fields') );
		
		//add_action( 'init', array( $this, 'chotu_disable_checkout_non_loggedin_user' ) );
        add_action( 'woocommerce_before_thankyou', array( $this, 'chotu_start_woocommerce_before_thankyou' ));
        add_action( 'woocommerce_thankyou', array( $this, 'chotu_create_captain_shop' ), 10, 1);
        
        add_action( 'init', array( $this, 'chotu_get_subscription_renewal_url' ) );

        add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );
        add_filter( 'woocommerce_order_button_text', array( $this, 'chotu_start_order_button_text' ), 20 );
	}
    
    /**
     * chotu_modified_checkout_fields
     * Remove unwanted checkout fields from checkout form on checkout page also set country as default is India(IN)
     * @param  mixed $fields
     * @return void
     */
    public function chotu_modified_checkout_fields($fields){
       $user_id = get_current_user_id();
         // Add or remove billing fields you do not want.
            // @link http://docs.woothemes.com/document/tutorial-customising-checkout-fields-using-actions-and-filters/#section-2
            $billing_keys = array(
                'billing_first_name',
                'billing_last_name',
                // 'billing_email',
                'billing_company',
                // 'billing_phone',
                'billing_address_1',
                'billing_address_2',
                'billing_city',
                'billing_postcode',
                'billing_country',
                'billing_state',
            );

            // For each unwanted billing key, unset.
            foreach ($billing_keys as $key) {
                unset($fields['billing'][$key]);
            }
            //unset($fields['shipping']);
            // mandatory from a woo pov to se the billing_country, else the order will fail.
            $fields['billing']['billing_country']['value'] = 'IN';
            $fields['billing']['billing_country']['class'] = 'hidden';
            $fields['billing']['billing_email']['class']   = 'hidden';
            $fields['billing']['billing_phone']['class']   = 'hidden';
            if(is_captain_logged_in()){
                $user = get_userdata( $user_id );
                $fields['billing']['billing_email']['default'] = $user->user_email ?? '';
                $fields['billing']['billing_phone']['default'] = $user->user_login ?? '';
            }
            return $fields;
    }
     
    /**
	 * chotu_disable_checkout_non_loggedin_user
	 * check if is not captain then redirect to whatsapp with start text
     * 
     * if cart page empty redirect to shop page 
	 * @return void
	 */
	public function chotu_disable_checkout_non_loggedin_user() {
		if ( is_checkout() && !is_captain_logged_in() && !isset($_GET['pay_for_order'])) {
			if($send_on_whatsapp_link = get_send_on_whatsapp_link('captain_onboarding_number','start')){
				wp_redirect($send_on_whatsapp_link,302);
				exit;
			}
			wp_redirect( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) );
			exit;
		}
        /**
         * if cart page empty redirect to shop page
         */
        if( is_cart() && WC()->cart->is_empty() ) {
            wp_safe_redirect( get_permalink( wc_get_page_id( 'shop' ) ) );
            exit();
        }
        /**
         * if cart page redirect to checkout page
         */
        if( is_cart() && !WC()->cart->is_empty() ) {
            wp_safe_redirect( get_permalink( wc_get_page_id( 'checkout' ) ) );
            exit();
        }
	}    

	/**
	 * chotu_create_captain_shop
	 * Attach captain rootshop and default data on order recieved page. after showing order success redirect to captain shop page.
	 * @param  mixed $order_id
	 * @return void
	 */
	public function chotu_create_captain_shop( $order_id ) {
        global $current_user;
        // $order_id = 605;
        $new_captain = new Captain_User($current_user->ID);
	    if ( ! $order_id )
	        return;
        if(!get_post_meta($order_id,'_subscription_renewal',true)){
            $plan_rootshop_id = '';
            $language    = '';
            $order       = wc_get_order( $order_id );
            foreach ( $order->get_items() as $item_id => $item_values ) {
                $product_id         = $item_values->get_product_id(); 
                $product            = wc_get_product( $product_id);
                $language           = wc_get_order_item_meta($item_id,'pa_language',true);
                $plan_rootshop_id   = get_post_meta($product_id,'plan_rootshop_id',true);      
            }
            switch_to_blog(get_main_site_id());
            $new_captain->attach_rootshop( $plan_rootshop_id );
            $new_captain->attach_language( $language );
            restore_current_blog();
        }
        $url = $current_user->user_url;
        if($url == ''){
            $url = network_home_url().'/'.$current_user->user_login;
        }
        echo "<script>jQuery(document).ready(function(){
            setTimeout(chotu_redirect_to_captain_shop, 3000);
        })
        function chotu_redirect_to_captain_shop(){
            window.location.href = '".$url."' 
        }
        </script>";
	}
    
    /**
     * chotu_get_subscription_renewal_url
     *
     * @return void
     */
    public function chotu_get_subscription_renewal_url(){
        if(isset($_GET['subscription_id'])){
            $subscription_id = $_GET['subscription_id'];
            $SubscriptionManager = new WC_Subscriptions_Manager();
            $SubscriptionManager::prepare_renewal( $subscription_id );
            // need order renewal id from subscription //

            $renewal_order_id = chotu_get_post_by_meta_value('_subscription_renewal', $subscription_id);
            if($renewal_order_id){
                update_post_meta($renewal_order_id,'_subscription_renewal_early',$subscription_id);
                $renewal_order = wc_get_order( $renewal_order_id );
                $url = $renewal_order->get_checkout_payment_url();
                wp_redirect( $url);
                exit;
            }
            //$subscription = wcs_get_subscription( $subscription->post->ID );
        }
    }    
    /**
     * chotu_start_order_button_text
     * change subscription renewal buttob text to pay now
     * @param  mixed $place_order_text
     * @return void
     */
    public function chotu_start_order_button_text($place_order_text){
        if ( isset( WC()->cart ) && count( wcs_get_order_type_cart_items( 'renewal' ) ) === count( WC()->cart->get_cart() ) ) {
			$place_order_text = _x( 'Pay Now', 'The place order button text while renewing a subscription', 'woocommerce-subscriptions' );
		}
		return $place_order_text;
    }

    public function chotu_start_woocommerce_before_thankyou(){
        echo do_shortcode('[block id="congrats-captain"]');
    }

    
}
$start_checkout = new Chotu_Start_Checkout();
$start_checkout->init();