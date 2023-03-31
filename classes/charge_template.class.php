<?php
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");

class charge_template extends dbop
{
	public $actionPage = "../charge_template.php";
	public $m_dbConn;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
$this->display_pg=new display_table($this->m_dbConn);
		//dbop::__construct();
	}
	public function startProcess()
	{
		$errorExists=0;
		if($_REQUEST['insert']=='Insert' && $errorExists==0)
		{
			if($_POST['socity_id']<>"" && $_POST['charge_template_name']<>"")
			{
				$sql = "select count(*)as cnt from charge_template where socity_id='".$_POST['socity_id']."' and status='Y'";
				$res = $this->m_dbConn->select($sql);
				
				if($res[0]['cnt']==0)
				{
					$insert_query="insert into charge_template (`socity_id`,`charge_template_name`,`charge_template_desc`) values ('".$_POST['socity_id']."','".addslashes(trim(ucwords($_POST['charge_template_name'])))."','".addslashes(trim(ucwords($_POST['charge_template_desc'])))."')";
					$data=$this->m_dbConn->insert($insert_query);
					return "Insert";
				}
				else
				{
					return "Already Exist";
				}
			}
			else
			{
				return "* Field Should not be blank.";
			}
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			if($_POST['socity_id']<>"" && $_POST['charge_template_name']<>"")
			{
				$up_query="update charge_template set `socity_id`='".$_POST['socity_id']."',`charge_template_name`='".addslashes(trim(ucwords($_POST['charge_template_name'])))."',`charge_template_desc`='".addslashes(trim(ucwords($_POST['charge_template_desc'])))."' where charge_template_id='".$_POST['id']."'";
				$data=$this->m_dbConn->update($up_query);
				return "Update";
			}
			else
			{
				return "* Field Should not be blank.";
			}
		}
		else
		{
			return $errString;
		}
	}
	public function combobox($query)
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
	public function display1($rsas)
	{
			$thheader=array('Socity Name','Charge Template Name','Charge Template Desc');
			$this->display_pg->edit="getcharge_template";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="charge_template.php";
			$res=$this->display_pg->display_new($rsas);
			return $res;
	}
	public function pgnation()
	{
			$sql1 = "select ct.charge_template_id,s.society_name,ct.charge_template_name,ct.charge_template_desc from charge_template as ct , society as s where ct.status='Y' and s.status='Y'";
			$cntr = "select count(*) as cnt from charge_template as ct , society as s where ct.status='Y' and s.status='Y'";
			
			$this->display_pg->sql1=$sql1;
			$this->display_pg->cntr1=$cntr;
			$this->display_pg->mainpg="charge_template.php";
			$limit="5";
			$page=$_REQUEST['page'];
			$extra="";
			$res=$this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
			return $res;
	}
	public function selecting()
	{
			$sql1="select charge_template_id,`socity_id`,`charge_template_name`,`charge_template_desc` from charge_template where charge_template_id='".$_REQUEST['charge_templateId']."'";
			$var=$this->m_dbConn->select($sql1);
			return $var;
	}
	public function deleting()
	{
			$sql1="update charge_template set status='N' where charge_template_id='".$_REQUEST['charge_templateId']."'";
			$this->m_dbConn->update($sql1);
	}
}
?>