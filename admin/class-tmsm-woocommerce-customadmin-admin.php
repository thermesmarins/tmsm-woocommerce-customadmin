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
	 * Color badges
	 */
	function color_badges() {

		$css = '';
		if (get_option( 'tmsm_woocommerce_vouchers_shippedstatus' ) == 'yes'){

			$css .= <<<TXT
<style type="text/css">

.order-status.status-processed {
background: #e1c8de;
color: #512e53;
}

.column-wc_actions .complete:after {
content: "\\f310" !important;
color: #2e4453;
}

.column-wc_actions .processed:after {
content: "\\f147" !important;
color: #512e53;

}

</style>
TXT;

		}

		$css .= <<<TXT
<style type="text/css">
.column-wc_actions .complete{
background: #c8d7e1;
color: #2e4453;
}
.column-wc_actions .processed{
background: #e1c8de;
color: #512e53
}

</style>
TXT;

		echo $css;
	}


	/**
	 * Order date format
	 *
	 * @param $date_format
	 *
	 * @return mixed
	 */
	function woocommerce_admin_order_date_format($date_format){

		$date_format = __( 'M j, Y', 'tmsm-woocommerce-customadmin' );
		return $date_format;
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
	 * Custom order statuses
	 *
	 * @return mixed
	 */
	function wc_get_order_statuses() {
		$order_statuses = array(
			'wc-pending'    => _x( 'Pending payment', 'Order status', 'woocommerce' ),
			'wc-processing' => _x( 'Paid', 'Order status', 'tmsm-woocommerce-customadmin' ),
			'wc-on-hold'    => _x( 'On hold', 'Order status', 'woocommerce' ),
			'wc-completed'  => _x( 'Completed', 'Order status', 'woocommerce' ),
			'wc-cancelled'  => _x( 'Cancelled', 'Order status', 'woocommerce' ),
			'wc-refunded'   => _x( 'Refunded', 'Order status', 'woocommerce' ),
			'wc-failed'     => _x( 'Failed', 'Order status', 'woocommerce' )
		);

		if (get_option( 'tmsm_woocommerce_vouchers_shippedstatus' ) == 'yes'){
			$order_statuses['wc-completed'] = _x( 'Shipped', 'Order status', 'tmsm-woocommerce-customadmin' );
			$order_statuses['wc-processed'] = _x( 'Processed', 'Order status', 'tmsm-woocommerce-customadmin' );
		}
		return $order_statuses;
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


	/**
	 * Rename order statuses
	 * @param $statuses array
	 *
	 * @return array
	 */
	function rename_order_statuses($statuses){
		if (get_option( 'tmsm_woocommerce_vouchers_shippedstatus' ) == 'yes'){
			$statuses['wc-processing'] = _x( 'Paid', 'Order status', 'tmsm-woocommerce-customadmin' );
			$statuses['wc-completed'] = _x( 'Shipped', 'Order status', 'tmsm-woocommerce-customadmin' );
		}
		return $statuses;
	}

	/**
	 * Rename order statuses in views filters
	 *
	 * @param $views array
	 *
	 * @return array
	 */
	function rename_views_filters($views){
		foreach($views as &$view){
			$view = str_replace(_x( 'Processing', 'Order status', 'woocommerce' ), _x( 'Paid', 'Order status', 'tmsm-woocommerce-customadmin' ), $view);
			if (get_option( 'tmsm_woocommerce_vouchers_shippedstatus' ) == 'yes') {
				$view = str_replace(_x( 'Completed', 'Order status', 'woocommerce' ), _x( 'Shipped', 'Order status', 'tmsm-woocommerce-customadmin' ), $view);
				$view = str_replace('Processed', _x( 'Processed', 'Order status', 'tmsm-woocommerce-customadmin' ), $view);
			}
		}
		return $views;
	}

	/**
	 * Rename bulk actions
	 *
	 * @param array $actions
	 *
	 * @return array
	 */
	function rename_bulk_actions(array $actions){
		if (get_option( 'tmsm_woocommerce_vouchers_shippedstatus' ) == 'yes'){
			$actions['mark_processing'] = __( 'Mark paid', 'tmsm-woocommerce-customadmin' );
			$actions['mark_completed']  = __( 'Mark shipped', 'tmsm-woocommerce-customadmin' );
		}
		return $actions;
	}

}
