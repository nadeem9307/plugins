<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://zonvoir.com
 * @since             1.0.0
 * @package           Scfunnelbuilder
 *
 * @wordpress-plugin
 * Plugin Name:       Studio Cart Funnel builder
 * Plugin URI:        https://zonvoir.com
 * Description:       Drag and drop sale funnel builder to increase sale of order.
 * Version:           1.0.0
 * Author:            zonvoir
 * Author URI:        https://zonvoir.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       scfunnelbuilder
 * Domain Path:       /languages
 */
use SCFunnelbuilder\Scfunnelbuilder;
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SCFUNNELBUILDER_VERSION', '1.0.0' );
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SC_FUNNEL_BUILDER_VERSION', '1.0.0' );

if ( ! defined( 'SC_FUNNEL_FILE' ) ) {
	define('SC_FUNNEL_FILE', __FILE__);
}

if ( ! defined( 'SC_FUNNEL_PATH' ) ) {
	define('SC_FUNNEL_PATH', dirname(SC_FUNNEL_FILE));
}

if ( ! defined( 'SC_FUNNEL_BASE' ) ) {
	define('SC_FUNNEL_BASE', plugin_basename(SC_FUNNEL_FILE));
}

if ( ! defined( 'SC_FUNNEL_DIR' ) ) {
	define('SC_FUNNEL_DIR', plugin_dir_path(SC_FUNNEL_FILE));
}

if ( ! defined( 'SC_FUNNEL_URL' ) ) {
	define('SC_FUNNEL_URL', plugins_url('/', SC_FUNNEL_FILE));
}

if ( ! defined( 'SC_FUNNEL_DIR_URL' ) ) {
	define('SC_FUNNEL_DIR_URL', plugin_dir_url(SC_FUNNEL_FILE));
}

if ( ! defined( 'GET_SC_FUNNEL_HOME_URL' ) ) {
	define('GET_SC_FUNNEL_HOME_URL', 'zonvoirdemo.in');
	
}
define('SC_FUNNEL_TEMPLATE_URL', 'https://demo.studiocart.co/wp-json/wp/v2/');
define('SC_FUNNEL_MAIN_PAGE_SLUG', 'sc_funnels');
define('SC_FUNNEL_SETTINGS_SLUG', 'sc_funnel_settings');
define('SC_FUNNEL_EDIT_FUNNEL_SLUG', 'edit_funnel');
define('SC_FUNNEL_FUNNELS_POST_TYPE', 'scfunnels');
define('SC_FUNNEL_STEPS_POST_TYPE', 'scfunnel_steps');
define('SC_FUNNEL_CREATE_FUNNEL_SLUG', 'create_funnel');
define('SC_FUNNEL_TEMPLATES_OPTION_KEY', 'scfunnels_remote_templates');
define('SC_FUNNEL_FUNNEL_PER_PAGE', 10);
define('SC_FUNNEL_TESTS', false);
define('SC_FUNNEL_ACTIVE_PLUGINS', apply_filters('active_plugins', get_option('active_plugins')));
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-scfunnelbuilder-activator.php
 */
function activate_scfunnelbuilder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-scfunnelbuilder-activator.php';
	Scfunnelbuilder_Activator::activate();
}


/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-scfunnelbuilder-deactivator.php
 */
function deactivate_scfunnelbuilder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-scfunnelbuilder-deactivator.php';
	Scfunnelbuilder_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_scfunnelbuilder' );
register_deactivation_hook( __FILE__, 'deactivate_scfunnelbuilder' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-scfunnelbuilder.php';
 
       
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_scfunnelbuilder() {

	$plugin = new Scfunnelbuilder();
	$plugin->run();

}
function scfunnelbuilder() {
	return Scfunnelbuilder::get_instance();
}

/**
* Include the autoloader
*/
if (file_exists(__DIR__ . '/includes/core/init/autoload.php')) {
	include __DIR__ . '/includes/core/init/autoload.php';
}
if(!function_exists('dd')){
	function dd($data){
		echo "<pre>";
		print_r($data);
		echo "</pre>";
		die;
	}
}
add_action('plugin_loaded','scfunnel_check_parent_plugin_active', 99);
function scfunnel_check_parent_plugin_active(){
	require_once(ABSPATH . '/wp-admin/includes/plugin.php');
	if (is_plugin_active('studiocart/studiocart.php') || is_plugin_active('studiocart-pro/studiocart.php')) {
		return true;
		//add_action('admin_notices', 'scfunnel_admin_notice');
	}else{
		deactivate_plugins('scfunnelbuilder/scfunnelbuilder.php');
		add_action('admin_notices', 'scfunnel_admin_notice');
	}
}
function scfunnel_admin_notice(){
	?>
	<div class="notice notice-error is-dismissible">
        <p><?php _e( 'Plugin deactivated. Please activate studiocart plugin first!', 'scfunnelbuilder' ); ?></p>
    </div>
<?php 
}

run_scfunnelbuilder();
