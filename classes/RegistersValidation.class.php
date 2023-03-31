<?php if(!isset($_SESSION)){ session_start(); } ?>
<?php
	include_once("utility.class.php");
	include_once("dbconst.class.php");

	error_reporting(0);	
	
	class RegistersValidation
	{
		public $m_dbConn;
		public $m_objUtility;
		public $isDelete;
		function __construct($dbConn)
		{
			$this->m_dbConn = $dbConn;
			$this->m_objUtility = new utility($dbConn);
			$this->isDelete = false;
		}
		
		public function getSocietyName()
		{
			$sql = "SELECT `society_name` FROM `society` WHERE `society_id` = '".$_SESSION['society_id']."'";			
			$result = $this->m_dbConn->select($sql);
			return $result[0]['society_name'];
		}
		
		public function getLedgerName($lid)
		{
			$sql = "SELECT `ledger_name` FROM `ledger` WHERE `id` = '".$lid."'";			
			$result = $this->m_dbConn->select($sql);
			return $result[0]['ledger_name'];
		}	
		
		public function ValidateRegisterEntries($tableName)
		{
			$groupName = '';
			echo "<br><font color='#0000FF'  style='font-size:20px;'>"."Validating  ".$tableName." Entries</font><br><br>";
			$sqlRegister = '';
			$GroupID = 0;
			if($tableName == 'liabilityregister')
			{
				$GroupID = 1;
			}
			else if($tableName == 'assetregister')
			{
				$GroupID = 2;
			}
			else if($tableName == 'incomeregister')
			{
				$GroupID = 3;
			}
			else if($tableName == 'expenseregister')
			{
				$GroupID = 4;
			}
			
			if(isset($_POST["method"]) && $_POST["method"] == "run" && $_POST["cleanInvalidEntries"] == "YES")
			{
				$this->isDelete = true;		
			}
			$sqlFetch = '';
			$sqlFetch =  " select * from `" . $tableName. "` ";
			if($tableName == 'liabilityregister' || $tableName == 'assetregister' )
			{
				$sqlFetch .= " where `Is_Opening_Balance` = 0 ";		
			}
			if($this->isDelete == false && isset($_REQUEST['developer']) )
			{
				echo '<br>'.$sqlFetch;
			}
			$result = $this->m_dbConn->select($sqlFetch);
			$isError = false;
			for($i = 0; $i < sizeof($result); $i++)
			{
				$isError = false;
				$amount = 0;
				
				if($result[$i]['Credit'] <> 0)
				{
					$amount = $result[$i]['Credit'];
				}
				else if($result[$i]['Debit'] <> 0)
				{
					$amount = $result[$i]['Debit'];
				}
				
				$arPaidToParentDetails = $this->m_objUtility->getParentOfLedger($result[$i]['LedgerID']);
				$PaidToGroupID = "";
				
				//get group of individual ledger
				if(!(empty($arPaidToParentDetails)))
				{
					$PaidToGroupID = $arPaidToParentDetails['group'];
					$PaidToCategoryID = $arPaidToParentDetails['category'];
					$PaidToCategoryName = $arPaidToParentDetails['category_name'];
					$PaidToLedgerName = $arPaidToParentDetails['ledger_name'];
					
					if($PaidToGroupID == LIABILITY)
					{
						$groupName = 'Liability';
					}
					else if($PaidToGroupID == ASSET)
					{
						$groupName = 'Asset';	
					}
					else if($PaidToGroupID == INCOME)
					{
						$groupName = 'Income';	
					}
					else if($PaidToGroupID == EXPENSE)
					{
						$groupName = 'Expense';	
					}
					
					if($PaidToGroupID == ASSET  && ($PaidToCategoryID == BANK_ACCOUNT || $PaidToCategoryID == CASH_ACCOUNT))
					{
						$isError = true;
						//bank or cash account entries are in assettable
						
						$sqlAssetRegisterI = " delete *  from `" . $tableName. "` where `LedgerID` = '".$result[$i]['LedgerID']."'	";
						echo "<br><font color='#FF0000' >**Error**Invalid Bank Entry In Register</font>";
						$url ="view_ledger_details.php?lid=".$result[$i]['LedgerID']."&gid=".$GroupID;
						echo $Url =	"&nbsp;&nbsp;<a href='' onClick=\"window.open('". $url ."','ViewLedgerPopup','type=fullWindow,fullscreen,scrollbars=yes');\"><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a>";	
					}
				}
				
				if($GroupID <> 0 && $PaidToGroupID <> "" && $GroupID <> $PaidToGroupID)
				{
					$isError = true;
					//entry in register in not of register type entry
					$sqlRegisterI = " select *  from `" . $tableName. "` where `VoucherID` = '".$result[$i]['VoucherID']."'	";
					
					if($this->isDelete == false)
					{
						echo "<br><font color='#FF0000' >**Error**Invalid Ledger Entry In ".$tableName."</font>";
						$url ="view_ledger_details.php?lid=".$result[$i]['LedgerID']."&gid=".$GroupID;
						echo $Url =	"&nbsp;&nbsp;<a href='' onClick=\"window.open('". $url ."','ViewLedgerPopup','type=fullWindow,fullscreen,scrollbars=yes');\"><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a>";	
						 if(isset($_REQUEST['developer']))
						 {
							echo "<br><font color='#FF0000' >query".$sqlRegisterI.'</font>';	
						 }
					}
					
						
				}
				if(($PaidToGroupID == LIABILITY || $PaidToGroupID == ASSET) && $PaidToCategoryID <>  $result[$i]['SubCategoryID'])
				{ 
					$isError = true;
					if($tableName == 'liabilityregister' || $tableName == 'assetregister' )
					{
						echo "<br><font color='#FF0000' >**Error** Invalid Category for ledger found in register.Expected Category Name: [".$PaidToCategoryName."]"."</font>";	
						$link = "ledger.php?edt=".$result[$i]['LedgerID'];	
						echo "&nbsp;<a href='' onClick=\"window.open('". $link ."','popup','type=fullWindow,fullscreen,scrollbars=yes');\"><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a> <br />";						
					}
				}
				
				$sqlYear ='SELECT * FROM `year`';
				$resultYear = $this->m_dbConn->select($sqlYear);
				
				if($result[$i]['Date'] < $resultYear[0]['BeginingDate'])
				{
					$isError = true;
					if($this->isDelete == false)
					{
						echo "<br><font color='#FF0000' >**Error**Entry Date is wrong or invalid</font>";
						echo "<hr>";
					}		
					
				}
				
				//else if($GroupID == $PaidToGroupID)
				{
				
					if($result[$i]['VoucherID'] <> '' && $result[$i]['VoucherID'] > 0)
					{
						
						$sqlVoucher = "select * from `voucher` where `id` = '".$result[$i]['VoucherID']."' ";
						$resultVoucher = $this->m_dbConn->select($sqlVoucher);
						
						if(sizeof($resultVoucher) == 0)
						{
							$isError = true;
							//voucher id not found for register entry
							if($this->isDelete == false)
							{
								echo "<br><font color='#FF0000' >**Error** Voucher Does Not Exists For This Entry</font>";	
							}
							$sqlRegister = " delete from `" . $tableName. "` where `VoucherID` = '".$result[$i]['VoucherID']."'	";
							if($this->isDelete == true)
							{
								$this->m_dbConn->delete($sqlRegister);
							}
							else if(isset($_REQUEST['developer']))
							{
								echo "<br><font color='#FF0000' >query".$sqlRegister.'</font>';	
							}
						}	
					}
				}
				if($isError == true)
				{
					echo "<br>LedgerName:".$this->getLedgerName($result[$i]['LedgerID']);
					echo "<br>Group:"." [ ".$groupName." ]";
					//echo "<br>Category ID:" . $PaidToCategoryName;
					if(isset($_REQUEST['developer']))
					{
						echo "<br>Entry Details:" . implode(" :: ",$result[$i]);
					}
					else
					{
						echo "<br>Date:" .getDisplayFormatDate($result[$i]['Date']);	
						echo "<br>Amount:" .number_format($amount,2);		
					}	
					if($this->isDelete == false)
					{
						echo "<hr>";
					}
				}
				
			}
			
	
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
			}
			else
			{
				$dbhost = 'localhost';
				$dbuser = 'hostmjbt_society';
				$dbpass = 'society123';
			}	
			
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
			
			
		}
		catch(Exception $e)
		{
			echo $e;
			return "fail";	
		}
	}
		
	function getLedgerNameArray()
	{
		$arr = array();
		$ledQuery = 'SELECT `id`, `ledger_name` FROM `ledger`';
		$res = $this->m_dbConn->select($ledQuery);						
		for($i = 0; $i < sizeof($res); $i++)
		{			
			$arr[$res[$i]['id']]= $res[$i]['ledger_name'];			
		}
		return $arr;
	}
	
	
	public function CheckVoucherType($VoucherID)
{
	$Url = '';
	
	$sql03 = "select `VoucherNo` from `voucher` where `id` = '".$VoucherID."' ";
	$data03 = $this->m_dbConn->select($sql03);
	
	$sql = "select * from `voucher` where `VoucherNo` = '".$data03[0]['VoucherNo']."' ";
	$data = $this->m_dbConn->select($sql);
	if($data <> "")
	{
		//check if jv exists in payment by voucher id
		$checkPaymentEntry = "select * from `paymentdetails` where `VoucherID` = '".$data[0]['id']."' ";
		$res2 = $this->m_dbConn->select($checkPaymentEntry);
		
		if(sizeof($res2) > 0)
		{
			//jv exists in payment means jv is of payment type
			$sql01 = "select * from `voucher` where `RefNo` = '".$res2[0]['id']."' and `RefTableID` = '3'  and `By` > 0 ";
			$res01 = $this->m_dbConn->select($sql01);
			
			$customLeafQuery = "SELECT `CustomLeaf` FROM `chequeleafbook` WHERE `id` = ".$res2[0]['ChqLeafID'];				
			$result = $this->m_dbConn->select($customLeafQuery);
			
			if($result[0]['CustomLeaf'] == -1)
			{
				$Url = "PaymentDetails.php?bankid=".$res01[0]['By']."&LeafID=".$res2[0]['ChqLeafID']."&edt=".$res2[0]['id'];																	
			}
			else
			{
				$Url = "PaymentDetails.php?bankid=".$res01[0]['By']."&LeafID=".$res2[0]['ChqLeafID']."&CustomLeaf= ". $result[0]['CustomLeaf']. "&edt=".$res2[0]['id'];																	
			}
			//echo $Url;
			return $Url;	
		}
		else
		{
			$checkPaymentEntry = "select * from `paymentdetails` where `Amount` = '".$data[1]['Credit']."' and `PaidTo` = '".$data[1]['To']."' and `InvoiceDate` = '".$data[1]['Date']."' ";
			$res3 = $this->m_dbConn->select($checkPaymentEntry);
			if(sizeof($res3) > 1)
			{
				//multiple entries fetched
				for($i=0; $i < sizeof($res3); $i++)
				{
					//search payment id in voucher
					$sql2 = "select * from `voucher` where `RefNo` = '".$res3[$i]['id']."' and `RefTableID` = '3' ";
					$res4 = $this->m_dbConn->select($sql2); 
					if(sizeof($res4) > 1)
					{
						if($res4[0]['VoucherNo'] == $data[0]['VoucherNo'] + 1)
						{
							//jv voucherno and payment voucher number match
							$customLeafQuery = "SELECT `CustomLeaf` FROM `chequeleafbook` WHERE `id` = ".$res3[$i]['ChqLeafID'];				
							$result = $this->m_dbConn->select($customLeafQuery);
							
							if($result[0]['CustomLeaf'] == -1)
							{
								$Url = "PaymentDetails.php?bankid=".$res4[0]['By']."&LeafID=".$res3[$i]['ChqLeafID']."&edt=".$res3[$i]['id'];																	
							}
							else
							{
								$Url = "PaymentDetails.php?bankid=".$res4[0]['By']."&LeafID=".$res3[$i]['ChqLeafID']."&CustomLeaf= ". $result[0]['CustomLeaf']. "&edt=".$res3[$i]['id'];																	
							}
							//echo $Url;
							return $Url;	
						}
						else
						{
							//no record found	
							//echo "test";
						}	
					}	
				}	
			}
			else if(sizeof($res3) == 1)
			{
				//one entry fetched	
				$sql2 = "select * from `voucher` where `RefNo` = '".$res3[0]['id']."' and `RefTableID` = '3' ";
				$res04 = $this->m_dbConn->select($sql2); 
				
				$customLeafQuery = "SELECT `CustomLeaf` FROM `chequeleafbook` WHERE `id` = ".$res3[0]['ChqLeafID'];				
				$result = $this->m_dbConn->select($customLeafQuery);
				
				if($result[0]['CustomLeaf'] == -1)
				{
					$Url = "PaymentDetails.php?bankid=".$res04[0]['By']."&LeafID=".$res3[0]['ChqLeafID']."&edt=".$res3[0]['id'];																	
				}
				else
				{
					$Url = "PaymentDetails.php?bankid=".$res04[0]['By']."&LeafID=".$res3[0]['ChqLeafID']."&CustomLeaf= ". $result[0]['CustomLeaf']. "&edt=".$res3[0]['id'];																	
				}
				//echo $Url;
				return $Url;	
			}
			else
			{
				//no record found		
			}
				
		}
	}
	
	return '';
}

		
	}
	
	
	
?>

