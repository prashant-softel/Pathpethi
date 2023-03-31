<?php if(!isset($_SESSION)){ session_start(); }
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once ("dbconst.class.php");
include_once('../swift/swift_required.php');
include_once( "include/fetch_data.php");
include_once("utility.class.php");
include_once("android.class.php");

class events extends dbop
{
	public $actionPage = "../events_view.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	public $objFetchData;
	public $obj_Utility;
	public $obj_android;
	
	function __construct($dbConn, $dbConnRoot, $SocietyID)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		$this->m_dbConnRoot = $dbConnRoot;
		//dbop::__construct();
		
		$this->objFetchData = new FetchData($dbConn);
		if(isset($SocietyID) && $SocietyID <> "")
		{
			$this->objFetchData->GetSocietyDetails($SocietyID);
		}
		else
		{
			$this->objFetchData->GetSocietyDetails($_SESSION['society_id']);
		}
		
		$this->obj_Utility = new utility($dbConn, $dbConnRoot);
	
	}
	public function startProcess()
	{		
		$errorExists=0;
		if($_REQUEST['insert']=='Create' && $errorExists==0)
		{
			if($_POST['events_date']<>"" && $_POST['events_title']<>"" )
			{			
				$startDate = date('Y-m-d');
				$days = (strtotime($_POST['events_date']) - strtotime($startDate)) / (60 * 60 * 24);
				echo 'ampm:'.$_POST['ampm'];
				if($days>=0)
				{					
					$uploaded_filename = "";
					$docGDriveID = "";
					//echo "trace1:";
				
					if($_POST['event_creation_type'] == 2)
					{
						//echo "trace2:";
						if($_FILES['userfile']['name'] != "")
						{
							$doc_type = "Events";
							$PostDate = $_POST['events_date'];         		                    
							
							$resResponse = $this->obj_Utility->UploadAttachment($_FILES, $doc_type, $PostDate, "Events");
							$sStatus = $resResponse["status"];
							$sMode = $resResponse["mode"];
							$sFileName = $resResponse["response"];
							$sUploadFileName = $resResponse["FileName"];

							if($sMode == "1")
							{
								$uploaded_filename = $sFileName;
							} 
							else if($sMode == "2")
							{
								$docGDriveID = $sFileName;
							}
							else
							{
								//failure or no file uploaded
							}
						}
					}
					//echo "trace3:";
				
					 $insert_query = "insert into events (`society_id`,`issued_by`,`events_date`,`end_date`,`events_title`,`events`,`event_time`,`events_url`,`event_type`,`event_charges`,`event_creation_type`,`Uploaded_file`,`event_version`,`attachment_gdrive_id`) values ('".$_SESSION['society_id']."','".$_POST['issueby']."','".getDBFormatDate($_POST['events_date'])."','".getDBFormatDate($_POST['end_date'])."','".addslashes(trim(ucwords($_POST['events_title'])))."','".addslashes(trim($_POST['events_desc']))."','".$_POST['hr'].":".$_POST['mn']." ".$_POST['ampm']."','".$_POST['events_url']."','".$_POST['event_type']."','".$_POST['event_charges']."','".$_POST['event_creation_type']."','".$sUploadFileName."','2','".$docGDriveID."')";
					$data = $this->m_dbConnRoot->insert($insert_query);
					//echo $insert_query;
					//die();
					//$path = "events_view_as_self";
					$path = "events_view";
					$iEventID = $data;
 					if($_POST['society_grp_id']<>"")
					{
						foreach($_POST['society_grp_id'] as $k => $v)
						{
							$sql = "insert into events_and_grp(`my_society_id`,`events_id`,`society_grp_id`) values ('".$_SESSION['society_id']."','".$data."','".$v."')";
							$res = $this->m_dbConn->insert($sql);
							
							$path = "events_view_as";
						}
					}
					else
					{
						$sql = "insert into events_and_grp(`my_society_id`,`events_id`,`society_grp_id`) values ('".$_SESSION['society_id']."','".$data."','0')";
						$res = $this->m_dbConn->insert($sql);
					}
					
					if($_POST['notify'])
					{											
						$mailSubject = $this->objFetchData->objSocietyDetails->sSocietyName .' : ' .$_POST['events_title'] ;						
						if($uploaded_filename != "")
						{							
							if($_POST['events_desc'] == "")
							{
								echo "event desc";
								$mailBody = 'Dear Members, <br /> <br /> Please find attached event notice regarding : ' . $_POST['events_title'] . '. <br /> <br /> Thanking you <br />';
							}
							else
							{
								$mailBody = $_POST['events_desc'];
							}
						}
						else
						{
							$mailBody = $_POST['events_desc'];
						}
						//echo "data:[".$data."]";
						$this->SendEventInEmail(0, "", "", $data);									
						
					}	
					
					
					?>
                    <script>window.location.href = '../<?php echo $path;?>.php?ev&add&tik_id=<?php echo time();?>&nok';</script>
                    <?php
				}
				else
				{
					return "Invalid date.<br>Please select current or future date";	
				}
				
				if($_POST['mobile_notify'])
					{	
					 									
					  $MobileSubject = $this->objFetchData->objSocietyDetails->sSocietyName .' : ' .$_POST['events_title'] ;
					  $this->SendMobileNotification($data);		
					}
			}
			else
			{
				return "Some * field is missing";
			}
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			if($_POST['events_date']<>"" && $_POST['events_title']<>"" && $_POST['events_desc']<>"")
			{
				$fileName = "";
				$docGDriveID = "";
							
				if($_POST['event_creation_type'] == 2)
				{
					//echo "trace2:";
					if($_FILES['userfile']['name'] != "")
					{
						$doc_type = "Events";
						$PostDate = $_POST['events_date'];         		                    
						
						$resResponse = $this->obj_Utility->UploadAttachment($_FILES, $doc_type, $PostDate, "Events");
						$sStatus = $resResponse["status"];
						$sMode = $resResponse["mode"];
						$sFileName = $resResponse["response"];
						$sUploadFileName = $resResponse["FileName"];
						if($sMode == "1")
						{
							$fileName = $sFileName;
						} 
						else if($sMode == "2")
						{
							$docGDriveID = $sFileName;
						}
						else
						{
							//failure or no file uploaded
						}
					}
				}
				if($_FILES['userfile']['name'] != "")
				{
				 $up_query="update events set `events_date`='".getDBFormatDate($_POST['events_date'])."',end_date='".getDBFormatDate($_POST['end_date'])."',`events_title`='".addslashes(trim(ucwords($_POST['events_title'])))."',`events`='".addslashes(trim($_POST['events_desc']))."', event_time='".$_POST['hr'].":".$_POST['mn']." ".$_POST['ampm']."', `event_type`='".$_POST['event_type']."',`event_charges`='".$_POST['event_charges']."',`Uploaded_file`='".$sUploadFileName."',`event_version`='2',`attachment_gdrive_id`='".$docGDriveID."' where events_id='".$_POST['event_id']."'";
				}
				else
				{
					$up_query="update events set `events_date`='".getDBFormatDate($_POST['events_date'])."',end_date='".getDBFormatDate($_POST['end_date'])."',`events_title`='".addslashes(trim(ucwords($_POST['events_title'])))."',`events`='".addslashes(trim($_POST['events_desc']))."', event_time='".$_POST['hr'].":".$_POST['mn']." ".$_POST['ampm']."', `event_type`='".$_POST['event_type']."',`event_charges`='".$_POST['event_charges']."',`event_version`='2' where events_id='".$_POST['event_id']."'";
				}
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
	public function pgnation()
	{
		$date = date('Y-m-d');
		$date_add = strtotime(date("Y-m-d", strtotime($date)) . " +1 month");
		$date_add_new = date('Y-m-d',$date_add);
		
		//$sql1 = "select e.events_id, e.events_date, e.events_title, e.events, e.timestamp, s.society_id, s.society_name from events as e, society as s where e.events_date>='".$date."' and e.events_date<='".$date_add_new."' and e.society_id=s.society_id and s.status='Y' and e.status='Y'";
		
		//$sql1 = "select e.events_id, e.events_date, e.events_title, e.events, e.event_time, s.society_id, s.society_name,eg.society_grp_id from events as e, society as s, events_and_grp as eg,society_group as sg where e.events_date>='".$date."' and e.events_date<='".$date_add_new."' and e.society_id=s.society_id and e.events_id=eg.events_id and sg.society_grp_id=eg.society_grp_id and s.status='Y' and e.status='Y'";
		$sql1 = "select e.events_id, e.events_date, e.events_title, e.events, e.event_time, s.society_id, s.society_name from events as e, society as s where  e.society_id=s.society_id and e.status='Y'";
		if($_REQUEST['society_id']<>"")
		{
			$sql1 .= " and s.society_id = '".$_REQUEST['society_id']."'";
		}
			
		$cntr = "select count(*) as cnt from events as e, society as s, events_and_grp as eg,society_group as sg where e.events_date>='".$date."' and e.events_date<='".$date_add_new."' and e.society_id=s.society_id and e.events_id=eg.events_id and sg.society_grp_id=eg.society_grp_id and s.status='Y' and e.status='Y'";
		if($_REQUEST['society_id']<>"")
		{
			$cntr .= " and s.society_id = '".$_REQUEST['society_id']."'";
		}
		
		
		$this->display_pg->sql1=$sql1;
		$this->display_pg->cntr1=$cntr;
		$this->display_pg->mainpg="events.php";
		$limit = "10";
		$page=$_REQUEST['page'];
		
		$extra = "&society_id=".$_REQUEST['society_id'].'&ev=ev';
		
		//$res=$this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		//return $res;
		$result = $this->m_dbConnRoot->select($sql1);
		//echo $sql1;
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
            	<?php //if(isset($_SESSION['sadmin'])){?>
                <th >Events Creator Society</th>
                <?php //}?>
                <th>Group Name</th>
                <th >Events Title</th>
                <th >Events Description</th>
                <th >Events Date</th>
                <th>Event Time</th>
                
				<?php if(isset($_SESSION['role']) && ($_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN)){?>
                <?php //if($_SESSION['society_id']==$res[0]['society_id']){?>
                <th >Delete</th>
                <?php //}
				}?>
            </tr>
            </thead>
            <tbody>
            <?php
				$kk = 0;
				foreach($res as $k => $v)
				{
					$startDate = date('Y-m-d');
					$days = (strtotime($res[$k]['events_date']) - strtotime($startDate)) / (60 * 60 * 24);
					
					if($days>=0)
					{
						$sql0 = "select * from society_group where society_grp_id='".$res[$k]['society_grp_id']."' and status='Y'";
						$res0 = $this->m_dbConn->select($sql0);	
						
						$sql11 = "select count(*)as cnt from society_group where grp_name='".addslashes($res0[0]['grp_name'])."' and society_id='".$_SESSION['society_id']."' and status='Y'";
						$res11 = $this->m_dbConn->select($sql11);	
						
						if($res11[0]['cnt']==1)
						{
							$lol = 1;
			?>
            	<tr >
                	<?php //if(isset($_SESSION['sadmin'])){?>
                    <td ><a href="society.php?id=<?php echo $res[$k]['society_id'];?>&show&imp" style="color:#00F; text-decoration:none;"><?php echo $res[$k]['society_name'];?></a></td>
                    <?php //}?>
                    
                    <td ><?php echo "<a href='../list_society_group_details.php?ev&id=".$res[$k]['society_grp_id']."&view'  style='color:blue;text-decoration:none;' title='Click to view society under this group'>".$res0[0]['grp_name']."</a>";//$grp_name = $this->grp_name($res[$k]['events_id']);?></td>
                    
                    <td ><?php echo $res[$k]['events_title'];?></td>
                    <td ><?php echo $res[$k]['events'];?></td>
                    <td ><?php echo $res[$k]['events_date'];?></td>
                    <td ><?php echo $res[$k]['event_time'];?></td>
                    
                    <?php if(isset($_SESSION['role']) && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN)){?>
                    <?php //if($_SESSION['society_id']==$res[0]['society_id']){?>
                    <td >
					<?php if($this->chk_delete_perm_admin()==1){?>
                    <a href="javascript:void(0);" onclick="getevents('delete-<?php echo $res[$k]['events_id'];?>-grp');">
                    <img src="../images/del.gif" />
                    </a>
                    <?php }else{?>
                    <a href="../del_control_admin.php?prm" target="_blank" style="text-decoration:none;"><font color=#FF0000 style='font-size:10px;'><b>Not Allowed</b></font></a>
                    <?php }//}?>
                    </td>
                    <?php }?>
                    
                </tr>
            <?php
						}
						else
						{
							if(isset($_SESSION['role']) && $_SESSION['role']==ROLE_SUPER_ADMIN)
							{
						?>
                        <tr >
							<?php //if(isset($_SESSION['sadmin'])){?>
                            <td ><a href="../society_view.php?imp" style="color:#00F; text-decoration:none;"><?php echo $res[$k]['society_name'];?></a></td>
                            <?php //}?>
                            
                            <td ><?php echo "<a href='../list_society_group_details.php?grp&id=".$res[$k]['society_grp_id']."&view' target='_blank' style='color:blue;text-decoration:none;' title='Click to view society under this group'>".$res0[0]['grp_name']."</a>";//$grp_name = $this->grp_name($res[$k]['events_id']);?></td>
                            
                            <td ><?php echo $res[$k]['events_title'];?></td>
                            <td ><?php echo $res[$k]['events'];?></td>
                            <td ><?php echo $res[$k]['events_date'];?></td>
                            <td ><?php echo $res[$k]['event_time'];?></td>
                            
                            <?php if(isset($_SESSION['role'])&& ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN)){?>
                            <td >
                            <?php if($this->chk_delete_perm_admin()==1 ){?>
                            <a href="javascript:void(0);" onclick="getevents('delete-<?php echo $res[$k]['events_id'];?>-grp');">
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
							else
							{
								if($lol!=1)
								{
									//echo '<tr><td colspan=12 align=center height=40><font color="#FF0000"><b>No Events Here</b></font></td></tr>';	
								}
							}
						}
					}
					$kk++;
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
	
	//----------------------------------------Add New Function ------------------------------------//
	public function selecting($eventId)
	{
	  $sql = "SELECT `events_id`,`society_id`,`issued_by`,`events_date`,`end_date`,`events_title`,`event_time`,`events_url`,`event_type`,`event_charges`,`Uploaded_file`,`event_creation_type`,`events` FROM `events` WHERE `events_id` = '".$eventId."'";		
		$res = $this->m_dbConnRoot->select($sql);
		$res[0]['events_date']=getDisplayFormatDate($res[0]['events_date']);
		$res[0]['end_date']=getDisplayFormatDate($res[0]['end_date']);
		
	 return $res;
	}
	
	public function deleting($event_id)
	{
		$sql = "update events set status='N' where events_id='".$event_id."'";
		//$sql = "delete from events where events_id='".$_REQUEST['eventsId']."'";
		$res = $this->m_dbConnRoot->update($sql);
		
		$sql1 = "update events_and_grp set status='N' where events_id='".$event_id."'";
		//$sql1 = "delete from events_and_grp where events_id='".$_REQUEST['eventsId']."'";
		$res1 = $this->m_dbConn->update($sql1);
		
		
		//echo "msg1&token=".time()."****grp";
	}
	//---------------------------------------------------------------------------------------------------///
	
	public function view_events1()
	{
		$date = date('Y-m-d');
		$date_add = strtotime(date("Y-m-d", strtotime($date)) . " +1 month");
		$date_add_new = date('Y-m-d',$date_add);
		
		$s = "select * from society_group where society_id='".$_SESSION['society_id']."' and status='Y'";
		$r = $this->m_dbConn->select($s);
		if($r<>"")
		{
			foreach($r as $k => $v)
			{
				$sid1 .= $r[$k]['my_society_id'].',';
			}
		}
		$sid = substr($sid1,0,-1);
		if($sid==""){$sid=0;}
		
		$sql = "select * from events where status='Y'  and society_id in (".$sid.") order by events_date";
		$res = $this->m_dbConnRoot->select($sql);
		
		if($res<>"")
		{
	foreach($res as $k => $v)
	{
		$events_id = $res[$k]['events_id'];
		
		$ss = "select * from events_and_grp where events_id='".$events_id."' and status='Y'";
		$rr = $this->m_dbConnRoot->select($ss);
		
		$sgrp = $rr[0]['society_grp_id'];
		
		$sss = "select * from society_group where society_grp_id='".$sgrp."' and status='Y'";
		$rrr = $this->m_dbConn->select($sss);
		
		$grp_name = $rrr[0]['grp_name'];
		
		$sss1 = "select * from society_group where grp_name='".$grp_name."' and status='Y'";
		$rrr1 = $this->m_dbConn->select($sss1);
		
		foreach($rrr1 as $k1 => $v1)
		{
			$ss1 = "select count(*)as cnt from society_group where society_id='".$_SESSION['society_id']."' and grp_name='".$grp_name."' and status='Y'";
			$rr1 = $this->m_dbConn->select($ss1);
			
			$cnt = $rr1[0]['cnt'];
			
		}
		
		if($cnt==1)
		{
	?>
	<table align="center" border="0">
	<tr align="left">
		<td width="250"><b>Event Title</b>&nbsp; : &nbsp;<?php echo $res[$k]['events_title'];?></td>
		<td rowspan="2" valign="middle" align="center" width="60"><a href="events_view_details.php?id=<?php echo $res[$k]['events_id'];?>&ev" style="color:#00F"><img src="../images/view.jpg" width="20" width="20" /></a></td>
   		<!-- <td rowspan="2" valign="middle" align="center" width="80"><a href="#" onClick="publishStream(); return false;" style="color:#00F"><b><img src="../fb/send.jpg" /></b></a></td> -->     
	</tr>
	<tr align="left">
		<td width="250"><b>Event Date</b>&nbsp; : &nbsp;<?php echo $res[$k]['events_date'];?></td>
	</tr>
	<tr>
		<td colspan="3" height="25"><hr /></td>
	</tr>
	</table>
	<?php
		}
	}
		}
		else
		{
			echo '<center><font color=red>No events here</font></center>';		
		}
	}
	
	///---------------------------------------------add new functions-------------------------///
	public function Event_edit($EventID)
	{
		//echo $Eventid;
		$sqlQuery = "select `events_id`,`Uploaded_file` from `events` where events_id='".$EventID."' and status='Y'";	
		//echo $sqlQuery;			
		$res = $this->m_dbConnRoot->select($sqlQuery);	
		//print_r($res);
		return $res;
	}
	
	///---------------------------------------------add new functions-------------------------///
	public function view_events($id = 0)
	{
		$date = date('Y-m-d');
		$date_add = strtotime(date("Y-m-d", strtotime($date)) . " +1 month");
		$date_add_new = date('Y-m-d',$date_add);
		
		$s = "select * from society_group where my_society_id='".$_SESSION['society_id']."' and status='Y'";
		$r = $this->m_dbConn->select($s);
		
		if($id == 0)
		{
			//$sql = "select * from events where status='Y' and events_date>='".$date."' and events_date<='".$date_add_new."' and society_id='".$_SESSION['society_id']."' order by events_date";
				 $sql = "select * from events where status='Y' and society_id='".$_SESSION['society_id']."' order by events_date desc";
		}
		else
		{
			// $sql = "select * from events where status='Y' and society_id='".$_SESSION['society_id']."' order by events_date desc";
			 $sql = "select * from events where status='Y' and events_date >='".$date."' and events_date <='".$date_add_new."' and society_id='".$_SESSION['society_id']."' and `events_id` = '".$id."' ";
		}
		//echo $sql;		
		$res = $this->m_dbConnRoot->select($sql);
		return $res;	
	}
	
	public function RightPanel($id)
	{
		
			 $sql = "select * from events where status='Y'  and society_id='".$_SESSION['society_id']."' and `events_id` = '".$id."' ";
				
		$res = $this->m_dbConnRoot->select($sql);
		return $res;	
	}

	public function view_events_details($id)
	{
		$sql = "select * from events where status='Y' and events_id='".$id."'";
		$res = $this->m_dbConnRoot->select($sql);
		return $res;	
	}
	
	public function show_soc_chk($event_id)
	{
		$sql = "select * from events_and_grp where status='Y' and events_id='".$event_id."'";
		$res = $this->m_dbConnRoot->select($sql);
		
		$society_grp_id = $res[0]['society_grp_id'];
		return $society_grp_id;
	}
	
	public function show_soc($event_id)
	{
		$sql = "select * from events_and_grp where status='Y' and events_id='".$event_id."'";
		$res = $this->m_dbConn->select($sql);
		
		$society_grp_id = $res[0]['society_grp_id'];
		
		$sql1 = "select * from society_group where status='Y' and society_grp_id='".$society_grp_id."'";
		$res1 = $this->m_dbConn->select($sql1);
		
		$grp_name = $res1[0]['grp_name'];
		
		$sql11 = "select * from society_group where status='Y' and grp_name='".$grp_name."'";
		$res11 = $this->m_dbConn->select($sql11);
		
		if($res11<>"")
		{
			?>
            <table align="center" border="0">
        	<tr align="left">
            <?php
			$i = 1;
			$ii = 1;
			foreach($res11 as $k => $v)
			{
				if($ii==1)
				{
					echo "</tr><tr>";	
					$ii = 0;
				}
		?>
        	<td align="center" width="19"><?php echo $i;?> . </td>
            <td align="left" width="250"><?php $this->soc_name($res11[$k]['society_id']);?></td>
        <?php
			$i++;
			$ii++;
			}
			?>
            </tr>
        	</table>
            <?php
		}
	}
	
	public function soc_name($society_id)
	{
		$sql11 = "select * from society where status='Y' and society_id='".$society_id."'";
		$res11 = $this->m_dbConn->select($sql11);
		
		echo $res11[0]['society_name'];
		
	}
	public function GetAttachmentFileLink($EventID)
	{
		$arAttachment = array();
		$sql = "select  * from `events` where `events_id`='".$EventID."'";
		//echo 
		$result = $this->m_dbConnRoot->select($sql);
		$sGDriveID = "";
		if(isset($result["0"]["attachment_gdrive_id"]) && $result["0"]["attachment_gdrive_id"] != "" || $result["0"]["attachment_gdrive_id"] != "-")
		{
			$sGDriveID = $result["0"]["attachment_gdrive_id"];
		}
		$sW2S_Uploaded_file = "";
		if(isset($result["0"]["Uploaded_file"]) && $result["0"]["Uploaded_file"] != "")
		{
			$sW2S_Uploaded_file = $result["0"]["Uploaded_file"];
		}
		$sNoticeVersion = $result["0"]["event_version"];
		
		$arAttachment["event_version"] = $sNoticeVersion;
		if($sNoticeVersion == "1")
		{
			$arAttachment["attachment_file"] = $sW2S_Uploaded_file;
			$arAttachment["Source"] = "1";//w2s
		}
		else if($sNoticeVersion == "2")
		{
			if($sW2S_Uploaded_file == "")
			{
				$arAttachment["attachment_file"] = "https://drive.google.com/file/d/". $sGDriveID ."/view";
				$arAttachment["Source"] = "2";//gdrive
			}
			else
			{
				$arAttachment["attachment_file"] = $sW2S_Uploaded_file;	
				//$arAttachment["attachment_file"] = "1518789971_anurag.xlsx";
				$arAttachment["Source"] = "1";//w2s
			}
		}
		else
		{

		}
		return $arAttachment;
	}
	public function SendEventInEmail($CronJobProcess, $DBName, $SocietyID, $EventID)
	{
		$bSendEMailInQueue = 1; $EmailSourceModule = 2; $PeriodID = ""; $UnitID = "";
		$sql_query  =  "select * from `events` where `events_id`='".$EventID."'";
		//echo $sql_query;
		$EventDetails = $this->m_dbConnRoot->select($sql_query);
		 
		if(!isset($EventDetails))
		{
			echo "Failed to send Event. Unable to fetch event details for EventID <".$EventID.">. ";
			die();
		}
		//print_r($EventDetails);
		$resAttachment = $this->GetAttachmentFileLink($EventID);
		$eventBody = $EventDetails[0]["events"];
		$EventSubject  = $EventDetails[0]["events_title"];
		$EventAttachment = "";
		if($resAttachment["event_version"] == "2" && $resAttachment["Source"] == "2" && $resAttachment["attachment_file"] != "")
		{
			$eventBody .= "<br>Please find attachment :". $resAttachment["attachment_file"];
		}
		//print_r($resAttachment);
		//die();
		$EventDetails[0]["Uploaded_file"];
		//echo "CronJob:".$CronJobProcess ." EventBody [".$eventBody."] EventSubject [".$EventSubject."] EventAttachment [".$EventAttachment."] SocID [".$SocietyID."]";
		if($CronJobProcess == 0)
		{
			 $DBName = $_SESSION["dbname"];
			 $SocietyID = $_SESSION["society_id"];
		}
		
		else if($DBName ==  "")
		{
			echo "Error: Unable to send notice. Database Name not passed. ";
			die();
		}
		else if($SocietyID  ==  "")
		{
			echo "Error: Unable to send notice. Society ID not passed. ";
			die();
		}
		  //echo "SocID:<".$SocietyID .">";
		$display = array();																						
		$bccUnitsArray = array();								
		
		//$sql = 'SELECT mem_other_family.other_email, mem_other_family.other_name, member_main.email, member_main.owner_name, member_main.unit FROM `mem_other_family` JOIN `member_main` on mem_other_family.member_id = member_main.member_id JOIN `unit` on unit.unit_id = member_main.unit where unit.society_id = '.$SocietyID;
		
		/*$sql = 'SELECT  mem_other_family.other_email, mem_other_family.other_name, member_main.email, member_main.owner_name FROM `mem_other_family` JOIN  `member_main` on mem_other_family.member_id = member_main.member_id JOIN `unit` on unit.unit_id = member_main.unit where unit.society_id = '.$SocietyID.' AND mem_other_family.send_commu_emails = 1 and member_main.member_id IN (SELECT  member_main.`member_id` FROM (select  `member_id` from `member_main` where `ownership_date` <= "NOW()"  ORDER BY `ownership_date` desc) as member_id Group BY unit)';

		//echo $sql;
		$result = $this->m_dbConn->select($sql);*/

		$emailIDList = $this->objFetchData->GetEmailIDToSendNotification(0);

		for($i = 0; $i < sizeof($emailIDList); $i++)
		{
			if($emailIDList[$i]['to_email'] <> "")
			{
				$display[$emailIDList[$i]['to_email']] = $emailIDList[$i]['to_name'];
				$bccUnitsArray[$i] = $emailIDList[$i]['unit'];
			}
		}													
										
																
		if(sizeof($display) == 0)
		{
			echo 'Email ID Missing';
			exit();
		}							
												
		// Create the mail transport configuration					
	  	$societyEmail = "";	  
	  	if($this->objFetchData->objSocietyDetails->sSocietyEmail <> "")
	  	{
			$societyEmail = $this->objFetchData->objSocietyDetails->sSocietyEmail;
	  	}
	  	else
	  	{
		 	$societyEmail = "techsupport@way2society.com";
			//$societyEmail = "societyaccounts@pgsl.in";
	  	}
	  
	  	try
	  	{
		  
		 	$EMailIDToUse = $this->obj_Utility->GetEmailIDToUse($bSendEMailInQueue, $EmailSourceModule, $PeriodID, $UnitID, $CronJobProcess, $DBName, $SocietyID, $EventID, $bccUnitsArray);
			if($EMailIDToUse['status'] == 0)
			{
				$EMailID = $EMailIDToUse['email'];
				$Password = $EMailIDToUse['password']; 					  
				//echo '<br/>Email ID To Use : [' . $EMailID . '][' . $Password . ']';
					$transport = Swift_SmtpTransport::newInstance('cs10.webhostbox.net', 465, "ssl")
						->setUsername($EMailID)
						->setSourceIp('0.0.0.0')
						->setPassword($Password) ; 
																			
				// Create the message
				$message = Swift_Message::newInstance();
				
				if($this->objFetchData->objSocietyDetails->sSocietyEmail <> "")
				{
					$message->setTo(array(
					   $societyEmail => $societyName
					));
				}
				//echo "emails in bcc:";
				//print_r($display);
				$message->setBcc($display);
				//echo $societyEmail. $this->objFetchData->objSocietyDetails->sSocietyName;
				//$message->setReplyTo(array( 'rahejagwing@gmail.com' => $this->objFetchData->objSocietyDetails->sSocietyName));										 					
				//echo 'Email : ' . $societyEmail;
				//echo 'Name : '  . $societyName = $this->objFetchData->objSocietyDetails->sSocietyName;
				 
				 $message->setReplyTo(array(
				   $societyEmail => $societyName
				)); 

				$message->setSubject($EventSubject);
				$message->setBody($eventBody);
				$message->setFrom("no-reply@way2society.com", $this->objFetchData->objSocietyDetails->sSocietyName);					
				$message->setContentType("text/html");										 
			
				//if($_POST['description'] == "Notice Uploaded")
				if($resAttachment["Source"] == "1"  && $resAttachment["attachment_file"] != "")
				{
					$message->attach(Swift_Attachment::fromPath('../Events/' . $resAttachment["attachment_file"]));
				}
				// Send the email
				$mailer = Swift_Mailer::newInstance($transport);
				$result = $mailer->send($message);
				//die();
				$result = 1;
				if($result == 1)
				{
					echo 'Success';
					if($CronJobProcess)
					{
						$sqlUpdate = "Update `emailqueue` set `Status`=1 WHERE `SourceTableID` = '".$EventID."' and `Status`=0"; 
						//echo $sqlUpdate;
						$this->m_dbConnRoot->update($sqlUpdate);
					}
				}
				else
				{
					echo 'Failed';
				}	
		  	}
		  	else
		  	{
				echo $EMailIDToUse['msg'];
		  	}
	  	}
	  	catch(Exception $exp)
		{
			echo "Error occure in email sending.";
		}
	}
	
	///----------------------------------------------Mobile Notification ----------------------------///
	
	public function SendMobileNotification($EventID)
	{
		 $UnitID = "";
		$sql_query  =  "select * from `events` where `events_id`='".$EventID."'";
		$EventDetails = $this->m_dbConnRoot->select($sql_query);
		//$eventMassage = $EventDetails[0]["events"];
		//$EventTitle  = $EventDetails[0]["events_title"];
		$eventMassage = $EventDetails[0]["events_title"];
		$EventTitle  = "New Event";
		$SocietyID = $_SESSION["society_id"];
		$emailIDList = $this->objFetchData->GetEmailIDToSendNotification(0);

		for($i = 0; $i < sizeof($emailIDList); $i++)
		{
			
			if($emailIDList[$i]['to_email'] <> "")
			{
				$unitID = $emailIDList[$i]['unit'];
				$objAndroid = new android($emailIDList[$i]['to_email'], $_SESSION['society_id'], $unitID);
				$sendMobile=$objAndroid->sendEventNotification($EventTitle,$eventMassage,$EventID);
			}
		}	
		
	}
	
}
?>