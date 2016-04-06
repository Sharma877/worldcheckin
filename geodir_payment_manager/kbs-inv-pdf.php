<?php
	if(isset($_GET['cur_user_id']) && !empty($_GET['cur_user_id']) && isset($_GET['kbs_gdinvoice_id']) && !empty($_GET['kbs_gdinvoice_id'])){
		ob_start();
		include('kbs-inv-pdf-template.php');
		$content = ob_get_clean();
		require_once('html2pdf/html2pdf.class.php');
		try
		{
			$html2pdf = new HTML2PDF('P', 'A4', 'en');
			$html2pdf->pdf->SetDisplayMode('fullpage');
			//$html2pdf->pdf->SetProtection(array('print'), 'spipu');
			$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
			//$html2pdf->Output($_GET['kbs_gdinvoice_id'].'_invoice.pdf','D');
			$html2pdf->Output($_GET['kbs_gdinvoice_id'].'_invoice.pdf');
		}
		catch(HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}
?>
