<?php
session_start();
include('../../../wp-config.php');

global $wpdb;
$invoice_id = $_GET['kbs_gdinvoice_id'];
$cur_user_id = $_GET['cur_user_id'];
$_SESSION['tax_Cal_data'] = $html_data;
$total = "";
$tax = "";
$tax_price = "";
$initial_price = "";




function pr($d__){echo "<pre>"; print_r($d__);echo "</pre>";}

if (isset($invoice_id)) {

    $invoice_table = $wpdb->base_prefix . 'geodir_invoice';

    $get_invoice_details = $wpdb->get_row("SELECT `discount`,`post_id` FROM `$invoice_table` WHERE id = '" . $invoice_id . "'");

    $get_post_type = $wpdb->get_row("SELECT `post_type` FROM `" . $wpdb->base_prefix . "posts` WHERE id = '" . $get_invoice_details->post_id . "'");

    $get_invoice_data = $wpdb->get_row("SELECT * FROM `" . $wpdb->base_prefix . "geodir_" . $get_post_type->post_type . "_detail` WHERE `post_id` = '" . $get_invoice_details->post_id . "'");

    $cart_data = json_decode($get_invoice_data->geodir_taxdata);

   // pr($get_post_type);
   //$get_invoice_data->geodir_taxdocument


    $total = $cart_data->Final_List_Price;
    $tax = $cart_data->Tax_Applied;
    $tax_price = $cart_data->Tax_Amount_Added_on_List_Price;
    $initial_price = $cart_data->List_Price;


    if ($get_invoice_details->discount > 0) {

        $total = $total - $get_invoice_details->discount;
    }

    $wpdb->update(
            $invoice_table, array(
        'paied_amount' => $total, // string
        'tax_amount' => $tax_price, // integer (number)
        'amount' => $initial_price
            ), array('ID' => $invoice_id)
    );
}

/* * ****************** Update price on the invoice page *********************** */

/* * *************** code edited by kindlebit on 25th-jan-2016 ******************* */


$user_id = $cur_user_id;

if (!$user_id) {
    wp_redirect(geodir_login_url());
    exit();
}

$invoice_info = geodir_get_invoice($invoice_id);
$is_owner = geodir_payment_check_invoice_owner($invoice_info, $user_id);

if (!$is_owner || empty($invoice_info)) {
    wp_redirect(geodir_payment_invoices_page_link());
    exit();
}

geodir_payment_add_invoice_scripts();

$item_name		=	$invoice_info->post_title;
$coupon_code	=	trim($invoice_info->coupon_code);

$payment_method	=	$invoice_info->paymentmethod;

$invoice_type	=	 $invoice_info->invoice_type;
$post_id		=	$invoice_info->post_id;
$amount			=	$invoice_info->amount;
$tax_amount		=	$invoice_info->tax_amount;
$discount		=	$invoice_info->discount;
$paied_amount	=	$invoice_info->paied_amount;
$date			=	 $invoice_info->date;
$date_updated	=	$invoice_info->date_updated;

$amount_display 		= 	geodir_payment_price($amount);
$tax_amount_display		=	geodir_payment_price($tax_amount);
$discount_display		=	geodir_payment_price($discount);
$paied_amount_display	=	geodir_payment_price($paied_amount);

$coupon_allowed			=	get_option('geodir_allow_coupon_code');

$pay_for_invoice		=	geodir_payment_allow_pay_for_invoice($invoice_info);

$invoice_details		=	geodir_payment_invoice_view_details($invoice_info);
$invoice_nonce			=	wp_create_nonce('gd_invoice_nonce_' . $invoice_id);

$date					=	$date_updated != '0000-00-00 00:00:00' ? $date_updated : $date;
$date					=	$date != '0000-00-00 00:00:00' ? $date : '';
$date_display			=	$date != '' ? date_i18n(geodir_default_date_format(), strtotime($date)) : '';

$dat_format				=	geodir_default_date_format() . ' ' . get_option('time_format');
$date_updated_display	=	$date != '' ? date_i18n($dat_format, strtotime($date)) : '';

$payment_method_display = geodir_payment_method_title($payment_method);

$inv_status = $invoice_info->status;
if (in_array(geodir_strtolower($inv_status), array('paid', 'active', 'subscription-payment', 'free'))) {
    $inv_status = 'confirmed';
} else if (in_array(geodir_strtolower($inv_status), array('unpaid'))) {
    $inv_status = 'pending';
}

$status_display = geodir_payment_status_name($inv_status);
$invoice_type_name = geodir_payment_invoice_type_name($invoice_type);

$incomplete = $inv_status == 'pending' && empty($invoice_info->paymentmethod) ? true : false;
if ($incomplete && $inv_status == 'pending') {
    $status_display = __('Incomplete', 'geodir_payments');
}

$listing_display = '';
$package_display = '';
if (( $invoice_type == 'add_listing' || $invoice_type == '' || $invoice_type == 'upgrade_listing' || $invoice_type == 'renew_listing' || $invoice_type == 'claim_listing' ) && $post_id > 0) {
    $post_status = get_post_status($post_id);
    $listing_display = get_the_title($post_id);

    if ($post_status == 'publish' || $post_status == 'private') {
        $listing_display = '<a href="' . get_permalink($post_id) . '" target="_blank">' . $listing_display . '</a>';
    }

    $package_id = $invoice_info->package_id;
    $package_display = $invoice_info->package_title;
}

$transaction_details = trim($invoice_info->HTML) != '' ? trim($invoice_info->HTML) : NULL;

if (!$invoice_info->paied_amount > 0) {
    $payment_method_display = __('Instant Publish', 'geodir_payments');
}
//ob_start();



$get_post_type = $wpdb->get_row("SELECT `post_author`,`post_type` FROM `" . $wpdb->base_prefix . "posts` WHERE `ID` = '" . $post_id . "'");

$post_type = $get_post_type->post_type;

$get_data = $wpdb->get_row("SELECT * FROM `" . $wpdb->base_prefix . "geodir_" . $post_type . "_detail` WHERE `post_id` = '" . $post_id . "'");
//echo "<pre>";print_r($get_data); echo "</pre>";
//
		$message = "";
switch ($get_data->geodir_nationalitycheck) {
    case "International User":
        $message = '<strong>Note :</strong> On the transfer of tax liability eg by specifying "tax liability of the beneficiary" 
(=REVERSE CHARGE MEACHNISM | Para § 13b UStG > The Recipient of the service is liable for VAT payments, because of “not within provider country taxable supply”. Accepted from: ' . $get_data->geodir_billingfirstname . " | " . date("d-m-Y H:i:s");
        break;
    case "Local User":
        $message = "";
        break;
    case "European User":
        $message = '<strong>Note :</strong> On the transfer of tax liability eg by specifying "tax liability of the beneficiary" (=REVERSE CHARGE MEACHNISM | Para § 13b UStG > The Recipient of the service is liable for VAT payments, because of “not within provider country taxable supply”.  ' . $get_data->geodir_billingfirstname . " | " . date("d-m-Y H:i:s");
        break;
}


 
?>	
<style type="text/css">

    table
    {

        font-size: 14px;
        vertical-align: middle;
        width:100%; display:inline-block;

    }



    table.pdf-logo-left td{padding:4px 10px; cellpadding:50%;}
    table.pdf-logo-left th{padding:4px 10px; cellpadding:50%;}	



    table.pdf-mid-sec{ width:100%; float:left;}
    table.pdf-mid-sec td{padding:4px 10px; cellpadding:50%; width:50%; }
    table.pdf-mid-sec th{padding:4px 10px; cellpadding:50%; width:50%;}	

    tr{ float:left; width:100%;}	 

    table.pdf-main-content{padding-top:30px; padding-bottom:10px;

                           border-collapse: collapse;

                           cellspacing:0; 
                           font-size: 12px;
    }

    table.pdf-main-content td{padding:11px; }
    table.pdf-main-content th{padding:11px; background:#f1f1f1; }



    table.pdf-total-sec{float:right; text-align:right; width:100%;} 
    table.pdf-total-sec td{ width:50%; float:right; text-align:right; cellpadding:50%;}

    table.table-1, table.table-2, table.table-3, table.table-4{width:100%;}
    table.table-1 tr th,table.table-2 tr th, table.table-3 tr th, table.table-4 tr th{ border:1px solid #000; width:50%; padding:2px 8px; background-color:#f1f1f1;}
    table.table-1 tr td, table.table-2 tr td, table.table-3 tr td, table.table-4 tr td {width:50%; padding:2px 8px;  border:0.5px solid #000;}
    table.table-4{border:1px solid #000;}
    table.table-4 tr th{border:1px solid #000;}
    h3{margin:0; padding:0; font-size:18px;}
    h4{margin:0; padding:0 ; font-size:16px;}
    span{color:blue;}
    .logo-section img{width:105%; position:absolute; top:0; left:-21px;}
</style>



<?php

function my_time($time) {
    $date = new DateTime($time);
    echo $date->format('d/m/Y');
}
?>


<page backcolor="#FEFEFE" backimgx="center" backimgy="bottom" backimgw="100%" backtop="0" backbottom="30mm" footer="date;heure;page" style="font-size: 12pt">
    <bookmark title="Lettre" level="0" ></bookmark>
<?php
switch ($status_display) {
    case "issued":
    
    case "Confirmed":
        $img = "issued.png";
        break;
    
    case "Incomplete":
        $img = "pending.png";
    break;
    default:
        $img = "pending.png";
    break;    
    
}
?>
    <div class="logo-section"><img src="<?php echo $img; ?>"></div>
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td colspan="0" style="width: 30%;">

                <table  class="pdf-logo-left"  cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
                    <tr><td><h4>Invoiced To</h4></td></tr>
                    <tr>
                        <td colspan="0"><strong><?php echo $get_data->geodir_billingfirstname . " " . $get_data->geodir_billinglastname; ?> </strong></td>
                        <td colspan="0"> &nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="0"><strong><?php echo $get_data->geodir_billingstate . " " . $get_data->geodir_billingzipcode; ?> </strong></td>
                        <td colspan="0">  &nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="0"><strong><?php echo $get_data->geodir_billingnationality; ?></strong></td>
                        <td colspan="0"> &nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="0"><strong> </strong></td>
                        <td colspan="0" > &nbsp;</td>
                    </tr>
                </table>
            </td>
            <td colspan="0" style="width: 70%;">
                <table class="pdf-logo" cellspacing="0" cellpadding="0" border="0" style="width: 100%; text-align: center; font-size: 14px">
                    <tr>
                        <td colspan="0" style="width: 75%;"></td>
                        <td colspan="0" style="width: 25%; color: #444444;">
                            &nbsp;
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <hr/>
    <table class="pdf-main-content table-1" border="0" cellpadding="0" colspan="0" width="100%">
        <tr style="background:#f1f1f1;">
            <td colspan="0"><h3><strong>Customer (<?php echo $get_data->geodir_nationalitycheck; ?>) </strong></h3></td>
            <td colspan="0"   style="" align="right"><h3>Invoice #<?php echo $invoice_id; ?></h3></td>
        </tr>
        <tr>
            <td colspan="0"><strong>Customer number #<?php echo $cur_user_id; ?></strong></td>
            <td colspan="0"  style="" align="right">Order Date: <?php my_time($date); ?></td>
        </tr>
        <tr>
            <td colspan="0"><strong>Customer VAT-ID: <?php echo $get_data->geodir_vatid; ?></strong></td>
            <td colspan="0"  style="" align="right">Invoice Date: <?php my_time($date); ?></td>
        </tr>
        <tr>
            <td colspan="0"><strong>&nbsp;</strong></td>
            <td colspan="0"  style="" align="right">Due Date: <?php my_time($date_updated); ?></td>
        </tr>
        <tr>
            <td colspan="0"><strong>&nbsp;</strong></td>
            <td colspan="0"  style="" align="right">Status: <?php echo $status_display; ?></td>
        </tr>
    </table>

    <table class="pdf-main-content table-2" border="0" cellpadding="0" colspan="0" width="100%">
        <tr  style="border:1px solid #ccc; background:#f1f1f1;">
            <td colspan="2" style="" align="center"><h3><strong>Description </strong></h3></td>
        </tr>
        <tr>
            <td colspan="0" style="width:80%;"><?php echo $item_name; ?></td>
            <td colspan="0" style="width:20%;text-align:right;"><?php echo $amount_display; ?></td>
        </tr>
        <tr>
            <td colspan="0" style="width:80%;">Proportional Code: (wci_10%_off)</td>
            <td colspan="0" style="width:20%;text-align:right;"> 00.00</td>
        </tr>
        <tr>
            <td colspan="0" style="" align="right">Sub Total net</td>
            <td colspan="0"  style="width:20%;text-align:right;"><?php echo $amount_display; ?></td>
        </tr>
        <tr>
            <td colspan="0" style="" align="right"><?php echo $cart_data->Tax_Applied; ?>% VAT</td>
            <td colspan="0"  style="width:20%;text-align:right;"><?php echo $tax_amount_display; ?></td>
        </tr>
<?php if ($coupon_allowed || $discount > 0) { ?>
            <tr>
                <td colspan="0" style="" align="right">Discount</td>
                <td colspan="0"  style="width:20%;text-align:right;"><?php echo $discount_display; ?></td>
            </tr>
<?php } ?>
        <tr>
            <td colspan="0" style="" align="right">Credit</td>
            <td colspan="0"  style="width:20%;text-align:right;"><?php echo number_format($paied_amount_display, 2); ?></td>
        </tr>
        <tr>
            <td colspan="0" style="" align="right"><strong>Total</strong></td>
            <td colspan="0"  style="width:20%;font-weight:bold;text-align:right;"><?php echo $paied_amount_display; ?></td>
        </tr>
    </table>

	<?php if ($status_display != "Incomplete") { ?>
        <?php echo $transaction_details; ?>
    <? } ?>
    <table class="pdf-main-content table-4" border="0" cellpadding="0" colspan="0" width="100%">
        <tr  style="border:1px solid #ccc; background:#f1f1f1;">
            <td colspan="0" style="width:100%"><strong><h3>Note of Reverse Charge </h3> </strong></td>
        </tr>
        <tr>
            <td colspan="0" style="width:100%; border-bottom:none;"><strong>EN - VAT due to the recipient <span><a href="<?php echo ($get_invoice_data->geodir_taxdocument != NULL)?$get_invoice_data->geodir_taxdocument:"#";?>" 	target="_blank">(§ 13b UStG)</a></span></strong></td>
        </tr>
        <tr>
            <td colspan="0" style="width:100%; border-top:none; border-bottom:none;" >"Recipient of the service is liable for VAT according reverse charge mechanism"</td>
        </tr>
        <tr>
            <td colspan="0" style="width:100%;  border-top:none; ">User confirmed information: parwani 03/16/2015 at 21:03 </td>
        </tr>
    </table>
    <p class="text-center" style="margin:0 auto; width:100%; margin-top:50px; font-size:12px;" align="center">
        Reduce costs & increase revenue by using our<span> Sales Promotion </span>or earn recurring income as <span>Affiliate Marketer.</span>
    </p>


</page>
