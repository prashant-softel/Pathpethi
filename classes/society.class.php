<?php
include_once("include/display_table.class.php");
include_once("defaults.class.php");
include_once("dbconst.class.php");
include_once("bill_period.class.php");

class society
{
	public $actionPage = "../society.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_billperiod;
	
	function __construct($dbConn, $dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		
		$this->display_pg=new display_table($this->m_dbConn);
		$this->obj_billperiod = new bill_period($this->m_dbConn);
		
	}
	public function startProcess()
	{
		$errorExists=0;
		if($_REQUEST['insert']=='Submit' && $errorExists==0)
		{
			/*if($_POST['society_code']<>"" && $_POST['society_name']<>"" && $_POST['circle']<>"" && $_POST['registration_date']<>"" 
			&& $_POST['registration_no']<>"" && $_POST['society_add']<>"" && $_POST['city']<>"" && $_POST['landmark']<>"" && $_POST['region']<>""  
			&& $_POST['postal_code']<>"" && $_POST['country']<>"" && $_POST['phone']<>"" && $_POST['phone2']<>"" && $_POST['circle']<>"" 
			&& $_POST['fax_number']<>"" && $_POST['pan_no']<>"" && $_POST['tan_no']<>"" && $_POST['service_tax_no']<>"" && $_POST['email']<>"" 
			&& $_POST['member_since']<>"")*/
			
			date_default_timezone_set('Asia/Kolkata');
			$societyCreatedDate = date("Y-m-d");
			
			$sqlFetchYearID = "SELECT  *  FROM `year` where '".$societyCreatedDate."' BETWEEN `BeginingDate` AND `EndingDate`";
			$resYearID = $this->m_dbConn->select($sqlFetchYearID);
			$YearID = $resYearID[0]['YearID'];
			if($YearID <> "")
			{
				$bIsSuccess = $this->AddPeriods($_POST['bill_cycle'] , $YearID);
				if($bIsSuccess == false)
				{
					return "unable to generate periods for year  ".$resYearID[0]['YearDescription'];		
				}
			}
			else
			{
				return "year not created please  add year first and then add society";			
			}
			
			if($_POST['society_code']<>"" && $_POST['society_name']<>"" && $_POST['email'])
			{				
				$regExist = false;
				if($_POST['registration_no']<>"")
				{								
					$sql00 = "select count(*)as cnt from society where registration_no = '".$this->m_dbConn->escapeString($_POST['registration_no'])."' and status='Y'";					
					$res00 = $this->m_dbConn->select($sql00);
					if($res00[0]['cnt'] > 0)
					{
						 $regExist = true;
					}				
				}
				
				if($regExist == false)
				{					
					$sql = "select count(*)as cnt from society where society_name = '".$this->m_dbConnRoot->escapeString($_POST['society_name'])."' and landmark='".$this->m_dbConn->escapeString($_POST['landmark'])."' and status='Y'";
					$res = $this->m_dbConn->select($sql);
					
					if($res[0]['cnt']==0)
					{
						$insert_society_root = "INSERT INTO `society`(`society_code`, `society_name`, `dbname`, `send_reminder_sms`,`client_id`) VALUES ('" . $_POST['society_code'] . "','" . $this->m_dbConn->escapeString(trim(ucwords($_POST['society_name']))) . "','" . $_SESSION['dbname'] . "', '".$_POST['send_reminder']."','".$_SESSION['client_id']."')";
						
						$result_society_id = $this->m_dbConnRoot->insert($insert_society_root);
						
						$update_dbname = "UPDATE dbname SET society_id = '" . $result_society_id . "' WHERE dbname = '" . $_SESSION['dbname'] . "'";
						$result_dbname = $this->m_dbConnRoot->update($update_dbname);
						
						$insert_mapping = "INSERT INTO `mapping`(`login_id`, `society_id`, `desc`, `role`, `profile`, `created_by`, `status`, `view`) VALUES ('" . $_SESSION['login_id'] . "', '" . $result_society_id . "', '" . ROLE_SUPER_ADMIN . "', '" . ROLE_SUPER_ADMIN . "', '" . PROFILE_SUPER_ADMIN_ID . "', '" . $_SESSION['login_id'] . "', 2, 'ADMIN')";
						
						$result_mapping = $this->m_dbConnRoot->insert($insert_mapping);
						
						$sqlUpdate = "UPDATE `login` SET `current_mapping`='" . $result_mapping . "' WHERE login_id = '" . $_SESSION['login_id'] . "'";
						$resultUpdate = $this->m_dbConnRoot->update($sqlUpdate);
						
						if($_SESSION['client_id'] > 0)
						{
							$sqlSelectSadmin = "select login_id from login where client_id = '" . $_SESSION['client_id'] . "' and authority = 'self'";
							$resultSelectSadmin = $this->m_dbConnRoot->select($sqlSelectSadmin);
							
							print_r($resultSelectSadmin);
							
							echo 'SAdmin Count : ' . sizeof($resultSelectSadmin);
							for($sadminCnt = 0 ; $sadminCnt < sizeof($resultSelectSadmin) ; $sadminCnt++)
							{
								if($resultSelectSadmin[$sadminCnt]['login_id'] <> $_SESSION['login_id'])
								{
									$insert_mapping_sadmin = "INSERT INTO `mapping`(`login_id`, `society_id`, `desc`, `role`, `profile`, `created_by`, `status`, `view`) VALUES ('" . $resultSelectSadmin[$sadminCnt]['login_id'] . "', '" . $result_society_id . "', '" . ROLE_SUPER_ADMIN . "', '" . ROLE_SUPER_ADMIN . "', '" . PROFILE_SUPER_ADMIN_ID . "', '" . $_SESSION['login_id'] . "', 2, 'ADMIN')";
						
									$result_mapping_sadmin = $this->m_dbConnRoot->insert($insert_mapping_sadmin);
								}
							}
						}						
						
						$insert_mapping = "INSERT INTO `mapping`(`society_id`, `desc`, `code`, `role`, `profile`, `created_by`, `view`) VALUES ('" . $result_society_id . "', '" . ROLE_ADMIN . "', '" . getRandomUniqueCode() . "', '" . ROLE_ADMIN. "', '" . PROFILE_ADMIN_ID . "', '" . $_SESSION['login_id'] . "', 'ADMIN')";
						
						$result_mapping = $this->m_dbConnRoot->insert($insert_mapping);
						
						 $insert_query = "insert into society (`society_id`, `society_code`, `society_name`, `circle`, `registration_date`, `registration_no`, `society_add`,`show_address_in_email`, `city`, `landmark`,
									 `region`, `postal_code`, `country`, `phone`, `phone2`, `fax_number`, `pan_no`, `tan_no`, `service_tax_no`, `email`, `cc_email`, `url`, `member_since`, `bill_cycle`, `int_rate`, 
									 `int_method`, `int_tri_amt`, `rebate_method`, `rebate`, `chq_bounce_charge`, `show_wing`, `show_parking`, `show_area`, `bill_method`, `property_tax_no`, `water_tax_no`, 
									 `calc_int`, `show_share`, `bill_footer`, `sms_start_text`, `sms_end_text`, `send_reminder_sms`,`bill_due_date`,`show_floor`,`society_creation_yearid`,`unit_presentation`,`bill_as_link`,`email_contactno`,`neft_notify_by_email`,`gstin_no`,`apply_service_tax`,`apply_GST_on_Interest`,`apply_GST_above_Threshold`,`service_tax_threshold`,`igst_tax_rate`,`cgst_tax_rate`,`sgst_tax_rate`,`cess_tax_rate`,`gst_start_date`) values ('" . $result_society_id . "', '".$_POST['society_code']."','".$this->m_dbConn->escapeString(trim(ucwords($_POST['society_name'])))."',
									 '".$this->m_dbConn->escapeString(trim(ucwords($_POST['circle'])))."','" . getDBFormatDate($_POST['registration_date']) . "' , '".$this->m_dbConn->escapeString(trim($_POST['registration_no']))."',
									 '".$this->m_dbConn->escapeString(trim(ucwords(     $_POST['society_add'])))."', '".$_POST['show_address_in_email']."', '".$this->m_dbConn->escapeString(trim(ucwords($_POST['city'])))."',
									 '".$this->m_dbConn->escapeString(trim(ucwords($_POST['landmark'])))."', '".$this->m_dbConn->escapeString(trim(ucwords($_POST['region'])))."', '".$_POST['postal_code']."', 
									 '".(trim(ucwords($_POST['country'])))."', '".$_POST['phone']."', '".$_POST['phone2']."', '".$_POST['fax_number']."', '".$_POST['pan_no']."', '".$_POST['tan_no']."', '".$_POST['service_tax_no']."', 
									 '".$this->m_dbConn->escapeString(trim($_POST['email']))."', '".$this->m_dbConn->escapeString(trim($_POST['cc_email']))."', '".$this->m_dbConn->escapeString(trim($_POST['url']))."', '".$_POST['member_since']."', '".$_POST['bill_cycle']."', '".$_POST['int_rate']."', '".$_POST['int_method']."',
									 '".$_POST['int_tri_amt']."', '".$_POST['rebate_method']."', '".$_POST['rebate']."', '".$_POST['chq_bounce_charge']."', '".$_POST['show_wing']."', '".$_POST['show_parking']."', '".$_POST['show_area']."', '".$_POST['bill_method']."',
									 '".$_POST['property_tax_no']."', '".$_POST['water_tax_no']."', '".$_POST['calc_int']."', '".$_POST['show_share']."', 
									 '".$_POST['bill_footer']."', '".$_POST['sms_start_text']."', '".$_POST['sms_end_text']."', '".$_POST['send_reminder']."', '".$_POST['bill_due_date']."', '".$_POST['show_floor']."','".$YearID."','".$_POST['unit_presentation']."','".$_POST['bill_as_link']."','".$_POST['email_contactno']."','".$_POST['neft_notify_by_email']."','".$_POST['gstin_no']."','".$_POST['apply_service_tax']."','".$_POST['apply_GST_On_Interest']."','".$_POST['apply_GST_above_Threshold']."','".$_POST['service_tax_threshold']."','".$_POST['igst_tax_rate']."','".$_POST['cgst_tax_rate']."','".$_POST['sgst_tax_rate']."','".$_POST['cess_tax_rate']."','".getDBFormatDate($_POST['gst_start_date'])."')";
					
						//echo $insert_query;
						
						$data=$this->m_dbConn->insert($insert_query);
						$_SESSION['gst_start_date'] = getDBFormatDate($_POST['gst_start_date']);
						//echo '<br>';
						
						/*$sql = "insert into login(`society_id`,`security_no`,`member_id`,`password`,`authority`,`name`, `current_society`)values('".$data."','".$_POST['key']."','".$this->m_dbConn->escapeString($_POST['admin_user'])."','".$this->m_dbConn->escapeString($_POST['admin_pass'])."','Super Admin','Admin', '" . $data . "')";
						$res = $this->m_dbConn->insert($sql);
						
						$sqlUpdate = "UPDATE `login` SET `current_society`='" . $data . "' WHERE login_id = '" . $_SESSION['login_id'] . "'";
						$resultUpdate = $this->m_dbConn->update($sqlUpdate);*/
						
						/*$sql1 = "insert into del_control_admin(`society_id`,`login_id`,`del_control_admin`)values('".$data."','".$res."','1')";
						$res1 = $this->m_dbConn->insert($sql1);*/
						
						$sqlDefault = "INSERT INTO `appdefault`(`APP_DEFAULT_SOCIETY`, `changed_by`) VALUES ('" . $result_society_id . "', '" . $_SESSION['login_id'] . "')";
						$resultDefault = $this->m_dbConn->insert($sqlDefault);
						
						$sqlDefault = "INSERT INTO `counter`(`society_id`) VALUES ('" . $result_society_id . "')";
						$resultDefault = $this->m_dbConn->insert($sqlDefault);
						
						$obj_default = new defaults($this->m_dbConn);						
						$obj_default->getDefaults($data, true);
						
						if($_POST['unit_presentation'] <> $_POST['unit_presentation_previous_value'])
						{
							$up_query = "update unit set  `unit_presentation` = '".$_POST['unit_presentation']."' where  `unit_presentation` = '".$_POST['unit_presentation_previous_value']."' ";
							$data = $this->m_dbConn->update($up_query);
						}
						
												
						?>
						<script>window.location.href = '../society.php?id=<?php echo $_SESSION['society_id'];?>&show&imp'</script>
						<?php
						//return "Insert";
					}
					else
					{
						return ucwords($_POST['society_name']).' society is exist under this landmark - ' . $_POST['landmark'];
					}
				}
				else
				{
					return "Already exist this registration no. - " . $_POST['registration_no'];	
				}
			}
			else
			{
				return "All * Field Required";
			}
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			if($_POST['society_code']<>"")
			{	
			
			 	$up_query="update society set `society_name`='".$this->m_dbConn->escapeString(trim(ucwords($_POST['society_name'])))."',`society_add`='".$this->m_dbConn->escapeString(trim(($_POST['society_add'])))."',`show_address_in_email`='".$_POST['show_address_in_email']."',
						`registration_date`='" . getDBFormatDate($_POST['registration_date']) . "',`landmark`='".$this->m_dbConn->escapeString(trim(ucwords($_POST['landmark'])))."',`state`='".$_POST['state_id']."',`city`='".$this->m_dbConn->escapeString(trim(ucwords($_POST['city'])))."',`region`='".$this->m_dbConn->escapeString(trim(ucwords($_POST['region'])))."',
						`postal_code`='".$_POST['postal_code']."',`country`='".$this->m_dbConn->escapeString(trim(ucwords($_POST['country'])))."',`phone`='".$_POST['phone']."',`phone2`='".$_POST['phone2']."',
						`fax_number`='".$_POST['fax_number']."',`email`='".$this->m_dbConn->escapeString(trim($_POST['email']))."',`cc_email`='".$this->m_dbConn->escapeString(trim($_POST['cc_email']))."',`member_since`='".$_POST['member_since']."',`bill_cycle`='".$_POST['bill_cycle']."',
						`int_rate`='".$_POST['int_rate']."',`int_tri_amt`='".$_POST['int_tri_amt']."',`int_method`='".$_POST['int_method']."',`rebate_method`='".$_POST['rebate_method']."',
						`chq_bounce_charge`='".$_POST['chq_bounce_charge']."',`show_wing`='".$_POST['show_wing']."',`calc_int`='".$_POST['calc_int']."',`show_parking`='".$_POST['show_parking']."',
						`show_area`='".$_POST['show_area']."',`bill_method`='".$_POST['bill_method']."',`rebate`='".$_POST['rebate']."',`property_tax_no`='".$_POST['property_tax_no']."',`water_tax_no`='".$_POST['water_tax_no']."',
						`show_share`='".$_POST['show_share']."', `registration_no`='".$this->m_dbConn->escapeString(trim($_POST['registration_no']))."',`circle` ='".$this->m_dbConn->escapeString(trim(ucwords($_POST['circle'])))."',`pan_no` = '".$_POST['pan_no']."' ,
						`tan_no` ='".$_POST['tan_no']."', `service_tax_no` ='".$_POST['service_tax_no']."',`url`='".$this->m_dbConn->escapeString(trim($_POST['url']))."', `bill_footer`='".$_POST['bill_footer']."',
						`sms_start_text` = '".$_POST['sms_start_text']."', `sms_end_text` = '".$_POST['sms_end_text']."', `send_reminder_sms` = '".$_POST['send_reminder']."', `bill_due_date` = '".$_POST['bill_due_date']."', `show_floor` = '".$_POST['show_floor']."',`unit_presentation` = '".$_POST['unit_presentation']."' ,`bill_as_link` = '".$_POST['bill_as_link']."',`email_contactno` = '".$_POST['email_contactno']."' , `neft_notify_by_email` = '".$_POST['neft_notify_by_email']."' , `gstin_no` = '".$_POST['gstin_no']."', `apply_service_tax` = '".$_POST['apply_service_tax']."',`apply_GST_On_Interest` = '".$_POST['apply_GST_On_Interest']."',`apply_GST_above_Threshold` = '".$_POST['apply_GST_above_Threshold']."', `service_tax_threshold` = '".$_POST['service_tax_threshold']."', `igst_tax_rate` = '".$_POST['igst_tax_rate']."' , `cgst_tax_rate` = '".$_POST['cgst_tax_rate']."' , `sgst_tax_rate` = '".$_POST['sgst_tax_rate']."' , `cess_tax_rate` = '".$_POST['cess_tax_rate']."', `gst_start_date` = '".getDBFormatDate($_POST['gst_start_date'])."' where society_code='".$_POST['society_code']."' and society_id ='".$_SESSION['society_id']."'";
		
				$data=$this->m_dbConn->update($up_query);
				
				$_SESSION['gst_start_date'] = getDBFormatDate($_POST['gst_start_date']);
				$up_query_soc_name = "UPDATE `society` SET `society_name`='" .$this->m_dbConn->escapeString(trim(ucwords($_POST['society_name']))). "', `send_reminder_sms` = '".$_POST['send_reminder']."' WHERE society_code='".$_POST['society_code']."' and society_id ='".$_SESSION['society_id']."'";
				
				$update_soc_name = $this->m_dbConnRoot->update($up_query_soc_name);
				
				//$updateAppDefault = "UPDATE `appdefault` SET `APP_DEFAULT_EMAILID`= '".$this->m_dbConn->escapeString(trim($_POST['email']))."' WHERE APP_DEFAULT_SOCIETY = '" . $_SESSION['society_id'] . "' and `changed_by`= '" . $_SESSION['login_id'] . "'";
		
				//$resultUpdate = $this->m_dbConn->update($updateAppDefault);
				
				if($_POST['unit_presentation'] <> $_POST['unit_presentation_previous_value'])
				{
					$up_query = "update unit set  `unit_presentation` = '".$_POST['unit_presentation']."' where  `unit_presentation` = '".$_POST['unit_presentation_previous_value']."' ";
					$data = $this->m_dbConn->update($up_query);
				}
				
				?>
               <script>window.location.href = '../society.php?id=<?php echo $_SESSION['society_id'];?>&show&imp'</script>
                <?php
				//echo $up_query;
				//echo $sql;
				return "Update";
			}
		}
		else
		{
			return $errString;
		}
	}
	public function combobox($query,$id,$showDefaultText = false)
	{
		if($showDefaultText == true)
		{
			$str ="<option value='0'>Please Select</option>";
		}
		else
		{
			$str;	
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
						if($v==$id)
						{
							$sel = "selected";
						}
						else
						{
							$sel = "";	
						}
						$str.="<OPTION VALUE=".$v." ".$sel.">";
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
			$thheader=array('Wing','Society Name','Address','City','Region','Postal Code','Country','Phone No.1','Phone No.2','Fax No.','Email id','Member Since');
			$this->display_pg->edit="getsociety";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="society.php";
			$res=$this->society_list_show($rsas);
			return $res;
	}
	public function pgnation()
	{
		if(isset($_SESSION['sadmin']))
		{
			//$sql1 = "select * from society where status='Y' order by society_id desc";
			
			//$cntr = "select count(*) as cnt from society where status='Y' ";
			$sql1 = "select * from society where status='Y' and society_id='".$_SESSION['society_id']."' order by society_id desc";
			//echo $sql1;
			$cntr = "select count(*) as cnt from society where status='Y' and society_id='".$_SESSION['society_id']."'";
		}
		else
		{
			$sql1 = "select * from society where status='Y' and society_id='".$_SESSION['society_id']."' order by society_id desc";
			//echo $sql1;
			$cntr = "select count(*) as cnt from society where status='Y' and society_id='".$_SESSION['society_id']."'";
			
		}
		
		$this->display_pg->sql1=$sql1;
		$this->display_pg->cntr1=$cntr;
		$this->display_pg->mainpg="society.php";
		$limit="20";
		$page = $_REQUEST['page'];
		$extra="";
		$res=$this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;
	}
	
	public function society_list_show($res)
	{
		if($res<>"")
		{
		?>
		<table align="center" border="0">
		<tr height="30" bgcolor="#CCCCCC">
        	<th width="220">Society Name</th>
            <th width="200">Address</th>
            <th width="100">Landmark</th>
            <th width="90">Phone No.</th>
            <th width="170">Email</th>
            <?php if(isset($_SESSION['sadmin'])){?>
            <th width="100">View</th>
            <th width="100">Add</th>
            <?php }?>
            
            <?php if(isset($_SESSION['admin'])){?>
            <th width="100">Action</th>
            <?php }?>
            <th width="90">View Report</th>
            <!--<th width="90">Import Wing</th>-->
        	<th width="50">Edit</th>
            <?php if(isset($_SESSION['sadmin'])){?>
            <th width="70">Delete</th>
            <?php }?>
        </tr>
        <?php foreach($res as $k => $v){?>
        <tr height="25" bgcolor="#BDD8F4" align="center">
        	<td align="center"><?php echo $res[$k]['society_name'];?></td>
            <td align="center">
			<div style="overflow-y:scroll;overflow-x:hidden;width:190px; height:80px; border:solid #CCCCCC 1px;" align="center">
			<?php echo $res[$k]['society_add'];?>
            </div>
            </td>
            <td align="center"><?php echo $res[$k]['landmark'];?></td>
            <td align="center"><?php echo $res[$k]['phone'];?></td>
            <td align="center"><a href="mailto:<?php echo $res[$k]['email'];?>" style="color:#0000FF" target="_blank"><?php echo $res[$k]['email'];?></a></td>
            
            <?php if(isset($_SESSION['sadmin'])){?>
            <td align="center"><a href="wing.php?imp&idd=<?php echo time();?>&sa&sid=<?php echo $res[$k]['society_id'];?>&id=<?php echo rand('0000000','9999999');?>" style="color:#0000FF;"><b>View Wing</b></a></td>
            <td align="center"><a href="wing.php?imp&ssid=<?php echo $res[$k]['society_id'];?>&s&idd=<?php echo time();?>" style="color:#0000FF;"><b>Add Wing</b></a></td>
            <?php }else{?>
            <td align="center"><a href="wing.php?imp&ssid=<?php echo $res[$k]['society_id'];?>&s&idd=<?php echo time();?>" style="color:#0000FF;"><b>Add Wing</b></a></td>
            <?php }?>
            
            
            <td align="center">
            <a href="reports.php?&sid=<?php echo $res[$k]['society_id'];?>" style="color:#0000FF;"><b>Member's Due</b></a>
            </td>
            
           <!-- <td align="center">
            <a href="wing_import.php?&sid=<?php //echo $res[$k]['society_id'];?>" style="color:#0000FF;"><b>Import Wing</b></a>
            </td>-->
            
            <td align="center">
            <a href="javascript:void(0);" onclick="society_edit(<?php echo $res[$k]['society_id']?>)"><img src="images/edit.gif" /></a>
            </td>
            
            <?php if(isset($_SESSION['sadmin'])){?>
            <td align="center">
            <?php if($this->chk_delete_perm_sadmin()==1){?>
            <a href="javascript:void(0);" onclick="del_society(<?php echo $res[$k]['society_id']?>);"><img src="images/del.gif" /></a>
            <?php }else{?>
            <a href="del_control_sadmin.php?prm" target="_blank" style="text-decoration:none;"><font color=#FF0000 style='font-size:10px;'><b>Not Allowed</b></font></a>
            <?php }?>
            </td>
            <?php }?>
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
	public function chk_delete_perm_sadmin()
	{
		$sql = "select * from del_control_sadmin where status='Y'";
		$res = $this->m_dbConn->select($sql);
		return $res[0]['del_control_sadmin'];
	}
	public function selecting()
	{
		$sql1 = "select s.society_id,s.society_code,s.society_name,s.circle,DATE_FORMAT(registration_date, '%d-%m-%Y'),s.registration_no,s.society_add,s.city,s.landmark,s.state,s.region,s.postal_code,s.country,s.phone,s.phone2,s.fax_number, s.pan_no, s.tan_no, s.service_tax_no, s.email,s.url, s.member_since, s.bill_cycle, s.int_rate, s.int_tri_amt, s.int_method, s.rebate_method, s.rebate, s.chq_bounce_charge, s.bill_method, s.show_wing, s.show_parking, s.show_area,s.calc_int,s.property_tax_no,s.water_tax_no,s.show_share,s.bill_footer, s.sms_start_text, s.sms_end_text, s.send_reminder_sms, s.bill_due_date,s.show_floor,s.unit_presentation, s.cc_email ,s.bill_as_link,s.email_contactno,s.neft_notify_by_email,s.show_address_in_email,s.apply_service_tax,s.service_tax_threshold,s.igst_tax_rate,s.cgst_tax_rate,s.sgst_tax_rate,s.cess_tax_rate ,s.gstin_no,s.apply_GST_On_Interest,s.apply_GST_above_Threshold,DATE_FORMAT(s.gst_start_date, '%d-%m-%Y') from society as s where s.society_id='".$_REQUEST['societyId']."'";
		$var=$this->m_dbConn->select($sql1);
		return $var;
	}
	public function deleting()
	{
		$sql0 = "select count(*)as cnt from unit where society_id='".$_REQUEST['societyId']."' and status='Y'";
		$res0 = $this->m_dbConn->select($sql0);
		
		if($res0[0]['cnt']==0)
		{
			$sql1="update society set status='N' where society_id='".$_REQUEST['societyId']."'";
			$this->m_dbConn->update($sql1);
			
			echo "msg1";
		}
		else
		{
			echo "msg";	
		}
	}
	
	public function del_society()
	{
		$sql1 = "update society set status='N' where society_id='".$_REQUEST['sss_id']."'";
		$this->m_dbConn->update($sql1);
		
		$sql2 = "update login set status='N' where society_id='".$_REQUEST['sss_id']."'";
		$this->m_dbConn->update($sql2);
		
	}
	
	public function check_socty_exist()
	{
		$sql = "select count(*)as cnt from society where society_name = '".$this->m_dbConn->escapeString($_REQUEST['soc_name'])."' and landmark='".$this->m_dbConn->escapeString($_REQUEST['landmark'])."' and status='Y'";
		$res = $this->m_dbConn->select($sql);
		
		if($res[0]['cnt']==0)
		{
			echo 0;
			echo '####';
		}
		else
		{
			echo 1;
			echo '####'.$_REQUEST['landmark'];
		}
	}
///////////////////////////////////////////////////////
/*	
	public function getInterestMethod($id)
	{
		
	$str.="<option value=''>Please Select</option>";
	$data =array('delay after due days','Full month');
	
		foreach($data as $value)
			{
				
				
						$str.="<OPTION VALUE=".$value.">";
					
					
				}
			
		
			return $str;
	}
	
*/

public function AddPeriods($cycleID , $yearID)
{
		$PeriodName = '';		
		$IsSuccess = false;	
				
		$FetchPeriod = $this->m_dbConn->select("select count(YearID) as count from `period` where `Billing_cycle`='".$cycleID."' and `YearID`= '".$yearID."'");
									
		if($FetchPeriod[0]['count'] == 0)
		{ 
		
			$months = getMonths($cycleID);
			
			$PrevYear =  $yearID - 1;
			$sqlFetchData = $this->m_dbConn->select("SELECT * FROM `year`  where  `YearID`= '".$PrevYear."'");
			
			$begin_date = $this->obj_billperiod->getBeginDate(end($months),$sqlFetchData[0]['YearDescription']);
			$end_date = $this->obj_billperiod->getEndDate(end($months),$sqlFetchData[0]['YearDescription']); 
									
			$insert_query="insert into period(`Billing_cycle`,`Type`,`YearID`,`PrevPeriodID`,`IsYearEnd`,`BeginingDate`,`EndingDate` )
									 values(".$cycleID.",'".end($months)."',".$PrevYear.",'0', '1','".$begin_date ."','".$end_date."')";
			$prevPeriod = $this->m_dbConn->insert($insert_query);	
			
			$this->obj_billperiod->setPeriod($months ,$cycleID,$yearID);
			$IsSuccess = true;	
		}
		else
		{
				$IsSuccess = false;	
		}
		
		return $IsSuccess;
				
}
	
	public function getMembers() {
		$sql = "SELECT DISTINCT owner_name FROM member_main";
		return $this->m_dbConn->select($sql);
	}

}
?>