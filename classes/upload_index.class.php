<style>
$table table
{
	border:1px;
}
</style>
<?php
include_once("include/display_table.class.php");

include_once ("dbconst.class.php"); 
include_once( "include/fetch_data.php");
include_once("utility.class.php");
include_once('../swift/swift_required.php');
define ("MAX_SIZE","1024");
//ini_set('post_max_size', '10M');
class show_album 
{
	
	public $actionPage = "../gallery_upload.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	public $IsIncluded;
	public $errormsg;
	public $errormsg1;
	public $table='<table>';
	public $table1='<table>';

	 
		
	function __construct($dbConn, $dbConnRoot,$IsHeader = false)
	{
		
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->IsIncluded = $IsHeader;
		$this->errormsg = '';
		//$this->table='<table open border=1>';
		$this->display_pg=new display_table($this->m_dbConnRoot);
		
	}
	public function startProcess()
	{
		if($_REQUEST['insert']=='Insert' && $errorExists==0)
		//{
		
			$curdate 		=  $this->curdate;
			$curdate_show	=  $this->curdate_show;
			$curdate_time	=  $this->curdate_time;
			$ip_location	=  $this->ip_location;

		//echo "how";
			if($_POST['insert']=='')
			{ 
			 
				if($_POST['name'])
				{
					//trim($str,"Hed!");
					$folder_name = trim($_POST['name'] . "_" . getRandomUniqueCode());
					$folder_name=str_replace(' ','',$folder_name);
					// echo "INSERT INTO `album`(`group_id`,`name`) VALUES ('".$_POST['group']."','".$_POST['name']."')";
					$insert_album= "INSERT INTO `album`(`group_id`,`name`,`folder`) VALUES ('".$_POST['group']."','".$_POST['name']."','" .$folder_name. "')";
					//echo $insert_album;
					$result_show = $this->m_dbConnRoot->insert($insert_album);	
					
					//die();
					if(!file_exists("../uploads"))
					{
						mkdir("../uploads");
					}
					else
					{
						mkdir("../uploads/".$folder_name."");
						mkdir("../uploads/".$folder_name."/thumb");
					}
					
					if($result_show <> '')
					{
					$select_album="Select * from `album` where id='".$result_show."'";	
					$selectAlbum = $this->m_dbConnRoot->select($select_album);	
					$AlbumName=$selectAlbum[0]['name'];
					$GroupId=$selectAlbum[0]['group_id'];
					$AlbumID=$selectAlbum[0]['id'];
					$this->sendEmail($GroupId,$AlbumName,$AlbumID);
					}
				}//close if
				
				if(isset($_POST['upload']))
				{
					error_reporting(0);  
					

						for($i=0; $i<count($_FILES['pic']['name']); $i++)
						{
							$album_id=$_POST['album'];
							//echo '<br>*****************';
							 $file= $_FILES['pic']['name'][$i];
							//echo '***************<br>';
							$file_type=$_FILES['pic']['type'][$i];
							$file_size=$_FILES['pic']['size'][$i];
							$file_tmp=$_FILES['pic']['tmp_name'][$i];
							list($txt, $ext) = explode(".", $file);
							$randon_name = $file.".".$ext;
							$kaboom = explode(".", $file); // Split file name into an array using the dot
							$fileExt = end($kaboom);
							$random_name= rand();
							 $file_info = getimagesize($_FILES['pic']['tmp_name'][$i]);
									
							if ($_FILES["pic"]["error"][$i] > 0)
							 {
								$error = $_FILES["pic"]["error"];
							 } 
							 else if(empty($file_info))
							 {
								$error.="The uploaded file does not seem to be an image.";
								$this->table .= "The uploaded file does not seem to be an image.";
							 }
							else if ($_FILES["pic"]["size"][$i] > 20480*1024) 
							{
   							// echo "Sorry, your file is too large.";
							 $error.="Sorry, your file is too large.";
							$this->table .= "Sorry, your file is too large.";
    						//$uploadOk = 0;
							}
							
							 
							else if (($_FILES["pic"]["type"][$i] == "image/gif") || 
									($_FILES["pic"]["type"][$i] == "image/jpeg") || 
									($_FILES["pic"]["type"][$i] == "image/png") || 
									($_FILES["pic"]["type"][$i] == "image/pjpeg")) 
							{
								
						        $url = "";
								$url_thumb= "";
								if ($_FILES["pic"]["type"][$i] == "image/jpeg")
								{
									$url =$random_name.'.'.$fileExt;
								}
								else if($_FILES["pic"]["type"][$i] == "image/png")
								{
									$url =$random_name.'.'.$fileExt;
								}
								else if ($_FILES["pic"]["type"][$i] == "image/gif")
								{
									$url =$random_name.'.'.$fileExt;
								}
									
								$url_thumb = $url;
				  				$url = '../uploads/'.$url;
								$url_thumb='../uploads/'.$url_thumb;
								
								if($file_size > MAX_SIZE*1024)
								{	
									$filename = $this->compress_image($_FILES["pic"]["tmp_name"][$i], $url, 20);
									$buffer = file_get_contents($url);
									
								}
								else
								{
									$filename = $this->compress_image($_FILES["pic"]["tmp_name"][$i], $url, 95);
									$buffer = file_get_contents($url);
									}
								unlink($url);
								unlink($url_thumb);
								$queryFolder = "SELECT `name`,`folder` FROM `album` WHERE `id`='$album_id'";
								//echo $queryFolder;
								$resFolder = $this->m_dbConnRoot->select($queryFolder);
								$foldername=$resFolder[0]['folder'];
								
								if(empty($album_id) or empty($file))
								{
									echo "Please Fill all the Fields: <br><br>";
								}
									
								$allowed = array("image/jpeg","image/png", "image/gif");
								if(!in_array($file_type, $allowed )) 
								{
									$this->table .=': Not uploded Only jpg, gif, and png files are allowed.<br>';
									continue;
								}
								else
								{	
									file_put_contents('../uploads/'.$foldername.'/'.$random_name.'.'.$fileExt, $buffer);
									
									 $save = "../uploads/".$foldername.'/thumb/'.$random_name.'.'.$fileExt; 
									 $file='../uploads/'.$foldername.'/'.$random_name.'.'.$fileExt;
									 list($width, $height) = getimagesize($file) ; 
									 if($width >$height )
									 {
									 //$modwidth = $width*0.10; 
									 $modwidth = 160; 
									 $diff = $width / $modwidth;
									//$modheight = $height*0.10; 
									$modheight = 120;
									 }
									 else{
										  $modwidth = 120; 
									 $diff = $width / $modwidth;
									//$modheight = $height*0.10; 
									$modheight = 160;
										 }
          							$tn = imagecreatetruecolor($modwidth, $modheight) ; 
          							$image = imagecreatefromjpeg($file) ; 
          							imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height) ; 
									imagejpeg($tn, $save, 100) ;
									$sql="INSERT INTO `photos`(`album_id`, `url`) VALUES ('$album_id','$random_name.$fileExt')";
									
									$this->errormsg1 ='';
									$data = $this->m_dbConnRoot->insert($sql);
									//$this->table1 .=''.$_FILES['pic']['name'][$i].': uploded<br>';
									$this->table1 .="<tr><td>Record Added Successfully".$_FILES['pic']['name'][$i]."</td></tr>";
									//$error = "Record Added Successfully";
								}		
						}// close else
						else
						{
							$error = "Uploaded image should be jpg or gif or png";
							$this->table1 .= "Uploaded image should be jpg or gif or png";
						}
					
						$this->table .='</table>';
						$this->table .='</table>';
						$this->errormsg = $this->table;
						$this->errormsg1 = $this->table1;
						
						//echo $this->table1;
						//echo "<br>Forloop End : " . $i . '<br/>';
							
				}// for loop
						
				if($error <> '')
				{
					
				return "error";	
				}
					
					
				return "Insert";
				
				}				
		}
		/*//}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{ 
		
		//$sql="INSERT INTO `photos`(`geoup_id`, `album_id`, `url`) VALUES ('$group_id','$album_id','$random_name.jpg')";
			//
			//$up_query="update `soc_group` set `society_id`='".$_POST['society_id']."' where `group_id`='".$_POST['id']."'";
			$data = $this->m_dbConnRoot->insert($sql);
		}
		return "Update";
			


*/
}

/*--------------------------------------image orientation-------------------------------------*/
function image_fix_orientation($filename) {
     $exif = exif_read_data($filename);
	// print_r($exif);
//   die();
    if (!empty($exif['Orientation'])) {
        $image = imagecreatefromjpeg($filename);
        switch ($exif['Orientation']) {
            case 3:
                $image = imagerotate($image, 180, 0);
                break;

            case 6:
                $image = imagerotate($image, -90, 0);
                break;

            case 8:
                $image = imagerotate($image, 90, 0);
                break;
        }
		
		
        imagejpeg($image, $filename, 90);
		//print_r($image);
		//die();
		//return $image;
    }
}		

public function compress_image($source_url, $destination_url, $quality) {

		$info = getimagesize($source_url);
		//print_r($info);
		
		$exif = exif_read_data($source_url);
		// print_r($exif);
    		if ($info['mime'] == 'image/jpeg')
			{	
			
        			$image = imagecreatefromjpeg($source_url);
			}
    		elseif ($info['mime'] == 'image/gif')
			{
        			$image = imagecreatefromgif($source_url);
			}
   		elseif ($info['mime'] == 'image/png')
		{
        			$image = imagecreatefrompng($source_url);

   		}
		
		switch ($exif['Orientation']) {
            case 3:
                $image = imagerotate($image, 180, 0);
                break;

            case 6:
                $image = imagerotate($image, -90, 0);
                break;

            case 8:
                $image = imagerotate($image, 90, 0);
                break;
        }
		
		imagejpeg($image, $destination_url, $quality);
		
		//return $this->image_fix_orientation($destination_url);
		return $image;
	}
	
	
	public function combobox($query, $id)
	{
		$str.="<option value=''>Please Select</option>";
		$data = $this->m_dbConnRoot->select($query);
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
		
			public function combobox1($query, $id)
	{
		$str.="<option value=''>Please Select</option>";
		$data = $this->m_dbConnRoot->select($query);
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
	public function display1($rsas)
	{
		$thheader = array('id','name');
		$this->display_pg->edit		= "getgallery_group";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "gallery_group.php";
		$this->display_pg->view		="getgalleryview";
		$res = $this->display_pg->display_datatable($rsas,true,true,true);
		return $res;
	}
	public function pgnation()
	{
		$sql1 = "select `group_id`,`group_name` from `group` where status='Y'";
		//$sql1 = "select * from `group`" ;
		
		//echo $sql1;
		$result = $this->m_dbConnRoot->select($sql1);
		$thheader = array('Group Name');
		$this->display_pg->edit		= "getgallery_group";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "gallery_group.php";
		$this->display_pg->view		="getgalleryview";
		$res = $this->display_pg->display_datatable($result);
		//$data=$this->display1($result);
		return $data;
	}
	public function selecting()
	{
		$sql = "select * from `group` where group_id='".$_REQUEST['groupId']."'";
		$res = $this->m_dbConnRoot->select($sql);
		return $res;
	}
	public function deleting()
	{
		$sql = "update `group` set status='N' where group_id='".$_REQUEST['groupId']."'";
		$res = $this->m_dbConnRoot->update($sql);
	}
	
	public function get_group_name($bShowPrimary = true)
	{	//$sql = "select `group_id`, `group_name` from `group` where group_id = '".$_REQUEST['group_id']."' ORDER BY group_name ASC";						
		$sql = "select  `group_name` from `group` where group_id = '".$_REQUEST['groupid']."'";		
		$res = $this->m_dbConnRoot->select($sql);			
		if($res<>"")
		{
			$aryResult = array();
			array_push($aryResult,array('success'=>'0'));
			if($bShowPrimary)
			{
				$show_dtl = array("group_id"=>'1', "group_name"=>'Primary');
				array_push($aryResult,$show_dtl);
			}
			foreach($res as $k => $v)
			{
			 	$show_dtl = array("group_id"=>$res[$k]['group_id'], "group_name"=>$res[$k]['group_name']);
				array_push($aryResult,$show_dtl);
			}
			echo json_encode($aryResult);
		}
		else
		{		
			$aryResult = array();
			if($bShowPrimary)
			{			
			$show_dtl = array("group_id"=>'1', "group_name"=>'Primary');
				array_push($aryResult,$show_dtl);
			}
			$show_dtl = array(array("success"=>1), array("message"=>'No Data To Display'));
			array_push($aryResult,$show_dtl);
			echo json_encode($aryResult);
		}

	}
	
	function Rotetion($photoId)
	{
		$sql="SELECT p.id, p.album_id,p.url,a.folder,a.name FROM `photos`as p join album as a on a.id=p.album_id where p.id='".$photoId."'"	;
		$result=$this->m_dbConnRoot->select($sql);
		return $result;
	}
	
	public function sendEmail($groupId,$AlbumName,$AlbumID)
	{	
	
		$sqlSelect="SELECT sg.`society_id`,db.`dbname` FROM `soc_group` as sg join `dbname` as db on sg.society_id= db.society_id where sg.`group_id`='".$groupId."'";
		$result=$this->m_dbConnRoot->select($sqlSelect);
		//print_r($result);
		for($i=0;$i<sizeof($result);$i++)
		{	
			$DatabaseName=$result[$i]['dbname'];
			$societyId=$result[$i]['society_id'];
			$dbConn = new dbop(false, $DatabaseName);
			$obj_utility=new utility($dbConn,$this->m_dbConnRoot);
			$MemberDetails=$this->fetchUnit($societyId,$dbConn);
			$SocietyDetails=$obj_utility->GetSocietyInformation($societyId);
			$societyEMail=$SocietyDetails['email'];
			$societyName=$SocietyDetails['society_name'];
			$societyAddress=$SocietyDetails['society_add'];
			
			for($iCount=0;$iCount<sizeof($MemberDetails);$iCount++)
			{ 
				 $MemberEmail=$MemberDetails[$iCount]['other_email'];
				 $MemberName=$MemberDetails[$iCount]['other_name'];
				$loginExist = "SELECT * FROM `login` WHERE `member_id` = '".$MemberEmail."'";
				$LoginExist=$this->m_dbConnRoot->select($loginExist);
				$encryptedEmail = $obj_utility->encryptData($MemberEmail);
							if(sizeof($LoginExist)==0)
							{
							
								$newUserUrl = "http://way2society.com/newuser.php?reg&u=".$MemberEmail."&n=".$MemberName."&tkn=".$encryptedEmail;
								$onclickURL = $newUserUrl.'&URL=http://way2society.com/Dashboard.php?View=MEMBER';
								$userURL = $newUserUrl.'&url=http://way2society.com/show_photo.php?id='.$AlbumID.'';
								
							}	
							else
							{
								
								//$onclickURL='http://way2society.com/Dashboard.php?View=MEMBER';
								$userURL ='http://way2society.com/show_photo.php?id='.$AlbumID.'';	
							
							}
							
			
		
		$date=date("d-m-Y");
		
		date_default_timezone_set('Asia/Kolkata');
		
		$mailSubject = "[Society Albume #".$AlbumName."]";
		$DBdate = new DateTime($date);
		$new_date_format = $DBdate->format('d-m-Y H:i:s');
		$mailBody = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
					<html xmlns="http://www.w3.org/1999/xhtml">
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />  
					<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
					</head>
					<body style="margin: 0; padding: 0;">					 
					<table align="center" border="1" bordercolor="#CCCCCC" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;">
					<tr>
						<td align="center" bgcolor="#D9EDF7" style="padding: 30px 0 20px 0;border-bottom:none;">
						<img src="http://way2society.com/images/logo.png" alt="Way2Society.com"  style="display: block;" />
						<br />
						<i><font color="#43729F" size="4"><b> Way2Society.com - Housing Society Social & Accounting Software </b></font></i></td>
					</tr>
					<tr>
						<td bgcolor="#ffffff" style="padding-top:20px; padding-bottom:20px; padding-left:10px; padding-right:10px; border-top:none; border-bottom:none;" >
						<table width="100%">
						<tr>
							<td align="center" style="font-size: 15px;font-weight: bold;">'.$societyName.'</td>
   						</tr>
   						<tr>
							<!--<td align="center">'.$societyAddress.'</td>-->
    					</tr>
						</table>
						<table width="100%">
						<tr>
    						<td align="center"><br>Following new photo album has be published.</td> 
						</tr>
						</table>
   						<table width="100%">
						<tr>
    						<td align="center">Date : '.$date.'</td>
    					</tr>
					<!--<tr>
							<td align="center"  style="color:#F00;"><b>Voting line closes on :&nbsp;'.$result[0]['end_date']. '</b></td>
   						</tr> -->          
						<tr>
							<td align="center">
							<div style="width:75%;">
							<table>
							<tr>
	   							 <td bgcolor="aliceblue" width="400" align="center" style="padding-bottom: 10px;padding-top: 10px;">
       							 <b> Album Name : '.$AlbumName.'</b></td>
   							</tr>
  						 	</table>
							</div>
							<table width="160px" cellspacing="0" cellpadding="0" border="0">
							<tr>
    							<td><br /></td>
    						</tr>
							<tbody>
							<tr>
								<td valign="middle" bgcolor="#337AB7" height="40" align="center">
		<a target="_blank" style="color:#ffffff;font-size:14px;text-decoration:none;font-family:Arial,Helvetica,sans-serif" href="'.$userURL.'">Click here to View</a></td>
							</tr>
							<tr>
    							<td><br /></td>
   						 	</tr>
							</tbody>
   						 	</table>
							<table width="250px">
   				 			<tbody>
							</tbody>
							</table>
						</td>
					</tr>
					<tr>
    				<td align="center">If you want to upload photos in album, please send then to society emails</td>
   					</tr>
					<tr>
    				<td align="center">'.$societyEMail.'</td>
   					</tr>
					<tr>
    					<td><br /></td>
   					</tr>
					<tr>
    					<td><br /></td>
    				</tr>
					<tr>
    					<td>If you are a new user, we will take you through a  simple process to create your account. </td>
  			 		</tr>
					<tr>
    					<td><br /></td>
   			 		</tr>
 					</table>
				</td>
			</tr>
			<tr>
				<td bgcolor="#CCCCCC" style="padding: 20px 20px 20px 20px;border-top:none;">
    			<table cellpadding="0" cellspacing="0" width="100%">           
				<td ><a href="http://way2society.com/" target="_blank"><i>Way2Society</i></a></td>
				<td align="right">
				<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td><a href="https://twitter.com/way2society" target="_blank"><img src="http://way2society.com/images/icon2.jpg" alt=""></a></td>
					<td style="font-size: 0; line-height: 0;" width="20">&nbsp;&nbsp;</td>
					<td><a href="https://www.facebook.com/way2soc" target="_blank"><img src="http://way2society.com/images/icon1.jpg" alt=""></a></td>
				</tr>
				</table>
 			</td>             
		</table>
		</td>
	</tr>
	</table>
	</body>
	</html>'	;		
					
							$EMailIDToUse = $obj_utility->GetEmailIDToUse(true,"","","","",$DatabaseName ,$societyId,"","");
							$EMailID = $EMailIDToUse['email'];
							$Password = $EMailIDToUse['password'];

							try
							{		
							
							//$transport = Swift_SmtpTransport::newInstance($host, 465, "ssl")
							
									$transport = Swift_SmtpTransport::newInstance('cs10.webhostbox.net', 465, "ssl")
										->setUsername($EMailID)
										->setSourceIp('0.0.0.0')
										->setPassword($Password) ; 
								
								$message = Swift_Message::newInstance();
								$UserEmails=$MemberEmail;
								
								$message->setTo(array(
								 
								 $MemberEmail => $MemberName
								 
								));	 
							
								$message->setSubject($mailSubject);
								$message->setBody($mailBody);
							
								$message->setFrom('no-reply@way2society.com',$societyName);
								
								$message->setContentType("text/html");										 
					
												
								$mailer = Swift_Mailer::newInstance($transport);
								$resultEmailSend = $mailer->send($message);											
							
								if($resultEmailSend == 1)
								{
									echo 'Success';
								}
								else
								{
									echo 'Failed';
								}	
							}
							catch(Exception $exp)
							{
							
								echo "Error occure in email sending.";
							}
						}
					}
				}	
						
	
	function fetchUnit($societyId,$dbConn)
	{
		
	 //$selectBDName="Select `dbname`,`society_id` from `dbname` where society_id='".$societyId."'";
		//$DBName=$this->m_dbConnRoot->select($selectBDName);
		//if($DBName <> '')
		//{
			//for($iDbname=0;$iDbname<sizeof($DBName);$iDbname++)
			//{
				//$DatabaseName=$DBName[$iDbname]['dbname'];
				//$SocietyID=	$DBName[$iDbname]['society_id'];
								
					$data = $dbConn->select("Select mm.`member_id`,mof.`other_name`,mof.`other_email`,mof.send_commu_emails from `member_main` as mm join `mem_other_family` as mof on mm.member_id=mof.member_id where mm.society_id='".$societyId."' and mof.send_commu_emails='1' and mof.`other_email`!=''");
					
		//}
	//}
	return $data;
		
}

}		
?>
