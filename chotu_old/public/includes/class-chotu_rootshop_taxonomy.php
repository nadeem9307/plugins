<?php
/**
 * Chotu_Product_Cat_Public
 */
class Chotu_Rootshop_Taxonomy_Public{    
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(){
        // to set the new captain msg CTA and the image depending on the chotu status and load the template with 
        // passing them as arguements so that it is displayed on the page
        add_action('flatsome_after_header', function(){
            global $chotu_status;
            $current_term = get_queried_object();
            if(is_tax('rootshop_cat') || is_tax('rootshop_tag')){
                $cat = get_queried_object();
                $cat_id = $cat->term_id;
                $thumbnail_id = get_term_meta( $cat_id, '_thumbnail_id', true );
                $image  = wp_get_attachment_url( $thumbnail_id );
                if(empty($image)){
                    $image = get_option('captain_default_cover_pic');
                }
                chotu_public_template('chotu_page_rootshop_taxonomy',array('term'=>$cat,'image'=>trim($image)));
            }
        } );
         //  to set the image URL of size specific to whatsapp sharing in the SEO yoast schema
        /**
         * https://wp-kama.ru/plugin/yoast/hook/wpseo_opengraph_image
         */
        add_filter( 'wpseo_opengraph_image', function( $image ) {
            global $wpseo_og;
            if(is_tax('rootshop_cat') || is_tax('rootshop_tag')){
                $tag = get_queried_object();
                $cat_id = $tag->term_id;
                $thumbnail_id = get_term_meta( $cat_id, '_thumbnail_id', true );
                return wp_get_attachment_image_url($thumbnail_id,'wa_share');
            }
            return $image;
            
        },30, 1 );
        
       
    
    }
}
new Chotu_Rootshop_Taxonomy_Public();
