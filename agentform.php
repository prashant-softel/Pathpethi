<?php include_once("includes/head_s.php");?>
<?php include("classes/agent_form.class.php");
include_once ("classes/dbconst.class.php");
//echo "h";
//var_dump($_SESSION);
$obj_agent_form = new agent_form($m_dbConn);
//error_reporting(1);
?>
<html>
<head>
<script type="text/javascript" src="js/jsAgent.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >

<title> Agent Master Form </title>
<script language="javascript" type="application/javascript">
	function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }	
	document.body.onload =	function()
	{			
		go_error();			
	}
	
	</script>
	<script type="text/javascript">
        $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
         	showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true, 
			yearRange: '-0:+10', // Range of years to display in drop-down,
        })
	});
	     
    </script>

</head>
<?php if(isset($_POST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body>
<?php }else{ ?>
<body>
<?php } ?>
	
<div class="panel panel-info" id="panel" style="display:block">
<div class="panel-heading" id="pageheader"> Agent Master Form </div>

<center>
<form name="agentForm" id="agentForm" method="post" action="process/agentform.process.php" enctype="multipart/form-data" onSubmit="return val()";>
<input type="hidden" name="agent_id" id="agent_id" value="">
<?php
$star = "<font color='#FF0000'>*</font>";
if(isset($_REQUEST['msg']))
		{
			$msg = "Sorry !!! You can't delete it. ( Dependency )";
		}
		else if(isset($_REQUEST['msg1']))
		{
			$msg = "Deleted Successfully.";
		}
	
?>
<table align='center'>
		<?php
		if(isset($msg))
		{
			if(isset($_POST["ShowData"]))
			{
		?>
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
		<?php
			}
			else
			{
			?>
            	<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $msg; ?></b></font></td></tr>	   
            <?php		
			}
		}
		else
		{
		?>	
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
        <?php
		}
		?>



<table align='center' style="width:100%;">

<table width="50%" style="font-size:12px; float:left;">
        <tr align="left">
        <td valign="middle"><?php echo $star;?></td>
			<td>A/C Type</td>
            <td>&nbsp;&nbsp; : &nbsp;&nbsp;</td>
			<td>
                <select name="accounttype" id="accounttype">
				<?php echo $combo_state=$obj_agent_form->combobox("select ID,Description from billing_cycle_master",'0');?>  
				</select>
            </td>
        </tr>
        <tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td> Agent Name </td>
            <td>&nbsp; : &nbsp;</td>			
			<td><input type="text" name="agentname" id="agentname"></td>                      
		</tr>
        <tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>SubGL Code</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="subglcode" id="subglcode"></td>
		</tr>
        <tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td> Age </td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="age" id="age"></td>
		</tr>
        <tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Qualification</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="qualification" id="qualification"></td>
		</tr>
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Residential Address</td>
            <td>&nbsp; : &nbsp;</td>
			<td><textarea cols="40" rows="5" name="resi_add" id="resi_add" value=""></textarea></td>
		</tr>
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>City</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="city" id="city"></td>
		</tr>
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>State</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="state" id="state"></td>
		</tr>
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Pin No</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="pinno" id="pinno"></td>
		</tr>
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Area</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="area" id="area"></td>
		</tr>
        
</table>

<table width="50%" style="font-size:12px; float:left;">
<tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Tel No</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="telno" id="telno"></td>
		</tr>
        <tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Mobile No</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="mob" id="mob" maxlength="10"></td>
		</tr>
        <tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Commission (%)</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="comm" id="comm"/></td>
		</tr>
        <tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td> T.D.S (%)</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="tds" id="tds"/></td>
		</tr>
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Date of Joining</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="datepicker" id="datepicker" class="basics" readonly style="width:80px;"/></td>

			<!--<td><input type="text" name="dateofjoining" id="datepicker" style="width:100px"/>-->
        </td>
		</tr>
        <tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Reffererd By</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="refferedby" id="refferedby"></td>
			<!--<td>
			<select name="refferedby" id="refferedby">
			<?php //echo $combo_state=$obj_agent_form->combobox("select agent_id,ref_by from pp_agent",'0');?>  
            </select>
            </td>-->
        </tr>
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Sar Charges</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="sarcharges" id="sarcharges"/></td>
		</tr>
        <tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Edu. Ses. </td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="edu" id="edu"/></td>
		</tr>
        <tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Pan Card </td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="pancard" id="pancard"/></td>
		</tr>
        <tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>saving A/c No </td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="savingacno" id="savingacno"/></td>
		</tr>

</table>  
</table>
<table  style=" width:100%;">
<tr>
<td colspan="8" style="text-align:center;"></br></br>
<input type="submit" name="insert" id="insert" value="submit" class="btn btn-primary" style="color:#FFF; width:100px;background-color:#337ab7;">
<!--<input type="button" class="btn btn-primary" name="delete" id="delete" value="delete" style="margin-right:2%">
<input type="button" class="btn btn-primary" name="report" id="report" value="Report" style="margin-right:2%">
<input type="button" class="btn btn-primary" name="addagent" id="addagent" value="Machine Add Agent" style="margin-right:2%; width: 150px">
<input type="button" class="btn btn-primary" name="commwiserecord" id="commwiserecord" value="Commissions Rate Date Wise Feed" style="margin-right:2%;width: 250px">-->
</td>
</tr>

</table>
</form>
</table>
</center>
</div>
<?php
	if(isset($_REQUEST['id']) && $_REQUEST['id'] <> '')
	{
		?>
			<script>
				getAgents('edit-' + <?php echo $_REQUEST['id'];?>);				
			</script>
		<?php
	}
	
	if(isset($_REQUEST['deleteid']) && $_REQUEST['deleteid'] <> '')
	{
		?>
			<script>				
				getAgents('delete-' + <?php echo $_REQUEST['deleteid'];?>);	
			</script>
		<?php
	}
	
?>
<?php include_once "includes/foot.php"; ?>
