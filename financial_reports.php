<?php 
include_once("includes/head_s.php");
include_once("classes/utility.class.php");
include_once("classes/dbconst.class.php");

$obj_Utility = new utility($m_dbConn);
$societyDetails = $obj_Utility->GetSocietyInformation($_SESSION['society_id']);

if($_SESSION['default_year'] == 0)
{?>
	<script> alert("Please Set Default Value For Current Year.");window.location.href="defaults.php";</script>
<?php	
}
else{
$CurrentMonthBeginingDate = $obj_Utility->getCurrentYearBeginingDate($_SESSION['default_year']);
}

?>

<script language="javascript">
	<?php
	
		if(isset($_REQUEST['report_name']))
		{
			$status1 = $obj_Utility->getIsDateInRange( getDBFormatDate($_REQUEST['from_date']) , getDBFormatDate($_SESSION['default_year_start_date']) , getDBFormatDate($_SESSION['default_year_end_date'])); 
			if($status1)
			{
				$_SESSION['from_date'] = $_REQUEST['from_date'];	
			}
			else
			{
				$_SESSION['from_date'] = 	getDisplayFormatDate($_SESSION['default_year_start_date']);		
			}
			
			$status2 = $obj_Utility->getIsDateInRange( getDBFormatDate($_REQUEST['from_date']) , getDBFormatDate($_SESSION['default_year_start_date']) , getDBFormatDate($_SESSION['default_year_end_date'])); 
			if($status2)
			{
				$_SESSION['to_date'] = $_REQUEST['to_date'];
			}
			else
			{
				$_SESSION['to_date'] = 	getDisplayFormatDate($_SESSION['default_year_end_date']);		
			}
			
			if($_REQUEST['report_name'] == 'Trial_Balance')
			{ ?>
				//window.location.href = "TrialBalance.php" target="_blank";
				 window.open('TrialBalance.php','_blank');
	<?php	  
			}
			else if($_REQUEST['report_name'] == 'Income&Expenditure_Statement')
			{ ?>
				//window.location.href = "IncomeStmt.php";
				window.open('IncomeStmt.php','_blank');
	<?php					
			}
			else if($_REQUEST['report_name'] == 'Balance_Sheet')
			{
				if($_REQUEST['chk_ShowPreviousYearBalanceSheet'] == 1)
				{ ?>
					window.open('BalanceSheet_withPreBalance.php?show_PreBalance','_blank');	
					
				<?php }
				else
				{ ?>
					window.open('BalanceSheet.php','_blank');
				
				<?php 
				}?>  
				
	<?php						
			}
			else if($_REQUEST['report_name'] == 'Opening_Balance_Sheet')
			{ ?>
				//window.location.href = "OpeningBalance.php";
				window.open('OpeningBalance.php','_blank');
	<?php
			}
			else if($_REQUEST['report_name'] == 'Contribution_Ledger')
			{ ?>
				//window.location.href = "ContributionLedger.php";
				window.open('ContributionLedger.php','_blank');
	<?php						
			}
			
			else if($_REQUEST['report_name'] == 'GST_Incoming_Report')
			{ ?>
				//window.location.href = "gst_incoming_report.php";
				window.open('gst_incoming_report.php','_blank');
	<?php						
			}
			else if($_REQUEST['report_name'] == 'GST_Paid_Details')
			{ ?>
				//window.location.href = "gst_paid_report.php";
				window.open('gst_paid_report.php','_blank');
	<?php						
			}
			else if($_REQUEST['report_name'] == 'GST_Tax_Details')
			{ ?>
				//window.location.href = "gst_tax_report.php";
				window.open('gst_tax_report.php','_blank');
	<?php						
			}
			else if($_REQUEST['report_name'] == 'TDS_Paid_Details')
			{ ?>
				//window.location.href = "gst_paid_report.php";
				window.open('tds_paid_report.php','_blank');
	<?php						
			}
			
		}			
	?>
</script>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>Financial Reports</title>                                              
                <script type="text/javascript">
				$( document ).ready(function() {
    				$('#ShowPreviousYearBalance').hide();
				});
				minStartDate = '<?php  echo getDisplayFormatDate($_SESSION['default_year_start_date']);?>';
				maxEndDate = '<?php  echo getDisplayFormatDate($_SESSION['default_year_end_date']);?>';
                    $(function()
                    {
                        $.datepicker.setDefaults($.datepicker.regional['']);
                        $(".basics").datepicker({ 
                        dateFormat: "dd-mm-yy", 
                        showOn: "both", 
                        buttonImage: "images/calendar.gif", 
                        buttonImageOnly: true,
						minDate: minStartDate,
						maxDate: maxEndDate
                    })});
					
					function HideDates(report_type)
					{
						//if(report_type == 'Opening_Balance_Sheet' || report_type == 'Contribution_Ledger_Detailed')
						if(report_type == 'Opening_Balance_Sheet')
						{
							$('#from').fadeOut('slow');	
							$('#to').fadeOut('slow');
						}
						else
						{
							$('#from').fadeIn('slow');	
							$('#to').fadeIn('slow');
						}
						
						if(report_type == 'Balance_Sheet')
						{
							$('#ShowPreviousYearBalance').fadeIn('slow');
						}
						else
						{
							$('#ShowPreviousYearBalance').fadeOut('slow');
						}
						
						
						
					}
					
					function setValue()
					{
						if(document.getElementById('chk_ShowPreviousYearBalanceSheet').checked)
						{
							document.getElementById('chk_ShowPreviousYearBalanceSheet').value = 1;
						}
						else
						{
							document.getElementById('chk_ShowPreviousYearBalanceSheet').value = 0;
						}
					}
					
                </script>        
        </head>   
        <body>        
            <div id="middle">
            <div class="panel panel-info" style="height:400px;">
                <div class="panel-heading" id="pageheader"> Financial Reports </div>
    		<center>
            	<form name="accounting_report" id="accounting_report" method="post">
                	<table style="border:1px solid black; width:40%;">
                    	<tr> <td colspan="3"> <br /> </td> </tr>
                    	<tr><h4><font  style="width:50px" color="#003399">Please select the period</font></h4></tr>
                        <tr>
                        	<th> &nbsp;&nbsp; Report Type </th>
                            <td> : &nbsp; </td>
                            <td>
                        	<select name="report_name" id="report_name" onChange="HideDates(this.value);">
                            	<option value="Trial_Balance"> Trial Balance </option>
                                <option value="Income&Expenditure_Statement"> Income & Expenditure Statement </option>
                                <option value="Balance_Sheet"> Balance Sheet </option>
                                <option value="Opening_Balance_Sheet"> Opening Balance Sheet </option>
                                <option value="Contribution_Ledger"> Contribution Ledger </option>
                                <!--<option value="GST_Incoming_Report">Incoming GST Report</option>
                                <option value="GST_Paid_Details"> Outgoing GST Report</option>-->
                                <?php if($societyDetails['apply_service_tax'] == "1")
								{
								?>
                                	<option value="GST_Incoming_Report">Output GST Report</option>
                                	<option value="GST_Paid_Details"> Input GST Report</option>
                                	<option value="GST_Tax_Details"> GST Tax Report</option>
                                <?php
								}
								?>
                                <option value="TDS_Paid_Details">TDS Report</option>
                            </select>
                            </td>
                        </tr>
                        <tr id="from">        	
                            <th> &nbsp;&nbsp; From </th>
                            <td> : &nbsp; </td>            
                            <td><input type="text" name="from_date" id="from_date"  class="basics" size="10" readonly   value="<?php if(isset($_SESSION['from_date'])){ echo $_SESSION['from_date']; } else { echo getDisplayFormatDate($CurrentMonthBeginingDate); }?>" style="width:80px;"/></td>
                        </tr>
                        <tr id="to">        	
                            <th> &nbsp;&nbsp; To </th>
                            <td> : &nbsp; </td>            
                            <td><input type="text" name="to_date" id="to_date"  class="basics" size="10" readonly   value=<?php if(isset($_SESSION['to_date'])){ echo $_SESSION['to_date']; } else { echo date("d-m-Y"); } ?>  style="width:80px;"/></td>
                        </tr>
                        <tr id="ShowPreviousYearBalance">
                        <td><label for="chk_ShowPreviousYearBalanceSheet">Show Previous Year Amounts</label></td>
                        <td> : &nbsp; </td> 
						<td><input type="checkbox" name="chk_ShowPreviousYearBalanceSheet" id="chk_ShowPreviousYearBalanceSheet" value="0" onChange="setValue();"></td>
						</tr>
                        <tr> <td colspan="3"> <br /> </td> </tr>
                        <tr>
                            <td colspan="3" align="center">                               	                         
                            	<input type="submit" name="insert" id="insert" value="Generate Report" >  
                        	</td>
                      	</tr>                          
                        <tr> <td colspan="3"> <br /> </td> </tr>
                    </table>
                </form>            
            </center>
            </div>
            </div>                       			
               
	<?php include_once "includes/foot.php"; ?>