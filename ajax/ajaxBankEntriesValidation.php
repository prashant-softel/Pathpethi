<?php 	include_once("../classes/BankEntriesValidation.class.php");
		include_once("../classes/include/dbop.class.php");
		$dbConn = new dbop();
		$obj_BankEntriesValidation = new BankEntriesValidation($dbConn);
				
		if(isset($_POST["method"]) && $_POST["method"] == "run" && $_POST["cleanInvalidEntries"] == "YES")
		{
			$res = $obj_BankEntriesValidation->getdbBackup();
			if($res == "success")
			{
				$obj_BankEntriesValidation->FetchRecord();
				echo "success";
			}
			else
			{
				echo "failed";
			}
		}
?>