<?php
	require_once("dompdf/dompdf_config.inc.php");
	
	if(isset($_REQUEST['data']))
	{
		$html = $_REQUEST['data'];
		$html = str_replace('\"', '"', $html);
		$filename = "Download";
			
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		if($_REQUEST['landscape']==1)
		{
		$dompdf->set_paper('A4', 'landscape');
		}
		
		$dompdf->render();
		/*To show SaveAs Dialog*/
		

		echo $dompdf->stream($filename. '.pdf');
	}
?>