<?php //if(!isset($_SESSION)){ session_start(); }
//echo "1";
include_once("include/dbop.class.php");
//echo "2";
include_once("include/display_table.class.php");
//echo "3";
include_once("ChequeDetails.class.php");

class PaymentGateway extends dbop
{
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_ChequeDetails;

	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = new dbop(true);
		$this->obj_ChequeDetails = new ChequeDetails($dbConn);
				
		//dbop::__construct();
	}
	
	public function InitiatePayment($GatewayID,$LoginID,$UnitID,$PaidTo, $Date,$Amount,$BillType,$TranxID,$Status,$payuMoneyId,$Mode,$Comments, $sTokenID = "")
	{
		$sql = "INSERT INTO `paymentgatewaytransactions`(`GatewayID`, `LoginID`, `UnitID`,`PaidTo`, `Date`, `Amount`, `BillType`, `TranxID`, `Status`,`unmappedstatus`, `payuMoneyId`,`bank_ref_num`,`amount_split`,`payment_id`, `Mode`, `Comments`,`token_id`) VALUES ('".$GatewayID."','".$LoginID."','".$UnitID."','".$PaidTo."','".$Date."','".$Amount."','".$BillType."','".$TranxID."','".$Status."','NA','".$payuMoneyId."','0','0','0','".$Mode."','".$Comments ."','".$sTokenID ."')";
		
		//echo $sql;
		$res = $this->m_dbConn->insert($sql);
		//echo $res;
		return $res;
	}
	public function GetTransactionDetails($TranxID, $UnitID = 0)
	{
		$UserUnitID = "";
		if($UnitID == "")
		{			
			$UserUnitID = $_SESSION["unit_id"];
		}
		else
		{
			$UserUnitID = $UnitID;
		}
		$sql = "select * from `paymentgatewaytransactions` where `TranxID`='".$TranxID."' and UnitID='".$UserUnitID."'";
		//echo $sql;
		$res = $this->m_dbConn->select($sql);
			//echo $res;
			
			return $res;
	}
	public function GetTransactionDetailsJSON($TranxID, $UnitID = 0, $bReco = false, $sStartDate = "", $sEndDate = "")
	{
		$UserUnitID = "";
		if($UnitID == "")
		{			
			$UserUnitID = $_SESSION["unit_id"];
		}
		else
		{
			$UserUnitID = $UnitID;
		}
		if($UnitID != 0)
		{
			$sql = "select * from `paymentgatewaytransactions` where ";
		}
		else
		{
			$sql = "select * from `paymentgatewaytransactions` where ";
		}
		if($bReco)
		{
			if($sStartDate != "" && $sEndDate != "")
			{
				$sql .= " `Date` between '". $sStartDate . "' and '".$sEndDate . "'";
			}	
		}
		else
		{
			if($UnitID != 0)
			{
				$sql .= " `TranxID`='".$TranxID."' and UnitID='".$UserUnitID."'";
			}
			else
			{
				$sql .= " `TranxID`='".$TranxID."'";
			}
		}
		$sql .= " and GatewayID = '2'";
		//echo $sql;
		$Response = $this->m_dbConn->select($sql);
			//echo $res;
		//echo sizeof($Response);
		$arFinalResponse = array(); 		
		$sStatusCode = "";
		$sTrnxID= "";
		$w2sPaymentID = "";
		$sTrnxAmount = "";
		$ResponseStatus = "";
		if(isset($Response))
		{
			//print_r($Response);
			for($iCnt = 0; $iCnt < sizeof($Response) ; $iCnt++) 
			{
				$arResponse = array();
				$ResponseStatus = $Response[$iCnt]["Status"];
				$PaidTo = $result[$iCnt]["id"];
				//echo "id:".$Response[0]["GatewayID"];
				if($Response[$iCnt]["GatewayID"] == "1")
				{
					$FailReason = $Response[$iCnt]["unmappedstatus"];
				}
				else if($Response[$iCnt]["GatewayID"] == "2")
				{
					//echo "3";
					$FailReason = $Response[$iCnt]["Comments"];
					if(strtolower($Response[$iCnt]["Status"]) == "success")
					{ 
						$sStatusCode  = "Y";
						$sTrnxID = $Response[$iCnt]["TranxID"];
						$w2sPaymentID = $Response[$iCnt]["id"];
						$sTrnxAmount = $Response[$iCnt]["Amount"];
					}
					else if(strtolower($Response[$iCnt]["Status"]) == "failure")
					{
						$sStatusCode  = "N";
					}

					$arResponse["tran_stat"] = $sStatusCode;
					$arResponse["paytm_tran_id"] = $sTrnxID;
					$arResponse["w2s_transact_id"] = $w2sPaymentID;
					$arResponse["amount"] = $sTrnxAmount;
					$arResponse["responseCode"] = "";
					$arResponse["status"] = "Success";
					array_push($arFinalResponse, $arResponse);
				}
			}

			
		}
		else
		{
		}
		if($bReco == false)
		{
			$arFinalResponse = $arFinalResponse[0];	
		}
		return $arFinalResponse;
	}
	public function CompletePayment($w2s_transact_id,$GatewayID,$LoginID,$UnitID,$PaidTo, $Date,$Amount,$BillType,$TranxID,$Status,$UnmapStatus,$payuMoneyId, $bank_ref_num, $amount_split, $PaymentID,$Mode, $Comments, $sTokenID)
	{
		$arRetVal = array();
		if(!$this->IsTranxIDAlreadyUsed($TranxID))
		{
			$sqlSelect = "select * from paymentgatewaytransactions where ID='".$w2s_transact_id."'";
			$bTrace = 0;
			if($bTrace == 1)
			{
				echo "sql:".$sqlSelect;
			}
			$resSelect = $this->m_dbConn->select($sqlSelect);
			if($bTrace == 1)
			{
				print_r($resSelect);
				echo "status:".$resSelect[0]["Status"];
			}
			if($resSelect[0]["Status"] == "0")
			{
				//$sql = "INSERT INTO `paymentgatewaytransactions`(`GatewayID`, `LoginID`, `UnitID`,`PaidTo`, `Date`, `Amount`, `BillType`, `TranxID`, `Status`,`unmappedstatus`, `payuMoneyId`,`bank_ref_num`,`amount_split`,`payment_id`, `Mode`, `Comments`) VALUES ('".$GatewayID."','".$LoginID."','".$UnitID."','".$PaidTo."','".$Date."','".$Amount."','".$BillType."','".$TranxID."','".$Status."','".$UnmapStatus."','".$payuMoneyId."','".$bank_ref_num."','".$amount_split."','".$PaymentID."','".$Mode."','".$Comments ."')";
				
				$sql = "update `paymentgatewaytransactions` set `GatewayID`='".$GatewayID."',`LoginID`='".$LoginID."',`UnitID`='".$UnitID."',`PaidTo`='".$PaidTo."',`Date`='".$Date."',`Amount`='".$Amount."',`BillType`='".$BillType."',`TranxID`='".$TranxID."',`Status`='".$Status."',`Mode`='".$Mode."',`Comments`='".$Comments ."',`token_id`='".$sTokenID."' where ID='".$w2s_transact_id."'";
				//echo $sql;
				$res=$this->m_dbConn->update($sql);

				if($res > 0)
				{
					//if($arResult["status"] == "1")
					//{
						//echo "date:".$Date . " date " . $Date . " txnid " . $txnid . " amount " .  $amount . " paidby " . $PaidBy . " paidto " . $PaidTo . " bankcode " .  $BankCode;
						//	echo "completed payment. Updating registers...";
						
						$ChqRes = $this->obj_ChequeDetails->AddNewValuesNew($Date, $Date, $TranxID, $Amount, $UnitID, $PaidTo, $BankCode, "-", DEPOSIT_ONLINE, $Comments, $BillType,0,0,0,0,1,false, $SocID, "4"/*,1Payment gateway flag*/);
						if($bTrace == 1)
						{
							print_r($ChqRes);
						}
						//	echo "done1";		 
						if($PGClientID == "1") // PayUMoney
						{
							//$w2surl = "http://localhost//w2s_aws_1/";
							$w2surl = "https://way2society.com/";
							$URL = "Location: ".$w2surl."/Response.php?TrnxID=".$txnid;
							//echo "url:".$URL;
							header($URL);
						}
						//	echo "done2";   
							
						
						$arRetVal["status"] = 1; //valid transaction
						$arRetVal["id"] = $res;

					// }
					// else
					// {
					// 	$sError = $arResult["Error"];
					// 	$bHasError = true;
					// }
				}
				else
				{
					$arRetVal["status"] = -1; //valid transaction
					$arRetVal["id"] = 0;
					$arRetVal["Error"] = "Error. way2Society Transaction ID &lt;".$w2s_transact_id."&gt failed";
				}
			}
			else
			{
				$arRetVal["status"] = -1; //valid transaction
				$arRetVal["id"] = 0;
				$arRetVal["Error"] = "way2Society Transaction ID &lt;".$w2s_transact_id."&gt already exist";
			}
		}
		else
		{
			$arRetVal["status"] = 0;//transaction id already used
			$arRetVal["id"] = 0;
			$arRetVal["Error"] = "Transaction id &lt;".$TranxID."&gt; already used";
		}
		return $arRetVal;
	}
	public function IsTranxIDAlreadyUsed($TranxID)
	{
		try
		{
		$sql = "select * from paymentgatewaytransactions where `TranxID`='".$TranxID."'";
		//echo $sql;
		$res=$this->m_dbConn->select($sql);
		//print_r($res);
		$sTransactionID = $res[0]["TranxID"];
		}
		catch(Exception $ex)
		{
			$GDriveFlag = 0;
			echo "Exception:".$ex->getMessage();
		}
		if($sTransactionID == "")
		{
			return false; 
		}
		else 
		{
			return true;
		}
	}
	public function combobox11($query,$id)
	{
	//$str.="<option value=''>Please Select</option>";
	$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($id==$v)
						{
							$sel = 'selected';	
						}
						else
						{
							$sel = '';
						}
						
						$str.="<OPTION VALUE=".$v.' '.$sel.">";
					}
					else
					{
						$str.=$v."</OPTION>";
					}
					$i++;
				}
			}
		}
			return $str;
	}
}
?>