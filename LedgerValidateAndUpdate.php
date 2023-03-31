<?php session_start();?>
<?php //if(!isset($_SESSION)){ session_start(); } ?>
<?php
	
	error_reporting(0);	
	include_once("classes/include/dbop.class.php");
	include_once("classes/utility.class.php");
	$dbopConn = new dbop();
	//echo "society_id".$_SESSION['society_id'];
	class LedgerValidateAndUpdate
	{
		public $m_dbConn;
		private $obj_utility;
		function __construct($dbConn)
		{
			$this->m_dbConn = $dbConn;
			$this->obj_utility = new utility($this->m_dbConn);
		}
		
		function getSocietyName()
		{
			$sql = "SELECT `society_name` FROM `society` WHERE `society_id` = '".$_SESSION['society_id']."'";			
			$result = $this->m_dbConn->select($sql);
			return $result[0]['society_name'];
		}	
		
		function FetchLedgers($SocietyID)
		{
			$OpeningType;
			$sqlFetchLedger = "select * from `ledger` where `society_id` = '".$_SESSION['society_id']."' ";
	echo $sqlFetchLedger;			
$data = $this->m_dbConn->select($sqlFetchLedger);
			foreach($data as $key => $value)
			{
				
				$aryParent = $this->obj_utility->getParentOfCategory($data[$key]['categoryid']);
				echo "<br>ID: ".$data[$key]['id'].":: ledger_name: ".$data[$key]['ledger_name'].":: group_id:".$aryParent['group'] .":: type:".$data[$key]['opening_type'];	
				if($aryParent['group'] == LIABILITY)
				{
					//ledger is liability 
					echo "<br>".$sqlLiability = "select * from `liabilityregister` where `LedgerID` = '".$data[$key]['id']."' and `Is_Opening_Balance` = 1";
					$resLiability = $this->m_dbConn->select($sqlLiability);
					if($data[$key]['opening_type'] == 1 && $resLiability[0]['Credit'] == 0 && $resLiability[0]['Debit'] > 0)
					{
						echo "Invalide Ledger";
					}
					else if($data[$key]['opening_type'] == 2 && $resLiability[0]['Debit'] == 0 && $resLiability[0]['Credit'] > 0)
					{
						echo "Invalide Ledger";
					}
					else if($data[$key]['opening_type'] == 0)
					{
						if($resLiability[0]['Credit'] > 0)
						{
							$OpeningType = 1;	
						}
						else if($resLiability[0]['Debit'] > 0)
						{
							$OpeningType = 2;
						}
						else if($resLiability[0]['Credit'] == 0 && $resLiability[0]['Debit'] == 0)
						{
							$OpeningType = 1;	
						}
					}
				}
				else if($aryParent['group'] == ASSET)
				{
					//ledger is liability 
					echo "<br>".$sqlAsset = "select * from `assetregister` where `LedgerID` = '".$data[$key]['id']."' and `Is_Opening_Balance` = 1";
					$resAsset = $this->m_dbConn->select($sqlAsset);
					
					if($data[$key]['opening_type'] == 1 && $resAsset[0]['Credit'] == 0 && $resAsset[0]['Debit'] > 0)
					{
						echo "<br>Invalid Ledger";
					}
					else if($data[$key]['opening_type'] == 2 && $resAsset[0]['Debit'] == 0 && $resAsset[0]['Credit'] > 0)
					{
						echo "<br>Invalid Ledger";
					}
					else if($data[$key]['opening_type'] == 0)
					{
						if($resAsset[0]['Credit'] > 0)
						{
							$OpeningType = 1;	
						}
						else if($resAsset[0]['Debit'] > 0)
						{
							$OpeningType = 2;
						}
						else if($resAsset[0]['Credit'] == 0 && $resAsset[0]['Debit'] == 0)
						{
							$OpeningType = 2;	
						}	
					}
				}
				else if($data[$key]['categoryid'] == BANK_ACCOUNT || $data[$key]['categoryid'] == CASH_ACCOUNT)
				{
					//ledger is bank or cash
					echo "<br>".$sqlBank = "select * from `bankregister` where `LedgerID` = '".$data[$key]['id']."' and `Is_Opening_Balance` = 1";
					$resBank = $this->m_dbConn->select($sqlBank);
					if($data[$key]['opening_type'] == 1 && $resBank[0]['ReceivedAmount'] == 0 && $resBank[0]['PaidAmount'] > 0)
					{
						echo "<br>Invalid Ledger";
					}
					else if($data[$key]['opening_type'] == 2 && $resBank[0]['PaidAmount'] == 0 && $resBank[0]['ReceivedAmount'] > 0)
					{
						echo "<br>Invalid Ledger";
					}
					else if($data[$key]['opening_type'] == 0)
					{
						if($resBank[0]['ReceivedAmount'] > 0)
						{
							$OpeningType = 1;	
						}
						else if($resBank[0]['PaidAmount'] > 0)
						{
							$OpeningType = 2;
						}
						else if($resBank[0]['ReceivedAmount'] == 0 && $resBank[0]['PaidAmount'] == 0)
						{
							$OpeningType = 2;	
						}	
					}
				}
				
				if($OpeningType <> "")
				{
					$sqlUpdateLedger = "Update `ledger` set `opening_type` = '".$OpeningType."' where `id` = '".$data[$key]['id']."' and `ledger_name` = '".$data[$key]['ledger_name']."' and `society_id` = '".$_SESSION['society_id']."'";
					echo "<br>". $sqlUpdateLedger;
					$this->m_dbConn->update($sqlUpdateLedger);
					$OpeningType = '';
				}
			}
			//print_r($data);
			
				
		}
	}
	
	$obj_ledger = new LedgerValidateAndUpdate($dbopConn);
	$society_Name = $obj_ledger->getSocietyName();	
	
?>
<center>
<div style="color:#0033CC;">Ledger Validation And Update Report - <?php echo $society_Name . '['.$_SESSION['society_id'].']'; ?></div>
</center>

<?php
$obj_ledger->FetchLedgers($_SESSION['society_id']);
?>