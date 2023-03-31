<?php
include_once("include/display_table.class.php");

include_once ("dbconst.class.php"); 
include_once( "include/fetch_data.php");

include_once('../swift/swift_required.php');

class show_album 
{
	
	public $actionPage = "../show_gallery.php";
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
	if($_REQUEST['insert']=='Insert' && $errorExists==0)

		//$curdate 		=  $this->curdate;
		//$curdate_show	=  $this->curdate_show;
		//$curdate_time	=  $this->curdate_time;
		//$ip_location	=  $this->ip_location;

//echo "how";
if($_POST['insert']=='')
		{ //echo "how";
			if($_POST['name'])
			{ //echo "test";
				$insert_album= "INSERT INTO `album`(`name`) VALUES ('".$_POST['name']."')";
				//echo $insert_album;		
				$result_show = $this->m_dbConnRoot->insert($insert_album);
			}
			
			
			return "Insert";
		}
		
		
	else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{ 
		
		//$sql="INSERT INTO `photos`(`geoup_id`, `album_id`, `url`) VALUES ('$group_id','$album_id','$random_name.jpg')";
			//
			//$up_query="update `soc_group` set `society_id`='".$_POST['society_id']."' where `group_id`='".$_POST['id']."'";
			$data = $this->m_dbConnRoot->insert($sql);
		}
			return "Update";
		}
		
		public function combobox($query, $id)
	{
		
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
}
?>
