<?php
	header("Access-Control-Allow-Origin : *");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	header('Access-Control-Allow-Methods: GET, POST, PUT');
	
	$show_dtl = array(array("success"=>0), array("response"=>array("token" => "qwesdfsdkfs324jsjkddfgdgks", "name" => "Ankur Patil", "map" => array(array("id" => 1, "society" => "Raheja Heights G CHS (303)", "role" => "Member"), array("id" => 2, "society" => "Raheja Heights C CHS (303)", "role" => "Member")))));
	echo json_encode($show_dtl);
?>