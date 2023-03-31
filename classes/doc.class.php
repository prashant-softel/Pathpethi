<?php
//include_once("classes/include/dbop.class.php");
//require_once("../functions.php");
		include_once("/../GDrive.php");

include_once("utility.class.php");

class document
{
	public $actionPage = "../directory.php";
	public $m_dbConn;
	public $obj_utility;
	public $m_bShowTrace;
		
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		//echo "6";
		$this->obj_utility = new utility($dbConn);
		//echo "7";
		$this->m_bShowTrace = 1;
	}
		
function reArrayFiles(&$file_post) {

    $file_ary = array();
    $file_count = count($file_post['name']);	
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
            echo "file:".$file_post[$key][$i];
        }
    }

    return $file_ary;
}

public function startProcess()
	{				
		//print_r($_FILES);
		$doc_type = $_POST["Document_type"];
		$PostDate = $_POST['post_date'];         		                    
		$Note = $_POST["note"];
		$docGDriveID = "";
		$file_name = "";

		try
		{
			echo "uploading doc...";
			$resResponse = $this->obj_utility->UploadAttachment($_FILES,  $doc_type,$PostDate, "Uploaded_Documents");
			$sStatus = $resResponse["status"];
			$sMode = $resResponse["mode"];
			$sFileName = $resResponse["response"];
			$sUploadFileName = $resResponse["FileName"];
			//$_POST['note'] = $sUploadFileName;

			if($sMode == "1")
			{
				$file_name = $sFileName;
				//$_POST['note'] = $resResponse["note"];
			} 
			else if($sMode == "2")
			{
				$docGDriveID = $sFileName;
			}
			else
			{
				//failure or no file uploaded
			}
							//print_r($arResponse);
			//die();
			//echo $ObjGDrive->UploadFiles($_FILES['userfile']['name'], $description, $mimeType, $_FILES['userfile']['tmp_name'], $folderName, $folderDesc, "","",$rootid, $UnitNo);			//$current_time = date("H:i", strtotime("now"-17));												 
			//if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile))		 
			$sDocVersion = '2';
			if($docGDriveID != "")
			{
				$sDocVersion = '2';
			}
				//echo "File is valid, and was successfully uploaded.\n";
				$insert_query="insert into documents (`Name`, `Unit_Id`, `Category`, `Note`,`Document`,`doc_type_id`,`doc_version`,`attachment_gdrive_id`) values ('".$_POST['doc_name']."', '".$_SESSION['unit_id']."','".$_POST['category']."', '".$Note."','".$sUploadFileName."','".$doc_type."','".$sDocVersion."','".$docGDriveID."')";
				//echo $insert_query;
				$data=$this->m_dbConn->insert($insert_query);
				//print_r($data);
				//"size:".sizeof($data);

			if(sizeof($data) >0)
			{
				return "Upload";
			
			}
			else if($_FILES['userfile']['error'] > 0)
			{
					return "Error";
			}
			else if(file_exists($uploaddir . $_FILES['userfile']['name']))
			{
					return "Exists";
			}
			else
			{
				return "NotUploaded";
			}
		}
		catch(Exception $ex)
		{
			//$GDriveFlag = 0;
			echo "Exception:".$ex->getMessage();
		}                      	
			
		$errorExists=0;								
	}
	
	public function fetchDocuments($LoadUploadedOnly = false)
	{				

		if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN))
		{
			$sql = "SELECT * FROM `documents`";
			if($LoadUploadedOnly)
			{
				$sql .= " where source_table=0";
			}	
		}
		else
		{
			$sql = "SELECT * FROM `documents` WHERE `Category` = 1 OR `Unit_Id` = ".$_SESSION['unit_id'];			
			if($LoadUploadedOnly)
			{
				$sql .= " and source_table=0";
			}
		}
		//echo $sql;
		$result = $this->m_dbConn->select($sql);
		return $result;
	}
	public function fetchDocumentsNew($LoadUploadedOnly = false, $UnitID = 0)
	{				

		if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN))
		{
			$sql = "SELECT * FROM `documents` ";
			$bUnitWise = false;
			//echo "unitid chk:".$UnitID;
			if($UnitID != "0")
			{
				$bUnitWise = true;
				$sql .= " where `Unit_Id` = '".$UnitID."' or `Category`='0'";	
			}
			if($LoadUploadedOnly)
			{
				if($bUnitWise)
				{
					$sql .= " and";	
				}
				else
				{
					$sql .= " where";	
				}
				$sql .= " source_table=0";
			}	
			//echo $sql;
		}
		else
		{
			$sql = "SELECT * FROM `documents` WHERE `Category` = 1 OR `Unit_Id` = ".$UnitID;			
			if($LoadUploadedOnly)
			{
				$sql .= " and source_table=0";
			}
		}
		//echo "qry:".$sql;
		$result = $this->m_dbConn->select($sql);
		return $result;
	}
	
	public function deleteDoc($DocID)
	{
		
		$query = "delete from `documents` where doc_id='".$DocID."'";
		$data = $this->m_dbConn->delete($query);
		return $data;
	
	}
	
	public function combobox($query,$id)
	{
	$str.="<option value=''>Please Select</option>";
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
						if($v==$id)
						{
							$sel = "selected";
						}
						else
						{
							$sel = "";	
						}
						$str.="<OPTION VALUE=".$v.">";
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