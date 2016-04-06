<?php
/**
 * Template for cancel to display page content when payment status is cancel.
 *
 * @since 1.0.0
 * @package GeoDirectory_Payment_Manager
 */
 
get_header();
###### WRAPPER OPEN ######
/**
 * Outputs the opening HTML wrappers for most template pages.
 *
 * This adds the opening html tags to the primary div, this required the closing tag below :: ($type='',$id='',$class='').
 *
 * @since 1.0.0
 * @param string $type Page type.
 * @param string $id The id of the HTML element.
 * @param string $class The class of the HTML element.
 * @see 'geodir_wrapper_close'
 */
do_action( 'geodir_wrapper_open', 'success-page', 'geodir-wrapper','');

	###### TOP CONTENT ######
	/**
	 * Called before the main content of a template page.
	 *
	 * @since 1.0.0
	 * @see 'geodir_after_main_content'
	 */
	do_action('geodir_before_main_content');
	/**
	 * Called before the main content and adds the sidebar top section and breadcrums
	 *
	 * @since 1.0.0
	 */
	do_action('geodir_success_before_main_content');
	
	###### MAIN CONTENT WRAPPERS OPEN ######
	// this adds the opening html tags to the content div, this required the closing tag below :: ($type='',$id='',$class='')
	/**
	 * Outputs the opening HTML wrappers for the content.
	 *
	 * This adds the opening html tags to the content div, this required the closing tag below :: ($type='',$id='',$class='')
	 *
	 * @since 1.0.0
	 * @param string $type Page type.
	 * @param string $id The id of the HTML element.
	 * @param string $class The class of the HTML element.
	 * @see 'geodir_wrapper_content_close'
	 */
	do_action( 'geodir_wrapper_content_open', 'success-page', 'geodir-wrapper-content','');

$title = PAY_CANCELATION_TITLE;
?>
<div class="geodir_preview_section">
	<h1><?php echo $title;?></h1>   
<?php 
if (isset($_REQUEST['err_msg']) && $_REQUEST['err_msg']) {
	echo "<h3>".$_REQUEST['err_msg']."</h3>";
	echo "<h3>".__('Your post has been saved, please contact support to arrange for it to be published.', 'geodir_payments')."</h3>";
}
$filecontent = stripslashes(get_option('post_payment_cancel_msg_content'));
if (!$filecontent) {
	$filecontent = PAY_CANCEL_MSG;
}
$store_name = get_option('blogname');
$search_array = array('[#site_name#]');
$replace_array = array($store_name);
$filecontent = str_replace($search_array,$replace_array,$filecontent);
echo $filecontent;
?> 
</div> <!-- content #end -->
<?php 		
	###### MAIN CONTENT WRAPPERS CLOSE ######
	/**
	 * Called after the main content of a template page.
	 *
	 * @see 'geodir_before_main_content'
	 * @since 1.0.0
	 */
	do_action('geodir_after_main_content');
	/**
	 * Outputs the closing HTML wrappers for the content.
	 *
	 * This adds the closing html tags to the wrapper_content div :: ($type='')
	 *
	 * @since 1.0.0
	 * @param string $type Page type.
	 * @see 'geodir_wrapper_content_open'
	 */
	do_action( 'geodir_wrapper_content_close', 'details-page');

	###### SIDEBAR ######
	/**
	 * Adds the page sidebar to the payment manager template pages.
	 *
	 * @since 1.0.0
	 */
	do_action('geodir_detail_sidebar');	
		
	###### WRAPPER CLOSE ######	
	/**
	 * Outputs the closing HTML wrappers for most template pages.
	 *
	 * This adds the closing html tags to the wrapper div :: ($type='')
	 *
	 * @since 1.0.0
	 * @param string $type Page type.
	 * @see 'geodir_wrapper_open'
	 */
	do_action( 'geodir_wrapper_close', 'success-page');
get_footer();
?>