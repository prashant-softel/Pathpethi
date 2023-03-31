
function val()
{
	var loanType=trim(document.getElementById("loan_type").value);
	var bank_id =trim(document.getElementById("bank_id").value);
	var loanAmount = trim(document.getElementById("loan_amt").value);
	var LoanDate = trim(document.getElementById("loan_date").value);
	var MaturityDate = trim(document.getElementById("maturity_date").value);
	var IntrestRate = trim(document.getElementById("int_rate").value);
	var LoanCharge = trim(document.getElementById("loan_chrgs").value);
	var Mortgages = trim(document.getElementsById("mortgage").value);
	
	var bank_leaf = trim(document.getElementsById("bank_leaf").value);
	var cheque_number = trim(document.getElementsById("cheque_number").value);
	

	
	if(loanType=="")
	{
		show_error('Select Loan Type');
		go_error();
		return false;
	}
	
	if(bank_leaf=="")
	{
		show_error("Select Leaf");
		go_error();
		return false;
	}

	if(cheque_number=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Cheque Number"; 
		go_error();
		return false;
	}


	if(bank_id=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Select Bank"; 
		go_error();
		return false;
	}

	//////////////////////////////////////////////////////////////////////////////////////////
	if(loanAmount=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter Loan Amount";
		go_error();
		return false;
	}

	if(LoanDate=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Select Loan Date";
		go_error();
		return false;
	}
	if(MaturityDate=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter Maturity Date";
		go_error();
		return false;
	}
	if(IntrestRate=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter Loan Intrest Rate";
		go_error();
		return false;
	}
	if(LoanCharge=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter Loan Charges"; 
		go_error();
		return false;
	}
	
	if(Mortgages == 0 || Mortgages == '')
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Select Mortgage"; 
		go_error();
		return false;	
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////
	function LTrim( value )
	{
	var re = /\s*((\S+\s*)*)/;
	return value.replace(re, "$1");
	}
	function RTrim( value )
	{
	var re = /((\s*\S+)*)\s*/;
	return value.replace(re, "$1");
	}
	function trim( value )
	{
	return LTrim(RTrim(value));
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	
}

$(document).ready(function(){
	$("#int_rate").focusout(function() {
		var loan_amt = parseFloat($("#loan_amt").val());
		var days = parseInt($("#loan_period").val());
		var int_rate = parseFloat($("#int_rate").val());
		getCalculatedValue(loan_amt,days,int_rate);
	});

	$("#loan_period").focusout(function() {
		var days = parseInt($("#loan_period").val());
		if (!isNaN(days)) {
			//var usDateFormat = startDate.split('-');
			//var usDate = new Date(usDateFormat[1] + '/' + usDateFormat[0] + '/' + usDateFormat[2]);
			var d = new Date();
			var us = moment(d).format('L');
			var ist = us.split('/');
			console.log("Date ",d);
			$("#loan_date").val(ist[1] + '-' + ist[0] + '-' + ist[2])
			d.setDate(d.getDate() + days);
			var us = moment(d).format('L');
			var ist = us.split('/');
			console.log("Date ",ist);
			$("#maturity_date").val(ist[1] + '-' + ist[0] + '-' + ist[2])
		}
		else {
			//$("#loan_period").focus();
		}
	});

	$('#loan_type').change(function(){
        var id = $(this).prop("id");
        var select_val = $(this).val();

        if (select_val == 'Gold Loan') {
        	alert('gold')
        }
    });
});

function getCalculatedValue(loan_amt,days,int_rate){

	var installment_amt = 0;
	var months = 0;
	var intrest_amount = 0;
	var maturity_amount = 0;

	if (!isNaN(days) && !isNaN(loan_amt) && !isNaN(int_rate)) {
		
		months = Math.floor(days / 30);
		console.log(loan_amt, int_rate);
		interest_amount = Math.round((loan_amt*int_rate) / 100);
		console.log(interest_amount, loan_amt);
		maturity_amount = loan_amt + interest_amount;
		installment_amt = Math.round(maturity_amount / months);
		console.log(months, interest_amount, maturity_amount, installment_amt);
		$("#installment_amt").val(installment_amt);
		$("#interest_amt").val(interest_amount);
		$("#maturity_amt").val(maturity_amount);
	}
}

function getBankLeafs()
{
	var bank_id = $('#bank_id').val();

	$.ajax({
		url : "ajax/pp_loan.ajax.php",
		type : "POST",
		data: {"method":"getBankLeafs","bank_id":bank_id} ,
		success : function(data)
		{	
			var result = data.split('@@@');
			$('#bank_leaf').empty();
			$('#bank_leaf').append(result[1]);
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
			//hideLoader();
		}
	});
}

function getChequeNoList()
{
	var leafID = $('#bank_leaf').val();

	$.ajax({
		url : "ajax/pp_loan.ajax.php",
		type : "POST",
		data: {"method":"getChequeNo","leaf_id":leafID} ,
		success : function(data)
		{	
			
			var result = data.split('@@@');
			$('#cheque_no_span').empty();
			$('#cheque_no_span').append(result[1]);
			$('#cheque_no_tr').show();
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
			//hideLoader();
		}
	});
}

//Show Account Numbers

function getAccountNumbers() {
	let mortgage  = $('#mortgage').val();
	let member_id = $('#id').val();
	if(mortgage != 0){
		$.ajax({
			url:'ajax/pp_loan.ajax.php',
			type:'POST',
			data:{'method':'getAccountNumber', 'member_id': member_id ,'mortgage_type':mortgage},
			success:function(response){
				console.log(response);
				let result = response.split('@@@');
				result = JSON.parse("["+result[1]+"]")[0];
				console.log(result);
				let option = "";
				result.forEach(row => {
					option += "<option value='"+row.id+"'>"+row.ledger_name+"</option>";	
				});
				
				$('#mortgage_account').html('').html(option);

				try {
					
				} catch (error) {
					
				}
			}
		});
	}
	else{
		show_error('Please select mortgage') ;
		go_error();
		return false;
	}
}

