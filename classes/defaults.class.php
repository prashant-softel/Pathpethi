<?php if(!isset($_SESSION)){ session_start(); } ?>

<?php
	
	include_once('dbconst.class.php');
	include_once('changelog.class.php');
	//Defaults : ID's in appdefault table in DB
	define ('APP_DEFAULT_YEAR', 'APP_DEFAULT_YEAR');
	define ('APP_DEFAULT_PERIOD', 'APP_DEFAULT_PERIOD');
	
	define ('APP_DEFAULT_INTEREST_ON_PRINCIPLE_DUE', 'APP_DEFAULT_INTEREST_ON_PRINCIPLE_DUE');
	define ('APP_DEFAULT_PENALTY_TO_MEMBER', 'APP_DEFAULT_PENALTY_TO_MEMBER');
	define	('APP_DEFAULT_BANK_CHARGES', 'APP_DEFAULT_BANK_CHARGES');
	define	('APP_DEFAULT_TDS_PAYABLE', 'APP_DEFAULT_TDS_PAYABLE');
	define	('APP_DEFAULT_IMPOSE_FINE', 'APP_DEFAULT_IMPOSE_FINE');       /// impose fine
	define ('APP_DEFAULT_INCOME_EXPENDITURE_ACCOUNT', 'APP_DEFAULT_INCOME_EXPENDITURE_ACCOUNT');
	define ('APP_DEFAULT_ADJUSTMENT_CREDIT', 'APP_DEFAULT_ADJUSTMENT_CREDIT');
	define ('APP_DEFAULT_IGST', 'APP_DEFAULT_IGST'); 	
	define ('APP_DEFAULT_CGST', 'APP_DEFAULT_CGST'); 	
	define ('APP_DEFAULT_SGST', 'APP_DEFAULT_SGST'); 	
	define ('APP_DEFAULT_CESS', 'APP_DEFAULT_CESS'); 	
	define ('APP_DEFAULT_LOAN', 'APP_DEFAULT_LOAN'); 	
	define ('APP_DEFAULT_SAVING_ACCOUNT', 'APP_DEFAULT_SAVING_ACCOUNT'); 	
	define ('APP_DEFAULT_FIXED_DEPOSIT', 'APP_DEFAULT_FIXED_DEPOSIT'); 	
	define ('APP_DEFAULT_DAILY_DEPOSIT', 'APP_DEFAULT_DAILY_DEPOSIT'); 
	define ('APP_DEFAULT_MONTHLY_DEPOSIT', 'APP_DEFAULT_MONTHLY_DEPOSIT'); 		
	
	define ('APP_DEFAULT_CURRENT_ASSET', 'APP_DEFAULT_CURRENT_ASSET');
	define ('APP_DEFAULT_DUE_FROM_MEMBERS', 'APP_DEFAULT_DUE_FROM_MEMBERS');
	define ('APP_DEFAULT_BANK_ACCOUNT', 'APP_DEFAULT_BANK_ACCOUNT');
	define ('APP_DEFAULT_CASH_ACCOUNT', 'APP_DEFAULT_CASH_ACCOUNT');	
	define ('APP_DEFAULT_SOCIETY', 'APP_DEFAULT_SOCIETY');
	//define ('APP_DEFAULT_EMAILID', 'APP_DEFAULT_EMAILID');
	
class defaults
{
	public $actionPage = "../defaults.php";
	public $m_dbConn;
	public $m_objLog;
	public $m_dbConnRoot;
	
	function __construct($dbConn, $dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->m_objLog = new changeLog($dbConn);
		if($dbConnRoot == NULL)
		{
			//echo "in if not found";
			$Root = new dbop(true);
			$this->m_dbConnRoot = $Root;
		}
		else
		{
			//echo "in if found";
			$this->m_dbConnRoot = $dbConnRoot;
		}
	}

	public function getDefaults($society_id, $bSetToSession = true)
	{
		$bSuccess = false;
		
		$sqlDefault = "select * from `appdefault` where " . APP_DEFAULT_SOCIETY . "=" . $society_id;
		
		//echo $sqlDefault;
		$resDefault = $this->m_dbConn->select($sqlDefault);
		
		if($bSetToSession)
		{
			$_SESSION['default_year'] = 0;
			$_SESSION['default_period'] = 0;
			$_SESSION['default_interest_on_principle'] = 0;
			$_SESSION['default_penalty_to_member'] = 0;
			$_SESSION['default_bank_charges'] = 0;
			$_SESSION['default_tds_payable'] = 0;
			$_SESSION['default_impose_fine'] = 0;   // impose fine
			$_SESSION['default_current_asset'] = 0;
			$_SESSION['default_bank_account'] = 0;
			$_SESSION['default_cash_account'] = 0;
			$_SESSION['default_due_from_member'] = 0;
			$_SESSION['default_income_expenditure_account'] = 0;
			$_SESSION['default_adjustment_credit'] = 0;
			$_SESSION['igst_service_tax'] = 0;
			$_SESSION['cgst_service_tax'] = 0;
			$_SESSION['sgst_service_tax'] = 0;
			$_SESSION['cess_service_tax'] = 0;
			$_SESSION['society_id'] = 0;
			$_SESSION['default_year_start_date'] = 0;
			$_SESSION['default_year_end_date'] = 0;
			$_SESSION['is_year_freeze'] = 0;
			$_SESSION['mem_other_id'] = 0;
			$_SESSION['default_loan'] = 0;
			$_SESSION['default_saving_account'] = 0;
			$_SESSION['default_fixed_deposit'] = 0;
			$_SESSION['default_daily_deposit'] = 0;
			$_SESSION['default_monthly_deposit'] = 0;
			//$_SESSION['defaultEmailID'] = "";
		
			if($resDefault<>"")
			{
				
				$_SESSION['default_period'] = $resDefault[0][APP_DEFAULT_PERIOD];
				$_SESSION['default_interest_on_principle'] = $resDefault[0][APP_DEFAULT_INTEREST_ON_PRINCIPLE_DUE];
				$_SESSION['default_penalty_to_member'] = $resDefault[0][APP_DEFAULT_PENALTY_TO_MEMBER];
				$_SESSION['default_bank_charges'] = $resDefault[0][APP_DEFAULT_BANK_CHARGES];
				$_SESSION['default_tds_payable'] = $resDefault[0][APP_DEFAULT_TDS_PAYABLE];
				$_SESSION['default_impose_fine'] = $resDefault[0][APP_DEFAULT_IMPOSE_FINE];  // impose fine
				$_SESSION['default_current_asset'] = $resDefault[0][APP_DEFAULT_CURRENT_ASSET];
				$_SESSION['default_bank_account'] = $resDefault[0][APP_DEFAULT_BANK_ACCOUNT];
				$_SESSION['default_cash_account'] = $resDefault[0][APP_DEFAULT_CASH_ACCOUNT];
				$_SESSION['default_due_from_member'] = $resDefault[0][APP_DEFAULT_DUE_FROM_MEMBERS];
				$_SESSION['default_income_expenditure_account'] = $resDefault[0][APP_DEFAULT_INCOME_EXPENDITURE_ACCOUNT];
				$_SESSION['default_adjustment_credit'] = $resDefault[0][APP_DEFAULT_ADJUSTMENT_CREDIT];
				$_SESSION['igst_service_tax'] = $resDefault[0][APP_DEFAULT_IGST];
				$_SESSION['cgst_service_tax'] = $resDefault[0][APP_DEFAULT_CGST];
				$_SESSION['sgst_service_tax'] = $resDefault[0][APP_DEFAULT_SGST];
				$_SESSION['cess_service_tax'] = $resDefault[0][APP_DEFAULT_CESS];
				$_SESSION['society_id'] = $resDefault[0][APP_DEFAULT_SOCIETY];
				$_SESSION['default_loan'] = $resDefault[0][APP_DEFAULT_LOAN];
				$_SESSION['default_saving_account'] = $resDefault[0][APP_DEFAULT_SAVING_ACCOUNT];
				$_SESSION['default_fixed_deposit'] = $resDefault[0][APP_DEFAULT_FIXED_DEPOSIT];
				$_SESSION['default_daily_deposit'] = $resDefault[0][APP_DEFAULT_DAILY_DEPOSIT];
				$_SESSION['default_monthly_deposit'] = $resDefault[0][APP_DEFAULT_MONTHLY_DEPOSIT];
				//$_SESSION['defaultEmailID'] = $resDefault[0][APP_DEFAULT_EMAILID];
				$bSuccess = true;
			}
		}
		
		$sqlIV = "SELECT `current_map_year` FROM  `mapping` where  login_id = '" . $_SESSION['login_id'] . "'  and `society_id` = '" . $_SESSION['society_id'] . "'  and  `unit_id` = '".$_SESSION['unit_id']."' ";
		$resIV = $this->m_dbConnRoot->select($sqlIV);
		
		if($resIV[0]['current_map_year'] == 0)
		{
			$_SESSION['default_year'] = $resDefault[0][APP_DEFAULT_YEAR];
		}
		else
		{
			$_SESSION['default_year'] = $resIV[0]['current_map_year'];
			$resDefault[0][APP_DEFAULT_YEAR] = $resIV[0]['current_map_year'];
		}
			
		$sqlSociety = "SELECT `society_creation_yearid`,`gst_start_date` FROM `society`  where `society_id`  = ".$society_id;
		$resSociety = $this->m_dbConn->select($sqlSociety);
		$_SESSION['society_creation_yearid'] = $resSociety[0]['society_creation_yearid'];
		
		$sqlUnitblock = "SELECT `block_unit` FROM `unit`  where `unit_id`  = ".$_SESSION['unit_id'];
		$resUnit = $this->m_dbConn->select($sqlUnitblock);
		$_SESSION['unit_blocked'] = $resUnit[0]['block_unit'];
		
		$_SESSION['gst_start_date'] = $resSociety[0]['gst_start_date'];
		
		$sqlYear = "SELECT * FROM `year`  where `YearID`  = ".$_SESSION['default_year'];
		$resYear = $this->m_dbConn->select($sqlYear);
		$_SESSION['is_year_freeze'] = $resYear[0]['is_year_freeze'];

		$sql_email = "Select member_id from login where login_id = '" . $_SESSION['login_id'] . "'";
		$result_email = $this->m_dbConnRoot->select($sql_email);

		//Causing SQL Error
		// if($result_email <> '')
		// {
		// 	$sql_mem_other = "Select mo.`mem_other_family_id` from `mem_other_family` as mo JOIN member_main as mm on mo.member_id = mm.member_id JOIN unit as u ON mm.unit = u.unit_id WHERE u.unit_id = '" . $_SESSION['unit_id'] . "' and mo.other_email = '" . $result_email[0]['member_id'] . "'";
		// 	$result_mem_other = $this->m_dbConn->select($sql_mem_other);

		// 	if($result_mem_other <> '')
		// 	{
		// 		$_SESSION['mem_other_id'] = $result_mem_other[0]['mem_other_family_id'];
		// 	}
		// }
		
		$this->setSessionDates();
		return $resDefault;
	}
	
	public function resetDefaults()
	{
		$_SESSION['default_year'] = 0;
		$_SESSION['default_period'] = 0;
		$_SESSION['default_interest_on_principle'] = 0;
		$_SESSION['default_penalty_to_member'] = 0;
		$_SESSION['default_bank_charges'] = 0;
		$_SESSION['default_tds_payable'] = 0;
		$_SESSION['default_impose_fine'] = 0;        //impose fine
		$_SESSION['default_current_asset'] = 0;
		$_SESSION['default_bank_account'] = 0;
		$_SESSION['default_cash_account'] = 0;
		$_SESSION['default_due_from_member'] = 0;
		$_SESSION['default_income_expenditure_account'] = 0;
		$_SESSION['default_adjustment_credit'] = 0;
		$_SESSION['igst_service_tax'] = 0;
		$_SESSION['cgst_service_tax'] = 0;
		$_SESSION['sgst_service_tax'] = 0;
		$_SESSION['cess_service_tax'] = 0;
		$_SESSION['default_loan'] = 0;
		$_SESSION['default_saving_account'] = 0;
		$_SESSION['default_fixed_deposit'] = 0;
		$_SESSION['default_daily_deposit'] = 0;
		$_SESSION['default_monthly_deposit'] = 0;
		$_SESSION['society_id'] = 0;
		$_SESSION['profile'] = '';
		$_SESSION['mem_other_id'] = 0;
		//$_SESSION['defaultEmailID'] = '';
	}
		
	public function setDefault($Society, $Year, $Period, $Interest, $penalty, $bankCharges,$tdsPayable, $CurrentAsset, $MemberDue, $BankAcc, $CashAcc,$defaultIncomeExpenditureAccount, $defaultAdjustmentCredit,$igstServiceTax,$cgstSertviceTax,$sgstServiceTax,$cessServiceTax,$ImposeFine, $default_loan, $default_saving_account, $default_fixed_deposit, $default_daily_deposit, $default_monthly_deposit/*$defaultServiceTax ,$defaultEmailID*/)
	{
		echo "bank : ". $bankCharges;
		//$sqlUpdate = "UPDATE `appdefault` SET `Value`= '" . $value . "' WHERE ID = '" . $id . "'";
		$sqlFetch = "SELECT * from `society` where `society_id` = '" . $Society . "' ";
		$res00 = $this->m_dbConn->select($sqlFetch);
		//$EmailChnage = "Prev EmailID: <". $res00[0]['email']."> Current EmailID: <". $defaultEmailID.">";
		
		$sqlUpdate = "UPDATE `appdefault` SET `APP_DEFAULT_YEAR`='" . $Year . "', `APP_DEFAULT_PERIOD`='" . $Period . "', `APP_DEFAULT_INTEREST_ON_PRINCIPLE_DUE`= '" . $Interest . "', `APP_DEFAULT_PENALTY_TO_MEMBER`= '" . $penalty . "', `APP_DEFAULT_BANK_CHARGES`= '" . $bankCharges . "',`APP_DEFAULT_TDS_PAYABLE`= '" . $tdsPayable . "', `APP_DEFAULT_CURRENT_ASSET`= '" . $CurrentAsset . "', `APP_DEFAULT_DUE_FROM_MEMBERS`= '" . $MemberDue . "', `APP_DEFAULT_INCOME_EXPENDITURE_ACCOUNT`= '" . $defaultIncomeExpenditureAccount . "', `APP_DEFAULT_BANK_ACCOUNT`= '" . $BankAcc . "', `APP_DEFAULT_CASH_ACCOUNT`= '" . $CashAcc . "', `APP_DEFAULT_ADJUSTMENT_CREDIT`= '" . $defaultAdjustmentCredit .  "', `APP_DEFAULT_IGST`= '" . $igstServiceTax .  "', `APP_DEFAULT_CGST`= '" . $cgstSertviceTax .  "', `APP_DEFAULT_SGST`= '" . $sgstServiceTax .  "', `APP_DEFAULT_CESS`= '" . $cessServiceTax .  "',`APP_DEFAULT_IMPOSE_FINE`= '" . $ImposeFine .  "',`APP_DEFAULT_LOAN`= '" . $default_loan .  "',`APP_DEFAULT_SAVING_ACCOUNT`= '" . $default_saving_account .  "',`APP_DEFAULT_FIXED_DEPOSIT`= '" . $default_fixed_deposit .  "',`APP_DEFAULT_DAILY_DEPOSIT`= '" . $default_daily_deposit .  "',`APP_DEFAULT_MONTHLY_DEPOSIT`= '" . $default_monthly_deposit .  "', `changed_by`= '" . $_SESSION['login_id'] . "' WHERE APP_DEFAULT_SOCIETY = '" . $Society . "'";
		
		//$sqlUpdate = "UPDATE `appdefault` SET `APP_DEFAULT_PERIOD`='" . $Period . "', `APP_DEFAULT_INTEREST_ON_PRINCIPLE_DUE`= '" . $Interest . "', `APP_DEFAULT_PENALTY_TO_MEMBER`= '" . $penalty . "', `APP_DEFAULT_BANK_CHARGES`= '" . $bankCharges . "',`APP_DEFAULT_TDS_PAYABLE`= '" . $tdsPayable . "', `APP_DEFAULT_CURRENT_ASSET`= '" . $CurrentAsset . "', `APP_DEFAULT_DUE_FROM_MEMBERS`= '" . $MemberDue . "', `APP_DEFAULT_INCOME_EXPENDITURE_ACCOUNT`= '" . $defaultIncomeExpenditureAccount . "', `APP_DEFAULT_BANK_ACCOUNT`= '" . $BankAcc . "', `APP_DEFAULT_CASH_ACCOUNT`= '" . $CashAcc . "', `APP_DEFAULT_ADJUSTMENT_CREDIT`= '" . $defaultAdjustmentCredit .  "', `APP_DEFAULT_SERVICE_TAX`= '" . $defaultServiceTax .  "', `changed_by`= '" . $_SESSION['login_id'] . "' WHERE APP_DEFAULT_SOCIETY = '" . $Society . "'";
		
		$result = $this->m_dbConn->update($sqlUpdate);
		
		$sqlYear = " UPDATE  `mapping` SET `current_map_year` = '" . $Year . "'  WHERE login_id = '" . $_SESSION['login_id'] . "'  and `society_id` = '" . $_SESSION['society_id'] . "' and  `unit_id` = '".$_SESSION['unit_id']."' ";	
		$resultYear = $this->m_dbConnRoot->update($sqlYear);
		
		//$sqlSocietyUpdate = "UPDATE `society` SET `email` = '" . $defaultEmailID .  "' where `society_id` = '" . $Society . "' ";
		
		//$res = $this->m_dbConn->update($sqlSocietyUpdate);
		
		//$this->m_objLog->setLog($EmailChnage, $_SESSION['login_id'], 'society', '-');
		
		$_SESSION['society_id'] = $Society;
		
		//$sqlUpdate = "UPDATE `login` SET `current_society`='" . $Society . "' WHERE login_id = '" . $_SESSION['login_id'] . "'";
		//$resultUpdate = $this->m_dbConn->update($sqlUpdate);
		
		return $result;
	}
	
	public function combobox($query, $id, $defaultText = '')
	{
		if($defaultText <> '')
		{
			$str = '<option value="0">' . $defaultText . '</option>';
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
	
	public function getSocietyName($SocietyID)
	{
		$sql = "Select society_name from society where society_id = '" . $SocietyID . "'";
		$result = $this->m_dbConn->select($sql);
		return $result[0]['society_name'];
	}
	
	public function getMemberID($SocietyID,$UnitID)
	{
		$sql="select member_id from `member_main` where unit='".$UnitID."' and  society_id = '" . $SocietyID . "' AND `ownership_status`=1";
		$res=$this->m_dbConn->select($sql);
		 return $res[0]['member_id'];		
	}
	
	public function setSessionDates()
	{
		if($_SESSION['default_year'] <> "" && $_SESSION['default_year'] > 0)
		{
			$sqlFetchDate = "select `YearID`,`BeginingDate`,`EndingDate`,`status` from `year` where `YearID` = '".$_SESSION['default_year']."' and `status` = 'Y' ";
			$resFetchDate = $this->m_dbConn->select($sqlFetchDate);
			
			if($resFetchDate <> "")
			{
				$_SESSION['default_year_start_date'] = $resFetchDate[0]['BeginingDate'];
				$_SESSION['default_year_end_date'] = $resFetchDate[0]['EndingDate'];
				
				$_SESSION['from_date'] = getDisplayFormatDate($resFetchDate[0]['BeginingDate']);
				$_SESSION['to_date'] = getDisplayFormatDate($resFetchDate[0]['EndingDate']);
				?>
				<script>
					localStorage.setItem("minGlobalCurrentYearStartDate", "<?php  echo getDisplayFormatDate($_SESSION['default_year_start_date']);?>");
					localStorage.setItem("maxGlobalCurrentYearEndDate", "<?php  echo getDisplayFormatDate($_SESSION['default_year_end_date']);?>");
				</script>
	<?php }
		}	
		
	}
	
}
?>