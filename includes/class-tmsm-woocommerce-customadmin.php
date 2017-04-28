<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/nicomollet
 * @since      1.0.0
 *
 * @package    Tmsm_Woocommerce_Customadmin
 * @subpackage Tmsm_Woocommerce_Customadmin/includes
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
 * @package    Tmsm_Woocommerce_Customadmin
 * @subpackage Tmsm_Woocommerce_Customadmin/includes
 * @author     Nicolas Mollet <nico.mollet@gmail.com>
 */
class Tmsm_Woocommerce_Customadmin {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Tmsm_Woocommerce_Customadmin_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	public function __construct() {

		$this->plugin_name = 'tmsm-woocommerce-customadmin';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();

		add_action( 'admin_head', array( $this, 'status_badges' ) );

	}

	/**
	 * Status badges
	 */
	function status_badges() {

		$status_pendingpayment = __('Pending payment', 'tmsm-woocommerce-customadmin');
		$status_failed = __('Failed', 'tmsm-woocommerce-customadmin');
		$status_processing = __('Processing', 'tmsm-woocommerce-customadmin');
		$status_completed = __('Completed', 'tmsm-woocommerce-customadmin');
		$status_onhold = __('On-Hold', 'woocommerce');
		$status_cancelled = __('Cancelled', 'woocommerce');
		$status_refunded = __('Refunded', 'woocommerce');

		$css = <<<TXT
<style type="text/css">
 
 .widefat .column-order_status {
   width: 80px;
 }
 
 /* General properties for badge */
 .widefat .column-order_status mark {
   font-size: 0.8em;
   border-radius: 3px;
   height: 2em;
   width: 6em;
   margin: 0 10px;
 }
 
 /* Adjust text placement in badge */
 .widefat .column-order_status mark.pending:after,
 .widefat .column-order_status mark.processing:after,
 .widefat .column-order_status mark.on-hold:after,
 .widefat .column-order_status mark.cancelled:after,
 .widefat .column-order_status mark.completed:after,
 .widefat .column-order_status mark.refunded:after,
 .widefat .column-order_status mark.failed:after {
   padding-top: 0.4em;
   font-weight: bold;
   font-family: inherit;
   font-size: 11px;
 }
 
 /* Pending status */
 .widefat .column-order_status mark.pending {
   background-color: #999; /* Orange */
 }
 
 .widefat .column-order_status mark.pending:after {
   content: "$status_pendingpayment";
   color: #fff;
 }
 
  /* Processing status */
 .widefat .column-order_status mark.processing {
   background-color: #73a724; /* Green */
 }
 
 .widefat .column-order_status mark.processing:after {
   content: "$status_processing";
   color: #ffffff;
 }
 
 /* On-Hold status */
 .widefat .column-order_status mark.on-hold {
   background-color: #999; /* Gray */
 }
 
 .widefat .column-order_status mark.on-hold:after {
   content: "$status_onhold";
   color: #ffffff;
 }
 
 /* Cancelled status */
 .widefat .column-order_status mark.cancelled {
   background-color: #a00; /* Red */
 }
 
 .widefat .column-order_status mark.cancelled:after {
   content: "$status_cancelled";
   color: #ffffff;
 }
 
 /* Completed status */
 .widefat .column-order_status mark.completed {
   background-color: #2ea2cc; /* Blue */
 }
 
 .widefat .column-order_status mark.completed:after {
    content: "$status_completed";
    color: #ffffff;
 }
 
 /* Refunded status */
 .widefat .column-order_status mark.refunded {
   background-color: #000; /* Black */
 }
 
 .widefat .column-order_status mark.refunded:after {
    content: "$status_refunded";
    color: #ffffff;
 }
 
 /* Failed status */
 .widefat .column-order_status mark.failed {
   background-color: #d0c21f; /* Pink */
 }
 
 .widefat .column-order_status mark.failed:after {
    content: "$status_failed";
    color: #ffffff;
 }
 
 </style>
TXT;

		echo $css;
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Tmsm_Woocommerce_Customadmin_Loader. Orchestrates the hooks of the plugin.
	 * - Tmsm_Woocommerce_Customadmin_i18n. Defines internationalization functionality.
	 * - Tmsm_Woocommerce_Customadmin_Admin. Defines all hooks for the admin area.
	 * - Tmsm_Woocommerce_Customadmin_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tmsm-woocommerce-customadmin-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tmsm-woocommerce-customadmin-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tmsm-woocommerce-customadmin-admin.php';

		$this->loader = new Tmsm_Woocommerce_Customadmin_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Tmsm_Woocommerce_Customadmin_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Tmsm_Woocommerce_Customadmin_i18n();

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

		$plugin_admin = new Tmsm_Woocommerce_Customadmin_Admin( $this->get_plugin_name(), $this->get_version() );

		//$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		//$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

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
	 * @return    Tmsm_Woocommerce_Customadmin_Loader    Orchestrates the hooks of the plugin.
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

}
