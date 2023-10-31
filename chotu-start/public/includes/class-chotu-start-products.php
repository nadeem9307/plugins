<?php
/**
 * 
 */
class Chotu_Start_Products
{	
	/**
	 * __construct
	 *
	 * @return void
	 */
	public function init()
	{
		$this->chotu_start_check_add_to_cart_restrictions();
		$this->chotu_start_set_product_url_cookie();
		/**
		 * hide sku from product detail page
		 */
		add_filter( 'wc_product_sku_enabled', '__return_false' );
		/**
		 * remove custom price string in product loop
		 * also added product exerpt in product loop
		 */
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
		add_filter( 'woocommerce_get_price_html',  array( $this,'chotu_start_remove_custom_price_loop'), 20, 2 );
		add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'chotu_short_description_in_loop'), 99, 2 );
		
		/**
		 * hide the product description tab in the bottom
		 */
		add_filter( 'woocommerce_product_tabs', array( $this,'chotu_start_remove_desc_tab'), 99 );

		add_action( 'woocommerce_single_product_summary', array( $this, 'chotu_start_start_whatApp_button' ), 50 );
		add_action( 'woocommerce_before_variations_form', array( $this, 'chotu_start_add_div_variations_form' ), 50 );
		/**
		 * Remove add-to-cart button in product loop
		 */
		add_filter( 'woocommerce_loop_add_to_cart_link', function ( $add_to_cart_html, $product ) {
			return '';
		}, 25, 2 );
		

		/**
		 * Force all products to be bought individually only 
		 * AND Hides the quantity selector in product single page
		 */
		add_filter( 'woocommerce_is_sold_individually', function($return, $product){
			return true;
		}, 10, 2 );

		/**
		 * check here if plan_rootshop_id is valid (exist in rootshop from main site) or not if not show coming soon button
		 */
		add_action( 'woocommerce_before_add_to_cart_button', function(){
			global $wpdb;
			$product_id = get_the_ID();
			
			$plan_rootshop_id = get_post_meta( $product_id, 'plan_rootshop_id', true);
			
			switch_to_blog(1);
			$current_blog_id = get_current_blog_id();
			$rootshop 		 = chotu_get_cpost($plan_rootshop_id, 'rootshop');
			restore_current_blog();
			
			if(!$rootshop){
				echo '<p class="button alt">coming soon</p>';
				echo "<style>.single_add_to_cart_button{display:none}</style>";
			}
		} );

	}	
	/**
	 * chotu_start_check_add_to_cart_restrictions
	 * check if captain not logged in remove add to cart button
	 * @return void
	 */
	public function chotu_start_check_add_to_cart_restrictions(){
		if (!is_captain_logged_in()) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		}
	}	
	/**
	 * chotu_start_set_product_url_cookie
	 * set product url in cookie if captain not logged in
	 * @return void
	 */
	public function chotu_start_set_product_url_cookie(){
		if(is_product()){
			if (!is_captain_logged_in()) {
				$product_url = get_the_permalink(get_the_ID()).'#swatch';
				setcookie('product_url', $product_url, (time() + 3600), "/");
			}
		}
		
	}
		
	/**
	 * chotu_start_remove_custom_price_loop
	 * Removes price string in all places except product single page
	 * Removes price string in related products loop too
	 * @param  mixed $price
	 * @param  mixed $product
	 * @return void
	 */
	public function chotu_start_remove_custom_price_loop($price, $product){
		global $woocommerce_loop;
		if(!is_product()){
			return '';
		}
		if ( $woocommerce_loop['name'] == 'related' ) {
			return '';
		}
		return $price;
	}

	/**
	 * chotu_show_excerpt_in_loop
	 * displays product short description in the loop page.
	 * @return void
	 */
	public function chotu_short_description_in_loop() {
		global $product;
		echo apply_filters('the_content',$product->get_short_description());
	}
		
	/**
	 * chotu_start_remove_desc_tab
	 * remove the product description tab
	 * rename the additional information tab as shop info
	 * @param  mixed $tabs
	 * @return void
	 */
	public function chotu_start_remove_desc_tab( $tabs ) {
		unset( $tabs['description'] );
		$tabs['additional_information']['title'] = __( 'Shop Info' );
		return $tabs;
	}

	/**
	 * chotu_start_start_whatApp_button
	 * restrict without login users add to cart on product page.
	 * @return void
	 */
	public function chotu_start_start_whatApp_button() {
		global $product; 
		// Check if the user is not logged in
		
		/**
		 * show product short and long description before button
		 */
		echo apply_filters('the_content', $product->get_description());
		echo '<BR><BR>';
		/**
			* show the "start on whatsapp" button with link to WhatsApp
		*/
		if (!is_captain_logged_in()) {
				$send_on_whatsapp_link = get_send_on_whatsapp_link('captain_onboarding_number','START');
			?>	
			<a href="<?php echo $send_on_whatsapp_link?>">
				<img src="<?php echo CHOTU_START_BASE_URL;?>/assets/images/START_on_WhatsApp.png">
			</a>
			<?php
		}
	}
	public function chotu_start_add_div_variations_form(){
		global $product;
		if($product->is_type('variable')){
			echo '<div id="swatch"><h2>Setup my shop</h2></div>';
		}else{
			echo '<div id="swatch">/div>';
		}
		
	}

}
add_action('wp', 'chotu_start_load_product_class' );
/**
 * chotu_start_load_product_class
 * create object of class and call the init function to run the class methods
 * @return void
 */
function chotu_start_load_product_class(){
	
	$start_product = new Chotu_Start_Products();
	$start_product->init();
}
