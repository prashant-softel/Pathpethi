<?php 
if(!isset($_SESSION)){ session_start(); }
//include_once("classes/include/dbop.class.php");
include_once("classes/include/check_session.php");
include_once("classes/head_s.class.php");
include_once("header.php");
include_once("classes/tips.class.php");
$obj_tips=new tips($m_dbConnRoot,$m_dbConn);
$SessionUser=  $_SESSION['View'];
if($_REQUEST["View"] == "ADMIN")
{
	$_SESSION["View"] = "ADMIN";
	//$_SESSION[name] = "SUPER ADMIN";
}
else if($_REQUEST["View"] == "MEMBER")
{
	$_SESSION["View"] = "MEMBER";
	//$_SESSION[name] = "SUSHIN SHETTY";
	
}
if($_SESSION['role'] == 'Master Admin')
{
include_once("includes/header2.php");
}
else
{
	include_once("includes/header.php");
}
if($SessionUser <> '' && $SessionUser != "ADMIN")
{


//if(isset($SessionUser))
//{
include_once("RightPanel.php");
}
else if($SessionUser == "ADMIN")
{
	include_once("RightPanel_admin.php");
}
$m_objHead_S = new head_s($m_dbConn);

include_once("datatools.php");
$bIsHide = bIsReportOrValidationPage($scriptName);
$TipsDetails = $obj_tips->RecordsCount();
?>
<script>
var aryTips=[];
var currentTip=-1;
/*localStorage.setItem('dbname', "<?php //echo $_SESSION['dbname']; ?>");
$(window).bind('storage', function (e) 
{
//alert("Old : " + e.originalEvent.key + " : New : " + e.originalEvent.newValue);
if(e.originalEvent.key != e.originalEvent.newValue)
{
	window.location.href = "initialize.php?imp";
}
    //console.log(e.originalEvent.key, e.originalEvent.newValue);
});*/

window.onfocus = function() {
    //focused = true; 
	//alert(localStorage.getItem('login'));
	if(localStorage.getItem('login') == null || localStorage.getItem('login') <= 0)
	{
		window.location.href = 'logout.php';
	}
	else
	{
		var dbName = localStorage.getItem('dbname');
		var dbNameSession = "<?php echo $_SESSION['dbname']; ?>";
		if(dbName != null && dbName.length > 0 && dbName != dbNameSession)
		{
			//alert(dbName + ":" + dbNameSession);
			window.location.href = 'initialize.php';
		}
	}
};
function Next()
	{
		//alert(currentTip);
		//alert("Next");
		currentTip++;
		//aryTips[aryTips.length] = tipsLenth;
		if( currentTip > aryTips.length -1 )
		{
			currentTip = 0;
		}
		//alert(currentTip);
		var obj = aryTips[currentTip];
		document.getElementById('view_more').innerHTML= "<a href='#' onClick='window.open(\"tips_detail.php?id="+obj['id'] + "\")'>View More >></a>";
		document.getElementById('show_tips').innerHTML="<b>"+obj['title']+" &nbsp; :</b><br>"+obj['desc'];
		
	}
	function Preview()
	{
		//alert(currentTip);
		//alert("Next");
		currentTip--;
		//aryTips[aryTips.length] = tipsLenth;
		if( currentTip < 0 )
		{
			currentTip = aryTips.length -1;
		}
		//alert(currentTip);
		var obj = aryTips[currentTip];
		document.getElementById('show_tips').innerHTML="<b>"+obj['title']+" &nbsp; :</b><br>"+obj['desc'];
		document.getElementById('view_more').innerHTML= "<a href='#' onClick='window.open(\"tips_detail.php?id="+obj['id'] + "\")'>View More >></a>";
	}
</script>
<style>
.content_div
{ 
width:100%;
float:left;
}
.marque_div{
    float: left;
    width: 100%;
    border: solid 1px #eee;
    margin-bottom: 20px;
    border-radius: 35px;
}
</style>
</head>
<body>
<?php
if(isset($_REQUEST['hm'])){$cls0 = 'first-current';}else{$cls0 = '';}
if(isset($_REQUEST['prm'])){$cls01 = 'current';}else{$cls01 = '';}
if(isset($_REQUEST['mm'])){$cls1 = 'current';}else{$cls1 = '';}
if(isset($_REQUEST['imp'])){$cls11 = 'current';}else{$cls11 = '';}
if(isset($_REQUEST['scm'])){$cls2 = 'current';}else{$cls2 = '';}
if(isset($_REQUEST['grp'])){$cls22 = 'current';}else{$cls22 = '';}
if(isset($_REQUEST['srm'])){$cls3 = 'current';}else{$cls3 = '';}
if(isset($_REQUEST['ev'])){$cls33 = 'current';}else{$cls33 = '';}
 if(isset($_REQUEST['as'])){$cls4 = 'current';}else{$cls4 = '';}

if(!isset($_SESSION['admin'])){$cls5 = 'last-current';}else{$cls5 = '';}

?>

					<!--<font color="#000" style="font-size:12px;padding-right:180px;float:right;margin-top:10px" ><a href="home_s.php?hm" style="color:#FF8000;font-size:28px"><img src="images/logo.png" width="100%" /></a>-->
                    <!--Welcome <b><?php //echo $_SESSION['sadmin'];?></b>&nbsp;&nbsp;<br>-->
                    <?php //$societyName = $m_objHead_S->GetSocietyName($_SESSION['society_id']);
					//if($societyName <> '')
					{
					?>
                    	 <b><?php //echo $m_objHead_S->GetSocietyName($_SESSION['society_id']);?></b>
                    	<!--<a href="defaults.php" style="color:#00CCFF">[Change]</a><br>-->
                    <?php
					}
					//else
					{
					?>
                    	<!--<a href="defaults.php">[Settings]</a><br>-->
                    <?php
					}
					?>
</font>

<!-- header -->
	<div id="header">
		<div class="container" style="text-align:center;">
        
                            <?php
//$SessionUser=  $_SESSION['View'];
//echo $SessionUser;
//echo "trace2";
if($_SESSION['View'] == "ADMIN")
{
?>
			
               
		<!--	<div class="row-2" style="text-align:center">
				<?php //include_once "../includes/menu.php";?>

                
                <div class="nav-box">
                    <div class="left" style="width:100%;text-align:center">
                        <div class="right" style="width:100%;text-align:center">
                            <ul id="chromemenu" class="top-navigation" style="text-align:center">
                                <li><a href="home_s.php?hm" id="<?php //echo $cls0;?>" class="first"><em><b style="height:50px;text-align:center;margin-top:5px;">HOME</b></em></a></li>
                                <li><a href="javascript:void(0);" id="<?php //echo $cls01;?>" rel="dropmenu0"><em><b style="height:50px;text-align:center;margin-top:5px;">PERMISSIONS</b></em></a></li>
                                <li><a href="javascript:void(0);" id="<?php //echo $cls1;?>" rel="dropmenu1"><em><b style="height:50px;text-align:center;margin-top:5px">TOOLS</b></em></a></li>
                                <li><a href="javascript:void(0);" id="<?php //echo $cls11;?>" rel="dropmenu11"><em><b style="height:50px;text-align:center;margin-top:5px">SOCIETY</b></em></a></li>
                                <li><a href="list_society_group.php?grp" id="<?php //echo $cls22;?>"><em><b style="height:50px;text-align:center;margin-top:5px">GROUP</b></em></a></li>
                                <li><a href="javascript:void(0);" id="<?php //echo $cls2;?>" rel="dropmenu2"><em><b style="height:50px;text-align:center;margin-top:5px">REPORTS</b></em></a></li>                  
                                <li><a href="javascript:void(0);" id="<?php //echo $cls3;?>" rel="dropmenu3"><em><b style="height:50px;text-align:center;margin-top:5px">SERVICES</b></em></a></li>
                                <li><a href="events_view_as.php?ev" id="<?php //echo $cls33;?>"><em><b style="height:50px;text-align:center;margin-top:5px">EVENTS</b></em></a></li>
                                <li><a href="javascript:void(0);" id="<?php //echo $cls4;?>" rel="dropmenu4"><em><b style="height:50px;text-align:center;margin-top:5px">SETTINGS</b></em></a></li>
                                <li><a href="logout_s.php" id="<?php //echo $cls0;?>"><em><b style="height:50px;text-align:center;margin-top:5px">LOGOUT</b></em></a></li>
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
													
                                                    
                                                    
<!--<div id="dropmenu0" class="dropmenudiv" style="width:170px;">
<a href="del_control_sadmin.php?prm" style="font-size:12px;text-align:center;height:30px;margin-top:10px">Permission for delete</a>
</div>

<div id="dropmenu1" class="dropmenudiv" style="width:170px;">
<a href="genbill.php?mm" style="font-size:12px;text-align:center;height:30px;margin-top:10px">Generate Bill</a>
<a href="BankAccountDetails.php" style="font-size:12px;text-align:center;height:30px;margin-top:10px">Issue Cheque</a>
<a href="BankAccountDetails.php" style="font-size:12px;text-align:center;height:30px;margin-top:10px">Deposit Cheque/Neft</a>
<a href="updateInterest.php" style="font-size:12px;text-align:center;height:30px;margin-top:10px">Update Interest </a>-->
<!--
<a href="desg.php?mm">Designation</a>
<a href="state_master.php?mm">State Master</a>
<a href="bill_period.php?mm">Bill Period</a>
<a href="bill_year.php?mm">Bill Year<>-->
<!--<a href="billmaster.php?mm">Bill Master</a>
<a href="genbill.php?mm">Generate Bill</a>
<a href="BankAccountDetails.php?mm">Incoming Cheque</a>
<a href="BankAccountDetails.php?mm">Issue Cheque</a>-->
<!--<a href="bankdetails.php?mm">Bank Details</a>
<a href="group.php">Group</a>
<a href="account_category.php">Account Category</a>
<a href="account_subcategory.php">Ledger</a>-->
<!--</div>-->

<!--<div id="dropmenu11" class="dropmenudiv" style="width:170px;">
<a href="society.php?id=<?php //echo $_SESSION['society_id'];?>&show&imp" style="font-size:12px;text-align:center;height:30px;margin-top:10px">View Society</a>
<a href="wing.php?imp"  style="font-size:12px;text-align:center;height:30px;margin-top:10px">View Wings</a>
<a href="unit.php?imp"  style="font-size:12px;text-align:center;height:30px;margin-top:10px">View Units</a>
<a href="list_member.php?scm" style="font-size:12px;text-align:center;height:30px;margin-top:10px">Members List</a>
<a href="mem_rem_data.php?scm" style="font-size:12px;text-align:center;height:30px;margin-top:10px">Members Records Status</a>
</div>
<div id="dropmenu2" class="dropmenudiv" style="width:170px;">
    <a href="unit.php?imp" style="font-size:12px;text-align:center;height:30px;margin-top:10px">Unit</a>
    <a href="reports.php?&sid=<?php //echo $_SESSION['society_id']; ?> " style="font-size:12px;text-align:center;height:30px;margin-top:10px">Member Dues</a>
    
</div>

<div id="dropmenu3" class="dropmenudiv" style="width:170px;">
<a href="service_prd_reg_view.php?srm" style="font-size:12px;text-align:center;height:30px;margin-top:10px">List of service provider</a>
<a href="service_prd_reg_search.php?srm" style="font-size:12px;text-align:center;height:30px;margin-top:10px">Search here</a>
</div>

<div id="dropmenu4" class="dropmenudiv" style="width:190px;">
<a href="settings.php?as" style="font-size:12px;text-align:center;height:30px;margin-top:10px">Manage Masters</a>
<a href="cp.php?as" style="font-size:12px;text-align:center;height:30px;margin-top:10px">Change Password</a>
<a href="add_member_id.php?as" style="font-size:12px;text-align:center;height:30px;margin-top:10px">Search login id & password</a>
</div>-->

<?php
}

?>
 <div class="panel-body" <?php if($bIsHide == true){ echo 'style="display:none;"';}else{echo 'style="width:50%;height:40px;margin-top:0px;margin-left:40px;;margin-left:4vw;width:50.00vw;height:4.vw" ';} ?>>
                            <!-- Nav tabs -->
                            <ul class="nav nav-pills" style="height:20px;height:2vw">
                                 <?php 
								// echo $_SESSION["View"] ;
								 if($_SESSION["View"] == "MEMBER")
								{
									?>
									<li class="active">
								<?php 
								}
								else if($_SESSION["View"] == "ADMIN")
								{
									?>
									
									<li>
								<?php
								}
								
								if($_SESSION['role'] <> 'Master Admin')
								{
								?> 
                                <a href="#home-pills" data-toggle="tab" onClick="ShowMemberView()" id="0">My Society</a>
                                <?php } ?>
                                </li>
                                <?php
								//print_r($_SESSION); 
                                if($_SESSION["unit_id"] == "0" || $_SESSION['role'] == "Admin Member")
								{
									?>
                                	<?php if($_SESSION["View"] == "ADMIN")
												{
													?>
													<li class="active">
												<?php 
												}
												else if($_SESSION["View"] == "MEMBER")
												{
													?>
                                                    
													<li>
												<?php
                                                }
												
										if($_SESSION['role'] <> 'Master Admin')
										{
												?> 
                                    <a href="#profile-pills" data-toggle="tab" onClick="ShowAdminView()" id="1">Accounting / Admin</a>
                                    <?php } ?> 
                                	</li>
                                    <?php 
								}
								?>
                            </ul>
                            
</div>
<?php 
if(sizeof($TipsDetails) > 0)
{
$script   = $_SERVER['SCRIPT_NAME'];
$pos = strrpos($script, '/');
	$scriptName = substr($script, ($pos + 1));
	if($scriptName=='home_s.php' || $scriptName=='Dashboard.php' )
	{
  if($_SESSION['View']==ADMIN)
  {?>
  <br>
<div style="width:98%; margin-left:1%; float:left">
<?php }
else {?>
<br>
<br><br>
<div style="width:75%; margin-left:1%;">
<?php }
?>

<div class="col-lg-12" >
        <div  style="font-size: small;">
            <div class="panel panel-info" id="panel" style="font-size: small; background-color: #F2FBFC; padding-right:15px">
            <table id="tips">
            <tr align="right"> <td   align="right" colspan="3">
              
               <i class="fa fa-angle-double-left" style="font-size:10px;font-size:1.35vw" onClick="Preview(this.value)"></i>
               &nbsp;&nbsp;&nbsp;
               <i class="fa fa-angle-double-right" style="font-size:10px;font-size:1.35vw" onClick="Next(this.value)"></i>
               </td></tr>
            <tr><td style="width:80px;">
            <!--<i class="fa fa-lightbulb-o" style="font-size:10px;font-size:3.75vw"></i>-->
            <img src="images/bulb.png" style="width:50px; margin-top: -15px;">
            </td>
            
            <td style="margin-bottom:none" colspan="2">
            <?php for($i=0;$i<sizeof($TipsDetails);$i++)
			{
				//$TipsDetails[$i]['desc'] = preg_replace("/<img [^>]+\>/i ", "", $TipsDetails[$i]['desc']);
					$TipsDetails[$i]['desc'] = strip_tags($TipsDetails[$i]['desc']); 
				if(strlen($TipsDetails[$i]['desc']) >220)
				{
				$TipsDetails[$i]['desc']= substr($TipsDetails[$i]['desc'],0,220) . '...'; 
				 }
				else
				{
					$TipsDetails[$i]['desc']= $TipsDetails[$i]['desc'];
				}
				$tipAry = json_encode(array("id" =>  $TipsDetails[$i]['id'], "title" =>  $TipsDetails[$i]['atr_title'], "desc" =>  $TipsDetails[$i]['desc']));
				?>
            <script>
							//var obj=[];
							//obj['id']='<?php// echo $TipsDetails[$i]['id'];?>'; 
							//obj['atr_title']='<?php //echo $TipsDetails[$i]['atr_title'];?>'; 
							//obj['disc']='<?php //echo $TipsDetails[$i]['disc'];?>'; 
						 //aryTips.push(obj);
						 aryTips.push((<?php echo $tipAry;?>));
						 console.log(aryTips[0]);
							//aryTips.push(obj);
			</script>
            <?php }
			?>
              <p style="margin-left:1%;text-align:justify;padding-bottom:0px; margin-top:-15px;" id="show_tips"></p>
              <!--  <p id="view_more"></p>-->
               
              </td></tr>
              <tr><td></td>
               <td align="left"></td>
              <td id="view_more" align="right" style="float: right;margin-top: -30px;margin-right: 1%;">
               <script>Next();</script>
               </td></tr>
                            </table>
            </div>
     </div>
  </div>
  </div>
<?php }
}?>
<br />
<script type="text/javascript">
	cssdropdown.startchrome("chromemenu");
	 
	function ShowMemberView(SelectedTab)
	{
		
		window.location.href = "Dashboard.php?View=MEMBER";
		//location.reload(true);
	}
	function ShowAdminView(SelectedTab)
	{
		
		
		window.location.href = "home_s.php?View=ADMIN";
		
		//location.reload(true);
	}
</script>