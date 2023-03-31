<?php
include_once("includes/head_s.php");
include_once ("check_default.php");
include_once("classes/unit_sorting.class.php");
$obj_unit = new unit_sorting($m_dbConn, $m_dbConnRoot);
$_SESSION['ssid'] = $_REQUEST['ssid'];
$_SESSION['wwid'] = $_REQUEST['wwid'];
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
    <script type="text/javascript" src="js/unit_sort.js"></script>
	<script type="text/javascript" src="js/populateData.js"></script>
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
    
    
    </head>

<?php if(isset($_REQUEST['ShowData']) || isset($_REQUEST['msg']) || isset($_REQUEST['msg1'])){ ?>
<body onLoad="go_error();">
<?php }else{ ?>
<body>
<?php } ?>

<br>
<div id="middle">
<div class="panel panel-default">
<div class="panel-heading" id="pageheader">Sort Units/Update Wing</div>
<br/>
<center>
<form name="unit_sort" id="unit" method="post" action="">
<input type="hidden" name="ssid" value="<?php echo $_GET['ssid'];?>">
<input type="hidden" name="wwid" value="<?php echo $_GET['wwid'];?>">
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
        
    
</table>
</form>

<?php if(isset($_SESSION['admin'])){?>
<center>
<a href="unit_print.php?ssid=<?php echo $_GET['ssid'];?>&wwid=<?php echo $_GET['wwid'];?>&society_id=<?php if($_GET['society_id']<>""){echo $_GET['society_id'];}else{echo $_SESSION['society_id'];}?>&wing_id=<?php echo $_GET['wing_id'];?>&unit_no=<?php echo $_GET['unit_no'];?>&insert=<?php if($_GET['insert']<>""){echo $_GET['insert'];}else{echo 'Search';}?>&ShowData=&imp=" target="_blank"><img src="images/print.png" width="40" width="40" /></a>
</center>
<?php } ?>

<table align="center">
<center>
<div id="set_common" style="border:1px solid #666; padding:5px; display:block; width:500px;border-radius: 15px;">
<div>
<center><b><font  style="color:blue; font-size:14px;">Select wing to apply to selected units</font></b></center><br/><br/>
<center>
Wing: <select id="header_combo"><?php echo $combo_wing = $obj_unit->combobox("select wing_id,wing from wing where status='Y' and society_id='".$_SESSION['society_id']."'",$wing_id); ?></select>
<input type="button" value="Apply" onClick="ApplyWing();"  class="btn btn-primary"/>
</center>
</div>

</div>
</center>
<!--<div style="text-align:left;font-weight:bold;color:blue;padding-left: 85px;">&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="all_unit" onclick="SelectAllUnit(this);">&nbsp;&nbsp;Select All Units</div>-->

<tr>
<td align="center">


<?php 
echo "<br>";
$str1 = $obj_unit->pgnation();
echo "<br>";
echo $str = $obj_unit->display1($str1);
echo "<br>";
$str1 = $obj_unit->pgnation();
echo "<br>";
?>
</td>
</tr>
<tr align="center"><td><input type="submit"  class="btn btn-primary" value="Submit" id="submit" name="submit" onClick="submitTableData();" ></td></tr>
<tr><td><br></td></tr>
</table>

</center>
</div>
</div>
<?php include_once "includes/foot.php"; ?>
