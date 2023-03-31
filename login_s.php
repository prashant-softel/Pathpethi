<?php
include_once "classes/login_s.class.php";
$main_obj = new login_s();

if(isset($_REQUEST['login_s']))
{
	$chk_log_s = $main_obj->chk_log_s();
}
else
{
	//$chk_log = '';	
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Society Software</title>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />

<link  href="css/admin.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/chromestyle.css" />
<script type="text/javascript" src="js/chrome.js"></script>
<style type="text/css">
.style1 
{
	font-weight: bold;
}
</style>

<script language="javascript">
function val()
{
	var sqr = trim(document.getElementById("sqr").value);
	var user = trim(document.getElementById("user").value);
	var pass = trim(document.getElementById("pass").value);	
	
	if(sqr=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter security no.";
		document.getElementById("sqr").focus();
		
		go_error()
		return false;
	}
	if(user=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter username";
		document.getElementById("user").focus();
		
		go_error();
		return false;
	}
	if(pass=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter password";
		document.getElementById("pass").focus();
		
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

function go1()
{
	document.getElementById("sqr").value = '';
	document.getElementById("user").value = '';
	document.getElementById("pass").value = '';
	document.getElementById("sqr").focus();
}
</script>
<script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
<script language="javascript" type="application/javascript">
function go_error()
{
	setTimeout('hide_error()',4000);	
}
function hide_error()
{
	document.getElementById('error').style.display = 'none';	
}
</script>
</head>

<body onLoad="go1();go_error();">

<div id="main">
<?php
if(isset($_REQUEST['alog'])){$cls1 = 'active';}else{$cls1 = '';}
if(isset($_REQUEST['mlog'])){$cls2 = 'active';}else{$cls2 = '';}
if(isset($_REQUEST['slog'])){$cls3 = 'active';}else{$cls3 = '';}
?>
<div id="header">


<div style="margin-left:390px; margin-top:7px; width:650px; height:50px; padding-top:20px;">
<table width="100%"><tr>
<td>
	<img src="images/index_02.gif" /></td><td><font color="#FF8000" size="+2"><b>Society Software</b></font>
    </td>
    </tr>
    </table>
</div>

<ul id="chromemenu" class="top-navigation-1">
	<li><a href="login_m.php?mlog" class="<?php echo $cls2;?>">Member Login</a></li>
    <li><a href="login.php?alog" class="<?php echo $cls1;?>">Admin Login</a></li>
    <li><a href="login_s.php?slog" class="<?php echo $cls3;?>">Super Admin Login</a></li>
</ul>

</div>

<div id="middle1" align="center">
<center><font color="#43729F" size="+1"><b>Super Admin Login Panel</b></font></center>

<form method="post" onSubmit="return val();">
<table align="center" border="0">

	<?php
    $star = "<font color='#FF0000'>*</font>";
	?>
    
	<?php		
    if(isset($chk_log_s))
    {
    ?>
        <tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $chk_log_s; ?></b></font></td></tr>
    <?php
    }
    else
    {
    ?>
        <tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"></b></font></td></tr>	   
    <?php		
    }
    ?>

<!--
<tr>
	<td>Type</td>
    <td>:</td>
    <td><select name="type"><option value="1">Admin</option><option value="2">User</option></select></td>
</tr>
-->

<tr>
	<td valign="middle"><?php echo $star;?></td>
	<td align="left">Security No. Pls</td>
    <td>:</td>
    <td><input type="password" name="sqr" id="sqr" size="20"/></td>
</tr>

<tr>
	<td valign="middle"><?php echo $star;?></td>
	<td align="left">Username</td>
    <td>:</td>
    <td><input type="text" name="user" id="user" size="20" value=""/></td>
</tr>

<tr>
	<td valign="middle"><?php echo $star;?></td>
	<td align="left">Password</td>
    <td>:</td>
    <td><input type="password" name="pass" id="pass" size="20" value=""/></td>
</tr>

<tr><td colspan="4">&nbsp;</td></tr>
    
<tr>
    <td colspan="4" align="center">
    <input type="submit" name="login_s" id="login" value="Login">
    </td>
</tr>
</table>

</form>

<br><br><br><br><br><br><br><br><br>

</div>
</div>



</body></html>