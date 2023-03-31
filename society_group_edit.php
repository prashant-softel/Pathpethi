<?php include_once "ses_set.php"; ?>
<?php 
if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
?>

<?php
include_once("classes/society_group.class.php");
$obj_society_group = new society_group($m_dbConn);

if(isset($_REQUEST['id']))
{
	if($_REQUEST['id']<>"")
	{
		$edit = $obj_society_group->grp_edit($_REQUEST['id']);
	}
}

?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jssociety_group.js"></script>
    <script language="javascript" type="application/javascript">
    function go_error()
    {
        setTimeout('hide_error()',6000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }
    function go1()
    {
        document.getElementById("grp_name").focus();
    }
	
	function check_grp_name(grp_name)
    {
        var grp_name_old = document.getElementById("grp_name_old").value;
		var n_grp_name = trim(grp_name);
		
        if(n_grp_name!="")
		{
			if(n_grp_name!=grp_name_old)
			{
				document.getElementById('error').style.display = '';		
				document.getElementById("error").innerHTML = "Checking group name is already exist or not...";	
				
            	remoteCall("ajax/check_grp_name.php","grp_name="+grp_name+"&grp_name_old="+grp_name_old,"res_check_grp_name");
			}
        }
		
		//////////////////////////////////////////////////////////////////////////////////////////
		function LTrim( value )
		{
			var re = /\s*((\S+\s*)*)/;
			return value.replace(re, "$1");
		}
		function RTrim( value )
		{
			var re = /((\s*\S+)*)\s*/;
			return value.replace(re, "$1");
		}
		function trim( value )
		{
			return LTrim(RTrim(value));
		}	
		//////////////////////////////////////////////////////////////////////////////////////////
    }
    function res_check_grp_name()
    {
        var res1 = sResponse;	
        var res = res1.split('###');alert(res[0]);
		
		if(res[0]==1)
		{
			document.getElementById('error').style.display = '';	
			document.getElementById("error").innerHTML = res[1]+" is already exist.<br>";	
			document.getElementById("grp_name").value = res[2];
			
			go_error();
		}
		else
		{
			document.getElementById('error').style.display = '';	
			document.getElementById("error").innerHTML = '<font color=green>Available</font>';
			
			go_error();
		}
    }
    </script>
</head>

<?php if(isset($_REQUEST['ShowData']) || isset($_REQUEST['up']) || isset($_REQUEST['del'])){ ?>
<body onLoad="go_error();go1();">
<?php }else{ ?>
<body onLoad="go1();">
<?php } ?>


<center><font color="#43729F" size="+1"><b>Update group of society</b></font></center>

<?php if(!isset($_SESSION['sadmin'])){?>
<br>
<center>
<a href="list_society_group.php?grp" style="color:#00F; text-decoration:none;"><b>Back to list</b></a>
</center>
<br>
<?php } ?>

<center>
<form name="society_group" id="society_group" method="post" action="process/society_group.process.php" onSubmit="return val();">
<table align='center'>
	<?php
		$star = "<font color='#FF0000'>*</font>";
	?>
    <table align='center' border="0">
		
        <tr height="25">
        	<td colspan="4" align="center" valign="top"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST['ShowData'];?></b></font></td>
        </tr>
        
		<tr>
        	<td valign="middle"><?php echo $star;?></td>
			<td>Group Name</td>
            <td>:</td>
			<td>
            	<input type="text" name="grp_name" id="grp_name" size="55" value="<?php echo strtolower($edit[0]['grp_name']);?>" onBlur="check_grp_name(this.value);"/>
                <input type="hidden" name="grp_name_old" id="grp_name_old" size="55" value="<?php echo strtolower($edit[0]['grp_name']);?>"/>
                </td>
		</tr>
        
		<tr>
        	<td valign="top"><?php echo $star;?></td>
			<td valign="top">Select Society</td>
            <td valign="top">:</td>
			<td>
                <div style="overflow-y:scroll;overflow-x:hidden;width:350px; height:150px; border:solid #CCCCCC 2px;">
					<?php echo $combo_society_id = $obj_society_group->combobox111("select society_id,society_name from society where status='Y'","society_id[]","society_id","select society_id from society_group where grp_name='".$edit[0]['grp_name']."' and status='Y'"); ?>
                </div>
            </td>
		</tr>
		
        <tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td colspan="4" align="center">
            <input type="hidden" name="id" id="id">
            <input type="submit" name="insert" id="insert" value="Update">
            </td>
		</tr>
</table>
</form>


</center>
<?php include_once "includes/foot.php"; ?>
