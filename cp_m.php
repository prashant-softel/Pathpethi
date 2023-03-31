<?php include_once "ses_set_m.php"; ?>
<?php
include_once("includes/header_m.php");

include_once("classes/cp_m.class.php");
$obj_cp = new cp_m($m_dbConn);
?>

<?php
if(isset($_POST['cp']))
{
	$cp = $obj_cp->cp_m($m_dbConn);
}
?>
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
        document.getElementById("np").value = '';
        document.getElementById("cnp").value = '';
        document.getElementById("np").focus();
    }	
    </script>
    
    <script language="javascript">
	function val()
	{
		var np = trim(document.getElementById("np").value);
		var cnp = trim(document.getElementById("cnp").value);	
		
		if(np=="")
		{
			document.getElementById('error').style.display = '';	
			document.getElementById("error").innerHTML = "Please enter new password";
			document.getElementById("np").focus();
			
			go_error();
			return false;
		}
		if(cnp=="")
		{
			document.getElementById('error').style.display = '';	
			document.getElementById("error").innerHTML = "Please enter confirm new password";
			document.getElementById("cnp").focus();
			
			go_error();
			return false;
		}
		if(np!=cnp)
		{
			document.getElementById('error').style.display = '';	
			document.getElementById("error").innerHTML = "Do not match the password";
			
			document.getElementById("np").value = "";
			document.getElementById("cnp").value = "";
			document.getElementById("np").focus();
			
			go_error();
			return false;
		}
		
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
	}
    </script>

<?php if(isset($cp)){?>
<body onLoad="go_error();go1();">
<?php }else{ ?>
<body onLoad="go1();">
<?php } ?>

<center><font color="#43729F" size="+1"><b>Change Password</b></font></center>
<center>
<form method="post" onSubmit="return val();">
<table align="center" border="0">	
	<?php
    $star = "<font color='#FF0000'>*&nbsp;</font>";
	?>
    
	<?php		
    if(isset($cp))
    {
    ?>
        <tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $cp; ?></b></font></td></tr>
    <?php
    }
    else
    {
    ?>
        <tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"></b></font></td></tr>	   
    <?php		
    }
    ?>
        
    <tr align="left">
    	<td valign="middle"><?php echo $star;?></td>
        <td>New password</td>
        <td>&nbsp;:&nbsp;</td>
        <td><input type="password" name="np" id="np" /></td>
    </tr>
    
    <tr align="left">
    	<td valign="middle"><?php echo $star;?></td>
        <td>Confirm new password</td>
        <td>&nbsp;:&nbsp;</td>
        <td><input type="password" name="cnp" id="cnp" /></td>
    </tr>
    
    <tr><td colspan="4">&nbsp;</td></tr>
    
    <tr>
        <td colspan="4" align="center">
        <input type="submit" name="cp" id="insert" value="Update">
        </td>
    </tr>
    
    </table>
    </form>

<center>
<?php include_once "includes/foot.php"; ?>
