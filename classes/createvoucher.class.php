<?php
//include_once("include/dbop.class.php");

include_once("register.class.php");
include_once("dbconst.class.php");
include_once("latestcount.class.php");
include_once("utility.class.php");
include_once("voucher.class.php");
include_once("changelog.class.php");

class createVoucher 
{
	public $actionPage = "../createvoucher.php";
	public $m_dbConn;
	public $m_voucher;
	public $m_register;
	public $m_latestcount;
	public $m_objUtility;
	public $changeLog;

	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->m_voucher = new voucher($dbConn);
		$this->m_latestcount = new latestCount($dbConn);
		$this->m_register = new regiser($dbConn);
		$this->m_objUtility = new utility($dbConn);
		$this->changeLog = new changeLog($dbConn);
	}			
	
	public function startProcess()
	{
		
		$msg='';
		$PreviousMsg="";
		$UpdateInvoice=false;
		if(isset($_POST['submit']) && $_POST['submit']== 'Update')
	  	{
			$UpdateInvoice=true;
			//print_r($_POST);
			//die();
			if($_POST['page']=='Liability')
			{
			$this->actionPage="../LiabilitySummary.php";
			}
			
			if($_POST['page']=='Income')
			{
			$this->actionPage="../IncomeDetails.php";
			}
			
			if($_POST['page']=='Expense')
			{
			$this->actionPage="../ExpenseDetails.php";
			}
			
			
			if(isset($_POST['Vno']) && $_POST['Vno'] <> '')
		  	{
				$PreviousData=$this->FetchData($_POST['Vno']);
			
			   if($PreviousData <> '')
			   {
				//print_r($PreviousData);
				foreach($PreviousData as $k=>$v)
				{
					//echo '1';
					if($PreviousData[$k]['By'] <> '')
					{
						//echo '2';
						$sql="select  changelogtbl.ChangedLogDec from `voucher` as vouchertbl JOIN `change_log` as changelogtbl on changelogtbl.ChangeLogID=vouchertbl.LatestChangeID where VoucherNo='".$_POST['Vno']."' and `By`=".$PreviousData[$k]['By']." ";
						//echo $sql;
						$data=$this->m_dbConn->select($sql);
						 
						$PreviousMsg=$data[0]['ChangedLogDec'];
						$msg=$this->DeletePreviousRecords($PreviousData[$k]['By'],$_POST['Vno'],$PreviousData[$k]['Debit'],$PreviousData[$k]['Date'],$PreviousData[$k]['id'],'By');
						
						
					}
					else
					{
						//echo '3';
						$msg.=$this->DeletePreviousRecords($PreviousData[$k]['To'],$_POST['Vno'],$PreviousData[$k]['Credit'],$PreviousData[$k]['Date'],$PreviousData[$k]['id'],'To');  
					}
				}
				
		    }	
		  	$iLatestChangeID = $this->changeLog->setLog($msg, $_SESSION['login_id'], 'VOUCHER', '--');
	   }
	   
	 }	 
	 $sqlVoucher="Select InvoiceStatusID from `invoicestatus` where TDSVoucherNo='".$_POST['Vno']."'"; 	
	 $data1=$this->m_dbConn->select($sqlVoucher);
	 $TDSVNo=0;
	// if($UpdateInvoice==1)  
		if( $data1<> '')
		{
			 $TDSVNo= $data1[0]['InvoiceStatusID'];
		}
		
		$IsSubmit = $_POST['submit'];
		$VoucherDate = $_POST['voucher_date'];
		$maxrows = $_POST['maxrows'];
		$arData = array();
		/*$byto =	array();
		$To = array();
		$Debit = array();
		$Credit = array();*/
		for($i=1;$i<=$_POST['maxrows'];$i++)
		{
			$arSubData = array();
			$arSubData['byto'] = $_POST['byto'.$i];
			$arSubData['To'] = $_POST['To'.$i];
			$arSubData['Debit'] = $_POST['Debit'.$i];
			$arSubData['Credit'] = $_POST['Credit'.$i];
			$arData[$i-1] = $arSubData;  
		}
			/*echo "<pre>";
			print_r($arData);
			echo "</pre>";*/
			//die();
		$is_invoice = $_POST['is_invoice'];
		$IGST_Amount = $_POST['igst_amount'];
		$CGST_Amount = $_POST['cgst_amount'];
		$SGST_Amount = $_POST['sgst_amount'];
		$Cess_Amount = $_POST['cess_amount'];
		$NewInvoiceNo = $_POST['invoice_no'];
		$InvoiceStatusID = $_POST['InvoiceStatusID'];
		$Note = $_POST['Note'];
		
	  	$Result = $this->createNewVoucher($PreviousMsg,$UpdateInvoice,$TDSVNo,$IsSubmit,$VoucherDate,$arData,$is_invoice,$IGST_Amount,$CGST_Amount,$SGST_Amount,$Cess_Amount,$NewInvoiceNo,$InvoiceStatusID,$Note);
		//print_r($Result);
		//die();
		
		//if($_POST['is_invoice']> 0)
	  // {
		   //$selectID="select *from voucher" vhere
		  // $updateQuery="update `invoicestatus` set InvoiceChequeAmount='".$_POST['Debit']."', AmountReceivable='".$_POST['Debit']."',IGST_Amount='".$_POST['igst_amount']."',CGST_Amount='".$_POST['cgst_amount']."',SGST_Amount='".$_POST['sgst_amount']."',CESS_Amount='".$_POST['cess_amount']."' where InvoiceStatusID='".$_POST['InvoiceStatusID']."' and NewInvoiceNo='".$_POST['invoice_no']."'";
	    //$updateQuery="update `invoicestatus` set InvoiceChequeAmount='".$_POST['Debit']."', AmountReceivable='".$_POST['Debit']."',IGST_Amount='".$_POST['igst_amount']."',CGST_Amount='".$_POST['cgst_amount']."',SGST_Amount='".$_POST['sgst_amount']."',CESS_Amount='".$_POST['cess_amount']."' where InvoiceStatusID='".$_POST['InvoiceStatusID']."' and NewInvoiceNo='".$_POST['invoice_no']."'";
		//$res=$this->m_dbConn->update($updateQuery);
	  // }
	  // else
	  // {
	  // $deleteData="Delete * from `invoicestatus` where InvoiceStatusID='".$_POST['InvoiceStatusID']."' and NewInvoiceNo='".$_POST['invoice_no']."' ";		$res1=$this->m_dbConn->delete($deleteData);
	   //}
		return $Result;
 }		
	
	public function combobox($query, $id, $bShowAll = false)
	{
		if($bShowAll == true)
		{
			$str.="<option value=''>All</option>";
		}
		else
		{
			$str.="<option value='0'>Please Select</option>";
		}
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			$vowels = array('/', '-', '.', '*', '%', '&', ',', '"');
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($id==$v)
						{
							$sel = 'selected';	
						}
						else
						{
							$sel = '';
						}
						
						$str.="<OPTION VALUE=".$v.' '.$sel.">";
					}
					else
					{
						$str.= str_replace($vowels, ' ', $v)."</OPTION>";
					}
					$i++;
				}
			}
		}
			return $str;
	}
	
	public function createNewVoucher($PreviousMsg,$UpdateInvoice,$invoicestatusID,$IsSubmit,$VoucherDate,$arData,$is_invoice,$IGST_Amount,$CGST_Amount,$SGST_Amount,$Cess_Amount,$NewInvoiceNo,$InvoiceStatusID,$Note)
	{
		$dataVoucher1=0;
		$Createmsg="";
		//echo"msg:".$PreviousMsg ." update1 ".$UpdateInvoice." update2 ".$invoicestatusID." update3 ".$IsSubmit." update4 ".$VoucherDate." update5 ".$is_invoice;
		
		/*echo "in createnewvoucher<br>";
		echo "<pre>";
		print_r($arData);
		echo "</pre>";*/
		
							
		if(isset($IsSubmit) && isset($VoucherDate) && $VoucherDate <> "")
		{ 				
			$SrNo=0;
			$total=0;
			$TDSAMount=0;
			$LatestVoucherNo = $this->m_latestcount->getLatestVoucherNo($_SESSION['society_id']);	
			try
			{
				$this->m_dbConn->begin_transaction();
				for($i=0;$i<sizeof($arData);$i++)
				{
					$SrNo++;
					//echo "byto1: ".$arData[$i]['byto']."<br>";
					//echo "To1: ".$arData[$i]['To']."<br>";
										
					if(isset($arData[$i]['byto']) && strtoupper($arData[$i]['byto'])=="BY")
					{
						//echo "byto2: ".$arData[$i]['byto']."<br>";
						//echo "To2: ".$arData[$i]['To']."<br>";

						if(isset($arData[$i]['To']) && $arData[$i]['To'] <> '0' && $arData[$i]['Debit'] <> 0 && $arData[$i]['Debit'] <> '')
						{	
							$LedgerName=$this->m_objUtility->getLedgerName($arData[$i]['To']);
							$Createmsg="Amount from ".$LedgerName." ".$arData[$i]['Debit']." debit voucher created.";
							$iLatestChangeID = $this->changeLog->setLog($Createmsg, $_SESSION['login_id'], 'VOUCHER', '--');	
							$dataVoucher1 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate),0,0,
$LatestVoucherNo,$SrNo,VOUCHER_JOURNAL,$arData[$i]['To'],TRANSACTION_DEBIT,$arData[$i]['Debit'],$Note);
							$arByParentDetails = $this->m_objUtility->getParentOfLedger($arData[$i]['To']);
					
							if(!(empty($arByParentDetails)))
							{
								$ByGroupID = $arByParentDetails['group'];
								$ByCategoryID = $arByParentDetails['category'];		
						
								if($ByGroupID==LIABILITY)
								{	
									//echo 'SetLiabilityRegister';
									$regResult1 = $this->m_register->SetLiabilityRegister(getDBFormatDate($VoucherDate),$arData[$i]['To'],$dataVoucher1,VOUCHER_JOURNAL, TRANSACTION_DEBIT, $arData[$i]['Debit'],0,$iLatestChangeID);	
						
								}
					
								if($ByGroupID==ASSET)
								{
									//echo 'SetAssetRegister';
									$regResult2 = $this->m_register->SetAssetRegister(getDBFormatDate($VoucherDate),$arData[$i]['To'], $dataVoucher1, VOUCHER_JOURNAL, TRANSACTION_DEBIT, $arData[$i]['Debit'],0,$iLatestChangeID);	
								}
					
								if($ByGroupID==INCOME)
								{
									//echo 'SetIncomeRegister';
									$regResult3 = $this->m_register->SetIncomeRegister($arData[$i]['To'], getDBFormatDate($VoucherDate), $dataVoucher1, VOUCHER_JOURNAL, TRANSACTION_DEBIT, $arData[$i]['Debit'],$iLatestChangeID);
								}
					
								if($ByGroupID==EXPENSE)
								{
									//echo 'SetExpenseRegister';
									$regResult4 = $this->m_register->SetExpenseRegister($arData[$i]['To'],getDBFormatDate($VoucherDate), $dataVoucher1, VOUCHER_JOURNAL, TRANSACTION_DEBIT,$arData[$i]['Debit'],0,$iLatestChangeID);
								}
							}			
						}			
					}
					else if( isset($arData[$i]['byto']) && strtoupper($arData[$i]['byto'])=="TO" )
					{					
						if(isset($arData[$i]['To']) && $arData[$i]['To'] <> '0' && $arData[$i]['Credit'] <> 0 && $arData[$i]['Credit'] <> '')	
						{
							$TDSAMount=$arData[$i]['Credit'];
							$LedgerName=$this->m_objUtility->getLedgerName($arData[$i]['To']);
							$Createmsg.=$LedgerName." amount ".$arData[$i]['Credit']."credit voucher created.";
							//$iLatestChangeID = $this->changeLog->setLog($Createmsg, $_SESSION['login_id'], 'VOUCHER', '--');	
							$dataVoucher2 = $this->m_voucher->SetVoucherDetails(getDBFormatDate($VoucherDate),0,0,
							$LatestVoucherNo,$SrNo,VOUCHER_JOURNAL,$arData[$i]['To'],TRANSACTION_CREDIT,$arData[$i]['Credit'],$Note);
							//echo 'settovoucher';
							$arToParentDetails = $this->m_objUtility->getParentOfLedger($arData[$i]['To']);
		
							if(!(empty($arToParentDetails)))
							{
								$ToGroupID = $arToParentDetails['group'];
								$ToCategoryID = $arToParentDetails['category'];		
							
								if($ToGroupID==LIABILITY)
								{
									//echo 'SetLiabilityRegister';
									$regResult1 = $this->m_register->SetLiabilityRegister(getDBFormatDate($VoucherDate),$arData[$i]['To'],$dataVoucher2, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $arData[$i]['Credit'], 0,$iLatestChangeID);	
						
								}
					
								if($ToGroupID==ASSET)
								{
								
									//echo 'SetAssetRegister';
									$regResult2 = $this->m_register->SetAssetRegister(getDBFormatDate($VoucherDate), $arData[$i]['To'], $dataVoucher2, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $arData[$i]['Credit'], 0,$iLatestChangeID);	
								}
					
								if($ToGroupID==INCOME)
								{
									//echo 'SetIncomeRegister';
									$regResult3 = $this->m_register->SetIncomeRegister($arData[$i]['To'], getDBFormatDate($VoucherDate), $dataVoucher2, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $arData[$i]['Credit'],$iLatestChangeID);
								}
			
								if($ToGroupID==EXPENSE)
								{
						
									//echo 'SetExpenseRegister';
									$regResult4 = $this->m_register->SetExpenseRegister($arData[$i]['To'],getDBFormatDate($VoucherDate), $dataVoucher2, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $arData[$i]['Credit'],0,$iLatestChangeID);
						
								}					
							}						
						}
					}
					$total+=$arData[$i]['Credit'];
					//print_r($total);
				}
				if($UpdateInvoice==true)
				{		
					if($is_invoice > 0)
					{
					$updateQuery="update `invoicestatus` set InvoiceChequeAmount='".$total."',InvoiceRaisedVoucherNo='".$LatestVoucherNo."', AmountReceivable='".$total."',IGST_Amount='".$IGST_Amount."',CGST_Amount='".$CGST_Amount."',SGST_Amount='".$SGST_Amount."',CESS_Amount='".$Cess_Amount."',NewInvoiceNo='".$NewInvoiceNo."' where InvoiceStatusID='".$InvoiceStatusID."'";
					$res1=$this->m_dbConn->update($updateQuery);
					}
					else
					{
						if($InvoiceStatusID <> '' && $NewInvoiceNo <> '')
						{
							$deleteData="Delete  from `invoicestatus` where InvoiceStatusID='".$InvoiceStatusID."' and NewInvoiceNo='".$NewInvoiceNo."'";
							$res1=$this->m_dbConn->delete($deleteData);
						}
					}
				}
				else
				{
					if(isset($is_invoice) && $is_invoice==1)
		 			{
						$DocumentStatus="Insert into `invoicestatus`(`NewInvoiceNo`,`InvoiceChequeAmount`,`InvoiceRaisedVoucherNo`,`AmountReceivable`,IGST_Amount,CGST_Amount,SGST_Amount,CESS_Amount) values('".$NewInvoiceNo."','".$total."','".$LatestVoucherNo."','".$total."','".$IGST_Amount."','".$CGST_Amount."','".$SGST_Amount."','".$Cess_Amount."')";
						$res=$this->m_dbConn->insert($DocumentStatus);
					}
				}
				if($invoicestatusID > 0)
				{
					$updateQuery="update `invoicestatus` set TDSVoucherNo='".$LatestVoucherNo."', TDSAmount='".$TDSAMount."' where InvoiceStatusID='".$invoicestatusID."'";
					$res1=$this->m_dbConn->update($updateQuery);	
				}
		//if($TDSAMount<400)
			//{
				//throw new Exception("value entered should greater than 400");
			//}
			}
			catch( Exception $exp)
			{
				echo "message:".$exp->getMessage();
				$this->m_dbConn->rollback();
			}
			if($PreviousMsg <> '')
			{
				$ChangeMsg='Old Record:'.$PreviousMsg.'New Record:'.$Createmsg;
			}
			else
			{
				$ChangeMsg=$Createmsg;		
			}
	
			$iLatestChangeID = $this->changeLog->setLog($ChangeMsg, $_SESSION['login_id'], 'VOUCHER', '--');
			$sql09="Update `voucher` set LatestChangeID='".$iLatestChangeID."' where id='".$dataVoucher1."'";
			//echo $sql09;
			$this->m_dbConn->commit();
			$res=$this->m_dbConn->update($sql09);
			return 'Update';		
		}
		else
		{
			return 'Record Not Updated';	
		}
	}
	
public function FetchData($VoucherNo)
{
	 $sqlfetch="select `id`,DATE_FORMAT(Date, '%d-%m-%Y') as Date,`RefNo`,`RefTableID`,`VoucherNo`,`SrNo`,`VoucherTypeID`,`By`,`To`,`Debit`,`Credit`,`Note`,`LatestChangeID`,`Timestamp` from `voucher`  where VoucherNo='".$VoucherNo."'  ";
	//echo $sqlfetch;
	$result=$this->m_dbConn->select($sqlfetch);
if($result <> '')
	{
		$sqlselect="select * from `invoicestatus` where InvoiceRaisedVoucherNo='".$result[0]['VoucherNo']."'";
		$result1=$this->m_dbConn->select($sqlselect);
		
	 	$result[0]['InvoiceStatusID']=$result1[0]['InvoiceStatusID'];
		$result[0]['NewInvoiceNo']=$result1[0]['NewInvoiceNo'];
	  	$result[0]['IGST_Amount']=$result1[0]['IGST_Amount'];
	  	$result[0]['CGST_Amount']=$result1[0]['CGST_Amount'];
	    $result[0]['SGST_Amount']=$result1[0]['SGST_Amount'];
		$result[0]['CESS_Amount']=$result1[0]['CESS_Amount'];
		$result[0]['is_invoice']=$result1[0]['is_invoice'];
	}
	
	
	return $result;
	
	
	
}	

public function Totalrows($VoucherNo)
{
	$sqlfetch2="select count(*) as cnt from `voucher` where VoucherNo='".$VoucherNo."' ";
	//echo $sqlfetch2;
	$result2=$this->m_dbConn->select($sqlfetch2);
	
	return $result2[0]['cnt'];
	
}


public function DeletePreviousRecords($LedgerID,$VoucherNo,$Amount,$Date,$VoucherID,$Type)
{
	$dataVoucher1=0;
	$Createmsg2="";
	
	if($Type=='By')
	{
		$arByParentDetails = $this->m_objUtility->getParentOfLedger($LedgerID);	
		
		$LedgerName=$this->m_objUtility->getLedgerName($LedgerID);
		$Createmsg2="Amount from ".$LedgerName." ".$Amount."debit voucher deleted";
		$sql001="delete from `voucher` where VoucherNo=".$VoucherNo." and `By`=".$LedgerID." ";
		//echo $sql001;
		$sqlDelete = $this->m_dbConn->delete($sql001);
		
		//print_r($arByParentDetails);	
	   if(!(empty($arByParentDetails)))
	   {
			//echo 'arToParentDetails';
			$ByGroupID = $arByParentDetails['group'];
			$ByCategoryID = $arByParentDetails['category'];		
													
			if($ByGroupID==LIABILITY)
			{
				//echo '1';												
				//$regResult1 = $this->m_register->SetLiabilityRegister(getDBFormatDate($_POST['voucher_date']),$_POST['To'.$i],$dataVoucher1,VOUCHER_JOURNAL, TRANSACTION_DEBIT, $_POST['Debit'.$i], 0);	
				$regDelete1="delete from `liabilityregister` where `Is_Opening_Balance` = 0 AND VoucherID=".$VoucherID." and `LedgerID`=".$LedgerID." and Debit=".$Amount." ";
				echo $regDelete1;
				$regResult1 = $this->m_dbConn->delete($regDelete1);
						//echo 'end';									
			}
															
			if($ByGroupID==ASSET)
			{
				//echo '2';										
				//$regResult2 = $this->m_register->SetAssetRegister(getDBFormatDate($_POST['voucher_date']), $_POST['To'.$i], $dataVoucher1, VOUCHER_JOURNAL, TRANSACTION_DEBIT, $_POST['Debit'.$i], 0);	
				$regDelete2="delete from `assetregister` where `Is_Opening_Balance` = 0 AND VoucherID=".$VoucherID." and `LedgerID`=".$LedgerID." and Debit=".$Amount." ";
				echo $regDelete2;
				$regResult2 = $this->m_dbConn->delete($regDelete2);	
				//echo 'end';		
			}
															
			if($ByGroupID==INCOME)
			{
				//echo '3';													
				//$regResult3 = $this->m_register->SetIncomeRegister($_POST['To'.$i], getDBFormatDate($_POST['voucher_date']), $dataVoucher1, VOUCHER_JOURNAL, TRANSACTION_DEBIT, $_POST['Debit'.$i]);
				$regDelete3="delete from `incomeregister` where VoucherID=".$VoucherID." and `LedgerID`=".$LedgerID." and Debit=".$Amount."";
				echo $regDelete3;
				$regResult3 = $this->m_dbConn->delete($regDelete3);
				//echo 'end';		
			}
															
			if($ByGroupID==EXPENSE)
			{
						
				//echo '4';													
				//$regResult4 = $this->m_register->SetExpenseRegister($_POST['To'.$i],getDBFormatDate($_POST['voucher_date']), $dataVoucher1, VOUCHER_JOURNAL, TRANSACTION_DEBIT,$_POST['Debit'.$i],0);
				$regDelete4="delete from `expenseregister` where VoucherID=".$VoucherID." and `LedgerID`=".$LedgerID." and Debit=".$Amount."";
				echo $regDelete4;
				$regResult4 = $this->m_dbConn->delete($regDelete4);	
				//echo 'end';												
			}
													
		}
		
		
	}
	else
	{
		 $arToParentDetails = $this->m_objUtility->getParentOfLedger($LedgerID);
		 $LedgerName=$this->m_objUtility->getLedgerName($LedgerID);
		 $Createmsg2="Amount from ".$LedgerName." ".$Amount."credit voucher deleted.";
		 $sql002="delete from `voucher` where VoucherNo=".$VoucherNo." and `To`=".$LedgerID." ";
		 echo $sql002;
		 $sqlDelete = $this->m_dbConn->delete($sql002);
	
		 if(!(empty($arToParentDetails)))
		   {
			
					$ToGroupID = $arToParentDetails['group'];
					$ToCategoryID = $arToParentDetails['category'];		
																			
					if($ToGroupID==LIABILITY)
					{
								
						//$regResult1 = $this->m_register->SetLiabilityRegister(getDBFormatDate($_POST['voucher_date']),$_POST['To'.$i],$dataVoucher2, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $_POST['Credit'.$i], 0);	
						$regDelete1="delete from `liabilityregister` where `Is_Opening_Balance` = 0 AND VoucherID=".$VoucherID." and `LedgerID`=".$LedgerID." and Credit=".$Amount." ";
						echo $regDelete1;
						$regResult1 = $this->m_dbConn->delete($regDelete1);																
					}
																			
					if($ToGroupID==ASSET)
					{
						
						//$regResult2 = $this->m_register->SetAssetRegister(getDBFormatDate($_POST['voucher_date']), $_POST['To'.$i], $dataVoucher2, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $_POST['Credit'.$i], 0);	
						$regDelete2="delete from `assetregister` where `Is_Opening_Balance` = 0 AND VoucherID=".$VoucherID." and `LedgerID`=".$LedgerID." and Credit=".$Amount." ";
						echo $regDelete2;
						$regResult2 = $this->m_dbConn->delete($regDelete2);		
					}
																			
					if($ToGroupID==INCOME)
					{
																				
						//$regResult3 = $this->m_register->SetIncomeRegister($_POST['To'.$i], getDBFormatDate($_POST['voucher_date']), $dataVoucher2, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $_POST['Credit'.$i]);
						$regDelete3="delete from `incomeregister` where VoucherID=".$VoucherID." and `LedgerID`=".$LedgerID." and Credit=".$Amount."";
						echo $regDelete3;
						$regResult3 = $this->m_dbConn->delete($regDelete3);
					
					}
					
					if($ToGroupID==EXPENSE)
					{
																			
						//$regResult4 = $this->m_register->SetExpenseRegister($_POST['To'.$i],getDBFormatDate($_POST['voucher_date']), $dataVoucher2, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $_POST['Credit'.$i],0);
						$regDelete4="delete from `expenseregister` where VoucherID=".$VoucherID." and `LedgerID`=".$LedgerID." and Credit=".$Amount."";
						echo $regDelete4;
						$regResult4 = $this->m_dbConn->delete($regDelete4);														
					}
														
			}
	
	}
	
	
	return $Createmsg2;	 
	
}	


public function DeletedRecord()
{
	if(isset($_REQUEST['Vno']) && $_REQUEST['Vno'] <> '')
		  	{
				$PreviousData=$this->FetchData($_REQUEST['Vno']);
			
				   if($PreviousData <> '')
				   {
					//print_r($PreviousData);
						foreach($PreviousData as $k=>$v)
						{
							//echo '1';
							if($PreviousData[$k]['By'] <> '')
							{
								//echo '2';
								$sqlUpdate="Update `paymentdetails` set `ExpenseBy`='0',`TDSAmount`='0',`InvoiceDate`='0000-00-00'  where `VoucherID`='".$PreviousData[$k]['id']."' ";
								//echo $sqlUpdate;
								$data23=$this->m_dbConn->update($sqlUpdate);

								
								$sql="select  changelogtbl.ChangedLogDec from `voucher` as vouchertbl JOIN `change_log` as changelogtbl on changelogtbl.ChangeLogID=vouchertbl.LatestChangeID where VoucherNo='".$_REQUEST['Vno']."' and `By`=".$PreviousData[$k]['By']." ";
								//echo $sql;
								$data=$this->m_dbConn->select($sql);
								 
								$PreviousMsg=$data[0]['ChangedLogDec'];
								$msg=$this->DeletePreviousRecords($PreviousData[$k]['By'],$_REQUEST['Vno'],$PreviousData[$k]['Debit'],$PreviousData[$k]['Date'],$PreviousData[$k]['id'],'By');
								
								
							}
							else
							{
								//echo '3';
								$msg.=$this->DeletePreviousRecords($PreviousData[$k]['To'],$_REQUEST['Vno'],$PreviousData[$k]['Credit'],$PreviousData[$k]['Date'],$PreviousData[$k]['id'],'To');  
							}
						}
					
			}	
		  	$iLatestChangeID = $this->changeLog->setLog($msg, $_SESSION['login_id'], 'VOUCHER', 'Delected Voucher Number:'.$_REQUEST['Vno']);	
			}
			$this->actionPage="../view_ledger_details.php?&lid='".$_REQUEST['lid']."'&gid='".$_REQUEST['gid']."'";
			return "Record Deleted Succesfully..";
}

public function StartEndDate($yearID)
{
	$sql="SELECT DATE_FORMAT(BeginingDate, '%d-%m-%Y') as BeginingDate,DATE_FORMAT(EndingDate, '%d-%m-%Y') as EndingDate FROM `year` where `YearID`='".$yearID."'  ";
	$data=$this->m_dbConn->select($sql);
	
	/*$BeginingDateArray=explode('-',$datepicker[0]['BeginingDate']);
	$EndDateArray=explode('-',$datepicker[0]['EndingDate']);
	$StartDate=$BeginingDateArray[0].'-'.$BeginingDateArray[0].'-'.$BeginingDateArray[0];
	$EndDate=$BeginingDateArray[0].'-'.$BeginingDateArray[0].'-'.$BeginingDateArray[0];
	*/
	return $data;
	
}

}
?>