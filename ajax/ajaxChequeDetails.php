<?php include_once("../classes/ChequeDetails.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_ChequeDetails = new ChequeDetails($dbConn);
//echo "<script>alert(testajax1);<//script>";
echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="edit")
{
	$select_type = $obj_ChequeDetails->selecting();

	foreach($select_type as $k => $v)
	{
		foreach($v as $kk => $vv)
		{
			echo $vv."#";
		}
	}
}
if(isset($_REQUEST["update"]))
{
	/*if($_POST['VoucherDate']<>"" && $_POST['ChequeDate']<>"" && $_POST['ChequeNumber']<>"" && $_POST['Amount']<>"" && $_POST['PaidBy']<>"" && $_POST['BankID']<>"" && $_POST['PayerBank']<>"" && $_POST['DepositID']<>"")
	{
		$obj_ChequeDetails->AddNewValues($_POST['VoucherDate'], $_POST['ChequeDate'], $_POST['ChequeNumber'], $_POST['Amount'], $_POST['PaidBy'], $_POST['BankID'], $_POST['PayerBank'], $_POST['PayerChequeBranch'], $_POST['DepositID'], $_POST['Comments']);
	}*/
	$obj_ChequeDetails->actionType = 1;
	$chqDetail = json_decode(str_replace('\\', '', $_REQUEST['data']), true);
	//print_r($PaidchqDetail);
	for($iCnt = 0 ; $iCnt < sizeof($chqDetail); $iCnt++)
	{
		$PaidBy = $chqDetail[$iCnt]["PaidBy"];
		$ChequeNumber = $chqDetail[$iCnt]["ChequeNumber"];
		$ChequeDate = $chqDetail[$iCnt]["ChequeDate"];
		$Amount = $chqDetail[$iCnt]["Amount"];
		$PayerBank = $chqDetail[$iCnt]["PayerBank"];
		$PayerChequeBranch = $chqDetail[$iCnt]["PayerChequeBranch"];
		$VoucherDate = $chqDetail[$iCnt]["VoucherDate"];
		$BankID = $chqDetail[$iCnt]["BankID"];
		$DepositID = $chqDetail[$iCnt]["DepositID"];
		$Comments = $chqDetail[$iCnt]["Comments"];
		$BillType = $chqDetail[$iCnt]["BillType"];
		
		$obj_ChequeDetails->AddNewValues($VoucherDate, $ChequeDate, $ChequeNumber, $Amount, $PaidBy, $BankID, $PayerBank, $PayerChequeBranch, $DepositID, $Comments,$BillType);
		
	}
}
else if(isset($_REQUEST["updateneft"]))
{
	$obj_ChequeDetails->actionType = 1;
	/*if($_POST['VoucherDate']<>"" && $_POST['ChequeDate']<>"" && $_POST['ChequeNumber']<>"" && $_POST['Amount']<>"" && $_POST['PaidBy']<>"" && $_POST['BankID']<>"" && $_POST['PayerBank']<>"" && $_POST['DepositID']<>"")
	{
		$obj_ChequeDetails->AddNewValues($_POST['VoucherDate'], $_POST['ChequeDate'], $_POST['ChequeNumber'], $_POST['Amount'], $_POST['PaidBy'], $_POST['BankID'], $_POST['PayerBank'], $_POST['PayerChequeBranch'], $_POST['DepositID'], $_POST['Comments']);
	}*/
	
	$chqDetail = json_decode(str_replace('\\', '', $_REQUEST['data']), true);
	
	//print_r($PaidchqDetail);
	for($iCnt = 0 ; $iCnt < sizeof($chqDetail); $iCnt++)
	{
		$PaidBy = $chqDetail[$iCnt]["PaidBy"];
		$ChequeNumber = $chqDetail[$iCnt]["ChequeNumber"];
		$ChequeDate = $chqDetail[$iCnt]["ChequeDate"];
		$Amount = $chqDetail[$iCnt]["Amount"];
		$PayerBank = $chqDetail[$iCnt]["PayerBank"];
		$PayerChequeBranch = $chqDetail[$iCnt]["PayerChequeBranch"];
		$VoucherDate = $chqDetail[$iCnt]["VoucherDate"];
		$BankID = $chqDetail[$iCnt]["BankID"];
		$DepositID = $chqDetail[$iCnt]["DepositID"];
		$Comments = $chqDetail[$iCnt]["Comments"];
		$BillType = $chqDetail[$iCnt]["BillType"];
		
		$obj_ChequeDetails->AddNewValues($VoucherDate, $ChequeDate, $ChequeNumber, $Amount, $PaidBy, $BankID, $PayerBank, $PayerChequeBranch, $DepositID, $Comments,$BillType);
		
	}
}

else if(isset($_REQUEST['getBankDetails']))
{
	$get_bankDetails = $obj_ChequeDetails->getPayerBankDetails($_REQUEST['PaidBy']);
	echo $get_bankDetails[0]['Payer_Bank'] . "@@@" . $get_bankDetails[0]['Payer_Cheque_Branch'] . "@@@" . $_REQUEST['Counter'];
}

if($_REQUEST["method"]=="updateChequeDetails")
{
	$obj_ChequeDetails->actionType = 3;
	$chqDetail = json_decode(str_replace('\\', '', $_REQUEST['data']), true);
	//echo "ajaxfile";
//print_r($chqDetail);
	//echo "sizeof($chqDetail)".sizeof($chqDetail);
	$ChequeDetailsId=$_REQUEST["ChequeDetailsId"];
	$PaidBy = $chqDetail[0]["PaidBy"];
	$ChequeNumber = $chqDetail[0]["ChequeNumber"];
	$ChequeDate = $chqDetail[0]["ChequeDate"];
	$Amount = $chqDetail[0]["Amount"];
	$PayerBank = $chqDetail[0]["PayerBank"];
	$PayerChequeBranch = $chqDetail[0]["PayerChequeBranch"];
	$VoucherDate = $chqDetail[0]["VoucherDate"];
	$BankID = $chqDetail[0]["BankID"];
	$DepositID = $chqDetail[0]["DepositID"];
	$Comments = $chqDetail[0]["Comments"];
	$BillType = $chqDetail[0]["BillType"];
	$obj_ChequeDetails->UpdateCheque($VoucherDate, $ChequeDate, $ChequeNumber, $Amount, $PaidBy, $BankID, $PayerBank, $PayerChequeBranch, $DepositID, $Comments,$ChequeDetailsId,$BillType);
}


if($_REQUEST["method"]=="delete")
{
	$obj_ChequeDetails->actionType = 2;
	//$obj_ChequeDetails->deleting();
	//return "Data Deleted Successfully";
	$select_type = $obj_ChequeDetails->selecting();
	//print_r($select_type);
	$PaidBy=$select_type[0]['PaidBy'];
	$PayerBank=$select_type[0]['PayerBank'];
	$PayerChequeBranch=$select_type[0]['PayerChequeBranch'];
	$ChequeDetailsId=$select_type[0]['ID'];
	
	$paymentIDs = $obj_ChequeDetails->deleteReturnChequeEntry($PaidBy);	
	foreach($paymentIDs as $k => $v)
	{
		foreach($v as $kk => $vv)
		{
			echo $vv.",";
		}
	}
	
	$obj_ChequeDetails->DeletePreviousRecord($PaidBy, $PayerBank, $PayerChequeBranch,$ChequeDetailsId);
}

if($_REQUEST['method'] == 'FetchVoucher')
{
	$selectQuery = 'SELECT bank.VoucherTypeID, vchr.VoucherNo FROM `bankregister` AS bank JOIN `voucher` AS vchr ON bank.VoucherID = vchr.id WHERE bank.ChkDetailID = "'.$_REQUEST['chqId'].'" AND vchr.RefTableID = 2';
	$voucherDetails = $obj_ChequeDetails->m_dbConn->select($selectQuery);	
		
	echo base64_encode($voucherDetails[0]['VoucherNo']) . '#'. base64_encode($voucherDetails[0]['VoucherTypeID']);	
}

?>