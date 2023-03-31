<?php

include_once('../classes/pp_loan.class.php');
include_once('../classes/include/dpop.class.php');

$dbConn = new dbop();
$dbConnRoot = new dbop(true);

$obj_loan = new pp_loan($dbConn,$dbConnRoot);


if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'getBankLeafs')
{
   $result =  $obj_loan->getBankLeafs($_REQUEST['bank_id']);
   echo "@@@".$result;
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'getChequeNo')
{
   $result =  $obj_loan->getChequeNo($_REQUEST['leaf_id']);
   echo "@@@".$result;
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'getAccountNumber')
{
   $result =  $obj_loan->getAccountNumber($_REQUEST['member_id'], $_REQUEST['mortgage_type']);
   echo "@@@".json_encode($result);
}


?>
