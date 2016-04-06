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



</style>

 
 
 
 
 
  <page backcolor="#FEFEFE" backimgx="center" backimgy="bottom" backimgw="100%" backtop="0" backbottom="30mm" footer="date;heure;page" style="font-size: 12pt">
    <bookmark title="Lettre" level="0" ></bookmark>
    
    
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td colspan="0" style="width: 30%;">
            <table  class="pdf-logo-left"  cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
		<tr>
			<td colspan="0"><strong>Invoice No. </strong></td>
			<td colspan="0"> <?php echo $fr['number'];?></td>
		</tr>
		<tr>
			<td colspan="0"><strong>Issue date: </strong></td>
			<td colspan="0"> <?php echo $fr['issue_date'];?></td>
		</tr>
		<tr>
			<td colspan="0"><strong>Due date: </strong></td>
			<td colspan="0"> <?php echo $fr['payment_to'];?></td>
		</tr>
		<tr>
			<td colspan="0"><strong>Payment type: </strong></td>
			<td colspan="0" > <?php echo $fr['payment_type'];?></td>
		</tr>
	</table>
            
            </td>
            <td colspan="0" style="width: 70%;">
            <table class="pdf-logo" cellspacing="0" cellpadding="0" border="0" style="width: 100%; text-align: center; font-size: 14px">
        <tr>
            <td colspan="0" style="width: 75%;"></td>
            <td colspan="0" style="width: 25%; color: #444444;">
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
			<th colspan="0" width="25%">Seller</th>
			<th colspan="0" width="25%">&nbsp;</th>
			<th colspan="0" width="25%">&nbsp;</th>
			<th colspan="0" width="25%">Buyer</th>
		</tr>
		<tr>
			<td colspan="0" width="25%"><?php echo $fr['seller_name'];?></td>
			<th colspan="0" width="25%">&nbsp;</th>
			<th colspan="0" width="25%">&nbsp;</th>
			<td colspan="0" width="25%"><?php echo $fr['buyer_name'];?></td>
		</tr>
		<tr>
			<td colspan="0" width="25%"><?php echo $fr['seller_tax_no'];?></td>
			<th colspan="0" width="25%">&nbsp;</th>
			<th colspan="0" width="25%">&nbsp;</th>
			<td colspan="0" width="25%"><?php echo $fr['buyer_tax_no'];?></td>
		</tr>
	</table>
	
	
	<table class="pdf-main-content" border="0" cellpadding="0" colspan="0" width="100%">
		<tr style="border:1px solid #ccc;">
			<th colspan="0" style="border:1px solid #ccc;">No.</th>
			<th colspan="0" style="border:1px solid #ccc;">Item</th>
			<th colspan="0" style="border:1px solid #ccc;" align="right">Qty</th>
			<th colspan="0" style="border:1px solid #ccc;" align="right">Unit Net Price</th>
			<th colspan="0" style="border:1px solid #ccc;" align="right">Unit Gross Price</th>
			<th style="border:1px solid #ccc;" align="right">Total Net</th>
			<th colspan="0" style="border:1px solid #ccc;" align="right">VAT %</th>
			<th colspan="0" style="border:1px solid #ccc;" align="right">Var Amount</th>
			<th colspan="0" style="border:1px solid #ccc;" align="right">Total Gross</th>
		</tr>
		<tr style="border:1px solid #ccc;">
			<td colspan="0" cellpadding="0" style="border:1px solid #ccc;"><?php echo $ii;?></td>
			<td colspan="0" cellpadding="0" style="border:1px solid #ccc;"><?php echo $fr['product_cache'];?></td>
			<td colspan="0" cellpadding="0" style="border:1px solid #ccc;" align="right">1</td>
			<td colspan="0" cellpadding="0" style="border:1px solid #ccc;" align="right"><?php echo number_format($fr['price_net'], 2);?></td>
			<td colspan="0" cellpadding="0" style="border:1px solid #ccc;" align="right"><?php echo number_format($fr['price_gross'], 2);?></td>
			<td colspan="0" cellpadding="0" style="border:1px solid #ccc;" align="right"><?php echo number_format($fr['price_net'], 2);?></td>
			<td colspan="0" cellpadding="0" style="border:1px solid #ccc;" align="right">19</td>
			<td colspan="0" cellpadding="0" style="border:1px solid #ccc;" align="right"><?php echo number_format($fr['price_tax'], 2);?></td>
			<td colspan="0" cellpadding="0" style="border:1px solid #ccc;" align="right"><?php echo number_format($fr['price_gross'], 2);?></td>
		</tr>
		<tr>
			<td colspan="0" cellpadding="0">&nbsp;</td>
			<td colspan="0" cellpadding="0">&nbsp;</td>
			<td colspan="0" cellpadding="0">&nbsp;</td>
			<td colspan="0" cellpadding="0" style="border-right:1px solid #ccc;">&nbsp;</td>
			<td colspan="0" cellpadding="0" style="border:1px solid #ccc;" align="right"><strong>Total</strong></td>
			<td colspan="0" cellpadding="0" style="border:1px solid #ccc;" align="right"><?php echo number_format($fr['price_net'], 2);?></td>
			<td colspan="0" cellpadding="0" style="border:1px solid #ccc;" align="right">&nbsp;</td>
			<td colspan="0" cellpadding="0" style="border:1px solid #ccc;" align="right"><?php echo number_format($fr['price_tax'], 2);?></td>
			<td colspan="0" cellpadding="0" style="border:1px solid #ccc;" align="right"><?php echo number_format($fr['price_gross'], 2);?></td>
		</tr>
		<tr>
			<td colspan="0" cellpadding="0">&nbsp;</td>
			<td colspan="0" cellpadding="0">&nbsp;</td>
			<td colspan="0" cellpadding="0">&nbsp;</td>
			<td colspan="0" cellpadding="0" style="border-right:1px solid #ccc;">&nbsp;</td>
			<td colspan="0" cellpadding="0" style="border:1px solid #ccc;" align="right"><strong>Tex Rate</strong></td>
			<td colspan="0" cellpadding="0" style="border:1px solid #ccc;" align="right"><?php echo number_format($fr['price_net'], 2);?></td>
			<td colspan="0" cellpadding="0" style="border:1px solid #ccc;" align="right">19</td>
			<td colspan="0" cellpadding="0" style="border:1px solid #ccc;" align="right"><?php echo number_format($fr['price_tax'], 2);?></td>
			<td colspan="0" cellpadding="0" style="border:1px solid #ccc;" align="right"><?php echo number_format($fr['price_gross'], 2);?></td>
		</tr>
	</table>
	
	<table  align="right" class="pdf-total-sec" border="0" cellpadding="0" colspan="0" width="100%">
		<tr  align="right">
			<td colspan="0" cellpadding="0" style="width: 70%;">&nbsp;</td>
			
			<td align="right" >
				
				
				<table  border="0" cellpadding="0" colspan="0" width="100%">
			<tr >
				<td  style="width: 50%;"><strong>Total Net Price </strong></td>
				<td  style="width: 10%;"><?php echo $fr['currency']." ".number_format($fr['price_net'], 2);?></td>
			</tr>
			
			<tr >
				<td  style="width: 50%;"><strong>Vat Amount </strong></td>
				<td  style="width: 10%;"><?php echo $fr['currency']." ".number_format($fr['price_tax'], 2);?></td>
			</tr>
			
			<tr >
				<td  style="width: 50%;"><strong>Total Gross Price </strong></td>
				<td  style="width: 10%;"><?php echo $fr['currency']." ".number_format($fr['price_gross'], 2);?></td>
			</tr>
			
			</table>	
				
			</td>
				
				
				
			
	</tr>
	</table>
			
			
	
	
	<hr/>
	<p><strong>Paid </strong> <?php echo $fr['currency']." ".number_format($fr['paid'], 2);?></p>
	<hr/>
	<p><strong>Total Due </strong> <?php echo $fr['currency']." ".number_format($fr['price_gross'], 2);?></p>
	<table cellspacing="0" cellpadding="0" border="0" style="width: 100%; text-align: center; font-size: 14px">
        <tr>
            <td style="width: 70%;"></td>
            <td style="width: 30%; color: #444444;">
				<p>Seller's Sigunature</p>
                <img style="width: 100%;" src="https://s3-eu-west-1.amazonaws.com/fs.firmlet.com/invoiceocean/accounts/stamps/288002/medium/WCI_StampSignature.png"  alt="Logo">
            </td>
        </tr>
    </table>
	<p>Reduce costs & increase sales by promoting with us • http://www.worldcheckin.com Earn money online on recurring basis • http://www.worldcheckin.com/become-an-affiliate Contact us if you need help • http://www.worldcheckin.com/contact/invoice-request </p>

</page>
