<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              chotu.com
 * @since             1.1
 * @package           Chotu
 *
 * @wordpress-plugin
 * Plugin Name:       chotu
 * Plugin URI:        chotu.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.4.8
 * Author:            Mohd Nadeem
 * Author URI:        chotu.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       chotu
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
define( 'CHOTU_VERSION', '1.4.8' );
define('CHOTU_PLUGIN_DIR', plugin_dir_url(__FILE__));
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-chotu-activator.php
 */
function activate_chotu() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-chotu-activator.php';
	Chotu_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-chotu-deactivator.php
 */
function deactivate_chotu() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-chotu-deactivator.php';
	Chotu_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_chotu' );
register_deactivation_hook( __FILE__, 'deactivate_chotu' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path(__FILE__ ) . 'includes/chotu-core-functions.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-chotu.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';


//require plugin_dir_path( __FILE__ ) . 'includes/chotu-shop-classes/abstracts/abstract-chotu-term.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_chotu() {
	$plugin = new Chotu();
	$plugin->run();
}
run_chotu();
