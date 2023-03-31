<?php if(!isset($_SESSION)){ session_start(); } ?>
<?php
 	//Turn off all error reporting
    //error_reporting(0);
	include_once("classes/dbmanager.class.php");
	
	if(!isset($_REQUEST['Import'])) 
	{
		if(!isset($_REQUEST['set']))
		{
			$obj_dbManager = new dbManager();
			$dbName = $obj_dbManager->getEmptyDBName();
			
			if($dbName == '')
			{
				?>
					<script>
						alert('No Database Available To Import New Society.\n\nPlease Contact System Administrator.');
						window.location.href = "initialize.php";
					</script>
				<?php	
				exit();
			}
			else
			{
				$_SESSION['dbname'] = $dbName;
				
				?>
					<script>
						localStorage.setItem('dbname', "<?php echo $_SESSION['dbname']; ?>");
						window.location.href = "import.php?set";
					</script>
				<?php	
			}
		}
	}
	
	include_once("includes/head_s.php");
	include_once("classes/bill_period.class.php");
	
	$obj_bill_period=new bill_period($m_dbConn);
//print_r($obj_unit_import);
?>


<html>
<head>
<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<!--<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" media="screen" />
<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
<script type="text/javascript" src="javascript/ui.datepicker.js"></script>-->
<script type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript" src="js/populateData.js"></script>
<script type="text/javascript">
       $(function()
        {
            //$.datepicker.setDefaults($.datepicker.regional['']);
//            $(".basics").datepicker({ 
//            dateFormat: "yy-mm-dd", 
//            showOn: "both", 
//            buttonImage: "images/calendar.gif", 
//            buttonImageOnly: true 
        //})
		
		//var dropdwn1=document.getElementById('Period').value;
		//var dropdwn2=document.getElementById('eselObj').value;
		
		
		
		});
		
		
		
		
		$(document).ready(
        function(){
            $('input:submit').attr('disabled',true);
            $('input:file').change(
			  
                function(){
                    if ($(this).val())
					{
                        $('input:submit').removeAttr('disabled'); 
                    }
                    else 
					{
                        $('input:submit').attr('disabled',true);
                    }
                });
        });
    
</script>

<script language="javascript" type="text/javascript">

			function jsGetCycles(cycleid)
			{
				//alert("1");
				$('#eselObj').empty();
				
				var Months = [];
				Months = getMonths(parseInt(cycleid));
				
				for (var iCnt = 0; iCnt < Months.length; iCnt++)
				{
					$('#eselObj').append(
					$('<option></option>').val(Months[iCnt]).html(Months[iCnt]));
				}
				var year_id=document.getElementById('Year').value;
				//alert(year_id);
				if(year_id.length >0)
				{
					
				jsGetperiods(year_id);
				}
			}
			function go_error()
			{
				setTimeout('hide_error()',10000);	
			}
			function hide_error()
			{
				document.getElementById('error').style.display = 'none';	
			}
	</script>
</head>




<body onLoad="go_error();">


<form name="society_form" action="process/import_report.process.php" method="post" enctype="multipart/form-data" >

<center>
<br>
<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Import Society Data</div>

<table>

<?php
if(isset($_POST["ShowData"]))
{
	?>
    <tr height="30"><td colspan=5 style="text-align:center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
<?php } ?> 
<!--<strong><div id="show" style="text-align:center; width:100%; color:#FF0000"><?php //echo $_POST["ShowData"]; ?></div></strong>-->
<!--<tr height="50" align="center"><td>&nbsp;</td><th colspan="3" align="center"><table align="center"><tr height="25"><th bgcolor="#CCCCCC" width="180">For Society Admin Login</th></tr></table></th></tr>-->
<BR/>
<BR/>

<!--<tr height="50" align="center"><th>&nbsp;</th><th colspan="3" align="center"><table align="center"><tr height="25"><th bgcolor="#CCCCCC" width="180">For Society Admin Login</th></tr></table></th></tr>
<tr align="left">
        	<td valign="middle"></td>
			<td>Security Code</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="key" id="key"  value='<?php //echo $_POST['key']; ?>'/></td>
</tr> 

<tr align="left">
        	<td valign="middle"></td>
			<td>User Name</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="admin_user" id="admin_user"  value='<?php //echo $_POST['admin_user']; ?>'/></td>
</tr> 


<tr align="left">
        	<td valign="middle"></td>
			<td>Password</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="admin_pass" id="admin_pass" value='<?php //echo $_POST['admin_pass']; ?>' /></td>
</tr>  -->

<!--<tr align="left">
			<td valign="middle"></td>
        	<td>Enter Date For Member Dues:</td>
            <td>&nbsp; : &nbsp;</td>
            <td><input type="text" name="date" class="basics" size="10" readonly  style="width:80px;"/></td>
</tr>
-->

</tr>        
      <tr>
      <td>&nbsp;&nbsp;&nbsp;</td>
      </tr>
<tr height="50" align="center"><td>&nbsp;</td><th colspan="3" align="center"><table align="center"><tr height="25"><th bgcolor="#CCCCCC" width="180">Particulars From Bill Main File</th></tr></table></th></tr>
<tr align="left">
        		<td valign="middle"></td>
				<td>Billing Cycle<?php //echo $star; ?></td>
                <td>&nbsp; : &nbsp;</td>        
				<td>
                <select name="Cycle" id="Cycle" value='<?php echo $_POST['Cycle']; ?>' onChange="jsGetCycles(this.value);" > <!--disabled="disabled"-->
             	 <?php echo $combo_state=$obj_bill_period->combobox("select ID,Description from billing_cycle_master",'0');?>   
                  </select>
                  
                  </td><td><font color="#FF0000">(eg.if QtrMonth="April" then select Monthly)</font></td>	        
        
        </tr>
        
        
        <tr align="left">
                <td valign="middle"></td>
				<td>E Society Period To Import<?php //echo $star; ?></td> 
                <td>&nbsp; : &nbsp;</td>       
				<td>
                <select name="eperiod" id="eselObj" value='<?php echo $_POST['eselObj']; ?>'> 
                </select>
                  
                 </td></td><td><font color="#FF0000">(QtrMonth of 'Bill Main' file you want to import)</font></td>	        
        
        </tr>
       
        
        
        
        
        <tr align="left">
        		<td valign="middle"></td>
				<td>Data Import Into Year<?php //echo $star; ?></td>
                <td>&nbsp; : &nbsp;</td>        
				<td>
                <select name="Year" id="Year" value='<?php echo $_POST['Year']; ?>' onChange="jsGetperiods(this.value);"> <!--disabled="disabled"-->
             	 <?php echo $combo_state = $obj_bill_period->combobox("select YearID,YearDescription from year where status='Y' ORDER BY YearID DESC", DEFAULT_YEAR ); ?>   
                  </select>
                  
                  </td><td><font color="#FF0000">(Financial Year of 'Bill Main')</font></td>	        
        
        </tr>
        
        <script>
			function jsGetperiods(year_id)
			{
				var cycleID=document.getElementById('Cycle').value;
				//alert(cycleID);
				populateDDListAndTrigger('select#Period', 'ajax/ajaxbill_period.php?getperiod&year=' + year_id + '&cycleID='+cycleID, 'period', 'periodFetched', false);
			}
			function periodFetched()
			{
				
			}
		</script>
        
        
         
  		<!--<tr align="left">
        		<td valign="middle"></td>
				<td>Data Import Into Period<?php //echo $star; ?></td>        
				<td>&nbsp; : &nbsp;</td>
                <td>-->
                <!--<select name="Period" id="Period" value='<?php echo $_POST['Period']; ?>'> --><!--disabled="disabled"-->
             	 <?php //echo $combo_state = $obj_bill_period->combobox("select ID,Type from period where status='Y'",'0'); ?>   
                 <!-- </select>-->
                  <input type="hidden"  name="Period" id="Period"  value='<?php echo $_POST['Period']; ?> ' />
                  
               <!--   </td>		        
        </tr>       --> 
        
      <tr>
      <td>&nbsp;&nbsp;&nbsp;</td>
      </tr>
      <BR/>
	<BR/>
     
      <tr height="50" align="center"><td>&nbsp;</td><th colspan="3" align="center"><table align="center"><tr height="25"><th bgcolor="#CCCCCC" width="180">Particulars For Bill </th></tr></table></th></tr>
         
   <tr align="left">
        	<td valign="middle"></td>
			<td>Bill Interest Rate</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="int_rate" id="int_rate" value='<?php echo $_POST['int_rate']; ?>'/></td>
		</tr>
        
        <tr align="left">
        	<td valign="middle"></td>
			<td>Bill Interest Method</td>
            <td>&nbsp; : &nbsp;</td>
			<td><select name="int_method" id="int_method" value='<?php echo $_POST['int_method']; ?>'>
            		<OPTION VALUE="<?php echo INTEREST_METHOD_DELAY_DUE; ?>">Delay After Due Days</OPTION>
                    <OPTION VALUE="<?php echo INTEREST_METHOD_FULL_MONTH; ?>" >Full Month</OPTION>
                </select>
            </td>
		</tr>
        
        <tr align="left">
        	<td valign="middle"></td>
			<td><!--Interest Trigger Amount--></td>
            <td><!--&nbsp; : &nbsp;--></td>
			<td><!--<input type="hidden" name="int_tri_amt" id="int_tri_amt" value="0" >--></td>
		</tr>
        
        <tr align="left">
        	<td valign="middle"></td>
			<td>Bill Rebate Method</td>
            <td>&nbsp; : &nbsp;</td>
			<td><select name="rebate_method" id="rebate_method" value='<?php echo $_POST['rebate_method']; ?>'>
            		<OPTION VALUE="<?php echo REBATE_METHOD_NONE; ?>">None</OPTION>
                    <OPTION VALUE="<?php echo REBATE_METHOD_FLAT; ?>">Flat Amount</OPTION>
                    <!--<OPTION VALUE="<?php //echo REBATE_DUE_WAIVER; ?>">Due Amount Waiver</OPTION>-->
                </select>
            </td>
		</tr>

        
        <tr align="left">
        	<td valign="middle"></td>
			<td>Bill  Rebate Amount</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="rebate" id="rebate" value='<?php echo $_POST['rebate']; ?>'/></td>
		</tr>
     
        


<tr align="left">
        	<td valign="middle"></td>
			<td>Cheque Bounce Charges</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="chq_bounce_charge" id="chq_bounce_charge" /></td>
		</tr>
     
        


<tr align="left">
        	<td valign="middle"></td>
			<td>Browse Files To Import</td>
            <td>&nbsp; : &nbsp;</td>
			<td id="browse"><input type="file" name="upload_files[]" id="file" multiple /></td>
            
</tr>   

<tr><td colspan="4">&nbsp;</td></tr>
<tr height="50" align="center">
 <td colspan="4" align="center"><input type="submit" name="Import" value="Import"  class="btn btn-primary" disabled /></td>
</tr>

</table>
<details><p><font color="#FF0000">Please select BuildingID,WingID,AccountMaster,FlatId,OwnerId,Tariff,BillMainPrevYear file..</font></p></details>
</center>
<!--<div id="show" style="color:#FF0000; font-weight:bold;"></div>
       <iframe id="uploadframe" name="uploadframe" src="<?php //echo $show_op;?>" width="800" height="500" scrolling="yes" frameborder="0"><?php //echo $show_op; ?></iframe>
    </div>-->

</form>
</div>

</div>
<?php include_once "includes/foot.php"; ?>