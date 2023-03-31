<?php 	include_once("../classes/RegistersValidation.class.php");
		include_once("../classes/include/dbop.class.php");
			
		$dbConn = new dbop();
		
		$obj_register = new RegistersValidation($dbConn);
		if(isset($_POST["method"]) && $_POST["method"] == "run" && $_POST["cleanInvalidEntries"] == "YES")
		{
			$res = $obj_register->getdbBackup();
			if($res == "success")
			{
				$obj_register->ValidateRegisterEntries('liabilityregister');
				$obj_register->ValidateRegisterEntries('assetregister');
				$obj_register->ValidateRegisterEntries('incomeregister');
				$obj_register->ValidateRegisterEntries('expenseregister');
				echo "success";
			}
			else
			{
				echo "failed";
			}
		}
?>