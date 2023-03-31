<?php include_once "ses_set_as.php";
 ?>

<?php
include_once "classes/report.class.php";
$obj_view_reports = new report($m_dbConn);
//echo $_GET["sid"];
$show_society_name=$obj_view_reports->show_society_name($_REQUEST["sid"]);
//echo $show_society_name;

$show_mem_due_details=$obj_view_reports->show_mem_due_details();
//print_r($show_mem_due_details);
?>
<?php

 if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
else
{
	include_once("includes/head_s.php");
}

if(isset($_GET['ssid'])){if($_GET['ssid']<>$_SESSION['society_id']){?><script>window.location.href = "logout.php";</script><?php }}

?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <script type="text/javascript" src="js/ajax.js"></script>
	<script language="javascript" type="application/javascript">
	function go_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
		});
        setTimeout('hide_error()',8000);	
    }
    function hide_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});
    }
	</script>
</head>
<body>
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

<center><font color="#43729F" size="+1"><b><?php echo $show_society_name;?></b></font></center>
<br>

<center><font color="#43729F" size="-1"><b>Member's Due Report</b></font></center>
<br>


<center>
<table border="0" align="center">
        <tr height="30" bgcolor="#2A9FFF">
            <th width="150">Wing Number (id)</th>
            <th width="150">Unit number (id)</th>
            <th width="350">Member Name (id)</th>
            <th width="150">Amount due</th>
         </tr>  
         
         
         <?php
		if($show_mem_due_details<>"")
		{
			foreach($show_mem_due_details as $k => $v)
			{
				?>
				 
                <tr height="25" bgcolor="#BDD8F4">
                    <td align="center"><?php echo $show_mem_due_details[$k]['wing'];  echo " (" . $show_mem_due_details[$k]['wing_id'] .")";?></td>
                    <td align="center"><?php echo $show_mem_due_details[$k]['unit'];  echo " (" . $show_mem_due_details[$k]['unit_id'] .")";?></td>
                    <td align="center"><?php echo $show_mem_due_details[$k]['member'];  echo " (" . $show_mem_due_details[$k]['member_id'] .")";?></td>
                    <td align="center">
                    
                    <a href="member_ledger_report.php?&uid=<?php echo $show_mem_due_details[$k]['unit_id'];?>" style="color:#0000FF;">
                    <?php echo $show_mem_due_details[$k]['amount'];?></a></td>
                </tr>
				
				<?php
			}
		}
		else
		{
			?>
            <tr height="25"><td colspan="6" align="center"><font color="#FF0000"><b>Records Not Found...<!--  by admin --></b></font></td></tr>
            <?php	
		}
		?>
</table>		
</center>
<?php include_once "includes/foot.php"; ?>