function get_year()
{
	populateDDListAndTrigger('select#year_id', 'ajax/get_unit.php?getyear', 'year', 'get_period', false);
}

function get_period(year_id)
{
	document.getElementById('error').innerHTML = 'Fetching Period. Please Wait...';
	if(year_id == null)
	{
		populateDDListAndTrigger('select#default_period', 'ajax/get_unit.php?getperiod&year=' + document.getElementById('year_id').value, 'period', 'hide_error', false);
	}
	else
	{
		populateDDListAndTrigger('select#default_period', 'ajax/get_unit.php?getperiod&year=' + year_id, 'period', 'hide_error', false);
	}
}

function ApplyValues()
{
	console.log('test 1');
	
	document.getElementById('error').style.display = 'block';
	document.getElementById('error').innerHTML = 'Saving defaults. Please wait...';
	
	var defaultYear = document.getElementById('default_year').value;
	var defaultPeriod = 0;//document.getElementById('default_period').value;
	var interestOnPrinciple = document.getElementById('default_interest_on_principle').value;
	var penaltyToMember = document.getElementById('default_penalty_to_member').value;
	var bankCharges = document.getElementById('default_bank_charges').value;
	var tdsPayable = document.getElementById('default_tds_payable').value;
	var imposeFine = document.getElementById('default_impose_fine').value;
	var currentAsset = document.getElementById('default_current_asset').value;
	var bankAccount = document.getElementById('default_bank_account').value;
	var cashAccount = document.getElementById('default_cash_account').value;	
	var dueFromMember = document.getElementById('default_due_from_member').value;
	var defaultIncomeExpenditureAccount = document.getElementById('default_income_expenditure_account').value;			
	var defaultSociety = document.getElementById('default_society').value;
	var defaultAdjustmentCredit = document.getElementById('default_adjustment_credit').value;
	var default_loan = document.getElementById('default_loan').value;
	var default_saving_account = document.getElementById('default_saving_account').value;
	var default_fixed_deposit = document.getElementById('default_fixed_deposit').value;
	var default_daily_deposit = document.getElementById('default_daily_deposit').value;
	var default_monthly_deposit = document.getElementById('default_monthly_deposit').value;
	//var defaultEmailID = document.getElementById('defaultEmailID').value;
	//var EmailValidation = ValidateEmail(true);
	if(defaultSociety == 0 /*|| !EmailValidation*/)
	{
		if(defaultSociety == 0)
		{
			document.getElementById('error').innerHTML = 'Please select a Society';
		}
		/*
		if(!EmailValidation)
		{
			document.getElementById('error').innerHTML = 'Please Enater Email ID';
		}*/
	}
	else
	{
		var sURL = "ajax/defaults.ajax.php";
		var obj = {'update':'', 'defaultYear':defaultYear, 
					'defaultPeriod':defaultPeriod, 
					'interestOnPrinciple':interestOnPrinciple,
					'penaltyToMember':penaltyToMember,
					'bankCharges':bankCharges,
					'tdsPayable':tdsPayable,
					'imposeFine':imposeFine,          // impose fine
					'currentAsset':currentAsset, 
					'dueFromMember':dueFromMember, 
					'bankAccount':bankAccount, 
					'cashAccount':cashAccount, 
					'societyid' : defaultSociety,
					'defaultIncomeExpenditureAccount' : defaultIncomeExpenditureAccount, 
					'defaultAdjustmentCredit' : defaultAdjustmentCredit,
					'default_loan' : default_loan,
					'default_saving_account' : default_saving_account,
					'default_fixed_deposit' : default_fixed_deposit,
					'default_daily_deposit' : default_daily_deposit,
					'default_monthly_deposit' : default_monthly_deposit
					};
		remoteCallNew(sURL, obj, 'defaultsApplied');
	}
}

function defaultsApplied()
{
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	console.log('respose ',sResponse);
	document.getElementById('error').innerHTML = sResponse;
	window.location.href = "defaults.php";
}
/*
function ValidateEmail(isFrmApply)
{
	var result = false;
	var EmailID;
	var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
	
	if(document.getElementById('defaultEmailID').value == "")
	{
		return false;	
	}
	else
	{
		EmailID = document.getElementById('defaultEmailID').value;
		result = emailReg.test(EmailID);	
	}
	
	if(result)
	{
		return result;		
	}
	else
	{
		if(!isFrmApply)
		{
			alert("Email Id Invalid.");
		}
		return result;	
	}
	
}*/