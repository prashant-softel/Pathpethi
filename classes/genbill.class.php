<?php

ini_set('display_error', 1);

if(!isset($_SESSION)){ session_start(); }

set_time_limit(0);
ignore_user_abort(1);
ini_set('default_socket_timeout', -1);
ini_set('max_execution_time', -1);
ini_set('memory_limit', -1);
ini_set('mysql.connect_timeout', -1);

include_once("include/display_table.class.php");
include_once("dbconst.class.php");
include_once ("include/fetch_data.php");
include_once("changelog.class.php");
include_once("latestcount.class.php");
include_once("voucher.class.php");
include_once("register.class.php");
include_once("utility.class.php");
include_once("billmaster.class.php");
include_once("bill_period.class.php");

class CUpdatedBillDetails
{	
	public $sBillNo;
	public $sBillDate;
	public $sDueDate;
	public $sPeriodID;
	public $sSocietyID;
	public $sWingID;
	public $sUnitID;
	public $arJournals;
	public $sAdjustmentCredit;	
	public $arrData;
	public $m_dbConn;
	private $m_IsSupplementary;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->sBillNo = "";
		$this->sBillDate = "";
		$this->sDueDate = "";
		$this->sPeriodID = "";
		$this->sSocietyID = "";
		$this->sWingID = "";
		$this->sUnitID = "";
		$this->sAdjustmentCredit = "";		
		$this->arJournals = "";
		$this->arrData = array();
	}
}

class genbill
{
	public $actionPage="../genbill.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	
	public 	$oUpdateBillData;
	private $obj_LatestCount;
	private $errofile_name;
	private $m_bill_file;
	public $objFetchData;
	public $strTrace;
	public $m_objUtility;
    public $delTrace;
	public $m_objLog;
   	private $obj_billmaster;			
	public $obj_voucher;
	public $obj_register;
	public $obj_bill_period;
	public $m_IsSupplementary;
	public $ShowDebugTrace;
	private $m_IsInterestTaxable;
	private $m_IsInterestTaxable_NoThreshold;
	private $m_DontChargeIntToLastBillAmt;	//No int charge to prev bill amount but Interest would be charged to Principal arrears
	private $changeLogDeleteRefArr = array(); // If bills get regenerated then its delete the old bill and add it again but both are separate function, so this arr will hold the ref of old log id for unit which bill deleted

	
    function __construct($dbConn, $dbConnRoot = "")
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->display_pg=new display_table($this->m_dbConn);
		
		$oUpdateBillData = new CUpdatedBillDetails($this->m_dbConn);
		$actionPage="../Maintenance_bill_edit.php";
		
		$this->obj_LatestCount = new latestCount($this->m_dbConn);
		$this->objFetchData = new FetchData($this->m_dbConn);
		$this->m_objUtility =  new utility($this->m_dbConn, $this->m_dbConnRoot);
        $this->m_objLog = new changeLog($this->m_dbConn);
		$this->obj_bill_period = new bill_period($this->m_dbConn);
		//$obj_utility = new utility($this->m_dbConn, $this->m_dbConnRoot);
				
		$this->obj_voucher = new voucher($this->m_dbConn);
		$this->obj_register = new regiser($this->m_dbConn);
		$this->obj_billmaster = new billmaster($this->m_dbConn);
		$this->m_IsSupplementary = 0;
		$this->ShowDebugTrace = 0;

		$this->m_IsInterestTaxable = 0;
		$this->m_IsInterestTaxable_NoThreshold = 0;

		//Code Review: subcategory column should change to ledger name
		$sqlCheck = "select * from ledger where id = " . INTEREST_ON_PRINCIPLE_DUE ;
		$sqlCheck = "select * from ledger where id = '". $_SESSION['default_interest_on_principle']."'";
					
		$resultCheck = $this->m_dbConn->select($sqlCheck);
		if($resultCheck <> "")
		{
			//echo "<script>alert('".$resultCheck[0]['subcategory']."')<//script>";
			$this->m_IsInterestTaxable = $resultCheck[0]['taxable'];
			$this->m_IsInterestTaxable_NoThreshold = $resultCheck[0]['taxable_no_threshold'];
		}
		$this->strTrace .=  '<tr><td>m_IsInterestTaxable : ' . $m_IsInterestTaxable . ' ---- m_IsInterestTaxable_NoThreshold : ' . $m_IsInterestTaxable_NoThreshold . '</td></tr>';
		
		if($this->ShowDebugTrace ==1 )
		{
			//echo "<BR>IsInterestTaxable". $this->m_IsInterestTaxable;
			//echo "<BR>IsInterestTaxable_NoThreshold". $this->m_IsInterestTaxable_NoThreshold;
		}
		$this->m_DontChargeIntToLastBillAmt = 0;
	}
	
	//Edit bill (Called from maintenancebill_edit_process.php)
	public function startProcess()
	{
		$errorExists=0;
		$actionPage="../Maintenance_bill_edit.php";

		if(!isset($_REQUEST['bill_method']))
		{
			$_REQUEST['bill_method'] = 0;
		}

		if(!isset($_REQUEST['no_int_on_prev_bill']))
		{
			$_REQUEST['no_int_on_prev_bill'] = 0;
		}
		$this->m_DontChargeIntToLastBillAmt = $_REQUEST['no_int_on_prev_bill'];
		if($this->ShowDebugTrace == 1)
		{
			echo "<BR>m_DontChargeIntToLastBillAmt " . $this->m_DontChargeIntToLastBillAmt;
		}
		
		$this->m_IsSupplementary = $_REQUEST['bill_method'];
		if($this->ShowDebugTrace == 1)
		{
			echo "<BR>Supp " . $this->m_IsSupplementary . "<BR>";
		}
		
		if($_REQUEST['insert']=='Insert' && $errorExists==0)
		{
			if($_POST['society_id']<>"" && $_POST['wing_id']<>"" && $_POST['unit_no']<>"")
			{
				$sql = "select count(*)as cnt from unit where society_id='".$_SESSION['society_id']."' and wing_id='".$_POST['wing_id']."' and unit_no='".addslashes(trim($_POST['unit_no']))."' and status='Y'";
				$res = $this->m_dbConn->select($sql);
				
				if($res[0]['cnt']==0)
				{
					foreach(explode(',',$_POST['unit_no']) as $k)
					{
						if($k<>"")
						{
							$sql0 = "select count(*)as cnt from unit where society_id='".$_SESSION['society_id']."' and wing_id='".$_POST['wing_id']."' and unit_no='".addslashes(trim($k))."'";
							$res0 = $this->m_dbConn->select($sql0);
							if($res0[0]['cnt']==0)
							{
							$rand_no = rand('00000000','99999999');
							$sql00 = "select count(*)as cnt from unit where rand_no='".$rand_no."' and status='Y'";
							$res00 = $this->m_dbConn->select($sql00);
							if($res00[0]['cnt']==1)
							{
								$rand_no = rand('00000000','99999999');
							}
							
							$sql1 = "insert into unit(`society_id`,`wing_id`,`unit_no`,`rand_no`)values
									('".$_SESSION['society_id']."','".$_POST['wing_id']."','".addslashes(trim($k))."','".$rand_no."')";
							$res1 = $this->m_dbConn->insert($sql1);
							}
						}
					}
					
					return "Insert";
				}
				else
				{
					return "Already exist";
				}
			}
			else
			{
				return "Some * field is missing";
			}
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			if($_POST['society_id']<>"" && $_POST['wing_id']<>"" && $_POST['unit_no']<>"")
			{
				$up_query="update unit set `society_id`='".$_POST['society_id']."',`wing_id`='".$_POST['wing_id']."',`unit_no`='".$_POST['unit_no']."' where unit_id='".$_POST['id']."'";
				$data=$this->m_dbConn->update($up_query);
				return "Update";
			}
			else
			{
				return "Some * field is missing";
			}
		}
		else if($_REQUEST['mode']=='Generate' && $errorExists==0)
		{
			$_SESSION['default_year'] = $_REQUEST['year_id'];
			$_SESSION['default_period'] = $_REQUEST['period_id'];
			$this->SetDefaultYearPeriod($_REQUEST['year_id'], $_REQUEST['period_id']);
			return $this->generateBill();
		}
		else if($_REQUEST['mode']== 'View' && $errorExists==0)
		{
			$_SESSION['default_year'] = $_REQUEST['year_id'];
			$_SESSION['default_period'] = $_REQUEST['period_id'];
			$this->SetDefaultYearPeriod($_REQUEST['year_id'], $_REQUEST['period_id']);
			
			return "";
		}
		else if($_REQUEST['Bill']== 'Update' && $errorExists==0)
		{
			return $this->UpdateBill();
		}
		else if($_REQUEST['mode']== 'Export To Excel' && $errorExists==0)
		{			
			return "Export";			
		}
		else
		{
			echo "<script>alert('error');</script>";
			return $errString;
		}
	}
	public function IsSupplementaryBill()
	{
		return $this->m_IsSupplementary; 
	}
	
	public function SetDefaultYearPeriod($year, $period)
	{
		$sqlUpdate = "UPDATE `appdefault` SET `APP_DEFAULT_YEAR`='" . $year . "',`APP_DEFAULT_PERIOD`='" . $period . "' WHERE `APP_DEFAULT_SOCIETY` = '" . $_SESSION['society_id'] . "'";
		$sqlResult = $this->m_dbConn->update($sqlUpdate);
	}
	
	
	public function comboboxunit($query,$id)
	{
		//*** Combo Box for unit present in voucher drop down list 
		
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
	
	
	
	public function combobox($query,$id, $defaultText = 'Please Select', $defaultValue = '')
	{
		$str = '';
		
		if($defaultText != '')
		{
			$str .= "<option value='" . $defaultValue . "' selected = 'selected'>" . $defaultText . "</option>";
		}
		
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
	
	
	public function FetchUnitName($IsOutsider,$id="")
	{
		$str = '';
		$defaultText = 'Please Select';
		$defaultValue = '';
		if($IsOutsider == 0)
		{
			$query = "select u.unit_id, CONCAT(CONCAT(u.unit_no,'-'), mm.owner_name) AS 'unit_no' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.status = 'Y' and u.society_id = '" . $_SESSION['society_id'] . "' and mm.ownership_status=1 ORDER BY u.sort_order ";
		}
		else if($IsOutsider == 1)
		{
			echo $query = " SELECT l.id, l.ledger_name FROM ledger as l JOIN account_category as ac ON l.categoryid = ac.category_id where `categoryid` = '".$_SESSION['default_Sundry_debetor']."' AND group_id = '".ASSET."'";
			
		//	WHERE `categoryid` NOT IN '".DUE_FROM_MEMBERS."'
		}
				
		if($defaultText != '')
		{
			$str .= "<option value='" . $defaultValue . "' selected = 'selected'>" . $defaultText . "</option>";
		}
		
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					echo $k;
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
	
	public function comboboxForLedgerReport($query,$id, $defaultText = 'Please Select', $defaultValue = '')
	{
		if($defaultText != '')
		{
			?>
        <ul>
			<li>&nbsp;<input type="checkbox"      id = '0' class="checkBox chekAll" checked/>&nbsp;<?php echo $defaultText ; ?></li>
			<?php 
		}
		
		$data = $this->m_dbConn->select($query);
		
		for($i = 0; $i < sizeof($data); $i++)
		{?>
        	<li>&nbsp;<input type="checkbox"  id="<?php echo $data[$i]['unit_id'];?>" class="checkBox" onChange="uncheckDefaultCheckBox(this.id);"/>&nbsp; <?php echo $data[$i]['name'];?></li>
        <?php		
		}
		?>
		</ul>
        <?php 
	}
	
	public function comboboxForLedgerReport2($query,$id, $defaultText = 'Please Select', $defaultValue = '')
	{
		if($defaultText != '')
		{
			?>
        <ul>
			<li>&nbsp;<input type="checkbox"      id = '0' class="checkBox chekAll" checked/>&nbsp;<?php echo $defaultText ; ?></li>
			<?php 
		}
		
		$data = $this->m_dbConn->select($query);
		
		for($i = 0; $i < sizeof($data); $i++)
		{?>
        	<li>&nbsp;<input type="checkbox"  id="<?php echo $data[$i]['unit_id'];?>" name="checkbox<?php echo $i;?>" value="<?php echo $data[$i]['unit_id'];?>" class="checkBox" onChange="uncheckDefaultCheckBox(this.id);"/>&nbsp; <?php echo $data[$i]['name'];?></li>
        <?php		
		}
		?>
		</ul>
        <?php 
	}
	public function generateBill()
	{
		$info = '';
		
		try
		{
			$this->m_dbConn->begin_transaction();
			{
				if($_REQUEST['period_id'] <> '' && $_REQUEST['period_id'] > 0 && $_REQUEST['bill_date'] <> '' && $_REQUEST['due_date'] <> '' && $_REQUEST['due_date_to_display'] <> '')
				{
					$sqlGen = '';
					$sqlGen_BillCalc = '';
					
					if($_REQUEST['society_id'] == '0')
					{
						//generate for all units in all societies
//						$sqlGen = "select master.UnitID, master.AccountHeadID, master.AccountHeadAmount,master.BillType from unitbillmaster as master where master.BillType='".$this->IsSupplementaryBill() ."'";
						$sqlGen_BillCalc = "select distinct(master.UnitID), unitinfo.society_id as SocietyID, unitinfo.taxable_no_threshold, master.BillType from unitbillmaster as master JOIN unit as unitinfo ON master.UnitID = unitinfo.unit_id and  master.BillType='".$this->IsSupplementaryBill(); "' ORDER BY unitinfo.sort_order ASC";
					}
					else if($_REQUEST['wing_id'] == '0' && $_REQUEST['unit_id'] == '0')
					{
						//generate for all units in single society
//						$sqlGen = "select master.UnitID, master.AccountHeadID, master.AccountHeadAmount,master.BillType from unitbillmaster as master JOIN unit as unitinfo on master.UnitID = unitinfo.unit_id where unitinfo.society_id = '" . $_REQUEST['society_id'] .  "' where master.BillType='".$this->IsSupplementaryBill()."'";
						$sqlGen_BillCalc = "select distinct(master.UnitID), unitinfo.society_id as SocietyID, unitinfo.taxable_no_threshold,master.BillType from unitbillmaster as master JOIN unit as unitinfo on master.UnitID = unitinfo.unit_id where unitinfo.society_id = '" . $_REQUEST['society_id'] .  "' and  master.BillType='".$this->IsSupplementaryBill()."' ORDER BY unitinfo.sort_order ASC";
					}
					else if($_REQUEST['unit_id'] == '0')
					{
						//generate for all units in single wing
//						$sqlGen = "select master.UnitID, master.AccountHeadID, master.AccountHeadAmount,master.BillType from unitbillmaster as master JOIN unit as unitinfo on master.UnitID = unitinfo.unit_id where unitinfo.society_id = '" . $_REQUEST['society_id'] .  "' and unitinfo.wing_id = '" . $_REQUEST['wing_id'] . "' and master.BillType=".$this->IsSupplementaryBill();
						$sqlGen_BillCalc = "select distinct(master.UnitID), unitinfo.society_id as SocietyID, unitinfo.taxable_no_threshold, master.BillType from unitbillmaster as master JOIN unit as unitinfo on master.UnitID = unitinfo.unit_id where unitinfo.society_id = '" . $_REQUEST['society_id'] .  "' and unitinfo.wing_id = '" . $_REQUEST['wing_id'] . "' and master.BillType='".$this->IsSupplementaryBill() ."' ORDER BY unitinfo.sort_order ASC";
					}
					else
					{
						//generate for single unit
						$sqlGen = "select master.UnitID, master.AccountHeadID, master.AccountHeadAmount from unitbillmaster as master where master.UnitID = '" . $_REQUEST['unit_id'] . "' and  master.BillType='".$this->IsSupplementaryBill() ."'";
						$sqlGen_BillCalc = "select distinct(master.UnitID), unitinfo.society_id as SocietyID, unitinfo.taxable_no_threshold from unitbillmaster as master JOIN unit as unitinfo ON master.UnitID = unitinfo.unit_id where master.UnitID = '" . $_REQUEST['unit_id'] . "' and master.BillType='".$this->IsSupplementaryBill() ."' ORDER BY unitinfo.sort_order ASC";
					}
				
					//echo '<br/>SqlGen : ' . $sqlGen_BillCalc;
					
					//$result = $this->m_dbConn->select($sqlGen);
					
					$result = $this->m_dbConn->select($sqlGen_BillCalc);
					
					$UnitIDCol = array();
					$aryGenBillInsertID = array();
					
					if($result <> "")
					{
						$this->obj_LatestCount->updateLatestBillNo($_SESSION['society_id'], $_REQUEST['bill_no']);
						$this->changeLogDeleteRefArr = array(); // set to empty				
						foreach($result as $k => $v)
						{
							//Delete Bill Details if already generated
							$this->DeleteBillDetails($result[$k]['UnitID'], $_REQUEST['period_id'],false,false,$this->IsSupplementaryBill());
						}
						
						$changeLog = new changeLog($this->m_dbConn);
						// $desc = $this->IsSupplementaryBill();
						if($this->IsSupplementaryBill())
						{
							$desc = 'Generated Supplementary Bill for Unit <' . $result[$k]['UnitID'] . '> Period <' . $_REQUEST['period_id'] . '>';
						}
						else
						{
							$desc = 'Generated '.$this->IsSupplementaryBill().' Bill for Unit <' . $result[$k]['UnitID'] . '> Period <' . $_REQUEST['period_id'] . '>';
						}
						if($_REQUEST['unit_id'] == '0')
						{
							if($this->IsSupplementaryBill())
							{
							$desc = 'Generated Supplementary Bill for Unit <All> Period <' . $_REQUEST['period_id'] . '>';
							}
							else
							{
								$desc = 'Generated '.$this->IsSupplementaryBill().' Bill for Unit <All> Period <' . $_REQUEST['period_id'] . '>';
							}
						}
						
						//$iLatestChangeID = $changeLog->setLog($desc, $_SESSION['login_id'], 'billregister', '--');
						
						$FontSize =$_POST['font_size'];
						
						//echo "Font Size:".$FontSize;
						
						$Notes = ($_REQUEST['bill_notes']);	
						$sBillDate = getDBFormatDate($this->m_dbConn->escapeString($_REQUEST['bill_date']));
						//echo '<br/>Bill date after getDBFormatDate <' . $sBillDate . ">"; 
						$this->strTrace .= '<tr><td>Bill date after getDBFormatDate <' . $sBillDate . "></td></tr>"; 

//						$sqlSelect = "SELECT * from `billregister` where `SocietyID` = '" . $this->m_dbConn->escapeString($result[$k]['SocietyID']). "' and `PeriodID` = '" . $this->m_dbConn->escapeString( $_REQUEST['period_id']). "' and `BillDate` = '" . $sBillDate . "' and `DueDate` = '" . getDBFormatDate($this->m_dbConn->escapeString($_REQUEST['due_date'])) . "' and `BillType` = '".$this->IsSupplementaryBill()."' and `font_size` = '".$FontSize."'";
//							echo "<BR>$sqlSelect<BR>";
					
					$sqlSelect = "SELECT * from `billregister` where `SocietyID` = '" . $this->m_dbConn->escapeString($result[$k]['SocietyID']). "' and `PeriodID` = '" . $this->m_dbConn->escapeString( $_REQUEST['period_id']). "' and `BillDate` = '" . $sBillDate . "' and `DueDate` = '" . getDBFormatDate($this->m_dbConn->escapeString($_REQUEST['due_date'])) . "' and `DueDateToDisplay` = '" . getDBFormatDate($this->m_dbConn->escapeString($_REQUEST['due_date_to_display'])) . "' and  `Notes` = '" . $this->m_dbConn->escapeString($Notes) . "' and `BillType` = '".$this->IsSupplementaryBill()."' and `font_size` = '".$FontSize."'";
					
					$ExistingBillRegister = $this->m_dbConn->select($sqlSelect);
					if($this->ShowDebugTrace == 1)
					{
						//echo "<BR>$sqlSelect<BR>";
					}
					
					if($ExistingBillRegister <> '')
					{
						
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>Bill Register exists : ". $ExistingBillRegister[0][ID] . "<BR> ";
						}
						$BillRegisterID = $ExistingBillRegister[0][ID];
						//print_r($ExistingBillRegister);
					}
						
					if($BillRegisterID <= 0)
					{
						$sqlInsert = "INSERT INTO `billregister`(`SocietyID`, `PeriodID`, `CreatedBy`, `BillDate`, `DueDate`, `DueDateToDisplay`, `LatestChangeID`, `Notes`,`BillType`,`font_size`) VALUES ('" . $this->m_dbConn->escapeString($result[$k]['SocietyID']). "', '" . $this->m_dbConn->escapeString( $_REQUEST['period_id']). "', '" . $this->m_dbConn->escapeString($_SESSION['login_id']). "', '" . $sBillDate . "', '" . getDBFormatDate($this->m_dbConn->escapeString($_REQUEST['due_date'])) . "', '" . getDBFormatDate($this->m_dbConn->escapeString($_REQUEST['due_date_to_display'])) . "', '" . $this->m_dbConn->escapeString($iLatestChangeID). "', '" . $this->m_dbConn->escapeString($Notes). "','".$this->IsSupplementaryBill()."','".$FontSize."')";
							
						$BillRegisterID = $this->m_dbConn->insert($sqlInsert);
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>Created new BillRegisterID:". $BillRegisterID . "<BR>";
						}
						//echo "<BR><BR>In if part " . $sqlInsert ;
					}

						//echo '<br/>BillRegisterID <' . $BillRegisterID . ">"; 
						$this->strTrace .= '<tr><td>BillRegisterID <' . $BillRegisterID . "></td></tr>"; 																												
						//die;
							
						//$aryGenBillInsertID[$result[$k]['UnitID']] = $resultInsert;
							
						$PeriodID = $_REQUEST['period_id'];
						$PrevPeriodID = $this->getPreviousPeriodData($PeriodID);
						
						$this->strTrace .= '<tr><td>Billtype :  ' . $this->IsSupplementaryBill() . ' PrevPeriodID  <' . $PrevPeriodID  . '></td></tr>'; 																												
						//var_dump($PrevPeriodID);
						if($PrevPeriodID < 1)
						{
							$desc = 'No prev period for period <' . $PeriodID . "> : assuming this is first period.";
							//return;
						}
						else
						{
							$desc = 'Prev period id <'.$PrevPeriodID.'> for period <' . $PeriodID . "> : assuming this is first period.";
						}
		
						$PrevPeriodBeginingDate = 0;
						$PrevPeriodEndingDate = 0;
					    //echo "check";
						/*if(!$this->IsSupplementaryBill())
						{*/
							//echo 'Getting data for PrevPeriodID : ' . $PrevPeriodID;
							//$sqlPrevQuery = "Select Type, YearID, PrevPeriodID, Status, BeginingDate, EndingDate from period where ID=" . $PeriodID;
							$sqlPymtQuery = "Select Type, YearID, PrevPeriodID, Status, BeginingDate, EndingDate from period where ID=" . $PrevPeriodID;
					//		echo '<br/>sqlPymtQuery : ' . $sqlPymtQuery;
							$Prevresult = $this->m_dbConn->select($sqlPymtQuery);
							//$PrevPeriodID = -1;
							if(!is_null($Prevresult))
							{
								$Type = $Prevresult[0]['Type'];
								$YearID = $Prevresult[0]['YearID'];
								//ToDo : Comment this out Bug
								//$PrevPeriodID = $Prevresult[0]['PrevPeriodID'];
								$PrevPeriodBeginingDate = $Prevresult[0]['BeginingDate'];
								$PrevPeriodEndingDate = $Prevresult[0]['EndingDate'];
											
								//echo '<br/>PrevPeriodID : ' . $PrevPeriodID;
								//echo '<br/>Prev Period : ' . $Type;
								//echo '<br/>Prev YearID : ' . $YearID;
								//echo '<br/>PrevPeriodBeginingDate : ' . $PrevPeriodBeginingDate;
								//echo '<br/>PrevPeriodEndingDate : ' . $PrevPeriodEndingDate;
								
							}
						/*}*/
		
		//-----		
		
				//echo "Get interest calc parameters of Society";
				$SocietyID = $_SESSION['society_id'];
/*				$sqlSocietyQuery = "Select int_rate, int_method, int_tri_amt, rebate_method, rebate, bill_cycle from society where society_id =" . $SocietyID;
		//		echo '<br/>sqlSocietyQuery : ' . $sqlSocietyQuery;
				$SQLResult = $this->m_dbConn->select($sqlSocietyQuery);
*/
				$societyInfo = $this->m_objUtility->GetSocietyInformation($SocietyID);
						
				//echo $sqlGen_BillCalc;
				//echo "<BR>sqlgen_BillCalc<BR>" . $sqlGen_BillCalc . "<BR>";		
				$result_BillCalc = $this->m_dbConn->select($sqlGen_BillCalc);
				//var_dump($result_BillCalc );
				if($result_BillCalc <> "")
				{
					$this->objFetchData->GetSocietyDetails($SocietyID);
					$SocietyDetails = $this->objFetchData->objSocietyDetails;
					
					//var_dump($this->objFetchData->objSocietyDetails->sSocietyCode);
					$bill_dir = '../m_bills_log/' . $this->objFetchData->objSocietyDetails->sSocietyCode;
					//echo $bill_dir;
					if (!file_exists($bill_dir)) 
					{
						mkdir($bill_dir, 0777, true);
					}

					//echo "<BR>Societyid " . $SocietyID;
					foreach($result_BillCalc as $k => $v)
					{
						if($result_BillCalc[$k]['UnitID'] <> 0)
						{
							$UnitID = $result_BillCalc[$k]['UnitID'];
							$Unit_taxable_no_threshold = 0;
							/*if($SocietyID == 254) //For Shree swami
							{
								if($PeriodID <= 3)
								{
									$Unit_taxable_no_threshold = $result_BillCalc[$k]['taxable_no_threshold'];
								}
								else
								{
									//if(($UnitID == 67 || $UnitID == 131 ||$UnitID == 132 ||$UnitID == 215 ||$UnitID == 216 ||$UnitID == 224 ||$UnitID == 223 ||$UnitID == 67 || $UnitID == 204 ||$UnitID == 268 ||$UnitID == 269))
									{
										$Unit_taxable_no_threshold = $result_BillCalc[$k]['taxable_no_threshold'];
									}
								}
								if($this->ShowDebugTrace == 1)
								{
									echo "<BR>UnitId : " . $UnitID . "  Unit_taxable_no_threshold : " . $Unit_taxable_no_threshold ;
								}
								$this->strTrace .=  '<tr><td>Unit_taxable_no_threshold  : ' . $Unit_taxable_no_threshold . '</td></tr>';
							}*/
							$Unit_taxable_no_threshold = $result_BillCalc[$k]['taxable_no_threshold'];
							if($this->ShowDebugTrace == 1)
							{
								echo "<BR>UnitId : " . $UnitID . "  Unit_taxable_no_threshold : " . $Unit_taxable_no_threshold ;
							}
							$this->strTrace .=  '<tr><td>Unit_taxable_no_threshold  : ' . $Unit_taxable_no_threshold . '</td></tr>';
							$iBillCounter = $this->obj_LatestCount->getLatestBillNo($_SESSION['society_id']);
							$BillFor = $this->objFetchData->GetBillFor($PeriodID);
							//var_dump($BillFor);
							$this->objFetchData->GetMemberDetails($UnitID);
							$CurUnitID = $this->objFetchData->objMemeberDetails->sUnitNumber;
							
							if(strpos($CurUnitID, '/') == true)
							{
								$CurUnitID = str_replace('/','-',$CurUnitID);
							}
							
							$this->errofile_name = $bill_dir.'/M_Bill_'.$_SESSION['society_id'].'_'. $CurUnitID .'_'.$BillFor.'_'.$iBillCounter .'.html';
							$this->errofile_name = str_replace(' ', '-',$this->errofile_name);

							$this->m_bill_file = fopen($this->errofile_name,"a");
							$this->strTrace = "<html><head><title>m-Bill log</title></head><body><table>";
							$this->strTrace .= "<tr><td>-----------------------------------------------------------------------</td></tr>";
							if($this->IsSupplementaryBill())
							{ 
								$this->strTrace .= "<tr><td>New Supplementary Bill Generated for SocietyID <".$_SESSION['society_id']."> : <b>".$this->objFetchData->objSocietyDetails->sSocietyName." </b></td></tr>";
							}
							else
							{
								$this->strTrace .= "<tr><td>New Bill Generated for SocietyID <".$_SESSION['society_id']."> : <b>".$this->objFetchData->objSocietyDetails->sSocietyName." </b></td></tr>";
							}
							$this->strTrace .= "<tr><td>UnitID <".$UnitID."> : <b>".$CurUnitID."</b></td></tr>";
							$this->strTrace .= "<tr><td>PeriodID <".$PeriodID.">  : <b>".$BillFor."</b></td></tr>";
							$this->strTrace .= "<tr><td>Bill Number <".$iBillCounter."></b></td></tr>";
							//echo "generating bill per unit";
//$result1 = $this->GetGSTNoThresholdFlag_perMember($societyInfo, $PeriodID, $PrevPeriodID, $PrevPeriodBeginingDate, $PrevPeriodEndingDate,							1);
							
							$BillDetailID = $this->generateBill_PerUnit($UnitID, $PeriodID, $PrevPeriodID, $PrevPeriodBeginingDate, $PrevPeriodEndingDate, $BillRegisterID, $iBillCounter, $sBillDate, $Unit_taxable_no_threshold, $societyInfo);

							$this->strTrace .= "<tr><td>-----------------------------------------------------------------------</td></tr>";

							$this->strTrace .= "</table></body></html>";
							//echo "file name " . $this->m_bill_file;
							fwrite($this->m_bill_file,$this->strTrace);
							fclose($this->m_bill_file);
							
						}
					}
				}
				//$info = 'Bills generated successully.';
				//echo $info;
				if($_POST["unit_id"] == 0)
				{
					$updateQuery = "UPDATE `society` SET `M_PeriodID` =" .$PeriodID. " WHERE society_id = ".$_SESSION['society_id'] ;
					//echo "<br>UPDATE ".$updateQuery;
					$this->m_dbConn->update($updateQuery);
				}
						//Insert or Uodate Reminder date in `remindersms` table						
						/*$countQuery = "SELECT count(ID) AS `cnt` FROM `remindersms` WHERE `society_id` = '" . $this->m_dbConn->escapeString($result[$k]['SocietyID']). "' AND `PeriodId` = '" . $this->m_dbConn->escapeString( $_REQUEST['period_id']). "'";
						
						$count = $this->m_dbConnRoot->select($countQuery);
						
						$reminderDate = $this->GetDateByOffset($_REQUEST['due_date'], -5);
						if($count[0]['cnt'] == 0)
						{																					
							$sqlReminderQuery = "INSERT INTO `remindersms`(`society_id`, `PeriodID`, `ReminderType`, `EventDate`, `EventReminderDate`, `LoginID`) VALUES ('" . $this->m_dbConn->escapeString($result[$k]['SocietyID']). "',
										 '" . $this->m_dbConn->escapeString( $_REQUEST['period_id']). "','".SENDBILLREMINDER."','" . getDBFormatDate($this->m_dbConn->escapeString($_REQUEST['due_date'])) . "','" . getDBFormatDate($this->m_dbConn->escapeString($reminderDate)) . "',
										 '".$_SESSION['login_id']."')";												
							$this->m_dbConnRoot->insert($sqlReminderQuery);
						}
						else
						{
							$updateTable = "UPDATE `remindersms` SET `EventDate`='" . getDBFormatDate($this->m_dbConn->escapeString($_REQUEST['due_date'])) . "',
								`EventReminderDate`='" . getDBFormatDate($this->m_dbConn->escapeString($reminderDate)) . "', `LoginID` = '".$_SESSION['login_id']."', `ReminderType` = '".SENDBILLREMINDER."'  WHERE `society_id`='" . $this->m_dbConn->escapeString($result[$k]['SocietyID']). "' AND
								`PeriodID`='" . $this->m_dbConn->escapeString( $_REQUEST['period_id']). "'";								
							$this->m_dbConnRoot->update($updateTable);
						}*/
						
						//$sqlReminderQuery = "INSERT INTO `remindersms`(`society_id`, `PeriodID`, `ReminderType`, `EventDate`, `EventReminderDate`, `LoginID`) VALUES ('" . $this->m_dbConn->escapeString($result[$k]['SocietyID']). "', '" . $this->m_dbConn->escapeString( $_REQUEST['period_id']). "','".SENDBILLREMINDER."','" . getDBFormatDate($this->m_dbConn->escapeString($_REQUEST['due_date'])) . "','" . getDBFormatDate($this->m_dbConn->escapeString($reminderDate)) . "', '".$_SESSION['login_id']."')";
						//$this->m_dbConnRoot->insert($sqlReminderQuery);
					}
					else
					{
						$info = "No Units found in Bill Master to Generate Bills for selected criteria.";
					}
				}
				else
				{
					$info = 'Please enter valid data for all the fields.';
				}
				//if($this->ShowDebugTrace == 1)
				{
					//echo "doing commit";
				}
				$this->m_dbConn->commit();
				return $info;
			}
		}
		catch(Exception $exp)
		{
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>Exception occured. Doing rollback.";
				print_r($exp);
			}
			$this->m_dbConn->rollback();
			return $exp;
		}
	}

	public function ReGenerateBill($UnitID, $PeriodID, $IsSupplementaryBill)
	{
		$bIsManualDelete = false;
		$bIsDeleteAll = false;
		
		$societyInfo = $this->m_objUtility->GetSocietyInformation($_SESSION['society_id']);
		$this->m_IsSupplementary = $IsSupplementaryBill;
		$sqlCheck = "Select b.ID,b.BillRegisterID,b.BillNumber,br.BillDate,u.taxable_no_threshold,p.PrevPeriodID from billdetails as b JOIN billregister as br ON b.BillRegisterID = br.ID JOIN unit as u ON u.unit_id = b.UnitID JOIN period as p ON p.ID = b.PeriodID where b.UnitID ='" . $UnitID . "' and b.PeriodID ='" . $PeriodID . "'  and b.BillType='".$IsSupplementaryBill."'";
		$sqlResult = $this->m_dbConn->select($sqlCheck);
		
		$PrevPeriodquery = "SELECT BeginingDate, EndingDate FROM period WHERE ID = '".$sqlResult[0]['PrevPeriodID']."'";
		$PrevPeriodDetails = $this->m_dbConn->select($PrevPeriodquery); 
		
		$iBillCounter = $sqlResult[0]['BillNumber'];
		$PrevPeriodID = $sqlResult[0]['PrevPeriodID'];
		$sBillDate = $sqlResult[0]['BillDate'];
		$Unit_taxable_no_threshold = $sqlResult[0]['taxable_no_threshold'];
		$PrevPeriodBeginingDate = $PrevPeriodDetails[0]['BeginingDate'];
		$PrevPeriodEndingDate = $PrevPeriodDetails[0]['EndingDate'];
		//$BillRegisterID = $sqlResult[0]['BillRegisterID'];
		
		if($sqlResult <> '')
		{
			$RefNo = $sqlResult[0]['ID'];
			$BillRegisterID = $sqlResult[0]['BillRegisterID'];

			$sqlCount = "Select Count(*) as cnt from billregister where ID = '" . $BillRegisterID . "'";
			$resCount = $this->m_dbConn->select($sqlCount);
		}
		 $this->changeLogDeleteRefArr = array();
		 $this->DeleteBillDetails($UnitID, $PeriodID,$bIsManualDelete,$bIsDeleteAll,$IsSupplementaryBill);
	
		//echo "<br>UnitID ".$UnitID." <br>PeriodID : ".$PeriodID." <br> PrevPeriodID : ".$PrevPeriodID." <br>PrevPeriodBeginingDate : ".$PrevPeriodBeginingDate." <br>PrevPeriodEndingDate : ".$PrevPeriodEndingDate. " <br>
		//		".$PrevPeriodEndingDate." <br> BillRegisterID : ".$BillRegisterID . "<br> iBillCounter: ".$iBillCounter." sBillDate ".$sBillDate." <br> Unit_taxable_no_threshold : ".$Unit_taxable_no_threshold." <br> ";
		
		//Error Log File Creation

		$this->objFetchData->GetSocietyDetails($_SESSION['society_id']);
		
		$SocietyDetails = $this->objFetchData->objSocietyDetails;
		
		$bill_dir = '../m_bills_log/' . $this->objFetchData->objSocietyDetails->sSocietyCode;
		
		if (!file_exists($bill_dir))  // create folder if not present
		{
			mkdir($bill_dir, 0777, true);
		}
		
		$BillFor = $this->objFetchData->GetBillFor($PeriodID);
		
		$this->objFetchData->GetMemberDetails($UnitID);
		
		$CurUnitID = $this->objFetchData->objMemeberDetails->sUnitNumber;
		
		if(strpos($CurUnitID, '/') == true)
		{
			$CurUnitID = str_replace('/','-',$CurUnitID);
		}
		
		$this->errofile_name = $bill_dir.'/M_Bill_'.$_SESSION['society_id'].'_'. $CurUnitID .'_'.$BillFor.'_'.$iBillCounter .'.html';
		$this->errofile_name = str_replace(' ', '-',$this->errofile_name);

		$this->m_bill_file = fopen($this->errofile_name,"a");
		$this->strTrace = "<html><head><title>m-Bill log</title></head><body><table>";
		$this->strTrace .= "<tr><td>-----------------------------------------------------------------------</td></tr>";
		if($this->IsSupplementaryBill())
		{ 
			$this->strTrace .= "<tr><td>Supplementary Bill regenerated at ".date('d-m-Y H:i:s')." - (UCT+05:30) by  ".$_SESSION['name']." (".$_SESSION['login_id'].") for SocietyID <".$_SESSION['society_id']."> : <b>".$this->objFetchData->objSocietyDetails->sSocietyName." </b></td></tr>";
		}
		else
		{
			$this->strTrace .= "<tr><td>Bill Regenerated at ".date('d-m-Y H:i:s')." - (UCT+05:30) by  ".$_SESSION['name']." (".$_SESSION['login_id'].") for SocietyID <".$_SESSION['society_id']."> : <b>".$this->objFetchData->objSocietyDetails->sSocietyName." </b></td></tr>";
		}
		
		$this->strTrace .= "<tr><td>UnitID <".$UnitID."> : <b>".$CurUnitID."</b></td></tr>";
		$this->strTrace .= "<tr><td>PeriodID <".$PeriodID.">  : <b>".$BillFor."</b></td></tr>";
		$this->strTrace .= "<tr><td>Bill Number <".$iBillCounter."></b></td></tr>";
		
		
		$BillDetailID = $this->generateBill_PerUnit($UnitID, $PeriodID, $PrevPeriodID, $PrevPeriodBeginingDate, $PrevPeriodEndingDate, $BillRegisterID, $iBillCounter, $sBillDate, $Unit_taxable_no_threshold, $societyInfo);

		$this->strTrace .= "<tr><td>-----------------------------------------------------------------------</td></tr>";

		$this->strTrace .= "</table></body></html>";
		//echo "file name " . $this->m_bill_file;
		fwrite($this->m_bill_file,$this->strTrace);
		fclose($this->m_bill_file);

		return $BillDetailID;
	}


	//If Bill has been already generated for a Unit/Period, delete it first and then regenerate.
	public function DeleteBillDetails($UnitID, $PeriodID,$bIsManualDelete = false,$bIsDeleteAll = true, $IsSupplementaryBill = false)
	{
//		$this->ShowDebugTrace = 1;
		//Check if data exist in billdetails
		
		$sqlCheck = "Select bill.ID,bill.BillRegisterID, bill.BillNumber, `BillSubTotal`, `BillInterest`, `CurrentBillAmount`, `PrincipalArrears`, `InterestArrears`, `TotalBillPayable`, PaymentReceived, PaidPrincipal, PaidInterest  from billdetails as bill  where bill.UnitID ='" . $UnitID . "' and bill.PeriodID ='" . $PeriodID . "'  and bill.BillType='".$IsSupplementaryBill."'";
		$sqlResult = $this->m_dbConn->select($sqlCheck);
		if($sqlResult <> '')
		{
			$RefNo = $sqlResult[0]['ID'];
			$BillRegisterID = $sqlResult[0]['BillRegisterID'];
			$BillNumber = $sqlResult[0]['BillNumber'];
			$BillSubTotal = $sqlResult[0]['BillSubTotal'];
			$BillInterest = $sqlResult[0]['BillInterest'];
			$CurrentBillAmount = $sqlResult[0]['CurrentBillAmount'];
			$PrincipalArrears = $sqlResult[0]['PrincipalArrears'];
			$InterestArrears = $sqlResult[0]['InterestArrears'];
			$TotalBillPayable = $sqlResult[0]['TotalBillPayable'];
			$PaymentReceived = $sqlResult[0]['PaymentReceived'];;
			$PaidPrincipal = $sqlResult[0]['PaidPrincipal'];;
			$PaidInterest = $sqlResult[0]['PaidInterest'];;
			
			
			$sqlCount = "Select Count(*) as cnt, BillDate from billregister where ID = '" . $BillRegisterID . "'";
			$resCount = $this->m_dbConn->select($sqlCount);

			if($resCount[0]['cnt'] > 0){

				$this->oUpdateBillData->sBillDate = $resCount[0]['BillDate'];
			}
			
			$BillDate = getDisplayFormatDate($this->oUpdateBillData->sBillDate);

			//Delete from billregister
			if(($bIsManualDelete == false && $bIsDeleteAll == true) || ($bIsManualDelete == true && $bIsDeleteAll == true) || ($bIsManualDelete == true && $bIsDeleteAll == false && $resCount[0]['cnt'] > 0))
			{
			    $sqlDelete = "Delete from billregister where ID = '" . $BillRegisterID . "'and BillType='".$IsSupplementaryBill."'";
				$sqlDelete = $this->m_dbConn->delete($sqlDelete);
			}
                        
             //Delete from billdetails
			$sqlDelete = "Delete from billdetails where UnitID ='" . $UnitID . "' and PeriodID ='" . $PeriodID . "' and BillType='".$IsSupplementaryBill."'";
			$sqlDelete = $this->m_dbConn->delete($sqlDelete);

			//Delete from VoucherSales
			$sqlSelect = "Select id, SrNo, VoucherNo from voucher where RefNo = '" . $RefNo . "' and RefTableID = '" . TABLE_BILLREGISTER . "'";
			$sqlResultVoucher = $this->m_dbConn->select($sqlSelect);
			if($sqlResultVoucher <> '')
			{
				if($this->ShowDebugTrace == 1)
				{
					echo "Found Voucher No :" . $sqlResultVoucher[0]['VoucherNo'];
					//print_r($sqlResultVoucher);
				}
				foreach($sqlResultVoucher as $key=>$value)
				{
					if($value['SrNo'] == '1')
					{
						//Delete from AssetRegister
						$sqlDelete = "Delete from assetregister where VoucherID = '" . $value['id'] . "'";
						$sqlResultDelete = $this->m_dbConn->delete($sqlDelete);
						if($this->ShowDebugTrace == 1)
						{
							//echo "<BR>Asset register deleted  for VoucherID = '" . $value['id'] . " result: " . $sqlResultDelete . "<BR>";
						}
					}
					if($sqlResultDelete == 0)
					{
						//Delete from IncomeRegister
						$sqlDelete = "Delete from incomeregister where VoucherID = '" . $value['id'] . "'";
						$sqlResultDelete = $this->m_dbConn->delete($sqlDelete);
						if($this->ShowDebugTrace == 1)
						{
							//echo "Income register deleted for VoucherID = " . $value['id'] . " result: " . $sqlResultDelete . "<BR>";
						}
					}
					if($sqlResultDelete == 0)
					{
						//Delete from liabilityRegister
						$sqlDelete = "Delete from liabilityregister where VoucherID = '" . $value['id'] . "'";
						$sqlResultDelete = $this->m_dbConn->delete($sqlDelete);
						if($this->ShowDebugTrace == 1)
						{
							//echo "Liability register deleted  for VoucherID = '" . $value['id'] . " result: " . $sqlResultDelete . "<BR>";
						}
					}
					$sqlResultDelete = 0;

				}				
				
				$sqlDelete = "Delete from voucher where RefNo = '" . $RefNo . "' and RefTableID = '" . TABLE_BILLREGISTER . "'";
				$sqlResultDelete = $this->m_dbConn->delete($sqlDelete);
			}
                        
			//trace msg for single deleted record
			//$this->delTrace = "Deleted Bill for Unit <".$UnitID."> PeriodID <".$PeriodID.">";

			$previousLog = $this->m_objLog->showChangeLog(TABLE_BILLREGISTER, $RefNo, true);

			if(empty($previousLog)){

				$LedgerDetailsInBill = $this->getAllIncludesLedgersInBill($RefNo);
				$unitNo = $this->m_objUtility->getLedgerName($UnitID);
				$billName = $this->m_objUtility->returnBillTypeString($this->IsSupplementaryBill());
			
				$dataArr = array('Date'=>$BillDate, 'Bill Type'=> $billName,  'Flat'=>$unitNo, 'Bill Number'=>$BillNumber, 'Payment Received'=>$PaymentReceived, 'Paid Principal'=>$PaidPrincipal, 'Paid Interest'=>$PaidInterest, 'Bill Sub Total'=>$BillSubTotal, 'Bill Interest'=>$BillInterest, 'Current Bill Amount'=>$CurrentBillAmount,'Principal Arrears'=>$PrincipalArrears, 'Interest Arrears'=>$InterestArrears, 'Total BillPayable'=>$TotalBillPayable, 'Ledgers'=>$LedgerDetailsInBill);

				$previousLogDesc = json_encode($dataArr);

				$previousLogID = 0;
			}
			else{

				$previousLogDesc = $previousLog[0]['ChangedLogDec'];
				$previousLogId = $previousLog[0]['ChangeLogID'];
			}

			//trace msg for deleted entries
			if($this->ShowDebugTrace == 1)
			{
				echo "Deleted Bill for Unit <".$UnitID."> PeriodID <".$PeriodID."><BR>";
			}
			if($bIsManualDelete == true && $bIsDeleteAll == false)
			{
				$logID = $this->m_objLog->setLog($previousLogDesc, $_SESSION['login_id'], TABLE_BILLREGISTER, $RefNo, DELETE, $previousLogId);
				$this->changeLogDeleteRefArr[$UnitID] = $logID;
			}
			else{

				$logDetail = $this->m_objLog->showChangeLog(TABLE_BILLREGISTER, $RefNo, true);
				$this->changeLogDeleteRefArr[$UnitID] = $logDetail[0]['ChangeLogID'];
			}
      }
	}
	
	private function SetVoucher($UnitID, $PeriodID, $InsertID, $BillDate, $resultFetch, $AdditionalBillingHeads)
	{
	//	$this->ShowDebugTrace = 1;
		if ($this->ShowDebugTrace == 1)
		{
			echo '<BR><br>Inside SetVoucher - UnitID: ' . $UnitID . "<BR>";
			//var_dump($resultFetch);
			
		}

		$sqlFetch = "Select `BillSubTotal`, `BillInterest`, `CurrentBillAmount`, `AdjustmentCredit`, `BillTax`,`IGST`,`CGST`,`SGST`,`CESS`, `Ledger_round_off` from `billdetails` where UnitID = '" . $UnitID . "' and PeriodID = '" . $PeriodID . "' and BillType = '" . $this->IsSupplementaryBill() .  "'";
		$sqlResult = $this->m_dbConn->select($sqlFetch);
		
		$iBillInterest = 0;
		$iCurrentBillAmount = 0;
		
		if($sqlResult <> '')
		{
			$iVoucherCouter = $this->obj_LatestCount->getLatestVoucherNo($_SESSION['society_id']);
			$iSrNo = 1;
			$BillSubTotal = $sqlResult[0]['BillSubTotal'];
			$iBillInterest = $sqlResult[0]['BillInterest'];
//			$iCurrentBillAmount = $sqlResult[0]['CurrentBillAmount'];
			$iAdjustmentCredit = $sqlResult[0]['AdjustmentCredit'];
			$iBillTax = $sqlResult[0]['BillTax'];
			$iIGST = $sqlResult[0]['IGST'];
			$iCGST = $sqlResult[0]['CGST'];
			$iSGST = $sqlResult[0]['SGST'];
			$iCESS = $sqlResult[0]['CESS'];
			$LedgerRoundOff = $sqlResult[0]['Ledger_round_off'];

			$iTotalAmount = $BillSubTotal + $iBillInterest + $iAdjustmentCredit + $iBillTax + $iIGST + $iCGST + $iSGST + $iCESS + $LedgerRoundOff ;
			
			if ($this->ShowDebugTrace == 1)
			{
				echo '<br/>TotalBillAmount: ' . $iTotalAmount . "<BR>";
			}
//			$obj_voucher = new voucher($this->m_dbConn);
			$sqlVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($BillDate), $InsertID, TABLE_BILLREGISTER, $iVoucherCouter, $iSrNo, VOUCHER_SALES, $UnitID, TRANSACTION_DEBIT, $iTotalAmount);
			if ($this->ShowDebugTrace == 1)
			{
				//echo '<br/>After SetVoucherDetails: ' . $regResult . "<BR>";
			}
			
//			$obj_register = new regiser($this->m_dbConn);
		 	$regResult = $this->obj_register->SetRegister(getDBFormatDate($BillDate), $UnitID, $sqlVoucherID, VOUCHER_SALES, TRANSACTION_DEBIT, $iTotalAmount, 0);
			if ($this->ShowDebugTrace == 1)
			{
				//echo '<br/>After SetRegister: ' . $regResult . "<BR>";
			}
			
			$sqlPeriod = "Select BeginingDate, EndingDate from period where ID = '" . $PeriodID . "'";
			$resultPeriod = $this->m_dbConn->select($sqlPeriod);
			
			$beginDate = $resultPeriod[0]['BeginingDate'];
			$endDate = $resultPeriod[0]['EndingDate'];
			
			
			/****************************************************************************
			//Get the Account Heads as per the period selected
			*****************************************************************************/

			$this->strTrace .=  '<tr><td>Updated AccountHead : ' . $sqlFetch . '</td></tr>';
			
			$logMsg =  "<br/><b>Records fetched for Period [" . $PeriodID . "] - Begin Date <= " . $beginDate . "</b>";
			
			$logMsg .= '<br/>' . implode('<br/>', array_map(function ($entry) 
			{
				return "Head : " . $entry['AccountHeadID'] . " Amount : " . $entry['AccountHeadAmount'] . " BeginPeriod " . $entry['BeginPeriod'] . " EndPeriod " . $entry['EndPeriod'];
			}, $resultFetch));
			
			if ($this->ShowDebugTrace == 1)
			{
				//echo $logMsg;
			}
			//$this->strTrace .= "<br/>" . $logMsg;
			
			$sqlResult = array();
			
			if($resultFetch <> "")
			{
				$unit_inprocess = 0;
				$ledger_inprocess = 0;
				$add = false;
				
				for($iCnt = 0; $iCnt < sizeof($resultFetch); $iCnt++)
				{
					if ($this->ShowDebugTrace == 1)
					{
						//echo "<BR>" . $resultFetch[$iCnt]['BeginPeriod'] . " " . $resultFetch[$iCnt]['EndPeriod'] . " Acct Head:". $resultFetch[$iCnt]['AccountHeadID'] . "  Amount : " . $resultFetch[$iCnt]['AccountHeadAmount'];
					}
					$dateDiff = $this->m_objUtility->getDateDiff($resultFetch[$iCnt]['EndPeriod'], $endDate);

					if($unit_inprocess <> $resultFetch[$iCnt]['UnitID'])
					{
						$ledger_inprocess = 0;
					}
					
					if($dateDiff >= 0 && $ledger_inprocess <> $resultFetch[$iCnt]['AccountHeadID'])
					{
						//echo "<BR>Passed";
						$unit_inprocess = $resultFetch[$iCnt]['UnitID'];
						$ledger_inprocess = $resultFetch[$iCnt]['AccountHeadID'];
						array_push($sqlResult, $resultFetch[$iCnt]);
					}
				}
			}
			
			//var_dump($sqlResult);
			
			$logMsg = "<br/><br/><b>Filtered Records for Period [" . $PeriodID . "] with Date Between " . $beginDate . " AND " . $endDate . "</b>";
			$logMsg .= "<br/>" . implode('<br/>', array_map(function ($entry) 
			{
				return "Head : " . $entry['AccountHeadID'] . " Amount : " . $entry['AccountHeadAmount'] . " BeginPeriod " . $entry['BeginPeriod'] . " EndPeriod " . $entry['EndPeriod'];
			}, $sqlResult));
			
			//echo $logMsg;
			$this->strTrace .= "<br/>" . $logMsg;
						
			$ExtraHeadCount = count($AdditionalBillingHeads) ;
			//echo '<br>ExtraHeadCount : ' . $ExtraHeadCount . '<br>';
			$this->strTrace .=  '<tr><td>ExtraHeadCount : ' . $ExtraHeadCount . '</td></tr>';
			
			//add AdditionalBillingHeads elements to member unitbillmaster records
			$Index = 0;
			$InterestOnPrincipalLegderID = INTEREST_ON_PRINCIPLE_DUE; //pending
			$InterestOnArrearsReversalCharge = 0;
			while ($Index < $ExtraHeadCount)
			{
					//echo "<BR>Account head : " . $AdditionalBillingHeads[$Index]['AccountHeadID'];
					if( $AdditionalBillingHeads[$Index]['AccountHeadID'] == $InterestOnPrincipalLegderID)
					{
						//Process Interest credit
						$InterestOnArrearsReversalCharge = $InterestOnArrearsReversalCharge + $AdditionalBillingHeads[$Index]['AccountHeadAmount'];
						if ($this->ShowDebugTrace == 1)
						{
									echo "<BR>SetVoucher:Interest reversal credit : AccountHeadID :". $AdditionalBillingHeads[$Index]['AccountHeadID'] . "  Amount :". $AdditionalBillingHeads[$Index]['AccountHeadAmount'] . "  taxable? :". $AdditionalBillingHeads[$Index]['Taxable'] . "<BR>";
						}
					}
					else
					{
						array_push($sqlResult, $AdditionalBillingHeads[$Index]);
					}
				$Index = $Index +1;
			}
			
			if ($this->ShowDebugTrace == 1)
			{
				//var_dump($sqlResult);
			}
			if($sqlResult <> '')
			{
				foreach($sqlResult as $k=>$v)
				{
					//echo '<br>ID : ' . $sqlResult[$k]['AccountHeadID'] . ' Amount : ' . $sqlResult[$k]['AccountHeadAmount'];
					$this->strTrace .=  '<tr><td>ID : ' . $sqlResult[$k]['AccountHeadID'] . ' Amount : ' . $sqlResult[$k]['AccountHeadAmount'].'</td></tr>';
					
					if($sqlResult[$k]['AccountHeadAmount'] <> '' || $sqlResult[$k]['AccountHeadAmount'] <> 0.00)
					{
						$iSrNo = $iSrNo + 1;
						
						$sqlVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($BillDate), $InsertID, TABLE_BILLREGISTER, $iVoucherCouter, $iSrNo, VOUCHER_SALES, $sqlResult[$k]['AccountHeadID'], TRANSACTION_CREDIT, $sqlResult[$k]['AccountHeadAmount']);
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>Head1 " . $sqlResult[$k]['AccountHeadID'] . "  Amount : " . $sqlResult[$k]['AccountHeadAmount'];
						}
						$regResult = $this->obj_register->SetRegister(getDBFormatDate($BillDate), $sqlResult[$k]['AccountHeadID'], $sqlVoucherID, VOUCHER_SALES, TRANSACTION_CREDIT, $sqlResult[$k]['AccountHeadAmount']);
					}
				}
				if($iBillInterest <> 0)	//Add interest head only if there is interest amount
				{
					$iSrNo = $iSrNo + 1;
					//echo "<BR>iBillInterest : ". $iBillInterest ; 
					$sqlVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($BillDate), $InsertID, TABLE_BILLREGISTER, $iVoucherCouter, $iSrNo, VOUCHER_SALES, INTEREST_ON_PRINCIPLE_DUE, TRANSACTION_CREDIT, $iBillInterest);

					$regResult = $this->obj_register->SetRegister(getDBFormatDate($BillDate), INTEREST_ON_PRINCIPLE_DUE, $sqlVoucherID, VOUCHER_SALES, TRANSACTION_CREDIT, $iBillInterest);
				}
				/*
				$SocDetails=$this->m_objUtility->GetSocietyInformation($_SESSION['society_id']);
				$bApplyServiceTax = $SocDetails['apply_service_tax'];

				$iDateDiff = $this->m_objUtility->getDateDiff(getDBFormatDate($BillDate), GST_START_DATE);

				if($iDateDiff < 0)
				{
					$bApplyServiceTax = 0;
				}
				if($this->ShowDebugTrace == 1)
				{
					echo "<BR>SetVoucher::bApplyServiceTax : " . $bApplyServiceTax  . "<BR>";
				}
				if($bApplyServiceTax == 1) */
				{
					if($iCGST <> 0)
					{
						$iSrNo = $iSrNo + 1;					
						$sqlVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($BillDate), $InsertID, TABLE_BILLREGISTER, $iVoucherCouter, $iSrNo, VOUCHER_SALES, CGST_SERVICE_TAX, TRANSACTION_CREDIT, $iCGST);
	
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>Head CGST" . CGST_SERVICE_TAX . "  Amount : " . $iCGST;
						}
						$this->strTrace .=  '<tr><td>ID : ' . CGST_SERVICE_TAX . ' Amount : ' . $iCGST.'</td></tr>';
						$regResult = $this->obj_register->SetRegister(getDBFormatDate($BillDate), CGST_SERVICE_TAX, $sqlVoucherID, VOUCHER_SALES, TRANSACTION_CREDIT, $iCGST, 0);
					}
					else
					{
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>CGST Amount is zero";	
							//pending : remove from Register
						}
					}
					
					if($iSGST <> 0)
					{					
						$iSrNo = $iSrNo + 1;					
						$sqlVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($BillDate), $InsertID, TABLE_BILLREGISTER, $iVoucherCouter, $iSrNo, VOUCHER_SALES, SGST_SERVICE_TAX, TRANSACTION_CREDIT, $iSGST);
	
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>Head SGST" . SGST_SERVICE_TAX . "  Amount : " . $iSGST ;
						}
						$this->strTrace .=  '<tr><td>ID : ' . SGST_SERVICE_TAX . ' Amount : ' . $iSGST.'</td></tr>';
						$regResult = $this->obj_register->SetRegister(getDBFormatDate($BillDate), SGST_SERVICE_TAX, $sqlVoucherID, VOUCHER_SALES, TRANSACTION_CREDIT, $iSGST, 0);
					}
					else
					{
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>SGST Amount is zero<BR>";	
						}
					}

					if($LedgerRoundOff <> 0)
					{					
						$iSrNo = $iSrNo + 1;					
						$sqlVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($BillDate), $InsertID, TABLE_BILLREGISTER, $iVoucherCouter, $iSrNo, VOUCHER_SALES, ROUND_OFF_LEDGER, TRANSACTION_CREDIT, $LedgerRoundOff);
	
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>Head SGST" . ROUND_OFF_LEDGER . "  Amount : " . $LedgerRoundOff ;
						}
						$this->strTrace .=  '<tr><td>ID : ' . ROUND_OFF_LEDGER . ' Amount : ' . $LedgerRoundOff.'</td></tr>';
						$regResult = $this->obj_register->SetRegister(getDBFormatDate($BillDate), ROUND_OFF_LEDGER, $sqlVoucherID, VOUCHER_SALES, TRANSACTION_CREDIT, $LedgerRoundOff, 0);
					}
					else
					{
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>SGST Amount is zero<BR>";	
						}
					}

					

/*
					if($iIGST <> 0)
					{
						$iSrNo = $iSrNo + 1;					
						$sqlVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($BillDate), $InsertID, TABLE_BILLREGISTER, $iVoucherCouter, $iSrNo, VOUCHER_SALES,IGST_SERVICE_TAX, TRANSACTION_CREDIT, c);
	
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>Head " . IGST_SERVICE_TAX . "  Amount : " . $iIGST . "<BR>";
						}
						$regResult = $this->obj_register->SetRegister(getDBFormatDate($BillDate), IGST_SERVICE_TAX, $sqlVoucherID, VOUCHER_SALES, TRANSACTION_CREDIT, $iIGST, 0);
					}
					else
					{
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>IGST Amount is zero";	
						}
					}
					
					
					if($iCESS <> 0)
					{
						$iSrNo = $iSrNo + 1;					
						$sqlVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($BillDate), $InsertID, TABLE_BILLREGISTER, $iVoucherCouter, $iSrNo, VOUCHER_SALES,CESS_SERVICE_TAX, TRANSACTION_CREDIT, $iCESS);
	
						$regResult = $this->obj_register->SetRegister(getDBFormatDate($BillDate), CESS_SERVICE_TAX, $sqlVoucherID, VOUCHER_SALES, TRANSACTION_CREDIT, $iCESS, 0);	
					}
					else
					{
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>CESS Amount is zero";	
						}
					}
					*/
				}
			}			
		}
	}

	public function SetBillCalcValues($UnitID, $PeriodID, $BillSubTotal, $AmountPaid = 0)
	{
		//Current Period
		$sqlQuery = "Select ID,BalanceAmount,PreviousArrears,BillSubTotal,BalPrincipal,BalInterest,BalanceAmount from billcalc where UnitID=" . $UnitID. " and PeriodID=" . $PeriodID;
		$this->strTrace .=  '<tr><td>'.$sqlQuery.'</td></tr>';
		$result = $this->m_dbConn->select($sqlQuery);
		// end of current preriod 
		echo "<script>alert('".$sqlQuery."');</script>";		
		$ID = $result[0]['ID'];
		echo "ID: <" .$ID.">";
		$this->strTrace .=  '<tr><td>ID: <" .$ID."></td></tr>';
		$oldBillSubTotal = $result[0]['BillSubTotal'];		//PS: Why do we need prev subtotal?

		if($result == "")
		{
			if($AmountPaid > 0)
			{
				echo "<script>alert('No bill generated for unit id <'".$UnitID."'>');</script>";
				return;
			}
			//Previous entry
			
			$PrevPeriodID = $PeriodID - 1;		//Pending: Not necessarily true
				
			$sqlPrevQuery = "Select ID,BalPrincipal,BalInterest,BalanceAmount,BillSubTotal from billcalc where UnitID=" . $UnitID.
			 " and PeriodID=" . $PrevPeriodID; 
			//echo $sqlPrevQuery;
			//echo "<script>alert('".$sqlPrevQuery."');<///script>";
			$Prevresult = $this->m_dbConn->select($sqlPrevQuery);
			
			$BalPrincipal = $Prevresult[0]['BalPrincipal'];
			$BalInterest = $Prevresult[0]['BalInterest'];
			$BalanceAmount = $Prevresult[0]['BalanceAmount'];
			$PrevBillSubTotal = $Prevresult[0]['BillSubTotal'];
			$AccumBillPrincipal = $BillSubTotal;
			$AccumBillInterest = 0;
			$AccumBillSubTotal = $BillSubTotal;
			if($BalanceAmount == 0)
			{
				$BalPrincipal = '0';
				$BalInterest = '0';
				$BalanceAmount = '0';
			}
			else
			{
				$AccumBillPrincipal = $BalPrincipal + $BillSubTotal;
				$AccumBillInterest = $BalInterest + '0';
				$AccumBillSubTotal = $BalanceAmount + $BillSubTotal;
			}
					$InsertQuery = "INSERT INTO `billcalc`(`ID`, `UnitID`, `PeriodID`, `ModifiedFlag`, `BillSubTotal`, `AdjustmentCredit`,
					 `Interest`, `Tax`, `PreviousArrears`, `ArrearInterest`, `TotalDue`, `LateDays`, `PaidAmt`, `PaidPrincipal`, `PaidInterest`,
					  `BalPrincipal`, `BalInterest`, `BalanceAmount`, `Remark`) VALUES (".$ID."," .$UnitID.",".$PeriodID.",'0',".$BillSubTotal.
					  ",'0','0','0',". $BalPrincipal .",". $BalInterest .",". $BalanceAmount .",'0','0','0','0',".$AccumBillPrincipal.
					  ",".$AccumBillInterest.",".$AccumBillSubTotal.",'test insert 1')";
						echo $InsertQuery;
						$this->strTrace .=  '<tr><td>'. $InsertQuery . '</td></tr>';
						echo "<script>alert('".$InsertQuery."');</script>";
						$this->m_dbConn->insert($InsertQuery);
		}
		else
		{
			$CurBalPrincipal = $result[0]['BalPrincipal'];
			$CurBalInterest = $result[0]['BalInterest'];
			$CurBalanceAmount = $result[0]['BalanceAmount'];
			$CurBillSubTotal = $result[0]['BillSubTotal'];
			if($AmountPaid == 0)
			{
				
				$SubTotalDiff = 0;
				if($BillSubTotal >= $CurBillSubTotal)
				{
				   $SubTotalDiff = $BillSubTotal - $CurBillSubTotal;
				}
				else
				{
					$SubTotalDiff = $CurBillSubTotal - $BillSubTotal;
				}
				$CurBalanceAmount = $BillSubTotal;
				$CurBalPrincipal += $SubTotalDiff;
				
				$UpdateQuery = "UPDATE `billcalc` SET `ID`=".$ID.",`UnitID`=".$UnitID.",`PeriodID`=".$PeriodID.",`ModifiedFlag`='1',`BillSubTotal`="
				.$BillSubTotal.",`AdjustmentCredit`='0',`Interest`=0,`Tax`='0',`PreviousArrears`='0',`ArrearInterest`='0',`TotalDue`='0'
				,`LateDays`='0',`PaidAmt`='0',`PaidPrincipal`='0',`PaidInterest`='0',`BalPrincipal`=".$CurBalPrincipal.",`BalInterest`='0',`BalanceAmount`="
				.$CurBalanceAmount.",`Remark`='test update 1' WHERE UnitID=" . $UnitID. " and PeriodID=" . $PeriodID;
				echo $UpdateQuery;
				$this->strTrace .=  '<tr><td>'. $UpdateQuery. '</td></tr>';
				echo "<script>alert('".$UpdateQuery."');</script>";
				$this->m_dbConn->update($UpdateQuery);
			}
			else
			{
				$PreviousArrears = $result[0]['PreviousArrears'];
				$ArrearInterest = $result[0]['ArrearInterest'];
				$TotalDue = $result[0]['TotalDue'];
				
				$iBalPrincipal = $result[0]['BalPrincipal'];
				$iBalInterest = $result[0]['BalInterest'];
				$iBalanceAmount = $result[0]['BalanceAmount'];
				
				$InterestAmount = CalculateInterst($AmountPaid, $InterestPercetage);
				$PaidPrincipal = $AmountPaid - $InterestAmount;
				
				$iBalPrincipal = $PaidPrincipal - $iBalPrincipal;
				$iBalInterest = $InterestAmount - $iBalInterest;
				$iBalAmount = $AmountPaid - $iBalAmount;
				
				$UpdateQuery = "UPDATE `billcalc` SET `ID`=".$ID.",`UnitID`=".$UnitID.",`PeriodID`=".$PeriodID.",`ModifiedFlag`='1',`BillSubTotal`="
				.$BillSubTotal.",`AdjustmentCredit`='0',`Interest`=0,`Tax`='0',`PreviousArrears`=". $PreviousArrears .
				",`ArrearInterest`=".$ArrearInterest.",`TotalDue`=".$TotalDue.",`LateDays`
				='0',`PaidAmt`=". $AmountPaid .",`PaidPrincipal`=".$PaidPrincipal.",`PaidInterest`=".$InterestAmount.",`BalPrincipal`=".$iBalPrincipal.
				",`BalInterest`=".$iBalInterest.",`BalanceAmount`="
				.$iBalAmount.",`Remark`='test update 2' WHERE UnitID=" . $UnitID. " and PeriodID=" . $PeriodID;
				echo $UpdateQuery;
				$this->strTrace .=  '<tr><td>'.$UpdateQuery . '</td></tr>';
				echo "<script>alert('".$UpdateQuery."');</script>";
				$this->m_dbConn->update($UpdateQuery);
			}
		}
	}
	
	
	public function CalculateInterst($AmountPaid, $InterestPercentage)
	{
		$InterestAmount = 0;
		$InterestPercentage = 10;
		$InterestAmount = $AmountPaid / $InterestPercentage;
		return $InterestAmount;
	}
	
	public function SetUpdatedBillParameters($objectUpdatedBill)
	{
		$this->oUpdateBillData = $objectUpdatedBill;
	}
	
	public function UpdateBill()
	{
		echo "GST code not implemented";
		//die;
		
		$info = '';
		$arvalues = explode(',',$this->oUpdateBillData->arJournals);
		$_REQUEST['due_date'] = $this->oUpdateBillData->sDueDate;
		$_REQUEST['bill_date'] = $this->oUpdateBillData->sBillDate;
		$_REQUEST['period_id'] = $this->oUpdateBillData->sPeriodID;
		$_REQUEST['society_id'] = $this->oUpdateBillData->sSocietyID;
		$_REQUEST['wing_id'] = $this->oUpdateBillData->sWingID;
		$_REQUEST['unit_id'] = $this->oUpdateBillData->sUnitID;
		$_REQUEST['adjustment_credit'] = $this->oUpdateBillData->sAdjustmentCredit;
		$this->actionPage ="../Maintenance_bill_edit.php?UnitID=" . $this->oUpdateBillData->sUnitID . "&PeriodID=" .$this->oUpdateBillData->sPeriodID ."&BT=".$this->IsSupplementaryBill();
		if($_REQUEST['period_id'] <> '' && $_REQUEST['bill_date'] <> '' && $_REQUEST['due_date'] <> '')
		{
			$sqlGen = '';
			if($_REQUEST['society_id'] == '0')
			{
				//generate for all units in all societies
				$sqlGen = "select master.UnitID, master.AccountHeadID, master.AccountHeadAmount from unitbillmaster as master where BillType=".$this->IsSupplementaryBill();
			}	
			else if($_REQUEST['wing_id'] == '0')
			{
				//generate for all units in single society
				$sqlGen = "select master.UnitID, master.AccountHeadID, master.AccountHeadAmount, master.BillType from unitbillmaster as master JOIN unit as unitinfo on master.UnitID = unitinfo.unit_id where unitinfo.society_id = '" . $_REQUEST['society_id'] .  "' AND master.billType =".$this->IsSupplementaryBill();
			}
			else if($_REQUEST['unit_id'] == '0')
			{
				//generate for all units in single wing
				$sqlGen = "select master.UnitID, master.AccountHeadID, master.AccountHeadAmount, master.BillType from unitbillmaster as master JOIN unit as unitinfo on master.UnitID = unitinfo.unit_id where unitinfo.society_id = '" . $_REQUEST['society_id'] .  "' and unitinfo.wing_id = '" . $_REQUEST['wing_id'] . "'  AND master.billType =".$this->IsSupplementaryBill();
			}
			else
			{
				//generate for single unit
				$sqlGen = "select master.UnitID, master.AccountHeadID, master.AccountHeadAmount, master.BillType from unitbillmaster as master where master.UnitID = '" . $_REQUEST['unit_id'] . "' AND master.billType =".$this->IsSupplementaryBill();
			}
		echo $sqlGen;
		$this->strTrace .=  '<tr><td>'.$sqlGen.'</td></tr>';
			$result = $this->m_dbConn->select($sqlGen);
			
			if($result == "")
			{
				echo "<script>alert('record doesnt exist');</script>";
			}
			$BillSubTotal = 0;
			if($result <> "")
			{
				//echo "before fetch data";
				$this->strTrace .=  '<tr><td>before fetch data</td></tr>';
				$objFetchDaa = new FetchData($this->m_dbConn);
				
				for($iCount = 0; $iCount < sizeof($this->oUpdateBillData->arrData); $iCount++)
				{
					$Header = $objFetchDaa->GetHeadingFromAccountHead($this->oUpdateBillData->arrData[$iCount]["key"]);
					//echo "after fetch data";
					$this->strTrace .=  '<tr><td>after fetch data</td></tr>';
					$HeaderAmount = $this->oUpdateBillData->arrData[$iCount]["value"];
					$HeaderName = $this->oUpdateBillData->arrData[$iCount]["key"];
					$BillSubTotal += $HeaderAmount;
					//echo "subbill <" . $BillSubTotal . ">.";
					if($HeaderAmount <> 0)
					{
						//$sqlCheck = "select ID from billregister where UnitID = '" . $this->oUpdateBillData->sUnitID . "' and PeriodID = '" . $_REQUEST['period_id'] . "' and AccountHeadID = '" . $this->oUpdateBillData->arrData[$iCount]["key"] . "'" ;
						
						$sqlCheck = "select ID from billdetails where UnitID = '" . $this->oUpdateBillData->sUnitID . "' and PeriodID = '" . $_REQUEST['period_id'] . "'" ;
						
						//echo '<br/>SqlCheck : ' . $sqlCheck;
						//echo "<script>alert('che". $sqlCheck ."');<//script>";
						$resultCheck = $this->m_dbConn->select($sqlCheck);
						if($resultCheck <> "")
						{
							$iLatestChangeID = '1';
							$iBillNo = $this->oUpdateBillData->sBillNo;
							
							//$sqlUpdate = "UPDATE `billregister` SET `CreatedBy`='" . $this->m_dbConn->escapeString($_SESSION['login_id']). "', `BillDate`='" . $this->oUpdateBillData->sBillDate . "', `DueDate`='" . $this->oUpdateBillData->sDueDate . "', `BillNumber`='" . $this->oUpdateBillData->sBillNo . "', `LatestChangeID`='" . $this->m_dbConn->escapeString($iLatestChangeID). "', `AccountHeadAmount`='". $HeaderAmount ."' WHERE ID ='" . $resultCheck[0]['ID'] . "' and UnitID ='" . $this->oUpdateBillData->sUnitID . "' and PeriodID='" . $_REQUEST['period_id'] . "'";
							
							
							$sqlUpdate1 = "UPDATE `billdetails` SET `BillNumber`='" . $this->oUpdateBillData->sBillNo . "', `AdjustmentCredit`='" .$this->oUpdateBillData->sAdjustmentCredit ."' WHERE ID ='" . $resultCheck[0]['ID'] . "' and UnitID ='" . $this->oUpdateBillData->sUnitID . "' and PeriodID='" . $_REQUEST['period_id'] . "'";
							$resultUpdate1 = $this->m_dbConn->update($sqlUpdate1);
							
							$sqlUpdate2 = "UPDATE `voucher` SET `Credit`= '" . $HeaderAmount ."' where `To`= '" . $HeaderName ."' and `RefNo` ='" . $resultCheck[0]['ID'] . "' ";
							$resultUpdate2 = $this->m_dbConn->update($sqlUpdate2);
							
							$sqlUpdate3 = "UPDATE `billregister` SET `CreatedBy`='" . $this->m_dbConn->escapeString($_SESSION['login_id']). "', `BillDate`='" . $this->oUpdateBillData->sBillDate . "', `DueDate`='" . $this->oUpdateBillData->sDueDate . "', `LatestChangeID`='" . $this->m_dbConn->escapeString($iLatestChangeID). "' WHERE ID ='" . $resultCheck[0]['ID'] . "'";
							$resultUpdate3 = $this->m_dbConn->update($sqlUpdate3);
						}
					}
				}
				//echo "subbilltotal <" . $BillSubTotal . ">.";
				$this->strTrace .=  '<tr><td>subbilltotal <" . $BillSubTotal . "></td></tr>';

				$info = "Bills generated successfully.";
			}
			else
			{
				$info = "No Units found in Bill Master to Generate Bills for selected criteria.";
			}
			echo "calling calcbill" . $_REQUEST['unit_id'] . '"'. $_REQUEST['period_id'] .'"' .$BillSubTotal;
			$this->strTrace .=  "<tr><td>calling calcbill" . $_REQUEST['unit_id'] . "'". $_REQUEST['period_id'] ."'" .$BillSubTotal . "</td></tr>";
			//$this->SetBillCalcValues($_REQUEST['unit_id'], $_REQUEST['period_id'], $BillSubTotal);	
			echo "bill calc done";
			$this->strTrace .=  '<tr><td>bill calc done</td></tr>';
		}
		else
		{
			$info = 'Please enter valid data for all the fields.';		
		}
		//echo "<script>alert('exit')<//script>";
		return $info;
	}
	
	public function GetValueFromBillCalc($UnitID, $PeriodID, $ColumnName)
	{
		$sqlCheck = "select ".$ColumnName." from billcalc where UnitID =" . $UnitID . " and PeriodID = " . $PeriodID ;
						
			//echo '<br/>SqlCheck : ' . $sqlCheck;
			//echo "<script>alert('che". $sqlCheck ."');<//script>";
			$resultCheck = $this->m_dbConn->select($sqlCheck);
			if($resultCheck <> "")
			{
				return $resultCheck[0][$ColumnName];
				
			}
	}
	public function GetValueFromChequeEntryDetails($UnitID, $PeriodID, $ColumnName)
	{
		//$sqlCheck = "select ".$ColumnName." from chequeentrydetails where UnitID =" . $UnitID . " and PeriodID = " . $PeriodID ;
		$sqlCheck = "select ".$ColumnName." from chequeentrydetails where ID='1'" ;
					
		//echo '<br/>SqlCheck : ' . $sqlCheck;
		//echo "<script>alert('che". $sqlCheck ."');<//script>";
		$resultCheck = $this->m_dbConn->select($sqlCheck);
		if($resultCheck <> "")
		{
			//echo "<script>alert('".$resultCheck[0][$ColumnName]."')<//script>";
			return $resultCheck[0][$ColumnName];
			
		}
	}

	public function IsLedgerTaxable($LedgerID)
	{
			//Code Review: subcategory column should change to ledger name
			$sqlCheck = "select * from ledger where id = " . $LedgerID ;
						
			//echo '<br/>SqlCheck : ' . $sqlCheck;
			$this->strTrace .=  '<tr><td>SqlCheck : ' . $sqlCheck . '</td></tr>';
			//echo "<script>alert('che". $sqlCheck ."');<//script>";
			$resultCheck = $this->m_dbConn->select($sqlCheck);
			if($resultCheck <> "")
			{
				//echo "<script>alert('".$resultCheck[0]['subcategory']."')<//script>";
				return $resultCheck[0]['taxable'];
			}	
			
			return 0;
	}

	public function GetListofSundryDebtors()
	{
			//Code Review: subcategory column should change to ledger name
			$sqlCheck = "select subcategory from ledger where receipt='1'" ;
						
			echo '<br/>SqlCheck : ' . $sqlCheck;
			$this->strTrace .=  '<tr><td>SqlCheck : ' . $sqlCheck . '</td></tr>';
			//echo "<script>alert('che". $sqlCheck ."');<//script>";
			$resultCheck = $this->m_dbConn->select($sqlCheck);
			/*if($resultCheck <> "")
			{
				//echo "<script>alert('".$resultCheck[0]['subcategory']."')<//script>";
				return $resultCheck[0][$ColumnName];
			}	
			*/
			return $resultCheck;
	}

	public function getPreviousPeriodData($PeriodID)
	{
//			echo '<br/>PeriodID : ' . $PeriodID;
			$sqlPrevQuery = "Select Type, YearID, PrevPeriodID, Status from period where ID=" . $PeriodID;
			//echo '<br/>sqlPrevQuery : ' . $sqlPrevQuery;
			$Prevresult = $this->m_dbConn->select($sqlPrevQuery);
			$PrevPeriodID = -1;
			if(!is_null($Prevresult))
			{
				$Type = $Prevresult[0]['Type'];			
				$YearID = $Prevresult[0]['YearID'];			
				$PrevPeriodID = $Prevresult[0]['PrevPeriodID'];			
/*				echo '<br/>Prev Period : ' . $Type;
				echo '<br/>Prev YearID : ' . $YearID;
				echo '<br/>PrevPeriodID : ' . $PrevPeriodID;*/
			}
			return $PrevPeriodID;	
	}

//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
	public function GetPrevBillData($PrevBillRegisterID)
	{
		$sqlBillPrevQuery = "Select * from billregister where ID=" . $PrevBillRegisterID ; 
		//echo '<br/>sqlPrevQuery : ' . $sqlBillPrevQuery;
		$SQLResult = $this->m_dbConn->select($sqlBillPrevQuery);

		return $SQLResult;
	}
/*
	public function PrevBillDueDate($PrevBillRegisterID)
	{
		$sqlBillPrevQuery = "Select DueDate from billregister where ID=" . $PrevBillRegisterID ; 
		//echo '<br/>sqlPrevQuery : ' . $sqlBillPrevQuery;
		$SQLResult = $this->m_dbConn->select($sqlBillPrevQuery);

		$PrevBillDueDate = 0;

		if(!is_null($SQLResult))
		{
			$PrevBillDueDate = $SQLResult[0]['DueDate'];;
		}
		return $PrevBillDueDate;
	}
	
	public function PrevBillBillDate($PrevBillRegisterID)
	{
		$PrevBillBillDate = 0;
		if($PrevBillRegisterID <> '' && $PrevBillRegisterID <> 0)
		{
			$sqlBillPrevQuery = "Select BillDate from billregister where ID=" . $PrevBillRegisterID ; 
			//echo '<br/>sqlPrevQuery : ' . $sqlBillPrevQuery;
			$SQLResult = $this->m_dbConn->select($sqlBillPrevQuery);
			if(!is_null($SQLResult))
			{
				$PrevBillBillDate = $SQLResult[0]['BillDate'];
			}
		}
		return $PrevBillBillDate;
	}
*/	
	
/*	public function generateBill()
	{
		$UnitID = 30;
		$PeriodID = 7;
		$PrevPeriodID = $this->getPreviousPeriodData($UnitID, $PeriodID);
		if($PrevPeriodID < 1	)
		{
			echo '<br/>No prev period for period <' . $PeriodID . "> : assuming this is first period.";
			//return;
		}
				
		$this->generateBill_PerUnit($UnitID, $PeriodID, $PrevPeriodID);
		return;
	}
*/

//****************************************
//*** Update GSTNoThresholdFlag ****
//****************************************

	public function UpdateGSTNoThresholdFlag($PeriodID)
	{
		$societyInfo = $this->m_objUtility->GetSocietyInformation($_SESSION['society_id']);	
		$ResultPreviousPeriodData = $this->objFetchData->getPreviousPeriodData($PeriodID,true);
		$this->GetGSTNoThresholdFlag_perMember($societyInfo, $PeriodID, $ResultPreviousPeriodData[0]['PrevPeriodID'], $ResultPreviousPeriodData[0]['BeginingDate'], $ResultPreviousPeriodData[0]['EndingDate'],1);
	}




	public function GetGSTNoThresholdFlag_perMember($societyInfo, $PeriodID, $PrevPeriodID, $PrevPeriodBeginingDate, $PrevPeriodEndingDate,$bUpdateDB = 0)
	{
		$i = 0;
		$returnResult = array();

		$SQL_Select = "select * from `unit` where taxable_no_threshold = 1";
		$UnitValue = $this->m_dbConn->select($SQL_Select);
	
		if($bUpdateDB == 1)
		{
			$SQL_Update = "update unit set taxable_no_threshold = 0";
			$UpdateValue = $this->m_dbConn->update($SQL_Update);
		}
		$SQL_Select = "select * from `unit` where taxable_no_threshold = 1";
		$UnitValue = $this->m_dbConn->select($SQL_Select);
		
		$ApplyServiceTaxOnInterest = $societyInfo['apply_GST_on_Interest'];
		$ApplyGSTAboveThreshold = $societyInfo['apply_GST_above_Threshold'];
		$GST_Threshold = $societyInfo['service_tax_threshold'];
	
		$sqlQuery = "SELECT distinct(primary_Owner_Name) FROM `member_main`";
		$sqlQuery = "SELECT primary_Owner_Name, count(primary_Owner_Name) as flatcount  FROM `member_main` group by primary_Owner_Name";
		$SQLResult = $this->m_dbConn->select($sqlQuery);

		if(!is_null($SQLResult))
		{
			for($iCounter = 0; $iCounter < sizeof($SQLResult); $iCounter++)
			{
				if($this->ShowDebugTrace)
				{
					echo "<BR><BR>". $iCounter;	
				} 
				
				$flatcount = $SQLResult[$iCounter]['flatcount'];
				$primary_Owner_Name = $SQLResult[$iCounter]['primary_Owner_Name'];
				if($this->ShowDebugTrace)
				{
					echo "<BR>primary_Owner_Name : " . $primary_Owner_Name;	
				}
				$TaxableLedgerTotal = 0;
				if($flatcount > 1 && $primary_Owner_Name <> '')
				{
					if($this->ShowDebugTrace)
					{
						echo "<BR><BR><BR>primary_Owner_Name : " . $primary_Owner_Name . " and flat count : " . $flatcount;	
					}
					
					$SQLGSTIN = "SELECT * FROM `unit` AS U JOIN member_main AS MM ON U.unit_id = MM.unit WHERE MM.primary_owner_name = '" . $primary_Owner_Name . "'";
				
					$SQLGSTINResult = $this->m_dbConn->select($SQLGSTIN);
					$TaxableAmount = 0;
				
					if(!is_null($SQLGSTINResult))
					{
						for($iGSTINCounter = 0; $iGSTINCounter < sizeof($SQLGSTINResult); $iGSTINCounter++)
						{
							$Unit_ID = $SQLGSTINResult[$iGSTINCounter]['unit_id'];
						
							$TaxableLedgerTotal = $TaxableLedgerTotal + $this->GetTaxableAmount($Unit_ID, $PeriodID, $PrevPeriodID, $PrevPeriodBeginingDate, $PrevPeriodEndingDate);
							
							if($this->ShowDebugTrace)
							{
								echo "<BR>TaxableLedgerTotal 123: " . $TaxableLedgerTotal;
							}
						}						
					}
					
					if($this->ShowDebugTrace)
					{
						echo '<br>=================';
						echo '<br>Taxable : '.$TaxableLedgerTotal.';';
						echo '<br>GstThreshold : '.$GST_Threshold.';';
						echo '<br>=================';
					}	
					
						
					if($TaxableLedgerTotal > $GST_Threshold)
					{
						if($this->ShowDebugTrace)
						{
							echo "<BR><BR>TaxableLedgerTotal " . $TaxableLedgerTotal . " > GST_Threshold " . $GST_Threshold  . " Update Unit_taxable_no_threshold flag";
						}
						$UnitArray= array();
						for($iGSTINCounter = 0; $iGSTINCounter < sizeof($SQLGSTINResult); $iGSTINCounter++)
						{
							$Unit_ID = $SQLGSTINResult[$iGSTINCounter]['unit_id'];
							$UnitArray[$iGSTINCounter]['unit_id'] = $Unit_ID;
					
							$SQLGSTINResult[$iGSTINCounter]['flag'] = 1;
							if($bUpdateDB == 1)
							{
								$SQL_Update = "update unit set taxable_no_threshold = 1 where unit_id=" . $Unit_ID;
								$UpdateValue = $this->m_dbConn->update($SQL_Update);
								
								if($this->ShowDebugTrace)
								{
									echo "<BR>SQL_Update " . $SQL_Update;
									echo "<BR>Updated " . $Unit_ID;
								}
							}
						}
						$returnResult[$i]['name'] = $primary_Owner_Name; 
						$returnResult[$i]['nt_unit'] = $UnitArray; 
						
						$i++;
					}
					else
					{
						$SQLGSTINResult[iGSTINCounter]['flag'] = 0;	
											
					}
				}
			}
		}
	
		return $returnResult;
	}
	
	private function GetTaxableAmount($UnitID, $PeriodID, $PrevPeriodID, $PrevPeriodBeginingDate, $PrevPeriodEndingDate)
	{
		//$ApplyServiceTaxOnInterest = $societyInfo['apply_GST_on_Interest'];
		//echo "<BR>ApplyServiceTaxOnInterest " . $ApplyServiceTaxOnInterest ;

		//echo '<BR> Before GetReversalCredits :<BR>';
		$AdditionalBillingHeads = $this->GetReversalCredits($UnitID, $PeriodID, $PrevPeriodBeginingDate, $PrevPeriodEndingDate);
		
//		$this->strTrace .=  '<tr><td>AdditionalBillingHeads :</td></tr>';

		if ($this->ShowDebugTrace == 1)
		{
				$ExtraHeadCount = count($AdditionalBillingHeads) ;
				//echo "<BR> AdditionalBillingHeads : " . $ExtraHeadCount . " <BR>";
				//var_dump($AdditionalBillingHeads);
		}
		$BillMaster = $this->GetBillMaster($UnitID, $PeriodID);

		if ($this->ShowDebugTrace == 1)
		{
			//var_dump($BillMaster);
		}
		$TaxableLedgerTotal_No_Threshold = 0;
		$InterestOnArrearsReversalCharge = 0;
		$TaxableLedgerTotal = 0;
		$BillNote = "";
		//** Old Code Commented
		//$BillSub_Total = $this->GetBillSubTotal_Plain($UnitID, $PeriodID, $BillMaster, $AdditionalBillingHeads, $TaxableLedgerTotal, $TaxableLedgerTotal_No_Threshold, $InterestOnArrearsReversalCharge, $BillNote);
		$TaxableLedgerTotal = $this->GetBillSubTotal_Plain($UnitID, $PeriodID, $BillMaster, $AdditionalBillingHeads, $TaxableLedgerTotal, $TaxableLedgerTotal_No_Threshold, $InterestOnArrearsReversalCharge, $BillNote,true);
		return $TaxableLedgerTotal;

	}

//*************************************

	private function GetDebitNoteTotal($UnitID, $PrevPeriodBeginingDate, $PrevPeriodEndingDate)
	{
		$debit_note_amount_total = 0;
		
		$DebitNoteSql = "SELECT * FROM credit_debit_note WHERE date >= '". $PrevPeriodBeginingDate . "' AND date <= '" . $PrevPeriodEndingDate . "' AND UnitID = ". $UnitID ." AND Note_Type = '". DEBIT_NOTE . "' AND BillType  = ". $this->IsSupplementaryBill();	
		$DebitNoteResult = $this->m_dbConn->select($DebitNoteSql);
		if ($this->ShowDebugTrace == 1)
		{
			echo '<BR><br/>Processing Debit Notes: ';
			echo '<br/>DebitNoteSql : ' . $DebitNoteSql;
			var_dump($DebitNoteResult);
		}
		for($i = 0 ; $i < sizeof($DebitNoteResult); $i++)
		{
			$debit_note_amount_total = $debit_note_amount_total + $DebitNoteResult[$i]['TotalPayable'];
		}			
		return $debit_note_amount_total;		
	}

public function ProcessPreviousPrincipal(&$PrevPrincipalDue, &$PaymentReceived, &$CurrentBillInterestAmount, &$PaidPrincipal, $societyInfo, $TransactionType, $PaymentDate, $PrevPeriodBeginingDate, $PrevPeriodEndingDate, $PrevBillDueDate)
{
	$SubPaidPrincipal=0;
	//echo '<br/><br/>PrevPrincipalDue Processing...';
	$this->strTrace .=  '<tr><td>** Processing PrevPrincipalDue...</td></tr>';
	if($PaymentReceived > 0) 
	{
		//echo '<br/>Remaining payment ' . $PaymentReceived . ' is applied to Prev Principal Arrars' . $PrevPrincipalDue;
		$this->strTrace .=  '<tr><td>Remaining payment ' . $PaymentReceived . ' is applied to Prev Principal Arrars ' . $PrevPrincipalDue . '</td></tr>';
		//echo '<br/>PrevPrincipalDue  : ' . $PrevPrincipalDue;
		$this->strTrace .=  '<tr><td>PrevPrincipalDue  : ' . $PrevPrincipalDue . '</td></tr>';
		//echo '<br/>PaymentReceived : ' . $PaymentReceived;
		$this->strTrace .=  '<tr><td>PaymentReceived : ' . $PaymentReceived . '</td></tr>';
		if($PrevPrincipalDue > 0)
		{
			//echo '<br/>PrevPrincipalDue Processing...';
			//$this->strTrace .=  '<tr><td>PrevPrincipalDue Processing...</td></tr>';
			if($PaymentReceived > $PrevPrincipalDue )
			{
				$SubPaidPrincipal = $PrevPrincipalDue;
				$PaymentReceived = $PaymentReceived - $PrevPrincipalDue;
				$PrevPrincipalDue = 0;
			}
			else
			{
				$SubPaidPrincipal = $PaymentReceived ;
				$PrevPrincipalDue = $PrevPrincipalDue - $PaymentReceived ;
				$PaymentReceived = 0;
			}	
			if ($this->ShowDebugTrace == 1)
			{
				echo '<br/>SubPaidPrincipal  : ' . $SubPaidPrincipal;
				echo '<br/>PaymentReceived : ' . $PaymentReceived;
				echo '<br/>';
			}
			$this->strTrace .=  '<tr><td>SubPaidPrincipal  : ' . $SubPaidPrincipal . '</td></tr>';
			$this->strTrace .=  '<tr><td>PaymentReceived : ' . $PaymentReceived . '</td></tr>';
		}
		else
		{
			$this->strTrace .=  '<tr><td>No PrevPrincipalDue  to process : ' . $PrevPrincipalDue . '</td></tr>';
		}

		$InterestMethod = $societyInfo['int_method'];
		if($InterestMethod == 1 && $societyInfo['bill_cycle'] == MONTHLY) //delay after days : For Skylark
		{
			$PrincipalPaymentDateDiff = $this->m_objUtility->getDateDiff($PaymentDate, $PrevPeriodBeginingDate);				
			$this->strTrace .=  '<tr><td>PaymentDateDiff of : ' . $PrincipalPaymentDateDiff . ' between PrevPeriodBeginingDate ' . $PrevPeriodBeginingDate . ' and PaymentDate ' . $PaymentDate . ' </td></tr>';
		}
		else
		{
			$PrincipalPaymentDateDiff = $this->m_objUtility->getDateDiff($PaymentDate, $PrevBillDueDate); //$PrevBillDueDate				
			if ($this->ShowDebugTrace == 1)
			{				
				echo '<br/>Date diff ' . $PrincipalPaymentDateDiff . ' No interest if prev principal amount paid before due date: ' . $PrevBillDueDate;
			}
			$this->strTrace .=  '<tr><td>No interest if prev principal amount paid before due date : ' . $PrevBillDueDate. ' </td></tr>';
			$this->strTrace .=  '<tr><td>PaymentDateDiff of : ' . $PrincipalPaymentDateDiff . ' between PrevBillDueDate ' . $PrevBillDueDate . ' and PaymentDate ' . $PaymentDate . ' </td></tr>';
		}

		if ($this->ShowDebugTrace == 1)
		{
			echo '<BR>ProcessPreviousPrincipal PaymentDateDiff of : ' . $PrincipalPaymentDateDiff . ' between PrevPeriodBeginingDate ' . $PrevPeriodBeginingDate . ' and PaymentDate ' . $PaymentDate . ' </br>';
			
		}
		//Use datediff instead
		if (($TransactionType <> CREDIT_NOTE) && $SubPaidPrincipal>0 && ($PrincipalPaymentDateDiff>0))
		{
			echo '<br/>Calc interest on : ' . $SubPaidPrincipal;
			$this->strTrace .=  '<tr><td>Calc interest on : ' . $SubPaidPrincipal . ' from PrevPeriodEdning date to Payment Date</td></tr>';
			//If payment is late calculate interest on part of principal that is paid by this cheque
			//Calculate interest on paid principal arrears from period Begining date till (excluding) Payment date
			if ($this->ShowDebugTrace == 1)
			{				
				echo "<BR>Test1";
				echo '<br/>Calculate interest on paid PrevPrincipalAmount :' . $InterestableAmount ;				
			}
			$InterestAmount = $this->GetInterest($UnitID, $SubPaidPrincipal, $PrevPeriodBeginingDate, $PaymentDate, $societyInfo, 1, $PrevPeriodBeginingDate);
			$CurrentBillInterestAmount = $CurrentBillInterestAmount + $InterestAmount;
			//echo '<br/>CurrentBillInterestAmount : ' . $CurrentBillInterestAmount;
			$this->strTrace .=  '<tr><td>CurrentBillInterestAmount : ' . $CurrentBillInterestAmount . '</td></tr>';
		}
		else
		{
			//echo '<br/>No interest: ';
			$this->strTrace .=  '<tr><td>No interest charged: </td></tr>';
		}
		
		$PaidPrincipal = $PaidPrincipal + $SubPaidPrincipal;
		//echo '<br/>Paid Principal: ' . $PaidPrincipal;
		$this->strTrace .=  '<tr><td>Paid Principal so far2: ' . $PaidPrincipal . '</td></tr>';
		if ($this->ShowDebugTrace == 1)
		{
				echo '<br/>PaidPrincipal : ' . $PaidPrincipal ;
				echo '<br/>Remaining PrevPrincipalDue  : ' . $PrevPrincipalDue  ;
		}
		
	}
	else
	{
		//echo '<br/>No payment for PrevPrincipalDue Processing...';
		$this->strTrace .=  '<tr><td>No payment for PrevPrincipalDue Processing...</td></tr>';
		
	}
	//echo '<br/>End of PrevPrincipalDue Processing...';
	$this->strTrace .=  '<tr><td>End of PrevPrincipalDue Processing... </td></tr>';
	
}

	public function ProcessPreviousBillPrincipal(&$PrevBillPrincipalAmount, &$PaymentReceived, &$CurrentBillInterestAmount, &$PaidPrincipal, $societyInfo, $TransactionType, $PaymentDate, $PrevPeriodEndingDate, $PrevBillDueDate, $PrevPeriodBeginingDate)
	{
		$SubPaidPrincipal = 0;			
		//echo '<br/><br/>PrevBillPrincipalAmount Processing...';
		$this->strTrace .=  '<tr><td>** Processing PrevBillPrincipalAmount ...</td></tr>';
		if($PaymentReceived > 0) 
		{
			//echo '<br/>PrevBillPrincipalAmount  : ' . $PrevBillPrincipalAmount;
			$this->strTrace .=  '<tr><td>PrevBillPrincipalAmount  : ' . $PrevBillPrincipalAmount . '</td></tr>';
			//echo '<br/>PaymentReceived : ' . $PaymentReceived;
			$this->strTrace .=  '<tr><td>PaymentReceived : ' . $PaymentReceived . '</td></tr>';
			if($PrevBillPrincipalAmount  > 0)
			{
				//echo '<br/>Processing...';
				//$this->strTrace .=  '<tr><td>Processing...</td></tr>';
				
				if($PaymentReceived > $PrevBillPrincipalAmount )
				{
					$SubPaidPrincipal = $PrevBillPrincipalAmount;
					$PaymentReceived = $PaymentReceived - $PrevBillPrincipalAmount;
					$PrevBillPrincipalAmount = 0;
				}
				else
				{
					$SubPaidPrincipal = $PaymentReceived ;
					$PrevBillPrincipalAmount = $PrevBillPrincipalAmount - $PaymentReceived ;
					$PaymentReceived = 0;
				}
				//echo '<br/>SubPaidPrincipal  : ' . $SubPaidPrincipal;
				$this->strTrace .=  '<tr><td>SubPaidPrincipal  : ' . $SubPaidPrincipal . '</td></tr>';
				//echo '<br/>Remaining PrevBillPrincipalAmount  : ' . $PrevBillPrincipalAmount;
				$this->strTrace .=  '<tr><td>Remaining PrevBillPrincipalAmount  : ' . $PrevBillPrincipalAmount . '</td></tr>';
					//echo '<br/>PaymentReceived : ' . $PaymentReceived;
				$this->strTrace .=  '<tr><td>PaymentReceived Balance : ' . $PaymentReceived . '</td></tr>';
				
				$PaymentDateDiff = $this->m_objUtility->getDateDiff($PaymentDate, $PrevBillDueDate);
				$this->strTrace .=  '<tr><td>PrevBillPrincipal PaymentDateDiff of : ' . $PaymentDateDiff . ' between PrevBillDueDate ' . $PrevBillDueDate . ' and PaymentDate ' . $PaymentDate . ' </td></tr>';
					if ($this->ShowDebugTrace == 1)
					{				
						echo "<BR>PrevBillPrincipal PaymentDateDiff of : ' . $PaymentDateDiff . ' between PrevBillDueDate ' . $PrevBillDueDate . ' and PaymentDate ' . $PaymentDate . ' </br>";
					}
				if (($TransactionType <> CREDIT_NOTE) && $SubPaidPrincipal > 0 && ($PaymentDateDiff  > 0))
				{
					
					$this->strTrace .=  '<tr><td>Calc interest on SubPaidPrincipal: ' . $SubPaidPrincipal . ' after Bill Due date and Payment Date</td></tr>';
					//If payment is late calculate interest on part of prev bill amount that is paid by this cheque
					if ($this->ShowDebugTrace == 1)
					{				
						echo '<br/>Calc interest on PrevBillPrincipal : ' . $SubPaidPrincipal;
						echo "<BR>Test2";
						echo '<br/>Calculate interest on Paid PrevBillPrincipalAmount becuase payment was after due date:' . $InterestableAmount ;				
					}
					
					$InterestAmount = $this->GetInterest($UnitID, $SubPaidPrincipal, $PrevBillDueDate, $PaymentDate, $societyInfo, 0, $PrevPeriodBeginingDate);
					//echo "Before m_DontChargeIntToLastBillAmt " . $this->m_DontChargeIntToLastBillAmt;
					if($this->m_DontChargeIntToLastBillAmt == 1)
					{
						$this->strTrace .=  '<tr><td>Interest : ' . $InterestAmount. ' of prev bill amount waived on amount ' . $SubPaidPrincipal . '</td></tr>';
						//echo '<BR>Interest : ' . $InterestAmount. ' of prev bill amount waived on amount ' . $SubPaidPrincipal;
						$InterestAmount = 0;						
					}
					$CurrentBillInterestAmount = $CurrentBillInterestAmount + $InterestAmount;
					//echo '<br/>CurrentBillInterestAmount : ' . $CurrentBillInterestAmount;
					$this->strTrace .=  '<tr><td>CurrentBillInterestAmount : ' . $CurrentBillInterestAmount . '</td></tr>';
				}
				else
				{
					//echo '<br/>No interest: ';
					$this->strTrace .=  '<tr><td>No interest due on this portion of ' . $SubPaidPrincipal . ' amount</td></tr>';
				}
				
			}
			$PaidPrincipal = $PaidPrincipal + $SubPaidPrincipal;
			//echo '<br/>Paid Principal: ' . $PaidPrincipal;
			$this->strTrace .=  '<tr><td>Paid Principal so far: ' . $PaidPrincipal . '</td></tr>';
			//echo '<br/>';
			
		}
		else
		{
			//echo '<br/>No payment for PrevBillPrincipalAmount Processing...';
			$this->strTrace .=  '<tr><td>No payment for PrevBillPrincipalAmount Processing...</td></tr>';
		}
		if ($this->ShowDebugTrace == 1)
		{
				echo '<br/>PaidPrincipal : ' . $PaidPrincipal ;
				echo '<br/>Remaining PrevBillPrincipalAmount  : ' . $PrevBillPrincipalAmount  ;
		}
			//echo '<br/>End of PrevBillPrincipalAmount Processing...';
		$this->strTrace .=  '<tr><td>End of PrevBillPrincipalAmount Processing...</td></tr>';
		//Process PrevPrincipal Arrears as we follow LIFO
		$SubPaidPrincipal = 0;			
		
	}
	public function ProcessBillInterest(&$PrevInterestDue, &$PaidInterest, &$PaymentReceived)
	{
		$this->strTrace .=  '<tr><td>** PrevInterestDue Processing...</td></tr>';
		//echo '<br/>PrevInterestDue  : ' . $PrevInterestDue;
		$this->strTrace .=  '<tr><td>PrevInterestDue  : ' . $PrevInterestDue . '</td></tr>';
		//echo '<br/>PaidInterest  : ' . $PaidInterest;
		$this->strTrace .=  '<tr><td>PaidInterest  : ' . $PaidInterest . '</td></tr>';
		//echo '<br/>PaymentReceived : ' . $PaymentReceived;
		$this->strTrace .=  '<tr><td>PaymentReceived : ' . $PaymentReceived . '</td></tr>';
		if($PrevInterestDue > 0)
		{
			//echo '<br/>Processing...';
			//$this->strTrace .=  '<tr><td>Processing PrevInterestDue...</td></tr>';
			if($PaymentReceived > $PrevInterestDue )
			{
				$PaidInterest = $PaidInterest + $PrevInterestDue;
				$PaymentReceived = $PaymentReceived - $PrevInterestDue;
				$PrevInterestDue = 0;
			}
			else
			{
				$PaidInterest = $PaidInterest + $PaymentReceived ;
				$PrevInterestDue = $PrevInterestDue - $PaymentReceived ;
				$PaymentReceived = 0;
			}
			
			//echo '<br/>PaidInterest  : ' . $PaidInterest;
			$this->strTrace .=  '<tr><td>PaidInterest  : ' . $PaidInterest . '</td></tr>';
			//echo '<br/>Remaining TotalPrevInterestDue : ' . $PrevInterestDue;
			$this->strTrace .=  '<tr><td>Remaining TotalPrevInterestDue : ' . $PrevInterestDue . '</td></tr>';
			//echo '<br/>PaymentReceived : ' . $PaymentReceived;
			$this->strTrace .=  '<tr><td>Balance PaymentReceived : ' . $PaymentReceived . '</td></tr>';
			if ($this->ShowDebugTrace == 1)
			{
				//echo '<br/>Processing Interest ';
				echo '<br/>PaidInterest : ' . $PaidInterest ;
				echo '<br/>Remaining TotalPrevInterestDue  : ' . $PrevInterestDue ;
				echo '<br/>PaymentReceived  : ' . $PaymentReceived ;
			}
		}
		else
		{
			//echo '<br/>No PrevInterestDue to process...';							
			$this->strTrace .=  '<tr><td>No PrevInterestDue to process...</td></tr>';
			if ($this->ShowDebugTrace == 1)
			{
				echo '<br/>No Interest for Processing ';
			}
		}
		
		//echo '<br/>End of PrevInterestDue Processing...';
		$this->strTrace .=  '<tr><td>End of PrevInterestDue Processing...</td></tr>';
			if ($this->ShowDebugTrace == 1)
			{
				echo '<br/>End of PrevInterestDue Processing... ';
			}
		//echo '<br/>';
		
	}
	
	public function generateBill_PerUnit($UnitID, $PeriodID, $PrevPeriodID, $PrevPeriodBeginingDate, $PrevPeriodEndingDate, $BillRegisterID, $BillNo, $sBillDate, $Unit_taxable_no_threshold, $societyInfo)
	{
		$Unit_taxable_no_threshold = 0; //This feature was added for IDP1. As per new law each unit would be treated as individual unit and this feature is not required
		$Main_BillDate = $sBillDate;
		//$this->ShowDebugTrace = 1;
		if($this->ShowDebugTrace == 1)
		{
			echo "<BR>-------------------- generateBill_PerUnit ---------------------";
		}

		$BillNote = "";
		$PrevPrincipalDue = 0;
		$PrevInterestDue = 0;
		$PrevBillSubTotal = 0;
		$PrevAdjustmentCredit= 0;
		$PrevBillTax = 0;
		$PrevIGST = 0;
		$PrevCGST = 0;
		$PrevSGST = 0;
		$PrevCESS = 0;
		$PrevRounding = 0;
		$PrevBillPrincipalAmount = 0;
		$PrevBillInterestAmount = 0;
		$CurrentBillInterestAmount = 0;
		$PrevBillBillDate="";
		$dateDiff  =0;		
		//$sqlPrevQuery = "Select ID, BillRegisterID, PrincipalArrears, InterestArrears, `BillSubTotal`, `AdjustmentCredit`, `BillTax`,`IGST`,`CGST`, `SGST`, `CESS`, `BillInterest` from billdetails where UnitID=" . $UnitID.	 " and PeriodID=" . $PrevPeriodID." AND BillType ='" . $this->IsSupplementaryBill()."' " ; 
$sqlPrevQuery = "Select bd.ID, BillRegisterID, PrincipalArrears, InterestArrears, `BillSubTotal`, `AdjustmentCredit`, `BillTax`,`IGST`,`CGST`, `SGST`, `CESS`, `BillInterest`, `Ledger_round_off`, br.BillDate, br.DueDate from billdetails as bd JOIN billregister as br on bd.BillregisterId = br.id where UnitID=" . $UnitID."  and bd.PeriodID=" . $PrevPeriodID." AND bd.BillType ='" . $this->IsSupplementaryBill()."' " ; 
//		echo "<BR><BR>". $sqlPrevQuery;

/*
				$sqlBillPrevQuery = "Select BillDate from billregister where ID=" . $PrevBillRegisterID ; 
			//echo '<br/>sqlPrevQuery : ' . $sqlBillPrevQuery;
			$SQLResult = $this->m_dbConn->select($sqlBillPrevQuery);
			if(!is_null($SQLResult))
			{
				$PrevBillBillDate = $SQLResult[0]['BillDate'];
			}
			*/
			//echo '<br/>sqlPrevQuery : ' . $sqlPrevQuery;
		$SQLResult = $this->m_dbConn->select($sqlPrevQuery);
		if(!is_null($SQLResult))
		{
			for($iCounter = 0; $iCounter < sizeof($SQLResult); $iCounter++)
			{
				$PrevBillRegisterID = $SQLResult[$iCounter]['BillRegisterID'];
				$PrevBillBillDate = $SQLResult[$iCounter]['BillDate'];
				if($PrevBillBillDate == '')
				{
				echo "<script>alert('No Prev bill data. Cannot generate bills. Pl contact support')<//script>";
				die();
				}
				
				$PrevBillDueDate = $SQLResult[$iCounter]['DueDate'];
				if ($this->ShowDebugTrace == 1)
				{
					echo "<BR>PrevBillBillDate : " . $PrevBillBillDate;
					echo "<BR>PrevBillDueDate : " . $PrevBillDueDate;
				}
				$this->strTrace .=  '<tr><td>PrevBillBillDate : ' . $PrevBillBillDate . '</td></tr>';
				$this->strTrace .=  '<tr><td>PrevBillDueDate : ' . $PrevBillDueDate . '</td></tr>';
				$this->strTrace .=  '<tr><td>CurrentBillDate : ' . $sBillDate . '</td></tr>';
				
				$dateDiff = $this->m_objUtility->getDateDiff($PrevBillDueDate,$sBillDate);
				$this->strTrace .=  '<tr><td>dateDiff : ' . $dateDiff . '</td></tr>';	
				
				$PrevPrincipalDue = $PrevPrincipalDue + $SQLResult[$iCounter]['PrincipalArrears'];
				$PrevInterestDue =  $PrevInterestDue + $SQLResult[$iCounter]['InterestArrears'];;
		
				$PrevBillSubTotal = $PrevBillSubTotal  +  $SQLResult[$iCounter]['BillSubTotal'];;
				$PrevAdjustmentCredit = $PrevAdjustmentCredit + $SQLResult[$iCounter]['AdjustmentCredit'];;
				
				$PrevIGST = $PrevIGST + $SQLResult[$iCounter]['IGST'];
				$PrevCGST = $PrevCGST + $SQLResult[$iCounter]['CGST'];
				$PrevSGST = $PrevSGST + $SQLResult[$iCounter]['SGST'];
				$PrevCESS = $PrevCESS + $SQLResult[$iCounter]['CESS'];
				$PrevRounding = $PrevRounding = $SQLResult[$iCounter]['Ledger_round_off'];
				
				$PrevBillTax = $PrevBillTax + $SQLResult[$iCounter]['BillTax']+ $SQLResult[$iCounter]['IGST']+ $SQLResult[$iCounter]['CGST'] +$SQLResult[$iCounter]['SGST'] + $SQLResult[$iCounter]['CESS'];
				
				$PrevBillPrincipalAmount = $PrevBillSubTotal + $PrevAdjustmentCredit + $PrevIGST + $PrevCGST + $PrevSGST + $PrevCESS+$PrevRounding;// + $PrevBillTax;
				
				$PrevBillPrincipal_ = $PrevBillPrincipalAmount;
				$PrevBillInterestAmount =  $PrevBillInterestAmount + $SQLResult[$iCounter]['BillInterest'];
				
				if($dateDiff  > 0)
				{
					$this->strTrace .=  '<tr><td> PrevBillDueDate greater than CurrentBillDate Hence interest not calculated.</td></tr>';	
				}
			}
		}
		else
		{
			$PrevBillBillDate = $sBillDate;
			echo '<br/>No Query Data. May be a first period: <br><br>';
			$this->strTrace .=  '<tr><td>No prev bills found for BillType =' . $this->IsSupplementaryBill(). ' Previous period ' . $PrevPeriodID. '.</td></tr>';				
			//return;
		}
		//=====================================
		
		$PrevPrincipalDue_ = $PrevPrincipalDue ;
		$PrevInterestDue_ = $PrevInterestDue;
		$PrevBillPrincipal_ = $PrevBillPrincipalAmount;
		$PrevBillInterest_ = $PrevBillInterestAmount;
		$this->strTrace .=  '<tr><td>PrevPrincipalDue: ' . $PrevPrincipalDue . '</td></tr>';
		$this->strTrace .=  '<tr><td>PrevInterestDue: ' . $PrevInterestDue . '</td></tr>';
		$this->strTrace .=  '<tr><td>PrevBillPrincipalAmount: ' . $PrevBillPrincipalAmount . '</td></tr>';
		$this->strTrace .=  '<tr><td>PrevBillInterestAmount: ' . $PrevBillInterestAmount . '</td></tr>';

		//We need to use LIFO : So payment is applied in this order
		//1. Interest 
		//2. Prev bill amount
		//3. Principal arrears
		
//		$PrevPrincipalDue = $PrevPrincipalDue + $PrevBillPrincipalAmount;
		$PrevInterestDue = $PrevInterestDue + $PrevBillInterestAmount;
		if($PrevInterestDue < 0)
		{
			$this->strTrace .=  '<tr><td>PrevInterestDue is ' . $PrevInterestDue . ' moved to PrevPrincipal' . $PrevPeriodID. '.</td></tr>';	
			//echo '<BR>PrevInterestDue is ' . $PrevInterestDue . ' is credit, so moved to PrevPrincipal ' . $PrevPrincipalDue;
			$PrevPrincipalDue = $PrevPrincipalDue + $PrevInterestDue;
			$PrevInterestDue = 0;
			
		}
		$PaidPrincipal = 0;
		$PaidInterest = 0;
//===========

		if ($this->ShowDebugTrace == 1)
		{
			/*echo '<br/>InterestRate : ' . $InterestRate;
			echo '<br/>InterestMethod : ' . $InterestMethod;
			echo '<br/>InterestTrigger : ' . $InterestTrigger;
			echo '<br/>RebateMethod : ' . $RebateMethod;
			echo '<br/>RebateAmount : ' . $RebateAmount;
			echo '<br/>';*/
		}
//==============

		//echo '<BR> Before GetReversalCredits :<BR>';
		$AdditionalBillingHeads = $this->GetReversalCredits($UnitID, $PeriodID, $PrevPeriodBeginingDate, $PrevPeriodEndingDate);
		
		$this->strTrace .=  '<tr><td>AdditionalBillingHeads :' . $AdditionalBillingHeads . '</td></tr>';

		if ($this->ShowDebugTrace == 1)
		{
				$ExtraHeadCount = count($AdditionalBillingHeads) ;
				//echo "<BR> AdditionalBillingHeads : " . $ExtraHeadCount . " <BR>";
				//var_dump($AdditionalBillingHeads);
		}
		$BillMaster = $this->GetBillMaster($UnitID, $PeriodID);

		if ($this->ShowDebugTrace == 1)
		{
			//var_dump($BillMaster);
		}
		$TaxableLedgerTotal_No_Threshold = 0;
		$InterestOnArrearsReversalCharge = 0;
		$TaxableLedgerTotal = 0;
		$BillSubTotal = $this->GetBillSubTotal_Plain($UnitID, $PeriodID, $BillMaster, $AdditionalBillingHeads, $TaxableLedgerTotal, $TaxableLedgerTotal_No_Threshold, $InterestOnArrearsReversalCharge, $BillNote);
		
		if ($this->ShowDebugTrace == 1)
		{
			echo '<br/>BillSubTotal including additional heads: ' . $BillSubTotal;
			echo '<br/>InterestOnArrearsReversalCharge : ' . $InterestOnArrearsReversalCharge;
			echo '<br/>BillNote : ' . $BillNote . "<BR>";
		}
		$this->strTrace .=  '<tr><td>BillSubTotal including additional heads: ' . $BillSubTotal . '</td></tr>';
		$this->strTrace .=  '<tr><td>InterestOnArrearsReversalCharge: ' . $InterestOnArrearsReversalCharge . '</td></tr>';

//		$PrevBillBillDate = $this->PrevBillBillDate($PrevBillRegisterID);
		$this->strTrace .=  '<tr><td>PrevBillBillDate : ' . $PrevBillBillDate . '</td></tr>';
			
		$sBillDate = $this->m_objUtility->GetDateByOffset2($sBillDate, -1);
		$this->strTrace .=  '<tr><td>sCurrentBillDate -1 : ' . $sBillDate . '</td></tr>';

		//******************************
		//*** Processing debit notes ***
		//******************************
		$DebitNoteTotal = $this->GetDebitNoteTotal($UnitID, $PrevBillBillDate, $sBillDate);
		$this->strTrace .=  '<tr><td>Debit note total : ' . $DebitNoteTotal . '</td></tr>';
		if ($this->ShowDebugTrace == 1)
		{
			echo '<br/>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount;
			echo "<BR>Debit note total " . $DebitNoteTotal;
		}
		$PrevBillPrincipalAmount = $PrevBillPrincipalAmount + $DebitNoteTotal;
		if ($DebitNoteTotal <> 0 and $this->ShowDebugTrace == 1)
		{
			echo '<br/>Debit note total : ' . $DebitNoteTotal;
			echo '<br/>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount;
		}
		//======================
		//==== Processing prev bill Credit
			$CreditAmount = 0;
			//$PrevPrincipalDue = -100000; //pending cleanup
			if($PrevPrincipalDue < 0)
			{
				$CreditAmount = $PrevPrincipalDue * -1;
				$PrevPrincipalDue = 0;
				
				$this->strTrace .=  '<tr><td>There is a credit of : ' . $CreditAmount . '</td></tr>';
				$this->strTrace .=  '<tr><td>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount . '</td></tr>';
	
				if ($this->ShowDebugTrace == 1)
				{
					echo '<br/><br/>There is a credit of : ' . $CreditAmount;
					echo '<br/>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount;
				}
				if($CreditAmount  > $PrevBillPrincipalAmount)
				{			
					$CreditAmount  = $CreditAmount  - $PrevBillPrincipalAmount;
					$PaidPrincipal = $PrevBillPrincipalAmount;
					$PrevBillPrincipalAmount = 0;
					if ($this->ShowDebugTrace == 1)
					{
						echo '<br/><br/>Processed credit of : ' . $CreditAmount;
						echo '<br/>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount;
					}
					
				}
				else
				{
					$PrevBillPrincipalAmount = $PrevBillPrincipalAmount - $CreditAmount ;
					$PaidPrincipal = $CreditAmount ;
					$CreditAmount  = 0;	
				}						
				//echo '<br/>There is a credit of : ' . $CreditAmount;
				$this->strTrace .=  '<tr><td>Processed credit of : ' . $CreditAmount . '</td></tr>';
				//echo '<br/>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount;
				$this->strTrace .=  '<tr><td>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount . '</td></tr>';
				//echo '<br/>PrevPrincipalDue : ' . $PrevPrincipalDue;
				$this->strTrace .=  '<tr><td>PrevPrincipalDue : ' . $PrevPrincipalDue . '</td></tr>';
				//echo '<br/>';
			}
			if ($this->ShowDebugTrace == 1)
			{
				echo '<br/>There is a credit of : ' . $CreditAmount;
				echo '<br/>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount;
				echo '<br/>PrevPrincipalDue : ' . $PrevPrincipalDue;
			}

			//Apply received amount to calculate
			$ProcessPrincipalFirst = $societyInfo['apply_receipt_to_principal_first'];
			//echo '<br/>****ProcessPrincipalFirst : ' . $ProcessPrincipalFirst ;
			$this->strTrace .=  '<tr><td>**ProcessPrincipalFirst : ' . $ProcessPrincipalFirst . '</td></tr>';

	
	//		echo '<br/>PrevPeriodBeginingDate : ' . $PrevPeriodBeginingDate;
		    $sqlCheck = "select * from chequeentrydetails where voucherdate >= '". $PrevBillBillDate . "' AND voucherdate <= '" . $sBillDate . "' AND PaidBy = " . $UnitID." AND BillType  = ". $this->IsSupplementaryBill() . " ORDER BY voucherdate ASC";
			//echo '<br/>SqlCheck inline : ' . $sqlCheck;
			//$this->strTrace .=  '<tr><td>SqlCheck inline : ' . $sqlCheck . '</td></tr>';
			$resultCheck = $this->m_dbConn->select($sqlCheck);
			
			
			if ($this->ShowDebugTrace == 1)
			{
				echo '<br/>PrevBillBillDate : ' . $PrevBillBillDate;
				echo '<br/>sCurrentBillDate -1 : ' . $sBillDate;
				echo "<BR>SqlCheckEntryDetails : " . $sqlCheck . "<BR>";
				$sqlCheck = "";
				print_r($resultCheck );
			}

			//Checking CREDIT Note to apply to prev bill amount
			
			//$creditNoteSql = "SELECT * FROM reversal_credits WHERE  UnitID = ". $UnitID ." AND ChargeType = ".CREDIT_NOTE." AND PeriodID = '".$PeriodID."' AND BillType  = ". $this->IsSupplementaryBill();
			$creditNoteSql = "SELECT * FROM credit_debit_note WHERE date >= '". $PrevBillBillDate . "' AND date <= '" . $sBillDate . "' AND UnitID = ". $UnitID ." AND Note_Type = '". CREDIT_NOTE . "'  AND BillType  = ". $this->IsSupplementaryBill();

			
			$CreditNoteResult = $this->m_dbConn->select($creditNoteSql);
			
			//echo '<br>********************** Credit Note *****************************<br>';
			if ($this->ShowDebugTrace == 1)
			{
				echo '<BR><br/>Processing Credit Notes: ';
				echo '<br/>creditNoteSql : ' . $creditNoteSql;
				var_dump($CreditNoteResult);
			}
			//Array push work when variable is array so defining here $resultcheck as array
			if(sizeof($resultCheck) == 0)
			{
				$resultCheck = array();
			}
	
			for($i = 0 ; $i < sizeof($CreditNoteResult); $i++)
			{
				$result = array();
				$result[0]['ID'] = $CreditNoteResult[$i]['ID'];
				$result[0]['VoucherDate'] = $CreditNoteResult[$i]['Date'];
				$result[0]['ChequeDate'] = $CreditNoteResult[$i]['Date'];
				$result[0]['ChequeNumber'] = 'CN-'.$CreditNoteResult[$i]['ID'];
				$result[0]['Amount'] = $CreditNoteResult[$i]['TotalPayable'];
				$result[0]['PaidBy'] = $CreditNoteResult[$i]['UnitID'];
				$result[0]['BankID'] = "";
				$result[0]['Timestamp'] = $CreditNoteResult[$i]['CreatedTimestamp'];
				$result[0]['EnteredBy'] = $CreditNoteResult[$i]['CreatedBy_LoginID'];
				//It is credit type because we are giving credit here
				$result[0]['TransactionType'] = CREDIT_NOTE;
				$result[0]['PayerBank'] = 0;
				$result[0]['PayerChequeBranch'] =0;
				//In reversal_credits table status by default is set to 1 we not updating and inserting any data for status so i keep it as Y  
				$result[0]['status'] = 'Y';
				$result[0]['DepositID'] = "-3";
				$result[0]['Comments'] = $CreditNoteResult[$i]['Note'];
				$result[0]['IsReturn'] = 0;
				$result[0]['isEnteredByMember'] = 0;
				$result[0]['BillType'] = $CreditNoteResult[$i]['BillType'];
				array_push($resultCheck,$result[0]);
			}			
			
			$PaymentDate = 0;
			$PaymentReceived = 0;
			$PaymentReceived_ = 0;
			$iTotalPayments = 0;
			
			if($resultCheck <> '')
			{
				foreach($resultCheck as $k => $v)
				{
					$PaymentDate = 0;
					$PaymentReceived = 0;
					$iTotalPayments = $iTotalPayments + 1;
					//echo '<br/><br/>///============ Payment : ' . $iTotalPayments;
					$this->strTrace .=  '<tr><td>///============ Payment : ' . $iTotalPayments . '</td></tr>';
					if($resultCheck[$k]['Amount'] <> 0)
					{
						$PaymentDate  = $resultCheck[$k]['VoucherDate'];					
						$ChequeDate = $resultCheck[$k]['ChequeDate'];
						$ChequeNumber = $resultCheck[$k]['ChequeNumber'];
						$PaymentReceived = $resultCheck[$k]['Amount'];
						$TDSAmount = $resultCheck[$k]['TDS_Amount'];
						$PayerBank = $resultCheck[$k]['PayerBank'];
						$IsReturn = $resultCheck[$k]['IsReturn'];
						$TransactionType = $resultCheck[$k]['TransactionType'];
	//					echo '<br/><br/>IsReturn : ' . $IsReturn;
						if ($this->ShowDebugTrace == 1)
						{
							
							if($TransactionType == CREDIT_NOTE)
							{
								echo '<br/>Processing Credit Note No';									
							}
							else
							{
								//$PaymentReceived = 300;
								echo '<br/>Processing Cheque No';
							}							
							echo ' : <' . $ChequeNumber . '> dated-- <' . $ChequeDate . '> for amount <' . $PaymentReceived . '>';
						}
						if ($IsReturn == 1)
						{
							if ($this->ShowDebugTrace == 1)
							{
								//echo '<br/>Cheque No: <' . $ChequeNumber . '> dated <' . $ChequeDate . '> for amount <' . $PaymentReceived . '> would not be processed as it was returned';
							}
							$this->strTrace .=  '<tr><td>Cheque No: <' . $ChequeNumber . '> dated <' . $ChequeDate . '> for amount <' . $PaymentReceived . '> would not be processed as it was returned</td></tr>';
						}
						else
						{
		
							//echo '<br/><br/>ChequeDate : ' . $ChequeDate;
							$this->strTrace .=  '<tr><td>ChequeDate : ' . $ChequeDate . '</td></tr>';
							//echo '<br/><br/>ChequeNumber : ' . $ChequeNumber;
							$this->strTrace .=  '<tr><td>ChequeNumber : ' . $ChequeNumber . '</td></tr>';
							//echo '<br/><br/>PayerBank : ' . $PayerBank;
							$this->strTrace .=  '<tr><td>PayerBank : ' . $PayerBank . '</td></tr>';
							//echo '<br/><br/>Amount : ' . $PaymentReceived;
							$this->strTrace .=  '<tr><td>Amount : ' . $PaymentReceived . '</td></tr>';
							$this->strTrace .=  '<tr><td>TDS Amount : ' . $TDSAmount . '</td></tr>';
		
							//echo '<br/><br/>PrevPrincipalDue : ' . $PrevPrincipalDue;
							$this->strTrace .=  '<tr><td>PrevPrincipalDue : ' . $PrevPrincipalDue . '</td></tr>';
							//echo '<br/>TotalPrevInterestDue : ' . $PrevInterestDue;
							$this->strTrace .=  '<tr><td>TotalPrevInterestDue : ' . $PrevInterestDue . '</td></tr>';
							//echo '<br/>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount;
							$this->strTrace .=  '<tr><td>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount . '</td></tr>';
							//echo '<br/>PrevBillInterestAmount : ' . $PrevBillInterestAmount;
							$PaymentReceived = $PaymentReceived + $TDSAmount;
							$PaymentReceived_ = $PaymentReceived_ + $PaymentReceived;
							//echo '<br/><br/>PaymentReceived : ' . $PaymentReceived;
							$this->strTrace .=  '<tr><td>EffectivePaymentReceived : ' . $PaymentReceived . '</td></tr>';
							//echo '<br/>PaymentDate : ' . $PaymentDate;
							$this->strTrace .=  '<tr><td>PaymentDate : ' . $PaymentDate . '</td></tr>';
					
							if ($this->ShowDebugTrace == 1)
							{
								echo '<br/>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount;
								echo '<br/>PrevPrincipalDue : ' . $PrevPrincipalDue ;
								echo '<br/>TotalPrevInterestDue  : ' . $PrevInterestDue ;
								if($TransactionType == CREDIT_NOTE)
								{
									echo '<br/>Credit Note  : ' . $PaymentReceived ;									
								}
								else
								{
									//$PaymentReceived = 300;
									echo '<br/>PaymentReceived  : ' . $PaymentReceived ;
								}
							}
							//Apply received amount to calculate
					
							//Update prev Principal amount and Prev Interest (this will be updated in current bill record
							if($PaymentReceived > 0)
							{
								if($ProcessPrincipalFirst==0)
								{
									//Process Interest first
									$this->ProcessBillInterest($PrevInterestDue, $PaidInterest, $PaymentReceived);								
								}

								//Process Previous Principal arrears
							if ($this->ShowDebugTrace == 1)
							{
								echo '<br/>Processing Previous Principal arrears : ' . $PrevPrincipalDue;
							}
								$this->ProcessPreviousPrincipal($PrevPrincipalDue, $PaymentReceived, $CurrentBillInterestAmount, $PaidPrincipal, $societyInfo, $TransactionType, $PaymentDate, $PrevPeriodBeginingDate, $PrevPeriodEndingDate, $PrevBillDueDate);

								//Process PrevBill Principal (subtotal+Tax+GST)first as we follow LIFO						
							if ($this->ShowDebugTrace == 1)
							{
								echo '<br/>Processing Previous PrevBill Principal arrears : ' . $PrevBillPrincipalAmount;
							}
								$this->ProcessPreviousBillPrincipal($PrevBillPrincipalAmount,$PaymentReceived, $CurrentBillInterestAmount, $PaidPrincipal, $societyInfo, $TransactionType, $PaymentDate, $PrevPeriodEndingDate, $PrevBillDueDate, $PrevPeriodBeginingDate);

								//Now process PrevPrincipal Arrears																		
								if($ProcessPrincipalFirst==1)
								{
									//Process Interest last
									$this->ProcessBillInterest($PrevInterestDue, $PaidInterest, $PaymentReceived);																								
								}
							}
				
							//if payment is more than all obligations. Apply it as credit
							//echo '<br/><br/>PaymentReceived: ' . $PaymentReceived;
							$this->strTrace .=  '<tr><td>PaymentReceived: ' . $PaymentReceived . '</td></tr>';
							//echo '<br/>Credit Amount: ' . $CreditAmount;
							$this->strTrace .=  '<tr><td>Credit Amount: ' . $CreditAmount . '</td></tr>';
							if($PaymentReceived > 0)
							{
								//echo '<br/>Processing credit... ';
								$this->strTrace .=  '<tr><td>Processing credit... </td></tr>';
								$CreditAmount = $CreditAmount + $PaymentReceived;
								//echo '<br/>Credit Amount: ' . $CreditAmount;
								$this->strTrace .=  '<tr><td>Credit Amount: ' . $CreditAmount . '</td></tr>';
							}										
							//echo '<br/>';
						}
					}
					else
					{
						//echo '<br/>No amount to process' ;				
						$this->strTrace .=  '<tr><td>No amount to process . </td></tr>';
					}				
				}//for each
			}//if($resultCheck <> '')//End of cheque entry  processing
			
			if ($this->ShowDebugTrace == 1)
			{
					echo '<br/>Processed credits: ' . $iTotalPayments;
			}
			$this->strTrace .=  '<tr><td>Processed credits: ' . $iTotalPayments . '</td></tr><tr></tr></br>---------------------------<tr></br></tr>';
			//echo '<br/><br/>';
	
			//If Principal is due after processing all payments made in last period
			$CurrentPeriod_CreditAmount  = 0;
		
			if($BillSubTotal < 0)
			{
				$CurrentPeriod_CreditAmount = $BillSubTotal * -1;
			}
			
			if($PrevBillPrincipalAmount < 0)
			{
				$PrevPrincipalDue = $PrevPrincipalDue + $PrevBillPrincipalAmount;				
				if ($this->ShowDebugTrace == 1)
				{				
					//echo "<BR>Credit of PrevBillPrincipalAmount " . $PrevBillPrincipalAmount . " added to PrevPrincipalDue" . $PrevPrincipalDue;
				}
				$PrevBillPrincipalAmount = 0;
			}
			
			if ($PrevPrincipalDue > 0 && $dateDiff < 0)
			{
				if ($this->ShowDebugTrace == 1)
				{				
					//echo "<BR>PrevPrincipalDue" . $PrevPrincipalDue;
				}
				if($CurrentPeriod_CreditAmount > 0 && $CurrentPeriod_CreditAmount > $PrevPrincipalDue)
				{
					if ($this->ShowDebugTrace == 1)
					{				
						//echo '<br/>22CurrentPerid Credit Amount ' . $CurrentPeriod_CreditAmount . ' is more than PrevPrincipalDue ' . $PrevPrincipalDue . ' so interest not calculated on PrevPrincipalDue' ;
					}
					$this->strTrace .=  '<tr><td>CurrentPerid Credit Amount ' . $CurrentPeriod_CreditAmount . ' is more than PrevPrincipalDue ' . $PrevPrincipalDue . ' so interest not calculated on PrevPrincipalDue </td></tr>';
					$CurrentPeriod_CreditAmount = $CurrentPeriod_CreditAmount - $PrevPrincipalDue;
				}
				else
				{			
					$InterestableAmount = $PrevPrincipalDue;
					if($CurrentPeriod_CreditAmount > 0 && $CurrentPeriod_CreditAmount < $PrevPrincipalDue)
					{
						$InterestableAmount  =  $PrevPrincipalDue - $CurrentPeriod_CreditAmount;
						$this->strTrace .=  '<tr><td>PrevPrincipalDue reduced by Subtotal credit :' . $CurrentPeriod_CreditAmount . '</td></tr>';
						if ($this->ShowDebugTrace == 1)
						{				
							//echo '<BR>PrevPrincipalDue reduced by Subtotal credit :' . $CurrentPeriod_CreditAmount . '</BR>';
						}
					}
					$this->strTrace .=  '<tr><td>Calculate interest on unpaid PrevPrincipalDue :' . $InterestableAmount . '</td></tr>';
					//Calculate interest on remaining PrevPrincipalArrears for whole billing period, after all payments are applied
					
					//$intCalcBeginingDate = $this->GetDateByOffset($PrevPeriodBeginingDate, -1);
					//Calculate interest on unpaid principal arrears for full period including Begin and end date				
					if ($this->ShowDebugTrace == 1)
					{				
						echo "<BR>Test5";
						echo '<br/>Calculate interest on unpaid PrevPrincipalDue :' . $InterestableAmount ;				
					}
					$InterestAmount = $this->GetInterest($UnitID, $InterestableAmount, $PrevPeriodBeginingDate, $PrevPeriodEndingDate, $societyInfo, 1, $PrevPeriodBeginingDate);
					$CurrentBillInterestAmount = $CurrentBillInterestAmount + $InterestAmount;
					//echo '<br/><br/>CurrentBillInterestAmount : ' . $CurrentBillInterestAmount;
					$this->strTrace .=  '<tr><td>CurrentBillInterestAmount : ' . $CurrentBillInterestAmount . '</td></tr>';
				}
			}			
			
			if ($this->ShowDebugTrace == 1)
			{				
				echo "<BR>PrevBillPrincipalAmount" . $PrevBillPrincipalAmount;
				echo "<BR>dateDiff" . $dateDiff;
			}
			if ($PrevBillPrincipalAmount > 0 && $dateDiff < 0)
			{
				if($CurrentPeriod_CreditAmount > 0 && $CurrentPeriod_CreditAmount >= $PrevBillPrincipalAmount)
				{
					if ($this->ShowDebugTrace == 1)
					{				
						//echo '<br/>CurrentPerid Credit Amount ' . $CurrentPeriod_CreditAmount . ' is more than ' . $PrevBillPrincipalAmount . ' so interest not calculated on PrevBillPrincipalAmount' ;	
					}
					$this->strTrace .=  '<tr><td>CurrentPeriod Credit Amount ' . $CurrentPeriod_CreditAmount . ' is more than or equal ' . $PrevBillPrincipalAmount . ' so interest not calculated on PrevBillPrincipalAmount </td></tr>';
					$CurrentPeriod_CreditAmount = $CurrentPeriod_CreditAmount - $PrevBillPrincipalAmount;
				}
				else
				{
					$InterestableAmount = $PrevBillPrincipalAmount;
					if($CurrentPeriod_CreditAmount > 0 && $CurrentPeriod_CreditAmount < $PrevBillPrincipalAmount)
					{
						$InterestableAmount  =  $PrevBillPrincipalAmount - $CurrentPeriod_CreditAmount;
						$this->strTrace .=  '<tr><td>PrevBillPrincipalAmount reduced by Subtotal credit :' . $CurrentPeriod_CreditAmount . '</td></tr>';
						
					}
					if ($this->ShowDebugTrace == 1)
					{				
						echo '<br/>Calculate interest on unpaid PrevBillPrincipalAmount :' . $InterestableAmount ;				
					}
					$this->strTrace .=  '<tr><td>Calculate interest on unpaid PrevBillPrincipalAmount of PrevBillPrincipalAmount :' . $InterestableAmount . '</td></tr>';
					//Calculate interest on remaining Bill Principal Arrears from Dues Date to end of period after all payments are applied
					//$InterestAmount = $this->GetInterest($UnitID, $InterestableAmount, $PrevPeriodBeginingDate, $PrevPeriodEndingDate, $societyInfo);
					//interest is charged to PrevBillPrincipalAmount after the due date
					if($_SESSION['society_id'] == 247 && $societyInfo['bill_cycle'] == '6' && ($PrevBillDueDate > $PrevPeriodEndingDate))   //For Veena Dalvai : Pending. need to find better solution
					{
						$PrevPeriodEndingDate = date('Y-m-d', strtotime($PrevBillDueDate. ' + 1 days'));
						$InterestAmount = $this->GetInterest($UnitID, $InterestableAmount, $PrevBillDueDate, $PrevPeriodEndingDate, $societyInfo, 1, $PrevPeriodBeginingDate);						
					}
					else
					{
						if ($this->ShowDebugTrace == 1)
						{				
							echo "<BR>Test4";
							echo '<br/>Calculate interest on unpaid PrevBillAmount after due date:' . $InterestableAmount ;				
						}
						//Calculate interest on remaining prev bill amount after the Due date **test

						if($_SESSION['society_id'] == 231 && ($PrevBillDueDate > $PrevPeriodEndingDate)){  // Miraaj society

							$PrevBillDueDate = $PrevPeriodEndingDate;
						} 

						$InterestAmount = $this->GetInterest($UnitID, $InterestableAmount, $PrevBillDueDate, $PrevPeriodEndingDate, $societyInfo, 1, $PrevPeriodBeginingDate);
					}
					//Unpaid prev bill amount
					if($this->m_DontChargeIntToLastBillAmt == 1)
					{
						$this->strTrace .=  '<tr><td>Waived Interest : ' . $InterestAmount. ' of prev bill amount waived on amount ' . $InterestableAmount . '</td></tr>';
						$InterestAmount = 0;
						if ($this->ShowDebugTrace == 1)
						{				
							echo '<BR>Waived Interest : ' . $InterestAmount. ' on prev bill amount ' . $InterestableAmount;						
						}
					}
					//$InterestAmount = $this->GetInterest($UnitID, $PrevBillPrincipalAmount, $PrevBillDueDate, $sBillDate, $BillCalcType, $InterestRate, $InterestMethod, $InterestTrigger, $RebateMethod, $RebateAmount);
					$CurrentBillInterestAmount = $CurrentBillInterestAmount + $InterestAmount;
					//echo '<br/>CurrentBillInterestAmount : ' . $CurrentBillInterestAmount;
					$this->strTrace .=  '<tr><td>CurrentBillInterestAmount : ' . $CurrentBillInterestAmount . '</td></tr>';
				}
			}

			$RebateMethod = $societyInfo['rebate_method'];			
			$RebateAmount = $societyInfo['rebate'];			
			if ($this->ShowDebugTrace == 1)
			{
				echo '<br/><br/>Total CurrentBillInterestAmount : ' . $CurrentBillInterestAmount;
				if($RebateMethod > 0)
				{
					echo '<br/><br/>RebateMethod : ' . $RebateMethod;
					echo '<br/>RebateAmount : ' . $RebateAmount;
				}
			}
			$this->strTrace .=  '<tr><td>Total CurrentBillInterestAmount : ' . $CurrentBillInterestAmount . '</td></tr>';
			if($RebateMethod == REBATE_METHOD_NONE)	//No rebate
			{
			}
			else if($RebateMethod == REBATE_METHOD_FLAT)	//Flat amount
			{
				//Give flat rebate in interest to all people upto RebateAmount
				//echo '<br/>Rebate Method : Flat';
				$this->strTrace .=  '<tr><td>Rebate Method : Flat</td></tr>';
				//echo '<br/>Rebate Amount : '. $RebateAmount;
				$this->strTrace .=  '<tr><td>Rebate Amount : '. $RebateAmount . '</td></tr>';
				if($CurrentBillInterestAmount > $RebateAmount)
				{	
					
					//echo '<br/>InterestAmount Before rebate : ' . $CurrentBillInterestAmount;
					$this->strTrace .=  '<tr><td>InterestAmount Before rebate : ' . $CurrentBillInterestAmount . '</td></tr>';
					//echo '<br/>RebateAmount : ' . $RebateAmount;
					$this->strTrace .=  '<tr><td>RebateAmount : ' . $RebateAmount . '</td></tr>';
						$CurrentBillInterestAmount = $CurrentBillInterestAmount - $RebateAmount;
					//echo '<br/>InterestAmount after rebate: ' . $CurrentBillInterestAmount;
					$this->strTrace .=  '<tr><td>InterestAmount after rebate: ' . $CurrentBillInterestAmount . '</td></tr>';
				}
				else
				{
					$CurrentBillInterestAmount = 0;
				}
			}
			else if($RebateMethod == REBATE_METHOD_WAIVE)	//Waive if upto
			{
				//echo '<br/>Rebate Method : Flat';
//				$this->strTrace .=  '<tr><td>Rebate Method : Flat</td></tr>';
				//echo '<br/>Rebate Amount : '. $RebateAmount;
//				$this->strTrace .=  '<tr><td>Rebate Amount : '. $RebateAmount . '</td></tr>';
				if($CurrentBillInterestAmount <= $RebateAmount)
				{
					//eg if interest is less than Rs25, dont charge interest.
					if ($this->ShowDebugTrace == 1)
					{
						echo '<br/>InterestAmount Before rebate : ' . $CurrentBillInterestAmount;
					}
					$this->strTrace .=  '<tr><td>InterestAmount : ' . $CurrentBillInterestAmount . ' below rebate ' . $RebateAmount . ' removed and made 0</td></tr>';
					$CurrentBillInterestAmount = 0;
				//echo '<br/>InterestAmount after rebate: ' . $CurrentBillInterestAmount;
				//$this->strTrace .=  '<tr><td>InterestAmount after rebate: ' . $CurrentBillInterestAmount . '</td></tr>';
				}
			}
			else if($RebateMethod == REBATE_METHOD_WAIVE_MENTION_AMOUNT)	//Waive interest on dues above x amount
			{
				//echo '<br/>Rebate Method : Flat';
//				$this->strTrace .=  '<tr><td>Rebate Method : Flat</td></tr>';
				// echo '<br/>Rebate Amount : '. $RebateAmount;
//				$this->strTrace .=  '<tr><td>Rebate Amount : '. $RebateAmount . '</td></tr>';
				//$TotalArrears = $PrevPrincipalDue + $PrevInterestDue;
				if($InterestableAmount <= $RebateAmount)
				{
					if ($this->ShowDebugTrace == 1)
					{
						echo '<br/><br/>Total arrears Before rebate : ' . $InterestableAmount;
					}
					$this->strTrace .=  '<tr><td>TotalArrears : ' . $InterestableAmount . ' less than ArrearsRebateAmount ' . $RebateAmount . ' so interest would be removed and made 0</td></tr>';
					$CurrentBillInterestAmount = 0;
					if ($this->ShowDebugTrace == 1)
					{
						echo '<br/>InterestAmount after rebate: ' . $CurrentBillInterestAmount;
					}
					$this->strTrace .=  '<tr><td>InterestAmount after rebate: ' . $CurrentBillInterestAmount . '</td></tr>';
				}
			}
		
		$BillTax = 0;
		$IGST = 0;
		$CGST = 0;
		$SGST = 0;
		$CESS = 0;
		$BillDate = getDBFormatDate($this->m_dbConn->escapeString($_REQUEST['bill_date']));

		//If INTEREST_ON_PRINCIPLE_DUE is taxable, then reversal credit of Interest would be considered for tax refund
		$this->CalculateGST($UnitID, $Unit_taxable_no_threshold, $Main_BillDate, $TaxableLedgerTotal, $TaxableLedgerTotal_No_Threshold, $CurrentBillInterestAmount, $InterestOnArrearsReversalCharge, $societyInfo, $IGST, $CGST, $SGST,$CESS);
		

			if($InterestOnArrearsReversalCharge <> 0)
			{
				if ($this->ShowDebugTrace == 1)
				{
					echo '<br>CurrentBillInterestAmount  : ' . $CurrentBillInterestAmount ;
					echo '<br>InterestOnArrearsReversalCharge : ' . $InterestOnArrearsReversalCharge;
				}
				$this->strTrace .=  '<tr><td>InterestOnArrearsReversalCharge : ' . $InterestOnArrearsReversalCharge . '</td></tr>';
				$CurrentBillInterestAmount = $CurrentBillInterestAmount + $InterestOnArrearsReversalCharge;
				if ($this->ShowDebugTrace == 1)
				{
					echo '<br>Updated CurrentBillInterestAmount  : ' . $CurrentBillInterestAmount ;
				}
			}
	
			//echo '<br/><br/>Final CurrentBillInterestAmount : ' . $CurrentBillInterestAmount;
			$this->strTrace .=  '<tr><td>Final CurrentBillInterestAmount : ' . $CurrentBillInterestAmount . '</td></tr>';
			//echo '<br/>PaidPrincipal : ' . $PaidPrincipal;
			$this->strTrace .=  '<tr><td>PaidPrincipal : ' . $PaidPrincipal . '</td></tr>';
			//echo '<br/>PaidInterest : ' . $PaidInterest;
			$this->strTrace .=  '<tr><td>PaidInterest : ' . $PaidInterest . '</td></tr>';
			//echo '<br/>Payment Credit after processing Dues : ' . $CreditAmount;
			$this->strTrace .=  '<tr><td>Payment Credit after processing Dues : ' . $CreditAmount . '</td></tr>';
			//echo '<br/>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount;
			$this->strTrace .=  '<tr><td>PrevBillPrincipalAmount : ' . $PrevBillPrincipalAmount . '</td></tr>';
			//echo '<br/>PrevPrincipalDue : ' . $PrevPrincipalDue;
			$this->strTrace .=  '<tr><td>PrevPrincipalDue : ' . $PrevPrincipalDue . '</td></tr>';
			//echo '<br/>PrevInterestDue : ' . $PrevInterestDue;
			$this->strTrace .=  '<tr><td>PrevInterestDue : ' . $PrevInterestDue . '</td></tr>';
			
			$BillSubTotal = $this->getTwoDecimalPoints($BillSubTotal);
			$CurrentBillInterestAmount = $this->getTwoDecimalPoints($CurrentBillInterestAmount);
			$BillTax = $this->getTwoDecimalPoints($BillTax);
			$IGST = $this->getTwoDecimalPoints($IGST);
			$CGST = $this->getTwoDecimalPoints($CGST);
			$SGST = $this->getTwoDecimalPoints($SGST);
			$CESS = $this->getTwoDecimalPoints($CESS);
			$PrevPrincipalDue = $this->getTwoDecimalPoints($PrevPrincipalDue);
			$PrevInterestDue = $this->getTwoDecimalPoints($PrevInterestDue);
			
			
		$CurrentBillAmount = $BillSubTotal + $CurrentBillInterestAmount + $BillTax + $IGST + $CGST + $SGST + $CESS ;
		$this->strTrace .=  '<tr><td>CurrentBillAmount : ' . $CurrentBillAmount . '</td></tr>';
		
		if ($this->ShowDebugTrace == 1)
		{
			echo "<BR><BR>After rounding CurrentBillAmount : " . $CurrentBillAmount;
		}
		//echo "issuppl:".$this->IsSupplementaryBill();
		/*if(!$this->IsSupplementaryBill())
		{*/
		//Total = Current Bill Amount + Principal Due + InterestDue
		//echo '<br/>PrevPrincipalDue : ' . $PrevPrincipalDue;
		$PrevPrincipalDue = $PrevPrincipalDue + $PrevBillPrincipalAmount;
		//echo '<br/>PrevPrincipalDue : ' . $PrevPrincipalDue;
		$this->strTrace .=  '<tr><td>PrevPrincipalDue : ' . $PrevPrincipalDue . '</td></tr>';
		$PrevPrincipalDue = $PrevPrincipalDue - $CreditAmount;
		//echo '<br/>PrevPrincipalDue : ' . $PrevPrincipalDue;
		$this->strTrace .=  '<tr><td>PrevPrincipalDue : ' . $PrevPrincipalDue . '</td></tr>';

		$this->strTrace .=  '<tr><td>TotalBillPayable : ' . $TotalBillPayable . '</td></tr>';
		$PrevDues = $PrevPrincipalDue + $PrevInterestDue;

		if ($this->ShowDebugTrace == 1)
		{
			/*echo '<br/>New month BillSubTotal : ' . $BillSubTotal;
			//echo '<br/>ReversalCredits : ' . $ReversalCredits;
			//echo '<br/>BillTax : ' . $BillTax;*/
			echo '<br/>CurrentBillInterestAmount : ' . $CurrentBillInterestAmount;
			echo '<br/>CurrentBillAmount : ' . $CurrentBillAmount;
			echo '<br/>PrevPrincipalDue : ' . $PrevPrincipalDue;
			echo '<br/>PrevInterestDue : ' . $PrevInterestDue;
			echo '<br/>PrevDues : ' . $PrevDues;
			echo '<br/>TotalBillPayable : ' . $TotalBillPayable;
		}

		$LateDays = 0;
		$RoundOffAmt = 0;
		$TotalBillPayable = 0;
		$societyInfo = $this->m_objUtility->GetSocietyInformation($_SESSION['society_id']);
		
		if($societyInfo['IsRoundOffLedgerAmt'] == 1 && (strtotime($Main_BillDate) >= strtotime('2020-12-01')))
		{ // If in society table IsRoundOffLedgerAmt is 0 means calculate invoice total by old method 
			//echo "<br>Date is greater";
			//$CurrentBillAmount = $BillSubTotal + $BillTax + $IGST + $CGST + $SGST + $CESS + $CurrentBillInterestAmount ;
			//$Total = $BillSubTotal + $BillTax + $IGST + $CGST + $SGST + $CESS + $CurrentBillInterestAmount;
			
			//echo "<br>Total RoundOffAmt : ".$RoundOffAmt;
			
			$TotalBillPayable = $CurrentBillAmount + $PrevPrincipalDue + $PrevInterestDue;
			$Total = $TotalBillPayable;
			$TotalBillPayable = $this->m_objUtility->getRoundValue2($Total);
			//10.24
			//= .24 = 10.24 (total)-10 (currbilamt
			$RoundOffAmt = $TotalBillPayable - $Total; 
			$RoundOffAmt = $this->getTwoDecimalPoints($RoundOffAmt);
			$this->strTrace .=  '<tr><td>Total RoundOffAmt : : ' . $RoundOffAmt . '</td></tr>';
		}
		else //if($societyInfo['IsRoundOffLedgerAmt'] == 1)
		{
			$TotalBillPayable = $CurrentBillAmount + $PrevPrincipalDue + $PrevInterestDue;
			$PrevPrincipalDue_ = $this->m_objUtility->getRoundValue2($PrevPrincipalDue_);
			$PrevInterestDue_ = $this->m_objUtility->getRoundValue2($PrevInterestDue_);
			$PrevBillPrincipal_ = $this->m_objUtility->getRoundValue2($PrevBillPrincipal_);
			$PrevBillInterest_ = $this->m_objUtility->getRoundValue2($PrevBillInterest_);
			$BillSubTotal = $this->m_objUtility->getRoundValue2($BillSubTotal);
			$CurrentBillInterestAmount = $this->m_objUtility->getRoundValue2($CurrentBillInterestAmount);
			$CurrentBillAmount = $this->m_objUtility->getRoundValue2($CurrentBillAmount);
			$PrevPrincipalDue = $this->m_objUtility->getRoundValue2($PrevPrincipalDue);
			$PrevInterestDue = $this->m_objUtility->getRoundValue2($PrevInterestDue);
			$TotalBillPayable = $this->m_objUtility->getRoundValue2($TotalBillPayable);
		}

		$InsertQuery = "INSERT INTO `billdetails`(`UnitID`, `PeriodID`, `BillRegisterID`, `BillNumber`,
		`ModifiedFlag`, `PrevPrincipalArrears`, `PrevInterestArrears`, `PrevBillPrincipal`, 
		`PrevBillInterest`, `PaymentReceived`, `PaidPrincipal`, `PaidInterest`,
		`BillSubTotal`, `AdjustmentCredit`, `BillTax`,`IGST`,`CGST`,`SGST`,`CESS`,`BillInterest`, `LateDays`, 
		`CurrentBillAmount`,`PrincipalArrears`, `InterestArrears`, `Ledger_round_off`, `TotalBillPayable`,`Note`,`BillType`) VALUES ("
			. $UnitID.",".$PeriodID."," . $BillRegisterID . ", " . $BillNo . ", '0'," 
			. $PrevPrincipalDue_ .","
			. $PrevInterestDue_.","
			. $PrevBillPrincipal_ . ","
			. $PrevBillInterest_ .","
			. $PaymentReceived_.','.$PaidPrincipal.','.$PaidInterest.','
			. $BillSubTotal.",0,". $BillTax.",".$IGST.",".$CGST.",".$SGST.",".$CESS.","
			. $CurrentBillInterestAmount.",".$LateDays.','
			. $CurrentBillAmount.","
			. $PrevPrincipalDue.","
			. $PrevInterestDue.","
			.$RoundOffAmt.","
			. $TotalBillPayable.","
			. "'" .$this->m_dbConn->escapeString($BillNote). "',"
			. $this->IsSupplementaryBill().")";

			if ($this->ShowDebugTrace == 1)
			{
				echo '<br/>InsertQuery : ' . $InsertQuery;
			}
			//$this->strTrace .=  '<tr><td>InsertQuery : ' . $InsertQuery . '</td></tr>';
			$BillDetailID = $this->m_dbConn->insert($InsertQuery);
			if ($this->ShowDebugTrace == 1)
			{
				echo '<br/>BillDetailID : ' . $BillDetailID;
			}
			$this->strTrace .=  '<tr><td>BillDetailID : ' . $BillDetailID . '</td></tr>';
			//$this->strTrace  .=  'Bill Date : '.$_REQUEST['bill_date'];
			$this->SetVoucher($UnitID, $PeriodID, $BillDetailID, $Main_BillDate, $BillMaster, $AdditionalBillingHeads);			
			
			$this->UpdateAdjustmentWithBillDetails($UnitID, $PeriodID, $PrevPeriodBeginingDate, $PrevPeriodEndingDate, $BillDetailID);
			
			if ($this->ShowDebugTrace == 1)
			{
				echo '<br/>---------------------- end of billgen for UnitID : ' . $UnitID . '------------------------------';
			}

			// log all the details
			// log start
			$LedgerDetailsInBill = $this->getAllIncludesLedgersInBill($BillDetailID);
			$unitNo = $this->objFetchData->objMemeberDetails->sUnitNumber;
			$billName = $this->m_objUtility->returnBillTypeString($this->IsSupplementaryBill());

			$dataArr = array('Date'=>$Main_BillDate, 'Bill Type'=> $billName,  'Flat'=>$unitNo, 'Bill Number'=>$BillNo, 
			'Payment Received'=>$PaymentReceived_, 'Paid Principal'=>$PaidPrincipal, 'Paid Interest'=>$PaidInterest,
			'Bill Sub Total'=>$BillSubTotal, 'Bill Interest'=>$CurrentBillInterestAmount, 'Current Bill Amount'=>$CurrentBillAmount,'Principal Arrears'=>$PrevPrincipalDue, 'Interest Arrears'=>$PrevInterestDue, 'Total BillPayable'=>$TotalBillPayable, 'Ledgers'=>$LedgerDetailsInBill);

			$logArr = json_encode($dataArr);
			$previousLogID = 0;
			
			if(!empty($this->changeLogDeleteRefArr[$UnitID])){

				$previousLogID = $this->changeLogDeleteRefArr[$UnitID];
			}
			$this->m_objLog->setLog($logArr, $_SESSION['login_id'], TABLE_BILLREGISTER, $BillDetailID, ADD, $previousLogID);
			
			// log end

			return $BillDetailID;
	}

	public function getAllIncludesLedgersInBill($refNo, $voucherType = VOUCHER_SALES, $creditOrDebitColumn = 'Credit', $byOrToColumn = 'To'){

		if(!empty($refNo) && $refNo != 0){

			$qry = "SELECT l.ledger_name, $creditOrDebitColumn FROM voucher as v JOIN ledger as l ON v.$byOrToColumn = l.id  WHERE RefNO = '$refNo' AND VoucherTypeID = '".$voucherType."'";
			return $result = $this->m_dbConn->select($qry,1);
		}
	}

	public function getTwoDecimalPoints($Amt){
		
		return number_format((float)$Amt,2, '.','');
	}

	private function getRoundValue($amount)
	{
		$roundAmount = 0;
		//$roundAmount = round($amount * 2)/2; //(Eg. 1 to 1.24 = 1, 1.25 to 1.74 = 1.5, 1.75 to 1.99 = 2)
		$roundAmount = round($amount); //(Eg. 1 to 1.49 = 1, 1.50 to 1.99 = 2)
		return $roundAmount;
	}
	
	/*
	public function GetNumberOfMonthsDuesDate($DueDate, $PaymentDate)
	{
		//echo '<br/>DueDate : ' . $DueDate;
		$this->strTrace .=  '<tr><td>DueDate : ' . $DueDate . '</td></tr>';
		//echo '<br/>PaymentDate : ' . $PaymentDate;
		$this->strTrace .=  '<tr><td>PaymentDate : ' . $PaymentDate . '</td></tr>';
		
		$gmtTimezone = new DateTimeZone('GMT');
		$datetime1 = new DateTime($DueDate, $gmtTimezone);
		//$datetime1 = $datetime1->modify('-1 day');
		
		$datetime2 = new DateTime($PaymentDate, $gmtTimezone);
		$datetime2 = $datetime2->modify('+1 day');
		
		$interval = $datetime1->diff($datetime2);
		$diff_d = $interval->format('%d');
		//echo '<br/>diff in days: ' . $diff_d;
		$this->strTrace .=  '<tr><td>diff in days: ' . $diff_d . '</td></tr>';
		
		$diff_m = $interval->format('%m');
		//echo '<br/>diff in months: ' . $diff_m;
		$this->strTrace .=  '<tr><td>diff in months: ' . $diff_m . '</td></tr>';
		
		$diff_y = $interval->format('%y');
		//echo '<br/>diff in years: ' . $diff_y;
		$this->strTrace .=  '<tr><td>diff in years: ' . $diff_y . '</td></tr>';
		
		if($diff_y > 0)
		{
			$diff_m = ($diff_y * 12) + $diff_m;			
		}
		
		//if payment is made few days late but less than a month, charge one month interest
		if($diff_m <= 0)
		{
			if($diff_d > 0)
			{
				$diff_m = 1;
			}			
		}
		else if($diff_d > 0)
		{
			$diff_m =  $diff_m + 1;
		}
		
		return $diff_m;	
	}
	*/
	public function GetNumberOfDaysAfterDuesDate($DueDate, $PaymentDate)
	{
		//echo '<br/>DueDate : ' . $DueDate;
		$this->strTrace .=  '<tr><td>DueDate : ' . $DueDate . '</td></tr>';
		
		//echo '<br/>PaymentDate : ' . $PaymentDate;
		$this->strTrace .=  '<tr><td>PaymentDate : ' . $PaymentDate. '</td></tr>';
		
		$datetime1 = new DateTime($DueDate);
		//$datetime1 = $datetime1->modify('-1 day');
		$datetime2 = new DateTime($PaymentDate);
		$interval = $datetime1->diff($datetime2);
		$diff = $interval->format('%R%a days');
		//echo '<br/>diff : ' . $diff;
		$this->strTrace .=  '<tr><td>Day diff : ' . $diff . '</td></tr>';
		
		return $diff;	
	}
/*
	public function GetDateByOffset($myDate, $Offset)
	{
		//echo '<br/>myDate : ' . $myDate;
		$this->strTrace .=  '<tr><td>myDate : ' . $myDate. '</td></tr>';
		
		//echo '<br/>Offset : ' . $Offset;
		$this->strTrace .=  '<tr><td>Offset : ' . $Offset. '</td></tr>';
		$datetime1 = new DateTime($myDate);
		$newDate = $datetime1->modify($Offset . ' day');
//		echo '<br/>Offetdate : ' . $newDate->format('y-m-d');

		return $newDate->format('Y-m-d');	
	}
*/
	public function GetInterest($UnitID, $DueAmount, $DueDate, $PaymentDate, $SQLResult, $bIncludeBothDates = 0, $PrevPeriodBeginingDate = '')
	{
		$this->strTrace .=  '<tr><td>GetInterest()::CurrentBillInterestAmount : ' . $CurrentBillInterestAmount . '</td></tr>';		
		
		//var_dump($SQLResult);
		//Todo : Remove hard coding
		$BillCalcType = 1;	//LIFO
		$InterestRate = 18;		//default to 18%
		$InterestMethod = 2;	//default to monthly			
		$InterestTrigger = 0;			
		$RebateMethod = 0;			
		$RebateAmount = 0;			
		$BillCycle = 0;	

		if(!is_null($SQLResult))
		{
			//$BillCalcType = $SQLResult[0]['int_calc_type'];
			$InterestRate = $SQLResult['int_rate'];			
			$InterestMethod = $SQLResult['int_method'];			
			$InterestTrigger = $SQLResult['int_tri_amt'];			
			$RebateMethod = $SQLResult['rebate_method'];			
			$RebateAmount = $SQLResult['rebate'];			
			$BillCycle = $SQLResult['bill_cycle'];			
		}
		//$InterestRate = 0;
/*		if (!(($RebateMethod == 3) ||($RebateMethod == 2) || ($RebateMethod == 1)))
		{
			$strMsg = 'Unsupported RebateMethod ' . $RebateMethod . '. <BR>Only RebateMethod None(1) and Flat(2) is supported. Rebate would not be processing';
			echo "<script>alert('".$strMsg."');</script>";		
		}
		//echo '<br/>$InterestTrigger : ' . $InterestTrigger . ' not implemented';
		$this->strTrace .=  '<tr><td>$InterestTrigger : ' . $InterestTrigger . ' not implemented</td></tr>';
*/
		if ($this->ShowDebugTrace == 1)
		{
			echo '<br/>Calculating interest on amount : ' . $DueAmount;
			echo '<br/>DueDate : ' . $DueDate; 
			echo '<br/>PaymentDate : ' . $PaymentDate; 
			echo '<br/>InterestMethod : ' . $InterestMethod; 
		}
	
		//echo '<br/>PrevPrincipalDue : ' . $DueAmount;
		$this->strTrace .=  '<tr><td><BR>Calculating interest on amount : ' . $DueAmount. '</td></tr>';
		
	//	echo '<br/>DueDate : ' . $DueDate;
		$this->strTrace .=  '<tr><td>DueDate : ' . $DueDate. '</td></tr>';
		
		//echo '<br/>PaymentDate : ' . $PaymentDate;
		$this->strTrace .=  '<tr><td>PaymentDate : ' . $PaymentDate. '</td></tr>';
		
		//echo '<br/>BillCalcType : ' . $BillCalcType;
		$this->strTrace .=  '<tr><td>BillCalcType : ' . $BillCalcType . '</td></tr>';
		
		$InterestAmount = 0;
				
//		$BillSubTotal = -1;
			
		//echo '<br/>InterestRate : ' . $InterestRate;
		$this->strTrace .=  '<tr><td>InterestRate : ' . $InterestRate. '</td></tr>';
		
		//echo '<br/>InterestMethod : ' . $InterestMethod;
		$this->strTrace .=  '<tr><td>InterestMethod : ' . $InterestMethod . '</td></tr>';
		
		//echo '<br/>InterestTrigger : ' . $InterestTrigger;
		$this->strTrace .=  '<tr><td>InterestTrigger : ' . $InterestTrigger. '</td></tr>';
		
		if($DueAmount > $InterestTrigger)
		{
			if ($this->ShowDebugTrace == 1)
			{
				echo '<br/>DueAmount : ' . $DueAmount;
				echo '<br/>InterestRate : ' . $InterestRate;
			}
			
			if ($InterestMethod == INTEREST_METHOD_DELAY_DUE)	//DelayAfterDueDate
			{
				$this->strTrace .=  '<tr><td>InterestMethod : DelayAfterDueDate</td></tr>';
				
				$DelayDays = $this->GetNumberOfDaysAfterDuesDate($DueDate, $PaymentDate);
				
				if($bIncludeBothDates == 1 && $DelayDays > 0)
				{
					$DelayDays = $DelayDays + 1;
				}
				if ($this->ShowDebugTrace == 1)
				{
					echo "<BR>GetInterest bIncludeBothDates:" . $bIncludeBothDates;
					echo "<br>DelayDays : " . $DelayDays;
				}
				$this->strTrace .=  '<tr><td>DelayDays : ' . $DelayDays . '</td></tr>';
				
				$InterestAmount =  ($DueAmount * $InterestRate/100 ) * $DelayDays/365;					
			}
			else if ($InterestMethod == INTEREST_METHOD_DELAY_SINCE_BILLING_DAYS)	//DelaySinceBillingDate
			{
				$this->strTrace .=  '<tr><td>InterestMethod : DelaySinceBillingDate</td></tr>';
				
				$DelayDays = $this->GetNumberOfDaysAfterDuesDate($DueDate, $PaymentDate);

				if($DelayDays > 0){ // If payment date is after due date then charge from 1st day of billing period till payment date

					// get the begining date of billing Cycle
					
					if(!empty($PrevPeriodBeginingDate)){

						$DelayDays = $this->GetNumberOfDaysAfterDuesDate($PrevPeriodBeginingDate, $PaymentDate);

						$this->strTrace .=  '<tr><td> New Delay Days: ' . $DelayDays . '</td></tr>';
					}
				}
								
				if($bIncludeBothDates == 1 && $DelayDays > 0)
				{
					$DelayDays = $DelayDays + 1;
				}

				$this->strTrace .=  '<tr><td>GetInterest bIncludeBothDates:' . $bIncludeBothDates . '</td></tr>';
				if ($this->ShowDebugTrace == 1)
				{
					echo "<BR>GetInterest bIncludeBothDates:" . $bIncludeBothDates;
					
					echo "<br>DelayDays : " . $DelayDays;
				}
				

				$this->strTrace .=  '<tr><td>DelayDays : ' . $DelayDays . '</td></tr>';
				
				$InterestAmount =  ($DueAmount * $InterestRate/100 ) * $DelayDays/365;					
			}
			else if ($InterestMethod == INTEREST_METHOD_FULL_MONTH)	//Full Month
			{					
				//echo '<br/>InterestMethod : Monthly';
				$this->strTrace .=  '<tr><td>InterestMethod : Monthly</td></tr>';
				$InterestAmount =  ($DueAmount * $InterestRate/100 )/12;
		
				$DueDateElements = explode('-', $DueDate);
				$DueDate_month = $DueDateElements[1];
				$PaymentDateElements = explode('-', $PaymentDate);
				$PaymentDate_month = $PaymentDateElements[1];
				$NumberOfMonths = 0;
				if($DueDate_month == 12)
				{
					//If Bill date is Dec and Due date is Jan
					echo '<BR>Before DueDate_month : ' . $DueDate_month . ' and PaymentDate_month : ' . $PaymentDate_month;
					if($PaymentDate_month < $DueDate_month)
					{
						$PaymentDate_month = $PaymentDate_month + 12;
						if($PaymentDate_month > 15)
						{
							$PaymentDate_month = 15;
						}
						
					}
					echo '<BR>After DueDate_month : ' . $DueDate_month . ' and PaymentDate_month : ' . $PaymentDate_month;
					$this->strTrace .=  '<tr><td>After DueDate_month : ' . $DueDate_month . ' and PaymentDate_month : ' . $PaymentDate_month . '</td></tr>';
				}
				
				
				// If the Billing cycle is monthly 
				
				// If the Billing cycle is monthly 
				// 6 is Monthly Cycle
				if($_SESSION['society_id'] == 247 && $BillCycle == 6) //For Veena Dalvai
				{
					
					/*$datetime1 = new DateTime($PaymentDate);
					$datetime2 = new DateTime($DueDate);
					$interval = $datetime1->diff($datetime2);
					$interval->format('%m');
					*/
				
					
					$Datediff = $this->m_objUtility->getDateDiff($PaymentDate, $DueDate);
					
					if($Datediff > 0)
					{
						//then check payment date more than due date then no. month is 1 
						$NumberOfMonths = 1;
					}
				}
				else if($PaymentDate_month >= $DueDate_month)
				{
					$NumberOfMonths = $PaymentDate_month - $DueDate_month + 1;
					
				}
				$this->strTrace .=  '<tr><td>DueDate_month : ' . $DueDate_month . ' and PaymentDate_month : ' . $PaymentDate_month . ' and NumberOfMonths : ' . $NumberOfMonths . '</td></tr>';
				$this->strTrace .=  '<tr><td>Monthly Interest Amt : ' . $InterestAmount. '</td></tr>';
				$this->strTrace .=  '<tr><td>Interest due for months: ' . $NumberOfMonths.'</td></tr>';
				if ($this->ShowDebugTrace == 1)
				{
					echo '<br/>Monthly Interest Amount : ' . $InterestAmount;
					echo '<br/>Due Date : ' . $DueDate . ' PaymentDate : ' . $PaymentDate;
					echo '<br/>Interest due for months : ' . $NumberOfMonths;
				}
				
				$InterestAmount = $InterestAmount * $NumberOfMonths; //for those many months
			}
			else //if ($InterestMethod == 3)	//Full Cycle If monthly, full month int, if quaterly then 3mn interest 
			{					
				$this->strTrace .=  '<tr><td>InterestMethod : Full Cycle</td></tr>';
				$CycleWeightage = getBillingCycleWeightage($BillCycle); 
				$this->strTrace .=  '<tr><td>CycleWeightage : ' . $CycleWeightage. '</td></tr>';
				$InterestAmount =  ($DueAmount * $InterestRate/100 )/$CycleWeightage;
				if ($this->ShowDebugTrace == 1)
				{
					echo '<br/>BillCycle : ' . $BillCycle;
					echo '<br/>CycleWeightage : ' . $CycleWeightage;
					//echo '<br/>Formula : ($DueAmount * $InterestRate/100 )/$CycleWeightage';
				}
			}
		}

		if ($this->ShowDebugTrace == 1)
		{
			echo '<br/>Interest Amt : ' . $InterestAmount;
		}
		$this->strTrace .=  '<tr><td>InterestAmount : ' . $InterestAmount.'</td></tr>';
		return $InterestAmount;			
	}

	public function GetAdjustment ($UnitID, $PeriodID, $PrevPeriodBeginingDate, $PrevPeriodEndingDate)
	{
		//echo '<br/>PrevPeriodBeginingDate : ' . $PrevPeriodBeginingDate;
		//echo '<br/>PrevPeriodEndingDate : ' . $PrevPeriodEndingDate;
		$sqlAdj = "select * from reversal_credits where date >= '". $PrevPeriodBeginingDate . "' AND date <= '" . $PrevPeriodEndingDate . "' AND UnitID = " . $UnitID . " AND Status = 1 AND BillType ='" . $this->IsSupplementaryBill()."' " ;
		//echo '<br/>GetPaymentsReceived:sqlAdj : ' . $sqlAdj;
		$resultAdj = $this->m_dbConn->select($sqlAdj);
//		return $sqlAdj ;
		
		$AdjAmount = 0;
		if($resultAdj <> '')
		{
			foreach($resultAdj as $k => $v)
			{
				$PaymentDate = 0;
				$PaymentReceived = 0;
				$iTotalPayments = $iTotalPayments + 1;
				if($resultAdj[$k]['Amount'] <> 0)
				{
					$PaymentDate  = $resultAdj[$k]['Date'];					
					$Amount = $resultAdj[$k]['Amount'];
					$AdjAmount = $AdjAmount + $Amount;
					echo "<BR>Ledger " . $resultAdj[$k]['LedgerID'] . " Amount " . $Amount ;
				}
			}
		}
		return $AdjAmount;
	}
	

	public function GetReversalCredits($UnitID, $PeriodID, $PrevPeriodBeginingDate, $PrevPeriodEndingDate)
	{
		//$this->ShowDebugTrace = 1;
		$AdditionalBillingHeads = array();
				
//		$sqlAdj = "select rc.ID, rc.Date, rc.VoucherID, rc.UnitId, rc.Amount, rc.Comments, ledgertbl.Taxable, rc.LedgerID  from reversal_credits as rc JOIN ledger as ledgertbl ON rc.ledgerid = ledgertbl.id where rc.date >= '". $PrevPeriodBeginingDate . "' AND rc.date <= '" . $PrevPeriodEndingDate . "' AND rc.UnitID = " . $UnitID . " AND rc.BillType ='" . $this->IsSupplementaryBill()."' AND rc.STATUS = 1 " ;		
		$sqlAdj = "select rc.Amount as AccountHeadAmount, rc.Comments, ledgertbl.Taxable, ledgertbl.taxable_no_threshold, rc.LedgerID as AccountHeadID  from reversal_credits as rc JOIN ledger as ledgertbl ON rc.ledgerid = ledgertbl.id where rc.date >= '". $PrevPeriodBeginingDate . "' AND rc.date <= '" . $PrevPeriodEndingDate . "' AND rc.UnitID = " . $UnitID . " AND rc.BillType ='" . $this->IsSupplementaryBill()."' AND rc.STATUS = 1 " ;		
		
		$resultAdj = $this->m_dbConn->select($sqlAdj);
		return $resultAdj;
/*		
		$ReversalCredits = 0;
		if($resultAdj <> '')
		{
			if ($this->ShowDebugTrace == 1)
			{
				echo '<br/>Reversal_charge :CHECK : ' . $sqlAdj;
				print_r($resultAdj);
			}
			foreach($resultAdj as $k => $v)
			{
				$PaymentDate = 0;
				$PaymentReceived = 0;
				$iTotalPayments = $iTotalPayments + 1;
				if($resultAdj[$k]['Amount'] <> 0)
				{
					$LedgerID = $resultAdj[$k]['LedgerID'];
//					$PaymentDate  = $resultAdj[$k]['Date'];					
					$Amount = $resultAdj[$k]['Amount'];
					$Taxable = $resultAdj[$k]['Taxable'];
					$note =  $Amount . ":" . $resultAdj[$k]['Comments'];
					if ($this->ShowDebugTrace == 1)
					{
						echo "<BR>Comments " . $note . "<BR>";
					}
					if($BillNote == "")
					{
						$BillNote = $note;
						
					}
					else
					{
						$BillNote = $BillNote . ":: " . $note;
					}
					//echo "<BR>BillNote " . $BillNote . "<BR>";
					$ReversalCredits = $ReversalCredits + $Amount;
					$this->strTrace .=  '<tr><td>LedgerID: <' . $LedgerID . '> Amount <' . $Amount . '></td></tr>';

					$tempArray = array("AccountHeadID" => $LedgerID, "AccountHeadAmount" => $Amount, "Taxable" => $Taxable);							
												
					array_push($AdditionalBillingHeads, $tempArray);
				}
			}
		}		
		else
		{
			if ($this->ShowDebugTrace == 1)
			{
				echo "<BR>No AdditionalBillingHeads<BR>";
			}
		}
		if ($this->ShowDebugTrace == 1)
		{
			echo "<BR>AdditionalBillingHeads<BR>";
			print_r($AdditionalBillingHeads);
		}
		return $AdditionalBillingHeads;*/
	}
	
	
	public function UpdateAdjustmentWithBillDetails ($UnitID, $PeriodID, $PrevPeriodBeginingDate, $PrevPeriodEndingDate, $BillDetailID)
	{
		//echo '<br/>PrevPeriodBeginingDate : ' . $PrevPeriodBeginingDate;
		//echo '<br/>PrevPeriodEndingDate : ' . $PrevPeriodEndingDate;
		$sqlAdj = "UPDATE reversal_credits SET PeriodID = " . $PeriodID . " where date >= '". $PrevPeriodBeginingDate . "' AND date <= '" . $PrevPeriodEndingDate . "' AND UnitID = " . $UnitID . " AND Status = 1 AND BillType ='" . $this->IsSupplementaryBill()."' " ;
		if($this->ShowDebugTrace == 1)
		{
			//echo '<br/>Reversal_credit update :sqlAdj : ' . $sqlAdj;
		}
		$resultAdj = $this->m_dbConn->update($sqlAdj);
		return $resultAdj;
	}
		
	public function GetPaymentsReceived ($UnitID, $PeriodID, $PrevPeriodID, $BeginingDate, $EndingDate)
	{
	
		//echo '<br/>PrevPeriodBeginingDate : ' . $PrevPeriodBeginingDate;
		$sqlCheck = "select * from chequeentrydetails where voucherdate >= '". $BeginingDate . "' AND voucherdate <= '" . $EndingDate . "' AND PaidBy = " . $UnitID;
		//echo '<br/>GetPaymentsReceived:SqlCheck : ' . $sqlCheck;
		$resultCheck = $this->m_dbConn->select($sqlCheck);
		return $resultCheck ;
	}



	public function GetBillMaster($UnitID, $PeriodID)
	{
		if($this->ShowDebugTrace == 1)
		{
			//echo "<BR>Inside GetBillMaster <BR>";		
		}//print_r($AdditionalBillingHeads );
		
		$sqlPeriod = "Select BeginingDate, EndingDate from period where ID = '" . $PeriodID . "'";
		$resultPeriod = $this->m_dbConn->select($sqlPeriod);
		
		$beginDate = $resultPeriod[0]['BeginingDate'];
		$endDate = $resultPeriod[0]['EndingDate'];		
		
		/****************************************************************************
		//Get the Account Heads as per the period selected
		*****************************************************************************/
		
		if($this->IsSupplementaryBill() == 1)
		{
			$sqlFetch = "select master.UnitID, master.AccountHeadID, master.AccountHeadAmount, master.BeginPeriod, master.EndPeriod, master.BillType, ledgertbl.taxable, ledgertbl.taxable_no_threshold from unitbillmaster as master JOIN ledger as ledgertbl ON master.AccountHeadID = ledgertbl.id where master.UnitID = '" . $UnitID . "' and ledgertbl.supplementary_bill = '1' and master.AccountHeadID != '" . INTEREST_ON_PRINCIPLE_DUE . "' and master.BeginPeriod <= '" . $beginDate . "' and master.BillType='".$this->IsSupplementaryBill()."' ORDER BY UnitID, AccountHeadID, EndPeriod ASC";
		}
		else
		{
			$sqlFetch = "select master.UnitID, master.AccountHeadID, master.AccountHeadAmount, master.BeginPeriod, master.EndPeriod,master.BillType, ledgertbl.taxable, ledgertbl.taxable_no_threshold from unitbillmaster as master JOIN ledger as ledgertbl ON master.AccountHeadID = ledgertbl.id where master.UnitID = '" . $UnitID . "' and ledgertbl.show_in_bill = '1' and master.AccountHeadID != '" . INTEREST_ON_PRINCIPLE_DUE . "' and master.BeginPeriod <= '" . $beginDate . "' and master.BillType='".$this->IsSupplementaryBill()."' ORDER BY UnitID, AccountHeadID, EndPeriod ASC";
		}
		

		if($this->ShowDebugTrace == 1)
		{
			//echo "<BR>sqlFetch : " . $sqlFetch;
		}
		$resultFetch = $this->m_dbConn->select($sqlFetch);

		$roundOfBillMasterAmount = true;
		$societyInfo   = $this->m_objUtility->GetSocietyInformation($_SESSION['society_id']);
		
		if($societyInfo['IsRoundOffLedgerAmt'] == 1 && strtotime($beginDate) >= strtotime('2020-12-01')){ 
			$roundOfBillMasterAmount = false;
		}

		if($roundOfBillMasterAmount){
			foreach($resultFetch as &$value){
				$value['AccountHeadAmount'] = $this->m_objUtility->getRoundValue2($value['AccountHeadAmount']);
			}
		}
		
		if($this->ShowDebugTrace == 1)
		{
			//var_dump($resultFetch);
		}
			
		if($resultFetch <> "")
		{
			//echo "<BR>BillMaster";	
			//print_r($resultFetch);
		}
		return $resultFetch;
	}
	
	public function GetBillSubTotal_Plain($UnitID, $PeriodID, $resultFetch, $AdditionalBillingHeads, &$TaxableLedgerTotal, &$TaxableLedgerTotal_No_Threshold, &$InterestOnArrearsReversalCharge, &$BillNote,$IsReturnOnlyTaxableledgerAmount = false)
	{
		//$this->ShowDebugTrace = 1;
		$BillSubTotal = 0;//-1;
		$BillTax = 0;
		$IGST = 0;
		$CGST = 0;
		$SGST = 0;
		$CESS = 0;
		
		$sqlPeriod = "Select BeginingDate, EndingDate from period where ID = '" . $PeriodID . "'";
		$resultPeriod = $this->m_dbConn->select($sqlPeriod);
		
		$beginDate = $resultPeriod[0]['BeginingDate'];
		$endDate = $resultPeriod[0]['EndingDate'];
		
		
		/****************************************************************************
		//Get the Account Heads as per the period selected
		*****************************************************************************/
		if($this->ShowDebugTrace == 1)
		{
				echo "<BR>GetBillSubTotal_Plain Master :<BR>". var_dump($resultFetch) . "<BR>";
		}
			
		if($resultFetch <> "")
		{
			//var_dump(resultFetch);
			$BillSubTotal = 0;
			
			$unit_inprocess = 0;
			$ledger_inprocess = 0;
			$add = false;
			for($iCnt = 0; $iCnt < sizeof($resultFetch); $iCnt++)
			{
				$dateDiff = $this->m_objUtility->getDateDiff($resultFetch[$iCnt]['EndPeriod'], $endDate);
				
				if($unit_inprocess <> $resultFetch[$iCnt]['UnitID'])
				{
					$ledger_inprocess = 0;
				}
				
				if($dateDiff >= 0 && $ledger_inprocess <> $resultFetch[$iCnt]['AccountHeadID'])
				{
					$unit_inprocess = $resultFetch[$iCnt]['UnitID'];
					$ledger_inprocess = $resultFetch[$iCnt]['AccountHeadID'];
					$LedgerAmount = $resultFetch[$iCnt]['AccountHeadAmount'];
				
					$BillSubTotal += $LedgerAmount;

					if($this->ShowDebugTrace == 1)
					{
						//echo "<BR>unit_inprocess :". $unit_inprocess . "  ledger_inprocess :". $ledger_inprocess . "  Amount :". $resultFetch[$iCnt]['AccountHeadAmount'] . "  taxable? :". $resultFetch[$iCnt]['taxable'] . "<BR>";
					}					
					if($this->ShowDebugTrace == 1)
					{
						echo "<BR>Head :". $ledger_inprocess . "  HeadAmount :". $resultFetch[$iCnt]['AccountHeadAmount'] . "  TaxableLedgerTotal :". $TaxableLedgerTotal;
					}
					if($resultFetch[$iCnt]['taxable_no_threshold'] == 1)
					{
						$TaxableLedgerTotal_No_Threshold += $resultFetch[$iCnt]['AccountHeadAmount'];
						if($this->ShowDebugTrace == 1)
						{
							echo " TaxableLedgerTotal_No_Threshold : " . $TaxableLedgerTotal_No_Threshold ;
						}
					}
					else if($resultFetch[$iCnt]['taxable'] == 1)
					{
						$TaxableLedgerTotal += $resultFetch[$iCnt]['AccountHeadAmount'];
						if($this->ShowDebugTrace == 1)
						{
							//echo "<BR>Head :". $ledger_inprocess . "  HeadAmount :". $resultFetch[$iCnt]['AccountHeadAmount'] . "  TaxableLedgerTotal :". $TaxableLedgerTotal . " Taxable";
						}
					}
					else
					{
						if($this->ShowDebugTrace == 1)
						{
								//echo "<BR>Head :". $ledger_inprocess . "  HeadAmount :". $resultFetch[$iCnt]['AccountHeadAmount'] . "  TaxableLedgerTotal :". $TaxableLedgerTotal;
						}						
					}
					if($this->ShowDebugTrace == 1)
					{
						echo "  TaxableLedgerTotal :". $TaxableLedgerTotal;
					}
					if($this->ShowDebugTrace == 1)
					{
						echo "<BR>Head :". $ledger_inprocess . "  HeadAmount :". $resultFetch[$iCnt]['AccountHeadAmount'] . "  TaxableLedgerTotal :". $TaxableLedgerTotal;
					}
					//echo "head:".$resultFetch[$iCnt]['AccountHeadAmount'];
				}
			}
		}
		if($AdditionalBillingHeads <> '')
		{
			if($this->ShowDebugTrace == 1)
			{
			///	echo "<BR>Processing AdditionalBillingHeads <BR>";		
//				print_r($AdditionalBillingHeads );
			}
			$InterestOnPrincipalLegderID = INTEREST_ON_PRINCIPLE_DUE; //pending take this id from default table
			foreach($AdditionalBillingHeads as $k=>$v)
			{
				$this->strTrace .=  '<tr><td>ID : ' . $AdditionalBillingHeads[$k]['AccountHeadID'] . ' Amount : ' . $AdditionalBillingHeads[$k]['AccountHeadAmount']. ' Taxable : ' . $AdditionalBillingHeads[$k]['Taxable'] . ' taxable_no_threshold : ' . $AdditionalBillingHeads[$k]['taxable_no_threshold'] . '</td></tr>';

				if($this->ShowDebugTrace == 1)
				{
					//echo '<br>ID : ' . $AdditionalBillingHeads[$k]['AccountHeadID'] . ' Amount : ' . $AdditionalBillingHeads[$k]['AccountHeadAmount']. ' Taxable : ' . $AdditionalBillingHeads[$k]['Taxable'];
				}

				if($AdditionalBillingHeads[$k]['AccountHeadAmount'] <> '' || $AdditionalBillingHeads[$k]['AccountHeadAmount'] <> 0.00)
				{
					if( $AdditionalBillingHeads[$k]['AccountHeadID'] == $InterestOnPrincipalLegderID)
					{
						//Process Interest credit
						$InterestOnArrearsReversalCharge = $InterestOnArrearsReversalCharge + $AdditionalBillingHeads[$k]['AccountHeadAmount'];
						if ($this->ShowDebugTrace == 1)
						{
							//echo "<BR>Interest reversal credit : AccountHeadID :". $AdditionalBillingHeads[$k]['AccountHeadID'] . "  Amount :". $AdditionalBillingHeads[$k]['AccountHeadAmount'] . "  taxable? :". $AdditionalBillingHeads[$k]['Taxable'];
						}
						
					}
					else
					{
						$BillSubTotal += $AdditionalBillingHeads[$k]['AccountHeadAmount'];
						if($this->ShowDebugTrace == 1)
						{
							//echo "<BR>reversal credit : AccountHeadID :". $AdditionalBillingHeads[$k]['AccountHeadID'] . "  Amount :". $AdditionalBillingHeads[$k]['AccountHeadAmount'] . "  taxable? :". $AdditionalBillingHeads[$k]['Taxable'] ;
						}
						if($AdditionalBillingHeads[$k]['taxable_no_threshold'] == 1)
						{
							$TaxableLedgerTotal_No_Threshold += $AdditionalBillingHeads[$k]['AccountHeadAmount'];
							if($this->ShowDebugTrace == 1)
							{
								echo " TaxableLedgerTotal_No_Threshold : " . $TaxableLedgerTotal_No_Threshold ;
							}
						}
						else if($AdditionalBillingHeads[$k]['Taxable'] == 1)
						{
							$TaxableLedgerTotal += $AdditionalBillingHeads[$k]['AccountHeadAmount'];
							if($this->ShowDebugTrace == 1)
							{
								//echo "<BR>Head :". $ledger_inprocess . "  HeadAmount :". $resultFetch[$iCnt]['AccountHeadAmount'] . "  TaxableLedgerTotal :". $TaxableLedgerTotal . " Taxable";
							}
						}
					}
					$note =  $AdditionalBillingHeads[$k]['AccountHeadAmount'] . ":" . $AdditionalBillingHeads[$k]['Comments'];
					if ($this->ShowDebugTrace == 1)
					{
						//echo "<BR>Comments " . $note . "<BR>";
					}
					if($BillNote == "")
					{
						$BillNote = $note;
						
					}
					else
					{
						$BillNote = $BillNote . "<BR>" . $note;
					}
					//echo "<BR>BillNote " . $BillNote . "<BR>";										
				}
			} //for each
		}
		if($this->ShowDebugTrace == 1)
		{
			echo "<BR>BillSubTotal :" . $BillSubTotal ;
			echo "<BR>TaxableLedgerTotal :" . $TaxableLedgerTotal;
			echo "<BR>TaxableLedgerTotal_No_Threshold Total: " . $TaxableLedgerTotal_No_Threshold ;
		}
		if($IsReturnOnlyTaxableledgerAmount == true)
		{
			return $TaxableLedgerTotal;
		} 
		return $BillSubTotal;
	}
	
	public function CalculateGST($UnitID, $Unit_taxable_no_threshold, $BillDate, $TaxableLedgerTotal, $TaxableLedgerTotal_No_Threshold, $CurrentBillInterestAmount, $InterestOnArrearsReversalCharge, $societyInfo, &$IGST,&$CGST,&$SGST,&$CESS)
	{
		if($this->ShowDebugTrace == 1)
		{
			echo "<BR><BR>Inside CalculateGST TaxableLedgerTotal : " . $TaxableLedgerTotal . "    TaxableLedgerTotal_No_Threshold : " . $TaxableLedgerTotal_No_Threshold;
		}
		if($Unit_taxable_no_threshold > 0)
		{
			$this->strTrace .=  '<tr><td>Unit_taxable_no_threshold  : ' . $Unit_taxable_no_threshold . '</td></tr>';
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>Unit_taxable_no_threshold is true";
			}
		}
		$IGST = 0;
		$CGST = 0;
		$SGST = 0;
		$CESS = 0;

		//echo "<BR>BillSubTotal with RC :" . $BillSubTotal ;
		//$societyInfo = $this->m_objUtility->GetSocietyInformation($_SESSION['society_id']);
		//$ServiceTaxRate = $societyInfo['service_tax_rate'];
		$IgstServiceTaxRate = $societyInfo['igst_tax_rate'];
		$CgstServiceTaxRate = $societyInfo['cgst_tax_rate'];
		$SgstServiceTaxRate = $societyInfo['sgst_tax_rate'];
		$CessServiceTaxRate = $societyInfo['cess_tax_rate'];
		$ApplyServiceTax = $societyInfo['apply_service_tax'];
		$ApplyServiceTaxOnInterest = $societyInfo['apply_GST_on_Interest'];
		$ApplyGSTAboveThreshold = $societyInfo['apply_GST_above_Threshold'];
		$ServiceTaxLimit = $societyInfo['service_tax_threshold'];
		
		$iDateDiff = $this->m_objUtility->getDateDiff($BillDate, GST_START_DATE);
		
		if($iDateDiff < 0)
		{
			$ApplyServiceTax = 0;
			if($this->ShowDebugTrace == 1)
			{
				echo "Bill date " . $BillDate . " isi before GST start date ". GST_START_DATE . " So reseting ApplyServiceTax :". $ApplyServiceTax . "<BR>";
			}
		}
		if($this->ShowDebugTrace == 1)
		{
			echo "<BR>ApplyServiceTax ". $ApplyServiceTax;
		}

			
		if($ApplyServiceTax == 1)
		{
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>GST threshold limit : " . $ServiceTaxLimit;
			}
			if($Unit_taxable_no_threshold == 1)
			{
				 $ServiceTaxLimit = 0;
				
				if($this->ShowDebugTrace == 1)
				{
					echo "<BR>Since taxable_no_threshold Unit, resetting GST threshold limit : " . $ServiceTaxLimit . "<BR>";
				}
			}
				
			if(($ApplyServiceTaxOnInterest == 1)|| ($this->m_IsInterestTaxable == 1)|| ($this->m_IsInterestTaxable_NoThreshold == 1))
			{
				if($this->m_IsInterestTaxable_NoThreshold == 1)
				{
					$TaxableLedgerTotal_No_Threshold+= $CurrentBillInterestAmount;
					if($this->ShowDebugTrace == 1)
					{
						echo "<BR>IsInterestTaxable". $this->m_IsInterestTaxable;
						echo "<BR>IsInterestTaxable_NoThreshold". $this->m_IsInterestTaxable_NoThreshold;
						echo "<BR>ApplyServiceTaxOnInterest is set, CurrentBillInterestAmount: " . $CurrentBillInterestAmount . " added to TaxableLedgerTotal_No_Threshold = " . $TaxableLedgerTotal_No_Threshold;
					$this->strTrace .=  '<tr><td>ApplyServiceTaxOnInterest=1, CurrentBillInterestAmount: ' . $CurrentBillInterestAmount . ' added to TaxableLedgerTotal_No_Threshold = ' . $TaxableLedgerTotal_No_Threshold . '</td></tr>';
					}
				}
				else
				{					
					$TaxableLedgerTotal += $CurrentBillInterestAmount;
				}

			}
	
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>TaxableLedgerTotal: " . $TaxableLedgerTotal ;
				echo "<BR>TaxableLedgerTotal_No_Threshold: " . $TaxableLedgerTotal_No_Threshold;
				echo "<BR>GST threshold limit : " . $ServiceTaxLimit . "<BR>";
			}
/*			if($TaxableLedgerTotal < 0)//Pending : -ve tax needs to be calculated?
			{
				if($this->ShowDebugTrace == 1)
				{
					echo "<BR>TaxableLedgerTotal is : " . $TaxableLedgerTotal . " so setting it to 0<BR>"; 	
				}
				$TaxableLedgerTotal = 0;
			}
*/
			$TaxableAmount = $TaxableLedgerTotal + $TaxableLedgerTotal_No_Threshold ;
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>TaxableAmount: " . $TaxableAmount . "<BR>";
			}
			if($TaxableAmount >= $ServiceTaxLimit)
			{
				$this->strTrace .=  '<tr><td>TaxableAmount : ' . $TaxableAmount . ' more than threshold: ' . $ServiceTaxLimit . '</td></tr>';
	
				if($ApplyGSTAboveThreshold == 1)
				{
					//Pending : Check this condition. If Service tax limit to be deducted after adding TaxableAmt_No_Threshold
					$TaxableAmount -= $ServiceTaxLimit ;
					
					$this->strTrace .=  '<tr><td>ApplyGSTAboveThreshold is set. So TaxableAmount : ' . $TaxableAmount . '</td></tr>';
					if($this->ShowDebugTrace == 1)
					{
						echo "<BR>ApplyGSTAboveThreshold is set, TaxableAmount - ServiceTaxLimit : " . $TaxableAmount . " <BR>";
					}
				}
			}
			else
			{
				$this->strTrace .=  '<tr><td>TaxableAmount : ' . $TaxableAmount . ' is less than GST threshold ' . $ServiceTaxLimit . ' Applying tax only on non_threshold_amount ' . $TaxableLedgerTotal_No_Threshold . ' </td></tr>';
				$TaxableAmount = $TaxableLedgerTotal_No_Threshold;	
				if($this->ShowDebugTrace == 1)
				{
					echo "<BR>Apply tax on non threshold amount : " . $TaxableAmount ;
				}
				echo "<BR>";
			}
			
			
			
			if($InterestOnArrearsReversalCharge <> 0)
			{
//				echo "IsInterestTaxable". $this->m_IsInterestTaxable;
//				echo "IsInterestTaxable_NoThreshold". $this->m_IsInterestTaxable_NoThreshold;
				$IsInterestLedgerTaxable = $this->m_IsInterestTaxable;//$this->IsLedgerTaxable(INTEREST_ON_PRINCIPLE_DUE);
				if($IsInterestLedgerTaxable == 1)
				{
					if($this->ShowDebugTrace == 1)
					{
						echo "<BR>IsInterestLedgerTaxable: " . $IsInterestLedgerTaxable;
						echo "<BR>InterestOnArrearsReversalCharge " . $InterestOnArrearsReversalCharge ;
						echo "<BR>Ledger is Taxable" . INTEREST_ON_PRINCIPLE_DUE ;
					}
					$this->strTrace .=  '<tr><td>Interest Taxable : InterestOnArrearsReversalCharge : ' . $InterestOnArrearsReversalCharge . '</td></tr>';
					$TaxableAmount = $TaxableAmount - $InterestOnArrearsReversalCharge;
				}
			}
	
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>TaxableAmount: " . $TaxableAmount;
	
			}
			$this->strTrace .=  '<tr><td>TaxableAmount : ' . $TaxableAmount . '</td></tr>';
			$SocietyID = $_SESSION['society_id'];
			if($SocietyID == 304) //For Tipco
			{
				//echo "<BR>SocietyID : " . $SocietyID ;
				//$this->strTrace .=  '<tr><td>getRoundValue2 : ' . $this->m_objUtility->getRoundValue2($TaxableAmount * $CgstServiceTaxRate / 100) . '</td></tr>';
				//$this->strTrace .=  '<tr><td>getRoundToNextRs : ' . $this->m_objUtility->getRoundToNextRs($TaxableAmount * $CgstServiceTaxRate / 100) . '</td></tr>';
		//		if($societyInfo['IsRoundOffLedgerAmt'] == 1 && (strtotime($Main_BillDate) >= strtotime('2020-12-01')))
				
				if($societyInfo['IsRoundOffLedgerAmt'] == 1 && strtotime($BillDate) >= strtotime('2020-12-01')){
					$IGST = ($TaxableAmount * $IgstServiceTaxRate) / 100;
					$CGST = ($TaxableAmount * $CgstServiceTaxRate) / 100;
					$SGST = ($TaxableAmount * $SgstServiceTaxRate) / 100;
					$CESS = ($TaxableAmount * $CessServiceTaxRate) / 100;

				}else{
					$IGST = $this->m_objUtility->getRoundToNextRs($TaxableAmount * $IgstServiceTaxRate / 100);
					$CGST = $this->m_objUtility->getRoundToNextRs($TaxableAmount * $CgstServiceTaxRate / 100);
					$SGST = $this->m_objUtility->getRoundToNextRs($TaxableAmount * $SgstServiceTaxRate / 100);
					$CESS = $this->m_objUtility->getRoundToNextRs($TaxableAmount * $CessServiceTaxRate / 100);
				}
				
			}
			else
			{
				if($societyInfo['IsRoundOffLedgerAmt'] == 1 && strtotime($BillDate) >= strtotime('2020-12-01')){
					$IGST = ($TaxableAmount * $IgstServiceTaxRate) / 100;
					$CGST = ($TaxableAmount * $CgstServiceTaxRate) / 100;
					$SGST = ($TaxableAmount * $SgstServiceTaxRate) / 100;
					$CESS = ($TaxableAmount * $CessServiceTaxRate) / 100;
				}
				else{
					$IGST = $this->m_objUtility->getRoundValue2($TaxableAmount * $IgstServiceTaxRate / 100);
					$CGST = $this->m_objUtility->getRoundValue2($TaxableAmount * $CgstServiceTaxRate / 100);
					$SGST = $this->m_objUtility->getRoundValue2($TaxableAmount * $SgstServiceTaxRate / 100);
					$CESS = $this->m_objUtility->getRoundValue2($TaxableAmount * $CessServiceTaxRate / 100);
				}
				
			}
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>CGST " . $CGST ;
				echo "<BR>SGST " . $SGST ;
			}
			$this->strTrace .=  '<tr><td>CGST : ' . $CGST . '</td></tr>';
			echo "<BR>";
			
		}
	}

	public function CalculateTax($UnitID, $Amount)
	{
		return 0;	
	}
	
	public function getNotes($SocietyID, $PeriodID, $supplementary_bill)
	{
		//Check if Notes for current period are set for society
		$sqlSelect = "Select Notes from billregister where SocietyID = '" . $SocietyID . "' and PeriodID = '" . $PeriodID . "' and BillType='"
		.$supplementary_bill."' Order By ID DESC";
		$result = $this->m_dbConn->select($sqlSelect);
		$Notes = '';
		if($result <> '')
		{
			$Notes = $result[0]['Notes'];
			$Notes = str_replace('<br />', "", $Notes); 
		}
		else
		{
			//Get notes for Previous Period
			$PrevPeriodID = $this->getPreviousPeriodData($PeriodID);
			$sqlSelect = "Select Notes from billregister where SocietyID = '" . $SocietyID . "' and PeriodID = '" . $PrevPeriodID . "' and BillType='".$supplementary_bill."' Order By ID DESC";
			$result = $this->m_dbConn->select($sqlSelect);
			if($result <> '')
			{
				$Notes = $result[0]['Notes'];
				$Notes = str_replace('<br />', "", $Notes);
			}
		}
		echo $Notes;
		$this->strTrace .=  '<tr><td>'.$Notes.'</td></tr>';
		return $Notes;
	}

	public function setNotes($Note, $PeriodID, $supplementary_bill)
	{
		//Check if Notes for current period are set for society
		
		$sqlCheck = "Select count(PeriodID) as Cnt from billregister where PeriodID = '" . $PeriodID . "'  and BillType='".$supplementary_bill."'";
		$sqlCount = $this->m_dbConn->select($sqlCheck);
		
		$msg = '';
		
		if($sqlCount[0]['Cnt'] == 0)
		{
			$msg = "No bills have been generated for the selected Period. Kindly generate bills and then retry to Update Notes.";
		}
		else
		{
			$sqlUpdate = "Update billregister SET `Notes` = '" . $this->m_dbConn->escapeString($Note) . "' WHERE PeriodID = '" . $PeriodID . "'  and BillType='".$supplementary_bill."'";
			$resultUpdate = $this->m_dbConn->update($sqlUpdate);
			
			$msg = "Notes updated successfully";
		}
		
		echo $msg;
	}
	
	public function getFontSize($SocietyID, $PeriodID, $supplementary_bill)
	{
		//Check if Notes for current period are set for society
		 $sqlSelect = "Select font_size from billregister where SocietyID = '" . $SocietyID . "' and PeriodID = '" . $PeriodID . "' and BillType='"
		.$supplementary_bill."' Order By ID DESC";
		$result = $this->m_dbConn->select($sqlSelect);
		$FontSize = '';
		if($result <> '')
		{
			$FontSize = $result[0]['font_size'];
			//$Notes = str_replace('<br />', "", $Notes); 
		}
		else
		{
			//Get notes for Previous Period
			$PrevPeriodID = $this->getPreviousPeriodData($PeriodID);
			$sqlSelect = "Select font_size from billregister where SocietyID = '" . $SocietyID . "' and PeriodID = '" . $PrevPeriodID . "' and BillType='".$supplementary_bill."' Order By ID DESC";
			$result = $this->m_dbConn->select($sqlSelect);
			if($result <> '')
			{
				$FontSize = $result[0]['font_size'];
				//$Notes = str_replace('<br />', "", $Notes);
			}
		}
		echo $FontSize;
		//$this->strTrace .=  '<tr><td>'.$Notes.'</td></tr>';
	}
	public function setFont($Font, $PeriodID, $supplementary_bill)
	{
		//Check if Notes for current period are set for society
		
		$sqlCheck = "Select count(PeriodID) as Cnt from billregister where PeriodID = '" . $PeriodID . "'  and BillType='".$supplementary_bill."'";
		$sqlCount = $this->m_dbConn->select($sqlCheck);
		
		$msg = '';
		
		if($sqlCount[0]['Cnt'] == 0)
		{
			$msg = "No bills have been generated for the selected Period. Kindly generate bills and then retry to Update Font SIze.";
		}
		else
		{
			$sqlUpdate = "Update billregister SET `font_size` = '" . $Font . "' WHERE PeriodID = '" . $PeriodID . "'  and BillType='".$supplementary_bill."'";
			$resultUpdate = $this->m_dbConn->update($sqlUpdate);
			
			$msg = "Font Size updated successfully";
		}
		
		echo $msg;
	}
	
	public function getBillAndDueDate($periodID, $societyID, $BillType,$bShowDueDate = true)
	{
		$sql = "Select `BillDate`, `DueDate`, `DueDateToDisplay` from `billregister` where `PeriodID` = '" . $periodID . "' and `SocietyID` = '" . $societyID . "' and `BillType` = '" . $BillType . "' ORDER BY ID DESC";
		$res = $this->m_dbConn->select($sql);
		$aryDate = array();
		if($res <> '')
		{ 
			$aryDate['BillDate'] = getDisplayFormatDate($res[0]['BillDate']);
			$aryDate['DueDate'] = getDisplayFormatDate($res[0]['DueDate']);
			$aryDate['DueDateToDisplay'] = getDisplayFormatDate($res[0]['DueDateToDisplay']);
		}
		else
		{
			$sql = "select BeginingDate, EndingDate	from period where ID = '" . $periodID . "'";
			$res = $this->m_dbConn->select($sql);
			$aryDate['BillDate'] = getDisplayFormatDate($res[0]['BeginingDate']);
			$aryDate['DueDate'] = $aryDate['DueDateToDisplay'] = getDisplayFormatDate($res[0]['EndingDate']);
		}
		
		if($BillType == "1" && $bShowDueDate == false)
		{
			$aryDate['DueDate'] = $aryDate['DueDateToDisplay'] = getDisplayFormatDate(PHP_MAX_DATE);
			
		}
		return $aryDate;
	}

	function getCollectionOfDataToDisplay($society_id, $wing_id, $unit_id, $period_id,$isBillSummary = false, $SupplementaryBill)
	{		
		$sqlPeriod = "SELECT max(id),PeriodID,BillDate,DueDate FROM `billregister` where `PeriodID` = '".$period_id."' and BillType='".$SupplementaryBill."' ";
		$resultPeriod = $this->m_dbConn->select($sqlPeriod);
		
		$prevPeriod = $this->getPreviousPeriodData($period_id);
		
		//$sqlPymtQuery = "Select Type, YearID, PrevPeriodID, Status, BeginingDate, EndingDate from period where ID=" . $prevPeriod;
		//		echo '<br/>sqlPymtQuery : ' . $sqlPymtQuery;
		//$Prevresult = $this->m_dbConn->select($sqlPymtQuery);
		
		$sqlPymtQuery2 = "Select Type, YearID, PrevPeriodID, Status, BeginingDate, EndingDate from period where ID=" . $period_id;
		$Prevresult2 = $this->m_dbConn->select($sqlPymtQuery2);
				
		$detailsArray = array();		
		
		if($unit_id == 0)
		{
			$memberIDS = $this->m_objUtility->getMemberIDs($resultPeriod[0]['BillDate']);
		//$sql1 = 'select unittbl.unit_id, unittbl.unit_no, member.owner_name, bill.ID as refNo, bill.PrincipalArrears,bill.AdjustmentCredit, bill.BillNumber, bill.InterestArrears, bill.BillInterest, bill.TotalBillPayable,bill.PrevInterestArrears,bill.BillSubTotal,bill.PeriodID,bill.BillRegisterID from billdetails as bill JOIN unit as unittbl on bill.UnitID = unittbl.unit_id JOIN wing as wingtbl on unittbl.wing_id = wingtbl.wing_id JOIN society as societytbl on unittbl.society_id = societytbl.society_id JOIN member_main AS member ON member.unit = unittbl.unit_id where bill.PeriodID = "' . $period_id . '" and societytbl.society_id = "' . $society_id . '" and member.society_id = "' . $society_id . '" and member.member_id IN (SELECT  member.`member_id` FROM (select  DISTINCT(unit),`member_id` from `member_main` where ownership_date <="'.$resultPeriod[0]['BillDate'].'"  ORDER BY ownership_date desc) as member_id ) '; 
			$sql1 = 'select unittbl.unit_id, unittbl.unit_no, member.owner_name, bill.ID as refNo, bill.PrincipalArrears,bill.AdjustmentCredit, bill.BillNumber, bill.InterestArrears, bill.BillInterest, bill.TotalBillPayable,bill.PrevInterestArrears,bill.BillSubTotal,bill.PeriodID,bill.BillRegisterID from billdetails as bill JOIN unit as unittbl on bill.UnitID = unittbl.unit_id JOIN wing as wingtbl on unittbl.wing_id = wingtbl.wing_id JOIN society as societytbl on unittbl.society_id = societytbl.society_id JOIN member_main AS member ON member.unit = unittbl.unit_id where bill.PeriodID = "' . $period_id . '" and societytbl.society_id = "' . $society_id . '" and member.society_id = "' . $society_id . '" and bill.BillType="'.$SupplementaryBill.'" and member.member_id IN ('.$memberIDS.') '; 
		}
		else
		{
		$sql1 = 'select unittbl.unit_id, unittbl.unit_no, member.owner_name, bill.ID as refNo, bill.PrincipalArrears,bill.AdjustmentCredit, bill.BillNumber, bill.InterestArrears, bill.BillInterest, bill.TotalBillPayable,bill.PrevInterestArrears,bill.BillSubTotal,bill.PeriodID,bill.BillRegisterID from billdetails as bill JOIN unit as unittbl on bill.UnitID = unittbl.unit_id JOIN wing as wingtbl on unittbl.wing_id = wingtbl.wing_id JOIN society as societytbl on unittbl.society_id = societytbl.society_id JOIN member_main AS member ON member.unit = unittbl.unit_id where bill.PeriodID = "' . $period_id . '" and societytbl.society_id = "' . $society_id . '" and member.society_id = "' . $society_id . '" and bill.BillType="'.$SupplementaryBill.'" '; 
		}
		
		if($wing_id <> 0)
		{
			$sql1 .= ' and unittbl.wing_id = "' . $wing_id . '"';
		}		
		if($unit_id <> 0)
		{
			$sql1 .= ' and unittbl.unit_id = "' . $unit_id . '"';
		}
		$sql1 .= '   Group BY unittbl.unit_id  ORDER BY unittbl.sort_order ASC';
				
		$res = $this->m_dbConn->select($sql1);
		
		$sql = 'SELECT `id`, `ledger_name` FROM `ledger` where `society_id` = '.$society_id . ' OR `id` = '.INTEREST_ON_PRINCIPLE_DUE;
		if($SupplementaryBill == 1)
		{
			$sql .= ' and supplementary_bill = "1"';
		}
		else
		{
			$sql .= ' and show_in_bill = "1"';	
		}
		
		$result = $this->m_dbConn->select($sql);
		
		//$LedgerShowInBill = array("id"=> INTEREST_ON_PRINCIPLE_DUE, "ledger_name"=> $result23[0]['ledger_name']);
		//array_push($result, $LedgerShowInBill);
		/*foreach($result as $key)
		{
			
			if($key['id'] <> INTEREST_ON_PRINCIPLE_DUE)
			{
				$sql23 = 'SELECT `ledger_name` FROM `ledger` where `id` = '.INTEREST_ON_PRINCIPLE_DUE .' and `society_id` = '.$society_id;
				$result23= $this->m_dbConn->select($sql23);
				$LedgerShowInBill = array("id"=> INTEREST_ON_PRINCIPLE_DUE, "ledger_name"=> $result23[0]['ledger_name']);
				array_push($result, $LedgerShowInBill);
				
			}
		}*/
			
		//print_r($result);	
		for($i = 0; $i <  sizeof($res);$i++)
		{	
			$details = array();
			$details[''] = '';
			$details['UNIT NO'] = $res[$i]['unit_no'];
			$details['MemberName'] = $res[$i]['owner_name'];
			if($isBillSummary == true)
			{
				$sqlFinyr = "SELECT periodtable.YearID,periodtable.Type,yeartable.YearDescription FROM `period` as periodtable join `year` as yeartable on periodtable.YearID = yeartable. YearID where periodtable.ID = '".$res[$i]['PeriodID']."' ";
				$resFinyr = $this->m_dbConn->select($sqlFinyr);
				
				if($resFinyr <> "")
				{
					$details['Fin. Year'] = $resFinyr[0]['YearDescription'];
					$details['BILL FOR'] = $resFinyr[0]['Type'];		
				}
							
				$sqlBillDate = "SELECT DATE_FORMAT(BillDate, '%d-%m-%Y') as BillDate FROM `billregister` where `ID`= '".$res[$i]['BillRegisterID']."' ";
				$resBillDate = $this->m_dbConn->select($sqlBillDate);
				
				if($resBillDate <> "")
				{
					$details['BillDate'] = $resBillDate[0]['BillDate'];		
				}
			}
			$details['BILL NO'] = $res[$i]['BillNumber'];
			for($j = 0; $j < sizeof($result); ++$j)
			{
				if($result[$j]['id'] == INTEREST_ON_PRINCIPLE_DUE)
				{
					 $sql = 'SELECT `Credit` from `voucher` where `To` = '.$result[$j]['id']. ' and    `RefNo` = ' . $res[$i]['refNo'] . ' and `RefTableID` = ' . TABLE_BILLREGISTER .' and Credit < 0 ';
				}
				else
				{
					$sql = 'SELECT `Credit` from `voucher` where `To` = '.$result[$j]['id']. ' and    `RefNo` = ' . $res[$i]['refNo'] . ' and `RefTableID` = ' . TABLE_BILLREGISTER ;	
				}
			//$result = $this->m_dbConn->select($sql);	
				//$sql = 'SELECT `AccountHeadAmount` FROM `unitbillmaster` where `AccountHeadID` = '.$result[$j]['id']. ' and `UnitID` = '.$res[$i]['unit_id'];
				$res1 = $this->m_dbConn->select($sql);
				//echo "<br>Credit:".$res1[0]['Credit'] ;
					if(sizeof($res1) > 0)
					{
						$Credit=0;
					//$details[$result[$j]['ledger_name']] = $res1[0]['AccountHeadAmount'];
						if($result[$j]['id'] <> INTEREST_ON_PRINCIPLE_DUE)
						{
							
							for($m = 0; $m < sizeof($res1); $m++)
							{
								$Credit += $res1[$m]['Credit'];
							
							}
						}
						else
						{
							$Credit = $res1[0]['Credit'];
						}
						$details[$result[$j]['ledger_name']]= $Credit;
					}
					else
					{
							$details[$result[$j]['ledger_name']] = '0.00';
					}
			}
			
			$details['BillSubTotal'] = $res[$i]['BillSubTotal'];
			if($SupplementaryBill == 0)
			{
				$details['AdjustmentCredit'] = $res[$i]['AdjustmentCredit'];
				$details['InterestOnArrears'] = $res[$i]['BillInterest'];
				$details['PreviousPrincipalArrears'] = $res[$i]['PrincipalArrears'];
				$details['PreviousInterestArrears'] = $res[$i]['InterestArrears'];
			}
			$details['Payable'] = $details['BillSubTotal'] + $details['AdjustmentCredit']  + $details['InterestOnArrears'] + $details['PreviousPrincipalArrears'] +$details['PreviousInterestArrears'];   
			
			$paymentReceived = $this->GetPaymentsReceived($res[$i]['unit_id'], $period_id, $prevPeriod, $Prevresult2[0]['BeginingDate'], $Prevresult2[0]['EndingDate']);
			//echo "size";
			$iPaidDetails = '';
			$iPaidTotal = 0;
			for($iPaidCnt = 0 ; $iPaidCnt < sizeof($paymentReceived) ; $iPaidCnt++)
			{
				$iPaidDetails .= ($iPaidCnt + 1) . ' - ' . $paymentReceived[$iPaidCnt] . '<br />';
				$iPaidTotal +=  $paymentReceived[$iPaidCnt]['Amount'];
			}
			
			//$details['Paid'] = $paymentReceived;
			if($isBillSummary == true)
			{
				$details['Paid'] = $iPaidTotal;
				$details['Balance'] = $details['Payable'] - $iPaidTotal;
				if($details['Balance'] < 0 )
				{
					$details['Balance'] = number_format($details['Balance'],2).' Cr'; 		
				}
				else
				{
					$details['Balance'] = number_format($details['Balance'],2).' Dr'; 		
				}
				$details['Payable'] = $details['Payable'];
			}
			
			array_push($detailsArray, $details);
			//$details['Payable'] = number_format($details['Payable'],2);
		}
	
		if($isBillSummary == false)
		{
			$DisplayTotal = array();
			$checkZero = array();
			for($i = 0; $i <  sizeof($detailsArray); $i++)
			{
				foreach($detailsArray[$i] as $key => $value)
				{
					if($key <> '' && $key <> 'UNIT NO' && $key <> 'MemberName' &&  $key <> 'Fin. Year' &&  $key <> 'BILL FOR' && $key <> 'BILL NO' && $key <> 'BillDate' && $key <> 'Balance')
					{
					   if(!isset($DisplayTotal[$key]))
						{
							$DisplayTotal[$key] = 0;
						}
						$DisplayTotal[$key] += $value;
						 if(!isset($checkZero[$key]))
						{
							$checkZero[$key] = false;
						}
						
						if($value == '0.00' && ($checkZero[$key] <> true || $checkZero[$key] == '')) 
						{
							$checkZero[$key] = false;
						}
						else
						{
							$checkZero[$key] = true;
						}
					
					}
					else
					{
						$DisplayTotal[$key] = '';	
						$checkZero[$key] = true;
					}
				  
				}
			}
			array_push($detailsArray,$DisplayTotal);
			$detailsArray = $this->unsetZeroKeysFromArray($detailsArray , $checkZero);
		}
		return $detailsArray;	
	}	
	
	public function getCurrentAndPreviousPeriod()
	{
		$response = '<option value="0">No Period To Display</option>';
		
		$curDate = $this->display_pg->curdate();
		
		$sql = "Select periodtbl.ID, periodtbl.Type, yeartbl.YearDescription, periodtbl.PrevPeriodID from period as periodtbl JOIN year as yeartbl on periodtbl.YearID = yeartbl.YearID JOIN society as societytbl on periodtbl.Billing_cycle = societytbl.bill_cycle where '" . $curDate . "' >= periodtbl.BeginingDate and '" . $curDate . "' <= periodtbl.EndingDate and societytbl.society_id = '" . $_SESSION['society_id'] . "'";
		//echo $sql;
		$result = $this->m_dbConn->select($sql);
		
		if($result <> '')
		{
			$response = '<option value="' . $result[0]['ID'] . '">' . $result[0]['Type'] . ' : ' . $result[0]['YearDescription'] .  '</option>';		
			
			if($result[0]['PrevPeriodID'] > 0)
			{
				$sql = "Select periodtbl.ID, periodtbl.Type, yeartbl.YearDescription from period as periodtbl JOIN year as yeartbl on periodtbl.YearID = yeartbl.YearID where periodtbl.ID = '" . $result[0]['PrevPeriodID'] . "'";
				//echo $sql;
				$result = $this->m_dbConn->select($sql);
				
				if($result <> '')
				{
					$response .= '<option value="' . $result[0]['ID'] . '">' . $result[0]['Type'] . ' : ' . $result[0]['YearDescription'] .  '</option>';			
				}
			}
		}
		return ($response);
	}
	
	//When Gst Update Page call  then it update
	public function UpdateGst($BillDetailID,$CGST,$SGST)
	{
		
		$BillDetails = $this->m_dbConn->select("SELECT CGST, SGST FROM billdetails WHERE ID = '".$BillDetailID."'");
		
		//Fetching the Details of Bill Details to use 
		$VoucherDetails = $this->m_dbConn->select("select `id`, `Date` as 'BillDate', `VoucherNo`, `To`,`By` ,`Credit` from `voucher` where `RefNo` ='" . $BillDetailID . "' and`RefTableID` = '" . TABLE_BILLREGISTER . "' ");
		
		//If data not found retrun the fuction call
		if(sizeof($VoucherDetails) == 0 || sizeof($VoucherDetails) == '')
		{
			$msg = 'Data Not Found !!';
			return $msg;
		}
		
		//Declaring Ledger Array to check GST ledger present in or not (for comparision)
		$Ledgers = array();
		$IsGstInsertOrUpdate = 0;
		//Size of mean already this much entry present in voucher table
		$SrNo = sizeof($VoucherDetails);
		$timestamp = getCurrentTimeStamp();
		//Declaring variable to use 
		$BillTotal = 0;
		$UnitID = '';
		$CGST_VoucherID = '';
		$SGST_VoucherID = '';
		$Unit_VoucherID = '';
		$BillDate = $VoucherDetails[0]['BillDate'];
		
		if ($this->ShowDebugTrace == 1)
		{
			echo '<br>Bill Date '.$VoucherDetails[0]['BillDate'];
			echo '<br>Sizeof of'.$VoucherDetails[0]['VoucherNo'];	
		}
		
		//Skip Ledger to calculate the Bill total We don;t want to include skip ledger in total of bill because we updating and inserting
		$SkipLedger = array(IGST_SERVICE_TAX,CGST_SERVICE_TAX,SGST_SERVICE_TAX,CESS_SERVICE_TAX);
		
		if ($this->ShowDebugTrace == 1)
		{
			var_dump($VoucherDetails);	
		}
		
		//Number of ledger Present in voucher Table 
		for($i = 0 ; $i < sizeof($VoucherDetails); $i++)
		{
			if($VoucherDetails[$i]['To'] <> "" && $VoucherDetails[$i]['To'] <> 0)
			{
				//Checking by side entry to push in ledger array and calculate billtotal
				$SkipLedgerCatched = in_array($VoucherDetails[$i]['To'],$SkipLedger);
				
				if($SkipLedgerCatched === false)
				{
					$BillTotal = $BillTotal + $VoucherDetails[$i]['Credit'];	
				}
				
				if($VoucherDetails[$i]['To'] == CGST_SERVICE_TAX || $VoucherDetails[$i]['To'] == SGST_SERVICE_TAX)
				{
					if($VoucherDetails[$i]['To'] == CGST_SERVICE_TAX)
					{
						//storing Voucher ID of CGST
						$CGST_VoucherID = $VoucherDetails[$i]['id'];
					}
					
					if($VoucherDetails[$i]['To'] == SGST_SERVICE_TAX)
					{
						//storing Voucher ID of CGST
						$SGST_VoucherID = $VoucherDetails[$i]['id'];
					}
				}
				//Pushing ID from To side in Ledger Array
				array_push($Ledgers,$VoucherDetails[$i]['To']);	
			}
			else if($VoucherDetails[$i]['By'] <> "" && $VoucherDetails[$i]['By'] <> 0)
			{
				//Storing Voucher Details of By as UnitID and UnitVoucherID
				$UnitID = $VoucherDetails[$i]['By'];
				$Unit_VoucherID = $VoucherDetails[$i]['id'];
			}
		}
		
		if ($this->ShowDebugTrace == 1)
		{
			echo '<Br> Before Adding CGST and SGST Bill Sub Total '.$BillTotal;
			echo '<Br> CGST '.$CGST.' SGST '.$CGST; 
			echo '<br>Unit ID '.$UnitID;	
		}
		
		$BillTotal = $BillTotal+$CGST+$SGST;
		
		if ($this->ShowDebugTrace == 1)
		{
			echo '<br> Total Value "'.$BillTotal.'" update for'.$UnitID;
			echo '<br>This is SrNo '.$SrNo;
			echo '<br>Timestamp '.$timestamp['DateTime'];	
		}
		
		$CGST_Id = CGST_SERVICE_TAX;
		$SGST_Id = SGST_SERVICE_TAX;
		
		if ($this->ShowDebugTrace == 1)
		{
			echo '<br> CGST ID '.$CGST_Id;
			echo '<br> SGST ID '.$SGST_Id;	
		}
		
		
		$IsCGST_IDExits = in_array($CGST_Id,$Ledgers);
		$IsSGST_IDExits = in_array($SGST_Id,$Ledgers);

		if(($CGST <> 0 && $CGST <> '') || ($SGST <> 0 && $SGST <> ''))
		{
			if($IsCGST_IDExits === true || $IsSGST_IDExits === true)
			{
				if($IsCGST_IDExits === true)
				{
					if ($this->ShowDebugTrace == 1)
					{
						echo " <br> IsCGST_IDExits UPDATE `voucher` SET `Credit` = '".$CGST."' WHERE `To` = '".$CGST_Id."' AND RefNo = '".$BillDetailID."' AND `VoucherNo` = '".$VoucherDetails[0]['VoucherNo']."' AND RefTableID = '".TABLE_BILLREGISTER."'"; 							
					}

					$UpdateCGST = $this->m_dbConn->update("UPDATE `voucher` SET `Credit` = '".$CGST."' WHERE `To` = '".$CGST_Id."' AND RefNo = '".$BillDetailID."' AND `VoucherNo` = '".$VoucherDetails[0]['VoucherNo']."' AND RefTableID = '".TABLE_BILLREGISTER."'");
					$this->obj_register->UpdateRegister($CGST_Id, $CGST_VoucherID, TRANSACTION_CREDIT, $CGST);
				}
				
				if($IsSGST_IDExits === true)
				{
					if ($this->ShowDebugTrace == 1)
					{
						echo "<br> IsSGST_IDExits UPDATE `voucher` SET `Credit` = '".$SGST."' WHERE `To` = '".$SGST_Id."' AND RefNo = '".$BillDetailID."' AND `VoucherNo` = '".$VoucherDetails[0]['VoucherNo']."' AND RefTableID = '".TABLE_BILLREGISTER."'"; 
					}

					$UpdateCGST = $this->m_dbConn->update("UPDATE `voucher` SET `Credit` = '".$SGST."' WHERE `To` = '".$SGST_Id."' AND RefNo = '".$BillDetailID."' AND `VoucherNo` = '".$VoucherDetails[0]['VoucherNo']."' AND RefTableID = '".TABLE_BILLREGISTER."'");
					$this->obj_register->UpdateRegister($SGST_Id, $SGST_VoucherID, TRANSACTION_CREDIT, $SGST); 
				}
				$IsGstInsertOrUpdate = EDIT;
			}
			
			if($IsCGST_IDExits === false || $IsSGST_IDExits === false)
			{
				if($IsCGST_IDExits === false)
				{
					$SrNo = $SrNo + 1;
					
					if ($this->ShowDebugTrace == 1)
					{
					    echo " <br> Insert into `voucher` (`Date`, `RefNo`, `RefTableID`, `RefTableID`,`SrNo`,`VoucherTypeID`,`To`,`Credit`,`Timestamp`) Values('".$BillDate."', '".$BillDetailID."', '".TABLE_BILLREGISTER."','".$VoucherDetails[0]['VoucherNo']."', '".$SrNo."','".VOUCHER_SALES."','".CGST_SERVICE_TAX."','".$CGST."', '".$timestamp['DateTime']."')";
					}

					$InsertCGST = $this->m_dbConn->insert("Insert into `voucher` (`Date`, `RefNo`, `RefTableID`, `VoucherNo`,`SrNo`,`VoucherTypeID`,`To`,`Credit`,`Timestamp`) Values('".$BillDate."', '".$BillDetailID."', '".TABLE_BILLREGISTER."','".$VoucherDetails[0]['VoucherNo']."', '".$SrNo."','".VOUCHER_SALES."','".CGST_SERVICE_TAX."','".$CGST."', '".$timestamp['DateTime']."')");
					$this->obj_register->SetRegister($BillDate,CGST_SERVICE_TAX,$InsertCGST,VOUCHER_SALES,TRANSACTION_CREDIT,$CGST,0);
				}
				
				if($IsSGST_IDExits === false)
				{
					$SrNo = $SrNo + 1;
					
					if ($this->ShowDebugTrace == 1)
					{
						echo "<br>Insert into `voucher` (`Date`, `RefNo`, `RefTableID`, `VoucherNo`,`SrNo`,`VoucherTypeID`,`To`,`Credit`,`Timestamp`) Values('".$BillDate."', '".$BillDetailID."', '".TABLE_BILLREGISTER."', '".$VoucherDetails[0]['VoucherNo']."','".$SrNo."','".VOUCHER_SALES."','".SGST_SERVICE_TAX."','".$SGST."', '".$timestamp['DateTime']."')";
					}


					$InsertCGST = $this->m_dbConn->insert("Insert into `voucher` (`Date`, `RefNo`, `RefTableID`, `VoucherNo`,`SrNo`,`VoucherTypeID`,`To`,`Credit`,`Timestamp`) Values('".$BillDate."', '".$BillDetailID."', '".TABLE_BILLREGISTER."', '".$VoucherDetails[0]['VoucherNo']."','".$SrNo."','".VOUCHER_SALES."','".SGST_SERVICE_TAX."','".$SGST."', '".$timestamp['DateTime']."')");				
					$this->obj_register->SetRegister($BillDate,SGST_SERVICE_TAX,$InsertCGST,VOUCHER_SALES,TRANSACTION_CREDIT,$SGST,0);				
				}
				$IsGstInsertOrUpdate = ADD;
			}
			
			if ($this->ShowDebugTrace == 1)
			{
				echo "<br> Updating Voucher for By <strong>UPDATE `voucher` SET `Debit` = '".$BillTotal."' WHERE `By` = '".$UnitID."' AND RefNo = '".$BillDetailID."' AND `VoucherNo` = '".$VoucherDetails[0]['VoucherNo']."' AND RefTableID = '".TABLE_BILLREGISTER."'";  
			}

			$UpdateByInVoucher = $this->m_dbConn->update("UPDATE `voucher` SET `Debit` = '".$BillTotal."' WHERE `By` = '".$UnitID."' AND RefNo = '".$BillDetailID."' AND `VoucherNo` = '".$VoucherDetails[0]['VoucherNo']."' AND RefTableID = '".TABLE_BILLREGISTER."'");
			
			$this->obj_register->UpdateRegister($UnitID, $Unit_VoucherID, TRANSACTION_DEBIT, $BillTotal);
			
			$PreviousTotalBillPayable = $this->m_dbConn->select("Select `BillInterest`,`PrincipalArrears`, `BillSubTotal`,`AdjustmentCredit` from billdetails WHERE ID = '".$BillDetailID."'"); 
			
			if ($this->ShowDebugTrace == 1)
			{
				echo '<br>PreviousTotalBillPayable ';
				var_dump($PreviousTotalBillPayable);
			}
			
			$CurrentBillAmount = $PreviousTotalBillPayable[0]['BillSubTotal'] + $PreviousTotalBillPayable[0]['BillInterest'] + $PreviousTotalBillPayable[0]['AdjustmentCredit'] + $CGST + $SGST;
			$TotalBillPayable = $PreviousTotalBillPayable[0]['PrincipalArrears'] + $CurrentBillAmount;
			
			if ($this->ShowDebugTrace == 1)
			{
				echo "<br> Update `billdetails` set `CGST` = '".$CGST."', `SGST` = '".$SGST."', `CurrentBillAmount` = '".$CurrentBillAmount."', TotalBillPayable = '".$TotalBillPayable."' WHERE ID = '".$BillDetailID."'";
				echo '<br> This need to Update '.$TotalBillPayable;
			}
			
			$UpdateBillDetailsTable = $this->m_dbConn->update("Update `billdetails` set `CGST` = '".$CGST."', `SGST` = '".$SGST."', `CurrentBillAmount` = '".$CurrentBillAmount."', TotalBillPayable = '".$TotalBillPayable."' WHERE ID = '".$BillDetailID."'");	
			
			
			if($IsGstInsertOrUpdate == ADD)
			{
				$msg = "Changes in billdetails :: CGST =".$CGST." And SGST = ".$SGST." Added";	
			}
			else if($IsGstInsertOrUpdate == EDIT)
			{
				$msg = "Changes in billdetails :: Updated CGST =".$CGST." And SGST = ".$SGST."  :: Previous CGST = ".$BillDetails[0]['CGST']." and SGST ".$BillDetails[0]['SGST'];				
			}
			
			$iLatestChangeID = $this->m_objLog->setLog($msg, $_SESSION['login_id'], 'billdetails', $BillDetailID);
			
			return true;			
		}	
	}
	/*
	public function BillComparision($With_periodID,$With_BillType,$With_UnitID,$To_periodID,$To_BillType,$To_UnitID)
	{
		//echo 'Access';
		$Result = array();
		
		$CompareWith_BillDetails = $this->GetVoucherDataToCompare($With_periodID,$With_UnitID,$With_BillType);
		$UnitMap = array();
		$UnitCount = 0;
		for($i = 0 ; $i < sizeof($CompareWith_BillDetails); $i++)
		{
			$LedgerCount = 0;
			for($j = 0; $j < sizeof($CompareWith_BillDetails[$i]); $j++)
			{
				if($CompareWith_BillDetails[$i][$j]['UnitID'] <> '' && $CompareWith_BillDetails[$i][$j]['UnitID'] <> 0)
				{
					 $UnitID =  $CompareWith_BillDetails[$i][$j]['UnitID'];
					 $UnitMap[$UnitCount] = $UnitID;
				}
				else if($CompareWith_BillDetails[$i][$j]['To'] <> '' && $CompareWith_BillDetails[$i][$j]['To'] <> 0)
				{
					$Result[$UnitCount][$UnitID][$LedgerCount]['LedgerID'] = $CompareWith_BillDetails[$i][$j]['To'];
					$Result[$UnitCount][$UnitID][$LedgerCount]['Amout1'] = $CompareWith_BillDetails[$i][$j]['Credit'];	
					
					$LedgerCount++;
				}
			}
			$UnitCount++;
		}
		
		//var_dump($CompareWith_BillDetails);
	/*	echo '<br>************* To ***************';
		echo '<br> Year ID '.$To_YearID;
		echo '<br> PeriodID '.$To_periodID;
		echo '<br> Bill Type'.$To_BillType;
		echo '<br> UnitID '.$To_UnitID;*/
	
	/*	$CompareTo_BillDetails = $this->GetVoucherDataToCompare($To_periodID,$To_UnitID,$To_BillType);
		
		for($i = 0 ; $i < sizeof($CompareTo_BillDetails); $i++)
		{
			$LedgerCount = 0;
			$LedgerMap = array();
			for($j = 0; $j < sizeof($CompareTo_BillDetails[$i]); $j++)
			{
				if($CompareTo_BillDetails[$i][$j]['UnitID'] <> '' && $CompareWith_BillDetails[$i][$j]['UnitID'] <> 0)
				{
					  $UnitID =  $CompareTo_BillDetails[$i][$j]['UnitID'];
				
					  $UnitIndex = array_search($UnitID,$UnitMap);
					
					  $LedgerMap = $this->getLedgerMapArray($CompareWith_BillDetails[$UnitIndex]);
				}
				else if($CompareTo_BillDetails[$i][$j]['To'] <> '' && $CompareTo_BillDetails[$i][$j]['To'] <> 0)
				{
					if($LedgerCount == 0)
					{
						$LedgerCount = sizeof($LedgerMap);	
					}
					
					$LedgerID = $CompareTo_BillDetails[$i][$j]['To'];
					$LedgerIndex = array_search($LedgerID,$LedgerMap);
				
					if($LedgerIndex !== false)
					{
						$Result[$UnitIndex][$UnitID][$LedgerIndex]['Amout2'] = $CompareTo_BillDetails[$i][$j]['Credit'];
						
					}
					else
					{	
						$Result[$UnitIndex][$UnitID][$LedgerCount]['LedgerID'] = $CompareTo_BillDetails[$i][$j]['To'];
						$Result[$UnitIndex][$UnitID][$LedgerCount]['Amout2'] = $CompareTo_BillDetails[$i][$j]['Credit'];
						$LedgerCount++;	
					}
				}
			}
			$UnitCount++;
		}
		return $Result;
	}
	*/

	public function GetVoucherDataToCompare($PeriodID,$UnitID,$BillType)
	{
		$Result = array();
		if($UnitID <> 0 && $UnitID <> '')
		{
				$sql = "select v.id,v.Date,v.RefNo,v.VoucherNo,v.By,v.To,v.Debit,v.Credit from Voucher as v JOIN billdetails b ON v.RefNo = b.ID where b.PeriodID = '".$PeriodID."' AND  b.BillType = '".$BillType."' AND b.UnitID = '".$UnitID."'
		 		And v.VoucherTypeID = '".VOUCHER_SALES."' and v.RefTableID = '".TABLE_BILLREGISTER."'";	
				return $BillDetails = $this->m_dbConn->select($sql);	
		}
		else
		{
			$ExitingBillDetails = $this->m_dbConn->select("SELECT ID, UnitID FROM `billdetails` where PeriodID = '".$PeriodID."' AND  BillType = '".$BillType."'");
			
			for($i = 0; $i < sizeof($ExitingBillDetails); $i++)
			{
				$sql = "select v.id,v.Date,v.RefNo,v.VoucherNo,v.By as UnitID,v.To,v.Debit,v.Credit from Voucher as v JOIN billdetails b ON v.RefNo = b.ID where b.PeriodID = '".$PeriodID."' AND b.UnitID = '".$ExitingBillDetails[$i]['UnitID']."'
		 		And v.VoucherTypeID = '".VOUCHER_SALES."' and v.RefTableID = '".TABLE_BILLREGISTER."'";	
				
				$BillDetails = $this->m_dbConn->select($sql);
				array_push($Result,$BillDetails);
			}
			return $Result;
		}
	}
	
	public function getLedgerMapArray($Data)
	{
		$Ledgers = array();
		$cnt = 0;
		for($i = 0 ; $i < sizeof($Data); $i++)
		{
			if($Data[$i]['To'] <> '' && $Data[$i]['To'] <> 0)
			{
				$Ledgers[$cnt] = $Data[$i]['To']; 	
				$cnt++;
			}			
		}
		return $Ledgers;
	}

	//when single bill is updated this code is called  
	//Bill edit
	public function BillDetailsUpdate($Detail,$UnitID,$PeriodID,$CurrentBillInterestAmount,$InterestArrears,$PrincipalArrears,$AdjustmentCredit, $SupplementaryBill)
	{
		//$this->ShowDebugTrace = 1;
		echo "Test unit".$UnitID;
		$BillSubTotal = 0;
		$TaxableLedgerTotal = 0;
		$TaxableLedgerTotal_No_Threshold = 0;
		$ChangeMsg = "";
		$CreditTransactionType = "`Credit`";

		$sqlCheck = "select `ID`,`BillInterest`,`PrincipalArrears`,`InterestArrears`,`BillRegisterID`,`LatestChangeID`, `AdjustmentCredit`, `BillNumber`, PaymentReceived, PaidPrincipal, PaidInterest from billdetails where UnitID = '" . $UnitID . "' and PeriodID = '" . $PeriodID . "' and BillType='".$SupplementaryBill ."'" ;
		$resultCheck = $this->m_dbConn->select($sqlCheck);
	//	var_dump($resultCheck);
		if($resultCheck == "" )
		{
			echo "<BR>Bill Not found for UnitID = '" . $UnitID . "' and PeriodID = '" . $PeriodID . "' and BillType='".$SupplementaryBill ."'";
			return -1;
		}
		$BillDetailRefNo =  $resultCheck[0]['ID'];
		$BillNo = $resultCheck[0]['BillNumber'];
		//Get Asset Entry from voucher table
		$sqlCheck3= "select `id`, `Date` as 'BillDate', VoucherNo from `voucher` where `By`= '" . $UnitID ."' and `RefNo` ='" . $resultCheck[0]['ID'] . "' and`RefTableID` = '" . TABLE_BILLREGISTER . "' ";

		$resultCheck3 = $this->m_dbConn->select($sqlCheck3);
		if($this->ShowDebugTrace == 1)
		{
			//echo "<BR>Voucher No : ". $resultCheck3[0]['VoucherNo'] . "<BR>";
			/*echo "<BR>resultCheck3 BillDetailID : " . $resultCheck[0]['ID'] . "<BR>";
			print_r($resultCheck3);
			echo "<BR>Detail<BR>";
			print_r($Detail);*/
		}

		foreach($Detail as $key => $val)
		{
			$HeaderAmount = $Detail[$key]['Amt'];
			$HeaderName = $Detail[$key]['Head'];
			$VoucherID = $Detail[$key]['VoucherID'];
			$HeadOldValue = $Detail[$key]['HeadOldValue'];
			$Taxable = $Detail[$key]['Taxable'];
			$Taxable_no_threshold = $Detail[$key]['Taxable_no_threshold'];
			if($this->ShowDebugTrace == 1)
			{
				echo "<br>Ledger:".$Detail[$key]['Head']."::Amt:".$Detail[$key]['Amt'] . " ::OldAmt:".$Detail[$key]['HeadOldValue'] .  " ::Taxable_no_threshold:".$Taxable_no_threshold . "<br>";
			}
//			if($resultCheck <> "")
			{
				//New ledger added into the bill
				if($VoucherID == 0 && $HeadOldValue == 0)
				{
					//First see if record exist
					$this->FindVoucherIdAndUpdateOrInsertInVoucherAndRegisterTable_Credit($HeaderName, $resultCheck[0]['ID'], TABLE_BILLREGISTER, $HeaderAmount);

					$LedgerDetails=$this->m_objUtility->GetLedgerDetails($HeaderName);
				/*	echo "<BR>Header :";
					print_r($HeaderName);
					echo "<BR>Ledger Details";
					print_r($LedgerDetails);*/
					$Taxable = $LedgerDetails[$HeaderName]['General']['taxable'];
					$Taxable_no_threshold = $LedgerDetails[$HeaderName]['General']['taxable_no_threshold'];
					if($HeaderName == INTEREST_ON_PRINCIPLE_DUE && $HeaderAmount <> 0)
					{
						//If Interest is add in the line item, then
						//$CurrentBillInterestAmount = $CurrentBillInterestAmount + $HeaderAmount;
						
					}

					if($this->ShowDebugTrace == 1)
					{
						echo "<BR>Header" . $HeaderName . " is taxable : " . $Taxable . "  and taxable_no_threshold : " . $Taxable_no_threshold . "<BR>";
					}
				}
				else
				{
					if($HeaderName == INTEREST_ON_PRINCIPLE_DUE && $HeaderAmount >= 0)	//Pending: Review this condition. When does it meet?
					{
						$sqlUpdate2 = "Delete from `voucher` where `To`= '" . $HeaderName ."' and `RefNo` ='" . $resultCheck[0]['ID'] . "' and `RefTableID` = '" . TABLE_BILLREGISTER . "' and `id`= '" . $VoucherID ."'";
						$resultUpdate2 = $this->m_dbConn->delete($sqlUpdate2);
						$sqlUpdate3 = "Delete from `incomeregister` where `VoucherID`= '" . $VoucherID ."' and `LedgerID` ='" . $HeaderName . "' ";
						$resultUpdate3 = $this->m_dbConn->delete($sqlUpdate3);
						if($this->ShowDebugTrace == 1)
						{
						/*	echo "<BR>Deleted Interest ledger " . $HeaderName . " Amount : " . $HeaderAmount . "<BR>";
							echo $sqlUpdate2 . '<br>';
							echo $sqlUpdate3 . '<br>';*/
						}
					}
					else
					{
						
						if($HeadOldValue <> $HeaderAmount)
						{
							if($this->ShowDebugTrace == 1)
							{
								//echo "<BR>Updating " . $HeaderName . " Amount : " . $HeaderAmount . "  :: BillSubTotal " . $BillSubTotal . " and TaxableLedgerTotal : " . $TaxableLedgerTotal . "<BR>";
							}
							$this->UpdateElseInsertVoucherAndRegister_Credit($VoucherID, $HeaderName, $resultCheck[0]['ID'], TABLE_BILLREGISTER, $HeaderAmount);
						}
						else
						{
							if($this->ShowDebugTrace == 1)
							{
								echo "<BR>Amount not changed. DB not updated <BR>";
							}							
						}
							
					}
				}
				//Update BillSubTotal
				if($HeaderName == INTEREST_ON_PRINCIPLE_DUE /*&& $HeaderAmount <> 0*/)
				{
					//If Interest is add in the line item, then
					$CurrentBillInterestAmount = $CurrentBillInterestAmount + $HeaderAmount;
				}
				else
				{
					$BillSubTotal += $HeaderAmount;
					
					if($Taxable_no_threshold == 1)
					{
						$TaxableLedgerTotal_No_Threshold += $HeaderAmount;
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>TaxableLedgerTotal_No_Threshold : " . $TaxableLedgerTotal_No_Threshold ;
						}
					}
					else if($Taxable == 1)
					{
						$TaxableLedgerTotal += $HeaderAmount;
					}
					
					
				}

				if($this->ShowDebugTrace == 1)
				{
					echo "<BR>BillSubTotal " . $BillSubTotal . " and TaxableLedgerTotal : " . $TaxableLedgerTotal . " and TaxableLedgerTotal_No_Threshold : " . $TaxableLedgerTotal_No_Threshold ;
				}						
			}
		}
		
		//Update Interest On Arrears
		if($this->ShowDebugTrace == 1)
		{
			echo "<BR>Updating INTEREST_ON_PRINCIPLE_DUE Amount :". $CurrentBillInterestAmount ."<BR>";
		}
		$this->FindVoucherIdAndUpdateOrInsertInVoucherAndRegisterTable_Credit(INTEREST_ON_PRINCIPLE_DUE, $resultCheck[0]['ID'], TABLE_BILLREGISTER, $CurrentBillInterestAmount);
		
		//Update Adjustment Credit/Rebate
		if($AdjustmentCredit <>  $resultCheck[0]['AdjustmentCredit'])
		{
			if($this->ShowDebugTrace == 1)
			{
				//echo "<BR>Updating ADJUSTMENT_CREDIT Amount :". $AdjustmentCredit ."<BR>";
				echo  "Adjustment Credit New : " . $AdjustmentCredit . "  Old :" .  $resultCheck[0]['AdjustmentCredit'] ;
			}
			
			//Update only if changed
			$this->FindVoucherIdAndUpdateOrInsertInVoucherAndRegisterTable_Credit(ADJUSTMENT_CREDIT, $resultCheck[0]['ID'], TABLE_BILLREGISTER, $AdjustmentCredit);
		}

		//BillDetailsUpdate

		$IGSTAmount = 0;
		$CGSTAmount = 0;
		$SGSTAmount = 0;
		$CESSAmount = 0;
		$Unit_taxable_no_threshold = 0;

/*
		if($PeriodID <= 3)
		{
			$sUnit_GST_Threshold = "select taxable_no_threshold from unit where unit_id = " . $UnitID ;		
			$Unit_GST_Threshold = $this->m_dbConn->select($sUnit_GST_Threshold );			
			$Unit_taxable_no_threshold = $Unit_GST_Threshold[0]['taxable_no_threshold'];
		}
		else
		{
			//if(($UnitID == 67 || $UnitID == 131 ||$UnitID == 132 ||$UnitID == 215 ||$UnitID == 216 ||$UnitID == 224 ||$UnitID == 223 ||$UnitID == 67))
			{
				$sUnit_GST_Threshold = "select taxable_no_threshold from unit where unit_id = " . $UnitID ;		
				$Unit_GST_Threshold = $this->m_dbConn->select($sUnit_GST_Threshold );			
				$Unit_taxable_no_threshold = $Unit_GST_Threshold[0]['taxable_no_threshold'];
			}
		}
		*/
		
		if($this->ShowDebugTrace == 1)
		{
			echo "<BR>UnitId : " . $UnitID . "  Unit_taxable_no_threshold : " . $Unit_taxable_no_threshold ;
		}
		
									
		$BillDate = $resultCheck3[0]['BillDate'];
		
		$InterestOnArrearsReversalCharge = 0;
		//$TaxableLedgerTotal_No_Threshold = 0;
		//BillDetailsUpdate
		$societyInfo = $this->m_objUtility->GetSocietyInformation($_SESSION['society_id']);

		$this->CalculateGST($UnitID, $Unit_taxable_no_threshold, $BillDate, $TaxableLedgerTotal, $TaxableLedgerTotal_No_Threshold, $CurrentBillInterestAmount, $InterestOnArrearsReversalCharge, $societyInfo, $IGSTAmount, $CGSTAmount, $SGSTAmount,$CESSAmount);
		
		if(CGST_SERVICE_TAX > 0)
		{
			if($this->ShowDebugTrace == 1)
			{
				//echo "<BR>CGST Ledger: " . CGST_SERVICE_TAX . " CGST Amt : " . $CGSTAmount . "<BR>";
			}
			$this->ServiceTaxImplement(CGST_SERVICE_TAX,$CGSTAmount,$resultCheck[0]['ID'],$ApplyServiceTax,TABLE_BILLREGISTER);
		}
		if(SGST_SERVICE_TAX > 0)
		{
			if($this->ShowDebugTrace == 1)
			{
				//echo "<BR> SGST Ledger: " . SGST_SERVICE_TAX . " SGST Amt : " . $SGSTAmount . "<BR>";
			}
			$this->ServiceTaxImplement(SGST_SERVICE_TAX,$SGSTAmount,$resultCheck[0]['ID'],$ApplyServiceTax,TABLE_BILLREGISTER);
		}
		
		$BillSubTotal = $this->getTwoDecimalPoints($BillSubTotal);
		$CurrentBillInterestAmount = $this->getTwoDecimalPoints($CurrentBillInterestAmount);
		$AdjustmentCredit = $this->getTwoDecimalPoints($AdjustmentCredit);
		$IGSTAmount = $this->getTwoDecimalPoints($IGSTAmount);
		$CGSTAmount = $this->getTwoDecimalPoints($CGSTAmount);
		$SGSTAmount = $this->getTwoDecimalPoints($SGSTAmount);
		$CESSAmount = $this->getTwoDecimalPoints($CESSAmount);
		$PrincipalArrears = $this->getTwoDecimalPoints($PrincipalArrears);
		$InterestArrears = $this->getTwoDecimalPoints($InterestArrears);
		
		$AssetTotal = $BillSubTotal + $CurrentBillInterestAmount + $AdjustmentCredit + $IGSTAmount + $CGSTAmount + $SGSTAmount + $CESSAmount;
		$RoundOffAmt = 0;
		$CurrentBillAmount = $AssetTotal;

		if($societyInfo['IsRoundOffLedgerAmt'] == 1 && (strtotime($BillDate) >= strtotime('2020-12-01')))
		{ // If in society table IsRoundOffLedgerAmt is 0 means calculate invoice total by old method 
			//echo "<br>Date is greater";
			//$CurrentBillAmount = $BillSubTotal + $BillTax + $IGST + $CGST + $SGST + $CESS + $CurrentBillInterestAmount ;
			//$Total = $BillSubTotal + $BillTax + $IGST + $CGST + $SGST + $CESS + $CurrentBillInterestAmount;
			
			$TotalBillPayable = $BillTotal =  $AssetTotal + $InterestArrears + $PrincipalArrears;
			$TotalBillPayable = $this->m_objUtility->getRoundValue2($BillTotal);
			
			$RoundOffAmt = $TotalBillPayable - $BillTotal; 
			$RoundOffAmt = $this->getTwoDecimalPoints($RoundOffAmt);
			$AssetTotal += $RoundOffAmt;

			$this->FindVoucherIdAndUpdateOrInsertInVoucherAndRegisterTable_Credit(ROUND_OFF_LEDGER, $resultCheck[0]['ID'], TABLE_BILLREGISTER, $RoundOffAmt);
		}
		else
		{
			$TotalBillPayable = $AssetTotal + $InterestArrears + $PrincipalArrears;
			$TotalBillPayable = $this->m_objUtility->getRoundValue2($TotalBillPayable);
		}


		
		
		if($this->ShowDebugTrace == 1)
		{
			/*echo "<BR>AssetTotal : ". $AssetTotal ;
			echo "<BR>InterestArrears : ". $InterestArrears ;
			echo "<BR>PrincipalArrears : ". $PrincipalArrears ;
			echo "<BR>TotalBillPayable : ". $TotalBillPayable ;*/
		}
		$this->obj_register->UpdateRegister($UnitID, $resultCheck3[0]['id'], TRANSACTION_DEBIT, $AssetTotal); 
			
		//echo "Verify this Asset update query: <BR>" . $sqlUpdate4 = "UPDATE `assetregister` SET `Debit`= '" . $AssetTotal ."' where `VoucherID`= '" . $resultCheck3[0]['id'] ."' and `LedgerID` ='" . $UnitID . "' ";
//		$resultUpdate4 = $this->m_dbConn->update($sqlUpdate4);*/


		$sqlUpdate5 = "UPDATE `voucher` SET `Debit`= '" . $AssetTotal ."' where `By`= '" . $UnitID ."' and `RefNo` ='" . $resultCheck[0]['ID'] . "' and `RefTableID` = '" . TABLE_BILLREGISTER . "' ";
		$resultUpdate5 = $this->m_dbConn->update($sqlUpdate5);
		//echo $sqlbillregister="SELECT `LatestChangeID`  FROM  `billregister` WHERE ID ='" . $resultCheck[0]['BillRegisterID'] . "' ";
		//$PrevChangeID =$this->m_dbConn->select($sqlbillregister);
		$changeLog = new changeLog($this->m_dbConn);
		$ChangeMsg .= "Amt changed for[";
		foreach($Detail as $key => $val)
		{
			if($Detail[$key]['Amt'] <> $Detail[$key]['HeadOldValue'])
			{
				$ChangeMsg .= $Detail[$key]['Head'].":".$Detail[$key]['HeadOldValue']."::".$Detail[$key]['Amt']."||";	
			}
		}
		
		if($CurrentBillInterestAmount <> $resultCheck[0]['BillInterest'])
		{
			$ChangeMsg .= INTEREST_ON_PRINCIPLE_DUE . ":".$resultCheck[0]['BillInterest']."::".$CurrentBillInterestAmount."||";	
		}
		
		if($PrincipalArrears <> $resultCheck[0]['PrincipalArrears'])
		{
			$ChangeMsg .= "PrinArrs:".$resultCheck[0]['PrincipalArrears']."::".$PrincipalArrears."||";	
		}
		
		if($InterestArrears <> $resultCheck[0]['InterestArrears'])
		{
			$ChangeMsg .= "IntArrs:".$resultCheck[0]['InterestArrears']."::".$InterestArrears."||";	
		}
		
		if($AdjustmentCredit <> $resultCheck[0]['AdjustmentCredit'])
		{
			$ChangeMsg .= "AdjCr:".$resultCheck[0]['AdjustmentCredit']."::".$AdjustmentCredit."||";	
		}
		$ChangeMsg .= "]";
		//echo "<br>ChangeMsg:".$ChangeMsg;
		$desc = 'Updated Bill for Unit <' . $UnitID . '> Period <' . $PeriodID . '>  PrevChangeID:'.$resultCheck[0]['LatestChangeID']."<br>".$ChangeMsg;
		//echo "<BR>" . $desc . "<BR>";
		//$iLatestChangeID = $changeLog->setLog($desc, $_SESSION['login_id'], 'billregister', $resultCheck[0]['ID']);
		//echo $BillRegisterUpdate = "UPDATE `billregister` SET `LatestChangeID`='" . $this->m_dbConn->escapeString($iLatestChangeID). "' WHERE ID ='" . $resultCheck[0]['BillRegisterID'] . "'";
		//$resultBillRegister = $this->m_dbConn->update($BillRegisterUpdate);
		 $sqlUpdate1 = "UPDATE `billdetails` SET `LatestChangeID`='" . $this->m_dbConn->escapeString($iLatestChangeID) . "',`BillSubTotal`='" . $BillSubTotal . "',`BillInterest`='" . $CurrentBillInterestAmount . "',`CurrentBillAmount`='" . $CurrentBillAmount . "',`PrincipalArrears`='" . $PrincipalArrears . "',`InterestArrears`='" . $InterestArrears . "', `AdjustmentCredit` = '" . $AdjustmentCredit . "', `TotalBillPayable`='" . $TotalBillPayable . "', `BillTax` = '" . $ServiceTax . "', `IGST` = '" . $IGSTAmount . "', `CGST` = '" . $CGSTAmount. "', `SGST` = '" . $SGSTAmount . "', `CESS` = '" . $CESSAmount . "',`Ledger_round_off` = '" . $RoundOffAmt . "' WHERE ID ='" . $resultCheck[0]['ID'] . "' and UnitID ='" . $UnitID . "' and PeriodID='" . $PeriodID . "' and BillType='".$SupplementaryBill ."'";
		$resultUpdate1 = $this->m_dbConn->update($sqlUpdate1);



		//log the Bill Edit 
		// log start
		$LedgerDetailsInBill = $this->getAllIncludesLedgersInBill($resultCheck[0]['ID'], VOUCHER_SALES, 'Credit', 'To');
		$this->objFetchData->GetUnitNumber($UnitID);
		$unitNo = $this->objFetchData->objMemeberDetails->sUnitNumber;
		$billName = $this->m_objUtility->returnBillTypeString($this->IsSupplementaryBill());

		$dataArr = array('Date'=>$BillDate, 'Bill Type'=> $billName,  'Flat'=>$unitNo, 'Bill Number'=>$BillNo, 'Payment Received'=>$resultCheck[0]['PaymentReceived'], 'Paid Principal'=>$resultCheck[0]['PaidPrincipal'], 'Paid Interest'=>$resultCheck[0]['PaidInterest'], 'Bill Sub Total'=>$BillSubTotal, 'Bill Interest'=>$CurrentBillInterestAmount, 'Current Bill Amount'=>$CurrentBillAmount,'Principal Arrears'=>$PrincipalArrears, 'Interest Arrears'=>$InterestArrears, 'Total BillPayable'=>$TotalBillPayable, 'Ledgers'=>$LedgerDetailsInBill);

		$logArr = json_encode($dataArr);
		
		$previousLogID = 0;
		$previousLogDetail = $this->m_objLog->showChangeLog(TABLE_BILLREGISTER, $resultCheck[0]['ID'], true);
		if(!empty($previousLogDetail)){
			$previousLogID = $previousLogDetail[0]['ChangeLogID'];
		}
		
		$this->m_objLog->setLog($logArr, $_SESSION['login_id'], TABLE_BILLREGISTER, $resultCheck[0]['ID'], EDIT, $previousLogID);
		
		// log end

/*
		foreach($Detail as $z => $v)
		{
			$HeaderAmount1 = $OldValue1 = 0;
			$HeaderName1 = "";
			$HeaderAmount1 = $Detail[$z]['Amt'];
			$HeaderName1 = $Detail[$z]['Head'];
			$OldValue1 = $Detail[$z]['HeadOldValue'];
			
			//$this->obj_billmaster->update_billmaster($_REQUEST['UnitID'],$HeaderName1,$HeaderAmount1,$_REQUEST['PeriodID'],$_REQUEST['PeriodID'],$_REQUEST['PeriodID'],0);		
		}
		*/
/*
		//Apurva's code
		//print_r($_SESSION);
		//print_r($Detail);
		
		$select0 = "select `BeginingDate`, `EndingDate` from `period` where `ID`='".$_REQUEST['PeriodID']."' and `YearID`='".$_SESSION['default_year']."'";
		$select01 = $this->m_dbConn->select($select0);
		
		foreach($Detail as $z => $v)
		{
			$HeaderAmount1 = $Detail[$z]['Amt'];
			$HeaderName1 = $Detail[$z]['Head'];
			
			$select1 = "select * from `unitbillmaster` where `UnitID`='".$_REQUEST['UnitID']."' and `AccountHeadID`='".$HeaderName1."' and `BeginPeriod`='".$select01[0]['BeginingDate']."' and `EndPeriod`='".$select01[0]['EndingDate']."'";
			$select11 = $this->m_dbConn->select($select1);
			
			if($select11 <> '')
			{
				$update1 = "update `unitbillmaster` set `AccountHeadAmount`='".$HeaderAmount1."' where `UnitID`='".$_REQUEST['UnitID']."' and `AccountHeadID`='".$HeaderName1."' and `BeginPeriod`='".$select01[0]['BeginingDate']."' and `EndPeriod`='".$select01[0]['EndingDate']."'";
				$update11 = $this->m_dbConn->update($update1);				
			}
			else
			{
				$insert1 = "insert into `unitbillmaster`(UnitID,CreatedBy,LatestChangeID,AccountHeadID,AccountHeadAmount,BeginPeriod,EndPeriod) values('".$_REQUEST['UnitID']."','".$_SESSION['login_id']."',0,'".$HeaderName1."','".$HeaderAmount1."','".$select01[0]['BeginingDate']."','".$select01[0]['EndingDate']."')";
				$insert11 = $this->m_dbConn->insert($insert1);
			}
		}	
*/
	}
	public function getInvoiceNumberNext()
	{	
	 
		$LastNumber = " select max(Inv_Number) as Inv_Number from sale_invoice " ;
		$Result = $this->m_dbConn->select($LastNumber);
		$Result = $Result[0]['Inv_Number'] + 1;
		return $Result;
	}
	
	public function DeleteRegisterAndVoucherEntry($RefNo,$RefTable,$VoucherType)
	{	
		//selecting voucherID and VoucherTableID
		//$this->ShowDebugTrace = 0;
		$FindVoucherIDQuery = " select * from voucher where RefNo='".$RefNo."' and RefTableID='".$RefTable."'";
		$FindVoucherIDQueryResult = $this->m_dbConn->select($FindVoucherIDQuery);
		//var_dump($FindVoucherIDQueryResult);
		
		for( $i=0;  $i<=sizeof($FindVoucherIDQueryResult); $i++)
		{
		//	echo 'first check whether Voucher ID is not null';
			
			if($FindVoucherIDQueryResult[$i]['id'] <> '')
			{
				if($this->ShowDebugTrace == 1)
				{
					echo '<BR>If Voucher ID is not Null then Check Current Voucher Belong to Which Register';
				}
				$GetVoucherGroup = array();
				if($FindVoucherIDQueryResult[$i]['By'] <> 0 &&  $FindVoucherIDQueryResult[$i]['By'] <> '')
				{
			   		$GetVoucherGroup = $this->m_objUtility->getParentOfLedger($FindVoucherIDQueryResult[$i]['By']);
				}
				else if($FindVoucherIDQueryResult[$i]['To'] <> 0 &&  $FindVoucherIDQueryResult[$i]['To'] <> '')
				{
					 $GetVoucherGroup = $this->m_objUtility->getParentOfLedger($FindVoucherIDQueryResult[$i]['To']);
				}
				
				if($this->ShowDebugTrace == 1)
				{
					echo '<BR>After Identify the Register Entry will deleted to respective register';
				}
			//	echo 'Group ID'.var_dump($GetVoucherGroup['group']);
				
				if($GetVoucherGroup['group'] == INCOME)
					{
						if($this->ShowDebugTrace == 1)
						{
							echo '<BR>delete VoucherEntry in income register';
						}
					//delete VoucherEntry in income register
					
					$deleteIncomeVoucher="DELETE FROM incomeregister WHERE VoucherID='".$FindVoucherIDQueryResult[$i]['id']."' and VoucherTypeID='".$VoucherType."'";
					$deleteIncomeVoucherResult=$this->m_dbConn->delete($deleteIncomeVoucher);
					}
					
				if($GetVoucherGroup['group'] == ASSET)
					{
						if($this->ShowDebugTrace == 1)
						{
							echo '<br>delete VoucherEntry in Assest register';
						}
					
					
					$deleteAssetVoucher="DELETE FROM assetregister WHERE VoucherID='".$FindVoucherIDQueryResult[$i]['id']."' and VoucherTypeID='".$VoucherType."'";
					$deleteAssetVoucherResult=$this->m_dbConn->delete($deleteAssetVoucher);
					}
					
				if($GetVoucherGroup['group'] == LIABILITY)
					{
						if($this->ShowDebugTrace == 1)
						{
							echo '<BR>delete VoucherEntry in Liability register';
						}
						
					 $deleteliabilityVoucher="DELETE FROM liabilityregister WHERE VoucherID='".$FindVoucherIDQueryResult[$i]['id']."' and VoucherTypeID='".$VoucherType."'";
					 $deleteliabilityVoucherResult=$this->m_dbConn->delete($deleteliabilityVoucher);	
					}
					
				if($GetVoucherGroup['group'] == EXPENSE)
					{
						if($this->ShowDebugTrace == 1)
						{	
							echo 'delete VoucherEntry in Expense register';
						}
					
					$deleteliabilityVoucher="DELETE FROM expenseregister WHERE VoucherID='".$FindVoucherIDQueryResult[$i]['id']."' and VoucherTypeID='".$VoucherType."'";
					$deleteliabilityVoucherResult=$this->m_dbConn->delete($deleteliabilityVoucher);	
					}	
				}
			}
		
		if($this->ShowDebugTrace == 1)
		{
			echo "<BR>deleting record from voucher table by using refno ,date and table<BR>";
		}
		
		$voucherdeleteQuery="DELETE FROM voucher WHERE RefNo='".$RefNo."' and RefTableID='".$RefTable."'";
		$voucherdeleteQueryResult=$this->m_dbConn->delete($voucherdeleteQuery);
	}
	public function deleteInvoice($InvoiceID, $unitID, $billdate, $IsRequestEditInvoice ,$bSkipBeginTrnx=false )
	{
		//*** Delete Invoice Function from all the register
		try
		{
			if(!$bSkipBeginTrnx)
			{
				echo "<BR> This is begin Transaction Function <BR>";			
				$this->m_dbConn->begin_transaction();
			}
			//deleting record from sale_invoice table
			//echo '<BR>Value of IsRequestDelete '.$IsRequestDelete;
			if($IsRequestEditInvoice == 1)
			{
				echo '<BR> Inside the Delete';
				$previousDetailQry = "SELECT Inv_Number, InvSubTotal, TotalPayable FROM `sale_invoice` WHERE ID = '$InvoiceID'";
				$previousDetail = $this->m_dbConn->select($previousDetailQry);
				$LedgerDetailsInBill = $this->getAllIncludesLedgersInBill($InvoiceID, VOUCHER_JOURNAL);

				$invoicedeleteQuery = "DELETE FROM sale_invoice WHERE ID='".$InvoiceID."' and Inv_Date='".$billdate."' and UnitID='".$unitID."'";
				$result=$this->m_dbConn->delete($invoicedeleteQuery);
			}
				
			$this->DeleteRegisterAndVoucherEntry($InvoiceID,TABLE_SALESINVOICE,VOUCHER_JOURNAL);
			
			$InvoiceAmt = $this->m_dbConn->select("SELECT sum(Debit) as Amount FROM `voucher` where RefNo='".$InvoiceID."' and RefTableID='".TABLE_SALESINVOICE."'");	
		
		if($IsRequestEditInvoice == 1)
		{
			// log the deleted entry details
			$previousLogDetails = $this->m_objLog->showChangeLog(TABLE_SALESINVOICE, $InvoiceID, true);
			$previousLogID = $previousLogDetails[0]['ChangeLogID'];
			$previousLogDesc = $previousLogDetails[0]['ChangedLogDec'];

			if(empty($previousLogDesc)){

				$unitNo = $this->m_objUtility->getLedgerName($unitID);
				$billName = $this->m_objUtility->returnBillTypeString(Invoice);

				$dataArr = array('Date'=>$billdate, 'Bill Type'=> $billName,  'Flat'=>$unitNo, 'Bill Number'=>$previousDetail[0]['Inv_Number'], 'Sub Total'=>$previousDetail[0]['InvSubTotal'], 'Total BillPayable'=>$previousDetail[0]['TotalPayable'], 'Ledgers'=>$LedgerDetailsInBill);

				$previousLogDesc = json_encode($dataArr);

				$previousLogID = 0;
			}

			//$desc = " UnitID ['".$unitID."'] Invoice Bill Deleted ||  Original Amt was '".$InvoiceAmt[0]['Amount']."' InvoiceID was '".$InvoiceID."'";	 	
			$iLatestChangeID = $this->m_objLog->setLog($previousLogDesc, $_SESSION['login_id'], TABLE_SALESINVOICE, $InvoiceID, DELETE, $previousLogID);		
		}
		
		$this->m_dbConn->commit();
		$info = " Statement Commited " ;
		return $info;
		}
		
		catch(Exception $exp)
		{
			$this->m_dbConn->rollback();
			return $exp;
		}
	}	
	
	public function ShowTaxable($LedgerID)
	{
		
		 $checktaxableLedger="select id, taxable from ledger where id = '".$LedgerID."' ";
		 $res = $this->m_dbConn->select($checktaxableLedger);
		 return $res[0]['taxable'];
	}
	
	public function GetInvoiceNumber($unitID)
	{
	   $result = $this->m_dbConn->select("SELECT Inv_Number from sale_invoice WHERE UnitID = '".$unitID."'");
	   return $result;
	}

	public function GetInvoiceDetail($InvoiceID){

		if(!empty($InvoiceID)){

			$qry = "SELECT UnitID, Inv_Number FROM sale_invoice WHERE ID = $InvoiceID";
			return $this->m_dbConn->select($qry);

		}
		
	}


	public function FetchSaleInvoice($InvoiceNo, $UnitID)
	{
		//*** This method to request invoice bill to invoice.php page with respective Inv_number and UnitID
		
		$SQL = "select * from sale_invoice where  Inv_Number= '".$InvoiceNo."' and UnitID='".$UnitID."'";
		$Result =$this->m_dbConn->select($SQL);
		return $Result;
		
	}
	
	public function FetchDebitCreditDetails($ID)
	{
		$SQL = "select * from credit_debit_note where  ID  =  '".$ID."'";
		$Result = $this->m_dbConn->select($SQL);
		return $Result;
	}
	
	public function GetNextIDOfDebitCredit()
	{
		
		$Result = $this->m_dbConn->select("SHOW TABLE STATUS LIKE 'credit_debit_note'");
		//$Result = $this->m_dbConn->select("Select max(ID) as NextID from credit_debit_note");
	
		$next_increment = $Result[0]['Auto_increment'];
		
		return $next_increment;
		
	}
	
	public function getSaleInvoicORDebitCreditNoteDetail($IsDebitCreditNote = false)
	{
		//getSaleInvoiceDetail() function is fetching all required information from sale_invoice table
		
		$InvoiceDetails = array();
		$LedgerID = '';
		if($IsDebitCreditNote == true)
		{
			$SqlUnitID = "Select DISTINCT(UnitID) from credit_debit_note where `Date` between '" .getDBFormatDate($_SESSION['from_date']). "' AND '" .getDBFormatDate($_SESSION['to_date'])."'";
		}
		else
		{
			$SqlUnitID = "Select DISTINCT(UnitID) from sale_invoice where Inv_Date between '" .getDBFormatDate($_SESSION['from_date']). "' AND '" .getDBFormatDate($_SESSION['to_date'])."'";
		}

		$ResultSqlUnitID = $this->m_dbConn->select($SqlUnitID);

		//echo '<BR>Size of Sale Invocie'.sizeof($ResultSqlUnitID);
		for($i = 0 ; $i < sizeof($ResultSqlUnitID) ; $i++)
		{
			//echo '<BR>Size'.sizeof($ResultSqlUnitID);
			$final =array();
			$LedgerID = $ResultSqlUnitID[$i]['UnitID'];	
			$CheckLedgerIDCategory = "SELECT categoryid FROM ledger WHERE id = '".$LedgerID."'";
			$CheckLedgerIDCategoryResult = $this->m_dbConn->select($CheckLedgerIDCategory);
			if($CheckLedgerIDCategoryResult[0]['categoryid'] == DUE_FROM_MEMBERS)
			{
			  if($IsDebitCreditNote == true)
			  {
				 $LedgerIDDetails = "Select n.ID, n.UnitID, n.CGST, n.SGST, n.Date as Date, n.Note_Sub_Total as SubTotal, n.Note_No, n.TotalPayable,n.BillType,n.Note_Type, m.owner_name, m.member_id, u.unit_no, ld.GSTIN_No from  credit_debit_note as n join member_main as m on n.UnitID = m.Unit join unit as u ON u.unit_id = n.UnitID left join ledger_details as ld ON ld.LedgerID = n.UnitID where m.ownership_status = 1 AND n.Date between '" .getDBFormatDate($_SESSION['from_date']). "' AND '" .getDBFormatDate($_SESSION['to_date']). "' AND n.UnitID = '".$LedgerID."'";
			  }
			  else
			  {
				 $LedgerIDDetails = "Select s.ID, s.UnitID, s.CGST, s.SGST, s.Inv_Date as Date, s.InvSubTotal as SubTotal, s.Inv_Number, s.TotalPayable, m.owner_name, m.member_id, u.unit_no, ld.GSTIN_No from sale_invoice as s join member_main as m on s.UnitID = m.Unit join unit as u ON u.unit_id = s.UnitID left join ledger_details as ld ON ld.LedgerID = s.UnitID where m.ownership_status = 1 AND s.Inv_Date between '" .getDBFormatDate($_SESSION['from_date']). "' AND '" .getDBFormatDate($_SESSION['to_date']). "' AND s.UnitID = '".$LedgerID."'";				  
			  }		

			}
			else
			{
				$LedgerIDDetails = "Select s.ID, s.UnitID, s.CGST, s.SGST, s.Inv_Date as Date, s.InvSubTotal as SubTotal, s.Inv_Number, s.TotalPayable, l.id AS LedgerID, l.ledger_name, acc.group_id, ld.GSTIN_No from sale_invoice as s join ledger as l on s.UnitID = l.id join account_category as acc ON acc.category_id = l.categoryid left join ledger_details as ld ON ld.LedgerID = l.id where Inv_Date between '" .getDBFormatDate($_SESSION['from_date']). "' AND '" .getDBFormatDate($_SESSION['to_date']). "' AND l.id ='".$LedgerID."'";
			}
			$ResultLedgerIDDetails = $this->m_dbConn->select($LedgerIDDetails);
			//$parentArray = array('Invoice' => $ResultLedgerIDDetails);
			for($j = 0 ; $j < sizeof($ResultLedgerIDDetails) ; $j++)
			{
				array_push($InvoiceDetails , $ResultLedgerIDDetails[$j]);
			}
		}
		//array_push($InvoiceDetails , $final);
		return $InvoiceDetails;
	}
	
	public function deleteDebitorCredit($DebitorCreditID,$NoteType,$IsUpdateRequest = false)
	{
		$ToOrBy = 'By';
		$CreditOrDebit = 'Debit';
		$VoucherType = VOUCHER_CREDIT_NOTE;
		//Deleting entry from Voucher and register due to we entry for Credit note
		if($NoteType == DEBIT_NOTE)
		{
			$ToOrBy = 'To';
			$CreditOrDebit = 'Credit';
			$VoucherType = VOUCHER_DEBIT_NOTE;
		}

		if($IsUpdateRequest == false){

			// log the deleted entry details
			$previousDetails = $this->m_objLog->showChangeLog(TABLE_CREDIT_DEBIT_NOTE, $DebitorCreditID, true);

			if(!empty($previousDetails)){
				
				$previousLogID = $previousDetails[0]['ChangeLogID'];
				$previousLogDesc = $previousDetails[0]['ChangedLogDec'];
			}
			else{

				$previousDetailsQuery = "SELECT UnitID, `Date`, Note_No, Note_Sub_Total, TotalPayable, BillType FROM `credit_debit_note` WHERE ID = '$DebitorCreditID'";
				$previousDetail = $this->m_dbConn->select($previousDetailsQuery);

				extract($previousDetail[0]);

				$LedgerDetailsInBill = $this->getAllIncludesLedgersInBill($DebitorCreditID, $VoucherType, $CreditOrDebit, $ToOrBy);
				$unitNo = $this->m_objUtility->getLedgerName($UnitID);
				$billName = $this->m_objUtility->returnBillTypeString($BillType);

				$dataArr = array('Date'=>$Date, 'Bill Type'=> $billName,  'Flat'=>$unitNo, 'Bill Number'=>$Note_No, 'Sub Total'=>$Note_Sub_Total, 'Total BillPayable'=>$TotalPayable, 'Ledgers'=>$LedgerDetailsInBill);

				$previousLogDesc = json_encode($dataArr);

				$previousLogID = 0;

			}
		}
		
		$this->DeleteRegisterAndVoucherEntry($DebitorCreditID,TABLE_CREDIT_DEBIT_NOTE,$VoucherType);

		if($IsUpdateRequest == false){

			$sql = "delete from credit_debit_note where ID='".$DebitorCreditID."'";
			$res = $this->m_dbConn->delete($sql);
			
			$iLatestChangeID = $this->m_objLog->setLog($previousLogDesc, $_SESSION['login_id'], TABLE_CREDIT_DEBIT_NOTE, $DebitorCreditID, DELETE, $previousLogID);		
		}
		
		return $res;
	}
	
	public function AddCreditDebitNote($Detail,$UnitID,$bill_date,$BillType,$NoteType,$BillNote,$IseditModeSet,$CreditDebitEditable_ID,$IsCallUpdtCnt = 1,$ExternalCounter, $flag = 0,$GUID)
	{
		$CalculatedGSTDetails = 0;
		return $this->AddCreditDebitNoteWithImport($Detail,$UnitID,$bill_date,$BillType,$NoteType,$BillNote,$IseditModeSet,$CreditDebitEditable_ID,$IsCallUpdtCnt,$ExternalCounter, $flag,$GUID,false,$CalculatedGSTDetails);
	}
	
	public function AddCreditDebitNoteWithImport($Detail,$UnitID,$bill_date,$BillType,$NoteType,$BillNote,$IseditModeSet,$CreditDebitEditable_ID,$IsCallUpdtCnt = 1,$ExternalCounter, $flag = 0,$GUID,$IsRequestFromImportCredit,$CalculatedGSTDetails)
	{
		//var_dump($IsRequestFromImportCredit);
		//var_dump($CalculatedGSTDetails);
		//error_reporting(1);		
		$ToOrBy = 'By';
		$CreditOrDebit = 'Debit';
		$VoucherType = VOUCHER_CREDIT_NOTE;
		
		if($NoteType == DEBIT_NOTE)
		{
			$ToOrBy = 'To';
			$CreditOrDebit = 'Credit';
			$VoucherType = VOUCHER_DEBIT_NOTE;
		}
		
		if($IseditModeSet == 1)
		{	
			$PreviousDataQuery = "Select ID,UnitID,Date,Note_No,TotalPayable,BillType from credit_debit_note where ID = '".$CreditDebitEditable_ID."'";
			$PreviousData = $this->m_dbConn->select($PreviousDataQuery);
			$VoucherSql = "SELECT id,VoucherNo,`".$ToOrBy."` as `To`,".$CreditOrDebit." as `Credit` FROM voucher where RefNo = ".$PreviousData[0]['ID']." AND RefTableID = '".TABLE_CREDIT_DEBIT_NOTE."' AND `".$ToOrBy."` != '' AND `".$ToOrBy."`  NOT IN ('".IGST_SERVICE_TAX."','".CGST_SERVICE_TAX."','".SGST_SERVICE_TAX."','".CESS_SERVICE_TAX."')";
			$voucherResult = $this->m_dbConn->select($VoucherSql);
			
			$this->deleteDebitorCredit($CreditDebitEditable_ID,$NoteType,true);
		}
		$timestamp = getCurrentTimeStamp();
		$bill_date = getDBFormatDate($bill_date);
		$IsGstRefund = false;
		$TaxableLedger = '';
		
		if($flag == 1)
		{
			for($i = 0; $i <= sizeof($Detail); $i++)
			{
			
				if($Detail[$i]['Head'] <> '')
				{
					$checkTaxableLedger = $Detail[$i]['invoicetaxable'];
					//echo '<br>Check Taxable Ledger.'.$i.' : '.$checkTaxableLedger;
					$TaxableLedgerHead =  $Detail[$i]['Head'];
					//echo '<br>Taxable Ledger Head.'.$i.' : '.$TaxableLedgerHead;
					$Total+=$Detail[$i]['Amt'];
					
				}
				if($Detail[$i]['Head']==$_SESSION['cgst_service_tax'])
				{
					$CGST=$Detail[$i]['Amt'];
				}
				if($Detail[$i]['Head']==$_SESSION['sgst_service_tax'])
				{
					$SGST=$Detail[$i]['Amt'];
				}
				
			}
			
			$SubTotal=$Total-($SGST+$CGST);
		}
		else if($IsRequestFromImportCredit == true)
		{
			echo '<br>Is IsRequestFromImportCredit';
			$SubTotal = $CalculatedGSTDetails['SubTotal'];
			$CGST = $CalculatedGSTDetails['CGST'];
			$SGST = $CalculatedGSTDetails['SGST'];
			$RoundOffAmt = $CalculatedGSTDetails['RoundOffAmt'];
			$Total = $CalculatedGSTDetails['Total'];
		}
		else
		{
			$Final = $this->CalculateInvoiceGst($Detail, $IseditModeSet);
			$SubTotal = $Final['SubTotal'];
			$CGST = $Final['CGST'];
			$SGST = $Final['SGST'];
			$RoundOffAmt = $Final['RoundOffAmt'];
			$Total = $Final['Total'];
			$TaxableLedger = $Final['TaxabledetailsString'];
		}
		/*echo '<br>Sub Total : '.$SubTotal;
		echo '<br>CGST : '.$CGST;
		echo '<br>SGST : '.$SGST;
		echo '<br>Taxable Ledger : '.$TaxableLedger;*/
		
		if($TaxableLedger <> 0 && $TaxableLedger !== '')
		{
			$IsGstRefund = true;
		}
		
		$previousLogID = 0;
		$requestStatus = ADD;

		if($IseditModeSet == 0) // fresh entry
		{
			if($ExternalCounter == 0 || $ExternalCounter == '')
			{
				$Counter = $this->m_objUtility->GetCounter($VoucherType,0,false);
				$ExternalCounter = $Counter[0]['CurrentCounter'];	
			}
			
			$sql = "INSERT INTO `credit_debit_note` (`id`,`UnitID`,`Date`,`Note_No`,`Note_Sub_Total`,`CGST`, `SGST`,`TotalPayable`,`Note`,`YearID`,`BillType`,`Note_Type`,`CreatedTimestamp`,`CreatedBy_LoginID`,`TaxableLedgers`, `Ledger_round_off`) 
			VALUES('','".$UnitID."','".$bill_date."','".$ExternalCounter."', '".$SubTotal."', '".$CGST."', '".$SGST."', '".$Total."','".$this->m_dbConn->escapeString($BillNote)."', '".$_SESSION['default_year']."','".$BillType."','".$NoteType."','".$timestamp['DateTime']."','".$this->m_dbConn->escapeString($_SESSION['login_id'])."','".$TaxableLedger."', '".$RoundOffAmt."') ";
			$RefNo = $this->m_dbConn->insert($sql);
			
		}
		else // update the exiting one
		{
		 	$updateSql = "Update `credit_debit_note` set `UnitID` = '".$UnitID."', `Date` = '".$bill_date."',`Note_No` = '".$ExternalCounter."',`BillType` = '".$BillType."',`Note_Sub_Total` = '".$SubTotal."',`CGST` = '".$CGST."',`SGST` = '".$SGST."',`TotalPayable` = '".$Total."',`Note` = '".$this->m_dbConn->escapeString($BillNote)."',`LastModified` = '".$timestamp['DateTime']."',`LatestChangeID`= '".$this->m_dbConn->escapeString($_SESSION['login_id'])."',`TaxableLedgers`= '".$TaxableLedger."', `Ledger_round_off` = '".$RoundOffAmt."' where ID = '".$CreditDebitEditable_ID."'";
			$CreditDebitTableUpdate = $this->m_dbConn->update($updateSql);
			$RefNo = $CreditDebitEditable_ID;
			
			//$desc = $this->returnLogDesc(EDIT,$PreviousData[0]['UnitID'],$UnitID,$bill_date,$PreviousData[0]['Date'],$voucherResult,$Detail,$PreviousData[0]['Note_No'],$ExternalCounter,$PreviousData[0]['TotalPayable'],$Total,$PreviousData[0]['BillType'],$BillType);
			
			$requestStatus = EDIT;
			$previousLogDetail = $this->m_objLog->showChangeLog(TABLE_CREDIT_DEBIT_NOTE, $CreditDebitEditable_ID, true);
			$previousLogID = $previousLogDetail[0]['ChangeLogID'];
			
			
					
		}
		
		$this->UpdateOrInsertVoucherAndRegisterDebitCreditNote($Detail,$RefNo,$LedgerAmount,$IsGstRefund, $bill_date, $Detail[$i], $UnitID, $BillNote, $SubTotal,$CGST,$SGST,$Total,$NoteType,$IsCallUpdtCnt,$ExternalCounter,$GUID, $RoundOffAmt);			
		
		// log the changes insert/update
		$LedgerDetailsInBill = $this->getAllIncludesLedgersInBill($RefNo, $VoucherType, $CreditOrDebit, $ToOrBy);
		$unitNo = $this->m_objUtility->getLedgerName($UnitID);
		$billName = $this->m_objUtility->returnBillTypeString($BillType);

		$dataArr = array('Date'=>$bill_date, 'Bill Type'=> $billName,  'Flat'=>$unitNo, 'Bill Number'=>$ExternalCounter, 'Sub Total'=>$SubTotal, 'Total BillPayable'=>$Total, 'Ledgers'=>$LedgerDetailsInBill);

		$logArr = json_encode($dataArr);
		
		$this->m_objLog->setLog($logArr, $_SESSION['login_id'], TABLE_CREDIT_DEBIT_NOTE, $RefNo, $requestStatus, $previousLogID);
		
		// log end	
		


		return 1;
	}
	
	public function UpdateOrInsertVoucherAndRegisterDebitCreditNote($Detail,$RefNo, $LedgerAmount, $IsGstRefund, $CreditNoteDate, $LedgerID, $UnitID, $Note,$SubTotal,$CGST,$SGST,$Total,$NoteType,$IsCallUpdtCnt,$ExternalCounter,$GUID, $RoundOffAmt)
	{
		//echo '<br>Access the UpdateOrInsertVoucherAndRegister';
		//echo '<br>RefNo '.$RefNo.'<br>LedgerAmount '.$LedgerAmount.'<br>IsGstRefund '.$IsGstRefund . '<br>CreditNoteDate '.$CreditNoteDate . '<br>LedgerID '.$LedgerID . '<br>UnitID'.$UnitID.'Note'.$Note . 'ExternalCounter'.$ExternalCounter ;
		
		$VoucherCouter = $this->obj_LatestCount->getLatestVoucherNo($_SESSION['society_id']);
		$SrNo = 0;
		//Counters to make record of total Number of credit note currently no counter set for credit or debit note
		$TransactionType = '';
		$VoucherType = '';
		//Check Is a credit note or debit note according to that make entry in respective table
		if($NoteType == CREDIT_NOTE)
		{
			//Storing $TransactionType for GST with respect to note type
			$TransactionType = TRANSACTION_DEBIT;
			$VoucherType = VOUCHER_CREDIT_NOTE;
			for($i = 0 ; $i < sizeof($Detail); $i++)
			{
				$SrNo = $SrNo + 1;
				//First We insert data in Voucher Table .
				
				if(!empty($GUID))
				{
					$IncomeVoucherID = $this->obj_voucher->SetVoucherDetails_WithGUID(getDBFormatDate($CreditNoteDate), $RefNo, TABLE_CREDIT_DEBIT_NOTE, $VoucherCouter, $SrNo, VOUCHER_CREDIT_NOTE, $Detail[$i]['Head'], TRANSACTION_DEBIT, $Detail[$i]['Amt'], 0,$ExternalCounter,$GUID);
				}
				else
				{
					$IncomeVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($CreditNoteDate), $RefNo, TABLE_CREDIT_DEBIT_NOTE, $VoucherCouter, $SrNo, VOUCHER_CREDIT_NOTE, $Detail[$i]['Head'], TRANSACTION_DEBIT, $Detail[$i]['Amt'], 0,$ExternalCounter);
				}
				
				//Inserting data into register
				$regResult = $this->obj_register->SetRegister(getDBFormatDate($CreditNoteDate), $Detail[$i]['Head'], $IncomeVoucherID, VOUCHER_CREDIT_NOTE, TRANSACTION_DEBIT, $Detail[$i]['Amt'],0);	
			}	
		}
		else if($NoteType == DEBIT_NOTE)
		{
			$TransactionType = TRANSACTION_CREDIT;
			$VoucherType = VOUCHER_DEBIT_NOTE;
			$SrNo = $SrNo + 1;
			
			if(!empty($GUID))
			{
				$AssetVoucherID = $this->obj_voucher->SetVoucherDetails_WithGUID(getDBFormatDate($CreditNoteDate), $RefNo, TABLE_CREDIT_DEBIT_NOTE, $VoucherCouter, $SrNo, VOUCHER_DEBIT_NOTE, $UnitID, TRANSACTION_DEBIT, $Total,0,$ExternalCounter,$GUID);			
			}
			else
			{
				$AssetVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($CreditNoteDate), $RefNo, TABLE_CREDIT_DEBIT_NOTE, $VoucherCouter, $SrNo, VOUCHER_DEBIT_NOTE, $UnitID, TRANSACTION_DEBIT, $Total,0,$ExternalCounter);			
			}
			$regResult = $this->obj_register->SetRegister(getDBFormatDate($CreditNoteDate), $UnitID, $AssetVoucherID, VOUCHER_DEBIT_NOTE, TRANSACTION_DEBIT, $Total,0);
			
			for($i = 0 ; $i < sizeof($Detail); $i++)
			{
				$SrNo = $SrNo + 1;
				
				if(!empty($GUID))
				{
					$IncomeVoucherID = $this->obj_voucher->SetVoucherDetails_WithGUID(getDBFormatDate($CreditNoteDate), $RefNo, TABLE_CREDIT_DEBIT_NOTE, $VoucherCouter, $SrNo, VOUCHER_DEBIT_NOTE, $Detail[$i]['Head'], TRANSACTION_CREDIT, $Detail[$i]['Amt'], 0,$ExternalCounter,$GUID);		
				}
				else
				{
					$IncomeVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($CreditNoteDate), $RefNo, TABLE_CREDIT_DEBIT_NOTE, $VoucherCouter, $SrNo, VOUCHER_DEBIT_NOTE, $Detail[$i]['Head'], TRANSACTION_CREDIT, $Detail[$i]['Amt'], 0,$ExternalCounter);
				}
				//First We insert data in Voucher Table .
				
			
				//Inserting data into register
				$regResult = $this->obj_register->SetRegister(getDBFormatDate($CreditNoteDate), $Detail[$i]['Head'], $IncomeVoucherID, VOUCHER_DEBIT_NOTE, TRANSACTION_CREDIT, $Detail[$i]['Amt'],0);	
			}
		}
		
		
		//If society refund Gst in Credit note then this code execue
		if($IsGstRefund == 1)
		{
			if($CGST <> 0)
			{
				//Making CGST entries;
				$SrNo = $SrNo + 1;
				
				if(!empty($GUID))
				{
					$CGSTVoucherID = $this->obj_voucher->SetVoucherDetails_WithGUID(getDBFormatDate($CreditNoteDate), $RefNo, TABLE_CREDIT_DEBIT_NOTE, $VoucherCouter, $SrNo, $VoucherType, CGST_SERVICE_TAX, $TransactionType, $CGST, 0,$ExternalCounter,$GUID);	
				}
				else
				{
					$CGSTVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($CreditNoteDate), $RefNo, TABLE_CREDIT_DEBIT_NOTE, $VoucherCouter, $SrNo, $VoucherType, CGST_SERVICE_TAX, $TransactionType, $CGST, 0,$ExternalCounter);
				}
				$regResult = $this->obj_register->SetRegister(getDBFormatDate($CreditNoteDate), CGST_SERVICE_TAX, $CGSTVoucherID, $VoucherType, $TransactionType, $CGST,0);
			}
			
			if($SGST <> 0)
			{
				//Making CGST entries;
				$SrNo = $SrNo + 1;
				if(!empty($GUID))
				{
					$SGSTVoucherID = $this->obj_voucher->SetVoucherDetails_WithGUID(getDBFormatDate($CreditNoteDate), $RefNo, TABLE_CREDIT_DEBIT_NOTE, $VoucherCouter, $SrNo, $VoucherType, SGST_SERVICE_TAX, $TransactionType, $SGST, 0,$ExternalCounter,$GUID);
				}
				else
				{
					$SGSTVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($CreditNoteDate), $RefNo, TABLE_CREDIT_DEBIT_NOTE, $VoucherCouter, $SrNo, $VoucherType, SGST_SERVICE_TAX, $TransactionType, $SGST, 0,$ExternalCounter);
				}
				
				$regResult = $this->obj_register->SetRegister(getDBFormatDate($CreditNoteDate), SGST_SERVICE_TAX, $SGSTVoucherID, $VoucherType, $TransactionType, $SGST,0);		
			}

			if($RoundOffAmt <> 0)
			{
				//Making CGST entries;
				$SrNo = $SrNo + 1;
				if(!empty($GUID))
				{
					$RoundOffLedgerVoucherID = $this->obj_voucher->SetVoucherDetails_WithGUID(getDBFormatDate($CreditNoteDate), $RefNo, TABLE_CREDIT_DEBIT_NOTE, $VoucherCouter, $SrNo, $VoucherType, ROUND_OFF_LEDGER, $TransactionType, $RoundOffAmt, 0,$ExternalCounter,$GUID);
				}
				else
				{
					$RoundOffLedgerVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($CreditNoteDate), $RefNo, TABLE_CREDIT_DEBIT_NOTE, $VoucherCouter, $SrNo, $VoucherType, ROUND_OFF_LEDGER, $TransactionType, $RoundOffAmt, 0,$ExternalCounter);
				}
				
				$regResult = $this->obj_register->SetRegister(getDBFormatDate($CreditNoteDate), ROUND_OFF_LEDGER, $RoundOffLedgerVoucherID, $VoucherType, $TransactionType, $RoundOffAmt	,0);		
			}
		}
		
		//Inserting data on asset table		
		
		if($NoteType == CREDIT_NOTE)
		{
			$SrNo = $SrNo + 1;
			
			if(!empty($GUID))
			{
				$AssetVoucherID = $this->obj_voucher->SetVoucherDetails_WithGUID(getDBFormatDate($CreditNoteDate), $RefNo, TABLE_CREDIT_DEBIT_NOTE, $VoucherCouter, $SrNo, VOUCHER_CREDIT_NOTE, $UnitID, TRANSACTION_CREDIT, $Total,0,$ExternalCounter,$GUID);			
			}
			else
			{
				$AssetVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($CreditNoteDate), $RefNo, TABLE_CREDIT_DEBIT_NOTE, $VoucherCouter, $SrNo, VOUCHER_CREDIT_NOTE, $UnitID, TRANSACTION_CREDIT, $Total,0,$ExternalCounter);			
			}
			$regResult = $this->obj_register->SetRegister(getDBFormatDate($CreditNoteDate), $UnitID, $AssetVoucherID, VOUCHER_CREDIT_NOTE, TRANSACTION_CREDIT, $Total,0);	
		}
		
		if($IsCallUpdtCnt == 1)
		{
			$this->m_objUtility->UpdateExVCounter($VoucherType,$ExternalCounter,0);		
		}
		
}
	
	public function AddNewSalesInvoice($Detail,$UnitID,$bill_date,$Note,$InvoiceNo,$ExitingUnitID,$IsCallUpdtCnt)
	{
			$this->SetSalesInvoiceVoucher($UnitID,$bill_date, $Detail,$Note,$InvoiceNo,$ExitingUnitID,$IsCallUpdtCnt,0);
	}
	
	public function EditSalesInvoice($Detail, $UnitID, $bill_date, $Note, $InvoiceNo, $ExitingUnitID, $IsCallUpdtCnt, $VoucherCounter, $bSkipBeginTrnx = false)
	{	
		$this->SetSalesInvoiceVoucher($UnitID, $bill_date, $Detail, $Note, $InvoiceNo, $ExitingUnitID, $IsCallUpdtCnt, $VoucherCounter, $bSkipBeginTrnx = false);
	}
		
	public function getPeriod($PeriodId)
	{
		$sqlPeriod = "Select periodtbl.type, yeartbl.YearDescription from period as periodtbl JOIN year as yeartbl ON periodtbl.YearID = yeartbl.YearID where periodtbl.ID = '" . $PeriodId . "'";
		$sqlResult = $this->m_dbConn->select($sqlPeriod);
		$msg = $sqlResult[0]['type'] . " " . $sqlResult[0]['YearDescription'];
		return $msg;
	}

	//Return values
	//1: Updated
	//2: New record inserted		
	//UpdateVoucherAndRegister_Credit(ADJUSTMENT_CREDIT, $resultCheck[0]['ID'], TABLE_BILLREGISTER);

	public function FindVoucherIdAndUpdateOrInsertInVoucherAndRegisterTable_Credit($LedgerID, $RefNo, $RefTableID, $Amount)
	{	
		//$this->ShowDebugTrace = 1;
		
		if($this->ShowDebugTrace == 1)
		{
			//echo "<BR>Inside FindVoucherIdAndUpdateOrInsertInVoucherAndRegisterTable_Credit LedgerID:" .  $LedgerID . "  Amount : " . $RefNo . "<BR>" ;
		}
			//For one VoucherNo, there are mutiple records in voucher table with VoucherID for each line of the bill
		//Check if record exist for given Ledger in the Voucher
		$sqlVoucher= "select `id`, `Debit`, `Credit` from `voucher` where `To`= '" . $LedgerID ."' and `RefNo` ='" . $RefNo . "'  and `RefTableID` = '" . $RefTableID . "' ";
		$resultVoucher = $this->m_dbConn->select($sqlVoucher);
		$voucherID = 0;
		if($this->ShowDebugTrace == 1)
		{
			//echo "Voucher object <BR>";
			//print_r($resultVoucher);
		}
		if($resultVoucher <> '')
		{
			//Record exist in the database
			if($resultVoucher[0]['id'] <> "")
			{
				$voucherID = $resultVoucher[0]['id'];
				//echo "<BR>Record found in voucher for  LedgerID: " . $LedgerID . "  at VoucherID: " . $voucherID . "<BR>";
				if($resultVoucher[0]['Credit'] <>  $Amount)
				{
					//Voucher exist and amount is different.. should update
					//echo "<BR>Test 1<BR>"; //Tested
					//echo "<BR>LedgerID: " . $LedgerID . " Old value " . $resultVoucher[0]['Credit'] . " and new value :".  $Amount . " are diff . existing voucher : " . $voucherID . " record updated<BR>";
					return $this->UpdateElseInsertVoucherAndRegister_Credit($voucherID, $LedgerID, $RefNo, $RefTableID, $Amount);
				}
				else
				{
					//echo "<BR>Test 2<BR>"; //Tested
					//echo "<BR>LedgerID: " . $LedgerID . " Old and new values are same :".  $Amount . " record not updated<BR>";
					return 0;
				}
			}
			else
			{
					//echo "<BR>Test 3<BR>";
			}
			
		}
		else
		{
			//echo "<BR>LedgerID: " . $LedgerID . " no prev record exist<BR>";
		}
		if($voucherID == 0 and $Amount == 0)
		{
			//No Record exist in the database and amount is 0
			echo "<BR>Test 4<BR>";
			echo "<BR>voucherID == 0 and Amount == 0<BR>";
			echo "<BR>LedgerID: " . $LedgerID . " new value is :".  $Amount . " and no prev record exist, so not updated<BR>";
			return;
		}
		echo "<BR>Test 5<BR>";
		//echo "<BR>LedgerID: " . $LedgerID . " VoucherID : " . $voucherID . " new value is :".  $Amount . "<BR>";
		$this->UpdateElseInsertVoucherAndRegister_Credit($voucherID, $LedgerID, $RefNo, $RefTableID, $Amount);

		return;
	}
	//If voucherID == 0 then new record would be inserted, so if you are not sure if ledger exist in Voucher then call FindVoucherIdAndUpdateOrInsertInVoucherAndRegisterTable_Credit
	public function UpdateElseInsertVoucherAndRegister_Credit($voucherID, $LedgerID, $RefNo, $RefTableID, $Amount)
	{	
		//$this->ShowDebugTrace = 1;
		
		if($this->ShowDebugTrace == 1)
		{
			//echo "Inside UpdateElseInsertVoucherAndRegister_Credit voucherID:" .  $voucherID . "  LedgerID:" .  $LedgerID . "  Amount : " . $Amount . "  RefNo : " . $RefNo ." <BR>" ;
		}
		//For one VoucherNo, there are mutiple records in voucher table with VoucherID for each line of the bill
		//Check if record exist for given Ledger in the Voucher
		if($voucherID <> 0)
		{
			if($this->ShowDebugTrace == 1)
			{
				//echo "<BR>Updating VoucherID " . $voucherID . "  LedgerID:" . $LedgerID . " Amount : " . $Amount . "<BR>";
			}
			$sqlUpdate6 = "UPDATE `voucher` SET `Credit`= '" . $Amount ."' where `To`= '" . $LedgerID ."' and `RefNo` ='" . $RefNo . "' and `RefTableID` = '" . $RefTableID . "' and `id`= '" . $voucherID ."'";
			//$sqlUpdate6 = "UPDATE `voucher` SET `Credit`= '" . $Amount ."' where `To`= '" . $LedgerID ."' and `RefNo` ='" . $RefNo . "' and `RefTableID` = '" . $RefTableID . "' ";
			if($this->ShowDebugTrace == 1)
			{
				//echo $sqlUpdate6 ;
			}
			$resultUpdate6 = $this->m_dbConn->update($sqlUpdate6);
			$this->obj_register->UpdateRegister($LedgerID, $voucherID, TRANSACTION_CREDIT, $Amount); 
			return 1;
		}
		else
		{
			
			if($this->ShowDebugTrace == 1)
			{
				//echo "<BR>Inserting LedgerID:" . $LedgerID . " Amount : " . $Amount . "RefNo".$RefNo."<BR>";
			}
			$sql = 'SELECT max(SrNo) as "M_SrNo", `Date`, `RefNo`,`RefTableID`,`VoucherNo`, `VoucherTypeID` FROM `voucher` WHERE `RefNo` = "'. $RefNo .'" AND `RefTableID` = "' . $RefTableID . '" ';
			$voucherDetails = $this->m_dbConn->select($sql);
//			$obj_voucher = new voucher($this->m_dbConn);
//			$obj_register = new regiser($this->m_dbConn);
			$voucherID = $this->obj_voucher->SetVoucherDetails($voucherDetails[0]['Date'],$voucherDetails[0]['RefNo'],$voucherDetails[0]['RefTableID'],$voucherDetails[0]['VoucherNo'],$voucherDetails[0]['M_SrNo'] + 1,$voucherDetails[0]['VoucherTypeID'],$LedgerID, TRANSACTION_CREDIT,$Amount,"EditBill");
			if($this->ShowDebugTrace == 1)
			{
				//echo "<BR>Inserting new voucher row " . $voucherID . "  LedgerID:" . $LedgerID . " Amount : " . $Amount . "<BR>";
			}
			$this->obj_register->SetRegister($voucherDetails[0]['Date'], $LedgerID, $voucherID, $voucherDetails[0]['VoucherTypeID'], TRANSACTION_CREDIT, $Amount); 
			return 2;
		}
		return 0;
	}	
	
	public function comboboxEx($query)
	{
		$id=0;
		//echo "<script>alert('test')<//script>";
		$str.="<option value=''>Please Select</option>";
	$data = $this->m_dbConn->select($query);
	//echo "<script>alert('test2')<//script>";
		if(!is_null($data))
		{
			$vowels = array('/', '*', '%', '&', ',', '(', ')', '"');
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
						$str.= str_replace($vowels, ' ', $v)."</OPTION>";
						//$str.=$v."</OPTION>";
					}
					//echo "<script>alert('".$str."')<//script>";
					$i++;
				}
			}
		}
		//return $str;
		//print_r( $str);
		//echo "<script>alert('test')<//script>";
		return $str;
	}
	
	function SetReminderSMSDetails($society_id, $period_id)
	{
		// "First we comfirm whether Send SMS reminder is set in society page or not";
		$IsSetSMSReminder = " SELECT send_reminder_sms FROM society WHERE society_id = '".$_SESSION['society_id']."' ";
		$ResultofIsSetSMSReminder = $this->m_dbConn->select($IsSetSMSReminder);
		
		//If in society sms reminder is set the this code execute
		if($ResultofIsSetSMSReminder[0]['send_reminder_sms'] == 1)
		{
			//Get bill due date
			$sqlDueDate = "SELECT `DueDateToDisplay` from billregister WHERE SocietyID = '" . $society_id . "' and PeriodID = '" . $period_id . "' ORDER BY ID DESC LIMIT 1";
			$sqlDueDateResult = $this->m_dbConn->select($sqlDueDate);
			$dueDate = $sqlDueDateResult[0]['DueDateToDisplay'];	
			if($dueDate <> '')
			{
				//Insert or Update Reminder date in `remindersms` table	
				$countQuery = "SELECT count(ID) AS `cnt` FROM `remindersms` WHERE `society_id` = '" . $this->m_dbConn->escapeString($society_id). "' AND `PeriodId` = '" . $this->m_dbConn->escapeString( $period_id). "'";
				$count = $this->m_dbConnRoot->select($countQuery);
				
				//Select days to set remiderdays before due date 			
				$DateDiffReminder = " SELECT SMS_Reminder_Days from society where society_id = '".$_SESSION['society_id']."'";
				$ResultDateDiffReminder = $this->m_dbConn->select($DateDiffReminder);
				$ReminderDays = -$ResultDateDiffReminder[0]['SMS_Reminder_Days'];
				//$ReminderDays = -2;
 				//Get Reminder_Day from Society Level
/*				$societyInfo = $this->m_objUtility->GetSocietyInformation($_SESSION['society_id']);
				//Add sms_reminder_days in the database with default to 5
				$ReminderDays = $societyInfo['sms_reminder_days'] * 1;
				if($dueDate <> '')
				{
					$ReminderDays = -5;
				}
	*/
	
				$reminderDate = $this->m_objUtility->GetDateByOffset2($dueDate, $ReminderDays);
				//echo $reminderDate = $this->GetDateByOffset($dueDate, $ReminderDays);

				//m_objUtility
				if($count[0]['cnt'] == 0)
				{																					
				$sqlReminderQuery = "INSERT INTO `remindersms`(`society_id`, `PeriodID`, `ReminderType`, `EventDate`, `EventReminderDate`, `LoginID`) VALUES ('" . $this->m_dbConn->escapeString($society_id). "',
								 '" . $this->m_dbConn->escapeString($period_id). "','".SENDBILLREMINDER."','" . getDBFormatDate($this->m_dbConn->escapeString($dueDate)) . "','" . getDBFormatDate($this->m_dbConn->escapeString($reminderDate)) . "',
								 '".$_SESSION['login_id']."')";												
					$this->m_dbConnRoot->insert($sqlReminderQuery);
				}
				else
				{
					$updateTable = "UPDATE `remindersms` SET `EventDate`='" . getDBFormatDate($this->m_dbConn->escapeString($dueDate)) . "',
						`EventReminderDate`='" . getDBFormatDate($this->m_dbConn->escapeString($reminderDate)) . "', `LoginID` = '".$_SESSION['login_id']."', `ReminderType` = '".SENDBILLREMINDER."'  WHERE `society_id`='" . $this->m_dbConn->escapeString($society_id). "' AND
						`PeriodID`='" . $this->m_dbConn->escapeString($period_id). "'";								
					$this->m_dbConnRoot->update($updateTable);
				}	
			}
		}
	}
	
	
	public function unsetZeroKeysFromArray($data , $checkZero)
	{
		foreach($checkZero as $key => $value)
		{
			if($checkZero[$key] == false)
			{
				$this->recursiveRemoval($data, $key);
			}
		}
		return $data;
		
	}
	
	function recursiveRemoval(&$array, $keyToRemove)
	{
		if(is_array($array))
		{
			foreach($array as $key => &$arrayElement)
			{
				if(is_array($arrayElement))
				{
					$this->recursiveRemoval($arrayElement, $keyToRemove);
				}
				else
				{
					if($key == $keyToRemove)
					{
						unset($array[$key]);
					}
				}
			}
		}
	}
	
	
function getCollectionOfDataToDisplay_optimize($society_id, $wing_id, $unit_id, $period_id,$isBillSummary = false, $SupplementaryBill)
	{
		$sqlPeriod = "SELECT max(id),PeriodID,BillDate,DueDate FROM `billregister` where `PeriodID` = '".$period_id."' and BillType='".$SupplementaryBill."' ";
		$resultPeriod = $this->m_dbConn->select($sqlPeriod);
		$prevPeriod = $this->getPreviousPeriodData($period_id);
		
		$sqlPymtQuery2 = "Select Type, YearID, PrevPeriodID, Status, BeginingDate, EndingDate from period where ID=" . $period_id;
		$Prevresult2 = $this->m_dbConn->select($sqlPymtQuery2);
				
		$sql = "SELECT `id`,`ledger_name` FROM `ledger`  WHERE `id` IN(SELECT vch.`To` from `voucher` as vch JOIN `billdetails` as bill on bill.ID = vch.`RefNo` where `To` <> '' and `RefTableID`=1 and BillType = '". $SupplementaryBill . "' and bill.`PeriodID` = '".$period_id."' ) and `id` NOT IN(".INTEREST_ON_PRINCIPLE_DUE.",".ADJUSTMENT_CREDIT.",".IGST_SERVICE_TAX.",".CGST_SERVICE_TAX.",".SGST_SERVICE_TAX.",".CESS_SERVICE_TAX.", ".ROUND_OFF_LEDGER.") ";
		$result = $this->m_dbConn->select($sql);
		
		//array_push($result,array('id' => INTEREST_ON_PRINCIPLE_DUE ,'ledger_name'  => "iNTEREST ON ARREARS"));
		//array_push($result,array('id' => ADJUSTMENT_CREDIT ,'ledger_name'  => "ADJUSTMENT CREDIT"));
			$societyInfo = $this->m_objUtility->GetSocietyInformation($_SESSION['society_id']);
			$ApplyServiceTax = $societyInfo['apply_service_tax'];
			if($ApplyServiceTax == 1)
			{
		$sqlII = "SELECT  IF(vch.`RefNo` > 0,' ',' ')  as '',vch.`RefNo`, uni.unit_no as `UNIT NO`, mem.owner_name as `OWNER NAME`,mem.owner_gstin_no as `OWNER GSTIN`,uni.area as AREA, bill.BillNumber as `BILL NUMBER`, ";
			}
			else
			{
				$sqlII = "SELECT  IF(vch.`RefNo` > 0,' ',' ')  as '',vch.`RefNo`, uni.unit_no as `UNIT NO`, mem.owner_name as `OWNER NAME`,uni.area as AREA, bill.BillNumber as `BILL NUMBER`, ";
			}
		for($m = 0; $m < sizeof($result); $m++)
		{
			$sqlII	.= " SUM( IF( vch.`To` =  '".$result[$m]['id']."', vch.`Credit` , 0.00 ) ) AS '".$result[$m]['ledger_name']."' ";
			
			if($m < sizeof($result))
			{
				$sqlII	.= " , ";
			}
		}
		
		/*$sqlII	.="	sum(vch.credit), sum(chq.Amount), (sum(vch.credit) - sum(chq.Amount)),bill.`BillSubTotal` ,bill.`BillInterest` as InterestOnArrears,
							bill.`AdjustmentCredit` , bill.`PrincipalArrears` as PreviousPrincipalArrears , bill.`InterestArrears` as PreviousInterestArrears,
							(bill.`BillSubTotal` + bill.`AdjustmentCredit`  + bill.`BillInterest` + bill.`PrincipalArrears` + bill.`InterestArrears`) as Payable
							FROM voucher AS vch JOIN billdetails AS bill ON vch.RefNo = bill.ID JOIN member_main AS mem ON bill.UnitID = mem.unit
							JOIN unit AS uni ON uni.unit_id = bill.UnitID LEFT JOIN chequeentrydetails as chq on bill.UnitID = chq.PaidBy WHERE bill.PeriodID ='".$period_id."' ";*/
		/*$sqlII	.="	bill.`BillSubTotal` ,bill.`BillInterest` as InterestOnArrears,
							bill.`AdjustmentCredit` , bill.`PrincipalArrears` as PreviousPrincipalArrears , bill.`InterestArrears` as PreviousInterestArrears,
							(bill.`BillSubTotal` + bill.`AdjustmentCredit`  + bill.`BillInterest` + bill.`PrincipalArrears` + bill.`InterestArrears`) as Payable
							FROM voucher AS vch JOIN billdetails AS bill ON vch.RefNo = bill.ID JOIN member_main AS mem ON bill.UnitID = mem.unit
							JOIN unit AS uni ON uni.unit_id = bill.UnitID LEFT JOIN chequeentrydetails as chq on bill.UnitID = chq.PaidBy WHERE bill.PeriodID ='".$period_id."' ";	*/
							//$societyInfo = $this->m_objUtility->GetSocietyInformation($_SESSION['society_id']);
		//$ApplyServiceTax = $societyInfo['apply_service_tax'];
		if($ApplyServiceTax == 1)
		{
			$sqlII	.="	bill.`BillSubTotal` as `Bill Sub Total` ,bill.`BillInterest` as `Interest On Arrears`,
								bill.`AdjustmentCredit` as `Adjustment Credit` , bill.`CGST` as CGST , bill.`SGST` as SGCT , bill.`Ledger_round_off` as `Round_Off_Amt`, bill.`PrincipalArrears` as `Previous Principal Arrears` , bill.`InterestArrears` as `Previous Interest Arrears` , bill.`PaymentReceived` as `Payment Received`,
								(bill.`BillSubTotal` + bill.`AdjustmentCredit`  + bill.`BillInterest` + bill.`PrincipalArrears` + bill.`InterestArrears` + bill.`CGST` + bill.`SGST` + bill.`Ledger_round_off`) as Payable
								FROM voucher AS vch JOIN billdetails AS bill ON vch.RefNo = bill.ID JOIN member_main AS mem ON bill.UnitID = mem.unit
								JOIN unit AS uni ON uni.unit_id = bill.UnitID WHERE bill.BillType = '". $SupplementaryBill . "' and bill.PeriodID ='".$period_id."' ";										
		}
		else
		{
			$sqlII	.="	bill.`BillSubTotal` as `Bill Sub Total` ,bill.`BillInterest` as `Interest On Arrears`,
								bill.`AdjustmentCredit` as `Adjustment Credit` , bill.`Ledger_round_off` as `Round_Off_Amt`, bill.`PrincipalArrears` as `Previous Principal Arrears` , bill.`InterestArrears` as `Previous Interest Arrears`, bill.`PaymentReceived` as `Payment Received`,
								(bill.`BillSubTotal` + bill.`AdjustmentCredit`  + bill.`BillInterest` + bill.`PrincipalArrears` + bill.`InterestArrears` + bill.`Ledger_round_off`) as Payable
								FROM voucher AS vch JOIN billdetails AS bill ON vch.RefNo = bill.ID JOIN member_main AS mem ON bill.UnitID = mem.unit
								JOIN unit AS uni ON uni.unit_id = bill.UnitID WHERE bill.BillType = '". $SupplementaryBill . "' and bill.PeriodID ='".$period_id."' ";										
		}
		if($unit_id == 0)
		{
			$memberIDS = $this->m_objUtility->getMemberIDs($resultPeriod[0]['BillDate']);
			$sqlII	.="	and mem.member_id IN (".$memberIDS.") ";
		}
		else
		{
			$sqlII   .= "   and uni.unit_id = '" . $unit_id . "'";
		}
		
		$sqlII   .= "    and bill.BillType='".$SupplementaryBill."' ";
		$sqlII	.="	GROUP BY vch.`RefNo`  ORDER BY uni.sort_order ASC";
		
		$resData = $this->m_dbConn->select($sqlII);
		
		/********** CALCULATE SUM OF LEDGERS *************/
		$sumArray = array();

		foreach ($resData as $k=>$subArray) 
		{
  			foreach ($subArray as $id=>$value) 
			{
    			$sumArray[$id]+=$value;
  			}
		}
		
		$sumArray[''] = "";
		$sumArray['UNIT_NO'] = " ";
		$sumArray['OWNER_NAME'] = "Total";
		$sumArray['BillNumber'] = " ";
		array_push($resData,$sumArray);
		
		/********* REMOVE REFNO FROM ARRAY ******/
		$this->recursiveRemoval($resData, 'RefNo');
		
		return $resData;
	}		

	public function getCollectionOfDataForContributionLedger_optimize($society_id, $wing_id, $unit_id, $period_id,$isBillSummary = false, $SupplementaryBill)
	{
		$sqlPeriod  = "Select ID from `period` where YearID = '" . $_SESSION['default_year'] . "'";
		$resPeriod = $this->m_dbConn->select($sqlPeriod);

		$iFirst = true;
		
		for($iCntr = 0 ; $iCntr < sizeof($resPeriod); $iCntr++)
		{
			if($iFirst == false)
			{
				$periodList .= ',';
			}
			$iFirst = false;
			$periodList .= $resPeriod[$iCntr]['ID'];
		}

		//echo $periodList;

		//$sql = "SELECT `id`,`ledger_name` FROM `ledger`  WHERE `id` IN(SELECT vch.`To` from `voucher` as vch JOIN `billdetails` as bill on bill.ID = vch.`RefNo` where `To` <> '' and `RefTableID`=1 and bill.`PeriodID` IN (" . $periodList . ") ) and `id` NOT IN(".INTEREST_ON_PRINCIPLE_DUE.",".ADJUSTMENT_CREDIT.") ";
		
		$sql = "SELECT `id`,`ledger_name` FROM `ledger`  WHERE `id` IN(SELECT vch.`To` from `voucher` as vch JOIN `billdetails` as bill on bill.ID = vch.`RefNo` where `To` <> '' and `RefTableID`=1 and BillType = '" . $SupplementaryBill . "' and bill.`PeriodID` IN (" . $periodList . ") ) and `id` NOT IN(".INTEREST_ON_PRINCIPLE_DUE.",".ADJUSTMENT_CREDIT.",".IGST_SERVICE_TAX.",".CGST_SERVICE_TAX.",".SGST_SERVICE_TAX.",".CESS_SERVICE_TAX.") ";

		$result = $this->m_dbConn->select($sql);
		
		$sqlII = "SELECT  vch.`RefNo`, uni.`unit_id`, uni.`unit_no` as 'Unit No', mem.`owner_name` as 'Member Name', bill.`BillNumber`, yr.`YearDescription` as 'Fin. Year', prd.`Type` as 'Bill For', prd.`ID` as 'PeriodID', ";
		
		for($m = 0; $m < sizeof($result); $m++)
		{
			$sqlII	.= " SUM( IF( vch.`To` =  '".$result[$m]['id']."', vch.`Credit` , 0.00 ) ) AS '".$result[$m]['ledger_name']."' ";
			
			if($m < sizeof($result))
			{
				$sqlII	.= " , ";
			}
		}

		$societyInfo = $this->m_objUtility->GetSocietyInformation($_SESSION['society_id']);
		$ApplyServiceTax = $societyInfo['apply_service_tax'];
		if($ApplyServiceTax == 1)
		{
			$sqlII	.="	bill.`BillSubTotal` ,bill.`BillInterest` as InterestOnArrears,
							bill.`AdjustmentCredit`, bill.`CGST` as CGST , bill.`SGST` as SGCT , bill.`PrincipalArrears` as PreviousPrincipalArrears , bill.`InterestArrears` as PreviousInterestArrears,
							(bill.`BillSubTotal` + bill.`AdjustmentCredit`  + bill.`BillInterest` + bill.`PrincipalArrears` + bill.`InterestArrears` + bill.`CGST` + bill.`SGST` ) as Payable
							FROM voucher AS vch JOIN billdetails AS bill ON vch.RefNo = bill.ID JOIN member_main AS mem ON bill.UnitID = mem.unit
							JOIN unit AS uni ON uni.unit_id = bill.UnitID JOIN year as yr ON yr.YearID = '".$_SESSION['default_year']."'  JOIN period as prd on bill.PeriodID = prd.ID WHERE bill.BillType = '". $SupplementaryBill . "' and bill.PeriodID IN (" . $periodList . ") AND mem.ownership_status = 1";								
		}
		else
		{
			$sqlII	.="	bill.`BillSubTotal` ,bill.`BillInterest` as InterestOnArrears,
							bill.`AdjustmentCredit`, bill.`PrincipalArrears` as PreviousPrincipalArrears , bill.`InterestArrears` as PreviousInterestArrears,
							(bill.`BillSubTotal` + bill.`AdjustmentCredit`  + bill.`BillInterest` + bill.`PrincipalArrears` + bill.`InterestArrears`) as Payable
							FROM voucher AS vch JOIN billdetails AS bill ON vch.RefNo = bill.ID JOIN member_main AS mem ON bill.UnitID = mem.unit
							JOIN unit AS uni ON uni.unit_id = bill.UnitID JOIN year as yr ON yr.YearID = '".$_SESSION['default_year']."'  JOIN period as prd on bill.PeriodID = prd.ID WHERE bill.BillType = '". $SupplementaryBill . "' and  bill.PeriodID IN (" . $periodList . ") AND mem.ownership_status = 1";										
		}										
							
		if($unit_id == 0)
		{
			$memberIDS = $this->m_objUtility->getMemberIDs($resultPeriod[0]['BillDate']);
			//$sqlII	.="	and mem.member_id IN (".$memberIDS.") ";
		}
		else
		{
			$sqlII   .= " and uni.unit_id = '" . $unit_id . "'";
		}
		
		$sqlII   .= "    and bill.BillType='".$SupplementaryBill."' ";
		$sqlII	.="	GROUP BY vch.`RefNo`  ORDER BY uni.sort_order,bill.PeriodID ASC";
		
		$resData = $this->m_dbConn->select($sqlII);
		
		/********** CALCULATE SUM OF LEDGERS *************/
		$sumArray = array();


		for($iCnt = 0; $iCnt < sizeof($resData); $iCnt++)
		{
			$sqlPeriod = "SELECT max(id),PeriodID,BillDate,DueDate FROM `billregister` where `PeriodID` = '".$resData[$iCnt]['PeriodID']."' and BillType='".$SupplementaryBill."' ";
			$resultPeriod = $this->m_dbConn->select($sqlPeriod);
			$prevPeriod = $this->getPreviousPeriodData($resData[$iCnt]['PeriodID']);
			
			$sqlPymtQuery2 = "Select Type, YearID, PrevPeriodID, Status, BeginingDate, EndingDate from period where ID='" . $resData[$iCnt]['PeriodID'] . "'";

			$Prevresult2 = $this->m_dbConn->select($sqlPymtQuery2);

			$paymentReceived = $this->GetPaymentsReceived($resData[$iCnt]['unit_id'], $resData[$iCnt]['PeriodID'], $prevPeriod, $Prevresult2[0]['BeginingDate'], $Prevresult2[0]['EndingDate']);
			

			$iPaidDetails = '';
			$iPaidTotal = 0;
			for($iPaidCnt = 0 ; $iPaidCnt < sizeof($paymentReceived) ; $iPaidCnt++)
			{
				$iPaidDetails .= ($iPaidCnt + 1) . ' - ' . $paymentReceived[$iPaidCnt] . '<br />';
				$iPaidTotal +=  $paymentReceived[$iPaidCnt]['Amount'];
			}
			
			if($isBillSummary == true)
			{
				$resData[$iCnt]['Paid'] = $iPaidTotal;
				$resData[$iCnt]['Balance'] = $resData[$iCnt]['Payable'] - $iPaidTotal;
				if($resData[$iCnt]['Balance'] < 0 )
				{
					$resData[$iCnt]['Balance'] = number_format($resData[$iCnt]['Balance'],2).' Cr'; 		
				}
				else
				{
					$resData[$iCnt]['Balance'] = number_format($resData[$iCnt]['Balance'],2).' Dr'; 		
				}
				$resData[$iCnt]['Payable'] = $resData[$iCnt]['Payable'];
			}
		}
		
		foreach ($resData as $k=>$subArray) 
		{
			foreach ($subArray as $id=>$value) 
			{
    			$sumArray[$id]+=$value;
  			}
		}
		
		//$sumArray[''] = "";
		$sumArray['Unit No'] = $resData[0]['Unit No'];
		$sumArray['Member Name'] = "Total";
		$sumArray['BillNumber'] = " ";
		$sumArray['Fin. Year'] = " ";
		$sumArray['Bill For'] = " ";
		$sumArray['Payable'] = " ";
		$sumArray['Paid'] = " ";
		$sumArray['Balance'] = " ";

		array_push($resData,$sumArray);
		
		/********* REMOVE REFNO FROM ARRAY ******/
		$this->recursiveRemoval($resData, 'RefNo');
		$this->recursiveRemoval($resData, 'unit_id');
		$this->recursiveRemoval($resData, 'PeriodID');
		
		return $resData;
	}		
	
	public function setMaintenanceBillReadUnreadFlag($UnitID,$PeriodID,$BillType)
	{
		//Maintenance Bill email read-unread flag
		if($_SESSION['unit_id'] == $UnitID && ($_SESSION['role'] == ROLE_MEMBER || $_SESSION['role'] == ROLE_ADMIN_MEMBER))
		{
			date_default_timezone_set('Asia/Kolkata');
			$readDate = date('Y-m-d h:i:s');

			$member_other_id = $_SESSION['mem_other_id'];

			$updateTable = "UPDATE `billdetails` SET `Bill_Read_Timestamp`= '" . $readDate .  "', `Mem_Other_ID` = '" . $member_other_id . "', `Login_ID` = '" . $_SESSION['login_id'] . "' where `UnitID` = '".$UnitID."' and `PeriodID` = '".$PeriodID."' and `BillType` ='".$BillType."' and `Bill_Read_Timestamp` = 0 ";								
			$this->m_dbConn->update($updateTable);
		}
		else
		{
			//do nothing
		}
	}
	
	

	function ServiceTaxImplement($GSTLedger,$ServiceTaxAmount,$refNo,$ApplyServiceTax, $RefTableID)
	{
		//$this->ShowDebugTrace = 1;
		
		$sqlCheckST= "select * from `voucher` where `To`= '" . $GSTLedger ."' and `RefNo` ='" . $refNo . "'  and `RefTableID` = '" . $RefTableID . "'";
		$resultST = $this->m_dbConn->select($sqlCheckST);

		if($this->ShowDebugTrace == 1)
		{
			//echo "<BR>Inside ServiceTaxImplement for :" . $GSTLedger . "<BR>";
			//echo $sqlCheckST;
			//echo "<BR>";
			//print_r($resultST);
			//echo "<BR>VoucherID :" . $resultST[0]['id'] . "<BR>";
		}
//		echo "<BR>GroupID :". $GroupID . "  LedgerID : " . $ledgerID . " VoucherID :" . $resultST[0]['id'] . "   Amount :" . $ServiceTaxAmount . "<BR>";
		$voucherID = $resultST[0]['id'];
		if($resultST[0]['id']  <> "")
		{
			if($this->ShowDebugTrace == 1)
			{
				echo "<BR>Exist in voucher with id:" .  $resultST[0]['id'] . "<BR>";
			}
			if($ServiceTaxAmount == 0) //pending delete even $ServiceTaxAmount = 0
			{
				if($this->ShowDebugTrace == 1)
				{
					echo "Deleting voucherID:" . $voucherID . " from voucher and incomeregister and liabilityregister<BR>";
				}
				//No Tax.. so delete the one existing earlier
				$sqlDeleteST = "DELETE from `voucher` WHERE `id` = '" . $voucherID  . "'";
				$resultDeleteST = $this->m_dbConn->delete($sqlDeleteST);

				$sqlDeleteST = "DELETE from `incomeregister` WHERE `VoucherID` = '" . $voucherID  . "'";
				$resultDeleteST = $this->m_dbConn->delete($sqlDeleteST);
				//if($resultDeleteST==0)
				{
					$sqlDeleteST = "DELETE from `liabilityregister` WHERE `VoucherID` = '" . $voucherID  . "'";
					$resultDeleteST = $this->m_dbConn->delete($sqlDeleteST);
				}
				return;
			}
		}			
		$this->UpdateElseInsertVoucherAndRegister_Credit($voucherID, $GSTLedger, $refNo, $RefTableID, $ServiceTaxAmount);
						
	}
	
	private function InsertInvoiceVoucherRegister($Inv_Date,$InsertID,$iVoucherCouter,$UnitID,$iTotalAmount,$resultFetch,$CGST,$SGST,$ExternalCounter,$IsCallUpdtCnt, $RoundOffAmt)
	{
		echo '<BR> Inside the InsertiInVoiceVoucherRegister';
		$iSrNo = 1;
		$sqlVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($Inv_Date), $InsertID, TABLE_SALESINVOICE, $iVoucherCouter, $iSrNo, VOUCHER_JOURNAL, $UnitID, TRANSACTION_DEBIT, $iTotalAmount,0,$ExternalCounter);
		echo 'ExternalCounter '.$ExternalCounter;
		echo 'IsCallUpdtCnt'.$IsCallUpdtCnt;
		if($IsCallUpdtCnt == 1)
		{
			$this->m_objUtility->UpdateExVCounter(VOUCHER_INVOICE,$ExternalCounter,0);		
		}

		
			if ($this->ShowDebugTrace == 1)
			{
				//echo '<br/>After SetVoucherDetails: ' . $sqlVoucherID . "<BR>";
			}
			
//			$obj_register = new regiser($this->m_dbConn);
			if(isset($UnitID) && $UnitID <> '')
			{
				$FetchUnitDetails = "Select u.wing_id,w.wing from unit as u join wing as w ON w.id = u.wing_id where unit_id ='".$UnitID."'";
			}
		 	$regResult = $this->obj_register->SetRegister(getDBFormatDate($Inv_Date), $UnitID, $sqlVoucherID, VOUCHER_JOURNAL, TRANSACTION_DEBIT, $iTotalAmount, 0);
			if ($this->ShowDebugTrace == 1)
			{
				//echo '<br/>After SetRegister: ' . $regResult . "<BR>";
			}
			
			if ($this->ShowDebugTrace == 1)
			{
				var_dump($resultFetch);
			}
			
			if($resultFetch <> '')
			{
				foreach($resultFetch as $k=>$v)
				{
					//echo '<br>ID : ' . $sqlResult[$k]['AccountHeadID'] . ' Amount : ' . $sqlResult[$k]['AccountHeadAmount'];
					$this->strTrace .=  '<tr><td>ID : ' . $resultFetch[$k]['Head'] . ' Amount : ' . $resultFetch[$k]['Amt'].'</td></tr>';
					
					if($resultFetch[$k]['Head'] <> '' || $resultFetch[$k]['Amt'] <> 0.00)
					{
						$iSrNo = $iSrNo + 1;
						
						$sqlVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($Inv_Date), $InsertID, TABLE_SALESINVOICE, $iVoucherCouter, $iSrNo, VOUCHER_JOURNAL, $resultFetch[$k]['Head'], TRANSACTION_CREDIT, $resultFetch[$k]['Amt'],0,$ExternalCounter);
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>Head " . $resultFetch[$k]['Head'] . "  Amount : " . $resultFetch[$k]['Amt'] . "<BR>";
						}
						$regResult = $this->obj_register->SetRegister(getDBFormatDate($Inv_Date), $resultFetch[$k]['Head'], $sqlVoucherID, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $resultFetch[$k]['Amt']);
					}
				}
				{
					if($CGST <> 0)
					{
						$iSrNo = $iSrNo + 1;					
						$sqlVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($Inv_Date), $InsertID, TABLE_SALESINVOICE, $iVoucherCouter, $iSrNo, VOUCHER_JOURNAL, CGST_SERVICE_TAX, TRANSACTION_CREDIT, $CGST,0,$ExternalCounter);
	
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>Head " . CGST_SERVICE_TAX . "  Amount : " . $CGST . "<BR>";
						}
						$regResult = $this->obj_register->SetRegister(getDBFormatDate($Inv_Date), CGST_SERVICE_TAX, $sqlVoucherID, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $CGST, 0);
					}
					else
					{
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>CGST Amount is zero";	
							//pending : remove from Register
						}
					}
					
					if($SGST <> 0)
					{				
						echo "This is SGST Amt ".$SGST;	
						$iSrNo = $iSrNo + 1;					
						$sqlVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($Inv_Date), $InsertID, TABLE_SALESINVOICE, $iVoucherCouter, $iSrNo, VOUCHER_JOURNAL, SGST_SERVICE_TAX, TRANSACTION_CREDIT, $SGST,0,$ExternalCounter);
	
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>Head " . SGST_SERVICE_TAX . "  Amount : " . $SGST . "<BR>";
						}
						$regResult = $this->obj_register->SetRegister(getDBFormatDate($Inv_Date), SGST_SERVICE_TAX, $sqlVoucherID, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $SGST, 0);
					}
					else
					{
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>SGST Amount is zero<BR>";	
						}
					}

					if($RoundOffAmt <> 0)
					{				
						echo "This is Round off Amt ".$RoundOffAmt;	
						$iSrNo = $iSrNo + 1;					
						$sqlVoucherID = $this->obj_voucher->SetVoucherDetails(getDBFormatDate($Inv_Date), $InsertID, TABLE_SALESINVOICE, $iVoucherCouter, $iSrNo, VOUCHER_JOURNAL, ROUND_OFF_LEDGER, TRANSACTION_CREDIT, $RoundOffAmt,0,$ExternalCounter);
	
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>Head " . ROUND_OFF_LEDGER . "  Amount : " . $SGST . "<BR>";
						}
						$regResult = $this->obj_register->SetRegister(getDBFormatDate($Inv_Date), ROUND_OFF_LEDGER, $sqlVoucherID, VOUCHER_JOURNAL, TRANSACTION_CREDIT, $RoundOffAmt, 0);
					}
					else
					{
						if($this->ShowDebugTrace == 1)
						{
							echo "<BR>Round Amount is zero<BR>";	
						}
					}

					
			}
		}
	}
	
	public function CalculateInvoiceGst($resultFetch, $isEditMode)
	{
		$Result = array();
		$InvSubTotal = 0;
		$data = '';
		$taxableamount = "";
		$Taxabledetails = array();
		$TaxabledetailsString = '';
	
		for($i = 0; $i <= sizeof($resultFetch); $i++)
		{
			
			if($resultFetch[$i]['Head'] <> '')
			{
			$checkTaxableLedger = $resultFetch[$i]['invoicetaxable'];
			
			$TaxableLedgerHead =  $resultFetch[$i]['Head'];
			
			if($resultFetch[$i]['invoicetaxable'] == 1)
			{	
				array_push($Taxabledetails,$TaxableLedgerHead);
			}
			//echo "Checking taxable ledeger".var_dump($Taxabledetails);
		
			$TaxabledetailsString = implode(',',$Taxabledetails);
		
			//echo "This is new implode function. ".$TaxabledetailsString;
		
			if($checkTaxableLedger==1)
			{
			$taxableamount+=$resultFetch[$i]['Amt'];
			//echo "<br>taxableamount".$taxableamount;
			}
			
			$InvSubTotal+=$resultFetch[$i]['Amt'];
			
			}
		}

		$societyInfo = $this->m_objUtility->GetSocietyInformation($_SESSION['society_id']);
		$ApplyServiceTax = $societyInfo['apply_service_tax'];
		$ServiceTaxLimit = $societyInfo['service_tax_threshold'];
		$IGSTRate = $societyInfo['igst_tax_rate'];
		$CGSTRate = $societyInfo['cgst_tax_rate'];
		$SGSTRate = $societyInfo['sgst_tax_rate'];
		$CESSRate = $societyInfo['cess_tax_rate'];
		
		$CGST = 0;
		$SGST = 0;
	
		$CGST = ($taxableamount * $CGSTRate) / 100;
		//echo $CGST_percentage;
		$SGST = ($taxableamount * $SGSTRate) / 100;

		if($societyInfo['IsRoundOffLedgerAmt'] == 0){ // If in society table IsRoundOffLedgerAmt is 0 means calculate invoice total by old method 

			$Result['SubTotal'] = $this->m_objUtility->getRoundValue2($InvSubTotal);
			$Result['CGST'] = $this->m_objUtility->getRoundValue2($CGST);
			$Result['SGST'] = $this->m_objUtility->getRoundValue2($SGST);
			$Result['RoundOffAmt'] = 0;
			$Result['Total'] = $Result['SubTotal'] + $Result['CGST'] + $Result['SGST'];

		}
		else{

			$Result['SubTotal'] = $InvSubTotal;
			$Result['CGST'] = $CGST;
			$Result['SGST'] = $SGST;
			$InvoiceTotal = $InvSubTotal + $CGST + $SGST;
			$GSTRoundTotal = $this->m_objUtility->getRoundValue2($InvoiceTotal);
			$Result['RoundOffAmt'] = $GSTRoundTotal - $InvoiceTotal; 
			$Result['Total'] = $GSTRoundTotal;
		}
		
		$Result['TaxabledetailsString'] = $TaxabledetailsString; 
		return $Result;
	}
	private function SetSalesInvoiceVoucher($UnitID, $BillDate, $resultFetch,$Note, $InvoiceNo = 0, $ExitingUnitID = 0, $IsCallUpdtCnt, $ExternalVoucherNumber,$bSkipBeginTrnx = false)
	{
		$IsRequestFromImportInvoice = false;
		$CalculatedGSTDetails = 0;
		return $this->SetSalesInvoiceVoucher_final($UnitID, $BillDate, $resultFetch,$Note, $InvoiceNo, $ExitingUnitID, $IsCallUpdtCnt, $ExternalVoucherNumber,$IsRequestFromImportInvoice,$CalculatedGSTDetails,$bSkipBeginTrnx);
	}
	
	public function SetSalesInvoiceVoucher_WithImport($UnitID, $BillDate, $resultFetch,$Note, $InvoiceNo, $ExitingUnitID = 0, $IsCallUpdtCnt,$CalculatedGSTDetails,$bSkipBeginTrnx = false)
	{
		return $this->SetSalesInvoiceVoucher_final($UnitID, $BillDate, $resultFetch,$Note, $InvoiceNo, $ExitingUnitID, $IsCallUpdtCnt, $ExternalVoucherNumber = 0,true,$CalculatedGSTDetails,$bSkipBeginTrnx);
	}
	
	private function SetSalesInvoiceVoucher_final($UnitID, $BillDate, $resultFetch,$Note, $InvoiceNo = 0, $ExitingUnitID = 0, $IsCallUpdtCnt, $ExternalVoucherNumber,$IsRequestFromImportInvoice,$CalculatedGSTDetails,$bSkipBeginTrnx = false)
	{
	try
		{
			if(!$bSkipBeginTrnx)
			{
				echo "<BR> This is begin Transaction Function <BR>";			
				$this->m_dbConn->begin_transaction();
			}
		
			//$this->ShowDebugTrace = 1;
			
			$EditMode = false;
			$InsertID = 0;
		
			if($ExitingUnitID <> 0 && $ExitingUnitID <> '')
			{
				echo 'Inside the Edit mode ';
				$EditMode = true;
			}


			
			if($EditMode == true)
			{
				//Collecting data of exiting invoice
				$PreviuosDetails = $this->FetchSaleInvoice($InvoiceNo, $ExitingUnitID);
				
				$InsertID = $PreviuosDetails[0]['ID'];
				//collecting data from voucher of ledgers
				
				echo '<BR> Previous ID  '.$PreviuosDetails[0]['ID'];
				$VoucherSql = "SELECT id,VoucherNo,`To`,Credit FROM voucher where RefNo = '".$PreviuosDetails[0]['ID']."' AND RefTableID = '".TABLE_SALESINVOICE."' AND `To` != '' AND `To`  NOT IN ('".IGST_SERVICE_TAX."','".CGST_SERVICE_TAX."','".SGST_SERVICE_TAX."','".CESS_SERVICE_TAX."')";
			
				$voucherResult = $this->m_dbConn->select($VoucherSql);
				//var_dump($voucherResult);
				//Now we delete the exiting sale_invoice excepting sale_invoice table. Because in sale invoice we allow user to change ledger and unit during edit mode, so we need to create new function for edit. ** to prevent wirting new function and  to use exiting code to use update invoice details we deleted the entry;
				$this->deleteInvoice($PreviuosDetails[0]['ID'], $ExitingUnitID, $PreviuosDetails[0]['Inv_Date'],0, $bSkipBeginTrnx=false );
				
			}
			
			$iBillInterest = 0;
			$iCurrentBillAmount = 0;
			
			if($resultFetch <> '')
			{
				$iVoucherCouter = $this->obj_LatestCount->getLatestVoucherNo($_SESSION['society_id']);
				$timestamp = getCurrentTimeStamp();
				$Inv_Date = getDBFormatDate($BillDate);
				
				
				if($IsRequestFromImportInvoice == true)
				{
				//	echo '<br>From Import';
				//	var_dump($CalculatedGSTDetails);
					$InvSubTotal = $CalculatedGSTDetails['Subtotal'];
					$CGST = $CalculatedGSTDetails['CGST'];
					$SGST = $CalculatedGSTDetails['SGST'];
					$RoundOffAmt = $CalculatedGSTDetails['RoundOffAmt'];
					$iTotalAmount = $CalculatedGSTDetails['Total'];	
				}
				else
				{
					$InvoiceCalculatedValue =  $this->CalculateInvoiceGst($resultFetch, $EditMode);
					$InvSubTotal = $InvoiceCalculatedValue['SubTotal'];
					$CGST = $InvoiceCalculatedValue['CGST'];
					$SGST = $InvoiceCalculatedValue['SGST'];
					$RoundOffAmt = $InvoiceCalculatedValue['RoundOffAmt'];
					$iTotalAmount = $InvoiceCalculatedValue['Total'];
					$TaxabledetailsString = $InvoiceCalculatedValue['TaxabledetailsString'];
						
				}
				
				if($EditMode == false)
				{
					
					if($IsRequestFromImportInvoice == true)
					{
						//echo '<br> Invoice Number '.$InvoiceNo;
						$BillDetailRefNo = $InvoiceNo;	
					}
					else
					{
						$Counter = $this->m_objUtility->GetCounter(VOUCHER_INVOICE,0);
						if($Counter <> '')
						{
							$BillDetailRefNo = $Counter[0]['CurrentCounter'];
						}
						else
						{
							$BillDetailRefNo = 1;
						}
					}
					
					echo 'Edit False';	
					$ExternalVoucherNumber = $BillDetailRefNo;
					
					$dataInsert="INSERT INTO sale_invoice(ID,UnitID,Inv_Date,Inv_Number,InvSubTotal,CGST,SGST,TotalPayable,Note,YearID,Locked,LatestChangeID,CreatedTimestamp,CreatedBy_LoginID,LastModified,ChangeLog,TaxableLedgers,Ledger_round_off) VALUES('','".$UnitID."','".$Inv_Date."','".$BillDetailRefNo."','".$InvSubTotal."','".$CGST."','".$SGST."','".$iTotalAmount."','".$Note."','".$_SESSION['default_year']."', '0', '0','".$timestamp['DateTime']."','".$_SESSION['login_id']."','0','0','".$TaxabledetailsString."', '".$RoundOffAmt."')";
					
					$InsertID=$this->m_dbConn->insert($dataInsert);	
				}
				//echo '<BR>InsertID11'.$InsertID;
				//echo '<BR>ExternalVoucherNumber11'.$ExternalVoucherNumber;			
				$this->InsertInvoiceVoucherRegister($Inv_Date,$InsertID,$iVoucherCouter,$UnitID,$iTotalAmount,$resultFetch,$CGST,$SGST,$ExternalVoucherNumber,$IsCallUpdtCnt, $RoundOffAmt);
				
				if($EditMode == true)
				{
					//desc = $this->returnLogDesc(EDIT,$ExitingUnitID,$UnitID,$Inv_Date,$PreviuosDetails[0]['Inv_Date'],$voucherResult,$resultFetch,$InvoiceNo,$ExternalVoucherNumber,$PreviuosDetails[0]['TotalPayable'],$iTotalAmount,0,0);
					
					// inserting the change log in table to keep record		 
					//$iLatestChangeID = $this->m_objLog ->setLog($desc, $_SESSION['login_id'], 'sale_invoice', $PreviuosDetails[0]['ID']);
					
					//updating sale invoice table
					
					$this->m_dbConn->update("UPDATE sale_invoice SET `Inv_Number` = '".$ExternalVoucherNumber."', `UnitID` = '".$UnitID."' , `Inv_Date` = '".$Inv_Date."' ,`InvSubTotal` = '".$InvSubTotal."', `CGST` = '".$CGST."',`SGST` = '".$SGST."',`TotalPayable` = '".$iTotalAmount."',`Note` = '".$Note."',`LatestChangeID` = '".$_SESSION['login_id']."',`LastModified` = '".$timestamp['DateTime']."',`ChangeLog` = '".$iLatestChangeID."', `TaxableLedgers` = '".$TaxabledetailsString."', Ledger_round_off = '".$RoundOffAmt."' WHERE UnitID = '".$ExitingUnitID."' AND Inv_Number = '".$InvoiceNo."'");
				
					}
				}

				// log the change insert/ update
				
				$LedgerDetailsInBill = $this->getAllIncludesLedgersInBill($InsertID, VOUCHER_JOURNAL);
				$unitNo = $this->m_objUtility->getLedgerName($UnitID);
				$billName = $this->m_objUtility->returnBillTypeString(Invoice);

				$dataArr = array('Date'=>$BillDate, 'Bill Type'=> $billName,  'Flat'=>$unitNo, 'Bill Number'=>$ExternalVoucherNumber, 'Sub Total'=>$InvSubTotal, 'Total BillPayable'=>$iTotalAmount, 'Ledgers'=>$LedgerDetailsInBill);

				$logArr = json_encode($dataArr);
				$previousLogID = 0;
				$requestStatus = ADD;
				if($EditMode){

					$requestStatus = EDIT;
					$previousLogDetail = $this->m_objLog->showChangeLog(TABLE_SALESINVOICE, $PreviuosDetails[0]['ID'], true);
					$previousLogID = $previousLogDetail[0]['ChangeLogID'];
				}
				$this->m_objLog->setLog($logArr, $_SESSION['login_id'], TABLE_SALESINVOICE, $InsertID, $requestStatus, $previousLogID);
				
				// log end				



				$this->m_dbConn->commit();
				return 1;
			}catch(Exception $exp)
			{
				//echo 'Inside the Catch';
				$this->m_dbConn->rollback();
				return $exp;
			}
		
	}
	
	function returnLogDesc($mode,$ExitingUnitID,$UnitID,$Current_Date,$PreviuosDate,$voucherResult,$resultFetch,$ExitingBillNumber,$OnUpdateBillNumber,$PrevBillTotal,$OnUpdateBillTotal,$PreviousBillType,$CurrentBillType)
	{
		$msg = array();
					
		$msg["action"] = $mode;
		
		if($ExitingBillNumber <> $OnUpdateBillNumber)
		{
			$msg["BillNumber"]["ExitingBillNumber"] =  $ExitingBillNumber;
			$msg["BillNumber"]["OnUpdateBillNumber"] =  $OnUpdateBillNumber;
		}
		
		if($PreviousBillType <> $CurrentBillType)
		{
			$msg["BillType"]["Previous"] =  $PreviousBillType;
			$msg["BillType"]["Current"] =  $CurrentBillType;
		}
		
		if($ExitingUnitID <> $UnitID)
		{
			$msg["Unit"]["PreviousUnit"] =  $ExitingUnitID;
			$msg["Unit"]["NewUnit"] =  $UnitID;
		}

		if($Current_Date <> $PreviuosDate)
		{
			$msg["Date"]["PreviousDate"] =  $PreviuosDate;
			$msg["Date"]["NewUnitDate"] =  $Current_Date;
		}
		
		for($i = 0 ; $i < sizeof($voucherResult); $i++)
		{
			$msg["PriviousLedgers"][$i]["LedgerID"] =  $voucherResult[$i]['To'];
			$msg["PriviousLedgers"][$i]["Amount"] = $voucherResult[$i]['Credit'];
		}
		
		for($i = 0 ; $i < sizeof($resultFetch) ; $i++)
		{
			$msg["UpdatedLedgers"][$i]["LedgerID"] =  $resultFetch[$i]['Head'];
			$msg["UpdatedLedgers"][$i]["Amount"] = $resultFetch[$i]['Amt'];								
		}
		
		if($PrevBillTotal <> $OnUpdateBillTotal)
		{
			$msg["BillTotal"]["PrevBillTotal"] =  $PrevBillTotal;
			$msg["BillTotal"]["OnUpdateBillTotal"] = $OnUpdateBillTotal;	
		}
		
		return json_encode($msg);
	}
	
	
	
	
	//**This function return the change log in the bills
	public function ShowChangeLog($bill_ID,$table_ID)
	{
		//***It return the all the change done for bill_ID ID ;
		$ChangeLogsDetails = $this->m_objLog->getlogChanges($bill_ID,$table_ID);
		//var_dump($ChangeLogsDetails);
		//** If any data exits then only body return
		if(count($ChangeLogsDetails) > 0)
		{
			//***Declarinf an empty variable
			$body = ""; 
				
			$body .= '<center>
			<div style="border:1px solid black;width:90%;text-align:center;" id="logchanges" name="logchanges">
				<div title="Change_history" style="text-align:center;border-bottom:1px solid black;padding:5px;font-size: 20px;font-family: sans-serif;"><Strong>Change History</Strong></div>
				<table width="100%">
					<tr style="border-bottom:1px solid black;text-align:center;">
						<th style="border-right:1px solid black;text-align:center;width:15%;padding: 5px;"><label>Updated Date</label></th>
						<th style="border-right:1px solid black;text-align:center;width:15%;"><label>Updated Time</label></th>
						<th style="border-right:1px solid black;text-align:center;width:15%;"><label>Updated By</label></th>
						<th style="text-align:center;"><label>Description</label></th>
					</tr>';
					
					//*** Here Number of time changes done in the bill it modify the data and append in body
					for($i = 0; $i < sizeof($ChangeLogsDetails); $i++)
					{  
						//** LoginDetailsOfChangedby return the Name who make changes 
						$LoginDetailsOfChangedby = $this->m_objUtility->GetMyLoginDetails($ChangeLogsDetails[$i]['ChangedBy']);
						//*** Return proper date and time
						$DateAndTime = $this->m_objUtility->DateAndTimeConversion($ChangeLogsDetails[$i]['ChangeTS']);
						//*** return the decription about changes so end user can understand
						$FormatLogDescription = $this->m_objUtility->FormatLogDescription($ChangeLogsDetails[$i]['ChangedLogDec']);
						if($FormatLogDescription <> '')
						{
				 $body .=  '<tr>
							<td style="border-right:1px solid black;border-bottom:1px solid black;text-align:center;">'.$DateAndTime[0]['Date'].'</td>
							<td style="border-right:1px solid black;border-bottom:1px solid black;text-align:center;">'.$DateAndTime[0]['Time'].'</td>
							<td style="border-right:1px solid black;border-bottom:1px solid black;text-align:center;">'.$LoginDetailsOfChangedby[0]['name'].'</td>
							<td style="text-align:left; border-bottom:1px solid black">'.$FormatLogDescription.'</td>
						</tr>';
						}
					}
					
				$body .= '</table>
			</div>
		</center>';
		return $body;	
		}
		
	} 
	
	
	public function updateBillMasterDetailsFromParkingPage($Detail,$yearId,$periodId)
	{
		//echo "In updateBillMasterDetailsFromParkingPage";
		//var_dump($Detail);
		$select0 = "select `BeginingDate`, `EndingDate` from `period` where `ID`='".$periodId."' and `YearID`='".$yearId."'";
		//echo $select0;
		$select01 = $this->m_dbConn->select($select0);
		/*echo "<pre>";
		print_r($select01);
		echo "</pre>";*/
		foreach($Detail as $z => $v)
		{
			$HeaderAmount1 = $Detail[$z]['Amt'];
			//echo "HeaderAmount1 :".$HeaderAmount1;
			$HeaderName1 = $Detail[$z]['Head'];
			//echo "HeaderName1 :".$HeaderName1;
			$unitId = $Detail[$z]['UnitId'];
			//echo "unitId :".$unitId;
			$select1 = "select * from `unitbillmaster` where `UnitID`='".$unitId."' and `AccountHeadID`='".$HeaderName1."' and `BeginPeriod`='".$select01[0]['BeginingDate']."' and `EndPeriod`='".$select01[0]['EndingDate']."'";
			$select11 = $this->m_dbConn->select($select1);
			
			if($select11 <> '')
			{
				//echo "in if";
				$update1 = "update `unitbillmaster` set `AccountHeadAmount`='".$HeaderAmount1."' where `UnitID`='".$unitId."' and `AccountHeadID`='".$HeaderName1."' and `BeginPeriod`='".$select01[0]['BeginingDate']."' and `EndPeriod`='".$select01[0]['EndingDate']."'";
				$result = $this->m_dbConn->update($update1);
				//var_dump($update11);				
			}
			else
			{
				//echo "in else";
				$insert1 = "insert into `unitbillmaster`(UnitID,CreatedBy,LatestChangeID,AccountHeadID,AccountHeadAmount,BeginPeriod,EndPeriod) values('".$unitId."','".$_SESSION['login_id']."',0,'".$HeaderName1."','".$HeaderAmount1."','".$select01[0]['BeginingDate']."','".$select01[0]['EndingDate']."')";
				$result = $this->m_dbConn->insert($insert1);
			}
		}
		echo "Success";
	}
	
	
}
?>