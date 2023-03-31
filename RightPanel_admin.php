<?php include_once("classes/dbconst.class.php");
$bIsHide = bIsReportOrValidationPage($scriptName);?>
<link href="dist/css/sb-admin-2.css" rel="stylesheet">

 <div class="panel panel-info" id="panel_widget"   <?php if($bIsHide == true){ echo 'style="display:none;width:0px;height:0px;"';}else{echo 'style="float:right;margin-top:0.5%;margin-right:2%;display:none;width:16.65vw"';} ?>>

                        <div class="panel-heading"  <?php if($bIsHide == true){ echo 'style="display:none;width:0px;height:0px;"';}else{echo 'style="vertical-align:middle;background-color:#F7F7F7;font-size:10px;font-size:1.25vw;width:16.50vw"';} ?>>
                         Quick Links<br />
                          <?php if($_SESSION["role"] == ROLE_ADMIN_MEMBER)
						  { ?>
                         <a href="dues_advance_frm_member_report.php?&sid=<?php echo $_SESSION['society_id']; ?>"><button type="button" class="btn btn-primary btn-circle" title="View Dues from Members" style="font-size:10px;font-size:1.75vw;width:3vw;height:3vw"><i class="fa fa-arrow-up" style="font-size:10px;font-size:1.25vw"></i>
                            </button></a>
                         <?php
						  }
						  else
						  {
							  ?>
                          <!--<a href="ledger.php">--><button type="button" class="btn btn-primary btn-circle" title="Manage Ledger" style="font-size:10px;font-size:1.75vw;width:3vw;height:3vw" onClick="window.open('ledger.php','QuickLedgerLink','type=fullWindow,fullscreen,scrollbars=yes')"><i class="fa fa-L" style="font-size:10px;font-size:1.60vw"></i>
                            </button><!--</a>-->
							<?php
							  
						  }
						  ?>
                         
                            <?php if($_SESSION["role"] == ROLE_ADMIN_MEMBER)
						  { ?>
                          
                            <a href="list_member.php"><button type="button" class="btn btn-success btn-circle" title="View Members List" style="font-size:10px;font-size:1.75vw;width:3vw;height:3vw"><i class="fa fa-list-ol" style="font-size:10px;font-size:1.25vw"></i>
                            </button></a>
                            <?php
						  }
						  else
						  {
							  ?>
                              <a href="genbill.php"><button type="button" class="btn btn-success btn-circle" title="Generate Bill" style="font-size:10px;font-size:1.75vw;width:3vw;height:3vw"><i class="fa fa-edit" style="font-size:10px;font-size:1.25vw"></i>
                            </button></a>
                              	<?php
							  
						  }
						  ?>
                              
                            <a href="BankAccountDetails.php"><button type="button" class="btn btn-warning btn-circle" title="Bank Accounts" style="font-size:10px;font-size:1.75vw;width:3vw;height:3vw"><i class="fa fa-bank" style="font-size:10px;font-size:1.25vw"></i>
                            </button></a>
                           
                           <?php if($_SESSION["role"] == ROLE_SUPER_ADMIN || $_SESSION["role"] == ROLE_ADMIN)
						  	{ ?>
							<a href="financial_reports.php"><button type="button" class="btn btn-info btn-circle" title="Financial Reports"  style="font-size:10px;font-size:1.75vw;width:3vw;height:3vw"><i class="fa fa-signal " style="font-size:10px;font-size:1.25vw"></i>
                            </button></a>
                            <?php 
							} ?>
                           <?php if($_SESSION["role"] == ROLE_ADMIN_MEMBER)
						  {
							  ?>
                          <!--<a href="ledger.php">--><button type="button" class="btn btn-primary btn-circle" title="Manage Ledger" style="font-size:10px;font-size:1.75vw;width:3vw;height:3vw" onClick="window.open('ledger.php','QuickLedgerLink','type=fullWindow,fullscreen,scrollbars=yes')"><i class="fa fa-inr" style="font-size:10px;font-size:1.25vw"></i>
                            </button><!--</a>-->
							<?php
							  
						  }
						  ?> 
                    </div>
            
            
                    
