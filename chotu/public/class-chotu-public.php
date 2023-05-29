<?php
use JeroenDesloovere\VCard\VCard;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       chotu.com
 * @since      1.0.0
 *
 * @package    Chotu_main
 * @subpackage Chotu_main/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Chotu_main
 * @subpackage Chotu_main/public
 * @author     Mohd Nadeem <mohdnadeemzonv@gmail.com>
 */
class Chotu_Public{
    /**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Chotu_main_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Chotu_main_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/chotu-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Chotu_main_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Chotu_main_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		 $localize = array(
        	'ajaxurl' => admin_url( 'admin-ajax.php' )
    	);
		wp_register_script($this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/chotu_public.js', '', '', true);
		wp_localize_script( 'chotu', 'chotu', $localize);
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/chotu_public.js', array( 'jquery' ), $this->version, false );

	}



	/**
     * chotu_return_shop_template
     *
     * @param  mixed $template
     * @return void
	 * to load the template for captain shop from public folder
     */
    public function chotu_return_shop_template ($template) {
        $file = '';
        if ( is_author() ) {
                $author = get_user_by( 'slug', get_query_var( 'author_name' ) );
                if(in_array('captain',$author->roles)){
					$file   = 'captain.php'; // the name of your custom template
					$find[] = $file;
					$find[] = plugin_dir_path( __FILE__ ).'public/templates/' . $file; // name of folder it could be in, in user's theme
            	}
            if ( $file ) {
                $template       = locate_template( array_unique( $find ) );
                if ( ! $template ) { 
                        // if not found in theme, will use your plugin version
                    $template = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' . $file;
                }
            }
        }
		if ( is_page('oye') ) {
			$file = 'oye.php';
			$find[] = plugin_dir_path( __FILE__ ).'public/templates/' . $file; // name of folder it could be in, in user's theme
		if ( $file ) {
			$template       = locate_template( array_unique( $find ) );
			if ( ! $template ) { 
				$template = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' . $file;
			}
		}
	}
        //dd($template);
        return $template;
    }	



	/**
	 * chotu_set_captain_login_expiration
	 *
	 * @param  mixed $seconds
	 * @param  mixed $user_id
	 * @param  mixed $remember
	 * @return void
	 * to set captain login expiration
	 * once captain is logged in, login will be valid for 1 year unless captain logs out
	 */
	public function chotu_set_captain_login_expiration($seconds, $user_id, $remember){
		//if "remember me" is checked;
		$captain = get_user_by('ID',$user_id);
		$user_roles = $captain->roles;
		if(in_array('captain',$user_roles)){
		  if ( $remember ) {
			//WP defaults to 1 year;
			$expiration = 365*24*60*60; //UPDATE HERE;
		  } else {
			  //WP defaults to 48 hrs/2 days;
			  $expiration = 365*24*60*60; //UPDATE HERE;
		  }
		}else{
		  $expiration = 2*24*60*60; //UPDATE HERE;
		}
		return $expiration;
	}
	/**
	 * chotu_update_captain_cp_and_pp
	 * update captain cover pic and captain profile pic using ajax method.
	 * @return void
	 */
	public function chotu_update_captain_cp_and_pp(){
		global $current_user;
		$attach_url='';
		if(chotu_check_is_captain()){
			if(isset($_FILES['captain_cover_pic']) && $_FILES['captain_cover_pic']['type'] !=''){
				$captain_cover_pic = get_user_meta($current_user->ID, 'captain_cover_pic',true);
				$att_id = chotu_find_image_post_id(trim($captain_cover_pic));
				wp_delete_attachment($att_id,true);
				$attach_url = chotu_myaccount_images_upload($_FILES['captain_cover_pic'],'cp');
				update_user_meta($current_user->ID, 'captain_cover_pic', $attach_url);
			}
			if(isset($_FILES['captain_display_pic']) && $_FILES['captain_display_pic']['name'] !=''){
				$captain_display_pic = get_user_meta($current_user->ID, 'captain_display_pic',true);
				$att_id = chotu_find_image_post_id(trim($captain_display_pic));
				wp_delete_attachment($att_id,true);
				$attach_url = chotu_myaccount_images_upload($_FILES['captain_display_pic'],'dp');
				update_user_meta($current_user->ID, 'captain_display_pic', $attach_url);
			}
			if(isset($_FILES['captain_gallery'])){
				$filename = array();
				$captain_gallery = unserialize(get_user_meta($current_user->ID, 'captain_gallery',true));
				if($captain_gallery){
					$countfiles = count($_FILES['captain_gallery']['name']);
					$total_files = count($captain_gallery) + $countfiles;
					if($total_files < 6){
						for($i = 0;$i < $countfiles; $i++){
							$filename['name'] 		= $_FILES['captain_gallery']['name'][$i];
							$filename['type'] 		= $_FILES['captain_gallery']['type'][$i];
							$filename['tmp_name'] 	= $_FILES['captain_gallery']['tmp_name'][$i];
							$filename['error'] 		= $_FILES['captain_gallery']['error'][$i];
							$filename['size'] 		= $_FILES['captain_gallery']['size'][$i];
							$attach_url = chotu_myaccount_images_upload($filename,'captain_gallery');
							array_push($captain_gallery,$attach_url);
						}
						update_user_meta($current_user->ID, 'captain_gallery', serialize($captain_gallery));
					}else{
						return wp_send_json_error( array('message'=>'☹️ Upload Max 5 Files allowed') );
					}
				}else{
					$captain_gallery = array();
					$countfiles = count($_FILES['captain_gallery']['name']);
					if($countfiles < 6){
						for($i = 0;$i < $countfiles; $i++){
							$filename['name'] 		= $_FILES['captain_gallery']['name'][$i];
							$filename['type'] 		= $_FILES['captain_gallery']['type'][$i];
							$filename['tmp_name'] 	= $_FILES['captain_gallery']['tmp_name'][$i];
							$filename['error'] 		= $_FILES['captain_gallery']['error'][$i];
							$filename['size'] 		= $_FILES['captain_gallery']['size'][$i];
							$attach_url = chotu_myaccount_images_upload($filename,'captain_gallery');
							array_push($captain_gallery,$attach_url);
						}
						update_user_meta($current_user->ID, 'captain_gallery', serialize($captain_gallery));
					}else{
						return wp_send_json_error( array('message'=>'☹️ Upload Max 5 Files allowed') );
					}
				}
			}
			
		}
		if($attach_url){
			return wp_send_json_success( array('message'=>'👍') );
		}
		return wp_send_json_error( array('message'=>'☹️ Something went wrong') );
		wp_die();
	}
	public function chotu_remove_gallery_image(){
		global $current_user;
		$captain_gallery = unserialize(get_user_meta($current_user->ID, 'captain_gallery',true));
		if(wp_delete_attachment($_POST['attachment_id'])){
			$key = array_search($_POST['attachment_id'], $captain_gallery);
			unset($captain_gallery[$key]);
			update_user_meta($current_user->ID, 'captain_gallery',serialize($captain_gallery));
			return wp_send_json_success( array('message'=>'👍 removed') );
		}
		return wp_send_json_error( array('message'=>'☹️ Something went wrong') );
		wp_die();
	}
	public function chotu_get_captain(){
		if(chotu_get_captain_id($_POST['captain'])){
			$captain = get_user_by('login',$_POST['captain']);
			return wp_send_json_success( array('url'=>$captain->user_url) );
		}else{
			$text = "Hello buddy, please checkout https://chotu.com, you will find it useful. Thanks";
			$url = chotu_whatsApp_share_url(chotu_append_isd_code($_POST['captain']),$text);
			return wp_send_json_success( array('url'=>$url) );
		}
		wp_die();
	}
	// public function chotu_reset_captain_pages(){
	// 	if(is_home() || is_front_page() || is_page('oye') || 'rootshop' == get_post_type() || is_tax('rootshop_cat') || is_tax('rootshop_tag')){
	// 		dd('yes');
	// 		chotu_reset_captain();
	// 	}
	// }
	public function chotu_cart_trans_text_cart_session(){
        // dd($_POST);
		if(!empty($_POST['chotu_product_items'])){
			WC()->session->set( 'chotu_product_items', $_POST['chotu_product_items'] );
		}
		//if (!isset(WC()->cart->cart_session_data['product_items']) ){
			
		//}
    }
  
}