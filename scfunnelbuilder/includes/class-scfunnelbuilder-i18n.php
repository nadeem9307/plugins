<?php
namespace SCFunnelbuilder;
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://zonvoir.com
 * @since      1.0.0
 *
 * @package    Scfunnelbuilder
 * @subpackage Scfunnelbuilder/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Scfunnelbuilder
 * @subpackage Scfunnelbuilder/includes
 * @author     zonvoir <sales@zonvoir.com>
 */
class Scfunnelbuilder_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'scfunnelbuilder',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
