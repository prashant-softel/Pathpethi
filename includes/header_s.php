<?php if(!isset($_SESSION)){ session_start(); }?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Way2Society</title>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<link rel="icon" type="image/png" href="favicon.ico">
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
if(isset($_REQUEST['prm'])){$cls01 = 'active';}else{$cls01 = '';}
if(isset($_REQUEST['mm'])){$cls1 = 'active';}else{$cls1 = '';}
if(isset($_REQUEST['imp'])){$cls11 = 'active';}else{$cls11 = '';}
if(isset($_REQUEST['scm'])){$cls2 = 'active';}else{$cls2 = '';}
if(isset($_REQUEST['grp'])){$cls22 = 'active';}else{$cls22 = '';}
if(isset($_REQUEST['srm'])){$cls3 = 'active';}else{$cls3 = '';}
if(isset($_REQUEST['ev'])){$cls33 = 'active';}else{$cls33 = '';}
if(isset($_REQUEST['as'])){$cls4 = 'active';}else{$cls4 = '';}

if(!isset($_SESSION['admin'])){$cls5 = 'active';}else{$cls5 = '';}

?>

<div id="header">

<div style="margin-left:30px; margin-top:7px; width:650px; height:50px; padding-top:20px;">
<font color="#FF8000" size="+2"><img src="../images/logo.png" /><b>Society Software</b></font>
</div>

<?php if(isset($_SESSION['sadmin'])){?>
<div style="margin-left:750px;width:210px;" align="center"><b>Welcome <?php echo $_SESSION['sadmin'];?></b></div>
<?php }?>

<ul id="chromemenu" class="top-navigation">
	<li><a href="../home_s.php?hm" class="<?php echo $cls0;?>">Homepage</a></li>
    <li><a href="javascript:void(0);" class="<?php echo $cls01;?>" rel="dropmenu0">Permission</a></li>
    <li><a href="javascript:void(0);" class="<?php echo $cls1;?>" rel="dropmenu1">All Master</a></li>
    <li><a href="javascript:void(0);" class="<?php echo $cls11;?>" rel="dropmenu11">Society</a></li>
    <li><a href="../list_society_group.php?grp" class="<?php echo $cls22;?>">Group</a></li>
    <li><a href="javascript:void(0);" class="<?php echo $cls2;?>" rel="dropmenu2">Members</a></li>
   	<li><a href="javascript:void(0);" class="<?php echo $cls3;?>" rel="dropmenu3">Services</a></li>
    <li><a href="../events_view_as.php?ev"   class="<?php echo $cls33;?>">Events</a></li>
   
    <li><a href="javascript:void(0);" class="<?php echo $cls4?>" rel="dropmenu4">Setting</a></li>
    
    <?php if(!isset($_SESSION['sadmin'])){?>
    <li><a href="login.php">Login</a></li>
    <?php }else{?>
    <li><a href="logout_s.php">Logout</a></li>
    <?php }?>
    
</ul>
</div>

<div id="dropmenu0" class="dropmenudiv" style="width:170px;">
<a href="../del_control_sadmin.php?prm">Permission for delete</a>
</div>

<div id="dropmenu1" class="dropmenudiv" style="width:170px;">
<a href="../document.php?mm">Document</a>
<a href="../cat.php?mm">Category</a>
<a href="../bg.php?mm">Blood Group</a>
<a href="../desg.php?mm">Designation</a>
</div>

<div id="dropmenu11" class="dropmenudiv" style="width:170px;">
<a href="../society_view.php?imp">View Society List</a>
<a href="../wing.php?imp">View Wing</a>
<a href="../unit.php?imp">View Unit</a>
</div>

<div id="dropmenu2" class="dropmenudiv" style="width:170px;">
<a href="../list_member.php?scm">Member List</a>
<a href="../mem_rem_data.php?scm">Member Records Status</a>    
</div>

<div id="dropmenu3" class="dropmenudiv" style="width:170px;">
<a href="../service_prd_reg_view.php?srm">List of service provider</a>
<a href="../service_prd_reg_search.php?srm">Search here</a>
</div>

<div id="dropmenu4" class="dropmenudiv" style="width:190px;">
<a href="../cp.php?as">Change Password</a>
<a href="../add_member_id.php?as">Search login id & password</a>
</div>


<script type="text/javascript">
	cssdropdown.startchrome("chromemenu");
</script>