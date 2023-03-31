<?php
include_once("include/display_table.class.php");
include_once("utility.class.php");
include_once("dbconst.class.php");
class ledger_details extends dbop
{
	public $actionPage = "../society.php";
	public $m_dbConn;
	public $obj_utility;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg = new display_table($this->m_dbConn);
		$this->obj_utility = new utility($this->m_dbConn);
	}
	
	public function combobox($query, $id, $bShowAll = false)
	{
		if($bShowAll == true)
		{
			$str.="<option value=''>All</option>";
		}
		else
		{
			$str.="<option value=''>Please Select</option>";
		}
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i = 0;
				foreach($value as $k => $v)
				{
					if($i == 0)
					{
						if($id == $v)
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


public function details($gid,$lid)
{
  
  if($gid == 1)
  {
	$sql = "select Date, ledgertbl.ledger_name as Particular, sum(Debit) as Debit, sum(Credit) as Credit,VoucherID,VoucherTypeID from `liabilityregister` as liabilitytbl JOIN `ledger` as ledgertbl on liabilitytbl.LedgerID=ledgertbl.id where liabilitytbl.LedgerID='".$lid."'";
  	$data = $this->m_dbConn->select($sql);
  }
  
  if($gid == 2)
  {
	  
	$categoryid = $this->obj_utility->getParentOfLedger($lid);
	if($categoryid['category'] == BANK_ACCOUNT)
	{
		$sql = "select Date,ledgertbl.ledger_name as Particular, sum(PaidAmount) as Debit, sum(ReceivedAmount) as Credit,VoucherID,VoucherTypeID from `bankregister` as banktbl JOIN `ledger` as ledgertbl on banktbl.LedgerID=ledgertbl.id where banktbl.LedgerID='".$lid."'";	
	}
	else
	{
		$sql = "select Date,ledgertbl.ledger_name as Particular, sum(Debit) as Debit, sum(Credit) as Credit,VoucherID,VoucherTypeID from `assetregister` as assettbl JOIN `ledger` as ledgertbl on assettbl.LedgerID=ledgertbl.id where assettbl.LedgerID='".$lid."'";	
	}
  	$data = $this->m_dbConn->select($sql);
  }
  
  if($gid == 3)
  {
	$sql = "select Date, ledgertbl.ledger_name as Particular, sum(Debit) as Debit, sum(Credit) as Credit,VoucherID,VoucherTypeID from `incomeregister` as incometbl JOIN `ledger` as ledgertbl on incometbl.LedgerID=ledgertbl.id where incometbl.LedgerID='".$lid."'";
  	$data = $this->m_dbConn->select($sql);
  
  }
  
  if($gid == 4)
  {
  	$sql = "select Date,ledgertbl.ledger_name as Particular, sum(Debit) as Debit, sum(Credit) as Credit,VoucherID,VoucherTypeID from `expenseregister` as expensetbl JOIN `ledger` as ledgertbl on expensetbl.LedgerID=ledgertbl.id where expensetbl.LedgerID='".$lid."'";
   	$data = $this->m_dbConn->select($sql);
  }
  return $data;
}	

public function get_category_name()
{						
	$sql = "select `category_id`, `category_name` from `account_category` where group_id = '".$_REQUEST['groupid']."' ORDER BY category_name ASC";		
	$res = $this->m_dbConn->select($sql);			
	if($res<>"")
	{
		$aryResult = array();
		array_push($aryResult,array('success'=>'0'));
		$show_dtl = array("id"=>'1', "category"=>'Primary');
		array_push($aryResult,$show_dtl);
		foreach($res as $k => $v)
		{
			$show_dtl = array("id"=>$res[$k]['category_id'], "category"=>$res[$k]['category_name']);
			array_push($aryResult,$show_dtl);
		}
		echo json_encode($aryResult);
	}
	else
	{		
		$aryResult = array();			
		$show_dtl = array("id"=>'1', "category"=>'Primary');
		array_push($aryResult,$show_dtl);
		$show_dtl = array(array("success"=>1), array("message"=>'No Data To Display'));
		array_push($aryResult,$show_dtl);
		echo json_encode($aryResult);
	}

}

public function display1($rsas)
{
	
	$thheader = array('Ledger Name');
	$this->display_pg->mainpg = "ledger_details.php";
	$res = $this->show_list_ledger($rsas, "");
	return $res;
}
		
		
public function pgnation($groupid, $categoryid)
{
	
	$sql1 = "select `id`,`ledger_name` from `ledger` where `categoryid`=".$categoryid."  and society_id='".$_SESSION['society_id']."' ORDER BY ledger_name ASC";
	$result = $this->m_dbConn->select($sql1);
	$this->show_list_ledger($result, $groupid);	
}
	
	
public function show_list_ledger($res, $groupid)
{
	
	if($res<>"")
	{
	?>
	<center>
	<table align="center" width="50%">
	<tr>
	<td>
	<table id="example" class="display" cellspacing="0" width="100%">
	<thead>
	<tr>
		<th >Ledger Name</th>
		<th >Balance</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($res as $k => $v)
	{
		
		$get_due_value = $this->details($_REQUEST['gid'],$res[$k]['id']);
		$DebitAmt = $get_due_value[0]['Debit'];
		$CreditAmt = $get_due_value[0]['Credit'];
		$categoryid = $this->obj_utility->getParentOfLedger($res[$k]['id']);
		
		if($categoryid['category'] == BANK_ACCOUNT)
		{
			$BalanceAmt = $CreditAmt - $DebitAmt;
		}
		else
		{
			
			$BalanceAmt = $DebitAmt-$CreditAmt;
			
			
		}
		//echo 'Balance :'.$BalanceAmt =$DebitAmt - $CreditAmt;//print_r($get_due_value);
		
		?>
	<tr>
	<td ><?php echo $res[$k]['ledger_name'];?></td>
	<td >    
		<?php if($groupid == LIABILITY) { $BalanceAmt = abs($BalanceAmt); }?>                 
	   <a href="view_ledger_details.php?&lid=<?php echo $res[$k]['id'];?>&gid=<?php echo $_REQUEST['gid'];?>" style="color:#0000FF;"><b><?php echo number_format($BalanceAmt,2);?></b></a>
		
		</td>
   </tr>
	<?php }?>
	</tbody>
	</table>
	</td>
	</tr>
	</table>
	<?php
	
	}
	else
	{
		?>
		<table align="center" border="0">
		<tr>
			<td><font color="#FF0000" size="2"><b>No Records Found.</b></font></td>
		</tr>
		</table>
		</center>
		<?php	
	}
}
}

?>

