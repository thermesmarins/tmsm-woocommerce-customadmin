<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/nicomollet
 * @since      1.0.0
 *
 * @package    Tmsm_Woocommerce_Customadmin
 * @subpackage Tmsm_Woocommerce_Customadmin/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tmsm_Woocommerce_Customadmin
 * @subpackage Tmsm_Woocommerce_Customadmin/public
 * @author     Nicolas Mollet <nico.mollet@gmail.com>
 */
class Tmsm_Woocommerce_Customadmin_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The options name to be used in this plugin
	 *
	 * @since  	1.0.0
	 * @access 	private
	 * @var  	string 		$option_name 	Option name of this plugin
	 */
	private $option_name = 'tmsm_woocommerce_customadmin';

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tmsm-woocommerce-customadmin-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmsm-golf-features-public.js', array( 'jquery' ), $this->version, false );

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
	 * Title field options
	 *
	 * @return mixed
	 */
	private static function billing_title_options(){

		$options = array(
			'2' => _x('Ms', 'honorific title', 'tmsm-woocommerce-customadmin' ),
			'1' => _x('Mr', 'honorific title', 'tmsm-woocommerce-customadmin' ),
		);

		return $options;
	}

	/**
	 * Add title & birthdate to checkout page
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	function billing_fields_title_birthday( $fields ) {

		$new_fields['billing_title']  = array(
			'type'            => 'radio',
			'label'          => _x('Title', 'honorific title', 'tmsm-woocommerce-customadmin'),
			'required'       => true,
			'class'          => ['billing-title'],
			'label_class'          => ['control-label'],
			'input_class'          => [''],
			//'custom_attributes'          => ['style' => 'display:inline-block'],
			'options'     => self::billing_title_options()
		);
		//array_unshift($fields, $field_title);
		$fields = array_merge($new_fields, $fields );
		return $fields;
	}


	/**
	 * Update order meta fields: title & birthdate
	 *
	 * @param $order_id integer
	 * @param $posted array
	 */
	function checkout_update_order_meta_title_birthday( $order_id, $posted ){
		if( isset( $posted['billing_title'] ) ) {
			update_post_meta( $order_id, '_billing_title', sanitize_text_field( $posted['billing_title'] ) );
		}
	}


	/**
	 * Mailchimp sync user merge vars: PRENOM, NOM, CIV
	 *
	 * @param WP_User $user
	 * @param array $merge_vars
	 *
	 * @return array
	 */
	function mailchimp_sync_user_mergevars($user, $merge_vars){

		//mailchimp_log('member.sync', "mailchimp_sync_user_mergevars");
		//mailchimp_log('member.sync', "get_user_meta all", get_user_meta($user->ID));

		$merge_vars['PRENOM'] = ( trim( get_user_meta( $user->ID, 'billing_first_name', true )) ? trim( get_user_meta( $user->ID, 'billing_first_name',
			true ) ) : trim( $user->first_name ) );
		$merge_vars['NOM']    = ( trim( get_user_meta( $user->ID, 'billing_last_name', true )) ? trim( get_user_meta( $user->ID, 'billing_last_name',
			true ) ) : trim( $user->last_name ) );

		$billing_title_value = get_user_meta($user->ID, 'billing_title', true);
		$billing_title_options = self::billing_title_options();

		if($billing_title_value && isset($billing_title_options[$billing_title_value])){
			//mailchimp_log('member.sync', "get_user_meta CIV", $billing_title_options[$billing_title_value] );
			$merge_vars['CIV'] = $billing_title_options[$billing_title_value];
		}
		return $merge_vars;
	}
}
