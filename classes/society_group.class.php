<?php if(!isset($_SESSION)){ session_start(); }
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");

class society_group extends dbop
{
	public $actionPage = "../society_group.php";
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
		if($_REQUEST['insert']=='Create' && $errorExists==0)
		{
			if($_POST['society_id']<>"")
			{
				$sql = "select count(*) as cnt from society_group where grp_name='".addslashes(trim(ucwords($_POST['grp_name'])))."' and status='Y'";
				$res = $this->m_dbConn->select($sql);
				if($res[0]['cnt']==0)
				{
					foreach($_POST['society_id'] as $k => $v)
					{
						$sql1 = "insert into society_group (`my_society_id`,`grp_name`,`society_id`) values ('".$_SESSION['society_id']."','".addslashes(trim(ucwords($_POST['grp_name'])))."','".$v."')";
						$res1 = $this->m_dbConn->insert($sql1);
					}
					
					?>
					<script>window.location.href = '../list_society_group.php?grp&add&tik_id=<?php echo time();?>&nok';</script>
                    <?php
				}
				else
				{
					return 'Already exist this group name';
				}
			}
			else
			{
				return 'All fields marked as * are compulsory';
			}
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			$sql = "select count(*) as cnt from society_group where grp_name='".addslashes(trim(ucwords($_POST['grp_name'])))."' and status='Y'";
			$res = $this->m_dbConn->select($sql);
			
			if($res[0]['cnt']==0)
			{
				//$sql1 = "update society_group set status='N' where grp_name='".addslashes(trim(ucwords($_POST['grp_name_old'])))."' and status='Y'";
				//$res1 = $this->m_dbConn->update($sql1);		
				
				$sql1 = "delete from society_group where grp_name='".addslashes(trim(ucwords($_POST['grp_name_old'])))."' and status='Y'";
				$res1 = $this->m_dbConn->delete($sql1);		
				
				foreach($_POST['society_id'] as $k => $v)
				{
					$sql1 = "insert into society_group (`my_society_id`,`grp_name`,`society_id`) 
							 values ('".$_SESSION['society_id']."','".addslashes(trim(ucwords($_POST['grp_name'])))."','".$v."')";
					$res1 = $this->m_dbConn->insert($sql1);
				}
			}
			else
			{
				
				$sql1 = "delete from society_group where grp_name='".addslashes(trim(ucwords($_POST['grp_name'])))."' and status='Y'";
				$res1 = $this->m_dbConn->delete($sql1);		
				
				foreach($_POST['society_id'] as $k => $v)
				{
					$sql1 = "insert into society_group (`my_society_id`,`grp_name`,`society_id`) 
							 values ('".$_SESSION['society_id']."','".addslashes(trim(ucwords($_POST['grp_name'])))."','".$v."')";
					$res1 = $this->m_dbConn->insert($sql1);
				}
			}
			
			?>
			<script>window.location.href = '../list_society_group.php?grp&up&tik_id=<?php echo time();?>&nok';</script>
            <?php
			//return "Update";
		}
		else
		{
			return $errString;
		}
	}
	public function combobox07($query,$id)
	{
	$str.="<option value=''>All</option>";
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
	public function combobox11($query,$name,$id)
	{
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			$pp = 0;
			foreach($data as $key => $value)
			{
				$i=0;
				
				foreach($value as $k => $v)
				{
					if($i==0)
					{
					?>
					&nbsp;<input type="checkbox" value="<?php echo $v;?>" name="<?php echo $name;?>" id="<?php echo $id;?><?php echo $pp;?>"/>					
					<?php
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
			$pp++;
			}
			?>
			<input type="hidden" size="2" id="count_<?php echo $id;?>" value="<?php echo $pp;?>" />
			<?php
		}
	}
	public function combobox111($query,$name,$id,$sql)
	{
		$s = $sql;
		$r = $this->m_dbConn->select($s);
		
		if($r<>"")
		{
			foreach($r as $t =>$z)
			{
				foreach($z as $m =>$g)
				{
					if($i==0)
					{
						$ee.= $g.",";
					}
				}
			}
		}
		$active = substr($ee,0,-1);
		$ww = explode(",",$active);
		
		
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			$pp = 0;
			foreach($data as $key => $value)
			{
				$i=0;
				
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if(in_array($v,$ww))
						{
							$s = "checked";
						}
						else
						{
							$s = "";
						}

						//$str.="<OPTION VALUE=".$v." ".$s.">";
					?>
					&nbsp;<input type="checkbox" value="<?php echo $v;?>" name="<?php echo $name;?>" id="<?php echo $id;?><?php echo $pp;?>" <?php echo $s;?>/>					
					<?php
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
			$pp++;
			}
			?>
			<input type="hidden" size="2" id="count_<?php echo $id;?>" value="<?php echo $pp;?>" />
			<?php
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
			$thheader=array('Group Name','Society Name');
			$this->display_pg->edit="getsociety_group";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="society_group.php";
			
			//$res = $this->display_pg->display_new($rsas);
			$res = $this->show_list($rsas);
			
			return $res;
	}
	public function pgnation()
	{
		if(isset($_SESSION['admin']))
		{
		$sql1 = "select sg.society_grp_id, sg.my_society_id, s.society_name, sg.grp_name from society_group as sg,society as s where sg.my_society_id=s.society_id and sg.my_society_id='".$_SESSION['society_id']."' and sg.status='Y' and s.status='Y' group by sg.grp_name order by s.society_name, sg.grp_name";
		
		$cntr = "select count(*) as cnt from society_group as sg,society as s where sg.my_society_id=s.society_id and sg.my_society_id='".$_SESSION['society_id']."' and sg.status='Y' and s.status='Y' group by sg.grp_name order by s.society_name, sg.grp_name";
		}
		else
		{
			$sql1 = "select sg.society_grp_id, sg.my_society_id, s.society_id,s.society_name, sg.grp_name from society_group as sg,society as s where sg.my_society_id=s.society_id and sg.status='Y' and s.status='Y' ";
			if($_REQUEST['society_id']<>"")
			{
				$sql1 .= " and s.society_id = '".$_REQUEST['society_id']."'";
			}
				$sql1 .= " group by sg.grp_name order by s.society_name, sg.grp_name";
			
			$cntr = "select count(*)as cnt from society_group as sg,society as s where sg.my_society_id=s.society_id and sg.status='Y' and s.status='Y'";
			if($_REQUEST['society_id']<>"")
			{
				$cntr .= " and s.society_id = '".$_REQUEST['society_id']."'";
			}
				$cntr .= " group by sg.grp_name order by s.society_name, sg.grp_name";
			
		}
		
		$this->display_pg->sql1=$sql1;
		$this->display_pg->cntr1=$cntr;
		$this->display_pg->mainpg="society_group.php";
		$limit="30";
		$page=$_REQUEST['page'];
		$extra = "&society_id=".$_REQUEST['society_id'].'&grp=grp';
		$res=$this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;
	}
	public function show_list($res)
	{
		if($res<>"")
		{
		?>
        <table align="center" border="0">
        <tr height="30" bgcolor="#CCCCCC">
        	<?php if(isset($_SESSION['sadmin'])){?>
            <th width="280">Group Creator Society</th>
            <?php } ?>    
            <th width="240">Group Name</th>
            <th width="130">View Society <br>under this group</th>
        	
            <?php if(!isset($_SESSION['sadmin'])){?>
            <th width="70">Edit</th>
            <th width="70">Delete</th>
            <?php } ?>    
        </tr>
        
        <?php foreach($res as $k => $v){?>
        <tr height="25" bgcolor="#BDD8F4" align="center">
        	<?php if(isset($_SESSION['sadmin'])){?>
            <td align="center"><?php echo $res[$k]['society_name'].$res[$k]['society_id'];?></td>
            <?php } ?>    
            <td align="center"><?php echo $res[$k]['grp_name'];?></td>
            <td align="center"><a href="list_society_group_details.php?grp&id=<?php echo $res[$k]['society_grp_id'];?>&tik_id=<?php echo time();?>&view"><img src="images/view.jpg" width="18" height="15" /></a></td>
        	
             <?php if(!isset($_SESSION['sadmin'])){?>
            <td align="center">
            <a href="../society_group_edit.php?scm&id=<?php echo $res[$k]['society_grp_id'];?>&tik_id=<?php echo time();?>&edit">
            <img src="images/edit.gif" />
            </a>
            </td>
            
            <td align="center">
            <?php if($this->chk_delete_perm_admin()==1){?>
            <a href="javascript:void(0);" onclick="del_group(<?php echo $res[$k]['society_grp_id']?>);"><img src="images/del.gif" /></a>
            <?php }else{?>
            <a href="del_control_admin.php?prm" target="_blank" style="text-decoration:none;"><font color=#FF0000 style='font-size:10px;'><b>Not Allowed</b></font></a>
            <?php }?>
            </td>
            <?php } ?> 
        </tr>
        <?php }?>
        </table>
        <?php	
		}
		else
		{
			?>
            <table align="center" border="0">
            <tr>
            	<td><font color="#FF0000" size="2"><b>No Records Found.</b></font></td>
            </tr>
            </table>
            <?php	
		}		
	}
	
	public function grp_edit($id)
	{
		$sql = "select grp_name,my_society_id from society_group where society_grp_id='".$id."' and status='Y'";
		$res = $this->m_dbConn->select($sql);
		$grp_name = $res[0]['grp_name'];
		
		if($res[0]['my_society_id']==$_SESSION['society_id'])
		{
		$sql1 = "select * from society_group where grp_name='".$grp_name."' and status='Y'";
		$res1 = $this->m_dbConn->select($sql1);
		
		return $res1;
		}
	}
	public function del_group()
	{
		$sql = "select grp_name,my_society_id from society_group where society_grp_id='".$_REQUEST['grp_id']."' and status='Y'";
		$res = $this->m_dbConn->select($sql);
		$grp_name = $res[0]['grp_name'];
		
		if($res[0]['my_society_id']==$_SESSION['society_id'])
		{
			$sql1 = "update society_group set status='N' where grp_name='".$grp_name."' and status='Y'";
			$res1 = $this->m_dbConn->update($sql1);
		}
	}
	
	public function chk_delete_perm_admin()
	{
		$sql = "select * from del_control_admin where status='Y' and login_id='".$_SESSION['login_id']."'";
		$res = $this->m_dbConn->select($sql);
		return $res[0]['del_control_admin'];
	}
	
	public function soc_name($society_id)
	{
		$sql = "select * from society where society_id='".$society_id."' and status='Y'";
		$res = $this->m_dbConn->select($sql);
		echo $res[0]['society_name'];
	}
	
	public function group_in_details($id)
	{
		if($id<>"")
		{
			$sql = "select grp_name from society_group where society_grp_id='".$id."' and status='Y'";
			$res = $this->m_dbConn->select($sql);
			$grp_name = $res[0]['grp_name'];
			
			$sql1 = "select * from society_group where grp_name='".$grp_name."' and status='Y'";
			$res1 = $this->m_dbConn->select($sql1);
			
		?>
        	<table align="center" border="0">
            <tr height="30" bgcolor="#CCCCCC">
            	<th>Group Creator Society</th>
            </tr>
            <tr height="25" bgcolor="#BDD8F4" align="center">
            	<td align="center"><?php $this->soc_name($res1[0]['my_society_id']);?></td>
            </tr>
            <tr height="30" bgcolor="#CCCCCC">
            	<th>Group Name</th>
            </tr>
            <tr height="25" bgcolor="#BDD8F4" align="center">
            	<td align="center"><?php echo $grp_name;?></td>
            </tr>
            <tr height="30" bgcolor="#CCCCCC">
            	<th>Society under this group</th>
            </tr>
            <tr height="25" bgcolor="#BDD8F4" align="center">
            	<td align="center">
                	<table align="center" border="0">
                    <?php $i=1;foreach($res1 as $k => $v){?>
                    <tr>
                    	<td width="20" align="center"><?php echo $i;?> .</td>
                        <td width="200"><?php $this->soc_name($res1[$k]['society_id']);?></td>
                    </tr>
                    <?php $i++;} ?>
                    </table>
                </td>
            </tr>
            </table>
        <?php	
		}
	}
	
	public function check_grp_name()
	{
		$sql = "select count(*)as cnt from society_group where grp_name='".$_REQUEST['grp_name']."' and status='Y'";
		$res = $this->m_dbConn->select($sql);
		
		if($res[0]['cnt']==0)
		{
			echo '0###'.$_REQUEST['grp_name'].'###'.$_REQUEST['grp_name_old'];		
		}
		else
		{
			echo '1###'.$_REQUEST['grp_name'].'###'.$_REQUEST['grp_name_old'];		
		}	
	}
	
	
}
?>