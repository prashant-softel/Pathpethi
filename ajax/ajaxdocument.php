
<?php
include_once("../classes/include/dbop.class.php");
include_once("../classes/document.class.php");
include_once("../classes/notice.class.php");

	  $dbConn = new dbop();
	  $dbConnRoot = new dbop(true);
	$obj_document = new document($dbConn, $dbConnRoot);
	$obj_notice = new document($dbConn, $dbConnRoot);

	if($_REQUEST["method"]=="edit")
	{
		echo $_REQUEST["method"]."@@@";
	
	$select_type=$obj_document->selecting();
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
		echo $_REQUEST["method"]."@@@";
	
		$obj_document->deleting();
		return "Data Deleted Successfully";
	}
	if($_REQUEST["method"]=="fetch_UnitDocs")
	{
		$UnitID = $_REQUEST["unit_id"];
		$sqlNotices = "select nc.id,nc.doc_id from notices as nc JOIN display_notices as dc ON nc.id = dc.notice_id where dc.unit_id='".$UnitID."'";
		//echo "<br>".$sqlNotices;
		$resultNotices = $dbConn->select($sqlNotices);
		//echo "<pre>";
		//print_r($resultNotices);
		//echo "</pre>";
		$arFolders = array();
		$cntr = 0;
		foreach ($resultNotices as $sNoticeID => $arNoticeDetails) 
		{
			//print_r($sNoticeID);
			$DocID = $arNoticeDetails["doc_id"];
			//echo "<br>id:".$DocID;
			if($DocID > 0)
			{
				$sqlDocDesc= "select doc_type from document_type where ID='".$DocID."'";
				//echo "<br>".$sqlDocDesc;
				$result = $dbConn->select($sqlDocDesc);

				//print_r($result);
				//echo $result[0]["doc_type"];
				$FolderName = $result[0]["doc_type"];
				array_push($arFolders, $FolderName);
				//$arFolders[$cntr] = $FolderName;
				$cntr++;
			}
		}

		echo json_encode($arFolders);
		
		
	}
?>
