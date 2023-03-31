<?php if(!isset($_SESSION)){ session_start(); }
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once ("dbconst.class.php");
include_once('../swift/swift_required.php');
include_once( "include/fetch_data.php");
include_once("utility.class.php");
include_once("android.class.php");

class doc_templates extends dbop
{
	//public $actionPage = "../events_view.php";
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

	public function combobox07($query,$id)
	{
		//$str.="<option value=''>All</option>";
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
	
	public function combobox_for_overdue_payment($query,$id)
	{
		//$str.="<option value=''>All</option>";
		$str.="<option value=''>Please Select</option>";
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i = 0;
				$due_amt = $this->obj_Utility->getDueAmount($value['unit_id']);
				if($due_amt > 0)
				{
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
							$str.=$v." - ".$due_amt."</OPTION>";
						}
						$i++;
					}
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
	
	public function comboboxRoot($query,$id)
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
						if($v==$id)
						{
							$sel = "selected";
						}
						else
						{
							$sel = "";	
						}
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
	
	public function fetch_template_details($id)
	{	
		$sqlQuery = "select * from document_templates where id='".$id."'";
		$res = $this->m_dbConnRoot->select($sqlQuery);
		return $res[0];
	}
	
	public function fetch_data($to_pass, $nominees_info = "")
	{
		$final = "";
		if($to_pass['template_id'] == 27 && $to_pass['unit_id'] <> 0) //overdue payment
		{
			$get_primary_name = $this->obj_Utility->GetUnitNo($to_pass['unit_id']);
			
			$get_due_amt = $this->obj_Utility->getDueAmount($to_pass['unit_id']);
			$get_due_amt_comma = number_format($get_due_amt,2);
		
			$templates = $this->fetch_template_details($to_pass['template_id']);
		
			$sql01 = "select society_name from society where society_id = '".$_SESSION['society_id']."'";
			$sql11 = $this->m_dbConn->select($sql01);
			$society_name = $sql11[0]['society_name'];

			$today_date = date("d-m-Y");
			
			$old = array("{Date_Today}","{Primary_Name}","{Due_Amount}","{Society_Name}");
			$new = array($today_date,$get_primary_name[0]['primary_owner_name'],$get_due_amt_comma,$society_name);
			
			$final = str_replace($old,$new,$templates['template_data']);
		}
		else if($to_pass['template_id'] == 25) //agm notice
		{
			$templates = $this->fetch_template_details($to_pass['template_id']);

			$sql01 = "select society_name from society where society_id = '".$_SESSION['society_id']."'";
			$sql11 = $this->m_dbConn->select($sql01);
			$society_name = $sql11[0]['society_name'];

			$correct_date=date_create($to_pass['date_of_meeting']);

			$day = date_format($correct_date,"l");
			$date = date_format($correct_date,"d");
			$month = date_format($correct_date,"M");
			$year = date_format($correct_date,"Y");
			$toput_date = $date." of ".$month;
			
			$toput_time_and_venue = $to_pass['time_of_meeting'].", ".$to_pass['venue'];
			
			$old = array("{day}","{date of month}","{year}","{time and venue}","{last AGM date}","{start time}","{Association Name}");
			$new = array($day,$toput_date,$year,$toput_time_and_venue,$to_pass['last_agm_date'],$to_pass['time_of_meeting'],$society_name);
			
			$final = str_replace($old,$new,$templates['template_data']);
			
		}
		else if($to_pass['template_id'] == 1) //associate member form 
		{
			$templates = $this->fetch_template_details($to_pass['template_id']);
			$final = $templates['template_data'];
			//echo $final;
		}
		else if(($to_pass['template_id'] == 29 || $to_pass['template_id'] == 28) && $to_pass['fine_rs_id'] <> 0) //credit(refund) and debit(fine) resp
		{
			$templates = $this->fetch_template_details($to_pass['template_id']);
			
			$sql01 = "select society_name from society where society_id = '".$_SESSION['society_id']."'";
			$sql11 = $this->m_dbConn->select($sql01);
			$society_name = $sql11[0]['society_name'];
			
			$sql01 = "SELECT rs.ID, rs.UnitID, rs.Amount, rs.Comments, mm.unit, mm.primary_owner_name FROM `reversal_credits` rs, `member_main` mm where rs.UnitID = mm.unit and rs.ID = '".$to_pass['fine_rs_id']."' and ownership_status = 1";
			$sql11 = $this->m_dbConn->select($sql01);
			
			$old = array("{Member_Name}","{dc_amount}","{dc_reason}","{SOCIETY_NAME}");
			$new = array($sql11[0]['primary_owner_name'],abs($sql11[0]['Amount']),$sql11[0]['Comments'],$society_name);
			
			$final = str_replace($old,$new,$templates['template_data']);
		}
		else if($to_pass['template_id'] == 31 || $to_pass['template_id'] == 33) //passport and domicile resp.
		{
			$templates = $this->fetch_template_details($to_pass['template_id']);
			
			$sql01 = "select society_name, registration_no, society_add,"./*_line_1, society_add_line_2,*/ "city from society where society_id = '".$_SESSION['society_id']."'";
			$sql11 = $this->m_dbConn->select($sql01);
			
			$today_date = date("d-m-Y");
			$old = array("{SOCIETY_NAME}","{SOCIETY_REGISTRATION_NUMBER}","{ADDRESS LINE 1}","{LETTER_DATE}","{APPLIER_NAME}","{RELATION_WITH_OWNER}","{OWNER_NAME}","{OWNER_ADDRESS}","{SINCE_STAYING_DATE}","{SOCIETY_CITY}","{ADDRESS LINE 2}");
			
			$new = array($sql11[0]['society_name'],$sql11[0]['registration_no'],$sql11[0]['society_add'],$today_date,$to_pass['applier_name'],$to_pass['relation'],$to_pass['owner_name'],$to_pass['address'],$to_pass['start_date'],$sql11[0]['city']," ");
			
			$final = str_replace($old,$new,$templates['template_data']);
		}
		else if($to_pass['template_id'] == 34) //bank noc
		{
			$templates = $this->fetch_template_details($to_pass['template_id']);
			
			$sql01 = "select society_name, registration_no, society_add,"./*_line_1, society_add_line_2*/" cc_no, cc_date from society where society_id = '".$_SESSION['society_id']."'";
			$sql11 = $this->m_dbConn->select($sql01);
			
			$today_date = date("d-m-Y");
			$old = array("{SOCIETY_NAME}","{SOCIETY_REGISTRATION_NUMBER}","{ADDRESS LINE 1}","{ADDRESS LINE 2}","{LETTER_DATE}","{AUTHORISED_NAME}","{BANK_NAME}","{BANK_ADDRESS}","{FLAT_NUMBER}","{OWNER_NAME}","{CC_NUMBER}","{CC_DATE}","{FLAT_COST}");
			
			$new = array($sql11[0]['society_name'],$sql11[0]['registration_no'],$sql11[0]['society_add']," ",$today_date,$to_pass['manager_name'],$to_pass['bank_name'],$to_pass['bank_address'],$to_pass['flat_no'],$to_pass['flat_owner_name'],$to_pass['cc_no'],$to_pass['cc_date'],$to_pass['flat_cost']);
			
			$final = str_replace($old,$new,$templates['template_data']);
		}
		else if($to_pass['template_id'] == 35) //lnl noc
		{
			$templates = $this->fetch_template_details($to_pass['template_id']);
			
			$sql01 = "select society_name, registration_no, society_add"./*_line_1, society_add_line_2*/" from society where society_id = '".$_SESSION['society_id']."'";
			$sql11 = $this->m_dbConn->select($sql01);
			
			$sql02 = "select unit_presentation from unit where unit_no = '".$to_pass['flat_no']."'";
			$sql22 = $this->m_dbConn->select($sql02);
			$unit_presentation = $sql22[0]['unit_presentation'];
			
			$today_date = date("d-m-Y");
			if($unit_presentation != 3)
			{
				$old = array("{SOCIETY_NAME}","{SOCIETY_REGISTRATION_NUMBER}","{ADDRESS LINE 1}","{ADDRESS LINE 2}","{LETTER_DATE}","{OWNER_NAME}","{OWNER_HIS_HER}","{FLAT_NO}","{TENANT}");
				$new = array($sql11[0]['society_name'],$sql11[0]['registration_no'],$sql11[0]['society_add']," ",$today_date,$to_pass['flat_owner_name'],$to_pass['gender'],$to_pass['flat_no'],$to_pass['tenant_name']);
			}
			else
			{
				$old = array("{SOCIETY_NAME}","{SOCIETY_REGISTRATION_NUMBER}","{ADDRESS LINE 1}","{ADDRESS LINE 2}","{LETTER_DATE}","{OWNER_NAME}","{OWNER_HIS_HER}","{FLAT_NO}","{TENANT}","only");
				$new = array($sql11[0]['society_name'],$sql11[0]['registration_no'],$sql11[0]['society_add']," ",$today_date,$to_pass['flat_owner_name'],$to_pass['gender'],$to_pass['flat_no'],$to_pass['tenant_name']," ");
			}
			
			$final = str_replace($old,$new,$templates['template_data']);
		}
		else if($to_pass['template_id'] == 36) //em noc
		{
			$templates = $this->fetch_template_details($to_pass['template_id']);
			
			$sql01 = "select society_name, registration_no, society_add"./*_line_1, society_add_line_2*/" from society where society_id = '".$_SESSION['society_id']."'";
			$sql11 = $this->m_dbConn->select($sql01);
			
			$today_date = date("d-m-Y");
			
			$old = array("{SOCIETY_NAME}","{SOCIETY_REGISTRATION_NUMBER}","{ADDRESS LINE 1}","{ADDRESS LINE 2}","{LETTER_DATE}","{OWNER_NAME}","{HIS_HER}","{FLAT_NO}","{CURRENT_ELECTRICITY_NAME}","{OWNER_ADDRESS}");
			$new = array($sql11[0]['society_name'],$sql11[0]['registration_no'],$sql11[0]['society_add']," ",$today_date,$to_pass['flat_owner_name'],$to_pass['gender'],$to_pass['flat_no'],$to_pass['current_em'],$to_pass['flat_owner_address']);
			
			$final = str_replace($old,$new,$templates['template_data']);
		}
		else if($to_pass['template_id'] == 37 || $to_pass['template_id'] == 38) //web access blocked and web access restored resp.
		{
			$templates = $this->fetch_template_details($to_pass['template_id']);
			
			$sql01 = "select society_name, registration_no, society_add"./*_line_1, society_add_line_2*/" from society where society_id = '".$_SESSION['society_id']."'";
			$sql11 = $this->m_dbConn->select($sql01);
			
			$today_date = date("d-m-Y");
			
			$sql02 = "select u.unit_id, u.block_unit, u.block_desc, mm.primary_owner_name from `unit` u, `member_main` mm where u.unit_id = mm.unit and u.unit_id = '".$to_pass['unit_id']."' and mm.ownership_status = 1";
			$sql22 = $this->m_dbConn->select($sql02);
			
			$old = array("{SOCIETY_NAME}","{SOCIETY_REGISTRATION_NUMBER}","{ADDRESS LINE 1}","{ADDRESS LINE 2}","{LETTER_DATE}","{member_name}","{block_reason}");
			$new = array($sql11[0]['society_name'],$sql11[0]['registration_no'],$sql11[0]['society_add']," ",$today_date,$sql22[0]['primary_owner_name'],$sql22[0]['block_desc']);
			
			$final = str_replace($old,$new,$templates['template_data']);
		}
		else if($to_pass['template_id'] == 49) //nomination form
		{
			$templates = $this->fetch_template_details($to_pass['template_id']);
			
			$sql01 = "select society_name, registration_no, society_add"./*_line_1, society_add_line_2*/" from society where society_id = '".$_SESSION['society_id']."'";
			$sql11 = $this->m_dbConn->select($sql01);
			
			$old = array("{OWNER_NAME}","{SHARE_SERIAL_NUMBER}","{SHARE_ISSUE_DATE}","{FIVE_OR_TEN_SHARES}","{SHARE_PER_AMOUNT}","{SHARE_START_NUMBER}","{SHARE_END_NUMBER}","{FLAT_NAME}","{TOTAL_AREA}","{WITNESS1_NAME_1}","{WITNESS1_ADDRESS_1}","{WITNESS2_NAME_2}","{WITNESS2_ADDRESS_1}","{SOCIETY_NAME}","{ADDRESS LINE 1}","{ADDRESS_LINE 2}","{NOMINATION_ID}");
			$new = array($to_pass['flat_owner_name'],$to_pass['sc_no'],$to_pass['sc_date'],$to_pass['no_of_shares'],$to_pass['amt_per_share'],$to_pass['start_no'],$to_pass['end_no'],$to_pass['flat_no'],$to_pass['area'],$to_pass['witness_name_1'],$to_pass['witness_address_1'],$to_pass['witness_name_2'],$to_pass['witness_address_2'],$sql11[0]['society_name'],$sql11[0]['society_add']," "," ");
			
			$before_final_replace = str_replace($old,$new,$templates['template_data']);
			
			$converted = array();
			for($iCnt = 0; $iCnt < 5; $iCnt++)
			{
				if($iCnt < sizeof($nominees_info))
				{
					$converted[$iCnt] = (array)$nominees_info[$iCnt];
				}
				else
				{
					$new_empty = array("name" => '', "address" => '', "relation" => '', "share" => '', "dob" => '', "guardian_name" => '');
					array_push($converted,$new_empty);
				}
			}
			
			$guardian_string = "6.{No} As the nominee/s at Sr.No {Sr.No} is the minor, I hereby appoint Shri/Shrimati {Guardian_Name} as the guardian/legal representative of the minor to represent the minor nominee in matters connected with this nomination.";
			
			$old_1 = array("{NOMINEE_1A}","{NOMINEE_1B}","{NOMINEE_1C}","{NOMINEE_1D}","{NOMINEE_1E}","{NOMINEE_2A}","{NOMINEE_2B}","{NOMINEE_2C}","{NOMINEE_2D}","{NOMINEE_2E}","{NOMINEE_3A}","{NOMINEE_3B}","{NOMINEE_3C}","{NOMINEE_3D}","{NOMINEE_3E}","{NOMINEE_4A}","{NOMINEE_4B}","{NOMINEE_4C}","{NOMINEE_4D}","{NOMINEE_4E}","{NOMINEE_5A}","{NOMINEE_5B}","{NOMINEE_5C}","{NOMINEE_5D}","{NOMINEE_5E}","{FIRST_NOMINEE_NAME}");
			$new_1 = array($converted[0]['name'],$converted[0]['address'],$converted[0]['relation'],$converted[0]['share'],$converted[0]['dob'],$converted[1]['name'],$converted[1]['address'],$converted[1]['relation'],$converted[1]['share'],$converted[1]['dob'],$converted[2]['name'],$converted[2]['address'],$converted[2]['relation'],$converted[2]['share'],$converted[2]['dob'],$converted[3]['name'],$converted[3]['address'],$converted[3]['relation'],$converted[3]['share'],$converted[3]['dob'],$converted[4]['name'],$converted[4]['address'],$converted[4]['relation'],$converted[4]['share'],$converted[4]['dob'],$converted[0]['name']);
			
			$before_guardian_string = str_replace($old_1,$new_1,$before_final_replace);
			
			$replace_guardian_string = array();
			$guardian_counter = 1;
			for($iCounter = 0; $iCounter < 5; $iCounter++)
			{
				if($converted[$iCounter]["guardian_name"] == "")
				{
					$replace_guardian_string[$iCounter] = "";
				}
				else
				{
					$make_guardian_string_1 = str_replace("{Sr.No}",$iCounter + 1,$guardian_string);
					$make_guardian_string_2 = str_replace("{Guardian_Name}",$converted[$iCounter]["guardian_name"],$make_guardian_string_1);
					$make_guardian_string_3 = str_replace("{No}",$guardian_counter,$make_guardian_string_2);
					$replace_guardian_string[$iCounter] = $make_guardian_string_3;
					$guardian_counter++;
				}
			}
			
			$old_2 = array("{Guardian_String_1}","{Guardian_String_2}","{Guardian_String_3}","{Guardian_String_4}","{Guardian_String_5}");
			$new_2 = array($replace_guardian_string[0],$replace_guardian_string[1],$replace_guardian_string[2],$replace_guardian_string[3],$replace_guardian_string[4]);
			$final = str_replace($old_2,$new_2,$before_guardian_string);
			
			if($to_pass['triplicate'] == 1)
			{
				$make_duplicate = str_replace("ORIGINAL","DUPLICATE",$final);
				$make_triplicate = str_replace("ORIGINAL","TRIPLICATE",$final);
				$final = $final."<br>".$make_duplicate."<br>".$make_triplicate;
			}
		}
		
		return $final;
		
	}
	
	function fetch_unit_id($rc_id)
	{
		$sql01 = "select UnitID from reversal_credits where ID = '".$rc_id."'";
		$sql11 = $this->m_dbConn->select($sql01);
		
		return $sql11[0]['UnitID'];
	}
	
	function fetch_subject($temp_id)
	{
		$sql01 = "select template_subject from document_templates where id = '".$temp_id."'";
		$sql11 = $this->m_dbConnRoot->select($sql01);
		
		return $sql11[0]['template_subject'];
	}
	
	function fetch_unit_details_and_cc_dets($unit_id)
	{
		$sql01 = "SELECT mm.unit, u.unit_no, u.unit_id, mm.primary_owner_name, u.society_id, s.society_id, s.cc_no, s.cc_date FROM `member_main` mm, `unit` u, `society` s where mm.unit = u.unit_id and u.unit_id = '".$unit_id."' and s.society_id = u.society_id and mm.ownership_status = 1";
		$sql11 = $this->m_dbConn->select($sql01);
		$sql11[0]['cc_date'] = getDisplayFormatDate($sql11[0]['cc_date']);
		
		return $sql11;
	}
	
	function fetch_data_for_nomination($unit_id)
	{
		$sql01 = "SELECT u.unit_id, u.unit_no, u.area, u.share_certificate, u.share_certificate_from, u.share_certificate_to, mm.primary_owner_name, s.amt_per_share FROM `unit` u, `member_main` mm, `society` s where u.unit_id = '".$unit_id."' and mm.ownership_status = '1' and u.unit_id = mm.unit and u.society_id = s.society_id";
		$sql11 = $this->m_dbConn->select($sql01);
		
		return $sql11;
	}
	
	//Query to add columns to society table
	//ALTER TABLE `society` ADD `cc_no` VARCHAR(100) NOT NULL , ADD `cc_date` DATE NOT NULL , ADD `SMS_Reminder_Days` INT(11) NOT NULL DEFAULT '1' ;
	//ALTER TABLE `society` ADD `amt_per_share` INT(11) NOT NULL ;
	//ALTER TABLE `society` CHANGE `society_add` `society_add_line_1` VARCHAR(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
	//ALTER TABLE `society` ADD `society_add_line_2` VARCHAR(250) NOT NULL AFTER `society_add_line_1`;
	
}
?>