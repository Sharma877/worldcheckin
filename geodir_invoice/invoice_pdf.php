<?php
if(!empty($_GET['p']) && !empty($_GET['inv_id'])){
	$invoiceID = str_replace('-AA-','/',$_GET['inv_id']);
	$invoiceName = str_replace('-AA-','_',$_GET['inv_id']);
	
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
		
		if($fr['number'] == $invoiceID ){
			ob_start();
			include('gd_invoice_template.php');
			$content = ob_get_clean();
			require_once('html2pdf/html2pdf.class.php');
			try
			{
				$html2pdf = new HTML2PDF('P', 'A4', 'en');
				$html2pdf->pdf->SetDisplayMode('fullpage');
		//      $html2pdf->pdf->SetProtection(array('print'), 'spipu');
				$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
				
				$html2pdf->Output($invoiceName.'_invoice.pdf','D');
				$html2pdf->Output('invoices/'.$invoiceName.'_invoice.pdf','F');
			}
			catch(HTML2PDF_exception $e) {
				echo $e;
				exit;
			}
			$ii++;
		}
	}
	
	
}else{
	header('Location: ' . $_SERVER['HTTP_REFERER']);
}
