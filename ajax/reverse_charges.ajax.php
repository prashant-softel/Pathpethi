
<?php 
include_once("../classes/reverse_charges.class.php");
include_once("../classes/include/dbop.class.php");
include_once("../classes/bill_period.class.php");
$m_dbConn = new dbop();	
$obj_reverse_charges = new reverse_charges($m_dbConn);
$obj_bill_period = new bill_period($dbConn);



if($_REQUEST["method"] == "getperiod")
{
	if(isset($_REQUEST['cycleID']))
	{
		$get_unit = $obj_reverse_charges->get_period($_REQUEST['cycleID']);
	}
	else
	{
		$get_unit = $obj_reverse_charges->get_period(0);
	}
}
else if($_REQUEST["method"] == "fetch_datatable")
{
	$unit_id = $_REQUEST['unit_id'];
	$bill_type = $_REQUEST['bill_type'];
	$bill_period = $_REQUEST['bill_period'];
	$trans_type = $_REQUEST['trans_type'];
	
	$str = $obj_reverse_charges->pgnation02($unit_id,$bill_type,$bill_period,$trans_type);
	
	echo $str;
}
else
{
	echo $_REQUEST["method"]."@@@";
}

if($_REQUEST["method"]=="edit")
{
	$select_type = $obj_reverse_charges->selecting();

	foreach($select_type as $k => $v)
	{
		foreach($v as $kk => $vv)
		{
			echo $vv."#";
		}
	}
}

if(isset($_REQUEST['id']))
{
	$id = $_POST['id'];
	$sql = "Update reversal_credits SET Status = '0' where ID = '".$id."' ";
	$res = $m_dbConn->update($sql);
	echo $res;
	
}

if(isset($_REQUEST['date']) && isset($_REQUEST['year_id']) && isset($_REQUEST['billtype']))
{
	
		
		 $date 	   = $_POST['date'];
		 $year_id  = $_POST['year_id'];
		 $billtype = $_POST['billtype'];
		 
			 $sql = "select * from billregister where BillDate = '".$date."'";
			 $res = $m_dbConn->select($sql);
			 if($res)
			 {
				  echo $res[0]['PeriodID']."#";
				 
			 }
			
			 
			 $new_billtype = $obj_reverse_charges->getBillType($date, $year_id, $billtype);
			 echo $new_billtype;
			 
		 
}
if($_REQUEST['mode'] == 'billperiod')
{
	// echo "Lol";die;
		if(isset($_REQUEST['year_id']) && isset($_REQUEST['billtype']) && isset($_REQUEST['role']))
		{
			
			// echo "hi ajax";die;
			$year_id  	= $_POST['year_id'];
			$billtype 	= $_POST['billtype'];
			$role 		= $_POST['role'];
			// $operation 	= $_POST['operation'];
			$new_billperiod = $obj_reverse_charges->getBillPeriod($year_id, $billtype, $role);
			
			    foreach($new_billperiod as $k => $v)
				{
												
					foreach($v as $kk => $vv)
					{
						echo $vv."#";
					}
				} 
		
		}
}

if($_REQUEST["method"]=="delete")
{
	$delete_id = $obj_reverse_charges->deleting();
	foreach($delete_id as $k => $v)
	{
		foreach($v as $kk => $vv)
		{
			echo $vv."#";
		}
	}
	// return "Data Deleted Successfully";
}

?>