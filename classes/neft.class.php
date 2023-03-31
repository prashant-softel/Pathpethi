<?php

//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once("dbconst.class.php");
include_once("changelog.class.php");
include_once("latestcount.class.php");
include_once("voucher.class.php");
include_once("register.class.php");

class neft extends dbop
{
	public $actionPage = "../neft.php";
	public $m_dbConn;
	private $obj_register;
	private $obj_changelog;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);

		//$this->curdate		= $this->display_pg->curdate();
		//$this->curdate_show	= $this->display_pg->curdate_show();
		//$this->curdate_time	= $this->display_pg->curdate_time();
		//$this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);
		
		$this->obj_changelog = new changeLog($this->m_dbConn);
	}

	public function startProcess()
	{
		$errorExists = 0;

		//$curdate 		=  $this->curdate;
		//$curdate_show	=  $this->curdate_show;
		//$curdate_time	=  $this->curdate_time;
		//$ip_location	=  $this->ip_location;
	}
	
	public function InsertData($SocietyID, $PaidBy, $PaidTo, $PayerBank, $PayerBranch, $Amount, $Date, $AccNumber, $TransactionNo, $Comments)
	{
		$changeID = $this->obj_changelog->setLog('New NEFT Entry', $_SESSION['login_id'], 'neft', '--');
		
		$sqlInsert = "INSERT INTO `neft`(`society_id`, `paid_by`, `paid_to`, `payer_bank`, `payer_branch`, `amount`, `date`, `acc_no`, `changed_by`, `change_log_id`, `transaction_no`, `comments`) VALUES ('" . $SocietyID . "', '" . $PaidBy . "', '" . $PaidTo . "', '" . $PayerBank . "' , '" . $PayerBranch . "', '" . $Amount . "', '" .  getDBFormatDate($Date) . "', '" . $AccNumber . "', '" . $_SESSION['login_id'] . "', '" . $changeID . "', '" . $TransactionNo . "', '" . $Comments . "')";
		
		$result = $this->m_dbConn->insert($sqlInsert);
		
		return $result;
	}
	
	public function UpdateData($ID, $PaidBy, $PaidTo, $PayerBank, $PayerBranch, $Amount, $Date, $AccNumber, $TransactionNo, $Comments)
	{
		$changeID = $this->obj_changelog->setLog('Update NEFT Entry for ID : ' . $ID, $_SESSION['login_id'], 'neft', '--');
				
		$sqlUpdate = "UPDATE `neft` SET `paid_by`='" . $PaidBy . "', `paid_to`='" . $PaidTo . "', `payer_bank`='" . $PayerBank . "', `payer_branch`='" . $PayerBranch . "', `amount`='" . $Amount . "', `date`='" . getDBFormatDate($Date) . "', `acc_no`='" . $AccNumber . "', `transaction_no`='" . $TransactionNo . "', `changed_by`='" . $_SESSION['login_id'] . "', `change_log_id`='" . $changeID . "', `comments`='" . $Comments . "' WHERE ID = '" . $ID . "'";
		
		$result = $this->m_dbConn->update($sqlUpdate);
		
		return $result;
	}
	
	public function ApproveTransaction($ID, $PaidBy, $PaidTo, $Date, $Amount)
	{
		$obj_LatestCount = new latestCount($this->m_dbConn);
		$LatestVoucherNo = $obj_LatestCount->getLatestVoucherNo($_SESSION['society_id']);
			
		$obj_voucher = new voucher($this->m_dbConn);
		$obj_register = new regiser($this->m_dbConn);
		
		$dataVoucher  = $obj_voucher->SetVoucherDetails(getDBFormatDate($Date), $ID, TABLE_NEFT, $LatestVoucherNo, 1, VOUCHER_RECEIPT, $PaidBy, TRANSACTION_DEBIT, $Amount);
			
		$resVal = $obj_register->SetAssetRegister(getDBFormatDate($dataVoucher), $PaidBy, $dataVoucher, VOUCHER_RECEIPT, TRANSACTION_CREDIT, $Amount, 0);
			
		$dataVoucher  =  $obj_voucher->SetVoucherDetails(getDBFormatDate($Date), $ID, TABLE_NEFT, $LatestVoucherNo, 2, VOUCHER_RECEIPT, $PaidTo,TRANSACTION_CREDIT, $Amount);
		
		$bankregisterquery = $obj_register->SetBankRegister(getDBFormatDate($Date), $PaidTo, $dataVoucher, VOUCHER_RECEIPT, TRANSACTION_RECEIVED_AMOUNT, $Amount, -2, $ID, 0);
		
		$changeID = $this->obj_changelog->setLog('Approved NEFT Entry for ID : ' . $ID, $_SESSION['login_id'], 'neft', '--');
		
		$sqlUpdate = "UPDATE `neft` SET `approved`='1', `changed_by`='" . $_SESSION['login_id'] . "' WHERE ID = '" . $ID . "'";
		$sqlResult = $this->m_dbConn->update($sqlUpdate);
				
		return "Approve";
	}
	
	public function combobox($query, $id, $defaultOption = '', $defaultValue = '')
	{
		$str = '';
		
		if($defaultOption)
		{
			$str.="<option value='" . $defaultValue . "'>" . $defaultOption . "</option>";
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
	public function comboboxEx($query)
	{
		
		$id=0;
		//echo "<script>alert('test')<//script>";
		$str="";
	$data = $this->m_dbConn->select($query);
	//echo "<script>alert('test2')<//script>";
		if(!is_null($data))
		{
			$vowels = array('/', '*', '%', '&', ',', '(', ')', '"');
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
						//$str.=$v."</OPTION>";
						$str.= str_replace($vowels, ' ', $v)."</OPTION>";
					}
					//echo "<script>alert('".$str."')<//script>";
					$i++;
				}
			}
		}
		//return $str;
		//print_r( $str);
		//echo "<script>alert('test')<//script>";
		return $str;
	}
	public function display1($rsas)
	{
		$thheader = array('Paid By','Paid To','Payer Bank', 'Amount','Date', 'Account No', 'Transaction No', 'Approved', 'Comments');
		$editFunction		= "getNeftDetails";
		//$this->display_pg->th		= $thheader;
		$mainpg	= "neft.php";

		$res = $this->display_table($thheader, $rsas, $editFunction, $mainpg, true, false);
		
		return $res;
	}
	
	function display_table($thheader, $rsas, $editFunction, $mainpg, $ShowEditOption = true, $ShowDeleteOption = true)
	{  
	   
		
		$str = "<table id='example' class='display' cellspacing='0' width='100%'>";

		if(!is_null($rsas))
		{
			
			$cnt2=$cnt1-1;
			$str.="<thead>";
			$str.="<tr class='head' height='30' bgcolor='#CCCCCC'>"; // style='color:#FFFFFF; background-color:#999999'
		
			
			if($ShowEditOption)
			{
				$str .= "<th align='center' width=80>&nbsp;&nbsp;";
				$str .= "Edit";
				$str .= "&nbsp;</th>";
			}
			
			if($ShowDeleteOption)
			{
				$str .= "<th align='center' width=80>&nbsp;&nbsp;";
				$str .= "Delete";
				$str .= "&nbsp;&nbsp;</th>";
			}
			
			$countth=count($thheader);
			
			for($k=0;$k<$countth;$k++)
			{	
				$str .= "<th>&nbsp;&nbsp;";
				$str .= $thheader[$k];
				$str .= "&nbsp;&nbsp;</th>";
			}
			
			$str.="</tr>";
			$str.="</thead>";
			$str.="<tbody>";
			foreach($rsas as $key => $value)
			{			
				$str .= "<tr height='25' bgcolor='#BDD8F4'>"; //  bgcolor='#EEB9EA'
				$i = 0;
				$curID = $rsas[$key]['ID'];
				$bApproved = true;
				if($rsas[$key]['approved'] == 'No')
				{
					$bApproved = false;
				}
				
				foreach($value as $k => $v)
				{
					if($i == 0)
					{
						if($ShowEditOption)
						{
							if(!$bApproved)
							{
								
								$str .= "<td align='center' valign='center'><a id='edit-".$v."' onClick='".$editFunction."(this.id);'><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a></td>";
							}
							else
							{
								
								$str .= "<td></td>";
							}
						}
						
						$i++;
					}
					else if($i == 8)
					{
						if($v == "Yes")
						{
							$str .= "<td align='center' valign='center' style='color:#00FF00;font-weight:bold;font-size:14px;'>Yes</td>";
						}
						else
						{
							$str .= "<td align='center' valign='center'><input type='button' value='Pending' onclick='approve($curID);'</td>";
						}
						$i++;
					}
					else
					{
						if(substr($v,0,9)=="../upload")
						{
							$str.="<td valign='center'><img name=".$i." src=".stripslashes($v)." width='90' height='70'></td>";
						}
						else
						{
							if(strlen($v)>100)
							{
								if($v<>"")
								{
									$str .= "<td valign='center' width='500px'>&nbsp;&nbsp;".$v."&nbsp;&nbsp;</td>";
								}
								else
								{
									$str .= "<td valign='center'>&nbsp;</td>";
								}
							}
							else
							{
								if($v<>"")
								{
									
									$str .= "<td valign='center' align='center'>&nbsp;&nbsp;".stripslashes($v)."&nbsp;&nbsp;</td>";
								}
								else
								{
									$str .= "<td valign='center'>&nbsp;</td>";
								}
							}
						}
						$i++;
					}
				}
				$str .= "</tr>";
			}
		}		
		$str.="<tbody>";
		$str.="</table>";
		return $str;
	}
	
	public function pgnation()
	{
		$sql1 = "select nefttbl.ID, IF(nefttbl.BillType = 0, 'NO', 'YES') as BillType,concat_ws(' - ', nefttbl.paid_by, ledgertblby.ledger_name), concat_ws(' - ', nefttbl.paid_to, ledgertblto.ledger_name), nefttbl.payer_bank, nefttbl.amount, nefttbl.date, nefttbl.acc_no, nefttbl.transaction_no, if(nefttbl.approved = 0, 'No', 'Yes') as approved, nefttbl.comments from neft as nefttbl JOIN ledger as ledgertblby on ledgertblby.id = nefttbl.paid_by JOIN ledger as ledgertblto on ledgertblto.id = nefttbl.paid_to where nefttbl.society_id='" . $_SESSION['society_id'] . "' ORDER BY nefttbl.approved ASC";
			
		/*$cntr = "select count(ID) as cnt from neft where society_id='" . $_SESSION['society_id'] . "'";

		$this->display_pg->sql1		= $sql1;
		$this->display_pg->cntr1	= $cntr;
		$this->display_pg->mainpg	= "neft.php";

		$limit	= "30";
		$page	= $_REQUEST['page'];
		$extra	= "";

		$res	= $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;*/
		$result = $this->m_dbConn->select($sql1);
		
		$data=$this->display1($result);
		return $data;
	}
	
	public function selecting($NeftID)
	{
		$sql = "SELECT `ID`, `society_id`, `paid_by`, `paid_to`, `payer_bank`, `payer_branch`, `amount`, `date`, `acc_no`, `transaction_no`,  `approved`, `comments` FROM `neft` where ID='" . $NeftID . "'";
		
		$res = $this->m_dbConn->select($sql);
		
		return $res;
	}
	public function GetBankDetails($BankID)
	{
		$sql = "select led.id, bm.AcNumber, bm.IFSC_Code,bm.BankName from `ledger` AS led JOIN `bank_master` AS bm ON led.id = bm.BankID  where led.categoryid='" . BANK_ACCOUNT . "' AND bm.BankID='". $BankID ."' AND bm.AllowNEFT=1";
		//echo $sql;
		$res = $this->m_dbConn->select($sql);
		//echo explode($res); 
		return $res;
	}
	public function deleting()
	{
		$sql = "update bank_master set status='N' where BankID='".$_REQUEST['BankDetailsId']."'";
		$res = $this->m_dbConn->update($sql);
	}
	
	public function getOpeningBalance($bankID)
	{
		$sql = "select Date, ReceivedAmount from bankregister where LedgerID = '" . $bankID . "' and Is_Opening_Balance = 1";
		$sqlResult = $this->m_dbConn->select($sql);
		echo $sqlResult[0]['Date'] . '@@@' . $sqlResult[0]['ReceivedAmount'];
	}
	
	public function pgnation_neft($unit_id)
	{
		$sql1 = "select chq.id, DATE_FORMAT(chq.VoucherDate, '%d-%m-%Y'), IF(chq.BillType = 0, 'NO', 'YES') as BillType,DATE_FORMAT(chq.ChequeDate, '%d-%m-%Y'), chq.ChequeNumber,chq.Amount,led.ledger_name,chq.PayerBank,chq.PayerChequeBranch, chq.Comments from chequeentrydetails as chq JOIN  ledger as led on chq.BankID = led.id where chq.PaidBy = '".$unit_id."' and chq.status='Y' ORDER BY ID DESC";		
		$result = $this->m_dbConn->select($sql1);
		$this->display1_neft($result);
	}
	
	public function display1_neft($rsas)
	{
		$thheader = array('VoucherDate','Supplementary Bill','Trans/Cheque Date','Trans/Cheque No','Amount','PaidTo','Payer Bank','Payer Branch','Comments');				
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "neft.php";
		
		$res = $this->display_pg->display_datatable($rsas, false, false);
		return $res;
	}
}
?>