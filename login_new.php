<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<?php

include_once "classes/include/dbop.class.php";
$m_dbConn = new dbop(true);

include_once "classes/login.class.php";
$main_obj = new login($m_dbConn);

//include_once "includes/head_test.php";
include_once("header.php");
if(isset($_REQUEST['login']))
{
	$chk_log = $main_obj->chk_log($m_dbConn);
}

if($_SERVER['HTTP_HOST'] != 'localhost')
{
	include_once "fb/fbmain.php";
}

if(isset($_GET['code']) && $_GET['code'] <> '')
{
	$fb_details = set_sess($userInfo);	
	
	//echo "<b>Please Wait...</b>";
	//print_r($fb_details);
	//$fb_details = array('name'=>'Ankur Patil', 'email'=>'ankur.2088@yahoo.co.in', 'fbid'=>'1234567890');
	if($fb_details['fbid']<>"")
	{
		$chk_log = $main_obj->chk_log_fb($fb_details['email'], $fb_details['fbid']);
	}
	else
	{
		?>
		<script>window.location.href = 'logout.php';</script>
		<?php
	}
}

//if($_SERVER['HTTP_HOST']<>'localhost')
{
	//include_once "fb/fbmain.php";
}
?>
<!--<link href="csss/style.css" rel="stylesheet" type="text/css" />-->
<!--  tracking id of anayltics.esociety@attuit.in -->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-65759475-1', 'auto');
  ga('send', 'pageview');

</script>
<script language="javascript">
function val()
{
	//var sqr = trim(document.getElementById("sqr").value);
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
	//document.getElementById("sqr").value = '';
	document.getElementById("user").value = '';
	document.getElementById("user").focus();
	document.getElementById("pass").value = '';
	//document.getElementById("sqr").focus();
}

</script>
 <link href="../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
   <link href="../bower_components/bootstrap-social/bootstrap-social.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<script language="JavaScript" type="text/javascript" src="js/validate.js"></script> 
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

<img src="images/logo.png"  />

<div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default" style="margin-top:2%;text-align:center;border:none;box-shadow: 0px 0px 0px rgba(0, 0, 0, 0.05);">

				<?php		
				if(isset($chk_log))
				{
				?>
					<font color="red" ><b id="error"><?php echo $chk_log; ?></b></font>
				<?php
				}
				else
				{
				?>
					<font color="red"><b id="error"></b></font>   
				<?php		
				}
				?>
				
			</div>
		</div>
	</div>
</div>
<div class="container" >
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default" style="margin-top:2%;">
                    <div class="panel-heading">
                        <h3 class="panel-title">Please Sign In</h3>
                    </div>
                    <div class="panel-body">
                        <form role="form" method="post" onSubmit="return val();">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="E-mail" type="text" name="user" id="user"  autofocus>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password"  name="pass" id="pass" type="password" value="">
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="remember" type="checkbox" value="Remember Me">Remember Me
                                    </label>
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                                <input  class="btn btn-lg btn-success btn-block" name="login" id="insert" value="Login" type="submit"
                            </fieldset>
                        </form>
                    </div>
					
                    
                </div>
				<div class="login-panel panel panel-default" style="margin-top:10%;text-align:center;border:none;box-shadow: 0px 0px 0px rgba(0, 0, 0, 0.05);">OR</div>
				<div class="login-panel panel panel-default" style="margin-top:2%">
					<div class="panel-body">
				<?php 
						if($_SERVER['HTTP_HOST'] == 'localhost')
						{
						?>
							<a class="btn btn-block btn-social btn-facebook" href="javascript:void(0);" onClick="alert('Facebook Connectivity Not Available On Localhost');">
                                <i class="fa fa-facebook"></i> Sign in with Facebook
                            </a>
							<a href="javascript:void(0);" onClick="alert('Facebook Connectivity Not Available On Localhost');"></a>
							<!--<a href="<?php echo $loginUrl?>"><img src="fb/fb_connect.jpg" alt="" /></a>-->
						<?php 
						}
						else
						{
						?>
							<a href="<?php echo $loginUrl?>" class="btn btn-block btn-social btn-facebook"><i class="fa fa-facebook"></i> Sign in with Facebook</a>
						<?php 
						}
						?>
					</div>
				</div>
				<div class="login-panel panel panel-default" style="margin-top:10%;text-align:center;border:none;box-shadow: 0px 0px 0px rgba(0, 0, 0, 0.05);">
				<a class="btn btn-outline btn-link" href="forgotpassword.php"  style="color:#00F;">Forgot Password ?</a><a type="button" class="btn btn-outline btn-link" href="newuser.php" style="color:#00F;">Don't have an account ?</a>
				</div>
            </div>
        </div>
    </div>
	<div style=" margin-top:10%;">
	</div>
<!--<form method="post" onSubmit="return val();">-->

    <!-- jQuery -->
    <script src="../bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>
<script type="text/javascript">
window.scrollTo(0,0);
</script>
<?php include_once "includes/foot_login.php";
 ?>														
