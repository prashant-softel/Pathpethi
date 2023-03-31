
<?php include_once("../classes/PaymentDetails.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
$obj_PaymentDetails = new PaymentDetails($dbConn);

echo $_REQUEST["method"]."@@@";
if($_REQUEST["method"]=="edit")
{
	$select_type = $obj_PaymentDetails->selecting();

	foreach($select_type as $k => $v)
	{
		foreach($v as $kk => $vv)
		{
			echo $vv."#";
		}
	}
}

if($_REQUEST["method"]=="UpdateCashPaymentDetails")
{

	$Detail = json_decode(str_replace('\\', '', $_REQUEST['data']), true);
	//print_r($Detail);
	$CashDetailsId=$_REQUEST["CashDetailsId"];
	$PaidTo=$Detail[0]['PaidTo'];
	$ChequeNumber=$Detail[0]['ChequeNumber'];
	$ChequeDate=$Detail[0]['ChequeDate'];
	$Amount=$Detail[0]['Amount'];
	$PayerBank=$Detail[0]['PayerBank'];
	$Comments=$Detail[0]['Comments'];
	$VoucherDate=$Detail[0]['VoucherDate'];
	$InvoiceDate=$Detail[0]['InvoiceDate'];
	$TDSAmount=$Detail[0]['TDSAmount'];
	$LeafID=$Detail[0]['LeafID'];
	$DoubleEntry = $Detail[0]["DoubleEntry"];
	$ExpenseBy=$Detail[0]['ExpenseBy'];
	//$obj_PaymentDetails->UpdateCashPayment($VoucherDate, $ChequeDate, $ChequeNumber, $Amount, $PaidBy, $BankID, $PayerBank, $Comments,$CashDetailsId);
	$obj_PaymentDetails->UpdatePaymentDetails($PaidTo,$ChequeNumber,$ChequeDate,$Amount,$PayerBank,$Comments,$VoucherDate,$InvoiceDate,$TDSAmount,$LeafID,$DoubleEntry,$ExpenseBy,$CashDetailsId);
}

if($_REQUEST["method"]=="EditPaymentDetails")
{
	//echo "inside ajax file EditPaymentDetails";
	$Detail = json_decode(str_replace('\\', '', $_REQUEST['data']), true);
	$PaidTo=$Detail[0]['PaidTo'];
	$ChequeNumber=$Detail[0]['ChequeNumber'];
	$ChequeDate=$Detail[0]['ChequeDate'];
	$Amount=$Detail[0]['Amount'];
	$PayerBank=$Detail[0]['PayerBank'];
	$Comments=$Detail[0]['Comments'];
	$VoucherDate=$Detail[0]['VoucherDate'];
	$InvoiceDate=$Detail[0]['InvoiceDate'];
	$TDSAmount=$Detail[0]['TDSAmount'];
	$LeafID=$Detail[0]['LeafID'];
	$DoubleEntry = $Detail[0]["DoubleEntry"];
	$ExpenseBy=$Detail[0]['ExpenseBy'];
	$ExpenseBy=$Detail[0]['ExpenseBy'];
	$ModeOfPayment=$Detail[0]['ModeOfPayment'];
	//print_r($Detail);
	$obj_PaymentDetails->UpdatePaymentDetails($PaidTo,$ChequeNumber,$ChequeDate,$Amount,$PayerBank,$Comments,$VoucherDate,$InvoiceDate,$TDSAmount,$LeafID,$DoubleEntry,$ExpenseBy, $ModeOfPayment);	
}
if($_REQUEST["method"]=="delete")
{
	$Data=$obj_PaymentDetails->m_dbConn->select("select * from `paymentdetails` where id=".$_REQUEST["PaymentDetailsId"]." ");
	//print_r($Data);
	$obj_PaymentDetails->deletePaymentDetails($Data[0]['ChequeDate'],$Data[0]['ChequeNumber'],$Data[0]['VoucherDate'],$Data[0]['Amount'],$Data[0]['PaidTo'],$Data[0]['ExpenseBy'],$Data[0]['PayerBank'],$Data[0]['ChqLeafID'],$Data[0]['Comments'],$Data[0]['InvoiceDate'],$Data[0]['TDSAmount'],$_REQUEST["PaymentDetailsId"]);
	//return "Data Deleted Successfully";
	
}
if($_REQUEST["mode"] == "Fill")
{
	/*$Data = $obj_PaymentDetails->m_dbConn->select("select PaidTo,ExpenseBy,ChequeDate,Amount,Comments from paymentdetails where ChequeNumber=".$_REQUEST["ChequeNumber"]." and ChqLeafID=".$_REQUEST["LeafID"]);
	foreach($Data as $k => $v)
	{
		foreach($v as $kk => $vv)
		{
			echo $vv."#";
		}
	}*/
	//echo $_REQUEST['Cheque'];
	$chqDetail = json_decode(str_replace('\\', '', $_REQUEST['Cheque']), true);
	for($iCnt = 0 ; $iCnt < sizeof($chqDetail); $iCnt++)
	{
	//$Data = $obj_PaymentDetails->m_dbConn->select("select PaidTo,ExpenseBy, DATE_FORMAT(ChequeDate, '%d-%m-%Y'),Amount,Comments,ChequeNumber from paymentdetails where ChequeNumber=".$chqDetail[$iCnt]['cheque']." and ChqLeafID=".$_REQUEST["LeafID"]);
		
      $Data = $obj_PaymentDetails->m_dbConn->select("select PaidTo,ExpenseBy, DATE_FORMAT(ChequeDate, '%d-%m-%Y') as ChequeDate,Amount,TDSAmount, DATE_FORMAT(InvoiceDate, '%d-%m-%Y') as InvoiceDate,ChequeNumber,Comments from `paymentdetails` as paymenttbl  where ChequeNumber='".$chqDetail[$iCnt]['cheque']."' and ChqLeafID='".$_REQUEST["LeafID"]."'");
	  
	  //print_r($Data);
		
		echo '^^' . $chqDetail[$iCnt]['no'] . '@@';
		foreach($Data as $k => $v)
		{	
			foreach($v as $kk => $vv)
			{
				echo $vv."#";
			}
		}	
	}
}

/*if($_REQUEST["mode"] == "test")
{
	$chqDetail = json_decode($_REQUEST['Cheque'], true);
	for($iCnt = 0 ; $iCnt < sizeof($chqDetail); $iCnt++)
	{
		$Data = $obj_PaymentDetails->m_dbConn->select("select PaidTo,ExpenseBy,ChequeDate,Amount,Comments,ChequeNumber from paymentdetails where ChequeNumber=".$chqDetail[$iCnt]['cheque']." and ChqLeafID=".$_REQUEST["LeafID"]);
		
		echo '^^' . $chqDetail[$iCnt]['no'] . '##';
		foreach($Data as $k => $v)
		{	
			foreach($v as $kk => $vv)
			{
				echo $vv."#";
			}
		}	
	}
	$leafID = $_REQUEST['LeafID'];
	//echo $chqDetail;
}*/
if(isset($_REQUEST["update"]))
{
	$PaidchqDetail = json_decode(str_replace('\\', '', $_REQUEST['data']), true);
	//print_r($PaidchqDetail);
	for($iCnt = 0 ; $iCnt < sizeof($PaidchqDetail); $iCnt++)
	{
		//$Data = $obj_PaymentDetails->m_dbConn->select("select PaidTo,ExpenseBy,ChequeDate,Amount,Comments,ChequeNumber from paymentdetails where ChequeNumber=".$chqDetail[$iCnt]['cheque']." and ChqLeafID=".$_REQUEST["LeafID"]);
		
		//print_r($PaidchqDetail[$iCnt]);
		//print_r($PaidchqDetail[$iCnt]["PaidTo"]);
		//echo '^^' . $PaidchqDetail[$iCnt]['no'] . '##';
		/*foreach($Data as $k => $v)
		{	
			foreach($v as $kk => $vv)
			{
				echo $vv."#";
			}
		}	*/
		$LeafID = $PaidchqDetail[$iCnt]["LeafID"];
		$SocietyID = $PaidchqDetail[$iCnt]["SocietyID"];
		$PaidTo = $PaidchqDetail[$iCnt]["PaidTo"];
		$CheqNumber = $PaidchqDetail[$iCnt]["ChequeNumber"];
		$ChequeDate = $PaidchqDetail[$iCnt]["ChequeDate"];
		$Amount = $PaidchqDetail[$iCnt]["Amount"];
		$PayerBank = $PaidchqDetail[$iCnt]["PayerBank"];
		$Comments = $PaidchqDetail[$iCnt]["Comments"];
		$VoucherDate = $PaidchqDetail[$iCnt]["VoucherDate"];
		$ExpenseBy = $PaidchqDetail[$iCnt]["ExpenseBy"];
		$DoubleEntry = $PaidchqDetail[$iCnt]["DoubleEntry"];
		$InvoiceDate=$PaidchqDetail[$iCnt]["InvoiceDate"];
		$TDSAmount=$PaidchqDetail[$iCnt]["TDSAmount"];
		$ModeOfPayment=$PaidchqDetail[$iCnt]["ModeOfPayment"];
		$strValues = $LeafID ."|". $SocietyID ."|". $PaidTo ."|". $CheqNumber ."|".  $ChequeDate ."|".  $Amount ."|". $PayerBank ."|". $Comments ."|". $VoucherDate ."|". $ExpenseBy ."|".  $DoubleEntry ."|". $InvoiceDate ."|". $TDSAmount ."|". $ModeOfPayment;
		echo $strValues;
		$obj_PaymentDetails->AddNewValues($LeafID, $SocietyID, $PaidTo, $CheqNumber, $ChequeDate, $Amount, $PayerBank, $Comments, $VoucherDate, $ExpenseBy, $DoubleEntry,$InvoiceDate,$TDSAmount,$ModeOfPayment);
		
	}
}
/*if($_REQUEST["method"]=="delete")
{
	$obj_PaymentDetails->deleting();
	return "Data Deleted Successfully";
}
*/
?>