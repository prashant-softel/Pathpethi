<?php
	require_once("dompdf/dompdf_config.inc.php");
	
	if(isset($_REQUEST['data']) && isset($_REQUEST['society']) && isset($_REQUEST['period']) && isset($_REQUEST['BT']))
	{
		$html = $_REQUEST['data'];
		$html = str_replace('\"', '"', $html);
		$specialChars = array('/','.', '*', '%', '&', ',', '(', ')', '"');
//$unitNoForPdf  = str_replace($specialChars, ' ', $objFetchData->objMemeberDetails->sUnitNumber);
		$filename = str_replace($specialChars, '', $_REQUEST['filename']);
		//$filename =  $_REQUEST['filename'];
		/*$bill_dir = 'maintenance_bills';
		if (!file_exists($bill_dir)) 
		{
  			mkdir($bill_dir, 0777, true);
		}*/
		
		$bill_dir = 'maintenance_bills/' . $_REQUEST['society'];
		if (!file_exists($bill_dir)) 
		{
  			mkdir($bill_dir, 0777, true);
		}
		
		$bill_dir = 'maintenance_bills/' . $_REQUEST['society'] . "/" . $_REQUEST['period'];
		if (!file_exists($bill_dir)) 
		{
  			mkdir($bill_dir, 0777, true);
		}
	
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->render();
		
		/*To show SaveAs Dialog*/
		//$dompdf->stream($_REQUEST['file'] . '.pdf');
		
		/*To Save File on Server*/
		$output = $dompdf->output();
		$response = file_put_contents($bill_dir . '/' . $filename . '.pdf', $output);
		echo $response;
	}
	else  if(isset($_REQUEST['society']) && isset($_REQUEST['period']) && isset($_REQUEST['bDownload']) && $_REQUEST['bDownload'] == "1")
	 {
		$bill_dir = 'maintenance_bills/' . $_REQUEST['society'] . "/" . $_REQUEST['period'];
		$specialChars = array('/','.', '*', '%', '&', ',', '(', ')', '"');
		$filename = str_replace($specialChars, '', $_REQUEST['filename']);
		header("Content-Type: application/pdf");
		header("Content-Length: " . filesize($bill_dir . '/' . $filename. '.pdf'));
		header("Content-Disposition:attachment;filename=downloaded.pdf");
		readfile($bill_dir . '/' . $filename. '.pdf');
	}
?>