<?php

/**
 * Chotu_Rootshop_Public
 */
class Chotu_Rootshop_Public{    
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(){
        // Set the rootshop-single's title to Yoast Title
        /**
         * https://wp-kama.ru/plugin/yoast/hook/wpseo_opengraph_title
         */
        add_filter( 'wpseo_opengraph_title', function($title){
            global $post;
            if(isset($post->ID)){
                if($post->post_type == "rootshop"){
                    $title = YoastSEO()->meta->for_current_page()->title;
                    if (empty($title)) {
                        $title = get_the_title($post->ID);
                    }
                }
            }
           
            return $title;
        } );

        // to set the image URL for the product with the size specific to wa share in the SEO yoast schema
        /**
         * https://wp-kama.ru/plugin/yoast/hook/wpseo_opengraph_image
         */
        add_filter( 'wpseo_opengraph_image', function($image){
            global $post;
            if(isset($post->ID)){
                if ($post->post_type == "rootshop") {
                    $attach_id = attachment_url_to_postid($image);
                    return wp_get_attachment_image_url($attach_id,'wa_share');
                }
            }
            return $image;
        },10, 1 );
        add_action( 'posts_orderby',function ( $args, $query ) {
            // exit out if it's the admin 
            if ( is_admin()) {
                return;
            }
            // order category archives by title in ascending order
            if ( get_post_type() == 'rootshop' ) {
                return 'wp_posts.menu_order ASC';
            }
          return $args;
        }, 10, 2);
    }

}
new Chotu_Rootshop_Public();
