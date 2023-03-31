<?php	include_once("../classes/genbill.class.php");
		include_once("../classes/include/dbop.class.php");
		include_once("../classes/include/fetch_data.php");
	 	$dbConn = new dbop();
		
		$obj_unit=new genbill($dbConn);
		$strBillNo = "BillNumber";
		$strBillDate = "BillDate";
		$strDueDate = "DueDate";
		$strPeriod_id = "period_id";
		$strSocietyID = "society_id";
		$strWingID = "wing_id";
		$strUnitID = "unit_id";
		$strAdjustmentCredit="txtRebate";
		
		//$strAccountHead = "AccountHead";
		$sBillNo = $_REQUEST[$strBillNo];
		$sBillDate = $_REQUEST[$strBillDate];
		$sDueDate = $_REQUEST[$strDueDate];
		$sPeriodID = $_REQUEST[$strPeriod_id];
		$sSocietyID = $_REQUEST[$strSocietyID];
		$sWingID = $_REQUEST[$strWingID];
		$sUnitID = $_REQUEST[$strUnitID];
		$sAdjustmentCredit = $_REQUEST[$strAdjustmentCredit];
		
		$Amount = $_REQUEST["txth1"];
		$arJournal = $_REQUEST["Journals"];		
		$arJorunals = explode(',', $_REQUEST["Journals"]);		
		$arData = array();
		echo "<script>alert('".$sBillNo."')</script>";
		//echo "tests" . $arJorunals ;
		//echo "<script>alert('test val:"  .$Amount . "');<//script>";	
		for($i=0; $i < sizeof($arJorunals) ;$i++)
		  {		
				{												
					{
						$Key = $arJorunals[$i];												
						$value = $_REQUEST["txth" .$arJorunals[$i] ];
						
						//echo "<script>alert('test:". $Key . "');<//script>";
						//echo "<script>alert('test value:". $value. "');<//script>";
						$arTemp = array("key"=>$Key, "value"=>$value);
						array_push($arData, $arTemp);
					}
				}
		  }
		  //$arData = array("key" => "test");
		  
		//$sManualBillNo = $_REQUEST["txtManualBillNo"];
//		$sRebate = $_REQUEST["txtRebate"];
//		$arEditFields = array($strBillNo => $sBillNo, $strBillDate => $sBillDate, $strDueDate => $sDueDate, "txtManualBillNo" => $sManualBillNo, "txtRebate" => $sRebate); 
		$objUpdateBillDetails = new CUpdatedBillDetails($dbConn);
		$objUpdateBillDetails->sBillNo = $sBillNo;
		$objUpdateBillDetails->sBillDate = $sBillDate;
		$objUpdateBillDetails->sDueDate = $sDueDate;
		$objUpdateBillDetails->sPeriodID = $sPeriodID;
		$objUpdateBillDetails->sSocietyID = $sSocietyID;
		$objUpdateBillDetails->sWingID  = $sWingID;
		$objUpdateBillDetails->sUnitID = $sUnitID;
		$objUpdateBillDetails->sAdjustmentCredit = $sAdjustmentCredit;
		$objUpdateBillDetails->arrData = $arData;
//		for($i=0; $i < sizeof($arJournal) ;$i ++)
		{
		  //echo "<tr><td align=center>".$counter."</td><td align=left>".$key ."</td><td align=right>".$value ."</td></tr>";
		  //echo $value;
//		  echo "<tr><td align=center>".$counter."</td><td colspan=3>". $[$i]["key"] ."</td><td align=right>".$data[$i]["value"] ."</td></tr>";
		//  echo $objFetchData->GetHeadingFromAccountHead(1);
		/*echo "<script>alert('test:". $arJournal[$i]["key"] ."');</script>";*/
		  //$SubTotal += $data[$i]["value"];
		  //$counter++;
	//	  		echo "<script>alert('".$arJorunal[$i]["key"]."')<//script>";
		//  echo $arJorunal[$i];
		}
		//echo "<script>alert('std')<//script>";
		//echo "<script>alert('duedate".$sDueDate."')<//script>";
//				echo "<script>alert('". $arJorunal[0]["value"]."')<///script>";
		$objUpdateBillDetails->arJournals = $arJorunal;
		echo "in main";
		$obj_unit->SetUpdatedBillParameters($objUpdateBillDetails);
		//echo "<script>alert('after set')<//script>";
		$validator = $obj_unit->startProcess();
?>
<html>
<body>
<form name="Goback" method="post" action="<?php echo $obj_unit->actionPage; ?>">

	<?php
		if($validator=="Insert")
		{
			$ShowData="Record Added Successfully";
		}

	?>
	
    </form>
    <script>
	document.Goback.submit();
</script>

    </body>
    </html>