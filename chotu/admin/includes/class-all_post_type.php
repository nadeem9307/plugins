<?php
/**
 * We are adding a column of "ID" to rootshop, UX_Block & Media admin columns
 */

class Chotu_Post_Type_Admin{
    /**
     * filter hooks
     */
    public function init(){
        /**
         * rootshop admin column
         */
        add_filter( 'manage_edit-rootshop_columns', array( $this,'chotu_add_custom_column' ), 15 );
        add_action( 'manage_rootshop_posts_custom_column', array( $this, 'chotu_display_custom_column' ), 10, 2);
        /**
         * ux block admin column
         */
        add_filter( 'manage_edit-blocks_columns', array( $this,'chotu_add_custom_column' ), 15 );
        add_action( 'manage_blocks_posts_custom_column', array( $this, 'chotu_display_custom_column' ), 10, 2);
        /**
         * media admin column
         */
        add_filter('manage_media_columns', array( $this, 'chotu_add_column_media_columns' ) );
        add_action('manage_media_custom_column', array( $this, 'chotu_add_media_column_data' ), 10, 2);
        
    }    
    /**
     * chotu_add_custom_column
     *
     * @param  mixed $columns
     * @return void
     */
    public function chotu_add_custom_column($columns){
        global $pagenow;
        return array_slice( $columns, 0, 1, true ) + array( 'ID' => 'ID' ) + array_slice( $columns, 1, count( $columns ) - 1, true );
    }    
    /**
     * chotu_display_custom_column
     *
     * @param  mixed $column
     * @param  mixed $postid
     * @return void
     */
    public function chotu_display_custom_column($column, $postid){
        if ( $column == 'ID' ) {
            echo $postid;
        }        
    }    
    /**
     * chotu_add_column_media_columns
     *
     * @param  mixed $columns
     * @return array
     */
    public function chotu_add_column_media_columns($columns) {
        return array_slice( $columns, 0, 1, true ) + array( 'attachment_id' => 'ID' ) + array_slice( $columns, 1, count( $columns ) - 1, true );
    }    
    /**
     * chotu_add_media_column_data
     *
     * @param  mixed $column_name
     * @param  mixed $media_id
     * @return int
     */
    public function chotu_add_media_column_data($column_name, $media_id) {
        if ($column_name === 'attachment_id') {
            echo $media_id;
        }
       
    }
}
add_action( 'init', 'chotu_load_post_instance' );
function chotu_load_post_instance(){
    $post_type = new Chotu_Post_Type_Admin();
    $post_type->init();
}