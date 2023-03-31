<?php
include_once("../classes/pp_loan.class.php");
include_once("../classes/include/dbop.class.php");
	$dbConn = new dbop();
	$obj_loan=new pp_loan($dbConn, '');
echo $_REQUEST["method"]."@@@";
if($_REQUEST["method"]=="show" || $_REQUEST["method"]=="edit")
	{
		
	// $select_type=$obj_loan->getMembers();
	// $loan_select_type=$obj_loan->getLoanSubCategory();

	foreach($select_type as $k => $v)
		{
		foreach($v as $kk => $vv)
			{
			echo $vv."#";
			}
		}


	}
if($_REQUEST["method"]=="delete")
	{
	$obj_society->deleting();
	return "Data Deleted Successfully";
	}
?>
