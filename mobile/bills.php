<?php
	header("Access-Control-Allow-Origin : *");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	header('Access-Control-Allow-Methods: GET, POST, PUT');
	
	$show_dtl = array(array("success"=>0), array("response"=>array(array("period_id" => 1, "bill_date" => "01-01-2016", "due_date" => "31-01-2016", "amount" => "1234.67"), array("period_id" => 2, "bill_date" => "01-02-2016", "due_date" => "29-02-2016", "amount" => "4227.34"))));
	echo json_encode($show_dtl);
?>