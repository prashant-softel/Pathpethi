<?php include_once("includes/head_s.php");

include_once("classes/unit.class.php");
$obj_unit = new unit($m_dbConn);
include_once("classes/bill_period.class.php");
$obj_bill_period=new bill_period($m_dbConn);
$show_wings=$obj_unit->getallwing();
//print_r($show_wings);
//echo $_REQUEST['ssid'];
//echo $_REQUEST['wwid'];
$_SESSION['ssid'] = $_REQUEST['ssid'];
$_SESSION['wwid'] = $_REQUEST['wwid'];
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <link href="css/messagebox.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/ajax.js"></script>
    <script type="text/javascript" src="js/ajax_new.js"></script> 
	<script type="text/javascript" src="js/jsunit.js"></script>
     <script type="text/javascript" src="js/status.js"></script> 
    <script type="text/javascript" src="js/validate.js"></script>
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
	
function get_wing(society_id)
{
	document.getElementById('error').style.display = '';	
	document.getElementById('error').innerHTML = 'Wait... Fetching wing under this society';	
	remoteCall("ajax/get_wing.php","society_id="+society_id,"res_get_wing");		
}

function res_get_wing()
{
	var res = sResponse;//alert(res)
	
	document.getElementById('error').style.display = 'none';	
	
	var count = res.split('****');
	var pp = count[0].split('###');
	
	document.getElementById('wing_id').options.length = 0;
	var that = document.getElementById('society_id').value;

	for(var i=0;i<count[1];i++) 
	{		
		var kk = pp[i].split('#');
		var wing_id = kk[0];
		var wing = kk[1];
		document.getElementById('wing_id').options[i] = new Option(wing,wing_id);
	}
	document.getElementById('wing_id').options[i] = new Option('All','');
	document.getElementById('wing_id').value = '';
}
	
	function clear_unit(wing)
	{
		if(wing=='')
		{
			document.getElementById('unit_no').value = '';	
		}
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
<div class="panel panel-info" id="panel" style="display:none">
<div class="panel-heading" id="pageheader">List of Units</div>
<!--<center><h3><?php //echo $_SESSION['society_name'];?></h3></center>
<center><h6><font color="#43729F"><b>Unit under wing</b></font></h6></center>-->
<center><br>
<!--<a href="unit.php?imp&ssid=<?php echo $_SESSION['society_id'];?>&idd=<?php echo time();?>"><input type="button" value="Add Unit"></a>-->
<button type="button" class="btn btn-primary" onclick="window.location.href='unit.php?imp&ssid=<?php echo $_SESSION['society_id'];?>&idd=<?php echo time();?>'">Add Unit</button>
</center>
<!--<center>
<a href="member_import.php?imp" style="color:#00F; text-decoration:none;"><b><u>Import Members</u></b></a>
</center>-->

<?php if(!isset($_REQUEST['ws'])){ $val ='';?>
<!--
<br>
<center>
<a href="society_view.php?imp" style="color:#00F; text-decoration:none;"><b>Add Unit</b></a>
</center>
-->
<?php }else{ $val =''; //'onSubmit="return val();"';
?>
<br>
<center>
<a href="wing.php?imp&ssid=<?php echo $_REQUEST['ssid'];?>&s&idd=<?php echo time();?>" style="color:#00F; text-decoration:none;"><b>Back</b></a>
</center>
<?php } ?>

<center>
<form name="unit" id="unit" method="post" action="process/unit.process.php" <?php echo $val;?>>
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
<br>

<?php /* if(isset($_SESSION['admin'])){?>
<center>
<a href="unit_print.php?ssid=<?php echo $_GET['ssid'];?>&wwid=<?php echo $_GET['wwid'];?>&society_id=<?php if($_GET['society_id']<>""){echo $_GET['society_id'];}else{echo $_SESSION['society_id'];}?>&wing_id=<?php echo $_GET['wing_id'];?>&unit_no=<?php echo $_GET['unit_no'];?>&insert=<?php if($_GET['insert']<>""){echo $_GET['insert'];}else{echo 'Search';}?>&ShowData=&imp=" target="_blank"><img src="images/print.png" width="40" width="40" ></a>
</center>
<?php } */ ?>

<table align="center">
<tr>
<div class="">
<ul class="nav nav-tabs">

<?php
 $link_tab = "unit_search.php?wing_id=" . $show_wings[$key]['wing_id'];
if($_REQUEST['wing_id'] == "")
{ ?>
	<li class="active"><a href="#" onClick="window.location.href='<?php echo $link_tab; ?>'" data-toggle="tab" id="wing_link">All Wings</a></li>
<?php }
else { 
?>	
	<li><a href="#"  onClick="window.location.href='<?php echo $link_tab; ?>'" data-toggle="tab" id="wing_link"><?php echo 'Wing '.$show_wings[$key]['wing']; ?></a></li>
    <?php } ?>
<?php 
if($show_wings <> '')
{
	foreach($show_wings as $key=>$val)
	{ 
	$test_link = "unit_search.php?wing_id=" . $show_wings[$key]['wing_id'];
	?>
    <?php
	if($_REQUEST['wing_id'] == $show_wings[$key]['wing_id'])
    {
	 ?>
	<li  class="active"><a href="#"  onClick="window.location.href='<?php echo $test_link; ?>'" data-toggle="tab" id="wing_link"><?php echo 'Wing '.$show_wings[$key]['wing']; ?></a></li>
    <?php }
    else
    { ?>
    <li><a href="#"  onClick="window.location.href='<?php echo $test_link; ?>'" data-toggle="tab" id="wing_link"><?php echo 'Wing '.$show_wings[$key]['wing']; ?></a></li>
    <?php } ?>

	<?php }
}?>
</ul>
</div>
</tr>
<tr>
<td align="center">


<?php 
echo "<br>";
$str1 = $obj_unit->pgnation(false,IsReadonlyPage());
echo "<br>";
//echo $str = $obj_unit->display1($str1);
//echo "<br>";
//$str1 = $obj_unit->pgnation();
echo "<br>";
?>
</td>
</tr>
</table>
</center>
</div>
</div>

<?php include_once "includes/foot.php"; ?>
<div id="openDialogOk" class="modalDialog" >
	<div>
		<div id="message_ok">
		</div>
	</div>
</div>
