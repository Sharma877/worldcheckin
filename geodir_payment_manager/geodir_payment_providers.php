<?php
// GLOBAL
function geodir_payment_form_handler_global( $invoice_id ) {
	// Clear cart
	geodir_payment_clear_cart();
	
	$invoice_info = geodir_get_invoice( $invoice_id );
	
	if ( !empty( $invoice_info ) ) {
		$paymentmethod = $invoice_info->paymentmethod;
		
		if ( $invoice_info->invoice_type == 'add_listing' || $invoice_info->invoice_type == 'upgrade_listing' || $invoice_info->invoice_type == 'renew_listing' ) {
			$post_id = $invoice_info->post_id;
			
			if ( $post_id && $paymentmethod != '' ) {
				geodir_save_post_meta( $post_id, 'paymentmethod', $paymentmethod );
			}
		}
	}
}
add_action( 'geodir_payment_form_handler_global' , 'geodir_payment_form_handler_global' );

// PAYPAL
function geodir_payment_form_paypal( $invoice_id ) {
	$invoice_info = geodir_get_invoice( $invoice_id );
	$paymentmethod = get_payment_options( $invoice_info->paymentmethod );
	
	$currency_code = geodir_get_currency_type();
	
	$user_id = $invoice_info->user_id;
	$post_id = $invoice_info->post_id;
	$item_name = $invoice_info->post_title;
	$merchantid = $paymentmethod['merchantid'];
	$paymode = $paymentmethod['payment_mode'];


    $return_url = geodir_info_url(array('pay_action'=>'return','pmethod'=>'paypal','pid'=>$post_id,'inv'=>$invoice_id));
    $cancel_return = geodir_info_url(array('pay_action'=>'cancel','pmethod'=>'paypal','pid'=>$post_id,'inv'=>$invoice_id));
    $notify_url = geodir_info_url(array('pay_action'=>'ipn','pmethod'=>'paypal'));


	
	$item_name = apply_filters( 'geodir_paypal_item_name', home_url( '/' ) . ' - ' . $item_name, $invoice_id );
	
	if ( $paymode =='sandbox' ) {
		$action = 'https://www.sandbox.paypal.com/us/cgi-bin/webscr';
	} else {
		$action = 'https://www.paypal.com/cgi-bin/webscr';
	}
	?>
	<form name="frm_payment_method" action="<?php echo $action;?>" method="post">
		<input type="hidden" name="business" value="<?php echo $merchantid;?>" />
		<input type="hidden" name="item_name" value="<?php echo esc_attr( $item_name );?>" />
		<input type="hidden" name="amount" value="<?php echo $invoice_info->paied_amount;?>" />
		<input type="hidden" name="currency_code" value="<?php echo $currency_code;?>" />
		<input type="hidden" name="no_note" value="1" />
		<input type="hidden" name="no_shipping" value="1" />
		<input type="hidden" name="custom" value="<?php echo $invoice_id;?>" />
		<input type="hidden" name="notify_url" value="<?php echo $notify_url;?>" />
		<input type="hidden" name="return" value="<?php echo $return_url;?>" />
		<input type="hidden" name="cancel_return" value="<?php echo $cancel_return;?>" />
		<?php do_action( 'geodir_payment_form_fields_paypal', $invoice_id ); ?>
	</form>
	<div class="wrapper">
		<div class="clearfix container_message">
			<center><h1 class="head2"><?php echo PAYPAL_MSG; ?></h1></center>
		</div>
	</div>
	<script type="text/javascript">setTimeout("document.frm_payment_method.submit()",50);</script>
	<?php
	exit;
}
add_action( 'geodir_payment_form_handler_paypal' , 'geodir_payment_form_paypal' );



// AUTHORIZENET
function geodir_payment_form_authorizenet( $invoice_id ) {
	global $current_user;
	
	$invoice_info = geodir_get_invoice( $invoice_id );
	$paymentmethod = get_payment_options( $invoice_info->paymentmethod );
	
	$currency_code = geodir_get_currency_type();
	
	$user_id = $invoice_info->user_id;
	$post_id = $invoice_info->post_id;
	$item_name = $invoice_info->post_title;
	$item_name = apply_filters( 'geodir_authorizenet_item_name', $item_name, $invoice_id );
	
	$payable_amount = $invoice_info->paied_amount;

	$sandbox = $paymentmethod['payment_mode'] == 'sandbox' ? true : false;
	$loginid = $paymentmethod['loginid'];
	$transkey = $paymentmethod['transkey'];
	
	$display_name = geodir_get_client_name($user_id);
	$user_email = $current_user->data->user_email;
	$user_phone = isset($current_user->data->user_phone) ? $current_user->data->user_phone : '';
	
	$cc_number = isset($_REQUEST['cc_number']) ? $_REQUEST['cc_number'] : '';
	$cc_month = isset($_REQUEST['cc_month']) ? $_REQUEST['cc_month'] : '';
	$cc_year = isset($_REQUEST['cc_year']) ? $_REQUEST['cc_year'] : '';
	$cv2 = isset($_REQUEST['cv2']) ? $_REQUEST['cv2'] : '';
	
	$x_card_num = $cc_number;
	$x_exp_date = $cc_month . substr( $cc_year, 2, strlen( $cc_year ) );
	$x_card_code = $cv2;
		
	require_once('authorizenet/authorizenet.class.php');
	
	$a = new authorizenet_class;
	if ($sandbox) {
		$a->is_sandbox(); // put api in sandbox mode
	}
	
	/*You login using your login, login and tran_key, or login and password.  It
	varies depending on how your account is setup.
	I believe the currently reccomended method is to use a tran_key and not
	your account password.  See the AIM documentation for additional information.*/	
	$a->add_field('x_login', $loginid);
	$a->add_field('x_tran_key', $transkey);
	//$a->add_field('x_password', 'CHANGE THIS TO YOUR PASSWORD');
	
	$a->add_field('x_version', '3.1');
	$a->add_field('x_type', 'AUTH_CAPTURE');
	//$a->add_field('x_test_request', 'TRUE');     Just a test transaction
	$a->add_field('x_relay_response', 'FALSE');
	
	/*
	You *MUST* specify '|' as the delim char due to the way I wrote the class.
	I will change this in future versions should I have time.  But for now, just
	 make sure you include the following 3 lines of code when using this class.
	*/	
	$a->add_field('x_delim_data', 'TRUE');
	$a->add_field('x_delim_char', '|');     
	$a->add_field('x_encap_char', '');
	
	/*
	Setup fields for customer information.  This would typically come from an
	array of POST values froma secure HTTPS form.
	*/	
	$a->add_field('x_first_name', $display_name);
	$a->add_field('x_last_name', '');
	/*
	$a->add_field('x_address', $address);
	$a->add_field('x_city', $userInfo['user_city']);
	$a->add_field('x_state', $userInfo['user_state']);
	$a->add_field('x_zip', $userInfo['user_postalcode']);
	$a->add_field('x_country', 'US');
	$a->add_field('x_country',  $userInfo['user_country']);
	*/
	$a->add_field('x_email', $user_email);
	$a->add_field('x_phone', $user_phone);
	
	/* Using credit card number '4007000000027' performs a successful test.  This
	 allows you to test the behavior of your script should the transaction be
	 successful.  If you want to test various failures, use '4222222222222' as
	 the credit card number and set the x_amount field to the value of the
	 Response Reason Code you want to test. 
	
	 For example, if you are checking for an invalid expiration date on the
	 card, you would have a condition such as:
	 if ($a->response['Response Reason Code'] == 7) ... (do something)
	
	 Now, in order to cause the gateway to induce that error, you would have to
	 set x_card_num = '4222222222222' and x_amount = '7.00'
	
	  Setup fields for payment information*/
	//$a->add_field('x_method', $_REQUEST['cc_type']);
	$a->add_field('x_method', 'CC');
	$a->add_field('x_card_num', $x_card_num);
	/*
	$a->add_field('x_card_num', '4007000000027');   // test successful visa
	$a->add_field('x_card_num', '370000000000002');   // test successful american express
	$a->add_field('x_card_num', '6011000000000012');  // test successful discover
	$a->add_field('x_card_num', '5424000000000015');  // test successful mastercard
	$a->add_field('x_card_num', '4222222222222');    // test failure card number
	*/
	$a->add_field('x_amount', $payable_amount);
	$a->add_field('x_exp_date', $x_exp_date);    /* march of 2008*/
	$a->add_field('x_card_code', $x_card_code);    // Card CAVV Security code
	
	/* Process the payment and output the results */
	$success = false;
	$message = '';
	$response_code = $a->process();

	switch ($response_code) {
		case 1:  /* Successs */
			$success = true;

			$transaction_details = '';
			$transaction_details .= "--------------------------------------------------<br />";
			$transaction_details .= sprintf(__("Payment Details for Invoice ID #%s", 'geodir_payments'), $invoice_id ) ."<br />";
			$transaction_details .= "--------------------------------------------------<br />";
			$transaction_details .= sprintf(__("Item Name: %s", 'geodir_payments'), $item_name)."<br />";
			$transaction_details .= "--------------------------------------------------<br />";
			$transaction_details .= sprintf(__("Trans ID: %s", 'geodir_payments'), $a->response['Transaction ID'])."<br />";
			$transaction_details .= sprintf(__("Status: %s", 'geodir_payments'), $a->response['Response Code'])."<br />";
			$transaction_details .= sprintf(__("Amount: %s", 'geodir_payments'),$a->response['Amount'])."<br />";
			$transaction_details .= sprintf(__("Type: %s", 'geodir_payments'),$a->response['Transaction Type'])."<br />";
			$transaction_details .= sprintf(__("Date: %s", 'geodir_payments'), date_i18n("F j, Y, g:i a", current_time( 'timestamp' )))."<br />";
			$transaction_details .= sprintf(__("Method: %s", 'geodir_payments'), 'Authorize.net')."<br />";
			$transaction_details .= "--------------------------------------------------<br />";	
			
			/*############ SET THE INVOICE STATUS START ############*/
			// update invoice status and transaction details
			geodir_update_invoice_status( $invoice_id, 'confirmed' );
			geodir_update_invoice_transaction_details( $invoice_id, $transaction_details );
			/*############ SET THE INVOICE STATUS END ############*/
			
			// send notification to admin
			geodir_payment_adminEmail( $post_id, $user_id, 'payment_success', $transaction_details );
			
			// send notification to client
			geodir_payment_clientEmail( $post_id, $user_id, 'payment_success', $transaction_details );


            $redirect_url =geodir_info_url(  array( 'pay_action' => 'success', 'inv' => $invoice_id, 'pid' => $post_id ) );
			wp_redirect( $redirect_url );
			exit;
		break;
		case 2:  /* Declined */
			$message = $a->get_response_reason_text();
			
			// update invoice status
			geodir_update_invoice_status( $invoice_id, 'canceled' );
		break;
		case 3:  /* Error */
			$message = $a->get_response_reason_text();
			
			// update invoice status
			geodir_update_invoice_status( $invoice_id, 'failed' );
		break;
	}

	if ( !$success ) {
		$_SESSION['display_message'] = $message;

        $redirect_url = geodir_info_url(  array( 'pay_action' => 'cancel', 'inv' => $invoice_id, 'pmethod' => 'authorizenet', 'err_msg' => urlencode( $message ) ));

        wp_redirect( $redirect_url );
	}
	exit;
}
add_action( 'geodir_payment_form_handler_authorizenet' , 'geodir_payment_form_authorizenet' );

// WORLDPAY
function geodir_payment_form_worldpay( $invoice_id ) {
	$invoice_info = geodir_get_invoice( $invoice_id ); 
	$paymentmethod = get_payment_options( $invoice_info->paymentmethod );
	$sandbox = $paymentmethod['payment_mode'] == 'sandbox' ? true : false;
    $ipn_url = geodir_info_url(  array( 'pay_action' => 'ipn', 'pmethod' => 'worldpay'));


    $user_id = $invoice_info->user_id;
	$post_id = $invoice_info->post_id;
	$item_name = $invoice_info->post_title;
	$item_name = apply_filters( 'geodir_worldpay_item_name', $item_name, $invoice_id );
	$payable_amount = $invoice_info->paied_amount;
	
	$client_name = geodir_get_client_name( $user_id );
	$client_email = geodir_payment_get_client_email( $user_id );
	
	$currency_code = geodir_get_currency_type();
							
	$instId = $paymentmethod['instId'];
	$accId1 = $paymentmethod['accId1'];
	$cartId = $invoice_id;
	$desc = $item_name;
	$currency = $currency_code;
	$amount = $payable_amount;
	
	$action_url = $sandbox ? 'https://secure-test.worldpay.com/wcc/purchase' : 'https://secure.worldpay.com/wcc/purchase';
	$testMode = $sandbox ? 100 : 0;
	?>
	<form action="<?php echo $action_url;?>" name="frm_payment_method" method="POST">
	  <input type="hidden" name="instId"  value="<?php echo $instId;?>">
	  <input type="hidden" name="cartId" value="<?php echo $cartId;?>" />
	  <input type="hidden" name="currency" value="<?php echo $currency;?>" />
	  <input type="hidden" name="amount"  value="<?php echo $amount;?>" />
	  <input type="hidden" name="desc" value="<?php echo esc_attr( $desc );?>" />
	  <input type="hidden" name="name" value="<?php echo esc_attr( $client_name );?>" />
	  <input type="hidden" name="email" value="<?php echo esc_attr( $client_email );?>" />
	  <input type="hidden" name="MC_callback" value="<?php echo $ipn_url;?>"> 
	  <input type="hidden" name="testMode" value="<?php echo $testMode;?>" />
	</form>
	<div class="wrapper">
		<div class="clearfix container_message">
			<h1 class="head2"><?php echo WORLD_PAY_MSG; ?></h1>
		</div>
	</div>
	<script type="text/javascript">setTimeout("document.frm_payment_method.submit()",50);</script>
	<?php
	exit;
}
add_action( 'geodir_payment_form_handler_worldpay' , 'geodir_payment_form_worldpay' );

// 2CO
function geodir_payment_form_2co( $invoice_id ) {
	$invoice_info = geodir_get_invoice( $invoice_id ); 
	$payment_method = get_payment_options( $invoice_info->paymentmethod );
	$sandbox = $payment_method['payment_mode'] == 'sandbox' ? true : false;
	
	$user_id = $invoice_info->user_id;
	$post_id = $invoice_info->post_id;
	$item_name = $invoice_info->post_title;
	$item_name = apply_filters( 'geodir_worldpay_item_name', $item_name, $invoice_id );
	$payable_amount = $invoice_info->paied_amount;
	
	$currency_code = geodir_get_currency_type();
	
	$user_info = get_userdata( $user_id ); 
							
	$payable_amount = $invoice_info->paied_amount;
	$last_postid = $invoice_info->post_id;
	$post_title = $invoice_info->post_title;
	
	$client_name = geodir_get_client_name( $user_id );
	$client_email = $user_info->user_email;
	
	$merchantid = $payment_method['vendorid'];
	if ( $merchantid == '' ) {
		$merchantid = '1303908';
	}
	$ipnfilepath = $payment_method['ipnfilepath'];
	
	$submit_url = $sandbox ? 'https://sandbox.2checkout.com/checkout/purchase' : 'https://www.2checkout.com/checkout/purchase';
	$sid = $merchantid;
	$name = $item_name;
	$price = $payable_amount;
	$x_receipt_link_url = $payment_method['ipnfilepath'];
?>
<form action="<?php echo $submit_url;?>" method="post" name="frm_payment_method">
  <input type="hidden" name="sid" value="<?php echo $sid;?>" />
  <input type="hidden" name="mode" value="2CO" />
  <input type="hidden" name="li_0_type" value="product" />
  <input type="hidden" name="li_0_product_id" value="<?php echo $invoice_id;?>" />
  <input type="hidden" name="li_0_name" value="<?php echo esc_attr( $name );?>" />
  <input type="hidden" name="li_0_price" value="<?php echo $price;?>" />
  <input type="hidden" name="li_0_tangible" value="N" />
  <input type="hidden" name="currency_code" value="<?php echo $currency_code;?>" />
  <input type="hidden" name="merchant_order_id" value="<?php echo $invoice_id;?>" />
  <input type="hidden" name="card_holder_name" value="<?php echo esc_attr( $client_name );?>" />
  <input type="hidden" name="email" value="<?php echo esc_attr( $client_email );?>" />
  <input type="hidden" name="x_receipt_link_url" value="<?php echo $x_receipt_link_url;?>" />
</form>
<div class="wrapper">
	<div class="clearfix container_message">
		<h1 class="head2"><?php echo TWOCO_MSG; ?></h1>
	</div>
</div>
<script type="text/javascript">setTimeout("document.frm_payment_method.submit()",50);</script>
	<?php
	exit;
}
add_action( 'geodir_payment_form_handler_2co' , 'geodir_payment_form_2co' );

// PRE BANK TRANSFER
function geodir_payment_form_prebanktransfer( $invoice_id ) {
	$invoice_info = geodir_get_invoice( $invoice_id );
	
	$user_id = $invoice_info->user_id;
	$post_id = $invoice_info->post_id;
	$item_name = $invoice_info->post_title;
	$item_name = apply_filters( 'geodir_prebanktransfer_item_name', $item_name, $invoice_id );
	$payable_amount = geodir_payment_price( $invoice_info->paied_amount );
		
	$transaction_details = '';
	$transaction_details .= '--------------------------------------------------<br />';
	$transaction_details .= sprintf( __( 'Payment Details for Invoice ID #%s', 'geodir_payments' ), $invoice_id ) . '<br />';
	$transaction_details .= '--------------------------------------------------<br />';
	$transaction_details .= sprintf( __( 'Item Name: %s', 'geodir_payments' ), $item_name ) . '<br />';
	$transaction_details .= '--------------------------------------------------<br />';
	$transaction_details .= sprintf( __( 'Status: %s', 'geodir_payments' ), __( 'Pending', 'geodir_payments' ) ) . '<br />';
	$transaction_details .= sprintf( __( 'Amount: %s', 'geodir_payments' ), $payable_amount ) . '<br />';
	$transaction_details .= sprintf( __( 'Type: %s', 'geodir_payments' ), __( 'Pre Bank Transfer', 'geodir_payments' ) ) . '<br />';
	$transaction_details .= sprintf( __( 'Date: %s', 'geodir_payments' ), date_i18n( 'F j, Y, g:i a', current_time( 'timestamp' ) ) ) . '<br />';
	$transaction_details .= sprintf( __( 'Method: %s', 'geodir_payments' ), __( 'Pre Bank Transfer', 'geodir_payments' ) ) . '<br />';
	$transaction_details .= '--------------------------------------------------<br />';	
	
	/*############ SET THE INVOICE STATUS START ############*/
	// update invoice status and transaction details
	geodir_update_invoice_status( $invoice_id, 'pending' );
	geodir_update_invoice_transaction_details( $invoice_id, $transaction_details );
	/*############ SET THE INVOICE STATUS END ############*/
	
	// send notification to admin
	geodir_payment_adminEmail( $post_id, $user_id, 'payment_success', $transaction_details );
	
	// send notification to client
	geodir_payment_clientEmail( $post_id, $user_id, 'payment_success', $transaction_details );



    $redirect_url = geodir_info_url(  array( 'pay_action' => 'success', 'inv' => $invoice_id, 'pid' => $post_id ) );

    wp_redirect($redirect_url);
	exit;	 
}
add_action( 'geodir_payment_form_handler_prebanktransfer', 'geodir_payment_form_prebanktransfer' );

// PAYMENT ON DELIVERY
/**
 * Perform payment on delivery request for current invoice.
 *
 * @since 1.0.0
 * @package GeoDirectory_Payment_Manager
 *
 * @param int $invoice_id Payment invoice id.
 */
function geodir_payment_form_payondelevary($invoice_id) {
}
add_action( 'geodir_payment_form_handler_payondelevary' , 'geodir_payment_form_payondelevary' );

// PAYMENT IPN HANDLERS
// PAYPAL IPN
function geodir_ipn_handler_paypal() {
	$paymentOpts = get_payment_options('paypal');
	$paymode = $paymentOpts['payment_mode'];
	$sandbox = $paymode == 'sandbox' ? true : false;
	
	$currency_code 	= geodir_get_currency_type(); // Actual curency code
	$merchantid 	= $paymentOpts['merchantid']; // Actual paypal business email
	
	/* read the post from PayPal system and add 'cmd' */
	$post_data = 'cmd=_notify-validate';
	
	$post = $_POST;

	foreach ($post as $key => $value) {
		$value = urlencode(stripslashes_deep($value));
		$value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i','${1}%0D%0A${3}',$value);/* this fiexs paypals invalid IPN , STIOFAN */
		$post_data .= "&$key=$value";
	}
	
	$post_content = str_replace("&", "\n", urldecode($post_data));
	
	// post back to PayPal system to validate
	$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($post_data) . "\r\n\r\n";
	$skip_trans_verifired = false;
	
	$paypal_url = $paymode == 'sandbox' ? 'ssl://www.sandbox.paypal.com' : 'ssl://www.paypal.com';
		
	$fp = fsockopen ($paypal_url, 443, $errno, $errstr, 30);
	
	if (!$fp) { 
		// HTTP ERROR
	} else {
		fputs ($fp, $header . $post_data);
	
		while (!feof($fp)) {
			$res = fgets ($fp, 1024);
			
			// Inspect IPN validation result and act accordingly
			$valid_ipn = strstr($res, "VERIFIED");
			$invalid_ipn = strstr($res, "INVALID");
			
			$invoice_id		= isset($post['custom']) ? $post['custom'] : NULL; // invoice id
			$invoice_info 	= geodir_get_invoice( $invoice_id );
			$user_id		= !empty( $invoice_info ) ? $invoice_info->user_id : '1';
			
			if ( $valid_ipn || $sandbox) { // it will enter in conditon in test mode. 
				$item_name		= $post['item_name'];
				$txn_id			= $post['txn_id'];
				$payment_status	= $post['payment_status'];
				$payment_type	= $post['payment_type'];
				$payment_date	= $post['payment_date'];
				$txn_type		= $post['txn_type'];
				$subscription 	= $txn_type == 'recurring_payment' || $txn_type == 'subscr_payment' ? true : false;
				
				$mc_currency	= $post['mc_currency'];
				$mc_gross		= $post['mc_gross'];
				$payment_gross	= $post['payment_gross'];
				$receiver_email	= $post['receiver_email'];
				$paid_amount	= $mc_gross ? $mc_gross : $payment_gross;
				
				$cart_amount	= $invoice_info->paied_amount;
				$post_id		= $invoice_info->post_id;
				
				/*####################################
				######## FRAUD CHECKS ################
				####################################*/
				$fraud					= false;
				$fraud_msg				= '';
				$transaction_details	= '';
				
				if ( $receiver_email != $merchantid ) {
					$fraud = true;
					$fraud_msg .= __('### The PayPal reciver email address does not match the paypal address for this site ###<br />', 'geodir_payments');
				}
				
				if ( $paid_amount != $cart_amount ) {
					$fraud = true;
					$fraud_msg .= __('### The paid amount does not match the price package selected ###<br />', 'geodir_payments');
				}
				
				if ( $mc_currency != $currency_code ) {
					$fraud = true;
					$fraud_msg .= __('### The currency code returned does not match the code on this site. ###<br />', 'geodir_payments');
				}
				
				/*#####################################
				######## PAYMENT SUCCESSFUL ###########
				######################################*/
				if ($txn_type == 'web_accept' || $txn_type == 'subscr_payment' || $txn_type == 'recurring_payment' || $txn_type == 'express_checkout' ) {
					$paid_amount_with_currency = $paid_amount . ' ' . $mc_currency;
					
					if ( $fraud ) {
						$transaction_details .= __('WARNING FRAUD DETECTED PLEASE CHECK THE DETAILS - (IF CORRECT, THEN PUBLISH THE POST)', 'geodir_payments')."<br />";
					}
					
					$transaction_details .= $fraud_msg;
					$transaction_details .= "--------------------------------------------------<br />";
					$transaction_details .= sprintf(__("Payment Details for Invoice ID #%s", 'geodir_payments'), $invoice_id) ."<br />";
					$transaction_details .= "--------------------------------------------------<br />";
					$transaction_details .= sprintf(__("Item Name: %s", 'geodir_payments'),$item_name)."<br />";
					$transaction_details .= "--------------------------------------------------<br />";
					$transaction_details .= sprintf(__("Trans ID: %s", 'geodir_payments'), $txn_id)."<br />";
					$transaction_details .= sprintf(__("Status: %s", 'geodir_payments'), $payment_status)."<br />";
					$transaction_details .= sprintf(__("Amount: %s", 'geodir_payments'), $paid_amount_with_currency)."<br />";
					$transaction_details .= sprintf(__("Type: %s", 'geodir_payments'),$payment_type)."<br />";
					$transaction_details .= sprintf(__("Date: %s", 'geodir_payments'), $payment_date)."<br />";
					$transaction_details .= sprintf(__("Method: %s", 'geodir_payments'), $txn_type)."<br />";
					$transaction_details .= "--------------------------------------------------<br />";
										
					/*############ SET THE INVOICE STATUS START ############*/
					// update invoice status and transaction details
					geodir_update_invoice_status( $invoice_id, 'confirmed', $subscription );
					geodir_update_invoice_transaction_details( $invoice_id, $transaction_details );
					/*############ SET THE INVOICE STATUS END ############*/
					
					// send notification to admin
					geodir_payment_adminEmail( $post_id, $user_id, 'payment_success', $transaction_details );
					
					// send notification to client
					geodir_payment_clientEmail( $post_id, $user_id, 'payment_success', $transaction_details );
					
				} else if ( $txn_type == 'subscr_cancel' || $txn_type == 'subscr_failed' ) {
					// Set the subscription ac canceled
					$post_content = str_replace("&", "\n", urldecode($post_data));
					$post_content .= '\n############## '.__('ORIGINAL SUBSCRIPTION INFO BELOW', 'geodir_payments').' ####################\n';
					$post_content .= $invoice_info->html;
					
					// update invoice status and transaction details
					$status = $txn_type == 'subscr_cancel' ? 'canceled' : 'failed';
					
					geodir_update_invoice_status( $invoice_id, $status, $subscription );
					geodir_update_invoice_transaction_details( $invoice_id, $post_content );
					
				} else if( $txn_type == 'subscr_signup' ) {
					$post_content = '####### '.__('THIS IS A SUBSCRIPTION SIGNUP AND IF A FREE TRIAL WAS OFFERD NO PAYMENT WILL BE RECIVED', 'geodir_payments').' ######\n';
					$post_content .= str_replace("&", "\n", urldecode($post_data));
					
					// update invoice status and transaction details
					geodir_update_invoice_status( $invoice_id, 'confirmed', $subscription );
					geodir_update_invoice_transaction_details( $invoice_id, $post_content );
				}
				/*#####################################
				######## PAYMENT SUCCESSFUL ###########
				######################################*/				
			} else if ( $invalid_ipn ) {
				// update invoice status
				geodir_update_invoice_status( $invoice_id, 'failed' );
					
				// send notification to admin
				geodir_payment_adminEmail( $invoice_id, $user_id, 'payment_fail' );
			}	
		}
	}
}
add_action( 'geodir_ipn_handler_paypal' , 'geodir_ipn_handler_paypal' );

// 2CO IPN
function geodir_ipn_handler_2co() {
	$post = $_POST;
	
	$message_type = isset( $post['message_type'] ) ? geodir_strtoupper($post['message_type']) : '';
	$invoice_id = isset( $post['vendor_order_id'] ) ? (int)$post['vendor_order_id'] : '';
	$invoice_status = isset( $post['invoice_status'] ) ? $post['invoice_status'] : '';
	
	if ( $invoice_id > 0 && $message_type != '' && $invoice_status != '' ) {
		$invoice_info = geodir_get_invoice( $invoice_id );
		if ( empty( $invoice_info ) ) {
			exit;
		}
		
		if ( $message_type == 'ORDER_CREATED' ) {
			$post_id		= $invoice_info->post_id;
			$user_id		= $invoice_info->user_id;
			
			$notify_status 	= 'payment_fail';
			
			if ( $invoice_status == 'approved' ) { // payment status approved
				$status 		= 'confirmed';
				$notify_status 	= 'payment_success';
			} else if ( $invoice_status == 'pending' ) { // payment status pending
				$status 		= 'pending';
			} else { // payment status fail
				$status 		= 'fail';
			}
			
			$item_name		= $post['item_name_1'];
			$txn_id			= $post['invoice_id'];
			$payment_status = geodir_payment_status_name( $status );
			$amount			= geodir_payment_price( $post['invoice_cust_amount'] );
			$payment_type 	= $post['payment_type'];
			$payment_date 	= date_i18n( "F j, Y, g:i a", current_time( 'timestamp' ) );
			$payment_method = geodir_payment_method_title( '2co' );
			
			$transaction_details = "--------------------------------------------------<br />";
			$transaction_details .= wp_sprintf( __( "Payment Details for Invoice ID #%s", 'geodir_payments' ), $invoice_id ) . "<br />";
			$transaction_details .= "--------------------------------------------------<br />";
			$transaction_details .= wp_sprintf( __( "Item Name: %s", 'geodir_payments' ), $item_name ) . "<br />";
			$transaction_details .= "--------------------------------------------------<br />";
			$transaction_details .= wp_sprintf( __( "Trans ID: %s", 'geodir_payments' ), $txn_id ) . "<br />";
			$transaction_details .= wp_sprintf( __( "Status: %s", 'geodir_payments' ), $payment_status ) . "<br />";
			$transaction_details .= wp_sprintf( __( "Amount: %s", 'geodir_payments' ), $amount ) . "<br />";
			$transaction_details .= wp_sprintf( __( "Type: %s", 'geodir_payments' ), $payment_type ) . "<br />";
			$transaction_details .= wp_sprintf( __( "Date: %s", 'geodir_payments' ), $payment_date ) . "<br />";
			$transaction_details .= wp_sprintf( __( "Method: %s", 'geodir_payments' ), $payment_method ) . "<br />";
			$transaction_details .= "--------------------------------------------------<br />";
			
			geodir_update_invoice_status( $invoice_id, $status );
			geodir_update_invoice_transaction_details( $invoice_id, $transaction_details );
			
			geodir_payment_adminEmail( $post_id, $user_id, $notify_status, $transaction_details ); // send notification to admin
			geodir_payment_clientEmail( $post_id, $user_id, $notify_status, $transaction_details ); // send notification to client
		}
	}
	exit;
}
add_action( 'geodir_ipn_handler_2co' , 'geodir_ipn_handler_2co' );

// 2CO IPN
function geodir_ipn_handler_worldpay() {
	$post = $_POST;
	
	$cardType 		= isset( $post['cardType'] ) ? geodir_strtoupper($post['cardType']) : '';
	$invoice_id 	= isset( $post['cartId'] ) ? (int)$post['cartId'] : '';
	$invoice_status = isset( $post['transStatus'] ) ? $post['transStatus'] : '';
	$txn_id			= isset( $post['transId'] ) ? $post['transId'] : '';
	
	if ( $invoice_id > 0 && $invoice_status != '' && $txn_id != '' ) {
		$invoice_info = geodir_get_invoice( $invoice_id );
		if ( empty( $invoice_info ) ) {
			exit;
		}
		
		$post_id		= $invoice_info->post_id;
		$user_id		= $invoice_info->user_id;
		
		$notify_status 	= 'payment_fail';
		
		if ( $invoice_status == 'Y' ) { // payment status approved
			$status 		= 'confirmed';
			$notify_status 	= 'payment_success';
		} else if ( $invoice_status == 'C' ) { // payment status pending
			$status 		= 'canceled';
		} else { // payment status fail
			$status 		= 'fail';
		}
		
		$item_name		= $invoice_info->post_title;
		$payment_status = geodir_payment_status_name( $status );
		$amount			= geodir_payment_price( $post['amount'] );
		$payment_type 	= $cardType;
		$payment_date 	= date_i18n( "F j, Y, g:i a", current_time( 'timestamp' ) );
		$payment_method = geodir_payment_method_title( 'worldpay' );
		
		$transaction_details = "--------------------------------------------------<br />";
		$transaction_details .= wp_sprintf( __( "Payment Details for Invoice ID #%s", 'geodir_payments' ), $invoice_id ) . "<br />";
		$transaction_details .= "--------------------------------------------------<br />";
		$transaction_details .= wp_sprintf( __( "Item Name: %s", 'geodir_payments' ), $item_name ) . "<br />";
		$transaction_details .= "--------------------------------------------------<br />";
		$transaction_details .= wp_sprintf( __( "Trans ID: %s", 'geodir_payments' ), $txn_id ) . "<br />";
		$transaction_details .= wp_sprintf( __( "Status: %s", 'geodir_payments' ), $payment_status ) . "<br />";
		$transaction_details .= wp_sprintf( __( "Amount: %s", 'geodir_payments' ), $amount ) . "<br />";
		$transaction_details .= wp_sprintf( __( "Type: %s", 'geodir_payments' ), $payment_type ) . "<br />";
		$transaction_details .= wp_sprintf( __( "Date: %s", 'geodir_payments' ), $payment_date ) . "<br />";
		$transaction_details .= wp_sprintf( __( "Method: %s", 'geodir_payments' ), $payment_method ) . "<br />";
		$transaction_details .= "--------------------------------------------------<br />";
		
		geodir_update_invoice_status( $invoice_id, $status );
		geodir_update_invoice_transaction_details( $invoice_id, $transaction_details );
		
		geodir_payment_adminEmail( $post_id, $user_id, $notify_status, $transaction_details ); // send notification to admin
		geodir_payment_clientEmail( $post_id, $user_id, $notify_status, $transaction_details ); // send notification to client
	}
	exit;
}
add_action( 'geodir_ipn_handler_worldpay' , 'geodir_ipn_handler_worldpay' );

function geodir_ipn_handler_googlewallet() {
	global $wpdb;
	
	require_once  (GEODIR_PAYMENT_MANAGER_PATH.'/googlewallet/JWT.php');
	
	$paymentOpts = get_payment_options('googlechkout');
	$merchantkey = $paymentOpts['merchantsecret'];
	$currency_code = geodir_get_currency_type();
	$merchantid = $paymentOpts['merchantid'];
	$merchantkey = $paymentOpts['merchantsecret'];
	
	$encoded_jwt = $_POST['jwt']; 
	$decodedJWT = JWT::decode($encoded_jwt, $merchantkey);

	$post_title = $decodedJWT->request->name;
	$payable_amount = $decodedJWT->request->price;
	
	// yes valid recipt
	$p_arr = explode(",", $decodedJWT->request->sellerData);
	$p_arr2 = explode(":", $p_arr[1]);
	$last_postid = $p_arr2[1];
	require_once  (GEODIR_PAYMENT_MANAGER_PATH.'/googlewallet/generate_token.php');
	
	// get orderId
	$orderId = $decodedJWT->response->orderId;

	if ( $_POST['jwt']) {
		if ($orderId) {	// yes valid recipt
			$p_arr = explode(",", $decodedJWT->request->sellerData);
			$p_arr2 = explode(":", $p_arr[1]);
	
			$postid               = $p_arr2[1];
			$item_name			  = $decodedJWT->request->name;
			$txn_id				  = $orderId;
			$payment_status       = 'PAID';
			$payment_type         = 'Google Wallet';
			$payment_date         = date("F j, Y, g:i a");
			$txn_type             = $decodedJWT->typ;
			
			$mc_currency          = $decodedJWT->request->currencyCode; // get curancy code
			$mc_gross             = $decodedJWT->request->price;
			$mc_amount3           = $decodedJWT->request->price;
			
			###################################################################################################################
			$header = '';

			// get current post status
			$current_post_status = get_post_status($postid);
			
			$post_pkg = geodir_get_post_meta($postid, 'package_id',true); /* get the post price package ID*/
			
			$pricesql = $wpdb->prepare( "select * from ".GEODIR_PRICE_TABLE." where status=1 and pid=%d", array($post_pkg) );			
			$priceinfo = $wpdb->get_row($pricesql, ARRAY_A); /* Get the price package info*/
			
			$pkg_price = $priceinfo['amount']; /* get the price of the package		*/
			$currency_code = geodir_get_currency_type(); /* get the actual curency code		*/
			$merchantid = $paymentOpts['merchantid']; /* Get the site paypal address*/
			if ($mc_gross) {
				$paid_amt = $mc_gross;
			} else {
				$paid_amt = $mc_amount3;
			}
			
			$productinfosql = $wpdb->prepare(
												"select ID,post_title,guid,post_author from $wpdb->posts where ID = %d",
												array($postid) 
											);
			$productinfo = $wpdb->get_results($productinfosql);
			foreach ($productinfo as $productinfoObj) {
				/*$post_link = home_url().'/?ptype=preview&alook=1&pid='.$postid;*/
				$post_title = '<a href="'.get_permalink($postid).'">'.$productinfoObj->post_title.'</a>'; 
				$aid = $productinfoObj->post_author;
				$userInfo = geodir_get_author_info($aid);
				$to_name = $userInfo->user_nicename;
				$to_email = $userInfo->user_email;
				$user_email = $userInfo->user_email;
			}
			
		
			/*######################################
			######## PAYMENT SUCCESSFUL ##########
			######################################*/
			
			if ($txn_type) {
				$post_default_status = geodir_new_post_default_status();
				if ($post_default_status=='') {
					$post_default_status = 'publish';
				}
				geodir_set_post_status($postid,$post_default_status);
				
				$transaction_details ='';
				$paid_amount_with_currency = get_option('geodir_currencysym') .$paid_amt;
				
				$transaction_details .= "--------------------------------------------------<br />";
				$transaction_details .= sprintf(__("Payment Details for Invoice ID #%s", 'geodir_payments'), $postid ) ."<br />";
				$transaction_details .= "--------------------------------------------------<br />";
				$transaction_details .= sprintf(__("Listing Title: %s", 'geodir_payments'),$item_name)."<br />";
				$transaction_details .= "--------------------------------------------------<br />";
				$transaction_details .= sprintf(__("Trans ID: %s", 'geodir_payments'), $txn_id)."<br />";
				$transaction_details .= sprintf(__("Status: %s", 'geodir_payments'), $payment_status)."<br />";
				$transaction_details .= sprintf(__("Amount: %s", 'geodir_payments'),$paid_amount_with_currency)."<br />";
				$transaction_details .= sprintf(__("Type: %s", 'geodir_payments'),$payment_type)."<br />";
				$transaction_details .= sprintf(__("Date: %s", 'geodir_payments'), $payment_date)."<br />";
				$transaction_details .= sprintf(__("  Method: %s", 'geodir_payments'), $txn_type)."<br />";
				$transaction_details .= "--------------------------------------------------<br />";		
				$transaction_details .= __("Information Submitted URL", 'geodir_payments')."<br />";
				$transaction_details .= "--------------------------------------------------<br />";

				// Extend expire date start	
				$invoice_info = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".INVOICE_TABLE." WHERE post_id = %d AND is_current=%s", array($postid,'1')));
				$invoice_id = $invoice_info->id;
				$invoice_package_id = '';
				if (!empty($invoice_info) && isset($invoice_info->package_id) ) {
					$invoice_package_id = $invoice_info->package_id;
					$invoice_alive_days = $invoice_info->alive_days;
					$invoice_status = $invoice_info->status;
				}
				
				$geodir_post_info = geodir_get_post_info($postid);
				
				if (!empty($geodir_post_info)) {
					$post_package_id = $geodir_post_info->package_id;
					$post_expire_date = $geodir_post_info->expire_date;
					
					if (!empty($invoice_package_id) && $invoice_alive_days>0 && $invoice_package_id==$post_package_id && geodir_strtolower($post_expire_date)!='never' && strtotime($post_expire_date) >= strtotime(date('Y-m-d')) && $current_post_status=='publish') {
						$alive_days = (int)($geodir_post_info->alive_days + $invoice_alive_days);
						$expire_date = date('Y-m-d', strtotime($post_expire_date."+".$invoice_alive_days." days"));
					} else {
						$alive_days = (int)$geodir_post_info->alive_days;
						if (geodir_strtolower($post_expire_date)!='never' && strtotime($post_expire_date) < strtotime(date('Y-m-d'))) {
							$alive_days = $invoice_alive_days;
						}
						
						$expire_date = $alive_days>0 ? date('Y-m-d', strtotime(date('Y-m-d')."+".$alive_days." days")) : 'Never';
					}
					
					geodir_save_post_meta($postid, 'alive_days', $alive_days);
					geodir_save_post_meta($postid, 'expire_date', $expire_date);
				}
				// Extend expire date start	end

				/*############ SET THE INVOICE STATUS START ############*/
				// update invoice statuse and transactio details
				geodir_update_invoice_status( $invoice_id, 'Paid' );
				geodir_update_invoice_transaction_details( $invoice_id, $transaction_details );

				/*############ SET THE INVOICE STATUS END ############*/
				geodir_payment_adminEmail( $postid, $aid, 'payment_success', $transaction_details ); /*email to admin*/
				geodir_payment_clientEmail( $postid, $aid, 'payment_success', $transaction_details ); /*email to client*/
			}
			
			/*######################################
			######## PAYMENT SUCCESSFUL ##########
			######################################*/
			header("HTTP/1.0 200 OK"); 
			echo $orderId;
			
		} elseif (strcmp($res, "INVALID") == 0) {
			geodir_payment_adminEmail( $_POST['custom'], '1', 'payment_fail' ); /* email to admin*/
		}
	}
}
add_action( 'geodir_ipn_handler_googlewallet' , 'geodir_ipn_handler_googlewallet' );
?>