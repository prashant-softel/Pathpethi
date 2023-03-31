<?php
	header("Access-Control-Allow-Origin : *");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	header('Access-Control-Allow-Methods: GET, POST, PUT');
	
	$show_dtl = array(array("success"=>0), array("response"=>array("bill" => array("amount" => "1233.00", "bill_date" => "01-10-2016", "due_date" => "25-10-2016"), "payment" => array("amount" => "5678.00", "date" => '20-09-2016'), "due" => array("amount" => "1234.00"))));
	echo json_encode($show_dtl);
?>