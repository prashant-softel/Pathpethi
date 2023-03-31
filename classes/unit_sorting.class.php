<?php if(!isset($_SESSION)){ session_start(); }
include_once("include/display_table.class.php");
include_once("dbconst.class.php");
include_once("register.class.php");

class unit_sorting
{
	public $actionPage= "../unit.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	function __construct($dbConn, $dbConnRoot = '')
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->display_pg=new display_table($this->m_dbConn);
	}

	
	public function display1($rsas, $bShowViewLink = false)
	{
		$thheader=array('Society Name','Wing','Unit No.');
		$this->display_pg->edit="getunit";
		$this->display_pg->th=$thheader;
		$this->display_pg->mainpg="unit.php";
		$res=$this->show_unit($rsas, $bShowViewLink);
		return $res;
	}
	

	
	public function pgnation($bShowViewLink = false)
	{
		if($bShowViewLink)
		{
			$_REQUEST['insert']='Search';
		}
		
		if($_REQUEST['insert']=='Search')
		{
			$sql1 = "select u.unit_id,s.society_id,s.society_name,w.wing_id,w.wing,u.unit_no,u.rand_no,m.owner_name from 
					 unit as u,society as s,wing as w,member_main as m
					 where u.status='Y' and w.status='Y' and s.status='Y' and m.status='Y' and 
					 u.wing_id=w.wing_id and u.unit_id=m.unit and u.society_id='".$_SESSION['society_id']."' and s.society_id='".$_SESSION['society_id']."' and 	m.society_id='".$_SESSION['society_id']."' and m.ownership_status = '1'";
					 
			if($_REQUEST['society_id']<>"")
			{		 
				$sql1 .= " and s.society_id='".$_REQUEST['society_id']."'";
			}
			else
			{
				if(isset($_SESSION['admin']))
				{
					$sql1 .= " and s.society_id='".$_SESSION['society_id']."'";
				}
			}
			
			if($_REQUEST['wing_id']<>"")
			{		 
				$sql1 .= " and w.wing_id='".$_REQUEST['wing_id']."'";
			}
			
			if($_REQUEST['unit_no']<>"")
			{		 
				$sql1 .= " and u.unit_no='".$_REQUEST['unit_no']."'";
			}
			
			if($_REQUEST['owner_name']<>"")
			{		 
				$sql1 .= " and m.owner_name LIKE '%".$_REQUEST['owner_name']."%'";
			}
			$sql1 .= " order by sort_order";	
			
			
			
		}
		else
		{
			$sql1 = "select u.unit_id,s.society_id,s.society_name,w.wing_id,w.wing,u.unit_no,u.rand_no,m.owner_name from 
					 unit as u,society as s,wing as w,member_main as m
					 where u.status='Y' and w.status='Y' and s.status='Y' and m.status='Y' and 
					 u.wing_id=w.wing_id and u.unit_id=m.unit and u.society_id='".$_SESSION['society_id']."' and s.society_id='".$_SESSION['society_id']."' and m.society_id='".$_SESSION['society_id']."' and m.ownership_status = '1'";
					 
			if(isset($_REQUEST['wing_id']) && $_REQUEST['wing_id'] <> "")
			{
				
				$sql1 .= " and u.wing_id=".$_REQUEST['wing_id']."";
			}		 
					 
					 
			if(isset($_SESSION['sadmin']))
			{
				if(isset($_REQUEST['sa']))
				{
					$sql1 .= " and s.society_id='".$_REQUEST['sid']."' and w.wing_id='".$_REQUEST['wid']."'";
				
				}
			}
			
			if(isset($_REQUEST['ssid']) && isset($_REQUEST['wwid']))
			{		 
				$sql1 .= " and s.society_id='".$_REQUEST['ssid']."' and w.wing_id='".$_REQUEST['wwid']."' order by sort_order";
			}
			else
			{
				if(isset($_SESSION['sadmin']))
				{
					$sql1 .= " order by sort_order";	
				}
				else
				{
					$sql1 .= " and s.society_id='".$_SESSION['society_id']."' order by sort_order";	
				}
			}
		}
		
		if($_REQUEST['insert']=='Search')
		{
			$cntr = "select count(*) as cnt from 
					 unit as u,society as s,wing as w
					 where u.status='Y' and w.status='Y' and s.status='Y' and 
					 u.wing_id=w.wing_id and u.society_id='".$_SESSION['society_id']."' and s.society_id='".$_SESSION['society_id']."'";
			if($_REQUEST['society_id']<>"")
			{		 
				$cntr .= " and s.society_id='".$_REQUEST['society_id']."'";
			}
			else
			{
				$cntr .= " and s.society_id='".$_SESSION['society_id']."'";
			}
			if($_REQUEST['wing_id']<>"")
			{		 
				$cntr .= " and w.wing_id='".$_REQUEST['wing_id']."'";
			}
			if($_REQUEST['unit_no']<>"")
			{		 
				$cntr .= " and u.unit_no='".$_REQUEST['unit_no']."'";
			}	
		}
		else
		{
			$cntr = "select count(*) as cnt from 
					 unit as u,society as s,wing as w
					 where u.status='Y' and w.status='Y' and s.status='Y' and 
					 u.wing_id=w.wing_id and u.society_id='".$_SESSION['society_id']."' and s.society_id='".$_SESSION['society_id']."'";
			
			if(isset($_REQUEST['wing_id']) && $_REQUEST['wing_id'] <> "")
			{
				
				$cntr .= " and u.wing_id=".$_REQUEST['wing_id']."";
			}	
			if(isset($_SESSION['sadmin']))
			{
				if(isset($_REQUEST['sa']))
				{
					$cntr .= " and s.society_id='".$_REQUEST['sid']."' and w.wing_id='".$_REQUEST['wid']."'";
				}
			}

			if(isset($_REQUEST['ssid']) && isset($_REQUEST['wwid']))
			{		 
				$cntr .= " and s.society_id='".$_REQUEST['ssid']."' and w.wing_id='".$_REQUEST['wwid']."' order sort_order";
			}
			else
			{
				if(isset($_SESSION['sadmin']))
				{
					$cntr .= " order by sort_order";	
				}
				else
				{
					$cntr .= " and s.society_id='".$_SESSION['society_id']."' order by sort_order";	
				}
			}
		}
		
		
		$this->display_pg->sql1=$sql1;
		$this->display_pg->cntr1=$cntr;
		$this->display_pg->mainpg="unit.php";
		$limit = "2000";
		$page=$_REQUEST['page'];
		
		if(isset($_SESSION['sadmin']))
		{
			if(isset($_REQUEST['sa']))
			{
				$extra = "&imp&id=".time()."&sa&sid=".$_REQUEST['sid']."&wid=".$_REQUEST['wid']."&id=".$_REQUEST['id'].'&wing_id='.$_REQUEST['wing_id'];
			}
			else
			{
				$extra = "&imp&id=".time();
			}
			
		}
		else
		{
			$extra = "&imp&ws&ssid=".$_REQUEST['ssid']."&wwid=".$_REQUEST['wwid']."&idd=".time().'&unit_no='.$_REQUEST['unit_no'].'&wing_id='.$_REQUEST['wing_id'];
		}
		
		$res = $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;
	}
	
	public function show_unit($res, $bShowViewLink = false)
	{
		if($bShowViewLink == true)
		{
			$sqlPeriod = "Select periodtbl.type, yeartbl.YearDescription from period as periodtbl JOIN year as yeartbl ON periodtbl.YearID = yeartbl.YearID where periodtbl.ID = '" . $_REQUEST['period_id'] . "'";
		
			$sqlResult = $this->m_dbConn->select($sqlPeriod);
			echo "<b><font color='#0000FF'> Bill's For : " . $sqlResult[0]['type'] . " "  . $sqlResult[0]['YearDescription'] . "</font></b><br><br>";
		}
		if($res<>"")
		{
			if(!isset($_REQUEST['page']))
			{
				$_REQUEST['page'] = 1;
			}
			$iCounter = 0;
			$sortOrder=0;
	
		?>
		<table id="unit_sort" style="text-align:center; width:100%;" class="table table-bordered table-hover table-striped">
        <thead>
		<tr height="30">
        	 <th width="100" style="text-align:center"><input type="checkbox" id="all_unit" onclick="SelectAllUnit(this);"></th>
            <th width="50" style="text-align:center">Wing</th>
            <th width="150" style="text-align:center">Unit No.</th>
            <th width="400" style="text-align:center">Member Name</th>
            <th width="20" style="text-align:center; border-right:none;">Sort Order</th>
            <th width="50" style="text-align:center;display:none;">Unit ID</th>
            
 		</tr>
        </thead>
        <tbody>
		<script>var unitArray = []; </script> 
        
        <?php 
		foreach($res as $k => $v){
			$iCounter++;
			$sortOrder +=100;
			$sql_due_amount="select sum(Debit)-sum(Credit) as amount from `assetregister` where LedgerID=".$res[$k]['unit_id']." GROUP BY LedgerID";
			$res20 = $this->m_dbConn->select($sql_due_amount);
			if($res20[0]['amount'] =='')
			{
			$res20[0]['amount']=0;
			}
			
			?>
        
			<script>unitArray.push(<?php echo $res[$k]['unit_id']; ?>)</script>
			
        	<tr id="<?php echo $iCounter;?>" height="25" align="center">
        	 <td><input type="checkbox" id="chk_<?php echo $res[$k]['unit_id'] ?>" /></td>
            <td> <select id="wing_<?php echo $res[$k]['unit_id'] ?>"  style="background-color:#FF9; width:100px;"><?php echo $this->combobox("select wing_id,wing from wing where status='Y' and society_id='".$_SESSION['society_id']."'",$res[$k]['wing_id']); ?></select></td>
            <td align="center"><?php echo $res[$k]['unit_no'];?></td>
            <td align="center"><?php echo $res[$k]['owner_name'];?></td>
            <td align="center" style="border-right:none;"><input type="text" value="<?php echo $sortOrder; ?>" id="<?php echo "sort_order".$iCounter;?>" style="background-color:#FF9; width:100px;"/> </td>
            <td align="center" style="display:none;"><?php echo $res[$k]['unit_id'];?></td>
            <?php if(isset($_SESSION['admin'])){?><td align="center"><?php echo $res[$k]['rand_no'];?></td><?php } ?>
     </tr>
        <?php 
		}?>
        
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
	
	public function selecting()
	{
			$sql1="select`unit_id`,`society_id`,`wing_id`,`unit_no`,`floor_no`,`unit_type`,`composition`,`area`,`carpet`,`commercial`,`residential`,`terrace` FROM unit where unit_id='".$_REQUEST['unitId']."' and society_id='".$_SESSION['society_id']."'";
			$var=$this->m_dbConn->select($sql1);
			echo $sql1;
			return $var;
	}
	public function deleting()
	{
		$sql0 = "select count(*)as cnt from member_main where unit='".$_REQUEST['unitId']."' and status='Y'";
		$res0 = $this->m_dbConn->select($sql0);
		
		if($res0[0]['cnt']==0)
		{	
			$sql1="update unit set status='N' where unit_id='".$_REQUEST['unitId']."'";
			$this->m_dbConn->update($sql1);
			
			echo "msg1###".$_SESSION['ssid'].'###'.$_SESSION['wwid'];
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
	public function wing_name($ww_id)
	{
		$sql = "select * from wing where wing_id='".$ww_id."' and status='Y'";
		$res = $this->m_dbConn->select($sql);
		echo $res[0]['wing'];
	}
	
	public function get_wing_name($ww_id)
	{
		$sql = "select * from wing where wing_id='".$ww_id."' and status='Y'";
		$res = $this->m_dbConn->select($sql);
		return $res[0]['wing'];
	}
	
	public function chk_delete_perm_admin()
	{
		$sql = "select * from del_control_admin where status='Y' and login_id='".$_SESSION['login_id']."'";
		$res = $this->m_dbConn->select($sql);
		return $res[0]['del_control_admin'];
	}
	
	public function getallwing()
	{
		
	$sql="select wing,wing_id from `wing` where society_id=".$_SESSION['society_id']." ";
	$res=$this->m_dbConn->select($sql);
	return $res;
		
		
	}
	
	public function UpdateUnitTable($SortOrderID,$UnitNO,$UnitID,$WingID)
	{
		$sql="update `unit` set `sort_order`='".$SortOrderID."' ,`wing_id`='".$WingID."' where `unit_no`='".$UnitNO."' and `society_id`='".$_SESSION['society_id']."' ";
		$res=$this->m_dbConn->update($sql);
		
		$sqlmem = "update `member_main` set  `wing_id`='".$WingID."' where `unit`='".$UnitID."' and `society_id`='".$_SESSION['society_id']."' ";
		$resmem = $this->m_dbConn->update($sqlmem);
		
		$sqlUpdateMappingQuery="update `mapping` set `sort_order`='".$SortOrderID."' where `unit_id`=".$UnitID." and `society_id`='".$_SESSION['society_id']."' ";
		$res002=$this->m_dbConnRoot->update($sqlUpdateMappingQuery);
		return $res002;	
		
	}
	
	public function combobox($query, $id, $bShowAll = false)
	{
		if($bShowAll == true)
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
}
?>