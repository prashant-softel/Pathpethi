
<?php include_once("../classes/servicerequest_master.class.php");	
	include_once("../classes/include/dbop.class.php");
	include_once("../classes/servicerequest.class.php");
	$dbConn = new dbop();
	$obj_cat = new serviceRequest_Category($dbConn);		
	$obj_servicerequest = new servicerequest($dbConn);

echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="edit")
{
	$select_type = $obj_cat->selecting();

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
	$obj_cat->deleting();
	return "Data Deleted Successfully";
}

if($_REQUEST["method"]=="getEmail")
{	
	$email = $obj_cat->getEmailOfMember();	
	echo $email;
}

if($_REQUEST["method"]=="getCategoryEmail")
{	
	$email = $obj_servicerequest->getEmailFromCategory();
	echo $email;
}


?>