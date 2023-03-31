<?php
include_once("../classes/dbconst.class.php");
include_once("../classes/unit.class.php");
include_once("../classes/include/dbop.class.php");
include_once("../classes/tenant.class.php");
// $dbConn=new dbop();
 //$obj_tenant = new tenant($dbConn);
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$obj_tenant = new tenant($dbConn);
$obj_unit = new unit($dbConn,$dbConnRoot);
if(($_REQUEST["method"]<>"fetch") && ($_REQUEST["method"]<>"fetchList") )

{
echo  $_REQUEST["method"]."@@@";
}

if($_REQUEST["method"]=="edit")
{
	$select_type = $obj_tenant->selecting($_REQUEST['TenantId']);
	echo json_encode($select_type);
	/*foreach($select_type as $k => $v)
	{
		foreach($v as $kk => $vv)
		{
			echo $vv."#";
		}
	}*/
}

if($_REQUEST["method"] == "view")
{
	$select_type = $obj_tenant->selecting($_REQUEST['TenantId']);
	echo json_encode($select_type);
}

if($_REQUEST["method"]=="delete")
{
	$obj_tenant->deleting($_REQUEST['TenantId']);
	return "Data Deleted Successfully";
}

if($_REQUEST["method"]=="del_photo")
	{
	$baseDir = dirname( dirname(__FILE__) );
	$sr_id=$_REQUEST['qr'];
	$img=$_REQUEST['img'];
	$sql2 = "select `img` FROM `tenant_module` WHERE tenant_id='$sr_id'";
	$res2 = $dbConn->select($sql2);
	$image=$res2[0]['img']; 
    $sql3="update `tenant_module` set `img`= '' where `tenant_id`='$sr_id'";
	$res3 = $dbConn->update($sql3);
	if (file_exists($baseDir.'\Tenant\\'.$img)) 
	{
	 	unlink($baseDir.'\Tenant\\'.$img);
		echo "file deleted";
	}
	else
	{
		echo "not deleted file";
	}
}

/*-----------------------------------------------Delete Document --------------------------------------------------*/

if($_REQUEST["method"]=="del_Doc")
	{
	//$baseDir = dirname( dirname(__FILE__) );
	$doc_id=$_REQUEST['id'];
	//echo $doc_id;
	$TenantId=$_REQUEST['Tid'];
	 $select = "select `doc_id` FROM `tenant_module` WHERE tenant_id='".$TenantId."'";
	$res =$dbConn->select($select);
	$Document=$res[0]['doc_id'];
	if($Document <> "")
	{
		$Doc_id = explode(',', $Document);
	}
	
	 $sql2="update `documents` set `status`= 'N' where `doc_id`='".$doc_id."'";
	$res2 = $dbConn->update($sql2);
	
	$Document_ID = array();
	for($i = 0; $i < sizeof($Doc_id); $i++)
	{
		if($Doc_id[$i] != $doc_id)
		{
			array_push($Document_ID, $Doc_id[$i]);
		}
	}
	
	//$doc_id=$res2[0]['doc_id'];
	 //$sql3 = "select * from documents where doc_id='".$doc_id."'";
	//$res3 = $dbConn->select($sql3);
	
	//array_push($Doc_id,$res3);
	
	$documents=implode(',' ,$Document_ID);
	$updateInsert="update `tenant_module` set doc_id='".$documents."' where tenant_id='".$TenantId."'";
	$update = $dbConn->update($updateInsert);
	
}


/*------------------------------------------ fetch tenant history------------------------------------*/
if($_REQUEST["method"] == 'fetch')
{
	$TenantList = $_REQUEST['TenantList'];
	$societyID = $_REQUEST['societyID'];
	$sqlTenant = "select `society_name` from `society` where `society_id` = '".$societyID."' " ;
	$resTenant = $dbConn->select($sqlTenant);
	
	$finalArray = $obj_unit->fetchTenantDetails($TenantList);
	
	$obj_unit->displayTenantResults($finalArray,$resTenant);
	
}

/*------------------------------------------ fetch tenant history for user------------------------------------*/
if($_REQUEST["method"] == 'fetchList')
{
	$TenantList = $_REQUEST['TenantList'];
	$societyID = $_REQUEST['societyID'];
 	$unitID = $_REQUEST['unitID'];
	$sqlTenant = "select `society_name` from `society` where `society_id` = '".$societyID."'" ;
	$resTenant = $dbConn->select($sqlTenant);
	
	$finalArray = $obj_unit->fetchTenantDetailsForUser($TenantList,$unitID);
	
	$obj_unit->displayTenantResultsHistory($finalArray,$resTenant);
	
}

?>
