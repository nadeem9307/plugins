<?php
/**
 * Chotu_Shop
 */
class Chotu_RootShop_Admin{
	
	/**
	 * __construct
	 *
	 * @param  mixed $shop_id
	 * @return void
	 */
    public function __construct(){
        /**
         * https://developer.wordpress.org/reference/hooks/init/
         */
        // register roosthop as a custom post type
        add_action('init', function(){
            /*
            * The $labels describes how the post type appears.
            */
            $labels = array(
                'name'          => 'Shops', // Plural name
                'singular_name' => 'Shop'   // Singular name
            );
            /*
            * The $supports parameter describes what the post type supports
            */
            $supports = array(
                'title',            // Post title
                'editor',           // Post content
                //'excerpt',        // Allows short description
                'author',           // Allows showing and choosing author
                'thumbnail',        // Allows feature images
                'revisions',        // Shows autosaved version of the posts
                'template',         // Shows autosaved version of the posts
                'custom-fields',    // Supports by custom fields
                'page-attributes',
            );
        
            /*
            * The $args parameter holds important parameters for the custom post type
            */
            $args = array(
                'labels'              => $labels,
                'description'         => 'Post type Rootshop', // Description
                'supports'            => $supports,
                'taxonomies'          => array( 'rootshop_cat', 'rootshop_tag' ), // Allowed taxonomies
                'hierarchical'        => false, // Allows hierarchical categorization, if set to false, the Custom Post Type will behave like Post, else it will behave like Page
                'public'              => true,  // Makes the post type public
                'show_ui'             => true,  // Displays an interface for this post type
                'show_in_rest'        => true,  // Whether to display in REST or not
                'show_in_menu'        => true,  // Displays in the Admin Menu (the left panel)
                'show_in_nav_menus'   => true,  // Displays in Appearance -> Menus
                'show_in_admin_bar'   => true,  // Displays in the black admin bar
                'menu_position'       => 5,     // The position number in the left menu
                'menu_icon'           => 'dashicons-cart',  // The URL for the icon used for this post type
                'can_export'          => true,  // Allows content export using Tools -> Export
                'has_archive'         => 'start',  // Enables post type archive (by month, date, or year)
                'exclude_from_search' => false, // Excludes posts of this type in the front-end search result page if set to true, include them if set to false
                'publicly_queryable'  => true,  // Allows queries to be performed on the front-end part if set to true
                'capability_type'     => 'post', // Allows read, edit, delete like “Post”
                'rewrite'             => array( 'slug' => 'start/%rootshop_cat%', 'with_front' => false )
            );
            register_post_type('rootshop', $args); //Create a post type with the slug is ‘product’ and arguments in $args.
        });
        /**
         * https://developer.wordpress.org/reference/hooks/init/
         */
        // register roosthop_cat as taxonomy
        add_action('init', function(){
            $labels = array(
                'name'                          => 'Shop Categories',
                'singular_name'                 => 'Shop Category',
                'search_items'                  => 'Search Shop Categories',
                'popular_items'                 => 'Popular Shop Categories',
                'all_items'                     => 'All Shop Categories',
                'parent_item'                   => 'Parent Shop Category',
                'edit_item'                     => 'Edit Shop Category',
                'update_item'                   => 'Update Shop Category',
                'add_new_item'                  => 'Add New Shop Category',
                'new_item_name'                 => 'New Shop Category',
                'separate_items_with_commas'    => 'Separate Shop categories with commas',
                'add_or_remove_items'           => 'Add or remove Shop categories',
                'choose_from_most_used'         => 'Choose from most used Shop categories'
                );
            
            $args = array(
                'label'                         => 'Shop Categories',
                'labels'                        => $labels,
                'public'                        => true,
                'hierarchical'                  => true,
                'show_ui'                       => true,
                'show_in_rest'                  => true,  // Whether to display in REST or not
                'show_in_nav_menus'             => true,
                'show_admin_column'             => true,
                'args'                          => array( 'orderby' => 'term_order' ),
                'rewrite'                       => array( 'slug' => 'begin', 'with_front' => true, 'hierarchical' => true ),
                'query_var'                     => true
            );
            register_taxonomy( 'rootshop_cat', 'rootshop', $args );
        });
        /**
         * https://developer.wordpress.org/reference/hooks/init/
         */
        // register roosthop_tag as taxonomy
        add_action('init', function(){
            $labels = array(
                'name' => _x( 'Shop Tags', 'taxonomy general name' ),
                'singular_name' => _x( 'Shop Tag', 'taxonomy singular name' ),
                'search_items' =>  __( 'Search Shop Tags' ),
                'popular_items' => __( 'Popular Shop Tags' ),
                'all_items' => __( 'All Shop Tags' ),
                'edit_item' => __( 'Edit Shop Tag' ), 
                'update_item' => __( 'Update Shop Tag' ),
                'add_new_item' => __( 'Add New Shop Tag' ),
                'new_item_name' => __( 'New Shop Tag Name' ),
                'separate_items_with_commas' => __( 'Separate Shop tags with commas' ),
                'add_or_remove_items' => __( 'Add or remove Shop tags' ),
                'choose_from_most_used' => __( 'Choose from the most used Shop tags' ),
                'menu_name' => __( 'Shop Tags' ),
            ); 

            $args = array(
                'hierarchical' => false,
                'labels' => $labels,
                'show_ui' => true,
                'show_admin_column' => true,
                'show_in_rest' => true, //whether to display in REST or not
                "show_in_menu" => true,
                "show_in_nav_menus" => true,
                'query_var' => true,
                'rewrite' => array( 'slug' => 'launch' ),
            );
            register_taxonomy('rootshop_tag','rootshop', $args );
        });
        /**
         * https://developer.wordpress.org/reference/hooks/init/
         */
        // add UX builder support to rootshop
        add_action( 'init', function () {
            if ( function_exists( 'add_ux_builder_post_type' ) ) {
                add_ux_builder_post_type( 'rootshop' );
            }
        } );
        /**
         * https://developer.wordpress.org/reference/hooks/add_meta_boxes/
         */
        // add metaboxes by name "Shop Items" & "Shop IDs"
        add_action( 'add_meta_boxes', function(){
            add_meta_box('shop-feed-meta',__( 'Shop Items', 'chotu' ),array($this,'chotu_rootshop_metabox_callback'),'rootshop');
            add_meta_box('rootshop-ids-meta',__( 'Shop IDs', 'chotu' ),array($this,'chotu_rootshop_ids_metabox_callback'),'rootshop');
        });
        /**
         * https://developer.wordpress.org/reference/hooks/save_post/
         */
        add_action( 'save_post_rootshop', function($post_id){
            // check permissions
            if (!current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
            if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'rootshop_meta_box_nonce' ) ) return;
            /**/ 
            if(isset($_POST['rootshopHTML'])){
                update_post_meta($post_id,'rootshopHTML',$_POST['rootshopHTML']);
            }else{
                update_post_meta($post_id,'rootshopHTML','');
            }
            /**/ 
            if(isset($_POST['rootshop_default_description'])){
                update_post_meta($post_id,'rootshop_default_description',$_POST['rootshop_default_description']);
            }else{
                update_post_meta($post_id,'rootshop_default_description','');
            }
            /**/
            if(isset($_POST['rootshop_default_announcement'])){
                update_post_meta($post_id,'rootshop_default_announcement',$_POST['rootshop_default_announcement']);
            }else{
                update_post_meta($post_id,'rootshop_default_announcement','');
            }
            /**/
            if(isset($_POST['rootshop_keywords'])){
                update_post_meta($post_id,'rootshop_keywords',$_POST['rootshop_keywords']);
            }else{
                update_post_meta($post_id,'rootshop_keywords','');
            }
            if(isset($_POST['rootshop_productIDs'])){
                update_post_meta($post_id,'rootshop_productIDs',$_POST['rootshop_productIDs']);
            }else{
                update_post_meta($post_id,'rootshop_productIDs','');
            }
            /**/ 
            if(isset($_POST['rootshop_showSearchKart'])){
                update_post_meta($post_id,'rootshop_showSearchKart',$_POST['rootshop_showSearchKart']);
            }else{
                update_post_meta($post_id,'rootshop_showSearchKart','');
            }
            /**/ 
            if(isset($_POST['rootshop_Editable'])){
                update_post_meta($post_id,'rootshop_Editable',$_POST['rootshop_Editable']);
            }else{
                update_post_meta($post_id,'rootshop_Editable','');
            }
        });
        /**
         * https://developer.wordpress.org/reference/hooks/manage_screen-id_columns/
         */
        add_filter( 'manage_edit-rootshop_columns', function(){
            $columns = array(
                'cb'        => '<input type="checkbox" />',
                'title'     => __( 'Title', 'rootshop' ),
                'author'     => __( 'Author', 'rootshop' ),
                'shortcode' => __( 'Shortcode', 'rootshop' ),
                // 'rootshop_cat'=> __('Category', 'rootshop'),
                // 'rootshop_tag'=> __('Tag', 'rootshop'),
                'date'      => __( 'Date', 'rootshop' ),
            );
            return $columns;
        } );
        /**
         * https://developer.wordpress.org/reference/hooks/manage_screen-id_columns/
         */
        add_action( 'manage_rootshop_posts_custom_column', function($column, $post_id){
            $post_data = get_post( $post_id, ARRAY_A );
            add_thickbox();
            switch ( $column ) {
                case 'shortcode':
                    echo '<textarea style="min-width:100%; max-height:30px; background:#eee;">[rootshopHTML id="' . $post_id . '"]</textarea>';
                    break;
            }
        }, 10, 2 );
        add_action( 'dp_duplicate_post',function( $new_post_id, $post, $status ) {
            // Perform some actions after the post has been duplicated.
            update_post_meta($new_post_id,'rootshopHTML',get_post_meta($post->ID,'rootshopHTML',true));
            update_post_meta($new_post_id,'rootshop_default_description',get_post_meta($post->ID,'rootshop_default_description',true));
            update_post_meta($new_post_id,'rootshop_default_announcement',get_post_meta($post->ID,'rootshop_default_announcement',true));
            update_post_meta($new_post_id,'rootshop_keywords',get_post_meta($post->ID,'rootshop_keywords',true));

            update_post_meta($new_post_id,'rootshop_productIDs',get_post_meta($post->ID,'rootshop_productIDs',true));
            update_post_meta($new_post_id,'rootshop_showSearchKart',get_post_meta($post->ID,'rootshop_showSearchKart',true));
            update_post_meta($new_post_id,'rootshop_Editable',get_post_meta($post->ID,'rootshop_Editable',true));
        },10,3);
        // for CPT (rootshop) category permalink
        add_filter('post_type_link', function($post_link, $post, $leavename, $sample){
            if (false !== strpos($post_link, '%rootshop_cat%')) {
                $taxonomy = 'rootshop_cat';
                $args = [
                    'format' => 'slug',
                    'separator' => '/',
                    'link' => false,
                    'inclusive' => true,
                ];
                $rootshop_cat_type_term = get_the_terms($post->ID, 'rootshop_cat');
                if (!empty($rootshop_cat_type_term)){
                    return str_replace(
                        "%{$taxonomy}%",
                        rtrim(
                            get_term_parents_list($rootshop_cat_type_term[0]->term_id, $taxonomy, $args),
                            "/"
                        ),
                        $post_link);
                }else{
                    $post_link = str_replace('%rootshop_cat%', 'uncategorized', $post_link);
                }   
            }
            return $post_link;
        }, 10, 4);

    }    
    /**
     * chotu_rootshop_metabox_callback
     *
     * @return void
     */
    public function chotu_rootshop_metabox_callback(){
        wp_nonce_field( 'rootshop_meta_box_nonce', 'meta_box_nonce' );
        chotu_admin_template('chotu_rootshop_item_metabox.php',array());
    }
    /**
     * chotu_rootshop_ids_metabox_callback
     *
     * @return void
     */
    public function chotu_rootshop_ids_metabox_callback(){
        wp_nonce_field( 'rootshop_meta_box_nonce', 'meta_box_nonce' );
        chotu_admin_template('chotu_rootshop_ids_metabox.php',array());
    }
}
new Chotu_RootShop_Admin();
