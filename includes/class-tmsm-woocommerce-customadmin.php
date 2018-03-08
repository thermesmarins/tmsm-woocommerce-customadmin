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
		$this->version     = '1.1.2';

		$this->load_dependencies();
		$this->set_locale();
		$this->register_processedstatus();
		$this->define_admin_hooks();

		add_action( 'login_redirect', array( $this, 'redirect_shop_managers' ), 100, 3 );

		add_filter( 'woocommerce_checkout_get_value', array( $this, 'checkout_default_values' ), 10, 2 );

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

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );



		$this->loader->add_filter( 'wc_order_statuses', $plugin_admin, 'wc_get_order_statuses', 999 );
		$this->loader->add_filter( 'admin_head', $plugin_admin, 'color_badges', 999 );
		$this->loader->add_filter( 'admin_head', $plugin_admin, 'menu_icons', 999 );
		$this->loader->add_filter( 'admin_head', $plugin_admin, 'hide_woocommerce', 999 );
		$this->loader->add_filter( 'admin_head', $plugin_admin, 'rename_menu', 999 );
		$this->loader->add_filter( 'admin_head', $plugin_admin, 'menu_customers', 999 );
		$this->loader->add_filter( 'admin_head', $plugin_admin, 'order_export', 999 );

		$this->loader->add_filter( 'admin_menu', $plugin_admin, 'menu_mailjet', 999 );

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
		$this->loader->add_filter( 'wc_order_statuses', $plugin_admin, 'rename_order_statuses', 10, 1 );
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


	}

	/**
	 * Define the locale for this plugin for internationalization.
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
		$this->loader->add_filter( 'wc_order_statuses', $plugin_posttypes, 'wc_order_statuses_processed' );


	}

	/**
	 * WooCommerce PDF Vouchers: gift datepicker format
	 *
	 * @param $date_format
	 *
	 * @return string
	 *
	 */
	function woo_vou_recipient_giftdate_format( $date_format ) {
		return 'dd-mm-yy';
	}

	/**
	 * WooCommerce PDF Vouchers: gift date format in cart
	 *
	 * @param $date
	 *
	 * @return string
	 */
	function woo_vou_get_cart_date_format( $date ) {

		if ( strpos( $date, '-' ) ) {

			// Explode $date to get date, month and year parameters
			$date_arr = explode( '-', $date );

			$dateObj = DateTime::createFromFormat( '!M', $date_arr[1] ); // Check month for string format
			if ( ! empty( $dateObj ) ) {
				$date_arr[1] = $dateObj->format( 'm' );
				$date        = implode( '-', $date_arr );
			}

		}

		return $date;
	}

	/**
	 * Shop Managers: redirect to orders
	 *
	 * @param $redirect_to
	 * @param $request
	 * @param $user
	 *
	 * @return string
	 */
	function redirect_shop_managers( $redirect_to, $request, $user ) {

		$redirect_to_orders = admin_url( 'edit.php?post_type=shop_order' );

		//is there a user to check?
		if ( isset( $user->roles ) && is_array( $user->roles ) ) {
			// Default redirect for admins
			if ( in_array( 'administrator', $user->roles ) || in_array( 'editor', $user->roles ) || in_array( 'contributor', $user->roles )
			     || in_array( 'author', $user->roles )
			) {
				return $redirect_to;
			} elseif ( in_array( 'shop_manager', $user->roles ) || in_array( 'shop_order_manager', $user->roles ) ) {
				// Redirect shop_manager and shop_order_manager to the orders page
				return $redirect_to_orders;
			} else {
				// Default redirect for other roles
				return $redirect_to;
			}
		} else {
			// Default redirect for no role
			return $redirect_to;
		}
	}

	/**
	 * Default checkout values
	 *
	 * @param $input
	 * @param $key
	 *
	 * @return string
	 */
	function checkout_default_values( $input, $key ) {
		global $current_user;
		switch ( $key ) :
			case 'billing_first_name':
			case 'shipping_first_name':
				return $current_user->first_name;
				break;

			case 'billing_last_name':
			case 'shipping_last_name':
				return $current_user->last_name;
				break;
			case 'billing_email':
				return $current_user->user_email;
				break;
		endswitch;
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Tmsm_Woocommerce_Customadmin_Loader. Orchestrates the hooks of the plugin.
	 * - Tmsm_Woocommerce_Customadmin_i18n. Defines internationalization functionality.
	 * - Tmsm_Woocommerce_Customadmin_Admin. Defines all hooks for the admin area.
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
