<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://flexithemes.com
 * @since             1.0.0
 * @package           Seo_Checklist
 *
 * @wordpress-plugin
 * Plugin Name:       SEO Checklist
 * Description:       The Ultimate Wordpress SEO Checklist! Use it from the Tools menu on the left.
 * Version:           1.0.2
 * Author:            Flexithemes
 * Author URI:        https://flexithemes.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       seo-checklist
 * Domain Path:       /languages
 */

if ( ! function_exists( 'seochecklist_seo_fs' ) ) {
    // Create a helper function for easy SDK access.
    function seochecklist_seo_fs() {
        global $seochecklist_seo_fs;

        if ( ! isset( $seochecklist_seo_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $seochecklist_seo_fs = fs_dynamic_init( array(
                'id'                  => '4022',
                'slug'                => 'seo_checklist',
                'type'                => 'plugin',
                'public_key'          => 'pk_e27e6d80ba02e2acce03a45cf0420',
                'is_premium'          => false,
                // If your plugin is a serviceware, set this option to false.
                'has_premium_version' => true,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'trial'               => array(
                    'days'               => 7,
                    'is_require_payment' => true,
                ),
                'menu'                => array(
                    'slug'           => 'seo_checklist',
                    'parent'         => array(
                        'slug' => 'tools.php',
                    ),
                ),
            ) );
        }

        return $seochecklist_seo_fs;
    }

    // Init Freemius.
    seochecklist_seo_fs();
    // Signal that SDK was initiated.
    do_action( 'seochecklist_seo_fs_loaded' );
}

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SEOCHECKLIST_SEOCHECKLIST_VERSION', '1.0.2' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-seo-checklist-activator.php
 */
function SEOCHECKLIST_activate_seo_checklist() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-seo-checklist-activator.php';
	SEOCHECKLIST_Seo_Checklist_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-seo-checklist-deactivator.php
 */
function SEOCHECKLIST_deactivate_seo_checklist() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-seo-checklist-deactivator.php';
	SEOCHECKLIST_Seo_Checklist_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'SEOCHECKLIST_activate_seo_checklist' );
register_deactivation_hook( __FILE__, 'SEOCHECKLIST_deactivate_seo_checklist' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-seo-checklist.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-seo-checklist-loader.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function SEOCHECKLIST_run_seo_checklist() {
    
	new SEOCHECKLIST_Seo_Checklist();
	$plugin = new SEOCHECKLIST_Seo_Checklist_Loader();
	$plugin->run();

}
SEOCHECKLIST_run_seo_checklist();
