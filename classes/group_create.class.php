<?php
include_once("include/display_table.class.php");

include_once ("dbconst.class.php"); 
include_once( "include/fetch_data.php");

include_once('../swift/swift_required.php');


class group_create extends dbop
{
	public $actionPage = "../group_create.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	function __construct($dbConn,$dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->display_pg=new display_table();

		//$this->curdate		= $this->display_pg->curdate();
		//$this->curdate_show	= $this->display_pg->curdate_show();
		//$this->curdate_time	= $this->display_pg->curdate_time();
		//$this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);

		dbop::__construct();
	}

	public function startProcess()
	{
		echo "startProcess";
		$errorExists = 0;

		//$curdate 		=  $this->curdate;
		//$curdate_show	=  $this->curdate_show;
		//$curdate_time	=  $this->curdate_time;
		//$ip_location	=  $this->ip_location;

		if($_REQUEST['insert']=='Insert' && $errorExists==0)
		{
			
				$groupToArray = array();
				$group=$_POST['society_id'];
			//print_r($group);
				echo 'group_id'.$_POST['group_id'];
				if ($group)
				{
					foreach ($group as $value)
					{
						array_push($groupToArray,$value);
					}
				}
				
			//$insert_group="insert into `soc_group` (`group_id`,`society_id`) values ('".$_POST['group_id']."','".$_POST['society_id']."')";
			//echo $insert_query;
			
			//$res=$this->m_dbConnRoot->insert($insert_group);
				
				for($i=0;$i<sizeof($groupToArray);$i++)
				{
					if($groupToArray[$i]==0)
					{
						$sqldata="insert into `soc_group`(`group_id`,`society_id`) values('".$_POST['group_id']."',".$groupToArray[$i].")";						
						$data=$this->m_dbConnRoot->insert($sqldata);
					}
					else
					{
						$sqldata="insert into `soc_group`(`group_id`,`society_id`) values('".$_POST['group_id']."',".$groupToArray[$i].")";						
						$data=$this->m_dbConnRoot->insert($sqldata);
					}	
					
				}
				
			return "Insert";
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			$up_query="update group set `group_name`='".$_POST['group_id']."',`society_name`='".$_POST['society_id']."' where id='".$_POST['id']."'";
			$data = $this->update($up_query);
			return "Update";
		}
		else
		{
			return $errString;
		}
	}
		public function combobox($query,$id, $defaultText = 'Please Select', $defaultValue = '')
	{
		$str = '';
		
		if($defaultText != '')
		{
			$str .= "<option value='" . $defaultValue . "'>" . $defaultText . "</option>";
		}
		
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

	public function get_category_name()
{						
	$sql = "select `group_id`, `group_name` from `group` where group_id = '".$_REQUEST['group_id']."' ORDER BY group_name ASC";		
	$res = $this->m_dbConnRoot->select($sql);			
	if($res<>"")
	{
		$aryResult = array();
		array_push($aryResult,array('success'=>'0'));
		$show_dtl = array("id"=>'1', "group"=>'Primary');
		array_push($aryResult,$show_dtl);
		foreach($res as $k => $v)
		{
			$show_dtl = array("id"=>$res[$k]['group_id'], "group"=>$res[$k]['group_name']);
			array_push($aryResult,$show_dtl);
		}
		echo json_encode($aryResult);
	}
	else
	{		
		$aryResult = array();			
		$show_dtl = array("id"=>'1', "group"=>'Primary');
		array_push($aryResult,$show_dtl);
		$show_dtl = array(array("success"=>1), array("message"=>'No Data To Display'));
		array_push($aryResult,$show_dtl);
		echo json_encode($aryResult);
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
		$thheader = array('group_name','society_name');
		$this->display_pg->edit		= "getgroup_create";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "group_create.php";

		$res = $this->display_pg->display_new($rsas);
		return $res;
	}
	public function pgnation()
	{
		
		
$sql1="SELECT society.society_name,group.group_name FROM `soc_group` JOIN `society` ON (society.society_id = soc_group.society_id) JOIN `group` ON (group.group_id = soc_group.group_id)";// where soc_group.status='Y'";
		//$sql1 = "select id,`group_name`,`society_name` from `group` where status='Y'";
		//$cntr = "select count(status) as cnt from `group` where status='Y'";

$result = $this->m_dbConnRoot->select($sql1);
		$thheader = array('society_name','group_name');
		$this->display_pg->edit		= "getgroup_create";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "group_create.php";
		$res = $this->display_pg->display_datatable($result);

		/*$this->display_pg->sql1		= $sql1;
		$this->display_pg->cntr1	= $cntr;
		$this->display_pg->mainpg	= "group_create.php";

		$limit	= "50";
		$page	= $_REQUEST['page'];
		$extra	= "";

		$res = $this->display_pg->display_datatable($result)*/;
		return $res;
	}
	public function selecting()
	{
		/*"SELECT
   group.group_name, society.society_name
FROM
   (SELECT DISTINCT Code FROM group) group
   FULL OUTER JOIN
   (SELECT DISTINCT Code FROM society) society
              ON group.group_name = society.society_name"; 
		*/
		$sql ="select id,`group_name`,`society_name` from `group` where id='".$_REQUEST['group_createId']."'";
		$res = $this->select($sql);
		return $res;
	}
	public function deleting()
	{
		$sql = "update group set status='N' where id='".$_REQUEST['group_createId']."'";
		$res = $this->update($sql);
	}
}
?>

