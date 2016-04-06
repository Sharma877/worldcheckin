<?php
include('../../../wp-config.php');
	global $wpdb;
	$sql7= "SELECT * FROM `wp_global_invoiceocean_api` WHERE `mode_enabled` = '1'";
	$result7 = $wpdb->get_row($sql7);
	$hostname = $result7->hosturl;
	$apitoken = $result7->apitoken;
			
	$url= "$hostname/invoices.json?api_token=$apitoken";		
	$ch = curl_init();		
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);		
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		
	curl_setopt($ch, CURLOPT_URL,$url);		
	$result=curl_exec($ch);		
	curl_close($ch);		
	$finalresult = json_decode($result, true);
	
	$ii = 1;
	foreach($finalresult as $fr){
		$invoiceName = str_replace('/','_', $fr["number"]);
		ob_start();
		include('gd_invoice_template.php');
		$content = ob_get_clean();
		require_once('html2pdf/html2pdf.class.php');
		try
		{
			$html2pdf = new HTML2PDF('P', 'A4', 'en');
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
			$html2pdf->Output('invoices/'.$invoiceName.'_invoice.pdf','F');
			unset($invoiceName);
		}
		catch(HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
		$ii++;
	}
?>
