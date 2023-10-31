<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       chotu.com
 * @since      1.0.0
 *
 * @package    Chotu
 * @subpackage Chotu/includes
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
 * @package    Chotu
 * @subpackage Chotu/includes
 * @author     Mohd Nadeem <mohdnadeemzonv@gmail.com>
 */
class Chotu
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Chotu_Loader    $loader    Maintains and registers all hooks for the plugin.
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

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('CHOTU_VERSION')) {
            $this->version = CHOTU_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'chotu';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Chotu_Loader. Orchestrates the hooks of the plugin.
     * - Chotu_i18n. Defines internationalization functionality.
     * - Chotu_Admin. Defines all hooks for the admin area.
     * - Chotu_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-chotu-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-chotu-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-chotu-admin.php';

        /**
         * The class responsible for defining all actions
         *  that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-chotu-public.php';
        /**
         * admin classes files include here
         * */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/includes/class-chotu_product.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/includes/class-all_post_type.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/includes/class-product_taxonomy.php';
        
        /**
         * public  classes files include here
         * */
        if (!is_admin()) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'public/includes/class-chotu_page_init_elements.php';
            require_once plugin_dir_path(dirname(__FILE__)) . 'public/includes/class-chotu_captain_my_account.php';
            require_once plugin_dir_path(dirname(__FILE__)) . 'public/includes/class-chotu_captain_shop.php';

            require_once plugin_dir_path(dirname(__FILE__)) . 'public/includes/class-chotu_product.php';
            require_once plugin_dir_path(dirname(__FILE__)) . 'public/includes/class-chotu_product_taxonomy.php';

            require_once plugin_dir_path(dirname(__FILE__)) . 'public/includes/class-chotu-cart.php';
            require_once plugin_dir_path(dirname(__FILE__)) . 'public/includes/class-chotu-wpforms.php';
            
        }
        $this->loader = new Chotu_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Chotu_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Chotu_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Chotu_Admin($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('after_setup_theme', $plugin_admin, 'chotu_theme_setup', 999);
        $this->loader->add_action('init', $plugin_admin, 'chotu_remove_plugin_image_sizes');
        $this->loader->add_action('intermediate_image_sizes_advanced', $plugin_admin, 'chotu_disable_plugin_image_sizes');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new Chotu_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        $this->loader->add_action('wp_ajax_chotu_update_captain_pics', $plugin_public, 'chotu_update_captain_pics');
        $this->loader->add_action('wp_ajax_nopriv_chotu_update_captain_pics', $plugin_public, 'chotu_update_captain_pics');
        
        $this->loader->add_action('wp_ajax_chotu_remove_gallery_image', $plugin_public, 'chotu_remove_gallery_image');
        $this->loader->add_action('wp_ajax_nopriv_chotu_remove_gallery_image', $plugin_public, 'chotu_remove_gallery_image');
        
        $this->loader->add_filter('template_include', $plugin_public, 'chotu_return_shop_template');
        $this->loader->add_filter('auth_cookie_expiration', $plugin_public, 'chotu_set_captain_login_expiration', 99, 3);
        
        $this->loader->add_action('wp_ajax_chotu_get_captain_vCard', $plugin_public, 'chotu_get_captain_vCard');
        $this->loader->add_action('wp_ajax_nopriv_chotu_get_captain_vCard', $plugin_public,'chotu_get_captain_vCard');

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Chotu_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

}