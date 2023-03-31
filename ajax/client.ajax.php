<?php 	include_once("../classes/client.class.php");
		include_once("../classes/include/dbop.class.php");
		
		$dbConnRoot = new dbop(true);
		$obj_client = new client($dbConnRoot, $_REQUEST['client']);
		
		$validator = '';
		
		if(isset($_REQUEST['insert']))
		{
			if($_REQUEST['insert'] == 'Insert')
			{
				if($_REQUEST['client_name'] <> '' && $_REQUEST['mobile'] <> '')
				{
					echo $validator = $obj_client->InsertData();
					
					if($validator > 0)
					{
						$validator = "Insert";
					}
				}
				else
				{
					$validator = 'All * Field Required';
				}
			}
			else if($_REQUEST['insert'] == 'Update')
			{
				if($_REQUEST['client_name'] <> '' && $_REQUEST['mobile'] <> '')
				{
					$validator = $obj_client->UpdateData();
					
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
			else if($_REQUEST['insert'] == 'Approve')
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
			//$result = $obj_neft->selecting($_REQUEST['neftId']);
			//echo json_encode($result);
		}
		else if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'fetchsociety')
		{
			$result = $obj_client->getSocietyName();
			echo $result;
		}
		else if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'fetchlist')
		{
			$result = $obj_client->getSocietyList();
			echo $result;
		}
		else if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'fetchuserlist')
		{
			$result = $obj_client->getUserList();
			echo $result;
		}
		else if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'fetchlogindetails')
		{			
			$result = $obj_client->getLoginDetails();			
			echo $result;
		}
		else if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'fetchAssignedSocieties')
		{
			$result = $obj_client->getAssignedSocieties();
			echo $result;
		}		
		else if(isset($_REQUEST['getSocieties']))	
		{			
			$result = $obj_client->getSocieties();
		}
		else if(isset($_REQUEST['getUnits']))
		{
			$result = $obj_client->getUnits();	
		}
		else if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'adduser')
		{
			$result = $obj_client->addUser();
			echo $result;
		}
		
		if($_REQUEST["method"]=="edit")
		{
			echo $_REQUEST["method"]."@@@";
			$select_type = $obj_client->selecting();
			
			foreach($select_type as $k => $v)
			{
				foreach($v as $kk => $vv)
				{
					echo $vv."#";
				}
			}
		}
?>

<?php
	if(isset($_REQUEST['insert']) && ($_REQUEST['insert'] == 'Insert' || $_REQUEST['insert'] == 'Update' || $_REQUEST['insert'] == 'Approve'))
	{
		?>
	<html>
	<body>
	<font color="#FF0000" size="+2">Please Wait...</font>
	
	<form name="Goback" method="post" action="<?php echo $obj_client->actionPage; ?>">
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