<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");   
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/tips.class.php");
include_once("classes/utility.class.php");
$obj_tips = new tips($m_dbConnRoot,$m_dbConn);
$obj_utility=new utility($m_dbConn);
$AllDetails = $obj_tips->getRecords();
//print_r($AllDetails);
?>
<style>
    .link{display:inline}
    .link {float: left}
	.disabled {
   pointer-events: none;
   cursor: default;
}
	
</style>


<div class="panel panel-info" style="margin-top:2%;margin-left:3.5%; border:none;width:85%">

    <div class="panel-heading" style="font-size:20px">
    <center>  View All Tips  </center>
    </div>
  
    <br />
     <div style="padding-left: 15px;"><button type="button" class="btn-primary btn-circle" onClick="history.go(-1);" style="float:left;"><i class="fa  fa-arrow-left"></i></button></div>
    <center>
    <?php if($_SESSION['role'] && ($_SESSION['role']==ROLE_MASTER_ADMIN))
	{
							 
if($_SESSION['is_year_freeze'] == 0)
{?>

<!--<button type="button" class="btn btn-primary" onclick="window.location.href='client.php'">Go Back</button>-->
   <button type="button" class="btn btn-primary" onClick="window.location.href='tips.php'">Add Tips</button>
<?php }?>
</center>   
    <!--<span class="link"><a href="addservicerequest.php">Create New Service Request</a></span> -->
    <br />
    <div class="panel-body">                        
        <div class="table-responsive">
            <table id="example" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                    <th style="width:5%">Edit</th>
                        <th style="width:5%">Delete</th>
                        <th style="width:20%">Dashboard Type</th> 
                        <th style="width:20%">Subject</th>
                        <th style="width:50%">Description</th>
                        <th style="width:5%">Video</th>
                         <!--<th>View</th>-->                                                                      
                    </tr>
                </thead>
                <tbody>
                	<?php 
						for($i = 0; $i < sizeof($AllDetails); $i++)
						{	?>						
						<tr>
                    	<td><a href="tips.php?edit=<?php echo $AllDetails[$i]['id'];?>"><img src="images/edit.gif" /></a></td>
                    	<td><a href="tips.php?deleteid=<?php echo $AllDetails[$i]['id'];?>"><img src="images/del.gif" /></a></td>
                        <td align="center">
						<?php if($AllDetails[$i]['dashboard_key']==0)
						{
							echo "Don't Show in Dashboard";
						}
						else if ($AllDetails[$i]['dashboard_key']==1)
						{
							echo "Show in All";
						}
						else if ($AllDetails[$i]['dashboard_key']==2)
						{
							echo "My Society";
						}
						else
						{
							echo "Accounting / Admin";
						}
						?>
                        </td>
                         <td align="center"><?php echo $AllDetails[$i]['atr_title'];?>
                        <td><div style="text-align:justify">
                        <?php
						//$AllDetails[$i]['desc'] = preg_replace("/<img[^>]+\>/i", "", $AllDetails[$i]['desc']); 
						$AllDetails[$i]['desc']  = strip_tags($AllDetails[$i]['desc'] ); 
						if(strlen($AllDetails[$i]['desc']) >250)
						{?>
						<?php echo substr($AllDetails[$i]['desc'],0,250);?>...
                        <?php }
						else
						{?>
							<?php echo $AllDetails[$i]['desc'];?>
						<?php }?>
                        </div></td>
                        <td>
                        <?php if($AllDetails[$i]['url'] <> '')
						{?>
                         <img src="images/video.png" width="35" height="35">
                         <?php }
						 else
						 {?>
                           <img src="images/video.png" width="35" height="35" style="display:none">
                         <?php }?>
                        </td>
                     </tr>
                    <?php
						}
					?>
                </tbody>
                            </table>
        </div>
    </div>
</div>
<?php }?>
<?php /*?><?php
if(isset($_REQUEST['rq']) && $_REQUEST['rq'] <> '')
	{
		?>
			<script>
				getService('delete-' + <?php echo $_REQUEST['rq'];?>);				
			</script>
		<?php
	}
?><?php */?>


<?php include_once "includes/foot.php"; ?>