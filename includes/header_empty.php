<?php if(!isset($_SESSION)){ session_start(); } ?>
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
                <table><tr><td style="text-align:center;font-size:50%"><!--<a class="navbar-brand" href="home.php" style="color:#990000;">--><img src="images/logo.png" width="60%" /><!--</a>--></td><td style="vertical-align:middle"><b>Way2Society.com - Housing Society Social & Accounting Software</b></td></tr></table>
            </div>
            <!-- /.navbar-header -->
			<ul class="nav navbar-top-links navbar-right" style="margin-top:6px">
				<ul style="float:right;color:#039;font-size:13px;margin-right:15%" class="nav navbar-top-links navbar-right">
                		
						<?php 
						if(isset($_SESSION['name'])){echo "Welcome " .$_SESSION['name']." ! ";} ?>
                	</ul>
            	<ul>
                	<li>
                    <?php
                    if(isset($_SESSION['name'])){?>
						<a href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                	<?php }?>
                    </li>
					
				</ul>
			</ul>
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
<?php //} ?>