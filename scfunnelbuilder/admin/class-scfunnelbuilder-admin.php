<?php
namespace SCFunnelbuilder;
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://zonvoir.com
 * @since      1.0.0
 *
 * @package    Scfunnelbuilder
 * @subpackage Scfunnelbuilder/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Scfunnelbuilder
 * @subpackage Scfunnelbuilder/admin
 * @author     zonvoir <sales@zonvoir.com>
 */
class Scfunnelbuilder_Admin {

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
		 * defined in Scfunnelbuilder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Scfunnelbuilder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/scfunnelbuilder-admin.css', array(), $this->version, 'all' );
		if( isset($_GET['page']) && ( 'edit_funnel' === $_GET['page'])){
			wp_enqueue_style( $this->plugin_name.'-funnel', plugin_dir_url( __FILE__ ) . 'js/dist/assets/index.css', array(), $this->version, 'all' );
			
		}
		

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
		 * defined in Scfunnelbuilder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Scfunnelbuilder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/scfunnelbuilder-admin.js', array( 'jquery' ), $this->version, false );
		// wp_enqueue_script( $this->plugin_name . '-funnel-window', plugin_dir_url(__FILE__) . 'js/dist/assets/index-ed497790.js', array( 'jquery'), $this->version, false );



		$is_sc_installed = 'no';
		$is_scfunnelbuilder_installed = 'no';
		if (is_plugin_active('studiocart/studiocart.php') || is_plugin_active('studiocart-pro/studiocart.php')) {
			$is_sc_installed = 'yes';
		}
		if (is_plugin_active('scfunnelbuilder/scfunnelbuilder.php')) {
			$is_scfunnelbuilder_installed = 'yes';
		}
		 
		 $funnel_id 		= '';
		 $funnel_title 	= '';
		 $step_id 		= '';
		 if (isset($_GET['id'])) {
			 $funnel_id 		= $_GET['id'];
			 $funnel_title 	= html_entity_decode(get_the_title($funnel_id));
			 if (isset($_GET['step_id'])) {
				 $step_id = filter_input(INPUT_GET, 'step_id', FILTER_VALIDATE_INT);
			 }
		 }

		 /**
		  * Get funnel preview link 
		 */
		 $steps 					= get_post_meta( $funnel_id, '_steps_order', true );
		 $funnel_preview_link 	= '#';
		 $response['success'] = false;
		 if ($steps) {
			 if ( isset($steps[0]) && $steps[0]['id'] ) {
				 $funnel_preview_link = get_post_permalink($steps[0]['id']);
			 }
		 }
		$settings = get_option('_scfunnels_general_settings');
		wp_localize_script( $this->plugin_name, 'ScFunnelVars', array(
			'ajaxurl' 					=> admin_url( 'admin-ajax.php' ),
			'rest_api_url' 				=> get_rest_url(),
			'security' 					=> wp_create_nonce('scfunnel-admin'),
			'admin_url' 				=> admin_url(),
			'edit_funnel_url' 			=> admin_url('admin.php?page=edit_funnel'),
			'i18n'                      => array( 'scfunnel' => $this->get_scfunnel_locale_data( 'scfunnel' ) ),
			'is_sc_installed' 			=> $is_sc_installed,
			'isAnyPluginMissing' 		=> ScFunnel_functions::is_any_plugin_missing(),
			// 'products' 					=> $products,
			'funnel_id' 				=> $funnel_id,
			'builder_type' 				=> $settings['builder'] ?? 'no',
			'builder_id' 				=> $settings['builder_id'] ?? '0',
			'step_id' 					=> $step_id,
			'funnel_title' 				=> $funnel_title,
			'funnel_preview_link' 		=> $funnel_preview_link,
			'site_url'	 				=> site_url(),
			'image_path' 				=> SC_FUNNEL_URL . 'admin/assets/images',
			'placeholder_image_path' 	=> SC_FUNNEL_URL . 'admin/assets/images/ob_placeholder.png',
			'nonce' 					=> wp_create_nonce('wp_rest'),
			'currentUrl'				=> (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
		));

	}
	/**
	 * Returns Jed-formatted localization data.
	 *
	 * @param string $domain Translation domain.
	 *
	 * @return array
	 */
	public function get_scfunnel_locale_data( $domain, $language_dir = null ) {
		$plugin_translations = $this->get_translations_for_plugin_domain( $domain, $language_dir );
		$translations = get_translations_for_domain( $domain );

		$locale = array(
			'domain'      => $domain,
			'locale_data' => array(
				$domain => array(
					'' => array(
						'domain' => $domain,
						'lang'   => is_admin() ? get_user_locale() : get_locale(),
					),
				),
			),
		);

		if ( ! empty( $translations->headers['Plural-Forms'] ) ) {
			$locale['locale_data'][ $domain ]['']['plural_forms'] = $translations->headers['Plural-Forms'];
		} else if ( ! empty( $plugin_translations['header'] ) ) {
			$locale['locale_data'][ $domain ]['']['plural_forms'] = $plugin_translations['header']['Plural-Forms'];
		}

		$entries = array_merge( $plugin_translations['translations'], $translations->entries );

		foreach ( $entries as $msgid => $entry ) {
			$locale['locale_data'][ $domain ][ $msgid ] = $entry->translations;
		}

		return $locale;
	}
	/**
	 * Get translactions for WePos plugin
	 *
	 * @param string $domain
	 * @param string $language_dir
	 *
	 * @return array
	 */
	public function get_translations_for_plugin_domain( $domain, $language_dir = null ) {
		if ( $language_dir == null ) {
			$language_dir      = SC_FUNNEL_PATH . '/languages/';
		}
		$languages     = get_available_languages( $language_dir );
		$get_site_lang = is_admin() ? get_user_locale() : get_locale();
		$mo_file_name  = $domain . '-' . $get_site_lang;
		$translations  = [];

		if ( in_array( $mo_file_name, $languages ) && file_exists( $language_dir . $mo_file_name . '.mo' ) ) {
			$mo = new \MO();
			if ( $mo->import_from_file( $language_dir . $mo_file_name . '.mo' ) ) {
				$translations = $mo->entries;
			}
		}

		return [
			'header'       => isset( $mo ) ? $mo->headers : '',
			'translations' => $translations,
		];
	}
	public function sc_funnel_admin_footer_script(){
		?>
		<?php
		if( isset($_GET['page']) && ( 'edit_funnel' === $_GET['page'] ) ) {
			wp_enqueue_script( $this->plugin_name . '-funnel-js', plugin_dir_url(__FILE__) . 'js/dist/assets/index.js', array( 'jquery'), $this->version, false );
		}
	}
	public function sc_funnel_update_product_id_builder_data($funnel_id, $step_id){
		$builder_data 	= get_post_meta($step_id,'_elementor_data',true);
		$step_product_id = get_post_meta($step_id,'step_product_id',true);
		array_walk_recursive($builder_data,function(&$item,$key) use ($step_product_id){
			if($key=='cf_id'){
				$item= $step_product_id; // Do This!
			}
		});
		update_post_meta($step_id,'_elementor_data',$builder_data);
	}

}
