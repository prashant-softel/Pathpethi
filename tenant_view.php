<?php 
include_once("includes/head_s.php"); 
include_once "classes/include/dbop.class.php";
include_once ("dbconst.class.php");
include_once('classes/tenant.class.php') ;
$dbConn = new dbop();
$obj_tenant = new tenant($dbConn);
		$details = $obj_tenant->getViewDetailsUser($_REQUEST['rq']);
		//print_r($details);
		
	
?>

<script language="javascript" type="application/javascript">
function printTable()
{
	document.getElementById('PrintableDiv').style.display = 'none';	
	document.getElementById('PrintableDiv').style.display = 'none';	
	
	window.print();
}
</script>

<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Service Request Details</title>
</head>-->

<div class="panel panel-info" id="panel" style="margin-top:6%;margin-left:3.5%; border:none;width:70%;display:none">
<div class="panel-heading" id="pageheader" style="font-size:20px">
   Lease Details
    </div>
    <br />
 <script type="text/javascript" src="lib/jquery-1.10.2.min.js"></script>
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
<body>

<center><!--<a href="servicerequest.php">Go Back</a>-->
<div style="padding-left: 15px;padding-bottom: 10px;"><button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;" id="btnBack"><i class="fa  fa-arrow-left"></i></button>
		 <center> <!--<INPUT TYPE="button" id="Print" onClick="printTable()" name="Print" value="Print"   class="btn btn-primary">--></center></div>
</center>


<br>
<br>
<?php 
if($details <> "")
	  { ?>
<div width="100%" style="font-size:12px;" id="PrintableDiv"  >
<div id="society_name" style="font-weight:bold; font-size:18px; display:none;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
   
<table width="100%" style="font-size:12px;" id="PrintableTable">

	<tr style="background-color:#bce8f1;font-size:14px;" height="25">
        <th style="width:25%;"><center>Name on the Lease Document</center></th>
        <th style="width:15%;"><center>Lease Start Date</center></th>
        <th style="width:15%;"><center>Lease End Date</center></th>
        <th style="width:10%;"><center>Agent Name</center></th>
        <th style="width:20%;"><center>Agent Contact No.</center></th>
        <!--<th style="width:20%;">Photo</th>-->
    </tr>
    <tr>
    	<td align="center" style="text-transform: capitalize;"><?php echo $details[0]['tenant_name'];?></td>
        <td align="center"><?php echo  $details[0]['start_date']?></td>
        <td align="center"><?php echo $details[0]['end_date']?></td>
        <td align="center"><?php echo  $details[0]['agent_name']?></td>
        <td align="center"><?php echo $details[0]['agent_no']?></td>
      <!--  <td align="center"><a href="<?php// echo substr($details[0]['img'],3);?>" class="fancybox"><img src="<?php// echo substr($details[0]['img_thumb'],3);?>" height="100" width="100" /></a></td>-->
        
    </tr> 
    <tr><td colspan="10"><br /></td></tr>   
    <tr style="background-color:#bce8f1;font-size:14px;"  height="25">
    	<th colspan="10" align="left">Tenant occcupaying in the flat </th>
   	</tr>
    <tr style="background-color:#f9f9f9;font-size:14px;"  height="20" align="left">
    <td style="width:30%"><b>Name</b></td>
    <td style="width:15%"><b>Relation</b></td>
    <td style="width:20%"><b>Date Of Birth</b></td>
    <td style="width:15%"><b>Contact No.</b></td>
    <td style="width:20%"><b>Email Address</b></td>
    </tr>
    
    <?php 
	for($i=0; $i<sizeof($details[0]['members']); $i++)
	{
	?><tr>
    	<td align="left" style="text-transform: capitalize;"><?php echo $details[0]['members'][$i]['mem_name'];?></td>
        <td align="left" style="text-transform: capitalize;"><?php echo $details[0]['members'][$i]['relation'];?></td>
        <td align="left"><?php echo $details[0]['members'][$i]['mem_dob'];?></td>
        <td align="left"><?php echo $details[0]['members'][$i]['contact_no'];?></td>
        <td align="left"><?php echo $details[0]['members'][$i]['email'];?></td>  </tr>
        <?php }?>
  
    <tr><td colspan="10"><br /></td></tr> 
        
    <!--<tr style="background-color:#bce8f1;font-size:14px;" height="25">
    <th colspan="10">Agent Detail</th>
    </tr>
    <tr style="background-color:#f9f9f9;font-size:14px;"  height="20">
    	<td  style="width:30%"><b>Name</b></td> 
        <td  style="width:30%"><b>Contact No</b></td>        
    </tr>
    <tr>
    	<td align="left" style="text-transform: capitalize;"><?php// echo $details[0]['agent_name']?></td>
        <td align="left"><?php //echo $details[0]['agent_no']?></td>
    </tr>
-->     <tr><td colspan="10"><br /></td></tr> 
    <tr style="background-color:#bce8f1;font-size:14px;" height="25">
    	<th colspan="10" align="left">Document List</th>        
 </tr>
 <?php 
 for($i=0;$i<sizeof($details[0]['documents']);$i++)
 {
 ?>
 <tr>
 <td align="left" style="text-transform: capitalize;"><a href="Uploaded_Documents/<?php echo $details[0]['documents'][$i]['Document'];?> " target="_blank"><?php echo $details[0]['documents'][$i]['Name'];?></a></td>    </tr>
   <?php  }?>
        <tr><td colspan="5"><br /></td></tr> 
</table>
</div>
</center> 
<?php }?>

</div>

<?php include_once "includes/foot.php"; ?>
        