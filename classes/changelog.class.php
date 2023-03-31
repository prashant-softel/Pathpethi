<?php 
if(!isset($_SESSION)){ session_start(); }
include_once("include/display_table.class.php");?>
<?php
	
	class changeLog 
	{
		public $m_dbConn;
		
		function __construct($dbConn)
		{
			$this->m_dbConn = $dbConn;
			$this->display_pg=new display_table($this->m_dbConn);
		}
		
		function setLog($desc, $changedBy, $changedTable, $changedKey)
		{
			$logID = 0;
			
			$sqlLog = "INSERT INTO `change_log`(`ChangedLogDec`, `ChangedBy`, `ChangedTable`, `ChangedKey`) VALUES ('" . $this->m_dbConn->escapeString($desc) . "', '" . $this->m_dbConn->escapeString($changedBy) . "', '" . $this->m_dbConn->escapeString($changedTable) . "', '" . $this->m_dbConn->escapeString($changedKey) . "')";
			
			$logID = $this->m_dbConn->insert($sqlLog);
			
			return $logID;
		}
		
		function getLog($logID)
		{
			$sqlLog = "Select log.ChangedLogDec, log.ChangeTS, log.ChangedTable, log.ChangedKey, user.name from change_log as log JOIN login as user on log.ChangedBy = user.login_id where log.ChangeLogID = '" . $logID . "'";
			
			$result = $this->m_dbConn->select($sqlLog);
			
			if($result <> "")
			{
				$response = array('success'=>'0', 'desc'=>$result[0]['ChangedLogDec'], 'user'=>$result[0]['name'], 'time'=>$result[0]['ChangeTS'], 'table'=>$result[0]['ChangedTable'], 'key'=>$result[0]['ChangedKey']);
				
				return $response;
			}
			else
			{
				$response = array('success'=>'1');
				
				return $response;
			}
			
			
		}
		
	
	public function display1($rsas, $bShowViewLink = false)
	{
		//echo "inside display1";
		$thheader=array('ChangeTS','ChangeBy','ChangedLogDec','ChangedTable');
		$this->display_pg->th=$thheader;
		$this->display_pg->mainpg="unit.php";
			//	echo "calling showunit";
		//$res=$this->display_pg->display_new($rsas);
		$res=$this->show_unit($rsas, $bShowViewLink);
		//echo "exiting display1";
		return $res;
	}
		
	public function pgnation($bShowViewLink = false)
	{
		//$_REQUEST['ChangedBy']  $_REQUEST['ChangeTSFrom']  $_REQUEST['ChangeTSTo']
		if($_REQUEST['method']=='applyFilter')
		{
			
			$sql1 = "SELECT chnglogtbl.ChangeLogID,chnglogtbl.ChangeTS,logintbl.name,chnglogtbl.ChangedLogDec,chnglogtbl.ChangedTable,chnglogtbl.ChangedKey FROM `change_log` as chnglogtbl JOIN `login` as logintbl on chnglogtbl.ChangedBy=logintbl.login_id WHERE 1";
			if($_REQUEST['ChangedBy'] > 0)
			{
				$sql1 .="  and chnglogtbl.ChangedBy='".$_REQUEST['ChangedBy']."'";
			}
			
			if($_REQUEST['ChangeTSFrom'] > 0 && $_REQUEST['ChangeTSTo'] > 0)
			{
				$sql1 .="  and chnglogtbl.ChangeTS between '".$_REQUEST['ChangeTSFrom']."' and '".$_REQUEST['ChangeTSTo']."'";
			}
			
			if($_REQUEST['ChangeTableName'] <> "" && $_REQUEST['ChangeTableName'] <> "Please Select")
			{
				$sql1 .="  and chnglogtbl.ChangedTable='".$_REQUEST['ChangeTableName']."'";
			}
		}
		else
		{
			$sql1 = "SELECT chnglogtbl.ChangeLogID,chnglogtbl.ChangeTS,logintbl.name,chnglogtbl.ChangedLogDec,chnglogtbl.ChangedTable,chnglogtbl.ChangedKey FROM `change_log` as chnglogtbl JOIN `login` as logintbl on chnglogtbl.ChangedBy=logintbl.login_id where 1";
		}
		//echo $sql1;
		if($_REQUEST['method']=='applyFilter')
		{
			$cntr = "SELECT chnglogtbl.ChangeLogID,chnglogtbl.ChangeTS,logintbl.name,chnglogtbl.ChangedLogDec,chnglogtbl.ChangedTable,chnglogtbl.ChangedKey FROM `change_log` as chnglogtbl JOIN `login` as logintbl on chnglogtbl.ChangedBy=logintbl.login_id where 1";
			
			if($_REQUEST['ChangedBy'] > 0)
			{
				$cntr .="  and chnglogtbl.ChangedBy='".$_REQUEST['ChangedBy']."'";
			}
			
			if($_REQUEST['ChangeTSFrom'] > 0 && $_REQUEST['ChangeTSTo'] > 0)
			{
				$cntr .="  and chnglogtbl.ChangeTS between '".$_REQUEST['ChangeTSFrom']."' and '".$_REQUEST['ChangeTSTo']."'";
			}
			
			if($_REQUEST['ChangeTableName'] <> "" && $_REQUEST['ChangeTableName'] <> "Please Select")
			{
				$cntr .="  and chnglogtbl.ChangedTable='".$_REQUEST['ChangeTableName']."'";
			}
		}
		else
		{
			$cntr = "SELECT chnglogtbl.ChangeLogID,chnglogtbl.ChangeTS,logintbl.name,chnglogtbl.ChangedLogDec,chnglogtbl.ChangedTable,chnglogtbl.ChangedKey FROM `change_log` as chnglogtbl JOIN `login` as logintbl on chnglogtbl.ChangedBy=logintbl.login_id where 1";
		}
		//$thheader=array('ChangeTS','ChangeBy','ChangedLogDec','ChangedTable');
		$thheader = array('TimeStamp','Change By','Changed Log Decription','Changed Table','Changed Key');	
		//$thheader = array('ChangeTS','ChangeBy','ChangedLogDec','ChangedTable','ChangedKey');	
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "ChangeLog.php";
		$this->display_pg->sql1=$sql1;
		$this->display_pg->cntr1=$cntr;
		$limit = "2000";
		$page=$_REQUEST['page'];
		//echo $sql1;
		$result=$this->m_dbConn->select($sql1);
		//print_r($result);
		$res = $this->display_pg->display_datatable($result,false,false);
		return $res;
		
		//$result = $this->m_dbConn->select($sql1);
		//$this->show_unit($result, false);
	}
	/*
	public function show_unit($res, $bShowViewLink = false)
	{
		
		if($res<>"")
		{
					
			//print_r($res);
			if(!isset($_REQUEST['page']))
			{
				$_REQUEST['page'] = 1;
			}
			$iCounter = 0;
			$sortOrder=0;
	
		?>
		<table id="example" style="text-align:center; width:100%;" class="table table-bordered table-hover table-striped">
        <thead>
		<tr height="30">
       		<th width="150" style="text-align:center">Change TimeStamp</th>
            <th width="150" style="text-align:center">Changed By</th>
            <th width="250" style="text-align:center">Changed Log Description</th>
            <th width="100" style="text-align:center">Changed Table</th>
         </tr>
        </thead>
        <tbody>
		<?php 
		//print_r($res);
		foreach($res as $k => $v){
			$iCounter++;
			?>
        	<td align="center"><?php echo $res[$k]['ChangeTS'];?></td>
            <td align="center"><?php echo $res[$k]['name'];?></td>
            <td align="center"><?php echo $res[$k]['ChangedLogDec'];?></td>
            <td align="center"><?php echo $res[$k]['ChangedTable'];?></td>
            
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
	}*/
	
	public function comboboxEx($query)
	{
		$id=0;
		//echo "<script>alert('test')<//script>";
		$str.="<option value=''>Please Select</option>";
	$data = $this->m_dbConn->select($query);
	//echo "<script>alert('test2')<//script>";
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
					//echo "<script>alert('".$str."')<//script>";
					$i++;
				}
			}
		}
		//return $str;
		//print_r( $str);
		//echo "<script>alert('test')<//script>";
		return $str;
	}
	
	}
?>