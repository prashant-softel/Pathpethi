<?php
include_once "dbconst.class.php";
include_once "utility.class.php";

class OpeningBalance
{
	
	private $m_dbConn;
	public $CreditTotal = 0;
	public $DebitTotal = 0;
	public $unsetArray = array();
	public $m_objUtility;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->m_objUtility = new utility($dbConn);
	}
	
	public function CategoryArray($GroupID,$IncomeExpenseAccount = 0)
	{
		$sql = "SELECT *  FROM `account_category` where group_id = ".$GroupID." order by parentcategory_id";
		$data = $this->m_dbConn->select($sql);
		return $this->FormatArray($data,$GroupID,$IncomeExpenseAccount);	
	}
	
	public function LedgerArray($GroupID,$SubCategoryID,$IncomeExpenseAccount = 0)
	{
		if($GroupID == 1)
		{	
			if($IncomeExpenseAccount <> 0)
			{
				$sql = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,SUM(liabilitytbl.Credit) as credit,SUM(liabilitytbl.Debit) as debit,ledgertbl.id as LedgerID FROM `liabilityregister` as liabilitytbl JOIN `ledger` as ledgertbl on liabilitytbl.LedgerID=ledgertbl.id JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id=".$SubCategoryID." and ledgertbl.society_id=".$_SESSION['society_id']." ";
			}
			else
			{
				$sql = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,SUM(liabilitytbl.Credit) as credit,SUM(liabilitytbl.Debit) as debit,ledgertbl.id as LedgerID FROM `liabilityregister` as liabilitytbl JOIN `ledger` as ledgertbl on liabilitytbl.LedgerID=ledgertbl.id JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id=".$SubCategoryID."  and ledgertbl.society_id=".$_SESSION['society_id']." ";
			}
			
			$sql .= "  GROUP BY ledgertbl.id";
		}
		if($GroupID == 2)
		{
			if($SubCategoryID == $_SESSION['default_cash_account'] || $SubCategoryID == $_SESSION['default_bank_account'])
			{
				$sql = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name, SUM( bk.PaidAmount ) AS credit,SUM( bk.ReceivedAmount ) as debit,ledgertbl.id as LedgerID  FROM bankregister as bk JOIN ledger as ledgertbl on ledgertbl.id = bk.LedgerID JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id=".$SubCategoryID."   and ledgertbl.society_id=".$_SESSION['society_id']." ";
			}
			else
			{
				$sql = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,SUM(assettbl.Credit) as credit,SUM(assettbl.Debit) as debit,ledgertbl.id as LedgerID FROM `assetregister` as assettbl JOIN `ledger` as ledgertbl on assettbl.LedgerID=ledgertbl.id JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id=".$SubCategoryID."   and ledgertbl.society_id=".$_SESSION['society_id']." ";
			}
			
			$sql .= "  GROUP BY ledgertbl.id";
		}
		if($GroupID == 3)
		{
			$sql = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,ledgertbl.id as LedgerID,SUM(incometbl.Credit) as credit,SUM(incometbl.Debit) as debit FROM `ledger` as ledgertbl LEFT JOIN `incomeregister` as incometbl on ledgertbl.id = incometbl.LedgerID AND incometbl.Date BETWEEN '".$from."' AND '".$to."' where ledgertbl.society_id = ".$_SESSION['society_id']." AND ledgertbl.categoryid=".$SubCategoryID." GROUP BY ledgertbl.id";
		}
		
		if($GroupID == 4)
		{
			$sql = "SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,ledgertbl.id as LedgerID,SUM(expensetbl.Credit) as credit,SUM(expensetbl.Debit) as debit FROM `ledger` as ledgertbl LEFT JOIN `expenseregister` as expensetbl on ledgertbl.id = expensetbl.LedgerID AND expensetbl.Date BETWEEN '".$from."' AND '".$to."' where ledgertbl.society_id = ".$_SESSION['society_id']." AND ledgertbl.categoryid=".$SubCategoryID." GROUP BY ledgertbl.id ";
		}
				
		if($sql <> "")
		{
			$data = $this->m_dbConn->select($sql);
			return $data;
		}
	}
	
	public function setAmount($datas, $parent, $creditamount,$debitamount, $maxId)
	{
		$total = 0;
		
		//for($iCnt = $maxId-1; $iCnt >= 0  ; $iCnt--)
		for($iCnt = sizeof($datas)-1; $iCnt >= 0  ; $iCnt--)
		{
			if($datas[$iCnt]['id'] == $parent)
			{
				$datas[$iCnt]['credit'] += $creditamount;
			    $datas[$iCnt]['debit'] += $debitamount;
			}
			
		}
		
		return $datas;
	}
	
	
	public function generateOpeningBalanceTree($datas, $parent = 0, $type,$depth = 0)
	{
		$SubHeadamountType;
		if($depth > 1000) return ''; // Make sure not to have an endless recursion
		
		$tree = '<ul>';
		foreach($datas as $key)
		{
			
			/*if($key['GroupType'] == 1)
				{
					echo "<pre>";
					print_r($key);
					echo "</pre>";
				}*/
			
			if($key['parent_id'] == $parent)
			{
				
				//var_dump($key);
				
				$amount = $key['credit'] - $key['debit'];
				$amountType = "";
				
				if($type == ASSET)
				{
					$amount = $key['debit'] - $key['credit'] ;
				}
				
				if($amount > 0 && $type == ASSET)
				{
					$amountType = ' Dr';
				}
				else if($amount < 0 && $type == ASSET)
				{
					$amountType = ' Cr';	
				}
				elseif($amount > 0 && $type == LIABILITY)
				{
					$amountType = ' Cr';	
				}
				elseif($amount < 0 && $type == LIABILITY)
				{
					$amountType = ' Dr';	
				}	
				
				$DisplayAmount = abs($amount);
				
				if($amount <> 0)
				{
					if($parent == 0 OR $parent == 1)
					{
						//echo "<br>Parent Exits";
						$tree .= '<li id="li_' . $key['id'] .'_'.$depth.  '" ><label><span style="border:none;width:250px;">';	
						$tree .= $key['name'].'['.$amountType.']'.'</span><span style="border:none;width:100px;text-align:right;">'.number_format($DisplayAmount,2).'</span></label>';
					}
					else
					{
						//echo "<br>Parent ";
						$tree .= '<li id="li_' . $key['id'] .'_'.$depth.  '"  style="display:none;"><label ><span style=" border:none;width:250px;" class="icon-minus-sign">';	
						if($key['LedgerID'] > 0)
						{
							$LedgetUrl = "ledger.php?edt=".$key['LedgerID'];
							$Url = "view_ledger_details.php?lid=".$key['LedgerID']."&gid=".$key['GroupType']."";
							$tree .= '<a href="'.$LedgetUrl.'" target="_blank">'.$key['name'].'</a></span><span style="border:none;width:100px;text-align:right;" class="icon-minus-sign"><a href="'.$Url.'" target="_blank">'.number_format($DisplayAmount,2). $amountType.'</a></span></label>';
						}
						else
						{
							$tree .= $key['name'].'</span><span style="border:none;width:100px;text-align:right;" class="icon-minus-sign">'.number_format($DisplayAmount,2). $amountType.'</span></label>';
						}
					}
				}
				$tree .= $this->generateOpeningBalanceTree($datas, $key['id'], $type,$depth+1);
				$tree .= '</li>';
			}
			
		}
		
		$tree .= '</ul>';
		return $tree;
	}


    public function generateOpeningBalanceTreeTable($datas, $parent = 0,$type, $depth = 0)
	{
		if($depth > 1000) return ''; // Make sure not to have an endless recursion
		
		$tree = '';
		foreach($datas as $key)
		{
			
			if($key['parent_id'] == $parent )
			{
				$amount = $key['credit'] - $key['debit'];
				if($type == ASSET)
				{
					$amount = $key['debit'] - $key['credit'];
				}
				if($amount <> 0)
				{
					if($parent == 0 OR $parent == 1)
					{
						$tree .= '<tr id="li_' . $key['id'] .'_'.$depth.  '" ><label><td style="width:490px;color:blue;border-right:1px solid black;border-left:1px solid black;"><span><b>';	
						$tree .= $key['name'].'</b></span></td><td style="text-align:right;width:100px;color:blue;border-right:1px solid black;"><span><u></u></span></td><td style="text-align:right;width:100px;color:blue;border-right:1px solid black;"><span><u>'.number_format($amount,2).'</u></span></td></label></tr>';
					}
					else
					{
							$tree .= '<tr id="li_' . $key['id'] .'_'.$depth.  '"><label ><td style="width:490px;border-right:1px solid black;border-left:1px solid black;"><span>';	
							if($key['LedgerID'] > 0)
							{
								$tree .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$key['name'].'</span></td><td style="text-align:right;width:100px;border-right:1px solid black;" ><span>'.number_format($amount,2).'</span></td><td style="text-align:right;width:100px;border-right:1px solid black;" ><span></span></td></label></tr>';
							}
							else
							{
								$tree .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$key['name'].'</b></span></td><td style="text-align:right;width:100px;border-right:1px solid black;" ><span></span></td><td style="text-align:right;width:100px;border-right:1px solid black;" ><span><b><u>'.number_format($amount,2).'</u></b></span></td></label></tr>';
							}
					}
				}
				$tree .= $this->generateOpeningBalanceTreeTable($datas, $key['id'],$type, $depth+1);
			}
			
		}
		
		return $tree;
	}
	
	
	public function FormatArray($Data,$GroupID,$IncomeExpenseAccount = 0)
	{
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
			
			$categories[] = array('id' => $category['category_id'], 'name' =>$category['category_name'], 'parent_id' => $category['parentcategory_id'], 'credit' =>0,'IsParent' => $parent,'debit' =>0,'GroupType' => $type,'LedgerID' => 0);
			if($IncomeExpenseAccount <> 0)
			{
			$LedgerData = $this->LedgerArray($GroupID,$category['category_id'],$IncomeExpenseAccount);
			}
			else
			{
				$LedgerData = $this->LedgerArray($GroupID,$category['category_id']);
			}
			foreach($LedgerData as $category2) 
			{
				$max++;
				
				if( $category2['LedgerID'] != "")
				{
					$arLedgerIDParentDetails = $this->m_objUtility->getParentOfLedger($category2['LedgerID']);
					if(!(empty($arLedgerIDParentDetails)))
					{
						$LedgerGroupID = $arLedgerIDParentDetails['group'];
						$LedgerCategoryID = $arLedgerIDParentDetails['category'];
						$date = $this->m_objUtility->getCurrentYearBeginingDate($_SESSION['default_year']);
						
						$res = $this->m_objUtility->getOpeningBalance($category2['LedgerID'],$date);
						if($res <> "")
						{
							
							$category2['credit'] = $res['Credit'];
							$category2['debit'] = $res['Debit'];
							if($LedgerCategoryID == BANK_ACCOUNT || $LedgerCategoryID == CASH_ACCOUNT)
							{
								$category2['credit'] = $res['Debit'];
								$category2['debit'] = $res['Credit'];		
							}
							
						}
						if($LedgerCategoryID == DUE_FROM_MEMBERS)
						{
							$LedgerName = $this->getMemberName($category2['LedgerID']);
							if($LedgerName <> "")
							{
								$category2['name'] = $category2['name'].' - '.$LedgerName;
									
							}	
						}
					}
				}
				
				$categories[] = array('id' => $max, 'name' =>$category2['name'], 'parent_id' => $category['category_id'], 'credit' => $category2['credit'],'debit' => $category2['debit'],'GroupType' => $type,'LedgerID' => $category2['LedgerID']);
			}
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
			}
		}
		
		//calculating creditfinal,debitfinal,openingbalancefinal,closingbalancefinal amount 
		for($iCnt = sizeof($categories)-1; $iCnt >= 0 ; $iCnt--)
		{
			$parent = $categories[$iCnt]['parent_id'];
			//adding credit and debit amount of ledgers and setting  to ledgers main parent
			$categories = $this->setAmount($categories, $parent, $categories[$iCnt]['credit'],$categories[$iCnt]['debit'],$iCnt);
		}
		for($iCnt = sizeof($categories)-1; $iCnt >= 0 ;$iCnt--)
		{
			
			$parent = $categories[$iCnt]['parent_id'];
			if($parent == 0 || $parent == 1)
			{
				$this->CreditTotal += $categories[$iCnt]['credit'];
				$this->DebitTotal += $categories[$iCnt]['debit'];	
			}
		}
		
		return $categories;
	
	}
	
	public function getTotal($Data)
	{
		$total = 0;
		foreach($Data as $key)
		{
			if($key['parent_id'] == 0 || $key['parent_id'] == 1)
			{
				$total += $key['credit'] - $key['debit'];
					
			}
			
		}	
		$total = abs($total);
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
	
	public function getMemberName($UnitID)
	{
		$sql = "select `owner_name` from `member_main` where `unit` = '".$UnitID."' and `society_id`='".$_SESSION['society_id']."' and  ownership_date <= '" .$_SESSION['default_year_start_date']. "'  order by  ownership_date desc ";
		$res = $this->m_dbConn->select($sql);
		return $res[0]['owner_name'];	
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
				$amount = $Data[$i]['credit']  -  $Data[$i]['debit'] ;
			}
			else if($GroupID == ASSET &&  $Data[$i]['GroupType'] == ASSET)
			{
				$amount = $Data[$i]['debit']  - $Data[$i]['credit'] ;
			}
			
			//if( ($Data[$i]['parent_id'] == 0 || $Data[$i]['parent_id'] == 1) && $amount < 0 )
			if(($Data[$i]['parent_id'] == 0 || $Data[$i]['parent_id'] == 1))
			{
				$isParentReadyToShift = $this->isAnyChildOfParentNegative($GroupID , $Data[$i]['id'] , $Data);
				
				if($isParentReadyToShift)
				{
					$newArray = array();
					array_push($newArray,$Data[$i]);
					
					$newArray[0]['credit'] = 0;
					$newArray[0]['debit'] = 0;
										
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
							$amount = $Data[$i]['credit']  - $Data[$i]['debit'] ;
						}
						else if($GroupID == ASSET && $Data[$i]['GroupType'] == ASSET)
						{
							$amount = $Data[$i]['debit'] - $Data[$i]['credit'] ;
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
							$amount = $Data[$i]['credit']  - $Data[$i]['debit'] ;
						}
						else if($GroupID == ASSET && $Data[$i]['GroupType'] == ASSET)
						{
							$amount = $Data[$i]['debit']  - $Data[$i]['credit'] ;
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
	
}
?>