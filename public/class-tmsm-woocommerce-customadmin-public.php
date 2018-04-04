<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/nicomollet
 * @since      1.1.3
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tmsm-woocommerce-customadmin-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Check if Title field is enabled
	 *
	 * @since    1.1.4
	 *
	 * @return bool
	 */
	private function checkout_title_field_is_enabled(){
		return get_option( 'tmsm_woocommerce_checkout_title', 'no' ) == 'yes';
	}

	/**
	 * Check if Birthday field is enabled
	 *
	 * @since    1.1.4
	 *
	 * @return bool
	 */
	private function checkout_birthday_field_is_enabled(){
		return get_option( 'tmsm_woocommerce_checkout_birthday', 'no' ) == 'yes';
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if(self::checkout_birthday_field_is_enabled()){
			wp_enqueue_script( 'jquery-mask', plugin_dir_url( __FILE__ ) . 'js/jquery.mask.min.js', array( 'jquery' ), null, true );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmsm-woocommerce-customadmin-public.js', array( 'jquery', 'jquery-mask' ), null, true );

			// Localize the script with new data
			$translation_array = array(
				'birthdayformat' => _x( 'mm/dd/yyyy', 'birthday date format', 'tmsm-woocommerce-customadmin' ),
			);
			wp_localize_script( $this->plugin_name, 'tmsm_woocommerce_customeadmin_i18n', $translation_array );
		}

	}

	/**
	 * Default checkout values: user firstname / lastname
	 *
	 * @param $input
	 * @param $key
	 *
	 * @return null|string
	 */
	function checkout_default_values_user( $input, $key ) {
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
	 * Default checkout values: birthday
	 *
	 * @param $input
	 * @param $key
	 *
	 * @var WP_User $current_user
	 *
	 * @return null|string
	 */
	function checkout_default_values_birthday( $input, $key ) {
		global $current_user;

		switch ( $key ) :
			case 'billing_birthday':
				if( method_exists('DateTime', 'createFromFormat') && !empty($current_user->ID)){
					$objdate = DateTime::createFromFormat( _x( 'Y-m-d', 'birthday date format conversion', 'tmsm-woocommerce-customadmin' ),
						get_user_meta($current_user->ID, 'billing_birthday', true) );
					if( $objdate instanceof DateTime ){
						return $objdate->format(_x( 'm/d/Y', 'birthday date format', 'tmsm-woocommerce-customadmin' ));
					}
				}
				return '';
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
	 * Add title field to checkout page
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	function billing_fields_title( $fields ) {

		if(self::checkout_title_field_is_enabled()){
			$new_fields['billing_title']  = array(
				'type'            => 'radio',
				'label'          => _x('Title', 'honorific title label', 'tmsm-woocommerce-customadmin'),
				'required'       => true,
				'class'          => ['billing-title'],
				'label_class'          => ['control-label'],
				'input_class'          => [''],
				'priority' => -100,
				//'custom_attributes'          => ['style' => 'display:inline-block'],
				'options'     => self::billing_title_options()
			);
			$fields = array_merge($new_fields, $fields );
		}

		return $fields;
	}

	/**
	 * Add birthday fields to checkout page
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	function billing_fields_birthday( $fields ) {
		if(self::checkout_birthday_field_is_enabled()){
			$new_fields['billing_birthday'] = array(
				'type'        => 'text',
				'label'       => _x( 'Birthday', 'birthday label', 'tmsm-woocommerce-customadmin' ),
				//'description'          => _x('Day', 'birthday day', 'tmsm-woocommerce-customadmin'),
				'placeholder' => _x( 'mm/dd/yyyy', 'birthday placeholder', 'tmsm-woocommerce-customadmin' ),
				'required'    => false,
				'class'       => [ 'billing-birthday' ],
				'label_class' => [ 'control-label' ],
				'input_class' => [ '' ],
				'priority'    => 200,
				'autocomplete'    => 'bday',
				//'custom_attributes'          => ['style' => 'display:inline-block'],
			);

			$fields = array_merge($new_fields, $fields );
		}

		return $fields;
	}

	/**
	 * Update order meta fields: title
	 *
	 * @param $order_id integer
	 * @param $posted array
	 */
	function checkout_update_order_meta_title( $order_id, $posted ){

		if( isset( $posted['billing_title'] ) ) {
			update_post_meta( $order_id, '_billing_title', sanitize_text_field( $posted['billing_title'] ) );
		}

	}

	/**
	 * Update order meta fields: birthday
	 *
	 * @param $order_id integer
	 * @param $posted array
	 */
	function checkout_update_order_meta_birthday( $order_id, $posted ){

		if( isset( $posted['billing_birthday'] ) ) {
			if( method_exists('DateTime', 'createFromFormat') ){
				$objdate = DateTime::createFromFormat( _x( 'm/d/Y', 'birthday date format conversion', 'tmsm-woocommerce-customadmin' ),
					sanitize_text_field( $posted['billing_birthday'] ) );
				if( $objdate instanceof DateTime ){
					update_post_meta( $order_id, '_billing_birthday', sanitize_text_field( $objdate->format('Y-m-d') ) );
				}
			}
		}

	}

	/**
	 * Mailchimp sync user merge tags: PRENOM, NOM, CIV, DDN
	 *
	 * @param array $merge_vars
	 * @param WP_User $user
	 *
	 * @return array
	 */
	function mailchimp_sync_user_mergetags($merge_vars, $user){

		// Firstname & Lastname
		$merge_vars['PRENOM'] = ( trim( get_user_meta( $user->ID, 'billing_first_name', true )) ? trim( get_user_meta( $user->ID, 'billing_first_name',
			true ) ) : trim( $user->first_name ) );
		$merge_vars['NOM']    = ( trim( get_user_meta( $user->ID, 'billing_last_name', true )) ? trim( get_user_meta( $user->ID, 'billing_last_name',
			true ) ) : trim( $user->last_name ) );

		// Title
		if(self::checkout_title_field_is_enabled()){
			$billing_title_value = get_user_meta($user->ID, 'billing_title', true);
			$billing_title_options = self::billing_title_options();


			if($billing_title_value && isset($billing_title_options[$billing_title_value])){
				$merge_vars['CIV'] = @$billing_title_options[$billing_title_value];
			}
		}

		// Birthday
		if(self::checkout_birthday_field_is_enabled()){
			$birthdayvalue = trim( get_user_meta( $user->ID, 'billing_birthday', true ));
			if ( ! empty( $birthdayvalue ) ) {
				$objdate = DateTime::createFromFormat( _x( 'Y-m-d', 'birthday date format conversion', 'tmsm-woocommerce-customadmin' ),
					sanitize_text_field( $birthdayvalue ) );
				if ( $objdate instanceof DateTime ) {
					$merge_vars['DDN'] = $objdate->format( 'm/d' ); // Fixed format by Mailchimp
				}
			}
		}

		return $merge_vars;
	}

	/**
	 * Update birthday value in user meta to format YYYY-MM-DD
	 *
	 * @param WC_Customer $customer
	 * @param $updated_props
	 */
	function woocommerce_customer_object_updated_props_birthday($customer, $updated_props){

		if(self::checkout_birthday_field_is_enabled()){
			if( method_exists('DateTime', 'createFromFormat') && !empty($customer->get_meta('billing_birthday', true))){
				$objdate = DateTime::createFromFormat( _x( 'm/d/Y', 'birthday date format conversion', 'tmsm-woocommerce-customadmin' ),
					sanitize_text_field( $customer->get_meta('billing_birthday', true) ) );
				if( $objdate instanceof DateTime ){
					$customer->update_meta_data('billing_birthday', sanitize_text_field( $objdate->format('Y-m-d') ));
				}
			}
		}

	}
}
