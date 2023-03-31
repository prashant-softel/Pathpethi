<?php include_once("classes/head.class.php"); 
$m_objHead_S = new head($m_dbConnRoot);
//echo "trace2.3";
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
	padding-left:100px;
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
                <table><tr><td style="text-align:center;font-size:50%"><!--<a class="navbar-brand" href="home.php" style="color:#990000;">--><img src="images/logo_t.gif" width="60%" /><!--</a>--></td><td style="vertical-align:middle"><b>Way2Society.com - Housing Society Social & Accounting Software</b><br /><font color="#039"><a href="initialize.php?imp"><?php if(isset($_SESSION['society_id']) && $_SESSION['society_id'] <> 0) { echo $societyName = $m_objHead_S->GetSocietyName($_SESSION['society_id']) . ' - [' . $_SESSION['desc'] . ']';} ?></a></font></td></tr></table>
            </div>
            <!-- /.navbar-header -->

                    
            <ul class="nav navbar-top-links navbar-right" style="margin-top:6px">
            	<ul>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-envelope fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                        <li>
                            <a href="#">
                                <div>
                                    <strong>Unread Notice</strong>
                                    <span class="pull-right text-muted">
                                        <em>Yesterday</em>
                                    </span>
                                </div>
                                <div>Society Meeting will be held on 10th May, 2015 at ...</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <strong>Unread Complaint</strong>
                                    <span class="pull-right text-muted">
                                        <em>Yesterday</em>
                                    </span>
                                </div>
                                <div><em>Reply: on 6th May, 2015 Security staff was sleeping while he was on Duty...</em></div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <strong>Unread Event</strong>
                                    <span class="pull-right text-muted">
                                        <em>Yesterday</em>
                                    </span>
                                </div>
                                <div>Holi Celebration at society garden...</div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="text-center" href="#">
                                <strong>Read All Messages</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-messages -->
                </li>
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-tasks fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-tasks">
                        <li>
                            <a href="#">
                                <div>
                                    <p>
                                        <strong>Task 1</strong>
                                        <span class="pull-right text-muted">40% Complete</span>
                                    </p>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
                                            <span class="sr-only">40% Complete (success)</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <p>
                                        <strong>Task 2</strong>
                                        <span class="pull-right text-muted">20% Complete</span>
                                    </p>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%">
                                            <span class="sr-only">20% Complete</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <p>
                                        <strong>Task 3</strong>
                                        <span class="pull-right text-muted">60% Complete</span>
                                    </p>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%">
                                            <span class="sr-only">60% Complete (warning)</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <p>
                                        <strong>Task 4</strong>
                                        <span class="pull-right text-muted">80% Complete</span>
                                    </p>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                            <span class="sr-only">80% Complete (danger)</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="text-center" href="#">
                                <strong>See All Tasks</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-tasks -->
                </li>
                
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a>
                        </li>
						<?php if($_SESSION['role'] == "Super Admin" || $_SESSION['role'] == "Admin")
						{
							?>
                        	<li><a href="settings.php?as"><i class="fa fa-gear fa-fw"></i> Settings</a>
                        	</li>
							<?php
						}
						?>
                        <li class="divider"></li>
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
                <ul style="float:right;color:#039;font-size:13px;margin-right:7%" class="nav navbar-top-links navbar-right">
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

                <div class="navbar-default sidebar" role="navigation">
                    <div class="sidebar-nav navbar-collapse">
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
                            <li>
                                <a href="view_member_profile_mem_edit.php"><i class="fa fa-table fa-fw"></i> Profile</a>
                            </li>
                            <?php 
							if($_SESSION["unit_id"] != "0")
							{
							?>
	                            <li>
    	                            <a href="MaintenanceBill_m.php"><i class="fa fa-edit fa-fw"></i> Billing</a>
        	                    </li>
                            <?php
							}
							?>
                            <li>
                                <a href="#"><i class="fa  fa-camera-retro fa-fw"></i> Photo Album<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="Gallery.php">Holi</a>
                                    </li>
                                    <li>
                                        <a href="Gallery.php">Diwali</a>
                                    </li>
                                    <li>
                                        <a href="Gallery.php">Ganesh Chaturthi</a>
                                    </li>
                                </ul>
                                <!-- /.nav-second-level -->
                            </li>
                            <li class="active">
                                <a href="events_view.php"><i class="fa  fa-calendar fa-fw"></i> Events</a>
                                <!-- /.nav-second-level -->
                            </li>
                            <li class="active">
                                <a href="notices.php?in=0"><i class="fa  fa-file-text-o fa-fw"></i> Notices</a>
                                <!-- /.nav-second-level -->
                            </li>
                            <li>
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
                            </li>
                            <li class="active">
                                <a href="Complaints.php?cm=0"><i class="fa  fa-edit  fa-fw"></i> Complaints</a>
                                <!-- /.nav-second-level -->
                            </li>
                            
                            <li class="active">
                                <a href="Ads.php"><i class="fa  fa-tags  fa-fw"></i> Classifields</a>
                                <!-- /.nav-second-level -->
                            </li>
                            
                            <li class="active">
                                <a href="Forum.php"><i class="fa  fa-comments  fa-fw"></i> Forum</a>
                                <!-- /.nav-second-level -->
                            </li>
                            
                            <li class="active">
                                <a href="Document_view.php"><i class="fa fa-book fa-fw"></i> Documents</a>
                                <!-- /.nav-second-level -->
                            </li>
                            
                            <li class="active">
                                <a href="Directory.php"><i class="fa  fa-folder  fa-fw"></i> Directory</a>
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
            <div class="navbar-default sidebar" role="navigation">
                    <div class="sidebar-nav navbar-collapse">
                        <ul class="nav" id="side-menu">
                            
                            <li>
                                <a href="home_s.php"><i class="fa fa-dashboard fa-fw"></i> HOME</a>
                            </li>
                            
                            <li>
                                <a href="#"><i class="fa fa-wrench fa-fw"></i> Tools<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="genbill.php?mm">Generate Bill</a>
                                    </li>
                                    <li>
                                        <a href="BankAccountDetails.php">Cheque Entry</a>
                                    </li>
                                    
                                    <li>
                                        <a href="updateInterest.php">Update Interest</a>
                                    </li>
                                    
                                    <li>
                                        <a href="createvoucher.php">Create Voucher</a>
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
                                        <a href="unit_search.php?imp">View Units</a>
                                    </li>
                                    <li>
                                        <a href="list_member.php?scm">Members List</a>
                                    </li>
                                    <li>
                                        <a href="mem_rem_data.php?scm">Member Record Status</a>
                                    </li>
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
										?>
										<li>
											<a href="settings.php?as">Manage Masters</a>
										</li>
										<li>
                                        	<a href="add_member_id.php?as">Member Login Code</a>
                                    	</li>
										<?php
									}
									?>
                                    <li>
                                        <a href="cp.php?as">Change Password</a>
                                    </li>
                            	</ul>
                            
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
    <?php include_once("analyticstracking.php"); ?>
<?php //} ?>