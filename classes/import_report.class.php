
<?php 
include_once("include/display_table.class.php");

class import_report 
{
	
	public $m_dbConn;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		
	}
	
	
	public function display1($rsas)
	{
		//echo '456';
			$thheader=array('Import Report','abc');
			
			$this->display_pg->mainpg="import_report.php";
			
			$res=$this->show_import_table($rsas);
			
			return $res;
	}
		
		
	public function pgnation()
	{
		
		
		$sql1 = "SELECT `society_id`, `society_flag` ,`wing_flag` ,`unit_flag`, `member_flag` ,`ledger_flag` ,`tarrif_flag`, `billdetails_flag` FROM `import_history`  where society_id='".$_SESSION['society_id']."'";
		//$data = $this->m_dbConn->select($sql1);
		$cntr = "select count(*) as cnt from `import_history` where society_id='".$_SESSION['society_id']."'";
		
			
		$this->display_pg->sql1=$sql1;
		$this->display_pg->cntr1=$cntr;
		$this->display_pg->mainpg="import_report.php";
		$limit="20";
		$page = $_REQUEST['page'];
		$extra	= "";
		
		$res=$this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		
		return $res;
	}
	
	
	public function show_import_table($res)
	{
		
	if($res<>"")
		{
			
		?>
        <center>
        <table align="center"><tr height="25"><th bgcolor="#CCCCCC" width="100" style="text-align:center;">Import Report</th></tr></table>
        </center>
        <center>
        
        
        <table align="center" border="0">
       
        
               <tr height="30" bgcolor="#CCCCCC">
        
        	<th width="220">Society Name</th>
            <th width="90">Society Import</th>
            <th width="90">Wing Import</th>
            <th width="90">Ledger Import</th>
            <th width="90">Unit Import</th>
            <th width="90">Member Import</th>
            <th width="90">Tarrif Import</th>
            <th width="90">Bill Details Import</th>
            <th width="90">Set Defaults</th>
            
        </tr>
        <?php foreach($res as $k => $v)
		{
			
			?>
        <tr height="25" bgcolor="#BDD8F4" align="center">
        
        <?php $res1243="select  society_name from `society` where society_id=".$res[$k]['society_id']." ";
		       
			   $data=$this->m_dbConn->select($res1243);
			   
			   
		?>
        <td align="center"><?php echo $data[0]['society_name'];?></td>
                
        <!-- check society_flag -->
        <?php
		if($res[$k]['society_flag']==1)
		{?>
			<td align="center">Success</td>	
			
			<?php }
			else{?>
			<td align="center"><a href="society_import.php">Failed</a></td>
        <?php }?>
        
        
        <!-- check society_flag -->
        <?php
		if($res[$k]['wing_flag']==1)
		{?>
			<td align="center">Success</td>	
			
			<?php }
			else{?>
        
        <td align="center"><a href="wing_import.php?sid=<?php echo $res[$k]['society_id']?>">Failed</a></td>
        <?php
		//$_SESSION['society_id']=$res[$k]['society_id']; 
		}?>
        
        
        <!-- check society_flag -->
        <?php
		if($res[$k]['ledger_flag']==1)
		{?>
			<td align="center">Success</td>	
			
			<?php }
			else{?>
        <td align="center"><a href="ledger_import.php?sid=<?php echo $res[$k]['society_id']?>">Failed</a></td>
        <?php }?>

		<!-- check society_flag -->
        <?php
		if($res[$k]['unit_flag']==1)
		{?>
			<td align="center">Success</td>	
			
			<?php }
			else{?>
        
        
        <td align="center"><a href="unit_import.php?sid=<?php echo $res[$k]['society_id']?>">Failed</a></td>
        <?php }?>
        
		<!-- check society_flag -->
        <?php
		if($res[$k]['member_flag']==1)
		{?>
			<td align="center">Success</td>	
			
			<?php }
			else{?>
        


        <td align="center"><a href="member_import.php?sid=<?php echo $res[$k]['society_id']?>">Failed</a></td>
        <?php }?>
        
		<!-- check society_flag -->
        <?php
		if($res[$k]['tarrif_flag']==1)
		{?>
			<td align="center">Success</td>	
			
			<?php }
			else{?>
        
        
        
        <td align="center"><a href="tarrif_import.php?sid=<?php echo $res[$k]['society_id']?>">Failed</a></td>
        <?php }?>
        
        <!-- check society_flag -->
        <?php
		if($res[$k]['billdetails_flag']==1)
		{?>
			<td align="center">Success</td>	
			
			<?php }
			else{?>
        
        <td align="center"><a href="member_dues_import.php?sid=<?php echo $res[$k]['society_id']?>">Failed</a></td>
        <?php }?>
      
    	<?php
		if($res[$k]['society_flag']==1 && $res[$k]['wing_flag']==1 && $res[$k]['unit_flag']==1 && $res[$k]['ledger_flag']==1 && $res[$k]['tarrif_flag']==1 && $res[$k]['member_flag']==1 && $res[$k]['billdetails_flag']==1)
		{
			//echo '1';
			?>
			<td align="center"><a href="defaults.php?sid=<?php echo $res[$k]['society_id'];?>">Go</a></td>
			<?php
			}
			else
			{//echo '2';
				?>
				<td align="center">Failed</td>
				
				<?php
				}
		?>
        
        
        <?php } 
		?>
		
	
        </tr>
		</table>
       </center> 
       <?php }
        
 }
 
}
 
 ?>