<?php
include_once("dbconst.class.php");
include_once("utility.class.php");

class CAdminPanel
{
	public $m_dbConn;
	public $obj_utility;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->obj_utility = new utility($this->m_dbConn);
		
	}

	public function GetSummary($GroupID)
	{
		if($GroupID==LIABILITY)
		{
			$sqlQuery = "SELECT DISTINCT liability.LedgerID, liability.CategoryID, liability.SubCategoryID FROM liabilityregister as liability JOIN ledger as led ON led.id=liability.LedgerID where led.society_id=".$_SESSION['society_id']." GROUP BY liability.SubCategoryID";
		}
		if($GroupID==ASSET)
		{
			$sqlQuery = "SELECT DISTINCT asset.LedgerID, asset.CategoryID, asset.SubCategoryID FROM assetregister as asset JOIN ledger as led ON led.id=asset.LedgerID where led.society_id=".$_SESSION['society_id']." and ( asset.SubCategoryID != '".BANK_ACCOUNT."' and asset.SubCategoryID != '".CASH_ACCOUNT."')  GROUP BY asset.SubCategoryID";
		}
		if($GroupID==INCOME)
		{
			$sqlQuery = "SELECT DISTINCT income.LedgerID,income.CategoryID, income.SubCategoryID, SUM( income.Debit ) AS debit, SUM( income.Credit ) AS credit FROM incomeregister as income JOIN ledger as led ON led.id =income.LedgerID where led.society_id=".$_SESSION['society_id']."  ";
			
			if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
			{
				$sqlQuery .= "  and Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
			}
			$sqlQuery .= " GROUP BY income.LedgerID ";
		}
		if($GroupID==EXPENSE)
		{
			$sqlQuery = "SELECT  DISTINCT expense.LedgerID,expense.CategoryID, expense.SubCategoryID, expense.Debit AS debit,  expense.Credit AS credit, expense.VoucherID, led.ledger_name FROM expenseregister as expense JOIN ledger as led ON led.id =expense.LedgerID where led.society_id='".$_SESSION['society_id']."' ";
			
			if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
			{
				$sqlQuery .= "  and Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
			}
			$sqlQuery .= " GROUP BY expense.SubCategoryID";
		}
		
		$retData = $this->m_dbConn->select($sqlQuery);
		//return $retData;
		return $this->GetCategoryTransactions($retData , $GroupID);
	}

	public function GetCategoryTransactions($Data , $GroupID)
	{
		
		for($i = 0; $i < sizeof($Data); $i++)
		{
			if($GroupID == LIABILITY)
			{
				$sqlQuery = "SELECT liability.CategoryID, liability.SubCategoryID, SUM( liability.Debit ) AS debit, SUM( liability.Credit ) AS credit FROM `liabilityregister` as liability  where liability.SubCategoryID = '".$Data[$i]['SubCategoryID']."'";
					
				if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
				{
					$sqlQuery .= "  and liability.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
				}
				$sqlQuery .= " GROUP BY liability.SubCategoryID ";
							
			}
			else if($GroupID == ASSET)
			{
				$sqlQuery = "SELECT assettbl.CategoryID, assettbl.SubCategoryID, SUM( assettbl.Debit ) AS debit, SUM( assettbl.Credit ) AS credit FROM `assetregister` as assettbl  where assettbl.SubCategoryID = '".$Data[$i]['SubCategoryID']."'";
					
				if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
				{
					
					$sqlQuery .= "  and assettbl.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
				}
				$sqlQuery .= " GROUP BY assettbl.SubCategoryID ";
				
			}
			$retData = $this->m_dbConn->select($sqlQuery);
			
			if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
			{
				$res = $this->obj_utility->getOpeningBalanceOfCategory($Data[$i]['SubCategoryID'] , getDBFormatDate($_SESSION['default_year_start_date']));
				if($res <> "")
				{
					$Data[$i]['debit'] = $retData[0]['debit'] + $res['Debit'];
					$Data[$i]['credit'] = $retData[0]['credit'] + $res['Credit'];
						
				}		
			}
		}
		
		return $Data;
	}










	/*	
	public function GetAssetSummary($NumberOfRecordsRequired)
	{
		$arBankNameAndBalance = "";
		$sqlSelect = "";
//$sqlQuery = "SELECT DISTINCT asset.LedgerID, asset.CategoryID, asset.SubCategoryID, SUM( asset.Debit ) AS debit, SUM( asset.Credit ) AS credit FROM assetregister as asset JOIN ledger as led ON led.id=asset.LedgerID where led.society_id=".$_SESSION['society_id']." GROUP BY asset.LedgerID";
		$sqlQuery = "SELECT DISTINCT asset.LedgerID, asset.CategoryID, asset.SubCategoryID, SUM( asset.Debit ) AS debit, SUM( asset.Credit ) AS credit FROM assetregister as asset JOIN ledger as led ON led.id=asset.LedgerID where led.society_id=".$_SESSION['society_id']." GROUP BY asset.SubCategoryID";
		$retData = $this->m_dbConn->select($sqlQuery);
		//print_r($retData);
		
		return $retData;
	}

	public function GetLiabilitySummary($NumberOfRecordsRequired)
	{
		$arBankNameAndBalance = "";
		$sqlSelect = "";
		//$sqlQuery = "SELECT DISTINCT liability.LedgerID, liability.CategoryID, liability.SubCategoryID, SUM( liability.Debit ) AS debit, SUM( liability.Credit ) AS credit FROM liabilityregister as liability JOIN ledger as led ON led.id=liability.LedgerID where led.society_id=".$_SESSION['society_id']." GROUP BY liability.LedgerID";
		$sqlQuery = "SELECT DISTINCT liability.LedgerID, liability.CategoryID, liability.SubCategoryID, SUM( liability.Debit ) AS debit, SUM( liability.Credit ) AS credit FROM liabilityregister as liability JOIN ledger as led ON led.id=liability.LedgerID where led.society_id=".$_SESSION['society_id']." GROUP BY liability.SubCategoryID";
		$retData = $this->m_dbConn->select($sqlQuery);
		//print_r($retData);
		
		return $retData;
	}

	

	public function GetExpenseSummary($NumberOfRecordsRequired)
	{
		$arBankNameAndBalance = "";
		$sqlSelect = "";
		$sqlQuery = "SELECT DISTINCT expense.LedgerID, SUM( expense.Debit ) AS debit, SUM( expense.Credit ) AS credit, expense.ExpenseHead AS ExpenseHead FROM expenseregister as expense JOIN ledger as led ON led.id =expense.LedgerID where led.society_id=".$_SESSION['society_id']." GROUP BY expense.LedgerID";
		$retData = $this->m_dbConn->select($sqlQuery);
		print_r($retData);
		
		return $retData;
	}
	*/
	public function GetIncomeSummaryDetailed($NumberOfRecordsRequired)
	{
		$arBankNameAndBalance = "";
		$sqlSelect = "";
		$sqlQuery = "SELECT DISTINCT income.LedgerID, SUM( income.Debit ) AS debit, SUM( income.Credit ) AS credit FROM incomeregister as income JOIN ledger as led ON led.id =income.LedgerID where led.society_id=".$_SESSION['society_id']." ";
		if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
		{
			
			$sqlQuery .= "  and income.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
		}
		$sqlQuery .= "  GROUP BY income.LedgerID";
		
		$retData = $this->m_dbConn->select($sqlQuery);
		//print_r($retData);
		
		return $retData;
	}
	public function GetExpenseSummaryDetailed($NumberOfRecordsRequired)
	{
		$arBankNameAndBalance = "";
		$sqlSelect = "";
		//$sqlQuery = "SELECT  expense.LedgerID, sum(expense.Debit) AS debit,  sum(expense.Credit) AS credit, expense.VoucherID FROM expenseregister as expense JOIN ledger as led ON led.id =expense.LedgerID where led.society_id=".$_SESSION['society_id']." GROUP BY expense.LedgerID";
		$sqlQuery = "SELECT  expense.LedgerID,  sum(expense.Debit) AS debit,  sum(expense.Credit) AS credit, expense.VoucherID, led.ledger_name FROM expenseregister as expense JOIN ledger as led ON led.id =expense.LedgerID where led.society_id='".$_SESSION['society_id']."'";
		if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
		{
			
			$sqlQuery .= "  and expense.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
		}
		$sqlQuery .= "  GROUP BY expense.LedgerID";
		$retData = $this->m_dbConn->select($sqlQuery);
		//print_r($retData);
		
		return $retData;
	}

	public function GetBankAccountAndBalance($NumberOfRecordsRequired = 0)
	{
		$arBankNameAndBalance = "";
		$sqlSelect = "";
		//$sqlQuery = "SELECT DISTINCT LedgerID, SUM( ReceivedAmount ) AS receipts, SUM( PaidAmount ) AS payments FROM bankregister GROUP BY LedgerID";
		$sqlQuery = "SELECT DISTINCT bk.LedgerID, SUM( bk.ReceivedAmount ) AS receipts, SUM( bk.PaidAmount ) AS payments FROM bankregister as bk JOIN ledger as led ON led.id = bk.LedgerID where led.society_id='".DEFAULT_SOCIETY."' ";
		//echo $sqlQuery;
		if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
		{
			
			$sqlQuery .= "  and bk.Date <= '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
		}
		
		$sqlQuery .= "  GROUP BY bk.LedgerID"; 
		
		if($NumberOfRecordsRequired > 0)
		{
			$sqlQuery .= '  LIMIT 0, ' . $NumberOfRecordsRequired;
		}
		
		$retData = $this->m_dbConn->select($sqlQuery);
		//print_r($retData);
		
		return $retData;
	}
	
	public function GetCategoryNameFromID($CategoryID)
	{
		$sqlQuery = "SELECT category_name FROM account_category where category_id=".$CategoryID;
		//echo $sqlQuery;
		//echo "<br>";
		$retData = $this->m_dbConn->select($sqlQuery);
		
		return $retData[0]["category_name"];
	}

	public function GetLedgerNameFromID($LedgerID)
	{
		$sqlQuery = "SELECT ledger_name FROM ledger where id=".$LedgerID;
		//echo $sqlQuery;
		//echo "<br>";
		$retData = $this->m_dbConn->select($sqlQuery);
		
		return $retData[0]["ledger_name"];
	}
	
	public function GetTotalIncome()
	{
		$sqlQuery = "SELECT SUM( inc.Debit ) AS payments, SUM( inc.Credit ) AS receipts,monthname( inc.date ) AS date FROM incomeregister as inc JOIN ledger as led ON led.id = inc.LedgerID where led.society_id=".$_SESSION['society_id']." ";
		if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
		{
			
			$sqlQuery .= "  and inc.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
		}
		$sqlQuery .= "  group by MONTH(inc.date) order by MONTH(inc.date) desc";
		
		//echo $sqlQuery;
		//echo "<br>";
		$retData = $this->m_dbConn->select($sqlQuery);
		
		return $retData;
	}
	
	public function GetTotalExpenses()
	{
		$sqlQuery = "SELECT SUM( exp.Debit ) AS receipts, SUM( exp.Credit ) AS payments,monthname( exp.Date ) AS date FROM expenseregister as exp JOIN ledger as led ON led.id = exp.LedgerID where led.society_id=".$_SESSION['society_id']. " ";
		if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
		{
			
			$sqlQuery .= "  and exp.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
		}
		$sqlQuery .= "  group by MONTH(exp.date) order by MONTH(exp.date) desc";
		
		//echo $sqlQuery;
		//echo "<br>";
		$retData = $this->m_dbConn->select($sqlQuery);
		
		return $retData;
	}
	
	public function GetTotalAssets()
	{
		//$sqlQuery = "SELECT SUM( asset.Debit ) AS receipts, SUM( asset.Credit ) AS payments FROM assetregister as asset JOIN ledger as led ON led.id = asset.LedgerID where led.society_id=".$_SESSION['society_id'];
		$sqlQuery = "SELECT SUM( asset.Debit ) AS receipts, SUM( asset.Credit ) AS payments,monthname( asset.date ) AS date FROM assetregister as asset JOIN ledger as led ON led.id = asset.LedgerID where led.society_id=".$_SESSION['society_id']." ";
		if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
		{
			
			$sqlQuery .= "  and asset.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
		}
		$sqlQuery .= "  group by YEAR(asset.date),MONTH(asset.date) order by asset.date desc";
		
		$retData = $this->m_dbConn->select($sqlQuery);
		//print_r($retData);
		return $retData;
	}
	
	public function GetTotalLiabilities()
	{
		//$sqlQuery = "SELECT SUM( lib.Debit ) AS receipts, SUM( lib.Credit ) AS payments FROM liabilityregister as lib JOIN ledger as led ON led.id = lib.LedgerID where led.society_id=".$_SESSION['society_id'];
		$sqlQuery = "SELECT SUM( lib.Debit ) AS receipts, SUM( lib.Credit ) AS payments,monthname( lib.date ) AS date  FROM liabilityregister as lib JOIN ledger as led ON led.id = lib.LedgerID where led.society_id=".$_SESSION['society_id']."  ";
		if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
		{
			
			$sqlQuery .= "  and lib.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
		}
		$sqlQuery .= "  group by YEAR(lib.date),MONTH(lib.date) order by lib.date desc";
		
		//echo $sqlQuery;
		//echo "<br>";
		//echo date("M", "2015-05-02");
		$retData = $this->m_dbConn->select($sqlQuery);
		
		return $retData;
	}
	
	public function GetTotalLiabilitiesOrAssets($groupID)
	{
		if($groupID == LIABILITY)
		{
			$sqlQuery = "SELECT SUM( lib.Debit ) AS receipts, SUM( lib.Credit ) AS payments,monthname( lib.date ) AS date,(SUM( lib.Credit ) - SUM( lib.Debit )) as BalAmount FROM liabilityregister as lib JOIN ledger as led ON led.id = lib.LedgerID where led.society_id=".$_SESSION['society_id']."  ";
			if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
			{
				
				$sqlQuery .= "  and lib.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
			}
			$sqlQuery .= "  group by YEAR(lib.date),MONTH(lib.date) order by lib.date desc";		
		}
		else if($groupID == ASSET)
		{
			$sqlQuery = "SELECT SUM( asset.Debit ) AS receipts, SUM( asset.Credit ) AS payments,monthname( asset.date ) AS date,(SUM( asset.Debit ) - SUM( asset.Credit )) as BalAmount FROM assetregister as asset JOIN ledger as led ON led.id = asset.LedgerID where led.society_id=".$_SESSION['society_id']." ";
			if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
			{
				
				$sqlQuery .= "  and asset.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
			}
			$sqlQuery .= "  group by YEAR(asset.date),MONTH(asset.date) order by asset.date desc";		
			
		}
		$retData = $this->m_dbConn->select($sqlQuery);
		if(count($retData) > 0)
		{
			foreach($retData as $key => $value)
			{
				if($value['date'] == 'April')
			   {
					
					if($groupID == LIABILITY)
					{
						$res = $this->obj_utility->getOpeningBalanceOfCategory(LIABILITY, getDBFormatDate($_SESSION['default_year_start_date']) ,true);
						if($res <> "")
						{
							if($res['OpeningType'] == TRANSACTION_CREDIT)
							{
								$value['BalAmount'] = $value['BalAmount'] + $res['Total'];
							}
							else
							{
								$value['BalAmount'] = $value['BalAmount'] - $res['Total'];
							}
						}
					}
					else if($groupID == ASSET)
					{
						$res = $this->obj_utility->getOpeningBalanceOfCategory(ASSET, getDBFormatDate($_SESSION['default_year_start_date']) ,true);
						if($res <> "")
						{
							if($res['OpeningType'] == TRANSACTION_DEBIT)
							{
								$value['BalAmount'] = $value['BalAmount'] + $res['Total'];
							}
							else
							{
								$value['BalAmount'] = $value['BalAmount'] - $res['Total'];
							}	
						}
						
					}
							
					
			   }		
			}
		}
		else
		{
			$retData = array();
			$BalAmount = 0;
			if($groupID == LIABILITY)
			{
				$res = $this->obj_utility->getOpeningBalanceOfCategory(LIABILITY, getDBFormatDate($_SESSION['default_year_start_date']) ,true);
				
			}
			else if($groupID == ASSET)
			{
				$res = $this->obj_utility->getOpeningBalanceOfCategory(ASSET, getDBFormatDate($_SESSION['default_year_start_date']) ,true);
				
			}
			if($res <> "")
			{
				$BalAmount = $res['Total'];
			}
			array_push($retData , array('date' => 'April' ,'BalAmount' => $BalAmount ));		
			
		}
		return $retData;
			
		
	}
	
	public function GetTotalMemberDues()
	{
		//$sqlQuery = "SELECT SUM( Debit ) AS payments, SUM( Credit ) AS receipts FROM assetregister where SubCategoryID=2";
		$sqlQuery = "SELECT SUM( asset.Debit ) AS receipts, SUM( asset.Credit ) AS payments,monthname( asset.date) as date,(SUM( asset.Debit ) - SUM( asset.Credit )) as BalAmount FROM assetregister as asset  JOIN ledger as led ON led.id = asset.LedgerID where led.society_id = '" . DEFAULT_SOCIETY . "' and  asset.SubCategoryID='".$_SESSION['default_due_from_member']."' ";
		if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
		{
			
			$sqlQuery .= "  and asset.Date BETWEEN '".getDBFormatDate($_SESSION['default_year_start_date'])."' AND '".getDBFormatDate($_SESSION['default_year_end_date'])."'";					
		}
		$sqlQuery .= "  group by YEAR(asset.date),MONTH(asset.date) order by asset.date desc";
		
		//echo "<br>";
		$retData = $this->m_dbConn->select($sqlQuery);
		if(count($retData) > 0)
		{
			foreach($retData as $key => $value)
			{
			   if($value['date'] == 'April')
			   {
					$res = $this->obj_utility->getOpeningBalanceOfCategory(DUE_FROM_MEMBERS, getDBFormatDate($_SESSION['default_year_start_date']));
					if($res <> "")
					{
						if($res['OpeningType'] == TRANSACTION_DEBIT)
						{
							$value['BalAmount'] = $value['BalAmount'] + $res['Total'];
						}
						else
						{
							$value['BalAmount'] = $value['BalAmount'] - $res['Total'];
						}	
					}
				}		
			}
		}
		else
		{
			$retData = array();
			$BalAmount = 0;
			$res = $this->obj_utility->getOpeningBalanceOfCategory(DUE_FROM_MEMBERS, getDBFormatDate($_SESSION['default_year_start_date']));
			
			if($res <> "")
			{
				$BalAmount = $res['Total'];
			}
			array_push($retData , array('date' => 'April' ,'BalAmount' => $BalAmount ));		
			
		}
		
		return $retData;
	}
	
	public function GetLastBillGenerated()
	{
		$sqlQuery = "SELECT sum(bill.TotalBillPayable) as amount,pd.Type FROM billdetails as bill JOIN period as pd on bill.PeriodID = pd.ID JOIN unit as unittbl on bill.UnitID = unittbl.unit_id where unittbl.society_id = '" . $_SESSION['society_id'] . "' ";
		if($_SESSION['default_year_start_date'] <> 0  && $_SESSION['default_year_end_date'] <> 0)
		{
			
			$sqlQuery .= "  and pd.YearID = '".$_SESSION['default_year']."' ";					
		}
		$sqlQuery .= "  group by pd.Type order by pd.ID desc";
		
		//echo $sqlQuery;
		//echo "<br>";
		$retData = $this->m_dbConn->select($sqlQuery);
		//print_r($retData);
		return $retData;
	}
	
	public function GetNEFTDetails()
	{
		$retAry = array();
		$sqlQuery = "select	count(*) as cnt from neft";
		$result = $this->m_dbConn->select($sqlQuery);
		$retAry['total'] = $result[0]['cnt'];
		
		$sqlQuery = "select	count(*) as cnt from neft where approved = 0";
		$result = $this->m_dbConn->select($sqlQuery);
		$retAry['pending'] = $result[0]['cnt'];
		
		return $retAry;
	}
	
	
	public function GetNoticeDetails()
	{
		$todayDate=date('Y-m-d');
		if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER))
		{
			$sql="select DISTINCT noticetbl.id, noticetbl.* FROM notices AS noticetbl,display_notices AS displaynoticetbl WHERE noticetbl.status='Y' and noticetbl.id=displaynoticetbl.notice_id  and noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.exp_date >= '".$todayDate."' ORDER BY noticetbl.id DESC LIMIT 3";
			//echo $sql;
			$result=$this->m_dbConn->select($sql);			
		}
		else
		{
			$sql="select DISTINCT noticetbl.id, noticetbl.* FROM notices AS noticetbl,display_notices AS displaynoticetbl WHERE  noticetbl.status='Y' and noticetbl.id=displaynoticetbl.notice_id and  noticetbl.society_id=".$_SESSION['society_id']." and noticetbl.exp_date >= '".$todayDate."' and  displaynoticetbl.unit_id IN (".$_SESSION['unit_id'].",0) ORDER BY noticetbl.id DESC LIMIT 3";
			//echo $sql;
			$result=$this->m_dbConn->select($sql);
			
		}
	return $result;
			
		
		
	}
	
	public function GetPaymentSummary()
	{
		
		$sql="select * from `chequeentrydetails`  where PaidBy=".$_SESSION['unit_id']."  ORDER BY VoucherDate DESC";
		//echo $sql;
		$result=$this->m_dbConn->select($sql);
		return $result;
		
	}
}

?>