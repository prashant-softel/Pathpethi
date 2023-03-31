<?php if(!isset($_SESSION)){ session_start(); }?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $_SESSION['society_name'];?></title>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />

<link  href="../css/admin.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../css/chromestyle.css" />
<script type="text/javascript" src="../js/chrome.js"></script>

<style type="text/css">
.style1 
{
	font-weight: bold;
}
</style>
</head>

<body>

<div id="main">
<?php
if(isset($_REQUEST['hm'])){$cls0 = 'active';}else{$cls0 = '';}
if(isset($_REQUEST['prf'])){$cls1 = 'active';}else{$cls1 = '';}
if(isset($_REQUEST['cp'])){$cls2 = 'active';}else{$cls2 = '';}
if(isset($_REQUEST['ev'])){$cls3 = 'active';}else{$cls3 = '';}
if(isset($_REQUEST['srm'])){$cls03 = 'active';}else{$cls03 = '';}

if(!isset($_SESSION['member_id'])){$cls5 = 'active';}else{$cls5 = '';}

?>

<div id="header">

<div style="margin-left:30px; margin-top:7px; width:650px; height:50px; padding-top:20px;">
<font color="#FF8000" size="+2"><b><?php echo $_SESSION['society_name'];?></b></font>
</div>

<?php if(isset($_SESSION['member_name'])){?>
<div style="margin-left:750px;width:210px;" align="center"><b>Welcome <?php echo $_SESSION['member_name'];?></b></div>
<?php }?>
<ul id="chromemenu" class="top-navigation">
	<li><a href="../home_m.php?hm" class="<?php echo $cls0;?>">Homepage</a></li>
   	
    <li><a href="javascript:void(0);" class="<?php echo $cls1;?>" rel="dropmenu1">Profile</a></li>
    <!--<li><a href="../view_member_profile.php?prf" class="<?php echo $cls1;?>">View Profile</a></li>-->
    
    <li><a href="../events_view.php?ev" class="<?php echo $cls3;?>">View Events</a></li> 
    <li><a href="../service_prd_reg_view.php?srm" class="<?php echo $cls03;?>">Service Provider</a></li>
    <li><a href="../cp_m.php?cp" class="<?php echo $cls2?>">Setting</a></li>
    
    <?php if(!isset($_SESSION['member_id'])){?>
    <li><a href="login.php">Login</a></li>
    <?php }else{?>
    <li><a href="logout_m.php">Logout</a></li>
    <?php }?>
    
</ul>
</div>

<div id="dropmenu1" class="dropmenudiv" style="width:170px;">
<a href="../view_member_profile.php?prf">View your profile</a>
<a href="../view_member_profile_mem_edit.php?prf">Edit your profile</a>
</div>

<div id="dropmenu01" class="dropmenudiv" style="width:170px;">
<a href="../service_prd_reg.php?srm">Registrtaion Form</a>
<a href="../service_prd_reg_view.php?srm">List of service provider</a>
</div>


<div id="dropmenu2" class="dropmenudiv" style="width:170px;">
<a href="../cp_m.php?cp">Change Password</a>
</div>


<script type="text/javascript">
	cssdropdown.startchrome("chromemenu");
</script>