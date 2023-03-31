<?php 
include_once("includes/head_s.php"); 
include_once "classes/include/dbop.class.php";
include_once("classes/servicerequest.class.php");
include_once ("classes/include/fetch_data.php");
include_once ("dbconst.class.php"); 
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
$obj_request = new servicerequest($dbConn);
$LoginIDQuery = "select `member_id` from `login` where `login_id`='".$_SESSION['login_id']."'";
$loginID = $dbConnRoot->select($LoginIDQuery);
//$details = $obj_request->getViewDetails($_REQUEST['rq']);
//
$SREmailIDs = array(); 
$strSREMailIDs = "";
if(isset($_REQUEST['rq']))
{
	if($_REQUEST['rq']<>"")
	{
		$details = $obj_request->getViewDetails($_REQUEST['rq'],true);
		
		//for($i=0;$i <= sizeof($edit)-1; $i++)
			//{ 
			$image=$details[0]['img'];
			$image_collection = explode(',', $image);	
			//print_r(sizeof($details));
			for($i=0;$i < sizeof($details); $i++)
			{
				//echo $details[$i]['email'];
				if(isset($details[$i]['email']) && $details[$i]['email'] != "")
				{
					array_push($SREmailIDs , $details[$i]['email']);
				}
			}
			//var_dump($SREmailIDs);
			if(sizeof($details) > 0)
			{
				$strSREMailIDs = implode(";", $SREmailIDs);
			}
	}
	}
?>
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Service Request Details</title>
</head>-->

<div class="panel panel-info" id="panel" style="margin-top:6%;margin-left:3.5%; border:none;width:70%;display:none">
<div class="panel-heading" id="pageheader" style="font-size:20px">
    Service Request Details
    </div>
    <br />
    	<script type="text/javascript" src="lib/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="lib/source/jquery.fancybox.pack.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="lib/source/jquery.fancybox.css?v=2.1.5" media="screen" />
<link rel="stylesheet" type="text/css" href="lib/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" />
<script type="text/javascript" src="lib/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>
<script type="text/javascript" src="js/jsServiceRequest.js"></script>
<script language="javascript" type="application/javascript">
	function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }	
	
	
</script>

<script language="javascript" type="application/javascript">
	function printTable()
	{
		//alert("test23");
	 // document.getElementById('PrintableTable').style.width='80%';
	 
	 //alert("testnew");
	  var divToPrint=document.getElementById('PrintableDiv');
	 document.getElementById('society_name').style.display='block';
	  newWin= window.open("");
	  newWin.document.write('<br><br><center>' + divToPrint.outerHTML + '</center>');
	  newWin.print();
	  newWin.close();
	  //document.getElementById('PrintableTable').style.width='100%';
	   document.getElementById('society_name').style.display='none';
	}
</script>
<script type="text/javascript" src="lib/jquery-1.7.2.min.js"></script>

	<!-- Add mousewheel plugin (this is optional) -->
	<script type="text/javascript" src="lib/jquery.mousewheel-3.0.6.pack.js"></script>

	<!-- Add fancyBox main JS and CSS files -->
	<script type="text/javascript" src="source/jquery.fancybox.js?v=2.0.6"></script>
	<link rel="stylesheet" type="text/css" href="source/jquery.fancybox.css?v=2.0.6" media="screen" />

	<!-- Add Button helper (this is optional) -->
	<link rel="stylesheet" type="text/css" href="source/helpers/jquery.fancybox-buttons.css?v=1.0.2" />
	<script type="text/javascript" src="source/helpers/jquery.fancybox-buttons.js?v=1.0.2"></script>

	<!-- Add Thumbnail helper (this is optional) -->
	<link rel="stylesheet" type="text/css" href="source/helpers/jquery.fancybox-thumbs.css?v=1.0.2" />
	<script type="text/javascript" src="source/helpers/jquery.fancybox-thumbs.js?v=1.0.2"></script>

	<!-- Add Media helper (this is optional) -->
	<script type="text/javascript" src="source/helpers/jquery.fancybox-media.js?v=1.0.0"></script>
    
    <script type="text/javascript">
		$(document).ready(function() {			
		
		//Simple image gallery. Uses default settings
		$('.fancybox').fancybox();
		$('.fancybox1').fancybox();
	
	
		//Button helper. Disable animations, hide close button, change title type and content
		$('.fancybox-buttons').fancybox({
		openEffect  : 'none',
		closeEffect : 'none',

		prevEffect : 'none',
		nextEffect : 'none',

		closeBtn  : false,

		helpers : {
			title : {
				type : 'inside'
			},
			buttons	: {}
		},

			afterLoad : function() {
				this.title = 'Image ' + (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
			}
		});
		});
	</script>
    
	<style type="text/css">
		.fancybox-custom .fancybox-skin 
		{
			box-shadow: 0 0 50px #222;
		}
		.fancybox-close{
			    position: absolute;
    top: -4px !important;
    right: -4px !important;
    width: 36px;
    height: 36px;
    cursor: pointer;
    z-index: 8040;
			}
	</style>

<body>

<!--<div id="bill_header" style="text-align:center; border-style:solid;">
            <div id="society_name" style="font-weight:bold; font-size:18px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
            <div id="society_reg" style="font-size:14px;"><?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Reg. No. ".$objFetchData->objSocietyDetails->sSocietyRegNo;
					echo " ";
					echo "Dated:".$objFetchData->objSocietyDetails->sSocietyRegDate; 
				}
				?>
            </div>
            <div id="society_address"; style="font-size:14px; border-left:thick;"><?php echo 'Reg. Add:'.$objFetchData->objSocietyDetails->sSocietyAddress; ?></div>
</div>-->
<?php //echo "role".$_SESSION['role'];?>
<center><!--<a href="servicerequest.php">Go Back</a>-->
<div style="padding-left: 15px;padding-bottom: 10px;"><button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;" id="btnBack"><i class="fa  fa-arrow-left"></i></button>
		 <center> <INPUT TYPE="button" id="Print" onClick="printTable()" name="Print" value="Print"   class="btn btn-primary"></center></div>
</center>


<br>
<br>
<?php 
//print_r($details);
if($details <> "")
	  { ?>
<div width="100%" style="font-size:12px;" id="PrintableDiv"  >
<div id="society_name" style="font-weight:bold; font-size:18px; display:none;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
   
<table width="100%" style="font-size:12px;" id="PrintableTable">

	<tr style="background-color:#bce8f1;font-size:14px;" height="25">
        <th style="width:20%;"><center>Service Request ID</center></th>
        <th style="width:20%;"><center>Raised Date</center></th>
        <th style="width:20%;"><center>Status</center></th>
        <th style="width:20%;"><center>Priority</center></th>
        <th style="width:20%;"><center>Category</center></th>
        <!--<th style="width:20%;">Photo</th>-->
    </tr>
    <tr>
    <?php $timestamp = strtotime($details[0]['timestamp']);
	
		//$sql = "SELECT `img` FROM `service_request` WHERE `request_id` = '".$_SESSION['unit_id']."' AND `society_id` = '".$_SESSION['society_id']."'";
		//$result = $this->m_dbConn->select($sql);
		
		$CategoryDetails = $obj_request->GetCategoryDetails( $details[0]['category']);
		//var_dump($CategoryDetails);

	?>
    	
    	<td align="center"><?php echo $_REQUEST['rq'];?></td>
       <td align="center"><?php echo date("d-m-Y (h:i:s A)", $timestamp +053000);?></td>
       
        <td align="center"><?php echo $details[sizeof($details)-1]['status'];?></td>
        <td align="center"><?php echo $details[0]['priority'];?></td>
        <td align="center"><?php echo $CategoryDetails[0]['category'];?></td>
      <!--  <td align="center"><a href="<?php// echo substr($details[0]['img'],3);?>" class="fancybox"><img src="<?php// echo substr($details[0]['img_thumb'],3);?>" height="100" width="100" /></a></td>-->
        
    </tr> 
    <tr><td colspan="10"><br /></td></tr>   
    <tr style="background-color:#bce8f1;font-size:14px;"  height="25">
    	<th colspan="10"  align="left"> Summary</th>
   	</tr>
    <tr>
    	<td colspan="10" align="left"><span style="margin-left:10px; float: left;margin-top: 5px;"><?php echo nl2br(htmlentities($details[0]['summery'], ENT_QUOTES, 'UTF-8'));?></span></td>
    </tr>
    <tr><td colspan="10"><br /></td></tr> 
        
    <tr style="background-color:#bce8f1;font-size:14px;" height="25">
    	<th colspan="10" align="left">Request Description</th>        
    </tr>
    <tr>
    	<td colspan="10" align="left"><span style="margin-left:10px; float: left; margin-top: 5px;"><?php echo $details[0]['details'];?></span></td>
    </tr>
     <tr><td colspan="10"><br /></td></tr> 
    <tr style="background-color:#bce8f1;font-size:14px;" height="25">
    	<th colspan="10" align="left">Attachments</th>        
 <tr><td>
 <table width="500%"><tr><td  align="left">
     <?php 
	// print_r($image_collection);
		for($i=0;$i<sizeof($image_collection);$i++)
		{
			if(strlen($image_collection[$i]) >0 )
			{
		?>
        
		<a href="upload/main/<?php echo $image_collection[$i]?>" class="fancybox"><img  style="    width: 100px;
    height: 70px;" src="upload/main/<?php echo $image_collection[$i]?>" ></a>
      <?php
	   }
		}
	  ?>
       </td></tr></table>
       
      </td>
  <!--  <td align="center"><a href="<?php// echo substr($details[0]['img'],3);?>" class="fancybox"><img src="<?php// echo substr($details[0]['img_thumb'],3);?>" height="100" width="100" /></a></td>-->

    </tr>
    <tr><td colspan="10"><br /></td></tr> 
    <tr style="background-color:#bce8f1;font-size:14px;"  height="25">
    	<th colspan="10" align="left">History</th>        
    </tr>
    <tr>
    	<th align="left">Summary</th>
        <th align="left">Changed By</th>
        <th align="left">Status</th>
        <th align="left">Timestamp</th>
    </tr>
   
    <?php
	for($i = sizeof($details)-1; $i>=0;$i-- )
		{
			$timestamp = strtotime($details[$i]['timestamp']);
		
	?>
    <tr>
    	<td align="left"><?php echo $details[$i]['summery']; ?></td>
        <td align="left"><?php echo $details[$i]['reportedby']; ?></td>
        <td align="left"><?php echo $details[$i]['status']; ?></td>
        <td align="left"><?php echo date("d-m-Y (h:i:s A)", $timestamp +053000 ); ?></td>
    </tr>
    <?php
		}
	?>
    <tr><td colspan="5"><br /></td></tr> 
</table>
</div>
<br />
<form name="viewrequest" id="viewrequest" method="post" action="process/servicerequest.process.php?vr=<?php echo $_REQUEST['rq'];?>" onSubmit="return val();">
<center>
<table style="border:1px solid #CCC; width:75%;font-size:12px; padding:10px; border-radius: 15px;">
<?php
	if(isset($_POST["ShowData"]))
		{
?>
			<tr height="30"><td colspan="3" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
<?php   }
		else
		{?>
			<tr height="30"><td colspan="3" align="center"><font color="red" style="size:11px;"><b id="error"></b></font></td></tr>
<?php 	} ?>   
	<tr>
		<th style="width:20%;">&nbsp; Changed By</th>
        <td style="width:10%;">&nbsp; : &nbsp;</td>
        <td style="width:70%;"><?php echo $_SESSION['name'];?></td>
    </tr>
    <tr><td colspan="3"><input type="hidden" name="changedby" id="changedby" value="<?php echo $_SESSION['name'];?>" /></td></tr>
     <?php if($_SESSION['role'] && $_SESSION['role']==ROLE_ADMIN){?>
     <tr align="left">
        <th>&nbsp; Priority</th>
        <td>&nbsp; : &nbsp;</td>
        <td>
        	<select id="priority" name="priority">
            	<option value="0" <?php if($details[0]['priority']=='0'){?> selected <?php }?>> Please Select </option>
                <option value="1 - Low" <?php if($details[0]['priority']=='1 - Low'){?> selected <?php }?>> 1 - Low </option>
                <option value="2 - Medium" <?php if($details[0]['priority']=='2 - Medium'){?> selected <?php }?>> 2 - Medium </option>
                <option value="3 - High" <?php if($details[0]['priority']=='3 - High'){?> selected <?php }?>> 3 - High </option>
            </select>
        </td>
	</tr>
    <tr><td colspan="3"><br /></td></tr>
    <?php }?>
    <tr>
    	<th>&nbsp; Status</th>
        <td>&nbsp; : &nbsp;</td>
        <td>
        	<select id="status" name="status">
            	<option value="0"> Please Select </option>
                <?php
				
				if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER || $_SESSION['role']==ROLE_SUPER_ADMIN))
	      		{?>
<!--                <option value="Raised" <?php //if($details[sizeof($details)-1]['status']=='Raised'){?> selected <?php// }?>> Raised </option>
-->                <option value="Assigned" <?php if($details[sizeof($details)-1]['status']=='Assigned'){?> selected <?php }?>> Assigned </option>
                <option value="In process" <?php if($details[sizeof($details)-1]['status']=='In process'){?> selected <?php }?>> In process </option>
                <option value="Resolved" <?php if($details[sizeof($details)-1]['status']=='Resolved'){?> selected <?php }?>> Resolved </option>
                <option value="Waiting for details" <?php if($details[sizeof($details)-1]['status']=='Waiting for details'){?> selected <?php }?>> Waiting for details </option>
   				<?php } 
				else
				{
                	$status=$details[sizeof($details)-1]['status'];
					?>
                	<option value="<?php echo $status;?>"  selected ><?php echo $status;?></option>
          <?php }?>
                
                <option value="Reopen" <?php if($details[sizeof($details)-1]['status']=='Reopen'){?> selected <?php }?>> Re-Open </option>
                <option value="Closed" <?php if($details[sizeof($details)-1]['status']=='Closed'){?> selected <?php }?>> Closed </option>                
            </select>
        </td>
    </tr>
    <tr><td colspan="3"><br /></td></tr>
    <tr>       
        <th>&nbsp; Comments</th>
        <td>&nbsp; : &nbsp;</td>
        <td><textarea name="comments" id="comments" rows="6" cols="60"></textarea></td>
	</tr>
    <tr><td colspan="3"><br /><input type="hidden" id="unit" name="unit" value="<?php echo $_SESSION['unit_id'] ?>"></td></tr>
    <tr align="center">
    	<td colspan="3"><input type="submit" name="submit" id="submit" value="Submit Comments"  class="btn btn-primary"/> </td>
    </tr>
    <tr><td colspan="3"><input type="hidden" name="emailID" id="emailID" value="<?php echo $loginID[0]['member_id']; ?>" /></td></tr>
        <tr><td colspan="3"><input type="hidden" name="SREmailIDs" id="SREmailIDs" value="<?php echo $strSREMailIDs; ?>" /></td></tr>
     
    <tr><td colspan="3"><br /></td></tr>      
</table> 
<br>
</center> 
</form>      
<?php }
else { ?>
<div style="font-size:14px; color:#FF0000;"> <p> Service Request <?php echo $_REQUEST['rq']; ?> does not exist. </p></div>
<?php } ?>
</center>
</body>
<!--</html>-->
<?php
if($_SESSION['is_year_freeze'] == 1)
{?>
<script>
	$("#viewrequest").css( 'display', 'none' );
</script>
<?php }?>

</div>

<?php include_once "includes/foot.php"; ?>
        