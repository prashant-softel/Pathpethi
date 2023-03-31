<?php 
include_once "ses_set_as.php";
include_once "classes/dbconst.class.php";		 
include_once("classes/voucher.class.php");
include_once("classes/latestcount.class.php");
include_once("classes/register.class.php");
//print_r($_SESSION);
?>

      
<?php
//print_r($_SESSION);
if(!isset($_SESSION['default_impose_fine']) || $_SESSION['default_impose_fine'] == "0")
{
	?>
	<script type="text/javascript">alert("Impose fine ledger isn't found in defaults. \r\n Please set it first to access Reversal credit/Fine.");</script>
	<?php
	header("location: defaults.php");
}
if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
else
{
	include_once("includes/head_s.php");
}
if(!isset($_REQUEST['uid']))
{
	?>
	<script type="text/javascript">
	alert("Please select unit to apply reversal credit");
	window.location.href = "list_member.php?scm#";
	</script>
<?php	
}
include_once("classes/reverse_charges.class.php");
$obj_reverse_charges = new reverse_charges($m_dbConn);
$get_unit_name=$obj_reverse_charges->getDetails($_REQUEST['uid']);
$insert_reverse_charges=$obj_reverse_charges->storeDetails($_REQUEST['uid']);
$year=$obj_reverse_charges->get_year($_REQUEST['uid']);
//$period=$obj_reverse_charges->get_Period($year[0]['YearID']);
$MaxPeriodID=$obj_reverse_charges->getMaxPeriodID();

?><head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<!--<link rel="stylesheet" href="css/ui.datepicker.css" type="text/css" media="screen" />
	<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
	<script type="text/javascript" src="javascript/ui.core.js"></script>    
	<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
	<script type="text/javascript" src="javascript/ui.datepicker.js"></script>-->
	<script type="text/javascript" src="js/ledger_details.js"></script>
    <script type="text/javascript" src="js/jsgenbill.js"></script>
	<script type="text/javascript" src="js/ajax.js"></script>
    <script type="text/javascript" src="js/populateData.js"></script>
	
	<script type="text/javascript">
	function hide_error()
	{
		
	}
	
	function changeURL()
	{
		var unit_id = document.getElementById('member').value;
		window.location.href = "reverse_charges.php?&uid=" + unit_id;
	}
	
	function view_report()
	{
		var unit_id = document.getElementById('member').value;
		var bill_type = document.getElementById('bill_method').value;
		var bill_period = document.getElementById('period_id').value;
		var trans_type = '';
		if(document.getElementById('rev_charge').checked == true)
		{
			trans_type = 1;
		}
		else if(document.getElementById('fine').checked == true)
		{
			trans_type = 2;
		}
		
		$.ajax({
		url : "ajax/reverse_charges.ajax.php",
		type : "POST",
		data: {"method": "fetch_datatable", "unit_id":unit_id, "bill_type":bill_type, "bill_period":bill_period, "trans_type":trans_type},
		success: function(data)
		{
			var datatable = data;
			document.getElementById('example').innerHTML = datatable;
			document.getElementById('example_info').style.display = "none";
			document.getElementById('example_paginate').style.display = "none";
		},		
		fail: function()
		{
			alert("Fail");
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
			alert(textStatus);
		}
		});
	}
	</script>

    <script type="text/javascript">
        $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "yy-mm-dd", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true 
        })});
			
		$(document).ready(function()
		{
			$('#year_id').trigger("change");
			if(document.getElementById('member').value == 0)
			{
				document.getElementById('insert').disabled = true;
			}
			else
			{
				document.getElementById('insert').disabled = false;
			}
		    

			$("#insert").click(function()
			{
				var dropdown_value = document.getElementById("ledger");
				var selected_value = dropdown_value.options[dropdown_value.selectedIndex].value;

				var amount = $("#amount").val();
				var comments = $("#comments").val();
				var period_id = $("#period_id option:selected").text();
					// alert(period_id);
				var last_chars = period_id.substr(-2);

					
					
					if(selected_value.length == 0)
					{
						alert("please select Ledger type");
						return false;
						
					}
					else if($('input[name=fine_type]:checked').length == 0)
					{
						alert("Please select Reverse type");
						return false;
						
					}
					else if(!amount)
					{
						alert("please Enter amount to be reversed");
						return false;
						
					}
					else if(comments.trim() == "")
					{
						alert("Please enter your Comments");
						return false;
					}
					else if(last_chars == "**")
					{
						alert("Note: To reflect Reversal Charges/Fine, you have to re-generate bills");
					}
					else
					{
						// alert("hello");
						return true;
							
					}
					
				
			});
			
			var selected = $("#bill_method option:selected");
			

			$("#bill_method").on("change",function()
			{
				
				var val = $("#insert").val();
					if(val != 'Update')
					{
						billperiod($("#bill_method").attr('id'));
					}
					if(val == 'Update')
					{
						// alert("shakti");
						selected.prop("selected",true);
					}
					
			});
			
			
			$("#period_id").on("change",function()
			{
				// $("#note_period").css("display", "block");

			});
			
			/*function billperiod(id)
			{
				var role = document.getElementById("role").value;
				var billtype = document.getElementById(id).value;
				var date = '0000-00-00';
				var yearid = <?php //echo $year[0]['YearID'];?>;
				
				var operation = $("#insert").val();
				
				$.ajax(
				{
					url: "ajax/reverse_charges.ajax.php",
					type: "POST",
					data: "year_id="+yearid+"&billtype="+billtype+"&role="+role+"&mode=billperiod",
					// dataType: "json",
					success: function(result)
					{ 
						var i,j;
						if(role == 'Super Admin')
						{
							var res = result.split("@@@");
							var res2 = res[1].split("#");
							var length = res2.length;
							//alert(length);
							if(res2 != "")
							{
								$("#period_id").empty();
								console.log("y"+res2);
								for(i=length-2;i>0;i-= 2)
								{
									//alert("Value :"+res2[j]+ "Result:"+res2[i]);
									if(i != 1)
									{
										j = i-1;
										//alert("Value1 :"+res2[j]+ "Result1:"+res2[i]);
										//alert();
										$("#period_id").append('<option selected="selected" value='+res2[j]+'>'+res2[i]+'**</option>');
									}
									else
									{
										j = i-1;
										$("#period_id").append('<option value='+res2[j]+'>'+res2[i]+'</option>');
										//alert("Value2 :"+res2[j]+ "Result2:"+res2[i]);
									}
								}  
							}
							else
							{
								if(billtype == 0)
								{
									document.getElementById(id).value = 1;
									alert("Maintenance bills not generated for selected financial year");
								}
								else if(billtype == 1)
								{
									document.getElementById(id).value = 0;
									alert("Supplementary bills not generated for selected financial year");
								}										
								return false;
							}
						}
						else
						{
							//$("#period_id").empty();
							// alert("h");
							var res = result.split("@@@");
							// alert(res[1]);
							var res2 = res[1].split("#");
							// alert(res2[0]);
							var length = res2.length;
							//alert(length);
							if(res2 != "")
							{
								$("#period_id").empty();
									
								for(i=length-2;i>0;i-= 2)
								{
									if(i != 1)
									{
										j = i-1;
										// alert("1");
										$("#period_id").append('<option selected="selected" value='+res2[j]+'>'+res2[i]+'**</option>');
									}
									else
									{
										j = i-1;
										$("#period_id").append('<option value='+res2[j]+'>'+res2[i]+'</option>');
									}
								} 
							}
							else
							{
								if(billtype == 0)
								{
									document.getElementById(id).value = 1;
									alert("Maintenance bills not generated for selected financial year");
								}
								else if(billtype == 1)
								{
									document.getElementById(id).value = 0;
									alert("Supplementary bills not generated for selected financial year");
								}										
								return false;
							}
						}
					}
				});
			}*/
				
		
			$("#amount").on("keypress input",function(e)
			{
				 var val = $(this).val();
				 	 
				if(val.charAt(0) == '.')
				{
					val = val.substr(1);
					document.getElementById("amount").value = val;
				}
				return isNumber(e, this);
				 
			});
			
			
			function isNumber(evt, element) 
			{
				
				var charCode = (evt.which) ? evt.which : event.keyCode

				if (
					
					(charCode != 46 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
					(charCode < 48 || charCode > 57))
					{
						return false;
						
					}
					
					else
					{
						return true;
					}
			}

			$('#comments').bind('keypress', function(e) {

			    if($('#comments').val().length >= 0)
			    {
			        var k = e.keyCode;
			        var ok = k >= 65 && k <= 90 || // A-Z
			            k >= 97 && k <= 122 || // a-z
			            k >= 48 && k <= 57 || // 0-9
			            k == 32; // {space}

			        if (!ok){
			            e.preventDefault();
			        }
			    }
			}); 
			

			//billperiod($("#bill_method").attr('id'));
		});
		
		
			
    </script>
</head>


<br />
<div id="middle">
<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Reverse Charge/Credit/Fine</div>

<center><h4><font color="#43729F" size="+1"><b><?php if($get_unit_name <> "") { echo 'Unit'.'  '.$get_unit_name; } else { echo 'All Units'; } ?></b></font></h4></center>

<form name="reverse_charges" id="reverse_charges" method="post"  >
<center>

<center><div><font color="#FF0000"><b><?php echo $insert_reverse_charges; ?></b></font></div></center	><br/>
<?php
		$star = "<font color='#FF0000'>*&nbsp;</font>";
		if(isset($_REQUEST['msg']))
		{
			$msg = "Sorry !!! You can't delete it. ( Dependency )";
		}
		else if(isset($_REQUEST['msg1']))
		{
			$msg = "Deleted Successfully.";
		}
		else
		{
			//$msg = '';	
		}
	?>
<div  style='width:100%'>	
<table style='width:70%'>
<tr><td><font color="#0033CC">NOTE: </font></td></tr>
<tr><td><font color="#0033CC">1.** in front of period means bill has already been generated for that period.</font></td></tr>
<tr><td><font color="#0033CC">2. If you add Reversal charge (Credit) or Fine (Debit) transaction to the period for which bills has already been generated, then you will have to re-generate the bills for that/those units. </font></td></tr>

</table>
</div>
    <table align='center'>
		<?php
		if(isset($msg))
		{
			if(isset($_POST["ShowData"]))
			{
		?>
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
		<?php
			}
			else
			{
			?>
            	<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $msg; ?></b></font></td></tr>	   
            <?php		
			}
		}
		else
		{
		?>	
				<tr height="30"><td colspan="4" align="center"><font color="red" style="size:11px;"><b id="error"><?php echo $_POST["ShowData"]; ?></b></font></td></tr>
        <?php
		}
		?>

         <tr align="left">
         	<td align="middle"></td>
            <td>Unit No.</td>
            <td>&nbsp; : &nbsp;</td>
            <td><select name="member" id="member" class="dropdown" onChange="changeURL();" >
            		<?php echo $combo_unit = $obj_reverse_charges->combobox("select u.unit_id, CONCAT(CONCAT(u.unit_no,' '), mm.owner_name) AS 'unit_no' from unit AS u JOIN `member_main` AS mm ON u.unit_id = mm.unit where u.society_id = '" . $_SESSION['society_id'] . "' and ownership_status = 1 ORDER BY u.sort_order",$_REQUEST['uid'],true);?>
				</select>
            </td>
         </tr>
         
         <tr align="left">
        	<td valign="middle"></td>
			<td>Bill Type</td>
            <td>&nbsp; : &nbsp;</td>
			<td><?php $selVal = "selected";?>
            		<select name="bill_method" id="bill_method" value="<?php echo $_REQUEST['bill_method'];?>"   style="width:142px;"> <!--onChange="ledgerFetched()"--> 
            		<OPTION VALUE="<?php echo BILL_TYPE_REGULAR; ?>"  <?php echo $_REQUEST['bill_method'] == BILL_TYPE_REGULAR? $selVal:'';?>>Regular Bill</OPTION>
                    <OPTION VALUE="<?php echo BILL_TYPE_SUPPLEMENTARY; ?>"  <?php echo $_REQUEST['bill_method']  == BILL_TYPE_SUPPLEMENTARY? $selVal:'';?>>Supplementary Bill</OPTION>
                </select>
            </td>
		</tr>
 
<tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Select Ledger to apply Reverse Charge/Fine</td>
            <td>&nbsp; : &nbsp;</td>
            <td>
            <select name="ledger" id="ledger">		
				<option value=''>Please Select</option>
                <?php  $combo_ledger = $obj_reverse_charges->combobox("select `id`,`ledger_name` from `ledger` where (show_in_bill=1 or supplementary_bill=1) and categoryid IN(select category_id from `account_category` where group_id in(1,3))",0);
               //$sql = "select `id`,`ledger_name` from `ledger` where `id`='".$_SESSION['default_interest_on_arear']."'";
			   
					echo $combo_ledger.$obj_reverse_charges->combobox("select `id`,`ledger_name` from `ledger` where `id`='". INTEREST_ON_PRINCIPLE_DUE . "'",1); 
					?>
			</select>
            </td>
</tr>

<!--<tr align="left">
        	<td valign="middle"><?php //echo $star;?></td>
			<td>Date</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="date" id="date"  class="basics" size="10" readonly  style="width:80px;"/></td>
		</tr>-->
        
        <tr align="left">
        	<td valign="middle"></td>
			<td>Bill Year </td>
            <td>&nbsp; : &nbsp;</td>
			<td>
            	<select name="year_id" id="year_id" style="width:142px;" onChange="get_period_for_reverse_charge(this.value, '<?php echo DEFAULT_PERIOD; ?>' );">
                	<?php echo $combo_state = $obj_reverse_charges->combobox02("select YearID,YearDescription from year where status='Y' and YearID = '".$_SESSION['default_year']."' ORDER BY YearID DESC", DEFAULT_YEAR, '' ); ?>
				</select>
             </td>
		</tr> 
        <?php //echo $period[0]['PeriodID'];?>       
        <tr align="left">
        	<td valign="middle"></td>
			<td>Bill Period </td>
            <td>&nbsp; : &nbsp;</td>
			<td>
                <select name="period_id" id="period_id" style="width:142px;" >
                
		          </select> 
				  <!--<span id="note_period" style="color:red;font-weight:bold;display:none;">*This changes will get affected in next month</span>-->
			</td>				  
		</tr>
		<tr>
		<td>
		<input type="hidden" name="bill_id" value="" id="bill_id" />
		<input type="hidden" name="role" value="<?php echo $_SESSION['role']; ?>" id="role" />
		<input type="hidden" name="year" value="<?php echo $year[0]['YearID'];?>" id="year" />
		</td>
		</tr>
		<tr>
		</tr>
		<tr  align="left">
		<td valign="middle">
		<?php echo $star;?>
		<td>Transaction Type: </td>
            <td>&nbsp; : &nbsp;</td>
			<td valign="middle">
			<input type="radio" name="fine_type" id="rev_charge" value="1"/> <label for="rev_charge" style="margin-left:3px"/>  Reverse charges</label> &nbsp;&nbsp;
			<input type="radio" name="fine_type" id="fine" value="2"/> <label for="fine" style="margin-left:3px">Fine </label>
			</td>
		</td>
		</tr>
		<tr>
		</tr>
		<tr>
		</tr>

<tr align="left">
        	<td valign="middle"><?php echo $star;?></td>
			<td>Enter amount</td>
            <td>&nbsp; : &nbsp;</td>
			<td><input type="text" name="amount" id="amount" /></td>
</tr>

<tr align="left">
        	<td valign="top"><?php echo $star;?></td>
			<td>Comments</td>
            <td>&nbsp; : &nbsp;</td>
			<td><textarea  id="comments"  name="comments" cols="50" rows="5"></textarea></td>
</tr>        
        
<tr><td colspan="4">&nbsp;</td></tr>
        <tr>
			<td colspan="4" align="center">
            <input type="button" name="view" id="view" value="View" style="border-color:#2e6da4; background-color:#337ab7; color:#fff; height:30px; width:100px;" class="btn btn-primary" onClick="view_report();">
            <input type="submit" name="insert" id="insert" value="Submit" style="width:100px; height:30px;color: #fff; background-color: #337ab7;border-color: #2e6da4;" class="btn btn-primary">
			<input type="button" name="Cancel" id="Cancel" value="Cancel" style="width:100px; height:30px;color: #fff; background-color: #337ab7;border-color: #2e6da4;" class="btn btn-primary"onClick="window.location.href='reverse_charges.php?&uid=<?php echo $_REQUEST['uid'];?>'">

            </td>
		</tr>



</table>
</center>
</form>
<?php 
  //echo "yr1".$year[0]['YearID'];
  //echo "yr2:".$_SESSION['default_year'];
//if($year[0]['YearID'] <> $_SESSION['default_year'])
{
	//echo '<center><font  color="#FF0000" style="font-weight:bold;font-size:16px;">Selected financial year has been locked,hence reversing bill charges is not allowed.</font></center>';
	?>
		<script>
			//document.getElementById('reverse_charges').style.display = 'none';
		</script>
	<?php			
}?>
<table align="center" width="100%">
<tr>
<td id="for_pgnation">
<?php
//echo "png:<br>";
$str1 = $obj_reverse_charges->pgnation($_REQUEST['uid']);
?>
</td>
</tr>

</table>
</div>
</div>


<?php include_once "includes/foot.php"; ?>
