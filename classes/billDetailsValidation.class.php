<?php
include_once("include/dbop.class.php");
include_once("utility.class.php");
include_once("dbconst.class.php");
include_once "unit_report.class.php";
include_once "include/fetch_data.php";
//include_once "list_member.class.php";


class billValidation extends dbop
{
	public $m_dbConn;
	public $obj_Utility;
	public $obj_unit_report;
	public $objFetchData;
	//public $objMemberData;
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->obj_Utility = new utility($this->m_dbConn);
		$this->obj_unit_report = new unit_report($this->m_dbConn);
		$this->objFetchData = new FetchData($this->m_dbConn);
		//$this->objMemberData=new list_member($this->m_dbConn);
					
	}
	
	function getPeriod($iyearID = 0)
	{
		$period = array();
		
		//$sql = "SELECT `ID`, `Type` FROM `period`";
		if($iyearID == 0)
		{
			$sql = "Select periodtbl.ID, periodtbl.Type, yeartbl.YearDescription from period as periodtbl JOIN year as yeartbl ON periodtbl.YearID = yeartbl.YearID";		
		}
		else
		{
			//$sqlII = "SELECT `bill_cycle` FROM `society`";
			//$data = $this->m_dbConn->select($sqlII);
			
			$sql = "Select ID,Type from period where YearID = '".$iyearID."'";// and  Billing_cycle = '".$data[0]['bill_cycle']."' ";	
		}
		
		$result = $this->m_dbConn->select($sql);
		
		if(sizeof($result) > 0)
		{
			for($i = 0; $i < sizeof($result); $i++)
			{
				if($iyearID == 0)
				{
					$period[$result[$i]['ID']] = $result[$i]['Type'] . " "  . $result[$i]['YearDescription'];
				}
				else
				{
					$period[$result[$i]['ID']] = $result[$i]['Type'];	
				}
			}
			
		}
		return $period;
	}
	
	function ValidateBillDetailsTable($user)
	{
		$ShowDebugTraces = 0;

		//validate if periods created for current financial year
		$currentYearsPeriod = $this->getPeriod($_SESSION['default_year']);
		if($ShowDebugTraces)
		{
			echo "<br/>Default year <BR>" . $_SESSION['default_year'] ;
			echo "<br/>CurrentYearsPeriod <BR>";
			print_r($currentYearsPeriod);
			echo "<br/>";
		}
		if (empty($currentYearsPeriod)) 
		{
			$sqlIV = "SELECT * FROM `year` where `YearID` = '".$_SESSION['default_year']."' ";
			$resYearDesc = $this->m_dbConn->select($sqlIV);		 
   			
			echo "<br /><br /><font color='#FF0000'> No Billing Periods Found For Current Financial Year [".$resYearDesc[0]['YearDescription']."].Kindly Generate Billing  Periods For Current Year.</font> To Generate Periods<a href='' onClick=\"window.open('bill_period.php','popup','type=fullWindow,fullscreen,scrollbars=yes');\">  Click Here</a> <br /><br />";		
		}
			
		//get all unit from unit table
		//echo $unitQuery = "SELECT `id`, `unit_id`, `unit_no` FROM `unit` ORDER BY `sort_order`";
		$unitQuery ="SELECT `id`, `unit_id`, `unit_no`, mm.owner_name FROM `unit` join `member_main` as mm on unit.unit_id=mm.unit ORDER BY `sort_order`";
		//$unitQuery = "SELECT `id`, `unit_id`, `unit_no` FROM `unit` where unit_id = 8 ORDER BY `sort_order`";

		$units = $this->m_dbConn->select($unitQuery);
		//print_r($units);
		
		$period = $this->getPeriod();
		
		$OpeningPeriod = $this->obj_Utility->getOpeningBalancePeriodID();
		if($ShowDebugTraces)
		{
			echo "<br/>OpeningPeriod :<". $OpeningPeriod .">";
			echo "<br/>Periods <BR>";
			print_r($period);
			echo"<BR>Validation for Unit ". $UnitID . "<BR>";
		}
		$maintenacecounter=0;
		$suplementrycounter=0;
		$Warmaintenacecounter=0;
		$Warsuplementrycounter=0;
		
		$ShowDebugTraces =0;
		$ShowDebugTracesDetail = 0;
		//for($i = 0; $i < 2; $i++)
		for($i = 0; $i < sizeof($units); $i++)
		{
				
			$mcounter=0;
			$scounter=0;
			$wMcounter=0;
			$wScounter=0;

			$UnitID = $units[$i]['unit_id'];
			$OwnerName = $units[$i]['owner_name'];

//			if($UnitID <> 82) continue;

			//$OwnerName = "SomeName"; //$units['owner_name']
			echo "<font color='blue'> Validation For Unit : ". $units[$i]['unit_no'] . "&nbsp;&nbsp;&nbsp;&nbsp;[&nbsp;&nbsp;" . $OwnerName ."&nbsp;&nbsp;]</font><br />";		
			
			
			for($iBill = 0; $iBill <= 1; $iBill++)
			{
				if($iBill == 0)
				{
					echo '<br/>*******Validating Maintenance Bill***********<br/>';
				}
				else
				{
					echo '<br/>*******Validating Supplementary Bill***********<br/>';
				}
				
				//$sql = "SELECT * FROM `billdetails` WHERE `UnitID` = ".$units[$i]['unit_id']. " ORDER BY `PeriodID`";
				$sql = "SELECT * FROM `billdetails` WHERE `UnitID` = ". $UnitID . " AND `BillType` = " . $iBill . " ORDER BY `PeriodID`";
				if($ShowDebugTracesDetail)
					echo $sql;
				$result = $this->m_dbConn->select($sql);
			
			
				if($ShowDebugTracesDetail)
				{
					echo "<br/>result <BR>";
					print_r($result);
					echo "<br/>";
				}
			if($result <> "")
			{
				//For each bill
				$UnitArray = $this->getAllUnits();

				$EncodeUnitArray;
				$EncodeUrl;
				
				if(sizeof($UnitArray) > 0)
				{
				$EncodeUnitArray = json_encode($UnitArray);
				$EncodeUrl = urlencode($EncodeUnitArray);
				}
				
				if(sizeof($UnitArray) > 0)
				{
					$Url = "member_ledger_report.php?&uid=".$UnitID."&Cluster=".$EncodeUrl;
					
				}
				else
				{
					$Url = "member_ledger_report.php?&uid=".$UnitID;
				}
					
	
				$resOpeBalSplit = $this->obj_Utility->getInceptionOpeningBalanceSplit($UnitID);

				if($ShowDebugTraces)
				{
					echo "<br/>resOpeBalSplit <BR>";
					print_r($resOpeBalSplit);
					echo "<br/>";
				}

				if($iBill == 0)
				{
					$openingBalance = $resOpeBalSplit[0]['TotalBillPayable'];
				}
				else
				{
					$openingBalance = $resOpeBalSplit[0]['supp_TotalBillPayable'];
				}

				$billDetails_openingBalance = 0;																		
				$TotalBalanceAmtData = 0;
				$value=0;
				$old_value='';
				
				//***validate for each Unit
				for($j = 0; $j < sizeof($result)-1; $j++)
				{
										
				if($OpeningPeriod == $result[$j]['PeriodID'])
				{
					$billDetails_openingBalance =  $result[0]['TotalBillPayable'];
					if($ShowDebugTraces)
					{						
						echo "<br/>UnitID : " . $UnitID . "<BR>";
						echo "OpeningPeriod : " .$OpeningPeriod . "<BR>";
						echo "$result[0]['PeriodID'] : " . $result[0]['PeriodID'] . "<BR>";
								//should take total
								
								//$billDetails_openingBalance = $result[0]['BillSubTotal'] + $result[0]['BillInterest'];
			
						echo "billDetails_openingBalance : " . $billDetails_openingBalance . "<BR>";
						print_r($result[0]);
						echo "<BR>BillSubTotal : " . $result[0]['BillSubTotal'] . "<BR>";
						echo "BillInterest : " . $result[0]['BillInterest'] . "<BR>";
						echo "TotalBillPayable : " . $result[0]['TotalBillPayable'] . "<BR>";
				/*		echo "<br/>2 Inside LastPeriod if block<BR>";
						echo "<br/>";					
				*/		
					}
					$TotalBalanceAmtData = $openingBalance;
					if($ShowDebugTraces)
					{
						echo "<br/><br/><br/><br/>Start PeriodID: " . $result[$j]['PeriodID'] . "<BR>";
						echo "<br/>New Opening Balance : " . $openingBalance . "<BR>";
						echo "<br/>New billdetail Opening Balance : " . $billDetails_openingBalance . "<BR>";
						echo "<br/>Start TotalBalanceAmtData 0: " . $TotalBalanceAmtData . "<BR>";
					}
					if($billDetails_openingBalance <> $openingBalance)
					{
						
						echo "<span class='odd' style='color:red'>**ERROR** Opening Balance of Ledger <". $openingBalance . " Dr> does not match with Opening Balance of Bill <" . number_format(abs($billDetails_openingBalance),2) . " Dr></span><br/>";
					}							
					
	
					continue;
				}
				
					//if(($result[$j]['PeriodID'] == 33)||($result[$j]['PeriodID'] == 32)||($result[$j]['PeriodID'] == 31))
						//$ShowDebugTraces = 1;
		
					
					if($ShowDebugTraces)
					{
						echo "<br/>Start TotalBalanceAmtData 0: " . $TotalBalanceAmtData . "<BR>";
					}
	
			
//		echo "<br/>Url : " . $Url . "<BR>";
//		echo "<br/>old_value : " . $old_value . "<BR>";
//			echo "<br/>value : " . $value . "<BR>";
					
					if ($old_value == $value++)
  					{
  						  $colour = '';
 					 }
 				 else
 					 {
  					  $colour = '#f8f8f8;';
   					 $old_value = $value;
  					}
					$old_value++;
					
					$getRecieptDate=$this->objFetchData->getBeginEndReceiptDate($UnitID,$result[$j]['PeriodID'],$result[$j]['BillType']);
					//$getMemberList=$this->objMemberData->list_member_show()
					if($ShowDebugTracesDetail)
					{
						echo "<br/>RecieptDate : <BR>";
						print_r($getRecieptDate);
					}
					if($getRecieptDate['BeginDate']=='')
					{
						$BeginDate='2015-02-01';
					}
					else
					{
						$BeginDate=$getRecieptDate['BeginDate'];
					}
					
					
					 $test="select sum(Amount) as Credit from chequeentrydetails where voucherdate >= '". $BeginDate . "' AND voucherdate <= '" . $getRecieptDate['EndDate'] . "' AND PaidBy = " . $UnitID ." AND BillType  = ". $result[$j]['BillType'];

					$creditAmt = $this->m_dbConn->select($test);

					$AmountReceivedInBillingCycle = $creditAmt[0]['Credit']; 
					$TotalBalanceAmtData  -= $creditAmt[0]['Credit'];
					if($ShowDebugTracesDetail)
					{
						echo "<br/>Subtract cheque payments of : <BR>";
						echo $test . "</br>";
						print_r($creditAmt);
						echo "<br/>TotalBalanceAmtData 1: " . $TotalBalanceAmtData . "<BR>";
					}
					
					 //$ReturnedChequeSQL ="select sum(Amount) as Debit from paymentdetails where voucherdate >= '". $BeginDate . "' AND voucherdate <= '" . $getRecieptDate['EndDate'] . "' AND PaidTo = " . $UnitID ;
$ReturnedChequeSQL = "select * from paymentdetails as pd JOIN chequeentrydetails as ced on ced.chequenumber= pd.chequeNumber where pd.voucherdate  >= '". $BeginDate . "' AND pd.voucherdate <= '" . $getRecieptDate['EndDate'] . "' AND pd.PaidTo = ". $UnitID . " and ced.BillType = ". $result[$j]['BillType']." and ced.IsReturn=1";

					$ChequeReturn = $this->m_dbConn->select($ReturnedChequeSQL);
						
					$TotalBalanceAmtData  += $ChequeReturn[0]['Amount'];
					if($ShowDebugTracesDetail)
					{
						if(sizeof($ChequeReturn) > 0)
							if($ChequeReturn[0]['Amount'] <> 0);
							{
								echo "<br/>Add ReturnedChequeSQL : <BR>";
								echo $ReturnedChequeSQL . "</br>";
								print_r($ChequeReturn);
								echo "<br/>BouncedEntry : " .  $ChequeReturn[0]['Amount'] . "<BR>";
								echo "<br/>TotalBalanceAmtData 2: " . $TotalBalanceAmtData . "<BR>";
							}
					}
					
//What is the purpsoe of this code?					
					if($result[$j]['BillType'] == 0)
					{
						$selectYear = "Select BeginingDate from year where now() between BeginingDate and EndingDate";
						$resultBeginDate = $this->m_dbConn->select($selectYear);
						$yearBeginDate = $resultBeginDate[0]['BeginingDate'];
						if($ShowDebugTracesDetail)
						{
							echo "<br/>resultBeginDate : <BR>";
							echo $selectYear ;
							print_r($resultBeginDate);
							echo "<br/>yearBeginDate : " . $yearBeginDate . "<BR>";											
						}
						
						if($this->obj_Utility->getDateDiff($BeginDate, $yearBeginDate) < 0)
						{
							//This is for voucher type 5. What is 5? Is it JV?
							 $testAmt ="select sum(Debit) as Debit, sum(Credit) as Credit from assetregister where Date >= '". $BeginDate . "' AND Date <= '" . $getRecieptDate['EndDate'] . "' AND LedgerID = " . $UnitID ." AND VoucherTypeID = 5";
							$resultAmt = $this->m_dbConn->select($testAmt);
						
							$TotalBalanceAmtData  -= $resultAmt[0]['Credit'] + $resultAmt[0]['Debit'];
							if($ShowDebugTracesDetail)
							{
								echo "<br/>AssetRegister for vouchertype 5: <BR>";
								echo $testAmt . "</br>";
								print_r($resultAmt);
								echo "Amount deducted : " . $resultAmt[0]['Credit'] + $resultAmt[0]['Debit'] . "<BR>";
								echo "<br/>TotalBalanceAmtData 3 : " . $TotalBalanceAmtData . "<BR>";
							}
						}
					}
					
					$result[$j]['CurrentBillAmount'] = $result[$j]['BillSubTotal'] + $result[$j]['AdjustmentCredit'] + $result[$j]['BillTax'] + $result[$j]['IGST'] + $result[$j]['CGST'] + $result[$j]['SGST'] + $result[$j]['CESS'] + $result[$j]['BillInterest'];
					
					$TotalBalanceAmtData += $result[$j]['CurrentBillAmount'];
					if($ShowDebugTraces)
					{
						echo "<BR>";
						print_r($result[$j]);
						echo "<BR>";
						echo "<br/>CurrentBillAmount : " . $result[$j]['CurrentBillAmount'] . "<BR>";
						echo "<br/>TotalBillPayable : " . $result[$j]['TotalBillPayable'] . "<BR>";
						echo "<br/>TotalBalanceAmtData 4: " . $TotalBalanceAmtData . "<BR>";
					}
					
					$isBillInvalid = false;
					if(number_format($TotalBalanceAmtData,2) != number_format($result[$j]['TotalBillPayable'],2))
					{  					
					if($ShowDebugTraces)
						echo "<BR>TotalBalanceAmtData : " . number_format($TotalBalanceAmtData,2)  . "<BR>$result[$j]['TotalBillPayable'] : " . number_format($result[$j]['TotalBillPayable'],2) . "<BR>";

						if($user == "report")
						{
						echo "<Div style='height:30px;background-color:" .$colour . "'>";	
						echo "<span style='color:red;'>**ERROR** Ledger Balance <a href='".$Url."' style='text-decoration: none;color: red;' target = '_blank'> [".$TotalBalanceAmtData."] and Total Payable Amount [".$result[$j]['TotalBillPayable']."] of ". $period[$result[$j]['PeriodID']] . " in the bill do not match .</a></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><a href='Maintenance_bill.php?UnitID=".$UnitID ."&PeriodID=".$result[$j]['PeriodID']."&BT=".$result[$j]['BillType']."' target='_blank'><img src='images/bin.png' style='width: 20px; height:20px'></a></span> <br />";
						echo "</div>";
								
						if($iBill==0)
						{
							$maintenacecounter++;
							$mcounter++;
						}
						else
						{
							$suplementrycounter++;
							$scounter++;
						}
				
					}
				 $value++;
				
				  }
				//$ShowDebugTraces=0;
				$ShowDetailedCheck = 0;
				 if($ShowDetailedCheck && $j > 0)
				 {				
					if($result[$j]['BillSubTotal'] <> $result[$j+1]['PrevBillPrincipal'])
					{			

						if($ShowDebugTraces)
							echo "<BR>PrevBillPrincipal : " .  $result[$j+1]['PrevBillPrincipal']  . "<BR>$result[$j]['BillSubTotal'] : " . $result[$j]['BillSubTotal'] . "<BR>";
							echo "<span style='color:red'>**ERROR** BillSubTotal[".$result[$j]['BillSubTotal']."] of ". $period[$result[$j]['PeriodID']] . " bill is not match with PrevBillPrincipal[". $result[$j+1]['PrevBillPrincipal']." of ". $period[$result[$j+1]['PeriodID']] . " bill.</span> <br />";
					}
					
					$prevArrearsTotal = $result[$j+1]['PrevPrincipalArrears'] + $result[$j+1]['PrevInterestArrears'] + $result[$j+1]['PrevBillPrincipal']+ $result[$j+1]['PrevBillInterest'];
					
					if($result[$j]['TotalBillPayable'] <> $prevArrearsTotal)
					{	
						if($ShowDebugTraces)
							echo "<BR>prevArrearsTotal : " . $prevArrearsTotal  . "<BR>$result[$j]['TotalBillPayable'] : " . $result[$j]['TotalBillPayable'] . "<BR>";
							echo "<span style='color:red'>**ERROR** Previous arrears[".number_format($prevArrearsTotal,2)."] of ". $period[$result[$j+1]['PeriodID']] . " Bill does not match with Bill Amount [".number_format($result[$j]['TotalBillPayable'],2)."] of ". $period[$result[$j]['PeriodID']] . " Bill.</span> <br/>";											
					}
					
					if($result[$j]['BillInterest'] <> $result[$j+1]['PrevBillInterest'])
					{
						if($ShowDebugTraces)
							echo "<BR>PrevBillInterest : " . $result[$j+1]['PrevBillInterest'] . "<BR>$result[$j]['BillInterest'] : " . $result[$j]['BillInterest'] . "<BR>";
							echo "<span style='color:red'>**ERROR** Previous Bill Interest[".$result[$j+1]['PrevBillInterest']. "] of ".$period[$result[$j+1]['PeriodID']]. " does not matched with Bill Interest[".$result[$j]['BillInterest']."] of ".$period[$result[$j]['PeriodID']]. " . </span><br />";
					}
				 }
				 
					if($isBillInvalid == true)
					{
						echo "**WARNING** Particulars of ". $period[$result[$j]['PeriodID']] . " bill are changed after generating ". $period[$result[$j+1]['PeriodID']] . " bill. Kindly regenerate bills after ". $period[$result[$j]['PeriodID']] .". <br />";	
						
						if($iBill==0)
						{
							$Warmaintenacecounter++;
							$wMcounter++;
		
						}
						else
						{
							$Warsuplementrycounter++;
							$wScounter++;
						}
										
					}
					
					$voucherQuery = "SELECT * FROM `voucher` WHERE `RefNo` = ".$result[$j+1]['ID'] ." AND `RefTableID` = 1";
					$details = $this->m_dbConn->select($voucherQuery);
					if($ShowDebugTracesDetail)
					{
						echo "<br/>Voucher details : <BR>" . $voucherQuery . "<BR>";
						print_r($details);
					}
	
					if($details <> "")
					{
						$total = 0;
						$TotalBalanceAmt=0;
						for($k = 1; $k < sizeof($details); $k++)
						{
							$total += $details[$k]['Credit'];
							
						}
						
						if($total <> $details[0]['Debit'])
						{
							echo "<span style='color:red'>**ERROR** Voucher details are invalid for ".$period[$result[$j+1]['PeriodID']] ." Bill. Kindly regenerate bill for ". $period[$result[$j+1]['PeriodID']] .".</span><br />";	
						}
						
						$billTotal = $result[$j+1]['BillInterest'] + $result[$j+1]['BillSubTotal'] + $result[$j+1]['AdjustmentCredit']+ $result[$j+1]['IGST'] + $result[$j+1]['CGST'] + $result[$j+1]['SGST'] + $result[$j+1]['CESS'] + $result[$j+1]['BillTax'];
						
						if($details[0]['Debit'] <> $billTotal)
						{
							echo "<span style='color:red'>**ERROR** BillTotal<". $billTotal. "> is not matched with voucher amount<".$details[0]['Debit']."> for ".$period[$result[$j+1]['PeriodID']] ." Bill. </span><br />";
						}
					}								
				}
				
				$voucherQuery = "SELECT * FROM `voucher` WHERE `RefNo` = ".$result[0]['ID'] ." AND `RefTableID` = 1";
				$details = $this->m_dbConn->select($voucherQuery);
				$details = $this->m_dbConn->select($voucherQuery);
				if($ShowDebugTraces)
				{
					echo "<br/>Voucher details 2 : " . $voucherQuery . "<BR>";
					print_r($details);
				}
				if($details <> "")
				{
					$total = 0;
					for($k = 1; $k < sizeof($details); $k++)
					{
						$total += $details[$k]['Credit'];	
					}
					
					if($total <> $details[0]['Debit'])
					{
						echo "**ERROR** Voucher details are invalid for ".$period[$result[$j+1]['PeriodID']] ." Bill. Kindly regenerate bill for ". $period[$result[$j+1]['PeriodID']] .".<br />";	
					}
					
					$billTotal = $result[0]['BillInterest'] + $result[0]['BillSubTotal'] + $result[0]['AdjustmentCredit'] + $result[0]['IGST'] + $result[0]['CGST'] + $result[0]['SGST'] + $result[0]['CESS'] + $result[0]['BillTax'];
					if($details[0]['Debit'] <> $billTotal)
					{
						echo "<span style='color:red'>**ERROR** BillTotal<". $billTotal. "> is not matched with voucher amount<".$details[0]['Debit']."> for ".$period[$result[$j+1]['PeriodID']] ." Bill. </span><br />";
					}
				}
			}
			else
			{
				echo "Bill for Unit ". $units[$i]['unit_no'] . " does not exist. <br />";	
				//echo "Bill for Unit ". $UnitID . " does not exist. <br />";	
			}
			echo "<hr />";
		}
		
		if(($mcounter > 0 || $scounter > 0) )
		{
			$backColorError="#ff00004d";
	
		}
		else
		{
			$backColorError="#ffffff";
		}
		 if(($wMcounter > 0 || $wScounter > 0))
		{
			$backColorWarning="#ffff007a";
		}
		
		else
		{
			$backColorWarning="#ffffff";
		}
		//echo "<div style='width:100%;font-size: 18px;color: #000; border-bottom: groove;padding-bottom: 40px;' > <div style='width:50%;float:left;background-color:".$backColorError.";'>
		//<span>Total Error &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;:&nbsp; &nbsp; </span>
		//<span style=''> Maintenance Bill &nbsp; &nbsp; : &nbsp; &nbsp;".$mcounter."</span>&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;
		//<span style=''>Supplementary Bill &nbsp; &nbsp;: &nbsp; &nbsp;".$scounter."</span></div>
		//<br>
		//<div style='width:50%;float:left;;background-color:".$backColorWarning."'>
		//<span>Total Warning &nbsp; &nbsp;:&nbsp; &nbsp; </span>
		//<span style=''> Maintenance Bill</span><span> &nbsp; &nbsp; : &nbsp; &nbsp;".$wMcounter."</span>&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;
		//<span style=''> Supplementary Bill</span><span> &nbsp; &nbsp;: &nbsp; &nbsp;".$wScounter."</span></div>
		//</div><br/><br/>";
		
		//echo "<BR>mcounter3 : " . $mcounter . "<BR>";
		//echo "<BR>scounter : " . $scounter . "<BR>";
		 
		echo "<div style='width:100%;font-size: 18px;color: #000; border-bottom: groove;padding-bottom: 60px;' >
		<table style='width:70%;float:left;background-color:".$backColorError.";'>
		<tr align='left' >
		<th style='width:15%' align='left'>Total Error</th><th style='width:4%'>&nbsp; &nbsp;:&nbsp; &nbsp; </th>
		<th  align='left' style='width:15%'> Maintenance Bill </th><th style='width:4%'>&nbsp; &nbsp; : &nbsp; &nbsp;</th>
		<th style='width:10%'>".$mcounter."</th>
		<th style='width:15%'' align='left'>Supplementary Bill</th><th style='width:4%'> &nbsp; &nbsp;: &nbsp; &nbsp;</th>
		<th style='width:10%'>".$scounter."</th></tr></table>
		<table style='width:70%;float:left;background-color:".$backColorWarning."'>
		<tr align='left'>
		<th  align='left' style='width:15%'>Total Warning </th><th style='width:4%' >&nbsp; &nbsp;:&nbsp; &nbsp; </th>
		<th  align='left' style='width:15%'> Maintenance Bill</th><th style='width:4%' > &nbsp; &nbsp; : &nbsp; &nbsp;</th>
		<th style='width:10%'>".$wMcounter."</th>
		<th align='left' style='width:15%'> Supplementary Bill</th><th  style='width:4%'> &nbsp; &nbsp;: &nbsp; &nbsp;</th>
		<th style='width:10%' >".$wScounter."</th></tr></table>
		</div><br/><br/>";
		}
		echo"<div style='width:100%;font-size: 20px;color: #000;background-color: #ffff007a;' id='warning'><span> Total Warning for Maintenance Bill &nbsp;&nbsp;&nbsp;< ".$Warmaintenacecounter." >&nbsp;&nbsp;&nbsp; and Total Warning for Supplementary Bill &nbsp;&nbsp;&nbsp;< ".$Warsuplementrycounter." ></span></div><br>"; 
		echo"<div style='width:100%;font-size: 20px;color:#000;background-color: #ff00004d;' id='error'><span> Total Error for Maintenance Bill &nbsp;&nbsp;&nbsp;< ".$maintenacecounter." > &nbsp;&nbsp;&nbsp; and Total Error for Supplementary Bill &nbsp;&nbsp;&nbsp;< ".$suplementrycounter." ></span></div><br>"; 
		
						
							
	}
	
	function getSocietyName($society_id)
	{
		$sql = "SELECT `society_name` FROM `society` WHERE `society_id` = ".$society_id;
		$result = $this->m_dbConn->select($sql);
		return $result[0]['society_name'];
	}
	
	function validateLedger()
	{		
		$sql = "SELECT * FROM `ledger` WHERE `show_in_bill` = 1";
		$ledgerDetails = $this->m_dbConn->select($sql);
		
		$errorMsg1 = '<font style="font-size:14px;font-weight:bold;">Following Ledgers are not of INCOME type but have "show in bill" checkbox selected.<br/>Kindly uncheck the checkbox and refresh the page to proceed with Generating Bill.<br /></font><br />';
		$errorMsg2 = "";
		$finalErrorMsg = "";
				
		for($i = 0; $i < sizeof($ledgerDetails); $i++)
		{
			$arParentDetails = $this->obj_Utility->getParentOfLedger($ledgerDetails[$i]['id']);															
			if(!(empty($arParentDetails)))
			{
				$ExpenseByGroupID = $arParentDetails['group'];								
				if(($ExpenseByGroupID == ASSET) || ($ExpenseByGroupID == EXPENSE))	
				{
					if($ledgerDetails[$i]['show_in_bill'] == 1)
					{						
						$link = "ledger.php?edt=".$ledgerDetails[$i]['id'];				                        
						$Url =	"<a href='' onClick=\"window.open('". $link ."','popup','type=fullWindow,fullscreen,scrollbars=yes');\"><font style='font-size:14px;'>". $ledgerDetails[$i]['ledger_name'] . "</font></a>";				
						$errorMsg2 .= $Url.'<br />';						
					}					
				}
			}
		}
		if($errorMsg2 <> "")
		{
			$finalErrorMsg = $errorMsg1.$errorMsg2;	
		}
		return $finalErrorMsg;
	}
	
	
	function validatePeriods()
	{
		$finalErrorMsg = "";
		$currentYearsPeriod = $this->getPeriod($_SESSION['default_year']);
		if (empty($currentYearsPeriod)) 
		{
			$sqlIV = "SELECT * FROM `year` where `YearID` = '".$_SESSION['default_year']."' ";
			$resYearDesc = $this->m_dbConn->select($sqlIV);		 
   			
			$errorMsg1 = '<br /><br /><font style="font-size:14px;font-weight:bold;"> No Billing Periods Found For Current Financial Year ['.$resYearDesc[0]['YearDescription'].'].Kindly Generate Billing  Periods For Current Year.<br /><br />';
			$errorMsg2 = " To Generate Periods<a href='' onClick=\"window.open('bill_period.php','popup','type=fullWindow,fullscreen,scrollbars=yes');\">  Click Here</a></font> <br /><br />";		
			$finalErrorMsg = $errorMsg1.$errorMsg2;	
		
		}
		return $finalErrorMsg;	
	}
	
	function validateServiceTaxLedger()
	{
		$sErrorMsg = "";
		$societyDetails = $this->obj_Utility->GetSocietyInformation($_SESSION['society_id']);
		$ApplyServiceTax = $societyDetails['apply_service_tax'];
		
		if($ApplyServiceTax == 1 && (IGST_SERVICE_TAX == 0 ||  CGST_SERVICE_TAX== 0 || SGST_SERVICE_TAX== 0 || CESS_SERVICE_TAX== 0))
		{
			$sErrorMsg = '<br /><br /><font style="font-size:14px;font-weight:bold;">Please set a default ledger for GST (Service Tax)<br /><br />';	
		}
		else if($ApplyServiceTax == 1 && (IGST_SERVICE_TAX > 0  || CGST_SERVICE_TAX > 0 || SGST_SERVICE_TAX > 0 || CESS_SERVICE_TAX > 0 ))
		{
			$arParentDetails = $this->obj_Utility->getParentOfLedger(IGST_SERVICE_TAX);	
			//$arParentDetails = $this->obj_Utility->getParentOfLedger(CGST_SERVICE_TAX);	
			//$arParentDetails = $this->obj_Utility->getParentOfLedger(SGST_SERVICE_TAX);	
			//$arParentDetails = $this->obj_Utility->getParentOfLedger(CESS_SERVICE_TAX);			
			//print_r($arParentDetails);												
			if(!(empty($arParentDetails)))
			{
				$GroupID = $arParentDetails['group'];

				if(($GroupID == EXPENSE) || ($GroupID == ASSET))	
				{
					$sErrorMsg = '<br /><br /><font style="font-size:14px;font-weight:bold;">GST (Service Tax) Ledger must be of type Income or Liability.<br /><br />';
				}
			}
			
			$arParentDetails = $this->obj_Utility->getParentOfLedger(CGST_SERVICE_TAX);	
			//print_r($arParentDetails);
			if(!(empty($arParentDetails)))
			{
				$GroupID = $arParentDetails['group'];

				if(($GroupID == EXPENSE) || ($GroupID == ASSET))	
				{
					$sErrorMsg = '<br /><br /><font style="font-size:14px;font-weight:bold;">GST (Service Tax) Ledger must be of type Income or Liability.<br /><br />';
				}
			}
			$arParentDetails = $this->obj_Utility->getParentOfLedger(SGST_SERVICE_TAX);	
			//print_r($arParentDetails);
			if(!(empty($arParentDetails)))
			{
				$GroupID = $arParentDetails['group'];

				if(($GroupID == EXPENSE) || ($GroupID == ASSET))	
				{
					$sErrorMsg = '<br /><br /><font style="font-size:14px;font-weight:bold;">GST (Service Tax) Ledger must be of type Income or Liability.<br /><br />';
				}
			}
			$arParentDetails = $this->obj_Utility->getParentOfLedger(CESS_SERVICE_TAX);
			//print_r($arParentDetails);	
			if(!(empty($arParentDetails)))
			{
				$GroupID = $arParentDetails['group'];

				if(($GroupID == EXPENSE) || ($GroupID == ASSET))	
				{
					$sErrorMsg = '<br /><br /><font style="font-size:14px;font-weight:bold;">GST (Service Tax) Ledger must be of type Income or Liability.<br /><br />';
				}
			}
		}

		return $sErrorMsg;
	}
	
	public function getAllUnits()
	{
		$sql="select `unit_id` from `unit` where `society_id` = ".$_SESSION['society_id']." and `status` = 'Y' order by sort_order asc";
		$res=$this->m_dbConn->select($sql);
		$flatten = array();
    	foreach($res as $key)
		{
			$flatten[] = $key['unit_id'];
		}

    	return $flatten;
	}
}

?>