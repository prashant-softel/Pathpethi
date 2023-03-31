<?php
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once("utility.class.php");
include_once("dbconst.class.php");
include_once("register.class.php");

class account_subcategory extends dbop
{
	public $actionPage = "../ledger.php";
	public $m_dbConn;
	private $obj_utility;
	private $obj_register;
	public $isCatError;
	public $isLedgerExits;
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);

		/*//$this->curdate		= $this->display_pg->curdate();
		//$this->curdate_show	= $this->display_pg->curdate_show();
		//$this->curdate_time	= $this->display_pg->curdate_time();
		//$this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);*/

		$this->obj_utility = new utility($this->m_dbConn);
		$this->obj_register = new regiser($this->m_dbConn);
		$this->isCatError = false;
		$this->isLedgerExits = false;
		////dbop::__construct();
	}

	public function startProcess()
	{
		$errorExists = 0;

		/*//$curdate 		=  $this->curdate;
		//$curdate_show	=  $this->curdate_show;
		//$curdate_time	=  $this->curdate_time;
		//$ip_location	=  $this->ip_location;*/

		if($_REQUEST['insert']=='Insert' && $errorExists==0)
		{	
			if($_POST['society_id']<>"" && $_POST['categoryid']<>"" && $_POST['ledger_name']<>"" &&  $_POST['opening_type'] > 0 ) 
			{
				if($_SESSION['society_creation_yearid'] <> "")
				{
					//$OpeningBalanceDate = $this->obj_utility->GetDateByOffset($_SESSION['default_year_start_date'] , -1);
					$OpeningBalanceDate = $this->obj_utility->GetDateByOffset($this->obj_utility->getCurrentYearBeginingDate($_SESSION['society_creation_yearid']) , -1);
					if($OpeningBalanceDate <> "")
					{
						$_POST['balance_date'] = $OpeningBalanceDate;		
					}		
				}
				
				$aryParent = $this->obj_utility->getParentOfCategory($_POST['categoryid']);
				
				$count_query = "select count(ledger_name) as count from ledger where ledger_name='".$_POST['ledger_name']."' and society_id='" . $_SESSION['society_id'] . "'";	
				$res = $this->m_dbConn->select($count_query);
				//echo $res[0]['count'];
				if($res[0]['count'] <= 0)
				{
					$openingBalance = abs($_POST['opening_balance']);
					$insert_query="insert into ledger (`society_id`,`categoryid`,`ledger_name`,`show_in_bill`,`supplementary_bill`,`taxable`,`sale`,`purchase`,`income`,`expense`,`payment`,`receipt`,`taxable_no_threshold`,`opening_type`,`opening_balance`,`note`,`opening_date`) values ('".$_POST['society_id']."','".$_POST['categoryid']."','".$_POST['ledger_name']."','".$_POST['show_in_bill']."','".$_POST["supplementary_bill"]."','".$_POST['taxable']."','".$_POST['sale']."','".$_POST['purchase']."','".$_POST['income']."','".$_POST['expense']."','".$_POST['payment']."','".$_POST['receipt']."','".$_POST['nothreshold']."','".$_POST['opening_type']."','".$openingBalance."','".$_POST['note']."','".getDBFormatDate($_POST['balance_date'])."')";			
					$ledgerID = $this->m_dbConn->insert($insert_query);
					//echo '<br>ParentID : ' . $aryParent['group'] . ' Type : ' . ASSET;
					
					if($aryParent['group'] == LIABILITY)
					{
						if($_POST['opening_type']==1)
						{
							$insertLiability = $this->obj_register->SetLiabilityRegister(getDBFormatDate($_POST['balance_date']), $ledgerID, 0, 0, TRANSACTION_CREDIT, $openingBalance, 1);
						}
						else
						{
							$insertLiability = $this->obj_register->SetLiabilityRegister(getDBFormatDate($_POST['balance_date']), $ledgerID, 0, 0, TRANSACTION_DEBIT, $openingBalance , 1);
						}
						
					}
					else if($_POST['categoryid'] == BANK_ACCOUNT || $_POST['categoryid'] == CASH_ACCOUNT)
					{
						$insertBank = $this->obj_register->SetBankRegister(getDBFormatDate($_POST['balance_date']), $ledgerID, 0, 0, TRANSACTION_RECEIVED_AMOUNT, $openingBalance, 0, 0, 1);
						
						$insertBank = $insert_query="insert into bank_master (`BankID`, `BankName`) values ('" . $ledgerID . "', '".$_POST['ledger_name']."')";
						$sqlInsertResult = $this->m_dbConn->insert($insertBank);
					}
					else if($aryParent['group'] == ASSET)
					{
						if($_POST['opening_type']==1)
						{
							$insertAsset = $this->obj_register->SetAssetRegister(getDBFormatDate($_POST['balance_date']), $ledgerID, 0, 0, TRANSACTION_CREDIT, $openingBalance, 1);
						}
						else
						{
							$insertAsset = $this->obj_register->SetAssetRegister(getDBFormatDate($_POST['balance_date']), $ledgerID, 0, 0, TRANSACTION_DEBIT, $openingBalance, 1);
						}
					}				
					
					if($aryParent['group'] == LIABILITY || $aryParent['group'] == ASSET)
					{
					
						$insertQuery="Insert into `ledger_details` (`LedgerID`,`GSTIN_No`,`PAN_No`,`TDS_NatureOfPayment`,`TDS_Ded_rate`) values('".$ledgerID."','".strtoupper($_POST['GSTIN_No'])."','".strtoupper($_POST['Pan_no'])."','".$_POST['natureOfPayment']."','".$_POST['nature_rate']."' )";
						$ledgerDetails = $this->m_dbConn->insert($insertQuery);
					}
					
					return "Insert";
				}
				else
				{
					$this->isLedgerExits = true;
					return "Ledger already exist";		
				}
			}
			else
			{
									
				return "All * Field Required";											
			}
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			if($_POST['society_id']<> "" && $_POST['categoryid']<>"" && $_POST['ledger_name']<>"" && $_POST['opening_type'] > 0 ) 
			{
					/*if($_SESSION['default_year_start_date'] <> "")
					{
						$OpeningBalanceDate = $this->obj_utility->GetDateByOffset($_SESSION['default_year_start_date'] ,-1);
						if($OpeningBalanceDate <> "")
						{
							$_POST['balance_date'] = $OpeningBalanceDate;		
						}		
					}*/
					$aryParent = $this->obj_utility->getParentOfCategory($_POST['categoryid']);
					if($aryParent['group'] == LIABILITY || $aryParent['group'] == ASSET)
					{
						if($_POST['balance_date'] == '')
						{
							return "All * Field Required";											
						}
					}
					$openingBalance = abs($_POST['opening_balance']);
					
					$selectQuery = 'SELECT cat.category_name,led.`categoryid`,led.`ledger_name`,led.`show_in_bill`,led.`taxable`,led.`sale`,led.`purchase`,
									led.`income`,led.`expense`,led.`payment`,led.`receipt`,led.`opening_type`,led.`opening_balance`,led.`status` FROM `ledger` 
									AS led JOIN `account_category` AS cat ON led.categoryid = cat.category_id WHERE led.`id` = "'.$_POST['id'].'"';
					$prevCategoryID = $this->m_dbConn->select($selectQuery);
					
					$prevAryParent = $this->obj_utility->getParentOfCategory($prevCategoryID[0]['categoryid']);					
					
					$logMsg .= "<br />Original Record : ";
					$logMsg .= implode(' || ', array_map(function ($entry) 
					{
						return $_POST['id'] . " | " . $entry['categoryid'] . " | " . $entry['ledger_name'] . " | " . $entry['show_in_bill'] . " | " . $entry['taxable']
							. " | " . $entry['sale'] . " | " . $entry['purchase'] . " | " . $entry['income'] . " | " . $entry['expense'] . " | " . $entry['payment']
							. " | " . $entry['receipt'] . " | " . $entry['opening_type'] . " | " . $entry['opening_balance'] . " | " . $entry['status'];
					}, $prevCategoryID));
					
					if($prevAryParent['group'] == $aryParent['group'])
					{
						try
						{
							$this->m_dbConn->begin_transaction();
							if($aryParent['group'] == ASSET)
							{						
								$isCatIDBank = false;
								$isPrevCatIDBank = false;
								if($_POST['categoryid'] == BANK_ACCOUNT || $_POST['categoryid'] == CASH_ACCOUNT)
								{
									$isCatIDBank = true;
								}
								if($prevCategoryID[0]['categoryid'] == BANK_ACCOUNT || $prevCategoryID[0]['categoryid'] == CASH_ACCOUNT)
								{
									$isPrevCatIDBank = true;
								}
								if( $isCatIDBank == true && $isPrevCatIDBank == false )
								{
									$assetregCountQuery = "SELECT Count(*) as cnt from `assetregister` WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance != 1";							
									$assetregCount = $this->m_dbConn->select($assetregCountQuery);
									if($assetregCount[0]['cnt'] > 0)
									{
										$this->isCatError = true;
										return "Ledger<".$prevCategoryID[0]['ledger_name']."> is associated with category<".$prevCategoryID[0]['category_name'].">. Changing it to Bank/Cash account is restricted.";
									}
								}
								else if( $isCatIDBank == false && $isPrevCatIDBank == true )
								{							
									$bankregCountQuery = "SELECT Count(*) as cnt from `bankregister` WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance != 1";							
									$bankregCnt = $this->m_dbConn->select($bankregCountQuery);							
									if($bankregCnt[0]['cnt'] > 0)
									{
										$this->isCatError = true;
										return "Ledger<".$prevCategoryID[0]['ledger_name']."> is associated with Bank/Cash account. Changing of Category ID is restricted.";								
									}
								}
								else if(($_POST['categoryid'] == BANK_ACCOUNT && $prevCategoryID[0]['categoryid'] == CASH_ACCOUNT) || ($_POST['categoryid'] == CASH_ACCOUNT && $prevCategoryID[0]['categoryid'] == BANK_ACCOUNT))
								{
									$bankregCountQuery = "SELECT Count(*) as cnt from `bankregister` WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance != 1";							
									$bankregCnt = $this->m_dbConn->select($bankregCountQuery);							
									if($bankregCnt[0]['cnt'] > 0)
									{
										$this->isCatError = true;
										return "Ledger<".$prevCategoryID[0]['ledger_name']."> is associated with category<".$prevCategoryID[0]['category_name'].">. Changing of Category ID is restricted.";
									}
								}
							}
							$this->m_dbConn->commit();
						}
						catch(Exception $exp)
						{
							$this->m_dbConn->rollback();
							return $exp;
						}

					}
					else
					{
						try
						{
							$this->m_dbConn->begin_transaction();
							$errorMsg = $this->moveRegEntriesAccordingToGroupChange($_POST['id'],$prevAryParent['group'],$aryParent['group'],$prevCategoryID[0]['categoryid'],$_POST['categoryid']);
							$this->m_dbConn->commit();
							if($errorMsg == "Changing of GroupID is Not Allowed")
							{
								$this->isCatError = true;
								return "Ledger<".$prevCategoryID[0]['ledger_name']."> is associated with Bank/Cash account. Changing of Group ID is restricted.";
							}
							else if($errorMsg == "Not Allowed")
							{
								$this->isCatError = true;
								return "Ledger<".$prevCategoryID[0]['ledger_name']."> is associated with group<".$prevAryParent['groupname'].">. Changing it to Bank/Cash account is restricted.";	
							}							
						}
						catch(Exception $exp)
						{
							$this->m_dbConn->rollback();
							return $exp;
						}
					}
					
					$up_query="update ledger set `society_id`='".$_POST['society_id']."',`categoryid`='".$_POST['categoryid']."',`ledger_name`='".$_POST['ledger_name']."',`show_in_bill`='".$_POST['show_in_bill']."',`taxable`='".$_POST['taxable']."',`sale`='".$_POST['sale']."',`purchase`='".$_POST['purchase']."',`income`='".$_POST['income']."',`expense`='".$_POST['expense']."',`payment`='".$_POST['payment']."',`receipt`='".$_POST['receipt']."',`taxable_no_threshold` = '".$_POST['nothreshold']."',`opening_type`='".$_POST['opening_type']."',`opening_balance`='".$openingBalance."',`note`='".$_POST['note']."',`opening_date` ='".getDBFormatDate($_POST['balance_date'])."',`supplementary_bill`='".$_POST['supplementary_bill']."' where id='".$_POST['id']."'";
					$data = $this->m_dbConn->update($up_query);
					
					$aryParentLedger = $this->obj_utility->getParentOfLedger($_POST['id']);
					
					if($aryParent['group'] == LIABILITY)
					{
						$selectLiability = "SELECT Count(*) as cnt from `liabilityregister` WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
						$dataSelectLiability = $this->m_dbConn->select($selectLiability);
						
						$deleteAsset = "DELETE FROM `assetregister` WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
						$dataDeleteAsset = $this->m_dbConn->delete($deleteAsset);
						
						$deleteBank = "DELETE FROM `bankregister` WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
						$dataDeleteBank = $this->m_dbConn->delete($deleteBank);
						
						if($dataSelectLiability[0]['cnt'] > 0)
						{
							if($_POST['opening_type']==1)
							{
								$updateLiability = "UPDATE `liabilityregister` SET `Date`='" . getDBFormatDate($_POST['balance_date']) . "',`CategoryID`='".$aryParentLedger['group']."',`SubCategoryID`='" . $aryParentLedger['category'] . "' ,`Credit`='" . $openingBalance . "',`Debit`=0  WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
							}
							else
							{
								$updateLiability = "UPDATE `liabilityregister` SET `Date`='" . getDBFormatDate($_POST['balance_date']) . "',`CategoryID`='".$aryParentLedger['group']."',`SubCategoryID`='" . $aryParentLedger['category'] . "' ,`Debit`='" . $openingBalance . "',`Credit`=0  WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";	
							}
							$dataLiability = $this->m_dbConn->update($updateLiability);
							
							$updateLiability2 = "UPDATE `liabilityregister` SET  `CategoryID`='".$aryParentLedger['group']."',`SubCategoryID`='" . $aryParentLedger['category'] . "'   WHERE LedgerID = '" . $_POST['id'] . "' ";
							$dataLiability2 = $this->m_dbConn->update($updateLiability2);
						}
						else
						{
							if($_POST['opening_type']==1)
							{
								$insertLiability = $this->obj_register->SetLiabilityRegister(getDBFormatDate($_POST['balance_date']), $_POST['id'], 0, 0, TRANSACTION_CREDIT, $openingBalance, 1);
							}
							else
							{
								$insertLiability = $this->obj_register->SetLiabilityRegister(getDBFormatDate($_POST['balance_date']), $_POST['id'], 0, 0, TRANSACTION_DEBIT, $openingBalance, 1);
							}
						}
					}
					else if($_POST['categoryid'] == BANK_ACCOUNT || $_POST['categoryid'] == CASH_ACCOUNT)
					{
						$deleteAsset = "DELETE FROM `assetregister` WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
						$dataDeleteAsset = $this->m_dbConn->delete($deleteAsset);
						
						$deleteLiability = "DELETE FROM `liabilityregister` WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
						$dataDeleteLiability = $this->m_dbConn->delete($deleteLiability);
						
						$selectAccount = "SELECT Count(*) as cnt from `bankregister` WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
						$dataSelectAccount = $this->m_dbConn->select($selectAccount);
						if($dataSelectAccount[0]['cnt'] > 0)
						{
							$updateBank = "UPDATE `bankregister` SET `Date`= '" . getDBFormatDate($_POST['balance_date']) . "', `ReceivedAmount`='" . $openingBalance . "' WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
							$dataBank = $this->m_dbConn->update($updateBank);
						}
						else
						{
							$insertBank = $this->obj_register->SetBankRegister(getDBFormatDate($_POST['balance_date']), $_POST['id'], 0, 0, TRANSACTION_RECEIVED_AMOUNT, $openingBalance, 0, 0, 1);	
							$insertBankMaster ="insert into bank_master(`BankID`, `BankName`) values ('" . $_POST['id'] . "', '".$_POST['ledger_name']."')";
							$sqlInsertResult = $this->m_dbConn->insert($insertBankMaster);
						}
					}
					else if($aryParent['group'] == ASSET)
					{
						$selectAsset = "SELECT Count(*) as cnt from `assetregister` WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
						$dataSelectAsset = $this->m_dbConn->select($selectAsset);
						
						$deleteLiability = "DELETE FROM `liabilityregister` WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
						$dataDeleteLiability = $this->m_dbConn->delete($deleteLiability);
						
						$deleteBank = "DELETE FROM `bankregister` WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
						$dataDeleteBank = $this->m_dbConn->delete($deleteBank);
						
						if($dataSelectAsset[0]['cnt'] > 0)
						{
							if($_POST['opening_type']==1)
							{
								
								$updateAsset = "UPDATE `assetregister` SET `Date`='" . getDBFormatDate($_POST['balance_date']) . "',`CategoryID`='".$aryParentLedger['group']."',`SubCategoryID`='" . $aryParentLedger['category'] . "' ,`Credit`='" . $openingBalance . "',`Debit`=0  WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
							}
							else
							{
								$updateAsset = "UPDATE `assetregister` SET `Date`='" . getDBFormatDate($_POST['balance_date']) . "',`CategoryID`='".$aryParentLedger['group']."',`SubCategoryID`='" . $aryParentLedger['category'] . "' ,`Debit`='" . $openingBalance . "',`Credit`=0  WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
							}
							$dataAsset = $this->m_dbConn->update($updateAsset);
							
							$updateAsset2 = "UPDATE `assetregister` SET  `CategoryID`='".$aryParentLedger['group']."',`SubCategoryID`='" . $aryParentLedger['category'] . "'   WHERE LedgerID = '" . $_POST['id'] . "'  ";
							$dataAsset2 = $this->m_dbConn->update($updateAsset2);
						}
						else
						{
							if($_POST['opening_type']==1)
							{
								$insertAsset = $this->obj_register->SetAssetRegister(getDBFormatDate($_POST['balance_date']), $_POST['id'], 0, 0, TRANSACTION_CREDIT, $openingBalance, 1);
							}
							else
							{
								$insertAsset = $this->obj_register->SetAssetRegister(getDBFormatDate($_POST['balance_date']), $_POST['id'], 0, 0, TRANSACTION_DEBIT, $openingBalance, 1);					//echo $insertAsset;	
							}	
							
						}
					}
					else
					{
						//if group type income or expense
						$deleteLiability = "DELETE FROM `liabilityregister` WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
						$dataDeleteLiability = $this->m_dbConn->delete($deleteLiability);
						
						$deleteAsset = "DELETE FROM `assetregister` WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
						$dataDeleteAsset = $this->m_dbConn->delete($deleteAsset);
						
						$deleteBank = "DELETE FROM `bankregister` WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
						$dataDeleteBank = $this->m_dbConn->delete($deleteBank);
					}
					
					$selectQuery = 'SELECT * FROM `ledger` WHERE `id` = "'.$_POST['id'].'"';
					$res = $this->m_dbConn->select($selectQuery);
					$logMsg .= "<br />Updated Record : ";
					$logMsg .= implode(' || ', array_map(function ($entry) 
					{
						return $_POST['id'] . " | " . $entry['categoryid'] . " | " . $entry['ledger_name'] . " | " . $entry['show_in_bill'] . " | " . $entry['taxable']
							. " | " . $entry['sale'] . " | " . $entry['purchase'] . " | " . $entry['income'] . " | " . $entry['expense'] . " | " . $entry['payment']
							. " | " . $entry['receipt'] . " | " . $entry['opening_type'] . " | " . $entry['opening_balance'] . " | " . $entry['status'];
					}, $res));
					
					$insertQuery = "INSERT INTO `change_log`(`ChangedLogDec`, `ChangedBy`, `ChangedTable`, `ChangedKey`) VALUES ('" . $this->m_dbConn->escapeString($logMsg) . "','".$_SESSION['login_id']."','ledger','".$_POST['id']."')";										
					$this->m_dbConn->insert($insertQuery);											
					
					if($aryParent['group'] == LIABILITY || $aryParent['group'] == ASSET)
					{
						$selquery="Select * from ledger_details where LedgerID='".$_POST['id']."'";
						$dataGSTIN=$this->m_dbConn->select($selquery);	
						if($dataGSTIN <> '')
						{
						$UpdateQuery="UPDATE `ledger_details` SET `GSTIN_No`='".strtoupper($_POST['GSTIN_No'])."',PAN_No='".strtoupper($_POST['Pan_no'])."',TDS_NatureOfPayment='".$_POST['natureOfPayment']."',TDS_Ded_rate='".$_POST['nature_rate']."' where LedgerID='".$_POST['id']."'";
						$ledgerDetails = $this->m_dbConn->update($UpdateQuery);
						}
						else
						{
							$insertQuery="Insert into `ledger_details` (`LedgerID`,`GSTIN_No`,`PAN_No`,`TDS_NatureOfPayment`,`TDS_Ded_rate`) values('".$_POST['id']."','".strtoupper($_POST['GSTIN_No'])."','".strtoupper($_POST['Pan_no'])."','".$_POST['natureOfPayment']."','".$_POST['nature_rate']."')";
						$ledgerDetails = $this->m_dbConn->insert($insertQuery);
						}
					}
					
					if(isset($_POST['edit']) && $_POST['edit'] <> "")
					{		
						return "LedgerUpdate";
						
					}
					else
					{
						return "Update";	
					}
			}
			else
			{
									
				return "All * Field Required";											
			}
		}
		else
		{
			return $errString;
		}
	}
	public function combobox($query, $id, $defaultString = '', $defaultValue = '')
	{
		if($defaultString <> '')
		{		
			$str.="<option value='" . $defaultValue . "'>" . $defaultString . "</option>";
		}
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
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
						$str.=$v."</OPTION>";
					}
					$i++;
				}
			}
		}
			return $str;
	}
	public function combobox1($query, $id )
	{
		
		
			$str.="<option value='0'>Please Select</option>";
		
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			$vowels = array('/',  '.', '*', '%', '&', ',', '"');
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
	
	public function display1($rsas)
	{
		/*$thheader = array('Category','Ledger','Opening Balance','Type','Show_In_Bill','Taxable','Sale','Purchase','Income','Expense','Payment','Receipt','Note','Opening Date');*/
		$thheader = array('Category','Ledger','GSTIN No','Opening Balance','Type','Show_In_Bill','Note','Opening Date');
		$this->display_pg->edit		= "getaccount_subcategory";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "ledger.php";
		$this->display_pg->view		= "getaccount_subcategory";
		$this->display_pg->print		= "getaccount_subcategory";
		//$res = $this->display_pg->display_new($rsas);
		$ShowEdit = false;
		if($_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1){
			
			$ShowEdit = true;
			
			}
		$res = $this->display_pg->display_datatable($rsas, $ShowEdit /*Show Edit Option*/, false /*Hide Delete Option*/,true /*Show View Option*/,true);
		return $res;
	}
	
	public function pgnation()
	{
		//$sql1 = "select ledger_table.id,ledger_table.society_id,Account.category_name,ledger_table.ledger_name,ledger_table.show_in_bill,ledger_table.taxable,ledger_table.sale,ledger_table.purchase,ledger_table.income,ledger_table.expense,ledger_table.payment,ledger_table.receipt,ledger_table.opening_type,ledger_table.opening_balance,ledger_table.note from `account_category` as `Account` Join `ledger` as `ledger_table` where Account.category_id = ledger_table.categoryid";
		
		/*$sql1 = "select ledger_table.id,CONCAT(Account.category_name,CONCAT('(',Account.category_id,')')) as 'Category Name',CONCAT(ledger_table.ledger_name, CONCAT('(',ledger_table.id,')')) as 'ledger',ledger_table.opening_balance,IF(ledger_table.opening_type = '0', 'None',IF(ledger_table.opening_type = '1', 'Credit',IF(ledger_table.opening_type = '2', 'Debit',''))) as opening_type, ledger_table.show_in_bill,ledger_table.taxable,ledger_table.sale,ledger_table.purchase,ledger_table.income,ledger_table.expense,ledger_table.payment,ledger_table.receipt,ledger_table.note,DATE(DATE_ADD(ledger_table.opening_date, INTERVAL 1 DAY)) as opening_date from `account_category` as `Account`,`ledger` as `ledger_table`, `society` as `society_table` where Account.category_id = ledger_table.categoryid and society_table.society_id = ledger_table.society_id and society_table.society_id =".$_SESSION['society_id']; */
		//echo $sql1 = "select ledger_table.id,CONCAT(Account.category_name,CONCAT('(',Account.category_id,')')) as 'Category Name',CONCAT(ledger_table.ledger_name, CONCAT('(',ledger_table.id,')')) as 'ledger',FORMAT(ledger_table.opening_balance,2) as opening_balance,IF(ledger_table.opening_type = '0', 'None',IF(ledger_table.opening_type = '1', 'Credit',IF(ledger_table.opening_type = '2', 'Debit',''))) as opening_type, ledger_table.show_in_bill,ledger_table.note,DATE_FORMAT(DATE(DATE_ADD(ledger_table.opening_date, INTERVAL 1 DAY)),'%d-%m-%Y') as opening_date from `account_category` as `Account`,`ledger` as `ledger_table`, `society` as `society_table` where Account.category_id = ledger_table.categoryid and society_table.society_id = ledger_table.society_id and society_table.society_id =".$_SESSION['society_id']; 
	 $sql1 ="select ledger_table.id,CONCAT(Account.category_name,CONCAT('(',Account.category_id,')')) as 'Category Name',CONCAT(ledger_table.ledger_name) as 'ledger',ld.GSTIN_No,FORMAT(ledger_table.opening_balance,2) as opening_balance,IF(ledger_table.opening_type = '0', 'None',IF(ledger_table.opening_type = '1', 'Credit',IF(ledger_table.opening_type = '2', 'Debit',''))) as opening_type, ledger_table.show_in_bill,ledger_table.note,DATE_FORMAT(DATE(DATE_ADD(ledger_table.opening_date, INTERVAL 1 DAY)),'%d-%m-%Y') as opening_date  from `account_category` as `Account` join `ledger` as `ledger_table` on Account.category_id = ledger_table.categoryid join `society` as `society_table` on society_table.society_id = ledger_table.society_id left join `ledger_details` as ld on ledger_table.id=ld.LedgerID where society_table.society_id ='".$_SESSION['society_id']."' order by ledger_table.id";
	 // $sql1 ="select ledger_table.id,CONCAT(Account.category_name,CONCAT('(',Account.category_id,')')) as 'Category Name',CONCAT(ledger_table.ledger_name, CONCAT('(',ledger_table.id,')')) as 'ledger',ld.GSTIN_No,FORMAT(ledger_table.opening_balance,2) as opening_balance,IF(ledger_table.opening_type = '0', 'None',IF(ledger_table.opening_type = '1', 'Credit',IF(ledger_table.opening_type = '2', 'Debit',''))) as opening_type, ledger_table.show_in_bill,ledger_table.note,DATE_FORMAT(DATE(DATE_ADD(ledger_table.opening_date, INTERVAL 1 DAY)),'%d-%m-%Y') as opening_date  from `account_category` as `Account` join `ledger` as `ledger_table` on Account.category_id = ledger_table.categoryid join `society` as `society_table` on society_table.society_id = ledger_table.society_id left join `ledger_details` as ld on ledger_table.id=ld.LedgerID where society_table.society_id ='".$_SESSION['society_id']."' order by ledger_table.id";
		$result = $this->m_dbConn->select($sql1);
		$this->display1($result);
	}
	public function selecting()
	{
		$sql = "select id,`society_id`,`categoryid`,`ledger_name`,`show_in_bill`,`taxable`,`sale`,`purchase`,`income`,`expense`,`payment`,`receipt`,`opening_type`,`opening_balance`,`note`,`opening_type`,DATE_FORMAT(opening_date, '%d-%m-%Y') as opening_date, `supplementary_bill`, `taxable_no_threshold` from ledger where id='".$_REQUEST['account_subcategoryId']."'";
		$res = $this->m_dbConn->select($sql);
		
		
		//$sqlDate = "select Date from assetregister where LedgerID = '" . $res[0]['id'] . "' and Is_Opening_Balance = 1" ;
		//$resDate = $this->m_dbConn->select($sqlDate);
		
		$group_id = $this->obj_utility->getParentOfLedger($res[0]['id']);
		
		/*$sDate = '';
		if($resDate <> '')
		{
			$sDate = $resDate[0]['Date'];
		}
		*/
		//$res[0]['Date'] = getDisplayFormatDate($sDate);
		$res[0]['Group'] = $group_id['group'];
		if($res <> '')
		{
		for($i=0;$i<sizeof($res);$i++)
		{
		$select="select * from `ledger_details` where LedgerID='".$res[$i]['id']."' and status='Y'";
		$res1 = $this->m_dbConn->select($select);
		
		$res[0]['GSTIN_No']=$res1[$i]['GSTIN_No'];
		$res[0]['PAN_No']=$res1[$i]['PAN_No'];
		$res[0]['nature_of_payId']=$res1[$i]['TDS_NatureOfPayment'];
		$res[0]['nature_deduction_rate']=$res1[$i]['TDS_Ded_rate'];
		}
		}
		return $res;
	}
	public function deleting()
	{
		$sql = "update ledger set status='N' where id='".$_REQUEST['account_subcategoryId']."'";
		$res = $this->m_dbConn->update($sql);
		$sql1="update ledger_details set status='N' where LedgerID='".$_REQUEST['account_subcategoryId']."'";
		$res1 = $this->m_dbConn->update($sql1);
	}
	
	
	public function FetchDate($default_year)
	{
		$sql = "select DATE_FORMAT(BeginingDate, '%d-%m-%Y') as BeginingDate from `year` where `YearID`='".$default_year."' ";
		$res = $this->m_dbConn->select($sql);
		return  $this->obj_utility->GetDateByOffset($res[0]['BeginingDate'],-1);
	}
	
	function moveRegEntriesAccordingToGroupChange($ledgerID, $prevGroup, $currentGroup, $prevCatID, $currentCatID)
	{
		$entries = array();	
		$oldIDs = "";
		$newIDs = "";	
		
		if($currentCatID == BANK_ACCOUNT || $currentCatID == CASH_ACCOUNT)
		{
			if($prevGroup == LIABILITY)
			{
				$countQuery = "SELECT Count(*) AS cnt FROM `liabilityregister` WHERE `LedgerID` = '".$ledgerID."' AND `Is_Opening_Balance` != 1";
			}
			else if($prevGroup == EXPENSE)
			{
				$countQuery = "SELECT count(*) AS cnt FROM `expenseregister` WHERE `LedgerID` = '".$ledgerID."'";  
			}
			else if($prevGroup == INCOME)
			{
				$countQuery = "SELECT count(*) AS cnt FROM `incomeregister` WHERE `LedgerID` = '".$ledgerID."'"; 
			}
			$count = $this->m_dbConn->select($countQuery);
			if($count[0]['cnt'] > 0)
			{
				return "Not Allowed";
			}
		}
		if($prevCatID == BANK_ACCOUNT || $prevCatID == CASH_ACCOUNT)
		{
			$selectQuery = "SELECT count(*) AS cnt FROM `bankregister` WHERE `LedgerID` = '".$ledgerID."' AND `Is_Opening_Balance` != 1";
			$count = $this->m_dbConn->select($selectQuery); 
			if($count[0]['cnt'] > 0)
			{
				return "Changing of GroupID is Not Allowed";
			}
		}
		
		if($prevGroup == LIABILITY)
		{
			$selectQuery = "SELECT * FROM `liabilityregister` WHERE `LedgerID` = '".$ledgerID."' AND `Is_Opening_Balance` != 1";
			$entries = $this->m_dbConn->select($selectQuery); 	
			$deleteQuery = "DELETE FROM `liabilityregister` WHERE `LedgerID` = '".$ledgerID."'";
			$this->m_dbConn->delete($deleteQuery);
		}
		else if($prevCatID == BANK_ACCOUNT || $prevCatID == CASH_ACCOUNT)
		{
			
		}
		else if($prevGroup == ASSET)
		{
			$selectQuery = "SELECT * FROM `assetregister` WHERE `LedgerID` = '".$ledgerID."' AND `Is_Opening_Balance` != 1";	
			$entries = $this->m_dbConn->select($selectQuery);
			$deleteQuery = "DELETE FROM `assetregister` WHERE `LedgerID` = '".$ledgerID."'";	
			$this->m_dbConn->delete($deleteQuery); 	
		}
		else if($prevGroup == EXPENSE)
		{
			$selectQuery = "SELECT * FROM `expenseregister` WHERE `LedgerID` = '".$ledgerID."'";
			$entries = $this->m_dbConn->select($selectQuery); 	
			$deleteQuery = "DELETE FROM `expenseregister` WHERE `LedgerID` = '".$ledgerID."'";	
			$this->m_dbConn->delete($deleteQuery);	
		}
		else if($prevGroup == INCOME)
		{
			$selectQuery = "SELECT * FROM `incomeregister` WHERE `LedgerID` = '".$ledgerID."'";	
			$entries = $this->m_dbConn->select($selectQuery); 	
			$deleteQuery = "DELETE FROM `incomeregister` WHERE `LedgerID` = '".$ledgerID."'";	
			$this->m_dbConn->delete($deleteQuery);
		}
		
		if($currentGroup == LIABILITY)
		{
			for($i = 0; $i < sizeof($entries); $i++)
			{
				$oldIDs .= $entries[$i]['id'] . "|";
				$transactionDetails = $this->getTransactionDetails($entries, $i);
				//$insertLiability = $this->obj_register->SetLiabilityRegister(getDBFormatDate($entries[$i]['Date']), $ledgerID, $entries[$i]['VoucherID'], $entries[$i]['VoucherTypeID'], $transactionDetails[0], $transactionDetails[1], 0);	
				$sqlInsert = "INSERT INTO `liabilityregister`(`Date`, `CategoryID`, `SubCategoryID`, `LedgerID`, `VoucherID`, `VoucherTypeID`, `" . $transactionDetails[0] . "`, `Is_Opening_Balance`) VALUES ('" . getDBFormatDate($entries[$i]['Date']) . "', '" . $currentGroup . "', '" . $currentCatID . "', '" . $ledgerID . "', '" . $entries[$i]['VoucherID'] . "',  '" . $entries[$i]['VoucherTypeID'] . "', '" . $transactionDetails[1] . "', '0')";
				$insertLiability = $this->m_dbConn->insert($sqlInsert);
				$newIDs .= $insertLiability . "|";
			}
		}
		else if($currentCatID == BANK_ACCOUNT || $currentCatID == CASH_ACCOUNT)
		{
			//
		}
		else if($currentGroup == ASSET)
		{			
			for($i = 0; $i < sizeof($entries); $i++)
			{
				$oldIDs .= $entries[$i]['id'] . "|";
				$transactionDetails = $this->getTransactionDetails($entries, $i);
				//$insertAsset = $this->obj_register->SetAssetRegister(getDBFormatDate($entries[$i]['Date']), $ledgerID, $entries[$i]['VoucherID'], $entries[$i]['VoucherTypeID'], $transactionDetails[0], $transactionDetails[1], 0);	
				$sqlInsert = "INSERT INTO `assetregister`(`Date`, `CategoryID`, `SubCategoryID`, `LedgerID`, `VoucherID`, `VoucherTypeID`, `" . $transactionDetails[0] . "`, `Is_Opening_Balance`) VALUES ('" . getDBFormatDate($entries[$i]['Date']) . "', '" . $currentGroup . "', '" . $currentCatID . "', '" . $ledgerID . "', '" . $entries[$i]['VoucherID'] . "',  '" . $entries[$i]['VoucherTypeID'] . "', '" . $transactionDetails[1] . "', '0')";
				$insertAsset = $this->m_dbConn->insert($sqlInsert);
				$newIDs .= $insertAsset . "|";				
			}
		}
		else if($currentGroup == INCOME)
		{
			for($i = 0; $i < sizeof($entries); $i++)
			{
				$oldIDs .= $entries[$i]['id'] . "|";
				$transactionDetails = $this->getTransactionDetails($entries, $i);
				$insertIncome = $this->obj_register->SetIncomeRegister($ledgerID, getDBFormatDate($entries[$i]['Date']), $entries[$i]['VoucherID'], $entries[$i]['VoucherTypeID'], $transactionDetails[0], $transactionDetails[1]);	
				$newIDs .= $insertIncome . "|";
			}
		}
		else if($currentGroup == EXPENSE)
		{
			for($i = 0; $i < sizeof($entries); $i++)
			{
				$oldIDs .= $entries[$i]['id'] . "|";
				$transactionDetails = $this->getTransactionDetails($entries, $i);				
				$insertExpense = $this->obj_register->SetExpenseRegister($ledgerID, getDBFormatDate($entries[$i]['Date']), $entries[$i]['VoucherID'], $entries[$i]['VoucherTypeID'], $transactionDetails[0], $transactionDetails[1]);					
				$newIDs .= $insertExpense . "|";
			}
		}
		$logMsg .= "<br />Records moved - ";
		$logMsg .= " oldGroup<".$prevGroup."> :".$oldIDs . "<br /> newGroup<".$currentGroup."> :".$newIDs;
		
		$insertQuery = "INSERT INTO `change_log`(`ChangedLogDec`, `ChangedBy`, `ChangedTable`) VALUES ('" . $this->m_dbConn->escapeString($logMsg) . "','".$_SESSION['login_id']."','registers')";										
		$this->m_dbConn->insert($insertQuery);
	}
	
	function getTransactionDetails($entries, $cnt)
	{
		$transactionType = TRANSACTION_CREDIT;
		if($entries[$cnt]['Debit'] > 0)
		{
			$transactionType = TRANSACTION_DEBIT;
			$amount = $entries[$cnt]['Debit'];
		}
		else
		{
			$amount = $entries[$cnt]['Credit'];
		}
		return array($transactionType, $amount);
	}		
}
?>