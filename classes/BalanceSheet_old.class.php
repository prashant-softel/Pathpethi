<?php
include_once "dbconst.class.php";
class BalanceSheet
{
	
	private $m_dbConn;
	public $CreditTotal=0;
	public $DebitTotal=0;
	public $OpeningBalanceTotal = 0;
	public $ClosingBalanceTotal = 0;
	public $unsetArray=array();
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
	}
	
	public function CategoryArray($GroupID, $from, $to,$IncomeExpenseAccount=0)
	{
		$sql="SELECT *  FROM `account_category` where group_id=".$GroupID."";
		$data=$this->m_dbConn->select($sql);
		return $this->FormatArray($data,$GroupID, $from, $to,$IncomeExpenseAccount);	
	}
	
	public function LedgerArray($GroupID,$SubCategoryID, $from, $to,$IncomeExpenseAccount=0)
	{
	
		if($GroupID==1)
		{	
			$sql="SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,SUM(liabilitytbl.Credit) as credit,SUM(liabilitytbl.Debit) as debit,ledgertbl.id as LedgerID FROM `liabilityregister` as liabilitytbl JOIN `ledger` as ledgertbl on liabilitytbl.LedgerID=ledgertbl.id JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id=".$SubCategoryID."  and ledgertbl.society_id=".$_SESSION['society_id']." AND liabilitytbl.Date BETWEEN '".$from."' AND '".$to."' GROUP BY ledgertbl.id";
		}
		if($GroupID==2)
		{
			if($SubCategoryID == $_SESSION['default_cash_account'] || $SubCategoryID == $_SESSION['default_bank_account'])
			{
				$sql="SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name, SUM( bk.PaidAmount ) AS credit,SUM( bk.ReceivedAmount ) as debit,ledgertbl.id as LedgerID  FROM bankregister as bk JOIN ledger as ledgertbl on ledgertbl.id = bk.LedgerID JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id=".$SubCategoryID."  and ledgertbl.society_id=".$_SESSION['society_id']." AND bk.Date BETWEEN '".$from."' AND '".$to."' GROUP BY ledgertbl.id";
			}
			else
			{
				$sql="SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,SUM(assettbl.Credit) as credit,SUM(assettbl.Debit) as debit,ledgertbl.id as LedgerID FROM `assetregister` as assettbl JOIN `ledger` as ledgertbl on assettbl.LedgerID=ledgertbl.id JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id=".$SubCategoryID." and ledgertbl.society_id=".$_SESSION['society_id']." AND assettbl.Date BETWEEN '".$from."' AND '".$to."' GROUP BY ledgertbl.id";
			}
		}
		if($GroupID==3)
		{
			$sql="SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,SUM(incometbl.Credit) as credit,SUM(incometbl.Debit) as debit,ledgertbl.id as LedgerID FROM `incomeregister` as incometbl JOIN `ledger` as ledgertbl on incometbl.LedgerID=ledgertbl.id JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id=".$SubCategoryID."  and ledgertbl.society_id=".$_SESSION['society_id']." AND incometbl.Date BETWEEN '".$from."' AND '".$to."' GROUP BY ledgertbl.id";
		}
		if($GroupID==4)
		{
			$sql="SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,SUM(expensetbl.Credit) as credit,SUM(expensetbl.Debit) as debit,ledgertbl.id as LedgerID FROM `expenseregister` as expensetbl JOIN `ledger` as ledgertbl on expensetbl.LedgerID=ledgertbl.id JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id=".$SubCategoryID."  and ledgertbl.society_id=".$_SESSION['society_id']." AND expensetbl.Date BETWEEN '".$from."' AND '".$to."' GROUP BY ledgertbl.id";
		}	
		
		$data=$this->m_dbConn->select($sql);
		return $data;	
	}
	
	public function setAmount($datas, $parent, $creditamount,$debitamount, $maxId,$openingBalance,$closingBalance)
	{
		$total=0;
		
		for($iCnt = $maxId-1; $iCnt >= 0  ; $iCnt--)
		{
			if($datas[$iCnt]['id'] == $parent)
			{
			   $datas[$iCnt]['credit'] += $creditamount;
			   $datas[$iCnt]['debit'] += $debitamount;
			   $datas[$iCnt]['OpeningBalance'] += $openingBalance;
			   $datas[$iCnt]['ClosingBalance'] += $closingBalance;
			}
					
		}
		
		return $datas;
	}
	
	public function generatePageTreeNew($datas, $parent = 0, $depth=0, $padding = 10)
	{
		$amount = array();
		if($depth > 1000) return ''; // Make sure not to have an endless recursion
		
		$tree = '<tr>';
		for($i = 0, $ni = count($datas); $i < $ni; $i++){
			if($datas[$i]['parent_id'] == $parent && $datas[$i]['credit'] >0){
				$tree .= '<td style="width:300px;padding-left:' . $padding . 'px;" id="li_' . $datas[$i]['id'] .'_'.$depth.  '" >';
				$tree .= $datas[$i]['name'] . '<td style="width:100px;padding-left:' . $padding . 'px;">' . $datas[$i]['credit'];
				$padding += 10;
				$tree .= $this->generatePageTreeNew($datas, $datas[$i]['id'], $depth+1, $padding);
				$padding -= 10;
				$tree .= '</td></td>';
			}
		}
		
		$tree .= '</tr>';
		return $tree;
	}
	

	public function generateBalanceSheet($datas, $parent = 0, $depth=0, $padding = 10)
	{
		if($depth > 1000) return ''; // Make sure not to have an endless recursion
		
		$tree = '<ul>';
		foreach($datas as $key){
			
			if($key['parent_id'] == $parent && ($key['credit'] > 0 ||  $key['debit'] >0)){
			$amount=abs($key['credit'] - $key['debit']);
				if($parent == 0 OR $parent == 1)
				{
					$tree .= '<li id="li_' . $key['id'] .'_'.$depth.  '" ><label><span style="border:none;width:250px;">';	
					$tree .= $key['name'].'</span><span style="border:none;width:80px;text-align:right;">'.number_format($amount,2).'</span></label>';
				}
				else
				{
					$tree .= '<li id="li_' . $key['id'] .'_'.$depth.  '"  style="display:none;"><label ><span style=" border:none;width:250px;" class="icon-minus-sign">';	
					if($key['LedgerID'] > 0)
					{
						$Url = "view_ledger_details.php?lid=".$key['LedgerID']."&gid=".$key['GroupType']."&dt";
						$tree .= '<a href="'.$Url.'">'. $key['name']. '</a></span><span style="border:none;width:80px;text-align:right;" class="icon-minus-sign"><a href="'.$Url.'">'.number_format($amount,2).'</a></span></label>';
					}
					else
					{
						$tree .= $key['name'].'</span><span style="border:none;width:80px;text-align:right;" class="icon-minus-sign">'.number_format($amount,2).'</span></label>';
					}
				}
				$padding += 10;
				$tree .= $this->generateBalanceSheet($datas, $key['id'], $depth+1, $padding);
				$padding -= 10;
				$tree .= '</li>';
			}
			
		}
		
		$tree .= '</ul>';
		return $tree;
	}

	public function generateTrialBalance($datas, $parent = 0, $depth=0, $padding = 10)
	{
		$amount = array();
		if($depth > 1000) return ''; // Make sure not to have an endless recursion
		
		$tree = '<ul>';
		for($i = 0, $ni = count($datas); $i < $ni; $i++){
			if($datas[$i]['parent_id'] == $parent && ($datas[$i]['credit'] > 0 OR  $datas[$i]['debit'] >0)){
				if($parent == 0 OR $parent == 1)
				{
					$balance = $datas[$i]['credit'] - $datas[$i]['debit'];
					$balance = abs($balance);
					$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '" style="border:none;"><label style="width:100%;"><div style="width:100%;border:none;"><div style="border:none;width:20%; float:left;" >';	
					if(!isset($_GET['q']) || (isset($_GET['q']) && $_GET['q'] == 2))
					{
					 	$tree .= $datas[$i]['name'].'</a></div><div style="border:none;width:20%;text-align:right;float:left;">'.number_format($datas[$i]['OpeningBalance'],2).'</div><div style="border:none;width:20%;text-align:right;float:left;">'.number_format($datas[$i]['credit'],2).'</div><div style="border:none;width:20%;text-align:right;float:left;">'.number_format($datas[$i]['debit'],2).'</div><div style="border:none;width:18%;text-align:right;float:left;">'.number_format($balance,2).'</div></div></label>';
					}
					else
					{
						$tree .= $datas[$i]['name'].'</a></div><div style="border:none;width:20%;text-align:right;float:left;">'.number_format($datas[$i]['credit'],2).'</div><div style="border:none;width:20%;text-align:right;float:left;">'.number_format($datas[$i]['debit'],2).'</div><div style="border:none;width:18%;text-align:right;float:left;">'.number_format($balance,2).'</div></div></label>';	
					}
					$balance = 0;
				}
				else
				{
					$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '"  style="display:none;"><label style="width:100%;"><div style="width:100%;border:none;"><div style=" border:none;width:20%;float:left;" class="icon-minus-sign">';	
					
					if($datas[$i]['LedgerID'] > 0)
					{
						$Url = "view_ledger_details.php?lid=".$datas[$i]['LedgerID']."&gid=".$datas[$i]['GroupType']."&dt";
						$tree .= '<a href="'.$Url.'">'.$datas[$i]['name'].'</a></div><div style="border:none;width:20%;text-align:right;float:left;" class="icon-minus-sign"><a href="'.$Url.'">'.number_format($datas[$i]['OpeningBalance'],2).'</a></div><div style="border:none;width:20%;text-align:right;float:left;" class="icon-minus-sign"><a href="'.$Url.'">'.number_format($datas[$i]['credit'],2).'</a></div><div style="border:none;width:20%;text-align:right;float:left;"><a href="'.$Url.'">'.number_format($datas[$i]['debit'],2).'</a></div><div style="border:none;width:18%;text-align:right;float:left;"><a href="'.$Url.'">'.number_format($datas[$i]['ClosingBalance'],2).'</a></div></label>';
					}
					else
					{
						$tree .= $datas[$i]['name'].'</div><div style="border:none;width:20%;text-align:right;float:left;" class="icon-minus-sign">'.number_format($datas[$i]['OpeningBalance'],2).'</div><div style="border:none;width:20%;text-align:right;float:left;" class="icon-minus-sign">'.number_format($datas[$i]['credit'],2).'</div><div style="border:none;width:20%;text-align:right;float:left;">'.number_format($datas[$i]['debit'],2).'</div><div style="border:none;width:18%;text-align:right;float:left;">'.number_format($datas[$i]['ClosingBalance'],2).'</div></div></label>';
					}
					
				}
				$padding += 10;
				$tree .= $this->generateTrialBalance($datas, $datas[$i]['id'], $depth+1, $padding);
				$padding -= 10;
				$tree .= '</li>';
			}
			
		}
		
		$tree .= '</ul>';
		return $tree;
	}
	
	

	
	public function generateTrialBalanceTable($datas, $parent = 0, $depth=0, $padding = 10)
	{
		$amount = array();
		if($depth > 1000) return ''; // Make sure not to have an endless recursion
		
		$tree = '';
		for($i = 0, $ni = count($datas); $i < $ni; $i++){
			if($datas[$i]['parent_id'] == $parent && ($datas[$i]['credit'] > 0 OR  $datas[$i]['debit'] >0)){
				if($parent == 0 OR $parent == 1)
				{
					$balance = $datas[$i]['credit'] - $datas[$i]['debit'];
					$balance = abs($balance);
					$tree .= '<tr id="li_' . $datas[$i]['id'] .'_'.$depth.  '" class="rowstyle" ><label><td style="padding-left:20px;"><span ><b>';	
					$tree .= $datas[$i]['name'].'</b></span></td><td style="text-align:right;"><span >'.number_format($datas[$i]['OpeningBalance'],2).'</span></td><td style="text-align:right;"><span >'.number_format($datas[$i]['credit'],2).'</span></td><td style="text-align:right;"><span >'.number_format($datas[$i]['debit'],2).'</span></td><td style="text-align:right;"><span >'.number_format($balance,2).'</span></td></label>';
					$balance = 0;
				}
				else
				{
					$tree .= '<tr id="li_' . $datas[$i]['id'] .'_'.$depth.  '"  class="rowstyle"><label ><td style="padding-left:20px;"><span style="width:300px;" >';	
					
					if($datas[$i]['LedgerID'] > 0)
					{
						$Url = "view_ledger_details.php?lid=".$datas[$i]['LedgerID']."&gid=".$datas[$i]['GroupType']."&dt";
						$tree .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$Url.'">'.$datas[$i]['name'].'</a></span></td><td style="text-align:right;"><span ><a href="'.$Url.'">'.number_format($datas[$i]['OpeningBalance'],2).'</a></span></td><td style="text-align:right;" ><span ><a href="'.$Url.'">'.number_format($datas[$i]['credit'],2).'</a></span></td><td style="text-align:right;"><span ><a href="'.$Url.'">'.number_format($datas[$i]['debit'],2).'</a></span></td><td style="text-align:right;"><span ><a href="'.$Url.'">'.number_format($datas[$i]['ClosingBalance'],2).'</a></span></td></label>';
					}
					else
					{
						$tree .= '<b>&nbsp;&nbsp;&nbsp;'.$datas[$i]['name'].'</b></span></td><td style="text-align:right;"><span >'.number_format($datas[$i]['OpeningBalance'],2).'</span></td><td style="text-align:right;" ><span >'.number_format($datas[$i]['credit'],2).'</span></td><td style="text-align:right;"><span >'.number_format($datas[$i]['debit'],2).'</span></td><td style="text-align:right;"><span >'.number_format($datas[$i]['ClosingBalance'],2).'</span></td></label>';
					}
					
				}
				$tree .= $this->generateTrialBalanceTable($datas, $datas[$i]['id'], $depth+1, $padding);
				$tree .= '</tr>';
			}
			
		}
		
		return $tree;
	}
	
	public function generateIncomeStatement($datas, $parent = 0, $depth=0, $padding = 10)
	{
		$amount = array();
		if($depth > 1000) return ''; // Make sure not to have an endless recursion
		
		$tree = '<ul>';
		for($i = 0, $ni = count($datas); $i < $ni; $i++){
			if($datas[$i]['parent_id'] == $parent && ($datas[$i]['credit'] > 0 OR  $datas[$i]['debit'] >0)){
				$netAmount = $datas[$i]['credit'] - $datas[$i]['debit'];
				$netAmount = abs($netAmount);
				if($parent == 0 OR $parent == 1)
				{
					$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '" ><label><span style="border:none;width:160px;">';	
					$tree .= $datas[$i]['name'].'</span><span style="border:none;width:228px;padding-left:50px;text-align:right;">'.number_format($netAmount,2).'</span></label>';
				}
				else
				{
					$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '"  style="display:none;"><label ><span style=" border:none;width:160px;" class="icon-minus-sign">';	
					
					if($datas[$i]['LedgerID'] > 0)
					{
						$Url = "view_ledger_details.php?lid=".$datas[$i]['LedgerID']."&gid=".$datas[$i]['GroupType']."&dt";
						$tree .= '<a href="'.$Url.'">'.$datas[$i]['name'].'</a></span><span style="border:none;width:228px;padding-left:50px;text-align:right;" class="icon-minus-sign"><a href="'.$Url.'">'.number_format($netAmount,2).'</a></span></label>';
					}
					else
					{
						$tree .= $datas[$i]['name'].'</span><span style="border:none;width:228px;padding-left:50px;text-align:right;" class="icon-minus-sign">'.number_format($netAmount,2).'</span></label>';
					}
					
				}
				$padding += 10;
				$tree .= $this->generateIncomeStatement($datas, $datas[$i]['id'], $depth+1, $padding);
				$padding -= 10;
				$tree .= '</li>';
			}
			
		}
		
		$tree .= '</ul>';
		return $tree;
	}
	
	public function generatePageTree($datas, $parent = 0, $depth=0)
	{
		$amount = array();
		if($depth > 1000) return ''; // Make sure not to have an endless recursion
		
		$tree = '<tr>';
		
		for($i=0, $ni=count($datas); $i < $ni; $i++){
			if($datas[$i]['parent_id'] == $parent){
				$tree .= '<td style="width:150px;padding-left:' . $padding . 'px;" id="li_' . $datas[$i]['id'] .  '">';
				$tree .= $datas[$i]['name'] . '<td style="width:50px;padding-left:' . $padding . 'px;">' . $datas[$i]['credit'];
				$padding += 10;
				$tree .= $this->generatePageTree($datas, $datas[$i]['id'], $depth+1);
				$padding -= 10;
				$tree .= '</td></td>';

			}
		}
		$tree .= '</tr>';
		return $tree;
	}
	
	public function FormatArray($Data,$GroupID, $from, $to,$IncomeExpenseAccount=0)
	{
		$type=$GroupID;
		$categories=array();	
		$max=1000;
		foreach($Data as $category) {
			$parent=false;
			if($category['parentcategory_id']==0)
			{
				$parent=true;	
			}
			$categories[] = array('id' => $category['category_id'], 'name' =>$category['category_name'], 'parent_id' => $category['parentcategory_id'], 'credit' =>0,'IsParent' => $parent,'debit' =>0,'GroupType' => $type,'LedgerID' => 0,'OpeningBalance' => 0,'ClosingBalance' => 0);
			if($IncomeExpenseAccount <> 0)
			{
			$LedgerData=$this->LedgerArray($GroupID,$category['category_id'], $from, $to,$IncomeExpenseAccount);
			}
			else{$LedgerData=$this->LedgerArray($GroupID,$category['category_id'], $from, $to);}
			foreach($LedgerData as $category2) 
			{
				$max++;
				$openingLedgerBalance = 0;
				$balance = $category2['credit'] - $category2['debit'];
				$closingBalance = abs($balance);
				if($GroupID == 1 || $GroupID == 2)
				{
					$openingLedgerBalance = $this->getLedgerOpeningBalance($category2['LedgerID'],$GroupID);
				}
				
				$categories[] = array('id' => $max, 'name' =>$category2['name'], 'parent_id' => $category['category_id'], 'credit' => $category2['credit'],'debit' => $category2['debit'],'GroupType' => $type,'LedgerID' => $category2['LedgerID'],'OpeningBalance' => $openingLedgerBalance,'ClosingBalance' => $closingBalance);
			}
		}
	   return $this->CalculateTotal($categories);
	}
	
	public function CalculateTotal($categories)
	{
		for($iCnt = sizeof($categories)-1; $iCnt >=0 ; $iCnt--)
		{
			$parent = $categories[$iCnt]['parent_id'];
			$categories = $this->setAmount($categories, $parent, $categories[$iCnt]['credit'],$categories[$iCnt]['debit'], $iCnt,$categories[$iCnt]['OpeningBalance'],$categories[$iCnt]['ClosingBalance']);
		}
		for($iCnt = sizeof($categories)-1; $iCnt >= 0 ;$iCnt--)
		{
			
			$parent = $categories[$iCnt]['parent_id'];
			if($parent==0 || $parent==1)
			{
				$this->CreditTotal +=$categories[$iCnt]['credit'];
				$this->DebitTotal +=$categories[$iCnt]['debit'];	
				$this->OpeningBalanceTotal += $categories[$iCnt]['OpeningBalance'];
				$this->ClosingBalanceTotal += $categories[$iCnt]['ClosingBalance']; 
			}
		}
		
		return $categories;
	
	}
	
	public function getTotal($Data)
	{
		$total=0;
		foreach($Data as $key)
		{
			if($key['parent_id']==0 or $key['parent_id']== 1)
			{
				$total +=abs($key['credit'] - $key['debit']);
			}
		}	
		
		return $total;
	}
	
	
	public function getCreditTotal($LIABILITYData,$ASSETData,$INCOMEData,$EXPENSEData)
	{
		$total=0;
		foreach($LIABILITYData as $key)
		{
			if($key['parent_id']==1)
			{
					$total +=$key['credit'] + $key['debit'];	
			}	
			
		}
		foreach($LIABILITYData as $key)
		{
			if($key['parent_id']==1)
			{
					$total +=$key['credit'] + $key['debit'];	
			}	
			
		}
		foreach($LIABILITYData as $key)
		{
			if($key['parent_id']==1)
			{
					$total +=$key['credit'] + $key['debit'];	
			}	
			
		}
		foreach($LIABILITYData as $key)
		{
			if($key['parent_id']==1)
			{
					$total +=$key['credit'] + $key['debit'];	
			}	
			
		}		
	}
	
	public function OpeningBalanceCalculate()
	{
		
		$LiabilitySql="SELECT SUM(Debit) as Debit,SUM(Credit) as Credit FROM `liabilityregister` where Is_Opening_Balance=1 ";
		$result1=$this->m_dbConn->select($LiabilitySql);
		$AssetSql="SELECT SUM(Debit) as Debit,SUM(Credit) as Credit FROM `assetregister` where Is_Opening_Balance=1 ";
		$result2=$this->m_dbConn->select($AssetSql);
		$LiabilityTotal = abs($result1[0]['Credit'] - $result1[0]['Debit']);
		$AssetTotal = abs($result2[0]['Credit'] - $result2[0]['Debit']);
		$OpeningBalance= $AssetTotal - $LiabilityTotal;
		return $OpeningBalance;
			
	}
	
	public function TotalAmount($TableName)
	{
		$Sql="SELECT SUM(Debit) as Debit,SUM(Credit) as Credit FROM `".$TableName."`";
		$result2=$this->m_dbConn->select($Sql);
		return $result2;
			
	}
	
	public function ArrayShifting($GroupID,&$Data,&$DataShiftarray)
	{
		for($i=0;$i < sizeof($Data);$i++)
		{
			if($GroupID==1){ $amount=$Data[$i]['credit'] - $Data[$i]['debit'];}
			else{$amount=$Data[$i]['debit'] - $Data[$i]['credit'];}
			
			if(($Data[$i]['parent_id']==0 || $Data[$i]['parent_id']==1) && $amount <0)
			{
				$newArray[] = array();
				array_push($newArray,$Data[$i]);
				$newArray[1]['credit'] =0;
				$newArray[1]['debit'] =0;
				array_push($DataShiftarray,$newArray[1]);
				$DataShiftarray=$this->FindAllChild($Data[$i]['id'],$Data,$this->unsetArray,$GroupID,$DataShiftarray,1,0);
			}

		}
		
		return $DataShiftarray;
	}
	
	public function FindAllChild($id ,&$Data,$unsetArray,$GroupID,&$DataShiftarray,$show=0,$parentAmount=0)
	{
		
		for($i=0;$i < sizeof($Data)-1;$i++)
		{
			if($Data[$i]['parent_id']==$id)
			{
				if($Data[$i]['GroupType']==1){ $amount=$Data[$i]['credit'] - $Data[$i]['debit'];}
				else{$amount=$Data[$i]['debit'] - $Data[$i]['credit'];}
				
				if($amount <0 && $show==1)
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
					
					
					foreach($DataShiftarray as $key =>$v)
					{
						
						if($DataShiftarray[$key]['id'] == $id)
						{
							$DataShiftarray[$key]['credit'] += $Data[$i]['credit'];
							$DataShiftarray[$key]['debit'] += $Data[$i]['debit'];
					
						}
					}
					
				
				}
				
				if($parentAmount < 0)
				{
					array_push($DataShiftarray,$Data[$i]);
					array_push($this->unsetArray,$i);
					$DataShiftarray=$this->FindAllChild($Data[$i]['id'],$Data,$unsetArray,$GroupID,$DataShiftarray,0,$parentAmount);
					
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
		foreach($this->unsetArray as $key=>$value)
		{
			unset($Data[$value]);	
		}
		return $Data;	
	}
	
	public function getLedgerOpeningBalance($lid,$gid)
	{
		
		if($gid == 1)
		{
			$tableName = 'liabilityregister';	
		}
		else if($gid == 2)
		{
			$tableName = 'assetregister';		
		}
		
		if($tableName <> "")
		{
			$sql = " select * from  `" . $tableName. "` where `LedgerID`= '".$lid."' and `Is_Opening_Balance` = 1 ";
			$res = $this->m_dbConn->select($sql);
			if($res[0]['Credit'] > 0)
			{
				return 	$res[0]['Credit'];
			}
			else if($res[0]['Debit'] > 0)
			{
				return 	$res[0]['Debit'];	
			}
			else
			{
				return 0;	
			}
		}
			
	} 
	
	
	
}
?>