<?php
class Chotu_Start_Product_Admin
{
    public function init(){
        /**
		 * add a column by name ID in the products table in wp-admin
		 */
		add_filter( 'manage_edit-product_columns', array( $this, 'start_add_column_product_ID' ), 15 );

		/**
		 * display the product_ID in the ID column in the products table in wp-admin
		 */
		add_action( 'manage_product_posts_custom_column', array( $this, 'start_show_column_product_ID' ), 10, 2 );

    }
    public function start_add_column_product_ID($columns){
		$columns['rootshop_link'] = 'Rootshop';
		return array_slice( $columns, 0, 1, true ) + array( 'product_ID' => 'ID' ) + array_slice( $columns, 1, count( $columns ) - 1, true );
	}

	public function start_show_column_product_ID($column, $postid ) {
		if ( $column == 'product_ID' ) {
			echo $postid;
		}
		if($column ==  'rootshop_link'){
			$rootshop_id = get_post_meta($postid,'plan_rootshop_id',true);
			if($rootshop_id){
				switch_to_blog(1);
				$rootshop = get_post($rootshop_id);
				$main_site_url = network_home_url();
				echo '<strong><a href="'.$main_site_url.$rootshop->post_type.'/'.$rootshop->post_name.'" target="_blank">'.$rootshop->post_title.'</a></strong>';
				restore_current_blog();
			}
		}
	}
    
}

add_action( 'init', 'chotu_start_laod_instance' );
function chotu_start_laod_instance(){
    $product = new Chotu_Start_Product_Admin();
    $product->init();
}
