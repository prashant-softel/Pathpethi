<?php

include_once("include/display_table.class.php");
?>
<?php

class reverse_charges extends dbop
{
	public $m_dbConn;
	
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);

	}


public function combobox($query, $id, $bShowAll = false)
	{
		if($bShowAll == true)
		{
			$str.="<option value='0'>All</option>";
		}
		else
		{
			//$str.="<option value=''>Please Select</option>";
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
	
	public function combobox02($query,$id, $defaultText = 'Please Select', $defaultValue = '')
	{
		$str = '';
		
		if($defaultText != '')
		{
			$str .= "<option value='" . $defaultValue . "' selected = 'selected'>" . $defaultText . "</option>";
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


public function getDetails($uid)
{
 $sql="select unit_no from `unit` where unit_id='".$uid."'";
 $res = $this->m_dbConn->select($sql);

 return $res[0]['unit_no'];

}

public function getBillPeriod($year_id, $billtype, $role)
{
	 $sql = "Select DISTINCT(br.PeriodID), p.Type from billregister  as br JOIN period as p on p.PrevPeriodID=br.PeriodID where br.BillType='".$billtype."' and p.YearID='".$year_id."' order by br.PeriodID desc";	
	if($role != "Super Admin")
	{
		$sql .=  " limit 0,2 ";

	}
			$res = $this->m_dbConn->select($sql);
			 return $res;
	 	
	
}
public function getBillType($date, $year_id, $billtype)
{
	$sql2 = "Select DISTINCT(br.PeriodID), p.Type from billregister  as br JOIN period as p on p.PrevPeriodID=br.PeriodID where br.BillType='".$billtype."' and br.BillDate='".$date."' and p.YearID='".$year_id."' order by br.PeriodID desc limit 0,2 ";
	// echo $sql2;die;
	$res = $this->m_dbConn->select($sql2);
	// echo "<pre>";print_r($res);
	// return $res;
	return $res[0]['Type'];

}	

public function selecting()
	{
		//echo "depd";
		//echo $_REQUEST['depositgroupId'];
		$sql = "select * from reversal_credits where ID='".$_REQUEST['reverse_chargesId']."'";
		// echo $sql;die;
		$res = $this->m_dbConn->select($sql);
		return $res;
	}


public function deleting()
	{
		//echo "depd";
		//echo $_REQUEST['depositgroupId'];
		$sql = "delete from reversal_credits where ID='".$_REQUEST['reverse_chargesId']."'";
		// echo $sql;die;
		$res = $this->m_dbConn->select($sql);
		return $res;
	}

	
public function storeDetails($uid)
{
	//echo "ins:".$_REQUEST['insert'];
	if($_REQUEST['insert']=='Submit')
	{
		$date = date('yyyy/mm/dd h:i:s', time());
		if($_POST['ledger']<>"" && $_POST['amount']<>"" && $_POST['period_id']<>"" && $_POST['comments']<>"" && $_POST['fine_type']<>"")
		{
			// echo "Hello";die;
			// echo "<pre>";print_r($_POST);die;
			if($_POST['fine_type'] == '1')
			{
				$amount = -($_POST['amount']);
				// echo $fine_type;die;
			}
			else if($_POST['fine_type'] == '2')
			{
				$amount = ($_POST['amount']);
				// echo $fine_type;die;
			}
			else
			{
				return "<font color='#FF0000'>All * Fields Are Required..</font>";								
			}
			$sql01 = "select * from period where ID = '".$_POST['period_id']."'";
			$sql11 = $this->m_dbConn->select($sql01);
				
			$get_bill_date="select DISTINCT BillDate from `billregister` where PeriodID=".$sql11[0]['PrevPeriodID']." and SocietyID=".$_SESSION['society_id']." ";
			$data = $this->m_dbConn->select($get_bill_date);	
						
			$sql = "INSERT INTO  `reversal_credits` (`Date`,`UnitID`,`Amount`,`LedgerID`,`Comments`,`BillType`, `ChargeType`,`PeriodID`,`ReportedBy`,`UpdateTime`) VALUES('".$data[0]['BillDate']."','".$uid."','".$amount."', '".$_POST['ledger']."', '".$_POST['comments']."', '".$_POST['bill_method']."', '".$_POST['fine_type']."','".$_POST['period_id']."','".$_SESSION['name']."',now()) ";
			$res = $this->m_dbConn->insert($sql);
						
			if($_POST['fine_type'] == 2)
			{
				echo "<script type='text/javascript'>if (window.confirm('Do you want to create a notice?'))
					{
         				window.location.href = 'addnotice.php?View=MEMBER&module=1&ID=$res';
					}
     				else
					{
        				//return false;
     				}
					</script>";
			}
		}
		$unit_id = "process/reverse_charge.process.php?&uid=".$_REQUEST['uid'];
		echo "<script type='text/javascript'>window.location.href='$unit_id'</script>";
	}
	else if($_REQUEST['insert']=='Update')
	{
		// echo "<pre>";print_r($_POST);die;
		// echo "hello";die;
		if($_POST['ledger']<>"" && $_POST['amount']<>"" && $_POST['period_id']<>"" && $_POST['comments']<>"" && $_POST['fine_type']<>"" && $_POST['bill_method']<>"" && $_POST['bill_id']<>"")
		{
			if($_POST['fine_type'] == '1')
			{
				$amount = -($_POST['amount']);
				// echo $fine_type;die;
			}
			else if($_POST['fine_type'] == '2')
			{
				$amount = ($_POST['amount']);
				// echo $fine_type;die;
			}
			else
			{
				return "<font color='#FF0000'>All * Fields Are Required..</font>";	
			}
			$get_bill_date="select DISTINCT BillDate from `billregister` where PeriodID=".$_POST['period_id']." and SocietyID=".$_SESSION['society_id']." ";
			// echo $get_bill_date;die;
			$data = $this->m_dbConn->select($get_bill_date);
			$update_reversebill = "update reversal_credits SET  Date = '".$data[0]['BillDate']."' , UnitID = '".$uid."' , Amount = '".$amount."' ,  LedgerID = '".$_POST['ledger']."' , Comments = '".$_POST['comments']."' , BillType = '".$_POST['bill_method']."' , ChargeType = '".$_POST['fine_type']."', UpdateTime=now() where ID = '".$_POST['bill_id']."' ";
			// echo $update_reversebill;die;
			$data2 = $this->m_dbConn->update($update_reversebill);
		}
		$unit_id = "process/reverse_charge.process.php?&uid=".$_REQUEST['uid'];
		echo "<script type='text/javascript'>window.location.href='$unit_id'</script>";
	}
}


public function SetVoucher($Date,$UnitID,$LedgerID,$Amount,$InsertID)
{

//echo "inside setvoucher";
					//$iSrNo =1;
					$obj_LatestCount=new latestCount($this->m_dbConn);
					$iVoucherCouter = $obj_LatestCount->getLatestVoucherNo($_SESSION['society_id']);
					//echo '$iVoucherCouter'.$iVoucherCouter;	
					$obj_voucher = new voucher($this->m_dbConn);
					
					$sqlDebitVoucherID = $obj_voucher->SetVoucherDetails(getDBFormatDate($Date), $InsertID, TABLE_REVERSAL_CREDITS, $iVoucherCouter, 1, VOUCHER_JOURNAL, $LedgerID, TRANSACTION_DEBIT,$Amount);
					//echo '$sqlVoucherID'.$sqlVoucherID;
					
					$sqlCreditVoucherID = $obj_voucher->SetVoucherDetails(getDBFormatDate($Date), $InsertID, TABLE_REVERSAL_CREDITS, $iVoucherCouter, 2, VOUCHER_JOURNAL, $UnitID, TRANSACTION_CREDIT,$Amount);
					
					//update  `reversal_credits` table
				$sql="update `reversal_credits`  set VoucherID='".$sqlCreditVoucherID."' where UnitID='".$UnitID."' and LedgerID='".$LedgerID."'";
					$res = $this->m_dbConn->update($sql);
					
					$obj_register = new regiser($this->m_dbConn);
					//update AssetRegister table
		 	$regResult = $obj_register->SetAssetRegister(getDBFormatDate($Date), $UnitID, $sqlCreditVoucherID, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $Amount, 0);
			
			//update incomeregister table
			$regResult = $obj_register->SetIncomeRegister($LedgerID, getDBFormatDate($Date), $sqlCreditVoucherID, VOUCHER_JOURNAL, TRANSACTION_DEBIT, $Amount);		
					
					echo "<font color='#FF0000'>Record Updated Successfully..</font>";
	
	
}


public function get_year($uid)
{
	$sql = "SELECT yeartbl.YearDescription,yeartbl.YearID FROM year as yeartbl JOIN period as periodtbl on periodtbl.YearID = yeartbl.YearID JOIN society as societytbl on periodtbl.ID = societytbl.M_PeriodID order by yeartbl.YearID desc";
	//echo $sql;
	$data = $this->m_dbConn->select($sql);
	return $data;
}

public function get_period($cycleID = 0)
{
	if($_SESSION['role'] == ROLE_SUPER_ADMIN)
	{
		$sql = "select * from period where YearID = '" . $_REQUEST['year'] . "' ";
		$res = $this->m_dbConn->select($sql);
	}
	else
	{
		$sql01 = "select M_PeriodID from society where society_id = '".$_SESSION['society_id']."'";
		$sql11 = $this->m_dbConn->select($sql01);
		
		$sql02 = "select * from period where ID = '".$sql11[0]['M_PeriodID']."' or PrevPeriodID = '".$sql11[0]['M_PeriodID']."'";
		$res = $this->m_dbConn->select($sql02);
	}
		
	if($res<>"")
	{
		$aryResult = array();
		array_push($aryResult,array('success'=>'0'));
		foreach($res as $k => $v)
		{
			$sqlCnt = "Select count(ID) as cnt from billregister where PeriodID = '" . $res[$k]['ID'] . "' and SocietyID = '" . $_SESSION['society_id'] . "'";
			$result = $this->m_dbConn->select($sqlCnt);
				
			if($result[0]['cnt'] > 0)
			{
				$show_dtl = array("id"=>$res[$k]['ID'], "period"=>('<font color="#FF0000">' . $res[$k]['Type'] . '**</font>'));
			}
			else
			{	
		 		$show_dtl = array("id"=>$res[$k]['ID'], "period"=>$res[$k]['Type']);
			}
			array_push($aryResult,$show_dtl);
		}
		echo json_encode($aryResult);
	}
	else
	{
		echo json_encode(array(array("success"=>1), array("message"=>'No Data To Display')));
	}
}

/*public function get_Period($yid)
{
	if(isset($_SESSION['role']) && $_SESSION['role']== 'Super Admin')
	{
	$sql = "SELECT periodtbl.ID as PeriodID,periodtbl.PrevPeriodID,periodtbl.Type FROM `period`  as periodtbl JOIN `society` as societytbl on periodtbl.Billing_cycle=societytbl.bill_cycle where periodtbl.YearID =".$yid." and periodtbl.ID <=societytbl.M_PeriodID  ";
	}
	else
	{
		
		$sql = "SELECT periodtbl.ID as PeriodID,periodtbl.PrevPeriodID,periodtbl.Type FROM `period`  as periodtbl JOIN `society` as societytbl on societytbl.M_PeriodID =periodtbl.ID ";	
	}
	$data = $this->m_dbConn->select($sql);
	return $data;
	
}*/
//pgnation02($unit_id,$bill_type,$bill_period,$trans_type);
public function pgnation02($UnitID, $BillType, $PeriodID, $TransType)
	{
		$ledgername_array=array();
		$sql1 = "select reversetbl.ID,DATE_FORMAT(reversetbl.Date,'%d-%m-%Y'),ledgertbl.ledger_name,reversetbl.LedgerID,IF(reversetbl.BillType = '0','Maintenance','Supplementary') as BillType,IF(reversetbl.ChargeType = '1', 'Reverse Charge', 'Fine'),abs(reversetbl.Amount) as Amount,DATE_FORMAT(reversetbl.timestamp,'%d-%m-%Y'),reversetbl.Comments from `reversal_credits` as reversetbl JOIN  `ledger` as ledgertbl on ledgertbl.id= reversetbl.UnitID where reversetbl.status=1 ";

		if($UnitID <> "" && $UnitID != 0)
		{
			$sql1 .= " and reversetbl.UnitID = '" . $UnitID . "' ";
		}
		if($BillType <> "")
		{
			$sql1 .= " and reversetbl.BillType = '". $BillType ."' ";
		}
		if($PeriodID <> "")
		{
			$sql1 .= " and reversetbl.PeriodID = '". $PeriodID ."' ";
		}
		if($TransType <> "")
		{
			$sql1 .= " and reversetbl.ChargeType = '". $TransType ."' ";
		}

		$sql1 .= "order by reversetbl.Date desc";
		
		$result = $this->m_dbConn->select($sql1);
		$get_ledger_name="select id,ledger_name from `ledger`";
		$result02=$this->m_dbConn->select($get_ledger_name);
		
		$sql_toget_PeriodID = "select reversetbl.ID,DATE_FORMAT(reversetbl.Date,'%d-%m-%Y'),ledgertbl.ledger_name,reversetbl.LedgerID,reversetbl.BillType,IF(reversetbl.ChargeType = '1', 'Reverse Charge', 'Fine'),abs(reversetbl.Amount) as Amount,DATE_FORMAT(reversetbl.timestamp,'%d-%m-%Y'),reversetbl.Comments,reversetbl.PeriodID from `reversal_credits` as reversetbl JOIN  `ledger` as ledgertbl on ledgertbl.id= reversetbl.UnitID where reversetbl.status=1 and reversetbl.UnitID = '" . $UnitID ."' order by reversetbl.Date desc";
		$sql_toget_PeriodID_res = $this->m_dbConn->select($sql_toget_PeriodID);
		$PeriodID = $sql_toget_PeriodID_res[0]['PeriodID'];		
		$BillType = $sql_toget_PeriodID_res[0]['BillType'];
		
		//print_r($result02);
		for($i = 0; $i < sizeof($result02); $i++)
		{
			$ledgername_array[$result02[$i]['id']]=$result02[$i]['ledger_name'];
		}
		for($i = 0; $i < sizeof($result); $i++)
		{
			$result[$i]['LedgerID'] = $ledgername_array[$result[$i]['LedgerID']];
			
			$sql_toget_PeriodID = "select reversetbl.ID,reversetbl.UnitID,DATE_FORMAT(reversetbl.Date,'%d-%m-%Y') as BillDate,ledgertbl.ledger_name,reversetbl.LedgerID,reversetbl.BillType,IF(reversetbl.ChargeType = '1', 'Reverse Charge', 'Fine'),abs(reversetbl.Amount) as Amount,DATE_FORMAT(reversetbl.timestamp,'%d-%m-%Y'),reversetbl.Comments,reversetbl.PeriodID from `reversal_credits` as reversetbl JOIN  `ledger` as ledgertbl on ledgertbl.id= reversetbl.UnitID where reversetbl.status=1 and reversetbl.ID = '" . $result[$i]['ID'] ."' order by reversetbl.Date desc";
			$sql_toget_PeriodID_res = $this->m_dbConn->select($sql_toget_PeriodID);
			$PeriodID = $sql_toget_PeriodID_res[0]['PeriodID'];
			//echo "iteration: ".$i."<br>";
			//echo "1st period id: ".$PeriodID."<br>";
			if($PeriodID == 0)
			{
				$BillDate = $sql_toget_PeriodID_res[0]['BillDate'];
				//echo "BillDate:".getDBFormatDate($BillDate)."<br>";
						
				$sql03 = "select * from `period` where '".getDBFormatDate($BillDate)."' BETWEEN (BeginingDate) and (EndingDate)";
				$sql33 = $this->m_dbConn->select($sql03);
				//echo "Period id after in between:".$sql33[0]['ID']."<br>";
				
				$sql04 = "Select * from `period` where PrevPeriodID = '".$sql33[0]['ID']."'";
				$sql44 = $this->m_dbConn->select($sql04);
			
				$PeriodID = $sql44[0]['ID'];
			}
			//echo "2nd Period:".$PeriodID."<br>";
			$BillType = $sql_toget_PeriodID_res[0]['BillType'];
			
			if($_REQUEST['uid'] != 0)
			{
				$result[$i]['Amount'] = '<a href="Maintenance_bill.php?UnitID='.$_REQUEST['uid'].'&PeriodID='.$PeriodID.'&BT='.$BillType.'" target="_blank">'.$result[$i]['Amount'].'</a>';
				$result[$i]['BillType'] = '<a href="Maintenance_bill.php?UnitID='.$_REQUEST['uid'].'&PeriodID='.$PeriodID.'&BT='.$BillType.'" target="_blank">'.$result[$i]['BillType'].'</a>';
			}
			else
			{
				$result[$i]['Amount'] = '<a href="Maintenance_bill.php?UnitID='.$sql_toget_PeriodID_res[0]['UnitID'].'&PeriodID='.$PeriodID.'&BT='.$BillType.'" target="_blank">'.$result[$i]['Amount'].'</a>';
				$result[$i]['BillType'] = '<a href="Maintenance_bill.php?UnitID='.$sql_toget_PeriodID_res[0]['UnitID'].'&PeriodID='.$PeriodID.'&BT='.$BillType.'" target="_blank">'.$result[$i]['BillType'].'</a>';
			}
		}	
	 	$this->display2($result);
	}

public function pgnation($UnitID)
	{
		$ledgername_array=array();
		if($UnitID == 0)
		{
			$sql1 = "select reversetbl.ID,DATE_FORMAT(reversetbl.Date,'%d-%m-%Y'),ledgertbl.ledger_name,reversetbl.LedgerID,IF(reversetbl.BillType = '0','Maintenance','Supplementary') as BillType,IF(reversetbl.ChargeType = '1', 'Reverse Charge', 'Fine'),abs(reversetbl.Amount) as Amount,DATE_FORMAT(reversetbl.timestamp,'%d-%m-%Y'),reversetbl.Comments from `reversal_credits` as reversetbl JOIN  `ledger` as ledgertbl on ledgertbl.id= reversetbl.UnitID where reversetbl.status=1 order by reversetbl.Date desc";
		}
		else
		{
			$sql1 = "select reversetbl.ID,DATE_FORMAT(reversetbl.Date,'%d-%m-%Y'),ledgertbl.ledger_name,reversetbl.LedgerID,IF(reversetbl.BillType = '0','Maintenance','Supplementary') as BillType,IF(reversetbl.ChargeType = '1', 'Reverse Charge', 'Fine'),abs(reversetbl.Amount) as Amount,DATE_FORMAT(reversetbl.timestamp,'%d-%m-%Y'),reversetbl.Comments from `reversal_credits` as reversetbl JOIN  `ledger` as ledgertbl on ledgertbl.id= reversetbl.UnitID where reversetbl.status=1 and reversetbl.UnitID = '" . $UnitID ."' order by reversetbl.Date desc";
		}
		// echo $sql1;
		$result = $this->m_dbConn->select($sql1);
		$get_ledger_name="select id,ledger_name from `ledger`";
		$result02=$this->m_dbConn->select($get_ledger_name);
						
		//print_r($result02);
		for($i = 0; $i < sizeof($result02); $i++)
		{
			$ledgername_array[$result02[$i]['id']]=$result02[$i]['ledger_name'];
		}
		for($i = 0; $i < sizeof($result); $i++)
		{
			$result[$i]['LedgerID'] = $ledgername_array[$result[$i]['LedgerID']];
			
			//$sql05 = "select UnitID from `reversal_credits` where ID = '".$result[$i]['ID']."'";
			//$sql55 = $this->m_dbConn->select($sql05);
			
			$sql_toget_PeriodID = "select reversetbl.ID,reversetbl.UnitID,DATE_FORMAT(reversetbl.Date,'%d-%m-%Y') as BillDate,ledgertbl.ledger_name,reversetbl.LedgerID,reversetbl.BillType,IF(reversetbl.ChargeType = '1', 'Reverse Charge', 'Fine'),abs(reversetbl.Amount) as Amount,DATE_FORMAT(reversetbl.timestamp,'%d-%m-%Y'),reversetbl.Comments,reversetbl.PeriodID from `reversal_credits` as reversetbl JOIN  `ledger` as ledgertbl on ledgertbl.id= reversetbl.UnitID where reversetbl.status=1 and reversetbl.ID = '" . $result[$i]['ID'] ."' order by reversetbl.Date desc";
			$sql_toget_PeriodID_res = $this->m_dbConn->select($sql_toget_PeriodID);
			$PeriodID = $sql_toget_PeriodID_res[0]['PeriodID'];
			//echo "iteration: ".$i."<br>";
			//echo "1st period id: ".$PeriodID."<br>";
			if($PeriodID == 0)
			{
				$BillDate = $sql_toget_PeriodID_res[0]['BillDate'];
				//echo "BillDate:".getDBFormatDate($BillDate)."<br>";
						
				$sql03 = "select * from `period` where '".getDBFormatDate($BillDate)."' BETWEEN (BeginingDate) and (EndingDate)";
				$sql33 = $this->m_dbConn->select($sql03);
				//echo "Period id after in between:".$sql33[0]['ID']."<br>";
				
				$sql04 = "Select * from `period` where PrevPeriodID = '".$sql33[0]['ID']."'";
				$sql44 = $this->m_dbConn->select($sql04);
			
				$PeriodID = $sql44[0]['ID'];
			}
			//echo "2nd Period:".$PeriodID."<br>";
			$BillType = $sql_toget_PeriodID_res[0]['BillType'];
			
			if($_REQUEST['uid'] != 0)
			{
				$result[$i]['Amount'] = '<a href="Maintenance_bill.php?UnitID='.$_REQUEST['uid'].'&PeriodID='.$PeriodID.'&BT='.$BillType.'" target="_blank">'.$result[$i]['Amount'].'</a>';
				$result[$i]['BillType'] = '<a href="Maintenance_bill.php?UnitID='.$_REQUEST['uid'].'&PeriodID='.$PeriodID.'&BT='.$BillType.'" target="_blank">'.$result[$i]['BillType'].'</a>';
			}
			else
			{
				$result[$i]['Amount'] = '<a href="Maintenance_bill.php?UnitID='.$sql_toget_PeriodID_res[0]['UnitID'].'&PeriodID='.$PeriodID.'&BT='.$BillType.'" target="_blank">'.$result[$i]['Amount'].'</a>';
				$result[$i]['BillType'] = '<a href="Maintenance_bill.php?UnitID='.$sql_toget_PeriodID_res[0]['UnitID'].'&PeriodID='.$PeriodID.'&BT='.$BillType.'" target="_blank">'.$result[$i]['BillType'].'</a>';
			}
		}	
	 	$this->display2($result);
	}
	
public function display2($rsas)
	{
		$thheader = array('Bill Date','Unit No','Ledger Name','BillType','Transaction Type','Amount','Entry Date','Comments');
		$this->display_pg->edit		= "getReserveBill";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "reverse_charges.php";

		//$res = $this->display_pg->display_new($rsas);
		$res = $this->display_pg->display_datatable($rsas, true, true);
		return $res;
	}


public function getMaxPeriodID()
{
	$sql1 = "select M_PeriodID from `society` where `society_id`=".$_SESSION['society_id']." ";
		$result = $this->m_dbConn->select($sql1);	
		return $result[0]['M_PeriodID'];
	
}

}

?>

