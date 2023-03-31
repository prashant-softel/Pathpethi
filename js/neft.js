function getNeftDetails(neftID)
{
	var iden=new Array();
	iden=neftID.split("-");
	//alert(iden[1]);
	
	if(iden[0]=="delete")
	{
		var d=confirm("Are you sure , you want to delete it ???");
		if(d==1)
		{
			var obj = {"form" : "neft", "method" : iden[0], "neftId" : iden[1]};
			remoteCallNew("ajax/ajaxneft.php", obj, 'loadchanges');
		}
	}
	else
	{
		var obj = {"form" : "neft", "method" : iden[0], "neftId" : iden[1]};
		remoteCallNew("ajax/ajaxneft.php", obj, 'loadchanges');
	}
}

function loadchanges()
{
	var sResponse = getResponse(RESPONSETYPE_JSON, true);
	//alert(sResponse);
	if(sResponse != null)
	{
		document.getElementById('PaidBy').value = sResponse[0]['paid_by'];
		document.getElementById('PaidTo').value = sResponse[0]['paid_to'];
		document.getElementById('BankName').value = sResponse[0]['payer_bank'];
		document.getElementById('BranchName').value = sResponse[0]['payer_branch'];
		document.getElementById('Amount').value = sResponse[0]['amount'];
		document.getElementById('Date').value = sResponse[0]['date'];
		//document.getElementById('AcNumber').value = sResponse[0]['acc_no'];
		document.getElementById('TransationNo').value = sResponse[0]['transaction_no'];
		document.getElementById('Comments').value = sResponse[0]['comments'];
		
		document.getElementById("id").value = sResponse[0]['ID'];
		document.getElementById("insert").value="Update";
	}
}

function approve(id)
{
	var obj = {"form" : "neft", "method" : "edit", "neftId" : id};
	remoteCallNew("ajax/ajaxneft.php", obj, 'setConfirm');
}

function setConfirm()
{
	var sResponse = getResponse(RESPONSETYPE_JSON, true);
	if(sResponse != null)
	{
		document.getElementById('PaidBy').value = sResponse[0]['paid_by'];
		//document.getElementById('PaidBy').disabled = true;
		document.getElementById('PaidTo').value = sResponse[0]['paid_to'];
		//document.getElementById('PaidTo').disabled = true;
		document.getElementById('BankName').value = sResponse[0]['payer_bank'];
		//document.getElementById('BankName').disabled = true;
		document.getElementById('BranchName').value = sResponse[0]['payer_branch'];
		//document.getElementById('BranchName').disabled = true;
		document.getElementById('Amount').value = sResponse[0]['amount'];
		//document.getElementById('Amount').disabled = true;
		document.getElementById('Date').value = sResponse[0]['date'];
		//document.getElementById('Date').disabled = true;
		//document.getElementById('AcNumber').value = sResponse[0]['acc_no'];
		//document.getElementById('AcNumber').disabled = true;
		document.getElementById('TransationNo').value = sResponse[0]['transaction_no'];
		//document.getElementById('TransationNo').disabled = true;
		document.getElementById('Comments').value = sResponse[0]['comments'];
		
		document.getElementById("id").value = sResponse[0]['ID'];
		document.getElementById("insert").value="Approve";
		
		scroll(0, 0);
	}
}


function val()
{
	var PaidTo =trim(document.getElementById("PaidTo").value);
	var bank_name =trim(document.getElementById("BankName").value);
	var transaction_amount =trim(document.getElementById("Amount").value);
	var transaction_date =trim(document.getElementById("Date").value);
	var transaction_number =trim(document.getElementById("TransationNo").value);
	var bFlag = true;
	var sMsg ="";
	
	if(PaidTo == "" || PaidTo == 0 )
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Select Society Account";
		go_error();
		return false;
	}
	else if(transaction_amount == "")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter Transaction Amount ";
		document.getElementById("Amount").focus();
		
		go_error();
		return false;
	}
	else if(transaction_date == "")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter Transaction Date ";
		document.getElementById("Date").focus();
		
		go_error();
		return false;
	}
	else if(transaction_number == "")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter Transaction Number ";
		document.getElementById("TransationNo").focus();
		
		go_error();
		return false;
	}
	else if(bank_name == "")
	{
	//	alert(bank_name);
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter Payer Bank Name ";
		document.getElementById("BankName").focus();
		
		go_error();
		return false;
	}
	else if (confirm("Are You Sure You want to submit NEFT Details") == false)
	  {
      //alert("cancle apyment");
		return false;
    }
	else if(transaction_date != "")
	{
		ajaxValidateDate(true,transaction_date);	
		if(bFlag == false && trim(sMsg)!= "Success")
		{
			document.getElementById('error').style.display = '';	
			document.getElementById("error").innerHTML = trim(sMsg);
			go_error();
			return false;
		}
	}
	$('input[type=submit]').click(function(){
    $(this).attr('disabled', 'disabled');
});
   
//alert("submit false");


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
	
	return false;
}
function GetAccountDetails(SelectedID)
{
	//console.log(SelectedID);
$.ajax({
		url : "ajax/ajaxneft.php",
		type : "POST",
		data: {"method" : "GetAccountDetails","BankID" : SelectedID} ,
		success : function(data)
		{	
			if(SelectedID != '') 
			{
				//sMsg =trim(data);
				var sData = JSON.parse(data);
				//console.log(sData);
				//document.getElementById("BankACHeading").innerHTML =  Data[0]["BankName"] + "Account Number";
				//document.getElementById("BankIFSCHeading").innerHTML =  Data[0]["BankName"] + "IFSC Code";
				document.getElementById("BankAC").style.display = 'table-row';
				document.getElementById("BankIFSC").style.display = 'table-row';
				document.getElementById("lblAccountNo").innerHTML = sData[0]["AcNumber"];
				document.getElementById("lblIFSCCode").innerHTML = sData[0]["IFSC_Code"];
			}
			else
			{
				document.getElementById("BankAC").style.display = 'none';
				document.getElementById("BankIFSC").style.display = 'none';
			}
		}
	});	
}
function ajaxValidateDate(bSubmit,transaction_date)
{
	$.ajax({
			url : "ajax/ajaxneft.php",
			type : "POST",
			data: {"method" : "validateDate","transaction_date" : transaction_date} ,
			success : function(data)
			{	
				if(trim(data)!= "Success") 
				{
					bFlag = false;
					sMsg =trim(data);
					document.getElementById("error").innerHTML = sMsg;
				}
				else if(bSubmit == true)
				{
						document.getElementById('PaidTo').disabled=false;
					document.getElementById("mode").value = document.getElementById("insert").value;
					document.neft.submit();	
				}
				
				if(bSubmit == false)
				{
						go_error();	
				}
			}
		});	
		
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
}

