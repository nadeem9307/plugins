<?php
class Chotu_Cart
{
    public function init()
    {
        /**
         * Remove payment requirement in cart
         */
        add_filter('woocommerce_cart_needs_payment', '__return_false');

        /**
         * Remove notes field in cart page
         */
        add_filter('woocommerce_enable_order_notes_field', '__return_false');

        /**
         * Remove coupon codes in cart page
         */
        remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);

        add_filter( 'woocommerce_checkout_fields',  array( $this, 'chotu_unset_checkout_fields' ) );
        add_filter( 'woocommerce_order_button_html', array( $this, 'chotu_add_place_order_button' ) );
       
        add_action( 'woocommerce_add_to_cart', array($this, 'chotu_add_captain_id_in_cart' ), 10);
        add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'chotu_update_cart_count' ) );
        add_action( 'woocommerce_add_to_cart', array( $this, 'chotu_add_default_qty' ), 10, 6);
        add_filter( 'woocommerce_get_cart_item_from_session', array($this, 'chotu_cart_item_from_session' ), 10, 3);
        add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'chotu_chotu_save_order_item_meta'), 10, 4);
        add_action( 'woocommerce_thankyou', array( $this, 'chotu_after_checkout_place_order') );
    }
    
    /**
     * chotu_save_order_item_meta
     * Save cart item custom meta as order item meta data and display it everywhere on orders and email notifications.
     * @param  mixed $item
     * @param  mixed $cart_item_key
     * @param  mixed $values
     * @param  mixed $order
     * @return void
     */
    public function chotu_chotu_save_order_item_meta( $item, $cart_item_key, $values, $order ) {
        if ( isset($values['product_qty'])) {
            $item->update_meta_data( 'product_qty', $values['product_qty'] );
        }
        if ( isset($values['product_note'])) {
            $item->update_meta_data( 'product_note', $values['product_note'] );
        }
        unset($_SESSION);
    }
    
    /**
     * Unset the fields we don't want in a free checkout.
     * @link http://docs.woothemes.com/document/tutorial-customising-checkout-fields-using-actions-and-filters/#section-2
     */
    public function chotu_unset_checkout_fields($fields) {
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
        foreach ($billing_keys as $key) {
            unset($fields['billing'][$key]);
        }
        // mandatory from a woo pov to se the billing_country, else the order will fail.
        $fields['billing']['billing_country']['value'] = 'IN';
        $fields['billing']['billing_country']['class'] = 'hidden';

        return $fields;
    }
        
    /**
     * chotu_add_place_order_button
     *
     * @param  mixed $button_html
     * @return void
     */
    public function chotu_add_place_order_button( $button_html ) {
        global $chotu_status;
        if($chotu_status == 'B'){
            ?>
            <button style="background: url('<?php echo CHOTU_PLUGIN_DIR;?>assets/images/order-on-whatsapp.png') no-repeat; 
                border: none;background-color: transparent !important; 
                height: 100px; width: 100%;background-position: center !important;background-size: contain !important;" 
                type="submit" class="button alt wc-forward" name="woocommerce_checkout_place_order"
                id="place_order" value="Place order" data-value="Place order"></button>
            <?php } ?>
            
        <?php
        return '';
    }

    /**
     * before showing thank you:
     * set order_status = on-hold and
     * set user_ID to captain
     * place order on woo and then call the whatsapp order function
     */
    public function chotu_after_checkout_place_order($order_id) {
        global $chotu_status, $woocommerce,$chotu_current_captain;
        if ($chotu_status == "B" && is_wc_endpoint_url('order-received') && isset($_GET['key'])) {
            $order = wc_get_order($order_id);
            $order->update_status('on-hold');
            if ($chotu_current_captain) {
                update_post_meta($order_id, '_customer_user', $chotu_current_captain->ID);
                $url = chotu_cart_place_wa_order($order_id);
                session_unset();
                session_destroy(); ?>
                <script>
                    window.location.href = "<?php echo $url ?>";
                </script>
                <?php
                exit();
            }
        }
    }

    /**
     * on add to cart, add captain_ID to cart session
    */
    public function chotu_add_captain_id_in_cart() {
        
        WC()->session->set('_customer_id', 0);
        if (!isset(WC()->cart->cart_session_data['captain'])) {
            $user = get_user_by( 'login', $_COOKIE['captain'] );
            if($user){
                WC()->session->set('_customer_id', $user->ID);
            }
            
        }
    }

    /**
     * Update cart count through AJAX
    */
    public function chotu_update_cart_count( $fragments ) {

        ob_start();
        
        $cart_count = WC()->cart->cart_contents_count;
        $cart_url   = wc_get_cart_url();
        
        ?>
        <a class="cart-contents" href="<?php echo $cart_url; ?>">
            <i class="fa-solid fa-bag-shopping" style="color: var(--primary); font-size: 2rem; vertical-align: middle;"></i>
        <?php
        if ( $cart_count > 0 ) {
            ?>
            <div class="cart-contents-count"><?php echo $cart_count; ?></div>
            <?php            
        }
            ?></a>
        <?php

        $fragments['a.cart-contents'] = ob_get_clean();
        
        return $fragments;
    }

    /**
        * chotu_add_default_qty
        * on add to cart, updates the default product quantity in the cart session
        * @param  mixed $cart_item_key
        * @param  mixed $product_id
        * @param  mixed $quantity
        * @param  mixed $variation_id
        * @param  mixed $variation
        * @param  mixed $cart_item_data
        * @return void
        */

    public function chotu_add_default_qty( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data  ) {
        $options = '';
        if($measurment_key = get_post_meta($product_id, 'product_order_quantity', true)){
            $options        = chotu_get_option($measurment_key);
        }else{
            $options        = chotu_get_option('default_quantity');
        }
        if (!isset($_SESSION)){
            session_start();
        }
        if($options !=''){
            $poq_arr = explode("|", $options);
            $_SESSION['qty_'.$cart_item_key] = trim($poq_arr[0]);
        }
    }

    /**
     * chotu_cart_item_from_session
     * retrieve the product quantity and note from normal session and pushes it to cart session
     * @param  mixed $data
     * @param  mixed $values
     * @param  mixed $key
     * @return void
     */
    public function chotu_cart_item_from_session( $data, $values, $key ) {
        if (!isset($_SESSION)){
            session_start();
        }
        $data['product_qty'] = isset( $_SESSION['qty_'.$key] ) ? $_SESSION['qty_'.$key] : '';
        $data['product_note'] = isset( $_SESSION['product_note_'.$key] ) ? $_SESSION['product_note_'.$key] : '';
        return $data;
    }

}
add_action('init', 'chotu_load_cart_class' );
/**
 * chotu_load_cart_class
 * create object of class and call the init function to run the class methods
 * @return void
 */
function chotu_load_cart_class(){
    $cart_class = new Chotu_Cart();
    $cart_class->init();
}

