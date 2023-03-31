<?php include_once("classes/head.class.php"); 
include_once("../classes/dbconst.class.php");
$m_objHead_S = new head($m_dbConnRoot,$m_dbConn);
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
	justify-content: center;
	padding-left:100px;
	padding-right:100px;
	
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
                <table><tr><td style="text-align:center;font-size:50%" onclick="window.location.href='<?php echo $URL; ?>'"><!--<a class="navbar-brand" href="home.php" style="color:#990000;">--><a href="<?php echo $URL; ?>"><img width="60%" src="images/logo_t.gif" /></a><!--</a>--></td><td style="vertical-align:middle"><b>Way2Society.com - Housing Society Social & Accounting Software</b><br /><font color="#039"><a href="initialize.php?imp"><?php if(isset($_SESSION['society_id']) && $_SESSION['society_id'] <> 0) { echo $societyName = $m_objHead_S->GetSocietyName($_SESSION['society_id']) . ' - [' . $_SESSION['desc'] .']';} ?></a>				
                </font></td></tr></table>                                                                                 
            </div>
            
            <ul class="nav navbar-top-links navbar-right" style="margin-top:6px">
            	<ul style="float:right;color:#039;font-size:13px;" class="nav navbar-top-links navbar-right">
                <?php echo "Welcome " .$_SESSION['name']." ! " ?>
                </ul>
                <ul>
                    <li><a href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                    </li>
                </ul>                
            </ul> 
             <div class="navbar-default sidebar" role="navigation"  <?php if($bIsHide == true){ echo 'style="display:none;"';}else{echo 'style="font-size:10px;font-size:1.00vw;"';} ?>>
                    <div class="sidebar-nav navbar-collapse" style="line-height:4px;line-height:1.3vw;vertical-align:middle;">
                        <ul class="nav" id="side-menu">
                        <li>
                                <a href="client.php"><i class="fa fa-th-list fa-fw"></i> Client List</a>
                            </li>
                            <li>
                                <a href="view_tips.php"><i class="fa fa-lightbulb-o fa-fw"></i> Add Tips</a>
                            </li>
                            <li>
                                <a href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                            </li>
                            </ul>
                            </div>
                            </div>
    	</nav>                               
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