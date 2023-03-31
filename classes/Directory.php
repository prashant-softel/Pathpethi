<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
// include_once("RightPanel.php");    
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/directory.class.php");
?>
<?php 
	$objDirectory = new direcory($m_dbConn);
	$result = $objDirectory->FetchUnits();
?>
 <div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">
 
    <div class="panel-heading" style="font-size:20px">
     Directory
    </div>
    <div class="panel-body">                        
        <div class="table-responsive">
              <table id="example" class="display" cellspacing="0" width="65%">
                <thead>
                    <tr align="center">
                    	<th>Owner Name</th>
                        <th>Unit No</th>                        
                        <th>Intercom No</th>  
                         <?php  
							if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER || $_SESSION['role']==ROLE_SUPER_ADMIN))
	      				{?>          
                        <th>Mobile</th>
                        <th>Email</th>  
                        <?php 
						} ?>                                  
                    </tr>
                </thead>
                <tbody>
                <?php if($result <> '')
				{
					foreach($result as $key=>$val)
					{
				?>
                       <tr align="center">
                       		<td><?php echo $result[$key]['owner_name'] ?></td>
                            <td><?php echo $result[$key]['unit_no'];?></td>
                            <td><?php echo $result[$key]['intercom_no'];?></td> 
                             <?php  
							if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER || $_SESSION['role']==ROLE_SUPER_ADMIN))
	      					{?>
                            <td><?php echo $result[$key]['mob'];?></td> 
                            <td><?php echo $result[$key]['email'];?></td> 
                            <?php 
							} ?>	
                        </tr> 
               <?php } 
				} ?>                                        			
                </tbody>
            </table>
        </div>
        
        <div class="panel-footer">
        Print
        </div>
        <br>
        <br />
        <br>
        <br />
        
      </div>
</div>



<?php include_once "includes/foot.php"; ?>