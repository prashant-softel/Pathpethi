<?php //if(!isset($_SESSION)){ session_start(); }
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");

class wing extends dbop
{
	public $actionPage = "../wing.php";
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
			if($_POST['wing']<>"")
			{
				$sql = "select count(*)as cnt from wing where society_id='".$_SESSION['society_id']."' and wing='".addslashes(trim(ucwords($_POST['wing'])))."' and status='Y'";
				$res = $this->m_dbConn->select($sql);
				
				if($res[0]['cnt']==0)
				{
					$insert_query = "insert into wing (`society_id`,`wing`) values ('".$_SESSION['society_id']."','".addslashes(trim(ucwords($_POST['wing'])))."')";
					$data = $this->m_dbConn->insert($insert_query);
					return "Insert";
				}
				else
				{
					return "Already Exist";
				}
			}
			else
			{
				return "* Field should not be blank.";
			}
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			if($_POST['wing']<>"")
			{	
				$up_query="update wing set `wing`='".addslashes(trim(ucwords($_POST['wing'])))."' where wing_id='".$_POST['id']."'";
				$data=$this->m_dbConn->update($up_query);
				return "Update";
			}
			else
			{
				return "* Field should not be blank.";
			}
		}
		else
		{
			return $errString;
		}
	}
	public function combobox($query, $id, $bShowAll)
	{
		if(isset($bShowAll) && $bShowAll == true)
		{
			$str.="<option value='0'>All</option>";
		}
		else
		{
			$str.="<option value=''>Please Select</option>";
		}
		
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
			$thheader=array('Society Name','Wing');
			$this->display_pg->edit="getwing";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="wing.php";
			
			//$res = $this->display_pg->display_new($rsas);
			$res = $this->show_wing_under_soc($rsas);
			
			return $res;
	}
	public function pgnation()
	{
		if($_REQUEST['insert']=='Search')
		{
			//echo '111';
			$sql1 = "select w.wing_id,s.society_name,s.society_id,w.wing from wing as w,society as s where w.status='Y' and s.status='Y' and w.society_id=s.society_id";
			if($_REQUEST['society_id']<>"")
			{
				$sss = 1;
				$sql1 .= " and s.society_id='".addslashes(trim($_REQUEST['society_id']))."'";
			}
			else{ $sss = 0;}
			if($_REQUEST['wing']<>"")
			{
				$sql1 .= " and w.wing like '%".addslashes(trim($_REQUEST['wing']))."%'";
			}
			if(isset($_SESSION['sadmin']))
			{
				if(isset($_REQUEST['sa']) && $sss<>1)
				{
					$sql1 .= " and s.society_id='".$_REQUEST['sid']."'";
				}
			}
			$sql1 .= " order by s.society_id desc,w.wing";	
		}
		else
		{
			//echo '222';
			if(isset($_SESSION['sadmin']))
			{
			$sql1 = "select w.wing_id,s.society_name,s.society_id,w.wing from wing as w,society as s where w.status='Y' and s.status='Y' and w.society_id='".$_SESSION['society_id']."' and s.society_id='".$_SESSION['society_id']."'";
			//echo $_REQUEST['sa'];
				if(isset($_REQUEST['sa']))
				{
					$sql1 .= " and s.society_id='".$_REQUEST['sid']."'";
				}
			$sql1 .= " order by s.society_id desc,w.wing";	
			}
			else
			{
			$sql1 = "select w.wing_id,s.society_name,s.society_id,w.wing from wing as w,society as s where w.status='Y' and s.status='Y' and w.society_id=s.society_id and s.society_id='".$_SESSION['society_id']."'";
			$sql1 .= " order by s.society_id desc,w.wing";		
			}
		}
		
		
		if($_REQUEST['insert']=='Search')
		{
			/*$cntr = "select count(*) as cnt from wing as w,society as s where w.status='Y' and s.status='Y' and w.society_id=s.society_id";
			if($_REQUEST['society_id']<>"")
			{
				$cntr .= " and s.society_id='".addslashes(trim($_REQUEST['society_id']))."'";
			}
			if($_REQUEST['wing']<>"")
			{
				$cntr .= " and w.wing like '%".addslashes(trim($_REQUEST['wing']))."%'";
			}
			$cntr .= " order by s.society_id desc,w.wing";*/
		}
		else
		{
			/*if(isset($_SESSION['sadmin']))
			{
			$cntr = "select count(*) as cnt from wing as w,society as s where w.status='Y' and s.status='Y' and w.society_id=s.society_id";
			if(isset($_REQUEST['sa']))
			{
				$cntr .= " and s.society_id='".$_REQUEST['sid']."'";
			}
			$cntr .= " order by s.society_id desc,w.wing";
			}
			else
			{
			$cntr = "select count(*) as cnt from wing as w,society as s where w.status='Y' and s.status='Y' and w.society_id=s.society_id and s.society_id='".$_SESSION['society_id']."'";
			$cntr .= " order by s.society_id desc,w.wing";	
			}*/
		}
		
		
		$this->display_pg->sql1=$sql1;
		$this->display_pg->cntr1=$cntr;
		$this->display_pg->mainpg="wing.php";
		$limit="10";
		$page=$_REQUEST['page'];
		
		if(isset($_SESSION['sadmin']))
		{
			if(isset($_REQUEST['sa']))
			{
				$extra = "&imp&idd=".time().'&sa&sid='.$_REQUEST['sid'].'&id='.$_REQUEST['id'];
			}
			else
			{
				$extra = "&imp&id=".time();
			}
		}
		else
		{
			$extra = "&imp&ssid=".$_REQUEST['ssid']."&s&idd=".time();
		}
		
		$result = $this->m_dbConn->select($sql1);
		$this->show_wing_under_soc($result);
		/*$res = $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;*/
	}
	public function show_wing_under_soc($res)
	{
		if($res<>"")
		{
		?>
		<table id="example" class="display" cellspacing="0" width="100%">
		<thead>
        <tr height="30" bgcolor="#CCCCCC" >
        
        	<th width="300" style="text-align:center;">Society Name</th>
            <th width="100" style="text-align:center;">Wing</th>
            
                     <th width="100" style="text-align:center;">Add</th>
          
            <?php if(isset($_GET['s'])){?>
        	<th width="70">Edit</th>
            <th width="70">Delete</th>
            <?php }?>
            
        </tr>
        </thead>
        <tbody>
        <?php foreach($res as $k => $v){?>
        <tr height="25" bgcolor="#BDD8F4" align="center">
        	<td align="center"><?php echo $res[$k]['society_name'];?></td>
            <td align="center"><?php echo $res[$k]['wing'];?></td>
            
        	<?php if(isset($_SESSION['sadmin'])){
				$show_totalunit_count="select count(*) as cnt from unit where wing_id='".$res[$k]['wing_id']."' and society_id='".$_SESSION['society_id']."'";
				$unit_count=$this->m_dbConn->select($show_totalunit_count);
				?>
            <td align="center"><a href="unit.php?imp&idd=<?php echo time();?>&sa&sid=<?php echo $_REQUEST['sid'];?>&wid=<?php echo $res[$k]['wing_id'];?>&id=<?php echo rand('0000000','9999999');?>" style="color:#0000FF;"><b><?php echo $unit_count[0]['cnt'];?></b></a></td>
            <td align="center"><a href="unit.php?imp&ssid=<?php echo $res[$k]['society_id'];?>&ws&wwid=<?php echo $res[$k]['wing_id'];?>&idd=<?php echo time();?>" style="color:#0000FF;"><b>Add Unit</b></a></td>
			<?php }else{?>
            <td align="center"><a href="unit.php?imp&ssid=<?php echo $res[$k]['society_id'];?>&ws&wwid=<?php echo $res[$k]['wing_id'];?>&idd=<?php echo time();?>" style="color:#0000FF;"><b>Add Unit</b></a></td>
            <?php }?>
            
            <?php if(isset($_GET['s'])){?>
            <td align="center">
            <a href="javascript:void(0);" onclick="getwing('edit-<?php echo $res[$k]['wing_id']?>')"><img src="images/edit.gif" /></a>
            </td>
            
            <td align="center">
            <?php if($this->chk_delete_perm_admin()==1){?>
            <a href="javascript:void(0);" onclick="getwing('delete-<?php echo $res[$k]['wing_id']?>');"><img src="../images/del.gif" /></a>
            <?php }else{?>
            <a href="del_control_admin.php?prm" target="_blank" style="text-decoration:none;"><font color=#FF0000 style='font-size:10px;'><b>Not Allowed</b></font></a>
            <?php }?>
            </td>
            <?php }?>
            
            </tr>
        <?php }?></tbody>
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
	
	public function selecting()
	{
		$sql1="select wing_id,`wing` from wing where wing_id='".$_REQUEST['wingId']."' and society_id=".$_SESSION['society_id'];
		$var=$this->m_dbConn->select($sql1);
		return $var;
	}
	public function deleting()
	{
		$sql0 = "select count(*)as cnt from unit where wing_id='".$_REQUEST['wingId']."' and status='Y'";
		$res0 = $this->m_dbConn->select($sql0);
		
		if($res0[0]['cnt']==0)
		{
			$sql1 = "update wing set status='N' where wing_id='".$_REQUEST['wingId']."'";
			$this->m_dbConn->update($sql1);
			
			$sql11 = "update unit set status='N' where wing_id='".$_REQUEST['wingId']."'";
			$this->m_dbConn->update($sql11);
			
			echo "msg1###".$_SESSION['ssid'];
		}
		else
		{
			echo "msg";	
		}
	}
	
	public function soc_name($s_id)
	{
		$sql = "select * from society where society_id='".$s_id."' and status='Y'";
		$res = $this->m_dbConn->select($sql);
		echo $res[0]['society_name'];
	}
	public function chk_delete_perm_admin()
	{
		$sql = "select * from del_control_admin where status='Y' and login_id='".$_SESSION['login_id']."'";
		$res = $this->m_dbConn->select($sql);
		return $res[0]['del_control_admin'];
	}
}
?>