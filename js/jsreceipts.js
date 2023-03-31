
var CONST_CASH = 1;
var CONST_CHEQUE = 2;
var CONST_ACC_SAVING = $('#saving_account').val();
var CONST_ACC_DAILY = $('#daily_account').val();
var CONST_ACC_MONTHLY = $('#monthly_account').val();
var CONST_ACC_FIXED = $('#fixed_account').val();
var CONST_ACC_LOAN = $('#loan_account').val();
var ledger_id_category_based = [];

// get getLedgerCategoryList for selected member
function getLedgerCategoryAndLedgerList(ledger_id, account_type, member_id = 0){
	$.ajax({
		url : "ajax/ajaxreceipts.php",
		type : "POST",
		data: {"method":"getLedgerCategoryAndLedgerList","ledger_id":ledger_id, "account_type":account_type, "member_id":member_id} ,
		success : function(data)
		{	
			// try {
				var result = data.split('@@@');
				console.log(result[1]);
				if(result[1] != undefined && result[1] != null){
					
					let categoryDetails = JSON.parse("["+result[1]+"]");
					categoryDetails = categoryDetails[0];
					let categoryList = ledgerList = "";
					if(ledger_id){
						categoryList = ledgerList = "<option value='0'>Please select</option>";
					}
					let selected = (ledger_id) ? "selected":"";
					console.log(categoryDetails);
					let temp_category_arr = [];
					categoryDetails.forEach(category => {
						ledger_id_category_based.push({"category_id":category['category_id'] , "ledger_id" : category['ledger_id'], "ledger_name" : category['ledger_name']});
						
						if(!temp_category_arr.includes(category['category_id'])){
							categoryList += "<option value="+category['category_id']+" "+selected+">"+category['category_name']+"</option>";
							temp_category_arr.push(category['category_id']);
						}
						
						if(ledger_id){
							ledgerList += "<option value="+category['ledger_id']+" "+selected+">"+category['ledger_name']+"</option>"; 
						}						
					});
					console.log(ledgerList);
					console.log(categoryList);
					$('#ledgerList').empty();
					$('#ledgerList').append(ledgerList);
					$('#ledger_category').empty();
					$('#ledger_category').append(categoryList);	
					
					if(!ledger_id){
						loadLedgerList();
					}					
				}				
			// } catch (error) {
			// 	console.log('You have an error ',error);
			// }			
		}
	});
}

// Load Ledger List

function loadLedgerList(){

	let ledger_category = $("#ledger_category").val();
	console.log(ledger_category, ledger_id_category_based);
	if(ledger_category && ledger_id_category_based != undefined){
		let ledgerOption = "";
		ledger_id_category_based.forEach(data =>{
			if(data.category_id == ledger_category){
				ledgerOption += "<option value='"+data.ledger_id+"'>"+data.ledger_name+"</option>";
			}
		});
		$('#ledgerList').html("<option value=''>Please select</option>"); 
		$('#ledgerList').append(ledgerOption);
	}
}

// get Ledger Name against the selected member 
function getLedgerNameList(){

    var loan_type = $('#loan_type').val();
	var member_id = $('#mem_name').val();
	
	if(member_id != '')
	{
			$.ajax({
			url : "ajax/ajaxreceipts.php",
			type : "POST",
			data: {"method":"getLoanName","loan_type":loan_type,'member_id':member_id} ,
			success : function(data)
			{	
				console.log("data",data);
				var result = data.split('@@@');
				$('#loan_name').empty();
				$('#loan_name').append(result[1]);
			}
		});	
	}
}

//Laon
//Fixed Deposit
// Saving 
// get Selected Loan Details
function getLedgerDetails()
{
	console.log("Loan Details");
	var ledger_id = $('#ledgerList').val();
    $.ajax({
		url : "ajax/ajaxreceipts.php",
		type : "POST",
		data: {"method":"getLedgerDetails","ledger_id":ledger_id} ,
		success : function(data)
		{	
			console.log("data",data);
			var result = data.split('@@@');
			$('#loan_details_table').empty();
			$('#loan_details_table').append(result[1]);
			$('#loan_details_table').css('display','table');
			
			var receipt_type = $('#receipt_type').val();
			
			if(receipt_type == CONST_CASH)
			{
				$('#cash_bank_name').val(result[2]);
				getBankDepositSlip();
			}
			else if(receipt_type == CONST_CHEQUE)
			{
				$('#cheque_bank_name').val(result[2]);
				getBankDepositSlip();
			}
			
		}
	});
}

// get DepositSlip for Selected Bank
function getBankDepositSlip()
{
	var bank_id = $('#cheque_bank_name').val();
	
	if(bank_id != '')
	{
			$.ajax({
			url : "ajax/ajaxreceipts.php",
			type : "POST",
			data: {"method":"getBankDepositSlip","bank_id":bank_id} ,
			success : function(data)
			{	
				//console.log(data);
				var result = data.split('@@@');
				$('#bank_deposit_slip').empty();
				$('#bank_deposit_slip').append(result[1]);
			}
		});
	}
	
}

// Check Selected Account Type Exist for Member

function checkAccountExists(member_id, account_type){

	$.ajax({
		url:'ajax/ajaxreceipts.php',
		type:'POST',
		data:{'method':'checkAccountExists', 'member_id':member_id, 'account_type':account_type},
		success:function(response){
			console.log(response);
		}
	});



}

// Loan Type List
function getLoanList()
{
	var acc_type = $('#acc_type').val();
	
	if(acc_type == CONST_ACC_LOAN)
	{
		getLoanType();
		getLoanName();
	}
}

// Show and Hide the table as per user input
function getTemplate(){

	var member_id = $('#mem_name').val();
	var acc_type = $('#acc_type').val();
	var receipt_type = $('#receipt_type').val();
	var ledger_id = $("#ledger_id").val();
	getLedgerCategoryAndLedgerList(ledger_id, acc_type, member_id);
	
	
	// if(acc_type == CONST_ACC_SAVING){
		
	// 	getLedgerNameList();
	// }


	// if(acc_type == CONST_ACC_SAVING){
	// 	checkAccountExists(member_id, acc_type);
	// }

	document.getElementById('ledger_detail_table').style.display = "table";
	// if(acc_type == CONST_ACC_LOAN)
	// {	
		
	// }
	// else
	// {
	// 	document.getElementById('loan_details_table').style.display = "none";
	// 	document.getElementById('loan_table').style.display = "none";
	// }
	
	if(receipt_type == CONST_CASH)
	{
		document.getElementById('cash_table').style.display = "table";
		document.getElementById('cheque_table').style.display = "none";
	}
	else if(receipt_type == CONST_CHEQUE)
	{
		document.getElementById('cheque_table').style.display = "table";
		document.getElementById('cash_table').style.display = "none";
	}
}

// Showing error

function show_error(msg){

	document.getElementById('error').style.display = '';	
	document.getElementById("error").innerHTML = msg; 
	go_error();
}

// Validation on form submission

function val(){

	var member_id = $('#mem_name').val();
	var acc_type = $('#acc_type').val();
	var receipt_type = $('#receipt_type').val();
	var msg = "";
	var prefix = '';
	
	if(member_id == "" || member_id == 0)
	{
		show_error("Please Select Member Name");
		return false;
	}
	
	if(acc_type == "" || acc_type == 0)
	{
		show_error("Please Select Account Type "); 
		return false;
	}
	
	if(receipt_type == "" || receipt_type == 0)
	{
		show_error("Please Select Receipt Type");
		return false;
	}
	
	if(acc_type == CONST_ACC_LOAN)
	{
		var loan_type = $('#loan_type').val();
		var loan_name = $('#loan_name').val();
		
		if(loan_type == "" || loan_type == 0)
		{
			show_error("Please Select Loan Type");
			return false;
		}
		
		if(loan_name == "" || loan_name == 0)
		{
			show_error("Please Select Loan Name");
			return false;
		}
	}
	
	if(receipt_type == CONST_CASH)
	{
		prefix = "cash_";
	}
	else if(receipt_type == CONST_CHEQUE)
	{
		prefix = "cheque_";
		
		var cheque_no = $('#cheque_no').val();
		var bank_deposit_slip = $('#bank_deposit_slip').val();
		
		if(cheque_no == "" || cheque_no == 0)
		{
			show_error("Please Enter Cheque Number");
			return false;
		}
		
		if(bank_deposit_slip == "" || bank_deposit_slip == 0)
		{
			show_error("Please Select Deposit Slip");
			return false;
		}
	}
	
	if(prefix == '')
	{
		show_error("Something went wrong try again");
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Something went wrong try again"; 
		go_error();
		return false;
	}
	else
	{
		var bank_name = $('#'+prefix+'bank_name').val();
		var date = $('#'+prefix+'date').val();
		var amount = $('#'+prefix+'amt').val();
		
		if(bank_name == "" || bank_name == 0)
		{
			show_error("Please Select Bank Name");
			return false;
		}
		
		if(date == "" || date == 0)
		{
			show_error("Please Select Date");
			return false;
		}
		
		if(amount == "" || amount == 0)
		{
			show_error("Please Enetr Amount");
			return false;
		}
	}
}


// Show All Details PrePopulated for Loan Receipt


function prePopulateLoanDetails(member_id, loan_category_id, loan_ledger_id){

		$('#mem_name').val(member_id);
		$('#acc_type').val(CONST_ACC_LOAN);
		$('#receipt_type').val(CONST_CHEQUE);
		getTemplate();
		setTimeout(function(){
			$('#loan_type').val(loan_category_id);
		}, 200);
		setTimeout(function(){
			getLoanName();
		}, 300);
		setTimeout(function(){
			console.log("loan_ledger_id",loan_ledger_id);
			$('#loan_name').val(loan_ledger_id);
			getLoanDetails();
		}, 1000);
}

$(document).ready(function () {
	let account_type = $("#acc_type").attr('data-type');
	
	let member_id = $("#mem_name").val();
	if(account_type && member_id){
		console.log('check');
		$("#acc_type").val(account_type);
		getTemplate();
		getLedgerDetails();	
	}
	
});









