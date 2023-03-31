<?php
include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once ("dbconst.class.php");
include_once("utility.class.php");

class tips 
{
	public $actionPage = "../view_tips.php";
	public $m_dbConn;
	public $m_dbConnRoot;

	function __construct($dbConnRoot,$dbConn)
	{
		$this->display_pg=new display_table();
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->obj_utility=new utility($this->m_dbConnRoot);
	}

	public function startProcess()
	{
		$errorExists = 0;
		
		if($_REQUEST['insert']=='Submit' && $errorExists==0)
		{
			
			//echo $_POST['training_type'];
			$insert_query="insert into `article`  (`atr_title`,`date`,`desc`,`dashboard_key`,`url`) values ('".$_POST['subject']."','".getDBFormatDate($_POST['date'])."','".$_POST['desc']."','".$_POST['type']."','".$_POST['url']."')";
			$data=$this->m_dbConnRoot->insert($insert_query);
			return "Insert";
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			//echo $_POST['training_type'];
			$TID=$_REQUEST['tips_id'];
			$up_query="update  `article`  set `atr_title`='".$_POST['subject']."',`date`='".getDBFormatDate($_POST['date'])."',`desc`='".$_POST['desc']."',`dashboard_key`='".$_POST['type']."',`url`='".$_POST['url']."'  where id='".$TID."'";
			$data = $this->m_dbConnRoot->update($up_query);
			return "Update";
		}
		else
		{
			return $errString;
		}
	}
	public function combobox($query)
	{
		
		$id=0;
		//echo "<script>alert('test')<//script>";
		$str="";
	$data = $this->m_dbConn->select($query);
///print_r($data);
	//echo "<script>alert('test2')<//script>";
		if(!is_null($data))
		{
			$vowels = array('/', '*', '%', '&', ',', '(', ')', '"');
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
						//$str.=$v."</OPTION>";
						 $str.= str_replace($vowels, ' ', $v)."</OPTION>";
					}
					//echo "<script>alert('".$str."')<//script>";
					$i++;
				}
			}
		}
		return $str;
	}
	public function display1($rsas)
	{
		$thheader = array('title','date','end_date','massege');
		$this->display_pg->edit		= "gettips";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "tips.php";

		$res = $this->display_pg->display_new($rsas);
		return $res;
	}
	public function pgnation()
	{
		$sql1 = "select id,`title`,`date`,`end_date`,`massege` from  where status='Y'";
		$cntr = "select count(status) as cnt from  where status='Y'";

		$this->display_pg->sql1		= $sql1;
		$this->display_pg->cntr1	= $cntr;
		$this->display_pg->mainpg	= "tips.php";

		$limit	= "50";
		$page	= $_REQUEST['page'];
		$extra	= "";

		$res	= $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;
	}
	public function selecting($TipsID)
	{
		$sql = "select `id`,`atr_title`,`dashboard_key`,`date`,`desc`,`url` from `article` where id='".$TipsID."'";
		$res=$this->m_dbConnRoot->select($sql);
		$res[0]['date'] = getDisplayFormatDate($res[0]['date']);
		
		return $res;
	}
	public function deleting($TipsID)
	{
		$sql = "update  `article` set status='N' where id='".$TipsID."'";
		$res = $this->m_dbConnRoot->update($sql);
		return $res;
	}
	
	public function getRecords()
	{
		$allTips="select * from `article` where status='Y'";
		$res=$this->m_dbConnRoot->select($allTips);
		return $res;
	}
	public function RecordsCount()
	{
		//print_r($_SESSION['View']);
		if($_SESSION['View']=="ADMIN")
		{
		 $allTips="SELECT * FROM `article` where status='Y' and (dashboard_key='1' OR dashboard_key='3') ORDER BY RAND () ";
		}
		else
		{
			 $allTips="SELECT * FROM `article` where status='Y' and (dashboard_key='1' OR dashboard_key='2') ORDER BY RAND () ";
		}
		$res=$this->m_dbConnRoot->select($allTips);
		
		return $res;
	}
	public function RecordsList($tip_id)
	{
		//print_r($_SESSION['View']);
		if($_SESSION['View']=="ADMIN")
		{
		$allTips="SELECT * FROM article where status='Y' and (dashboard_key='1' OR dashboard_key='3' OR  dashboard_key='0')  ORDER BY id='".$tip_id."' DESC";
		// $allTips="SELECT * FROM `article` where status='Y' and  id='".$tip_id."' and (dashboard_key='1' OR dashboard_key='2')  ";
		}
		else
		{
			$allTips="SELECT * FROM article where status='Y' and (dashboard_key='1' OR dashboard_key='2' OR dashboard_key='0' )  ORDER BY id='".$tip_id."' DESC";
			// $allTips="SELECT * FROM `article` where status='Y' and '".$tip_id."' and (dashboard_key='0' OR dashboard_key='2')  ";
		}
		$res=$this->m_dbConnRoot->select($allTips);
		
		return $res;
	}
}
?>