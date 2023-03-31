<?php
include_once "dbconst.class.php";
include_once "utility.class.php";

class BalanceSheet
{
	
	private $m_dbConn;
	public $CreditTotal = 0;
	public $DebitTotal = 0;
	public $CreditTotalPrev = 0;
	public $DebitTotalPrev = 0;
	public $OpeningBalanceCreditTotal = 0;
	public $OpeningBalanceDebitTotal = 0;
	public $ClosingBalanceCreditTotal = 0;
	public $ClosingBalanceDebitTotal = 0;
	public $unsetArray = array();
	public $m_objUtility;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->m_objUtility = new utility($dbConn);
	}
	
	public function CategoryArray($GroupID, $from, $to,$IncomeExpenseAccount = 0,$isAddOpeningBalance = true)
	{
		
		  //echo "<br>From : ".$from;
		  //echo "<br>to : ".$to;
		 $YearID = $this->m_objUtility->getYearIDFromDates($from,$to);
		// echo "YearID ".var_dump($YearID);
		
		//fetch all categories of groupid GroupID 
		$sql = "SELECT *  FROM `account_category` where group_id = ".$GroupID."";
		$data = $this->m_dbConn->select($sql);
		
		//converting category array to tree display format array
		if(isset($_GET['show']) && $_GET['show'] == 0)
		{
			return $this->FormatArray($data,$GroupID, $from, $to,$IncomeExpenseAccount,false,$isAddOpeningBalance,$YearID);
		}
		else
		{
			return $this->FormatArray($data,$GroupID, $from, $to,$IncomeExpenseAccount,true,$isAddOpeningBalance,$YearID);		
		}
	}
	
	public function LedgerArray($GroupID,$SubCategoryID, $from, $to,$IncomeExpenseAccount = 0,$YearID)
	{
	
		//fetching all ledgers of particular group from respective register
		if($GroupID == LIABILITY)
		{	
			//$sql = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,SUM(liabilitytbl.Credit) as credit,SUM(liabilitytbl.Debit) as debit,ledgertbl.id as LedgerID FROM `liabilityregister` as liabilitytbl JOIN `ledger` as ledgertbl on liabilitytbl.LedgerID = ledgertbl.id JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id = ".$SubCategoryID."  and ledgertbl.society_id = ".$_SESSION['society_id']."  GROUP BY ledgertbl.id";
			//$sql = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,SUM(liabilitytbl.Credit) as credit,SUM(liabilitytbl.Debit) as debit,ledgertbl.id as LedgerID FROM `liabilityregister` as liabilitytbl LEFT JOIN `ledger` as ledgertbl on liabilitytbl.LedgerID = ledgertbl.id JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id = ".$SubCategoryID."  and ledgertbl.society_id = ".$_SESSION['society_id']." AND liabilitytbl.Date BETWEEN '".$from."' AND '".$to."' GROUP BY ledgertbl.id";
			$sql = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,ledgertbl.id as LedgerID,SUM(liabilitytbl.Credit) as credit,SUM(liabilitytbl.Debit) as debit FROM `ledger` as ledgertbl LEFT JOIN `liabilityregister` as liabilitytbl on ledgertbl.id = liabilitytbl.LedgerID AND liabilitytbl.Date BETWEEN '".$from."' AND '".$to."' where ledgertbl.society_id = ".$_SESSION['society_id']." AND ledgertbl.categoryid=".$SubCategoryID."  GROUP BY ledgertbl.id ";		
		}
		
		if($GroupID == ASSET)
		{
			if($SubCategoryID == $_SESSION['default_cash_account'] || $SubCategoryID == $_SESSION['default_bank_account'])
			{
				//$sql = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name, SUM( bk.PaidAmount ) AS credit,SUM( bk.ReceivedAmount ) as debit,ledgertbl.id as LedgerID  FROM bankregister as bk JOIN ledger as ledgertbl on ledgertbl.id = bk.LedgerID JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id = ".$SubCategoryID."  and ledgertbl.society_id = ".$_SESSION['society_id']."  GROUP BY ledgertbl.id";
				//$sql = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name, SUM( bk.PaidAmount ) AS credit,SUM( bk.ReceivedAmount ) as debit,ledgertbl.id as LedgerID  FROM bankregister as bk JOIN ledger as ledgertbl on ledgertbl.id = bk.LedgerID JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id = ".$SubCategoryID."  and ledgertbl.society_id = ".$_SESSION['society_id']." AND bk.Date BETWEEN '".$from."' AND '".$to."' GROUP BY ledgertbl.id";
				$sql = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,ledgertbl.id as LedgerID,SUM(bk.PaidAmount) as credit,SUM(bk.ReceivedAmount) as debit FROM `ledger` as ledgertbl LEFT JOIN `bankregister` as bk on ledgertbl.id = bk.LedgerID AND bk.Date BETWEEN '".$from."' AND '".$to."' where ledgertbl.society_id = ".$_SESSION['society_id']." AND ledgertbl.categoryid= ".$SubCategoryID." GROUP BY ledgertbl.id ";
				//$sql = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,ledgertbl.id as LedgerID,SUM(bk.ReceivedAmount) as credit,SUM(bk.PaidAmount) as debit FROM `ledger` as ledgertbl LEFT JOIN `bankregister` as bk on ledgertbl.id = bk.LedgerID AND bk.Date BETWEEN '".$from."' AND '".$to."' where ledgertbl.society_id = ".$_SESSION['society_id']." AND ledgertbl.categoryid= ".$SubCategoryID." GROUP BY ledgertbl.id ";
			}
			else
			{
				//$sql = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,SUM(assettbl.Credit) as credit,SUM(assettbl.Debit) as debit,ledgertbl.id as LedgerID FROM `assetregister` as assettbl JOIN `ledger` as ledgertbl on assettbl.LedgerID = ledgertbl.id JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id = ".$SubCategoryID." and ledgertbl.society_id = ".$_SESSION['society_id']."  GROUP BY ledgertbl.id";
				//$sql = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,SUM(assettbl.Credit) as credit,SUM(assettbl.Debit) as debit,ledgertbl.id as LedgerID FROM `assetregister` as assettbl JOIN `ledger` as ledgertbl on assettbl.LedgerID = ledgertbl.id JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id = ".$SubCategoryID." and ledgertbl.society_id = ".$_SESSION['society_id']." AND assettbl.Date BETWEEN '".$from."' AND '".$to."' GROUP BY ledgertbl.id";
				$sql = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,ledgertbl.id as LedgerID,SUM(assettbl.Credit) as credit,SUM(assettbl.Debit) as debit FROM `ledger` as ledgertbl LEFT JOIN `assetregister` as assettbl on ledgertbl.id = assettbl.LedgerID AND assettbl.Date BETWEEN '".$from."' AND '".$to."' where ledgertbl.society_id = ".$_SESSION['society_id']." AND ledgertbl.categoryid=".$SubCategoryID." GROUP BY ledgertbl.id ";
			}
		}
		
		if($GroupID == INCOME)
		{
			//$sql = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,SUM(incometbl.Credit) as credit,SUM(incometbl.Debit) as debit,ledgertbl.id as LedgerID FROM `incomeregister` as incometbl JOIN `ledger` as ledgertbl on incometbl.LedgerID = ledgertbl.id JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id = ".$SubCategoryID."  and ledgertbl.society_id = ".$_SESSION['society_id']."  GROUP BY ledgertbl.id";
			//$sql = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,SUM(incometbl.Credit) as credit,SUM(incometbl.Debit) as debit,ledgertbl.id as LedgerID FROM `incomeregister` as incometbl JOIN `ledger` as ledgertbl on incometbl.LedgerID = ledgertbl.id JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id = ".$SubCategoryID."  and ledgertbl.society_id = ".$_SESSION['society_id']." AND incometbl.Date BETWEEN '".$from."' AND '".$to."' GROUP BY ledgertbl.id";
			$sql = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,ledgertbl.id as LedgerID,SUM(incometbl.Credit) as credit,SUM(incometbl.Debit) as debit FROM `ledger` as ledgertbl LEFT JOIN `incomeregister` as incometbl on ledgertbl.id = incometbl.LedgerID AND incometbl.Date BETWEEN '".$from."' AND '".$to."' where ledgertbl.society_id = ".$_SESSION['society_id']." AND ledgertbl.categoryid=".$SubCategoryID." GROUP BY ledgertbl.id";
			$sqlPrev = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,ledgertbl.id as LedgerID,SUM(IF(incometbl.Credit IS NULL,'0.00',incometbl.Credit)) as creditPrev,SUM(IF(incometbl.Debit IS NULL,'0.00',incometbl.Debit)) as debitPrev FROM `ledger` as ledgertbl LEFT JOIN `incomeregister` as incometbl on ledgertbl.id = incometbl.LedgerID AND incometbl.Date BETWEEN '".$YearID[0]['BeginingDatePrevYear']."' AND '".$YearID[0]['EndingDatePrevYear']."' where ledgertbl.society_id = ".$_SESSION['society_id']." AND ledgertbl.categoryid=".$SubCategoryID." GROUP BY ledgertbl.id";
		}
		
		if($GroupID == EXPENSE)
		{
			//$sql = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,SUM(expensetbl.Credit) as credit,SUM(expensetbl.Debit) as debit,ledgertbl.id as LedgerID FROM `expenseregister` as expensetbl JOIN `ledger` as ledgertbl on expensetbl.LedgerID = ledgertbl.id JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id = ".$SubCategoryID."  and ledgertbl.society_id = ".$_SESSION['society_id']."  GROUP BY ledgertbl.id";
			//$sql = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,SUM(expensetbl.Credit) as credit,SUM(expensetbl.Debit) as debit,ledgertbl.id as LedgerID FROM `expenseregister` as expensetbl JOIN `ledger` as ledgertbl on expensetbl.LedgerID = ledgertbl.id JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id = ".$SubCategoryID."  and ledgertbl.society_id = ".$_SESSION['society_id']." AND expensetbl.Date BETWEEN '".$from."' AND '".$to."' GROUP BY ledgertbl.id";
			$sql = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,ledgertbl.id as LedgerID,SUM(expensetbl.Credit) as credit,SUM(expensetbl.Debit) as debit FROM `ledger` as ledgertbl LEFT JOIN `expenseregister` as expensetbl on ledgertbl.id = expensetbl.LedgerID AND expensetbl.Date BETWEEN '".$from."' AND '".$to."' where ledgertbl.society_id = ".$_SESSION['society_id']." AND ledgertbl.categoryid=".$SubCategoryID." GROUP BY ledgertbl.id ";
			$sqlPrev = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,ledgertbl.id as LedgerID,SUM(IF(expensetbl.Credit IS NULL,'0.00',expensetbl.Credit)) as creditPrev,SUM(IF(expensetbl.Debit IS NULL,'0.00',expensetbl.Debit)) as debitPrev FROM `ledger` as ledgertbl LEFT JOIN `expenseregister` as expensetbl on ledgertbl.id = expensetbl.LedgerID AND expensetbl.Date BETWEEN '".$YearID[0]['BeginingDatePrevYear']."' AND '".$YearID[0]['EndingDatePrevYear']."' where ledgertbl.society_id = ".$_SESSION['society_id']." AND ledgertbl.categoryid=".$SubCategoryID." GROUP BY ledgertbl.id ";
		}	
		
		//echo "<br><br><br> sql: ".$sql;
		$data = $this->m_dbConn->select($sql);
		
		//previous years records for income and expense only
		if($GroupID == INCOME  ||  $GroupID == EXPENSE)
		{
			//echo "<br><br><br><br> Presql: ".$sqlPrev;
			$data2 = $this->m_dbConn->select($sqlPrev);
		}
		
		
		$output = array();
		//to merge previous credit and debit amount to original array
		if(sizeof($data2) > 0)
		{
			$arrayAB = array_merge($data, $data2);
			foreach ( $arrayAB as $value ) 
			{
			  $id = $value['LedgerID'];
			  if ( !isset($output[$id]) ) {
				$output[$id] = array();
				$output[$id]['creditPrev'] = '0';
				$output[$id]['debitPrev'] = '0';
			  }
			  $output[$id] = array_merge($output[$id], $value);
			}
			$output = array_values($output);
		}
		else
		{
				$output = $data;
		}
	return $output;	
}
	
	public function setAmount($datas, $parent, $creditamount,$creditamountPrev,$debitamount, $debitamountPrev,$maxId,$openingBalanceCredit,$openingBalanceDebit,$closingBalanceCredit,$closingBalanceDebit)
	{
		//adding credit and debit amount of ledgers and setting  to ledgers main parent
		$total = 0;
		
		//for($iCnt = $maxId-1; $iCnt >= 0  ; $iCnt--)
		for($iCnt = sizeof($datas)-1; $iCnt >= 0  ; $iCnt--)
		{
			if($datas[$iCnt]['id'] == $parent)
			{
			   $datas[$iCnt]['credit'] += $creditamount;
			   $datas[$iCnt]['debit'] += $debitamount;
			   $datas[$iCnt]['creditPrev'] += $creditamountPrev;
			   $datas[$iCnt]['debitPrev'] += $debitamountPrev;
			   $datas[$iCnt]['OpeningBalanceCredit'] += $openingBalanceCredit;
			   $datas[$iCnt]['OpeningBalanceDebit'] += $openingBalanceDebit;
			   $datas[$iCnt]['ClosingBalanceCredit'] += $closingBalanceCredit;
			   $datas[$iCnt]['ClosingBalanceDebit'] += $closingBalanceDebit;
			}
					
		}
		
		return $datas;
	}
	
	public function generateBalanceSheet($datas, $parent = 0,$type, $depth = 0)
	{
		if($depth > 1000) return ''; // Make sure not to have an endless recursion
		
		$tree = '<ul>';
		foreach($datas as $key)
		{
			
			//if($key['parent_id'] == $parent && ($key['credit'] > 0 ||  $key['debit'] > 0))
			if($key['parent_id'] == $parent)
			{
				//if current array element is parent and have credit or debit amount 
				$amount = ($key['credit'] + $key['OpeningBalanceCredit']) - ($key['debit'] + $key['OpeningBalanceDebit']);
				if($type == '2')
				{
					$amount = ($key['OpeningBalanceDebit'] + $key['debit']) - ($key['credit'] + $key['OpeningBalanceCredit']);
				}
				$amountType = '';
				
				if($amount > 0 && $type == '2')
				{
					$amountType = ' Dr';
				}
				elseif($amount < 0 && $type == '2')
				{
					$amountType = ' Cr';	
				}
				elseif($amount > 0 && $type == '1')
				{
					$amountType = ' Cr';	
				}
				elseif($amount < 0 && $type == '1')
				{
					$amountType = ' Dr';	
				}
				
				//$amount = abs($amount);
				if($amount <> 0)
				{
					if($parent == 0 OR $parent == 1)
					{
						$tree .= '<li id="li_' . $key['id'] .'_'.$depth.  '" ><label><span style="border:none;width:250px;">';	
						$tree .= $key['name'].'</span><span style="border:none;width:100px;text-align:right;">'.number_format(abs($amount),2).'</span></label>';
					}
					else
					{
						
						$tree .= '<li id="li_' . $key['id'] .'_'.$depth.  '"  style="display:none;"><label ><span style=" border:none;width:250px;" class="icon-minus-sign">';	
						if($key['LedgerID'] > 0)
						{
							$LedgetUrl = "ledger.php?edt=".$key['LedgerID'];
							$Url = "view_ledger_details.php?lid=".$key['LedgerID']."&gid=".$key['GroupType']."&dt";
							$tree .= '<a href = "'.$LedgetUrl.'" target="_blank">'. $key['name']. '</a></span><span style="border:none;width:100px;text-align:right;" class="icon-minus-sign"><a href = "'.$Url.'" target="_blank">'.number_format(abs($amount),2). $amountType.'</a></span></label>';
						}
						else 
						{
							$tree .= $key['name'].'</span><span style="border:none;width:100px;text-align:right;" class="icon-minus-sign">'.number_format(abs($amount),2). $amountType.'</span></label>';
						}
						
					}
				}
				//display all subtree menus inside parent tree menu
				$tree .= $this->generateBalanceSheet($datas, $key['id'],$type, $depth+1);
				$tree .= '</li>';
			}
			
		}
		
		$tree .= '</ul>';
		return $tree;
	}	

	public function generateBalanceSheet_withPreviousyear($datas, $parent = 0,$type, $depth = 0)
	{
		
		if($depth > 1000) return ''; // Make sure not to have an endless recursion
				
		$tree = '<ul>';
		foreach($datas as $key)
		{
			
			//if($key['parent_id'] == $parent && ($key['credit'] > 0 ||  $key['debit'] > 0))
			if($key['parent_id'] == $parent)
			{
				//if current array element is parent and have credit or debit amount 
				$amount = ($key['credit'] + $key['OpeningBalanceCredit']) - ($key['debit'] + $key['OpeningBalanceDebit']);
				$PreviousYearamount = ($key['PreviousYearcredit'] + $key['PreviousYearOpeningBalanceCredit']) - ($key['PreviousYeardebit'] + $key['PreviousYearOpeningBalanceDebit']);
				
				if($type == '2')
				{
					$PreviousYearamount = ($key['PreviousYearOpeningBalanceDebit'] + $key['PreviousYeardebit']) - ($key['PreviousYearcredit'] + $key['PreviousYearOpeningBalanceCredit']);
					$amount = ($key['OpeningBalanceDebit'] + $key['debit']) - ($key['credit'] + $key['OpeningBalanceCredit']);
				}
				$amountType = '';
				
				if(($amount > 0  || $PreviousYearamount > 0) && $type == '2')
				{
					$amountType = ' Dr';
				}
				elseif(($amount < 0  || $PreviousYearamount < 0 )&& $type == '2')
				{
					$amountType = ' Cr';	

				}
				elseif(($amount > 0  || $PreviousYearamount > 0) && $type == '1')
				{
					$amountType = ' Cr';	
				}
				elseif(($amount < 0  || $PreviousYearamount < 0) && $type == '1')
				{
					$amountType = ' Dr';	
				}
				
				//$amount = abs($amount);
				if($amount <> 0 || $PreviousYearamount <> 0)
				{
					if($parent == 0 OR $parent == 1)
					{
						$tree .= '<li id="li_' . $key['id'] .'_'.$depth.  '" ><label>';	
						$tree .= '<span style="border:none;width:100px;text-align:left;">'.number_format(abs($PreviousYearamount),2).'</span><span style="border:none;width:195px;">'.$key['name'].'</span><span style="border:none;width:100px;text-align:right;">'.number_format(abs($amount),2).'</span></label>';
					}
					else
					{
						
						$tree .= '<li id="li_' . $key['id'] .'_'.$depth.  '"  style="display:none;"><label ><span style="border:none;width:100px;text-align:left;" class="icon-minus-sign">';	
						if($key['LedgerID'] > 0)
						{
							$Url = "view_ledger_details.php?lid=".$key['LedgerID']."&gid=".$key['GroupType']."&dt";
							$tree .= '<a href = "'.$Url.'">'.number_format(abs($PreviousYearamount),2). $amountType.'</a></span><span style=" border:none;width:195px;" class="icon-minus-sign"><a href = "'.$Url.'">'. $key['name']. '</a></span><span style="border:none;width:100px;text-align:right;" class="icon-minus-sign"><a href = "'.$Url.'">'.number_format(abs($amount),2). $amountType.'</a></span></label>';
						}
						else 
						{
							$tree .= number_format(abs($PreviousYearamount),2). $amountType.'</span><span style=" border:none;width:195px;" class="icon-minus-sign">'.$key['name'].'</span><span style="border:none;width:100px;text-align:right;" class="icon-minus-sign">'.number_format(abs($amount),2). $amountType.'</span></label>';
						}
						
					}
				}
				//display all subtree menus inside parent tree menu
				$tree .= $this->generateBalanceSheet_withPreviousyear($datas, $key['id'],$type, $depth+1);
				$tree .= '</li>';
			}
			
		}
		
		$tree .= '</ul>';
		return $tree;
	}
	
	
	
	
	
	
	public function generateBalanceSheetTable_withPreviousYear($datas, $parent = 0,$type, $depth = 0)
	{
		if($depth > 1000) return ''; // Make sure not to have an endless recursion
		
		$tree = '';
		foreach($datas as $key)
		{
			
			//if($key['parent_id'] == $parent && ($key['credit'] > 0 ||  $key['debit'] > 0))
			if($key['parent_id'] == $parent)
			{
				//if current array element is parent and have credit or debit amount 
				//$amount = $key['credit'] - $key['debit'];
				$amount = ($key['credit'] + $key['OpeningBalanceCredit']) - ($key['debit'] + $key['OpeningBalanceDebit']);
				$PreviousYearamount = ($key['PreviousYearcredit'] + $key['PreviousYearOpeningBalanceCredit']) - ($key['PreviousYeardebit'] + $key['PreviousYearOpeningBalanceDebit']);
				if($type == '2')
				{
					//$amount = $key['debit'] - $key['credit'];
					$amount = ($key['OpeningBalanceDebit'] + $key['debit']) - ($key['credit'] + $key['OpeningBalanceCredit']);
					$PreviousYearamount = ($key['PreviousYearOpeningBalanceDebit'] + $key['PreviousYeardebit']) - ($key['PreviousYearcredit'] + $key['PreviousYearOpeningBalanceCredit']);
				}
				if($amount <> 0 || $PreviousYearamount <> 0)
				{
					if($parent == 0 OR $parent == 1)
					{
						$tree .= '<tr id="li_' . $key['id'] .'_'.$depth.  '"><label><td style="text-align:right;width:100px;color:blue;border-right:1px solid black;"><span><u>'.number_format($PreviousYearamount,2).'</u></span></td><td style="width:490px;color:blue;border-right:1px solid black;border-left:1px solid black;"><span><b>';	
						$tree .= $key['name'].'</b></span></td><td style="text-align:right;width:100px;color:blue;border-right:1px solid black;"><span><u></u></span></td><td style="text-align:right;width:100px;color:blue;border-right:1px solid black;"><span><u>'.number_format($amount,2).'</u></span></td></label></tr>';
					}
					else
					{
						
							$tree .= '<tr id="li_' . $key['id'] .'_'.$depth.  '" ><label ><td style="text-align:right;width:100px;border-right:1px solid black;" ><span>'.number_format($PreviousYearamount,2).'</span></td><td style=" width:490px;border-right:1px solid black;border-left:1px solid black;" ><span>';	
							if($key['LedgerID'] > 0)
							{
								$tree .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$key['name']. '</span></td><td style="text-align:right;width:100px;border-right:1px solid black;" ><span>'.number_format($amount,2).'</span></td><td style="text-align:right;width:100px;border-right:1px solid black;" ><span></span></td></label></tr>';
							}
							else
							{
								$tree .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$key['name'].'</b></span></td><td style="text-align:right;width:100px;border-right:1px solid black;" ><span></span></td><td style="text-align:right;width:100px;border-right:1px solid black;" ><span><b><u>'.number_format($amount,2).'</u></b></span></td></label></tr>';
							}
						
					}
				}
				//display all subtree menus inside parent tree menu
				$tree .= $this->generateBalanceSheetTable_withPreviousYear($datas, $key['id'],$type, $depth+1);
			}
			
		}
		return $tree;
	}
	
		public function generateBalanceSheetTable($datas, $parent = 0,$type, $depth = 0)
	{
		if($depth > 1000) return ''; // Make sure not to have an endless recursion
		
		$tree = '';
		foreach($datas as $key)
		{
			
			//if($key['parent_id'] == $parent && ($key['credit'] > 0 ||  $key['debit'] > 0))
			if($key['parent_id'] == $parent)
			{
				//if current array element is parent and have credit or debit amount 
				//$amount = $key['credit'] - $key['debit'];
				$amount = ($key['credit'] + $key['OpeningBalanceCredit']) - ($key['debit'] + $key['OpeningBalanceDebit']);
				if($type == '2')
				{
					//$amount = $key['debit'] - $key['credit'];
					$amount = ($key['OpeningBalanceDebit'] + $key['debit']) - ($key['credit'] + $key['OpeningBalanceCredit']);
				}
				if($amount <> 0)
				{
					if($parent == 0 OR $parent == 1)
					{
						$tree .= '<tr id="li_' . $key['id'] .'_'.$depth.  '"><label><td style="width:490px;color:blue;border-right:1px solid black;border-left:1px solid black;"><span><b>';	
						$tree .= $key['name'].'</b></span></td><td style="text-align:right;width:100px;color:blue;border-right:1px solid black;"><span><u></u></span></td><td style="text-align:right;width:100px;color:blue;border-right:1px solid black;"><span><u>'.number_format($amount,2).'</u></span></td></label></tr>';
					}
					else
					{
						
							$tree .= '<tr id="li_' . $key['id'] .'_'.$depth.  '" ><label ><td style=" width:490px;border-right:1px solid black;border-left:1px solid black;" ><span>';	
							if($key['LedgerID'] > 0)
							{
								$tree .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$key['name']. '</span></td><td style="text-align:right;width:100px;border-right:1px solid black;" ><span>'.number_format($amount,2).'</span></td><td style="text-align:right;width:100px;border-right:1px solid black;" ><span></span></td></label></tr>';
							}
							else
							{
								$tree .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$key['name'].'</b></span></td><td style="text-align:right;width:100px;border-right:1px solid black;" ><span></span></td><td style="text-align:right;width:100px;border-right:1px solid black;" ><span><b><u>'.number_format($amount,2).'</u></b></span></td></label></tr>';
							}
						
					}
				}
				//display all subtree menus inside parent tree menu
				$tree .= $this->generateBalanceSheetTable($datas, $key['id'],$type, $depth+1);
			}
			
		}
		return $tree;
	}

	public function generateTrialBalance($datas, $parent = 0, $setZero = false)
	{
		$closingBalanceDebit = 0;
		$closingBalanceCredit = 0;
		$amount = array();
		if($depth > 1000) return ''; // Make sure not to have an endless recursion
		
		$tree = '<ul >';
		for($i = 0, $ni = count($datas); $i < $ni; $i++)
		{
			//if($datas[$i]['parent_id'] == $parent && ($datas[$i]['credit'] <> 0 ||  $datas[$i]['debit'] <> 0))
			if($setZero == true)
			{
				$datas[$i]['OpeningBalanceCredit'] = 0;
				$datas[$i]['OpeningBalanceDebit'] = 0;		
			}
			if($datas[$i]['parent_id'] == $parent)
			{
				//if current array element is parent and have credit or debit amount 
				$closingBalanceCredit = 0;
				$closingBalanceDebit =	0;	

				if($parent == 0 || $parent == 1)
				{
					//current tree menu is parent
					
					if($datas[$i]['OpeningBalanceCredit'] >= $datas[$i]['credit'])
					{
						$TrnxCredit = $datas[$i]['OpeningBalanceCredit'] - $datas[$i]['credit'];
					}
					else
					{
						$TrnxCredit = $datas[$i]['credit'] - $datas[$i]['OpeningBalanceCredit']  ;
					}
					
					if($datas[$i]['OpeningBalanceDebit'] >= $datas[$i]['debit'])
					{
						$TrnxDebit = $datas[$i]['OpeningBalanceDebit'] - $datas[$i]['debit'];
					}
					else
					{
						$TrnxDebit = $datas[$i]['debit'] - $datas[$i]['OpeningBalanceDebit']  ;
					}
					
					if($datas[$i]['GroupType'] == LIABILITY ||  $datas[$i]['GroupType'] == ASSET)
					{
						$balance =( $datas[$i]['OpeningBalanceCredit'] + $datas[$i]['credit']) - ($datas[$i]['OpeningBalanceDebit'] + $datas[$i]['debit']);
					}
					else
					{
						$balance = $datas[$i]['credit']  - $datas[$i]['debit'];
					}
					//calculate closing balance
					if($balance < 0)
					{
						$closingBalanceDebit = abs($balance);	
					}
					else if($balance > 0)
					{
						$closingBalanceCredit = $balance;	
					}
					else
					{
						$closingBalanceCredit = 0;
						$closingBalanceDebit =	0;	
					}
					
					if( $datas[$i]['id'] == CURRENT_ASSET && $datas[$i]['ClosingBalanceCredit'] <> 0 )
					{
						$closingBalanceCredit = $datas[$i]['ClosingBalanceCredit'];
						$closingBalanceDebit =	$datas[$i]['ClosingBalanceDebit'];	
					}
					
					
					//echo "Q:".$_GET['q'];
					if(!isset($_GET['q']) || (isset($_GET['q']) && $_GET['q'] == 2))
					{
						//detailed tree view i.e q parameter set and equal to 2 or q not set
						$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '" style="border:none;"><label style="width:100%;"><div style="width:100%;border:none;">';	
						$tree .= '<div style="border:none;width:24%; float:left;" >';
					 	$tree .= $datas[$i]['name'].'</a></div><div style="border:none;width:12.5%;text-align:right;float:left;">'.number_format($datas[$i]['OpeningBalanceCredit'],2).'</div>
								 <div style="border:none;width:12.5%;text-align:right;float:left;">'.number_format($datas[$i]['OpeningBalanceDebit'],2).'</div>
								 <div style="border:none;width:12.5%;text-align:right;float:left;">'.number_format($datas[$i]['credit'],2).'</div>
								 <div style="border:none;width:12.5%;text-align:right;float:left;">'.number_format($datas[$i]['debit'],2).'</div>
								 <div style="border:none;width:12.5%;text-align:right;float:left;">'.number_format($closingBalanceCredit,2).'</div>
								 <div style="border:none;width:12.5%;text-align:right;float:right;">'.number_format($closingBalanceDebit,2).'</div></div></label>';
						
					}
					else
					{
						//summary tree view i.e q=1
						if(isset($_GET['show']) &&  $_GET['show'] == 1)
						{
							$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '" style="border:none;"><label style="width:100%;"><div style="width:100%;border:none;">';	
							$tree .= '<div style="border:none;width:60%; float:left;" >';
							$tree .= $datas[$i]['name'].'</a></div><div style="border:none;width:17.5%;text-align:right;float:left;display:none">'.number_format($datas[$i]['credit'],2).'</div>
									<div style="border:none;width:17.5%;text-align:right;float:left;display:none">'.number_format($datas[$i]['debit'],2).'</div>
									<div style="border:none;width:20%;text-align:right;float:left;">'.number_format($closingBalanceCredit,2).'</div>
									<div style="border:none;width:20%;text-align:right;float:left;">'.number_format($closingBalanceDebit,2).'</div></div></label>';	
						}
						else 	if(isset($_GET['show']) &&  $_GET['show'] == 0 &&  ($closingBalanceCredit <> 0 || $closingBalanceDebit <> 0))
						{
								$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '" style="border:none;"><label style="width:100%;"><div style="width:100%;border:none;">';	
								$tree .= '<div style="border:none;width:60%; float:left;" >';
								$tree .= $datas[$i]['name'].'</a></div><div style="border:none;width:17.5%;text-align:right;float:left;display:none">'.number_format($datas[$i]['credit'],2).'</div>
									<div style="border:none;width:17.5%;text-align:right;float:left;display:none">'.number_format($datas[$i]['debit'],2).'</div>
									<div style="border:none;width:20%;text-align:right;float:left;">'.number_format($closingBalanceCredit,2).'</div>
									<div style="border:none;width:20%;text-align:right;float:left;">'.number_format($closingBalanceDebit,2).'</div></div></label>';	
						}
					}
					$balance = 0;
				}
				else
				{
					
					
					if($datas[$i]['LedgerID'] > 0)
					{
						//display all ledgers
						$Url = "view_ledger_details.php?lid=".$datas[$i]['LedgerID']."&gid=".$datas[$i]['GroupType']."&dt";
						$LedgetUrl = "ledger.php?edt=".$datas[$i]['LedgerID'];
						if($datas[$i]['OpeningBalanceCredit'] >= $datas[$i]['credit'])
						{
							$TrnxCredit1 = $datas[$i]['OpeningBalanceCredit'] - $datas[$i]['credit'];
						}
						else
						{
							$TrnxCredit1 = $datas[$i]['credit'] - $datas[$i]['OpeningBalanceCredit']  ;
						}
						if($datas[$i]['OpeningBalanceDebit'] >= $datas[$i]['debit'])
						{
							$TrnxDebit1 = $datas[$i]['OpeningBalanceDebit'] - $datas[$i]['debit'];
						}
						else
						{
							$TrnxDebit1 = $datas[$i]['debit'] - $datas[$i]['OpeningBalanceDebit']  ;
						}
						if(!isset($_GET['q']) || (isset($_GET['q']) && $_GET['q'] == 2))
						{
							//detailed tree view i.e q parameter set and equal to 2 or q not set
							$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '"  style="display:none;"><label style="width:100%;"><div style="width:100%;border:none;">';	
							$tree .= '<div style="border:none;width:24%; float:left;" class="icon-minus-sign">';
							$tree .= '<a href = "'.$LedgetUrl.'" target="_blank">'.$datas[$i]['name'].'</a></div><div style="border:none;width:12.5%;text-align:right;float:left;" class="icon-minus-sign"><a href = "'.$Url.'" target="_blank">'.number_format($datas[$i]['OpeningBalanceCredit'],2).'</a></div>
									<div style="border:none;width:12.5%;text-align:right;float:left;" class="icon-minus-sign"><a href="'.$Url.'" target="_blank">'.number_format($datas[$i]['OpeningBalanceDebit'],2).'</a></div>
									<div style="border:none;width:12.5%;text-align:right;float:left;" class="icon-minus-sign"><a href="'.$Url.'" target="_blank">'.number_format($datas[$i]['credit'],2).'</a></div>
									<div style="border:none;width:12.5%;text-align:right;float:left;"><a href = "'.$Url.'" target="_blank">'.number_format($datas[$i]['debit'],2).'</a></div>
									<div style="border:none;width:12.5%;text-align:right;float:left;"><a href = "'.$Url.'" target="_blank">'.number_format($datas[$i]['ClosingBalanceCredit'],2).'</a></div>
									<div style="border:none;width:12.5%;text-align:right;float:right;"><a href = "'.$Url.'" target="_blank">'.number_format($datas[$i]['ClosingBalanceDebit'],2).'</a></div></label>';
						}
						else 
						{
							//summary tree view i.e q=1
									if(isset($_GET['show']) &&  $_GET['show'] == 1)
									{
											$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '"  style="display:none;"><label style="width:100%;"><div style="width:100%;border:none;">';	
											$tree .= '<div style="border:none;width:60%; float:left;" class="icon-minus-sign">';
											$tree .= '<a href = "'.$LedgetUrl.'" target="_blank">'.$datas[$i]['name'].'</a></div><div style="border:none;width:17.5%;text-align:right;float:left;display:none" class="icon-minus-sign"><a href="'.$Url.'" target="_blank">'.number_format($datas[$i]['credit'],2).'</a></div>
													<div style="border:none;width:17.5%;text-align:right;float:left;display:none"><a href="'.$Url.'" target="_blank">'.number_format($datas[$i]['debit'],2).'</a></div>
													<div style="border:none;width:20%;text-align:right;float:left;"><a href = "'.$Url.'" target="_blank">'.number_format($datas[$i]['ClosingBalanceCredit'],2).'</a></div>
													<div style="border:none;width:20%;text-align:right;float:left;"><a href = "'.$Url.'" target="_blank">'.number_format($datas[$i]['ClosingBalanceDebit'],2).'</a></div></label>';	
									}
									else 	if(isset($_GET['show']) &&  $_GET['show'] == 0 &&  ($datas[$i]['ClosingBalanceCredit'] <> 0 || $datas[$i]['ClosingBalanceDebit'] <> 0))
									{
												$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '"  style="display:none;"><label style="width:100%;"><div style="width:100%;border:none;">';	
												$tree .= '<div style="border:none;width:60%; float:left;" class="icon-minus-sign">';
												$tree .= '<a href = "'.$LedgetUrl.'" target="_blank">'.$datas[$i]['name'].'</a></div><div style="border:none;width:17.5%;text-align:right;float:left;display:none" class="icon-minus-sign"><a href="'.$Url.'" target="_blank">'.number_format($datas[$i]['credit'],2).'</a></div>
														<div style="border:none;width:17.5%;text-align:right;float:left;display:none"><a href="'.$Url.'" target="_blank">'.number_format($datas[$i]['debit'],2).'</a></div>
														<div style="border:none;width:20%;text-align:right;float:left;"><a href = "'.$Url.'" target="_blank">'.number_format($datas[$i]['ClosingBalanceCredit'],2).'</a></div>
														<div style="border:none;width:20%;text-align:right;float:left;"><a href = "'.$Url.'" target="_blank">'.number_format($datas[$i]['ClosingBalanceDebit'],2).'</a></div></label>';	
									}	
						}
					}
					else
					{
						if($datas[$i]['OpeningBalanceCredit'] >= $datas[$i]['credit'])
						{
							$TrnxCredit2 = $datas[$i]['OpeningBalanceCredit'] - $datas[$i]['credit'];
						}
						else
						{
							$TrnxCredit2 = $datas[$i]['credit'] - $datas[$i]['OpeningBalanceCredit']  ;
						}
						
						if($datas[$i]['OpeningBalanceDebit'] >= $datas[$i]['debit'])
						{
							$TrnxDebit2 = $datas[$i]['OpeningBalanceDebit'] - $datas[$i]['debit'];
						}
						else
						{
							$TrnxDebit2 = $datas[$i]['debit'] - $datas[$i]['OpeningBalanceDebit']  ;
						}
						
						if(!isset($_GET['q']) || (isset($_GET['q']) && $_GET['q'] == 2))
						{
							//detailed tree view i.e q parameter set and equal to 2 or q not set
							$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '"  style="display:none;"><label style="width:100%;"><div style="width:100%;border:none;">';	
     						$tree .= '<div style="border:none;width:24%; float:left;">';
							$tree .= $datas[$i]['name'].'</div><div style="border:none;width:12.5%;text-align:right;float:left;" class="icon-minus-sign">'.number_format($datas[$i]['OpeningBalanceCredit'],2).'</div>
									<div style="border:none;width:12.5%;text-align:right;float:left;" class="icon-minus-sign">'.number_format($datas[$i]['OpeningBalanceDebit'],2).'</div>
									<div style="border:none;width:12.5%;text-align:right;float:left;" class="icon-minus-sign">'.number_format($datas[$i]['credit'],2).'</div>
									<div style="border:none;width:12.5%;text-align:right;float:left;">'.number_format($datas[$i]['debit'],2).'</div>
									<div style="border:none;width:12.5%;text-align:right;float:left;">'.number_format($datas[$i]['ClosingBalanceCredit'],2).'</div>
									<div style="border:none;width:12.5%;text-align:right;float:right;">'.number_format($datas[$i]['ClosingBalanceDebit'],2).'</div></div></label>';
						}
						else
						{
							//summary tree view i.e q=1
								if(isset($_GET['show']) &&  $_GET['show'] == 1)
									{
											$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '"  style="display:none;"><label style="width:100%;"><div style="width:100%;border:none;">';	
											$tree .= '<div style="border:none;width:60%; float:left;">';
											$tree .= $datas[$i]['name'].'</div><div style="border:none;width:17.5%;text-align:right;float:left;display:none" class="icon-minus-sign">'.number_format($datas[$i]['credit'],2).'</div>
													<div style="border:none;width:17.5%;text-align:right;float:left;display:none">'.number_format($datas[$i]['debit'],2).'</div>
													<div style="border:none;width:20%;text-align:right;float:left;">'.number_format($datas[$i]['ClosingBalanceCredit'],2).'</div>
													<div style="border:none;width:20%;text-align:right;float:left;">'.number_format($datas[$i]['ClosingBalanceDebit'],2).'</div></div></label>';
									}
									else 	if(isset($_GET['show']) &&  $_GET['show'] == 0 &&  ($datas[$i]['ClosingBalanceCredit'] <> 0 || $datas[$i]['ClosingBalanceDebit'] <> 0))
									{
												$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '"  style="display:none;"><label style="width:100%;"><div style="width:100%;border:none;">';	
												$tree .= '<div style="border:none;width:60%; float:left;">';
												$tree .= $datas[$i]['name'].'</div><div style="border:none;width:17.5%;text-align:right;float:left;display:none" class="icon-minus-sign">'.number_format($datas[$i]['credit'],2).'</div>
														<div style="border:none;width:17.5%;text-align:right;float:left;display:none">'.number_format($datas[$i]['debit'],2).'</div>
														<div style="border:none;width:20%;text-align:right;float:left;">'.number_format($datas[$i]['ClosingBalanceCredit'],2).'</div>
														<div style="border:none;width:20%;text-align:right;float:left;">'.number_format($datas[$i]['ClosingBalanceDebit'],2).'</div></div></label>';
									}			
							
						}
					}
					
				}
				
				$padding += 10;
				$tree .= $this->generateTrialBalance($datas, $datas[$i]['id'],$setZero);
				$padding -= 10;
				$tree .= '</li>';
			}
			
		}
		
		$tree .= '</ul>';
		return $tree;
	}
	
	

	
	public function generateTrialBalanceTable($datas, $parent = 0,$setZero = false,$subparentNode = "",$endTreeDisplayArray ='')
	{
		
		$amount = array();
		$closingBalanceDebit = 0;
		$closingBalanceCredit = 0;
		$Str = $subparentNode;
		$childCounter = 0;
		
		if($depth > 1000) return ''; // Make sure not to have an endless recursion
		
		$tree = '';
		for($i = 0, $ni = count($datas); $i < $ni; $i++)
		{
			$TrnxCredit = 0;
			$TrnxDebit = 0;
			$childCounter++;
			$closingBalanceDebit = 0;
			$closingBalanceCredit = 0;
			
			if($setZero == true)
			{
				$datas[$i]['OpeningBalanceCredit'] = 0;
				$datas[$i]['OpeningBalanceDebit'] = 0;		
			}
			
			if($datas[$i]['OpeningBalanceCredit'] >= $datas[$i]['credit'])
			{
				$TrnxCredit = $datas[$i]['OpeningBalanceCredit'] - $datas[$i]['credit'];
			}
			else
			{
				$TrnxCredit = $datas[$i]['credit'] - $datas[$i]['OpeningBalanceCredit']  ;
			}
			
			if($datas[$i]['OpeningBalanceDebit'] >= $datas[$i]['debit'])
			{
				$TrnxDebit = $datas[$i]['OpeningBalanceDebit'] - $datas[$i]['debit'];
			}
			else
			{
				$TrnxDebit = $datas[$i]['debit'] - $datas[$i]['OpeningBalanceDebit']  ;
			}
			
			//if($datas[$i]['parent_id'] == $parent && ($datas[$i]['credit'] <> 0 ||  $datas[$i]['debit'] <> 0))
			if($datas[$i]['parent_id'] == $parent)
			{
				if($parent == 0 OR $parent == 1)
				{
					//$balance = $datas[$i]['credit'] - $datas[$i]['debit'];
					$balance =( $datas[$i]['OpeningBalanceCredit'] + $datas[$i]['credit']) - ($datas[$i]['OpeningBalanceDebit'] + $datas[$i]['debit']);					
					//calculate closing balance
					if($balance < 0)
					{
						$closingBalanceDebit = abs($balance);	
					}
					else if($balance > 0)
					{
						$closingBalanceCredit = $balance;	
					}
					else
					{
						$closingBalanceCredit = 0;
						$closingBalanceDebit =	0;	
					}
					if( $datas[$i]['id'] == CURRENT_ASSET && $datas[$i]['ClosingBalanceCredit'] <> 0 )
					{
						$closingBalanceCredit = $datas[$i]['ClosingBalanceCredit'];
						$closingBalanceDebit =	$datas[$i]['ClosingBalanceDebit'];	
					}
					
					if(!isset($_GET['q']) || (isset($_GET['q']) && $_GET['q'] == 2))
					{
						//detailed tree view i.e q parameter set and equal to 2 or q not set
						$tree .= '<tr id="li_' . $datas[$i]['id'] .'_'.$depth.  '" class="rowstyle" ><label>';	
						$tree .= '<td style="padding-left:20px;"><b><span >'.$datas[$i]['name'].'</b></span></td>
								  <td style="text-align:right;border-right:1px solid black;"><span ><b><u></u></b></span></td>
								  <td style="text-align:right;border-right:1px solid black;"><span ><b><u></u></b></span></td>
								  <td style="text-align:right;border-right:1px solid black;"><span ><b><u></u></b></span></td>
								  <td style="text-align:right;border-right:1px solid black;"><span ><b><u></u></b></span></td>
								  <td style="text-align:right;border-right:1px solid black;"><span ><b><u></u></b></span></td>
								  <td style="text-align:right;border-right:1px solid black;"><span ><b><u></u></b></span></td></label></tr>';
						
						$subparentNode = $datas[$i]['name'];
						$datas[$i]['credit'] = $TrnxCredit; 
						$datas[$i]['debit'] = $TrnxDebit;
						$datas[$i]['ClosingBalanceCredit'] = $closingBalanceCredit ; 
						$datas[$i]['ClosingBalanceDebit'] = $closingBalanceDebit;
						$endTreeDisplayArray = $datas[$i];
					}
					else
					{
						//summary tree view i.e q=1
						
						
						if(isset($_GET['show']) &&  $_GET['show'] == 1)
						{
								$tree .= '<tr id="li_' . $datas[$i]['id'] .'_'.$depth.  '" class="rowstyle" ><label>';	
								$tree .= '<td style="padding-left:20px;"><span ><b>'.$datas[$i]['name'].'</b></span></td>
										  <td style="text-align:right;"><span ></span></td>
										  <td style="text-align:right;"><span ></span></td></label></tr>';
			
								$subparentNode = $datas[$i]['name'];
								$datas[$i]['credit'] = $TrnxCredit; 
								$datas[$i]['debit'] = $TrnxDebit;
								$datas[$i]['ClosingBalanceCredit'] = $closingBalanceCredit ; 
								$datas[$i]['ClosingBalanceDebit'] = $closingBalanceDebit;
								$endTreeDisplayArray = $datas[$i];
						}
						else 	if(isset($_GET['show']) &&  $_GET['show'] == 0 &&  ($datas[$i]['ClosingBalanceCredit'] <> 0 || $datas[$i]['ClosingBalanceDebit'] <> 0))
						{
									$tree .= '<tr id="li_' . $datas[$i]['id'] .'_'.$depth.  '" class="rowstyle" ><label>';	
									$tree .= '<td style="padding-left:20px;"><span ><b>'.$datas[$i]['name'].'</b></span></td>
											  <td style="text-align:right;"><span ></span></td>
											  <td style="text-align:right;"><span ></span></td></label></tr>';
				
									$subparentNode = $datas[$i]['name'];
									$datas[$i]['credit'] = $TrnxCredit; 
									$datas[$i]['debit'] = $TrnxDebit;
									$datas[$i]['ClosingBalanceCredit'] = $closingBalanceCredit ; 
									$datas[$i]['ClosingBalanceDebit'] = $closingBalanceDebit;
									$endTreeDisplayArray = $datas[$i];
						}		
						
					}
					$balance = 0;
				}
				else
				{
					if($datas[$i]['LedgerID'] > 0)
					{
						if(!isset($_GET['q']) || (isset($_GET['q']) && $_GET['q'] == 2))
						{
							//detailed tree view i.e q parameter set and equal to 2 or q not set
							$tree .= '<tr id="li_' . $datas[$i]['id'] .'_'.$depth.  '"  class="rowstyle"><label ><td style="padding-left:20px;"><span style="width:300px;" >';	
							$tree .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$datas[$i]['name'].'</span></td>
									<td style="text-align:right;border-left:1px solid black;border-right:1px dashed black;"><span >'.number_format($datas[$i]['OpeningBalanceCredit'],2).'</span></td>
									<td style="text-align:right;border-right:1px dashed black;"><span >'.number_format($datas[$i]['OpeningBalanceDebit'],2).'</span></td>
									<td style="text-align:right;border-right:1px dashed black;" ><span >'.number_format($datas[$i]['credit'],2).'</span></td>
									<td style="text-align:right;border-right:1px dashed black;"><span >'.number_format($datas[$i]['debit'],2).'</span></td>
									<td style="text-align:right;border-right:1px dashed black;"><span >'.number_format($datas[$i]['ClosingBalanceCredit'],2).'</span></td>
									<td style="text-align:right;border-right:1px dashed black;"><span >'.number_format($datas[$i]['ClosingBalanceDebit'],2).'</span></td></label></tr>';
						}
						else
						{
							//summary tree view i.e q=1
									if(isset($_GET['show']) &&  $_GET['show'] == 1)
									{
											$tree .= '<tr id="li_' . $datas[$i]['id'] .'_'.$depth.  '"  class="rowstyle"><label ><td style="padding-left:20px;"><span style="width:300px;" >';	
											$tree .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$datas[$i]['name'].'</span></td>
														<td style="text-align:right;"><span >'.number_format($datas[$i]['ClosingBalanceCredit'],2).'</span></td>
														<td style="text-align:right;"><span >'.number_format($datas[$i]['ClosingBalanceDebit'],2).'</span></td></label></tr>';
									}
									else 	if(isset($_GET['show']) &&  $_GET['show'] == 0 &&  ($datas[$i]['ClosingBalanceCredit'] <> 0 || $datas[$i]['ClosingBalanceDebit'] <> 0))
									{
												$tree .= '<tr id="li_' . $datas[$i]['id'] .'_'.$depth.  '"  class="rowstyle"><label ><td style="padding-left:20px;"><span style="width:300px;" >';	
												$tree .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$datas[$i]['name'].'</span></td>
															<td style="text-align:right;"><span >'.number_format($datas[$i]['ClosingBalanceCredit'],2).'</span></td>
															<td style="text-align:right;"><span >'.number_format($datas[$i]['ClosingBalanceDebit'],2).'</span></td></label></tr>';
									}		
						}
					}
					else
					{
						if(!isset($_GET['q']) || (isset($_GET['q']) && $_GET['q'] == 2))
						{
							//detailed tree view i.e q parameter set and equal to 2 or q not set
							$tree .= '<tr id="li_' . $datas[$i]['id'] .'_'.$depth.  '"  class="rowstyle"><label ><td style="padding-left:20px;"><span style="width:300px;" >';	
							$tree .= '<b>&nbsp;&nbsp;&nbsp;'.$datas[$i]['name'].'</b></span></td>
									<td style="text-align:right;border-right:1px solid black;"><span ><b><u></u></b></span></td>
									<td style="text-align:right;border-right:1px solid black;"><span ><b><u></u></b></span></td>
									<td style="text-align:right;border-right:1px solid black;" ><span ><b><u></u></b></span></td>
									<td style="text-align:right;border-right:1px solid black;"><span ><b><u></u></b></span></td>
									<td style="text-align:right;border-right:1px solid black;"><span ><b><u></u></b></span></td>
									<td style="text-align:right;border-right:1px solid black;"><span ><b><u></u></b></span></td></label></tr>';
							
							$subparentNode = $datas[$i]['name'];
							$datas[$i]['credit'] = $TrnxCredit; 
							$datas[$i]['debit'] = $TrnxDebit;
							$endTreeDisplayArray = $datas[$i];
						}
						else
						{
							//summary tree view i.e q=1
							if(isset($_GET['show']) &&  $_GET['show'] == 1)
							{
									$tree .= '<tr id="li_' . $datas[$i]['id'] .'_'.$depth.  '"  class="rowstyle"><label ><td style="padding-left:20px;"><span style="width:300px;" >';	
									$tree .= '<b>&nbsp;&nbsp;&nbsp;'.$datas[$i]['name'].'</b></span></td>
									<td style="text-align:right;"><span ></span></td>
									<td style="text-align:right;"><span ></span></td></label></tr>';
							
									$subparentNode = $datas[$i]['name'];
									$datas[$i]['credit'] = $TrnxCredit; 
									$datas[$i]['debit'] = $TrnxDebit;
									$endTreeDisplayArray = $datas[$i];
							}
							else 	if(isset($_GET['show']) &&  $_GET['show'] == 0 &&  ($datas[$i]['ClosingBalanceCredit'] <> 0 || $datas[$i]['ClosingBalanceDebit'] <> 0))
							{
										$tree .= '<tr id="li_' . $datas[$i]['id'] .'_'.$depth.  '"  class="rowstyle"><label ><td style="padding-left:20px;"><span style="width:300px;" >';	
										$tree .= '<b>&nbsp;&nbsp;&nbsp;'.$datas[$i]['name'].'</b></span></td>
									<td style="text-align:right;"><span ></span></td>
									<td style="text-align:right;"><span ></span></td></label></tr>';
							
										$subparentNode = $datas[$i]['name'];
										$datas[$i]['credit'] = $TrnxCredit; 
										$datas[$i]['debit'] = $TrnxDebit;
										$endTreeDisplayArray = $datas[$i];
							}		
							
							
						}
					}
					
				}
				
				$tree .= $this->generateTrialBalanceTable($datas, $datas[$i]['id'], $setZero ,$subparentNode,$endTreeDisplayArray);
				
				if($subparentNode <> "" &&  strcasecmp($subparentNode, $Str) <>  0)
				{
					
					//echo "chknode:::".$subparentNode." :: " .$Str;
					if(!isset($_GET['q']) || (isset($_GET['q']) && $_GET['q'] == 2))
					{
						//detailed tree view i.e q parameter set and equal to 2 or q not set
						
						if($endTreeDisplayArray['parent_id'] == 0 ||  $endTreeDisplayArray['parent_id'] == 1)
						{
							//echo "test";
							$tree .= '<tr id="li_' . $endTreeDisplayArray['id'] .'_'.$depth.  '" class="rowstyle" ><label>';	
							$tree .= '<td style="padding-left:20px;"><b><span ></b></span></td>
									  <td style="text-align:right;border:1px solid black;"><span ><b>'.number_format($endTreeDisplayArray['OpeningBalanceCredit'],2).'</b></span></td>
									  <td style="text-align:right;border:1px solid black;"><span ><b>'.number_format($endTreeDisplayArray['OpeningBalanceDebit'],2).'</b></span></td>
									  <td style="text-align:right;border:1px solid black;"><span ><b>'.number_format($endTreeDisplayArray['credit'],2).'</b></span></td>
									  <td style="text-align:right;border:1px solid black;"><span ><b>'.number_format($endTreeDisplayArray['debit'],2).'</b></span></td>
									  <td style="text-align:right;border:1px solid black;"><span ><b>'.number_format($endTreeDisplayArray['ClosingBalanceCredit'],2).'</b></span></td>
									  <td style="text-align:right;border:1px solid black;"><span ><b>'.number_format($endTreeDisplayArray['ClosingBalanceDebit'],2).'</b></span></td></label></tr>';
						}
						else
						{
							$tree .= '<tr id="li_' . $endTreeDisplayArray['id'] .'_'.$depth.  '"  class="rowstyle"><label ><td style="padding-left:20px;"><span style="width:300px;" >';	
							$tree .= '<b>&nbsp;&nbsp;&nbsp;</b></span></td>
									<td style="text-align:right;border:1px solid black;"><span ><b>'.number_format($endTreeDisplayArray['OpeningBalanceCredit'],2).'</b></span></td>
									<td style="text-align:right;border:1px solid black;"><span ><b>'.number_format($endTreeDisplayArray['OpeningBalanceDebit'],2).'</b></span></td>
									<td style="text-align:right;border:1px solid black;" ><span ><b>'.number_format($endTreeDisplayArray['credit'],2).'</b></span></td>
									<td style="text-align:right;border:1px solid black;"><span ><b>'.number_format($endTreeDisplayArray['debit'],2).'</b></span></td>
									<td style="text-align:right;border:1px solid black;"><span ><b>'.number_format($endTreeDisplayArray['ClosingBalanceCredit'],2).'</b></span></td>
									<td style="text-align:right;border:1px solid black;"><span ><b>'.number_format($endTreeDisplayArray['ClosingBalanceDebit'],2).'</b></span></td></label></tr>';	
							
						}
												
					}
					else
					{
						//summary tree view i.e q=1
						
						if($endTreeDisplayArray['parent_id'] == 0 ||  $endTreeDisplayArray['parent_id'] == 1)
						{
							$tree .= '<tr id="li_' . $endTreeDisplayArray['id'] .'_'.$depth.  '" class="rowstyle" ><label>';	
							$tree .= '<td style="padding-left:20px;"><span ><b></b></span></td>
									  <td style="text-align:right;border-top:1px solid black;border-bottom:1px solid black;"><span ><b>'.number_format($endTreeDisplayArray['ClosingBalanceCredit'],2).'</b></span></td>
									  <td style="text-align:right;border-top:1px solid black;border-bottom:1px solid black;"><span ><b>'.number_format($endTreeDisplayArray['ClosingBalanceDebit'],2).'</b></span></td></label></tr>';
						}
						else
						{
							//summary tree view i.e q=1
							$tree .= '<tr id="li_' . $endTreeDisplayArray['id'] .'_'.$depth.  '"  class="rowstyle"><label ><td style="padding-left:20px;"><span style="width:300px;" >';	
							$tree .= '<b>&nbsp;&nbsp;&nbsp;</b></span></td>
									<td style="text-align:right;border-top:1px solid black;border-bottom:1px solid black;"><span ><b>'.number_format($endTreeDisplayArray['ClosingBalanceCredit'],2).'</b></span></td>
									<td style="text-align:right;border-top:1px solid black;border-bottom:1px solid black;"><span ><b>'.number_format($endTreeDisplayArray['ClosingBalanceDebit'],2).'</b></span></td></label></tr>';
						}
						
					}
				}
				$subparentNode = '';
				
			}
			
		}
		
		return $tree;
	}
	
	

	
	
/*	public function generateIncomeStatement($GroupID,$datas, $parent = 0, $depth = 0, $padding = 10)
	{
		$amount = array();
		if($depth > 1000) return ''; // Make sure not to have an endless recursion
		
		$tree = '<ul>';
		for($i = 0, $ni = count($datas); $i < $ni; $i++)
		{
			if($datas[$i]['parent_id'] == $parent)
			{
				$netAmount = 0;
				if($GroupID == INCOME)
				{
					$netAmount = $datas[$i]['credit'] - $datas[$i]['debit'];
					$netAmount2 = $datas[$i]['creditPrev'] - $datas[$i]['debitPrev'];
				}
				else if($GroupID == EXPENSE)
				{
					$netAmount = $datas[$i]['debit'] - $datas[$i]['credit'];	
					$netAmount2 = $datas[$i]['debitPrev'] - $datas[$i]['creditPrev'];		
				}
				//$netAmount = abs($netAmount);
				if($netAmount <> 0 || $netAmount2 <> 0)
				{
					if($parent == 0 || $parent == 1)
					{
						//current array element is parent tree menu
						$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '" ><label><span style="border:none;width:94px;text-align:right;" class = "Previous">'.number_format($netAmount2,2).'</span><span style="border:none;width:250px;">';	
						$tree .= $datas[$i]['name'].'</span><span style="border:none;width:100px;text-align:right;">'.number_format($netAmount,2).'</span></label>';
					}
					else
					{
						
						
						
						if($datas[$i]['LedgerID'] > 0)
						{
							//current array element is ledger
							$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '"  style="display:none;"><label ><span style="border:none;width:94px;text-align:right;" class="icon-minus-sign Previous"><a href="'.$Url.'">'.number_format($netAmount2,2).'</a></span><span style=" border:none;width:250px;" class="icon-minus-sign">';	
							$Url = "view_ledger_details.php?lid=".$datas[$i]['LedgerID']."&gid=".$datas[$i]['GroupType']."&dt";
							$tree .= '<a href="'.$Url.'">'.$datas[$i]['name'].'</a></span><span style="border:none;width:100px;text-align:right;" class="icon-minus-sign"><a href="'.$Url.'">'.number_format($netAmount,2).'</a></span></label>';
						}
						else
						{
							//current array element is subtree menu
							$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '"  style="display:none;"><label ><span style="border:none;width:94px;text-align:right;" class="icon-minus-sign Previous">'.number_format($netAmount2,2).'</span><span style=" border:none;width:250px;" class="icon-minus-sign">';	
							$tree .= $datas[$i]['name'].'</span><span style="border:none;width:100px;text-align:right;" class="icon-minus-sign">'.number_format($netAmount,2).'</span></label>';
						}
						
					}
				}
				
				$padding += 10;
				$tree .= $this->generateIncomeStatement($GroupID,$datas, $datas[$i]['id'], $depth+1, $padding);
				$padding -= 10;
				$tree .= '</li>';
			}
			
		}
		
		$tree .= '</ul>';
		return $tree;
	}*/
	
	
		public function generateIncomeStatement($GroupID,$datas, $parent = 0, $depth = 0, $padding = 10)
	{
		$amount = array();
		if($depth > 1000) return ''; // Make sure not to have an endless recursion
		
		for($i = 0, $ni = count($datas); $i < $ni; $i++)
		{
			if($datas[$i]['parent_id'] == $parent)
			{
				$netAmount = 0;
				if($GroupID == INCOME)
				{
					$netAmount = $datas[$i]['credit'] - $datas[$i]['debit'];
					$netAmount2 = $datas[$i]['creditPrev'] - $datas[$i]['debitPrev'];
				}
				else if($GroupID == EXPENSE)
				{
					$netAmount = $datas[$i]['debit'] - $datas[$i]['credit'];	
					$netAmount2 = $datas[$i]['debitPrev'] - $datas[$i]['creditPrev'];		
				}
				
				if($netAmount <> 0 || $netAmount2 <> 0)
				{
					if($parent == 0 || $parent == 1)
					{
						//current array element is parent tree menu 
						$tree .= '<tr id="li_' . $datas[$i]['id'] .'_'.$depth.  '"    data-tt-id="' . $datas[$i]['id'] .'"><td  style="width: 10px;" class = "icon-class"></td><td style="border:none;width:25%;text-align:right;" class = "Previous">'.number_format($netAmount2,2).'</td><td style="border:none;width:50%">';	
						$tree .= $datas[$i]['name'].'</td><td style="border:none;width:25%;text-align:right;" class="Current">'.number_format($netAmount,2).'</td></tr>';
					}
					else
					{
						
						
						
						if($datas[$i]['LedgerID'] > 0)
						{
							//current array element is ledger
							$this->iCounter ++;
							$Url = "view_ledger_details.php?lid=".$datas[$i]['LedgerID']."&gid=".$datas[$i]['GroupType']."&dt";
							$LedgetUrl = "ledger.php?edt=".$datas[$i]['LedgerID'];
							$tree .= '<tr id="li_' . $datas[$i]['id'] .'_'.$depth.  '"  style="display:none;" data-tt-parent-id="' . $datas[$i]['parent_id'] .'"  data-tt-id="-1"><td  style="width: 10px;" class = "icon-class"></td><td style="border:none;text-align:right;" class="Previous"><a href="'.$Url.'" target="_blank">'.number_format($netAmount2,2).'</a></td><td style=" border:none;" class="icon-minus-sign">';	
							$tree .= '<a href="'.$LedgetUrl.'" target="_blank">'.$datas[$i]['name'].'</a></td><td style="border:none;text-align:right;" class="Current"><a href="'.$Url.'" target="_blank">'.number_format($netAmount,2).'</a></td></tr>';
						}
						else
						{
							//current array element is subtree menu
							$tree .= '<tr id="li_' . $datas[$i]['id'] .'_'.$depth.  '"  style="display:none;"  data-tt-parent-id="' . $datas[$i]['parent_id'] .'"  data-tt-id="' . $datas[$i]['id'] .'"><td  style="width: 10px;" class = "icon-class"></td><td style="border:none;text-align:right;" class="Previous">'.number_format($netAmount2,2).'</td><td style=" border:none;" class="icon-minus-sign">';	
							$tree .= $datas[$i]['name'].'</td><td style="border:none;text-align:right;" class="Current">'.number_format($netAmount,2).'</td></tr>';
						}
						
					}
				}
				
				$padding += 10;
				$tree .= $this->generateIncomeStatement($GroupID,$datas, $datas[$i]['id'], $depth+1, $padding);
				$padding -= 10;
			}
			
		}
		
		return $tree;
	}
	
	
	public function generateIncomeStatementTable($GroupID,$datas, $parent = 0, $depth = 0, $padding = 10)
	{
		$amount = array();
		if($depth > 1000) return ''; // Make sure not to have an endless recursion
		
		$tree = '';
		for($i = 0, $ni = count($datas); $i < $ni; $i++)
		{
			if($datas[$i]['parent_id'] == $parent)
			{
				$netAmount = 0;
				if($GroupID == INCOME)
				{
					$netAmount = $datas[$i]['credit'] - $datas[$i]['debit'];
					$netAmount2 = $datas[$i]['creditPrev'] - $datas[$i]['debitPrev'];
				}
				else if($GroupID == EXPENSE)
				{
					$netAmount = $datas[$i]['debit'] - $datas[$i]['credit'];		
					$netAmount2 = $datas[$i]['debitPrev'] - $datas[$i]['creditPrev'];
				}
				//$netAmount = abs($netAmount);
				if($netAmount <> 0 || $netAmount2 <> 0)
				{
					if($parent == 0 || $parent == 1)
					{
						//current array element is parent tree menu
						$tree .= '<tr id="li_' . $datas[$i]['id'] .'_'.$depth.  '" ><label><td style="text-align:right;width:100px;color:blue;border-right:1px solid black;" class = "Previous"><span><u></u></span></td><td style="text-align:right;width:100px;color:blue;border-right:1px solid black;" class = "Previous"><span><u>'.number_format($netAmount2,2).'</u></span></td><td style="width:490px;color:blue;border-right:1px solid black;border-left:1px solid black;"><span><b>';	
						$tree .= $datas[$i]['name'].'</b></span></td><td style="text-align:right;width:100px;color:blue;border-right:1px solid black;"><span><u></u></span></td><td style="text-align:right;width:100px;color:blue;border-right:1px solid black;"><span><u>'.number_format($netAmount,2).'</u></span></td></label></tr>';
					}
					else
					{
						
						$tree .= '<tr id="li_' . $datas[$i]['id'] .'_'.$depth.  '"><label ><td style="text-align:right;width:100px;border-right:1px solid black;" class = "Previous"><span>'.number_format($netAmount2,2).'</span></td><td style="text-align:right;width:100px;border-right:1px solid black;"  class = "Previous"><span></span></td><td style="width:490px;border-right:1px solid black;border-left:1px solid black;"><span>';	
						
						if($datas[$i]['LedgerID'] > 0)
						{
							//current array element is ledger
							$tree .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$datas[$i]['name'].'</span></td><td style="text-align:right;width:100px;border-right:1px solid black;" ><span>'.number_format($netAmount,2).'</span></td><td style="text-align:right;width:100px;border-right:1px solid black;" ><span></span></td></label></tr>';
						}
						else
						{
							//current array element is subtree menu
							$tree .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$datas[$i]['name'].'</b></span></td><td style="text-align:right;width:100px;border-right:1px solid black;" ><span></span></td><td style="text-align:right;width:100px;border-right:1px solid black;" ><span><b><u>'.number_format($netAmount,2).'</u></b></span></td></label></tr>';
						}
						
					}
				}
				
				$tree .= $this->generateIncomeStatementTable($GroupID,$datas, $datas[$i]['id'], $depth+1, $padding);
			}
			
		}
		
		return $tree;
	}
	
	public function FormatArray($Data,$GroupID, $from, $to,$IncomeExpenseAccount = 0,$showZeros = true,$isAddOpeningBalance,$YearID)
	{
		/*echo '<pre>';
		print_r($Data);
		echo '</pre>';*/
		//converting  array to tree format array 
		$type = $GroupID;
		$categories = array();	
		$max = 1000;
		
		foreach($Data as $category) 
		{
			$parent = false;
			if($category['parentcategory_id'] == 0 || $category['parentcategory_id'] == 1)
			{
				$parent = true;	
				$category['parentcategory_id'] = 0;
			}
			
			//array of main parent
			$categories[] = array('id' => $category['category_id'], 'name' =>$category['category_name'], 'parent_id' => $category['parentcategory_id'], 'IsParent' => $parent,'credit' =>0,'creditPrev' => 0,'debit' =>0,'debitPrev' => 0,'GroupType' => $type,'LedgerID' => 0,'OpeningBalanceCredit' => 0,'OpeningBalanceDebit' => 0,'ClosingBalanceCredit' => 0,'ClosingBalanceDebit' => 0);
			
			if($IncomeExpenseAccount <> 0)
			{
				//**O Need to check whether is using
				$LedgerData=$this->LedgerArray($GroupID,$category['category_id'], $from, $to,$IncomeExpenseAccount,$YearID);
			}
			else
			{
				$LedgerData=$this->LedgerArray($GroupID,$category['category_id'], $from, $to,0,$YearID);
			}
			
			foreach($LedgerData as $category2) 
			{
				
				//print_r($category2);
				$max++;
				$openingLedgerBalance = 0;
				$openingLedgerBalanceCredit = 0;
				$openingLedgerBalanceDebit = 0;
				if($category2['credit'] == "")
				{
					$category2['credit'] = 0;	
						
				}
				if($category2['debit'] == "")
				{
					$category2['debit'] = 0;	
						
				}
				//print_r($category2);
				
				$balance = $category2['credit'] - $category2['debit'];
				$closingBalanceCredit = 0;
				$closingBalanceDebit = 0;
				$openingLedgerBalanceCredit = 0;
				$openingLedgerBalanceDebit = 0;
				
				if($GroupID == 1 || $GroupID == 2 || $isAddOpeningBalance == true)
				{
					$openingLedgerBalance = $this->getLedgerOpeningBalance($category2['LedgerID'],$GroupID , $from ,$to);
					
					if($openingLedgerBalance <> "")
					{
						
						if($openingLedgerBalance['Credit'] > 0)
						{
							$openingLedgerBalanceCredit = $openingLedgerBalance['Credit'];	
						}
						else
						{
							$openingLedgerBalanceDebit = $openingLedgerBalance['Debit'];		
						}
						
						
					}
					
				}
				$balance = ($openingLedgerBalanceCredit + $category2['credit'] )- ($openingLedgerBalanceDebit + $category2['debit']);
				
				if($balance < 0)
				{
					$closingBalanceDebit = abs($balance);	
				}
				else if($balance > 0)
				{
					$closingBalanceCredit = $balance;	
				}
				else
				{
					$closingBalanceCredit = 0;
					$closingBalanceDebit =	0;	
				}
				
				if( $category2['LedgerID'] != "")
				{
					$arLedgerIDParentDetails = $this->m_objUtility->getParentOfLedger($category2['LedgerID']);
					if(!(empty($arLedgerIDParentDetails)))
					{
						$LedgerGroupID = $arLedgerIDParentDetails['group'];
						$LedgerCategoryID = $arLedgerIDParentDetails['category'];
						
						if($LedgerCategoryID == DUE_FROM_MEMBERS)
						{
							$LedgerName = $this->getMemberName($category2['LedgerID'],$to);
							if($LedgerName <> "")
							{
								$category2['name'] = $category2['name'].' - '.$LedgerName;
									
							}	
						}
					}
				}
				//generating array of ledgers
				if($showZeros == false)
				{
					//if(($category2['credit'] <> 0 || $category2['debit'] <> 0 ) &&  ($closingBalanceCredit <> 0 || $closingBalanceDebit <> 0))
					//if($closingBalanceCredit <> 0 || $closingBalanceDebit <> 0)
					if($openingLedgerBalanceCredit == 0 && $openingLedgerBalanceDebit == 0 && $closingBalanceCredit == 0 && $closingBalanceDebit == 0 && $category2['credit'] == 0 && $category2['debit'] == 0)
					{
						//skip 
					}
					else
					{
						$categories[] = array('id' => $max, 'name' =>$category2['name'], 'parent_id' => $category['category_id'], 'credit' => $category2['credit'],'debit' => $category2['debit'],'GroupType' => $type,'LedgerID' => $category2['LedgerID'],'OpeningBalanceCredit' => $openingLedgerBalanceCredit,'OpeningBalanceDebit' => $openingLedgerBalanceDebit,'ClosingBalanceCredit' => $closingBalanceCredit,'ClosingBalanceDebit' => $closingBalanceDebit);
					}
				}
				else
				{
					$categories[] = array('id' => $max, 'name' =>$category2['name'], 'parent_id' => $category['category_id'], 'credit' => $category2['credit'],'creditPrev' => $category2['creditPrev'],'debit' => $category2['debit'],'debitPrev' => $category2['debitPrev'],'GroupType' => $type,'LedgerID' => $category2['LedgerID'],'OpeningBalanceCredit' => $openingLedgerBalanceCredit,'OpeningBalanceDebit' => $openingLedgerBalanceDebit,'ClosingBalanceCredit' => $closingBalanceCredit,'ClosingBalanceDebit' => $closingBalanceDebit);
				}
			}
		}
	   if(($GroupID == EXPENSE || $GroupID == INCOME))
	   {
		  /* echo "<pre>";
		   print_r($categories);
		   echo "</pre>";*/
	   }
	   return $this->CalculateTotal($categories);
	}
	
	public function CalculateTotal($categories)
	{
		for($iCnt = sizeof($categories)-1; $iCnt >= 0 ; $iCnt--)
		{
			$parent = $categories[$iCnt]['parent_id'];
			if( ($categories[$iCnt]['parent_id'] == 0 || $categories[$iCnt]['parent_id'] == 1) || $categories[$iCnt]['LedgerID'] == 0)
			{
					$categories[$iCnt]['credit'] = 0;
					$categories[$iCnt]['debit'] = 0;
					$categories[$iCnt]['creditPrev'] = 0;
					$categories[$iCnt]['debitPrev'] = 0;
					$categories[$iCnt]['OpeningBalanceCredit'] = 0;
					$categories[$iCnt]['OpeningBalanceDebit'] = 0;
					$categories[$iCnt]['ClosingBalanceCredit'] = 0;
					$categories[$iCnt]['ClosingBalanceDebit'] = 0;		
			}
		}
		
		if($isRequestforPreviousYearBalance == true)
		{
			/*$this->CreditTotal = 0;
			$this->DebitTotal = 0;
			$this->CreditTotalPrev = 0;
			$this->DebitTotalPrev = 0;
			$this->OpeningBalanceCreditTotal = 0; 
			$this->OpeningBalanceDebitTotal = 0;*/
			
		}
		
		//calculating creditfinal,debitfinal,openingbalancefinal,closingbalancefinal amount 
		for($iCnt = sizeof($categories)-1; $iCnt >= 0 ; $iCnt--)
		{
			$parent = $categories[$iCnt]['parent_id'];
			//adding credit and debit amount of ledgers and setting  to ledgers main parent
			$categories = $this->setAmount($categories, $parent, $categories[$iCnt]['credit'],$categories[$iCnt]['creditPrev'],$categories[$iCnt]['debit'],$categories[$iCnt]['debitPrev'],$iCnt,$categories[$iCnt]['OpeningBalanceCredit'],$categories[$iCnt]['OpeningBalanceDebit'],$categories[$iCnt]['ClosingBalanceCredit'],$categories[$iCnt]['ClosingBalanceDebit']);
		}
		
		for($iCnt = sizeof($categories)-1; $iCnt >= 0 ;$iCnt--)
		{
			
			$parent = $categories[$iCnt]['parent_id'];
			if($parent == 0 || $parent == 1)
			{
				/*$tmpVar = $categories[$iCnt]['credit'] -  $categories[$iCnt]['debit'];
				if($tmpVar < 0)
				{
					$tmpVar = abs($tmpVar);
					$this->DebitTotal += $tmpVar;		
				}
				else
				{
					$this->CreditTotal += $tmpVar;	
				}*/
				
				$this->CreditTotal += $categories[$iCnt]['credit'];
				$this->DebitTotal += $categories[$iCnt]['debit'];	
				$this->CreditTotalPrev += $categories[$iCnt]['creditPrev'];
				$this->DebitTotalPrev += $categories[$iCnt]['debitPrev'];	
				$this->OpeningBalanceCreditTotal += $categories[$iCnt]['OpeningBalanceCredit'];
				$this->OpeningBalanceDebitTotal += $categories[$iCnt]['OpeningBalanceDebit'];	
				//echo "<br>Counter $iCnt => ".$this->OpeningBalanceDebitTotal;
				
				if( $categories[$iCnt]['id'] == CURRENT_ASSET && $categories[$iCnt]['ClosingBalanceCredit'] <> 0 )
				{
						$this->ClosingBalanceCreditTotal += abs($categories[$iCnt]['ClosingBalanceCredit']);
						$this->ClosingBalanceDebitTotal += abs($categories[$iCnt]['ClosingBalanceDebit']);	
				}
				else
				{
					$tempBal = $categories[$iCnt]['ClosingBalanceCredit'] - $categories[$iCnt]['ClosingBalanceDebit'];
					if($tempBal < 0 )
					{
						$tempBal = abs($tempBal);
						
							$this->ClosingBalanceDebitTotal += $tempBal;	
					}
					else
					{
						$this->ClosingBalanceCreditTotal += $tempBal;	
					}
				}
				//$this->ClosingBalanceCreditTotal += $categories[$iCnt]['ClosingBalanceCredit']; 
				//$this->ClosingBalanceDebitTotal += $categories[$iCnt]['ClosingBalanceDebit']; 
			}
		}
		
		return $categories;
	
	}
	
	public function getTotal($Data)
	{
		$total = 0;
		foreach($Data as $key)
		{
			if($key['parent_id'] == 0 or $key['parent_id'] == 1)
			{
				$tmp = ($key['OpeningBalanceCredit'] + $key['credit']) - ($key['debit'] + $key['OpeningBalanceDebit']);
				$total += abs($tmp);
			}
		}	
		
		return $total;
	}
	
	
	public function getCreditTotal($LIABILITYData,$ASSETData,$INCOMEData,$EXPENSEData)
	{
		$total = 0;
		foreach($LIABILITYData as $key)
		{
			if($key['parent_id'] == 1)
			{
					$total += $key['credit'] + $key['debit'];	
			}	
			
		}
		foreach($LIABILITYData as $key)
		{
			if($key['parent_id'] == 1)
			{
					$total += $key['credit'] + $key['debit'];	
			}	
			
		}
		foreach($LIABILITYData as $key)
		{
			if($key['parent_id'] == 1)
			{
					$total += $key['credit'] + $key['debit'];	
			}	
			
		}
		foreach($LIABILITYData as $key)
		{
			if($key['parent_id'] == 1)
			{
					$total += $key['credit'] + $key['debit'];	
			}	
		}		
	}
	
	public function OpeningBalanceCalculate()
	{
		
		//calculateing opening balance of aasets and liabilities
		/*$previousYearOpeningBalance = false
		if($previousYearOpeningBalance == true)
		{
			$year_start_date = strtotime($_SESSION['default_year_start_date'].' -1 year');
		}
		else*/
		{
			$year_start_date = $_SESSION['default_year_start_date'];	
		}
		
		$LiabilitySql = "SELECT SUM(Debit) as Debit,SUM(Credit) as Credit FROM `liabilityregister` where Is_Opening_Balance = 1 ";
		
		if($_SESSION['default_year_start_date'] <> 0)
		{
			$LiabilitySql .= "  and Date = '".getDBFormatDate($year_start_date)."' ";					
		}
		$result1 = $this->m_dbConn->select($LiabilitySql);
		
		$AssetSql = "SELECT SUM(Debit) as Debit,SUM(Credit) as Credit FROM `assetregister` where Is_Opening_Balance = 1 ";
		
		if($_SESSION['default_year_start_date'] <> 0)
		{
			$AssetSql .= "  and Date = '".getDBFormatDate($year_start_date)."' ";					
		}
		$result2 = $this->m_dbConn->select($AssetSql);
		
		$BankSql = "SELECT SUM(PaidAmount) as Debit,SUM(ReceivedAmount) as Credit FROM `bankregister` where Is_Opening_Balance = 1 ";
		if($_SESSION['default_year_start_date'] <> 0)
		{
			$BankSql .= "  and Date = '".getDBFormatDate($year_start_date)."' ";					
		}
		$result3 = $this->m_dbConn->select($BankSql);
		
		$LiabilityTotal = abs($result1[0]['Credit'] - $result1[0]['Debit']);
		$AssetTotal = abs($result2[0]['Credit'] - $result2[0]['Debit']);
		//$BankTotal = abs($result3[0]['Credit'] - $result3[0]['Debit']);
		
		//$OpeningBalance = ($AssetTotal + $BankTotal) - $LiabilityTotal;
		$OpeningBalance = $AssetTotal - $LiabilityTotal;
		
		return $OpeningBalance;
			
	}
	
	public function TotalAmount($TableName)
	{
		$Sql = "SELECT SUM(Debit) as Debit,SUM(Credit) as Credit FROM `".$TableName."`";
		$result2 = $this->m_dbConn->select($Sql);
		
		return $result2;
	}
	
	public function ArrayShifting($GroupID,&$Data,&$DataShiftarray)
	{
		//if parent tree category closing amount is in minus then shift ledger to opposite site
		for($i = 0; $i < sizeof($Data); $i++)
		{
			$isParentReadyToShift  = false;
			$amount = 0;
			if($GroupID == LIABILITY &&  $Data[$i]['GroupType'] == LIABILITY)
			{ 
				$amount = ($Data[$i]['credit'] + $Data[$i]['OpeningBalanceCredit']) - ($Data[$i]['debit'] + $Data[$i]['OpeningBalanceDebit']);
			}
			else if($GroupID == ASSET &&  $Data[$i]['GroupType'] == ASSET)
			{
				$amount = ($Data[$i]['debit'] + $Data[$i]['OpeningBalanceDebit']) - ($Data[$i]['credit'] + $Data[$i]['OpeningBalanceCredit']);
			}
			
			//if( ($Data[$i]['parent_id'] == 0 || $Data[$i]['parent_id'] == 1) && $amount < 0 )
			if( ($Data[$i]['parent_id'] == 0 || $Data[$i]['parent_id'] == 1))
			{
				$isParentReadyToShift = $this->isAnyChildOfParentNegative($GroupID , $Data[$i]['id'] , $Data);
				
				if($isParentReadyToShift)
				{
					$newArray = array();
					array_push($newArray,$Data[$i]);
					
					$newArray[0]['credit'] = 0;
					$newArray[0]['debit'] = 0;
					$newArray[0]['creditPrev'] = 0;
					$newArray[0]['debitPrev'] = 0;
					$newArray[0]['OpeningBalanceCredit'] = 0;
					$newArray[0]['OpeningBalanceDebit'] = 0;
					$newArray[0]['ClosingBalanceCredit'] = 0;
					$newArray[0]['ClosingBalanceDebit'] = 0;
					
					array_push($DataShiftarray,$newArray[0]);
					
					//find all child of parent to shift
					$DataShiftarray=$this->FindAllChild($Data[$i]['id'],$Data,$this->unsetArray,$GroupID,$DataShiftarray,1,0);
				}
			}

		}
		
		return $DataShiftarray;
	}
	
	public function FindAllChild($id ,&$Data,$unsetArray,$GroupID,&$DataShiftarray,$show = 0,$parentAmount = 0)
	{
		//find all child of parent to shift
		for($i=0;$i < sizeof($Data);$i++)
		{
				$isParentReadyToShift = false;
				$amount = 0;
				
				if($Data[$i]['parent_id'] == $id)
				{
						if($GroupID == LIABILITY && $Data[$i]['GroupType'] == LIABILITY)
						{ 
							$amount = ($Data[$i]['credit'] + $Data[$i]['OpeningBalanceCredit']) - ($Data[$i]['debit'] + $Data[$i]['OpeningBalanceDebit']);
						}
						else if($GroupID == ASSET && $Data[$i]['GroupType'] == ASSET)
						{
							$amount = ($Data[$i]['debit'] + $Data[$i]['OpeningBalanceDebit']) - ($Data[$i]['credit'] + $Data[$i]['OpeningBalanceCredit']);
						}
						
						$isParentReadyToShift = $this->isAnyChildOfParentNegative($GroupID , $Data[$i]['id'] , $Data);
						
						if($amount <0  && $isParentReadyToShift == true && $show == 1 )
						{
							$parentAmount = $amount;
							foreach($Data as $key =>$v)
							{
								if($Data[$key]['id'] == $id)
								{
									
									$Data[$key]['credit'] -= $Data[$i]['credit'];
									$Data[$key]['debit'] -= $Data[$i]['debit'];
									$Data[$key]['creditPrev'] -= $Data[$i]['creditPrev'];
									$Data[$key]['debitPrev'] -= $Data[$i]['debitPrev'];
									$Data[$key]['OpeningBalanceCredit'] -= $Data[$i]['OpeningBalanceCredit'];
									$Data[$key]['OpeningBalanceDebit'] -= $Data[$i]['OpeningBalanceDebit'];
									$Data[$key]['ClosingBalanceCredit'] -= $Data[$i]['ClosingBalanceCredit'];
									$Data[$key]['ClosingBalanceDebit'] -= $Data[$i]['ClosingBalanceDebit'];
								}
							}
						}
						else if($show == 0 && $amount < 0)
						{
							$parentAmount = 0;
							foreach($Data as $key =>$v)
							{
								
								if($Data[$key]['id'] == $id)
								{
									
									$Data[$key]['credit'] -= $Data[$i]['credit'];
									$Data[$key]['debit'] -= $Data[$i]['debit'];
									$Data[$key]['creditPrev'] -= $Data[$i]['creditPrev'];
									$Data[$key]['debitPrev'] -= $Data[$i]['debitPrev'];
									$Data[$key]['OpeningBalanceCredit'] -= $Data[$i]['OpeningBalanceCredit'];
									$Data[$key]['OpeningBalanceDebit'] -= $Data[$i]['OpeningBalanceDebit'];
									$Data[$key]['ClosingBalanceCredit'] -= $Data[$i]['ClosingBalanceCredit'];
									$Data[$key]['ClosingBalanceDebit'] -= $Data[$i]['ClosingBalanceDebit'];
								}
							}
										
						}
						else if($show == 1 && $amount < 0 && $isParentReadyToShift == false)
						{
							$parentAmount = 0;
							foreach($Data as $key =>$v)
							{
								
								if($Data[$key]['id'] == $id)
								{
									
									$Data[$key]['credit'] -= $Data[$i]['credit'];
									$Data[$key]['debit'] -= $Data[$i]['debit'];
									$Data[$key]['creditPrev'] -= $Data[$i]['creditPrev'];
									$Data[$key]['debitPrev'] -= $Data[$i]['debitPrev'];
									$Data[$key]['OpeningBalanceCredit'] -= $Data[$i]['OpeningBalanceCredit'];
									$Data[$key]['OpeningBalanceDebit'] -= $Data[$i]['OpeningBalanceDebit'];
									$Data[$key]['ClosingBalanceCredit'] -= $Data[$i]['ClosingBalanceCredit'];
									$Data[$key]['ClosingBalanceDebit'] -= $Data[$i]['ClosingBalanceDebit'];
								}
							}
										
						}
						
						if($parentAmount < 0 && $isParentReadyToShift  == true && $show == 1)
						{
							//add array index number to be unset or remove from main array after shofting
							$newArray = array();
							array_push($newArray,$Data[$i]);
							if($newArray[0]['id'] == DUE_FROM_MEMBERS  && $newArray[0]['GroupType'] == ASSET && $newArray[0]['LedgerID']  == 0)
							{
								$newArray[0]['name'] = "Advance From Members";		
							}
							
							$newArray[0]['credit'] = 0;
							$newArray[0]['debit'] = 0;
							$newArray[0]['creditPrev'] = 0;
							$newArray[0]['debitPrev'] = 0;
							$newArray[0]['OpeningBalanceCredit'] = 0;
							$newArray[0]['OpeningBalanceDebit'] = 0;
							$newArray[0]['ClosingBalanceCredit'] = 0;
							$newArray[0]['ClosingBalanceDebit'] = 0;
												
							array_push($DataShiftarray,$newArray[0]);
							
							//again find child of current parent
							$DataShiftarray = $this->FindAllChild($Data[$i]['id'],$Data,$unsetArray,$GroupID,$DataShiftarray,0,$parentAmount);
							
						}
						else if($isParentReadyToShift  == true && $show == 1 && $parentAmount >= 0)
						{
							$newArray = array();
							array_push($newArray,$Data[$i]);
							if($newArray[0]['id'] == DUE_FROM_MEMBERS  && $newArray[0]['GroupType'] == ASSET && $newArray[0]['LedgerID']  == 0)
							{
								$newArray[0]['name'] = "Advance From Members";		
							}
							$newArray[0]['credit'] = 0;
							$newArray[0]['debit'] = 0;
							$newArray[0]['creditPrev'] = 0;
							$newArray[0]['debitPrev'] = 0;
							$newArray[0]['OpeningBalanceCredit'] = 0;
							$newArray[0]['OpeningBalanceDebit'] = 0;
							$newArray[0]['ClosingBalanceCredit'] = 0;
							$newArray[0]['ClosingBalanceDebit'] = 0;
												
							array_push($DataShiftarray,$newArray[0]);
							
							//again find child of current parent
							$DataShiftarray = $this->FindAllChild($Data[$i]['id'],$Data,$unsetArray,$GroupID,$DataShiftarray,0,$parentAmount);
							
						}
						else if($amount < 0 && $show ==0)
						{
								array_push($DataShiftarray,$Data[$i]);
								array_push($this->unsetArray,$i);		
						}
						else if($show == 1 && $amount < 0 && $isParentReadyToShift == false && $Data[$i]['LedgerID'] == 0)
						{
							
							$newArray = array();
							array_push($newArray,$Data[$i]);
							
							if($newArray[0]['id'] == DUE_FROM_MEMBERS  && $newArray[0]['GroupType'] == ASSET && $newArray[0]['LedgerID']  == 0)
							{
								$newArray[0]['name'] = "Advance From Members";		
							}
							$newArray[0]['credit'] = 0;
							$newArray[0]['debit'] = 0;
							$newArray[0]['creditPrev'] = 0;
							$newArray[0]['debitPrev'] = 0;
							$newArray[0]['OpeningBalanceCredit'] = 0;
							$newArray[0]['OpeningBalanceDebit'] = 0;
							$newArray[0]['ClosingBalanceCredit'] = 0;
							$newArray[0]['ClosingBalanceDebit'] = 0;
												
							array_push($DataShiftarray,$newArray[0]);
							array_push($this->unsetArray,$i);			
						}
						else if($show == 1 && $amount < 0 && $isParentReadyToShift == false && $Data[$i]['LedgerID'] > 0)
						{
							array_push($DataShiftarray,$Data[$i]);
							array_push($this->unsetArray,$i);			
						}
						
						if($show == 1)
						{
							$parentAmount = 0;
						}
				}
		
		}
		return $DataShiftarray;	
	}
	
	public function UnsetArray(&$Data)
	{
		//unset array element from main array and return final array
		foreach($this->unsetArray as $key=>$value)
		{
			unset($Data[$value]);	
		}
		$Data = array_values($Data);
		
		return $Data;	
	}
	
	public function getLedgerOpeningBalance($lid,$gid, $from ,$to)
	{
		
		$openingBalance = array("Credit" => 0 ,"Debit" => 0);
		//fetch opening balance of each ledger of type asset or libility
		/*if($gid == 1)
		{
			$tableName = 'liabilityregister';	
		}
		else if($gid == 2)
		{
			$tableName = 'assetregister';		
			
			$obj_utility = new utility($this->m_dbConn);
			$parentDetails = $obj_utility->getParentOfLedger($lid);
			
			if($parentDetails['category'] == CASH_ACCOUNT || $parentDetails['category'] == BANK_ACCOUNT)
			{
				$tableName = 'bankregister';
			}
		}
		
		if($tableName <> "")
		{
			$sql = "select * from  `" . $tableName. "` where `LedgerID` = '".$lid."' and `Is_Opening_Balance` = 1 ";
			if($tableName == 'bankregister')
			{
				//$sql = "select PaidAmount as Debit, ReceivedAmount as Credit from  `" . $tableName. "` where `LedgerID` = '".$lid."' and `Is_Opening_Balance` = 1";
				$sql = "select PaidAmount as Credit, ReceivedAmount as Debit from  `" . $tableName. "` where `LedgerID` = '".$lid."' and `Is_Opening_Balance` = 1";
			}
			*/
			//$res = $this->m_dbConn->select($sql);
			//echo "test";
			
			
			$arParentDetails = $this->m_objUtility->getParentOfLedger($lid);
			if(!(empty($arParentDetails)))
			{
				$GroupID = $arParentDetails['group'];
				$CategoryID = $arParentDetails['category'];						
				
			//}
			//$date = $this->m_objUtility->getPreviousYearEndingDate($_SESSION['default_year']);
			//echo "date : ".$date;
			//$date = $m_objUtility->GetDateByOffset($from, 1);
			$res = $this->m_objUtility->getOpeningBalance($lid,$from);
			/*if($gid == 1 )
			{
				$tmp = $res['Credit'] - $res['Debit'];
				if($tmp < 0)
				{
					$type = 2;
					$openingBalance['Debit'] = $tmp;		
				}
				else
				{
					$openingBalance['Credit'] = $tmp;		
				}
			}
			else if($CategoryID == BANK_ACCOUNT || $CategoryID == CASH_ACCOUNT)
			{
				$tmp = $res['Credit'] - $res['Debit'];
				
				if($tmp < 0)
				{
					$tmp = abs($tmp);
					$openingBalance['Credit'] = $tmp;		
				}
				else
				{
					$openingBalance['Debit'] = $tmp;	
				}
				
		   }
			else if($gid == 2 )
			{
				$tmp = $res['Debit'] - $res['Credit'];
				
				if($tmp < 0)
				{
					$tmp = abs($tmp);
					$openingBalance['Credit'] = $tmp;		
				}
				else
				{
					$openingBalance['Debit'] = $tmp;	
				}
							
			}
			
			*/
			
			if($res['OpeningType'] == TRANSACTION_CREDIT)
			{
				$openingBalance['Credit'] = $res['Total'];				
			}
			else if($res['OpeningType'] == TRANSACTION_DEBIT)
			{
				$openingBalance['Debit'] = $res['Total'];		
			}
			return $openingBalance;
		}
			/*if($res['Credit'] > 0)
			{
				return 	'Cr_'.$res['Credit'];
			}
			else if($res['Debit'] > 0)
			{
				return 	'Dr_'.$res['Debit'];	
			}
			else
			{
				return 'None_0';	
			}*/
		//}
		 	
	} 
	
	
	public function getMemberName($UnitID,$to)
	{
		$memberIDS = $this->m_objUtility->getMemberIDs(getDBFormatDate($to));	
		//$sql = "select `owner_name` from `member_main` where `unit` = '".$UnitID."' and `society_id`='".$_SESSION['society_id']."' and  member_id IN (SELECT `member_id` FROM (select  `member_id` from `member_main` where ownership_date <= '".getDBFormatDate($to)."'  ORDER BY ownership_date desc) as member_id Group BY unit)  ";
		$sql = "select `owner_name` from `member_main` where `unit` = '".$UnitID."' and `society_id`='".$_SESSION['society_id']."' and  member_id IN (".$memberIDS.")  ";
		$res = $this->m_dbConn->select($sql);
		return $res[0]['owner_name'];	
	}
	
	
	public function isAnyChildOfParentNegative($GroupID , $id , &$Data)
	{
		$isParentReadyToShift = false;
		for($i=0;$i < sizeof($Data);$i++)
		{
				$amount = 0;
				
				if($Data[$i]['parent_id'] == $id)
				{
						if($GroupID == LIABILITY && $Data[$i]['GroupType'] == LIABILITY)
						{
							$amount = ($Data[$i]['credit'] + $Data[$i]['OpeningBalanceCredit']) - ($Data[$i]['debit'] + $Data[$i]['OpeningBalanceDebit']);
						}
						else if($GroupID == ASSET && $Data[$i]['GroupType'] == ASSET)
						{
							$amount = ($Data[$i]['debit'] + $Data[$i]['OpeningBalanceDebit']) - ($Data[$i]['credit'] + $Data[$i]['OpeningBalanceCredit']);
						}
						
						if($amount < 0)
						{
							$isParentReadyToShift = true;
							break;
						}
						else if($amount > 0 && $Data[$i]['LedgerID'] == 0)
						{
							$isParentReadyToShift = $this->isAnyChildOfParentNegative($GroupID , $Data[$i]['id'],$Data);
							
							if($isParentReadyToShift == true)
							{
								break;	
							}
						}
				}
		}
		return $isParentReadyToShift; 	
				
	}
	
	public function MergeCurrentAndPreviousYearData($PreviousYearData,$CurrentYearData)
	{
		$PreviousYearLedgerIDs = array();
		$loopSize = 0;
		if(sizeof($PreviousYearData) <= sizeof($CurrentYearData))
		{
			$loopSize = sizeof($CurrentYearData);
		}
		else
		{
			$loopSize = sizeof($PreviousYearData);
		}
		$PreviousYearLedgerIDs = array_column($PreviousYearData, 'id');
		
		for($i = 0 ; $i < $loopSize; $i++)
		{
			if($CurrentYearData[$i]['id'] <> 0 && $CurrentYearData[$i]['id'] <> '')
			{
				if(in_array($CurrentYearData[$i]['id'],$PreviousYearLedgerIDs))
				{
					
					$PreviousYearDataIndex = array_search($CurrentYearData[$i]['id'],$PreviousYearLedgerIDs);
					
					if($PreviousYearData[$PreviousYearDataIndex]['LedgerID'] <> 0 && $PreviousYearData[$PreviousYearDataIndex]['LedgerID'] <> '')
					{
						if($CurrentYearData[$i]['LedgerID'] == $PreviousYearData[$PreviousYearDataIndex]['LedgerID'])
						{
												
							//echo "<br>Current Year : ".$CurrentYearData[$i]['id']."-".$CurrentYearData[$i]['LedgerID'];
							//echo "<br>Previous Year Index : ".$PreviousYearDataIndex."-".$PreviousYearData[$PreviousYearDataIndex]['LedgerID'];
							//echo 
							$CurrentYearData[$i]['PreviousYeardebit'] = $PreviousYearData[$PreviousYearDataIndex]['debit'];
							$CurrentYearData[$i]['PreviousYearcredit'] = $PreviousYearData[$PreviousYearDataIndex]['credit'];
							$CurrentYearData[$i]['PreviousYearOpeningBalanceDebit'] = $PreviousYearData[$PreviousYearDataIndex]['OpeningBalanceDebit'];
							$CurrentYearData[$i]['PreviousYearOpeningBalanceCredit'] = $PreviousYearData[$PreviousYearDataIndex]['OpeningBalanceCredit'];
							unset($PreviousYearData[$PreviousYearDataIndex]);
							unset($PreviousYearLedgerIDs[$PreviousYearDataIndex]);	
						}
					}
					else
					{
						//echo "<br>Current Year : ".$CurrentYearData[$i]['id']."-".$CurrentYearData[$i]['LedgerID'];
						//echo "<br>Previous Year Index : ".$PreviousYearDataIndex."-".$PreviousYearData[$PreviousYearDataIndex]['LedgerID'];
						//echo 
						$CurrentYearData[$i]['PreviousYeardebit'] = $PreviousYearData[$PreviousYearDataIndex]['debit'];
						$CurrentYearData[$i]['PreviousYearcredit'] = $PreviousYearData[$PreviousYearDataIndex]['credit'];
						$CurrentYearData[$i]['PreviousYearOpeningBalanceDebit'] = $PreviousYearData[$PreviousYearDataIndex]['OpeningBalanceDebit'];
						$CurrentYearData[$i]['PreviousYearOpeningBalanceCredit'] = $PreviousYearData[$PreviousYearDataIndex]['OpeningBalanceCredit'];
						unset($PreviousYearData[$PreviousYearDataIndex]);
						unset($PreviousYearLedgerIDs[$PreviousYearDataIndex]);	
					}
					
				}
			}
		}
	
		$CurrentYearData = $this->addPendingEntries($CurrentYearData,$PreviousYearData,0);
		return $CurrentYearData;
	}
	
	
	public function addPendingEntries($CurrentYearData,$PreviousYearData,$count)
	{
		if($count < 1000)
		{
			if(!empty($PreviousYearData))
			{
				$NextKey = count($CurrentYearData);
				$PreviousDataKeys = array_keys($PreviousYearData);
				$Key = $PreviousDataKeys[0];
				$Current_End_Keys = array_keys($CurrentYearData);
				$End_Key = $Current_End_Keys[sizeof($CurrentYearData)-1];
				$count++;
				
				$NewArray = array();
				$NewArray['id'] = $PreviousYearData[$Key]['id'];
				$NewArray['name'] = $PreviousYearData[$Key]['name'];
				$NewArray['parent_id'] = $PreviousYearData[$Key]['parent_id'];
				$NewArray['GroupType'] = $PreviousYearData[$Key]['GroupType'];
				$NewArray['LedgerID'] = $PreviousYearData[$Key]['LedgerID'];
				$NewArray['PreviousYeardebit'] = $PreviousYearData[$Key]['debit'];
				$NewArray['PreviousYearcredit'] = $PreviousYearData[$Key]['credit'];
				$NewArray['PreviousYearOpeningBalanceDebit'] = $PreviousYearData[$Key]['OpeningBalanceDebit'];
				$NewArray['PreviousYearOpeningBalanceCredit'] = $PreviousYearData[$Key]['OpeningBalanceCredit'];
				
				$firstsize = $this->getArraySize($CurrentYearData,$PreviousYearData);
				{
					$CurrentYearData = array_slice($CurrentYearData, 0, $firstsize+$count, true) + array($NextKey => $NewArray) + array_slice($CurrentYearData, $firstsize+1, count($CurrentYearData) - 1, true) ;	
				}
				unset($PreviousYearData[$Key]);
				return $this->addPendingEntries($CurrentYearData,$PreviousYearData,$count);
			}
			else
			{
				return $CurrentYearData;
			}	
		}
		else
		{
			return $CurrentYearData;
		}
	}
	
	public function getArraySize($CurrentYearData,$PreviousYearData)
	{
			$PreviousYearLedgerIDs = array_column($PreviousYearData, 'id');
			$PreviousYearLedgerParentIDs = array_column($PreviousYearData, 'parent_id');
			$CurrentYearDataIDs = array_column($CurrentYearData, 'id');
			$CurrentYearLedgerParentIDs = array_column($CurrentYearData, 'parent_id');
			
			$FirstParentID = $PreviousYearLedgerParentIDs[0];
			$FirstID = $PreviousYearLedgerIDs[0];	
			
			//echo "<br>ParentID ".$FirstParentID;
			$keys = array_keys($CurrentYearLedgerParentIDs, $FirstParentID);
			
			$PerfectPosition = 0;
			for($i = 0 ; $i < count($keys); $i++)
			{
				if($PerfectPosition == 0)
				{
					if($CurrentYearData[$keys[$i]]['id'] < $FirstID)
					{
						continue;
					}
					else if($CurrentYearData[$keys[$i]]['id'] > $FirstID)
					{
						$PerfectPosition = $keys[$i-1];
					}	
				}
			}
			
			if($PerfectPosition == 0)
			{
				$PerfectPosition = count($CurrentYearData);
			}
			
			return $PerfectPosition;
	}
	
	
	
	public function ProfitandLoss($INCOMEcredit,$INCOMEdebit,$INCOMEOpeningcredit,$INCOMEOpeningdebit,$EXPENSEcredit,$EXPENSEdebit,$EXPENSEOpeningcredit,$EXPENSEOpeningdebit,$LIABILITYTotal)
	{
		$Result = array();
		$INCOMEOpeningFinal = $INCOMEOpeningcredit - $INCOMEOpeningdebit;
		$incomeTotal = abs(($INCOMEcredit + $INCOMEOpeningcredit)- ($INCOMEdebit + $INCOMEOpeningdebit ));
		
		$EXPENSEOpeningFinal = $EXPENSEOpeningdebit - $EXPENSEOpeningcredit;
		$expenseTotal = abs(($EXPENSEdebit + $EXPENSEOpeningdebit) - ($EXPENSEcredit + $EXPENSEOpeningcredit));
		
		$LastYearBalance =  $INCOMEOpeningFinal - $EXPENSEOpeningFinal;
		$CurrentYearBalance =  abs(($INCOMEcredit- $INCOMEdebit)- ($EXPENSEdebit - $EXPENSEcredit));
		if($incomeTotal > $expenseTotal)
		{
			
			$netIncome = $incomeTotal - $expenseTotal;
			$ProfitnLoss = $netIncome;
			$LIABILITYTotal = $LIABILITYTotal +  $ProfitnLoss;
			
		}
		else
		{
			$netLoss = $expenseTotal - $incomeTotal;
			$ProfitnLoss = $netLoss;
			$LIABILITYTotal = $LIABILITYTotal  - $ProfitnLoss;
		}
		$Result['LastYearBalance'] = $LastYearBalance;
		$Result['CurrentYearBalance'] = $CurrentYearBalance;
		$Result['ProfitnLoss'] = $ProfitnLoss;
		$Result['LIABILITYTotal'] = $LIABILITYTotal;
		
		return $Result;
	}
	
}
?>