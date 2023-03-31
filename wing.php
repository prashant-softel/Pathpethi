<?php //include_once "ses_set_as.php"; ?>
<?php

/*if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
else
{
	include_once("includes/head_s.php");
}*/
include_once("includes/head_s.php");
//include_once("includes/menu.php");
if(isset($_SESSION['admin']))
{
	if($_REQUEST['ssid']<>"")
	{	
		if(isset($_GET['ssid'])){if($_GET['ssid']<>$_SESSION['society_id']){echo 'logout';?><script>//window.location.href = "logout.php";</script><?php }}
	}
	else if($_REQUEST['society_id']<>"")
	{
		if(isset($_GET['society_id'])){if($_GET['society_id']<>$_SESSION['society_id']){echo 'logout';?><script>//window.location.href = "logout.php";</script><?php }}
	}
}

include_once("classes/wing.class.php");
$obj_wing = new wing($m_dbConn);

$_SESSION['ssid'] = $_REQUEST['ssid'];
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/jswing.js"></script>

	<script language="javascript" type="application/javascript">
    function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }
    function go1()
    {
        document.getElementById("wing1").focus();
    }
	
    </script>

</head>

<?php if(isset($_REQUEST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();go1();">
<?php }else{ ?>
<body onLoad="go1();">
<?php } ?>
<br>
<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Wing Under Society</div>


<!--<center>
<a href="ledger_import.php?imp" style="color:#00F; text-decoration:none;"><b><u>Import Ledgers</u></b></a><br/>
<a href="unit_import.php?imp" style="color:#00F; text-decoration:none;"><b><u>Import Unit</u></b></a>

</center>-->

<?php if(isset($_SESSION['admin'])){?>
<?php if(!isset($_REQUEST['s'])){ $val ='';?>
<br>
<center>
<a href="society_view.php?imp" style="color:#00F; text-decoration:none;"><b>Add Wing</b></a>
</center>
<?php }else{ $val = 'onSubmit="return val();"';
?>
<br>
<center>
<a href="society_view.php?imp" style="color:#00F; text-decoration:none;"><b>Back</b></a>
</center>

<?php } ?>
<?php }?>

<center>
<form name="wing" id="wing" method="post" action="process/wing.process.php" <?php echo $val;?>>

<input type="hidden" name="sid" value="<?php echo $_REQUEST['sid'];?>">
<input type="hidden" name="ssid" value="<?php echo $_REQUEST['ssid'];?>">
	
	<?php
		$star = "<font color='#FF0000'>*</font>";
		if(isset($_REQUEST['msg']))
		{
			$msg = "Sorry !!! You can't delete it. ( Dependency )";
		}
		else if(isset($_REQUEST['msg1']))
		{
			$msg = "Record Deleted Successfully.";
		}
		else
		{
			//$msg = '';	
		}
	?>
    <table align='center'>
		<?php
		if(isset($msg))
		{
			if(isset($_REQUEST["ShowData"]))
			{
		?>
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_REQUEST["ShowData"]; ?></b></font></td></tr>
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
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_REQUEST["ShowData"]; ?></b></font></td></tr>
        <?php
		}
		?>
        
		<tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
<!--			<td>Society</td>
            <td>&nbsp; : &nbsp;</td>-->
			<td>
                <?php if(isset($_SESSION['admin'])){?>                
                <input type="hidden" name="society_id" id="society_id" value="<?php echo $_SESSION['society_id'];?>"><?php //echo $_SESSION['society_name'];?>
                <?php }else{ 
				if($_REQUEST['society_id']<>""){$society_id = $_REQUEST['society_id'];}else if($_REQUEST['sid']<>""){$society_id = $_REQUEST['sid'];}
				?>
                <select name="society_id" id="society_id" style="width:180px;display:none">
				<?php if(isset($_SESSION['sadmin'])){?>	
				<option><?php echo $_SESSION['society_id']; ?></option>
                <?php }else{?>
                <?php echo $combo_society = $obj_wing->combobox("select society_id,concat_ws(' - ',society_name,landmark) from society where status='Y' and society_id='".$_SESSION['sid']."'",$society_id, false); ?>
                <?php }?>    
				</select>
                
                <?php }?>
            </td>
		</tr>
        	
		<tr align="left">
        	<td valign="middle"><?php if(isset($_REQUEST['s'])){echo $star;}?></td>
			<td>Enter Wing</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="wing" id="wing1" /></td>
		</tr>
        
        <tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td colspan="4" align="center">
            <?php if(isset($_REQUEST['s'])){?>
            
            <input type="hidden" name="id" id="id">
            <input type="submit" name="insert" id="insert" value="Insert">
            
			<?php }else{ ?>
            
            <input type="submit" name="insert" id="insert" value="Search">

            <?php } ?>
            </td>
		</tr>
</table>
</form>


<table align="center">
<tr>
<td align="center">
<?php
echo "<br>";
$str1 = $obj_wing->pgnation();
//echo "<br>";
//echo $str = $obj_wing->display1($str1);
//echo "<br>";
//$str1 = $obj_wing->pgnation();
//echo "<br>";
?>
</td>
</tr>
</table>

</center>
</div>
<?php include_once "includes/foot.php"; ?>
