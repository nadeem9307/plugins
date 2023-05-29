<?php
if(!function_exists('dd')){  
    /**
     * dd
     * custom function by Nadeem for personal use
     * @param  mixed $data
     * @return array
        */
    function dd($data){
      echo "<pre>";
      print_r($data);
      echo "</pre>";
      die;
    }
}

  if(!function_exists('chotu_check_is_captain')){  
    /**
     * chotu_check_is_captain
     * check if logged in user is captain or not
     * @return bool
     */
    function chotu_check_is_captain(){
      global $current_user;
      if(is_user_logged_in()){
        $user_roles = $current_user->roles;
        if(in_array('captain',$user_roles)){
          return true;
        }
      }
      return false;
    }
  }
  
  if(!function_exists('chotu_get_captain_id')){
    /**
     * chotu_get_captain_id
     * retrieve the captain_ID from a given username
     * @param  mixed $username
     * @return integer
     */
    function chotu_get_captain_id($username){
      if(isset($username)){
        $captain = get_user_by('login',$username);
        if($captain !=''){
          if(in_array('captain',$captain->roles)){
            return $captain->ID;
          }
        }
      }
    }
  }



  if(!function_exists('chotu_append_isd_code')){
    /**
     * chotu_append_isd_code
     * appends 91 to a 10-digit mobile number
     * @param  mixed $mobile_number
     * @return void
     */
    function chotu_append_isd_code($mobile_number){
      if(preg_match('/^[0-9]{10}+$/', $mobile_number)){
        $mobile_number = '91'.$mobile_number;
      }
      return $mobile_number;
    }
  }


  
  if(!function_exists('chotu_myaccount_images_upload')){ 
    /**
     * chotu_myaccount_images_upload
     * Upload an image, create a post and return the attachment URL
     * used to update captain's cover photo and display pic
     * @param  mixed $file
     * @return bool
     */
    function chotu_myaccount_images_upload($file = array(),$type = ''){
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
          if($type == 'dp' || $type == 'cp'){
            return $file_return['url'];
          }else{
            return $attachment_id;
          }
        }
      }
      return false;
    }
  }



  //to load admin template passed as arguement
  if (!function_exists('chotu_admin_template')) {    
    /**
     * chotu_admin_template
     *
     * @param  mixed $template_name
     * @param  mixed $term
     * @return void
     */
    function chotu_admin_template($template_name,$term) {
      require_once(plugin_dir_path(__DIR__) . "admin/templates/".$template_name);
    }
  }


  // to load public template passed as arguement
  if (!function_exists('chotu_public_template')) {    
    /**
     * chotu_public_template
     *
     * @param  mixed $template_name
     * @param  mixed $data
     * @return void
     */
    function chotu_public_template($template_name,$args) {
      require_once(plugin_dir_path(__DIR__) . "public/templates/".$template_name.'.php');
    }
  }
  // end-user moves from captain shop 1 to captain shop 2. 
  // In this case, we should delete the shop 1 wishlist and recreate wishlist in shop 2.
  // checks if the captain ID in the wishlist matches with the captain cookie set and deletes and creates
  // a new wishlist if captain ID doesnt match
  if(!function_exists('chotu_check_cart_captain')){
    function chotu_check_cart_captain(){
      global $woocommerce;
      if(!WC()->cart->is_empty()){
        //$token = $wishlist->get_token();
        $captain_id = WC()->session->get('_customer_id');//chotu_get_captain_id_wishlist($token);
        // dd($captain_id.'fds');
        if($captain_id != chotu_get_captain_id($_COOKIE['captain'])){
          chotu_woocommerce_cart_empty();
          //yith_plugin_fw_update_list_expiry_of_forwarded_captain($token,$wishlist);
        }
      }
    }
  }

  // generic function to add image field with add/remove image buttons in the wp-admin
  // used in rootshop_cat and rootshop_tag
  function chotu_add_script(){
		?>
		<script>
		   jQuery(document).ready( function($) {
			 function code_media_upload(button_class) {
			   var _custom_media = true,
			   _code_send_attachment = wp.media.editor.send.attachment;
			   $('body').on('click', button_class, function(e) {
				 var button_id = '#'+$(this).attr('id');
				 var send_attachment_bkp = wp.media.editor.send.attachment;
				 var button = $(button_id);
				 _custom_media = true;
				 wp.media.editor.send.attachment = function(props, attachment){
				   if ( _custom_media ) {
					 $('#thumbnail_id').val(attachment.id);
					 $('#thumbnail_id-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
					 $('#thumbnail_id-wrapper .custom_media_image').attr('src',attachment.url).css('display','block');
				   } else {
					 return _code_send_attachment.apply( button_id, [props, attachment] );
				   }
				  }
			   wp.media.editor.open(button);
			   return false;
			 });
		   }
		   code_media_upload('.product_tag_media_button.button'); 
		   $('body').on('click','.code_media_remove',function(){
			 $('#thumbnail_id').val('');
			 $('#thumbnail_id-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
		   });
		   $(document).ajaxComplete(function(event, xhr, settings) {
			 var queryStringArr = settings.data.split('&');
			 if( $.inArray('action=add-tag', queryStringArr) !== -1 ){
			   var xml = xhr.responseXML;
			   $response = $(xml).find('term_id').text();
			   if($response!=""){
				 // Clear the thumb image
				 $('#thumbnail_id-wrapper').html('');
			   }
			 }
		   });
		 });
	  </script>
	  <?php
  }

if(!function_exists('chotu_get_cpost')){  
  /**
   * chotu_get_cpost
   * Retrieves the CPT by a specific ID
   * get custom post name
   * @param  mixed $post_name
   * @param  mixed $post_type
   * @return void
   */
  function chotu_get_cpost($post_name,$post_type){
    global $wpdb;
    return $wpdb->get_row( $wpdb->prepare( "
        SELECT *
        FROM $wpdb->posts
        WHERE (post_name = %s AND post_type=%s OR ID=%d) AND post_status='publish'
        ",
        $post_name,$post_type,$post_name
      ) );
  }
}
/**
 * chotu_find_image_post_id
 * Retrieves the attachment ID from the image URL;
 * @param  mixed $url
 * @return void
 */
function chotu_find_image_post_id($url) {
  global $wpdb;
  $postid = $wpdb->get_var($wpdb->prepare("SELECT DISTINCT ID FROM $wpdb->posts WHERE guid='$url'"));
  if ($postid) {
    return $postid;
  }
  return false;
}

if(!function_exists('chotu_woocommerce_cart_empty')){  
  /**
   * chotu_woocommerce_cart_empty
   *
   * @return void
   */
  function chotu_woocommerce_cart_empty(){
    global $woocommerce;
    if(!is_admin()){
      $woocommerce->cart->empty_cart();
    }
  }
}
if(!function_exists('chotu_price_mrp_check')){
  function chotu_price_mrp_check($chotu_current_captain,$product,$page_type){
    $phone_number = '';
    $ask_price_url= '';
    $captain_id   = $chotu_current_captain;
    $curauth      = get_userdata($captain_id);
    $add_to_cart = '';
    $ask_price_mrp = '';
    $phone_number = $curauth->user_login;
    if(preg_match('/^[0-9]{10}+$/', $phone_number)){
        $phone_number = '91'.$phone_number;
        $ask_price_url =  chotu_whatsApp_share_url($phone_number,get_the_permalink(get_the_ID()).'%0a₹__ ❓%0a');
    }
      // Ravi Logic Starts
      $ask_price_mrp =  '';
      if ($product->get_price() != ''){
        if($product->is_type( 'variable')){
          $add_to_cart   = '<a rel="nofollow" href="'.esc_url( $product->get_permalink() ).'"  class="button add_to_cart_button ajax_add_to_cart"><i class="fa-solid fa-list"></i>Choose</a>';
        } elseif($product->is_type( 'simple') && $page_type !='single'){
          chotu_product_loop_quantity($product);
          if($product->get_price() == 0){
              $ask_price_mrp =  '<span class="ask_price_btn"><a href="'.$ask_price_url.'" class="ask_price_btn">'.do_shortcode('[block id="ask-price"]').'</a></span>';
            }else{
              $ask_price_mrp =  '<span class="ask_price">MRP</span>';
            }
          $add_to_cart   = '<a rel="nofollow" href="'.esc_url( $product->add_to_cart_url() ).'" data-quantity="'.esc_attr( isset( $quantity ) ? $quantity : 1 ).'" data-product_id="'.esc_attr( $product->get_id() ).'" data-product_sku="'.esc_attr( $product->get_sku() ).'" class="button add_to_cart_button ajax_add_to_cart">ADD TO CART</a>';
        }
      }
      // Ravi Logic Ends



      // Nadeem Logic Starts
      // if($product->is_type( 'variable') && $product->get_price() != 0 && $product->get_price() != ''){
      //   $ask_price_mrp =  '<span class="ask_price_btn" id="tag"><span class="ask_price button alt" style="margin-bottom: 0;padding: 3px 3px 3px 3px;display: inline;">MRP</span></span>';
      //   $add_to_cart   = '<a rel="nofollow" href="'.esc_url( $product->get_permalink() ).'"  class="button product_type_simple variable">Choose</a>';
      // }elseif($product->is_type( 'variable') && $product->get_price() == 0){
      //   $ask_price_mrp =  '<span class="ask_price_btn" id="tag"><a href="'.$url.'" class="ask_price button alt">'.do_shortcode('[block id="ask-price"]').'</a></span>';
      //   $add_to_cart   = '<a rel="nofollow" href="'.esc_url( $product->get_permalink() ).'"  class="button product_type_simple variable">Choose</a>';
      // }elseif(!$product->is_type( 'variable') && $product->get_price() != 0 && $product->get_price() != ''){
      //   chotu_product_loop_quantity($product);
      //   $ask_price_mrp =  '<span class="ask_price_btn" id="tag"><span class="ask_price button alt" style="margin-bottom: 0;padding: 3px 3px 3px 3px;display: inline;">MRP</span></span>';
      //   $add_to_cart   = '<a rel="nofollow" href="'.esc_url( $product->add_to_cart_url() ).'" data-quantity="'.esc_attr( isset( $quantity ) ? $quantity : 1 ).'" data-product_id="'.esc_attr( $product->get_id() ).'" data-product_sku="'.esc_attr( $product->get_sku() ).'" class="button product_type_simple add_to_cart_button ajax_add_to_cart">Add to cart</a>';
      // }elseif(!$product->is_type( 'variable') && $product->get_price() == 0){
      //   chotu_product_loop_quantity($product);
      //   $ask_price_mrp =  '<span class="ask_price_btn" id="tag"><a href="'.$url.'" class="ask_price button alt">'.do_shortcode('[block id="ask-price"]').'</a></span>';
      //   $add_to_cart   = '<a rel="nofollow" href="'.esc_url( $product->add_to_cart_url() ).'" data-quantity="'.esc_attr( isset( $quantity ) ? $quantity : 1 ).'" data-product_id="'.esc_attr( $product->get_id() ).'" data-product_sku="'.esc_attr( $product->get_sku() ).'" class="button product_type_simple add_to_cart_button ajax_add_to_cart">Add to cart</a>';
      // }
      // else{
      //  $add_to_cart   =  '<a rel="nofollow" href="javascript:void(0)" data-quantity="'.esc_attr( isset( $quantity ) ? $quantity : 1 ).'" data-product_id="'.esc_attr( $product->get_id() ).'" data-product_sku="'.esc_attr( $product->get_sku() ).'" class="button product_type_simple">No add to cart</a>';
      // }
      // Nadeem Logic Ends
      if($page_type == 'single'){
        echo $ask_price_mrp;
        // add to cart comes from default woocommerce
      }else{
        echo '<table>
                <tr>
                  <td style="width:30%; text-align: center;">'.$ask_price_mrp.'
                  </td>
                  <td style="width:70%; text-align: center;">'.$add_to_cart.'
                  </td>
                </tr>
              </table>';
      }
  }
}

if(!function_exists('chotu_reset_captain')){
  function chotu_reset_captain(){
    setcookie('captain', '', time() - 3600, '/');
    chotu_woocommerce_cart_empty();
  }
}

if(!function_exists('chotu_set_visited_captain_shop_history')){
  function chotu_set_visited_captain_shop_history($captain){
    $timstamp = strtotime(date('Y-m-d H:i:s'));
    if(isset($_COOKIE['mylist'])){
      $mylist = json_decode( sanitize_text_field( wp_unslash( $_COOKIE[ 'mylist' ] ) ),true);
      if(array_key_exists($captain,$mylist)){
        $mylist[$captain] = $timstamp;
        $mylist = wp_json_encode(stripslashes_deep($mylist));
        setcookie('mylist', $mylist, strtotime("+1 year"), "/");
      }else{
        $mylist[$captain] = $timstamp;
        $new_list = wp_json_encode(stripslashes_deep($mylist));
        setcookie('mylist', $new_list, strtotime("+1 year"), "/");
      }
      
    }else{
      $mylist = wp_json_encode( stripslashes_deep(array($captain => $timstamp)));
      setcookie('mylist', $mylist, strtotime("+1 year"), "/");
    }
    //setcookie('captain', null, -1, '/');
  }
}
if(!function_exists('chotu_whatsApp_share_url')){
  function chotu_whatsApp_share_url($number,$text){
    //whatsapp://send?phone='.$phone_number.'&text='
    //$url = "whatsapp://send?phone=$number/?text=$text";
    $url = 'https://wa.me/'.$number.'?text='.$text;
    return $url;
  }
}
if(!function_exists('chotu_get_visited_captain_shop_history')){
  function chotu_get_visited_captain_shop_history(){
    if(isset( $_COOKIE[ 'mylist' ])){
      $mylist = json_decode( sanitize_text_field( wp_unslash( $_COOKIE[ 'mylist' ] ) ),true);
      if(!empty($mylist)){
        arsort($mylist);
      }
      return $mylist;
    }
    return array();
  }
}
function chotu_product_loop_quantity($product){
  $html = '';
  if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
    $html = '<form action="' . esc_url( $product->add_to_cart_url() ) . '" class="cart text-center" method="post" style="margin-bottom: 0 !important;" enctype="multipart/form-data">';
    $html .= woocommerce_quantity_input( array(), $product, false );
    //$html .= chotu_price_mrp_check($chotu_current_captain,$product);
    $html .= '<button style="display:none" type="submit" class="button alt add_to_cart_trigger">' . esc_html( $product->add_to_cart_text() ) . '</button>';
    $html .= '</form>';
    echo $html;
  }
}
if(!function_exists('chotu_show_favorites')){
  function chotu_show_favorites($favorites){
    if(!empty($favorites) && !isset($favorites[0]['posts'])){
      $captain_favorites  = implode(",",$favorites);
      if($captain_favorites !=''){
        if(!empty($favorites)){
          $fav_cat_array = array();
          foreach ($favorites as $key => $value) {
            $terms = get_the_terms( $value, 'product_cat' );
            if(!empty($terms)){
              foreach ($terms as $term) {
                $fav_cat_array[$term->name][$key] = $value;
              }
            }else{
              $fav_cat_array['product_ids'][$key]=$value;
            }
          }
          echo '<div style="background-color:var(--lightprimary);
          margin: 0% -5%;
          padding: 5%;
          border-radius: 5px;">';
          echo do_shortcode('[block id="my-choice"]');
          if(!empty($fav_cat_array)){
            foreach ($fav_cat_array as $key => $fav_cat) {
              $fav_cat  = implode(",",$fav_cat);
              if($key !='product_ids'){
                echo do_shortcode('<h2>'.$key.'</h2>[products ids="'.$fav_cat.'" class="captain_favorites"]');
              }else{
                echo do_shortcode('[products ids="'.$fav_cat.'" class="captain_favorites"]');
              }
            }
          }
          echo '</div>';
        }
      }
    }
  }
}
