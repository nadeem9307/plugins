<?php
namespace SCFunnelbuilder;

use SCFunnelbuilder\Scfunnelbuilder_Loader;
use SCFunnelbuilder\Scfunnelbuilder_i18n;
use SCFunnelbuilder\Scfunnelbuilder_Admin;
use SCFunnelbuilder\Scfunnelbuilder_Public;
use SCFunnelbuilder\Menu\ScFunnel_Menus as Menu;
use SCFunnelbuilder\CPT\ScFunnel_CPT as CPT;
use SCFunnelbuilder\Rest\Rest_Server;
use SCFunnelbuilder\Store_Data\ScFunnel_Funnel_Store_Data as Funnel_Store;
use SCFunnelbuilder\Store_Data\ScFunnel_Steps_Store_Data as Step_Store;
use SCFunnelbuilder\Modules\ScFunnel_Modules_Manager as Module_Manager;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://zonvoir.com
 * @since      1.0.0
 *
 * @package    Scfunnelbuilder
 * @subpackage Scfunnelbuilder/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Scfunnelbuilder
 * @subpackage Scfunnelbuilder/includes
 * @author     zonvoir <sales@zonvoir.com>
 */
class Scfunnelbuilder {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Scfunnelbuilder_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	public static $instance = null;

    public $module_manager;

    public $admin;

    public $plugin_public;

    public $template_manager;

    public $menu;

    public $cpt;
	public $funnel_store;

    public $step_store;
 /**
     * Instance.
     *
     * Ensures only one instance of the plugin class is loaded or can be loaded.
     *
     * @since  1.0.0
     * @access public
     * @static
     *
     * @return ScFunnel An instance of the class.
     */
    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
            do_action('scfunnels/loaded');
        }
        return self::$instance;
    }
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SCFUNNELBUILDER_VERSION' ) ) {
			$this->version = SCFUNNELBUILDER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'scfunnelbuilder';
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->init();
		$this->init_hooks();
		$this->init_rest_api();
		add_action( 'plugins_loaded', array($this, 'load_plugin'), 99 );

	}

	

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Scfunnelbuilder_Loader. Orchestrates the hooks of the plugin.
	 * - Scfunnelbuilder_i18n. Defines internationalization functionality.
	 * - Scfunnelbuilder_Admin. Defines all hooks for the admin area.
	 * - Scfunnelbuilder_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-scfunnelbuilder-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-scfunnelbuilder-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-scfunnelbuilder-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-scfunnelbuilder-public.php';

		$this->loader = new Scfunnelbuilder_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Scfunnelbuilder_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Scfunnelbuilder_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Scfunnelbuilder_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_footer', $plugin_admin, 'sc_funnel_admin_footer_script' );
		$this->loader->add_action( 'update_product_id_builder_data', $plugin_admin, 'sc_funnel_update_product_id_builder_data',10,2 );
		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Scfunnelbuilder_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Scfunnelbuilder_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
	public function load_plugin(){
		do_action( 'scfunnelbuilder/init' );
	}
	
	public function init() {
        $this->cpt                     	   = new CPT();
        $this->menu                        = new Menu();
        $this->funnel_store                = new Funnel_Store();
        $this->step_store                  = new Step_Store();
    }
	public function init_hooks() {
        $this->loader->add_action( 'init', $this, 'load_admin_modules' );
    }
	public function load_admin_modules() {
		
        $this->module_manager = new Module_Manager();
		
    }	
	/**
	 * init_rest_api
	 *
	 * @return void
	 */
	public function init_rest_api(){
		$this->loader->add_action( 'init', $this, 'load_rest_api' );
	}
	public function load_rest_api() {
        Rest_Server::instance()->init();
    }
}
