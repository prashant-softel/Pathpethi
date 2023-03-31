<?php include_once("includes/head_s.php");

include_once("classes/cp.class.php");
$obj_cp = new cp($m_dbConnRoot);

$code = $obj_cp->code();
?>

<?php
if(isset($_POST['cp']))
{
	$cp = $obj_cp->cp();
	$code = $obj_cp->code();
	unset($_REQUEST["res"]);
}
?>
	<script type="text/javascript" src="js/ajax.js"></script>
	<script language="javascript" type="application/javascript">
	function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }
	function hide_status()
	{

		document.getElementById('lblStatus').style.display = 'none';	
	}
    function go1()
    {
		document.getElementById("op").value = '';
        document.getElementById("np").value = '';
        document.getElementById("cnp").value = '';
        document.getElementById("op").focus();
    }
	</script>
	<script language="javascript">
	function val()
	{
		var code = trim(document.getElementById("code").value);	
		var op = trim(document.getElementById("op").value);
		var np = trim(document.getElementById("np").value);
		var cnp = trim(document.getElementById("cnp").value);	
		
		
		if(code=="")
		{
			document.getElementById('error').style.display = '';	
			document.getElementById("error").innerHTML = "Please enter security code";
			document.getElementById("code").focus();
			
			go_error()
			return false;
		}
		if(op=="")
		{
			document.getElementById('error').style.display = '';	
			document.getElementById("error").innerHTML = "Please enter old password";
			document.getElementById("op").focus();
			
			go_error()
			return false;
		}
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
		if(op==np)
		{
			document.getElementById('error').style.display = '';	
			document.getElementById("error").innerHTML = "Old password and New Password can not be same.";
			
			document.getElementById("np").value = "";
			document.getElementById("cnp").value = "";
			document.getElementById("np").focus();
			
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
	
	function code_generate()
	{
		remoteCall("ajax/code_generate.php","&new_emp_id=","res_code_generate");
	}
	function res_code_generate()
	{
		var res = sResponse;
		document.getElementById('code').value = res;
	}
	
	function reset_code()
	{
		var code_old = document.getElementById('code_old').value;
		document.getElementById('code').value = code_old	
	}
	function ValidateEmail()  
	{  
		var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;  
		if((document.getElementById("txtNewUserName").value != "") && (document.getElementById("txtConfirmUserName").value != "") && (document.getElementById("txtNewUserName").value == document.getElementById("txtConfirmUserName").value))
		{
			if(document.getElementById("txtNewUserName").value.match(mailformat))  
			{  
				//alert("Email address valid!");  
				return true;  
			}  
			else  
			{  
				alert("Entered email address is not in valid email format !");  
				document.getElementById("txtNewUserName").focus();  
				return false;  
			}
		}
		else
		{
			if(document.getElementById("txtNewUserName").value == "" || document.getElementById("txtConfirmUserName").value == "")
			{
				alert("Please provide valid email address!");  
			}
			else if(document.getElementById("txtNewUserName").value != document.getElementById("txtConfirmUserName").value)
			{
				alert("You have entered an email address and confirm email address does not match!");  
			}
			document.getElementById("txtNewUserName").focus();  
			return false; 
		}
	} 
    </script>

<!---  -->
<style type="text/css">
#single_form_element{margin-top:0px;}
#single_form_element .chk_avlblty{width:110px; border:#000000; width:190px; padding-top:5px;}
#single_form_element .strength-text{font-size:13px;font-weight:bold;}
#single_form_element .chk_avlblty span.password-strength{width:110px;-moz-border-radius:5px;-webkit-border-radius:5px;height:10px;background-color:#555;display:block;}
#single_form_element .chk_avlblty span.client-avail{display:block;}
</style>

<script type="text/javascript" src="js/pass.js"></script>
<script type="text/javascript" src="js/passwordstrength.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$( 'input.password-box' ).live( 'keyup', function() {
	var howStrong = passwordStrength( $( this ).val() );
	$( '.strength-text', $( this ).next() ).text( howStrong );
	var sColor = '#555';
	switch( howStrong ) {
		case 'Strong' :
			$( '.password-strength', $( this ).next() ).css( 'background-color', 'green' );
			break;
		case 'Medium' :
			$( '.password-strength', $( this ).next() ).css( 'background-color', 'lightgreen' );
			break;
		case 'Weak' :
			$( '.password-strength', $( this ).next() ).css( 'background-color', 'orange' );
			break;
		case 'Short' :
			$( '.password-strength', $( this ).next() ).css( 'background-color', 'red' );
			break;
		default :
			$( '.password-strength', $( this ).next() ).css( 'color', '#555' );
	}
	}).focusout( function() {
		$( this ).trigger( 'keyup' );
	});

});
function ShowUserName()
{ 

document.getElementById('frmPassword').style.display = 'none';
document.getElementById('frmUserName').style.display = 'block';
}
function ShowPassword()
{ 
document.getElementById('frmUserName').style.display = 'none';
document.getElementById('frmPassword').style.display = 'block';
}
</script>
<!---  -->

<?php if(isset($cp)){?>
<body onLoad="go_error();go1();">
<?php }else{ ?>
<body onLoad="go1();">
<?php } ?>
<br>
<div class="panel panel-info" id="panel" style="display:none;width:70%">
        <div class="panel-heading" id="pageheader">Change Username / Password</div>

<center>
 <div class="col-lg-12" style="margin-top:20px;min-height:140px">
    <div class="well" style="margin-top:20px;min-height:140px">
		
		
        <table style="font-size:12px;text-align:center;">
        <tr height="40"><td colspan="2" style="text-align:justify;font-size:2vw;padding-bottom:1vw">Choose what you would like to do ?</td></tr>
        <tr style="padding-top:40px">
        <td><button id="btnChangeUName" class="btn btn-primary" name="btnChangeUName" onClick="ShowUserName()">Change Username</button></td>
        <td> <button id="btnChangePassword" name="btnChangePassword" class="btn btn-primary" onClick="ShowPassword()">Change Password</button></td>
        </tr>
        </table>
        <br/>
        
        <?php 
		//echo "data".$_REQUEST["res"];
		
		if(isset($_REQUEST["res"]))
		{
			if($_REQUEST["res"] == "0")
			{
				echo "<label id='lblStatus' style='color:#FF0000'>Unable to change Username, please try again</label>";
			}
			else if($_REQUEST["res"] == "1")
			{
				echo "<label id='lblStatus' style='color:#FF0000'>Username change Successfully</label>";
			}
			else
			{
				echo "<label id='lblStatus' style='color:#FF0000'>Error occured. Unable to change Username, please try again</label>";
			}
		}
		else
		{
				echo "<label id='lblStatus' style='color:#FF0000'></label>";
		
		}
		
		$star = "<font color='#FF0000'>*&nbsp;</font>";
		?>
		
		<?php		
		if(isset($cp))
		{
		?>
			<table><tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $cp; ?></b></font></td></tr></table>
		<?php
		}
		else
		{
		?>
			<table><tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"></b></font></td></tr></table>	   
		<?php		
		}
		?>
        <table><tr height="30"><td colspan="4" align="center"><font style="size:11px;background-color:#FFF;border-radius:10px;color:#F00;padding:5px"><b id="Note"></b>Note: Change of UserName or Password would be updated for all societies/units you are connected. Your communcation email id in connected societies/units will be un-affected. </font></td></tr></table>	   
    </div>
</div>
 <form method="post" id="frmPassword" style="display:none" onSubmit="return val();">
 
<table align="center" border="0">	
	
    
    <tr align="left" height="40" style="display:none;">
    	<td valign="middle"><?php echo $star;?></td>
        <td>Security code</td>
        <td>&nbsp;:&nbsp;</td>
        <td>
        <input type="text" name="code" id="code" value="0"  style="width:90px;" maxlength="10"/>
		<input type="hidden" name="code_old" id="code_old" value="<?php echo $code['security_no']?>"  style="width:90px;" maxlength="10"/>        	
        &nbsp;&nbsp; <a href="javascript:void(0);" style="color:#00F; font-size:10px;" onClick="code_generate();"><b>Generate New</b></a>
        &nbsp;&nbsp; <a href="javascript:void(0);" style="color:#00F; font-size:10px;" onClick="reset_code();"><b>Reset</b></a>
        </td>
    </tr>
        
    <tr align="left">
    	<td valign="middle"><?php echo $star;?></td>
        <td>Enter Old Password</td>
        <td>&nbsp;:&nbsp;</td>
        <td><input type="password" name="op" id="op" /></td>
    </tr>
    <tr height="40"><td colspan="3">&nbsp;</td><td><font color="#FF0000" style="font-size:11px;"><u>Note</u> : Password is case sensitive</font></td></tr>
    	
    <tr align="left">
    	<td valign="top"><?php echo $star;?></td>
        <td valign="top">Enter New Password</td>
        <td valign="top">&nbsp;:&nbsp;</td>
        <td>
        <div id="single_form_element">
        <input type="password" name="np" id="np" class="password-box"/>
        <div class="chk_avlblty chk_pswd"><span class="password-strength"></span> <span class="strength-text"></span></div>
        </div>
    	<font color="#FF0000" size="-2"><b>To make it stronger, use upper and lower case letters,<br>numbers and symbols like !  ? $ % ^ & )</b></font>
        </td>
    </tr>
    
    <tr align="left">
    	<td valign="middle"><?php echo $star;?></td>
        <td>Confirm New Password</td>
        <td>&nbsp;:&nbsp;</td>
        <td><input type="password" name="cnp" id="cnp" /></td>
    </tr>
    
    <tr><td colspan="4">&nbsp;</td></tr>
    
    <tr>
        <td colspan="4" align="center">
        <input type="submit" name="cp" id="insert" value="Update"  class="btn btn-primary" >
        </td>
    </tr>
    
    </table>
    </form>
<form method="post" id="frmUserName" style="display:none" onSubmit="return ValidateEmail();" action="process/cp.process.php?cu=true">
 
<table align="center" border="0" style="">	
	<?php
    $star1 = "<font color='#FF0000'>*&nbsp;</font>";
	?>
    
	<?php		
    if(isset($cp1))
    {
    ?>
        <tr height="30"><td colspan="3" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $cp1; ?></b></font></td></tr>
    <?php
    }
    else
    {
    ?>
        <tr height="30"><td colspan="3" align="center"><font color="red" style="size:11px;"><b id="error"></b></font></td></tr>	   
    <?php		
    }
	
    ?>
<tr height="40" ><td><?php echo $star;?>Your Display name </td><td>: </td><td><input type="text" id="txtDisplayName" name="txtDisplayName" value="<?php echo $code['name'] ?>"></td></tr>
<tr height="40" ><td><?php echo $star;?>Your Current Username </td><td>: </td><td><input type="text" value="<?php echo $code['member_id'] ?>"></td></tr>
<tr height="40" ><td><?php echo $star;?>Your New Username </td><td>: </td><td><input type="text" id="txtNewUserName" name="txtNewUserName" value=""></td></tr>
<tr height="40" ><td><?php echo $star;?>Confirm Your New Username </td><td>: </td><td><input type="text" id="txtConfirmUserName" name="txtConfirmUserName" value=""></td></tr>
<tr height="40" ><td colspan="3"  align="center"><input type="submit" name="cu" value="Update" class="btn btn-primary" style="background-color:#FFF;color:#000000"></td></tr>
</table>
</form>
</center>
</div>
<script type="text/javascript">
setTimeout('hide_status()',10000);
</script>
<?php include_once "includes/foot.php"; ?>