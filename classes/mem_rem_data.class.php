<?php if(!isset($_SESSION)){ session_start(); }

//include_once("include/dbop.class.php");

class mem_rem_data extends dbop
{
	public $m_dbConn;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		//dbop::__construct();
	}
	
	public function rem_mem_form()
	{
		$sql = "SELECT * FROM member_main as mm, unit as u, wing as w, society as s where mm.unit=u.unit_id and u.wing_id=w.wing_id and s.society_id=mm.society_id and mm.status='Y' and u.status='Y' and w.status='Y' and mm.ownership_status='1' ";
	
		if(isset($_SESSION['admin']))
		{
			$sql .= " and s.society_id = '".$_SESSION['society_id']."'";
		}
		
		if($_REQUEST['society_id']<>"")
		{
			$sql .= " and s.society_id = '".$_REQUEST['society_id']."'";
		}
		if($_REQUEST['wing_id']<>"")
		{
			$sql .= " and w.wing_id = '".$_REQUEST['wing_id']."'";
		}
		if($_REQUEST['member_name']<>"")
		{
			$sql .= " and mm.owner_name like '%".addslashes($_REQUEST['member_name'])."%'";
		}
		
		$sql .= " order by s.society_id,wing,u.sort_order";
		
		$res = $this->m_dbConn->select($sql);
		
		if($res<>"")
		{
		?>
       <table id="example" class="display" cellspacing="0" width="100%">
	   <thead>
        <tr height="30">
        	<th width="70">Wing</th>
            <th width="70">Unit No.</th>
        	<th width="150">Member Name</th>
            <!--<th width="120">Member Spouse <br> Form</th>
            <th width="120">Member Child <br> Form</th>-->
            <th width="120">Member Family <br> Form</th>
            <th width="150">Member Car Parking <br> Form</th>
            <th width="150">Member Bike Parking <br> Form</th>
        </tr>
		</thead>
		<tbody>
        <?php
			foreach($res as $k => $v)
			{
		?>
        <tr height="25" bgcolor="#BDD8F4">
        	<td align="center"><?php echo $res[$k]['wing'];?></td>
            <td align="center"><?php echo $res[$k]['unit_no'];?></td>
        	<td align="center"><a href="view_member_profile.php?scm&id=<?php echo $res[$k]['member_id'];?>&tik_id=<?php echo time();?>&m&view" style="color:#00F;"><?php echo $res[$k]['owner_name'];?></a></td>
            <!--<td align="center"><?php //$spouse	= $this->spouse($res[$k]['member_id'],$res[$k]['unit']);?></td>
            <td align="center"><?php //$child 	= $this->child($res[$k]['member_id']);?></td>-->
            <td align="center"><?php $other		= $this->other($res[$k]['member_id']);?></td>
            <td align="center"><?php $car 		= $this->car($res[$k]['member_id']);?></td>
            <td align="center"><?php $bike 		= $this->bike($res[$k]['member_id']);?></td>
        </tr>
        <?php
			}
		?>
		</tbody>
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
	
	public function spouse($member_id,$uid)
	{
		$sql = "select count(*)as cnt from mem_spouse_details where member_id = '".$member_id."' and status='Y'";
		$res = $this->m_dbConn->select($sql);
		
		if($res[0]['cnt']==0)
		{	
			if(isset($_SESSION['sadmin']))
			{
				echo "<font color='#FF0000'><b>Not Filled</b></font>";
			}
			else
			{
				echo "<a href='mem_spouse_details_new.php?scm&idd=$member_id&mrs&uid=".$uid."tik_id=".time()."&m' style='text-decoration:none'><font color='#FF0000'><b>Not Filled</b></font></a>";
			}
		}
		else
		{
			echo "<font color='#006633'><b>Filled</b></font>";
		}
	}
	
	public function child($member_id)
	{
		$sql = "select count(*)as cnt from mem_child_details where member_id = '".$member_id."' and status='Y'";
		$res = $this->m_dbConn->select($sql);
		
		if($res[0]['cnt']==0)
		{	
			if(isset($_SESSION['sadmin']))
			{
				echo "<font color='#FF0000'><b>Not Filled</b></font>";
			}
			else
			{
				echo "<a href='mem_child_details_new.php?scm&idd=$member_id&mrs&tik_id=".time()."&m' style='text-decoration:none'><font color='#FF0000'><b>Not Filled</b></font></a>";
			}
		}
		else
		{
			echo "<font color='#006633'><b>Filled</b></font>";
		}
	}
	
	public function other($member_id)
	{
		$sql = "select count(*)as cnt from mem_other_family where member_id = '".$member_id."' and status='Y'";
		$res = $this->m_dbConn->select($sql);
		
		if($res[0]['cnt']==0)
		{
			if(isset($_SESSION['sadmin']))
			{
				echo "<font color='#FF0000'><b>Not Filled</b></font>";
			}
			else
			{	
				echo "<a href='mem_other_family_new.php?scm&idd=$member_id&mrs&tik_id=".time()."&m' style='text-decoration:none'><font color='#FF0000'><b>Not Filled</b></font></a>";
			}
		}
		else
		{
			echo "<font color='#006633'><b>Filled</b></font>";
		}
	}
	
	public function car($member_id)
	{
		$sql = "select count(*)as cnt from mem_car_parking where member_id = '".$member_id."' and status='Y'";
		$res = $this->m_dbConn->select($sql);
		
		if($res[0]['cnt']==0)
		{	
			if(isset($_SESSION['sadmin']))
			{
				echo "<font color='#FF0000'><b>Not Filled</b></font>";
			}
			else
			{
				echo "<a href='mem_vehicle_new.php?scm&idd=$member_id&mrs&tik_id=".time()."&m' style='text-decoration:none'><font color='#FF0000'><b>Not Filled</b></font></a>";
			}
		}
		else
		{
			echo "<font color='#006633'><b>Filled</b></font>";
		}
	}
	
	public function bike($member_id)
	{
		$sql = "select count(*)as cnt from mem_bike_parking where member_id = '".$member_id."' and status='Y'";
		$res = $this->m_dbConn->select($sql);
		
		if($res[0]['cnt']==0)
		{
			if(isset($_SESSION['sadmin']))
			{
				echo "<font color='#FF0000'><b>Not Filled</b></font>";
			}
			else
			{	
				echo "<a href='mem_vehicle_new.php?scm&idd=$member_id&mrs&tik_id=".time()."&m' style='text-decoration:none'><font color='#FF0000'><b>Not Filled</b></font></a>";
			}
		}
		else
		{
			echo "<font color='#006633'><b>Filled</b></font>";
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
}
?>
