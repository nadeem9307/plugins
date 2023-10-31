<?php
/**
 * Chotu_Product_Public
 */
class Chotu_Product_Taxonomy_Public{  
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(){
        // adds the image to the top of the category/tag page.
        add_action('flatsome_after_header', function(){
            global $chotu_status;
            $current_term = get_queried_object();
            if(is_tax('product_cat')){
                $cat = get_queried_object();
                $cat_id = $cat->term_id;
                $thumbnail_id = get_term_meta( $cat_id, 'thumbnail_id', true );
                $image  = wp_get_attachment_url( $thumbnail_id );
                if(empty($image)){
                    $image = get_option('captain_default_cover_pic');
                }
                chotu_public_template('chotu_page_product_taxonomy',array('term' => $cat,'image'=> $image));
            }
        });


        //  to set the image URL of size specific to whatsapp sharing in SEO yoast schema
        /**
         * https://wp-kama.ru/plugin/yoast/hook/wpseo_opengraph_image
         */
        add_filter( 'wpseo_opengraph_image', function( $image ) {
            if(is_product_category()){
                $cate           = get_queried_object();
                $cat_id         = $cate->term_id;
                $thumbnail_id   = get_term_meta( $cat_id, 'thumbnail_id', true );
                return wp_get_attachment_image_url($thumbnail_id,'wa_share');
            }
            return $image;
        },30, 1 );
    }
}
new Chotu_Product_Taxonomy_Public();