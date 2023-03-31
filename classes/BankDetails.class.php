<?php

//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once("dbconst.class.php");
include_once("register.class.php");
include_once("utility.class.php");

class BankDetails extends dbop
{
	public $actionPage = "../BankDetails.php";
	public $m_dbConn;
	private $obj_register;
	private $obj_utility;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);

		/*//$this->curdate		= $this->display_pg->curdate();
		//$this->curdate_show	= $this->display_pg->curdate_show();
		//$this->curdate_time	= $this->display_pg->curdate_time();
		//$this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);*/

		$this->obj_register = new regiser($this->m_dbConn);
		$this->obj_utility = new utility($this->m_dbConn);
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
			//if($_POST['LedgerName'] <> "" && $_POST['BankName']<>"" && $_POST['BranchName']<>"" && $_POST['AcNumber']<>""&& $_POST['Address']<>"" && $_POST['IFSC_Code']<>"" && $_POST['MICR_Code']<>"" && $_POST['Phone1']<>"" && $_POST['Fax']<>"" && $_POST['Email']<>"" && $_POST['Website']<>"" && $_POST['ContactPerson']<>"" && $_POST['ContactPersonPhone']<>"")
			$LedgerID = $_POST['LedgerID'];
			$Balance = $_POST['Balance'];
			
			if($LedgerID == '0' && $_POST['BankName']<>"")
			{
				if($_SESSION['default_year_start_date'] <> "")
				{
					$OpeningBalanceDate = $this->obj_utility->GetDateByOffset($_SESSION['default_year_start_date'] , -1);
					if($OpeningBalanceDate <> "")
					{
						$_POST['Balance_Date'] = $OpeningBalanceDate;		
					}		
				}
				
				if($LedgerID == '0')
				{
					$LedgerName = $_POST['BankName'];
					$sqlNewLedger = "INSERT INTO `ledger`(`society_id`, `categoryid`, `ledger_name`, `purchase`, `income`, `expense`, `payment`, `receipt`, `opening_balance`,`opening_date`,`opening_type`) VALUES ('".$_SESSION['society_id']."', '" . BANK_ACCOUNT . "', '" . $LedgerName . "', 1, 1, 1, 1, 1, '" . $Balance . "','".getDBFormatDate($_POST['Balance_Date'])."','2')";	
					$LedgerID = $this->m_dbConn->insert($sqlNewLedger);
					
					//$insertAsset = $this->obj_register->SetAssetRegister(getDBFormatDate($_POST['Balance_Date']), $LedgerID, 0, 0, TRANSACTION_DEBIT, $Balance, 1);
					
					$insertBank = $this->obj_register->SetBankRegister(getDBFormatDate($_POST['Balance_Date']), $LedgerID, 0, 0, TRANSACTION_RECEIVED_AMOUNT, $Balance, 0, 0, 1);
				}
				
				$isAllowNEFT = 0;
				if(isset($_POST['AllowNEFT']))
				{
					$isAllowNEFT = 1; 
				}
				
				//echo $insert_query;
				$insert_query="insert into bank_master (`BankID`, `BankName`,`BranchName`,`AcNumber`,`Address`,`IFSC_Code`,`MICR_Code`,`Phone1`,`Phone2`,`Fax`,`Email`,`Website`,`ContactPerson`,
				`ContactPersonPhone`,`Note`, `AllowNEFT`) values ('" . $LedgerID . "', '".$_POST['BankName']."','".$_POST['BranchName']."','".$_POST['AcNumber']."','".$_POST['Address']."','".$_POST['IFSC_Code']."','".$_POST['MICR_Code']."','".$_POST['Phone1']."','".$_POST['Phone2']."','".$_POST['Fax']."','".$_POST['Email']."','".$_POST['Website']."','".$_POST['ContactPerson']."','".$_POST['ContactPersonPhone']."','".$_POST['Note']."', '" . $isAllowNEFT . "')";
				
				$data = $this->m_dbConn->insert($insert_query);
						
				return "Insert";
			}
			else if($LedgerID <> '0' && $_POST['BankName']<>"")
			{
				if($_SESSION['default_year_start_date'] <> "")
				{
					$OpeningBalanceDate = $this->obj_utility->GetDateByOffset($_SESSION['default_year_start_date'] , -1);
					if($OpeningBalanceDate <> "")
					{
						$_POST['Balance_Date'] = $OpeningBalanceDate;		
					}		
				}
				
				$isAllowNEFT = 0;
				if(isset($_POST['AllowNEFT']))
				{
					$isAllowNEFT = 1; 
				}
				
				$insert_query="insert into bank_master (`BankID`, `BankName`,`BranchName`,`AcNumber`,`Address`,`IFSC_Code`,`MICR_Code`,`Phone1`,`Phone2`,`Fax`,`Email`,`Website`,`ContactPerson`,
				`ContactPersonPhone`,`Note`,`AllowNEFT`) values ('" . $LedgerID . "', '".$_POST['BankName']."','".$_POST['BranchName']."','".$_POST['AcNumber']."','".$_POST['Address']."','".$_POST['IFSC_Code']."','".$_POST['MICR_Code']."','".$_POST['Phone1']."','".$_POST['Phone2']."','".$_POST['Fax']."','".$_POST['Email']."','".$_POST['Website']."','".$_POST['ContactPerson']."','".$_POST['ContactPersonPhone']."','".$_POST['Note']."', '" . $isAllowNEFT . "')";
				
				$data = $this->m_dbConn->insert($insert_query);
			
				$sqlDelete = "DELETE FROM `assetregister` WHERE LedgerID = '" . $LedgerID . "' and Is_Opening_Balance = 1";	
				$resultDelete = $this->m_dbConn->delete($sqlDelete);
				
				$sqlDelete = "DELETE FROM `bankregister` WHERE LedgerID = '" . $LedgerID . "' and Is_Opening_Balance = 1";	
				$resultDelete = $this->m_dbConn->delete($sqlDelete);
				
				//$insertAsset = $this->obj_register->SetAssetRegister(getDBFormatDate($_POST['Balance_Date']), $LedgerID, 0, 0, TRANSACTION_DEBIT, $Balance, 1);
				
				$insertBank = $this->obj_register->SetBankRegister(getDBFormatDate($_POST['Balance_Date']), $LedgerID, 0, 0, TRANSACTION_RECEIVED_AMOUNT, $Balance, 0, 0, 1);
				
				$sqlUpdate = "UPDATE `ledger` SET `opening_balance`= '" . $Balance . "',`opening_date` ='".getDBFormatDate($_POST['Balance_Date'])."',`opening_type` = '2'  WHERE id = '" . $LedgerID .  "'";
				$resultUpdate = $this->m_dbConn->update($sqlUpdate);
				
				return "Insert";
			}
			else
			{
				return "All * Field Required..";
			}
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			/*if($_SESSION['default_year_start_date'] <> "")
			{
				$OpeningBalanceDate = $this->obj_utility->GetDateByOffset($_SESSION['default_year_start_date'] , -1);
				if($OpeningBalanceDate <> "")
				{
					$_POST['Balance_Date'] = $OpeningBalanceDate;		
				}		
			}*/
			
			$isAllowNEFT = 0;
			if(isset($_POST['AllowNEFT']))
			{
				$isAllowNEFT = 1; 
			}
				
			$up_query="update bank_master set `BankName`='".$_POST['BankName']."',`BranchName`='".$_POST['BranchName']."',`AcNumber`='".$_POST['AcNumber']."',`Address`='".$_POST['Address']."',`IFSC_Code`='".$_POST['IFSC_Code']."',`MICR_Code`='".$_POST['MICR_Code']."',`Phone1`='".$_POST['Phone1']."',`Phone2`='".$_POST['Phone2']."',`Fax`='".$_POST['Fax']."',`Email`='".$_POST['Email']."',`Website`='".$_POST['Website']."',`ContactPerson`='".$_POST['ContactPerson']."',`ContactPersonPhone`='".$_POST['ContactPersonPhone']."',`Note`='".$_POST['Note']."',`AllowNEFT`='".$isAllowNEFT."' where BankID='".$_POST['id']."'";
			$data = $this->m_dbConn->update($up_query);
			
			//$updateAsset = "UPDATE `assetregister` SET `Date`='" . getDBFormatDate($_POST['Balance_Date']) . "', `Debit`='" . $_POST['Balance'] . "' WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
			//$dataAsset = $this->m_dbConn->update($updateAsset);
			
			$updateBank = "UPDATE `bankregister` SET `Date`= '" . getDBFormatDate($_POST['Balance_Date']) . "', `ReceivedAmount`='" . $_POST['Balance'] . "' WHERE LedgerID = '" . $_POST['id'] . "' and Is_Opening_Balance = 1";
			$dataBank = $this->m_dbConn->update($updateBank);
			
			$updateLedger = "UPDATE `ledger` SET `ledger_name` = '" . $_POST['BankName'] .  "', `opening_balance`='" . $_POST['Balance'] . "',`opening_date` ='".getDBFormatDate($_POST['Balance_Date'])."',`opening_type` = '2' WHERE id= '" . $_POST['id'] . "'";
			$dataLedger = $this->m_dbConn->update($updateLedger);
			
			return "Update";
		}
		else
		{
			return $errString;
		}
	}
	public function combobox($query)
	{
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
	
	public function display1($rsas)
	{
		$thheader = array('BankName','BranchName','A/C No.', 'Address','IFSC Code', 'MICR Code', 'Phone1','Phone2','Fax','Email','Website','ContactPerson','ContactPersonPhone', 'Note');
		$this->display_pg->edit		= "getBankDetails";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "BankDetails.php";

		$res = $this->display_pg->display_new($rsas);
		return $res;
	}
	public function pgnation()
	{
		//$sql1 = "select BankID, concat_ws('-',BankID,BankName),`BranchName`,`AcNumber`,`Address`,`IFSC_Code`,`MICR_Code`,`BranchName`,`Phone1`,`Phone2`,`Fax`,`Email`,`Website`,`ContactPerson`,`ContactPersonPhone`,`Note` from bank_master where status='Y'";
		$sql1 = "select bk.BankID, concat_ws('-',bk.BankID,bk.BankName),bk.BranchName,bk.AcNumber,bk.Address,bk.IFSC_Code,bk.MICR_Code,bk.BranchName,bk.Phone1,bk.Phone2,bk.Fax,bk.Email,bk.Website,bk.ContactPerson,bk.ContactPersonPhone,bk.Note from bank_master as bk JOIN ledger as led ON led.id = bk.BankID where led.society_id=".$_SESSION['society_id']." and bk.status='Y'";
		$cntr = "select count(status) as cnt from bank_master where status='Y'";

		/*$this->display_pg->sql1		= $sql1;
		$this->display_pg->cntr1	= $cntr;
		$this->display_pg->mainpg	= "BankDetails.php";

		$limit	= "20";
		$page	= $_REQUEST['page'];
		$extra	= "";

		$res	= $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;*/
		$result = $this->m_dbConn->select($sql1);
		$thheader = array('BankName','BranchName','A/C No.', 'Address','IFSC Code', 'MICR Code', 'Phone1','Phone2','Fax','Email','Website','ContactPerson','ContactPersonPhone', 'Note');
		$this->display_pg->edit		= "getBankDetails";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "BankDetails.php";
		$res = $this->display_pg->display_datatable($result);
	}
	public function selecting()
	{
		$sql = "select bank.BankID, bank.BankName, bank.BranchName, leg.ledger_name, bank.AcNumber, bank.Address, bank.IFSC_Code, bank.MICR_Code, bank.BranchName, bank.Phone1, bank.Phone2, bank.Fax, bank.Email, bank.Website, bank.ContactPerson, bank.ContactPersonPhone, bank.Note, leg.opening_balance, bank.AllowNEFT from bank_master as bank JOIN ledger AS leg ON bank.BankID = leg.id where BankID='".$_REQUEST['BankDetailsId']."' and leg.society_id='".$_SESSION['society_id'] ."'";
		$res = $this->m_dbConn->select($sql);
		//echo $sql;
		$sqlDate = "select Date from bankregister where LedgerID = '" . $res[0]['BankID'] . "' and Is_Opening_Balance = 1" ;
		$resDate = $this->m_dbConn->select($sqlDate);
		
		$sDate = '';
		if($resDate <> '')
		{
			$sDate = $resDate[0]['Date'];
			$sDate = getDisplayFormatDate($sDate);
		}
		
		$res[0]['Date'] = $sDate;
		
		return $res;
	}
	public function deleting()
	{
		$sql = "update bank_master set status='N' where BankID='".$_REQUEST['BankDetailsId']."'";
		$res = $this->m_dbConn->update($sql);
	}
	
	public function getOpeningBalance($bankID)
	{
		$sql = "select Date from bankregister where LedgerID = '" . $bankID . "' and Is_Opening_Balance = 1";
		$sqlResult = $this->m_dbConn->select($sql);
		$BalDate = '';
		if($sqlResult <> '')
		{
			$BalDate = $sqlResult[0]['Date'];
		}
		
		$sql = "select opening_balance from ledger where id = '" . $bankID . "'";
		$sqlResult = $this->m_dbConn->select($sql);
		$BalAmt = '';
		if($sqlResult <> '')
		{
			$BalAmt = $sqlResult[0]['opening_balance'];
		}
		
		echo $BalDate . '@@@' . $BalAmt;
	}
	
	public function FetchDate($default_year)
	{
		$sql = "select DATE_FORMAT(BeginingDate, '%d-%m-%Y') as BeginingDate from `year` where `YearID`='".$default_year."' ";
		$res = $this->m_dbConn->select($sql);
		return  $this->obj_utility->GetDateByOffset($res[0]['BeginingDate'],-1);
	}
}
?>