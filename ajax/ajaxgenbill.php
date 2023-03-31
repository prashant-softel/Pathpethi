<?php
	include_once("../classes/genbill.class.php");
	include_once("../classes/include/dbop.class.php");
	include_once ("../classes/include/exportToExcel.php");
	$dbConn = new dbop();
	$obj_genbill = new genbill($dbConn);
        //remove all empty spaces after php closing brackets
        ob_clean();

	if(isset($_REQUEST['getnote']))
	{
		$get_notes = $obj_genbill->getNotes($_REQUEST['society'], $_REQUEST['period'], $_REQUEST['supplementary_bill']);
	}
	else if(isset($_REQUEST['setnote']))
	{
		$set_notes = $obj_genbill->setNotes($_REQUEST['note'], $_REQUEST['period'], $_REQUEST['supplementary_bill']);
	}
	else if(isset($_REQUEST['getsize']))
	{
		$get_font = $obj_genbill->getFontSize($_REQUEST['society'], $_REQUEST['period'], $_REQUEST['supplementary_bill']);
	}
	else if(isset($_REQUEST['setfont']))
	{
		$set_font = $obj_genbill->setFont($_REQUEST['font'], $_REQUEST['period'], $_REQUEST['supplementary_bill']);
	}
	else if(isset($_REQUEST['getdate']))
	{
		$bShowDueDate = true;
		if(isset($_REQUEST['hide_duedate']) && $_REQUEST['hide_duedate'] == 1 && $_REQUEST['supplementary_bill'] ==1)
		{
			$bShowDueDate = false;
		}
		
		$get_dates = $obj_genbill->getBillAndDueDate($_REQUEST['period'], $_REQUEST['society'], $_REQUEST['supplementary_bill'],$bShowDueDate);
		echo $get_dates['BillDate'] . "@@@" . $get_dates['DueDate'];
	}
	else if(isset($_REQUEST['Export']))
	{		
		$details = $obj_genbill->getCollectionOfDataToDisplay($_REQUEST['society_id'], $_REQUEST['wing_id'], $_REQUEST['unit_id'], $_REQUEST['period_id']);			
		exportExcel($details);
	}
	else if(isset($_REQUEST['method']) && $_REQUEST["method"]=="BillEdit")
	{
		echo $_REQUEST["method"]."@@@";		
		$Detail = json_decode(str_replace('\\', '', $_REQUEST['data']), true);
		//print_r($Detail);
		$CurrentBillInterestAmount=$_REQUEST["InterestOnPrincipleDue"];
		//echo "<br>CurrentBillInterestAmount:".$CurrentBillInterestAmount;
		$InterestArrears =$_REQUEST["IntrestOnPreviousarrears"];
		$PrincipalArrears=$_REQUEST["PrinciplePreviousArrears"];
		$AdjustmentCredit=$_REQUEST["AdjustmentCredit"];
		$UnitID=$_REQUEST["UnitID"];
		$PeriodID=$_REQUEST["PeriodID"];
		$SupplementaryBill = $_REQUEST["SupplementaryBill"];
		$obj_genbill->BillDetailsUpdate($Detail,$UnitID,$PeriodID,$CurrentBillInterestAmount,$InterestArrears,$PrincipalArrears,$AdjustmentCredit, $SupplementaryBill);
		
	}
	else if(isset($_REQUEST['method']) && $_REQUEST["method"]=="bcheckLatestPeriod")
	{
		$bLatestPeriod = 'failed';		
		$sql = "SELECT max(PeriodID) as PeriodID FROM `billdetails` WHERE BillType = '" . $_REQUEST['BT'] . "'";
		$res = $dbConn->select($sql);
                //echo $res[0]['PeriodID'].":".$_REQUEST["iPeriodID"];
		if(sizeof($res) > 0 && $res[0]['PeriodID'] ==$_REQUEST["iPeriodID"])
		{
		    $bLatestPeriod = 'success';
		}
		echo $bLatestPeriod;
	}
     else if(isset($_REQUEST['method']) && $_REQUEST["method"]=="performDelete")
	{
				$IsSupplementaryBill = false;	
				if($_REQUEST["IsSupplemenataryBill"] == '1')
				{
					$IsSupplementaryBill = true;	
				}
				
                if($_REQUEST["iUnitID"] == '0')
                {
                    //fetch all unit
                    $sql = "SELECT `unit_id` FROM `unit`";
                    $res = $dbConn->select($sql);
                    for($i = 0;$i < sizeof($res);$i++)
                    {
                       $obj_genbill->DeleteBillDetails($res[$i]["unit_id"], $_REQUEST["iPeriodID"],true,true,$IsSupplementaryBill);  
                    }
                    $obj_genbill->delTrace = "Deleted Bill for Unit <All> PeriodID <".$_REQUEST["iPeriodID"].">";
                    $obj_genbill->m_objLog->setLog($obj_genbill->delTrace, $_SESSION['login_id'], 'billdetails', 0);
                }
                else
                {
                  $obj_genbill->DeleteBillDetails($_REQUEST["iUnitID"], $_REQUEST["iPeriodID"],true,false,$IsSupplementaryBill); 
                }
	}
?>