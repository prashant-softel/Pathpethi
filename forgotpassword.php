<?php 
include_once("includes/header_empty.php");
	include_once("classes/forgetpassword.class.php");
	
	include_once("classes/include/dbop.class.php");
	$m_dbConnRoot = new dbop(true);
	$obj_password = new forgetpassword($m_dbConnRoot);
	$msg = '';
	if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'verify')
	{
		if(!isset($_REQUEST['email']) || $_REQUEST['email'] == '')
		{
			$msg = 'Please Enter Your E-Mail ID';
		}
		else
		{
			$msg = $obj_password->checkEmail($_REQUEST['email']);
		}
	}
	
	$star = "<font color='#FF0000'>*</font>";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<center>
    <br />
    <div style="border:1px solid black; width:50%; height:50%;position: relative;top: 50%;transform: translateY(50%);">
    	<h2>Recover Password</h2>
    	
        <div id="msg" style="color:#FF0000;font-weight:bold;padding:10px;"><?php echo $msg; ?></div>
        
		
             <div id="no_fb" style="display:block; ">
             <h4>Please enter your e-mail id to recover your password</h4>
            
            <form name="recover_password"  method="post" action="">
                <input type="hidden" name="mode" value="verify" />
                <table>
                    <tr>
                        <td><input type="text" name="email" id="email" value="<?php echo $_REQUEST['email']; ?>" style="height:30px;width:300px;text-align:center;" /></td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center" style="padding:10px;"><input type="submit" value="Recover Password" style="background-color:#09F; width:50%;"/></td>
                    </tr>
                </table>
             </form>
         	</div>
         
         <h5 style="color:#09F;">&#x27a4;&nbsp;<a href="logout.php"><b>Click here to login...</b></a></h5>
        </div>
         <br />
    </center>
</body>
</html>