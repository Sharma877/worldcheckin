<?php
/**
 * Template to display payment checkout page content. 
 *
 * @since 1.2.6
 * @package GeoDirectory_Payment_Manager
 */
 
// call header
get_header();

###### WRAPPER OPEN ######
/** This action is documented in geodir-payment-templates/cancel.php */
do_action('geodir_wrapper_open', 'checkout-page', 'geodir-wrapper', '');

	###### TOP CONTENT ######
	/**
	 * Called before the main content and the page specific content.
	 *
	 * @since 1.2.6
	 * @param string $type Page type.
	 */
	do_action('geodir_top_content', 'checkout-page');

	/**
	 * Called before the main content of a payment checkout template page.
	 *
	 * @since 1.2.6
	 */
	do_action('geodir_checkout_before_main_content');
	/** This action is documented in geodir-payment-templates/cancel.php */
	do_action('geodir_before_main_content', 'checkout-page');

	###### MAIN CONTENT WRAPPERS OPEN ######
	/** This action is documented in geodir-payment-templates/cancel.php */
	do_action('geodir_wrapper_content_open', 'checkout-page', 'geodir-wrapper-content', '');

		###### MAIN CONTENT ######
		/**
		 * Called before the page content of a payment checkout template page.
		 *
		 * @since 1.2.6
		 * @see 'geodir_checkout_after_page_content'
		 */
		do_action('geodir_checkout_before_page_content');

		/**
		 * Add the page content of checkout template page.
		 *
		 * @since 1.2.6
		 */
		do_action('geodir_checkout_page_content');

		/**
		 * Called after the page content of a payment checkout template page.
		 *
		 * @since 1.2.6
		 * @see 'geodir_checkout_before_page_content'
		 */
		do_action('geodir_checkout_after_page_content');
		/** This action is documented in geodir-payment-templates/cancel.php */
		do_action('geodir_after_main_content');

	###### MAIN CONTENT WRAPPERS CLOSE ######
	/** This action is documented in geodir-payment-templates/cancel.php */
	do_action('geodir_wrapper_content_close', 'checkout-page');

	###### SIDEBAR ######
	/**
	 * Calls the GeoDirectory author sidebar.
	 *
	 * @since 1.0.0
	 */
    do_action('geodir_author_sidebar_right');

###### WRAPPER CLOSE ######	
/** This action is documented in geodir-payment-templates/cancel.php */
do_action('geodir_wrapper_close', 'checkout-page');

// call footer
get_footer();
?>