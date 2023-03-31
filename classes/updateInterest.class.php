<?php if(!isset($_SESSION)){session_start(); }
include_once("include/display_table.class.php");
include_once("dbconst.class.php");
include_once("voucher.class.php");
include_once("register.class.php");
include_once("utility.class.php");
include_once("genbill.class.php");


class updateInterest extends dbop
{
	public $actionPage = "../updateInterest.php";
	public $m_dbConn;
	public $m_objUtility;
	public $m_objGenbill;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		$this->m_objUtility = new utility($this->m_dbConn);
		$this->m_objGenbill = new genbill($this->m_dbConn);
	}
	
	public function startProcess()
	{	
		try
		{
			$this->m_dbConn->begin_transaction();
				
			for($i = 0; $i < $_POST['Count']; $i++)
			{		
				$sqlQuery = 'SELECT `UnitID`, `BillSubTotal`, `AdjustmentCredit`,`BillTax`, `IGST`,`CGST`,`SGST`,`CESS`,`BillInterest`, `CurrentBillAmount`, `PrincipalArrears`, `InterestArrears`, `TotalBillPayable`,`PeriodID`,`BillType` FROM `billdetails` WHERE `ID` = '.$_POST['billDetailsID'.$i];

				
				$billDetails = $this->m_dbConn->select($sqlQuery);
				if($_POST['Interest'.$i] <> $billDetails[0]['BillInterest'])
				{	
					//echo "inside";
					$CurrentBillAmount = $billDetails[0]['BillSubTotal'] + $billDetails[0]['AdjustmentCredit'] + $billDetails[0]['BillTax'] + $_POST['Interest'.$i];
					$UnitID = $billDetails[0]['UnitID'];
					$PeriodID = $billDetails[0]['PeriodID'];
					$InterestArrears =  $billDetails[0]['InterestArrears'];
					$PrincipalArrears = $billDetails[0]['PrincipalArrears'];
					$AdjustmentCredit = $billDetails[0]['AdjustmentCredit'];
					$BillType = $_POST["bill_type"];
					//echo "BillType.".$BillType;
					$CurrInterestAmount = $_POST['Interest'.$i];
					//echo "UnitiD:".$UnitID . " PeriodID:". $PeriodID ." BillType:" .$BillType;
					
					$BillRegisterData = $this->m_objGenbill->objFetchData->GetValuesFromBillRegister($UnitID, $PeriodID, $BillType);
					//print_r($BillRegisterData);
					$data = array();
					for($iVal = 0; $iVal < sizeof($BillRegisterData) ; $iVal++) 
					{
						$BillDetails = $BillRegisterData[$iVal]["value"];
						$LedgerID = $BillDetails->sHeader;
						$LedgerAmount = $BillDetails->sHeaderAmount;
						$LedgerVoucherID = $BillDetails->sVoucherID;
						$TaxableFlag = $BillDetails->Taxable;
						if(($LedgerID <> INTEREST_ON_PRINCIPLE_DUE) && ($LedgerID <>  SERVICE_TAX) && ($LedgerID <> IGST_SERVICE_TAX) &&  ($LedgerID <> CGST_SERVICE_TAX) && ($LedgerID <> SGST_SERVICE_TAX) &&  ($LedgerID <> CESS_SERVICE_TAX))
						{
							$HeaderAndAmount = array("Head"=>$LedgerID, "Amt"=> $LedgerAmount, "HeadOldValue"=> $LedgerAmount, "VoucherID"=>$LedgerVoucherID, "Taxable" => $TaxableFlag);

						array_push($data, $HeaderAndAmount);
						}
					}
					if(sizeof($data) > 0)
					{
					$this->m_objGenbill->BillDetailsUpdate($data, $UnitID, $PeriodID, $CurrInterestAmount, $InterestArrears, $PrincipalArrears, $AdjustmentCredit, $BillType);
					echo "<br>interest update complete.";
					}
					else
					{
						echo "<br>nothing to update interest.";
					}
				}

			}
			
			$this->m_dbConn->commit();			
			return "Update"; 
		}
		catch(Exception $exp)
		{
			$this->m_dbConn->rollback();
			echo "Exception:". $exp;
			return $exp;
		}
	}

	function IsGST()
	{		
		return $this->m_objUtility->IsGST();
	}
	
	function getDetails($periodID, $billType = 0)
	{		
		if($periodID == 0)
		{
			$periodQuery = 'SELECT `M_PeriodID` FROM `society` WHERE `society_id` = '.$_SESSION['society_id'];
			$res = $this->m_dbConn->select($periodQuery);
			$periodID = $res[0]['M_PeriodID'];
		}
				
		$sql = "SELECT billdetails.ID, ledger.ledger_name as 'Unit', billdetails.BillSubTotal, billdetails.AdjustmentCredit, billdetails.BillTax, billdetails.BillInterest, billdetails.CurrentBillAmount, billdetails.PrincipalArrears, billdetails.InterestArrears, billdetails.TotalBillPayable, billdetails.Note, billdetails.BillType, billdetails.CGST, billdetails.SGST FROM `billdetails` JOIN `ledger` ON ledger.id = billdetails.UnitID WHERE ledger.society_id = '".$_SESSION['society_id']."' AND billdetails.PeriodID = '".$periodID."' AND billdetails.BillType = '" . $billType . "'";
		//echo $sql;		
		$result = $this->m_dbConn->select($sql);
		
		//if(sizeof($result) > 0)
		//{
			$result[0]['M_Period'] = $periodID;
		//}
		return $result;
	}
	
	function getPeriod($period, $billtype = 0)
	{
		$sqlPeriod = "Select periodtbl.type, yeartbl.YearDescription from period as periodtbl JOIN year as yeartbl ON periodtbl.YearID = yeartbl.YearID where periodtbl.ID = '" . $period . "'";
		
		$billTypeText = 'Maintenance';
		if($billtype == 1)
		{
			$billTypeText = 'Supplementary';
		}

		$sqlResult = $this->m_dbConn->select($sqlPeriod);
		return "<b><font color='#0000FF'>" . $billTypeText . " Bill's For : " . $sqlResult[0]['type'] . " "  . $sqlResult[0]['YearDescription'] . "</font></b><br><br>";
	}
	
	public function combobox($query,$id)
	{
	$str.="<option value=''>Please Select</option>";
	$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($id==$v)
						{
							$sel = 'selected';	
						}
						else
						{
							$sel = '';
						}
						
						$str.="<OPTION VALUE=".$v.' '.$sel.">";
					}
					else
					{
						$str.=$v."</OPTION>";
					}
					$i++;
				}
			}
		}
			return $str;
	}
	
}
?>