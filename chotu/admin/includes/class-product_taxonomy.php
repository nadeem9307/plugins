<?php
/**
 * whatever we show about the product in the /wp-admin is coded here
 * we are adding a column of product_ID in the products admin page
 */

class Chotu_Product_Taxonomy_Admin{
    /**
     * filter hooks
     */

    public function init(){
        add_filter( 'manage_edit-product_cat_columns', array( $this, 'chotu_product_taxonomy_column' ) );
        add_filter( 'manage_edit-product_tag_columns', array( $this, 'chotu_product_taxonomy_column') );
        add_filter( 'manage_product_cat_custom_column', array( $this, 'chotu_product_taxonomy_column_data'), 10, 3);
        add_filter( 'manage_product_tag_custom_column', array( $this, 'chotu_product_taxonomy_column_data'), 10, 3);

    }    
    /**
     * chotu_product_taxonomy_column
     *
     * @param  mixed $columns
     * @return void
     */
    public function chotu_product_taxonomy_column($columns) {
        return array_slice( $columns, 0, 1, true ) + array( 'term_id' => 'ID' ) + array_slice( $columns, 1, count( $columns ) - 1, true );
    }    
    /**
     * chotu_product_taxonomy_column_data
     *
     * @param  mixed $content
     * @param  mixed $column_name
     * @param  mixed $term_id
     * @return void
     */
    public function chotu_product_taxonomy_column_data($content, $column_name, $term_id) {
        if ($column_name == 'term_id') {
            return $term_id;
        }
        return $content;
    }
}
add_action( 'init', 'chotu_load_instance' );
function chotu_load_instance(){
    $product_taxonomy = new Chotu_Product_Taxonomy_Admin();
    $product_taxonomy->init();
}