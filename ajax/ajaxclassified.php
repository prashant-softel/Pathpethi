<?php
include_once("../classes/include/dbop.class.php");
include_once("../classes/dbconst.class.php");
include_once("../classes/addclassified.class.php");

$m_dbConn = new dbop();
$m_dbConnRoot = new dbop(true);
$obj_classified=new classified($m_dbConnRoot,$m_dbConn);

if($_REQUEST["method"] == 'aprove')
{
	// echo "update `photos` set cover='1' where `id`='".$_REQUEST['photoID']."'";
	 $aprove = "update `classified` set `status`='Y' where `id`='".$_REQUEST['id']."'";
	$res = $m_dbConnRoot->update($aprove);
}

echo $_REQUEST["method"]."@@@";
if($_REQUEST["method"]=="edit")
	{
		
	$select_type=$obj_classified->selecting();
	foreach($select_type as $k => $v)
		{
		foreach($v as $kk => $vv)
			{
			echo $vv."####";
			}
		}
	}
if($_REQUEST["method"]=="delete")
	{
	$obj_classified->deleting();
	return "Data Deleted Successfully";
	}
	
	
	
if($_REQUEST["method"]=="del_photo")
	{
	$baseDir = dirname( dirname(__FILE__) );
	echo $baseDir;
	
	$cl_id=$_REQUEST['id'];
	$img=$_REQUEST['img'];
	  $sql2 = "select `img` FROM `classified` WHERE id='$cl_id'";

	$res2 = $m_dbConnRoot->select($sql2);
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
	
    $sql3="update classified set `img`= '$image_coll' where id='$cl_id'";
	$res3 = $m_dbConnRoot->update($sql3);
	//echo $baseDir.'/ads/'.$image_collection[0];
	//if (file_exists($baseDir.'/ads/'.$image)) 
	echo $baseDir.'\ads\\'.$img;
	if (file_exists($baseDir.'\ads\\'.$img)) 
	{
		
		unlink($baseDir.'\ads\\'.$img);

		//unlink($baseDir.'/ads/'.$image);
		echo "file deleted";
	}
	else
	{
		echo "not deleted file";
	}
		
}
?>