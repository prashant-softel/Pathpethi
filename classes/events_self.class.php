<?php if(!isset($_SESSION)){ session_start(); }
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once ("dbconst.class.php");

class events_self 
{
	public $actionPage = "../events_self.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	
	function __construct($dbConn, $dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
$this->display_pg=new display_table($this->m_dbConn);
		//dbop::__construct();
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
					<input type="checkbox" value="<?php echo $v;?>" name="<?php echo $name;?>" id="<?php echo $id;?><?php echo $pp;?>"/>					
					<?php
					}
					else if($i==1)
					{
						$society_grp_id = $this->society_grp_id($v);
					echo "<a href='../list_society_group_details.php?grp&id=".$society_grp_id."&view' target='_blank' style='color:blue;text-decoration:none;' title='Click to view society under this group'>".$v."</a>";
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
	public function society_grp_id($grp_name)
	{
		$sql = "select society_grp_id from society_group where grp_name='".addslashes($grp_name)."' and status='Y'";
		$res = $this->m_dbConn->select($sql);
		return $res[0]['society_grp_id'];
	}
	public function display1($rsas)
	{
			$thheader=array('Events Date','Events Title','Events','Created on');
			$this->display_pg->edit="getevents";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="events.php";
			
			//$res = $this->display_pg->display_new($rsas);
			$res = $this->show_events($rsas);
			
			return $res;
	}
	public function pgnation($id)
	{
		//echo 'pgnation';
		$date = date('Y-m-d');
		$date_add = strtotime(date("Y-m-d", strtotime($date)) . " +1 month");
		$date_add_new = date('Y-m-d',$date_add);
		
		
		if($id >0 && $id <> "")
		{
		$sql1 = "select e.events_id, e.events_date, e.events_title,e.event_time, e.events, e.timestamp, s.society_id, s.society_name from `events` as e, `society` as s where  e.society_id=s.society_id and s.status='Y' and e.status='Y' and e.events_id=".$id." ";
		}
		else
		{
		$sql1 = "select e.events_id, e.events_date, e.events_title,e.event_time, e.events, e.timestamp, s.society_id, s.society_name from `events` as e, `society` as s where  e.society_id=s.society_id and s.status='Y' and e.status='Y'";
		}
		//$sql1 = "select e.events_id, e.events_date, e.events_title, e.events, e.event_time, s.society_id, s.society_name from events as e, society as s where e.events_date>='".$date."' and e.events_date<='".$date_add_new."' and e.society_id=s.society_id and s.status='Y' and e.status='Y'";
		
		if(isset($_SESSION['role']) && $_SESSION['role']==ROLE_ADMIN)
		{
			$sql1 .= " and s.society_id = '".$_SESSION['society_id']."'";
		}
		else
		{
			if($_REQUEST['society_id']<>"")
			{
				$sql1 .= " and s.society_id = '".$_REQUEST['society_id']."'";
			}
		}
		$cntr = "select count(*) as cnt from events as e, society as s where e.events_date>='".$date."' and e.events_date<='".$date_add_new."' and e.society_id=s.society_id and s.status='Y' and e.status='Y'";
		
		if(isset($_SESSION['admin']))
		{
			$cntr .= " and s.society_id = '".$_SESSION['society_id']."'";
		}
		else
		{
			if($_REQUEST['society_id']<>"")
			{
				$cntr .= " and s.society_id = '".$_REQUEST['society_id']."'";
			}
		}
		
		$this->display_pg->sql1=$sql1;
		$this->display_pg->cntr1=$cntr;
		$this->display_pg->mainpg="events_self.php";
		$limit = "30";
		$page=$_REQUEST['page'];
		
		$extra = "&society_id=".$_REQUEST['society_id'].'&ev=ev';
		
		//$res=$this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		//return $res;
		//echo $sql1;
		$result = $this->m_dbConnRoot->select($sql1);
		$this->show_events($result);
	}
	
	public function show_events($res)
	{
		
		//print_r($res);
		if($res<>"")
		{
			?>
            <table id="example" class="display" cellspacing="0" width="100%">
            <thead>
            <tr >
            	<?php if(isset($_SESSION['role']) && $_SESSION['role']==ROLE_SUPER_ADMIN){?>
                <th >Events Creator Society</th>
                <?php }?>
                
                <th >Events Title</th>
                <th >Events Description</th>
                <th >Event Date</th>
                <th >Event Time</th>
                
				<?php if(isset($_SESSION['role']) && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN)){?>
                <th>Delete</th>
                <?php }?>
            </tr>
            </thead>
            <tbody>
            <?php
			$kk = 1;
			foreach($res as $k => $v)
			{
				
				$startDate = date('Y-m-d');
				$days = (strtotime($res[$k]['events_date']) - strtotime($startDate)) / (60 * 60 * 24);
				
				if($days>=0)
				{						
			?>
            	<tr >
                	<?php if(isset($_SESSION['role']) && ($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN)){?>
                    <td align="center"><a href="society.php?id=<?php echo $res[$k]['society_id'];?>&show&imp" style="color:#00F; text-decoration:none;"><?php echo $res[$k]['society_name'];?></a></td>
                    <?php }?>
                    <td ><?php echo $res[$k]['events_title'];?></td>
                    <td ><?php echo $res[$k]['events'];?></td>
                    <td ><?php echo $res[$k]['events_date'];?></td>
                    <td ><?php echo $res[$k]['event_time'];?></td>
                    
                    <?php if(isset($_SESSION['role']) && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN)){?>
                    <td >
					<?php if($this->chk_delete_perm_admin()==1){?>
                    <a href="javascript:void(0);" onclick="getevents('delete-<?php echo $res[$k]['events_id'];?>-self');">
                    <img src="../images/del.gif" />
                    </a>
                    <?php }else{?>
                    <a href="del_control_admin.php?prm" target="_blank" style="text-decoration:none;"><font color=#FF0000 style='font-size:10px;'><b>Not Allowed</b></font></a>
                    <?php }?>
                    </td>
                    <?php }?>
                </tr>
              <?php		
				}
			}
			?>
            </tbody>
            </table>
            <?php
		}
		else
		{
			?>
            <center><font color="#FF0000"><b>No events here</b></font></center>
            <?php	
		}
	}
	public function grp_name($events_id)
	{
		$sql = "select * from events_and_grp as eg, society_group as sg where eg.status='Y' and sg.status='Y' and eg.events_id='".$events_id."' and eg.my_society_id='".$_SESSION['society_id']."' and sg.society_grp_id=eg.society_grp_id";
		$res = $this->m_dbConn->select($sql);
		
		$grp_name = $res[0]['grp_name'];
		$society_grp_id = $res[0]['society_grp_id'];
		
		if($grp_name=='')
		{
			echo "<a style='color:red;text-decoration:none;'>No group</a>";
		}
		else
		{
			echo "<a href='../list_society_group_details.php?grp&id=".$society_grp_id."&view' target='_blank' style='color:blue;text-decoration:none;' title='Click to view society under this group'>".$grp_name."</a>";
		}
	}
	
	public function chk_delete_perm_admin()
	{
		$sql = "select * from del_control_admin where status='Y' and login_id='".$_SESSION['login_id']."'";
		$res = $this->m_dbConn->select($sql);
		return $res[0]['del_control_admin'];
	}
	
	public function deleting()
	{
		//$sql = "update events set status='N' where events_id='".$_REQUEST['eventsId']."'";
		$sql = "delete from events where events_id='".$_REQUEST['eventsId']."'";
		$res = $this->m_dbConn->update($sql);
		
		//$sql1 = "update events_and_grp set status='N' where events_id='".$_REQUEST['eventsId']."'";
		$sql1 = "delete from events_and_grp where events_id='".$_REQUEST['eventsId']."'";
		$res1 = $this->m_dbConn->update($sql1);
		
		echo "msg1&token=".time()."****self";
	}
	
	public function view_events()
	{
		$date = date('Y-m-d');
		$date_add = strtotime(date("Y-m-d", strtotime($date)) . " +1 month");
		$date_add_new = date('Y-m-d',$date_add);
		
		$sql = "select * from events where status='Y' and events_date>='".$date."' and events_date<='".$date_add_new."' order by events_date";
		$res = $this->m_dbConn->select($sql);
		return $res;	
	}
	
	public function view_events_details($id)
	{
		$sql = "select * from events where status='Y' and events_id='".$id."'";
		$res = $this->m_dbConn->select($sql);
		return $res;	
	}
	
	public function getcount()
	{
		$sql = "select e.events_id from `events` as e, `society` as s where  e.society_id=s.society_id and s.status='Y' and e.status='Y'";
		$res = $this->m_dbConnRoot->select($sql);
		return $res;
	}
	
	public function FetchEvents($id)
	{
		$sql = "select * from `events` as e, `society` as s where  e.society_id=s.society_id and s.status='Y' and e.status='Y' and e.events_id=".$id." ";
		$res = $this->m_dbConnRoot->select($sql);
		return $res;
	}
	
}
?>