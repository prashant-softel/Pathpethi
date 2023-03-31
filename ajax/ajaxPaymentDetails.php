
<?php include_once("../classes/PaymentDetails.class.php");
include_once("../classes/include/dbop.class.php");
include_once("../classes/dbconst.class.php");
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
	$obj_PaymentDetails->actionType = 3;
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
	$PaymentVoucherNo = 0;
	echo $obj_PaymentDetails->UpdatePaymentDetails($PaidTo,$ChequeNumber,$ChequeDate,$Amount,$PayerBank,$Comments,$VoucherDate,$InvoiceDate,$TDSAmount,$LeafID,$DoubleEntry,$ExpenseBy,$CashDetailsId,$PaymentVoucherNo);
}

if($_REQUEST["method"]=="EditPaymentDetails")
{
	//echo "inside ajax file EditPaymentDetails";
	$obj_PaymentDetails->actionType = 3;
	$Detail1 = json_decode(str_replace('\\', '', $_REQUEST['data']), true);	
	
	$Detail = sortArray($Detail1);
	
	for($iCnt = 0 ; $iCnt < sizeof($Detail); $iCnt++)
	{
		$PaidTo=$Detail[$iCnt]['PaidTo'];
		$ChequeNumber=$Detail[$iCnt]['ChequeNumber'];
		$ChequeDate=$Detail[$iCnt]['ChequeDate'];
		$Amount=$Detail[$iCnt]['Amount'];
		$PayerBank=$Detail[$iCnt]['PayerBank'];
		$Comments=$Detail[$iCnt]['Comments'];
		$VoucherDate=$Detail[$iCnt]['VoucherDate'];
		$InvoiceDate=$Detail[$iCnt]['InvoiceDate'];
		$TDSAmount=$Detail[$iCnt]['TDSAmount'];
		$LeafID=$Detail[$iCnt]['LeafID'];
		$DoubleEntry = $Detail[$iCnt]["DoubleEntry"];
		$ExpenseBy=$Detail[$iCnt]['ExpenseBy'];
		$ExpenseBy=$Detail[$iCnt]['ExpenseBy'];
		$ModeOfPayment=$Detail[$iCnt]['ModeOfPayment'];
		$rowID =  $Detail[$iCnt]['RowID'];
		$reconcleDate = $Detail[$iCnt]['ReconcileDate'];
		$rStatus = $Detail[$iCnt]['ReconcileStatus'];
		$reconcile = $Detail[$iCnt]['Reconcile'];
		$returnFlag = $Detail[$iCnt]['ReturnFlag'];
		$MultipleEntry = $Detail[$iCnt]["MultipleEntry"];
		$Ref = $Detail[$iCnt]['Ref'];		
		$InvoiceAmount = $Detail[$iCnt]['InvoiceAmount'];
		echo $obj_PaymentDetails->UpdatePaymentDetails($PaidTo,$ChequeNumber,$ChequeDate,$Amount,$PayerBank,$Comments,$VoucherDate,$InvoiceDate,$TDSAmount,$LeafID,$DoubleEntry,$ExpenseBy, $rowID, $ModeOfPayment, $reconcleDate, $rStatus, $reconcile, $returnFlag, $MultipleEntry, $Ref,$InvoiceAmount );	
	}
}
if($_REQUEST["method"]=="delete")
{
	$obj_PaymentDetails->actionType = 2;
	$Data=$obj_PaymentDetails->m_dbConn->select("select * from `paymentdetails` where id=".$_REQUEST["PaymentDetailsId"]." ");
	$MultipleEntryData = array();
	
	if($Data[0]['Reference'] <> 0)
	{
		$MultipleEntryData = $obj_PaymentDetails->m_dbConn->select("SELECT * FROM `paymentdetails` WHERE `Reference` = '".$Data[0]['Reference']."'");		
	}
	if(sizeof($MultipleEntryData) > 0)
	{
		$prevRef = 0;
		for($i = 0; $i < sizeof($MultipleEntryData); $i++)
		{
			$obj_PaymentDetails->deletePaymentDetails($MultipleEntryData[$i]['ChequeDate'],$MultipleEntryData[$i]['ChequeNumber'],$MultipleEntryData[$i]['VoucherDate'],
				$MultipleEntryData[$i]['Amount'],$MultipleEntryData[$i]['PaidTo'],$MultipleEntryData[$i]['ExpenseBy'],$MultipleEntryData[$i]['PayerBank'],$MultipleEntryData[$i]['ChqLeafID'],
				$MultipleEntryData[$i]['Comments'],$MultipleEntryData[$i]['InvoiceDate'],$MultipleEntryData[$i]['TDSAmount'],$MultipleEntryData[$i]["id"],false,$MultipleEntryData[$i]['Reference'],$prevRef);
			$prevRef = $MultipleEntryData[$i]['Reference'];				
		}
	}
	else
	{
		$obj_PaymentDetails->deletePaymentDetails($Data[0]['ChequeDate'],$Data[0]['ChequeNumber'],$Data[0]['VoucherDate'],$Data[0]['Amount'],$Data[0]['PaidTo'],$Data[0]['ExpenseBy'],$Data[0]['PayerBank'],$Data[0]['ChqLeafID'],$Data[0]['Comments'],$Data[0]['InvoiceDate'],$Data[0]['TDSAmount'],$_REQUEST["PaymentDetailsId"]);	
	}		
}
if($_REQUEST['method'] == 'FetchVoucher')
{
	$sql = "SELECT `Reference` FROM `paymentdetails` WHERE `id` = '".$_REQUEST['pId']."'";
	$result = $obj_PaymentDetails->m_dbConn->select($sql);
	
	if($result[0]['Reference'] <> 0)
	{
		$selectQuery = 'SELECT bank.VoucherTypeID, vchr.VoucherNo FROM `bankregister` AS bank JOIN `voucher` AS vchr ON bank.VoucherID = vchr.id WHERE bank.ChkDetailID = "'.$result[0]['Reference'].'" AND vchr.RefTableID = 3';		
	}
	else
	{		
		$selectQuery = 'SELECT bank.VoucherTypeID, vchr.VoucherNo FROM `bankregister` AS bank JOIN `voucher` AS vchr ON bank.VoucherID = vchr.id WHERE bank.ChkDetailID = "'.$_REQUEST['pId'].'" AND vchr.RefTableID = 3';
	}
	$voucherDetails = $obj_PaymentDetails->m_dbConn->select($selectQuery);	
		
	echo base64_encode($voucherDetails[0]['VoucherNo']) . '#'. base64_encode($voucherDetails[0]['VoucherTypeID']);	
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'fetchLedgers')
{	
	$result = $obj_PaymentDetails->comboboxEx("select id,concat_ws(' - ', ledgertable.ledger_name,categorytbl.category_name)  from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where ledgertable.payment='1' and ledgertable.society_id=".$_SESSION['society_id']. " ORDER BY ledgertable.ledger_name ASC");
	echo $result;	
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'fetchExpenseBy')
{
	$PaidTo = $obj_PaymentDetails->comboboxEx("select id,concat_ws(' - ', ledgertable.ledger_name,categorytbl.category_name)  from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where ledgertable.expense='1' and ledgertable.society_id=".$_SESSION['society_id']." ORDER BY ledgertable.ledger_name ASC");	
	echo $PaidTo;
}


if($_REQUEST["mode"] == "Fill")
{
	$MultipleEntryArr = array();	
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
		if($_REQUEST["CustomLeaf"] == "0")
		{			
      		$sql1 = "select PaidTo,ExpenseBy, DATE_FORMAT(ChequeDate, '%d-%m-%Y') as ChequeDate,Amount,TDSAmount, DATE_FORMAT(InvoiceDate, '%d-%m-%Y') as InvoiceDate,ChequeNumber,Comments,ModeOfPayment,paymenttbl.id, DATE_FORMAT(VoucherDate, '%d-%m-%Y') as VoucherDate,`IsMultipleEntry`,`InvoiceAmount`,accounttbl.group_id from `paymentdetails` as paymenttbl join ledger as ledgertbl on ledgertbl.id = paymenttbl.PaidTo join `account_category` as accounttbl on accounttbl.category_id = ledgertbl.categoryid  where ChequeNumber='".$chqDetail[$iCnt]['cheque']."' and ChqLeafID='".$_REQUEST["LeafID"]."' ";			
		} 
		else
		{			
	  		$sql1 = "select PaidTo,ExpenseBy, DATE_FORMAT(ChequeDate, '%d-%m-%Y') as ChequeDate,Amount,TDSAmount, DATE_FORMAT(InvoiceDate, '%d-%m-%Y') as InvoiceDate,ChequeNumber,Comments,ModeOfPayment,paymenttbl.id, DATE_FORMAT(VoucherDate, '%d-%m-%Y') as VoucherDate,`IsMultipleEntry`,`InvoiceAmount`, accounttbl.group_id from `paymentdetails` as paymenttbl join ledger as ledgertbl on ledgertbl.id = paymenttbl.PaidTo join `account_category` as accounttbl on accounttbl.category_id = ledgertbl.categoryid where paymenttbl.id='".$chqDetail[$iCnt]['cheque']."' and ChqLeafID='".$_REQUEST["LeafID"]."' ";
		}
		
		if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
		{
			$sql1 .= "  and VoucherDate BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
		}
		
		$Data = $obj_PaymentDetails->m_dbConn->select($sql1);
		
		if($_REQUEST["CustomLeaf"] != "0")
		{
			$key = array_search($Data[0]['ChequeNumber'], $MultipleEntryArr);		
			if($key !== false)
			{			
				continue;				
			}
			else if($Data[0]['IsMultipleEntry'] == 1)
			{		
				array_push($MultipleEntryArr, $Data[0]['ChequeNumber']);
			}
			
			$refQuery = "select `Reference` from paymentdetails where ChqLeafID='".$_REQUEST["LeafID"]."' And `id` = '".$Data[0]['id']."'";
			$iReference = $obj_PaymentDetails->m_dbConn->select($refQuery);
			if($iReference[0]['Reference'] <> 0)
			{
				$sql2 = "select PaidTo,ExpenseBy, DATE_FORMAT(ChequeDate, '%d-%m-%Y') as ChequeDate,Amount,TDSAmount, DATE_FORMAT(InvoiceDate, '%d-%m-%Y') as InvoiceDate,ChequeNumber,Comments,ModeOfPayment,id,DATE_FORMAT(VoucherDate, '%d-%m-%Y') as VoucherDate,`IsMultipleEntry`,`InvoiceAmount` from `paymentdetails` as paymenttbl  where ChequeNumber='".$Data[0]['ChequeNumber']."' and ChqLeafID='".$_REQUEST["LeafID"]."' ";			
				$Data = $obj_PaymentDetails->m_dbConn->select($sql2);	
			}
		}		
		
		$reconcileStatus = $obj_PaymentDetails->m_dbConn->select("SELECT bank.ReconcileStatus, bank.Reconcile, bank.Return, DATE_FORMAT(bank.`Reconcile Date`, '%d-%m-%Y') as ReconcileDate FROM `bankregister` AS bank JOIN `voucher` AS voucher ON bank.VoucherID = voucher.id WHERE bank.ChkDetailID = '".$Data[0]['id']."' AND voucher.RefTableID = ". TABLE_PAYMENT_DETAILS);		
		
			 $sqlVoucher = "select v.Note,v.Date, ins.NewInvoiceNo as InvoiceNo,ins.InvoiceStatusID, ins.AmountReceived as InvoceAmount,ins.TDSAmount from `voucher` as v join `invoicestatus` as ins on v.VoucherNo=ins.InvoiceClearedVoucherNo where v.RefNo ='".$Data[0]['id']."' and `RefTableID` = '".TABLE_PAYMENT_DETAILS."' group by ins.InvoiceStatusID";
		 
		$data2 = $obj_PaymentDetails->m_dbConn->select($sqlVoucher);
		
		for($i = 0; $i < sizeof($Data); $i++)
		{			
			if(sizeof($reconcileStatus) > 0)
			{			
				$Data[$i]['ReconcileStatus'] = $reconcileStatus[0]['ReconcileStatus'];
				$Data[$i]['Reconcile'] = $reconcileStatus[0]['Reconcile'];		
				$Data[$i]['Return'] = $reconcileStatus[0]['Return'];
				$Data[$i]['ReconcileDate'] = $reconcileStatus[0]['ReconcileDate'];
			}
									
			if(sizeof($data2) > 0)
			{
				$inviceDetails = array();
				for($iCntRow=0; $iCntRow<sizeof($data2);$iCntRow++)
				{
					$InvoiceNo=$data2[$iCntRow]['InvoiceNo'];
					$InvoiceID=$data2[$iCntRow]['InvoiceStatusID'];
			
		 $sqlLegerName="SELECT ds.*, v.date,v.Note,l.ledger_name,l.id FROM `invoicestatus` as ds join `voucher` as v on ds.InvoiceRaisedVoucherNo=v.VoucherNo join ledger as l on v.By=l.id where InvoiceStatusID='".$InvoiceID."'";
		$data3 = $obj_PaymentDetails->m_dbConn->select($sqlLegerName);
		$data3[0]['date']=getDisplayFormatDate($data3[0]['date']);
				$data2[$iCntRow]['ExpenseDetails'] = $data3[0];
				}
				
				$Data[$i]['InvoiceData'] = json_encode($data2);
			}
		}
		
	  //print_r($Data);
		
		echo '^^' . $chqDetail[$iCnt]['no'] . '@@';
		foreach($Data as $k => $v)
		{
			echo '_//_';								
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
	$obj_PaymentDetails->actionType= 1;
	$PaidchqDetail1 = json_decode(str_replace('\\', '', $_REQUEST['data']), true);
	//print_r($PaidchqDetail);
	$PaidchqDetail = sortArray($PaidchqDetail1);

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
		$MultipleEntry = $PaidchqDetail[$iCnt]["MultipleEntry"];
		$Ref = $PaidchqDetail[$iCnt]['Ref'];
		$InvoiceAmount = $PaidchqDetail[$iCnt]['InvoiceAmount'];
		$strValues = $LeafID ."|". $SocietyID ."|". $PaidTo ."|". $CheqNumber ."|".  $ChequeDate ."|".  $Amount ."|". $PayerBank ."|". $Comments ."|". $VoucherDate ."|". $ExpenseBy ."|".  $DoubleEntry ."|". $InvoiceDate ."|". $TDSAmount ."|". $ModeOfPayment ."|" . $MultipleEntry."|" .$InvoiceAmount.'<br />';		
		$obj_PaymentDetails->AddNewValues($LeafID, $SocietyID, $PaidTo, $CheqNumber, $ChequeDate, $Amount, $PayerBank, $Comments, $VoucherDate, $ExpenseBy, $DoubleEntry,$InvoiceDate,$TDSAmount,$ModeOfPayment,0,0,0,0,$MultipleEntry,$Ref,0,$InvoiceAmount);		
	}
}
/*if($_REQUEST["method"]=="delete")
{
	$obj_PaymentDetails->deleting();
	return "Data Deleted Successfully";
}
*/



if($_REQUEST["method"]=="AddPaymentDetails")
{
	 $obj_PaymentDetails->actionType = 4;
	$Detail = json_decode($_REQUEST['data'], true);
	
	$obj_PaymentDetails->BeginTransaction();
	for($iCnt = 0 ; $iCnt < sizeof($Detail); $iCnt++)
	{   
		$PopupPayment=$Detail[$iCnt]['popupPayment'];
		$PaidTo=$Detail[$iCnt]['PaidTo'];
		$ChequeNumber=$Detail[$iCnt]['ChequeNumber'];
		$ChequeDate=$Detail[$iCnt]['ChequeDate'];
		$Amount=$Detail[$iCnt]['Amount'];
		$PayerBank=$Detail[$iCnt]['PayerBank'];
		$Comments=$Detail[$iCnt]['Comments'];
		$VoucherDate=$Detail[$iCnt]['VoucherDate'];
		$InvoiceDate=$Detail[$iCnt]['InvoiceDate'];
		$TDSAmount=0;
		$LeafID=$Detail[$iCnt]['LeafID'];
		$DoubleEntry = 0;
		//ExpenseBy=$Detail[$iCnt]['ExpenseBy'];
		$ExpenseBy=0;
		$ModeOfPayment=$Detail[$iCnt]['ModeOfPayment'];
		//echo "modeof payment".$ModeOfPayment;
		$rowID =0;
		$reconcleDate =$Detail[$iCnt]['reconcileDate'];
		$rStatus =$Detail[$iCnt]['recStatus'];
		$reconcile =$Detail[$iCnt]['reconcile'];
		$returnFlag = 0;
		$MultipleEntry =0;
		$Ref = 0;		
		$InvoiceAmount = $Detail[$iCnt]['InvoiceAmount'];
		$PaymentVoucherNo = 0;
		 //$obj_PaymentDetails->UpdatePaymentDetails($PaidTo,$ChequeNumber,$ChequeDate,$Amount,$PayerBank,$Comments,$VoucherDate,$InvoiceDate,$TDSAmount,$LeafID,$DoubleEntry,$ExpenseBy, $rowID, $ModeOfPayment, $reconcleDate, $rStatus, $reconcile, $returnFlag, $MultipleEntry, $Ref,$InvoiceAmount,$PaymentVoucherNo );
		
		if($PopupPayment==1)
	 	{
			$InvoicesData=$Detail[$iCnt]['Invoices'];
			$ClearVoucherNo=$Detail[$iCnt]['ClearVoucherNo'];
			$selectTDS="select ds.`TDSVoucherNo`,v.id from `invoicestatus` as ds join `voucher` as v on ds.TDSVoucherNo=v.VoucherNo where `InvoiceClearedVoucherNo`='".$ClearVoucherNo."'";
		
			$results1=$dbConn->select($selectTDS);
			//print_r($results1);
			if($results1 <> '')
			 	{ 
				 for($iVCount=0; $iVCount < sizeof($results1);$iVCount++)
				 {
					$UpdateQuery="delete  from `voucher` where VoucherNo='".$results1[$iVCount]['TDSVoucherNo']."'";
					$results2=$dbConn->delete($UpdateQuery);
			 		if($results1[$iVCount]['id'] > 0)
					{
				   		$UpdateQuery1="delete  from `liabilityregister` where VoucherID='".$results1[$iVCount]['id']."'";
						$results3=$dbConn->delete($UpdateQuery1);
					
						$UpdateQuery2="delete  from `expenseregister` where VoucherID='".$results1[$iVCount]['id']."'";
					 	$results3=$dbConn->delete($UpdateQuery2);
					 	$UpdateQuery3="delete  from `incomeregister` where VoucherID='".$results1[$iVCount]['id']."'";
					 	$results3=$dbConn->delete($UpdateQuery3);
					 	$UpdateQuery4="delete from `assetregister` where VoucherID='".$results1[$iVCount]['id']."'";
						$results3=$dbConn->delete($UpdateQuery4);
					}
				}
				
				}
			//die();
			
			 $UpdateQuery2="update `invoicestatus` set `InvoiceClearedVoucherNo`='',TDSVoucherNo='',AmountReceived='',TDSAmount='',TDSAmount='',CGST_Amount='',SGST_Amount='',CESS_Amount='' where `InvoiceClearedVoucherNo`='".$ClearVoucherNo."'";
			$results3=$dbConn->update($UpdateQuery2);
			$InvoicesData=json_decode($InvoicesData, true);
			
			for($iRow=0; $iRow < sizeof($InvoicesData);$iRow++) 
			{ 			
			$InvoiceDate = $InvoicesData[$iRow]["InvoiceDate"];
			$InvoiceNumber = $InvoicesData[$iRow]["InvoiceNumber"];
			$ExpenceBy = $InvoicesData[$iRow]["ExpenceBy"];
			$InvoiceAmount = $InvoicesData[$iRow]["InvoiceAmount"];
			$TDSAmount = $InvoicesData[$iRow]["TDSAmount"];
			$IGSTAmount = $InvoicesData[$iRow]["IGSTAmount"];
			$CGSTAmount = $InvoicesData[$iRow]["CGSTAmount"];
			$SGSTAmount = $InvoicesData[$iRow]["SGSTAmount"];
			$CESSAmount = $InvoicesData[$iRow]["CESSAmount"];
			$TDSPayable = $InvoicesData[$iRow]["TDSPayable"];
			$IsInvoice = $InvoicesData[$iRow]["IsInvoice"];
			//echo $TDSPayable;
			//die();
			$DocStatusID = $InvoicesData[$iRow]["InvoiceStatusID"];
			//echo $DocStatusID ;
			$NewInvoice = $InvoicesData[$iRow]["NewInvoice"];
			if($NewInvoice==1)
			{
				$obj_PaymentDetails->AddTDSDetailsEx($ExpenceBy, $PaidTo, $InvoiceDate, $InvoiceAmount, VOUCHER_JOURNAL, $Comments, $VoucherNo);
			 	
			  $DocumentStatus="Insert into `invoicestatus`(`NewInvoiceNo`,`InvoiceChequeAmount`,`InvoiceRaisedVoucherNo`,`AmountReceivable`,`TDSAmount`,`IGST_Amount`,`CGST_Amount`,`SGST_Amount`,`CESS_Amount`,`is_invoice`) values('".$InvoiceNumber."','".$InvoiceAmount."','".$VoucherNo."','".$InvoiceAmount."','".$TDSAmount."','".$IGSTAmount."','".$CGSTAmount."','".$SGSTAmount."','".$CESSAmount."','".$IsInvoice."')";
	$DocStatusID=$dbConn->insert($DocumentStatus);
	$InvoicesData[$iRow]["InvoiceStatusID"]=$DocStatusID;
			}
			//$obj_PaymentDetails->AddTDSDetails($PaidTo,$ChequeNumber,$ChequeDate,$Amount,$PayerBank,$Comments,$VoucherDate,$LeafID,$InvoiceDate,$InvoiceNumber,$ExpenceBy, $InvoiceAmount, $TDSAmount,$TDSVoucherNo);
			if($TDSAmount <> '' && $TDSAmount <> 0)
			{
			$obj_PaymentDetails->AddTDSDetailsEx($PaidTo, $TDSPayable, $InvoiceDate, $TDSAmount, VOUCHER_JOURNAL, $Comments, $VoucherNo);
			$InvoicesData[$iRow]['TDSVoucherNo'] =  $VoucherNo;
			}
			else
			{
				$InvoicesData[$iRow]['TDSVoucherNo'] =  0;
			}
		}
		
		$res =$obj_PaymentDetails->UpdatePaymentDetails($PaidTo,$ChequeNumber,$ChequeDate,$Amount,$PayerBank,$Comments,$VoucherDate,$InvoiceDate,$TDSAmount,$LeafID,$DoubleEntry,$ExpenseBy, $rowID, $ModeOfPayment, $reconcleDate, $rStatus, $reconcile, $returnFlag, $MultipleEntry, $Ref,$InvoiceAmount,$PaymentVoucherNo, true );
		for($iRow=0; $iRow < sizeof($InvoicesData);$iRow++) 
		{ 		
			
			$InvoiceNumber = $InvoicesData[$iRow]["InvoiceNumber"];
			$InvoiceAmount = $InvoicesData[$iRow]["InvoiceAmount"];
			$TDSAmount = $InvoicesData[$iRow]["TDSAmount"];
			$IGSTAmount = $InvoicesData[$iRow]["IGSTAmount"];
			$CGSTAmount = $InvoicesData[$iRow]["CGSTAmount"];
			$SGSTAmount = $InvoicesData[$iRow]["SGSTAmount"];
			$CESSAmount = $InvoicesData[$iRow]["CESSAmount"];
			$DocStatusID = $InvoicesData[$iRow]["InvoiceStatusID"];
			$VoucherNo = $InvoicesData[$iRow]["TDSVoucherNo"];
			$obj_PaymentDetails->UpdateInvoiceStatus($PaymentVoucherNo,$InvoiceNumber,$VoucherNo,$InvoiceAmount, $TDSAmount,$DocStatusID,$IGSTAmount,$CGSTAmount,$SGSTAmount,$CESSAmount);
		}
		
	//echo $res;
		if($res == "-2")  // cheque number already exist in another leaf
		{ 
			echo "0";
		}
		else
		{
			echo "1";
			$obj_PaymentDetails->CommitTransaction();
		}
		
	 }
	}
	
}	

function sortArray($array)
{
	foreach($array as $key=>$value){
        $arr_Ref[$key] = $array[$key]['Ref'];
        $arr_ME[$key] = $array[$key]['MultipleEntry'];
        }
	array_multisort($arr_Ref, SORT_ASC, $arr_ME, SORT_DESC,$array);	
	return $array;	
}
?>