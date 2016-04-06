<?php
/**
 * Template to display page content after payment process completed. 
 *
 * @since 1.0.0
 * @package GeoDirectory_Payment_Manager
 */
 
get_header();
###### WRAPPER OPEN ######
/** This action is documented in geodir-payment-templates/cancel.php */
do_action( 'geodir_wrapper_open', 'success-page', 'geodir-wrapper','');
	
	###### TOP CONTENT ######
	/** This action is documented in geodir-payment-templates/cancel.php */
	do_action('geodir_before_main_content');
	/** This action is documented in geodir-payment-templates/cancel.php */
	do_action('geodir_success_before_main_content');
	
	###### MAIN CONTENT WRAPPERS OPEN ######
	/** This action is documented in geodir-payment-templates/cancel.php */
	do_action( 'geodir_wrapper_content_open', 'success-page', 'geodir-wrapper-content','');

$title = PAYMENT_SUCCESS_TITLE;?>
<div class="geodir_preview_section" >
<h1><?php echo $title;?></h1>   

<?php 
$filecontent = stripslashes(get_option('post_payment_success_msg_content'));
if(!$filecontent)
{
	$filecontent = PAYMENT_SUCCESS_MSG;
}
$store_name = get_option('blogname');
$order_id = $_REQUEST['pid'];
/*if(get_post_type($order_id)=='event')
{
	$post_link = home_url().'/?ptype=preview_event&alook=1&pid='.$_REQUEST['pid'];
}else
{
$post_link = home_url().'/?ptype=preview&alook=1&pid='.$_REQUEST['pid'];	
}*/

$post_link =  get_permalink($_REQUEST['pid']);

$search_array = array('[#site_name#]','[#submited_information_link#]');
$replace_array = array($store_name,$post_link);

$filecontent = str_replace($search_array,$replace_array,$filecontent);
echo $filecontent;
?>
</div> <!-- content #end -->
<?php 		
	###### MAIN CONTENT WRAPPERS CLOSE ######
	/** This action is documented in geodir-payment-templates/cancel.php */
	do_action('geodir_after_main_content');
	/** This action is documented in geodir-payment-templates/cancel.php */
	do_action( 'geodir_wrapper_content_close', 'details-page');
	
	###### SIDEBAR ######
	/** This action is documented in geodir-payment-templates/cancel.php */
	do_action('geodir_detail_sidebar');    

	###### WRAPPER CLOSE ######	
/** This action is documented in geodir-payment-templates/cancel.php */
do_action( 'geodir_wrapper_close', 'success-page');
get_footer(); ?>