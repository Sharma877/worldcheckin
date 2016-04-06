<?php
/**
 * Template to display payment invoice details. 
 *
 * @since 1.2.6
 * @package GeoDirectory_Payment_Manager
 */
 
$invoice_id = !empty( $_GET['invoice_id'] ) ? (int)$_GET['invoice_id'] : NULL;

// call header
get_header();

###### WRAPPER OPEN ######
/** This action is documented in geodir-payment-templates/cancel.php */
do_action('geodir_wrapper_open', 'invoice-detail-page', 'geodir-wrapper', '');

	###### TOP CONTENT ######
	/** This action is documented in geodir-payment-templates/checkout.php */
	do_action('geodir_top_content', 'invoice-detail-page');

	/**
	 * Called before the main content of a payment invoice detail template page.
	 *
	 * @since 1.2.6
	 * @param int $invoice_id Current payment invoice id.
	 */
	do_action('geodir_invoice_detail_before_main_content', $invoice_id);
	/** This action is documented in geodir-payment-templates/cancel.php */
	do_action('geodir_before_main_content', 'invoice-detail-page');

	###### MAIN CONTENT WRAPPERS OPEN ######
	/** This action is documented in geodir-payment-templates/cancel.php */
	do_action('geodir_wrapper_content_open', 'invoice-detail-page', 'geodir-wrapper-content', '');

		###### MAIN CONTENT ######
		/**
		 * Called before the page content of a payment invoice detail template page.
		 *
		 * @since 1.2.6
		 * @param int $invoice_id Current payment invoice id.
		 * @see 'geodir_invoice_detail_after_page_content'
		 */
		do_action('geodir_invoice_detail_before_page_content', $invoice_id);

		/**
		 * Add the page content of invoice detail template page.
		 *
		 * @since 1.2.6
		 * @param int $invoice_id Current payment invoice id.
		 */
		do_action('geodir_invoice_detail_page_content', $invoice_id);

		/**
		 * Called after the page content of a payment invoice detail template page.
		 *
		 * @since 1.2.6
		 * @param int $invoice_id Current payment invoice id.
		 * @see 'geodir_invoice_detail_before_page_content'
		 */
		do_action('geodir_invoice_detail_after_page_content', $invoice_id);
		/** This action is documented in geodir-payment-templates/cancel.php */
		do_action('geodir_after_main_content');

	###### MAIN CONTENT WRAPPERS CLOSE ######
	/** This action is documented in geodir-payment-templates/cancel.php */
	do_action('geodir_wrapper_content_close', 'invoice-detail-page');

	###### SIDEBAR ######
    /**
     * Calls the GeoDirectory author sidebar.
     *
     * @since 1.0.0
     */
    do_action('geodir_author_sidebar_right');

###### WRAPPER CLOSE ######	
/** This action is documented in geodir-payment-templates/cancel.php */
do_action('geodir_wrapper_close', 'invoice-detail-page');

// call footer
get_footer();
?>