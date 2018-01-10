<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/nicomollet
 * @since      1.0.0
 *
 * @package    Tmsm_Woocommerce_Customadmin
 * @subpackage Tmsm_Woocommerce_Customadmin/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tmsm_Woocommerce_Customadmin
 * @subpackage Tmsm_Woocommerce_Customadmin/admin
 * @author     Nicolas Mollet <nico.mollet@gmail.com>
 */
class Tmsm_Woocommerce_Customadmin_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Tmsm_Woocommerce_Customadmin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tmsm_Woocommerce_Customadmin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tmsm-woocommerce-customadmin-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Tmsm_Woocommerce_Customadmin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tmsm_Woocommerce_Customadmin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmsm-woocommerce-customadmin-admin.js', array( 'jquery' ), $this->version, false );

	}


	/**
	 * Remove tour guide
	 *
	 * @since 1.0.7
	 *
	 * @return bool
	 */
	public function woocommerce_enable_admin_help_tab(){
		return false;
	}


	/**
	 * Empty WP Rocket cache on save product
	 *
	 * @param $product
	 */
	public function empty_wprocket_cache_on_save_product($product){
		// clear cache of the default domain
		if(function_exists('rocket_clean_domain')){
			rocket_clean_domain();
		}
	}


	/**
	 * Customers menu item
	 *
	 * @since  1.0.4
	 * @access public
	 */
	public static function menu_customers() {
		add_submenu_page(
			'woocommerce',
			__( 'Customers', 'woocommerce' ),
			__( 'Customers', 'woocommerce' ),
			'list_users',
			'users.php?role=customer&orderby=id&order=desc'
		);
	}


	/**
	 * Hide WooCommerce menu for shop_order_manager
	 *
	 * @since  1.0.4
	 * @access public
	 */
	function hide_woocommerce() {
		$roles = wp_get_current_user()->roles;
		if ( is_array( $roles ) && isset( $roles[0] ) && $roles[0] == 'shop_order_manager' ):
			echo '<style type="text/css">';
			echo '#adminmenu #toplevel_page_woocommerce {display: none !important;}';
			echo '</style>';
		endif;
	}

	/**
	 * Reports menu for Advanced Order Export For WooCommerce
	 *
	 * @since  1.0.4
	 * @access public
	 */
	public static function order_export() {

		if ( class_exists( 'WC_Order_Export_Admin' ) ):
			add_submenu_page(
				'edit.php?post_type=shop_order',
				__( 'Export Orders', 'woocommerce-order-export' ),
				__( 'Export Orders', 'woocommerce-order-export' ),
				'view_woocommerce_reports',
				'admin.php?page=wc-order-export'

			);
		endif;


	}

	/**
	 * Registered column for display
	 *
	 * @since  1.0.4
	 * @access public
	 */
	public static function users_columns( $columns ) {
		$columns['registered'] = __( 'Registered', 'tmsm-woocommerce-customadmin' );

		return $columns;
	}

	/**
	 * Handles the registered date column output.
	 *
	 * This uses the same code as column_registered, which is why
	 * the date isn't filterable.
	 *
	 * @param $value
	 * @param $column_name
	 * @param $user_id
	 *
	 * @return bool|int|string
	 */
	public static function users_custom_column( $value, $column_name, $user_id ) {

		global $mode;
		$mode = empty( $_REQUEST['mode'] ) ? 'list' : $_REQUEST['mode'];


		if ( 'registered' != $column_name ) {
			return $value;
		} else {
			$user = get_userdata( $user_id );

			if ( is_multisite() && ( 'list' == $mode ) ) {
				$formated_date = __( 'Y/m/d', 'tmsm-woocommerce-customadmin' );
			} else {
				$formated_date = __( 'Y/m/d g:i:s a', 'tmsm-woocommerce-customadmin' );
			}
			$registerdate = mysql2date( $formated_date, $user->user_registered );

			return $registerdate;
		}
	}

	/**
	 * Makes the column sortable
	 *
	 * @since  1.0.4
	 * @access public
	 */
	public static function users_sortable_columns( $columns ) {
		$custom = array(
			// meta column id => sortby value used in query
			'registered' => 'id',
		);

		return wp_parse_args( $custom, $columns );
	}


	/**
	 * Rename WooCommerce menu to Orders
	 */
	function rename_menu() {
		global $menu;
		// Pinpoint menu item
		$woo = self::recursive_array_search( 'WooCommerce', $menu );
		// Validate
		if ( ! $woo ) {
			return;
		}
		$menu[ $woo ][0] = __( 'Orders', 'woocommerce' );
	}

	/**
	 * Recursive array search
	 *
	 * @param $needle
	 * @param $haystack
	 *
	 * @return bool|int|string
	 */
	public static function recursive_array_search( $needle, $haystack ) {
		foreach ( $haystack as $key => $value ) {
			$current_key = $key;
			if (
				$needle === $value
				|| (
					is_array( $value )
					&& self::recursive_array_search( $needle, $value ) !== false
				)
			) {
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
	 * Menus icons
	 */
	function menu_icons() {
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

		$status_pendingpayment = _x( 'Pending payment', 'Order status', 'woocommerce' );
		$status_failed         = _x( 'Failed', 'Order status', 'woocommerce' );
		$status_processing     = _x( 'Processing', 'Order status', 'woocommerce' );
		$status_completed      = _x( 'Completed', 'Order status', 'woocommerce' );
		$status_onhold         = _x( 'On hold', 'Order status', 'woocommerce' );
		$status_cancelled      = _x( 'Cancelled', 'Order status', 'woocommerce' );
		$status_refunded       = _x( 'Refunded', 'Order status', 'woocommerce' );
		$status_processed       = _x( 'Processed', 'Order status', 'tmsm-woocommerce-customadmin' );

		$css
			= <<<TXT
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
 .wp-list-table .type-shop_order  .column-order_status mark.processed:after,
 .wp-list-table .type-shop_order  .column-order_status mark.failed:after {
   padding-top: 0.4em;
   font-weight: bold;
   font-family: inherit;
   font-size: 11px;
   line-height: 1;
   margin: 0;
   text-indent: 0;
   position: absolute;
   top: 0;
   left: 0;
   width: 100%;
   height: 100%;
   text-align: center;
 }
 .column-order_actions .processing,
 .column-order_actions .cancelled,
 .column-order_actions .complete,
 .column-order_actions .processed
 {
    color:white;
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
 .wp-list-table .type-shop_order  .column-order_status mark.processing, .column-order_actions .processing {
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
 .wp-list-table .type-shop_order  .column-order_status mark.completed, .column-order_actions .complete {
   background-color: #2ea2cc; /* Blue */
 }
 
 .wp-list-table .type-shop_order  .column-order_status mark.completed:after {
    content: "$status_completed";
    color: #ffffff;
 }
 
 /* Completed status */
 .wp-list-table .type-shop_order  .column-order_status mark.processed, .column-order_actions .processed {
   background-color: #7a43b6; /* Blue */
 }
 
 .wp-list-table .type-shop_order  .column-order_status mark.processed:after {
    content: "$status_processed";
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
	 * Put view action first in Order actions
	 *
	 * @param $actions
	 * @param $order
	 *
	 * @return mixed
	 */
	function admin_order_actions($actions, $order){
		$action_view = null;
		if(isset($actions['view'])){
			$action_view = $actions['view'];
			//echo 'enleve view';
			unset($actions['view']);
		}

		array_unshift($actions, $action_view);
		return $actions;
	}
}
