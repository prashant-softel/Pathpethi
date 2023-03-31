<?php
include_once("classes/head.class.php"); 
include_once("../classes/dbconst.class.php");
$m_objHead_S = new head($m_dbConnRoot,$m_dbConn);
include_once("classes/notice.class.php");
$obj_notice = new notice($m_dbConn);
$display_notices=$obj_notice->FetchNotices("");
$NoticeCounter = count($display_notices);
include_once("classes/events.class.php");
$obj_events = new events($m_dbConn, $m_dbConnRoot);
$events = $obj_events->view_events();
$EventsCounter = count($events);
include_once("classes/servicerequest.class.php");
$obj_request = new servicerequest($m_dbConn, $m_dbConnRoot);
$requests = $obj_request->getRecords($_REQUEST['cm']);
$RequestsCounter = count($requests);
include_once("classes/tenant.class.php");
$obj_request = new tenant($m_dbConn);
$LeaseAlert = $obj_request->TenantAlert();
//echo "<pre>";
//print_r($LeaseAlert);
//echo "</pre>";

/*include_once("classes/FixedDeposit.class.php");
$obj_FixedDeposit = new FixedDeposit($m_dbConn,$m_dbConnRoot);
$fds = $obj_FixedDeposit->getMaturedFDs();
$FDCounter = count($fds);*/
$FDCounter =0;
$msgCounter = 0;
 $bIsHide = bIsReportOrValidationPage($scriptName);
//var_dump($events);
//echo $NoticeCounter;
//var_dump($display_notices[$NoticeCounter - 1]);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Way2Society</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<link rel="icon" type="image/png" href="favicon.ico">
<meta name="description" content="Contact us page - free business website template available at TemplateMonster.com for free download."/>
<link href="csss/style.css" rel="stylesheet" type="text/css" />
<link href="cssss/layout.css" rel="stylesheet" type="text/css" />
<script src="jsss/maxheight.js" type="text/javascript"></script>
<!--[if lt IE 7]>
	<link href="ie_style.css" rel="stylesheet" type="text/css" />
<![endif]-->
<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- MetisMenu CSS -->
<link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

<!-- Custom CSS -->
<link href="dist/css/sb-admin-2.css" rel="stylesheet">

<!-- Custom Fonts -->
<link href="bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="cssss/chromestyle.css" />

<!-- Bootstrap Core CSS -->
    <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="bower_components/datatables-responsive/css/dataTables.responsive.css" rel="stylesheet">


<!--<link rel="stylesheet" type="text/css" href="../cssss/chromestyle.css" />-->
<script type="text/javascript" src="js/chrome.js"></script>
<script>
	function HideMsgCounter()
	{
			localStorage.setItem("msgCounter",0);	
	}
</script>
<style>
#right-notice
{
	float:right;
	background:#F00;
	width:100px;
	height:40px;
	color:#000000;
	}	
#pageheader {
	color:#43729F;
	font-weight:bold;
	font-size:20px;
	text-align:center;
	font-family:Georgia, "Times New Roman", Times, serif;
	justify-content: center;
	padding-left:100px;
	padding-right:100px;
	
}	
.msg-notify{
  /* background:red;
   background-image:"images/icon.png";
   position:relative;
   top: -10px;
   left: -35px;*/
   background-color: #fa3e3e;
  border-radius: 2px;
  color: white;
 
  padding: 3px 3px;
  font-size: 10px;
  
  position: relative; /* Position the badge within the relatively positioned button */
   top: -6px;
   left: -37px;
}
</style>
</head>
<body>

    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <?php //var_dump($_SESSION);
				$URL = "";
				if($_SESSION['role'] == "Super Admin" || $_SESSION['role'] == "Admin")
				{
					$URL = "home_s.php?View=ADMIN";
				}
				else
				{
					$URL = "Dashboard.php?View=MEMBER";
				}
				?>
                        
                <table><tr><td style="text-align:center;font-size:50%" onclick="window.location.href='<?php echo $URL; ?>'"><!--<a class="navbar-brand" href="home.php" style="color:#990000;">--><a href="<?php echo $URL; ?>"><img width="60%" src="images/logo_t.gif" /></a><!--</a>--></td><td style="vertical-align:middle"><b>Way2Society.com - Housing Society Social & Accounting Software</b><br /><font color="#039"><a href="initialize.php?imp"><?php if(isset($_SESSION['society_id']) && $_SESSION['society_id'] <> 0) { echo $societyName = $m_objHead_S->GetSocietyName($_SESSION['society_id']) . ' - [' . $_SESSION['desc'] .']';} ?></a>
				<?php if($_SESSION['role'] ==  ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN || $_SESSION['role'] == ROLE_ADMIN_MEMBER)
				{?>
					&nbsp;&nbsp;&nbsp;<a href="defaults.php" title="click here to change year"><?php if(isset($_SESSION['society_id']) && $_SESSION['society_id'] <> 0) { echo '[' . $m_objHead_S->GetYearDesc($_SESSION['default_year']). ']';} ?></a>
                <?php }?>
                
                <?php if(IsReadonlyPage() == true &&  $_SESSION['role']  <> ROLE_MEMBER)
				{?>
                <i class='fa  fa-lock'  style='font-size:10px;font-size:1.75vw;color:#F00;' title="This year has been locked by accountant"></i>
                <?php }?>
                </font></td></tr></table>
            </div>
            <!-- /.navbar-header -->

                    
            <ul class="nav navbar-top-links navbar-right" style="margin-top:6px">
            	<ul>
				<?php if($_SESSION['module']['notice'] == "0" && $_SESSION['module']['service_request'] == "0" && $_SESSION['module']['event'] == "0")
				{
					//hide all
				}
				else
				{
					?> 
					<li class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#">
							<i class="fa fa-envelope fa-fw" onclick="HideMsgCounter();"></i>  <i class="fa fa-caret-down"></i>
						</a>
                       <span class="badge msg-notify" id = "counter" style="height:15px; font-size:9px; visibility:hidden;"></span>
		
                        <ul class="dropdown-menu dropdown-messages">
							<?php if($_SESSION['module']['notice'] == "1")
							{
								?>
								<li>
									<a href="notices.php">
										<div>
											<strong>Recent Notice</strong>
											
										</div>
										
                                        <?php 
                                             if(sizeof($display_notices) > 0)
                                            { 
                                                for($m = sizeof($display_notices) -1 ; $m > sizeof($display_notices) -3; $m--)
                                                {
													
                                                    if(isset($display_notices[$m]["subject"]) )
                                                    {
                                                        $msgCounter++;
                                                    ?>
                                                        <div ><?php echo $display_notices[$m]["subject"];?><span class="pull-right text-muted">
											<em><?php echo  getDisplayFormatDate($display_notices[$m]["post_date"]); ?></em>
										</span></div>
                                                     <?php
                                                    }
                                                }
											 }?>
									</a>
								</li>
								<li class="divider"></li>
								<?php
							}
							?>
							<?php if($_SESSION['module']['service_request'] == "1")
							{
								?>
								<li>
									<a href="servicerequest.php">
										<div><strong>Recent Service Request</strong></div>
											<?php 
                                             if(sizeof($requests) > 0)
                                            { 
                                                for($m = 0 ; $m < sizeof($requests), $m <= 2; $m++)
                                                {
                                                    if(isset($requests[$m]["summery"]) && $requests[$m]["summery"] <> "" )
                                                    {
                                                        $msgCounter++;
                                                 
                                               			  $text=strip_tags($requests[$m]["summery"])
														?>
								
                                                        <div><?php echo substr($text ,0,25);echo "..."  ?>
                                                   </div>
                                                        <div>
                                                        <span style="float:right;margin-top:-20px;" class="text-muted"><?php echo getDisplayFormatDate($requests[$m]["dateofrequest"]);?></span></div>
                                                     <?php
                                                    }
												  }
                                            }?>
										</a>
								</li>
								<li class="divider"></li>
                                <?php if($_SESSION['role'] ==  ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN || $_SESSION['role'] == ROLE_ADMIN_MEMBER)
				{?>
                                <li>
                                		<a href="FixedDeposit.php">
                                        	<div><strong>Fixed Deposit Maturity Alert</strong></div>
                                            
                                            <?php 
												if(sizeof($fds) > 0)
												{ 
													for($m = 0 ; $m < sizeof($fds), $m <= 2; $m++)
													{
														if(isset($fds[$m]['fd_name']) &&$fds[$m]['fd_name'] <> "" )
														{
															$msgCounter++;
														?>
															<div><?php echo $fds[$m]['fd_name'];  ?>
																<span style="float:right;" class="text-muted"><?php echo getDisplayFormatDate($fds[$m]['maturity_date']);?></span>
															</div>
														 <?php
														}
													}
												}?>
                                        </a>
                                
                                </li>
                                <li class="divider"></li>
                                <?php }?>
                                
								<?php
							}
							?>
							<?php if($_SESSION['module']['event'] == "1")
							{
								?>						
								<li>
									<a href="events_view.php">
										<div>
											<strong>Recent Event</strong>
											
										</div>
									 <?php 
									 if(sizeof($events) > 0)
                                            {  $startDate = date('Y-m-d');
                                                for($m = sizeof($events) -1 ; $m > sizeof($events) -3; $m--)
                                                {
													
                                                    if(isset($events[$m]["events_title"]) )
                                                    { 
													if( $events[$m]['end_date'] >=$startDate )
                                                       { //echo $msgCounter;
														$msgCounter++;
                                                    ?>
                                                       <div><?php echo $events[$m]["events_title"];  ?><span class="pull-right text-muted">
												<em><?php echo getDisplayFormatDate($events[$m]["events_date"]);?></em>
											</span>
										 </div>
                                                     <?php
                                                    
													   }
													  }
                                                }
                                            }?>
                                        
                                       
									</a>
								</li>
								<?php
							}
							?>
                            <li class="divider"></li>
                            <?php if($_SESSION['module']['event'] == "1")
							{
								//print_r($LeaseAlert[0]['unit_id']);
								 $redirectURL = "tenant_list.php?u_id=".$LeaseAlert[0]['unit_id']."";
								if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER))
								{
									$redirectURL = "show_tenant.php";
								}?>
													
								<li>
                             		<a href="<?php echo  $redirectURL;?>">
										<div>
											<strong>Lease Expiry Alert </strong>
											
										</div>
									 <?php 
									 if(sizeof($LeaseAlert) > 0)
                                            { 
                                                for($m = sizeof($LeaseAlert) -1 ; $m > sizeof($LeaseAlert) -3; $m--)
                                                {
													
                                                    if(isset($LeaseAlert[$m]["tenant_id"]) )
                                                    {
                                                        //echo $msgCounter;
														$msgCounter++;
                                                    ?>
                                                       <div>wing ( <?php echo $LeaseAlert[$m]['wing'];?> )&nbsp; : &nbsp;<?php echo $LeaseAlert[$m]["unit_no"];  ?><span class="pull-right text-muted">
												<em><?php echo getDisplayFormatDate($LeaseAlert[$m]["end_date"]);?></em>
											</span>
										 </div>
                                                     <?php
                                                    }
                                                }
                                            }?>
                                        
                                       
									</a>
                                    
								</li>
								<?php
							}?>
							<!--<li class="divider"></li>
							<li>
								<a class="text-center" href="#">
									<strong>Read All Messages</strong>
									<i class="fa fa-angle-right"></i>
								</a>
							</li>-->
						</ul>
						<!-- /.dropdown-messages -->
					</li>
					<!-- /.dropdown -->
					<?php
				}
				?>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    
                    <ul class="dropdown-menu dropdown-user">
                        <!--<li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a>
                        </li>-->
						<?php if($_SESSION['role'] == "Super Admin" || $_SESSION['role'] == "Admin")
						{
							?>
                        	<li><a href="settings.php?as"><i class="fa fa-gear fa-fw"></i> Settings</a>
                        	</li>
                            <li class="divider"></li>
							<?php
						}
						?>
                        
                        <?php 
                    //    $SessionUser=  $_SESSION['View'];
			//print_r( $_SESSION);
			//echo $SessionUser;
			//if($SessionUser != "MEMBER")
			//{
				//?>
                         <li>

                                <a href="initialize.php?imp"><i class="fa   fa-home  fa-fw"></i><?php echo $societyName = $m_objHead_S->GetSocietyName($_SESSION['society_id'])?></a>
                                
                            </li>
                  <?php //}          ?>
                            <li class="divider"></li>
                        <li><a href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                </ul>
                <?php
                $sCurFileName = basename($_SERVER['PHP_SELF']);
                $bDocumentView = false;
                if($sCurFileName == "GDrive_view.php")
                {
                	$bDocumentView = true;
                } 
                ?>
                <ul 
                <?php 
                if($bDocumentView)
                	{ 
                		echo ' style="float:right;color:#039;font-size:13px;-webkit-padding-start: 40px !important;    margin-left: 3% !important;-webkit-margin-before: 1em !important;
    -webkit-margin-after: 1em !important;
    -webkit-margin-start: 0px !important;
    -webkit-margin-end: 0px !important;"';
					}
					else
					{
						echo 'style="float:right;color:#039;font-size:13px;"';
					}
    			?>
      class="nav navbar-top-links navbar-right">
                <?php echo "Welcome " .$_SESSION['name']." ! " ?>
                </ul>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->
            <?php
			$SessionUser=  $_SESSION['View'];
			//print_r( $_SESSION);
			//echo $SessionUser;
			if($SessionUser == "MEMBER")
			{
			?>

                <div class="navbar-default sidebar" role="navigation"  <?php if($bIsHide == true){ echo 'style="display:none;"';}else{echo 'style="font-size:10px;font-size:1.00vw;"';} ?>>
                    <div class="sidebar-nav navbar-collapse" style="line-height:4px;line-height:1.3vw;vertical-align:middle;">
                        <ul class="nav" id="side-menu">
                            <!--<li class="sidebar-search">
                                <div class="input-group custom-search-form">
                                    <input type="text" class="form-control" placeholder="Search...">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </li>-->
                            <li>
                                <a href="Dashboard.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                            </li>
                            <?php 
                            	if($_SESSION['role'] == "Member" || $_SESSION['role'] == "Admin Member")
                            	{
                            		?>
			                            <li>
			                                <!--<a href="view_member_profile_mem_edit.php"><i class="fa fa-table fa-fw"></i> Profile</a>-->
			                                <a href="view_member_profile.php?prf&id=<?php echo $_SESSION['owner_id'];?>&idd=<?php echo time();?>"><i class="fa fa-table fa-fw"></i> Profile</a>
			                                
			                            </li>
			                        <?php
			                    }
			                ?>
                            <?php 
							if($_SESSION["unit_id"] != "0")
							{
								?>
								<li>
									<a href="#">
									<i class="fa  fa-folder  fa-fw"></i>Bills and Payments
									<span class="fa arrow">
									</span>
									</a>
									<ul class="nav nav-second-level">
										<li>
											<a href="MaintenanceBill_m.php">Bills Ledger</a>
										</li>
										<li>
											<?php  $NEFTURL = "neft.php?SID=".base64_encode($_SESSION["society_id"])."&UID=".base64_encode($_SESSION['unit_id']); ?>
											<a href="<?php echo $NEFTURL; ?>">Record NEFT Payment</a>
										</li>
                                    </ul>
								</li>
								<?php
							}
							?>
                            <li>
                                <a href="Gallery.php"><i class="fa  fa-camera-retro fa-fw"></i> Photo Album</a>
                                
							<?php if($_SESSION['module']['event'] == "1")
							{
								?>
                            	<li class="active">
                               		<a href="events_view.php"><i class="fa  fa-calendar fa-fw"></i> Events</a>
                                </li>
								<?php
							}
							?>
							
							<?php if($_SESSION['module']['notice'] == "1")
							{
								?>
                            	<li class="active">
                               		<a href="notices.php?in=0"><i class="fa  fa-file-text-o fa-fw"></i> Notices</a>
                                </li>
								<?php
							}
							?>
                            <?php if($_SESSION['module']['notice'] == "1")
							{
								?>
                            	<li class="active"  style="font-size: 0.98em;">
                               		<a href="commitee.php"><i class="fa  fa-group fa-fw"></i> Committee Members</a>
                                </li>
								<?php
							}
							?>
							<?php if($_SESSION['module']['service'] == "1")
							{
								?>
								<li>
									<!--<a href="#">-->
                                    <li class="active">
                               		<a href="service_prd_reg_view.php?srm"><i class="fa  fa-search  fa-fw"></i> Service Provider</a>
                                </li>
									<!--<i class="fa  fa-search  fa-fw"></i> Service Provider
									<span class="fa arrow">
									</span>-->
									<!--</a>
									<ul class="nav nav-second-level">
										<li>
											<a href="service_prd_reg_view.php?srm">List of service providers</a>
										</li>
										<li>
											<a href="service_prd_reg_search.php?srm">Search Here</a>
										</li>
									</ul>
								</li>-->
								<?php
							}
							?>
							<?php if($_SESSION['module']['service_request'] == "1")
							{
								?>
                            	<li class="active">
                                	<a href="servicerequest.php?type=open"><i class="fa  fa-edit  fa-fw"></i>Service Request</a>
                                	<!-- /.nav-second-level -->
                            	</li>
								<?php
							}
							?>
                            <?php if($_SESSION['module']['classified'] == "1")
							{
								?>
								<li class="active">
									<a href="classified.php"><i class="fa  fa-tags  fa-fw"></i> Classifieds</a>
									<!-- /.nav-second-level -->
								</li>
								<?php
							}
							?>
                              <?php 	if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER))
												{ 	?>
								<li class="active">
									<a href="poll.php"><i class="fa  fa-lightbulb-o fa-fw"></i>Poll</a>
									<!-- /.nav-second-level -->
								</li>
								<?php
							}else
							{?>
								<li class="active">
									<a href="polls.php"><i class="fa  fa-lightbulb-o fa-fw"></i>Poll</a>
									<!-- /.nav-second-level -->
								</li>
								
							<?php }
							?>
                            <?php if($_SESSION['module']['forum'] == "1")
							{
								?>
							<!-- 	<li class="active">
									<a href="Forum.php"><i class="fa  fa-comments  fa-fw"></i> Forum</a>
					
								</li> -->
								<?php
							}
							?>
                            
							<?php if($_SESSION['module']['document'] == "1")
							{
							
							?>
                            	<li>
             
									<a href="#">
									<i class="fa  fa-book  fa-fw"></i> Documents
									<span class="fa arrow">
									</span>
									</a>
									<ul class="nav nav-second-level">
										<li>
											<a href="GDrive_view.php?Mode=1"><i class="fa fa-book fa-fw"></i> View Documents</a>
										</li>
										<li>
											<a href="document_maker.php?View=ADMIN"><i class="fa fa-file-o fa-fw"></i> Document Maker</a>
										</li>
									</ul>
								</li>
								<?php
							}
							?>
                            <?php if($_SESSION['module']['directory'] == "1")
							{
								?>
								<li>
									<a href="#">
									<i class="fa  fa-folder  fa-fw"></i> Directory
									<span class="fa arrow">
									</span>
									</a>
									<ul class="nav nav-second-level">
										<li>
											<a href="MemberListings.php">Member Listing</a>
										</li>
										<li>
											<a href="ProfessionalListings.php">Professional Listing</a>
										</li>
                                        <li>
											<a href="BloodGroupListings.php">Blood Group Listing</a>
										</li>
									</ul>
								</li>
								<?php
							}
							?>
                                                        
                            <li>
                                <a href="settings.php?as"><i class="fa  fa-cog  fa-fw"></i> Settings<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="cp.php?as">Change UserName/Password</a>
                                    </li>
                            	</ul>
                            
                            </li>

                            <li class="active">
                                <a href="tips_detail.php"><i class="fa  fa-question-circle fa-fw"></i> Help</a>
                                <!-- /.nav-second-level -->
                            </li>

                            <li class="active">
                                <a href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                                <!-- /.nav-second-level -->
                            </li>
                        </ul>
                    </div>
                    <!-- /.sidebar-collapse -->
                </div>
            <!-- /.navbar-static-side -->
            <?php }
			else if($SessionUser == "ADMIN")
			{ ?>
            <div class="navbar-default sidebar" role="navigation" <?php if($bIsHide == true){ echo 'style="display:none;"';} ?>>
                    <div class="sidebar-nav navbar-collapse">
                        <ul class="nav" id="side-menu">
                            
                            <li>
                                <a href="home_s.php"><i class="fa fa-dashboard fa-fw"></i> HOME</a>
                            </li>
                            
                            <li>
                                <a href="#"><i class="fa fa-wrench fa-fw"></i> Tools<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level"> 
                                <?php if($_SESSION['profile'][PROFILE_GENERATE_BILL] == 1)
								{ 	?>	                               
                                    <li>
                                        <a href="genbill.php">Generate Bill</a>
                                    </li>
                                 <?php
								}	
								if($_SESSION['profile'][PROFILE_SEND_NOTIFICATION] == 1)
								{ 	?>
                                    <li>
                                    	<a href="notification.php">Send Bill Notification</a>
                                    </li>
                               	<?php if($_SESSION['profile'][PROFILE_UPDATE_INTEREST] == 1)
								{	?>  
                                    <li>
                                        <a href="updateInterest.php">Update Bill Interest</a>
                                    </li>
								<?php
                                }	?>
                                    <li>
                                        <a href="BankAccountDetails.php">Bank and Cash Entry</a>
                                    </li>
                                <?php
								}	
									if($_SESSION['profile'][PROFILE_PAYMENTS] == 1)
									{
								?>                                
                                    <li>
                                        <a href="createvoucher.php">Journal Voucher</a>
                                    </li>
                                <?php
                                }	?>   
                                    <li>
                                        <a href="sendGeneralMsgs.php">Send General SMS</a>
                                    </li>
                                </ul>
                            
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fa  fa-building  fa-fw"></i>Society
                                    <span class="fa arrow">
                                    </span>
                                </a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="society.php?id=<?php echo $_SESSION['society_id'];?>&show&imp">View Society</a>
                                    </li>
                                    <li>
                                        <a href="list_member.php?scm">View Units</a>
                                    </li>
                                   <!-- <li>
                                        <a href="list_member.php?scm">Members List</a>
                                    </li>-->
                                    <?php if($_SESSION['role'] == "Super Admin" || $_SESSION['role'] == "Admin")
                                    { ?>
                                    	<li>
                                        	<a href="mem_rem_data.php?scm">Member Record Status</a>
                                    	</li>
                                    <?php }
                                    ?>
                                </ul>
                            
                            </li>
                            <!--<li class="active">
                            
                                <a href="Events_m.php"><i class="fa fa-files-o fa-fw"></i> Groups</a>
                            
                            </li>-->
                            <li>
                                <a href="reportmain.php"><i class="fa fa-wrench fa-fw"></i> Reports</a>
                                <!--<ul class="nav nav-second-level">
                                    <li>
                                        <a href="unit.php?imp">Units</a>
                                    </li>
                                    <li>
                                        <a href="reports.php?">Member Dues</a>
                                    </li>
                            	</ul> -->
                                
                            </li>
                            <!--<li>
                                <a href="#">
                                <i class="fa  fa-search  fa-fw"></i> Services
                                <span class="fa arrow">
                                </span>
                                </a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="service_prd_reg_view.php?srm">List of service providers</a>
                                    </li>
                                    <li>
                                        <a href="service_prd_reg_search.php?srm">Search Here</a>
                                    </li>
                            	</ul>
                            </li>-->
                            
                            <!--<li>
                                <a href="Events_m.php"><i class="fa  fa-calendar  fa-fw"></i> Events</a>
                             
                            </li>-->
                            
                            <li>
                                <a href="settings.php?as"><i class="fa  fa-cog  fa-fw"></i> Settings<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
									<?php if($_SESSION['role'] == "Super Admin" || $_SESSION['role'] == "Admin")
									{
										if($_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1)
										{
										?>
											<li>
												<a href="settings.php?as">Manage Masters</a>
											</li>
										<?php
										}
									}
									if($_SESSION['role'] == "Super Admin")
									{
										?>
										<li>
                                        	<a href="add_member_id.php?as">Member Login Code</a>
                                    	</li>
										<?php
									}
									?>
                                    <li>
                                        <a href="cp.php?as">Change UserName/Password</a>
                                    </li>
                                    <?php
									if($_SESSION['role'] == "Super Admin")
									{
									?>
                                        <li>
                                            <a href="client_details.php?client=<?php echo base64_encode($_SESSION['client_id']); ?>">View Client Details</a>
                                        </li>
                                        <?php
									}									
									?>
                            	</ul>
                            
                            </li>
                            <li>

                                <a href="tips_detail.php"><i class="fa fa-question-circle fa-fw"></i> Help</a>
                                
                            </li>
                            <li>

                                <a href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                                
                            </li>
                        </ul>
                    </div>
                </div>
                <?php } ?>
        </nav>

        <!-- Page Content -->
<!--        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">Blank</h1>
                    </div>
                </div>
            </div>
        </div>-->
    </div>
    
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="dist/js/sb-admin-2.js"></script>
    <script> 
		var msgCounter =  "<?php echo $msgCounter?>";
		
		if(localStorage.getItem("msgCounter") == null)
		{
			localStorage.setItem("msgCounter","<?php echo $msgCounter?>");	
			if(localStorage.getItem("msgCounter") == 0)
			{
				msgCounter = 0;	
				document.getElementById('counter').innerHTML ='';
				document.getElementById('counter').style.visibility = 'hidden';
			}
			else
			{
				document.getElementById('counter').innerHTML = msgCounter;
				document.getElementById('counter').style.visibility = 'visible';
			}
		}
		else if(localStorage.getItem("msgCounter") == 0)
		{
			msgCounter = 0;	
			document.getElementById('counter').innerHTML ='';
			document.getElementById('counter').style.visibility = 'hidden';
		}
		else if(msgCounter > 0)
		{
			document.getElementById('counter').innerHTML = msgCounter;
			document.getElementById('counter').style.visibility = 'visible';
		}
		
		</script>
    	
	<?php include_once("analyticstracking.php"); ?>
<?php //} ?>