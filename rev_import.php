<?php 

include_once "ses_set_as.php";
 ?>


<?php
 // Turn off all error reporting
        error_reporting(0);
if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
{
	include_once("includes/head_s.php");
}

include_once("classes/rev_import.class.php");
$obj_rev_import = new rev_import($m_dbConn);

?>
<head>
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
<html>
<?php if(isset($_POST['ShowData'])){ ?>
<body onLoad="go_error();">
<?php } ?>

<center>
<h1><font color="#2A9FFF" size="+1"><b>Reverse Import</b></font></h1>
</center>
<div align="center">
<form name="reverse_import" method="post" action="process/rev_import.process.php" >
<table align='center'>

<?php 
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
			}?>

		<tr align="left">
			<td valign="middle"><?php //echo $star;?></td>
            <td>Society : &nbsp;</td>
			<td><select name="society_name" id="society_name">
            	<?php
					echo $combo_society = $obj_rev_import->combobox("select society_id,concat_ws(' - ',society_name,landmark) from society where status='Y'",$_SESSION['society_id']); 
				?>		
            </select>
            </td>
		</tr>
        <tr>&nbsp;&nbsp;&nbsp;&nbsp;</tr>
        
         <tr align="left">
        	<td>Wing  </td>
            <td><input type="checkbox" name="wing" id="wing" value="1"/></td>
		</tr>
        
        <tr align="left">
        	<td>Unit  </td>
            <td><input type="checkbox" name="unit" id="unit" value="1" /></td>
		</tr>
        
        <tr align="left">
        	<td>Member  </td>
            <td><input type="checkbox" name="member" id="member" value="1" /></td>
		</tr>
        
        <tr align="left">
        	<td>Ledger  </td>
            <td><input type="checkbox" name="ledger" id="ledger" value="1" /></td>
		</tr>
        
        <tr align="left">
        	<td>Tarrif  </td>
            <td><input type="checkbox" name="tarrif" id="tarrif" value="1" /></td>
		</tr>
         <tr>
			<td colspan="4" align="center">
        <input type="submit" name="insert" id="insert" value="Submit">
        </td>
        </tr>
    </table>
    </form>
    </div>
</body>
</html>
