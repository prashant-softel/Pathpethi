<?php
require_once 'google-api-php-client/src/Google/Client.php';
require_once 'google-api-php-client/src/Google/Service/Oauth2.php';
require_once 'google-api-php-client/src/Google/Service/Drive.php';
//error_reporting(7);
?><link href="styles/default/default.css" rel="stylesheet" type="text/css" media="screen" />
		
		<!-- Makes the file tree(s) expand/collapsae dynamically -->
		<script src="js/jquery_min.js" type="text/javascript"></script>
		<script src="js/php_file_tree_jquery.js" type="text/javascript"></script>
		<?php
//session_start();
include_once("classes/include/dbop.class.php");
//$objConn = new dbop($m_dbConn);
//$res= $objConn->select("select * from society");
//print_r($res);
header('Content-Type: text/html; charset=utf-8');
// Init the variables


class GDrive
{
	public $m_dbConn;
	public $m_service;
	public $m_UnitNo;

	function __construct($dbConn, $UnitNo = 0)
	{
		//print_r($dbConn);
		$this->m_dbConn = $dbConn;
		$this->m_UnitNo = $UnitNo;
		//echo "before Initialize".$UnitNo ;
		//echo "inside Initialize";
		
		$this->Initialize(); 
	}
	public function Initialize()
	{
		$curdir = "c:/wamp/www";
		$curdir .= dirname($_SERVER['REQUEST_URI'])."/";

		$curdir .= "../conf/GoogleClientId.json";
		$curdir = "conf/GoogleClientId.json";
		
		$json = json_decode(file_get_contents($curdir), true);
		//print_r($json);
		
		//echo "Current dir is: ".$curdir;
		$CLIENT_ID = $json['web']['client_id'];
		$CLIENT_SECRET = $json['web']['client_secret'];
		$REDIRECT_URI = $json['web']['redirect_uris'][0];
		//echo "clientid:".$CLIENT_ID;
		// Create a new Client
		$client = new Google_Client();
		$client->setClientId($CLIENT_ID);
		$client->setClientSecret($CLIENT_SECRET);
		$client->setRedirectUri($REDIRECT_URI);
		$client->addScope(
			"https://www.googleapis.com/auth/drive", 
			"https://www.googleapis.com/auth/drive.appfolder",
		"https://www.googleapis.com/auth/drive.file",
		"https://www.googleapis.com/auth/drive.readonly",
		"https://www.googleapis.com/auth/drive.metadata.readonly",
		"https://www.googleapis.com/auth/drive.appdata",
		"https://www.googleapis.com/auth/drive.metadata",
		"https://www.googleapis.com/auth/drive.photos.readonly");
		echo "generating client";
		// Get the file path from the variable
		//$file_tmp_name = $_FILES["file"]["tmp_name"];
		// Get the client Google credentials
		//$credentials = $_COOKIE["credentials"];
		$credentials = $_COOKIE["credentials"];
		$sqlSelect = "select GDrive_Credentials,GDrive_UserID from `society`";
		//echo $sqlSelect;
		//print_r($m_dbConn);
		$res = $this->m_dbConn->select($sqlSelect);
		//print_r($res);
		$credentials = $res[0]["GDrive_Credentials"];
	
		//echo "Credential:".$credentials;
		// Get your app info from JSON downloaded from google dev console
		// Refresh the user token and grand the privilege
		$client->setAccessToken($credentials);
		//echo "access token set";
		//print_r($this->m_service);
		$this->m_service = new Google_Service_Drive($client);
		//print_r($this->m_service);
		echo "done Initialize";
	}
	public function GetGDriveFolderList($RootID)
	{
		$RootFolder= $this->getRootFolder($RootID);//get root folder name
		echo "root name:".$RootFolder;

		if($RootFolder <> '')
		{
			$arFinal = array();
			 $SubFolder= $this->getSubRootFolder($RootID, true, $arFinal);// get sub root folder name
			 echo "<pre>";
			 $arData = array( "Documents" => $SubFolder);
			 //print_r($arData); //see array of drive documents on ui
			 echo "</pre>";
			if($SubFolder<>"")
			{
				//echo "<br>final array is:<br>";
				//print_r($SubFolder);
				//GetFoldersandFiles($this->m_service,$SubFolder);
				 
			$List_of_docs =  array(
					'GDrive' => array(
						$FinalFolders,
					));
				return $arData;
			}
		}
	}
	public function ShowGDriveTree($RootID)
	{
		$arData = $this->GetGDriveFolderList($RootID);
		//print_r($arData);
		if(sizeof($arData) > 0)
		{
			return $this->Getdir($arData);
		}
		{
			echo "No data found";
		}
	}
	function GetParentFolderID($FolderName, $parentFolderID)
	{
		$files = $this->m_service->files->listFiles();
		//print_r($files);
		$found = false;

		// Go through each one to see if there is already a folder with the specified name
		foreach ($files['items'] as $item)
		 {
		 	echo "<br>CID:".$item['id']." Title:".$item['title'] ;
		 	echo "<br>pID:".$parentFolderID;
		 	$CanFind = false;
		 	if($item['id'] == $parentFolderID)
		 	{
		 		echo "<br>parent found";
		 		$CanFind = true;
		 	}
		 	if($CanFind)
		 	{
				if ($item['title'] == $folderName) 
				{
					$found = true;
					echo "<br>folder found:".$folderName;
					return $item['id'];
					break;
				}
			}
		}
	}
	function GetFoldersandFiles($SubFolder)
	{
		$SubFolderFileList=$this->getFileListForSubFolder($SubFolder); // get list of files for sub folder
		 echo "<pre>";
		 //print_r( $SubFolderFileList);
		 echo "</pre>";
		 if($SubFolderFileList<> "")
		 {
			 //downloadFile($this->m_service,$SubFolderFileList);
			 FileDownload($SubFolderFileList);
		 }
		 return $SubFolderFileList;
	}
	function GetChildFolderID($ParentFolderID, $expFolderName)
	{
		$pageToken = NULL;

		  do 
		  {

		    try 
		    {
		      $parameters = array();
		      $search = "mimeType = 'application/vnd.google-apps.folder' AND trashed != true";
	    	$parameters = array("q" => $search);
		
		      if ($pageToken) 
		      {
		        $parameters['pageToken'] = $pageToken;
		      }
		      $children = $This->m_service->children->listChildren($ParentFolderID, $parameters);

		      foreach ($children->getItems() as $child) 
		      {
		        print 'File Id: ' . $child->getId();
		      }
		      $pageToken = $children->getNextPageToken();
		    } 
		    catch (Exception $e) 
		    {
		      print "An error occurred: " . $e->getMessage();
		      $pageToken = NULL;
		    }
		  } while ($pageToken);
	}
	/**
	* Get the folder ID if it exists, if it doesnt exist, create it and return the ID*/
	function getFolderIfExistsElse($folderName, $folderDesc, $parentFolderID, $bSubFolder) 
	{
		// List all user files (and folders) at Drive root
		echo "called getFolderIfExistsElse";
		//print_r($this->m_service);
		try
			 {
			 	$files = $this->m_service->files->listFiles();
		} 
			catch (Exception $e)
			 {
				print "An error occurred new: " . $e->getMessage();
			 }
		//print_r($files);
		$found = false;
		$CanFind = true;
		 	
		// Go through each one to see if there is already a folder with the specified name
		foreach ($files['items'] as $item)
		 {
		 	if(!$bSubFolder)
		 	{

		 	}
		 	//$ParentID = $this->GetParentFolderID();
		 	echo "<br>CID:".$item['id']." Title:".$item['title'] ;
		 	echo "<br>pID:".$parentFolderID;
		 	echo "<pre>";
		 	//print_r($item);
		 	echo "parent id:". $item["modelData"]['parents'][0]["id"];
		 	echo "</pre>";
		 	/*if ($item['title'] == $folderName && !$bSubFolder)
		 	{
		 		echo "<br>parent found";
		 		
					$found = true;
					$CanFind = true;
		 		return $item['id'];
		 		break;
		 	}*/
		 	if($CanFind)
		 	{
				if ($item['title'] == $folderName) 
				{
					$found = true;
					echo "<br>folder found:".$folderName;
					return $item['id'];
					break;
				}
			}
		}
        //die();
		// If not, create one
		if ($found == false) 
		{
			echo "start creating folder:".$folderName;
			$folder = new Google_Service_Drive_DriveFile();

			//Setup the folder to create
			$folder->setTitle($folderName);
			echo "1";
			
			if(!empty($folderDesc))
				$folder->setDescription($folderDesc);
			echo "2";
				
			//Set the parent id if you are creating a sub folder
			if(!empty($parentFolderID))
			{
				$parent = new Google_Service_Drive_ParentReference();
				$parent->setId($parentFolderID);
				$folder->setParents(array($parent));
			}
			echo "set mime";
			$folder->setMimeType('application/vnd.google-apps.folder');

			//Create the Folder
			try
			 {
				$createdFile = $this->m_service->files->insert($folder, array(
					'mimeType' => 'application/vnd.google-apps.folder',
					));
				//print_r($createdFile);
				// Return the created folder's id
				return $createdFile->id;
			 } 
			catch (Exception $e)
			 {
				print "An error occurred: " . $e->getMessage();
			 }
		}
	}

	function printFilesInFolder($folderId) {
	  $pageToken = NULL;

	  do {
	    try {
	      $parameters = array();
	      if ($pageToken) {
	        $parameters['pageToken'] = $pageToken;
	      }
		  echo "listing children";
	      $children = $this->m_service->children->listChildren($folderId, $parameters);

	      foreach ($children->getItems() as $child) {
	        print 'File Id: ' . $child->getId() . " desc: ".$child->getDescription();
	      }
	      $pageToken = $children->getNextPageToken();
	    } catch (Exception $e) {
	      print "An error occurred: " . $e->getMessage();
	      $pageToken = NULL;
	    }
	  } while ($pageToken);
	}
	/**
	 * Insert new file in the Application Data folder. */
	function insertFile($title, $description, $mimeType, $filename, $folderName, $folderDesc, $subFolderName, $subFolderDesc, $strParentFolderID)
	 {
		$file = new Google_Service_Drive_DriveFile();

		// Set the metadata
		$file->setTitle($title);
		$file->setDescription($description);
		$file->setMimeType($mimeType);
		
		$parentFolderID = $strParentFolderID;

		// Setup the folder you want the file in, if it is wanted in a folder
		if(isset($folderName)) 
		{
			if(!empty($folderName))
			 {
			 	echo "folder not empty";
				$parent = new Google_Service_Drive_ParentReference();
				echo "parent is set";
				$parentFolderID = $this->getFolderIfExistsElse($folderName, $folderDesc, $parentFolderID, false); 
				$parent->setId($parentFolderID);
				echo "set parent 1";
				$file->setParents(array($parent));
				echo "<br> parent done:".$parentFolderID;
		
			}
		}
		if(!empty($subFolderName)) 
		{
				echo "<br> set subfolder name:".$subFolderName . " its desc :".$subFolderDesc;
			
				$parent = new Google_Service_Drive_ParentReference();
				$parent->setId($this->getFolderIfExistsElse($subFolderName, $subFolderDesc, $parentFolderID , true));
				echo "set parent 2";
				$file->setParents(array($parent));
		}
		echo "<br> parent:".$parentFolderID;
		//$objConn = new dbop($m_dbConn);
		$bUpdateSocietyLevel = false;
		if($bUpdateSocietyLevel)
		{
			$sql = "update `society` set `GDrive_W2S_ID`='".$parentFolderID."'";
			echo $sql;
			$res= $this->m_dbConn->update($sql);
			//print_r($res);
		}
		try 
		{
			// Get the contents of the file uploaded
			$data = file_get_contents($filename);

			// Try to upload the file, you can add the parameters e.g. if you want to convert a .doc to editable google format, add 'convert' = 'true'
			$createdFile = $this->m_service->files->insert($file, array(
				'data' => $data,
				'mimeType' => $mimeType,
				'uploadType'=> 'multipart'
				));

			// Return a bunch of data including the link to the file we just uploaded
			return $createdFile;
		} 
		catch (Exception $e)
		 {
			print "An error occurred: " . $e->getMessage();
		 }
	}

	//echo "<br>Link to file: " . $driveInfo["alternateLink"];
	function getRootFolder2($rootid) 
	{
		$pageToken = null;
	do {
	    $search = "mimeType = 'application/vnd.google-apps.folder' AND trashed != true";
	    $parameters = array("q" => $search,
	        'fields' => 'nextPageToken, items(id, title)');
		echo "<pre>";
		//echo "param".print_r($parameters);
		$response = $this->m_service->files->listFiles($parameters);
		$cntr = 1;
		foreach ($response->items as $file) {
			echo "<br>count:".$cntr;
			//print_r($file);
			$cntr++;
			echo "<br>mime:".$file->getMimeType();
	        printf("<br>Found file: %s (%s)\n", $file->name, $file->id);
			$this->printFilesInFolder($file->id);
	    }
		 $pageToken = $repsonse->pageToken;
	} while ($pageToken != null);
	echo "</pre>";
	    /* $files = $this->m_service->files->listFiles($parameters);
		//print_r($files);
	    echo "</pre>";
		if (!empty($files["items"]))
		 { 
	        $RootFolder['id'] = $files["items"][0]->getId();
			$RootFolder['name'] = $files["items"][0]->getTitle(); // the first element
			//echo $RootFolder['name'];
		 }
		else
		{
	        return false;
		}
		echo "<pre>";
		//print_r($RootFolder);
		echo "</pre>";
		return $RootFolder['id'] ; */
	}

	function getRootFolder($rootid) 
	{
		
	    $search = "mimeType = 'application/vnd.google-apps.folder' AND trashed != true";
	    $parameters = array("q" => $search);
		//echo "<pre>";
		//echo "param".print_r($parameters);
		echo "1";
		//print_r($this->m_service->files);
	    $files = $this->m_service->files->listFiles($parameters);
	    echo "size:".sizeof($files);
		//print_r($files);
	   // echo "</pre>";
		if (!empty($files["items"]))
		 { 
	        $RootFolder['id'] = $files["items"][0]->getId();
			$RootFolder['name'] = $files["items"][0]->getTitle(); // the first element
			//echo $RootFolder['name'];
		 }
		else
		{
	        return false;
		}
		echo "2";
		echo "<pre>";
		print_r($RootFolder);
		echo "</pre>";
		return $RootFolder['id'] ;
	}

	function getSubRootFolder($RootFolder, $FirstCall, $arFinal)
	{
	   $pageToken = NULL;
	echo "<pre>";
		  
	  do {
	    try {
	      $parameters = array();
		  if($FirstCall)
		  {
			//$SubFolder =  array();
	      }
		  if ($pageToken) 
		  {
	        $parameters['pageToken'] = $pageToken;
	      }
		  //print_r($RootFolder);
	      $children = $this->m_service->children->listChildren($RootFolder, $parameters);
		  //echo "<br>children for root folder:".$RootFolder;
		  //print_r($children);
		  $counter=0;
		   for($iCont=0;$iCont<sizeof($children);$iCont++)
		   {
			    $file = $this->m_service->files->get($children[$iCont]['id']);
				//echo "<br>MIME Type:".$file["mimeType"];
				
				$MimeType = $file["mimeType"];
				if( $MimeType == "application/vnd.google-apps.folder")
				{
					//print_r($file);
					$FolderID = $file["id"];
					$desc = $file["description"];
					//echo "Folder ". $desc . " and current folder:".  $this->m_UnitNo;
					if($this->m_UnitNo == 0 || $this->m_UnitNo == $desc)
					{
						//echo "<br>Folder found:".$desc;
						
						$sArrayName = $desc;
						$sArraySubName = $FolderID;				
						$sArraySubName = "SubId";
							
						//$SubFolder[$sArraySubName][$file->getId()] = $file->getTitle();//= getSubRootFolder($this->m_service, $FolderID, false);
						//$SubFolder[$sArrayName][$counter]=  getSubRootFolder($this->m_service, $FolderID, false, $arFinal);
						$arFinal[$desc] = $this->getSubRootFolder($FolderID, false, $arFinal[$desc]);
					}
					else
					{
						echo "<br>Folder ". $desc . " not matched with current unit:".  $this->m_UnitNo;
						continue;
					}
				}
				else
				{
					//echo "<br>Counter:".$iCont."<br>";
				   $sArrayName = "GDrive";
					$sArraySubName = "SubId";
					//echo "<br>Title:".$file->getTitle();;
					$sArrayName = $file->getDescription();
					$sArraySubName = $file->getId();
					$sArraySubName = "SubId";
					$SubFolder[$sArrayName][$file->getId()]= $file->getTitle();
					//$SubFolder[$sArraySubName][$counter]=$file->getId(); //issue with subFolder
					$arFinal[$file->getId()] = $file->getTitle();	
					$counter++;
					$pageToken = $children->getNextPageToken();
					//echo "<pre>";
					//echo "<pre>";
				}
				//$arFinal[$file->getDescription()] = $SubFolder;
		   }
		   echo "</pre>";
		  
		return $arFinal;
	   }
		catch (Exception $e) 
		{
	      print "An error occurred: " . $e->getMessage();
	      $pageToken = NULL;
	    }
		
	  } while ($pageToken);
	}

	function getFileListForSubFolder($SubFolder)
	{
	 $pageToken = NULL;
		 do {
		 try {
	      $parameters = array();
	      	if ($pageToken)
			 {
	        	$parameters['pageToken'] = $pageToken;
	     	 }
			//print_r($SubFolder);
		   	for($iCnt=0;$iCnt<sizeof($SubFolder);$iCnt++)
			{
				echo "<br>cnt:".$iCnt."sub folder id:".$SubFolder['SubId'][$iCnt]."<br>";
				$FileList = $this->m_service->children->listChildren($SubFolder['SubId'][$iCnt], $parameters);
				$size = sizeof($FileList);
				echo "Size:".$size;
				if($size > 0)
				{
				//print_r($FileList);
				$k=0;
				 for($i=0;$i<sizeof($FileList);$i++)
				 {
					echo "<br>cntr:".$i . "";
					$file = $this->m_service->files->get($FileList[$i]['id']);
					echo "file:".$file;
					$FileName['FileId'][$k]=$file->getId();
					//$FileName['FileName'][$k]= $file->getTitle();
					//$FileName['downloadUrl'][$k]= $file->getDownloadUrl();
				 
			//echo "<pre>";
			//	print_r( $FileName);
			//echo "</pre>";
					$k++;
				 }
				}
			//$pageToken = $children->getNextPageToken();
			}
		
		 }
		catch (Exception $e) 
		{
	      print "An error occurred: 3 " . $e->getMessage();
	      $pageToken = NULL;
	    }
		return  $FileName;
	  } while ($pageToken);
	}

	/*function downloadFile($this->m_service, $FileName) 
	{
		$downloadUrl = $file->getDownloadUrl();	
		if ($downloadUrl) 
		{
			$request = new Google_HttpRequest($downloadUrl, 'GET', null, null);
			$httpRequest = Google_Client::$io->authenticatedRequest($request);
			if ($httpRequest->getResponseHttpCode() == 200)
			{

			return $httpRequest->getResponseBody();
			} 
			else 
			{
				// An error occurred.
				return null;
			}
		}
	 else
	   {
		// The file doesn't have any content stored on Drive.
		return null;
	   }
	}*/
	function printFile($file_id) {
		echo '<pre>';
		print_r($file_id);
		echo '</pre>';
	  //try {
		 // for($i=0;$i<sizeof($file_id);$i++)
		  //{
			// echo "FileID :".$file_id; 	
	    	//$file = $this->m_service->files->get($file_id);
			echo '<pre>';
			print_r($file);
			echo '</pre>';
	    print "Title: " . $file->getTitle();
	    print "Description: " . $file->getDescription();
	    print "MIME type: " . $file->getMimeType();
		 //}
	 // } catch (Exception $e) {
	  //  print "An error occurred: " . $e->getMessage();
	  //}
	  
	}
	function CreateUnitFolder()
	{
		$List_of_documents = array
				(
					'UnitNo' => array(
						'101' => 'event_path_1.pdf',
					),
					'Notices' => array(
						'subject 1' => 'notice_path_1.pdf',
						'subject 2' => 'notice_path_2.pdf'
					),
					/* 'test' => array(
						'one' => 'one-nine-1.doc',
						'two' => 'two-nine-2.docx',          
						'three' => array(   
							'one' => 'one-nine-three-1.ppt',
							'two' => 'two-nine-three-2.pptx'
						), 
					),*/
				);
	}
	function FileDownload($fileList)
	{ 
	try {
	 echo "test";
	 echo '<pre>';
	//print_r($fileList);
	 echo '</pre>';
	//https://drive.google.com/open?id=0B9ez4Vc-n0DbWkV6VmtRZFJIbnhqU3d2QmNHTTZfWWJYZGM0
	//$folder_id='1YdvXnwE2dQTfugwenRzV2RNEmusyO9IY';
	//$file_id='14DeSs_ySFo-UAmc0YRH34U0Ovlw-g9xs';


	//printFile($this->m_service,$fileList);
	//$file = $this->m_service->files->get($file_id);
	//echo "<pre>";
	//print_r($file);
	//echo "</pre>";
	//header('Content-Type: '.$file->getMimeType());
	//print(downloadFile($this->m_service,$file));


	  } catch (Exception $e) {
	  print "An error occurred1: " . $e->getMessage();
	  }
	}
	function Getdir($GDocuments)
	{
		echo "<br>";
			$List_of_documents = array
			(
				'Events' => array(
					'event 1' => 'event_path_1.pdf',
				),
				'Notices' => array(
					'subject 1' => 'notice_path_1.pdf',
					'subject 2' => 'notice_path_2.pdf'
				),
				/* 'test' => array(
					'one' => 'one-nine-1.doc',
					'two' => 'two-nine-2.docx',          
					'three' => array(   
						'one' => 'one-nine-three-1.ppt',
						'two' => 'two-nine-three-2.pptx'
					), 
				),*/
			);
			
			/* $List_of_GDrive_documents	= array('Documents' => array(
					'doc 1' => 'doc_path1.gif',
					'doc 2' => 'doc_path2.png',
					'doc 3' => 'doc_path3.jpg'
				),
				); */
				//print_r($GDocuments);
				$List_of_GDrive_documents = $GDocuments;
			$arMain = array_merge($List_of_documents, $GDocuments);	
			//print_r($arMain);
			return $this->display_directory($arMain, true, false);
		
	}
	function display_directory($List_of_documents, $first_call, $bGDriveTree)
	{
		$TreeUL = "<ul";
		if( $first_call ) { $TreeUL .= " class=\"php-file-tree\""; $first_call = false; }
		$TreeUL .= ">";
		$return_link = "http://phpFileTree/?file=[link]/";
		$cntr = 1;
		foreach($List_of_documents as $key => $value)
		{
			//echo "Key=" . $key . ", Value=" . $value;
			
			if(is_array($value))
			{
				//echo "<br>key:".$key;
				//echo "<br>folder:".$value;
				$TreeUL .=  "<li class=\"pft-directory\"><a href=\"#\">" . htmlspecialchars($key) . "</a>";
				//$TreeUL .= "<ul>";
				//echo "<br>calling recursive";
				//$bGDriveTree = false;
				if($key == "Documents" && $bGDriveTree == false)
				{
					$bGDriveTree = true;	
				}
				$TreeUL .= $this->display_directory($value, false, $bGDriveTree);
				//$TreeUL .= "</ul>";
				$TreeUL .=  "</li>";
			}
			else
			{
				//echo "<br>this file val:".$this_file;
				//echo sizeof($value);
				//echo "<br>cntr:".$cntr;
				//echo "<br>size:".sizeof($List_of_documents);
				if($cntr == 1)
				{
					//$TreeUL .= "<ul";
					//$TreeUL .= " class=\"pft-file\"";
					//$TreeUL .= ">";
					//echo "inside 1";
				}				
				$ext = "ext-" . substr($value, strrpos($value, ".") + 1); 
				//echo "<br>new ext:".$ext;
				//echo "<br>new key:".$key;
				//echo "<br>new val:".$value;
				//echo "dir:".$directory;
				
				$link = str_replace("[link]", "$directory" . urlencode($value), $return_link);
				if($bGDriveTree)
				{
					$link = "https://drive.google.com/file/d/".$key . "/view";
				}
				//echo "<br>new link:".$link;
				$link = "<li class=\"pft-file " . strtolower($ext) . "\"><a href=\"$link\" target=\"_blank\">" . htmlspecialchars($value) . "</a></li>";
				$TreeUL .= $link;
					//echo $link;
				if($cntr == sizeof($List_of_documents))
				{
					//$TreeUL .= "</ul>";
					//echo "inside end";
				}	
				$cntr++;
			}
			//echo "<br>";
		}
		$TreeUL .= "</ul>";
		return $TreeUL;
	}

}


/*if(isset($_REQUEST['id']))
{
	$rootid=$_REQUEST['id'];
}
else
{
	//echo "connecting db";
	$objConn = new dbop($m_dbConn);
	$sql = "select GDrive_W2S_ID from `society`";
	$res= $objConn->select($sql);
	//print_r($res);
	$rootid = $res[0]['GDrive_W2S_ID'];
	//echo "rootid:".$rootid;
	$ObjGDrive = new GDrive($objConn);
	//echo "Initialize done";
	$ObjGDrive->GetGDriveFolderList($rootid);
	//print_r($_SESSION);
}
if($rootid != "")
{
	
}
else
{
	echo "<br>Root ID not found.";
}*/

// Set the file metadata for drive
//$mimeType = $_FILES["file"]["type"];
//$title = $_FILES["file"]["name"];
//$description = "Uploaded from your very first google drive application!";

if(isset($_FILES['file_array']) || isset($_REQUEST["InitializeGDrive"]))
{
	$GDriveFlag = 1;
	try
	{
		$name_array=$_FILES["file_array"]["name"];
		$name_type=$_FILES["file_array"]["type"];
		$description="File";
		$file_temp_array=$_FILES["file_array"]["tmp_name"];	
		print_r($file_temp_array);
		$driveInfo = "";
		$folderName = "";
		$folderDesc = "";
		$parentFolderID = "";

		if (!empty($_POST["folderName"]))
		$folderName = $_POST["folderName"];
		if (!empty($_POST["folderDesc"]))
		$folderDesc = $_POST["folderDesc"];
		if (!empty($_POST["ParentFolderID"]))
		$parentFolderID = $_POST["ParentFolderID"];
		
		$subFolderName = "";
		$subFolderDesc = "";
		$subFolderName = 'Lease';
		$subFolderDesc = 'Lease files';

		for($i=0; $i<count($file_temp_array); $i++)
		{
			$title =$name_array[$i];
			$mimeType=$name_type[$i];
			$description=$description[$i];
			$file_tmp_name=$file_temp_array[$i];
			// Get the folder metadata
			
			echo "<br>title :".$title." file_tmp_name :".$file_tmp_name." mimeType:".$mimeType;
			$objConn = new dbop($m_dbConn);
			$ObjGDrive = new GDrive($objConn);
			//die();
			//$ObjGDrive->ShowGDriveTree($rootid);

echo "title:".$title . " desc:".$description ." mimeType : ". $mimeType . " file_tmp_name " . $file_tmp_name;
			// Call the insert function with parameters listed below
//die();
			$driveInfo = $ObjGDrive->insertFile($title, $description, $mimeType, $file_tmp_name, $folderName, $folderDesc, $subFolderName, $subFolderDesc, $parentFolderID);
			

		}
	}
	catch(Exception $ex)
	{
		$GDriveFlag = 0;
	}
	$RedirectURL = "society_gd.php?id=".$_SESSION['society_id']."&show&imp&GDriveFlag=".$GDriveFlag;
	//$RedirectURL = "fileupload1.php?code=".$parentFolderID;
		header("location: ".$RedirectURL);
	
}

?>
<!--<p><a href="#" onClick="<?php //GetRootFolder($this->m_service)?>">click here</a></p>-->