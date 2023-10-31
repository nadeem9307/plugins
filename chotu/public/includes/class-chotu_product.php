<?php

/**
 * Chotu_Product_Public
 */
class Chotu_Product_Public
{
    /**
     * __construct
     *
     * @return void
     */
    public function init(){

        /**
         * to hide the product sku from being displayed on the page
         */
        add_filter( 'wc_product_sku_enabled', '__return_false');
        
        /**
         * remove price from all places. this removes the add to cart button too.
         */
        add_action( 'wp', array($this, 'chotu_remove_price'));

        /**
         * show cart by $chotu_status
         */
        add_action( 'wp', array($this, 'chotu_show_cart_by_status'),5);
        
        /**
         * Force add to cart in product loop as remove price removes this CTA.
         */
        add_action( 'woocommerce_after_shop_loop_item', array($this, 'chotu_product_loop_CTA'), 10);
        
        /**
         * Show Ask Price on the Product Single Page
         */
        add_action( 'woocommerce_single_product_summary', array($this, 'chotu_product_single_show_price'), 10);
        add_action( 'wp_head', array( $this, 'chotu_set_product_og' ), 10);
        add_action( 'wp_head', array( $this, 'chotu_remove_add_to_cart_button' ), 20); 
        add_action( 'woocommerce_before_shop_loop_item', array( $this, 'chotu_show_favorite_icon' ), 10);
        add_action( 'woocommerce_single_product_summary', array( $this, 'chotu_show_favourite_single_product_summary' ), 10);
        /**
         * Force all products to be bought individually only 
         * Hides the quantity selector in product single page
         */
        add_filter( 'woocommerce_is_sold_individually', function($return, $product){
            return true;
        }, 10, 2 );

        /**
         * to set the primary color of the text for the product loop
         */
        add_filter( 'woocommerce_product_loop_title_classes', function ($classes) {
            $classes .= ' primary-color';
            return $classes;
        });

        /**
         * to return the size by which the product has to be displayed in the search
         * https://fibosearch.com/documentation/tips-tricks/how-to-change-image-sizes/
         */
        add_filter( 'dgwt/wcas/suggestion_details/product/thumb_size', function ($size) {
            return 'woocommerce_gallery_thumbnail';
        } );

        /**
         * to fetch the thumbnail URL of the product whose product ID is passed
         * https://fibosearch.com/documentation/tips-tricks/how-to-change-image-size-in-the-search-suggestions/
         */
        add_filter( 'dgwt/wcas/product/thumbnail_src', function ($src, $product_id) {
            $thumbnail_url = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'woocommerce_gallery_thumbnail');
            if (is_array($thumbnail_url) && !empty($thumbnail_url[0])) {
                $src = $thumbnail_url[0];
            }
            return $src;
        }, 10, 2);

        /**
         * Make "add to cart" text in product-single page Blank.
         */
        add_filter( 'woocommerce_product_single_add_to_cart_text', function(){
            return __( '', 'woocommerce' ); 
        } ); 
    }
    
    /**
     * chotu_remove_price
     * hide price for simple, variable on single, loop. everywhere. this hides the add-to-cart too
     * @return void
     */
    public function chotu_remove_price(){
        remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
        remove_action('woocommerce_single_variation', 'woocommerce_single_variation', 10);
        
    }
  
    /**
     * chotu_show_cart_by_status
     * do not show add to cart for A,C,D. show it only for B.
     * We cannot use $chotu_status, as both status and this function are run on the same hook: init.
     * @return void
     */
    public function chotu_show_cart_by_status(){
        global $chotu_status;
        if (!isset($_COOKIE['captain'])) {
            remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
            remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20);
            add_filter( 'woocommerce_is_purchasable', '__return_false');
        }

    }

    /**
     * chotu_product_loop_CTA
     * A,C,D - none. 
     * B:   if variable, show choose. 
     *      if simple 
     *          if price <> NULL, show add-to-cart button
     *          if price = NULL, show nothing
     * @return void
     */
    public function chotu_product_loop_CTA(){
        global $product, $chotu_status;
        $CTA = '';
        switch ($chotu_status) {
            case "A":
            case "C":
            case "D":
                break;
            case "B":
                if($product->is_type('variable')){
                    $CTA = '<a rel="nofollow" href="'.esc_url( $product->get_permalink() ).'"  style="padding: 5% !important; margin: 0 !important; border:none !important; background-color: transparent !important;" class="button add_to_cart_button ajax_add_to_cart"><img src="'. CHOTU_PLUGIN_DIR."assets/images/choose.png".'"></img></a>';
                }elseif($product->is_type('simple')){
                    if($product->get_price() != ''){
                        if( !in_array( $product->get_id(), array_column( WC()->cart->get_cart(), 'product_id' ) ) ){
                            $CTA = '<a rel="nofollow" href="'.esc_url( $product->add_to_cart_url() ).'" data-quantity="'.esc_attr( isset( $quantity ) ? $quantity : 1 ).'" data-product_id="'.esc_attr( $product->get_id() ).'" data-product_sku="'.esc_attr( $product->get_sku() ).'" class="button add_to_cart_button ajax_add_to_cart" style="padding: 5% !important; margin: 0 !important; border:none !important; background-color: transparent !important;"><img src="'. CHOTU_PLUGIN_DIR."assets/images/order.png".'"></img></a>';
                            }
                        }
                    }
                }
        echo $CTA;
    }

    /**
     * chotu_product_single_show_price
     * ask_price button on single page
     * @return void
     */
    public function chotu_product_single_show_price(){
        global $product, $chotu_current_captain, $chotu_status;
        
        switch ($chotu_status) {
            case "A":
            case "C":
            case "D":
                break;
            case "B":
                $phone_number = '';
                $ask_price_url= '';
                $phone_number = $chotu_current_captain->user_login;
                            
                $phone_number   = chotu_prepend_isd_code($phone_number);
                $ask_price_url  = chotu_whatsApp_share_url($phone_number, get_the_permalink(get_the_ID()).$chotu_current_captain->user_login.'%0a'.chotu_get_title( 'post', $chotu_current_captain->captain_language, get_the_ID()).'%0a₹__ ❓%0a');
                
                if($product->get_price() == ''){
                    echo '';
                } elseif($product->get_price() == '0'){
                    echo '<a href="'.$ask_price_url.'"><button style="background-color:var(--lightprimary); border-radius:99px;">'.do_shortcode('[block id="ask-price"]').'</button></a>';
                } elseif($product->get_price() !== '0'){
                    echo '<p>MRP</p>';
                } 
                break;
        }
    }
    /**
     * Set the OG:Meta, and check with favorites for the product page
     */
    public function chotu_set_product_og(){
        global $chotu_current_captain,$chotu_status;
        if(is_product()){
            $product_id = get_the_ID();
            $product = wc_get_product($product_id);
            $description = $product->get_short_description();
            $image = wp_get_attachment_image_url(get_post_meta($product_id,'_thumbnail_id',true),'wa_share');
            chotu_set_og_data($product->get_name(), get_the_permalink($product_id), $image, $description );
            
        }
        echo '<meta name="captain_language" content="'.$chotu_current_captain->captain_language ?? ''.'">';
    }
    /**
     * on product-single page, check if the product is sold by the captain or not
     * if not, hide add to cart button and show an error
     */
    public function chotu_remove_add_to_cart_button() {
        global $chotu_status, $chotu_current_captain;
        if(!empty($chotu_current_captain)){
            $captain_fav_product_ids = $chotu_current_captain->favorite_products();
            if(is_product()){
                $product_id = get_the_ID();
                $product = wc_get_product($product_id);
                if(!in_array($product->get_id(),$captain_fav_product_ids)){
                    if($chotu_status == 'B'){
                        $error_message = sprintf('<a href="%s" class="button wc-forward">%s</a> %s', wc_get_checkout_url(), __('Checkout', 'chotu'), sprintf(__('Sorry, I do not sell "%s" product right now.', 'chotu'), $product->get_title()));
                        wc_add_notice($error_message, 'error');
                        echo "<style>.single_add_to_cart_button{display:none;}</style>";
                    }
                }
            }
        }
    }
    /**
     * show favorite icon in LOOP for logged in captain ($chotu_status = "C")
     */
    public function chotu_show_favorite_icon() {
        global $chotu_status,$chotu_current_captain;
        switch ($chotu_status) {
            case "A":
            case "B":
            case "D":
                break;
            case "C":
                if($chotu_current_captain->is_premium()){
                    echo do_shortcode('[favorite_button post_id="" site_id="1"]');
                }
                break;
        }
    }
    /**
     * show fav icon in product single page for logged in captain ($chotu_status = "C")
     */
    public function chotu_show_favourite_single_product_summary(){
        global $chotu_status,$chotu_current_captain;
        switch ($chotu_status) {
            case "A":
            case "B":
            case "D":
                break;
            case "C":
                if($chotu_current_captain->is_premium()){
                    echo do_shortcode('[favorite_button post_id="" site_id="1"]');
                }
                break;
        }
    }
}
add_action( 'init', 'chotu_load_product_class' );
/**
 * chotu_load_product_class
 * create object of class and call the init function to run the class methods
 * @return void
 */
function chotu_load_product_class(){
    $product_class = new Chotu_Product_Public();
    $product_class->init();
}