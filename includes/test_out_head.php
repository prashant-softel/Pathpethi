<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Society Software</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta name="description" content="Contact us page - free business website template available at TemplateMonster.com for free download."/>
<link href="../csss/style_test_out.css" rel="stylesheet" type="text/css" />
<link href="../csss/layout.css" rel="stylesheet" type="text/css" />
<script src="jsss/maxheight.js" type="text/javascript"></script>
<!--[if lt IE 7]>
	<link href="ie_style.css" rel="stylesheet" type="text/css" />
<![endif]-->

<link rel="stylesheet" type="text/css" href="cssss/chromestyle.css" />
<link rel="stylesheet" type="text/css" href="cssss/chromestyle.css" />

</head>
<body>
<?php
if(isset($_REQUEST['md'])){$cls1 = 'first-current';}else{$cls1 = '';}

if(isset($_REQUEST['sd'])){$cls2 = 'current';}else{$cls2 = '';}
if(isset($_REQUEST['cd'])){$cls3 = 'current';}else{$cls3 = '';}
if(isset($_REQUEST['od'])){$cls4 = 'current';}else{$cls4 = '';}
if(isset($_REQUEST['ccd'])){$cls5 = 'current';}else{$cls5 = '';}

if(isset($_REQUEST['bd'])){$cls6 = 'last-current';}else{$cls6 = '';}
?>

<!-- header -->
	<div id="header">
		<div class="container">
			<div class="row-1">
				
                <div class="logo" style="width:930px; margin-top:20px;" align="center">
                <font color="#FF8000" size="+2"><b>Society Software</b></font>
                </div>
                
			</div>
			<div class="row-2" align="center">
                <!-- nav box begin -->
                <div class="nav-box">
                    <div class="left">
                        <div class="right">
                            <ul id="chromemenu" class="top-navigation">
                                <li><a href="member_main_new1.php?md&idd=<?php echo time();?>" id="<?php echo $cls1;?>" class="first"><em><b style="width:120px;">Member Details</b></em></a></li>
                                <li><a href="mem_spouse_details_new1.php?sd&idd=<?php echo time();?>" id="<?php echo $cls2;?>"><em><b style="width:120px;">Spouse Details</b></em></a></li>
                                <li><a href="mem_child_details_new1.php?cd&idd=<?php echo time();?>" id="<?php echo $cls3;?>"><em><b style="width:120px;">Child Details</b></em></a></li>
                                <li><a href="mem_other_family_new1.php?od&idd=<?php echo time();?>" id="<?php echo $cls4;?>"><em><b style="width:120px;">Other Details</b></em></a></li>
                                <li><a href="mem_car_parking_new1.php?ccd&idd=<?php echo time();?>" id="<?php echo $cls5;?>"><em><b style="width:120px;">Car Details</b></em></a></li>
                                <li><a href="mem_bike_parking_new1.php?bd&idd=<?php echo time();?>" id="<?php echo $cls6;?>" class="last"><em><b style="width:120px;">Bike Details</b></em></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- nav box end -->
			</div>
		</div>
	</div>
<!-- content -->
	<div id="content" align="center">
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