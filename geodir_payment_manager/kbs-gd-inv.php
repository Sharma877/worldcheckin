<?php

global $wpdb;
		$_SESSION['tax_Cal_data'] = $html_data;
		$total = "";
		$tax = "";
		$tax_price = "";
		$initial_price = "";
		
		if(isset($invoice_id)){
			
			   $invoice_table = $wpdb->base_prefix.'geodir_invoice';
			
			   $get_invoice_details = $wpdb->get_row( "SELECT `discount`,`post_id` FROM `$invoice_table` WHERE id = '".$invoice_id."'" );
			   
			   $get_post_type = $wpdb->get_row( "SELECT `post_type` FROM `".$wpdb->base_prefix."posts` WHERE id = '".$get_invoice_details->post_id."'" );
		
			   $get_invoice_data = $wpdb->get_row( "SELECT * FROM `".$wpdb->base_prefix."geodir_".$get_post_type->post_type."_detail` WHERE `post_id` = '".$get_invoice_details->post_id."'" );
			   
				$cart_data = json_decode($get_invoice_data->geodir_taxdata);
				
				//echo "<pre>"; print_r($cart_data);echo "</pre>";
				
				
				$total = $cart_data->Final_List_Price;
				$tax = $cart_data->Tax_Applied;
				$tax_price = $cart_data->Tax_Amount_Added_on_List_Price;
				$initial_price = $cart_data->List_Price;


				if($get_invoice_details->discount > 0){
				  
				 $total = $total - $get_invoice_details->discount;
				  
				}
				
					  $wpdb->update( 
						  $invoice_table, 
						  array( 
							  'paied_amount' => $total,	// string
							  'tax_amount' => $tax_price,	// integer (number)
							  'amount' => $initial_price
						  ), 
						  array( 'ID' => $invoice_id )
					  );
		}
		
		/******************** Update price on the invoice page ************************/	
			
		/***************** code edited by kindlebit on 25th-jan-2016 ********************/	    
  
  
	$user_id = get_current_user_id();
	
	if ( !$user_id ) {
		wp_redirect( geodir_login_url() );
		exit();
	}
	
	$invoice_info 	= geodir_get_invoice( $invoice_id );
	$is_owner 		= geodir_payment_check_invoice_owner( $invoice_info, $user_id );
	
	if ( !$is_owner || empty( $invoice_info ) ) {
		wp_redirect( geodir_payment_invoices_page_link() );
		exit();
	}
	
	geodir_payment_add_invoice_scripts();
	
	$item_name = $invoice_info->post_title;
	$coupon_code = trim( $invoice_info->coupon_code );
	
	$payment_method = $invoice_info->paymentmethod;
	
	$invoice_type = $invoice_info->invoice_type;
	$post_id = $invoice_info->post_id;
	$amount = $invoice_info->amount;
	$tax_amount = $invoice_info->tax_amount;
	$discount = $invoice_info->discount;
	$paied_amount = $invoice_info->paied_amount;
	$date = $invoice_info->date;
	$date_updated = $invoice_info->date_updated;
	
	$amount_display = geodir_payment_price($amount);
	$tax_amount_display = geodir_payment_price($tax_amount);
	$discount_display = geodir_payment_price($discount);
	$paied_amount_display = geodir_payment_price($paied_amount);
	
	$coupon_allowed = get_option( 'geodir_allow_coupon_code' );
	
	$pay_for_invoice = geodir_payment_allow_pay_for_invoice( $invoice_info );
	
	$invoice_details = geodir_payment_invoice_view_details( $invoice_info );	
	$invoice_nonce = wp_create_nonce( 'gd_invoice_nonce_' . $invoice_id );	
	
	$date = $date_updated != '0000-00-00 00:00:00' ? $date_updated : $date;
	$date = $date != '0000-00-00 00:00:00' ? $date : '';
	$date_display = $date != '' ? date_i18n( geodir_default_date_format(), strtotime( $date ) ) : '';
	
	$dat_format = geodir_default_date_format() . ' ' . get_option( 'time_format' );
	$date_updated_display = $date != '' ? date_i18n( $dat_format, strtotime( $date ) ) : '';
	
	$payment_method_display = geodir_payment_method_title( $payment_method );
	
	$inv_status = $invoice_info->status;
	if ( in_array( geodir_strtolower( $inv_status ), array( 'paid', 'active', 'subscription-payment', 'free' ) ) ) {
		$inv_status = 'confirmed';
	} else if ( in_array( geodir_strtolower( $inv_status ), array( 'unpaid' ) ) ) {
		$inv_status = 'pending';
	}
	
	$status_display = geodir_payment_status_name( $inv_status );
	$invoice_type_name = geodir_payment_invoice_type_name( $invoice_type );
	
	$incomplete = $inv_status == 'pending' && empty($invoice_info->paymentmethod) ? true : false;
	if ($incomplete && $inv_status == 'pending') {
		$status_display = __('Incomplete', 'geodir_payments');
	}
	
	$listing_display = '';
	$package_display = '';
	if ( ( $invoice_type == 'add_listing' || $invoice_type == '' || $invoice_type == 'upgrade_listing' || $invoice_type == 'renew_listing' || $invoice_type == 'claim_listing' ) && $post_id > 0 ) {
		$post_status = get_post_status( $post_id );
		$listing_display = get_the_title( $post_id );
		
		if ( $post_status == 'publish' || $post_status == 'private' ) {
			$listing_display = '<a href="' . get_permalink( $post_id ) . '" target="_blank">' . $listing_display . '</a>';
		}
		
		$package_id = $invoice_info->package_id;
		$package_display = $invoice_info->package_title;
	}
	
	$transaction_details = trim($invoice_info->HTML) != '' ? trim($invoice_info->HTML) : NULL;
	
	if ( !$invoice_info->paied_amount > 0 ) {
		$payment_method_display = __( 'Instant Publish', 'geodir_payments' );
	}
	ob_start();
	?>
	
	<img style="width: 100%;" src="https://s3-eu-west-1.amazonaws.com/fs.firmlet.com/invoiceocean/accounts/logos/288002/medium/3Dtransbg_-_222x100_-_TEST.png" alt="Logo">
	<div class="entry-content gd-pmt-invoice-detail gd-pmt-invoice-<?php echo $inv_status;?>" id="gd_pmt_invoice_detail">
		<h4><?php _e( 'Invoice Details:' , 'geodir_payments' );?></h4>
		
		<ul class="gd-order-details">
			<li class="gd-pmt-order"><?php _e( 'Invoice:' , 'geodir_payments' );?><strong>#<?php echo $invoice_id;?></strong></li>
			<li class="gd-pmt-date" title="<?php esc_attr_e( $date );?>"><?php _e( 'Date:' , 'geodir_payments' );?><strong><?php echo $date_display;?></strong></li>
			<li class="gd-pmt-total"><?php _e( 'Total:' , 'geodir_payments' );?><strong><?php echo $paied_amount_display;?></strong></li>
			<li class="gd-pmt-method"><?php _e( 'Payment Method:' , 'geodir_payments' );?><strong><?php echo $payment_method_display;?></strong></li>
			<li class="gd-pmt-status"><?php _e( 'Status:' , 'geodir_payments' );?><strong><?php echo $status_display;?></strong></li>
		</ul>
		
		<h4><?php _e( 'Item Details:' , 'geodir_payments' );?></h4>
		<div class="gd-invoice-detail-info clearfix">
			<table class="gd-cart-tbl">
				<thead>
					<tr>
						<td><?php _e( 'Item' , 'geodir_payments' );?></td>
						<td class="gd-cart-price"><?php _e( 'Price', 'geodir_payments' );?></td>
					</tr>
				</thead>
				<tbody>
					<tr class="gd-cart-item gd-cart-amount">
						<td class="gd-item-name"><?php echo $item_name ;?></td>
						<td class="gd-cart-price"><?php echo $amount_display ;?></td>
					</tr>					
				</tbody>
				<tfoot>
					<?php if ( $tax_amount > 0 || $discount > 0 ) { ?>
					<tr class="gd-cart-subtotal gd-cart-bold">
						<td class="gd-item-name"><?php _e( 'Sub-Total:', 'geodir_payments' );?></td>
						<td class="gd-cart-price"><?php echo $amount_display ;?></td>
					</tr>
					<?php } ?>
					<?php if ( $tax_amount > 0 ) { ?>
					<tr class="gd-cart-tax">
						<td class="gd-item-name"><?php _e( 'Tax:', 'geodir_payments' );?></td>
						<td class="gd-cart-price"><?php echo $tax_amount_display ;?></td>
					</tr>
					<?php } ?>
					<?php if ( $coupon_allowed || $discount > 0 ) { ?>
					<tr class="gd-cart-discount">
						<td class="gd-item-name"><?php echo wp_sprintf( __( 'Discount%s:' , 'geodir_payments' ), ( $coupon_code != '' ? ' ( ' . $coupon_code . ' )' : '' ) );?></td>
						<td class="gd-cart-price"><?php echo $discount_display ;?></td>
					</tr>
					<?php } ?>
					<tr class="gd-cart-total gd-cart-gry gd-cart-bold">
						<td class="gd-item-name"><?php _e( 'Total:', 'geodir_payments' );?></td>
						<td class="gd-cart-price"><?php echo $paied_amount_display ;?></td>
					</tr>
				</tfoot>
			</table>
		</div>
		<div class="gd-pmt-listing-detail clearfix">
				<h4><?php _e( 'Listing Details:' , 'geodir_payments' );?></h4>
				<div class="gd-inv-detail">
					<label class="gd-inv-lbl"><?php _e( 'Type:', 'geodir_payments' );?> </label>
					<span class="gd-inv-val"><?php echo $invoice_type_name;?></span>
				</div>
				<?php do_action( 'geodir_payment_invoice_before_listing_details', $invoice_info ); ?>
				<?php if ( $listing_display ) { ?>
				<div class="gd-inv-detail">
					<label class="gd-inv-lbl"><?php _e( 'Listing ID:', 'geodir_payments' );?> </label>
					<span class="gd-inv-val"><?php echo $post_id;?></span>
				</div>
				<div class="gd-inv-detail">
					<label class="gd-inv-lbl"><?php _e( 'Listing Title:', 'geodir_payments' );?> </label>
					<span class="gd-inv-val"><?php echo $listing_display;?></span>
				</div>
				<?php } if ( $package_display ) { ?>
				<div class="gd-inv-detail">
					<label class="gd-inv-lbl"><?php _e( 'Package ID:', 'geodir_payments' );?> </label>
					<span class="gd-inv-val"><?php echo $package_id;?></span>
				</div>
				<div class="gd-inv-detail">
					<label class="gd-inv-lbl"><?php _e( 'Package:', 'geodir_payments' );?> </label>
					<span class="gd-inv-val"><?php echo $package_display;?></span>
				</div>
				<?php } if ( $date_updated_display ) { ?>
				<div class="gd-inv-detail">
					<label class="gd-inv-lbl"><?php _e( 'Last Updated:', 'geodir_payments' );?> </label>
					<span class="gd-inv-val" title="<?php echo $date;?>"><?php echo $date_updated_display;?></span>
				</div>
				<?php } ?>
				<?php do_action( 'geodir_payment_invoice_after_listing_details', $invoice_info ); ?>
			</div>
			<?php if ( $transaction_details ) { ?>
			<div class="gd-pmt-trans-detail clearfix">
				<h4><?php _e( 'Transaction Details:' , 'geodir_payments' );?></h4>
				<span class="gd-trans-text"><?php echo $transaction_details;?></span>
			</div>
			<?php } ?>
		
		<?php
		global $wpdb;
		$get_post_type = $wpdb->get_row( "SELECT `post_author`,`post_type` FROM `".$wpdb->base_prefix."posts` WHERE `ID` = '".$post_id."'" );
		
		$post_type = $get_post_type->post_type;		
		
		$get_data = $wpdb->get_row( "SELECT * FROM `".$wpdb->base_prefix."geodir_".$post_type."_detail` WHERE `post_id` = '".$post_id."'" );
		
		if($get_data->geodir_usertype == "business-user" && $get_data->geodir_validatedoc != 1){ ?>
		  
			<style>
			.button.btn-primary {
			  display: none;
			}
			</style>
		  
		 <?php } ?>
		
		<div style="margin-top: 15px;" class="gd-pmt-custom-detail clearfix <?php echo $transaction_details ? 'gd-pmt-custom-trans' : '';?>">
			  <div class="gd-pmt-listing-detail clearfix">
					  <h4>User Details:</h4>
					  <div class="gd-inv-detail">
						  <label class="gd-inv-lbl">First Name: </label>
						  <span class="gd-inv-val"><?php echo $get_data->geodir_billingfirstname; ?></span>
					  </div>
					  
					  <div class="gd-inv-detail">
						  <label class="gd-inv-lbl">Last Name: </label>
						  <span class="gd-inv-val"><?php echo $get_data->geodir_billinglastname; ?></span>
					  </div>
					  
					  <?php if($get_data->geodir_usertype == "business-user"){ ?>
					  
					  <div class="gd-inv-detail">
						  <label class="gd-inv-lbl">Company Name: </label>
						  <span class="gd-inv-val"><?php echo $get_data->geodir_companyname; ?></span>
					  </div>
					  
					  <div class="gd-inv-detail">
						  <label class="gd-inv-lbl">Company City: </label>
						  <span class="gd-inv-val"><?php echo $get_data->geodir_companycity; ?></span>
					  </div>					  
					  
					  <div class="gd-inv-detail">
						  <label class="gd-inv-lbl">Company Address: </label>
						  <span class="gd-inv-val"><?php echo $get_data->geodir_companyaddress; ?></span>
					  </div>						
					  
					  <?php }else{ ?>
					  
					  <div class="gd-inv-detail">
						  <label class="gd-inv-lbl">Billing City: </label>
						  <span class="gd-inv-val"><?php echo $get_data->geodir_billingcity; ?></span>
					  </div>					  
					  
					  <div class="gd-inv-detail">
						  <label class="gd-inv-lbl">Billing Address: </label>
						  <span class="gd-inv-val"><?php echo $get_data->geodir_billingaddress; ?></span>
					  </div>						
					  
					  <?php } ?>
					  
					  <div class="gd-inv-detail">
						  <label class="gd-inv-lbl">Customer Id: </label>
						  <span class="gd-inv-val"><?php echo $get_post_type->post_author; ?></span>
					  </div>					  
					<img style="width: 100%;" src="https://s3-eu-west-1.amazonaws.com/fs.firmlet.com/invoiceocean/accounts/stamps/288002/medium/WCI_StampSignature.png"  alt="Logo">  
			  </div>
		</div>
		</div>
		
	</div>		
