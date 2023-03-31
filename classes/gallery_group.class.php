<?php
include_once("include/display_table.class.php");
include_once("defaults.class.php");
include_once("dbconst.class.php");
//include_once('../swift/swift_required.php');
//include_once( "include/fetch_data.php");
class gallery_group
{
	public $actionPage = "../gallery_group.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	
	function __construct($dbConn, $dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		
		$this->display_pg=new display_table($this->m_dbConnRoot);
		//dbop::__construct();
	}
	public function startProcess()
	{
		$errorExists = 0;

if($_POST['insert']=='insert'  && $errorExists==0)
		{ //echo "how";
			if($_POST['group'])
			{// echo "are";('" . $_POST['group']."')"
				$insert_group_root= "INSERT INTO `group`(`group_name`,`created_by`) VALUES ('".addslashes(trim(ucwords($_POST['group'])))."','".$_SESSION['login_id']."')";
				//echo $insert_group_root;		
				$result_group_id = $this->m_dbConnRoot->insert($insert_group_root);
						
						
			$groupToArray = array();
				$group=$_POST['society_id'];
			//print_r($group);
				//echo 'group_id'.$_POST['group_id'];
				if ($group)
				{
					foreach ($group as $value)
					{
						array_push($groupToArray,$value);
					}
				}
				
				if(!empty($groupToArray))
				{
					for($i=0;$i<sizeof($groupToArray);$i++)
					{
						$sqldata="insert into `soc_group`(`group_id`,`society_id`) values('".$result_group_id."',".$groupToArray[$i].")";						
						$data=$this->m_dbConnRoot->insert($sqldata);
					}
				}
				
			}
				
			return "Insert";
		}
	else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{ 
		
		$up_query="update `group` set `group_name`='".$_POST['group']."' where `group_id`='".$_POST['id']."'";
			//
			//$up_query="update `soc_group` set `society_id`='".$_POST['society_id']."' where `group_id`='".$_POST['id']."'";
			$data = $this->m_dbConnRoot->update($up_query);
		
				 		
			$societyToArray = array();
				$group=$_POST['society_id'];
				
		print_r($groupToArray);
				//echo 'group_id'.$_POST['id'];
				if ($group)
				{
					//print_r($groupToArray);
					foreach ($group as $value)
					{
						array_push($societyToArray,$value);
					}
				}
				
				//delete query
				$sqldata="delete from `soc_group` where group_id='".$_POST['id']."'";
				$data=$this->m_dbConnRoot->delete($sqldata);
				if(!empty($societyToArray))
				{
					for($i=0;$i<sizeof($societyToArray);$i++)
					
					{	//echo "update `soc_group` set `society_id`='".$_POST['society_id']."' where `society_id`='".$groupToArray[$i]."'";
						// $sqldata= "update `soc_group` set `society_id`='".$societyToArray[$i]."' where `group_id`='".$_POST['id']."'";
						 $sqldata="insert into `soc_group`(`group_id`,`society_id`) values('".$_POST['id']."','".$societyToArray[$i]."')";			
					//$sqldata="update `soc_group` set `society_id`='".$_POST['society_id']."' where `society_id`='".$societyToArray[$i]."'";
						//$sqldata="update soc_group set `society_id`=('".$result_group_id."',".$groupToArray[$i].") where society_id in (select society_id from society)";
						//$sqldata="update `soc_group` set `society_id`=('".$result_group_id."',".$groupToArray[$i].")";
						
						//$sqldata="update soc_group set `society_id`='".$_POST['society_id']."' where group_id='".$_POST['id']."'";
									
						$data=$this->m_dbConnRoot->update($sqldata);
					}
				}
				
			}
					
			return "Update";
		}
		
	public function combobox11($query ,$name, $id, $group_id)
	{ 
		//echo $query;
		$data = $this->m_dbConnRoot->select($query);
		//print_r($data);
		if(!is_null($data))
		{
			$str = 0;
			foreach($data as $key => $value)
			{
				$i=0;
				
				foreach($value as $k => $v)
				{
					//$data = $this->m_dbConnRoot->select($query);
					if($i==0)
					{
						$result = $this->check($group_id,$v);
					
						if($result == true)
						{
							
					?>
					&nbsp;<input type="checkbox" value="<?php echo $v;?>" name="<?php echo $name;?>" id="<?php echo $id;?><?php echo $pp;?>" checked="checked"/>	
                    				
					<?php
						}
						else
						{
							?>
					&nbsp;<input type="checkbox" value="<?php echo $v;?>" name="<?php echo $name;?>" id="<?php echo $id;?><?php echo $pp;?>" />					
					<?php
						}
                    }
					else
					{
					echo $v;
					?>
						<br />
					<?php
					}
					$i++;
				}
			$str++;
			
			}
			?>
	<input type="hidden" size="2" id="count_<?php echo $id;?>" value="<?php echo $pp;?>" />
			<?php
		}
	}

public function check($group_id,$society_id)
{ 
	//$sql1="select group_id FROM `soc_group` where gruop_id=$group_id AND society=$society_id";
	$sqldata="SELECT count(soc_group.society_id) as cnt FROM `soc_group` where soc_group.group_id = '" . $group_id . "' and soc_group.society_id = '" . $society_id . "'";
		
	$res = $this->m_dbConnRoot->select($sqldata);
	
	$resultData = $res[0]['cnt'];
	
	$result = false;
	if($resultData > 0)
	{
		$result = true;
	}
	return $result;
}

	public function display1($rsas)
	{
		$thheader = array('group_id','group_name');
		$this->display_pg->edit		= "getgallery_group";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "gallery_group.php";
		$this->display_pg->view		="getgalleryview";
		$res = $this->display_pg->display_datatable($rsas,true,true,true);
		return $res;
	}
	public function pgnation()
	{
		if($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN)
		{
			//$sql1 = "select `group_id`,`group_name` from `group` where status='Y'";
			 $sql1= "select g.`group_id`, g.`group_name` from `group` as g JOIN `soc_group` as s ON g.`group_id` = s.`group_id` Join `society` as c on s.`society_id` = c.`society_id`  where s.`status`='Y' and g.`status`='Y' and  c.`client_id` = '" .  $_SESSION['society_client_id'] . "' and c.society_id ='".$_SESSION['society_id']."'";
		}
		else
		{
			$sql1 = "select `group_id`,`group_name` from `group` where status='Y' AND created_by = '" . $_SESSION['login_id'] . "'";
		}
		
	   	
		$result = $this->m_dbConnRoot->select($sql1);
		$thheader = array('Group Name','Create Album','Create Poll');
		$this->display_pg->edit		= "getgallery_group";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "gallery_group.php";
		$this->display_pg->view		="getgalleryview";
		
		
		for($i=0;$i<sizeof($result);$i++)
		{
			$result[$i]['Create'] = '<a href="gallery_upload.php?pg=3">Create Album</a>';
			
			
			
			 $sql="SELECT count(group_id) as cnt FROM `album` where `group_id`='".$result[$i]['group_id']."'";
			
			$res = $this->m_dbConnRoot->select($sql);
			$result[$i]['Create'] = '<a href="gallery_upload.php?pg=3&group_id='.$result[$i]['group_id'].'">Create Album</a>';
			//$result[$i]['Create'] = '<a href="gallery_upload.php?pg=3&grp='.$result[$i]['group_id'].'">Create Album</a>';
			//echo "result".$res;
			//$resultData = $res[0]['cnt'];
	
			/*if($resultData==0){
				$result[$i]['Manage'] = '<a href="gallery_upload.php?pg=3">Manage Album</a>';	
				
							//$result[$i]['Manage'] = '<a href="gallery_upload.php?pg=5&grp='.$result[$i]['group_id'].'">Manage Album</a>';
				}
			else{
				$result[$i]['Manage'] = '<a href="gallery_upload.php?pg=5&grp='.$result[$i]['group_id'].'">Manage Album</a>';
				}*/
				 $sql="SELECT count(group_id) as cnt FROM `poll_question` where `group_id`='".$result[$i]['group_id']."'";
				 $res = $this->m_dbConnRoot->select($sql);
				 //$resultData = $res[0]['cnt'];
				 //if($resultData==0)
				 //{
		         $result[$i]['Create Poll'] ='<a href="create_poll.php?group_id='.$result[$i]['group_id'].'">Create Poll</a>';		}
				//}?>
	 <?php
		//$queryFolder = "SELECT a.`id`, a.`name`,a.`folder` FROM `album` as a JOIN `soc_group` as g ON a.group_id = g.group_id where g.society_id = '" .$_SESSION['society_id'] . "'";
		//$res1 = $m_dbConnRoot->select($queryFolder);
		/*for($j=0;$j<sizeof ($res1);$j++)
		{
		$result[$j]['link']='<a href="gallery_upload.php?id">manage</a>';
		}*/
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
}
?>
