<?php if(!isset($_SESSION)){ session_start(); } ?>
<?php
include_once("includes/head_s.php");
include_once("classes/include/dbop.class.php");	
include_once("classes/import_reverse_charges.class.php");	

if(isset($_REQUEST['type']))
{
	//echo $_REQUEST['type'];
}

$dbConnRoot = new dbop(true);
$obj_import_rc = new import_reverse_charges($dbConnRoot,$m_dbConn);
?>


<html>
<head>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<script type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/populateData.js"></script>
<script type="text/javascript">

$(document).ready(
	function(){		
		$('input:submit').attr('disabled',true);
		$('input:file').change(
		function(){
			if($(this).val() != '')
			{
				var filename = $('input[type=file]').val().split('\\').pop();
				var fileExtension = ['csv', 'txt'];
				if($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) 
				{
					$('input:submit').attr('disabled',true);
					alert("Only formats are allowed : "+ fileExtension.join(', '));
				}
				else
				{
					$('input:submit').removeAttr('disabled');
				}
			}
			else 
			{
				alert('Please Select ".csv" or ".txt(Comma Separated Values)" File');
				$('input:submit').attr('disabled',true);
			}			
		});		
});

</script>

<script language="javascript" type="text/javascript">
function go_error()
{
	setTimeout('hide_error()',10000);	
}
function hide_error()
{
	document.getElementById('error').style.display = 'none';	
}		

</script>
</head>

<body onLoad="go_error();">

<center>

<form id="import_rcf_form" name="import_rcf_form" action="process/import_reverse_charges.process.php" method="post" enctype="multipart/form-data" >

<br>
<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Import Reverse Charge/Fine</div>
        <div style="text-align:right;padding-right: 50px;padding-top: 10px;">
        
        </div>
       
<div id="right_menu">
<table style="margin-top:-200px">
<?php
/*echo "<pre>";
print_r($_SESSION);
echo "</pre>";*/
if(isset($_POST["ShowData"]))
{
	
	?>
    <tr height="30"><td colspan=5 style="text-align:center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
<?php } ?> 
<strong><div id="show" style="text-align:center; width:100%; color:#FF0000"><?php //echo $show_op; ?></div></strong>
<BR/>
<BR/>

<tr><br></tr>
<tr><br></tr>
</tr>        
      <tr>
      <td valign="middle" style="color:#FF0000"><?php echo "*";?></td>
      <td>Bill Type</td>
      <td>&nbsp; : &nbsp;</td>
      <td><select name = "bill_type" id="bill_type" style="width:142px;">
      <option value="<?php echo BILL_TYPE_REGULAR ?>">Regular Bill</option>
      <option value="<?php echo BILL_TYPE_SUPPLEMENTARY ?>">Supplementary Bill</option>
      </select>
      </td>
      <!--<td><input type="radio" name="methodofimport" id="methodofimportC" value="Custom" onClick="showhide()" >&nbsp; &nbsp;Custom</td>-->
      </tr>
      <tr><br></tr>
      <tr><br></tr>
      <?php $qry1 = "select `YearID`, `YearDescription` from `year` where `status`='Y' and `YearID` = '".$_SESSION['default_year']."' ORDER BY `YearID` DESC";
	  //echo $qry;
	  ?>
            
      <tr><br></tr>
      <tr><br></tr>
      <tr id="tohide2" >
        	<td style="color:#FF0000"><?php echo "*"?></td>
			<td>Bill Year </td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            	<select name="year_id" id="year_id" style="width:142px;" ><?php /*?>onChange="get_period(this.value, <?php echo DEFAULT_PERIOD; ?>, 'period_id');"<?php */?>
                	<?php echo $obj_import_rc->combobox($qry1,0); ?>
				</select>
            </td>
		</tr>    
         <tr><br></tr>
      <tr><br></tr>
        <?php $qry2 = "select ID, Type from period  where  status='Y' and YearID = '".DEFAULT_YEAR."'";	 
	  ?>
        <tr id="tohide3" >
        	<td style="color:#FF0000"><?php echo "*" ?></td>
            <td>Bill For </td>
            <td>&nbsp; : &nbsp;</td>
            <td>
            	<select name="bill_for" id="bill_for" style="width:142px;" >
                	<?php echo $obj_import_rc->combobox($qry2,0); ?>
				</select>
            </td>
        </tr>
         <tr><br></tr>
      <tr><br></tr>
      
      <tr><br></tr>
      <tr><br></tr>
      
<tr align="left">
        	<td valign="middle"></td>
			<td>Browse File To Import</td>
            <td>&nbsp; : &nbsp;</td>
			<td id="browse" colspan="2"><input type="file" name="upload_files[]" id="file" multiple /><br/></td>
            
</tr>   

<tr align="center">
 <td colspan="5" align="center"><input type="submit" id="Import" name="Import" value="Import"  disabled  class="btn btn-primary" style="width:100px; height:30px; font-family:'Times New Roman', Times, serif; font-style:normal;color: #fff;background-color: #337ab7;border-color: #2e6da4;"/></td>
 
 </tr>

<input type="hidden" name="sid" value="<?php echo $_SESSION['society_id'];?>">
</table>
<div style="color:#FF0000">* File Should be in the *.csv/*.txt(comma delimited file) format.</div>

<span>Reverse Charge/Fine Sample File : <a href="samplefile/Reverse_Charge_Fine.csv" download>Click here to Download</a> </span>

</form>
<br>
</center>

</div>

</div>
<?php include_once "includes/foot.php"; ?>