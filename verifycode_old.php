<?php include_once("includes/header_empty.php");//include_once("includes/head_s.php");
	include_once('classes/include/check_session.php');
	include_once("classes/initialize.class.php");
	include_once("classes/defaults.class.php");
	include_once("classes/include/dbop.class.php");
	
	//include_once("classes/include/dbop.class.php");
	//$m_dbConnRoot = new dbop(true);
	$obj_initialize = new initialize($m_dbConnRoot);
	
	$msg = '';
	if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'verify')
	{
		if(!isset($_REQUEST['code']) || $_REQUEST['code'] == '')
		{
			$msg = 'Please Enter Your Code';
		}
		else
		{
			$result = $obj_initialize->verifyCode($_REQUEST['code']);
			if($result == '')
			{
				$msg = 'Invalid Code Entered. Please Re-Enter The Code';
			}
			else if($result[0]['status'] == 2 || $result[0]['status'] == 3)
			{
				$msg = 'Code Already In Use.';
			}
			else
			{
				$obj_initialize->setLoginIDToMap($result[0]['id'], 2);
				
				$mapDetails = $obj_initialize->getMapDetails($result[0]['id']);
		
				if($mapDetails <> '')
				{
					$_SESSION['current_mapping'] = $_REQUEST['mapid'];
					$obj_initialize->setCurrentMapping($_REQUEST['mapid']);
					
					$dbName = $mapDetails[0]['dbname'];
					$_SESSION['dbname'] = $dbName;
					
					$society_id = $mapDetails[0]['society_id'];
					$_SESSION['society_id'] = $society_id;
					
					$role = $mapDetails[0]['role'];
					$_SESSION['role'] = $role;
					
					$unit_id = $mapDetails[0]['unit_id'];
					$_SESSION['unit_id'] = $unit_id;
					
					$_SESSION['desc'] = $mapDetails[0]['desc'];
					
					$obj_initialize->setProfile($mapDetails[0]['profile']);
					
					?>
						<script>window.location.href = "initialize.php?set";</script>
					<?php
				}
			}
		}
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>
<body>
	<center>
    	<h2>Enter Your Code</h2>
        <div id="msg" style="color:#FF0000;font-weight:bold;"><?php echo $msg; ?></div>

    	<form name="verify_code"  method="post" action="">
    		<input type="text" id="code" name="code" />
            <input type="hidden" name="mode" value="verify" />
            <br /><br />
        	<input type="submit" value="Verify Code" />
        </form>
    </center>
</body>
</html>