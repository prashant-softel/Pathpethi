<?php
	require_once("dompdf/dompdf_config.inc.php");
	include_once("PDFMerger/PDFMerger.php");
	
	if(isset($_REQUEST['data']) && isset($_REQUEST['society']))
	{
		$html = $_REQUEST['data'];
		$html = str_replace('\"', '"', $html);
		$filename = $_REQUEST['filename'];
		
		/*$bill_dir = 'maintenance_bills';
		if (!file_exists($bill_dir)) 
		{
  			mkdir($bill_dir, 0777, true);
		}*/
		
		$bill_dir = 'Reports/'. $_REQUEST['society'];
		if (!file_exists($bill_dir)) 
		{
  			mkdir($bill_dir, 0777, true);
		}
		
		$bill_dir = 'Reports/'. $_REQUEST['society'];
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
	else if(isset($_REQUEST['society']) && isset($_REQUEST['merge']) && isset($_REQUEST['filename']))
	{
		include_once ("classes/include/dbop.class.php");
		$dbConn = new dbop();
	
		try
		{
			$pdf = new PDFMerger;
			
			$bill_dir = 'Reports/'. $_REQUEST['society'] . '/';
			
			$combine_file = 'Reports/'. $_REQUEST['society'] . '/' . $_REQUEST['filename'] . '.pdf';
			if (file_exists($combine_file)) 
			{
				try
				{
					unlink($combine_file);
				}
				catch(Exception $exp)
				{
					//echo '<br/>Exception : ' . $exp;
				}
			}
			
			$sqlUnit = "select unittbl.`unit_no` from `unit` as unittbl ORDER BY unittbl.`sort_order` ASC";
			$result = $dbConn->select($sqlUnit);
			//print_r($result);
			for($iCnt = 0; $iCnt < sizeof($result); $iCnt++)
			{
				$unitNo = $result[$iCnt]['unit_no'];
				$pdfFileName = 'MemberLedgerReport-' . $_REQUEST['society'] . "-" . $unitNo . '.pdf';
				//echo $pdfFileName;
				if(file_exists($bill_dir.$pdfFileName))
				{
					//echo '<br/>Add : ' . $bill_dir.$pdfFileName;
					$pdf->addPDF($bill_dir.$pdfFileName, 'all');
				}
			}
			//echo '<br>Combine File Name : ' . $combine_file;
			$pdf->merge('file', $combine_file);
		}
		catch(Exception $exp)
		{
			//echo "<br/>Merge Exception : " . $exp;
		}
		
		if(file_exists($combine_file))
		{
			echo 1;
		}
		else
		{
			echo 0;
		}
	}
?>