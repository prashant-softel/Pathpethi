<?php include_once("includes/header_empty.php");//include_once("includes/head_s.php");
	include_once('classes/include/check_session.php');
	include_once("classes/initialize.class.php");
	include_once("classes/defaults.class.php");
	include_once("classes/include/dbop.class.php");
	
	//include_once("classes/include/dbop.class.php");
	//$m_dbConnRoot = new dbop(true);
	$obj_initialize = new initialize($m_dbConnRoot);
	$bSubmitComplete = false;
	$msg = '';
	if(isset($_REQUEST['submit']) && $_REQUEST['submit'] == 'Submit')
	{
		$data = $_REQUEST;
		$res = $obj_initialize->fetchLoginDetails();
		$result = $obj_initialize->sendEmail($res,true,$data);
		if($result == true)
		{
				$msg = 'Your request has been recorded successfully.<br>We will get back to you soon.';
				$bSubmitComplete = true;
		}
        else if($result == false)
		{
				$msg = 'Your request not recorded.Please try again.';
		}
		}
	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>

<script  language="javascript" type="text/javascript">

function go_error()
{
	setTimeout('hide_error()',10000);	
}
function hide_error()
{
	document.getElementById('error').style.display = 'none';	
}
	
function varifyForm()
{
	var society_name = trim(document.getElementById("society_name").value);
	//var wing = trim(document.getElementById("wing").value);
	var  unit_no = trim(document.getElementById("unit").value);
		
	if(society_name == "")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter society name";
		document.getElementById("society_name").focus();
		
		go_error();
		return false;
	}
	else if(unit_no == "")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter unit number";
		document.getElementById("unit").focus();
		
		go_error();
		return false;
	}
	/*else if(wing == "")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter wing";
		document.getElementById("wing").focus();
		
		go_error();
		return false;
	}*/
	
	

   
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
</head>
<body>
	<center>
    <br />
    <br />

        <table align="center" cellspacing="10">
        	<h3><a href='verifycode.php?imp'>Click here if Have A New Code To Link Another Society/Flat</a></h3>
        </table>
        <br />
        OR
    	<br />
    	<br />
    	<div class="panel panel-info" id="panel"  style="width:40%;border-radius:10px;">
	<div class="panel-heading" id="pageheader" style="font-size:16px;"><b>Enter Society Details</b></div>
        <form role="form" method="post" onSubmit="return varifyForm();">
        <?php
		$star = "<font color='#FF0000'>*&nbsp;</font>";
?>
        <table align="center" cellspacing="10" >
        <tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $msg?></b></font></td></tr>
         <tr height="30"><td colspan="4" align="center"><font color="blue" style="size:11px;"><b>Kindly enter your society details to receive an account activation email.</b></font></td></tr>
            <tr align="left">
                    <td valign="middle"></td>
                    <th>Society Name<?php echo $star;?> </th>
                    <td>&nbsp; : &nbsp;</td>
                    <td><input type="text" required="required" name="society_name" id="society_name" value="<?php echo $_REQUEST['society_name'];?>"/></td>
            </tr>
            
            
            <tr align="left">
                    <td valign="middle"></td>
                    <th>Unit/Flat/Shop No<?php echo $star;?> </th>
                    <td>&nbsp; : &nbsp;</td>
                    <td><input type="text" required="required"  name="unit" id="unit" value="<?php echo $_REQUEST['unit'];?>"/></td>
            </tr>
            
            
            <tr align="left">
                    <td valign="middle"></td>
                    <th>Contact No<?php echo $star;?> </th>
                    <td>&nbsp; : &nbsp;</td>
                    <td><input type="text" name="wing" id="wing" required="required" value="<?php echo $_REQUEST['wing'];?>"/></td>
            </tr>
            <tr><td><br /></td></tr>

            <tr align="center">
                    <td valign="middle"  colspan="4"> <input   name="submit" id="submit" <?php if($bSubmitComplete){echo "Disabled";}?> value="Submit" type="submit" class="btn btn-primary"></td>
            </tr>
     	</table>
       
      </form>
    </div>
    </center>
</body>
</html>