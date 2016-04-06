<?php
/**
 * Template to display all payment invoices for logged in user. 
 *
 * @since 1.2.6
 * @package GeoDirectory_Payment_Manager
 */
 
// call header
get_header();

###### WRAPPER OPEN ######
/** This action is documented in geodir-payment-templates/cancel.php */
do_action('geodir_wrapper_open', 'invoices-page', 'geodir-wrapper', '');

	###### TOP CONTENT ######
	/** This action is documented in geodir-payment-templates/checkout.php */
	do_action('geodir_top_content', 'invoices-page');

	/**
	 * Called before the main content of a payment invoices template page.
	 *
	 * @since 1.2.6
	 */
	do_action('geodir_invoices_before_main_content');
	/** This action is documented in geodir-payment-templates/cancel.php */
	do_action('geodir_before_main_content', 'invoices-page');

	###### MAIN CONTENT WRAPPERS OPEN ######
	/** This action is documented in geodir-payment-templates/cancel.php */
	do_action('geodir_wrapper_content_open', 'invoices-page', 'geodir-wrapper-content', '');

		###### MAIN CONTENT ######
		/**
		 * Called before the page content of a payment invoices template page.
		 *
		 * @since 1.2.6
		 * @see 'geodir_invoices_after_page_content'
		 */
		do_action('geodir_invoices_before_page_content');

		/**
		 * Add the page content of invoices template page.
		 *
		 * @since 1.2.6
		 */
		do_action('geodir_invoices_page_content');

		/**
		 * Called after the page content of a payment invoices template page.
		 *
		 * @since 1.2.6
		 * @see 'geodir_invoices_before_page_content'
		 */
		do_action('geodir_invoices_after_page_content');
		/** This action is documented in geodir-payment-templates/cancel.php */
		do_action('geodir_after_main_content');

	###### MAIN CONTENT WRAPPERS CLOSE ######
	/** This action is documented in geodir-payment-templates/cancel.php */
	do_action('geodir_wrapper_content_close', 'invoices-page');

	###### SIDEBAR ######
    /**
     * Calls the GeoDirectory author sidebar.
     *
     * @since 1.0.0
     */
    do_action('geodir_author_sidebar_right');

###### WRAPPER CLOSE ######	
/** This action is documented in geodir-payment-templates/cancel.php */
do_action('geodir_wrapper_close', 'invoices-page');

// call footer
get_footer();
?>