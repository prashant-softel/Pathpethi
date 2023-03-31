<?php if(!isset($_SESSION)){ session_start(); } ?>
<?php		
	//include_once("check_session.php");
	include_once("classes/include/dbop.class.php");
	include_once("classes/utility.class.php");	
	include_once("classes/dbconst.class.php");	
	
	class LedgerValidate
	{
		public $m_dbConn;
		private $obj_utility;
		private $CurrentYearBeginingDate;
		function __construct($dbConn)
		{
			$this->m_dbConn = $dbConn;
			$this->obj_utility = new utility($this->m_dbConn);
			//$this->CurrentYearBeginingDate = $this->obj_utility->getCurrentYearBeginingDate($_SESSION['default_year']);
			$sqlFetch="SELECT `society_creation_yearid` FROM `society` where society_id = '".$_SESSION['society_id']."'";
			$res = $this->m_dbConn->select($sqlFetch);
			
			$this->CurrentYearBeginingDate= $res[0]['society_creation_yearid'];
		}					
		
		function getSocietyName()
		{
			$sql = "SELECT `society_name` FROM `society` WHERE `society_id` = '".$_SESSION['society_id']."'";			
			$result = $this->m_dbConn->select($sql);
			return $result[0]['society_name'];
		}
		
		function FetchLedgers()
		{
			$OpeningType;
			$sqlFetchLedger = "select * from `ledger` where `society_id` = '".$_SESSION['society_id']."' ";
			$data = $this->m_dbConn->select($sqlFetchLedger);
						
			foreach($data as $key => $value)
			{				
				$aryParent = $this->obj_utility->getParentOfCategory($data[$key]['categoryid']);
				$link = "ledger.php?edt=".$data[$key]['id'];				                        
				$Url =	"<a href='' onClick=\"window.open('". $link ."','popup','type=fullWindow,fullscreen,scrollbars=yes');\"> <img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/> </a> <br />";				
				if($data[$key]['opening_balance'] < 0)
				{
					echo "<font color='#FF0000'>**ERROR**</font> Opening Balance of ".$data[$key]['ledger_name']." [".$data[$key]['opening_balance']."] should not be negative.";
					echo $Url;	
					echo "<hr />";
				}
				
				if($data[$key]['categoryid'] == "")
				{
					echo "<font color='#FF0000'>**ERROR**</font> Category ID of ledger should not be blank.";
					echo $Url;
					echo "<hr />";
				}
				
				if($aryParent['group'] == LIABILITY)
				{
					//ledger is liability 
					$sqlLiability = "select * from `liabilityregister` where `LedgerID` = '".$data[$key]['id']."' and `Is_Opening_Balance` = 1";
					$resLiability = $this->m_dbConn->select($sqlLiability);
					
					if($resLiability[0]['Credit'] < 0)
					{
						echo "<font color='#FF0000'>**ERROR**</font> Liability credit amount [".$resLiability[0]['Credit']."] of ".$data[$key]['ledger_name']." should not be negative.";
						echo $Url;	
						echo "<hr />";
					}
					if($resLiability[0]['Debit'] < 0)
					{
						echo "<font color='#FF0000'>**ERROR**</font> Liability Debit amount [".$resLiability[0]['Debit']."] of ".$data[$key]['ledger_name']." should not be negative.";	
						echo $Url;	
						echo "<hr />";
					}
					
					if($resLiability[0]['Date'] <> $this->obj_utility->GetDateByOffset($this->CurrentYearBeginingDate, -1))
					{
						echo "<font color='#FF0000'>**ERROR**</font> Liability [".$data[$key]['ledger_name']."] Opening Balance Date[".getDisplayFormatDate($resLiability[0]['Date'])."] does not match with Current Year Begining Date[".getDisplayFormatDate($this->CurrentYearBeginingDate)."]";								
					    echo $Url;	                  
						echo "<hr />";
					}
					
					if($data[$key]['opening_type'] == 1 && $resLiability[0]['Credit'] == 0 && $resLiability[0]['Debit'] > 0)
					{
						if($data[$key]['opening_balance'] != $resLiability[0]['Debit'])
						{
							echo "<font color='#FF0000'>**ERROR**</font> Opening Balance of ".$data[$key]['ledger_name']." [".$data[$key]['opening_balance']."] does not match with Opening Balance [" .$resLiability[0]['Debit']."] of Liability." ;
							echo $Url;								
							echo "<hr />";								
						}
					}
					else if($data[$key]['opening_type'] == 2 && $resLiability[0]['Debit'] == 0 && $resLiability[0]['Credit'] > 0)
					{
						if($data[$key]['opening_balance'] != $resLiability[0]['Credit'])
						{
							echo "<font color='#FF0000'>**ERROR**</font> Opening Balance of ".$data[$key]['ledger_name']." [".$data[$key]['opening_balance']."] does not match with Opening Balance [" .$resLiability[0]['Credit']."] of Liability.";
							echo $Url;								
							echo "<hr />";								
						}
					}
					else if($data[$key]['opening_type'] == 0)
					{						
						if($resLiability[0]['Credit'] > 0)
						{
							echo "<font color='#FF0000'>**ERROR**</font> Opening Type of Ledger ".$data[$key]['ledger_name']." is none. Ledger has Credit Amount [".$resLiability[0]['Credit']."] present in Liability.";
							echo $Url;		
							echo "<hr />";	
						}
						else if($resLiability[0]['Debit'] > 0)
						{
							echo "<font color='#FF0000'>**ERROR**</font> Opening Type of Ledger ".$data[$key]['ledger_name']." is none. Ledger has Debit Amount [".$resLiability[0]['Debit']."] present in Liability.";
							echo $Url;		
							echo "<hr />";	
						}
						else if( $resLiability[0]['Credit'] == 0 && $resLiability[0]['Debit'] == 0)
						{
							echo "<font color='#FF0000'>**ERROR**</font> Opening Type of Ledger ".$data[$key]['ledger_name']." is none. Ledger has zero Credit Amount present in Liability.";
							echo $Url;		
							echo "<hr />";
						}
					}
				}
				else if($aryParent['group'] == ASSET)
				{
					//ledger is asset
					if($data[$key]['categoryid'] == BANK_ACCOUNT || $data[$key]['categoryid'] == CASH_ACCOUNT)
					{
						//ledger is bank or cash
						$sqlBank = "select * from `bankregister` where `LedgerID` = '".$data[$key]['id']."' and `Is_Opening_Balance` = 1";
						$resBank = $this->m_dbConn->select($sqlBank);
						
						if($resBank[0]['ReceivedAmount'] < 0)
						{						
							echo "<font color='#FF0000'>**ERROR**</font> Bank Register  Received amount [".$resBank[0]['ReceivedAmount']."] of ".$data[$key]['ledger_name']." should not be negative.";
							echo $Url;		
							echo "<hr />";
						}
						else if($resBank[0]['PaidAmount'] < 0)
						{
							echo "<font color='#FF0000'>**ERROR**</font> Bank Register Paid amount [".$resBank[0]['PaidAmount']."] of ".$data[$key]['ledger_name']." should not be negative.";
							echo $Url;		
							echo "<hr />";
						}
						
						if($resBank[0]['Date'] <> $this->obj_utility->GetDateByOffset($this->CurrentYearBeginingDate, -1))
						{
							echo "<font color='#FF0000'>**ERROR**</font> Bank Register [".$data[$key]['ledger_name']."] Opening Balance Date[".getDisplayFormatDate($resBank[0]['Date'])."] does not match with Current Year Begining Date[".getDisplayFormatDate($this->CurrentYearBeginingDate)."].";	
							echo $Url;	
							echo "<hr />";
						}
						
						if($data[$key]['opening_type'] == 1 && $resBank[0]['ReceivedAmount'] == 0 && $resBank[0]['PaidAmount'] > 0)
						{
							if($data[$key]['opening_balance'] != $resBank[0]['PaidAmount'])
							{
								echo "<font color='#FF0000'>**ERROR**</font> Opening Balance of ".$data[$key]['ledger_name']." [".$data[$key]['opening_balance']."] does not match with Opening Balance [" .$resBank[0]['PaidAmount']."] of Bank Register.";
								echo $Url;
								echo "<hr />";	
							}
						}
						else if($data[$key]['opening_type'] == 2 && $resBank[0]['PaidAmount'] == 0 && $resBank[0]['ReceivedAmount'] > 0)
						{
							if($data[$key]['opening_balance'] != $resBank[0]['ReceivedAmount'])
							{
								echo "<font color='#FF0000'>**ERROR**</font> Opening Balance of ".$data[$key]['ledger_name']." [".$data[$key]['opening_balance']."] does not match with Opening Balance [" .$resBank[0]['ReceivedAmount']."] of Bank Register.";
								echo $Url;
								echo "<hr />";	
							}
						}
						else if($data[$key]['opening_type'] == 0)
						{
							if($resBank[0]['ReceivedAmount'] > 0)
							{
								echo "<font color='#FF0000'>**ERROR**</font> Opening Type of Ledger ".$data[$key]['ledger_name']." is none. Ledger has Received Amount present in Bank Register.";
								echo $Url;	
								echo "<hr />";	
							}
							else if($resBank[0]['PaidAmount'] > 0)
							{
								echo "<font color='#FF0000'>**ERROR**</font> Opening Type of Ledger ".$data[$key]['ledger_name']." is none. Ledger has Paid Amount present in Bank Register.";
								echo $Url;	
								echo "<hr />";	
							}
							else if($resBank[0]['ReceivedAmount'] == 0 && $resBank[0]['PaidAmount'] == 0)
							{
								echo "<font color='#FF0000'>**ERROR**</font> Opening Type of Ledger ".$data[$key]['ledger_name']." is none. Ledger has zero Received Amount Amount present in Bank Register.";
								echo $Url;	
								echo "<hr />";
							}							
						}
					}
					else
					{					
						$sqlAsset = "select * from `assetregister` where `LedgerID` = '".$data[$key]['id']."' and `Is_Opening_Balance` = 1";
						$resAsset = $this->m_dbConn->select($sqlAsset);											
						
						if($resAsset[0]['Credit'] < 0)
						{							
							echo "<font color='#FF0000'>**ERROR**</font> Asset credit amount [".$resAsset[0]['Credit']."] of ".$data[$key]['ledger_name']." should not be negative.";
							echo $Url;	
							echo "<hr />";
						}
						if( $resAsset[0]['Debit'] < 0)
						{
							echo "<font color='#FF0000'>**ERROR**</font> Asset Debit amount [".$resAsset[0]['Debit']."] of ".$data[$key]['ledger_name']." should not be negative.";
							echo $Url;	
							echo "<hr />";
						}
						
						if($resAsset[0]['Date'] <> $this->obj_utility->GetDateByOffset($this->CurrentYearBeginingDate, -1))
						{
							echo "<font color='#FF0000'>**ERROR**</font> Asset [".$data[$key]['ledger_name']."] Opening Balance Date[".getDisplayFormatDate($resAsset[0]['Date'])."] does not match with Current Year Begining Date [".getDisplayFormatDate($this->CurrentYearBeginingDate)."].";
							echo $Url;	
							echo "<hr />";
						}
						
						if($data[$key]['opening_type'] == 1 && $resAsset[0]['Credit'] == 0 && $resAsset[0]['Debit'] > 0)
						{
							if($data[$key]['opening_balance'] != $resAsset[0]['Debit'])
							{							
								echo "<font color='#FF0000'>**ERROR**</font> Opening Balance of ".$data[$key]['ledger_name']." [".$data[$key]['opening_balance']."] does not match with Opening Balance [" .$resAsset[0]['Debit']."] of Asset.";
								echo $Url;
								echo "<hr />";	
							}
						}
						else if($data[$key]['opening_type'] == 2 && $resAsset[0]['Debit'] == 0 && $resAsset[0]['Credit'] > 0)
						{
							if($data[$key]['opening_balance'] != $resAsset[0]['Credit'])
							{
								echo "<font color='#FF0000'>**ERROR**</font> Opening Balance of ".$data[$key]['ledger_name']." [".$data[$key]['opening_balance']."] does not match with Opening Balance [" .$resAsset[0]['Credit']."] of Asset.";
								echo $Url;
								echo "<hr />";	
							}
						}
						else if($data[$key]['opening_type'] == 0)
						{
							if($resAsset[0]['Credit'] > 0)
							{
								echo "<font color='#FF0000'>**ERROR**</font> Opening Type of Ledger ".$data[$key]['ledger_name']." is none. Ledger has Credit Amount [".$resAsset[0]['Credit']."] present in Asset.";
								echo $Url;
								echo "<hr />";	
							}
							else if($resAsset[0]['Debit'] > 0)
							{
								echo "<font color='#FF0000'>**ERROR**</font> Opening Type of Ledger ".$data[$key]['ledger_name']." is none. Ledger has Debit [".$resAsset[0]['Debit']."] Amount present in Asset.";
								echo $Url;	
								echo "<hr />";	
							}
							else if( $resAsset[0]['Credit'] == 0 && $resAsset[0]['Debit'] == 0)
							{
								echo "<font color='#FF0000'>**ERROR**</font> Opening Type of Ledger ".$data[$key]['ledger_name']." is none. Ledger has zero Debit Amount present in Asset.";
								echo $Url;	
								echo "<hr />";
							}						
						}
					}
				}															
			}										
		}
	}	
	$dbopConn = new dbop();
	$obj_ledger = new LedgerValidate($dbopConn);	
	$society_Name = $obj_ledger->getSocietyName();	
?>	
<script language="javascript">
	var currentdate = new Date();
	var hours = currentdate.getHours();
	hours = hours % 12;
  	hours = hours ? hours : 12; 
	var datetime = currentdate.getDate() + "/"+(currentdate.getMonth() + 1) + "/" + currentdate.getFullYear() + " " + hours + ":" 					
					+ ((currentdate.getMinutes() < 10)? ("0" + currentdate.getMinutes()): (currentdate.getMinutes())) + ':' + 
					((currentdate.getSeconds() < 10) ? ("0" + currentdate.getSeconds()) : (currentdate.getSeconds()));						    		
</script>
<center>
<div style="color:#0033CC;">Ledger Validation Report - <?php echo $society_Name; ?> [<script> document.write(datetime); </script>]</div>
</center>
		
<?php        
	$obj_ledger->FetchLedgers();		
	
?>