<?php 
include_once("../classes/include/dbop.class.php");
include_once("../classes/dbconst.class.php");
include_once("../classes/ContributionLedgerDetailed.class.php");
include_once("../classes/genbill.class.php");
	  $dbConn = new dbop();
$obj_ContributionLedgerDetailed = new ContributionLedgerDetailed($dbConn);
$obj_genbill = new genbill($dbConn);


if($_REQUEST["method"] == 'fetch')
{

	$finalArray = array();
	$societyID = $_REQUEST['societyID'];
	$unitIDArray = json_decode(str_replace('\\', '', $_REQUEST['unitIDArray']), true);
	$sqlBillcycle = "select `society_name` from `society` where `society_id` = '".$societyID."' " ;
	$resBillcycle = $dbConn->select($sqlBillcycle);
	
	echo "<div style='display:none;' id='societyname'><center><h1><font>"  .$resBillcycle[0]['society_name']. "</font></h1></center></div>";	
	echo "<div><center><font>BILL SUMMARY WITH BIFURCATION</font></center></div>";	
		
	for($i = 0 ;$i < sizeof($unitIDArray); $i++)
	{
		//$tempArray = $obj_ContributionLedgerDetailed->startProcess($societyID,$unitIDArray[$i]);
		$finalArray = $obj_ContributionLedgerDetailed->getCollection($societyID,$unitIDArray[$i]);

		if($_REQUEST['ignore_zero'] == 'true')
		{
			$finalArray = $obj_genbill->unsetZeroKeysFromArray($finalArray , $obj_ContributionLedgerDetailed->checkZero);
		}
		
		if(sizeof($finalArray) > 0 )
		{
			$obj_ContributionLedgerDetailed->displayResults1($finalArray);
			//array_push($finalArray,$tempArray);
		}
	}
	
	//$finalArray = $obj_ContributionLedgerDetailed->getCollection($societyID,$unitIDArray[$i]);
	
	
	
	//print_r($finalArray);
	//$obj_ContributionLedgerDetailed->displayResults1($finalArray);
	
}

?>