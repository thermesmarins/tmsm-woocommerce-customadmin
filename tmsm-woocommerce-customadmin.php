<?php
/*
@link              https://github.com/nicomollet
@since             1.0.0
@package           Tmsm_Woocommerce_Customadmin
@wordpress-plugin

Plugin Name:       TMSM WooCommerce Custom Admin
Plugin URI:        https://github.com/thermesmarins/tmsm-woocommerce-customadmin
Description:       Custom WooCommerce admin area for Thermes Marins de Saint-Malo
Version:           1.0.8
Author:            Nicolas Mollet
Author URI:        https://github.com/nicomollet
Requires PHP:      5.6
License:           GPL-2.0+
License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain:       tmsm-woocommerce-customadmin
Domain Path:       /languages
Github Plugin URI: https://github.com/thermesmarins/tmsm-woocommerce-customadmin
Github Branch:     master
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tmsm-woocommerce-customadmin-install.php
 */
function activate_tmsm_woocommerce_customadmin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tmsm-woocommerce-customadmin-install.php';
	Tmsm_Woocommerce_Customadmin_Install::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tmsm-woocommerce-customadmin-install.php
 */
function deactivate_tmsm_woocommerce_customadmin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tmsm-woocommerce-customadmin-install.php';
	Tmsm_Woocommerce_Customadmin_Install::deactivate();
}

register_activation_hook( __FILE__, 'activate_tmsm_woocommerce_customadmin' );
register_deactivation_hook( __FILE__, 'deactivate_tmsm_woocommerce_customadmin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-tmsm-woocommerce-customadmin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tmsm_woocommerce_customadmin() {

	$plugin = new Tmsm_Woocommerce_Customadmin();
	$plugin->run();

}
run_tmsm_woocommerce_customadmin();
