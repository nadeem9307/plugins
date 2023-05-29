<?php

/**
 * Chotu_Product_Public
 */
class Chotu_Product_Public{    
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(){

        // to hide the product sku from being displayed on the page
        /**
         * https://wp-kama.com/plugin/woocommerce/function/wc_product_sku_enabled
         */
        add_filter( 'wc_product_sku_enabled', '__return_false'  );

        // to set the is_purchasable to false so that the product cannot be added to cart
        /**
         * https://wp-kama.com/plugin/woocommerce/hook/woocommerce_is_purchasable
         */
        // add_filter( 'woocommerce_is_purchasable', '__return_false' );

       /**
        * https://developer.wordpress.org/reference/hooks/init/
        */
        add_action( 'init', array($this,'chotu_remove_price' ) );
        /**
         * https://wp-kama.com/plugin/woocommerce/hook/woocommerce_after_shop_loop_item
         */
        add_action( 'woocommerce_after_shop_loop_item', array($this,'chotu_product_ask_price_button' ), 10 );
        /**
         * https://wp-kama.com/plugin/woocommerce/hook/woocommerce_single_product_summary
         */
        add_action( 'woocommerce_single_product_summary', array($this,'chotu_product_ask_price_btn_single_page' ), 10 );


        
        // to set the primary color of the text for the product loop
        /**
         * https://wp-kama.com/plugin/woocommerce/hook/woocommerce_product_loop_title_classes
         */
        add_filter( 'woocommerce_product_loop_title_classes', function($classes) {
            global $woocommerce_loop;
            $classes .= ' primary-color';
            return $classes;
        });

        
        // to set the meta value for the meta key short description for wp seo yoast schema
        /**
         * https://wp-kama.ru/plugin/yoast/hook/wpseo_metadesc
         */
        add_filter( 'wpseo_metadesc', function ($desc , $presentation){
            if(is_product()){
                global $product;
                return $product->get_short_description();
            }else{
                return $desc;
            }
        },10, 2 );


        // to set the image URL for the product with the size specific to wa share in the SEO yoast schema
        /**
         * https://wp-kama.ru/plugin/yoast/hook/wpseo_opengraph_image
         */
        add_filter( 'wpseo_opengraph_image', function($image){
            if(is_product()){
                $attach_id = attachment_url_to_postid($image);
                return wp_get_attachment_image_url($attach_id,'wa_share');
            }
            return $image;
        },10, 1 );


        // to check if the MRP is set and show it on the page for the variable product
        /**
         * https://wp-kama.com/plugin/woocommerce/hook/woocommerce_available_variation
         */
        add_filter( 'woocommerce_available_variation', function( $variation_data, $product, $variation ) {
            // Here your custom text
            $mrp ='';
            $product_id = $variation_data['variation_id'];
            $product = wc_get_product($product_id);
            if($product->get_price()){
                $mrp = '<span class="ask_price_btn"><a href="javascript:void" class="ask_price button alt">MRP</a></span>';
                $mrp .= '<style>#tag{display:none}</style>';
            }
            $variation_data['price_html'] = $mrp;
            return $variation_data;
        }, 10, 3 );
        /**
         * show favorite icon for logged in captain ($chotu_status = "C")
         * https://wp-kama.com/plugin/woocommerce/hook/woocommerce_before_shop_loop_item
         */
        add_action( 'woocommerce_before_shop_loop_item', function(){
            global $product,$chotu_current_captain,$chotu_status;
            switch ($chotu_status) {
                case "A":
                break;
                case "B":
                break;
                case "C":
                    echo do_shortcode('[favorite_button post_id="" site_id=""]');
                break;
                case "D":
                break;
            }
        }, 10 );

        /**
         * Override loop template and show quantities next to add to cart buttons
         */
        // add_action( 'woocommerce_after_shop_loop_item',function(){
        //     global $chotu_current_captain,$product;
        //     $html= '';
        //     if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
        //         $html = '<form action="' . esc_url( $product->add_to_cart_url() ) . '" class="cart" method="post" enctype="multipart/form-data">';
        //         $html .= woocommerce_quantity_input( array(), $product, false );
        //         //$html .= chotu_price_mrp_check($chotu_current_captain,$product);
        //         $html .= '<button style="display:none" type="submit" class="button alt add_to_cart_trigger">' . esc_html( $product->add_to_cart_text() ) . '</button>';
        //         $html .= '</form>';
        //     }
        //     echo $html;
        // });
        add_action( 'woocommerce_single_product_summary', function(){
            global $chotu_status;
                if($chotu_status == "C") {
                    echo do_shortcode('[favorite_button post_id="" site_id=""]');
                }
            }, 10 );
            /* add to cart ajax <js></js>*/
        add_action( 'wp_footer' ,function(){
            ?>
            <script type='text/javascript'>
                jQuery(function($){
                    // Update data-quantity
                    $(document.body).on('click input', 'input.qty', function() {
                        $(this).parent().closest('.col-inner').find('a.ajax_add_to_cart').attr('data-quantity', $(this).val());
                        //$(".added_to_cart").remove(); // Optional: Removing other previous "view cart" buttons
                    }).on('click', '.add_to_cart_button', function(){
                        var button = $(this);
                        setTimeout(function(){
                            button.parent().find('.quantity > input.qty').val(1); // reset quantity to 1
                        }, 1000); // After 1 second
        
                    });
                });
            </script>
            <?php
        });

        add_filter( 'woocommerce_add_to_cart_validation',function( $passed_validation, $product_id, $quantity, $variation_id = '', $variation = array(), $cart_item_data = array() ){
            $product_data = wc_get_product( $product_id );
            if (!in_array($product_id,chotu_get_captain_products())) {
                /* translators: %s: product name */
                $error_message = sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', wc_get_checkout_url(), __( 'Checkout', 'chotu' ), sprintf( __( 'Sorry, I do not sell "%s" product right now.', 'chotu' ), $product_data->get_title() ) );
                // add your notice
                wc_add_notice( $error_message, 'error' );
                $passed_validation = false;
            }
            return $passed_validation;
        }, 5, 6 );
    }
     // to hide the price of the product(both simple and variable) on single product page,
     // product loop page from being displayed on the page
    public function chotu_remove_price(){
        global $chotu_status;
        /**
         * https://wp-kama.com/plugin/woocommerce/hook/woocommerce_single_product_summary
         */
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
        remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
        if(!isset($_COOKIE['captain'])){
            remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
            remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20);
            add_filter( 'woocommerce_is_purchasable', '__return_false');
        }
        //add_filter( 'woocommerce_product_variation_get_price','__return_false');
    }

    //  to set the ask price button if no MRP is set on the product loop page depending on the chotu status
    // also sets the whatsapp link on clicking the ask price button
    public function chotu_product_ask_price_button(){
        global $product,$chotu_current_captain,$chotu_status;
        switch ($chotu_status) {
            case "A":
              break;
            case "B":
                chotu_price_mrp_check($chotu_current_captain,$product,'');
              break;
            case "C":
              break;
            case "D":
              break;
          }
    }    
    /**
     * chotu_product_ask_price_btn_single_page
     *
     * @return void
     */
    public function chotu_product_ask_price_btn_single_page(){
        global $product,$chotu_current_captain,$chotu_status;
        switch ($chotu_status) {
            case "A":
              break;
            case "B":
                chotu_price_mrp_check($chotu_current_captain,$product,'single');
              break;
            case "C":
              break;
            case "D":
              break;
          }
    }
}
new Chotu_Product_Public();
