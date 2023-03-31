
<?php include_once("../classes/account_subcategory.class.php");
include_once("../classes/include/dbop.class.php");
include_once("../classes/utility.class.php");
$dbConn = new dbop();
$obj_account_subcategory = new account_subcategory($dbConn);

echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="edit")
{
	$select_type = $obj_account_subcategory->selecting();

	foreach($select_type as $k => $v)
	{
		echo json_encode($v);
		/*foreach($v as $kk => $vv)
		{
			echo $vv."#";
		}*/
	}
}

if($_REQUEST["method"]=="delete")
{
	$obj_account_subcategory->deleting();
	return "Data Deleted Successfully";
}


if($_REQUEST["method"]=="FetchGroup")
{
	$obj_utility = new utility($dbConn);
	//$Detail = json_decode(str_replace('\\', '', $_REQUEST['data']), true);
	//print_r($Detail);
	$ledgerid=$_REQUEST['ledgerid'];
	//echo "ledgerid".$ledgerid;
	$ledgerParent = $obj_utility->getParentOfLedger($ledgerid);
	echo $ledgerParent['group'];
}

?>