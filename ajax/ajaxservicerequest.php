<?php 
include_once("../classes/include/dbop.class.php");
include_once("../classes/servicerequest.class.php");
$m_dbConn = new dbop();
$obj_servicerequest = new servicerequest($m_dbConn);

echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="edit")
{
	$select_type = $obj_servicerequest->selecting($_REQUEST['requestId']);

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
	$obj_servicerequest->deleting($_REQUEST['requestId']);
	return "Data Deleted Successfully";
}

if($_REQUEST["method"]=="del_photo")
	{
	$baseDir = dirname( dirname(__FILE__) );
	//echo $baseDir;
	//print_r($_REQUEST);
	$sr_id=$_REQUEST['qr'];
	$img=$_REQUEST['img'];
	  $sql2 = "select `img` FROM `service_request` WHERE request_id='$sr_id'";

	$res2 = $m_dbConn->select($sql2);
	 $image=$res2[0]['img'];
			$image_collection = explode(',', $image);
			//echo $baseDir.'/ads/'.$image_collection[0];
			for($i=0;$i<sizeof($image_collection);$i++)
			{
				if($image_collection[$i]==$img)
				{
					 unset($image_collection[$i]); 
					 break;
				}
			}
	
	$image_coll = implode(',', $image_collection);
	
     $sql3="update `service_request` set `img`= '$image_coll' where `request_id`='$sr_id'";
	$res3 = $m_dbConn->update($sql3);
	//echo $baseDir.'/ads/'.$image_collection[0];
	//if (file_exists($baseDir.'/ads/'.$image)) 
	//echo $baseDir.'\ads\\'.$img;
	if (file_exists($baseDir.'\upload\main\\'.$img)) 
	{
		
		unlink($baseDir.'\upload\main\\'.$img);

		//unlink($baseDir.'/ads/'.$image);
		echo "file deleted";
	}
	else
	{
		echo "not deleted file";
	}
		
}
?>
