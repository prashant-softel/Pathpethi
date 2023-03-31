<?php 
include_once "ses_set_as.php"; 
include_once("classes/dbconst.class.php");
?>

<?php
include_once "classes/income_details.class.php";
$obj_income_details = new income_details($m_dbConn);
$ledger_name=$obj_income_details->ledgername($_REQUEST['id']);
$show_income_details=$obj_income_details->show_income_details($_REQUEST['id']);
// echo $_POST['selectdate'];
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
        <!--<script type="text/javascript" language="javascript" src="js/tablefilter.js"></script> --> 
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

<center><font color="#43729F" size="+1"><b><?php echo $ledger_name;?></b></font></center>
<br>

<center><font color="#43729F" size="-1"><b>Income Details</b></font></center>
<br>


<center>
<form action="" method="post">

<input type="hidden" name="id" value="<?php echo $_REQUEST['id'];?>">
<label>Filter By Date:</label>
<select name="selectdate">
            
  <?php  echo $combo_group= $obj_income_details->combobox("select DISTINCT Date as Date1,Date from `incomeregister` where LedgerID=".$_REQUEST['id']."",$_REQUEST['selectdate']); ?>
</select>
<input type="submit" name="search" id="search"  value="Search" onClick="<?php $obj_income_details->show_income_details($_REQUEST['id'])?>"/>
<?php //echo $combo_group;?>
</form>
<table border="0" align="center">
        
        
        
        <tr height="30" bgcolor="#2A9FFF">
            <th width="150" id="th_0">Date</th>
            <th width="150" id="th_1">From</th>
            <th width="150">Credit</th>
           
            
         
         
         <?php
		 //print_r($show_income_details);
		if($show_income_details <>"")
		{
			foreach($show_income_details as $k => $v)
			{
				$show_particular=$obj_income_details->show_particulars($_REQUEST['id'],$show_income_details[$k]['VoucherID']);
				
				
				?>
				 
                <tr height="25" bgcolor="#BDD8F4">
                    <td align="center"><?php echo getDisplayFormatDate($show_income_details[$k]['Date']);?></td>
                    <td align="center"><?php echo $show_particular;?></td>
                    <td align="center"><?php echo $show_income_details[$k]['Credit'];;?></td>
                    
                  
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

</body>
		
</html>