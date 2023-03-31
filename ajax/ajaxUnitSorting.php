<?php include_once("../classes/unit_sorting.class.php");
include_once("../classes/include/dbop.class.php");
	  $dbConn = new dbop();
	  $dbConnRoot = new dbop(true);
$obj_unit_sorting = new unit_sorting($dbConn, $dbConnRoot);

echo $_REQUEST["update"]."@@@";
if(isset($_REQUEST["update"]))
{
	$unitOrderDetail = json_decode(str_replace('\\', '', $_REQUEST['data']), true);
	
	for($iCnt = 0 ; $iCnt <= sizeof($unitOrderDetail); $iCnt++)
	{
		//$SortOrderID = $unitOrderDetail[$iCnt]["SortOrderID"];
		$SortOrderID = ($iCnt + 1) * 100;
		$WingID = $unitOrderDetail[$iCnt]["WingID"];
		$UnitNO = $unitOrderDetail[$iCnt]["UnitNO"];
		$UnitID = $unitOrderDetail[$iCnt]["UnitID"];
		//echo "<br />";
		//$UnitNO=$unitOrderDetail[$iCnt];
		$obj_unit_sorting->UpdateUnitTable($SortOrderID,$UnitNO,$UnitID,$WingID);
		
	}
}

?>