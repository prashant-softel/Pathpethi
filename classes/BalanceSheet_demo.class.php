<?php //include_once "ses_set_s.php"; ?>
<?php //include_once("includes/head_s.php");?>
<?php
include_once "dbconst.class.php";
//include_once ("include/dbop.class.php");
	//$dbConn = new dbop();
//echo "test";
class BalanceSheet
{
	
	private $m_dbConn;
	public $CreditTotal=0;
	public $DebitTotal=0;
	public $unsetArray=array();
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
	}
	
	public function CategoryArray($GroupID,$IncomeExpenseAccount=0)
	{
		//echo $GroupID;
	$sql="SELECT *  FROM `account_category` where group_id=".$GroupID."";
	$data=$this->m_dbConn->select($sql);
	//print_r($data);
	//return $data;
	return $this->FormatArray($data,$GroupID,$IncomeExpenseAccount);	
	}
	
	public function LedgerArray($GroupID,$SubCategoryID,$IncomeExpenseAccount=0)
	{
		//echo $GroupID;
	if($GroupID==1)
	{	
	//$sql="SELECT  DISTINCT ledgertbl.ledger_name as name,SUM(assettbl.Credit) as credit,assettbl.Debit as debit FROM `liabilityregister` as assettbl JOIN `ledger` as ledgertbl on ledgertbl.id=assettbl.LedgerID where assettbl.SubCategoryID=".$SubCategoryID." and assettbl.Credit > 0";
	//$sql="SELECT incometbl.LedgerID,ledgertbl.id,categorytbl.group_id, ledgertbl.categoryid FROM `liabilityregister` as incometbl JOIN `ledger` as ledgertbl on incometbl.LedgerID=ledgertbl.id JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id=".$SubCategoryID."  GROUP BY ledgertbl.id";
		
		/*if($IncomeExpenseAccount <> 0)
		{
			//echo 'IncomeExpenseAccount'.$IncomeExpenseAccount;
		$sql="SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,SUM(liabilitytbl.Credit) as credit,SUM(liabilitytbl.Debit) as debit FROM `liabilityregister` as liabilitytbl JOIN `ledger` as ledgertbl on liabilitytbl.LedgerID=ledgertbl.id JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id=".$SubCategoryID." and  liabilitytbl.LedgerID NOT IN(".$IncomeExpenseAccount.") GROUP BY ledgertbl.id";
		}
		else
		{*/
			$sql="SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,SUM(liabilitytbl.Credit) as credit,SUM(liabilitytbl.Debit) as debit FROM `liabilityregister` as liabilitytbl JOIN `ledger` as ledgertbl on liabilitytbl.LedgerID=ledgertbl.id JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id=".$SubCategoryID."   GROUP BY ledgertbl.id";
		//}
	}
	if($GroupID==2)
	{
		//echo "asset";
		//$sql="SELECT DISTINCT led.ledger_name,asset.LedgerID, asset.CategoryID, asset.SubCategoryID, SUM( asset.Debit ) AS debit, SUM( asset.Credit ) AS credit FROM assetregister as asset JOIN ledger as led ON led.id=asset.LedgerID where asset.SubCategoryID=".$SubCategoryID." GROUP BY led.id";
	//$sql="SELECT   ledgertbl.ledger_name as name,SUM(assettbl.Credit) as credit,assettbl.Debit as debit FROM `assetregister` as assettbl JOIN `ledger` as ledgertbl on ledgertbl.id=assettbl.LedgerID where assettbl.SubCategoryID=".$SubCategoryID."  GROUP BY ledgertbl.id";	
		if($SubCategoryID == $_SESSION['default_cash_account'] || $SubCategoryID == $_SESSION['default_bank_account'])
		{
		$sql="SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name, SUM( bk.PaidAmount ) AS credit,SUM( bk.ReceivedAmount ) as debit  FROM bankregister as bk JOIN ledger as ledgertbl on ledgertbl.id = bk.LedgerID JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id=".$SubCategoryID."  GROUP BY ledgertbl.id";
		//echo $sql;
		}
		else{
		$sql="SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,SUM(assettbl.Credit) as credit,SUM(assettbl.Debit) as debit FROM `assetregister` as assettbl JOIN `ledger` as ledgertbl on assettbl.LedgerID=ledgertbl.id JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id=".$SubCategoryID."  GROUP BY ledgertbl.id";
		}
	}
	if($GroupID==3)
	{
		//echo "asset";
		//$sql="SELECT DISTINCT led.ledger_name,asset.LedgerID, asset.CategoryID, asset.SubCategoryID, SUM( asset.Debit ) AS debit, SUM( asset.Credit ) AS credit FROM assetregister as asset JOIN ledger as led ON led.id=asset.LedgerID where asset.SubCategoryID=".$SubCategoryID." GROUP BY led.id";
	//$sql="SELECT   ledgertbl.ledger_name as name,SUM(assettbl.Credit) as credit,assettbl.Debit as debit FROM `assetregister` as assettbl JOIN `ledger` as ledgertbl on ledgertbl.id=assettbl.LedgerID where assettbl.SubCategoryID=".$SubCategoryID."  GROUP BY ledgertbl.id";	
	$sql="SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,SUM(incometbl.Credit) as credit,SUM(incometbl.Debit) as debit FROM `incomeregister` as incometbl JOIN `ledger` as ledgertbl on incometbl.LedgerID=ledgertbl.id JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id=".$SubCategoryID."  GROUP BY ledgertbl.id";
	}
	if($GroupID==4)
	{
		//echo "asset";
		//$sql="SELECT DISTINCT led.ledger_name,asset.LedgerID, asset.CategoryID, asset.SubCategoryID, SUM( asset.Debit ) AS debit, SUM( asset.Credit ) AS credit FROM assetregister as asset JOIN ledger as led ON led.id=asset.LedgerID where asset.SubCategoryID=".$SubCategoryID." GROUP BY led.id";
	//$sql="SELECT   ledgertbl.ledger_name as name,SUM(assettbl.Credit) as credit,assettbl.Debit as debit FROM `assetregister` as assettbl JOIN `ledger` as ledgertbl on ledgertbl.id=assettbl.LedgerID where assettbl.SubCategoryID=".$SubCategoryID."  GROUP BY ledgertbl.id";	
	$sql="SELECT ledgertbl.categoryid,ledgertbl.ledger_name as name,SUM(expensetbl.Credit) as credit,SUM(expensetbl.Debit) as debit FROM `expenseregister` as expensetbl JOIN `ledger` as ledgertbl on expensetbl.LedgerID=ledgertbl.id JOIN `account_category` as categorytbl on ledgertbl.categoryid = categorytbl.category_id where categorytbl.category_id=".$SubCategoryID."  GROUP BY ledgertbl.id";
	}
	$data=$this->m_dbConn->select($sql);
	//print_r($data);
	return $data;	
	}
	
	public function setAmount($datas, $parent, $creditamount,$debitamount, $maxId)
	{
		//echo "set";
		//echo '<br/>' . $parent . ":" . $creditamount . ":" . $debitamount .":". $maxId;
		//$datas[$parent]['credit'] += $amount;
		//echo '<br/>' . $parent . ":" . $amount . ":" . $maxId;
		$total=0;
		
		for($iCnt = $maxId-1; $iCnt >= 0  ; $iCnt--)
		{
			//echo '<br/>Current Parent : ' . $parent.'Current Parent : '.$datas[$iCnt]['id']; 
			if($datas[$iCnt]['id'] == $parent)
			{
				//echo "<br>";
				//echo 'Old Amount for : ' . $parent . ' = ' . $datas[$iCnt]['debit'];
				  //$total +=$datas[$iCnt]['credit'];
				  $datas[$iCnt]['credit'] +=$creditamount;
				   $datas[$iCnt]['debit'] +=$debitamount;
				  //echo "<br>";
				//echo 'New Amount for : ' . $parent . ' = ' . $datas[$iCnt]['debit'];
				//break;
			}
			/*if($datas[$iCnt]['id'] == $parent)
			{
				$datas[$iCnt]['credit']=$total ;
			}*/
			
		}
		
		return $datas;
	}
	
	public function generatePageTreeNew($datas, $parent = 0, $depth=0, $padding = 10)
	{
		//echo "generatetree";
		//echo "<br>";
		//var_dump($datas);
		//die();
		$amount = array();
		if($depth > 1000) return ''; // Make sure not to have an endless recursion
		
		$tree = '<tr>';
		//$padding = 20;
		for($i = 0, $ni = count($datas); $i < $ni; $i++){
			//print_r($datas[$i]);
			
			if($datas[$i]['parent_id'] == $parent && $datas[$i]['credit'] >0){
				//echo $parent;
				//echo $datas[$i]['name'];
				//echo "parent<br>";
				
				$tree .= '<td style="width:300px;padding-left:' . $padding . 'px;" id="li_' . $datas[$i]['id'] .'_'.$depth.  '" >';
				$tree .= $datas[$i]['name'] . '<td style="width:100px;padding-left:' . $padding . 'px;">' . $datas[$i]['credit'];
				$padding += 10;
				$tree .= $this->generatePageTreeNew($datas, $datas[$i]['id'], $depth+1, $padding);
				$padding -= 10;
				$tree .= '</td></td>';
				//echo '<br/> ' . $tree;
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
			//echo "22";
				$amount=abs($key['credit'] - $key['debit']);
				if($parent == 0 OR $parent == 1)
				{
					//echo "test";
					$tree .= '<li id="li_' . $key['id'] .'_'.$depth.  '" ><label><span style="border:none;width:320px;">';	
					//$tree .= $key['name'].'</span><span style="border:none;width:100px;padding-left:50px;">'.number_format($amount,2).'</span></label>';
					$tree .= $key['name'].'</span><span style="border:none;width:100px;text-align:right;">'.number_format($amount,2).'</span></label>';
				}
				else
				{
					$tree .= '<li id="li_' . $key['id'] .'_'.$depth.  '"  style="display:none;"><label ><span style=" border:none;width:320px;" class="icon-minus-sign">';	
					//$tree .= $key['name'].'</span><span style="border:none;width:100px;padding-left:50px;" class="icon-minus-sign">'.number_format($amount,2).'</span></label>';
					$tree .= $key['name'].'</span><span style="border:none;width:100px;text-align:right;" class="icon-minus-sign">'.number_format($amount,2).'</span></label>';
				}
				$padding += 10;
				$tree .= $this->generateBalanceSheet($datas, $key['id'], $depth+1, $padding);
				$padding -= 10;
				$tree .= '</li>';
				//echo '<br/> ' . $tree;
			}
			
		}
		
		$tree .= '</ul>';
		return $tree;
	}

	public function generateTrialBalance($datas, $parent = 0, $depth=0, $padding = 10)
	{
		//echo "generatetree";
		//echo "<br>";
		//var_dump($datas);
		//echo "<br><br>";
		//print_r($datas);
		//echo "count".count($datas);
		//die();
		$amount = array();
		if($depth > 1000) return ''; // Make sure not to have an endless recursion
		
		$tree = '<ul>';
		//$padding = 20;
		for($i = 0, $ni = count($datas); $i < $ni; $i++){
			//print_r($datas[$i]);
			//echo "parent new:".$datas[$i]['parent_id']."<br>";
			
			//echo "old".$parent;
			if($datas[$i]['parent_id'] == $parent && ($datas[$i]['credit'] > 0 OR  $datas[$i]['debit'] >0)){
				//echo "test";
				//echo $datas[$i]['name'];
				//echo "parent<br>";
				//$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '" ><label ><span style="width:300px;">';
				if($parent == 0 OR $parent == 1)
				{
					$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '" ><label><span style="border:none;width:300px;">';	
					$tree .= $datas[$i]['name'].'</span><span style="border:none;width:100px;padding-left:50px;text-align:right;">'.number_format($datas[$i]['credit'],2).'</span><span style="border:none;width:100px;padding-left:50px;text-align:right;">'.number_format($datas[$i]['debit'],2).'</span></label>';
				}
				else
				{
					$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '"  style="display:none;"><label ><span style=" border:none;width:300px;" class="icon-minus-sign">';	
					$tree .= $datas[$i]['name'].'</span><span style="border:none;width:100px;padding-left:50px;text-align:right;" class="icon-minus-sign">'.number_format($datas[$i]['credit'],2).'</span><span style="border:none;width:100px;padding-left:50px;text-align:right;">'.number_format($datas[$i]['debit'],2).'</span></label>';
				}
				//$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '" ><label><span style="width:300px;">';
				//$tree .= $datas[$i]['name'].'</span><span style="width:300px;padding-left:' . $padding . 'px;">'.$datas[$i]['credit'].'</label>';
				$padding += 10;
				$tree .= $this->generateTrialBalance($datas, $datas[$i]['id'], $depth+1, $padding);
				$padding -= 10;
				$tree .= '</li>';
				//echo '<br/> ' . $tree;
			}
			
		}
		
		$tree .= '</ul>';
		return $tree;
	}
	
	public function generateIncomeStatement($datas, $parent = 0, $depth=0, $padding = 10)
	{
		//echo "generatetree";
		//echo "<br>";
		//var_dump($datas);
		//echo "<br><br>";
		//print_r($datas);
		//echo "count".count($datas);
		//die();
		$amount = array();
		if($depth > 1000) return ''; // Make sure not to have an endless recursion
		
		$tree = '<ul>';
		//$padding = 20;
		for($i = 0, $ni = count($datas); $i < $ni; $i++){
			//print_r($datas[$i]);
			//echo "parent new:".$datas[$i]['parent_id']."<br>";
			
			//echo "old".$parent;
			if($datas[$i]['parent_id'] == $parent && ($datas[$i]['credit'] > 0 OR  $datas[$i]['debit'] >0)){
				//echo "test";
				//echo $datas[$i]['name'];
				//echo "parent<br>";
				//$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '" ><label ><span style="width:300px;">';
				if($parent == 0 OR $parent == 1)
				{
					$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '" ><label><span style="border:none;width:200px;">';	
					$tree .= $datas[$i]['name'].'</span><span style="border:none;width:50px;padding-left:50px;text-align:right;">'.number_format($datas[$i]['credit'],2).'</span><span style="border:none;width:50px;padding-left:50px;text-align:right;">'.number_format($datas[$i]['debit'],2).'</span></label>';
				}
				else
				{
					$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '"  style="display:none;"><label ><span style=" border:none;width:200px;" class="icon-minus-sign">';	
					$tree .= $datas[$i]['name'].'</span><span style="border:none;width:50px;padding-left:50px;text-align:right;" class="icon-minus-sign">'.number_format($datas[$i]['credit'],2).'</span><span style="border:none;width:50px;padding-left:50px;text-align:right;">'.number_format($datas[$i]['debit'],2).'</span></label>';
				}
				//$tree .= '<li id="li_' . $datas[$i]['id'] .'_'.$depth.  '" ><label><span style="width:300px;">';
				//$tree .= $datas[$i]['name'].'</span><span style="width:300px;padding-left:' . $padding . 'px;">'.$datas[$i]['credit'].'</label>';
				$padding += 10;
				$tree .= $this->generateIncomeStatement($datas, $datas[$i]['id'], $depth+1, $padding);
				$padding -= 10;
				$tree .= '</li>';
				//echo '<br/> ' . $tree;
			}
			
		}
		
		$tree .= '</ul>';
		return $tree;
	}
	
	public function generatePageTree($datas, $parent = 0, $depth=0)
	{
		//var_dump($datas);
		//die();
		//echo "test";
		$amount = array();
		if($depth > 1000) return ''; // Make sure not to have an endless recursion
		
		$tree = '<tr>';
		
		for($i=0, $ni=count($datas); $i < $ni; $i++){
			if($datas[$i]['parent_id'] == $parent){
				//echo "parent";
				$tree .= '<td style="width:150px;padding-left:' . $padding . 'px;" id="li_' . $datas[$i]['id'] .  '">';
				$tree .= $datas[$i]['name'] . '<td style="width:50px;padding-left:' . $padding . 'px;">' . $datas[$i]['credit'];
				$padding += 10;
				$tree .= $this->generatePageTree($datas, $datas[$i]['id'], $depth+1);
				$padding -= 10;
				$tree .= '</td></td>';

				//echo '<br/> ' . $tree;
			}
		}
		$tree .= '</tr>';
		return $tree;
	}
	
	public function FormatArray($Data,$GroupID,$IncomeExpenseAccount=0)
	{
		//echo "maindata";
		//echo sizeof($Data);
		//print_r($Data);
		//echo "<br><br>";
		$type=$GroupID;
		$categories=array();	
		$max=1000;
		foreach($Data as $category) {
			$parent=false;
			if($category['parentcategory_id']==0)
			{
				$parent=true;	
			}
			$categories[] = array('id' => $category['category_id'], 'name' =>$category['category_name'], 'parent_id' => $category['parentcategory_id'], 'credit' =>0,'IsParent' => $parent,'debit' =>0,'GroupType' => $type);
			if($IncomeExpenseAccount <> 0)
			{
			$LedgerData=$this->LedgerArray($GroupID,$category['category_id'],$IncomeExpenseAccount);
			}
			else{$LedgerData=$this->LedgerArray($GroupID,$category['category_id']);}
			//echo "LedgerData";
			//echo sizeof($LedgerData);
			//print_r($LedgerData);
			foreach($LedgerData as $category2) 
			{
				$max++;
				$categories[] = array('id' => $max, 'name' =>$category2['name'], 'parent_id' => $category['category_id'], 'credit' => $category2['credit'],'debit' => $category2['debit'],'GroupType' => $type);
			}
		}
	  //print_r($categories);
	  //echo "<br><br>";
	  //echo "total".sizeof($categories);
	  //print_r($categories);
	  return $this->CalculateTotal($categories);
	}
	
	public function CalculateTotal($categories)
	{
		//echo "Calculate";
		//print($categories);
		
		for($iCnt = sizeof($categories)-1; $iCnt >=0 ; $iCnt--)
		{
			$parent = $categories[$iCnt]['parent_id'];
			//echo "iCnt".$iCnt;
			$categories = $this->setAmount($categories, $parent, $categories[$iCnt]['credit'],$categories[$iCnt]['debit'], $iCnt);
		}
		for($iCnt = sizeof($categories)-1; $iCnt >= 0 ;$iCnt--)
		{
			
			$parent = $categories[$iCnt]['parent_id'];
			if($parent==0 || $parent==1)
			{
				//print_r($categories[$iCnt]);
			//die();
				//echo "<br> old amount for credit:".$this->CreditTotal."old amount for debit:".$this->DebitTotal;
				//echo "<br> old amount for :".$this->CreditTotal;
				$this->CreditTotal +=$categories[$iCnt]['credit'];
				$this->DebitTotal +=$categories[$iCnt]['debit'];	
			}
		}
		
		return $categories;
	
	}
	
	public function getTotal($Data)
	{
		$total=0;
		foreach($Data as $key)
		{
			//echo "<br>";
			//print_r($key);
			//echo $key['parent_id'];
			//echo "<br>";
			if($key['parent_id']==0 or $key['parent_id']== 1)
			{
			
					$total +=abs($key['credit'] - $key['debit']);
					
			}
			/*
			else if($key['parent_id']==0 && $key['GroupType'] == 2)
			{
				$total +=abs($key['debit'] - $key['credit']);
			}*/	
			
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
		//if($result1[0]['Total'] > $result2[0]['Total']){ $OpeningBalance=$result1[0]['Total']- $result2[0]['Total'];}else{$OpeningBalance=$result2[0]['Total']-$result1[0]['Total'];}
		
		//$OpeningBalance=$result2[0]['Total'] - $result1[0]['Total'];
		//echo "Lcredit".$result1[0]['Credit'].":"."Ldeb".$result1[0]['Debit'];
		$LiabilityTotal = abs($result1[0]['Credit'] - $result1[0]['Debit']);
		//echo "assetcrdt".$result2[0]['Credit'].":"."assetdbet".$result2[0]['Debit'];
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
		//$newArray[] = array();
		for($i=0;$i < sizeof($Data);$i++)
		{
			if($GroupID==1){ $amount=$Data[$i]['credit'] - $Data[$i]['debit'];}
			else{$amount=$Data[$i]['debit'] - $Data[$i]['credit'];}
			//$amount=$Data[$i]['credit'] - $Data[$i]['debit'];
			
			if(($Data[$i]['parent_id']==0 || $Data[$i]['parent_id']==1) && $amount <0)
			{
				//$Data[$i]['credit'] =0;
				//$Data[$i]['debit'] =0;
				
				$newArray[] = array();
				array_push($newArray,$Data[$i]);
				$newArray[1]['credit'] =0;
				$newArray[1]['debit'] =0;
				array_push($DataShiftarray,$newArray[1]);
				//print_r($Data[$i]);				//array_push($this->unsetArray,$i);
				$DataShiftarray=$this->FindAllChild($Data[$i]['id'],$Data,$this->unsetArray,$GroupID,$DataShiftarray,1);
			}

		}
		
		return $DataShiftarray;
	}
	
	public function FindAllChild($id ,&$Data,$unsetArray,$GroupID,&$DataShiftarray,$show=0)
	{
		
		for($i=0;$i < sizeof($Data)-1;$i++)
		{
			if($Data[$i]['parent_id']==$id)
			{
				if($Data[$i]['GroupType']==1){ $amount=$Data[$i]['credit'] - $Data[$i]['debit'];}
				else{$amount=$Data[$i]['debit'] - $Data[$i]['credit'];}
				
				if($amount <0 && $show==1)
				{
					//$Data[$id]['credit'] -=
					foreach($Data as $key =>$v)
					{
						
						if($Data[$key]['id'] == $id)
						{
							//print_r($DataShiftarray[$key]);
						//echo "<br>";
					$Data[$key]['credit'] -= $Data[$i]['credit'];
					$Data[$key]['debit'] -= $Data[$i]['debit'];
					
						}
					}
					
					
					foreach($DataShiftarray as $key =>$v)
					{
						
						if($DataShiftarray[$key]['id'] == $id)
						{
							//print_r($DataShiftarray[$key]);
						//echo "<br>";
					$DataShiftarray[$key]['credit'] += $Data[$i]['credit'];
					$DataShiftarray[$key]['debit'] += $Data[$i]['debit'];
					
						}
					}
					
				
				}
				
				if($amount < 0)
				{
					array_push($DataShiftarray,$Data[$i]);
					array_push($this->unsetArray,$i);
					$DataShiftarray=$this->FindAllChild($Data[$i]['id'],$Data,$unsetArray,$GroupID,$DataShiftarray,0);}
				}
		
		}
	return $DataShiftarray;	
	}
	
	public function UnsetArray(&$Data)
	{
		//print_r($this->unsetArray );
		foreach($this->unsetArray as $key=>$value)
		{
			//echo "<br>".$value;
			unset($Data[$value]);	
		}
		return $Data;	
	}
	
	
	
}
?>