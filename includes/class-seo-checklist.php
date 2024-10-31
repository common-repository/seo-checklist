<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://flexithemes.com
 * @since      1.0.0
 *
 * @package    Seo_Checklist
 * @subpackage Seo_Checklist/includes
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
 * @package    Seo_Checklist
 * @subpackage Seo_Checklist/includes
 * @author     Flexithemes <contact@flexithemes.com>
 */
class SEOCHECKLIST_Seo_Checklist {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Seo_Checklist_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $SEOCHECKLIST_loader;
        
        protected $SEOCHECKLIST_checks;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $SEOCHECKLIST_plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $SEOCHECKLIST_version;

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
	
		if ( defined( 'SEOCHECKLIST_SEOCHECKLIST_VERSION' ) ) {
			$this->SEOCHECKLIST_version = SEOCHECKLIST_SEOCHECKLIST_VERSION;
		} else {
			$this->SEOCHECKLIST_version = '1.0.0';
		}
		$this->SEOCHECKLIST_plugin_name = 'seo-checklist';

		$this->SEOCHECKLIST_load_dependencies();
		$this->SEOCHECKLIST_set_locale();
		$this->SEOCHECKLIST_define_admin_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Seo_Checklist_Loader. Orchestrates the hooks of the plugin.
	 * - Seo_Checklist_i18n. Defines internationalization functionality.
	 * - Seo_Checklist_Admin. Defines all hooks for the admin area.
	 * - Seo_Checklist_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function SEOCHECKLIST_load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-seo-checklist-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-seo-checklist-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-seo-checklist-admin.php';
                
                $this->loader = new SEOCHECKLIST_Seo_Checklist_Loader();
                
                /**
		 * Display Admin Page
		 */
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/seo-checklist-admin-display.php';
                
                /**
		 * Run SEO Checks
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-seo-checks.php';

                $this->SEOCHECKLIST_checks = new SEOCHECKLIST_SEOChecks();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Seo_Checklist_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function SEOCHECKLIST_set_locale() {

		$SEOCHECKLIST_plugin_i18n = new SEOCHECKLIST_Seo_Checklist_i18n();

		add_action( 'plugins_loaded', array($SEOCHECKLIST_plugin_i18n, 'load_plugin_textdomain') );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function SEOCHECKLIST_define_admin_hooks() {

		$SEOCHECKLIST_plugin_admin = new SEOCHECKLIST_Seo_Checklist_Admin( $this->SEOCHECKLIST_get_plugin_name(), $this->SEOCHECKLIST_get_version() );

		add_action( 'admin_enqueue_scripts', array($SEOCHECKLIST_plugin_admin, 'enqueue_styles') );
		add_action( 'admin_enqueue_scripts', array($SEOCHECKLIST_plugin_admin, 'enqueue_scripts') );
        add_action( 'admin_menu', array($SEOCHECKLIST_plugin_admin, 'add_sublevel_menu' ));

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function SEOCHECKLIST_run() {
		
		$this->SEOCHECKLIST_loader->SEOCHECKLIST_run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function SEOCHECKLIST_get_plugin_name() {
		return $this->SEOCHECKLIST_plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Seo_Checklist_Loader    Orchestrates the hooks of the plugin.
	 */
	public function SEOCHECKLIST_get_loader() {
		return $this->SEOCHECKLIST_loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function SEOCHECKLIST_get_version() {
		return $this->SEOCHECKLIST_version;
	}

}
