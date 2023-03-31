
<?php
include_once("../classes/service_prd_reg.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
	  $dbConnRoot = new dbop(true);
$obj_service_prd_reg=new service_prd_reg($dbConn, $dbConnRoot);
echo $_REQUEST["method"]."@@@";
if($_REQUEST["method"]=="edit")
	{
	$select_type=$obj_service_prd_reg->selecting();
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
	$obj_service_prd_reg->deleting();
	return "Data Deleted Successfully";
	}
	
if($_REQUEST["method"] == 'aprove')
{
	// echo "update `photos` set cover='1' where `id`='".$_REQUEST['photoID']."'";
	echo  $aprove = "update `service_prd_reg` set `active`='1' where `service_prd_reg_id`='".$_REQUEST['id']."'";
	$res = $dbConnRoot->update($aprove);
}
if($_REQUEST["method"]=="del_Doc")
	{
	$baseDir = dirname( dirname(__FILE__) );
	echo $baseDir;
	
	$SprID=$_REQUEST['sprId'];
	$DocName=$_REQUEST['Doc'];
	 echo $sql2 = "select `attached_doc` FROM `spr_document` WHERE spr_document_id ='$SprID'";
	 $res = $dbConnRoot->select($sql2);
	 $image=$res[0]['attached_doc'];
		
		if(file_exists($baseDir.'/Service_Provider_Documents/'.$image))
		{
			echo $baseDir.'/Service_Provider_Documents/'.$image;
			 //unset($image);
			 unlink($baseDir.'\Service_Provider_Documents\\'.$image) ;
			 echo "file deleted";  
				
		}
		else
		{
			echo "not deleted file";
		}
		$sql3 = "Delete FROM `spr_document` WHERE spr_document_id ='$SprID'";
		$res2 = $dbConnRoot->delete($sql3);
					
}
?>
