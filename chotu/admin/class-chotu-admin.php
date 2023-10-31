<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       chotu.com
 * @since      1.0.0
 *
 * @package    Chotu_main
 * @subpackage Chotu_main/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Chotu_main
 * @subpackage Chotu_main/admin
 * @author     Mohd Nadeem <mohdnadeemzonv@gmail.com>
 */
class Chotu_Admin{
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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}
	
	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/chotu-admin.css', array(), $this->version, 'all' );

	}

    /**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/chotu-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * chotu_main_theme_setup
	 *
	 * @return void
	 * to add custom size for an image for whatsapp sharing
	 */
	public function chotu_theme_setup() {
		add_image_size( 'wa_share', 400, 210, true ); 
	    add_theme_support( 'woocommerce', array(
			'thumbnail_image_width' 		=> 300,
			'gallery_thumbnail_image_width' => 100,
			'single_image_width' 			=> 600,
		) );
  		/*  Register menus. */
		register_nav_menus( array(
			'enduser_shop_menu'        		=> __( 'B - End User Shop Menu', 'flatsome' ),
			'captain_loggedin_menu'        	=> __( 'C - Captain Logged in Menu', 'flatsome' ),
		) );
		/**
		 * make rootshop support UX Builder
		 */
		if ( function_exists( 'add_ux_builder_post_type' ) ) {
			add_ux_builder_post_type( 'rootshop' );
		}
	}

	/**
	 * chotu_remove_plugin_image_sizes
	 * remove the thumbnail sizes which are not required by chotu
	 * @return void
	 */
	public function chotu_remove_plugin_image_sizes(){
		remove_image_size('thumbnail');
	    remove_image_size('medium');
	    remove_image_size('medium_large');
	    remove_image_size('large');
	    remove_image_size('2048x2048');
	    remove_image_size('1536x1536');
	    remove_image_size('dgwt-wcas-product-suggestion');
	}
 
	/**
	 * chotu_disable_plugin_image_sizes
	 * to disable plugin image sizes which are no longer used by chotu	
	 * @param  mixed $sizes
	 * @return void
	 */
	public function chotu_disable_plugin_image_sizes($sizes) {
		unset($sizes['thumbnail']); 
		unset($sizes['medium']); 
		unset($sizes['medium_large']); 
		unset($sizes['large']); 
		unset($sizes['2048x2048']); 
		unset($sizes['1536x1536']); 
		unset($sizes['dgwt-wcas-product-suggestion']); 
		return $sizes;
	}	
}