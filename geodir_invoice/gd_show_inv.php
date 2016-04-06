<?php

if(!empty($_GET['p']) && !empty($_GET['inv_id'])){
		$invoiceID = str_replace('-AA-','/',$_GET['inv_id']);
		$url= base64_decode($_GET['p']);		
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		
		curl_setopt($ch, CURLOPT_URL,$url);		
		$result=curl_exec($ch);		
		curl_close($ch);		
		$finalresult = json_decode($result, true);

		$ii = 1;
		foreach($finalresult as $fr){
			if($fr['number'] == $invoiceID ){ ?>
				<p class="download-btn">
					<a href="https://www.worldcheckin.com/wp-content/plugins/geodir_invoice/invoice_pdf.php?inv_id=<?php echo $_GET['inv_id'].'&p='.$_GET['p'];?>"> Click Here to download </a>
				</p>
				<style type="text/css">
					table
					{

						font-size: 14px;
						vertical-align: middle;
						width:100%; /*display:inline-block;*/
					}
					table.pdf-logo-left td{padding:4px 10px; cellpadding:50%;}
					table.pdf-logo-left th{padding:4px 10px; cellpadding:50%;}	
					table.pdf-mid-sec{ width:26%; float:left;}
					table.pdf-mid-sec td{padding:4px 10px; cellpadding:50%; width:50%; }
					table.pdf-mid-sec th{padding:4px 10px; cellpadding:50%; width:auto; text-align:left;}	
					tr{ width:100%;}	 
					table.pdf-main-content{
						padding-top:30px; 
						padding-bottom:10px;
						border-collapse: collapse;
						cellspacing:0; 
						font-size: 12px;
						
					}
					table.pdf-main-content td{padding:11px;}
					table.pdf-main-content th{padding:11px; background:#f1f1f1; }
					table.pdf-total-sec{float:right; text-align:right; width:100%;} 
					table.pdf-total-sec td{ width:50%;  text-align:right; cellpadding:50%;}
					
					table.pdf-main-content th {   padding: 11px;  width: 120px;}
					.pdf-main-content tr {  }
					table.pdf-main-content td {  padding: 11px;  width: 120px;  text-align: center;}
					.download-btn {  background: #ccc none repeat scroll 0 0;  border-radius: 5px;  float: right;  padding: 8px;}
					.download-btn a{ text-decoration: none;}
					.td-border {  border: 1px solid #ccc;}
				</style>
				<table width="100%">
					<tr>
						<td style="width: 30%;">
						<table  class="pdf-logo-left"  style="width: 100%;">
							<tr>
								<td><strong>Invoice No. </strong></td>
								<td> <?php echo $fr['number'];?></td>
							</tr>
							<tr>
								<td><strong>Issue date: </strong></td>
								<td> <?php echo $fr['issue_date'];?></td>
							</tr>
							<tr>
								<td><strong>Due date: </strong></td>
								<td> <?php echo $fr['payment_to'];?></td>
							</tr>
							<tr>
								<td><strong>Payment type: </strong></td>
								<td > <?php echo $fr['payment_type'];?></td>
							</tr>
						</table>
					</td>
					<td style="width: 70%;">
						<table class="pdf-logo" style="width: 100%; text-align: center; font-size: 14px">
							<tr>
								<td style="width: 75%;"></td>
								<td style="width: 25%; color: #444444;">
									<img style="width: 100%;" src="https://s3-eu-west-1.amazonaws.com/fs.firmlet.com/invoiceocean/accounts/logos/288002/medium/3Dtransbg_-_222x100_-_TEST.png" alt="Logo">
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<hr/>
			<table class="pdf-mid-sec" width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<th width="25%">Seller</th>
						<th width="25%">&nbsp;</th>
						<th width="25%">&nbsp;</th>
						<th width="25%">Buyer</th>
					</tr>
					<tr>
						<td width="25%"><?php echo $fr['seller_name'];?></td>
						<th width="25%">&nbsp;</th>
						<th width="25%">&nbsp;</th>
						<td width="25%"><?php echo $fr['buyer_name'];?></td>

					</tr>
					<tr>
						<td width="25%"><?php echo $fr['seller_tax_no'];?></td>
						<th width="25%">&nbsp;</th>
						<th width="25%">&nbsp;</th>
						<td width="25%"><?php echo $fr['buyer_tax_no'];?></td>
					</tr>
				</table>
				
				
				<table class="pdf-main-content" width="100%">
					<tr class="td-border">
						<th class="td-border">No.</th>
						<th class="td-border">Item</th>
						<th class="td-border">Qty</th>
						<th class="td-border">Unit Net Price</th>
						<th class="td-border">Unit Gross Price</th>
						<th class="td-border">Total Net</th>
						<th class="td-border">VAT %</th>
						<th class="td-border">Var Amount</th>
						<th class="td-border">Total Gross</th>
					</tr>
					<tr>
						<td class="td-border"><?php echo $ii;?></td>
						<td class="td-border"><?php echo $fr['product_cache'];?></td>
						<td class="td-border">1</td>
						<td class="td-border"><?php echo number_format($fr['price_net'], 2);?></td>
						<td class="td-border"><?php echo number_format($fr['price_gross'], 2);?></td>
						<td class="td-border"><?php echo number_format($fr['price_net'], 2);?></td>
						<td class="td-border">19</td>
						<td class="td-border"><?php echo number_format($fr['price_tax'], 2);?></td>
						<td class="td-border"><?php echo number_format($fr['price_gross'], 2);?></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td class="td-border"><strong>Total</strong></td>
						<td class="td-border"><?php echo number_format($fr['price_net'], 2);?></td>
						<td class="td-border">&nbsp;</td>
						<td class="td-border"><?php echo number_format($fr['price_tax'], 2);?></td>
						<td class="td-border"><?php echo number_format($fr['price_gross'], 2);?></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td class="td-border"><strong>Tex Rate</strong></td>
						<td class="td-border"><?php echo number_format($fr['price_net'], 2);?></td>
						<td class="td-border">19</td>
						<td class="td-border"><?php echo number_format($fr['price_tax'], 2);?></td>
						<td class="td-border"><?php echo number_format($fr['price_gross'], 2);?></td>
					</tr>
				</table>
				
				<table  class="pdf-total-sec" width="100%">
					<tr>
						<td  style="width: 70%;">&nbsp;</td>
						<td>
							<table  width="100%">
								<tr>
									<td  style="width: 50%;"><strong>Total Net Price </strong></td>
									<td  style="width: 30%;"><?php echo $fr['currency']." ".number_format($fr['price_net'], 2);?></td>
								</tr>
								
								<tr>
									<td  style="width: 50%;"><strong>Vat Amount </strong></td>
									<td  style="width: 30%;"><?php echo $fr['currency']." ".number_format($fr['price_tax'], 2);?></td>
								</tr>
								
								<tr>
									<td  style="width: 50%;"><strong>Total Gross Price </strong></td>
									<td  style="width: 30%;"><?php echo $fr['currency']." ".number_format($fr['price_gross'], 2);?></td>
								</tr>
							</table>	
						</td>
					</tr>
				</table>
				<hr/>
				<p><strong>Paid </strong> <?php echo $fr['currency']." ".number_format($fr['paid'], 2);?></p>
				<hr/>
				<p><strong>Total Due </strong> <?php echo $fr['currency']." ".number_format($fr['price_gross'], 2);?></p>
				<table style="width: 100%; text-align: center; font-size: 14px">
					<tr>
						<td style="width: 70%;"></td>
						<td style="width: 30%; color: #444444;">
							<p>Seller's Sigunature</p>
							<img style="width: 100%;" src="https://s3-eu-west-1.amazonaws.com/fs.firmlet.com/invoiceocean/accounts/stamps/288002/medium/WCI_StampSignature.png"  alt="Logo">
						</td>
					</tr>
				</table>
				<p>Reduce costs & increase sales by promoting with us • http://www.worldcheckin.com Earn money online on recurring basis • http://www.worldcheckin.com/become-an-affiliate Contact us if you need help • http://www.worldcheckin.com/contact/invoice-request </p>
			<?php
				$ii++;
			}
		}
	
}else{
	header('Location: ' . $_SERVER['HTTP_REFERER']);
}
