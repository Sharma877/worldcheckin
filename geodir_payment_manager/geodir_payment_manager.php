<?php
/**
 * This is the main Payment Manager plugin file, here we declare and call the important stuff.
 *
 * @since 1.0.0
 * @package GeoDirectory_Payment_Manager
 *
 * @global object $wpdb WordPress Database object.
 * @global string $plugin_prefix Geodirectory plugin table prefix.
 * @global array $geodir_addon_list List of active GeoDirectory extensions.
 */

/*
Plugin Name: GeoDirectory Payment Manager
Plugin URI: http://wpgeodirectory.com
Description: GeoDirectory Payment Manager plugin.
Version: 1.3.5
Author: GeoDirectory
Author URI: http://wpgeodirectory.com
*/

define("GEODIRPAYMENT_VERSION", "1.3.5");
global $wpdb, $plugin_prefix, $geodir_addon_list;

if (is_admin()) {
	/**
     * Include any functions needed for updates.
     *
     * @since 1.0.0
     */
	require_once('gd_update.php');
}

///GEODIRECTORY CORE ALIVE CHECK START
if (is_admin()) {
	/**
	 * Include WordPress plugin core file to use core functions to check for active plugins.
	 *
	 * @since 1.0.0
	 */
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	if (!is_plugin_active('geodirectory/geodirectory.php')) {
		return;
	}
}
/// GEODIRECTORY CORE ALIVE CHECK END

$geodir_addon_list['geodir_payment_manager'] = 'yes' ;

if (!isset($plugin_prefix)) {
	$plugin_prefix = $wpdb->prefix.'geodir_';
}
	
$geodir_get_package_info_cache = array();// This will store the cached package info per package for each page load so not to run for each listing

if (!defined('GEODIR_PAYMENT_MANAGER_PATH')) define('GEODIR_PAYMENT_MANAGER_PATH', plugin_dir_path( __FILE__ ) );

/* ---- Table Names ---- */
if (!defined('GEODIR_PRICE_TABLE')) define('GEODIR_PRICE_TABLE', $plugin_prefix . 'price' );	
if (!defined('INVOICE_TABLE')) define('INVOICE_TABLE', $plugin_prefix . 'invoice' );	
if (!defined('COUPON_TABLE')) define('COUPON_TABLE', $plugin_prefix . 'coupons' );

if (!defined('GEODIRPAYMENT_TEXTDOMAIN')) define('GEODIRPAYMENT_TEXTDOMAIN','geodir_payments');

add_action('plugins_loaded','geodir_load_translation_geodirpayment');
function geodir_load_translation_geodirpayment()
{
    /**
     * Filter the plugin locale.
     *
     * @since 1.0.0
     */
	$locale = apply_filters('plugin_locale', get_locale(), 'geodir_payments');
    load_textdomain('geodir_payments', WP_LANG_DIR . '/' . 'geodir_payments' . '/' . 'geodir_payments' . '-' . $locale . '.mo');
    load_plugin_textdomain('geodir_payments', false, dirname(plugin_basename(__FILE__)) . '/geodir-payment-languages');
	/**
	 * Define language constants, here as they are not loaded yet.
	 *
	 * @since 1.0.0
	 */
	include_once('language.php');
}

/**
 * Include the main functions related to payment manager plugin.
 *
 * @since 1.0.0
 */
include_once( 'geodir_payment_functions.php' );
/**
 * Include the template related functions for payment manager plugin.
 *
 * @since 1.0.0
 */
include_once( 'geodir_payment_template_functions.php' );
/**
 * Include the hook actions used for payment manager plugin.
 *
 * @since 1.0.0
 */
include_once( 'geodir_payment_actions.php' );
/**
 * Include the payment gateways related functions.
 *
 * @since 1.2.6
 */
include_once( 'geodir_payment_providers.php' );  
 
if ( is_admin() ) {
	register_activation_hook( __FILE__, 'geodir_payment_activation' ); 
	register_deactivation_hook( __FILE__, 'geodir_payment_deactivation' );
	register_uninstall_hook(__FILE__,'geodir_payment_uninstall');
}

if ( is_admin() )	{
	/**
	 * Include any functions needed for upgrades.
	 *
	 * @since 1.0.0
	 */
	require_once('gd_upgrade.php');	
}

add_action('activated_plugin','geodir_payment_plugin_activated') ;
function geodir_payment_plugin_activated($plugin) {
	if (!get_option('geodir_installed')) {
		$file = plugin_basename(__FILE__);
		
		if ($file == $plugin) {
			$all_active_plugins = get_option( 'active_plugins', array() );
			
			if (!empty($all_active_plugins) && is_array($all_active_plugins)) {
				foreach ($all_active_plugins as $key => $plugin) {
					if ($plugin ==$file) {
						unset($all_active_plugins[$key]);
					}
				}
			}
			update_option('active_plugins',$all_active_plugins);
		}
		
		wp_die(__('<span style="color:#FF0000">There was an issue determining where GeoDirectory Plugin is installed and activated. Please install or activate GeoDirectory Plugin.</span>', 'geodir_payments'));
	}
}