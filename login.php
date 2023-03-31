<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<?php

include_once "classes/include/dbop.class.php";
$m_dbConn = new dbop(true);

include_once "classes/login.class.php";
$main_obj = new login($m_dbConn);

include_once("header.php");

include_once("Facebook/fbmain.php");

$FBCallBackURL = "";

/*********************************************************/
//Create call back URL for FB Login

if(isset($_REQUEST['mCode'])  && $_REQUEST['mCode'] <> "")
{
	$FBCallBackURL  = FB_BASE_APP_URL . "login.php?mCode=".$_REQUEST['mCode'];
}
else if(isset($_REQUEST['c'])  && $_REQUEST['c'] <> "")
{
	$FBCallBackURL  = FB_BASE_APP_URL . "login.php?c=".$_REQUEST['c'];
}

if(isset($_REQUEST['url']))
{
		if((isset($_REQUEST['c'])  && $_REQUEST['c'] <> "") ||  (isset($_REQUEST['mCode'])  && $_REQUEST['mCode'] <> ""))
		{
			$FBCallBackURL .= '&url='.$_REQUEST['url'];
		}
		else
		{
			$FBCallBackURL .= '?url='.$_REQUEST['url'];
		}
}

setLoginURL($FBCallBackURL);
/*********************************************************/

if(isset($_REQUEST['login']))
{
	if(isset($_REQUEST['mCode']) && $_REQUEST['mCode'] <> '' && isset($_REQUEST['u']))
	{
		$chk_log = $main_obj->chk_log($_REQUEST['mCode'], $_REQUEST['u']);
	}
	else
	{
		$chk_log = $main_obj->chk_log();
	}
}
if((isset($_REQUEST['mCode']) && isset($_REQUEST['u'])) ||   isset($_REQUEST['u']))
{
	$emailID = $main_obj->obj_Utility->decryptData($_REQUEST['u']);
}

if(isset($_GET['code']) && $_GET['code'] <> '')
{
	$fb_details = $UserDetails;//getFBUserDetails();
	//var_dump($fb_details);
	
	//echo "<b>Please Wait...</b>";
	//print_r($fb_details);
	//$fb_details = array('name'=>'Ankur Patil', 'email'=>'ankur.2088@yahoo.co.in', 'fbid'=>'1234567890');
	if($fb_details['fbid']<>"")
	{	
		$connectCode = "";
		if(isset($_GET['c']))
		{
			$connectCode = $_GET['c'];
		}
		else if(isset($_GET['mCode']))
		{
			$connectCode = $_GET['mCode'];
		}

		
		$chk_log = $main_obj->chk_log_fb($fb_details['email'], $fb_details['fbid'], $connectCode);
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

  ga('create', 'UA-66785488-1', 'auto');
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
	//document.getElementById("user").value = '';
	document.getElementById("user").focus();
	document.getElementById("pass").value = '';
	//document.getElementById("sqr").focus();
}

</script>
 <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
   <link href="bower_components/bootstrap-social/bootstrap-social.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
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
<Style>
body { 
  background:url(images/login_bg.jpeg) no-repeat center center fixed; 
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;
}
</style>
<body onLoad="go1();go_error();">

<noscript>
    <div style="color:#F00;text-align:center;font-size:2vw">This site <strong>requires</strong> JavaScript. <br/>Please Enable Javascript from settings and refresh this page.</div> 
</noscript>

<div class="container" style="margin:0%;margin-top:1%;">
        <div class="row">
            <div class="col-md-4 col-md-offset-4" style="background:#FFF;border-radius: 30px 30px 0px 0px">
                <div class="login-panel panel panel-default" style="margin-top:10%;text-align:center;border:none;box-shadow: 0px 0px 0px rgba(0, 0, 0, 0.05);background:#FFF;">
<a href="index.php" id="logo"><img src="images/logo.png" style="padding-bottom:5%;padding-top:2%" /></a>
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
<div class="container" style="margin:0%">
        <div class="row" style="">
            <div class="col-md-4 col-md-offset-4" style="background:#FFF;border-radius: 0px 0px 30px 30px;padding-top:10px">
                <div class="login-panel panel panel-default" style="margin-top:-2%;">
                    <div class="panel-heading">
                        <h3 class="panel-title">Please Sign In</h3>
                    </div>
                    <div class="panel-body">
                        <form role="form" method="post" action="<?php echo $FBCallBackURL; ?>" onSubmit="return val();">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="E-mail" type="text" name="user" id="user" value="<?php echo $emailID; ?>" autofocus>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password"  name="pass" id="pass" type="password" value="">
                                </div>
                                <!--<div class="checkbox">
                                    <label>
                                        <input name="remember" type="checkbox" value="Remember Me">Remember Me
                                    </label>
                                </div> -->
                                <!-- Change this to a button or input when using this as a form -->
                                <input  class="btn btn-lg btn-success btn-block" name="login" id="insert" value="Login" type="submit">
                            </fieldset>
                        </form>
                    </div>
					
                </div>
				<!-- <div class="login-panel panel panel-default" style="margin-top:10%;text-align:center;border:none;box-shadow: 0px 0px 0px rgba(0, 0, 0, 0.05);">OR</div>
				<div class="login-panel panel panel-default" style="margin-top:2%">
					<div class="panel-body">
						<a href="<?php echo htmlspecialchars($loginUrl); ?>" class="btn btn-block btn-social btn-facebook"><i class="fa fa-facebook"></i> Sign in with Facebook</a>
					</div>
				</div> -->
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
    <script src="bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="dist/js/sb-admin-2.js"></script>

<?php include_once "includes/foot_login.php";
 ?>														
<script type="text/javascript">
window.scrollTo(0,0);
</script>