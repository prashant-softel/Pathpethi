<?php

include_once "fb/fbmain-test.php";

if(isset($_GET['code']) && $_GET['code'] <> '')
{
	$fb_details = set_sess($userInfo);	
	
	//echo "<b>Please Wait...</b>";
	print_r($fb_details);
	//$fb_details = array('name'=>'Ankur Patil', 'email'=>'ankur.2088@yahoo.co.in', 'fbid'=>'1234567890');
	if($fb_details['fbid']<>"")
	{
		//$chk_log = $main_obj->chk_log_fb($fb_details['email'], $fb_details['fbid']);
	}
	else
	{
		?>
		<script>//window.location.href = 'logout.php';</script>
		<?php
	}
}

?>
<!--<link href="csss/style.css" rel="stylesheet" type="text/css" />-->
<style>
#insert,input,select
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
	var sqr = trim(document.getElementById("sqr").value);
	var user = trim(document.getElementById("user").value);
	var pass = trim(document.getElementById("pass").value);	
	
	/*if(sqr=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter security no.";
		document.getElementById("sqr").focus();
		
		go_error()
		return false;
	}*/
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

<body onLoad="go1();go_error();">
<br>
<br>
<font color="#000" style="font-size:16px"><b>LOGIN PANEL</b></font><br><br>
<?php 
if($_SERVER['HTTP_HOST'] == 'localhost')
{
?>
	<!--<a href="javascript:void(0);" onClick="error();"><img src="fb/fb_connect.jpg" alt="" /></a>-->
    <a href="<?php echo $loginUrl?>"><img src="fb/fb_connect.jpg" alt="" /></a>
<?php 
}
else
{
?>
	<a href="<?php echo $loginUrl?>"><img src="fb/fb_connect.jpg" alt="" /></a>
<?php 
}
?>
<br><br>OR
<form method="post" onSubmit="return val();">
<table align="center" border="0" >

	<?php
    $star = "<font color='#FF0000'>*</font>";
	?>
    
	<?php		
    if(isset($chk_log))
    {
    ?>
        <tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $chk_log; ?></b></font></td></tr>
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
	<!--<td valign="middle"><?php //echo $star;?></td>
	<td align="left" ><font color="#000" style="font-size:16px">Security Code</font></td>
    <td><font color="#000" style="font-size:16px">&nbsp;:&nbsp;</font></td>
    <td><input type="password" name="sqr" id="sqr" size="20" style="background:none;color:#000" /></td>-->
</tr>

<tr>
	<td valign="middle"><?php echo $star;?></td>
	<td align="left"><font color="#000" style="font-size:16px">Username</font></td>
    <td><font color="#000" style="font-size:16px">&nbsp;:&nbsp;</font></td>
    <td ><input type="text" name="user" id="user" size="20" value="" style="background:none;color:#000" /></font></td>
</tr>

<tr>
	<td valign="middle"><?php echo $star;?></td>
	<td align="left"><font color="#000" style="font-size:16px">Password</font></td>
    <td><font color="#000" style="font-size:16px">&nbsp;:&nbsp;</font></td>
    <td><input type="password" name="pass" id="pass" size="20" value="" style="background:none;color:#000"/></td>
</tr>
<tr><td></td></tr>
<tr><td colspan="3">&nbsp;</td><td><font color="#FF0000" style="font-size:16px;">Password is Case Sensitive</font></td></tr>

<tr><td colspan="4">&nbsp;</td></tr>
    
<tr>
    <td colspan="4" align="center">
    <input type="submit" name="login" id="insert" value="Login">
    </td>
</tr>
</table>

</form>
<br />

<a href="newuser.php" style="color:#00F;"><b>Don't have an account ???</b></a>
