<?php
/**
 * Chotu_Produts
 */
class Chotu_Product_Admin{	
	/**
	 * product_id
	 *
	 * @var mixed
	 */
	public $product_id;
	
	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct( ) {

		// add new meta value for the meta key new captain msg CTA
		/**
		 * https://developer.wordpress.org/reference/functions/add_meta_box/
		 */
		add_action( 'add_meta_boxes_product', function(){
			add_meta_box( 'product_meta_box', 'Keywords', array($this,'chotu_product_add_fields' ), 'product', 'normal', 'low' );
		} );

		
		// update the new captain msg CTA for the specific product
		/**
		 * https://developer.wordpress.org/reference/hooks/save_post_post-post_type/
		 */
		add_action( 'save_post_product', function( $post_id ){
			if ( !isset( $_POST['product_meta_box_nonce'] ) ){
				return;
			}
			if ( isset( $_POST['product_keywords'] ) ) {
			  update_post_meta( $post_id, 'product_keywords', $_POST['product_keywords'] );
			}
		}, 10, 2 );
    }
    /**
	 * chotu_add_product_cat_new_fields
	 *
	 * @param  mixed $post
	 * @return void
	 */
	public function chotu_product_add_fields( $post ) {
		chotu_admin_template('chotu_product.php',$post);
	}
}
new Chotu_Product_Admin();