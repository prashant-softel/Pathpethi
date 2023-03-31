<?php include_once "ses_set_s.php"; 
?>
<?php include_once("includes/head_s.php");
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/utility.class.php");
$obj_AdminPanel = new CAdminPanel($m_dbConn);
$obj_utility = new utility($m_dbConn);
//echo "<br>member";
//print_r($_SESSION["member_details1"]);
//echo "<br>sadmin";
//print_r($_SESSION["sadmin_details1"]);
//echo "<br>admin";
//print_r($_SESSION["admin_details1"]);
//*echo '<br>' . round(1.75 * 2) / 2; 

?>
<html>
<head>
	<style>
		.main_block{
			width:33%;
			border:0px solid #000;
			text-align:center;
			vertical-align:top;
			border-radius:15px;
			height:175px;
		}
	.main_div{
			background-color:#FFFFFF;
			border-radius:15px;
			width:80%;
			border:1px solid #333;
			margin:auto;
			min-height:100%;
			height:175px;
			box-shadow: 8px 8px 7px #888888;
		}
	.main_head{
			/*background:#990000;*/
			border-top-left-radius:15px;
			border-top-right-radius:15px;
			color:#000;
			/*font-size:16px;*/
			font-weight:bold;
			padding:3px;
			padding-right:10px;
			text-align:right;
		 	height:10px;
			text-decoration:underline;
		}
	.main_data{
			background:none;
			color:#000;
			/*font-size:12px;*/
			text-align:center;
			height:81px;
		}
	.main_footer{
			background:#990000;
			border-bottom-left-radius:12px;
			border-bottom-right-radius:12px;
			color:#00F;
/*			font-size:12px;*/
			font-weight:bolder;
			text-align:center;
			height:30px;
			display:table;
			width:100%;
		}
		
	.main_footer, a{
			
		}
	 .Details
	 {
		 color:#FFF;
	 }
	</style>
    <script>
    function ShowMemberView(SelectedTab)
	{
		window.location.href = "Dashboard.php?View=MEMBER";
		//location.reload(true);
		
	}
	
	function ShowAdminView(SelectedTab)
	{
		//alert("test");
		
		
		window.location.href = "home_s.php?View=ADMIN";
//		//location.reload(true);
	}
	</script>
</head>
<body>

<center>
<br>
<br>

<table style="width:100%;display:none;width:75vw" id="table1">
	<tr>
    	<td style="width:33%">
        	<div class="col-lg-3 col-md-6" style="width:100%">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                            <table style="width:100%">
                                <tr>
                                <td>                                                                    
                                <i class="fa fa-inr fa-5x" style="font-size:10px;font-size:3.75vw"></i>
                                </td>
                                <td style="text-align:right;font-size:150%;vertical-align:middle;font-size:1.25vw">
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CASH&nbsp;&&nbsp;BANK&nbsp;&nbsp
                                </td>
                                </tr>
                                </table>
                            
                         	<div class="col-xs-9 text-right" style="width:100%">                                    
                            
							<?php
								$arBankDetails = $obj_AdminPanel->GetBankAccountAndBalance(3);
			    	$iCounter = 0;
                                if($arBankDetails <> '')
                                {
									 ?>
                                   <table style="width:100%;">
                                   <?php
                                   foreach($arBankDetails as $arData=>$arvalue)
                                   {
                                       $len = strlen($obj_AdminPanel->GetLedgerNameFromID($arvalue["LedgerID"]));
                                       
                                       $BankName =  ($len > 15) ? (substr($obj_AdminPanel->GetLedgerNameFromID($arvalue["LedgerID"]), 0, 15) . '...') : $obj_AdminPanel->GetLedgerNameFromID($arvalue["LedgerID"]);
                                       
                                       $receipts =$arvalue["receipts"];
                                       $payments = $arvalue["payments"]; 
                                       $BalAmount = $receipts - $payments;
                                       ?>
                                       <div  class="huge" style="font-size:30px"><tr><td style="width:60%;text-align:left;font-size:1.00vw;"><?php  echo $BankName ?></td><td style="width:5%;">:</td><td style="font-weight:bold; width:35%; text-align:right;;font-size:1.00vw"><?php  echo number_format($BalAmount,2); ?></td></tr>
                                       </div>
                                       <?php
									   
									   $iCounter++;
							   			if($iCounter >= 3)
							   			{
								   			break;
							   			}
										
                                    }
									  echo "</table>";
								}
									$ReqRows =  3 - $iCounter;
                                    ?>
									<div style="color:#337BB7">
								   <table style="width:100%;">
								   <tr>
								   <?php 
								   for($ICnt = 1; $ICnt <= $ReqRows; $ICnt++)
								   {
									?>
								   <td style="width:60%;text-align:left;color:#337BB7;font-size:1.00vw;">No Data
								   </td>
								   </tr>
								   <?php 
								   }
								   ?>
                                   </table></div>
                                    <?php
                              // }
							?>
                             </div>
                            </div>
                			</div>
                    	
                        
                        <a href="BankAccountDetails.php">
                            <div class="panel-footer">
                                <span class="pull-left" style="font-size:10x;font-size:1.00vw">View Details</span>
                                <span class="pull-right"><i class="fa fa-2x fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
					</div>
				</div>
			</div>
        </td>
        <td style="width:33%">
        <div class="col-lg-3 col-md-6" style="width:100%">
            <div class="panel panel-green" style="border-color:#5CB85C">
                <div class="panel-heading">
                    <div class="row">
                    <table style="width:100%">
                    <tr>
                    <td>                                                                    
                    <i class="fa fa-plus-square fa-5x" style="font-size:10px;font-size:3.75vw"></i>
                    </td>
                    <td style="text-align:right;font-size:150%;vertical-align:middle;font-size:1.25vw">
                    &nbsp;&nbsp;&nbsp;INCOME&nbsp;&nbsp;&nbsp;&nbsp
                    </td>
                    </tr>
                    </table>
                    
                   <div class="col-xs-9 text-right" style="width:100%">   
				  <?php
                   $arBankDetails = $obj_AdminPanel->GetTotalIncome();
				   $iCounter = 0;
				   if($arBankDetails <> '')
				   {
					   	?>
                        <table style="width:100%;">
                        <?php
							
						   foreach($arBankDetails as $arData=>$arvalue)
						   {
							   $month = $arvalue["date"];
							   $receipts =$arvalue["receipts"];
							   $payments = $arvalue["payments"]; 
							   $BalAmount = $receipts - $payments;
							   if($month <> "")
							   {
							   ?>
                               <tr><td style="width:60%;text-align:left;font-size:1.00vw"><?php  echo $month ?></td><td style="width:5%;">:</td><td style="font-weight:bold; width:35%; text-align:right;font-size:1.00vw"><?php  echo number_format($BalAmount,2); ?></td></tr>
                               
                            <?php
							   }
							   
							   $iCounter++;
							   if($iCounter >= 3)
							   {
								   break;
							   }
						   }
                            echo "</table>";
				   }
					   $ReqRows =  3 - $iCounter;
					   ?>
                       <div style="color:#5CB85C">
                       <table style="width:100%;">
                       <tr>
                       <?php 
					   for($ICnt = 1; $ICnt <= $ReqRows; $ICnt++)
					   {
						   ?>
                       <td style="width:60%;text-align:left;color:#5CB85C;font-size:1.00vw;">No Data
                       </td>
                       </tr>
                       <?php 
					   }
					   ?>
                       </tr>
                       </table></div>
                   
                </div>
                 </div>
              </div>
                <a href="IncomeDetails.php">
                    <div class="panel-footer">
                        <span class="pull-left" style="font-size:10x;font-size:1.00vw">View Details</span>
                        <span class="pull-right"><i class="fa fa-2x fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
              </div>
        </div>
        <td style="width:33%">
		<div class="col-lg-3 col-md-6" style="width:100%">
            <div class="panel panel-red" style="border-color:#D9524F">
                <div class="panel-heading">
                    <div class="row">
                    <table style="width:100%">
                    <tr>
                    <td>                                                                    
                    <i class="fa fa-minus-square fa-5x" style="font-size:10px;font-size:3.75vw"></i>
                    </td>
                    <td style="text-align:right;font-size:150%;vertical-align:middle;;font-size:1.25vw">
                    &nbsp;&nbsp;EXPENDITURE
                    </td>
                    </tr>
                    </table>
                    
                   <div class="col-xs-9 text-right" style="width:100%">                   	  <?php
						$arBankDetails = $obj_AdminPanel->GetTotalExpenses();
						$iCounter = 0;
						if($arBankDetails <> '')
					   	{

							?>
                        		<table style="width:100%;">
                        	<?php
							foreach($arBankDetails as $arData=>$arvalue)
							{
								$month = $arvalue["date"];
								$receipts =$arvalue["receipts"];
								$payments = $arvalue["payments"]; 
								$BalAmount = $receipts - $payments;
							   if($month <> "")
							   {
								   ?>
                               <tr><td style="width:60%;text-align:left;font-size:1.00vw"><?php  echo $month ?></td><td style="width:5%;">:</td><td style="font-weight:bold; width:35%; text-align:right;font-size:1.00vw"><?php  echo number_format($BalAmount,2); ?></td></tr>
                            <?php
							   }
								 
								   $iCounter++;
								   if($iCounter >= 3)
								   {
									   break;
								   }
							}
							echo "</table>";
							//echo "<br><b><a href='ExpenseDetails.php'>Details</a></b><br>";
						}
						$ReqRows =  3 - $iCounter;
					   ?>
                       <div style="color:#D9524F">
                       <table style="width:100%;">
                       <tr>
                       <?php 
					   for($ICnt = 1; $ICnt <= $ReqRows; $ICnt++)
					   {
						   ?>
                       <td style="width:60%;text-align:left;color:#D9524F;font-size:1.00vw;">No Data
                       </td>
                       </tr>
                       <?php 
					   }
					   ?>
					  </table>
                      </div>
                </div>
                 </div>
              </div>
                <a href="ExpenseDetails.php">
                    <div class="panel-footer">
                        <span class="pull-left" style="font-size:10x;font-size:1.00vw">View Details</span>
                        <span class="pull-right"><i class="fa fa-2x fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
              </div>
        </div>
    </tr>
    <tr></tr>
</table>

<table style="width:100%;display:none;width:75vw" id="table2">
	<tr>
    <td style="width:33%">
    	<div class="col-lg-3 col-md-6" style="width:100%">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                    <table style="width:100%">
                    <tr>
                    <td>                                                                    
                    <i class="fa fa-arrow-up fa-5x" style="font-size:10px;font-size:3.75vw"></i>
                    </td>
                    <td style="text-align:right;font-size:150%;vertical-align:middle;font-size:1.25vw">
                    MEMBER'S&nbsp;DUES&nbsp;&nbsp;
                    </td>
                    </tr>
                    </table>
                    
                   <div class="col-xs-9 text-right" style="width:100%">  
                	<?php
  						$arBankDetails = $obj_AdminPanel->GetTotalMemberDues();
						$iCounter = 0;
						if($arBankDetails <> '')
					   	{

							?>
                        		<table style="width:100%;">
                        	<?php
						   foreach($arBankDetails as $arData=>$arvalue)
						   {
							   $month =$arvalue["date"];
							   /*$receipts =$arvalue["receipts"];
							   $payments = $arvalue["payments"];
							   $BalAmount = $receipts - $payments;*/
							    $BalAmount = $arvalue["BalAmount"]; 
							   
							   //echo "<tr align=right><td>".$month." : </td><td align=right><b>".number_format($BalAmount,2);"</b></td></tr>";
							   if($month <> "")
							   {
								   ?>
                                   
                               <tr><td style="width:60%;text-align:left;font-size:1.00vw"><?php  echo $month ?></td><td style="width:5%;">:</td><td style="font-weight:bold; width:35%; text-align:right;font-size:1.00vw"><?php  echo number_format($BalAmount,2); ?></td></tr>
                            <?php
							   }
							   
							   $iCounter++;
							   if($iCounter >= 3)
							   {
								   break;
							   }
						   }
							?>
                            </table>
                            <?php
						   //echo "<br><b><a href='reports.php?&sid=" . $_SESSION['society_id'] . "'>Details</a></b><br><br>";
						}
						$ReqRows =  3 - $iCounter;
					   ?>
                       <div style="color:#337BB7">
                       <table style="width:100%;">
                       <tr>
                       <?php 
					   for($ICnt = 1; $ICnt <= $ReqRows; $ICnt++)
					   {
						   ?>
                       <td style="width:60%;text-align:left;color:#337BB7;font-size:1.00vw;">No Data
                       </td>
                       </tr>
                       <?php 
					   }
					   ?>
                       </table>
                      </div>
				      </div>
                 </div>
              </div>
                <a href="dues_advance_frm_member_report.php?&sid=<?php echo $_SESSION['society_id']; ?>">
                    <div class="panel-footer">
                        <span class="pull-left" style="font-size:10x;font-size:1.00vw">View Details</span>
                        <span class="pull-right"><i class="fa fa-2x fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
              </div>
        </div>
        </td>
        <td style="width:33%">
        	<div class="col-lg-3 col-md-6" style="width:100%">
            <div class="panel panel-green" style="border-color:#5CB85C">
                <div class="panel-heading">
                    <div class="row">
                    <table style="width:100%">
                    <tr>
                    <td>                                                                    
                    <i class="fa fa-cubes fa-5x" style="font-size:10px;font-size:3.75vw"></i>
                    </td>
                    <td style="text-align:right;font-size:150%;vertical-align:middle;font-size:1.25vw">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ASSETS&nbsp;&nbsp;
                    </td>
                    </tr>
                    </table>
                    
                   <div class="col-xs-9 text-right" style="width:100%">  
				  <?php
                   	//$arBankDetails = $obj_AdminPanel->GetTotalAssets();
					$arBankDetails = $obj_AdminPanel->GetTotalLiabilitiesOrAssets(ASSET);
					$iCounter = 0;
					if($arBankDetails <> '')
					 {

						?>
                        		<table style="width:100%;">
                        	<?php
						foreach($arBankDetails as $arData=>$arvalue)
						{
						   $month = $arvalue["date"];
						  /* $receipts =$arvalue["receipts"];
						   $payments = $arvalue["payments"]; 
						   $BalAmount = $receipts - $payments;*/
						   $BalAmount = $arvalue["BalAmount"];
						   //echo "<tr align=right><td>".$month." : </td><td align=right><b>".number_format($BalAmount,2);"</b></td></tr>";
						   if($month <> "")
							{
						   ?>
                               <tr><td style="width:60%;text-align:left;font-size:1.00vw"><?php  echo $month ?></td><td style="width:5%;">:</td><td style="font-weight:bold; width:35%; text-align:right;font-size:1.00vw"><?php  echo number_format($BalAmount,2); ?></td></tr>
                            <?php
							}
						   
						   $iCounter++;
						   if($iCounter >= 3)
						   {
							   break;
						   }
						}
						?>
						</table>
                        <?php
						//echo "<br><b><a href='AssetSummary.php'>Details</a></b><br><br>";
					 }
					 $ReqRows =  3 - $iCounter;
					   ?>
                       <div style="color:#5CB85C">
                       <table style="width:100%;">
                       <tr>
                       <?php 
					   for($ICnt = 1; $ICnt <= $ReqRows; $ICnt++)
					   {
						   ?>
                       <td style="width:60%;text-align:left;color:#5CB85C;font-size:1.00vw;">No Data
                       </td>
                       </tr>
                       <?php 
					   }
					   ?>
                  </table>
                      </div>
                </div>
                 </div>
              </div>
                <a href="AssetSummary.php">
                    <div class="panel-footer">
                        <span class="pull-left" style="font-size:10x;font-size:1.00vw">View Details</span>
                        <span class="pull-right"><i class="fa fa-2x fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
              </div>
        </div>
        </td>
        <td style="width:33%">
			<div class="col-lg-3 col-md-6" style="width:100%">
            <div class="panel panel-red" style="border-color:#D9524F">
                <div class="panel-heading">
                    <div class="row">
                    <table style="width:100%">
                    <tr>
                    <td>                                                                    
                    <i class="fa fa-exclamation-triangle fa-5x" style="font-size:10px;font-size:3.75vw"></i>
                    </td>
                    <td style="text-align:right;font-size:150%;vertical-align:middle;font-size:1.25vw">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;LIABILITIES&nbsp;&nbsp;
                    </td>
                    </tr>
                    </table>
                    
                   <div class="col-xs-9 text-right" style="width:100%">  
                	  <?php
						//$arBankDetails = $obj_AdminPanel->GetTotalLiabilities();
						$arBankDetails = $obj_AdminPanel->GetTotalLiabilitiesOrAssets(LIABILITY);
						$iCounter = 0;
  						if($arBankDetails <> '')
					   	{
							?>
                        		<table style="width:100%;">
                        	<?php
							foreach($arBankDetails as $arData=>$arvalue)
							{
								$month = $arvalue["date"];
							   /*$receipts =$arvalue["receipts"];
							   $payments = $arvalue["payments"]; 
							   $BalAmount = $payments - $receipts;*/
							   $BalAmount = $arvalue["BalAmount"];
							   //echo "<tr align=right><td>".$month." : </td><td align=right><b>".number_format($BalAmount,2);"</b><br>";
							   if($month <> "")
							   {
								   ?>
                               <tr><td style="width:60%;text-align:left;font-size:1.00vw"><?php  echo $month ?></td><td style="width:5%;">:</td><td style="font-weight:bold; width:35%; text-align:right;font-size:1.00vw"><?php  echo number_format($BalAmount,2); ?></td></tr>
                            <?php
							   }
							   
							   $iCounter++;
							   if($iCounter >= 3)
							   {
								   break;
							   }
							}
							?>
							</table>
                            <?php
							//echo "<br><b><a href='LiabilitySummary.php'>Details</a></b><br><br>";
						}
						$ReqRows =  3 - $iCounter;
					   ?>
                       <div style="color:#D9524F">
                       <table style="width:100%;">
                       <tr>
                       <?php 
					   for($ICnt = 1; $ICnt <= $ReqRows; $ICnt++)
					   {
						   ?>
                       <td style="width:60%;text-align:left;color:#D9524F;font-size:1.00vw;">No Data
                       </td>
                       </tr>
                       <?php 
					   }
					   ?>
					  </table>
                      </div>
                </div>
                 </div>
              </div>
                <a href="LiabilitySummary.php">
                    <div class="panel-footer">
                        <span class="pull-left" style="font-size:10x;font-size:1.00vw">View Details</span>
                        <span class="pull-right"><i class="fa fa-2x fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
              </div>
        </div>
        </td>
    </tr>
    <tr>
    </tr>
</table>

<table style="width:100%;display:none;width:75vw" id="table3">
	<tr>
    	<td  style="width:33%">
        	<div class="col-lg-3 col-md-6" style="width:100%">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                    <table style="width:100%">
                    <tr>
                    <td>                                                                    
                    <i class="fa fa-file-text-o fa-5x" style="font-size:10px;font-size:3.75vw"></i>
                    </td>
                    <td style="text-align:right;font-size:150%;vertical-align:middle;font-size:1.25vw">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MAINTENANCE&nbsp;BILLS&nbsp;&nbsp;
                    </td>
                    </tr>
                    </table>
                    
                   <div class="col-xs-9 text-right" style="width:100%">  
                	<?php
						$iCounter = 0;
						$arBankDetails = $obj_AdminPanel->GetLastBillGenerated();
   						if($arBankDetails <> '')
					   	{
						   ?>
                        		<table style="width:100%;">
                        	<?php
						   
						   foreach($arBankDetails as $arData=>$arvalue)
						   {
							   $amount = $arvalue["amount"];
							   $Month = $arvalue["Type"]; 
							   //echo "<br>".$Month." : <b>".number_format($amount,2);"</b><br>";
							   ?>
                               <tr><td style="width:60%;text-align:left;font-size:1.00vw"><?php  echo $Month ?></td><td style="width:5%;">:</td><td style="font-weight:bold; width:35%; text-align:right;font-size:1.00vw"><?php  echo number_format($amount,2); ?></td></tr>
                            <?php
							   
							   $iCounter++;
							   if($iCounter >= 3)
							   {
								   break;
							   }
						   }
						   ?>
                           </table>
                           <?php
						}
						$ReqRows =  3 - $iCounter;
					   ?>
                       <div style="color:#337BB7">
                       <table style="width:100%;">
                       <tr>
                       <?php 
					   for($ICnt = 1; $ICnt <= $ReqRows; $ICnt++)
					   {
						   ?>
                       <td style="width:60%;text-align:left;color:#337BB7;font-size:1.00vw;">No Data
                       </td>
                       </tr>
                       <?php 
					   }
					   ?>
				  </table>
                      </div>
                </div>
                 </div>
              </div>
                <a href="genbill.php">
                    <div class="panel-footer">
                        <span class="pull-left" style="font-size:10x;font-size:1.00vw">Generate New bill</span>
                        <span class="pull-right"><i class="fa fa-2x fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
              </div>
        </div>
        </td>
        <td class="main_block" style="width:33%">
        	<div class="main_div" style="display:none;">
            	<div class="main_head">
               	</div>
                <div class="main_data">
                </div>
         	</div>
        </td>
        <td class="main_block">
        	<div class="main_div" style="display:none;">
            	<div class="main_head">
                </div>
                <div class="main_data">
                </div>
         	</div>
        </td>
    </tr>
</table>
</center>

</table>
<?php include_once "includes/foot.php"; ?>
<script>

function myFunction() 
{
	$("#table1").fadeIn(2000);
	$("#table2").fadeIn(2000);
	$("#table3").fadeIn(2000);
}
myFunction();
</script>