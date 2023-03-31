<?php
	header("Access-Control-Allow-Origin : *");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	header('Access-Control-Allow-Methods: GET, POST, PUT');
	
	$show_dtl = array(array("success"=>0), array("response"=>array(array("period_id" => 1, "date" => "01-01-2016", "amount" => "1234.67", "mode" => "Cheque"), array("period_id" => 2, "date" => "01-02-2016", "amount" => "4227.34", "mode" => "NEFT"))));
	echo json_encode($show_dtl);
?>