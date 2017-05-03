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
		$this->version = '1.0.3';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();

		add_action( 'admin_head', array( $this, 'status_badges' ) );
		add_action( 'admin_head', array( $this, 'menu_icons' ) );
		add_action( 'admin_menu', array( $this, 'menu_icons' ) );
		add_action( 'admin_menu', array( $this, 'rename_menu' ), 999 );
		add_action( 'login_redirect', array( $this, 'redirect_shop_managers' ), 100, 3 );

	}

	/**
	 * Rename WooCommerce menu to Orders
	 */
	function rename_menu()
	{
		global $menu;
		// Pinpoint menu item
		$woo = self::recursive_array_search( 'WooCommerce', $menu );
		// Validate
		if( !$woo )
			return;
		$menu[$woo][0] = __('Orders', 'woocommerce');
	}

	/**
	 * Recursive array search
	 *
	 * @param $needle
	 * @param $haystack
	 *
	 * @return bool|int|string
	 */
	public static function recursive_array_search( $needle, $haystack )
	{
		foreach( $haystack as $key => $value )
		{
			$current_key = $key;
			if(
				$needle === $value
				|| (
					is_array( $value )
					&& self::recursive_array_search( $needle, $value ) !== false
				)
			)
			{
				return $current_key;
			}
		}
		return false;
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

		$redirect_to_orders = admin_url( 'edit.php?post_type=shop_order');

		//is there a user to check?
		if ( isset( $user->roles ) && is_array( $user->roles ) ) {
			//check for admins
			if ( in_array( 'administrator', $user->roles ) || in_array( 'editor', $user->roles ) || in_array( 'contributor', $user->roles ) || in_array( 'author', $user->roles ) ) {
				// redirect them to the default place
				return $redirect_to;
			} elseif ( in_array( 'shop_manager', $user->roles ) ||  in_array( 'shop_order_manager', $user->roles ) ) {
				//Redirect shop managers to the orders page
				return $redirect_to_orders;
			} else {
				return home_url();
			}
		} else {
			return $redirect_to;
		}
	}


	/**
	 * Menus icons
	 */
	function menu_icons(){
		echo '<style type="text/css">';
		echo '#adminmenu #toplevel_page_woocommerce .menu-icon-generic div.wp-menu-image:before{content: "\f174" !important;font-family: "dashicons" !important;}';
		echo '#adminmenu #menu-posts-shop_order     .menu-icon-shop_order div.wp-menu-image:before{content: "\f174" !important;font-family: "dashicons" !important;}';
		echo '#adminmenu #menu-posts-shop_coupon    .menu-icon-shop_coupon div.wp-menu-image:before{content: "\f524" !important;font-family: "dashicons" !important;}';
		echo '#adminmenu #menu-posts-product        .menu-icon-product div.wp-menu-image:before{content: "\f312" !important;font-family: "dashicons" !important;}';
		echo '#adminmenu #toplevel_page_wc-reports  .menu-icon-generic div.wp-menu-image:before{content: "\f239" !important;font-family: "dashicons" !important; font-size: 20px !important;}';
		echo '</style>';
	}

	/**
	 * Status badges
	 */
	function status_badges() {

		$status_pendingpayment = _x('Pending payment', 'Order status', 'woocommerce' );
		$status_failed = _x('Failed', 'Order status', 'woocommerce' );
		$status_processing = _x('Processing', 'Order status', 'woocommerce' );
		$status_completed = _x('Completed', 'Order status', 'woocommerce' );
		$status_onhold = _x('On hold', 'Order status', 'woocommerce' );
		$status_cancelled = _x( 'Cancelled', 'Order status', 'woocommerce' );
		$status_refunded = _x('Refunded', 'Order status', 'woocommerce' );

		$css = <<<TXT
<style type="text/css">
 
 .wp-list-table .manage-column.column-order_status,
 .wp-list-table .type-shop_order  .column-order_status {
   width: 80px;
 }
 
 /* General properties for badge */
 .wp-list-table .type-shop_order  .column-order_status mark {
   font-size: 0.8em;
   border-radius: 3px;
   height: 2em;
   width: 6em;
   margin: 0 10px;
 }
 
 /* Adjust text placement in badge */
 .wp-list-table .type-shop_order  .column-order_status mark.pending:after,
 .wp-list-table .type-shop_order  .column-order_status mark.processing:after,
 .wp-list-table .type-shop_order  .column-order_status mark.on-hold:after,
 .wp-list-table .type-shop_order  .column-order_status mark.cancelled:after,
 .wp-list-table .type-shop_order  .column-order_status mark.completed:after,
 .wp-list-table .type-shop_order  .column-order_status mark.refunded:after,
 .wp-list-table .type-shop_order  .column-order_status mark.failed:after {
   padding-top: 0.4em;
   font-weight: bold;
   font-family: inherit;
   font-size: 11px;
 }
 
 /* Pending status */
 .wp-list-table .type-shop_order  .column-order_status mark.pending {
   background-color: #999; /* Gray */
 }
 
 .wp-list-table .type-shop_order  .column-order_status mark.pending:after {
   content: "$status_pendingpayment";
   color: #fff;
 }
 
  /* Processing status */
 .wp-list-table .type-shop_order  .column-order_status mark.processing {
   background-color: #73a724; /* Green */
 }
 
 .wp-list-table .type-shop_order  .column-order_status mark.processing:after {
   content: "$status_processing";
   color: #ffffff;
 }
 
 /* On-Hold status */
 .wp-list-table .type-shop_order  .column-order_status mark.on-hold {
   background-color: #999; /* Gray */
 }
 
 .wp-list-table .type-shop_order  .column-order_status mark.on-hold:after {
   content: "$status_onhold";
   color: #ffffff;
 }
 
 /* Cancelled status */
 .wp-list-table .type-shop_order  .column-order_status mark.cancelled {
   background-color: #a00; /* Red */
 }
 
 .wp-list-table .type-shop_order  .column-order_status mark.cancelled:after {
   content: "$status_cancelled";
   color: #ffffff;
 }
 
 /* Completed status */
 .wp-list-table .type-shop_order  .column-order_status mark.completed {
   background-color: #2ea2cc; /* Blue */
 }
 
 .wp-list-table .type-shop_order  .column-order_status mark.completed:after {
    content: "$status_completed";
    color: #ffffff;
 }
 
 /* Refunded status */
 .wp-list-table .type-shop_order  .column-order_status mark.refunded {
   background-color: #000; /* Black */
 }
 
 .wp-list-table .type-shop_order  .column-order_status mark.refunded:after {
    content: "$status_refunded";
    color: #ffffff;
 }
 
 /* Failed status */
 .wp-list-table .type-shop_order  .column-order_status mark.failed {
   background-color: #d0c21f; /* Yellow */
 }
 
 .wp-list-table .type-shop_order  .column-order_status mark.failed:after {
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
