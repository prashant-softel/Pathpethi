<?php //include_once "ses_set_default.php"; ?>
<?php

?>
<?php
	include_once("includes/head_s.php");
	include_once("classes/dbconst.class.php");
	//include_once("includes/menu.php");
	include_once("classes/defaults.class.php");
	include_once("classes/include/dbop.class.php");
	$m_dbConnRoot = new dbop(true);
	$obj_defaults = new defaults($m_dbConn,$m_dbConnRoot );
	
	$default_society = $_SESSION['society_id'];
	if(isset($_REQUEST['sid']))
	{
		if($_REQUEST['sid'] == 'new')
		{
			$obj_defaults->getDefaults(0, true);
			?>
            	<script>window.location.href = "import.php"</script>
            <?php
		}
		else
		{
			$default_society = $_REQUEST['sid'];
		}
		
	}
	
	$default_year = 0;
	$default_period = 0;
	$default_interest_on_principle = 0;
	$default_penalty_to_member = 0;
	$default_bank_charges = 0;
	$default_tds_payable = 0;
	$default_impose_fine = 0;  /// impose fine
	$default_current_asset = 0;
	$default_bank_account = 0;
	$default_cash_account = 0;
	$default_due_from_member = 0;
	$default_income_expenditure_account = 0;
	$default_adjustment_credit = 0;
    $igst_service_tax = 0;
	$cgst_service_tax = 0;
	$sgst_service_tax = 0;
	$cess_service_tax = 0;
	$default_loan = 0;
	$default_saving_account = 0;
	$default_fixed_deposit = 0;
	$default_daily_deposit = 0;
	$default_monthly_deposit = 0;
	//$defaultEmailID = '';
		
	$defaultValues = $obj_defaults->getDefaults($default_society, false);
	$defaultValues[0][APP_DEFAULT_YEAR] = $_SESSION['default_year'];
	
	if($defaultValues <> '')
	{
		$default_year = $defaultValues[0][APP_DEFAULT_YEAR];
		$default_period = $defaultValues[0][APP_DEFAULT_PERIOD];
		$default_interest_on_principle = $defaultValues[0][APP_DEFAULT_INTEREST_ON_PRINCIPLE_DUE];
		$default_penalty_to_member = $defaultValues[0][APP_DEFAULT_PENALTY_TO_MEMBER];
		$default_bank_charges = $defaultValues[0][APP_DEFAULT_BANK_CHARGES];
		$default_tds_payable = $defaultValues[0][APP_DEFAULT_TDS_PAYABLE];
		$default_impose_fine = $defaultValues[0][APP_DEFAULT_IMPOSE_FINE];    // impose fine
		$default_current_asset = $defaultValues[0][APP_DEFAULT_CURRENT_ASSET];
		$default_bank_account = $defaultValues[0][APP_DEFAULT_BANK_ACCOUNT];
		$default_cash_account = $defaultValues[0][APP_DEFAULT_CASH_ACCOUNT];
		$default_due_from_member = $defaultValues[0][APP_DEFAULT_DUE_FROM_MEMBERS];
		$default_income_expenditure_account = $defaultValues[0][APP_DEFAULT_INCOME_EXPENDITURE_ACCOUNT];
		$default_adjustment_credit = $defaultValues[0][APP_DEFAULT_ADJUSTMENT_CREDIT];
        $igst_service_tax = $defaultValues[0][APP_DEFAULT_IGST];
		$cgst_service_tax = $defaultValues[0][APP_DEFAULT_CGST];
		$sgst_service_tax = $defaultValues[0][APP_DEFAULT_SGST];
		$cess_service_tax = $defaultValues[0][APP_DEFAULT_CESS];
		$default_loan = $defaultValues[0][APP_DEFAULT_LOAN];;
		$default_saving_account = $defaultValues[0][APP_DEFAULT_SAVING_ACCOUNT];;
		$default_fixed_deposit = $defaultValues[0][APP_DEFAULT_FIXED_DEPOSIT];;
		$default_daily_deposit = $defaultValues[0][APP_DEFAULT_DAILY_DEPOSIT];;
		$default_monthly_deposit = $defaultValues[0][APP_DEFAULT_MONTHLY_DEPOSIT];;
		//$defaultEmailID = $defaultValues[0][APP_DEFAULT_EMAILID];
	}
	 if($_SESSION['profile'][PROFILE_MANAGE_MASTER] == 1 && $_SESSION['role'] == ROLE_SUPER_ADMIN)
	 {
	 		$attrDisplay = ''; 
	 }
	 else
	 {
	  		$attrDisplay = 'disabled'; 
	}
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <script type="text/javascript" src="js/jquery_min.js"></script>
	<script type="text/javascript" src="js/ajax_new.js"></script>
	<script type="text/javascript" src="js/populateData.js"></script>    
	<script type="text/javascript" src="js/defaults.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
		});
        setTimeout('hide_error()',5000);	
    }
    function hide_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});
    }
	</script>
<style>
   select[disabled]
   {
  		background-color:#D3D3D3;
	}
</style> 
</head>
<body>
<?php
	//include_once('classes/dbconst.class.php');
?>
<br>
<div class="panel panel-info" id="panel" style="display:none">
<div class="panel-heading" id="pageheader">Set Defaults</div>

<center>
	<div id="error" style="color:#CC0000;font-weight:bold;"></div>
    <br>
    <table align='center'>
		<tr><td colspan="2" style="text-align:center;font-weight:bold;font-size:14px;padding-bottom:5px;color:#0033FF;">Current Society</td></tr>
        <tr>
			<input type="hidden" name="default_society" id="default_society" value="<?php echo $_SESSION['society_id']; ?>" />
            <td colspan="2" style="font-weight:bold;"><?php echo $obj_defaults->getSocietyName($_SESSION['society_id']); ?><a href="initialize.php">&nbsp;[Change]</a></td>
		</tr>
    </table>
    
    <br>
    <table align='center'>    
        <tr><td colspan="2" style="text-align:center;font-weight:bold;font-size:14px;padding-bottom:5px;color:#0033FF;">Year Defaults</td></tr>
        <tr>
			<td>Current Year : &nbsp;</td>
			<td><select name="default_year" id="default_year">
            	<?php
					if($default_year <> 0)
					{ 
						echo $combo_year = $obj_defaults->combobox("select YearID, YearDescription from year where status = 'Y' and YearID >='".$_SESSION['society_creation_yearid']."' ORDER BY YearID DESC", $default_year); 
                    }
                    else
                    {
						echo $combo_year = $obj_defaults->combobox("select YearID, YearDescription from year where status = 'Y' and YearID >='".$_SESSION['society_creation_yearid']."' ORDER BY YearID DESC", $default_year, "Please Select"); 
                    }
				?>		
            </select>
            </td>
		</tr>
       </table>
       <!--<table>
        <tr>
        	<td>Current Period : &nbsp;</td>
            <td> 
            	<select name="default_period" id="default_period">
                	<?php 
						if($default_year <> 0)
						{
							//echo $combo_period = $obj_defaults->combobox("select ID, Type from period where YearID = '" . $default_year . "'", $default_period); 
						}
						else
						{
							//echo '<option value="0">Please Select</option>';
						}
					?>
                </select>
            </td>
        </tr>
   </table>-->
   <br>
  <table style="width:80%">
  <tr><td><br><br></td></tr>
  <tr>
  <td>
  	<table style="float:left; width:47%">
  	<tr>
  		<td colspan="2" style="text-align:center;font-weight:bold;font-size:14px;padding-bottom:5px;color:#0033FF;">Ledger Defaults</td>
  	</tr>
    <tr><td><br></td></tr>
  	<tr>
    	<td>Interest On Principle Due : &nbsp;</td>
  		<td><select name="default_interest_on_principle" id="default_interest_on_principle"  <?php echo $attrDisplay;?>>
                	<?php 
						//echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ', ledger_name, id) from ledger where society_id = '" . $default_society .  "' ORDER BY ledger_name ASC", $default_interest_on_principle, "Please Select"); 
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC",$default_interest_on_principle, "Please Select");
					?>
        	</select>
    	</td>
  </tr>
  <tr>
       <td>Penalty To Member : &nbsp;</td>
       <td><select name="default_penalty_to member" id="default_penalty_to_member" <?php echo $attrDisplay;?>>
                	<?php 
						//echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ', ledger_name, id) from ledger where society_id = '" . $default_society .  "' ORDER BY ledger_name ASC", $default_penalty_to_member, "Please Select"); 
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $default_penalty_to_member, "Please Select"); 
					?>
            </select>
       </td>
  </tr>
  <tr>
      	<td>Bank Charges : &nbsp;</td>
        <td><select name="default_bank_charges" id="default_bank_charges"  <?php echo $attrDisplay;?>>
                	<?php 
						//echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ', ledger_name, id) from ledger where society_id = '" . $default_society .  "' ORDER BY ledger_name ASC", $default_bank_charges, "Please Select"); 
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $default_bank_charges, "Please Select"); 
					?>
            </select>
        </td>
  </tr>
  <tr>
     	<td>TDS Payable : &nbsp;</td>
        <td><select name="default_tds_payable" id="default_tds_payable"  <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $default_tds_payable, "Please Select"); 
					?>
                </select>
        </td>
  </tr>
  <tr>
     	<td>TDS Receivable : &nbsp;</td>
        <td><select name="default_tds_receivable" id="default_tds_receivable"  <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $default_tds_receivable, "Please Select"); 
					?>
                </select>
        </td>
  </tr>
  <tr>
       <td>Impose Fine : &nbsp;</td>
       <td><select name="default_impose_fine" id="default_impose_fine"  <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $default_impose_fine, "Please Select"); 
					?>
           </select>
       </td>
  </tr>
  <tr>
    	<td>Income & Expenditure A/C: &nbsp;</td>
        <td><select name="default_income_expenditure_account" id="default_income_expenditure_account" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $default_income_expenditure_account, "Please Select"); 
					?>
            </select>
        </td>
    </tr>
    <tr>
       	<td>Adjustment Credit: &nbsp;</td>
        <td><select name="default_adjustment_credit" id="default_adjustment_credit" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $default_adjustment_credit, "Please Select"); 
					?>
           </select>
       </td>
   </tr>
   <tr>
       	<td>Suspense A/C: &nbsp;</td>
        <td><select name="default_suspense_account" id="default_suspense_account" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $default_suspense_account, "Please Select"); 
					?>
           </select>
       </td>
   </tr>
   <tr>
       	<td>Ledger Round Off: &nbsp;</td>
        <td><select name="default_ledger_round_off" id="default_ledger_round_off" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select id, concat_ws(' - ' , ledgertable.ledger_name,'(',categorytbl.category_name, ')') from `ledger` as ledgertable join `account_category` as categorytbl on categorytbl.category_id=ledgertable.categoryid where society_id='" . $default_society .  "' ORDER BY ledgertable.ledger_name ASC", $default_ledger_round_off, "Please Select"); 
					?>
           </select>
       </td>
   </tr>
  </table>
  
  <table style="width:5%; float:left;"><tr><td></td></tr></table>
  
  <table style="float:left; width:48%">
  	<tr>
  		<td colspan="2" style="text-align:center;font-weight:bold;font-size:14px;padding-bottom:5px;color:#0033FF;">Account Category Defaults</td>
    </tr>
    <tr><td><br></td></tr>
    <tr>
       	<td>Current Asset : &nbsp;</td>
        <td><select name="default_current_asset" id="default_current_asset"  <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select category_id,category_name from account_category  ORDER BY category_name ASC", $default_current_asset, "Please Select"); 
					?>
            </select>
        </td>
    </tr>
    <tr>
       	<td>Fixed Asset : &nbsp;</td>
        <td><select name="default_fixed_asset" id="default_fixed_asset"  <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select category_id,category_name from account_category  ORDER BY category_name ASC", $default_fixed_asset, "Please Select"); 
					?>
            </select>
        </td>
    </tr>
     <tr>
        <td>Bank Account : &nbsp;</td>
        <td><select name="default_bank_account" id="default_bank_account" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select category_id,category_name from account_category  ORDER BY category_name ASC", $default_bank_account, "Please Select"); 
						
						//echo $combo_period = $obj_defaults->combobox("select category_id, concat_ws(' - ', category_name, category_id) from account_category  ORDER BY category_name ASC", $default_bank_account, "Please Select"); 
					?>
            </select>
       	</td>
     </tr>
     <tr>
       	<td>Cash Account : &nbsp;</td>
        <td><select name="default_cash_account" id="default_cash_account" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select category_id, category_name from account_category ORDER BY category_name ASC", $default_cash_account, "Please Select"); 
					?>
            </select>
        </td>
    </tr>
    <tr>
       <td>Due From Member : &nbsp;</td>
       <td><select name="default_due_from_member" id="default_due_from_member" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select category_id,category_name from account_category ORDER BY category_name ASC", $default_due_from_member, "Please Select"); 
					?>
           </select>
       </td>
    </tr>
    <tr>
       <td>Loan Category : &nbsp;</td>
       <td><select name="default_loan" id="default_loan" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select category_id,category_name from account_category ORDER BY category_name ASC", $default_loan, "Please Select"); 
					?>
           </select>
       </td>
    </tr>
	<tr>
       <td>Saving Account : &nbsp;</td>
       <td><select name="default_saving_account" id="default_saving_account" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select category_id,category_name from account_category ORDER BY category_name ASC", $default_saving_account, "Please Select"); 
					?>
           </select>
       </td>
    </tr>
	<tr>
       <td>Fixed Deposit Category : &nbsp;</td>
       <td><select name="default_fixed_deposit" id="default_fixed_deposit" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select category_id,category_name from account_category ORDER BY category_name ASC", $default_fixed_deposit, "Please Select"); 
					?>
           </select>
       </td>
    </tr>
	<tr>
       <td>Daily Deposit Category : &nbsp;</td>
       <td><select name="default_daily_deposit" id="default_daily_deposit" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select category_id,category_name from account_category ORDER BY category_name ASC", $default_daily_deposit, "Please Select"); 
					?>
           </select>
       </td>
    </tr>
	<tr>
       <td>Monthly Deposit Category : &nbsp;</td>
       <td><select name="default_monthly_deposit" id="default_monthly_deposit" <?php echo $attrDisplay;?>>
                	<?php 
						echo $combo_period = $obj_defaults->combobox("select category_id,category_name from account_category ORDER BY category_name ASC", $default_monthly_deposit, "Please Select"); 
					?>
           </select>
       </td>
    </tr>
  </table>
  </td>
 </tr>
  <tr>
   <td><br><br></td>
  </tr>
 
   </table>
    <br><br>
    <table>
        <tr>
			<td colspan="2" align="center"><input type="button" name="insert" id="insert" value="Save" onClick="ApplyValues();" style="width:120px; color:#FFF;background-color: #337ab7;" class="btn btn-primary"></td>
		</tr>
	</table>
    <br>
    <br>
</center>
</div>
<?php include_once "includes/foot.php"; ?>
