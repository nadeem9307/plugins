<?php
class Chotu_Cart{

    public function __construct(){
        add_filter( 'gettext', array($this, 'chotu_text_strings') , 20, 3 );
        add_filter( 'woocommerce_cart_needs_payment', '__return_false' );
        add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );
        remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
        add_action( 'woocommerce_before_checkout_form', function(){
            echo '<div class="woocommerce-checkout col-inner has-border">'.do_shortcode( '[woocommerce_cart]' ).'</div>';
        } ,11 );
        // Unset the fields we don't want in a free checkout.
        add_filter( 'woocommerce_checkout_fields',function( $fields ) {
            // Add or remove billing fields you do not want.
            // @link http://docs.woothemes.com/document/tutorial-customising-checkout-fields-using-actions-and-filters/#section-2
            $billing_keys = array(
                'billing_first_name',
                'billing_last_name',
                'billing_email',
                'billing_company',
                'billing_phone',
                'billing_address_1',
                'billing_address_2',
                'billing_city',
                'billing_postcode',
                'billing_country',
                'billing_state',
            );

            // For each unwanted billing key, unset.
            foreach( $billing_keys as $key ) {
                unset( $fields['billing'][$key] );
            }
            $fields['billing']['billing_country']['value']= 'IN';
            $fields['billing']['billing_country']['class']= 'hidden';
            
            return $fields;
        });

        add_action( 'woocommerce_thankyou',function($order_id){
            global $chotu_status;
            if($chotu_status == "B" && is_wc_endpoint_url( 'order-received' ) && isset( $_GET['key'] )){
                $order = wc_get_order( $order_id );
                $order->update_status( 'on-hold' );
                $order_user = update_post_meta($order_id,'_customer_user',chotu_get_captain_id($_COOKIE['captain']));
                $url = chotu_cart_place_wa_order($order_id);
                $start_url = home_url('/oye/?reset_cookie=true');?>
                <script>
                    window.location.href = "<?php echo $url?>";
                    setTimeout(redirect_place_order, 3000);
                    function redirect_place_order(){
                        location.href = "<?php echo $start_url;?>";
                    }
                </script>
                <?php
                //wp_redirect($url);
                exit();
            }
        } ); 
        // add_filter('woocommerce_get_cart_item_from_session', function($item,$values,$key){
        //     if (!isset($values['captain']) ){
        //         $item['captain'] = $_COOKIE['captain'];
        //     }
        //     return $item;
        // }, 1, 3 ); 
        add_action( 'woocommerce_add_to_cart', function (){
            WC()->session->set( '_customer_id', 0 );
            if (!isset(WC()->cart->cart_session_data['captain']) ){
                WC()->session->set( '_customer_id', chotu_get_captain_id($_COOKIE['captain']) );
            }
            
        },10); 
        add_action( 'wp_footer',function() {
            ?>
                <script type="text/javascript">
                    (function($){
                        jQuery('.ajax_add_to_cart').on( 'click', function(e){
                            // Testing output on browser JS console
                            var product_id = jQuery(this).attr('data-product_id');
                            jQuery('.post-'+product_id+ ' .quantity').css('display','none');
                            // Your code goes here
                        });
                    })(jQuery);
                </script>
            <?php
        });
    }
     /**
     * Change text strings
     *
     * @link http://codex.wordpress.org/Plugin_API/Filter_Reference/gettext
     */
    public function chotu_text_strings( $translated_text, $text, $domain ) {
        switch ( strtolower( $translated_text ) ) {
            case 'view cart' :
                $translated_text = "<i class='fa-solid fa-cart-shopping'></i>";
                break;
        }
        return $translated_text;
    }
}
new Chotu_Cart();