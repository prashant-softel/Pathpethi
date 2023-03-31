<?php
include_once("genbill.class.php");
include_once("dbconst.class.php");

class ContributionLedgerDetailed
{
	public $m_dbConn;
	public $obj_genbill;
	public $obj_FetchData;
	public $checkZero = array();
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->obj_genbill = new genbill($this->m_dbConn);
	}

	public function getCollection($societyID,$unitID)
	{	
		return  $this->obj_genbill->getCollectionOfDataForContributionLedger_optimize($societyID,'',$unitID,0,true, 0);
	}
	
	public function startProcess($societyID,$unitID)
	{
		$sqlBillcycle = "select `bill_cycle`,`society_name` from `society` where `society_id` = '".$societyID."' " ;
		$resBillcycle = $this->m_dbConn->select($sqlBillcycle);
		
		if($resBillcycle <> "")
		{
		 	//$sqlPeriod = "select * from `period` where `Billing_cycle` = '".$resBillcycle[0]['bill_cycle']."' and  `YearID` = '".$_SESSION['default_year']."'" ;
			$sqlPeriod = "select * from `period` where `YearID` = '".$_SESSION['default_year']."'" ;
			$resPeriod = $this->m_dbConn->select($sqlPeriod);
			
			if($resPeriod <> "")
			{
				$details = array();
				$DisplayTotal = array();
						
				for($i = 0;$i < sizeof($resPeriod); $i++)
				{
					$resDetails = $this->obj_genbill->getCollectionOfDataToDisplay($societyID,'',$unitID,$resPeriod[$i]['ID'],true);	
					array_push($details,$resDetails);
					
					foreach($resDetails[0] as $key => $value)
					{
						if($key <> '' && $key <> 'UNIT NO' && $key <> 'MemberName' &&  $key <> 'Fin. Year' &&  $key <> 'BILL FOR' && $key <> 'BILL NO' && $key <> 'BillDate' && $key <> 'Balance')
						{
						   if(!isset($DisplayTotal[0][$key]))
							{
								$DisplayTotal[0][$key] = 0;
							}
							$DisplayTotal[0][$key] += $value;
							
							if(!isset($this->checkZero[$key]))
							{
								$this->checkZero[$key] = false;
							}
							
							if($value == '0.00' && ($this->checkZero[$key] <> true || $this->checkZero[$key]== '')) 
							{
								$this->checkZero[$key] = false;
							}
							else
							{
								$this->checkZero[$key] = true;
							}
						}
						else
						{
							$DisplayTotal[0][$key] = '';
							$this->checkZero[$key] = true;		
						}
					  
					}
					
				}
				
				array_push($details,$DisplayTotal);
				//$details = $this->obj_genbill->unsetZeroKeysFromArray($details , $this->checkZero);
				//print_r($thsi->checkZero);	
				//$this->displayResults($details);	
				return $details;
			}	
			//print_r($details);	
		}
		
		
		
	}
	
	
	public function displayResults($details)
	{
		
		//print_r($details);
		$flag = false;
		$skip = false;
		for($rowStart = 0; $rowStart < sizeof($details); $rowStart++)
		{
			$flag = false;
			//echo '<div class="table-wrapper">';
		echo '<br><table style="text-align:center; width:100%;" class="table table-bordered table-hover table-striped" cellpadding="50">';
				
		for($row = 0; $row < sizeof($details[$rowStart]); $row++)
		{
			foreach($details[$rowStart][$row] as $row2)
			{		
				if(!$flag) 
				{
					echo '<tr style="border:1px solid gray;">';
					echo implode('<td style="border:1px solid gray;">', array_keys($row2)) . "\n";
					$flag = true;
					echo '</tr>';
					echo '<tr style="border:1px solid gray;font-weight:lighter;">';
					echo implode('<td style="border:1px solid gray;font-weight:lighter;">', array_values($row2)) . "\n";
					echo '</tr>';
					
				}
				else
				{
					echo '<tr style="border:1px solid gray;font-weight:lighter;">';
					echo implode('<td style="border:1px solid gray;font-weight:lighter;">', array_values($row2)) . "\n";
					echo '</tr>';
				}
			}
		}
		echo '</table></div>';
	}
	}

	public function displayResults1($details)
	{
		
		$flag = false;
		$skip = false;

		$tempUnitNo = "";

		$flag = false;
		
		for($rowStart = 0; $rowStart < sizeof($details); $rowStart++)
		{
			if($tempUnitNo == '' || $tempUnitNo != $details[$rowStart]['Unit No'])	
			{
				if($tempUnitNo != "")
				{
					echo '<tr>';

					echo '</tr>';
					echo '</table>';
				}
				$tempUnitNo = $details[$rowStart]['Unit No'];

				echo '<br><table style="text-align:center; width:100%;" class="table table-bordered table-hover table-striped" cellpadding="50">';

				$flag = false;
			}
				
			if(!$flag) 
			{
				echo '<tr style="border:1px solid gray;">';
				echo '<td style="border:1px solid gray;">' . implode('<td style="border:1px solid gray;">', array_keys($details[$rowStart])) . "\n";
				$flag = true;
				echo '</tr>';
				echo '<tr style="border:1px solid gray;font-weight:lighter;">';
				echo '<td style="border:1px solid gray;">' . implode('<td style="border:1px solid gray;font-weight:lighter;">', array_values($details[$rowStart])) . "\n";
				echo '</tr>';
				
			}
			else
			{
				echo '<tr style="border:1px solid gray;font-weight:lighter;">';
				echo '<td style="border:1px solid gray;">' . implode('<td style="border:1px solid gray;font-weight:lighter;">', array_values($details[$rowStart])) . "\n";
				echo '</tr>';
			}
		}
		echo '</table></div>';
	}
}
?>