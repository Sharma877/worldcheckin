<?php
/**
 * Contains functions related to Payment Manager plugin upgrade.
 *
 * @since 1.0.0
 * @package GeoDirectory_Payment_Manager
 *
 * @global object $wpdb WordPress Database object.
 */
global $wpdb;

if (get_option('geodir_payments'.'_db_version') != GEODIRPAYMENT_VERSION) {
	add_action( 'plugins_loaded', 'geodir_payments_upgrade_all' );
	update_option( 'geodir_payments'.'_db_version',  GEODIRPAYMENT_VERSION );
}

/**
 * Handles upgrade for all payment manager versions.
 *
 * @since 1.0.0
 * @package GeoDirectory_Payment_Manager
 */
function geodir_payments_upgrade_all() {
	geodir_payment_activation_script();
	geodir_payments_upgrade_1_0_9();
    add_action('wp_loaded','geodir_create_payment_pages');
}

/**
 * Handles upgrade for payment manager versions <= 1.0.9.
 *
 * @since 1.0.9
 * @package GeoDirectory_Payment_Manager
 *
 * @global object $wpdb WordPress Database object.
 */
function geodir_payments_upgrade_1_0_9() {
	global $wpdb, $plugin_prefix;
}
