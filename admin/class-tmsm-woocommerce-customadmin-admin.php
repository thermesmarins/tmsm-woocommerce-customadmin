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
	 * Polylang: Display a country flag or the name of the language as a "post state"
	 *
	 * @param array    $post_states An array of post display states.
	 * @param \WP_Post $post        The current post object.
	 *
	 * @return array A filtered array of post display states.
	 */
	function polylang_display_post_states_language( $post_states, $post ) {
		if( is_plugin_active( 'polylang/polylang.php' ) ){
			foreach(get_the_terms( $post, 'language' ) as $language){
				if(file_exists(POLYLANG_DIR . '/flags/' . $language->slug . '.png')){
					$post_states['polylang'] = '<img src="data:image/png;base64,' . base64_encode( file_get_contents( POLYLANG_DIR . '/flags/' . $language->slug . '.png' ) ).'">';
				}
				else{
					$post_states['polylang'] = $language->name;
				}
			}
		}
		return $post_states;
	}

	/**
	 *  Mailjet: Move admin menu to submenu of Settings
	 */
	function menu_mailjet(){

		add_submenu_page( 'options-general.php',
			__( 'Change your mailjet settings', 'wp-mailjet' ),
			__( 'Mailjet', 'wp-mailjet' ),
			'read',
			'wp_mailjet_options_top_menu',
			'manage_options'
		);

		remove_menu_page('wp_mailjet_options_top_menu');
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
	 * Rename order preview actions
	 *
	 * @param array $actions
	 * @param  WC_Order $order Order object.
	 *
	 * @return mixed
	 */
	function woocommerce_admin_order_preview_actions($actions, $order){

		$status_actions = array();

		$status_actions = @$actions['status']['actions'];

		if (get_option( 'tmsm_woocommerce_vouchers_shippedstatus' ) == 'yes') {


			$status_actions['complete']['name'] =  _x( 'Shipped', 'Order status', 'tmsm-woocommerce-customadmin' );

			if ( $order->has_status( array( 'processing', 'completed' , 'complete' ) ) ) {
				$status_actions['processed'] = array(
					'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=processed&order_id=' . $order->get_id() ), 'woocommerce-mark-order-status' ),
					'name'   => _x( 'Processed', 'Order status', 'tmsm-woocommerce-customadmin' ),
					'action' => 'processed',
				);
			}
		}
		if ( $status_actions ) {
			$actions['status'] = array(
				'group'   => __( 'Change status: ', 'woocommerce' ),
				'actions' => $status_actions,
			);
		}

		return $actions;
	}

	/**
	 * Rename bulk actions
	 *
	 * @param array $actions
	 *
	 * @return array
	 */
	function rename_bulk_actions(array $actions){
		$actions['mark_processing'] = __( 'Mark paid', 'tmsm-woocommerce-customadmin' );

		if (get_option( 'tmsm_woocommerce_vouchers_shippedstatus' ) == 'yes'){

			$actions['mark_completed']  = __( 'Mark shipped', 'tmsm-woocommerce-customadmin' );
			$actions['mark_processed'] = __('Mark as processed', 'tmsm-woocommerce-vouchers');

		}
		return $actions;
	}

	/**
	 * Order actions for processed
	 *
	 * @param array $actions
	 * @param WC_Order $order
	 *
	 * @return mixed
	 */
	function woocommerce_admin_order_actions($actions, $order){
		//print_r($actions);

		if ( get_option( 'tmsm_woocommerce_vouchers_shippedstatus' ) == 'yes' ) {

			$actions['complete']['name'] = _x( 'Ship', 'Change order status', 'tmsm-woocommerce-customadmin' );

			if ( $order->has_status( array( 'processing', 'completed' ) ) ) {

				// Get Order ID (compatibility all WC versions)
				$order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
				// Set the action button
				$actions['processed'] = array(
					'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=processed&order_id='
					                                     . $order_id ),
						'woocommerce-mark-order-status' ),
					'name'   => __( 'Mark as processed', 'tmsm-woocommerce-vouchers' ),
					'action' => "view processed", // keep "view" class for a clean button CSS
				);
			}
		}


		return $actions;
	}

	/**
	 * Bulk action handler for processed
	 */
	function admin_action_mark_processed() {

		// if an array with order IDs is not presented, exit the function
		if( !isset( $_REQUEST['post'] ) && !is_array( $_REQUEST['post'] ) )
			return;

		if (get_option( 'tmsm_woocommerce_vouchers_shippedstatus' ) == 'yes'){
			foreach( $_REQUEST['post'] as $order_id ) {
				$order = new WC_Order( $order_id );
				$order_note = __('Status changed to Processed', 'tmsm-woocommerce-vouchers');
				$order->update_status( 'processed', $order_note, true );
			}

			// of course using add_query_arg() is not required, you can build your URL inline
			$location = add_query_arg( array(
				'post_type' => 'shop_order',
				'marked_processed' => 1, // marked_processed=1 is just the $_GET variable for notices
				'changed' => count( $_REQUEST['post'] ), // number of changed orders
				'ids' => join( $_REQUEST['post'], ',' ),
				'post_status' => 'all'
			), 'edit.php' );

			wp_redirect( admin_url( $location ) );
		}


		exit;

	}

	/**
	 * Action when order goes from processing to processed
	 *
	 * @param $order_id int
	 * @param $order WC_Order
	 */
	function status_processing_to_processed($order_id, $order){
		$order->update_status( 'completed');
		$order->update_status( 'processed');
	}

	/**
	 * Action when order goes from completed to processed
	 *
	 * @param $order_id int
	 * @param $order WC_Order
	 */
	function status_completed_to_processed($order_id, $order){

	}

	/**
	 * Get list of statuses which are consider 'paid'.
	 *
	 * @param $statuses array
	 * @return array
	 */
	function woocommerce_order_is_paid_statuses($statuses){
		$statuses[] = 'processed';
		return $statuses;
	}

	/**
	 * WooCommerce reports with custom statuts processed as paid status
	 *
	 * @param $statuses array
	 *
	 * @return array
	 */
	function woocommerce_reports_order_statuses($statuses){
		if(isset($statuses)){
			if(in_array('completed', $statuses) || in_array('processing', $statuses)){
				array_push( $statuses, 'processed');
			}
		}
		return $statuses;
	}

}
