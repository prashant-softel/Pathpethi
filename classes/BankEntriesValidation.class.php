<?php

include_once("dbconst.class.php");
include_once("utility.class.php");
include_once("PaymentDetails.class.php");
class BankEntriesValidation
{
	public $m_dbConn;
	public $ErrorLog;
	public $m_objUtility;
	public $m_objPayments;
	public $isDelete;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->m_objUtility = new utility($dbConn);
		$this->m_objPayments = new PaymentDetails($dbConn);
		$this->isDelete = false;
	}
	
	public function FetchRecord()
	{
		
		if(isset($_POST["method"]) && $_POST["method"] == "run" && $_POST["cleanInvalidEntries"] == "YES")
		{
			$this->isDelete = true;		
		}
		//fetch all records from bank register
		$sqlFetchBank = "SELECT * FROM `bankregister`  where `Is_Opening_Balance` <> 1 order by `Date`";
		$resBank = $this->m_dbConn->select($sqlFetchBank);
		//validate each record fetch from bankregister
		$Id_Array = array();
		for($i = 0; $i < sizeof($resBank); $i++)
		{?>
			<div id="result<?php echo $i ?>" class="validEntry" style="padding:10px;">
             <?php
				if($this->isDelete == false)
				{
					if($i == 0)
					{
						echo "<br><br><br><font color='#0000FF'>Validating Bank Entry..</font>";
					}
					else
					{
						echo "<font color='#0000FF'>Validating Bank Entry..</font>";
					}
				}
				$BankTable = "<table border='1px solid black' style='border-collapse:collapse;' class='banktbl'><tr><th>ID</th><th>DATE</th><th>LEDGER ID</th><th>VOUCHER ID</th><th>VOUCHER TYPE ID</th><th>PAID AMOUNT</th><th>RECEIVED AMOUNT</th><th>CHECK DETAILS ID</th><th>Cheque Date</th><th>RECONCILE DATE</th></tr>";
				$BankTable .= "<tr><td>".$resBank[$i]['id']."</td><td>".getDisplayFormatDate($resBank[$i]['Date'])."</td><td>".$resBank[$i]['LedgerID']."</td><td>".$resBank[$i]['VoucherID']."</td><td>".$resBank[$i]['VoucherTypeID']."</td><td>".$resBank[$i]['PaidAmount']."</td><td>".$resBank[$i]['ReceivedAmount']."</td><td>".$resBank[$i]['ChkDetailID']."</td><td>".getDisplayFormatDate($resBank[$i]['Cheque Date'])."</td><td>".getDisplayFormatDate($resBank[$i]['Reconcile Date'])."</td></tr></table>";
				if($this->isDelete == false)
				{
					echo $BankTable;
				}
				//get table id from voucher to check entry is from  ie.paymentdetails or chequeentrydetails
				$RefTableName = $this->getRefTableName($resBank[$i]['VoucherID']);	
				if($this->isDelete == false)
				{
					//echo "<br>RefTableName:".$RefTableName;
				}
				
				$sqlYear ='SELECT * FROM `year`';
				$resultYear = $this->m_dbConn->select($sqlYear);
				
				if($resBank[$i]['Reconcile Date'] <> 0 && $resBank[$i]['Reconcile Date'] < $resBank[$i]['Cheque Date'])
				{
					if($this->isDelete == false)
					{
						?>
                        <script type="text/javascript">											
                        $("#result<?php echo $i ?>").removeClass("validEntry").addClass("error");						
						</script>	
                        <?php
						$ChequeNumber = $this->getChequeNumber($resBank[$i]['ChkDetailID'],$resBank[$i]['VoucherTypeID']);
						echo "<br>ChequeNumber:" .$ChequeNumber;		
						echo "<br><font color='#FF0000' >**Error**Reconcile Date is wrong or invalid</font>";
						$url ='bank_reconciliation.php?ledgerID='.$resBank[$i]['LedgerID'];
						echo $Url =	"&nbsp;&nbsp;<a href='' onClick=\"window.open('". $url ."','bankRecpopup','type=fullWindow,fullscreen,scrollbars=yes');\"><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a>";	
						//echo "<hr>";
					}							
				}
				
				if($resBank[$i]['Date'] < $resultYear[0]['BeginingDate'])
				{
					if($this->isDelete == false)
					{
						///echo "<br><font color='#FF0000' >**Error**Entry Date is wrong or invalid</font>";
						//echo "<hr>";
					}							
				}
				$chkDetailID = $resBank[$i]['ChkDetailID'];
				//echo "<br>ChkDetailID:".$chkDetailID;
				$voucherID = $resBank[$i]['VoucherID'];
				$voucherTypeID = $resBank[$i]['VoucherTypeID'];			
				
				//get vouchertype from voucher table
				$Type = $this->getVoucherType($voucherTypeID);
				$voucherType = $Type[0]['type'];
				$paidAmount = $resBank[$i]['PaidAmount'];
				$receivedAmount = $resBank[$i]['ReceivedAmount'];
				if($RefTableName <> "")
				{
					if($RefTableName == TABLE_PAYMENT_DETAILS)
					{	
						//reading record from paymentdetails table
						if($this->isDelete == false)
						{
							echo "<br>Type:Payment";			
						}
						$sqlFetchPayment = "SELECT * FROM `paymentdetails` where `id`='".$chkDetailID."'";
						$resPayment = $this->m_dbConn->select($sqlFetchPayment);
						if($resPayment <> "")
						{
							//entry exist in paymentdetails table now check for related voucher and register table for existence
							$this->paymentDetailsChecker($resPayment,$resBank[$i]['id'],$i);
						}
						else
						{
							//entry does not exist in paymentdetails
							if($this->isDelete == false)
							{
								echo "<br>Record not found in paymentdetails[id:".$chkDetailID." missing]";
								$delBank = "<font color='#FF0000'>DELETE FROM `bankregister` WHERE `id`='".$resBank[$i]['id']."'</font> ";
								echo "<br>**Error**".$delBank;								
								?>
								<script type="text/javascript">											
                                $("#result<?php echo $i ?>").removeClass("validEntry").addClass("error");						
                                </script>	
                                <?php
							}
							else
							{
								$delBank = "DELETE FROM `bankregister` WHERE `id`='".$resBank[$i]['id']."'";
								$data1 = $this->m_dbConn->delete($delBank);
							}
							
							$voucher_select = "select `id`,`VoucherNo` from `voucher` where `RefNo`=".$resBank[$i]['ChkDetailID']." and `RefTableID`=".TABLE_PAYMENT_DETAILS." ";
							$resVoucher = $this->m_dbConn->select($voucher_select);
							$jvVoucherNo = $resVoucher[0]['VoucherNo']-1;
							$jvvoucher_select = "select * from `voucher` where `VoucherNo`=".$jvVoucherNo." ";
							$resJVVoucher = $this->m_dbConn->select($jvvoucher_select);
							
							foreach($resVoucher as $key => $val)
							{
								array_push($Id_Array,$resVoucher[$key]['id']);
							}
							if(sizeof($resJVVoucher) > 0)
							{
								if($resJVVoucher[0]['RefNo'] == 0 && $resJVVoucher[0]['RefTableID'] == 0)
								{
									//means jv for this entry exists
									foreach($resVoucher as $key => $val)
									{
										array_push($Id_Array,$resJVVoucher[$key]['id']);
									}		
								}
							}
							$VoucherIDArray = implode(',', $Id_Array);
							if($this->isDelete == true)
							{
								if(sizeof($resJVVoucher) > 0)
								{
									$delVoucher2 = "DELETE FROM `voucher` where `VoucherNo`=".$jvVoucherNo."";
									$data32 = $this->m_dbConn->delete($delVoucher2);	
								}
								$delVoucher = "DELETE FROM `voucher` where `RefNo`='".$resBank[$i]['ChkDetailID']."' and `RefTableID`='".TABLE_PAYMENT_DETAILS."'";
								$data1 = $this->m_dbConn->delete($delVoucher);
								$sqlFetchIncome = "DELETE FROM `incomeregister` where `VoucherID` IN ($VoucherIDArray)";
								$data2 = $this->m_dbConn->delete($sqlFetchIncome);
								$sqlFetchExpense = "DELETE FROM `expenseregister` where `VoucherID` IN ($VoucherIDArray)";
								$data3 = $this->m_dbConn->delete($sqlFetchExpense);
								$sqlFetchAsset = "DELETE FROM `assetregister` where `VoucherID` IN ($VoucherIDArray)";
								$data4 = $this->m_dbConn->delete($sqlFetchAsset);
								$sqlFetchLiability = "DELETE FROM `liabilityregister` where `VoucherID` IN ($VoucherIDArray)";
								$data5 = $this->m_dbConn->delete($sqlFetchLiability);
								//return;
							}
							else
							{
								if(sizeof($resJVVoucher) > 0)
								{
									$delVoucher2 = "<font color='#FF0000'>DELETE FROM `voucher` where `VoucherNo`=".$jvVoucherNo."</font>";
									echo "<br>**Error**".$delVoucher2;	
								}
								$delVoucher = "<font color='#FF0000'>DELETE FROM `voucher` where `RefNo`='".$resBank[$i]['ChkDetailID']."' and `RefTableID`='".TABLE_PAYMENT_DETAILS."'</font> ";
								echo "<br>**Error**".$delVoucher;
								$sqlFetchIncome = "<font color='#FF0000'>DELETE FROM `incomeregister` where `VoucherID` IN ($VoucherIDArray)</font>";
								echo "<br>**Error**".$sqlFetchIncome;
								$sqlFetchExpense = "<font color='#FF0000'>DELETE FROM `expenseregister` where `VoucherID` IN ($VoucherIDArray)</font>";
								echo "<br>**Error**".$sqlFetchExpense;
								$sqlFetchAsset = "<font color='#FF0000'>DELETE FROM `assetregister` where `VoucherID` IN ($VoucherIDArray)</font>";
								echo "<br>**Error**".$sqlFetchAsset;
								$sqlFetchLiability = "<font color='#FF0000'>DELETE FROM `liabilityregister` where `VoucherID` IN ($VoucherIDArray)</font>";
								echo "<br>**Error**".$sqlFetchLiability;
								//return;
								?>
								<script type="text/javascript">											
                                $("#result<?php echo $i ?>").removeClass("validEntry").addClass("error");						
                                </script>	
                                <?php
							}
							
						}
					}
					else if($RefTableName == TABLE_CHEQUE_DETAILS)
					{
						//reading record from chequeentrydetails table
						if($this->isDelete == false)
						{
							echo "<br>Type:Receipt";
						}
						$sqlFetchCheque = "SELECT * FROM `chequeentrydetails` where `ID`='".$chkDetailID."'";
						$resCheque = $this->m_dbConn->select($sqlFetchCheque);									
						if($resCheque <> "")
						{
							//entry exist in chequeentrydetails table now check for related voucher and register table for existence
							$this->receiptDetailsChecker($resCheque,$resBank[$i]['id'],$i);
						}
						else
						{
							//entry does not exist in chequeentrydetails
							if($this->isDelete == false)
							{
								echo "<br>Record not found in chequeentrydetails[id:".$chkDetailID." missing]";
								$delBank = "<font color='#FF0000'>DELETE FROM `bankregister` WHERE `id`='".$resBank[$i]['id']."'</font> ";
								echo "<br>**Error**".$delBank;	
								?>
								<script type="text/javascript">											
                                $("#result<?php echo $i ?>").removeClass("validEntry").addClass("error");						
                                </script>	
                                <?php							
							}
							else
							{
								$delBank = "DELETE FROM `bankregister` WHERE `id`='".$resBank[$i]['id']."'";
								$data1 = $this->m_dbConn->delete($delBank);
							}
							
							$voucher_select = "select `id`,`VoucherNo` from `voucher` where `RefNo`=".$resBank[$i]['ChkDetailID']." and `RefTableID`=".TABLE_CHEQUE_DETAILS." ";
							$resVoucher = $this->m_dbConn->select($voucher_select);
							//print_r($resVoucher);
							foreach($resVoucher as $key => $val)
							{
								array_push($Id_Array,$resVoucher[$key]['id']);
							}
							$VoucherIDArray = implode(',', $Id_Array);
							//echo "VoucherIDArray:".$VoucherIDArray;
							
							if($this->isDelete == true)
							{
								$delVoucher = "DELETE FROM `voucher` where `RefNo`='".$resBank[$i]['ChkDetailID']."' and `RefTableID`='".TABLE_CHEQUE_DETAILS."'";
								$data1 = $this->m_dbConn->delete($delVoucher);
								$sqlFetchIncome = "DELETE FROM `incomeregister` where `VoucherID` IN ($VoucherIDArray)";
								$data2 = $this->m_dbConn->delete($sqlFetchIncome);
								$sqlFetchExpense = "DELETE FROM `expenseregister` where `VoucherID` IN ($VoucherIDArray)";
								$data3 = $this->m_dbConn->delete($sqlFetchExpense);
								$sqlFetchAsset = "DELETE FROM `assetregister` where `VoucherID` IN ($VoucherIDArray)";
								$data4 = $this->m_dbConn->delete($sqlFetchAsset);
								$sqlFetchLiability = "DELETE FROM `liabilityregister` where `VoucherID` IN ($VoucherIDArray)";
								$data5 = $this->m_dbConn->delete($sqlFetchLiability);
								//return;
							}
							else
							{
								$delVoucher = "<font color='#FF0000'>DELETE FROM `voucher` where `RefNo`='".$resBank[$i]['ChkDetailID']."' and `RefTableID`='".TABLE_CHEQUE_DETAILS."'</font> ";
								echo "<br>**Error**".$delVoucher;
								$sqlFetchIncome = "<font color='#FF0000'>DELETE FROM `incomeregister` where `VoucherID` IN ($VoucherIDArray)</font>";
								echo "<br>**Error**".$sqlFetchIncome;
								$sqlFetchExpense = "<font color='#FF0000'>DELETE FROM `expenseregister` where `VoucherID` IN ($VoucherIDArray)</font>";
								echo "<br>**Error**".$sqlFetchExpense;
								$sqlFetchAsset = "<font color='#FF0000'>DELETE FROM `assetregister` where `VoucherID` IN ($VoucherIDArray)</font>";
								echo "<br>**Error**".$sqlFetchAsset;
								$sqlFetchLiability = "<font color='#FF0000'>DELETE FROM `liabilityregister` where `VoucherID` IN ($VoucherIDArray)</font>";
								echo "<br>**Error**".$sqlFetchLiability;
								//return;
								?>
								<script type="text/javascript">											
                                $("#result<?php echo $i ?>").removeClass("validEntry").addClass("error");						
                                </script>	
                                <?php
							}
						}							
					}
					else
					{
							echo $delBank = "<br><font style='background:#FFFF00;'>**Warning**Unhandled Record</font> ";
					}
						
				}
				else
				{
						//bankregister entry not connected to any voucher hence delete bankregister entry
						
						if($this->isDelete == true)
						{
							$delBank = "DELETE FROM `bankregister` WHERE `id`='".$resBank[$i]['id']."'";
							$data5 = $this->m_dbConn->delete($delBank);
						}
						else
						{
							echo "<br>Voucher not found[Voucher ID:".$resBank[$i]['VoucherID']." missing]";
							$delBank = "<font color='#FF0000'>DELETE FROM `bankregister` WHERE `id`='".$resBank[$i]['id']."' </font>";
							echo "<br>**Error**".$delBank;
							?>
							<script type="text/javascript">											
                            $("#result<?php echo $i ?>").removeClass("validEntry").addClass("error");						
                            </script>	
                            <?php
						}
				}
			if($this->isDelete == false)
			{	
				//echo "<br>---------------------------------------------------------------------------------------------------------------------------------------------------------";	
				echo "<hr>";	
			}
			?>
        </div>       
		<?php
		}			
	}
	
	
	public function paymentDetailsChecker($PaymentDetails,$BankID,$val)
	{
		$isContraEntry=true;
		$Id_Array = array();
		$Id_Array2 = array();
		
		//get PaidTo group id and category id 
		$arPaidToParentDetails = $this->m_objUtility->getParentOfLedger($PaymentDetails[0]['PaidTo']);
		if(!(empty($arPaidToParentDetails)))
		{
			$PaidToGroupID = $arPaidToParentDetails['group'];
			$PaidToCategoryID = $arPaidToParentDetails['category'];
			$PaidToCategoryName = $arPaidToParentDetails['category_name'];
		}
		if($this->isDelete == false)
		{
			echo "<br>PaidTo:[".$arPaidToParentDetails['ledger_name']."][".$arPaidToParentDetails['group_name']."]";
		}
		//get PayerBank group id and category id 
		$arParentDetails = $this->m_objUtility->getParentOfLedger($PaymentDetails[0]['PayerBank']);
		if(!(empty($arParentDetails)))
		{
			$PayerBankGroupID = $arParentDetails['group'];
			$PayerBankCategoryID = $arParentDetails['category'];
			$PayerBankCategoryName = $arParentDetails['category_name'];
		}
		if($this->isDelete == false)
		{
			echo "<br>PayerBank:[".$arParentDetails['ledger_name']."][".$arParentDetails['group_name']."]";
		}
		//check if payment voucher exists
		$voucher_select = "select `id`,`VoucherNo` from `voucher` where `RefNo`=".$PaymentDetails[0]['id']." and `RefTableID`=".TABLE_PAYMENT_DETAILS." ";
		$resVoucher = $this->m_dbConn->select($voucher_select);
		if($resVoucher == "")
		{
			
			//payment voucher does not exists
			if($this->isDelete == true)
			{
				$this->m_objPayments->deletePaymentDetails($PaymentDetails[0]['ChequeDate'],$PaymentDetails[0]['ChequeNumber'],$PaymentDetails[0]['VoucherDate'],$PaymentDetails[0]['Amount'],$PaymentDetails[0]['PaidTo'],$PaymentDetails[0]['ExpenseBy'],$PaymentDetails[0]['PayerBank'],$PaymentDetails[0]['ChqLeafID'],$PaymentDetails[0]['Comments'],$PaymentDetails[0]['InvoiceDate'],$PaymentDetails[0]['TDSAmount'],$PaymentDetails[0]['id'],false);
				//return;
			}
			else
			{
				echo "<br>payment voucher does not exists";
				$this->m_objPayments->deletePaymentDetails($PaymentDetails[0]['ChequeDate'],$PaymentDetails[0]['ChequeNumber'],$PaymentDetails[0]['VoucherDate'],$PaymentDetails[0]['Amount'],$PaymentDetails[0]['PaidTo'],$PaymentDetails[0]['ExpenseBy'],$PaymentDetails[0]['PayerBank'],$PaymentDetails[0]['ChqLeafID'],$PaymentDetails[0]['Comments'],$PaymentDetails[0]['InvoiceDate'],$PaymentDetails[0]['TDSAmount'],$PaymentDetails[0]['id'],true);
				?>
				<script type="text/javascript">											
                $("#result<?php echo $val ?>").removeClass("validEntry").addClass("error");						
                </script>	
                <?php
				return;	
			}
			
		}
		else
		{
			//payment voucher exists
			if($PaidToGroupID == LIABILITY || $PaidToGroupID == ASSET)
			{ 
				$voucherTemp = "select `id`,`VoucherNo` from `voucher` where `RefNo`=".$PaymentDetails[0]['id']." and `RefTableID`=".TABLE_PAYMENT_DETAILS." and `To` = '".$PaymentDetails[0]['PaidTo']."' ";
				$resVoucherTemp = $this->m_dbConn->select($voucherTemp);
				
				if(sizeof($resVoucherTemp) > 0)
				{
					$this->checkForValidCategory($arPaidToParentDetails,$PaymentDetails,$resVoucherTemp[0]['id']);
				}
			}
			
			foreach($resVoucher as $key => $val)
			{
				array_push($Id_Array,$resVoucher[$key]['id']);
			}
			$VoucherIDArray = implode(',', $Id_Array);
			if($this->isDelete == false)
			{
			echo "<br>PaidToCategoryID::".$PaidToCategoryName;
			echo "<br>PayerBankCategoryID::".$PayerBankCategoryName;
			}
			/*check condition for contra entry
				if paidto is bank account and payerbank is cash account i.e contra entry
				if paidto is bank account and payerbank is bank account  i.e contra entry
				if paidto is cash account and payerbank is bank account  i.e contra entry
			*/
			if(($PaidToCategoryID == BANK_ACCOUNT || $PaidToCategoryID == CASH_ACCOUNT) && ($PayerBankCategoryID == BANK_ACCOUNT || $PayerBankCategoryID == CASH_ACCOUNT))
			{
				//set flag for contra entry
				$isContraEntry = true;	
			}
			else
			{
				$isContraEntry = false;	
			}
			
			//entry is not contra entry check all register and voucher
			if($isContraEntry == false)
			{
				if($this->isDelete == false)
				{
					echo "<br>Payment Voucher Exists";
				}
				//payment voucher is exist now check if entry exists in any register
				if($resVoucher <> "")
				{
					//check entry in each register
					$sqlFetchLiability="SELECT * FROM `liabilityregister` where `VoucherID` IN ($VoucherIDArray)";
					$resLiability = $this->m_dbConn->select($sqlFetchLiability);
					$sqlFetchAsset="SELECT * FROM `assetregister` where `VoucherID` IN ($VoucherIDArray)";
					$resAsset = $this->m_dbConn->select($sqlFetchAsset);	
					$sqlFetchIncome="SELECT * FROM `incomeregister` where `VoucherID` IN ($VoucherIDArray)";
					$resIncome = $this->m_dbConn->select($sqlFetchIncome);
					$sqlFetchExpense="SELECT * FROM `expenseregister` where `VoucherID` IN ($VoucherIDArray)";
					$resExpense = $this->m_dbConn->select($sqlFetchExpense);
					//if any not exists in any register
					if($resLiability == "" && $resAsset == "" && $resExpense == "" && $resIncome == "")
					{
						
						if($this->isDelete == true)
						{
							$this->m_objPayments->deletePaymentDetails($PaymentDetails[0]['ChequeDate'],$PaymentDetails[0]['ChequeNumber'],$PaymentDetails[0]['VoucherDate'],$PaymentDetails[0]['Amount'],$PaymentDetails[0]['PaidTo'],$PaymentDetails[0]['ExpenseBy'],$PaymentDetails[0]['PayerBank'],$PaymentDetails[0]['ChqLeafID'],$PaymentDetails[0]['Comments'],$PaymentDetails[0]['InvoiceDate'],$PaymentDetails[0]['TDSAmount'],$PaymentDetails[0]['id'],false);
							//return;
						}
						else
						{
							echo "<br>Record not exists in any register";
							$this->m_objPayments->deletePaymentDetails($PaymentDetails[0]['ChequeDate'],$PaymentDetails[0]['ChequeNumber'],$PaymentDetails[0]['VoucherDate'],$PaymentDetails[0]['Amount'],$PaymentDetails[0]['PaidTo'],$PaymentDetails[0]['ExpenseBy'],$PaymentDetails[0]['PayerBank'],$PaymentDetails[0]['ChqLeafID'],$PaymentDetails[0]['Comments'],$PaymentDetails[0]['InvoiceDate'],$PaymentDetails[0]['TDSAmount'],$PaymentDetails[0]['id'],true);
							?>
							<script type="text/javascript">											
                            $("#result<?php echo $val ?>").removeClass("validEntry").addClass("error");						
                            </script>	
                            <?php
							return;	
						}
					}
				}
				
				//if expenseby is not blanck or 0 then entry is double entry
				if($PaymentDetails[0]['ExpenseBy'] <> "" && $PaymentDetails[0]['ExpenseBy'] <> 0)
				{
					
					$arParentExpenseByDetails = $this->m_objUtility->getParentOfLedger($PaymentDetails[0]['ExpenseBy']);
					if(!(empty($arParentExpenseByDetails)))
					{
						$ExpenseByGroupID = $arParentExpenseByDetails['group'];
						$ExpenseByCategoryID = $arParentExpenseByDetails['category'];
					}
					if($this->isDelete == false)
					{
						echo "<br>double entry";
						echo "<br>ExpenseBy:[".$arParentExpenseByDetails['ledger_name']."][".$arParentExpenseByDetails['group_name']."]";
					}
					//check for journal voucher
					if($PaymentDetails[0]['VoucherID'] <> 0)
					{
						$query = "SELECT `VoucherNo` FROM `voucher` WHERE `id` = '".$PaymentDetails[0]['VoucherID']."'";
						$jvNo = $this->m_dbConn->select($query);
						$JVoucherNo=$jvNo[0]['VoucherNo'];						
					}
					else
					{
						$JVoucherNo = $resVoucher[0]['VoucherNo']-1;
					}
					$jvoucher_select = "select `id` from `voucher` where `VoucherNo`=".$JVoucherNo." ";
					$VoucherData02 = $this->m_dbConn->select($jvoucher_select);
					
					
					if($VoucherData02 == "")
					{
						
						//journal voucher does not exist
						if($this->isDelete == true)
						{
							$this->m_objPayments->deletePaymentDetails($PaymentDetails[0]['ChequeDate'],$PaymentDetails[0]['ChequeNumber'],$PaymentDetails[0]['VoucherDate'],$PaymentDetails[0]['Amount'],$PaymentDetails[0]['PaidTo'],$PaymentDetails[0]['ExpenseBy'],$PaymentDetails[0]['PayerBank'],$PaymentDetails[0]['ChqLeafID'],$PaymentDetails[0]['Comments'],$PaymentDetails[0]['InvoiceDate'],$PaymentDetails[0]['TDSAmount'],$PaymentDetails[0]['id'],false);
							//return;
						}
						else
						{
							echo "<br>journal voucher does not exist";
							$this->m_objPayments->deletePaymentDetails($PaymentDetails[0]['ChequeDate'],$PaymentDetails[0]['ChequeNumber'],$PaymentDetails[0]['VoucherDate'],$PaymentDetails[0]['Amount'],$PaymentDetails[0]['PaidTo'],$PaymentDetails[0]['ExpenseBy'],$PaymentDetails[0]['PayerBank'],$PaymentDetails[0]['ChqLeafID'],$PaymentDetails[0]['Comments'],$PaymentDetails[0]['InvoiceDate'],$PaymentDetails[0]['TDSAmount'],$PaymentDetails[0]['id'],true);
							?>
							<script type="text/javascript">											
                            $("#result<?php echo $val ?>").removeClass("validEntry").addClass("error");						
                            </script>	
                            <?php
							return;	
						}
					}
					else
					{
						//journal voucher exists
						foreach($VoucherData02 as $key => $val)
						{
							array_push($Id_Array2,$VoucherData02[$key]['id']);
						}
						$VoucherIDArray2 = implode(',', $Id_Array2);
						
						//check entry in each register
						
						$sqlFetchLiability = "SELECT * FROM `liabilityregister` where `VoucherID` IN ($VoucherIDArray2)";
						$resLiability2 = $this->m_dbConn->select($sqlFetchLiability);
						$sqlFetchAsset = "SELECT * FROM `assetregister` where `VoucherID` IN ($VoucherIDArray2)";
						$resAsset2 = $this->m_dbConn->select($sqlFetchAsset);	
						$sqlFetchIncome = "SELECT * FROM `incomeregister` where `VoucherID` IN ($VoucherIDArray2)";
						$resIncome2 = $this->m_dbConn->select($sqlFetchIncome);
						$sqlFetchExpense = "SELECT * FROM `expenseregister` where `VoucherID` IN ($VoucherIDArray2)";
						$resExpense2 = $this->m_dbConn->select($sqlFetchExpense);
						
						//if entry not found in any register
						if($resLiability2 == "" && $resAsset2 == "" && $resExpense2 == "" && $resIncome2 == "")
						{
							if($this->isDelete == true)
							{
								$this->m_objPayments->deletePaymentDetails($PaymentDetails[0]['ChequeDate'],$PaymentDetails[0]['ChequeNumber'],$PaymentDetails[0]['VoucherDate'],$PaymentDetails[0]['Amount'],$PaymentDetails[0]['PaidTo'],$PaymentDetails[0]['ExpenseBy'],$PaymentDetails[0]['PayerBank'],$PaymentDetails[0]['ChqLeafID'],$PaymentDetails[0]['Comments'],$PaymentDetails[0]['InvoiceDate'],$PaymentDetails[0]['TDSAmount'],$PaymentDetails[0]['id'],false);
								//return;
							}
							else
							{
								$this->m_objPayments->deletePaymentDetails($PaymentDetails[0]['ChequeDate'],$PaymentDetails[0]['ChequeNumber'],$PaymentDetails[0]['VoucherDate'],$PaymentDetails[0]['Amount'],$PaymentDetails[0]['PaidTo'],$PaymentDetails[0]['ExpenseBy'],$PaymentDetails[0]['PayerBank'],$PaymentDetails[0]['ChqLeafID'],$PaymentDetails[0]['Comments'],$PaymentDetails[0]['InvoiceDate'],$PaymentDetails[0]['TDSAmount'],$PaymentDetails[0]['id'],true);
								?>
							<script type="text/javascript">											
                            $("#result<?php echo $val ?>").removeClass("validEntry").addClass("error");						
                            </script>	
                            <?php
								return;	
							}
						}
					}
				}
			}
			else
			{
				//if contra entry flag true
				if($this->isDelete == false)
				{
					echo "<br>contra entry";
				}
				//echo "<br>Payment Voucher Exists";
				//payment voucher is exist now check if entry exists in any register
				if($resVoucher <> "")
				{
					//check entry in each register
					$sqlFetchLiability="SELECT * FROM `liabilityregister` where `VoucherID` IN ($VoucherIDArray)";
					$resLiability = $this->m_dbConn->select($sqlFetchLiability);
					$sqlFetchAsset="SELECT * FROM `assetregister` where `VoucherID` IN ($VoucherIDArray)";
					$resAsset = $this->m_dbConn->select($sqlFetchAsset);	
					$sqlFetchIncome="SELECT * FROM `incomeregister` where `VoucherID` IN ($VoucherIDArray)";
					$resIncome = $this->m_dbConn->select($sqlFetchIncome);
					$sqlFetchExpense="SELECT * FROM `expenseregister` where `VoucherID` IN ($VoucherIDArray)";
					$resExpense = $this->m_dbConn->select($sqlFetchExpense);
					//if any not exists in any register
					if($resLiability == "" || $resAsset == "" || $resExpense == "" || $resIncome == "")
					{
						
						if($resLiability <> "")
						{
							echo $delBank = "<br><font style='background:#FFFF00;'>**Warning**Invalid Contra Entry Found In Liability Register.</font> ";
						}
						if($resAsset <> "")
						{
							echo $delBank = "<br><font style='background:#FFFF00;'>**Warning**Invalid Contra Entry Found In Asset Register.</font> ";
						}
						if($resExpense <> "")
						{
							echo $delBank = "<br><font style='background:#FFFF00;'>**Warning**Invalid Contra Entry Found In Expense Register.</font> ";
						}
						if($resIncome <> "")
						{
							echo $delBank = "<br><font style='background:#FFFF00;'>**Warning**Invalid Contra Entry Found In Income Register.</font> ";
						}
						
						
					}
				}
				
				//if expenseby is not blanck or 0 then entry is double entry
				if($PaymentDetails[0]['ExpenseBy'] <> "" && $PaymentDetails[0]['ExpenseBy'] <> 0)
				{
					
					$arParentExpenseByDetails = $this->m_objUtility->getParentOfLedger($PaymentDetails[0]['ExpenseBy']);
					if(!(empty($arParentExpenseByDetails)))
					{
						$ExpenseByGroupID = $arParentExpenseByDetails['group'];
						$ExpenseByCategoryID = $arParentExpenseByDetails['category'];
					}
					if($this->isDelete == false)
					{
						echo "<br>double entry";
						echo "<br>ExpenseBy:[".$arParentExpenseByDetails['ledger_name']."][".$arParentExpenseByDetails['group_name']."]";
					}
					//check for journal voucher
					$JVoucherNo = $resVoucher[0]['VoucherNo']-1;
					$jvoucher_select = "select `id` from `voucher` where `VoucherNo`=".$JVoucherNo." ";
					$VoucherData02 = $this->m_dbConn->select($jvoucher_select);
					
					
					if($VoucherData02 == "")
					{
						
						//journal voucher does not exist
						if($this->isDelete == true)
						{
							$this->m_objPayments->deletePaymentDetails($PaymentDetails[0]['ChequeDate'],$PaymentDetails[0]['ChequeNumber'],$PaymentDetails[0]['VoucherDate'],$PaymentDetails[0]['Amount'],$PaymentDetails[0]['PaidTo'],$PaymentDetails[0]['ExpenseBy'],$PaymentDetails[0]['PayerBank'],$PaymentDetails[0]['ChqLeafID'],$PaymentDetails[0]['Comments'],$PaymentDetails[0]['InvoiceDate'],$PaymentDetails[0]['TDSAmount'],$PaymentDetails[0]['id'],false);
							//return;
						}
						else
						{
							echo "<br>journal voucher does not exist";
							$this->m_objPayments->deletePaymentDetails($PaymentDetails[0]['ChequeDate'],$PaymentDetails[0]['ChequeNumber'],$PaymentDetails[0]['VoucherDate'],$PaymentDetails[0]['Amount'],$PaymentDetails[0]['PaidTo'],$PaymentDetails[0]['ExpenseBy'],$PaymentDetails[0]['PayerBank'],$PaymentDetails[0]['ChqLeafID'],$PaymentDetails[0]['Comments'],$PaymentDetails[0]['InvoiceDate'],$PaymentDetails[0]['TDSAmount'],$PaymentDetails[0]['id'],true);
							?>
							<script type="text/javascript">											
                            $("#result<?php echo $val ?>").removeClass("validEntry").addClass("error");						
                            </script>	
                            <?php
							return;	
						}
					}
					else
					{
						//journal voucher exists
						foreach($VoucherData02 as $key => $val)
						{
							array_push($Id_Array2,$VoucherData02[$key]['id']);
						}
						$VoucherIDArray2 = implode(',', $Id_Array2);
						
						//check entry in each register
						
						$sqlFetchLiability = "SELECT * FROM `liabilityregister` where `VoucherID` IN ($VoucherIDArray2)";
						$resLiability2 = $this->m_dbConn->select($sqlFetchLiability);
						$sqlFetchAsset = "SELECT * FROM `assetregister` where `VoucherID` IN ($VoucherIDArray2)";
						$resAsset2 = $this->m_dbConn->select($sqlFetchAsset);	
						$sqlFetchIncome = "SELECT * FROM `incomeregister` where `VoucherID` IN ($VoucherIDArray2)";
						$resIncome2 = $this->m_dbConn->select($sqlFetchIncome);
						$sqlFetchExpense = "SELECT * FROM `expenseregister` where `VoucherID` IN ($VoucherIDArray2)";
						$resExpense2 = $this->m_dbConn->select($sqlFetchExpense);
						
						//if entry not found in any register
						if($resLiability2 == "" || $resAsset2 == "" || $resExpense2 == "" || $resIncome2 == "")
						{
							
							if($resLiability2 <> "")
							{
								echo $delBank = "<br><font style='background:#FFFF00;'>**Warning**Invalid Contra Entry Found In Liability Register.</font> ";
							}
							if($resAsset2 <> "")
							{
								echo $delBank = "<br><font style='background:#FFFF00;'>**Warning**Invalid Contra Entry Found In Asset Register.</font> ";
							}
							if($resExpense2 <> "")
							{
								echo $delBank = "<br><font style='background:#FFFF00;'>**Warning**Invalid Contra Entry Found In Expense Register.</font> ";
							}
							if($resIncome2 <> "")
							{
								echo $delBank = "<br><font style='background:#FFFF00;'>**Warning**Invalid Contra Entry Found In Income Register.</font> ";
							}
							
							
						}
					}
				}	
			}
		}
	}
	
	
	
	public function paymentDetailsCheckerOLD($PaymentDetails,$BankID)
	{
		$isContraEntry=true;
		$Id_Array = array();
		$Id_Array2 = array();
		
		//get PaidTo group id and category id 
		$arPaidToParentDetails = $this->m_objUtility->getParentOfLedger($PaymentDetails[0]['PaidTo']);
		if(!(empty($arPaidToParentDetails)))
		{
			$PaidToGroupID = $arPaidToParentDetails['group'];
			$PaidToCategoryID = $arPaidToParentDetails['category'];
			$PaidToCategoryName = $arPaidToParentDetails['category_name'];
		}
		echo "<br>PaidTo:[".$arPaidToParentDetails['ledger_name']."][".$arPaidToParentDetails['group_name']."]";
		
		//get PayerBank group id and category id 
		$arParentDetails = $this->m_objUtility->getParentOfLedger($PaymentDetails[0]['PayerBank']);
		if(!(empty($arParentDetails)))
		{
			$PayerBankGroupID = $arParentDetails['group'];
			$PayerBankCategoryID = $arParentDetails['category'];
			$PayerBankCategoryName = $arParentDetails['category_name'];
		}
		echo "<br>PayerBank:[".$arParentDetails['ledger_name']."][".$arParentDetails['group_name']."]";
		
		//check if payment voucher exists
		$voucher_select = "select `id`,`VoucherNo` from `voucher` where `RefNo`=".$PaymentDetails[0]['id']." and `RefTableID`=".TABLE_PAYMENT_DETAILS." ";
		$resVoucher = $this->m_dbConn->select($voucher_select);
		if($resVoucher == "")
		{
			//payment voucher does not exists
			echo "<br>Record not found in voucher[RefNo:".$PaymentDetails[0]['id']."  RefTableID:".TABLE_PAYMENT_DETAILS." missing]";
			$delBank = "<font color='#FF0000'>DELETE FROM `bankregister` WHERE `id`='".$BankID."'</font> ";
			echo "<br>**Error**".$delBank;
			$delPayment = "<font color='#FF0000'>DELETE FROM `paymentdetails` WHERE `id`='".$PaymentDetails[0]['id']."'</font> ";
			echo "<br>**Error**".$delPayment;
		}
		else
		{
			//payment voucher exists
			foreach($resVoucher as $key => $val)
			{
				array_push($Id_Array,$resVoucher[$key]['id']);
			}
			$VoucherIDArray = implode(',', $Id_Array);
			echo "<br>PaidToCategoryID::".$PaidToCategoryName;
			echo "<br>PayerBankCategoryID::".$PayerBankCategoryName;
			
			/*check condition for contra entry
				if paidto is bank account and payerbank is cash account i.e contra entry
				if paidto is bank account and payerbank is bank account  i.e contra entry
				if paidto is cash account and payerbank is bank account  i.e contra entry
			*/
			if(($PaidToCategoryID == BANK_ACCOUNT || $PaidToCategoryID == CASH_ACCOUNT) && ($PayerBankCategoryID == BANK_ACCOUNT || $PayerBankCategoryID == CASH_ACCOUNT))
			{
				//set flag for contra entry
				$isContraEntry = true;	
			}
			else
			{
				$isContraEntry = false;	
			}
			
			//entry is not contra entry check all register and voucher
			if($isContraEntry == false)
			{
				//echo "<br>Single entry";
				//payment voucher is exist now check if entry exists in any register
				if($resVoucher <> "")
				{
					//check entry in each register
					$sqlFetchLiability="SELECT * FROM `liabilityregister` where `VoucherID` IN ($VoucherIDArray)";
					$resLiability = $this->m_dbConn->select($sqlFetchLiability);
					$sqlFetchAsset="SELECT * FROM `assetregister` where `VoucherID` IN ($VoucherIDArray)";
					$resAsset = $this->m_dbConn->select($sqlFetchAsset);	
					$sqlFetchIncome="SELECT * FROM `incomeregister` where `VoucherID` IN ($VoucherIDArray)";
					$resIncome = $this->m_dbConn->select($sqlFetchIncome);
					$sqlFetchExpense="SELECT * FROM `expenseregister` where `VoucherID` IN ($VoucherIDArray)";
					$resExpense = $this->m_dbConn->select($sqlFetchExpense);
					//if any not exists in any register
					if($resLiability == "" && $resAsset == "" && $resExpense == "" && $resIncome == "")
					{
						echo "<br>Record not found any register";
						$delBank = "<font color='#FF0000'>DELETE FROM `bankregister` WHERE `id`='".$BankID."'</font> ";
						echo "<br>**Error**".$delBank;
						$delPayment = "<font color='#FF0000'>DELETE FROM `paymentdetails` WHERE `id`='".$PaymentDetails[0]['id']."'</font> ";
						echo "<br>**Error**".$delPayment;
						$delVoucher = "<font color='#FF0000'>DELETE FROM `voucher` where `RefNo`='".$PaymentDetails[0]['id']."' and `RefTableID`='".TABLE_PAYMENT_DETAILS."'</font> ";
						echo "<br>**Error**".$delVoucher;
					}
				}
				
				//if expenseby is not blanck or 0 then entry is double entry
				if($PaymentDetails[0]['ExpenseBy'] <> "" && $PaymentDetails[0]['ExpenseBy'] <> 0)
				{
					//echo "<br>journal voucher entry";
					$arParentExpenseByDetails = $this->m_objUtility->getParentOfLedger($PaymentDetails[0]['ExpenseBy']);
					if(!(empty($arParentExpenseByDetails)))
					{
						$ExpenseByGroupID = $arParentExpenseByDetails['group'];
						$ExpenseByCategoryID = $arParentExpenseByDetails['category'];
					}
					echo "<br>ExpenseBy:[".$arParentExpenseByDetails['ledger_name']."][".$arParentExpenseByDetails['group_name']."]";
					
					//check for journal voucher
					$JVoucherNo = $resVoucher[0]['VoucherNo']-1;
					$jvoucher_select = "select `id` from `voucher` where `VoucherNo`=".$JVoucherNo." ";
					$VoucherData02 = $this->m_dbConn->select($jvoucher_select);
					
					
					if($VoucherData02 == "")
					{
						//journal voucher does not exist
						echo "<br>Journal Voucher Record not found in voucher[Voucher No:".$JVoucherNo." missing]";
						$delBank2 = "<font color='#FF0000'>DELETE FROM `bankregister` WHERE `id`='".$BankID."'</font> ";
						echo "<br>**Error**".$delBank2;
						$delPayment2 = "<font color='#FF0000'>DELETE FROM `paymentdetails` WHERE `id`='".$PaymentDetails[0]['id']."'</font> ";
						echo "<br>**Error**".$delPayment2;
						$delVoucher2 = "<font color='#FF0000'>DELETE FROM `voucher` where `RefNo`='".$PaymentDetails[0]['id']."' and `RefTableID`='".TABLE_PAYMENT_DETAILS."'</font> ";
						echo "<br>**Error**".$delVoucher2;
						
					}
					else
					{
						//journal voucher exists
						foreach($VoucherData02 as $key => $val)
						{
							array_push($Id_Array2,$VoucherData02[$key]['id']);
						}
						$VoucherIDArray2 = implode(',', $Id_Array2);
						
						//check entry in each register
						$sqlFetchLiability = "SELECT * FROM `liabilityregister` where `VoucherID` IN ($VoucherIDArray2)";
						$resLiability2 = $this->m_dbConn->select($sqlFetchLiability);
						$sqlFetchAsset = "SELECT * FROM `assetregister` where `VoucherID` IN ($VoucherIDArray2)";
						$resAsset2 = $this->m_dbConn->select($sqlFetchAsset);	
						$sqlFetchIncome = "SELECT * FROM `incomeregister` where `VoucherID` IN ($VoucherIDArray2)";
						$resIncome2 = $this->m_dbConn->select($sqlFetchIncome);
						$sqlFetchExpense = "SELECT * FROM `expenseregister` where `VoucherID` IN ($VoucherIDArray2)";
						$resExpense2 = $this->m_dbConn->select($sqlFetchExpense);
						
						//if entry not found in any register
						if($resLiability2 == "" && $resAsset2 == "" && $resExpense2 == "" && $resIncome2 == "")
						{
							echo "<br>Record not found any register";
							$delBank2 = "<font color='#FF0000'>DELETE FROM `bankregister` WHERE `id`='".$BankID."'</font> ";
							echo "<br>**Error**".$delBank2;
							$delPayment2 = "<font color='#FF0000'>DELETE FROM `paymentdetails` WHERE `id`='".$PaymentDetails[0]['id']."'</font> ";
							echo "<br>**Error**".$delPayment2;
							$delVoucher2 = "<font color='#FF0000'>DELETE FROM `voucher` where `VoucherNo`='".$JVoucherNo."' </font> ";
							echo "<br>**Error**".$delVoucher2;
						}
					}
				}
			}
			else
			{
				//if contra entry flag true
				if($this->isDelete == false)
				{
					echo "<br>contra entry";	
				}
			}
		}
	}
	
	public function receiptDetailsChecker($ReceiptDetails,$BankID,$val)
	{
		$isContraEntry = false;	
		$Id_Array = array();
		//check for voucher exists
		$voucher_select = "select `id`,`VoucherNo` from `voucher` where `RefNo`=".$ReceiptDetails[0]['ID']." and `RefTableID`=".TABLE_CHEQUE_DETAILS." ";
		$resVoucher = $this->m_dbConn->select($voucher_select);
		if($resVoucher == "")
		{
			//voucher not found in voucher table
			
			if($this->isDelete == true)
			{
				$delBank = "DELETE FROM `bankregister` WHERE `id`='".$BankID."'";
				$data1 = $this->m_dbConn->delete($delBank);
				$delReceipt = "DELETE FROM `chequeentrydetails` WHERE `ID`='".$ReceiptDetails[0]['ID']."'";
				$data2 = $this->m_dbConn->delete($delReceipt);
			}
			else
			{
				echo "<br>Record not found in voucher[RefNo:".$ReceiptDetails[0]['ID']."  RefTableID:".TABLE_CHEQUE_DETAILS." missing]";
				$delBank = "<font color='#FF0000'>DELETE FROM `bankregister` WHERE `id`='".$BankID."'</font> ";
				echo "<br>**Error**".$delBank;
				$delReceipt = "<font color='#FF0000'>DELETE FROM `chequeentrydetails` WHERE `ID`='".$ReceiptDetails[0]['ID']."'</font> ";
				echo "<br>**Error**".$delReceipt;
				?>
				<script type="text/javascript">											
                $("#result<?php echo $val ?>").removeClass("validEntry").addClass("error");						
                </script>	
                <?php
			}
		}
		else
		{
			//get category id and group id for PaidBy
			$arPaidByParentDetails = $this->m_objUtility->getParentOfLedger($ReceiptDetails[0]['PaidBy']);
			if(!(empty($arPaidByParentDetails)))
			{
				$PaidByGroupID = $arPaidByParentDetails['group'];
				$PaidByCategoryID = $arPaidByParentDetails['category'];
			}
			if($this->isDelete == false)
			{
				echo "<br>PaidBy:[".$arPaidByParentDetails['ledger_name']."][".$arPaidByParentDetails['group_name']."]";
			}
			//get category id and group id for PaidBy
			$arBankIDDetails = $this->m_objUtility->getParentOfLedger($ReceiptDetails[0]['BankID']);
			if(!(empty($arBankIDDetails)))
			{
				$BankGroupID = $arBankIDDetails['group'];
				$BankCategoryID = $arBankIDDetails['category'];
			}
			if($this->isDelete == false)
			{
				echo "<br>Deposite to ledger:[".$arBankIDDetails['ledger_name']."][".$arBankIDDetails['group_name']."]";
			}
			foreach($resVoucher as $key => $val)
			{
				array_push($Id_Array,$resVoucher[$key]['id']);
			}
			$VoucherIDArray = implode(',', $Id_Array);
			/*check condition for contra entry
			if PaidBy is bank account and selected Bank is cash account i.e contra entry
			if PaidBy is bank account and selected Bank is bank account  i.e contra entry
			if PaidBy is cash account and selected Bank is bank account  i.e contra entry
			*/
			if(($PaidByCategoryID == BANK_ACCOUNT || $PaidByCategoryID == CASH_ACCOUNT) && ($BankCategoryID == BANK_ACCOUNT || $BankCategoryID == CASH_ACCOUNT))
			{
				//set flag for contra entry
				$isContraEntry = true;	
			}
			else
			{
				$isContraEntry = false;	
			}
			
			//if entry is not contra then check all register for existence
			if($isContraEntry == false)
			{
				$sqlFetchLiability = "SELECT * FROM `liabilityregister` where `VoucherID` IN ($VoucherIDArray)";
				$resLiability = $this->m_dbConn->select($sqlFetchLiability);
				$sqlFetchAsset = "SELECT * FROM `assetregister` where `VoucherID` IN ($VoucherIDArray)";
				$resAsset = $this->m_dbConn->select($sqlFetchAsset);	
				$sqlFetchIncome = "SELECT * FROM `incomeregister` where `VoucherID` IN ($VoucherIDArray)";
				$resIncome = $this->m_dbConn->select($sqlFetchIncome);
				$sqlFetchExpense = "SELECT * FROM `expenseregister` where `VoucherID` IN ($VoucherIDArray)";
				$resExpense = $this->m_dbConn->select($sqlFetchExpense);
				
				//entry not found in any register
				if($resLiability == "" && $resAsset == "" && $resExpense == "" && $resIncome == "")
				{
					
					if($this->isDelete == true)
					{
						$delBank = "DELETE FROM `bankregister` WHERE `id`='".$BankID."'";
						$data2 = $this->m_dbConn->delete($delBank);
						$delReceipt = "DELETE FROM `chequeentrydetails` WHERE `ID`='".$ReceiptDetails[0]['ID']."'";
						$data2 = $this->m_dbConn->delete($delReceipt);
						$delVoucher = "DELETE FROM  `voucher` where `RefNo`='".$ReceiptDetails[0]['ID']."' and `RefTableID`='".TABLE_CHEQUE_DETAILS."'";
						$data2 = $this->m_dbConn->delete($delVoucher);
					}
					else
					{
						echo "<br>Record not found any register";
						$delBank = "<font color='#FF0000'>DELETE FROM `bankregister` WHERE `id`='".$BankID."'</font> ";
						echo "<br>**Error**".$delBank;
						$delReceipt = "<font color='#FF0000'>DELETE FROM `chequeentrydetails` WHERE `ID`='".$ReceiptDetails[0]['ID']."'</font> ";
						echo "<br>**Error**".$delReceipt;
						$delVoucher = "<font color='#FF0000'>DELETE FROM  `voucher` where `RefNo`='".$ReceiptDetails[0]['ID']."' and `RefTableID`='".TABLE_CHEQUE_DETAILS."'</font> ";
						echo "<br>**Error**".$delVoucher;
						?>
						<script type="text/javascript">											
                        $("#result<?php echo $val ?>").removeClass("validEntry").addClass("error");						
                        </script>	
                        <?php
					}
				}
			}
			else
			{
				if($this->isDelete == false)
				{
					echo "<br>contra entry";
				}
				//check if wrong contra entry exists in any register
				$sqlFetchLiability = "SELECT * FROM `liabilityregister` where `VoucherID` IN ($VoucherIDArray)";
				$resLiability = $this->m_dbConn->select($sqlFetchLiability);
				$sqlFetchAsset = "SELECT * FROM `assetregister` where `VoucherID` IN ($VoucherIDArray)";
				$resAsset = $this->m_dbConn->select($sqlFetchAsset);	
				$sqlFetchIncome = "SELECT * FROM `incomeregister` where `VoucherID` IN ($VoucherIDArray)";
				$resIncome = $this->m_dbConn->select($sqlFetchIncome);
				$sqlFetchExpense = "SELECT * FROM `expenseregister` where `VoucherID` IN ($VoucherIDArray)";
				$resExpense = $this->m_dbConn->select($sqlFetchExpense);
				
				//check if entry found in any register
				if($resLiability == "" || $resAsset == "" || $resExpense == "" || $resIncome == "")
				{
					if($resLiability <> "")
					{
						echo $delBank = "<br><font style='background:#FFFF00;'>**Warning**Invalid Contra Entry Found In Liability Register.</font> ";
					}
					if($resAsset <> "")
					{
						echo $delBank = "<br><font style='background:#FFFF00;'>**Warning**Invalid Contra Entry Found In Asset Register.</font> ";
					}
					if($resExpense <> "")
					{
						echo $delBank = "<br><font style='background:#FFFF00;'>**Warning**Invalid Contra Entry Found In Expense Register.</font> ";
					}
					if($resIncome <> "")
					{
						echo $delBank = "<br><font style='background:#FFFF00;'>**Warning**Invalid Contra Entry Found In Income Register.</font> ";
					}
				}	
			}
		}
	}
	
	public function getVoucherType($voucherTypeID)
	{
		$sql = 'SELECT `type` FROM `vouchertype` where `id` = ' .$voucherTypeID;
		$result = $this->m_dbConn->select($sql);
		return $result; 
	}
	
	public function getRefTableName($voucherID)
	{
		$sql = "SELECT `RefTableID` FROM `voucher` where `id`='".$voucherID."' ";
		$TableName = $this->m_dbConn->select($sql);
		return $TableName[0]['RefTableID']; 	
	}
	
	public function getSocietyDetails()
	{
		//fetch society name
		$sqlFetch = "SELECT `society_name` FROM `society`  where `society_id`='".$_SESSION['society_id']."' ";
		$resBank = $this->m_dbConn->select($sqlFetch);
		return $resBank[0]['society_name'];	
	}
	
	public function getdbBackup()
	{

		try
		{
			if($_SERVER['HTTP_HOST']=="localhost")
			{
				$dbhost = 'localhost';
				$dbuser = 'root';
				$dbpass = '';
				//$dbprefix = 'hostmjbt_society';
			}
			else
			{
				$dbhost = 'localhost';
				$dbuser = 'hostmjbt_society';
				$dbpass = 'society123';
				//$dbprefix = 'hostmjbt_society';
			}	
			
			//$backup_dir_db = $_SERVER['DOCUMENT_ROOT'].'beta/ValidationBackup/db/' . date("Ymd");
			$backup_dir_db = '../ValidationBackup/db/' . date("Ymd");
			if (!file_exists($backup_dir_db)) {
				mkdir($backup_dir_db, 0777, true);
			}
			
			$dbname = $_SESSION['dbname'];
			$backup_file = $backup_dir_db . '/' . $dbname . '_' . date("Y-m-d_H-i-s") . '.sql.gz';
			$command = "mysqldump --opt -h $dbhost -u $dbuser -p$dbpass $dbname | gzip  > " . $backup_file;
			
			//$backup_file = $backup_dir_db . '/' . $dbname . '_' . date("Y-m-d_H-i-s") . '.sql';
			//echo $command = "mysqldump --opt -h $dbhost -u $dbuser -p $dbpass $dbname> " . $backup_file;
			system($command, $retval);
			if($retval == 0)
			{
				return "success";	
			}
			
			//return "success";	
			//echo '<br/> Result : ' . $retval;
			
		}
		catch(Exception $e)
		{
			echo $e;
			return "fail";	
		}
	}
	
	public function checkForValidCategory($arPaidToParentDetails,$PaymentDetails,$VoucherID)
	{
		if($arPaidToParentDetails['group'] == LIABILITY)
		{
			$sql = "select * from `liabilityregister` where `LedgerID` = '".$PaymentDetails[0]['PaidTo']."' and `VoucherID` = '".$VoucherID."'";
		}
		else
		{
			$sql = "select * from `assetregister` where `LedgerID` = '".$PaymentDetails[0]['PaidTo']."' and `VoucherID` = '".$VoucherID."'";
		}
		
		$res = $this->m_dbConn->select($sql);
		if(sizeof($res) > 0 && $arPaidToParentDetails['category'] <> $res[0]['SubCategoryID'])
		{
			echo "<br><font color='#FF0000' >**Error** Invalid Category for ledger found in register.Expected Category Name: [".$arPaidToParentDetails['category_name']."]"."</font>";
			/*$customLeafQuery = "SELECT `CustomLeaf` FROM `chequeleafbook` WHERE `id` = ".$PaymentDetails[0]['ChqLeafID'];				
			$result = $this->m_dbConn->select($customLeafQuery);
			$Url ="";
			if($result[0]['CustomLeaf'] == -1)
			{
				$Url = "PaymentDetails.php?bankid=".$PaymentDetails[0]['PayerBank']."&LeafID=".$PaymentDetails[0]['ChqLeafID']."&edt=".$PaymentDetails[0]['id'];																	
			}
			else
			{
				$Url = "PaymentDetails.php?bankid=".$PaymentDetails[0]['PayerBank']."&LeafID=".$PaymentDetails[0]['ChqLeafID']."&CustomLeaf= ". $result[0]['CustomLeaf']. "&edt=".$PaymentDetails[0]['id'];																	
			}*/
			$link = "ledger.php?edt=".$PaymentDetails[0]['PaidTo'];	
			//echo "<a href='' onClick=\"window.open('". $Url ."','popup','type=fullWindow,fullscreen,scrollbars=yes');\"><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a> <br />";						
			echo "&nbsp;<a href='' onClick=\"window.open('". $link ."','popup','type=fullWindow,fullscreen,scrollbars=yes');\"><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a> <br />";						
		}
	}
	
	public function getChequeNumber($id ,$type)
	{
			if($type == VOUCHER_RECEIPT)
			{
				$sql = "select  `ChequeNumber` from `chequeentrydetails` where `ID` ='".$id."' "; 	
			}
			else
			{
					$sql = "select  `ChequeNumber` from `paymentdetails` where `id` ='".$id."' "; 	
			}
			$res = $this->m_dbConn->select($sql);
			if(sizeof($res) > 0)
			{
				return 	$res[0]['ChequeNumber'];
			}
			return 	'';
	}
	
}
?>