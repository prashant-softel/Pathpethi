<?php 	include_once("../classes/neft.class.php");
		include_once("../classes/include/dbop.class.php");
		include_once("../classes/dbconst.class.php");
		include_once("../classes/ChequeDetails.class.php");
		include_once("../classes/include/fetch_data.php");
		
		$dbConn = new dbop();
		$obj_neft = new neft($dbConn);
		$obj_ChequeDetails = new ChequeDetails($dbConn);
		$objFetchData = new FetchData($dbConn);
		$obj_neft->actionPage .= '?'.$_REQUEST['url'];		
		
		$validator = '';
		
		
		if(isset($_REQUEST['mode']))
		{
		
			if($_REQUEST['mode'] == 'Submit')
			{
				if($_REQUEST['society_id'] <> '' && $_REQUEST['PaidTo'] <> '0' && $_REQUEST['BankName'] <> '' && $_REQUEST['Amount'] <> '' && $_REQUEST['TransationNo'] <> '' && $_REQUEST['Date'] <> '' && $_REQUEST['tra_type'] <> '')
				{
					//$validator = $obj_neft->InsertData($_REQUEST['society_id'], $_REQUEST['PaidBy'], $_REQUEST['PaidTo'], $_REQUEST['BankName'], $_REQUEST['BranchName'], $_REQUEST['Amount'], $_REQUEST['Date'], $_REQUEST['AcNumber'], $_REQUEST['TransationNo'], $_REQUEST['Comments']);
					$NEFTDate = $_REQUEST['Date'];
					$sVoucherDate = $NEFTDate;
					if($_REQUEST['tra_type'] == 2)
					{
						$ID = 0;
						$sql = "SELECT `ID` FROM `period` WHERE '".getDBFormatDate($NEFTDate)."' BETWEEN `BeginingDate` AND `EndingDate` ";
						$period = $obj_ChequeDetails->m_dbConn->select($sql);
						if($period[0]['ID'] <> "")
						{							
							$selectQuery = 'SELECT `id` FROM `depositgroup` WHERE `periodID` = "'.$period[0]['ID'].'" AND `bankid` = "'.$_REQUEST['PaidTo'].'"';
							$pID = $obj_ChequeDetails->m_dbConn->select($selectQuery);
							if(sizeof($pID) == 0)
							{
								$BillFor = $objFetchData->GetBillFor($period[0]['ID']);
								$BillFor_Bill = $obj_ChequeDetails->m_objUtility->displayFormatBillFor($BillFor);
								$insert_query="insert into depositgroup (`bankid`,`createby`,`status`,`desc`, `periodID`) values ('".$_REQUEST['PaidTo']."','".$_SESSION['login_id']."','0','MembersSlip : ".$BillFor_Bill."','".$period[0]['ID']."')";
								$ID = $obj_ChequeDetails->m_dbConn->insert($insert_query);								
							}
							else
							{
								$ID = $pID[0]['id'];
							}
						}
						else
						{												
							$now = new DateTime($sVoucherDate);
   							$month = $now->format('M');
  							$year = $now->format('Y');
							$selectQuery = 'SELECT `id` FROM `depositgroup` WHERE `desc` = "MembersSlip : '.$month.' - '.$year.'" AND `bankid` = "'.$_REQUEST['PaidTo'].'"';
							$pID = $obj_ChequeDetails->m_dbConn->select($selectQuery);
							if(sizeof($pID) == 0)
							{
								$insert_query="insert into depositgroup (`bankid`,`createby`,`status`,`desc`) values ('".$_REQUEST['PaidTo']."','".$_SESSION['login_id']."','0','MembersSlip : ".$month." - ".$year."')";
								$ID = $obj_ChequeDetails->m_dbConn->insert($insert_query);
							}
							else
							{
								$ID = $pID[0]['id'];
							}
						}
																								
						$validator = $obj_ChequeDetails->AddNewValues($sVoucherDate, $NEFTDate, $_REQUEST['TransationNo'], $_REQUEST['Amount'], $_REQUEST['PaidBy'], $_REQUEST['PaidTo'], $_REQUEST['BankName'], $_REQUEST['BranchName'], $ID, $_REQUEST['Comments'], $_REQUEST['BillType'],0,0,0,0,1);
					}
					else
					{
						$validator = $obj_ChequeDetails->AddNewValues($sVoucherDate, $NEFTDate, $_REQUEST['TransationNo'], $_REQUEST['Amount'], $_REQUEST['PaidBy'], $_REQUEST['PaidTo'], $_REQUEST['BankName'], $_REQUEST['BranchName'], -2, $_REQUEST['Comments'],$_REQUEST['BillType'],0,0,0,0,1);
					}
					
					/*if($validator > 0)
					{
						$validator = "Insert";
					}*/
				}
				else
				{
					$validator = 'All * Field Required';
				}
			}
			else if($_REQUEST['mode'] == 'Update')
			{
				if($_REQUEST['id'] <> '' && $_REQUEST['PaidBy'] <> '0' && $_REQUEST['PaidTo'] <> '0' && $_REQUEST['BankName'] <> '' && $_REQUEST['Amount'] <> '' && $_REQUEST['AcNumber'] <> '' && $_REQUEST['TransationNo'] <> '' && $_REQUEST['Date'] <> '')
				{
					$validator = $obj_neft->UpdateData($_REQUEST['id'], $_REQUEST['PaidBy'], $_REQUEST['PaidTo'], $_REQUEST['BankName'], $_REQUEST['BranchName'], $_REQUEST['Amount'], $_REQUEST['Date'], $_REQUEST['AcNumber'], $_REQUEST['TransationNo'], $_REQUEST['Comments']);
					
					if($validator > 0)
					{
						$validator = "Update";
					}
				}
				else
				{
					$validator = 'All * Field Required';
				}
			}
			else if($_REQUEST['mode'] == 'Approve')
			{
				if($_REQUEST['id'] <> '' && $_REQUEST['PaidBy'] <> '0' && $_REQUEST['PaidTo'] <> '0' && $_REQUEST['BankName'] <> '' && $_REQUEST['Amount'] <> '' && $_REQUEST['AcNumber'] <> '' && $_REQUEST['TransationNo'] <> '' && $_REQUEST['Date'] <> '')
				{
					$validator = $obj_neft->UpdateData($_REQUEST['id'], $_REQUEST['PaidBy'], $_REQUEST['PaidTo'], $_REQUEST['BankName'], $_REQUEST['BranchName'], $_REQUEST['Amount'], $_REQUEST['Date'], $_REQUEST['AcNumber'], $_REQUEST['TransationNo'], $_REQUEST['Comments']);
					
					$validator = $obj_neft->ApproveTransaction($_REQUEST['id'], $_REQUEST['PaidBy'], $_REQUEST['PaidTo'], $_REQUEST['Date'], $_REQUEST['Amount']);
				}
				else
				{
					$validator = 'All * Fields Required';
				}
			}
		}
		else if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'edit')
		{
			$result = $obj_neft->selecting($_REQUEST['neftId']);
			echo json_encode($result);
		}
		else if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'GetAccountDetails')
		{
			$result = $obj_neft->GetBankDetails($_REQUEST['BankID']);
			echo json_encode($result);
		}
?>

<?php
	if(isset($_REQUEST['mode']) && ($_REQUEST['mode'] == 'Submit' || $_REQUEST['mode'] == 'Update' || $_REQUEST['mode'] == 'Approve'))
	{
		?>
	<html>
	<body>
	<font color="#FF0000" size="+2">Please Wait...</font>
	
	<form name="Goback" method="post" action="<?php echo $obj_neft->actionPage; ?>">
		<?php			
		if($validator=="Insert")
		{
			$ShowData = "Record Added Successfully";
		}
		else if($validator=="Update")
		{
			$ShowData = "Record Updated Successfully";
		}
		else if($validator=="Delete")
		{
			$ShowData = "Record Deleted Successfully";
		}
		else if($validator=="Approve")
		{
			$ShowData = "Transaction Approved Successfully";
		}
		else
		{
			foreach($_POST as $key=>$value)
			{
				echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
			}
			$ShowData = $validator;
		}		
		?>
	
	<input type="hidden" name="ShowData" value="<?php echo $ShowData; ?>">
	</form>
	
	<script>
		document.Goback.submit();
	</script>
	
	</body>
	</html>
    <?php
	}
?>

<?php 
	if($_REQUEST['method'] == 'validateDate')
	{
		
		include_once("../classes/include/dbop.class.php");
		include_once("../classes/dbconst.class.php");
		$dbConn = new dbop();
		$sMsg = "";
		
		$sql = "SELECT * FROM `year` where '".getDBFormatDate($_REQUEST['transaction_date'])."' BETWEEN `BeginingDate` AND `EndingDate` ";
		$result = $dbConn->select($sql);
		
		if(sizeof($result) > 0)
		{
			if($result[0]['is_year_freeze'] == 1)
			{
				$sMsg = "Entered date <".$_REQUEST['transaction_date']."> is not allowed, please contact committee members. ";		
			}
			else
			{
				$sMsg = "Success";
			}	
		}
		else
		{
				$sMsg = "Entered date <".$_REQUEST['transaction_date']."> is not allowed, please contact committee members. ";	
		}
		
		echo $sMsg;
		
	}
?>