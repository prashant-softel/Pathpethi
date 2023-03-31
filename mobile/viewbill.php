<?php
	header("Access-Control-Allow-Origin : *");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	header('Access-Control-Allow-Methods: GET, POST, PUT');
	
	$show_dtl = array(array("success"=>0), array("response"=>array("bill_for" => "October 2016 - December 2016", "bill_date" => '01-01-2016', "due_date" => '31-01-2016', "particulars" => array("Maintenance" => "1234.00", "Sinking Fund" => "500.00", "Lift Charges" => '200'), "sub_total" => '3400.00', "adjustment" => "0.00", "interest" => '50.00', "previous_principle" => '1000.00', "previous_interest" => '200.00', "previous_arrears" => '1200.00', 'balance' => '5500.00', "notes" => 'Test Notes')));
	echo json_encode($show_dtl);
?>