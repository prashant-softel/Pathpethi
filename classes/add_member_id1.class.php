<?php if(!isset($_SESSION)){ session_start(); }
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once("dbconst.class.php");

class add_member_id
{
	public $actionPage = "../add_member_id1.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	
	function __construct($dbConn, $dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->display_pg=new display_table($this->m_dbConnRoot);
		////dbop::__construct();
	}
	public function startProcess()
	{
		$errorExists=0;
		if($_POST['insert']=='Insert' && $errorExists==0)
		{
			if($_POST['com_id']<>"" && $_POST['member_id']<>"" && $_POST['password']<>"")
			{
				$sql = "select count(*)as cnt from login where com_id='".$_POST['com_id']."' and status='Y'";
				$res = $this->m_dbConnRoot->select($sql);
				
				$sql22 = "select count(*)as cnt22 from login where member_id='".$_POST['member_id']."' and status='Y'";
				$res22 = $this->m_dbConnRoot->select($sql22);

				if($res[0]['cnt']==0 && $res22[0]['cnt22']==0)
				{		
					$sql1 = "select owner_name from member_main where member_id='".$_POST['com_id']."' and status='Y'";
					$res1 = $this->m_dbConnRoot->select($sql1);
					$name = $res1[0]['owner_name'];
					
					$insert_query = "insert into login (`society_id`,`com_id`,`member_id`,`password`,`authority`,`name`) values ('".$_SESSION['society_id']."','".$_POST['com_id']."','".$_POST['member_id']."','".$_POST['password']."','".$_SESSION['login_id']."','".$name."')";
					$data=$this->m_dbConnRoot->insert($insert_query);
					return "Insert";
				}
				else
				{
					return "Already exist";
				}
			}
			else
			{
				return "Some * field is missing";
			}	
		}
		else if($_POST['insert']=='Update' && $errorExists==0)
		{
			if($_POST['com_id']<>"" && $_POST['member_id']<>"" && $_POST['password']<>"")
			{
				$sql1 = "select owner_name from member_main where member_id='".$_POST['com_id']."' and status='Y'";
				$res1 = $this->m_dbConnRoot->select($sql1);
				$name = $res1[0]['owner_name'];
					
				$up_query="update login set `com_id`='".$_POST['com_id']."',`member_id`='".$_POST['member_id']."',`password`='".$_POST['password']."',name='".$name."' where login_id='".$_POST['id']."'";
				$data=$this->m_dbConnRoot->update($up_query);
				return "Update";
			}
			else
			{
				return "Some * field is missing";
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
	public function combobox07($query,$id)
	{
	$str.="<option value=''>All</option>";
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
			$thheader=array('Login ID', 'Description','Code', 'Role', 'Status');
			$this->display_pg->edit="getadd_member_id1";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="add_member_id1.php";
			$res=$this->show($rsas);
			return $res;
	}
	public function pgnation()
	{
		$sql1 = "Select societytbl.society_name, maptbl.id, maptbl.login_id, maptbl.desc, maptbl.code, maptbl.role, maptbl.status, maptbl.unit_id ,maptbl.view from mapping as maptbl JOIN society as societytbl ON maptbl.society_id = societytbl.society_id and maptbl.society_id = '" . $_SESSION['society_id'] . "' order by maptbl.sort_order";

		/*$cntr = "select count(id) as cnt from mapping where society_id = '" . $_SESSION['society_id'] . "'";
		
		$this->display_pg->sql1=$sql1;
		$this->display_pg->cntr1=$cntr;
		$this->display_pg->mainpg="add_member_id.php";
		$limit="20";
		$page=$_REQUEST['page'];
		$extra = "";
				
		$res=$this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;*/
		$result = $this->m_dbConnRoot->select($sql1);
		return $result;
	}
	
	public function show($res)
	{
		if($res<>"")
		{
			if(!isset($_REQUEST['page']))
			{
				$_REQUEST['page'] = 1;
			}
			$iCounter = 1 + (($_REQUEST['page'] - 1) * 20);
			?>
            <!--<table align="center" border="0">-->
            <input type="button" id="SendAllTop" name="SendAll" onClick="SendAll()" style="text-align:center;float:right;display:block;margin: 1%;font-size: 14px;" value="Send Email to All Selected"/><b style="font-size:20px;;float:right;margin-top:5px">Select All Inactive Units</b><input type="checkbox" id="checkall" name="checkall" onclick="ToggleCheckAll()" style="zoom:2;float:right"></input>
            
           	<table id="example" class="display" cellspacing="0" width="100%">
			<thead>
            <tr height="30">
            	<th>Sr No</th>
                <th>Name</th>
                <th>Desc/Unit</th>
                <th>E-Mail</th>
                <th>Code</th>
                <th>Role</th>
                <th>Status</th>
				<th>Edit Role</th>
                 <th>Activation Link</th>
                 <th>Invite (check it) </th>
                 <th>Status</th>
            </tr>	
            </thead>
             <tbody>
            <?php
				foreach($res as $k => $v)
				{
					$memberDetails = $this->getMemberInfo($res[$k]['login_id']);
					$memberProfile = $this->getMemberProfile($res[$k]['unit_id']);
					
					?>
                    <tr height="25" bgcolor="#BDD8F4">
                    	<td align="center"><?php echo $iCounter++; ?></td>
                        <?php if($res[$k]['role'] == ROLE_SUPER_ADMIN || $res[$k]['role'] == ROLE_ADMIN)
						{
							$name = $memberDetails[0]['name'];
							?>
                        		<td align="center"><?php if($memberDetails <> '') { echo $memberDetails[0]['name']; }?></td>
                            <?php
						}
						else
						{
							$name =  $memberProfile[0]['owner_name'];
							?>
                        		<td align="center"><?php if($memberProfile <> '') { echo $memberProfile[0]['owner_name']; }?></td>
                            <?php
						}
						?>
                        <td align="center"><?php echo $res[$k]['desc'];?></td>
                        <td align="center"><?php if($memberProfile <> '') { echo $memberProfile[0]['email']; } else { echo $memberDetails[0]['member_id']; }?></td>
                        <td align="center"><p><?php echo $res[$k]['code'];?></p></td>
                        <td align="center"><?php echo $res[$k]['role'];?></td>
                        <td align="center"><?php  echo ($res[$k]['status'] == '1') ? 'Inactive' : (($res[$k]['status'] == '2') ? 'Active' : 'Deleted');?></td>
						<?php 
						if($res[$k]['role'] != ROLE_SUPER_ADMIN)
						{
							?>
							<td align="center"><a href="updateuser.php?id=<?php echo $res[$k]['id'] ?>"><img src="images/edit.gif" /></a></td>
                            <?php if($res[$k]['status'] == 1 && $res[$k]['view'] == 'MEMBER'){?>
                             <td align="center"><a onclick="emailPromtWindow(<?php echo "'".$memberProfile[0]['email']."'" ?>,<?php echo "'".$name."'" ?>,<?php echo "'".$res[$k]['code']."'" ?>);" ><i class='fa  fa-envelope-o'  style='font-size:10px;font-size:1.25vw;color:#F00;' ></i></a></td>
							<td align="center"><input type="checkbox" id="<?php echo $memberProfile[0]['email'] ?>" class="chckbxUnits" name="<?php echo $name ?>"  
                             style='font-size:10px;font-size:1.25vw;color:#F00;' /></td>
									 <?php  if($memberProfile[0]['email'] != "")
                                     {
                                    ?>	 
                                     <td align="center"> <div id="lbl<?php echo $memberProfile[0]['email'] ?>" name="lbl" class="lblStatus"></div>
                                     </td>
                                     <?php
									 }
									 else
									 {
										 ?>
                                         <td align="center"> <div id="lbl<?php echo $memberProfile[0]['email'] ?>" name="lbl" class="lblStatus"></div>
                                     </td>
										 <?php
										 
									 }
									 ?>
                              
							<?php
							}
							else
							{?>
                            	<td></td>
                                <td></td>
                                <td></td>
                         <?php
							}
						}
						else
						{
						?>
                        	<td></td>
                            <td></td>
                            <td></td>
                            <td></td>
							<?php
						}
						?>
                       
                    </tr>
                    <?php	
				}
			?>
            </tbody>
            </table>
            </div>
            <?php
		}
		else
		{
			?>
				<center><font color="#FF0000"><b>No Records Found</b></font></center>
			<?php
		}
	}
	
	public function chk_delete_perm_admin()
	{
		$sql = "select * from del_control_admin where status='Y'";
		$res = $this->m_dbConnRoot->select($sql);
		return $res[0]['del_control_admin'];
	}
	
	public function selecting()
	{
		$sql1 = "select login_id,`com_id`,`member_id`,`password` from login where login_id='".$_REQUEST['add_member_idId']."'";
		$var=$this->m_dbConnRoot->select($sql1);
		return $var;
	}
	public function deleting()
	{
		$sql1="update login set status='N' where login_id='".$_REQUEST['add_member_idId']."'";
		$this->m_dbConnRoot->update($sql1);
	}
	public function get_mem_info()
	{
		
		$sql0 = "select count(*)as cnt from login where com_id='".$_REQUEST['com_id']."' and status='Y'";
		$res0 = $this->m_dbConnRoot->select($sql0);	
		
		if($res0[0]['cnt']==1)
		{
			$sql = "select * from login where com_id='".$_REQUEST['com_id']."' and status='Y'";
			$res = $this->m_dbConnRoot->select($sql);	
			
			echo '1#'.$res[0]['login_id'].'#'.$res[0]['member_id'].'#'.$res[0]['password'];
		}
		else
		{
			echo '0#kk';
		}
	}
	
	public function getMemberInfo($login_id)
	{
		$sql = "Select * from login where login_id = '" . $login_id . "'";
		$result = $this->m_dbConnRoot->select($sql);
		
		return $result;
	}
	
	public function getMemberProfile($unit_id)
	{
		$sql = "Select * from member_main where society_id = '" . $_SESSION['society_id'] . "' and unit = '" . $unit_id . "'";
		$result = $this->m_dbConn->select($sql);
		return $result;
	}
}
?>