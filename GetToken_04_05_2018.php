<?php
	//echo "1";
	include_once("classes/include/dbop.class.php");
	//echo "1";
	include_once("classes/dbconst.class.php");
	//echo "1";
	include_once("classes/utility.class.php");
	//echo "1";
	include_once("classes/neft.class.php");
	//echo "1";
	
	$ClientKey = "188374677837";
	$bTrace = false;
	if($bTrace)
	{
		echo "<br>". $ClientKey;
	}
	$ClientID = base64_encode($ClientKey);
	$ClientID = $_REQUEST["ClientID"];
	//echo "<br>".$ClientID;
	//$ClientID = strtr(rtrim(base64_encode($ClientKey), '='), '+/', '-_');
	if($bTrace)
	{
		echo "<br>".$ClientID;
	}
	//echo "<br>".base64_decode(strtr($ClientID, '-_', '+/'));
	//echo "<br>".
	$UserClientKey = base64_decode($ClientID);
	$bError = false;
	//$bTrace = true;
	if($ClientKey != $UserClientKey)
	{
		$sError = "Client ID &lt;ClientID&gt; found";
		$bError = true;
	}
	$bMobileNoProvided = false;
	$bUnitNoProvided = false;
	$bSocietyIDProvided = 0;
	$SocID = "";
	$UniqueID = "";
	$dbConn = "";
	$bMobileNoProvided;
	$sMobileNo = "";
	$UnitID = "";
	
	if(!$bError)
	{
		if(isset($_REQUEST["Site_id"]) && $_REQUEST["Site_id"] != "")
		{
			$sSiteID = $_REQUEST["Site_id"];
			$bSocietyIDProvided = 1;
			$SocID = $sSiteID;
	 		$sql = "select dbname from dbname where society_id='".$SocID."'";
			//echo "sql:".$sql;
		 	$dbConnRoot = new dbop(true);
		 	$resDBName = $dbConnRoot->select($sql);
		 	//echo "soc:";
		 	//print_r($resDBName);
			$dbConn = new dbop(false, $resDBName[0]["dbname"]);
		}
		else
		{
			$sError = "Society ID &lt;site_id&gt; not provided";
			
		}
		if($SocID != "" && isset($_REQUEST["Unique_id"]) && $_REQUEST["Unique_id"] != "")
		{
			$UniqueID = $_REQUEST["Unique_id"];
			if($UniqueID == "")
			{
				$sError = "Unique_id &lt;Unique_id&gt; not provided";		
			}
			else if($UniqueID == "0")
			{
				$sError = "Invalid Unique_id &lt;Unique_id&gt; provided";		
			}
			else
			{
				$UniqueID = str_replace(' ', '', $UniqueID);
				if(strlen($UniqueID) == 10)
				{
					$_REQUEST["mob_no"] = $UniqueID;

				}
				else
				{
					$sqlUnit = "select unit_id from unit where `unit_no` = '".$UniqueID."'";
					$resUnits = $dbConn->select($sqlUnit);
					if($bTrace)
					{
						print_r($resUnits);
					}
					if(sizeof($resUnits) > 0);
					{
						$_REQUEST["unit_id"] = $resUnits[0]["unit_id"];
					}
				}
			}

			if(isset($_REQUEST["mob_no"]) && $_REQUEST["mob_no"] != "")
			{
				$sMobileNo = $_REQUEST["mob_no"];
				$bMobileNoProvided = true;
			}
			else
			{
				$sError = "Mobile Number &lt;mob_no&gt; not provided";
				
			}
			if($bTrace)
			{
				echo "flag:".$bMobileNoProvided;
				echo "mob:".$sMobileNo;
			}
			if(isset($_REQUEST["unit_id"]) && $_REQUEST["unit_id"] != "")
			{
				$UnitID = $_REQUEST["unit_id"];//its UnitID
				$bUnitNoProvided = true;
			}
			else
			{
				if(!$bMobileNoProvided)
				{
					$sError = "UniqueID &lt;Unique_id&gt; not provided";	
					
				}
			}
			if($bMobileNoProvided || $bUnitNoProvided)
			{
				$sError = "";
			}
			if(!$bError)
			{
				$bSuccess = false;
				$sStatus = "Failure";
				$sResponse = "";
				try
				{
					$objUtility = new utility($dbConn, $dbConnRoot);
					$randomNumber = $objUtility->generateRandomString(30);
					$arResponse = array();
					$arResponse["unique_id"] = $UnitID;
					$arResponse["Site_id"] = $SocID;
					$arResponse["Token"] = $randomNumber;

					if($bTrace)
					{
						echo "uniq:".$UniqueID;
					}
					$sqlUpdate = "update api_tokens set status=0 where `ClientID`='2' and `UniqueID`='".$UniqueID."'";
					$dbConn->insert($sqlUpdate);
					//echo "insert:";
					$iStatus = 0;
					$sqlInsert = "insert into api_tokens (`ClientID`,`UniqueID`,`Token`,`status`) values('2','".$UniqueID."','".$randomNumber."','1')";
					$iStatus = $dbConn->insert($sqlInsert);
					if($bTrace)
					{
						echo "status:".$iStatus;
					}
					if($iStatus > 0)
					{
						$bSuccess = true;
						$sStatus = "Success";
					}
				}
				catch(Exception $ex)
				{
					if($bTrace)
					{
						echo "msg".$ex;
					}
					$bSuccess = false;
					$sResponse = "Unepxected Error Occurred. Please try again Later.";
				}

				$arResponse["status"] = $sStatus;
				//echo "Success:".$bSuccess;
				if($bSuccess)
				{
					$sError = "";
				}
				$arResponse["responseCode"] = $sError;

			}
		}
		else
		{
			if($SocID != "")
			{ 
				$sError = "Unique_id ID &lt;Unique_id&gt; not provided";
			}
			else
			{
				$sError = "Society ID &lt;site_id&gt; not provided";
			}
		}

	}
	if($bMobileNoProvided || $bUnitNoProvided)
	{
		$sError = "";
	}
	if($bTrace)
	{
		echo "soc:".$SocID;
	 	echo "flag:".$bSocietyIDProvided;
		echo "unit:".$UnitID;
	}
	if($bError)
	{
		$arResponse = array();
		$arResponse["unique_id"] = $UniqueID;
		$arResponse["Site_id"] = "";
		$arResponse["status"] = "Failure";
		$arResponse["responseCode"] = $sError;
	}
	echo json_encode($arResponse);
?>