<?php
/**
 * whatever we show about the product in the /wp-admin is coded here
 * we are adding a column of product_ID in the products admin page
 */

class Chotu_Product_Admin{	
	
	/**
	 * init
	 * call filters and action on loading the class
	 * @return void
	 */
	public function init(){
		/**
		 * add a column by name ID in the products table in wp-admin
		 */
		add_filter( 'manage_edit-product_columns', array( $this, 'add_column_product_ID' ),15 );

		/**
		 * display the product_ID in the ID column in the products table in wp-admin
		 */
		add_action( 'manage_product_posts_custom_column', array( $this, 'show_column_product_ID' ), 10, 2 );

		add_action( 'restrict_manage_posts', array( $this,'chotu_admin_tag_add_filter' ) );
		add_action( 'pre_get_posts', array( $this,'chotu_admin_tag_filter_products' ) );
	}
	
	/**
	 * add_column_product_ID
	 *
	 * @param  mixed $columns
	 * @return void
	 */
	public function add_column_product_ID($columns){
		return array_slice( $columns, 0, 1, true ) + array( 'product_ID' => 'ID' ) + array_slice( $columns, 1, count( $columns ) - 1, true );
	}
	
	/**
	 * show_column_product_ID
	 *
	 * @param  mixed $column
	 * @param  mixed $postid
	 * @return void
	 */
	public function show_column_product_ID($column, $postid ) {
		if ( $column == 'product_ID' ) {
			echo $postid;
		}
	}	
	/**
	 * chotu_admin_tag_add_filter
	 *
	 * @return void
	 */
	public function chotu_admin_tag_add_filter() {
		global $typenow;

		if ($typenow == 'product') { // Replace 'post' with your post type
			$admin_tags = get_terms(array(
				'taxonomy' => 'admin_tag', // WooCommerce product category taxonomy
				'hide_empty' => false,
			));

			echo '<select name="filter_by_admin_tag">';
			echo '<option value="">Filter by admin tags</option>';

			foreach ($admin_tags as $tag) {
				$selected = isset($_GET['filter_by_admin_tag']) && $_GET['filter_by_admin_tag'] == $tag->term_id ? 'selected' : '';
				echo '<option value="' . $tag->term_id . '" ' . $selected . '>' . $tag->name . '</option>';
			}

			echo '</select>';
		}
	}
	public function chotu_admin_tag_filter_products($query) {
		global $pagenow;
	
		if (is_admin() && $pagenow == 'edit.php' && isset($_GET['filter_by_admin_tag']) && !empty($_GET['filter_by_admin_tag'])) {
			$tax_query = array(
				array(
					'taxonomy' => 'admin_tag',
					'field'    => 'term_id',
					'terms'    => $_GET['filter_by_admin_tag'],
					'operator' => 'IN',
				),
			);
	
			$query->set('tax_query', $tax_query);
		}
	}

}
add_action('init', 'chotu_product_admin');
function chotu_product_admin(){
	$chotu_product_admin = new Chotu_Product_Admin();
	$chotu_product_admin->init();
}