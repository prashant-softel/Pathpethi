<?php if(!isset($_SESSION)){ session_start(); }
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once("dbconst.class.php");

class mem_car_parking extends dbop
{
	public $actionPage = "../mem_car_parking_new.php";
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
		if($_REQUEST['insert']=='Add More' || $_REQUEST['insert']=='Add' && $errorExists==0)
		{
			if($_POST['member_id']<>"" && $_POST['car_reg_no']<>"" && $_POST['car_owner']<>"" && $_POST['car_make']<>"" && $_POST['car_color']<>"")
			{
				$sql = "select count(*)as cnt from mem_car_parking where member_id='".$_POST['member_id']."' and car_reg_no='".$_POST['car_reg_no']."' and status='Y'";
				$res = $this->m_dbConn->select($sql);
				
				if($res[0]['cnt']==0)
				{
					$insert_query="insert into mem_car_parking (`member_id`,`parking_slot`,`car_reg_no`,`car_owner`,`car_model`,`car_make`,`car_color`,`parking_sticker`) values ('".$_POST['member_id']."','".addslashes(trim(ucwords($_POST['parking_slot'])))."','".addslashes(trim(strtoupper($_POST['car_reg_no'])))."','".addslashes(trim(ucwords($_POST['car_owner'])))."','".addslashes(trim(ucwords($_POST['car_model'])))."','".addslashes(trim(ucwords($_POST['car_make'])))."','".addslashes(trim(ucwords($_POST['car_color'])))."','".addslashes(trim(ucwords($_POST['parking_sticker'])))."')";
					$data = $this->m_dbConn->insert($insert_query);
										
				if(isset($_SESSION['role']) && $_SESSION['role']==ROLE_MEMBER)
				{
					?>
					<script>window.location.href = '../view_member_profile.php?prf&id=<?php echo $_POST['member_id'];?>';</script>
				<?php
                }
				else
				{
				  if($_POST['mkm']=='mkm')
					{
					?>
					<script>window.location.href = '../view_member_profile.php?scm&id=<?php echo $_POST['member_id'];?>&tikon=<?php echo time();?>';</script>
					<?php
					}
					else if($_POST['mrs']=='mrs')
					{
					?>
					<script>window.location.href = '../mem_rem_data.php?scm';</script>
					<?php	
					}
					else
					{
					return "Insert";
					}
                
				
                	
				}
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
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			if($_POST['member_id']<>"" && $_POST['car_reg_no']<>"" && $_POST['car_owner']<>"" && $_POST['car_make']<>"" && $_POST['car_color']<>"")
			{
				$up_query="update mem_car_parking set `member_id`='".$_POST['member_id']."',`parking_slot`='".addslashes(trim(ucwords($_POST['parking_slot'])))."',`car_reg_no`='".addslashes(trim(strtoupper($_POST['car_reg_no'])))."',`car_owner`='".addslashes(trim(ucwords($_POST['car_owner'])))."',`car_model`='".addslashes(trim(ucwords($_POST['car_model'])))."',`car_make`='".addslashes(trim(ucwords($_POST['car_make'])))."',`car_color`='".addslashes(trim(ucwords($_POST['car_color'])))."' where mem_car_parking_id='".$_POST['id']."'";
				$data=$this->m_dbConn->update($up_query);
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
	public function combobox07($query,$id)
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
			$thheader=array('Member Name','Parking Slot','Car Reg No.','Car Owner Name','Car Model','Car Make','Car Colour');
			$this->display_pg->edit="getmem_car_parking";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="mem_car_parking.php";
			$res=$this->display_pg->display_new($rsas);
			return $res;
	}
	public function pgnation()
	{
		$sql1 = "select mcp.mem_car_parking_id, mm.owner_name, mcp.parking_slot, mcp.car_reg_no, mcp.car_owner, mcp.car_model, mcp.car_make, mcp.car_color 
				 from mem_car_parking as mcp, member_main as mm
				 where mcp.status='Y' and mm.status='Y'
				 and mcp.member_id=mm.member_id
				 ";
		
		$cntr = "select count(*) as cnt 
				 from mem_car_parking as mcp, member_main as mm
				 where mcp.status='Y' and mm.status='Y'
				 and mcp.member_id=mm.member_id
				 ";
		
		$this->display_pg->sql1=$sql1;
		$this->display_pg->cntr1=$cntr;
		$this->display_pg->mainpg="mem_car_parking.php";
		$limit="5";
		$page=$_REQUEST['page'];
		$extra="";
		$res=$this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;
	}
	public function selecting()
	{
			$sql1="select mem_car_parking_id,`member_id`,`parking_slot`,`car_reg_no`,`car_owner`,`car_model`,`car_make`,`car_color` from mem_car_parking where mem_car_parking_id='".$_REQUEST['mem_car_parkingId']."'";
			$var=$this->m_dbConn->select($sql1);
			return $var;
	}
	public function deleting()
	{
			$sql1="update mem_car_parking set status='N' where mem_car_parking_id='".$_REQUEST['mem_car_parkingId']."'";
			$this->m_dbConn->update($sql1);
	}
	
	public function owner_name($member_id)
	{
		$sql = "select * from member_main where member_id='".$member_id."' and status='Y'";
		$res = $this->m_dbConn->select($sql);	
		echo $res[0]['owner_name'];
		
		$_SESSION['owner_id'] = $member_id;
		$_SESSION['owner_name'] = $res[0]['owner_name'];
	}
}
?>