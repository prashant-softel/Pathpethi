<?php if(!isset($_SESSION)){ session_start(); }?>
<?php 
//include_once "../fb/fbmain_new.php";
include_once "fb/fbmain.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo $_SESSION['society_name'];?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta name="description" content="Contact us page - free business website template available at TemplateMonster.com for free download."/>
<link href="csss/style.css" rel="stylesheet" type="text/css" />
<link href="csss/layout.css" rel="stylesheet" type="text/css" />
<script src="jsss/maxheight.js" type="text/javascript"></script>
<!--[if lt IE 7]>
	<link href="ie_style.css" rel="stylesheet" type="text/css" />
<![endif]-->

<link rel="stylesheet" type="text/css" href="cssss/chromestyle.css" />
<!--<link rel="stylesheet" type="text/css" href="../cssss/chromestyle.css" />-->
<script type="text/javascript" src="js/chrome.js"></script>

</head>
<body>
<?php
if(isset($_REQUEST['hm'])){$cls0 = 'first-current';}else{$cls0 = '';}
if(isset($_REQUEST['prf'])){$cls1 = 'current';}else{$cls1 = '';}
if(isset($_REQUEST['cp'])){$cls2 = 'current';}else{$cls2 = '';}
if(isset($_REQUEST['ev'])){$cls3 = 'current';}else{$cls3 = '';}
if(isset($_REQUEST['srm'])){$cls03 = 'current';}else{$cls03 = '';}

if(!isset($_SESSION['member_id'])){$cls5 = 'current';}else{$cls5 = '';}

?>

<!-- header -->
	<div id="header">
		<div class="container">
			<div class="row-1">
				
                <div class="logo" style="width:600px; margin-top:40px;">
                	<font color="#FF8000" size="+2"><b><?php echo $_SESSION['society_name'];?></b><br /></font>
                </div>
				
                <ul class="top-links1" style="float:right;height:30px; margin-top:100px;">
					Welcome <b><?php echo $_SESSION['member_name'];?></b>&nbsp;&nbsp;
				</ul>
			</div>
			<div class="row-2">
<!-- nav box begin -->
				<div class="nav-box">
					<div class="left">
						<div class="right">
							<ul id="chromemenu" class="top-navigation">
								<li><a href="home_m.php?hm" id="<?php echo $cls0;?>" class="first"><em><b style="width:150px;">HomePage</b></em></a></li>
								<li><a href="javascript:void(0);" id="<?php echo $cls1;?>" rel="dropmenu1"><em><b style="width:160px;">Profile</b></em></a></li>
								<li><a href="events_view.php?ev" id="<?php echo $cls3;?>"><em><b style="width:160px;">View Events</b></em></a></li>
								<li><a href="service_prd_reg_view.php?srm" id="<?php echo $cls03;?>"><em><b style="width:160px;">Service Provider</b></em></a></li>
								<li><a href="cp_m.php?cp" id="<?php echo $cls2;?>"><em><b style="width:160px;">Setting</b></em></a></li>
								
                                <li><a href="logout_m.php" id="last"><em><b style="width:160px;">Logout</b></em></a></li>
                                <!--<li><a href="<?php echo $logoutUrl?>" id="last"><em><b style="width:160px;">FB-Logout</b></em></a></li>-->
                                
							</ul>
						</div>
					</div>
				</div>
<!-- nav box end -->
			</div>
		</div>
	</div>
<!-- content -->
	<div id="content">
		<div class="container">
			<div class="section">
<!-- box begin -->
				<div class="box">
					<div class="border-top">
						<div class="border-right">
							<div class="border-bot">
								<div class="border-left">
									<div class="left-top-corner">
										<div class="right-top-corner">
											<div class="right-bot-corner">
												<div class="left-bot-corner">
													<div class="inner">
													
<div id="dropmenu1" class="dropmenudiv" style="width:170px;">
<a href="view_member_profile.php?prf">View your profile</a>
<a href="view_member_profile_mem_edit.php?prf">Edit your profile</a>
</div>

<div id="dropmenu01" class="dropmenudiv" style="width:170px;">
<a href="service_prd_reg.php?srm">Registrtaion Form</a>
<a href="service_prd_reg_view.php?srm">List of service provider</a>
</div>


<div id="dropmenu2" class="dropmenudiv" style="width:170px;">
<a href="cp_m.php?cp">Change Password</a>
</div>


<script type="text/javascript">
	cssdropdown.startchrome("chromemenu");
</script>