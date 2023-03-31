<?php

include_once ("dbconst.class.php");
include_once("include/dbop.class.php");
include_once("notice.class.php");
include_once("utility.class.php");
include_once("doc.class.php");
include_once("tenant.class.php");

class CDocumentsUserView
{

	public $m_dbConn;
	public $m_bShowTrace;
	public $m_notice;
	public $m_objDoc;
	public $m_objUtility;
	public $m_objTenant;
	public $m_TreeMode;

	function __construct($dbConn, $TreeModeType)
	{		
		//echo "ctr";
		$this->m_dbConn = $dbConn;
		$this->m_bShowTrace = 0;
		$this->m_notice = new notice($dbConn);
		$this->m_objUtility = new utility($dbConn);
		$this->m_objDoc = new document($dbConn);
		$this->m_objTenant = new tenant($dbConn);
		$this->m_TreeMode = $TreeModeType;
	}
	function GetMiscDocuments($UnitID, &$DocsCount)
	{

		//echo "UnitID for misc docs:".$UnitID;
		if($UnitID != "ALL")
		{
			//$UnitID = $_SESSION["unit_id"];
		}
		$resUnit = $this->m_objUtility->GetUnitDesc($UnitID);
		$UnitNo = $resUnit[0]["unit_no"];
		//echo "<br>unitid:".$UnitID;
		if($UnitNo == '')
		{
			$UnitNo = "ALL";
		}
		if($UnitID == "ALL")
		{
			$UnitID = "0";
		}
		$resDocuments = $this->m_objDoc->fetchDocumentsNew(1, $UnitID);

		$arFolderName = $this->GetDocsForUnitAsDocumentType($resDocuments, $UnitNo, "Misc", "doc_id","doc_type_id", "Unit_Id","doc_version");
		//echo "<pre>";
		//print_r($arFolderName);
		//print_r($resDocuments);
		//echo "</pre>";
		$DocsCount = sizeof($resDocuments);
		return $arFolderName;
	}
	function GetLeaseDocuments( $UnitID, &$LeaseDocsCount)
	{
		//$UnitID = $_SESSION["unit_id"];
		
		$resTenants = $this->m_objTenant->getTenantDocumentsNew($UnitID);
		$UnitNo = $resUnit[0]["unit_no"];
		//echo "<br>unitid:".$UnitID;
		if($UnitNo == '')
		{
			$UnitNo = "ALL";
		}
		if($this->m_bShowTrace)
		{
			echo "tenant docs:";
			echo "<pre>";
			//print_r($arFolderName);
			print_r($resTenants);
			echo "</pre>";
		}
		//die();
		//$resDocuments = $this->m_objDoc->fetchDocuments();
		$arFolderName = $this->GetDocsForUnitAsDocumentType($resTenants, $UnitNo, "Lease", "tenant_id","doc_type_id", "unit_id","doc_version");
		//echo "<pre>";
		//print_r($arFolderName);
		//print_r($resDocuments);
		//echo "</pre>";
		$LeaseDocsCount = sizeof($resTenants);
		return $arFolderName;
	}
	 
	function UpdateFolderCount($arCollection, $bRootFolder = true)
	{
		//print_r($arCollection);
		//die();
		//echo "size:". sizeof($arCollection);
		$arTempCollection = array();
		$iCntr = 0;
		//die();

		if(sizeof($arCollection) > 0)
		{
			foreach($arCollection as $sKey => $arValue) 
			{

				//echo "<br> cnt key:".$sKey ." value:".$arValue;
				$iSubLimit = sizeof($arValue);
				if($bRootFolder)
				{
					$iSubLimit = $iSubLimit - 1;	
				}
				$sNewKey = $sKey . " (". $iSubLimit .")";
				//$sNewKey = $sKey;
				$iSubLimitNew = $iSubLimit;
				
				if(is_array($arValue))
				{
					//echo "size:". sizeof($arValue);
					//print_r($arValue);

					foreach($arValue as $sNewKey2 => $arNewValue) 
					{

						//echo "<br>Inside key:".$sNewKey2 ." value:".$arNewValue;
						if(!is_array($arNewValue))
						{
							$sNewKey = $sKey . " (". $iSubLimitNew.")";
							//$sNewKey = $sKey;
						}
						//$print_r($arV);
					}

					$arTempCollection[$sNewKey] = $this->UpdateFolderCount($arValue, false);

				}
				else
				{
					//echo "size 2:". sizeof($arValue);
					$arTempCollection[$sKey] = $arValue;
				}
			}
		}
		else
		{
			return $arTempCollection;
		}
		return $arTempCollection;
	}
	
	function array_combine_($keys, $values)
	{
	    $result = array();
	    foreach ($keys as $i => $k) {
	        $result[$k][] = $values[$i];
	    }
	    array_walk($result, create_function('&$v', '$v = (count($v) == 1)? array_pop($v): $v;'));
	    return    $result;
	}
	function CombineArray(&$arFirst, $arSecond)
	{
		if($this->m_bShowTrace)
		{
			echo "<br>start first";
			echo "<pre>";
			print_r($arFirst);
			echo "</pre>";

			echo "<br>start arSecond";
			echo "<pre>";
			print_r($arSecond);
			echo "</pre>";
		}
		if(sizeof($arFirst) > 0)
		{
			foreach($arFirst as $sKey => &$arValue) 
			{
				if($this->m_bShowTrace)
				{

				echo "<br>key:".$sKey ." value:".$arValue;
				}
				$iSubLimit = sizeof($arValue);
				//$sNewKey = $sKey . " (". $iSubLimit .")";
				$sNewKey = $sKey;
				//$iSubLimitNew = $iSubLimit - 1;
				if(array_key_exists($sKey, $arSecond))
				{
					$arSecondInnerNode = $arSecond[$sKey];
						
					foreach($arValue as $sFirstSubKey => &$arFirstSubValue) 
					{
						//echo "<br>found";
						$arNewNode = array();
						$arMissedNodes =array();
						$bNodeFound = false;
						if(array_key_exists($sFirstSubKey, $arSecondInnerNode))
						{
							if($this->m_bShowTrace)
							{
								echo "key found:".$sFirstSubKey;
							}
						}
						else
						{
							if($this->m_bShowTrace)
							{
								echo "failed. key not found:".$sFirstSubKey;
								//echo "trace::".$sSubKey;
								echo "<pre>";
								print_r($arFirstSubValue);
								echo "</pre>";
							}
							//$arMissedNodes[$sFirstSubKey]= $arFirstSubValue;
						}

						/*foreach ($arSecondInnerNode as $sSubKey => $arSubValue) 
						{
							echo "trace::".$sSubKey;
							echo "<pre>";
							print_r($arSubValue);
							echo "</pre>";
							$bFound = false;
							$arNewSubChildNode = array();
							$arNewSubChildNode = $arFirstSubValue;
							//unset($arFirstSubValue[$sSubKey]);
 							echo "<br>comparing key:".$sSubKey . " with: " . $sFirstSubKey;		
							if($sSubKey == $sFirstSubKey)
							{
								$bFound = true;
								$bNodeFound = true;
						
								foreach ($arSubValue as $sSubChildKey => $arSubChildValue) 
								{
									//echo "<br>sub id:".$sSubChildKey . " value ". $arSubChildValue;
									array_push($arFirstSubValue, $arSubChildValue);
									
								}
								
								//echo "<pre>";
								//print_r($arNewSubChildNode);
								//echo "</pre>";
								//$arNewNode[$sSubKey]=$arNewSubChildNode;
								$arMissedNodes[$sSubKey]= $arNewSubChildNode;
							}
							else
							{
								echo "<br>node not found :".$sSubKey;
								//$arNewNode[$sSubKey]=$arNewSubChildNode;
								//$arFirst[$sSubKey] = $arSubValue;
								$arMissedNodes[$sSubKey]=$arSubValue;
							}
							//$arFirstSubValue[$sFirstSubKey] = $arNewSubChildNode;	
						}
						echo "found:".$bFound;	
						if(!$bNodeFound)
						{
							echo "<br>node not found,adding into main:".$sSubKey;
							//$arNewNode[$sSubKey] = $arFirstSubValue;
							//$arFirst[$sKey] = array($sSubKey => $arSubValue);
							//$arFirst[$sKey] = $arFirstSubValue[$sSubKey];
							//echo "new first:";
							echo "<pre>";
							print_r($arMissedNodes);
							echo "</pre>";
							$arFirst[$sKey] = $arMissedNodes;
						}
						if($bFound)
						{
						}
						else
						{
						
						}*/
						
					}
					//echo "<br>trace node:";
					//echo "<pre>";
					//print_r($arFirstSubValue);
					//echo "</pre>";
				}
				else
				{
					//echo "not found";
					
				}
			}
			//$this->CombineArray($arSecond, $arFirst);

			
		}
		if($this->m_bShowTrace)
		{
			echo "final first:";
			echo "<pre>";
			print_r($arFirst);
			echo "</pre>";
		}
		return $arFirst;
	}

	function GetUnitDescriptionsFromNotices($UnitID)
	{
		$res = $this->m_notice->GetUnitDescriptionsFromNotices();
		//print_r($res);
		$arUnitDesc = array();
		//echo "cnt:";print_r(sizeof($res));
		$arUnitToNotices =  array();
		/*$resultNotices = $this->m_notice->FetchAllNoticesEx(0);
		foreach ($resultNotices as $sNoticeID => $arNotice) 
		{
			//print_r($arNotice);

		}*/
		//echo "size:".sizeof($result);

		//echo "unitid:".$UnitID;
		$arNotices = $this->GetNotices($UnitID,$NoticesCount);
		$arMiscDocuments = $this->GetMiscDocuments($UnitID,$DocsCount);
		$arLeaseDocuments = $this->GetLeaseDocuments($UnitID, $LeaseCount);
		if($this->m_bShowTrace)
		{
			echo "<br>all misc docs:";
			echo "<pre>";
			print_r($arMiscDocuments);
			echo "</pre>";
		}
		$arUnitNos = $this->m_dbConn->select("select distinct(unit_no) from unit");
		if($this->m_bShowTrace)
		{
			echo "<br>all units:";
			echo "<pre>";
			print_r($arUnitNos);
			echo "</pre>";
		}
		if($this->m_bShowTrace)
		{
			echo "<br>all notices:";
			echo "<pre>";
			print_r($arNotices);
			echo "</pre>";
		}

		
		$arUnits = array();
		if($_REQUEST["Mode"] == "1")
		{
			$arUnitIDs = $this->m_dbConn->select("select unit_no from unit where unit_id='".$UnitID."'");
			$ReqUnitNo = "";
			if($UnitID != "0" )
			{
				$ReqUnitNo = $arUnitIDs[0]["unit_no"];
					
			}
			foreach ($arUnitNos as $key => $value) 
			{
				if($UnitID == $ReqUnitNo || $ReqUnitNo == "")
				{
					$arUnits[$value["unit_no"]] = array('Notices' =>  "");
				}
			}
		}
		else
		{
			$arDocTypes = $this->m_dbConn->select("select doc_type from document_type");
			$arUnitTemp = array();
			foreach ($arDocTypes as $sDockey => $sDocvalue) 
			{
				//echo "val".$sDocvalue[0][];
				//print_r($sDocvalue);
				$arUnits[$sDocvalue["doc_type"]] = array();
			}
			//$arUnitIDs = $this->m_dbConn->select("select unit_no from unit where unit_id='".$UnitID."'");
			$ReqUnitNo = "";
			if($UnitID != "0" )
			{
				$ReqUnitNo = $arUnitIDs[0]["unit_no"];
					
			}
			foreach ($arUnits as $sUnitKey => $sUnitValue) 
			{
				//echo "<br>key:".$sUnitKey;
				foreach ($arUnitNos as $key => $value) 
				{
					//echo "<br>value:".$value["unit_no"];
					if($UnitID == $ReqUnitNo || $ReqUnitNo == "")
					{
						//echo "<br>key:".$sUnitKey;
				
						//echo "unitkey:".print_r($sUnitValue);
						$arExistingValues = $arUnits[$sUnitKey];
						if(sizeof($arExistingValues) > 0)
						{
							//print_r($arExistingValues);
							//array_push($arExistingValues, $value["unit_no"]);
							$arExistingValues[$value["unit_no"]] = "";	
						}
						else
						{
							$arExistingValues[$value["unit_no"]] = "";
						}

						$arUnits[$sUnitKey] = $arExistingValues;
					}
				}
			}
		}
		
		if($this->m_bShowTrace)
		{
			echo "<br>all units array:";
			echo "<pre>";
			print_r($arUnits);
			echo "</pre>";
		}
		$arNotices = array_replace_recursive($arUnits, $arNotices);
		
		if($this->m_bShowTrace)
		{
			echo "<br>all notices:";
			echo "<pre>";
			print_r($arNotices);
			echo "</pre>";

			echo "<br>all misc docs:";
			echo "<pre>";
			print_r($arMiscDocuments);
			echo "</pre>";
		}
		if(sizeof($arMiscDocuments) > 0)
		{
			$arJoined = array_replace_recursive($arNotices, $arMiscDocuments);
		}
		else
		{
			$arJoined = $arNotices;
		}
		if($this->m_bShowTrace)
		{
			echo "<br>ar joined:";
			echo "<pre>";
			print_r($arJoined);
			echo "</pre>";
			//$arCombine =  $this->CombineArray($arJoined, $arMiscDocuments); 
			
			echo "<br>new Lease:";
			echo "<pre>";
			print_r($arLeaseDocuments);
			echo "</pre>";
		}
		if($this->m_bShowTrace)
		{
		//$arCombine =  $this->CombineArray( $arCombine, $arMiscDocuments); 
		
			echo "lease docs:".sizeof($arLeaseDocuments);
			echo "<pre>";
			print_r($arLeaseDocuments);
			echo "</pre>";
		}
		if(sizeof($arLeaseDocuments) > 0)
		{
			if($this->m_bShowTrace)
			{
				echo "joining lease docs:";
			}
			$arCombineNew =  array_replace_recursive($arJoined, $arLeaseDocuments);
		
		}
		else
		{
			$arCombineNew = $arJoined;
		}
		if($_REQUEST["Mode"] == 1)
		{
			$arOnlyAllNodes = $arCombineNew["ALL"];

			if($this->m_bShowTrace)
			{
				echo "arOnlyAllNodes";
				echo "<pre>";
				print_r($arCombineNew);
				echo "</pre>";
			}

			if($this->m_bShowTrace)
			{
				echo "combined before";
				echo "<pre>";
				print_r($arCombineNew);
				echo "</pre>";
			}

			if(sizeof($arOnlyAllNodes) > 0)
			{
				if($this->m_bShowTrace)
				{
					echo "only all:";
					echo "<pre>";
					print_r($arOnlyAllNodes);
					echo "</pre>";
				}
				foreach ($arCombineNew as $Combinedkey => &$Combinedvalue) 
				{
					if($this->m_bShowTrace)
					{
						echo "jn before for ID::".$Combinedkey;
						echo "<pre>";
						print_r($Combinedvalue);
						echo "</pre>";
					}
					$Combinedvalue = array_merge_recursive($Combinedvalue, $arOnlyAllNodes);
					if($this->m_bShowTrace)
					{
						echo "jn after::";
						echo "<pre>";
						print_r($Combinedvalue);
						echo "</pre>";
					}

				}
				unset($arCombineNew["ALL"]);
			}
		}
		else
		{
			/*$arDocTypes = $this->m_dbConn->select("select doc_type from document_type");
			$arUnitTemp = array();
			foreach ($arDocTypes as $sDockey => $sDocvalue) 
			{
				$arOnlyAllNodes =$sDocvalue["ALL"];

				//if($this->m_bShowTrace)
				{
					echo "arOnlyAllNodes";
					echo "<pre>";
					print_r($arCombineNew);
					echo "</pre>";
				}

				if($this->m_bShowTrace)
				{
					echo "combined before";
					echo "<pre>";
					print_r($arCombineNew);
					echo "</pre>";
				}

				if(sizeof($arOnlyAllNodes) > 0)
				{
					if($this->m_bShowTrace)
					{
						echo "only all:";
						echo "<pre>";
						print_r($arOnlyAllNodes);
						echo "</pre>";
					}
					foreach ($arCombineNew as $Combinedkey => &$Combinedvalue) 
					{
						if($this->m_bShowTrace)
						{
							echo "jn before for ID::".$Combinedkey;
							echo "<pre>";
							print_r($Combinedvalue);
							echo "</pre>";
						}
						$Combinedvalue = array_merge_recursive($Combinedvalue, $arOnlyAllNodes);
						if($this->m_bShowTrace)
						{
							echo "jn after::";
							echo "<pre>";
							print_r($Combinedvalue);
							echo "</pre>";
						}

					}
					unset($arCombineNew["ALL"]);
				}
			}*/
		}
		if($this->m_bShowTrace)
		{
			echo "combined after";
			echo "<pre>";
			print_r($arCombineNew);
			echo "</pre>";
		}
		$arNewCombined = $this->UpdateFolderCount($arCombineNew);
		//ksort($arNewCombined);
		if($this->m_bShowTrace)
		{
			echo "final:";
			echo "<pre>";
			print_r($arNewCombined);
			echo "</pre>";
		}
		//echo "<pre>";
		//print_r($arMiscDocuments);
		//echo "</pre>";
		
		//die();
		//$arNewNotices = $this->UpdateFolderCount($arNotices);

		//$arNewMiscDocs = $this->UpdateFolderCount($arMiscDocuments);

		//$arNewLeaseDocs = $this->UpdateFolderCount($arLeaseDocuments);
		//echo "<pre>";
		//print_r($arNewMiscDocs);
		//echo "</pre>";
		/*echo "<pre>";
		print_r($arMiscDocuments);
		echo "</pre>";*/
		//die();
		//$allNotice = array( "Notices ( Overall : " . $NoticesCount.")"  => $arNewNotices);
		//$arMiscDocs = array( "Uploaded Documents ( Overall : " . $DocsCount.")" => $arNewMiscDocs);
		//$arTenants = array( "Lease Documents ( Overall : " . $LeaseCount.")" => $arNewLeaseDocs);
		//$arMain = $this->array_combine_($arNotices, $arMiscDocuments);
		//$arNew = $this->flipAndGroup($arMain);
		//$arMain = array_merge($arNotices, $arMiscDocuments, $arLeaseDocuments);
		//$arMain = array_merge($allNotice, $arMiscDocs, $arTenants);
		//$arMain = array_merge($arNewNotices, $arNewLeaseDocs);
		//$arMain = $this->UpdateFolderCount($arMain);
		//echo "<pre>";
		//print_r($arNew);
		//echo "</pre>";
		if($this->m_bShowTrace)
		{
			echo "final ar:";
			echo "<pre>";
			print_r($arNewCombined);
			echo "</pre>";
		}
		//die();
		
		//print_r($arUnitDesc);
		//return $arMain;
		//return $arCombineNew;
		return $arNewCombined;
		
	}
	
	function GetNotices($UnitID, &$ResultCount)
	{
		//$UnitID = $_SESSION["unit_id"];
		//$UnitID = "26";
		if($UnitID == 0 || $UnitID == '')
		{
			$result = $this->m_notice->FetchAllNoticesEx(0);
		}
		else
		{
			$result = $this->m_notice->FetchAllNoticesEx(0, $UnitID, true);
		}
		$ResultCount = sizeof($result);
		/*echo "cntr:".sizeof($result);
		echo "<pre>";
		print_r($result);
		echo "</pre>";*/
		$resUnit = $this->m_objUtility->GetUnitDesc($UnitID);
		$UnitNo = $resUnit[0]["unit_no"];
		//echo "<br>unitid:".$UnitID;
		if($UnitNo == '')
		{
			$UnitNo = "ALL";
			//echo "sendin all";
		}
		//echo "details:";
		$arFolderName = $this->GetDocsForUnitAsDocumentType($result, $UnitNo, "Notice", "id","doc_id", "unit_id","notice_version");
		
		
		return $arFolderName;
	}
	function GetDocsForUnitAsDocumentType($result, $UnitNo, $sModule, $ModuleIDColName, $docIDColName, $UnitIDColName, $versionColName, $SeperatedByID = 1)
	{
		$arUnits = array();
		$arFolderName = array();
		//echo "UnitID : ".$UnitNo . " Module: " . $sModule . " ID : " . $ModuleIDColName . " DocID: " .  $docIDColName  . " unitCol : " .  $UnitIDColName  . " version: " .  $versionColName;
		//echo "<pre>";

		$CurresUnit = $this->m_objUtility->GetUnitDesc($CurUnitID);
		if($this->m_bShowTrace)
		{
			echo "<br>module:". $sModule;
		}
		//print_r($CurresUnit);
		$CurUnitNo = $CurresUnit[0]["unit_no"];

		foreach ($result as $key => $value) 
		{
			//echo $key;
			$NoticeDetails = $value;
			//print_r($NoticeDetails);
			$DocID = $NoticeDetails[$docIDColName];			
			$CurUnitID = $NoticeDetails[$UnitIDColName];
			if($CurUnitID == 0)
			{			
				//echo "<br>id:".$DocID . "UID:".$CurUnitID;

				$CurUnitID = $_SESSION["unit_id"];
			}
			else
			{
				//echo "<br>cur:".$CurUnitID;
			}
			
			if($this->m_bShowTrace)
			{
				echo "<br>current:".$CurUnitID;
			}
			if($CurUnitID != '' && $CurUnitID != 0 && $CurUnitID != "0")
			{
				if($CurUnitID != $_SESSION["unit_id"])
				{
					//$CurUnitID = $_SESSION["unit_id"];
				}
				$CurresUnit = $this->m_objUtility->GetUnitDesc($CurUnitID);
				//echo "<br>res:";
				//print_r($CurresUnit);
				$CurUnitNo = $CurresUnit[0]["unit_no"];
			}
			else
			{
				$CurUnitNo = "";
				$CurUnitID = $_SESSION["unit_id"];	
			}
			if($this->m_bShowTrace)
			{
				echo "<br>current No:".$CurUnitNo . " UnitNo:".$UnitNo;
			}
			if($CurUnitNo == '')
			{
				//$CurUnitNo = "ALL";
				$CurUnitNo = $UnitNo;

				//$CurUnitNo = $_SESSION["unit_id"];
			}
			if($sModule == "Misc" && $_SESSION["unit_id"] != "0")
			{
				$CurUnitNo = "ALL";
				$CurUnitNo = $UnitNo;

				//$CurUnitNo = $_SESSION["unit_id"];
			}
			if($DocID == 4)
			{
				//echo "<br>id:".$DocID . "UID:".$UnitID;
			}
			if($this->m_bShowTrace)
			{
				echo "<br>current No:".$CurUnitNo;
				echo "<br>current new:".$CurUnitID;
			}

			if($CurUnitNo != $UnitNo && $CurUnitNo!= "ALL")
			{
				//continue;
				//$CurUnitNo = $UnitNo;
			}
			$NoticeID = $NoticeDetails[$ModuleIDColName];
			
			$notice_version = $NoticeDetails[$versionColName];
			//echo "v:".$notice_version;
			$FolderName = "";
			if($notice_version > 1)
			{
				$sqlDocDesc= "select doc_type from document_type where ID='".$DocID."'";
				//echo "<br>".$sqlDocDesc;
				$result = $this->m_dbConn->select($sqlDocDesc);
				//print_r($result);
				//echo $result[0]["doc_type"];
				$FolderName = $result[0]["doc_type"];
				//echo "<br>id:".$DocID . "UID:".$UnitID."FolderName:".$FolderName;
			}
			else
			{
				//$FolderName = "Others";
				$FolderName = $sModule;
			}

			if($this->m_bShowTrace)
			{
				echo "nc version:".$notice_version .". folder:".$FolderName;
			}
			
			if($_REQUEST["Mode"] == "2")
			{
				$sParentFoldername = $FolderName;
				$sChildFolderName = $CurUnitNo;
			}
			if($_REQUEST["Mode"] == "1")
			{	
				$sParentFoldername = $CurUnitNo;
				$sChildFolderName = $FolderName;
			}

			if(array_key_exists($sParentFoldername, $arFolderName))
			{
				$arTemp = $arFolderName[$sParentFoldername];//
				if($this->m_bShowTrace)
				{
					echo "<br>id:".$DocID . "UNo:".$CurUnitNo." checking for folder:".$FolderName;
				}
				//echo "<pre>";
				
				//print_r($arTemp);
				//echo "</pre>";
				//array_push($arTemp, array($CurUnitNo  =>  $NoticeID));
				$arIDs = $arTemp[$sChildFolderName];
				//echo "<pre>";
				//print_r($arIDs);
				//echo "</pre>";
				//die();
				//array_push($arData,$NoticeID);
				if(sizeof($arIDs) == 0)
				{
					//echo "empty";
					$arIDs =  array();
					//array_push($arIDs, $sModule);
				//$arIDs =
				}
				//eturn $arFolderNam

				 //= $NoticeID;
				//$arData =  array("ID" => $NoticeID, "RiD" => "Notice");
				if($this->m_bShowTrace)
				{
					echo "<br>noticeID:".$NoticeID;
				}
				if($SeperatedByID == 1)
				{
					array_push($arIDs, $sModule ."|" . $NoticeID);
				}
				else
				{
					array_push($arIDs, $NoticeID);
				}

				if($this->m_bShowTrace)
				{
					echo "<pre>";
					print_r($arIDs);
					echo "</pre>";
				}
				$arTemp[$sChildFolderName] = $arIDs;
				//array_push($arTemp, $arIDs);
				if($this->m_bShowTrace)
				{
					echo "<pre>";
					print_r($arTemp);
					echo "</pre>";
				}
				//print_r($arTemp);
				if(sizeof($arTemp) > 0)
				{
					$arFolderName[$sParentFoldername] = $arTemp;//
				}
			}
			else
			{
				$arData = array();
				//$arData["Details"] = array("ID" => $NoticeID, "RiD" => "Notice");
				//array_push($arData, $sModule);
				if($SeperatedByID == 1)
				{
					array_push($arData, $sModule ."|" . $NoticeID);
				}
				else
				{
					array_push($arData, $NoticeID);
				}
				//echo "<br>First id:".$DocID . "UNo:".$CurUnitNo." folder not exist:".$FolderName;
				$arTemp2[$sChildFolderName] =  $arData;//
				$arFolderName[$sParentFoldername] = $arTemp2;	
				//echo "<pre>";
				//print_r($arFolderName);
				//echo "</pre>";
				//die();
			}
			//$arUnits[$FolderName] = 
			//echo "<br>".$FolderName;
		}
	//echo "</pre>";
			
		return $arFolderName;
	}

}
?>