<?php

/**
 * Chotu_Rootshop_Tag_Admin
 */
class Chotu_Rootshop_Tag_Admin{	
	/**
	 * rootshop_cat_id
	 *
	 * @var mixed
	 */
	public $rootshop_cat_id;
	
	/**
	 * __construct
	 *
	 * @param  mixed $rootshop_cat_id
	 * @return void
	 */
	public function __construct() {

        // Enqueues all scripts, styles, settings, and templates necessary to use all media JS APIs.
		/**
		 * https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
		 */
		add_action( 'admin_enqueue_scripts',function(){
			wp_enqueue_media();
		} );

        // to add the new field image with add/remove image button for the product tag
		/**
		 * https://developer.wordpress.org/reference/hooks/admin_footer/
		 */
		add_action( 'admin_footer','chotu_add_script');// function definition in core functions


		// to load the template passed as an arguement from the specific location mentioned in the func
		/**
		 * https://wordpress.org/support/topic/woocommerce-add-second-description-to-product-tag-pages/
		 */
		add_action( 'rootshop_tag_add_form_fields',function(){
			chotu_admin_template('chotu_rootshop_image.php',array());
		}, 10, 2 );


		// to add the meta value for the meta key image and new captain msg CTA
		/**
		 * https://gist.github.com/nikolays93/63b7293d6d6ca580f3b41694c86c749c
		 */
		add_action( 'created_rootshop_tag',function($term_id, $tt_id){
			if( isset( $_POST['thumbnail_id'] ) && '' !== $_POST['thumbnail_id'] ){
				$image = $_POST['thumbnail_id'];
				add_term_meta( $term_id, '_thumbnail_id', $image, true );
			}
			
		}, 10, 2 );


		// to load the edit template passed as an arguement in the function
		/**
		 * https://www.appsloveworld.com/wordpress/100/156/create-custom-field-into-tag-of-attributes-in-woocommerce-with-add-action
		 */
		add_action( 'rootshop_tag_edit_form_fields',function($term, $taxonomy ){
			chotu_admin_template('chotu_rootshop_image_edit.php',$term);
		}, 10, 2 );
		/**
		 * https://hooks.wbcomdesigns.com/reference/functions/is_product_tag/
		 */
		add_action( 'edited_rootshop_tag', function($term_id, $tt_id){
			if( isset( $_POST['thumbnail_id'] ) && '' !== $_POST['thumbnail_id'] ){
				$image = $_POST['thumbnail_id'];
				update_term_meta ( $term_id, '_thumbnail_id', $image );
			} else {
				update_term_meta ( $term_id, '_thumbnail_id', '' );
			}
		}, 10, 2 );

		//  to add the images in the list of tags displayed in wp-admin
		/**
		 * https://developer.wordpress.org/reference/hooks/manage_screen-id_columns/
		 */
		add_filter( 'manage_edit-rootshop_tag_columns',function ($columns){
			$res = array_slice($columns, 0, 1, true) +
			array( 'rootshop_tag_image' => __( 'Image', 'chotu' ) ) +
			array_slice($columns, 1, count($columns)-1, true);
		   return $res;
		},15 );


		//  to fetch the meta value for the meta key image of the product tag
		/**
		 * https://stackoverflow.com/questions/23858236/how-to-add-remove-columns-in-woocommerce-admin-product-list
		 */
		add_filter( 'manage_rootshop_tag_custom_column', function($string, $columns, $term_id){
			if ( $columns == 'rootshop_tag_image' ) {
				$image_id = get_term_meta ( $term_id, '_thumbnail_id', true );
				if ( $image_id ) {
				  echo '<img src="'.wp_get_attachment_image_url( $image_id, 'thumbnail' ).'"  width="50px" height="50px" class="attachment-thumbnail size-thumbnail">';
				}
			}
		}, 10, 3 );
		
	}
}
new Chotu_Rootshop_Tag_Admin();
