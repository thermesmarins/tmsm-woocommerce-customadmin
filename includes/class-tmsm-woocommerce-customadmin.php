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
	 * @var      Tmsm_Woocommerce_Customadmin_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
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
		$this->version     = '1.1.5';

		$this->load_dependencies();
		$this->set_locale();
		$this->register_processedstatus();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}


	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.7
	 *
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Tmsm_Woocommerce_Customadmin_Admin( $this->get_plugin_name(), $this->get_version() );

		// Styles & Scripts
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'login_redirect', $plugin_admin, 'redirect_shop_managers', 10, 3 );

		$this->loader->add_filter( 'admin_head', $plugin_admin, 'color_badges', 999 );
		$this->loader->add_filter( 'admin_head', $plugin_admin, 'menu_icons', 999 );
		$this->loader->add_filter( 'admin_head', $plugin_admin, 'hide_woocommerce', 999 );
		$this->loader->add_filter( 'admin_head', $plugin_admin, 'rename_menu', 999 );
		$this->loader->add_filter( 'admin_head', $plugin_admin, 'menu_customers', 999 );
		$this->loader->add_filter( 'admin_head', $plugin_admin, 'order_export', 999 );

		$this->loader->add_filter( 'admin_menu', $plugin_admin, 'menu_mailjet', 999 );
		$this->loader->add_filter( 'admin_menu', $plugin_admin, 'menu_discounts', 999 );

		$this->loader->add_filter( 'get_rocket_option_wl_plugin_name', $plugin_admin, 'wprocket_name', 10 );

		$this->loader->add_filter( 'display_post_states', $plugin_admin, 'polylang_display_post_states_language', 10, 2 );

		// Users
		$this->loader->add_filter( 'manage_users_columns', $plugin_admin, 'users_columns' );
		$this->loader->add_action( 'manage_users_custom_column', $plugin_admin, 'users_custom_column', 10, 3 );
		$this->loader->add_filter( 'manage_users_sortable_columns', $plugin_admin, 'users_sortable_columns', 10, 1 );

		// WooCommerce
		$this->loader->add_filter( 'woocommerce_enable_admin_help_tab', $plugin_admin, 'woocommerce_enable_admin_help_tab' );
		$this->loader->add_filter( 'woocommerce_admin_order_date_format', $plugin_admin, 'woocommerce_admin_order_date_format' );
		$this->loader->add_action( 'woocommerce_admin_process_product_object', $plugin_admin, 'empty_wprocket_cache_on_save_product' );


		$this->loader->add_filter( 'woocommerce_admin_order_actions', $plugin_admin, 'admin_order_actions', 10, 2 );
		$this->loader->add_filter( 'wc_order_statuses', $plugin_admin, 'rename_order_statuses', 999, 1 );
		$this->loader->add_filter( 'bulk_actions-edit-shop_order', $plugin_admin, 'rename_bulk_actions', 50, 1 );
		$this->loader->add_filter( 'views_edit-shop_order', $plugin_admin, 'rename_views_filters', 50, 1 );
		$this->loader->add_filter( 'woocommerce_admin_order_preview_actions', $plugin_admin, 'woocommerce_admin_order_preview_actions', 50, 2 );
		$this->loader->add_filter( 'woocommerce_admin_order_actions', $plugin_admin, 'woocommerce_admin_order_actions', 10, 2 );
		$this->loader->add_filter( 'admin_action_mark_processed', $plugin_admin, 'admin_action_mark_processed', 10 );
		$this->loader->add_action( 'woocommerce_order_status_processing_to_processed', $plugin_admin, 'status_processing_to_processed', 10, 2 );
		$this->loader->add_action( 'woocommerce_order_status_completed_to_processed', $plugin_admin, 'status_completed_to_processed', 10, 2 );
		$this->loader->add_action( 'woocommerce_order_is_paid_statuses', $plugin_admin, 'woocommerce_order_is_paid_statuses', 10, 1 );
		$this->loader->add_action( 'woocommerce_reports_order_statuses', $plugin_admin, 'woocommerce_reports_order_statuses', 10, 1 );

		remove_action( 'admin_notices', 'woothemes_updater_notice');

		// Options
		$this->loader->add_filter( 'woocommerce_get_settings_checkout', $plugin_admin, 'woocommerce_get_settings_checkout_birthday', 10, 2 );

	}


	/**
	 * Register all of the hooks related to the public area functionality
	 * of the plugin.
	 *
	 * @since    1.1.3
	 *
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Tmsm_Woocommerce_Customadmin_Public( $this->get_plugin_name(), $this->get_version() );

		// Styles & Scripts
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// WooCommerce title & birthday fields
		$this->loader->add_filter( 'mailchimp_sync_user_mergetags', $plugin_public, 'mailchimp_sync_user_mergetags', 10, 2 );
		$this->loader->add_filter( 'woocommerce_checkout_get_value', $plugin_public, 'checkout_default_values_user', 10, 2 );
		$this->loader->add_filter( 'woocommerce_checkout_get_value', $plugin_public, 'checkout_default_values_birthday', 20, 2 );
		$this->loader->add_filter( 'woocommerce_billing_fields', $plugin_public, 'billing_fields_title', 10, 1 );
		$this->loader->add_filter( 'woocommerce_billing_fields', $plugin_public, 'billing_fields_birthday', 20, 1 );
		$this->loader->add_action( 'woocommerce_checkout_update_order_meta', $plugin_public, 'checkout_update_order_meta_title', 10, 2 );
		$this->loader->add_action( 'woocommerce_checkout_update_order_meta', $plugin_public, 'checkout_update_order_meta_birthday', 20, 2 );
		$this->loader->add_action( 'woocommerce_customer_object_updated_props', $plugin_public, 'woocommerce_customer_object_updated_props_birthday', 20, 2 );

	}

	/**
	 * Define the processed status for WooCommerce
	 *
	 * Uses the Tmsm_Woocommerce_Vouchers_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function register_processedstatus() {

		$plugin_posttypes = new Tmsm_Woocommerce_Customadmin_Processedstatus();

		$this->loader->add_filter( 'init', $plugin_posttypes, 'register_post_status_processed' );

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Tmsm_Woocommerce_Customadmin_Loader. Orchestrates the hooks of the plugin.
	 * - Tmsm_Woocommerce_Customadmin_i18n. Defines internationalization functionality.
	 * - Tmsm_Woocommerce_Customadmin_Admin. Defines all hooks for the admin area.
	 * - Tmsm_Woocommerce_Customadmin_Public. Defines all hooks for the public area.
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
		 * The class responsible for defining processed status
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tmsm-woocommerce-customadmin-processedstatus.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tmsm-woocommerce-customadmin-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tmsm-woocommerce-customadmin-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-tmsm-woocommerce-customadmin-public.php';

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
