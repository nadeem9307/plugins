<?php

defined('ABSPATH') or die("You can't access this file directly.");
use chillerlan\QRCode\{QRCode, QROptions};

if(!function_exists('chotu_prepend_isd_code')){
  /**
   * chotu_prepend_isd_code
   * prepends ISD code to a given mobile number basis the country code
   * @param  mixed $mobile_number
   * @return void
   */
  function chotu_prepend_isd_code($mobile_number, $country_code = "IN"){
    switch ($country_code){
      case "IN":
        if(preg_match('/^[0-9]{10}+$/', $mobile_number)){
          $mobile_number = '91'.$mobile_number;
        }
        break;
    }
    return $mobile_number;
  }
}
  
if(!function_exists('chotu_images_upload')){ 
  /**
   * chotu_images_upload
   * Upload an image, create a post and return the attachment URL
   * used for any image uplod in chotu plugin
   * @param  mixed $file
   * @return url, ID or false
   */
  function chotu_images_upload( $file = array(), $type = ''){
    require_once( ABSPATH . 'wp-admin/includes/admin.php' );
    $file_return = wp_handle_upload( $file, array('test_form' => false ) );
    if( isset( $file_return['error'] ) || isset( $file_return['upload_error_handler'] ) ) {
      return false;
    } else {
      $filename = $file_return['file'];
      $attachment = array(
        'post_mime_type' => $file_return['type'],
        'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
        'post_content' => '',
        'post_status' => 'inherit',
        'guid' => $file_return['url']
      );
      $attachment_id = wp_insert_attachment( $attachment, $file_return['url'] );
      require_once(ABSPATH . 'wp-admin/includes/image.php');
      $attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
      wp_update_attachment_metadata( $attachment_id, $attachment_data );
      
      if( 0 < intval( $attachment_id ) ) {
        if($type == 'url'){
          return $file_return['url'];
        }else{
          return $attachment_id;
        }
      }
    }
    return false;
  }
}

if(!function_exists('chotu_admin_template')) {    
  /**
   * chotu_admin_template
   * helper function to load admin template along with other arguments
   * @param  mixed $template_name
   * @param  mixed $args
   * @return void
   */
  
  function chotu_admin_template($template_name, $args) {
    require_once(plugin_dir_path(__DIR__) . "admin/templates/".$template_name);
  }
}

if(!function_exists('chotu_public_template')) {    
  /**
   * chotu_public_template
   * to load public template with other parameters sent as argument
   * @param  mixed $template_name
   * @param  mixed $args
   * @return void
   */
  function chotu_public_template($template_name,$args) {
    require_once(plugin_dir_path(__DIR__) . "public/templates/".$template_name.'.php');
  }
}

if(!function_exists('chotu_find_image_post_id')){
  /**
   * chotu_find_image_post_id
   * Retrieves the attachment ID from the image URL;
   * @param  mixed $url
   * @return ID, false
   */
  function chotu_find_image_post_id($url) {
    global $wpdb;
    $postid = $wpdb->get_var($wpdb->prepare("SELECT DISTINCT ID FROM $wpdb->posts WHERE guid='$url'"));
    if ($postid) {
      return $postid;
    }
    return false;
  }
}

if(!function_exists('chotu_reset_captain')){  
  /**
   * chotu_reset_captain
   * resets the captain cookie and cart session
   * @return void
   */
  function chotu_reset_captain(){
    setcookie('captain', '', time() - 3600, '/'); // setting to null is deprecated, hence setting expiry time to current MINUS 1hour
    chotu_woocommerce_cart_empty();
  }
}

if(!function_exists('chotu_woocommerce_cart_empty')){  
  /**
   * chotu_woocommerce_cart_empty
   * Empties the woocommerce cart
   * @return void
   */
  function chotu_woocommerce_cart_empty(){
    global $woocommerce;
    if(!is_admin()){
      $woocommerce->cart->empty_cart();
    }
  }
}

if(!function_exists('chotu_get_title')) {  
  /**
   * chotu_get_title
   * retrieves the translated title of the post/term name for a given id.
   * @param  mixed $type
   * @param  mixed $language
   * @param  mixed $id
   * @param  mixed $truncate
   * @return void
   */
  function chotu_get_title( $type, $language, $id, $truncate = false){
    if ( $type == 'post'){
      $title = get_post($id)->post_title;
    }elseif ( $type == 'term'){
      $title = get_term($id)->name;
    }
    if(($language != "en") && ($language != "")){
      $title_language = "title_".$language;
      if($type == 'post' && get_post_meta( $id, $title_language,true)){
        $title = get_post_meta( $id, $title_language,true);
      }elseif($type == 'term' && get_term_meta( $id, $title_language,true)){ 
        $title = get_term_meta( $id, $title_language,true);
      }
    }
    if ($truncate == true){
      if(strlen( $title ) > 50){
        $title = substr( $title, 0, 50) . '…';
      }
    }
    return $title;
  }
}

if(!function_exists('chotu_set_og_data')){  
  /**
   * chotu_set_og_data
   * Sets the OG:Meta tags for the page where this function is called.
   * @param  mixed $title
   * @param  mixed $url
   * @param  mixed $image
   * @param  mixed $description
   * @return void
   */
  function chotu_set_og_data($title, $url, $image, $description){
      if($image == ''){
          $image =  wp_get_attachment_image_url(chotu_get_option('chotu_logo'),'wa_share');
      }?>
          <meta property="og:title" content="<?php echo $title;?>" />
          <meta property="og:url" content="<?php echo $url;?>" />
          <meta property="og:image" content="<?php echo $image;?>">
          <meta property="og:description" content="<?php echo $description;?>">
          <?php
    }
}

if(!function_exists('chotu_show_cart_icon')){
  /**
   * chotu_show_cart_icon
   * Show the cart icon with the number in bubble
   * AJAX count updation is added through filter: woocommerce_add_to_cart_fragments in cart class
   * @return void
   */
  function chotu_show_cart_icon(){
    $cart_count = WC()->cart->cart_contents_count; // Set variable for cart item count
    $cart_url = wc_get_cart_url();  // Set Cart URL
    ?>
    <a class="cart-contents" href="<?php echo $cart_url; ?>">
        <i class="fa-solid fa-bag-shopping" style="color: var(--primary); font-size: 2rem; vertical-align: middle;"></i>
        <?php
              if ( $cart_count > 0 ) {
                  ?>
                <div class="cart-contents-count"><?php echo $cart_count; ?></div>
                <?php
              }
        ?>
    </a>
<?php
  }
}
if(!function_exists('get_product_with_category')){  
  /**
   * get_product_with_category
   *
   *  return product with category group
   * 
   * @param  mixed $products
   * @return array
   */
  function get_product_with_category($products){
      $product_array= array();
      if(!empty($products)){
          foreach ($products as $key => $product) {   
              $terms = get_the_terms ( $product->ID, 'product_cat' );
              foreach ( $terms as $term ) {
                  $product_array[$term->term_id][] = $product->ID;
              }
          }
          return $product_array;
      }   
  }
}

if(!function_exists('get_chotu_default_language')){  
  /**
   * get_chotu_default_language
   *
   * @return void
   */
  function get_chotu_default_language(){
    $default_language = chotu_get_option('default_site_language');
    if($default_language != 'en'){
      return $default_language;
    }
    return '';
  }
}
