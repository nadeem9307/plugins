<?php
/**
 * Chotu_Product_Taxonomy_Public
 * adds the image and translated title to the top of the category and tage pages
 * sets the OG:Meta
 */

class Chotu_Product_Taxonomy_Public{  
    /**
     * __construct
     *
     * @return void
     */
    public function init(){
        add_action('flatsome_after_header', array( $this, 'chotu_add_content_after_header') );
        add_action( 'wp_head', array( $this, 'chotu_add_og_tags' ) );
        /**
         * Translate category title in two places:
         * 1. above product title in product loop
         * 2. category loop
         */
        add_action( 'woocommerce_before_subcategory_title', array( $this, 'chotu_get_category_title' ) );
        add_action( 'woocommerce_before_subcategory', array( $this, 'chotu_get_category_title' ) );
        add_filter( 'term_links-product_cat',array( $this, 'chotu_translate_product_cat_title' ),10,1 );
        add_filter( 'term_links-product_tag',array( $this, 'chotu_translate_product_tag_title' ),10,1 );
    }
    
    public function chotu_add_content_after_header(){
        $current_term = get_queried_object();
        if(is_tax('product_cat')){
            $cat = get_queried_object();
            $cat_id = $cat->term_id;
            $thumbnail_id = get_term_meta( $cat_id, 'thumbnail_id', true );
            $image  = wp_get_attachment_url( $thumbnail_id );
            if(empty($image)){
                $image = chotu_get_option('captain_default_cover_pic');
            }
            chotu_public_template('chotu_page_product_category',array('term' => $cat,'image'=> $image));
        }
        if(is_tax('product_tag')){
            $tag = get_queried_object();
            $tag_id = $tag->term_id;
            chotu_public_template('chotu_page_product_tag',array('term' => $tag));
        }
    }
     /**
     * set the OG meta data for category page
     */
    public function chotu_add_og_tags() {
        if(is_product_category() || is_product_tag()){
            $cate           = get_queried_object();
            $cat_id         = $cate->term_id;
            $title          = $cate->name;
            $description    = $cate->description;
            $term_link      = get_term_link( $cate->term_id );
            $thumbnail_id   = get_term_meta( $cat_id, 'thumbnail_id', true );
            $image = wp_get_attachment_image_url($thumbnail_id,'wa_share');
            chotu_set_og_data($title, $term_link, $image, $description );
        }
    }
    /**
     * chotu_get_category_title
     * woocommerce_before_subcategory       - category name on top of product title in product loop
     * woocommerce_before_subcategory_title - category name in the category loop
     * this function fetches the category title in the captain's language.
     * @param  mixed $category
     * @return void
     */
    public function chotu_get_category_title($category){
        global $chotu_current_captain;
        $cat_title = chotu_get_title( 'term', $chotu_current_captain->captain_language, ($category->term_id));
        $category->name = $cat_title;
        return $category;
    }    
    /**
     * chotu_translate_product_cat_title
     *
     * @param  mixed $links
     * @return void
     */
    public function chotu_translate_product_cat_title($links){
        return $this->chotu_translate_product_taxonomy($links,'product_cat');
    }  
    /**
     * chotu_translate_product_tag_title
     *
     * @param  mixed $links
     * @return void
     */
    public function chotu_translate_product_tag_title($links){
        return $this->chotu_translate_product_taxonomy($links,'product_tag');
    }    
      
    /**
     * chotu_translate_product_taxonomy
     *
     * @param  mixed $links
     * @param  mixed $taxonomy
     * @return void
     */
    public function chotu_translate_product_taxonomy($links, $taxonomy){
        if(!empty($links)){
            global $chotu_current_captain;
            $terms = get_the_terms( get_the_ID(), $taxonomy );
            $new_links = array();
            foreach ( $terms as $term ) {
                $link = get_term_link( $term, $taxonomy );
                if ( is_wp_error( $link ) ) {
                    return $link;
                }
                $new_links[] = '<a href="' . esc_url( $link ) . '" rel="tag">' . chotu_get_title('term', $chotu_current_captain->captain_language, $term->term_id) . '</a>';
            }
            return $new_links;
        }
        return $links;
    }
}
add_action('init', 'chotu_load_chotu_product_tax' );
/**
 * chotu_load_chotu_product_tax
 * create object of class and call the init function to run the class methods
 * @return void
 */
function chotu_load_chotu_product_tax(){
    $chotu_product_tax = new Chotu_Product_Taxonomy_Public();
    $chotu_product_tax->init();
}
