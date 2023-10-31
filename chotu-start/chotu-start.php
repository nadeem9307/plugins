<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://chotu.com
 * @since             1.0.0
 * @package           Chotu_Start
 *
 * @wordpress-plugin
 * Plugin Name:       Chotu Start
 * Plugin URI:        https://chotu.com
 * Description:       Subscribe for a shopboy
 * Version:           5.0.2
 * Author:            Mohd Nadeem
 * Author URI:        https://chotu.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       chotu-start
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CHOTU_START_VERSION', '5.0.2' );
define( 'CHOTU_START_BASE_DIR', plugin_dir_path( __FILE__ ) );
define( 'CHOTU_START_BASE_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-chotu-start-activator.php
 */
function activate_chotu_start() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-chotu-start-activator.php';
	Chotu_Start_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-chotu-start-deactivator.php
 */
function deactivate_chotu_start() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-chotu-start-deactivator.php';
	Chotu_Start_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_chotu_start' );
register_deactivation_hook( __FILE__, 'deactivate_chotu_start' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-chotu-start.php';
if (file_exists(__DIR__ . '/includes/core/autoload.php')) {
	include __DIR__ . '/includes/core/autoload.php';
}
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_chotu_start() {

	$plugin = new Chotu_Start();
	$plugin->run();

}
run_chotu_start();