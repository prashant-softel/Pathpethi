<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
// include_once("RightPanel.php");    
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/utility.class.php");
$obj_utlity = new utility($m_dbConn);
$resBlocked = $obj_utlity->GetBlockedUsersDesc();
$strReason = $resBlocked[0]["block_desc"];

?>
<style>
    .link{display:inline}
    .link {float: left}
	.disabled {
   pointer-events: none;
   cursor: default;
}
</style>
<!--<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/ajax_new.js"></script>
<script type="text/javascript" src="js/jsServiceRequest.js"></script>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
-->
<div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">
 
    <div class="panel-heading" style="font-size:20px">
     Access Denied
    </div>
    <br />
  
    <!--<span class="link"><a href="addservicerequest.php">Create New Service Request</a></span> -->
    <br />
    <div class="panel panel-danger" style="font-size: large;">
        <div class="panel-heading">
            <table>
                <tr>
                    <td>
                        <i class="fa fa-lock fa-45x" style="font-size:20px;font-size:10.75vw"></i>
                    </td>
                    <td style="margin-bottom:none">
                        <!-- <p style="margin-left:5%;text-align:justify;padding-bottom:1px"><b>Access Denied.</b></p> -->
                        <p style="margin-left:5%;text-align:justify;padding-bottom:1px">We are sorry, but your access has been blocked for this feature. Please contact your Managing Committee for resolution.</p>
                    
                    <br>
                    Reason : <?php echo strtoupper($strReason); ?>
                    <br>
                    <input type="button" name="ok" id="ok" style="background-color: white;width: 200px;height: 45px;padding: 2px 4px 2px 4px;margin-top: 5%" value="Contact Committee" onclick="window.location.href='commitee.php'" >
                        <input type="button" name="ok" id="ok" style="background-color: white;width: 100px;height: 45px;margin-left:5%;padding: 2px 4px 2px 4px;margin-top: 5%" value="OK" onclick="window.location.href='Dashboard.php'" >
                    </td>
                </tr>
            </table>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/foot.php"; ?>