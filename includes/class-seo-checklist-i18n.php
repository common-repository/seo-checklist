<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://flexithemes.com
 * @since      1.0.0
 *
 * @package    Seo_Checklist
 * @subpackage Seo_Checklist/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Seo_Checklist
 * @subpackage Seo_Checklist/includes
 * @author     Flexithemes <contact@flexithemes.com>
 */
class SEOCHECKLIST_Seo_Checklist_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'seo-checklist',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
