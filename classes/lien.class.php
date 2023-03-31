 <?php
include_once("dbconst.class.php");
include_once("include/display_table.class.php");
include_once("utility.class.php");
class lien extends dbop
{
	public $m_dbConn;
	public $m_dbConnRoot;
	public $actionPage = "../lien.php";
	public $obj_utility;
	function __construct($dbConn, $dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		$this->m_dbConnRoot = $dbConnRoot;
		$this->obj_utility=new utility($this->m_dbConn, $this->m_dbConnRoot);
		//dbop::__construct();
	}
	//Used to get UnitNo and owner name from unit and member_main
	public function getUnitNoAndOwnerDetails($unitId)
	{
		$sql="Select u.`unit_no`, mm.`owner_name` from `unit` u, `member_main` mm where u.`unit_id` = ".$unitId. " AND mm.`unit` = ".$unitId;
		$res=$this->m_dbConn->select($sql);
		return $res;
	}
	//used for displaying all lien details for one unit id
	public function getAllLienDetailsForDoc($unitId = 0)
	{
		//$UnitID = $_SESSION["unit_id"];
		 $sql =" select L.* from `mortgage_details` as L where L.Status='Y'";
		 //echo "unitid:".$UnitID ;
		// if($SESSION['role']!=ROLE_ADMIN && $_SESSION['role']!=ROLE_SUPER_ADMIN && $_SESSION['role']!=ROLE_ADMIN_MEMBER)
		 if($UnitID != 0)
		 {
		 	$sql .= " and L.UnitId='".$unitId."'";
		 }
		$result=$this->m_dbConn->select($sql);
		return $result;
	}
	public function getAllLienDetails($unitId)
	{
		//$UnitID = $_SESSION["unit_id"];
		$sql =" select L.* from `mortgage_details` as L where L.Status='Y' and L.UnitId='".$unitId."'";
		$result=$this->m_dbConn->select($sql);
		return $result;
	}
	//used to display lien of selected lienId
	public function getLienByLienId($lienId)
	{
		$sql="SELECT * from `mortgage_details` where `Id`= ".$lienId." AND Status='Y'";
		$res=$this->m_dbConn->select($sql);
		/*echo "<pre>";
		print_r($res);
		echo "</pre>";*/
		for($i=0;$i<sizeof($res);$i++)
		{
			$res[$i]['SocietyNOCDate']=getDisplayFormatDate($res[$i]['SocietyNOCDate']);
			$res[$i]['CloseDate']=getDisplayFormatDate($res[$i]['CloseDate']);
		}
		return $res;
	}
	//used to get all documents for lien uploaded by one unit
	public function getDocumentsByUnitId($unitId,$docType)
	{
		$sql="SELECT * from documents where `Unit_id`=".$unitId." AND doc_type_id=".$docType." AND status='Y'";
		$res=$this->m_dbConn->select($sql);
		return $res;
	}
	//used for displaying lien of one unit only
	/*public function display1($rsas, $method)
	{
		//echo "Method:".$method;
		$thheader = array('Owner Name','Bank Name','Society NOC Date');
		$this->display_pg->edit		= $method;
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "addLien.php";
		if($_SESSION['role']=="Super Admin")
		{
			if ($method=="closed")
			{
				$res = $this->display_pg->display_datatable($rsas, false, false, true);
				return $res;
			}
			else if ($method=="getlien")
			{
				$res = $this->display_pg->display_datatable($rsas, true, true);
				return $res;
			}
			else
			{
			}
		}
		else
		{
			$res = $this->display_pg->display_datatable($rsas, false, false, true);
			return $res;
		}
	}*/
	//To check the access of the user
	function checkAccess()
	{
		if($_SESSION['role']=="Super Admin")
		{
			return 0;
		}
		else
		{
			return 1;
		}
	}
	//Used to display lien details for all units on report page
	public function display2($res,$method)
	{ 
		$isCheckAccess=$this->checkAccess();
		//print_r($res);
		if($res<>"")
		{
			
			if($isCheckAccess==0 and $method=="open")
			{
			?>
            	<table id="example" class="display" cellspacing="0" width="100%">
            	<thead>
            		<tr  height="30" bgcolor="#FFFFFF">
                		<th style="text-align:center">Unit No.</th>
           				<th style="text-align:center">Owner Name</th>
            			<th style="text-align:center">Bank Name</th>
            			<th style="text-align:center">Society NOC Date</th>
            			<th style="text-align:center">Edit</th>
            			<th style="text-align:center">Delete</th>
            		</tr>
            	</thead>
            	<tbody>
            	<?php
				for($i=0;$i<sizeof($res);$i++)
				{
			 	?>
					<tr height="25" bgcolor="#BDD8F4" align="center"> 
                		<td align="center"><?php echo $res[$i]['unit_no'];?></td>
                    	<td align="center"><a href="viewLien.php?lienId=<?php echo $res[$i]['Id'];?>"><?php echo $res[$i]['owner_name'];?></a></td>
                		<td align="center"><?php echo $res[$i]['BankName'];?> </td>
                		<td align="center"><?php echo getDisplayFormatDate($res[$i]['SocietyNOCDate']);?> </td>
                    	<td><a href="addLien.php?method=edit&lienId=<?php echo $res[$i]['Id']?>&unit_id=<?php echo $res[$i]['UnitId']; ?>"><img src="images/edit.gif"  /></a></td>
                    	<td><a onclick="deleteLien(<?php echo $res[$i]['Id']; ?>,<?php echo $res[$i]['UnitId'];?>)"><img src="images/del.gif" /></a></td>
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
				<table id="example" class="display" cellspacing="0" width="100%">
            	<thead>
            		<tr  height="30" bgcolor="#FFFFFF">
                		<th style="text-align:center">Unit No.</th>
           				<th style="text-align:center">Owner Name</th>
            			<th style="text-align:center">Bank Name</th>
            			<th style="text-align:center">Society NOC Date</th>
            			<th style="text-align:center">View</th>
            		</tr>
            	</thead>
            	<tbody>
            	<?php
				for($i=00;$i<sizeof($res);$i++)
				{
			 	?>
					<tr height="25" bgcolor="#BDD8F4" align="center"> 
                		<td align="center"><?php echo $res[$i]['unit_no'];?></td>
                    	<td align="center"><?php echo $res[$i]['owner_name'];?></td>
                		<td align="center"><?php echo $res[$i]['BankName'];?> </td>
                		<td align="center"><?php echo getDisplayFormatDate($res[$i]['SocietyNOCDate']);?> </td>
                    	<td><a href="viewLien.php?lienId=<?php echo $res[$i]['Id']?> "><img src="images/view.jpg"  /></a></td>
                    </tr>
      			<?php
				}      
				?>
            	</tbody>
        	</table>
            <?php
			}
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
	//Used for displaying lien for all unit.
	/*public function display2($rsas, $method)
	{
		//echo "Method:".$method;
		$thheader = array('Owner Name','Unit Id','Bank Name','Society NOC Date');
		$this->display_pg->edit		= $method;
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "lien.php";
		if($_SESSION['role']=="Super Admin")
		{
			if ($method=="closed")
			{
				$res = $this->display_pg->display_datatable($rsas, false, false, true);
				return $res;
			}
			else if ($method=="getlien")
			{
				$res = $this->display_pg->display_datatable($rsas, true, true);
				return $res;
			}
			else
			{
			}
		}
		else
		{
			$res = $this->display_pg->display_datatable($rsas, false, false, true);
			return $res;
		}
	}*/
	//Used to display lien details on lien page when unit_id is set
	public function display1($res,$method)
	{ 
	//print_r($res);
		$isAccess=$this->checkAccess();
		if($res<>"")
		{
			if($isAccess==0 and $method=="open")
			{
			?>
            	<table id="example" class="display" cellspacing="0" width="100%">
            	<thead>
            		<tr  height="30" bgcolor="#FFFFFF">
                    	<th style="text-align:center">Owner Name</th>
            			<th style="text-align:center">Bank Name</th>
            			<th style="text-align:center">Society NOC Date</th>
            			<th style="text-align:center">Edit</th>
            			<th style="text-align:center">Delete</th>
            		</tr>
            	</thead>
            	<tbody>
            		<?php
					for($i=0;$i<sizeof($res);$i++)
					{
			 		?>
						<tr height="25" bgcolor="#BDD8F4" align="center"> 
                			<td align="center"><a href="viewLien.php?lienId=<?php echo $res[$i]['Id']; ?>"><?php echo $res[$i]['owner_name'];?></a></td>
                			<td align="center"><?php echo $res[$i]['BankName'];?> </td>
                			<td align="center"><?php echo getDisplayFormatDate($res[$i]['SocietyNOCDate']);?> </td>
                    		<td><a href="addLien.php?method=edit&lienId=<?php echo $res[$i]['Id']?>&unit_id=<?php echo $res[$i]['UnitId']; ?>"><img src="images/edit.gif"  /></a></td>
                    		<td><a onclick="deleteLien(<?php echo $res[$i]['Id'];?>,<?php echo $res[$i]['UnitId']?>)"><img src="images/del.gif" /></a></td>
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
				<table id="example" class="display" cellspacing="0" width="100%">
            	<thead>
            		<tr  height="30" bgcolor="#FFFFFF">
                    	<th style="text-align:center">Owner Name</th>
            			<th style="text-align:center">Bank Name</th>
            			<th style="text-align:center">Society NOC Date</th>
            			<th style="text-align:center">View</th>
            		</tr>
            	</thead>
            	<tbody>
            		<?php
					for($i=0;$i<sizeof($res);$i++)
					{
			 		?>
						<tr height="25" bgcolor="#BDD8F4" align="center"> 
                			<td align="center"><?php echo $res[$i]['owner_name'];?></td>
                			<td align="center"><?php echo $res[$i]['BankName'];?> </td>
                			<td align="center"><?php echo getDisplayFormatDate($res[$i]['SocietyNOCDate']);?> </td>
                    		<td><a href="viewLien.php?lienId=<?php echo $res[$i]['Id']; ?>"><img src="images/view.jpg"  /></a></td>
                        </tr>
      				<?php
					}      
					?>
            	</tbody>
        		</table>
                <?php
			}
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
	//Called by lien.php page
	public function pgnation($type,$unitId)
	{
		if($unitId=="")
		{
			if($type=="closed")
			{
				$query="SELECT md.`Id`, mm.`owner_name`, u.`unit_no`, md.`BankName`, md.`SocietyNOCDate`, md.`UnitId`, md.`Amount`,md.`CloseDate`,md.`Note` FROM `mortgage_details` md, `member_main` mm, `unit` u where md.`LienStatus`='Open' AND mm.`member_id` = md.`member_id` AND mm.`unit`= u.`unit_id` AND md.`Status` = 'Y'";
				$res=$this->m_dbConn->select($query);
				for($i=0;$i<sizeof($res);$i++)
				{
					$res[$i]['SocietyNOCDate']=getDisplayFormatDate($res[$i]['SocietyNOCDate']);
				}
				if($res!="")
				{
					$data=$this->display2($res,$type);
				}
				else
				{
					$res="";
					$data=$this->display2($res,$type);
				}
				return $data;
			}
			if($type=="open")
			{
				$query="SELECT md.`Id`, mm.`owner_name`, u.`unit_no`, md.`BankName`, md.`SocietyNOCDate`,md.`UnitId`, md.`Amount`,md.`CloseDate`,md.`Note` FROM `mortgage_details` md, `member_main` mm, `unit` u where md.`LienStatus`='Open' AND mm.`member_id` = md.`member_id` AND mm.`unit`= u.`unit_id` AND md.`Status` = 'Y'";
				$res=$this->m_dbConn->select($query);
				for($i=0;$i<sizeof($res);$i++)
				{
					$res[$i]['SocietyNOCDate']=getDisplayFormatDate($res[$i]['SocietyNOCDate']);
				}
				if($res!="")
				{
					$data=$this->display2($res,$type);
				}
				else
				{
					$res="";
					$data=$this->display2($res,$type);
				}
				return $data;
			}
			if($type=="deleted")
			{
				$query="SELECT md.`Id`, mm.`owner_name`, u.`unit_no`, md.`BankName`, md.`SocietyNOCDate`,md.`UnitId`, md.`Amount`,md.`CloseDate`,md.`Note` FROM `mortgage_details` md, `member_main` mm, `unit` u where mm.`member_id` = md.`member_id` AND mm.`unit`= u.`unit_id` AND md.`Status` = 'N'";
				$res=$this->m_dbConn->select($query);
				for($i=0;$i<sizeof($res);$i++)
				{
					$res[$i]['SocietyNOCDate']=getDisplayFormatDate($res[$i]['SocietyNOCDate']);
				}
				if($res!="")
				{
					$data=$this->display2($res,$type);
				}
				else
				{
					$res="";
					$data=$this->display2($res,$type);
				}
				return $data;
			}
		}
		else
		{
			if($type=="closed")
			{
				$query="SELECT md.`Id`, mm.`owner_name`, md.`BankName`, md.`SocietyNOCDate`,md.`UnitId`, md.`Amount`,md.`CloseDate`,md.`Note` FROM `mortgage_details` md, `member_main` mm where md.`LienStatus`='Closed' AND mm.`member_id` = md.`member_id` AND md.`UnitId`=".$unitId. " AND md.`Status` = 'Y'";
				$res=$this->m_dbConn->select($query);
				for($i=0;$i<sizeof($res);$i++)
				{
					$res[$i]['SocietyNOCDate']=getDisplayFormatDate($res[$i]['SocietyNOCDate']);
				}
				$method="closed";
				if($res!="")
				{
					$data=$this->display1($res,$type);
				}
				else
				{
					$res="";
					$data=$this->display1($res,$type);
				}
				return $data;
			}
			if($type=="open")
			{
				$query="SELECT md.`Id`, mm.`owner_name`, md.`BankName`, md.`SocietyNOCDate`,md.`UnitId`, md.`Amount`,md.`CloseDate`,md.`Note` FROM `mortgage_details` md, `member_main` mm where md.`LienStatus`='Open' AND mm.`member_id` = md.`member_id` AND md.`UnitId`=".$unitId." AND md.`Status` = 'Y'";
				$res=$this->m_dbConn->select($query);
				for($i=0;$i<sizeof($res);$i++)
				{
					$res[$i]['SocietyNOCDate']=getDisplayFormatDate($res[$i]['SocietyNOCDate']);
				}
				$method="getlien";
				if($res!="")
				{
					$data=$this->display1($res,$type);
				}
				else
				{
					$res="";
					$data=$this->display1($res,$type);
				}
				return $data;
			}
			if($type=="deleted")
			{
				$query="SELECT md.`Id`, mm.`owner_name`, md.`BankName`, md.`SocietyNOCDate`,md.`UnitId`, md.`Amount`,md.`CloseDate`,md.`Note` FROM `mortgage_details` md, `member_main` mm where mm.`member_id` = md.`member_id` AND md.`UnitId`=".$unitId." AND md.`Status` = 'N'";;
				$res=$this->m_dbConn->select($query);
				for($i=0;$i<sizeof($res);$i++)
				{
					$res[$i]['SocietyNOCDate']=getDisplayFormatDate($res[$i]['SocietyNOCDate']);
				}
				if($res!="")
				{
					$data=$this->display1($res,$type);
				}
				else
				{
					$res="";
					$data=$this->display1($res,$type);
				}
				return $data;
			}
		}
	}
	//Used to get society header while printing lien details.
	public function getHeader()
	{
		$socRes=$this->m_dbConn->select("SELECT * from `society` where society_id='".$_SESSION['society_id']."';");
		return $socRes;
	}
	public function deleteLien($lienId)
	{
		$sql="Update `mortgage_details` set `Status` = 'N' where `Id` = ".$lienId;
		//echo $sql;
		$res=$this->m_dbConn->update($sql);
		return $res;
	}
}
?>	