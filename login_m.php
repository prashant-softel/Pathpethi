<?php 
include_once "classes/include/dbop.class.php";
$m_dbConn = new dbop();
include_once "classes/login_m.class.php";
$main_obj = new login_m($m_dbConn);
include_once "includes/head_test.php";
include_once("header.php");
if(isset($_REQUEST['login_m']))
{
	$chk_log_m = $main_obj->chk_log_m($m_dbConn);
}

if($_SERVER['HTTP_HOST']<>'localhost')
{
	//include_once "fb/fbmain.php";
}
?>
<style>
input,select
{
	color:#333;
	background-color:transparent;
	
	
	
	border-radius: 5px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	-moz-box-shadow: 2px 2px 7px #666;
	-webkit-box-shadow: 2px 2px 7px #666;
	box-shadow: 1px 1px 4px #666;
	padding:1px 0 0 0;
}
#insert
{
	color:#333;
	background-color:#FFF;
	
	
	
	border-radius: 5px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	-moz-box-shadow: 2px 2px 7px #666;
	-webkit-box-shadow: 2px 2px 7px #666;
	box-shadow: 1px 1px 4px #666;
	padding:1px 0 0 0;
}
</style>

<script language="javascript">
function val()
{
	var user = trim(document.getElementById("user").value);
	var pass = trim(document.getElementById("pass").value);	
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
		
	if(user=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter email id";
		document.getElementById("user").focus();
		
		go_error();
		return false;
	}
	else
	{
		if(reg.test(user)==false) 
		{
			document.getElementById('error').style.display = '';	
			document.getElementById("error").innerHTML = "Invalid email id";
			document.getElementById("user").focus();
			
			go_error();
			return false;	
		}	
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
	document.getElementById("user").value = '';
	document.getElementById("pass").value = '';
	document.getElementById("user").focus();
}

function error()
{
	alert("Oopss !!! You are in local machine");	
}
</script>
<script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
<script language="javascript" type="application/javascript">
function go_error()
{
	setTimeout('hide_error()',8000);	
}
function hide_error()
{
	document.getElementById('error').style.display = 'none';	
}
</script>
<title>Society Software</title>
<br>
<br>

<body onLoad="go1();go_error();">
<font color="#000" style="font-size:16px" ><b>MEMBER LOGIN PANEL</b></font>

<form method="post" onSubmit="return val();">
<table align="center" border="0">
	<?php
    $star = "<font color='#FF0000'>*</font>";
	?>
    
	<?php		
    if(isset($chk_log_m))
    {
    ?>
        <tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $chk_log_m; ?></b></font></td></tr>
    <?php
    }
    else
    {
    ?>
        <tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"></b></font></td></tr>	   
    <?php		
    }
    ?>

<tr>
	<td valign="middle"><?php echo $star;?></td>
	<td align="left"><font color="#000" style="font-size:16px">Email id</font></td>
    <td><font color="#000" style="font-size:16px">&nbsp;:&nbsp;</font></td>
    <td><input type="text" name="user" id="user" size="20"/ ></td>
    <td></td>
</tr>

<tr>
	<td valign="middle"><?php echo $star;?></td>
	<td align="left"><font color="#000" style="font-size:16px">Password</font></td>
    <td><font color="#000" style="font-size:16px">&nbsp;:&nbsp;</font></td>
    <td><input type="password" name="pass" id="pass" size="20"/ ></td>
</tr>

<tr><td colspan="3">&nbsp;</td><td><font color="#FF0000" style="font-size:14px;"><u>Note</u> : Password is case sensitive</font></td></tr>

<tr><td colspan="4">&nbsp;</td></tr>
    
<tr>
    <td colspan="4" align="center">
    <input type="submit" name="login_m" id="insert" value="Login">
    </td>
</tr>
</table>
</form>
<!--
<br>
OR
<br><br>-->

<?php 
if($_SERVER['HTTP_HOST']=='localhost')
{
?>
	<!--<a href="javascript:void(0);" onClick="error();"><img src="fb/fb_connect.jpg" alt="" /></a>-->
<?php 
}
else
{
?>
	<!--<a href="<?php //echo $loginUrl?>"><img src="fb/fb_connect.jpg" alt="" /></a>-->
<?php 
}
?>

<?php

if(isset($userInfo))
{
	set_sess($userInfo);
}

?>
<p class="footer">
<?php include_once "includes/foot.php";
//include_once("footer.php"); ?>														
</body>
</html>