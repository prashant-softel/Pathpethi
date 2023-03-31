<?php
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");

class account_category extends dbop
{
	public $actionPage = "../account_category.php";
	public $m_dbConn;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);

		/*//$this->curdate		= $this->display_pg->curdate();
		//$this->curdate_show	= $this->display_pg->curdate_show();
		//$this->curdate_time	= $this->display_pg->curdate_time();
		//$this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);*/

		////dbop::__construct();
	}

	public function startProcess()
	{
		$errorExists = 0;

		/*//$curdate 		=  $this->curdate;
		//$curdate_show	=  $this->curdate_show;
		//$curdate_time	=  $this->curdate_time;
		//$ip_location	=  $this->ip_location;*/
		
		if($_REQUEST['insert']=='Insert' && $errorExists==0)
		{			
			$count_query = "select count(category_name) as count from account_category where category_name='".$_POST['category_name']."'";	
			$res = $this->m_dbConn->select($count_query);
			//echo $res[0]['count'];
			if($res[0]['count'] <= 0)
			{
				if($_POST['group_id']<>"" && $_POST['parentcategory_id']<>"" && $_POST['category_name']<>"") 
				{
				$insert_query="insert into account_category (`srno`,`group_id`,`parentcategory_id`,`category_name`,`description`,`enteredby`,`is_fd_category`) values ('".$_POST['srno']."','".$_POST['group_id']."','".$_POST['parentcategory_id']."','".$_POST['category_name']."','".$_POST['description']."','".$_SESSION['login_id']."','".$_POST['is_fd_category']."')";
				//echo $insert_query;
				$data = $this->m_dbConn->insert($insert_query);
				return "Insert";
				}
				else
				{
					return "All * Field Required";											
				}
			}
			else
			{
				return "Data already exist";		
			}
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			$up_query="update account_category set `srno`='".$_POST['srno']."',`group_id`='".$_POST['group_id']."',`parentcategory_id`='".$_POST['parentcategory_id']."',`category_name`='".$_POST['category_name']."',`description`='".$_POST['description']."',`enteredby`='".$_SESSION['login_id']."' ,`is_fd_category` = '".$_POST['is_fd_category']."'  where category_id='".$_POST['id']."'";
			$data = $this->m_dbConn->update($up_query);
			return "Update";
		}
		else
		{
			return $errString;
		}
	}
	
	public function combobox($query, $id)
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
	
	public function display1($rsas)
	{
		$thheader = array('srno','group_id','parentcategory_id','category_name','description');
		$this->display_pg->edit		= "getaccount_category";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "account_category.php";

		$res = $this->display_pg->display_datatable($rsas);
		return $res;
	}
	public function pgnation()
	{
		//$sql1 = "select Account.category_id,Account.srno,GroupTable.groupname,Account.parentcategory_id,Account.category_name,Account.description from `account_category` as `Account` join `group` as `GroupTable` on Account.group_id = GroupTable.id where Account.status='Y'";
		//$sql1 = "select Account.category_id,Account.srno,GroupTable.groupname,Account1.category_name as 'parent_category_ID',Account.category_name,Account.description from `account_category` as `Account`, `account_category` as `Account1`, `group` as `GroupTable` where Account.group_id = GroupTable.id and Account.parentcategory_id = Account1.category_id";
		$sql1 = "select Account.category_id,Account.srno,GroupTable.groupname,Account1.category_name as 'parent_category_ID',Account.category_name,Account.description,IF(Account.is_fd_category  = 1, '<i class=\'fa  fa-check\'  style=\'font-size:10px;font-size:1.75vw;color:#6698FF;\'></i>', '') as  is_fd_category  from `account_category` as `Account`, `account_category` as `Account1`, `group` as `GroupTable` where Account.group_id = GroupTable.id and Account.parentcategory_id = Account1.category_id and Account.status = 'Y' Order By GroupTable.id,Account1.category_id " ;
		
		/*$cntr = "select count(status) as cnt from account_category where status='Y'";

		$this->display_pg->sql1		= $sql1;
		$this->display_pg->cntr1	= $cntr;
		$this->display_pg->mainpg	= "account_category.php";

		$limit	= "50";
		$page	= $_REQUEST['page'];
		$extra	= "";

		$res	= $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;
		*/
		$result = $this->m_dbConn->select($sql1);
		$thheader = array('SrNo','Group','Parent Category','Category','Description','FD Category');
		$this->display_pg->edit		= "getaccount_category";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "account_category.php";
		$res = $this->display_pg->display_datatable($result);
		//$data=$this->display1($result);
		return $data;
	}
	public function selecting()
	{
		$sql = "select category_id,`srno`,`group_id`,`parentcategory_id`,`category_name`,`description`,`is_fd_category` from account_category where category_id='".$_REQUEST['account_categoryId']."'";
		$res = $this->m_dbConn->select($sql);
		return $res;
	}
	public function deleting()
	{
		 $sql = "update account_category set status='N' where category_id='".$_REQUEST['account_categoryId']."'";
		$res = $this->m_dbConn->update($sql);
	}
	
	public function get_category_name($bShowPrimary = true)
	{						
		$sql = "select `category_id`, `category_name` from `account_category` where group_id = '".$_REQUEST['groupid']."'";		
		$res = $this->m_dbConn->select($sql);			
		if($res<>"")
		{
			$aryResult = array();
			array_push($aryResult,array('success'=>'0'));
			if($bShowPrimary)
			{
				$show_dtl = array("id"=>'1', "category"=>'Primary');
				array_push($aryResult,$show_dtl);
			}
			foreach($res as $k => $v)
			{
			 	$show_dtl = array("id"=>$res[$k]['category_id'], "category"=>$res[$k]['category_name']);
				array_push($aryResult,$show_dtl);
			}
			echo json_encode($aryResult);
		}
		else
		{		
			$aryResult = array();
			if($bShowPrimary)
			{			
				$show_dtl = array("id"=>'1', "category"=>'Primary');
				array_push($aryResult,$show_dtl);
			}
			$show_dtl = array(array("success"=>1), array("message"=>'No Data To Display'));
			array_push($aryResult,$show_dtl);
			echo json_encode($aryResult);
		}

	}
}
?>