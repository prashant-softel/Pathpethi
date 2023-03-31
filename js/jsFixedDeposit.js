var currentLocation = window.location.href;

function getDetails(str)
{
	var iden	= new Array();
	iden		= str.split("-");
	
	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ?");
		if(conf==1)
		{
			
			remoteCall("ajax/ajaxFixedDeposit.php","form=FixedDeposit&method="+iden[0]+"&LedgerID="+iden[1],"loadchanges");
		}
	}
	else if(iden[0]=="edit" || iden[0]=="renew")
	{
		remoteCall("ajax/ajaxFixedDeposit.php","form=FixedDeposit&method="+iden[0]+"&LedgerID="+iden[1],"loadchanges");
	}
	else if(iden[0]=="view")
	{
		var LedgerID = iden[1];
		$.ajax({
        url: 'ajax/account_subcategory.ajax.php',
        type: 'POST',
		//dataType:"JSON",
        data: {"ledgerid": LedgerID, "method":"FetchGroup"},
        success: function(data){
			fetchdata = data.split("@@@");
			Groupid = fetchdata[1];
			popupFDLedgerWindow = window.open('view_ledger_details.php?lid=' + iden[1] + '&gid=' + Groupid ,'popupFDLedgerWindow','type=fullWindow,fullscreen,scrollbars=yes');
			
			/*alert(data);
            fetchdata = data.split("@@@");
			//Groupid = data[0];
			fd_id = fetchdata[1];
			//alert(Groupid);
			//alert(Groupid[1]);
			//"UpdateFDInterest.php?edt=".$result[$i]["ledger_id"]."&fdreadonly=1&fd_id=".$result[$i]['fd_id'];
			
			popupFDLedgerWindow = window.open('UpdateFDInterest.php?edt=' + iden[1] + '&fdreadonly=1&fd_id=' + fd_id);
			
			//popupFDLedgerWindow = window.open('view_ledger_details.php?lid=' + iden[1] + '&gid=' + Groupid ,'popupFDLedgerWindow','type=fullWindow,fullscreen,scrollbars=yes');*/
		 }
    	});
		
	}
}

function loadchanges()
{
	
	document.getElementById('insert').style.display = 'inline-block';
	var a		= sResponse.trim();
	var arr1	= new Array();
	//var arr2	= new Array();
	arr1		= a.split("@@@");
	//arr2		= arr1[1].split("#");
	//alert(arr1[1]);
	var res = JSON.parse(arr1[1]);
	//alert(JSON.stringify(res));
	
	document.getElementById('btnBack').style.display = 'none';
	
	if(arr1[0] == "edit" || arr1[0] == "view")
	{
		window.scroll(0, 0);
		document.getElementById("renew_header").innerHTML = '';
		document.getElementById("renew_header").style.display = 'none';
		//document.getElementById('FD_Close').style.display = 'table-row';
		//document.getElementById('int_table').style.display = 'table';
		var fdoptions = document.getElementsByClassName('fd_options'), i;

		for (i = 0; i < fdoptions.length; i += 1)
		{
			fdoptions[i].style.display = 'table-row';
		}
		$("select").prop("disabled",false);
		var fields = document.forms['FixedDeposit'];
			for(var i=0,fLen = fields.length;i < fLen;i++){
			 fields.elements[i].readOnly = false;
			}
			
		document.getElementById('new_entry').style.display = 'block';
		document.getElementById('btnAdd').style.display = 'none';
		document.getElementById('cancel').style.visibility = 'visible';
		document.getElementById('id').value =  res[0]['LedgerID'];
		document.getElementById('FD_Name').value =  res[0]['LedgerName'];
		document.getElementById('Principal_Amount').value =  res[0]['PrincipalAmount'];
		document.getElementById('Deposit_Date').value =  res[0]['DepositDate'];
		document.getElementById('FDR_No').value =  res[0]['FDRNO'];
		document.getElementById('Maturity_Date').value =  res[0]['MaturityDate'];
		document.getElementById('Interest_Rate').value =  res[0]['InterestRate'];
		document.getElementById('FD_Period').value =  res[0]['FDPeriod'];
		document.getElementById('Maturity_Amount').value =  res[0]['MaturityAmount'];
		document.getElementById('accrued_interest_legder').value =  res[0]['AccruedInterestLegder'];
		//document.getElementById('accrued_interest_amt').value =  res[0]['InterestAccrued'];
		document.getElementById('interest_legder').value =  res[0]['InterestLegder'];
		document.getElementById('interest_amt').value =  res[0]['Interest'];
		document.getElementById('Note').value =  res[0]['Note'];
		//document.getElementById("FD_Close").checked = (res[0]['FDClose'] == 1) ? true : false;
		//document.getElementById('FD_Close').disabled = false;
		document.getElementById('FD_Bank_Name').value =  res[0]['BankID'];
		document.getElementById('status').value =  res[0]['Status'];
		document.getElementById('Category').value =  res[0]['CategoryID'];
		document.getElementById('Interest_Note').value = "Interest Note for " + res[0]['LedgerName'];
		document.getElementById('ref').value = res[0]['Ref'];
		
		var is_readonly = document.getElementById('fd_readonly').value;
		if(is_readonly == 1)
		{
			//alert(is_readonly);
			document.getElementById('FD_Bank_Name').value =  res[0]['BankID'];
			document.getElementById('FD_Bank_Name').disabled = true;

			document.getElementById('Category').value =  res[0]['CategoryID'];
			document.getElementById('Category').disabled = true;
			
			document.getElementById('FD_Name_td').innerHTML = res[0]['LedgerName'];
			document.getElementById('fd_heading').innerHTML = res[0]['LedgerName'];
			
			document.getElementById('FDR_No_td').innerHTML = res[0]['FDRNO'];
			
			document.getElementById('Deposit_Date_td').innerHTML = res[0]['DepositDate'];
			
			document.getElementById('Maturity_Date_td').innerHTML = res[0]['MaturityDate'];

			document.getElementById('FD_Period_td').innerHTML = res[0]['FDPeriod'];
			
			document.getElementById('Principal_Amount_td').innerHTML = res[0]['PrincipalAmount'];
			
			document.getElementById('Interest_Rate_td').innerHTML = res[0]['InterestRate'];
			
			document.getElementById('Maturity_Amount_td').innerHTML = res[0]['MaturityAmount'];
			
			document.getElementById('Note_td').innerHTML = res[0]['Note'];
			
			document.getElementById('int_table').style.display = 'table';
			
			if(document.getElementById('fd_status').value == 'Closed')
			{
				document.getElementById("fd_header").innerHTML = '<font color="#00F" ><b>FD is ' + document.getElementById('fd_status').value + '.</b></font>';
			}
			else if(document.getElementById('fd_status').value == 'Renewed')
			{
				document.getElementById("fd_header").innerHTML = '<font color="#00CC33" ><b>FD is ' + document.getElementById('fd_status').value + '.</b></font>';				
			}
			else if(document.getElementById('fd_status').value == 'Active')
			{
				document.getElementById("fd_header").innerHTML = '<font color="#00CC33" ><b>FD is ' + document.getElementById('fd_status').value + '.</b></font>';				
			}
			else if(document.getElementById('fd_status').value == 'Pending')
			{
				document.getElementById("fd_header").innerHTML = '<font color="#FF0000" ><b>FD is ' + document.getElementById('fd_status').value + '.</b></font>';				
			}
			//'<font color="#FF0000"  style="font-size:21px;"><b>This Fixed Deposit Has Been  Closed</b></font>' ;
			
			document.getElementById('mode').value = "UpdateInterest";
			document.getElementById('insert').value = "UpdateInterest";
			
			var fd_id = document.getElementById('ref').value;
			$.ajax({
	        url: 'ajax/ajaxFixedDeposit2.php',
    	    type: 'POST',
			dataType:"JSON",
	        data: {"fd_id": fd_id, "method":"update_ledgers"},
    	    success: function(data)
			{
				var accrued_interest_legder = data[0]['accrued_interest_legder'];
				var interest_legder = data[0]['interest_legder'];
				if(accrued_interest_legder != 0)
				{
					document.getElementById('accrued_interest_legder').value = accrued_interest_legder;
					document.getElementById('accrued_interest_legder').disabled = true;
				}
				if(interest_legder != 0)
				{
					document.getElementById('interest_legder').value = interest_legder;
					document.getElementById('interest_legder').disabled = true;
				}
				
				var fd_close = data[1];
				if(fd_close == 1)
				{
					document.getElementById('int_table').style.display = "none";
					/*document.getElementById('Payout_tr').style.display = "none";
					document.getElementById('Close_tr').style.display = "none";
					document.getElementById('Renew_tr').style.display = "none";*/
				}
			}
			})			
		}
		else
		{	
			if(document.getElementById("FD_Close").checked == true || res[0]['FDRenew'] == 1)
			{
				document.getElementById('status').style.display = 'none';
				document.getElementById('FD_Close').disabled = true;
				document.getElementById('FD_Period').disabled = true;
					
			
				var f = document.forms['FixedDeposit'];
				for(var i=0,fLen=f.length;i<fLen;i++){
				  f.elements[i].readOnly = true;
				}
				//validateClose();
			
				var closefd = document.getElementsByClassName('closefd'), i;

				for (i = 0; i < closefd.length; i += 1)
				{
					closefd[i].readOnly = true;
				}
				$("select").prop("disabled",true);
				//alert('disabled');
			}	
			else if(res[0]['Status']  == 'Pending')
			{
				document.getElementById('new_entry').style.display = 'none';
				document.getElementById('status').style.display = 'block';
				document.getElementById('status_msg').innerHTML = '<font color="#FF0000"  style="font-size:21px;">Payment is pending for Fixed Deposit [' + res[0]['LedgerName']+ ' - ' + res[0]['LedgerID'] + ']</font> &nbsp;&nbsp<button id="pay_now" onClick="showStatus(' + res[0]['LedgerID'] + ',' + res[0]['BankID'] + ');" class="btn btn-primary">Pay Now</button>' ;
			}
			else
			{
				document.getElementById('status').style.display = 'none';
				//document.getElementById('FD_Bank_Name').disabled = false;
			
				var f = document.forms['FixedDeposit'];
				for(var i=0,fLen=f.length;i<fLen;i++){
				  f.elements[i].readOnly = false;
				}			
			}
		
		
			if(document.getElementById("FD_Close").checked == true)
			{	
				document.getElementById("renew_header").style.display = 'block';
				document.getElementById("renew_header").innerHTML = '<font color="#FF0000"  style="font-size:21px;"><b>This Fixed Deposit Has Been  Closed</b></font>' ;
			}
			else if(res[0]['FDRenew'] == 1)
			{
				document.getElementById("renew_header").style.display = 'block';
				document.getElementById("renew_header").innerHTML = '<font color="#FF0000"  style="font-size:21px;"><b>This Fixed Deposit Has Been  Renewed</b></font>' ;
			}
		
			document.getElementById("insert").value = "Update";
			if(document.getElementById("FD_Close").checked == true || res[0]['FDRenew'] == 1)
			{
				//document.getElementById('insert').disabled = true;	
				document.getElementById('insert').style.display = 'none';
			}
			else	if(res[0]['Status']  == 'Pending')
			{
				document.getElementById('insert').disabled = true;
			}
			else
			{
				document.getElementById('insert').disabled = false;	
			}
		
			//document.getElementById('Category').disabled = true;
			document.getElementById('btnViewReport').style.display = 'none';
		}
			//var LedgerID = res[0]['LedgerID'];
		var fd_id = document.getElementById('ref').value;
		$.ajax({
        url: 'ajax/ajaxFixedDeposit2.php',
        type: 'POST',
		dataType:"JSON",
        data: {"fd_id": fd_id, "method":"fetch_vouchers"},
        success: function(data)
		{
			//alert(data);
			if(data != "")
			{
				var my_data = [];
	  		    for(var j = 0; j < data.length; j++)
			    {
					if(data[j] != null)
					{
						my_data.push(data[j][0]);
					}
	 		    }
			    //alert(my_data);
		   			    
				var table = "<table style='border:1px solid #cccccc' width='100%'><tr><th style='width:50%;text-align:center;background-color:#337ab7; font-size:14px;color: #fff;padding-top: 5px;' colspan='7'>Vouchers</th></tr><tr style='text-align:center'><td>Sr.No.</td><td>Date</td><td>Particular</td><td>Voucher No</td><td>Amount</td><td>Note</td><td>Edit</td></tr>";
			    for(var i = 0; i < my_data.length; i++)
			    {
					var d = new Date(my_data[i]['Date']);
					var curr_date = d.getDate();
					var curr_month = d.getMonth();
					var curr_year = d.getFullYear();
					var dis_date = curr_date + "-" + (curr_month+1) + "-" + curr_year;
					
					if(my_data[i]['VoucherTypeID'] == 2 || my_data[i]['VoucherTypeID'] == 3)
					{
						//view_ledger_details.php?lid=1&gid=
						//table += "<tr style='text-align:center'><td>" + (i+1) + "</td><td>" + dis_date + "</td><td><div style='cursor:pointer;color:#06F' onClick=ViewVoucherDetail('" + my_data[i]['ledger_id'] + "','" + my_data[i]['VoucherTypeID'] + "','" + my_data[i]['id'] + "');>" + my_data[i]['ledger_name'] + "</div></td><td><div style='cursor:pointer;color:#06F' onClick=ViewVoucherDetail('" + my_data[i]['ledger_id'] + "','" + my_data[i]['VoucherTypeID'] + "','" + my_data[i]['id'] + "');>" + my_data[i]['VoucherNo'] + "</div></td><td>" + my_data[i]['Debit'] + "</td><td>" + my_data[i]['Note'] + "</td><td><a href='bank_statement.php?LedgerID=" + my_data[i]['ledger_id'] + "' target='_blank'><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a></td></tr>";
						table += "<tr style='text-align:center'><td>" + (i+1) + "</td><td>" + dis_date + "</td><td><div style='cursor:pointer;color:#06F' onClick=window.open('view_ledger_details.php?lid=" + my_data[i]['ledger_id'] + "&gid=" + my_data[i]['group_id'] + "')>" + my_data[i]['ledger_name'] + "</div></td><td><div style='cursor:pointer;color:#06F' onClick=ViewVoucherDetail('" + my_data[i]['ledger_id'] + "','','" + my_data[i]['VoucherTypeID'] + "','" + my_data[i]['id'] + "');>" + my_data[i]['VoucherNo'] + "</div></td><td>" + my_data[i]['Debit'] + "</td><td>" + my_data[i]['Note'] + "</td><td><a href='bank_statement.php?LedgerID=" + my_data[i]['ledger_id'] + "' target='_blank'><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a></td></tr>";
					}
					else
					{
						table += "<tr style='text-align:center'><td>" + (i+1) + "</td><td>" + dis_date + "</td><td><div style='cursor:pointer;color:#06F' onClick=window.open('view_ledger_details.php?lid=" + my_data[i]['ledger_id'] + "&gid=" + my_data[i]['group_id'] + "')>" + my_data[i]['ledger_name'] + "</div></td><td><div style='cursor:pointer;color:#06F' onClick=ViewVoucherDetail('" + my_data[i]['ledger_id'] + "','','" + my_data[i]['VoucherTypeID'] + "','" + my_data[i]['id'] + "');>" + my_data[i]['VoucherNo'] + "</div></td><td>" + my_data[i]['Debit'] + "</td><td>" + my_data[i]['Note'] + "</td><td><a href='VoucherEdit.php?Vno="+ my_data[i]['VoucherNo'] + "&pg=' target='_blank'><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a></td></tr>";
					}
			    }
			    table += "</table>";
		   		
			    document.getElementById('put_voucher_table').innerHTML = table;
			 }
		 },
		 fail: function()
		 {
			 alert("fail");
			 //hideLoader();
		 },
		 error: function(XMLHttpRequest, textStatus, errorThrown) 
		 {
			 alert("error");
			 //hideLoader();
		 }
    	 });		
	}
	
	/*if(arr1[0] == "renew")
	{
		window.scroll(0, 0);
		var fdoptions = document.getElementsByClassName('fd_options'), i;
		document.getElementById('int_table').style.display = 'table';

		for (i = 0; i < fdoptions.length; i += 1)
		{
			fdoptions[i].style.display = 'none';
		}
		
		document.getElementById('new_entry').style.display = 'block';
		document.getElementById('btnAdd').style.display = 'none';
		document.getElementById('cancel').style.visibility = 'visible';
		document.getElementById('id').value = res[0]['LedgerID'];
		document.getElementById('FD_Name').value = res[0]['LedgerName'];
		document.getElementById('Principal_Amount').value = '';
		document.getElementById('Deposit_Date').value = res[0]['MaturityDate'];
		document.getElementById('FDR_No').value = res[0]['FDRNO'];
		document.getElementById('Maturity_Date').value = '';
		document.getElementById('Interest_Rate').value = '';
		document.getElementById('FD_Period').value = '';
		document.getElementById('Maturity_Amount').value = '';
		document.getElementById('accrued_interest_legder').value = '';
		document.getElementById('accrued_interest_amt').value = '';
		document.getElementById('interest_legder').value = '';
		document.getElementById('interest_amt').value = '';
		//document.getElementById('accrued_interest_legder').value =  res[0]['AccruedInterestLegder'];
		document.getElementById('accrued_interest_amt').value =  res[0]['InterestAccrued'];
		document.getElementById('interest_legder').value =  res[0]['InterestLegder'];
		document.getElementById('interest_amt').value =  res[0]['Interest'];
		document.getElementById('Note').value = '';
		document.getElementById('FD_Bank_Name').value =  res[0]['BankID'];
		document.getElementById('status').value =  res[0]['Status'];
		document.getElementById('Category').value =  res[0]['CategoryID'];
		document.getElementById('ref').value = res[0]['Ref'];
		document.getElementById('Category').disabled = true;
		document.getElementById('FD_Bank_Name').disabled = true;
		document.getElementById('btnViewReport').style.display = 'none';
		
		 if(res[0]['FDRenew'] == 1)
		 {
			 alert("renewed");
			document.getElementById('status').style.display = 'none';
			document.getElementById('FD_Close').disabled = true;
			document.getElementById('FD_Period').disabled = true;
					
			var f = document.forms['FixedDeposit'];
			for(var i = 0,fLen = f.length;i < fLen;i++)
			{
			 	f.elements[i].readOnly = true;
			}
			
			$("select").prop("disabled",true);
		}
		 if(res[0]['Status']  == 'Pending')
		{
				document.getElementById('new_entry').style.display = 'none';
				document.getElementById('status').style.display = 'block';
				document.getElementById('status_msg').innerHTML = '<font color="#FF0000"  style="font-size:21px;">Payment is pending for Fixed Deposit [' + res[0]['LedgerName']+ ' - ' + res[0]['LedgerID'] + ']</font> &nbsp;&nbsp<button id="pay_now" onClick="showStatus(' + res[0]['LedgerID'] + ',' + res[0]['BankID'] + ');" class="btn btn-primary">Pay Now</button>' ;
				document.getElementById("renew_header").style.display = 'none';
		}
		else if(res[0]['PrincipalAmount']  == 0)
		{
				document.getElementById('new_entry').style.display = 'none';
				document.getElementById('status').style.display = 'block';
				document.getElementById('status_msg').innerHTML = '<font color="#FF0000"  style="font-size:21px;">Before renewing kindly set principal amount  for  fixed deposit [' + res[0]['LedgerName'] + ']</font>' ;
				document.getElementById("renew_header").style.display = 'none';
		}
		else
		{
			document.getElementById('status').style.display = 'none';
			document.getElementById("renew_header").style.display = 'block';
			document.getElementById("renew_header").innerHTML = '<font color="#2e6da4"  style="font-size:21px;"><b>Renewing  Fixed Deposit [' + res[0]['LedgerName']+ ' - ' + document.getElementById('FDR_No').value + '] </b></font>' ;
			
			var f = document.forms['FixedDeposit'];
			for(var i=0,fLen=f.length;i<fLen;i++)
			{
			  f.elements[i].readOnly = false;
			}			
		}
		
		
		document.getElementById("insert").value = "Renew";
		if(document.getElementById("FD_Close").checked == true ||  res[0]['FDRenew'] == 1)
		{
			document.getElementById('insert').disabled = true;	
		}
		else if(res[0]['Status']  == 'Pending')
		{
			document.getElementById('insert').disabled = true;
		}
		else
		{
			document.getElementById('insert').disabled = false;	
		}
	}
	else if(arr1[0] == "delete")
	{
		//window.location.href ="../FixedDeposit.php?mst&"+arr1[1]+"&mm";
	}*/
}

/*
function ledgerChange(ledger)
{
	if(ledger.value == 0)
	{
		document.getElementById('Deposit_Date').value = '';
		document.getElementById('Principal_Amount').value = '';
		document.getElementById('FD_Name').value = '';	
	}
	else
	{
		document.getElementById('FD_Name').value = document.getElementById('LedgerID').options[document.getElementById('LedgerID').selectedIndex].text;;
		getOpeningBalanceAndDate();
	}
}

function getOpeningBalanceAndDate()
{
	document.getElementById('error').style.display = 'block';
	document.getElementById('error').innerHTML = 'Fetching Opening Balance...';
	var LedgerID = document.getElementById('LedgerID').value;
	
	var sURL = "ajax/ajaxFixedDeposit.php";
	var obj = {'getbalance':'', 'ledger': LedgerID };
	remoteCallNew(sURL, obj, 'balanceFetched');
}

function balanceFetched()
{
	document.getElementById('error').innerHTML = '';
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	aryResult = sResponse.split('@@@');
	
	if(aryResult[0] != null)
	{
		document.getElementById('Deposit_Date').value = aryResult[0].trim();
	}
	if(aryResult[1] != null)
	{
		document.getElementById('Principal_Amount').value = aryResult[1].trim();
	}
}*/

function hide_accrued_int()
{
	if(document.getElementById('FD_Bank_Payout').checked == true)
	{
		document.getElementById('accrued_interest_tr').style.display = "none";
	}
	else
	{
		document.getElementById('accrued_interest_tr').style.display = "table-row";
	}
}

function update_mode(fd_id)
{
	if(document.getElementById('FD_Close').checked == true)
	{
		var ledger_id = document.getElementById('id').value;
		var acc_int_ledger = document.getElementById('accrued_interest_legder').value;
		$.ajax({
	    url: 'ajax/ajaxFixedDeposit2.php',
    	type: 'POST',
		dataType:"JSON",
    	data: {"fd_id": fd_id, "ledger_id": ledger_id, "acc_int_led": acc_int_ledger, "method": "fetch_acc_int"},
	    success: function(data)
		{
			//alert(data);
			if(data[1] == 0)
			{
				document.getElementById('accrued_interest_amt').value = data[0];
				//document.getElementById('mode').value = "Update";
				document.getElementById('FD_Renew').hidden = true;
				document.getElementById('on_close').style.display = "table-row";
				document.getElementById('Maturity_Amount_td').innerHTML = "<input type='text' id='Maturity_Amount' name='Maturity_Amount' value='" + document.getElementById('Maturity_Amount_td').innerHTML + "' />";
				document.getElementById('Maturity_Date_td').innerHTML = "<input type='text' id='Maturity_Date' name='Maturity_Date' value='" + document.getElementById('Maturity_Date_td').innerHTML + "' />";
				if(document.getElementById('FD_Bank_Payout').checked == true)
				{
					document.getElementById('accrued_interest_tr').style.display = "table-row";
				}
			}
			else if(data[1] == 1)
			{
				alert("FD is already closed.");
				window.location.href = "FixedDeposit.php";
			}
		}
		})
	}
	else if(document.getElementById('FD_Close').checked == false)
	{
		//document.getElementById('mode').value = "UpdateInterest";
		document.getElementById('FD_Renew').hidden = false;
		document.getElementById('on_close').style.display = "none";
		document.getElementById('Maturity_Amount_td').innerHTML = document.getElementById('Maturity_Amount').value;
		document.getElementById('Maturity_Date_td').innerHTML = document.getElementById('Maturity_Date').value;
	}
}

function for_renew(fd_id)
{	
	if(document.getElementById('FD_Renew').checked == true)
	{
		document.getElementById('FD_Close').hidden = true;
		document.getElementById('for_renew').style.display = "block";
		document.getElementById('renew_same_fd').checked = true;
		populate_fd_name_no();
		if(document.getElementById('FD_Bank_Payout').checked == false)
		{
			var ledger_id = document.getElementById('id').value;
			var acc_int_ledger = document.getElementById('accrued_interest_legder').value;
			$.ajax({
		    url: 'ajax/ajaxFixedDeposit2.php',
    		type: 'POST',
			dataType:"JSON",
    		data: {"fd_id": fd_id, "ledger_id": ledger_id, "acc_int_led": acc_int_ledger, "method": "fetch_acc_int"},
	    	success: function(data)
			{
				document.getElementById('accrued_interest_amt').value = data[0];
				document.getElementById('on_close').style.display = "table-row";
			}
			})
		}
		//document.getElementById('mode').value = "Renew";
	}
	else if(document.getElementById('FD_Renew').checked == false)
	{
		document.getElementById('FD_Close').hidden = false;
		document.getElementById('for_renew').style.display = "none";
		document.getElementById('on_close').style.display = "none";
		//document.getElementById('mode').value = "UpdateInterest";
	}
}

function validateClose(ledger_id)
{
/*	$.ajax({
    url: 'ajax/ajaxFixedDeposit2.php',
    type: 'POST',
	dataType:"JSON",
    data: {"ledgerid": ledger_id, "method":"fetch_all_dets"},
    success: function(data)
	{
		var deposit_date = data[0]['deposit_date'];
		var  maturity_date = data[0]['maturity_date']; 
		var int_rate = data[0]['int_rate']; 
		var maturity_amt = data[0]['maturity_amt']; 
*/		
		var deposit_date = document.getElementById('Deposit_Date_td').innerHTML;
		var maturity_date = document.getElementById('Maturity_Date').value;
		var int_rate = document.getElementById('Interest_Rate_td').innerHTML;
		var maturity_amt = document.getElementById('Maturity_Amt').value;
		if(document.getElementById("FD_Close").checked ==  true)
		{
			if(deposit_date == '' ||  maturity_date == '' ||   int_rate == '' ||   maturity_amt == '')
			{
				document.getElementById("error").innerHTML = "Enter Values For Deposit Date , Maturity Date , Interest Rate,Maturity Amount";
				go_error();
			}
			else
			{
				var closefd = document.getElementsByClassName('closefd'), i;

				for (i = 0; i < closefd.length; i += 1)
				{
					//closefd[i].style.visibility = 'visible';
					closefd[i].style.display = 'table-row';
				}
				
				var renewfd = document.getElementsByClassName('renewfd'), m;

				for (m = 0; m < renewfd.length; m+= 1)
				{
					//renewfd[m].style.visibility = 'hidden';
					renewfd[m].style.display = 'none';
				}  
			}
		}
		else
		{
			var closefd = document.getElementsByClassName('closefd'), i;

			for (i = 0; i < closefd.length; i += 1)
			{
				//closefd[i].style.visibility = 'hidden';
				closefd[i].style.display = 'none';
			} 
		}
/*	}
	})	
*/
}

function validateData2()
{
	document.getElementById('mode').value = document.getElementById('insert').value;
	//document.getElementById('ref').value = "";
	
	return true;
}

function validateData()
{
	var all_correct = 0;
	var mode = document.getElementById('mode').value;
	if(mode == 'UpdateInterest') 
	{
		if(document.getElementById('FD_Close').checked == false && document.getElementById('FD_Renew').checked == false) //i.e. update only interest
		{
			if(document.getElementById('FD_Bank_Payout').checked == true)
			{
				var interest_legder = document.getElementById('interest_legder').value;
				var interest_amt = document.getElementById('interest_amt').value;
				var Interest_Date = document.getElementById('Interest_Date').value;
				var Interest_Note = document.getElementById('Interest_Note').value;
				if(interest_legder == 0 || interest_amt == 0 || Interest_Date == '' || Interest_Note == '')
				{
					alert('Please fill all * marked fields.');
					return false;
				}
				else
				{
					document.getElementById('FD_Bank_Name').disabled = false;
					document.getElementById('Category').disabled = false;
					document.getElementById('interest_legder').disabled = false;
					document.getElementById('accrued_interest_legder').disabled = false;
					return true;
				}
			}
			else if(document.getElementById('FD_Bank_Payout').checked == false)
			{
				var accrued_interest_legder = document.getElementById('accrued_interest_legder').value;
				var interest_legder = document.getElementById('interest_legder').value;
				var interest_amt = document.getElementById('interest_amt').value;
				var Interest_Date = document.getElementById('Interest_Date').value;
				var Interest_Note = document.getElementById('Interest_Note').value;
				if(accrued_interest_legder == 0 || interest_legder == 0 || interest_amt == 0 || Interest_Date == '' || Interest_Note == '')
				{	
					alert('Please fill all * marked fields.');
					return false;
				}
				else
				{
					document.getElementById('FD_Bank_Name').disabled = false;
					document.getElementById('Category').disabled = false;
					document.getElementById('interest_legder').disabled = false;
					document.getElementById('accrued_interest_legder').disabled = false;
					return true;
				}
			}
		}
		else if(document.getElementById('FD_Close').checked == true) //i.e. Close FD
		{
			var accrued_interest_legder = document.getElementById('accrued_interest_legder').value;
			var interest_legder = document.getElementById('interest_legder').value;
			var interest_amt = document.getElementById('interest_amt').value;
			var Interest_Date = document.getElementById('Interest_Date').value;
			var Interest_Note = document.getElementById('Interest_Note').value;
			var accrued_interest_amt = document.getElementById('accrued_interest_amt').value;
			var Maturity_Amount = document.getElementById('Maturity_Amount').value;
			var Maturity_Date = document.getElementById('Maturity_Date').value;
			if(accrued_interest_legder == 0 || interest_legder == 0 || interest_amt == 0 || Interest_Date == '' || Interest_Note == '' || accrued_interest_amt == 0 || Maturity_Amount == 0 || Maturity_Date == '')
			{
				alert('Please fill all * marked fields.');
				return false;
			}
			else
			{
				var conf = confirm("Are you sure, you want to Close the FD?");
				if(conf==1)
				{
					document.getElementById('FD_Bank_Name').disabled = false;
					document.getElementById('Category').disabled = false;
					document.getElementById('interest_legder').disabled = false;
					document.getElementById('accrued_interest_legder').disabled = false;
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		else if(document.getElementById('FD_Renew').checked == true) //i.e. Renew FD
		{
			var FD_Name_RN = document.getElementById('FD_Name_RN').value;
			var FDR_No_RN = document.getElementById('FDR_No_RN').value;
			var DoD_RN = document.getElementById('DoD_RN').value;
			var DoM_RN = document.getElementById('DoM_RN').value;
			var principal_amt_RN = document.getElementById('principal_amt_RN').value;
			var ROI_RN = document.getElementById('ROI_RN').value;
			var maturity_amt_RN = document.getElementById('maturity_amt_RN').value;
			//var Note_RN = document.getElementById('Note_RN').value;
			if(FD_Name_RN == '' || FDR_No_RN == '' || DoD_RN == '' || DoM_RN == '' || principal_amt_RN == 0 || ROI_RN == 0 || maturity_amt_RN == 0)
			{
				alert('Please fill all * marked fields.');
				return false;
			}
			else
			{
				var conf = confirm("Are you sure, you want to Renew the FD?");
				if(conf==1)
				{
					document.getElementById('FD_Bank_Name').disabled = false;
					document.getElementById('Category').disabled = false;
					document.getElementById('interest_legder').disabled = false;
					document.getElementById('accrued_interest_legder').disabled = false;
					return true;
				}
				else
				{
					return false;
				}
			}			
		}
	}
}

function submitForm(status)
{
	if(status == true)
	{	
		document.FixedDeposit.submit();
	}	
}

function populate_fd_name_no()
{
	if(document.getElementById('renew_same_fd').checked == true)
	{
		document.getElementById('FD_Name_RN').value = document.getElementById('FD_Name_td').innerHTML;
		document.getElementById('FDR_No_RN').value = document.getElementById('FDR_No_td').innerHTML;
	}
	else
	{
		document.getElementById('FD_Name_RN').value = '';
		document.getElementById('FDR_No_RN').value = '';
	}
}

function validateData1()
{
	
	//var  fd_bank_name = document.getElementById('FD_Bank_Name').value;
	//var fd_name = removeEmptySpaces(document.getElementById('FD_Name').value);
	//var  category_name = removeEmptySpaces(document.getElementById('Category').value); 
	//var fdr_no = removeEmptySpaces(document.getElementById('FDR_No').value); 
	//var  note = removeEmptySpaces(document.getElementById('Note').value); 
	var isfdClose = document.getElementById('FD_Close').checked; 
	/*
	var bank_name = document.getElementById('Bank_Name').value; 
	var cheque_number = document.getElementById('ChequeNumber').value; */
	//var deposit_date = removeEmptySpaces(document.getElementById('Deposit_Date').value);
	//var maturity_date = removeEmptySpaces(document.getElementById('Maturity_Date').value);
	//var principal_amount = removeEmptySpaces(document.getElementById('Principal_Amount').value);
	//var int_rate = removeEmptySpaces(document.getElementById('Interest_Rate').value);
	//var maturity_amount = removeEmptySpaces(document.getElementById('Maturity_Amount').value);
	var isfdRenew = false;
	var accured_int_ledger = 0; 
	var int_ledger = 0;
	var accured_int_amt = 0;
	var int_amt = 0;
	
	if(document.getElementById('insert').value == 'Renew' ||  isfdClose == true)
	{
		isfdRenew = true;
		accured_int_ledger = document.getElementById('accrued_interest_legder').value; 
		int_ledger = document.getElementById('interest_legder').value;
		accured_int_amt = removeEmptySpaces(document.getElementById('accrued_interest_amt').value);
		int_amt = removeEmptySpaces(document.getElementById('interest_amt').value);
	}
	
	var bsubmitForm = true;
	
	if(fd_bank_name == 0)
	{
		bsubmitForm = false;
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Select  FD Bank Name";
		document.getElementById('FD_Bank_Name').focus();
		go_error();
	}
	else if(category_name == 0)
	{
		bsubmitForm = false;
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Select  Category";
		document.getElementById('Category').focus();
		go_error();
	}
	else if(fd_name == "")
	{
		bsubmitForm = false;
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter  FD Name";
		document.getElementById('FD_Name').focus();
		go_error();
	}
	else if(fdr_no == "")
	{
		bsubmitForm = false;
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter  FDR No";
		document.getElementById('FDR_No').focus();
		go_error();		
	}	
	else if(deposit_date == "")
	{
		bsubmitForm = false;
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter  Deposit Date";
		document.getElementById('Deposit_Date').focus();
		go_error();		
	}	
	else if(maturity_date == "")
	{
		bsubmitForm = false;
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter  Maturity Date";
		document.getElementById('Maturity_Date').focus();
		go_error();		
	}	
	else if(principal_amount == "")
	{
		bsubmitForm = false;
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter  Principal  Amount";
		document.getElementById('Principal_Amount').focus();
		go_error();		
	}	
	else if(int_rate == "")
	{
		bsubmitForm = false;
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter  Rate of Interest";
		document.getElementById('Interest_Rate').focus();
		go_error();		
	}	
	else if(maturity_amount == "")
	{
		bsubmitForm = false;
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter  Maturity Amount";
		document.getElementById('Maturity_Amount').focus();
		go_error();		
	}	
	else if(note == "")
	{
		bsubmitForm = false;
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter  Note";
		document.getElementById('Note').focus();
		go_error();
	}	
	else if((isfdRenew == true  || isfdClose  == true ) &&  (accured_int_ledger == 0 || accured_int_ledger == ""))
	{
		bsubmitForm = false;
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter Accrued Interest on FD Ledger Name";
		window.scroll(0, 0);
		go_error();
	}
	else if((isfdRenew == true  || isfdClose  == true ) &&  (int_ledger == 0 || int_ledger == ""))
	{
		bsubmitForm = false;
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter  Interest on FD Ledger Name";
		window.scroll(0, 0);
		go_error();
	}
	else if((isfdRenew == true  || isfdClose  == true ) && accured_int_amt == "" )
	{
		bsubmitForm = false;
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter Accrued Interest on FD Ledger Amount";
		document.getElementById('accrued_interest_amt').focus();
		go_error();
	}
	else if((isfdRenew == true  || isfdClose  == true ) && int_amt == "")
	{
		bsubmitForm = false;
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter Interest on FD Ledger Amount";
		document.getElementById('interest_amt').focus();
		go_error();
	}
	
	if(isfdClose  == true)
	{
		var iTotalAmount =  parseFloat(principal_amount) + parseFloat(accured_int_amt) + parseFloat(int_amt);
		maturity_amount = parseFloat(maturity_amount);
		
		if(iTotalAmount != maturity_amount)
		{
			bsubmitForm = false;
			document.getElementById('error').style.display = '';	
			document.getElementById("error").innerHTML = "Maturity Amount Must Be Total of  Principal Amount,Accrued Interest Amount,Interest Amount.Expected Maturity Amount " + iTotalAmount ;
			document.getElementById('Maturity_Amount').focus();
			go_error();
		}
				
		
	}
	
	/*else if(isfdClose == true &&  bank_name == 0 )
	{
		bsubmitForm = false;
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Select Bank To Deposit Maturity Amount";
		window.scroll(0, 0);
		go_error();
	}
	else if(isfdClose == true &&  cheque_number.length == 0 )
	{
		bsubmitForm = false;
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter Cheque Number";
		window.scroll(0, 0);
		go_error();
	}*/
	
	if(bsubmitForm == true)
	{
		var isDepositDateValid = ValidateDate("Deposit_Date",true);
		var isMaturityDateValid = ValidateDate("Maturity_Date",false);
		
		if(isDepositDateValid  == false ||  isMaturityDateValid == false )
		{
			bsubmitForm = false;	
		}
		else if(isDepositDateValid  == true &&  isMaturityDateValid == true )
		{
			var bIsDepositDateGreater = CompareDates();
			if(bIsDepositDateGreater == true)
			{
					bsubmitForm = false;	
			}
			
		}
	}
	
	
	
	if(bsubmitForm == true)
	{
		if(isfdClose)
		{
			bsubmitForm = false;
			var sMsg = "Closing FD Account <br /><br />FD Name: " + document.getElementById('FD_Name').value + "<br /> Maturity Date: " + document.getElementById('Maturity_Date').value + " <br />Maturity Amount: " + document.getElementById('Maturity_Amount').value + "<br /><br />Are you sure you want to continue?<br /><br />Click YES to Close FD<br />Click NO to Cancel"; 
			
			//window.location.href = "FixedDeposit.php#openDialogYesNo";
			window.location.href = currentLocation + "#openDialogYesNo";
			var sText = '<a href="#close" title="Close" class="close" id="close">X</a>';
			sText += '<h2>Confirm Operation</h2><p>' + sMsg + '</p><br/><br/>';
			sText += '<a href="#close" title="Close" class="yes" id="dialogYesNo_yes">YES</a>';
			sText += '<a href="#close" title="Close" class="no" id="dialogYesNo_no">NO</a>';
			
			document.getElementById('message_yesno').innerHTML = sText;
			document.getElementById("dialogYesNo_yes").onclick = function () {submitForm(true);};
			document.getElementById("dialogYesNo_no").onclick = function () {submitForm(false); };
		}
		else if(isfdRenew)
		{
			bsubmitForm = false;
			var sMsg = "Renew FD Account <br /><br />FD Name: " + document.getElementById('FD_Name').value + "<br /> Renewal Date: " + document.getElementById('Deposit_Date').value + " <br />New Principal Amount: " + document.getElementById('Principal_Amount').value + "<br /><br />Are you sure you want to continue?<br /><br />Click YES to Renew FD<br />Click NO to Cancel"; 
			
			//window.location.href = "FixedDeposit.php#openDialogYesNo";
			window.location.href = currentLocation + "#openDialogYesNo";
			var sText = '<a href="#close" title="Close" class="close" id="close">X</a>';
			sText += '<h2>Confirm Operation</h2><p>' + sMsg + '</p><br/><br/>';
			sText += '<a href="#close" title="Close" class="yes" id="dialogYesNo_yes">YES</a>';
			sText += '<a href="#close" title="Close" class="no" id="dialogYesNo_no">NO</a>';
			
			document.getElementById('message_yesno').innerHTML = sText;
			document.getElementById("dialogYesNo_yes").onclick = function () {submitForm(true);};
			document.getElementById("dialogYesNo_no").onclick = function () {submitForm(false); };	
		}
		else
		{
			submitForm(bsubmitForm);				
		}
	}
	
	return false;	
	
}


/*function submitForm(status)
{
	if(status == true)
	{	
		document.getElementById('mode').value =  document.getElementById('insert').value;
		document.getElementById('Category').disabled = false;
		document.getElementById('FD_Bank_Name').disabled = false;	
		document.FixedDeposit.submit();
	}		
	
}*/

function ValidateDate(id,bIsCheck)
{
	var minYear = 1999; //MIN YEAR
	
	var str = document.getElementById(id).value;
	var ret = true;
	var date = str.substring(0, 2);
	var month = str.substring(3, 5);
	var year = str.substring(6, 10);

	var myDate = new Date(year, month - 1, date);
	var today = new Date();
	
	// m[1] is year 'YYYY' * m[2] is month 'MM' * m[3] is day 'DD'					
	var m = str.match(/(\d{2})-(\d{2})-(\d{4})/);
	
	// STR IS NOT FIT m IS NOT OBJECT
	if( m === null || typeof m !== 'object')
	{
		alert("Date Format Not Valid(dd-mm-yyyy)..");
		ret = false;
	}				
	else if (typeof m !== 'object' && m !== null && m.size!==3)
	{
		alert("Date Format Not Valid(dd-mm-yyyy)..");
		ret = false;
	}
	else  if (typeof m == 'object' && m == null && m.size ==3)
	{
			var date = str.substring(0, 2);
			var month = str.substring(3, 5);
			var year = str.substring(6, 10);
			
			if( (m[3].length < 4) || m[3] < minYear)
			{
				ret = false; 
				alert("Please Enter Valid Year..");
			}
			else if( (m[2].length < 2) || m[2] < 1 || m[2] > 12)
			{
				ret = false;
				alert("Please Enter Valid Month..");
			}
			else if( (m[1].length < 2) || m[1] < 1 || m[1] > 31)
			{
				ret = false;
				alert("Please Enter Valid Date..");
			}	
	}
	
	if(myDate > today && bIsCheck == true) {
		ret = false;
		alert("Entered date is greater than today's date ");
	}
	
	if(ret == false)
	{
		document.getElementById(id).focus();
	}
	
	return ret;
}

function fetchFDTable(Type)
{
	showLoader();
	$.ajax({
			url : "ajax/ajaxFixedDeposit.php",
			type : "POST",
			data : {"method" : 'fetchTable',"fetchType" : Type},
			success : function(data)
			{	
				document.getElementById('showTable').innerHTML = data;
				hideLoader();
				
				 $('#example').dataTable(
				 {
					"bDestroy": true
				}).fnDestroy();
				
				var datatable = $('#example').dataTable({
					 "bDestroy": true,
					dom: 'T<"clear">Blfrtip',
					"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
					buttons: 
					[
						{
							extend: 'colvis',
							width:'inherit'/*,
							collectionLayout: 'fixed three-column'*/
						}
					],
					"oTableTools": 
					{
						"aButtons": 
						[
							{ "sExtends": "copy", "mColumns": "visible" },
							{ "sExtends": "csv", "mColumns": "visible" },
							{ "sExtends": "xls", "mColumns": "visible" },
							{ "sExtends": "pdf", "mColumns": "visible" },
							{ "sExtends": "print", "mColumns": "visible" }
						],
					 "sRowSelect": "multi"
				},
				/*"scrollY":   "250px",
		 		"scrollCollapse": true,	*/	
				fnInitComplete: function ( oSettings )
				{
					//var otb = $(".DTTT_container")
					$(".DTTT_container").append($(".dt-button"));
				}
			});
			},
				
			fail: function()
			{
				hideLoader();	
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				hideLoader();
			}
		});		
}


function FetchFDSummary()
{
	document.getElementById('msgDiv').innerHTML = '';
	document.getElementById('msgDiv').style.display = 'none';
	
	showLoader();
	
	var checks = document.getElementsByClassName('checkBox');
	var status = document.getElementById('status').value;
	var ledgerIDArray = [];
    var flag = false;
	
	checks.forEach(function(val, index, ar) 
	{
		if(ar[index].checked && flag == false) 
		{
			
			if(ar[index].id > 0)
			{
				ledgerIDArray.push(ar[index].id);
			}
			else
			{
				flag = true;
				ledgerIDArray = [];
				checks.forEach(function(val, index, ar) 
				{
					if(ar[index].id > 0)
					{
						ledgerIDArray.push(ar[index].id);
					}
					
					
				});
				
				
			}
			
           
        }
		
    });
	
	
	if(ledgerIDArray.length == 0)
	{
		//this array is empty
		 ledgerIDArray = [];
		checks.forEach(function(val, index, ar) 
		{
			if(ar[index].id > 0)
			{
				ledgerIDArray.push(ar[index].id);
			}
			
			
		});
	}
	
	//alert(JSON.stringify(ledgerIDArray));
	
	$.ajax({
			url : "ajax/ajaxFixedDeposit.php",
			type : "POST",
			data : {"method" : 'fetchReport',"ledgerIDArray" : JSON.stringify(ledgerIDArray),"status" : status},
			success : function(data)
			{
				document.getElementById('showTable').innerHTML = data;
				var result = removeEmptySpaces(data);
				if (result == '')
				{
				  	document.getElementById('btnExport').style.display = 'none';
					document.getElementById('Print').style.display = 'none';
				}
				else
				{
					document.getElementById('btnExport').style.display = 'inline-block';
					document.getElementById('Print').style.display = 'inline-block';
				}
				hideLoader();
			},
				
			fail: function()
			{
				hideLoader();
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				hideLoader();
			}
		});
		
	
}


function addNew()
{
	document.getElementById('new_entry').style.display = 'block';
	document.getElementById('btnAdd').style.display = 'none';
	document.getElementById('btnViewReport').style.display = 'none';
	var fdoptions = document.getElementsByClassName('fd_options'), i;

	for (i = 0; i < fdoptions.length; i += 1)
	{
		fdoptions[i].style.display = 'none';
	}
	
}

function validateRenew()
{
	var deposit_date = document.getElementById('Deposit_Date').value; 
	var  maturity_date = document.getElementById('Maturity_Date').value; 
	var int_rate = document.getElementById('Interest_Rate').value; 
	var maturity_amt = document.getElementById('Maturity_Amount').value; 
	var closefd = document.getElementById("FD_Close").checked;
	
	if(renew == true && closefd == false )
	{
		if(deposit_date == '' ||  maturity_date == '' ||   int_rate == '' ||   maturity_amt == '')
		{
			document.getElementById("error").innerHTML = "Enter Values For Deposit Date , Maturity Date , Interest Rate,Maturity Amount";
			go_error();
		}
		else
		{
				var renewfd = document.getElementsByClassName('renewfd'), i;

				for (i = 0; i < renewfd.length; i += 1)
				{
					renewfd[i].style.display = 'table-row';
				} 
				
				var closefd = document.getElementsByClassName('closefd'), m;

				for (m = 0; m < closefd.length; m += 1)
				{
					closefd[m].style.display = 'none';
				} 
		}
	}
	else
	{
		var renewfd = document.getElementsByClassName('renewfd'), i;

		for (i = 0; i < renewfd.length; i += 1)
		{
			renewfd[i].style.display = 'none';
		} 
	}			
}


function formError()
{
	document.getElementById('btnAdd').style.display = 'none';
	document.getElementById('btnViewReport').style.display = 'none';
	
  /*if(document.getElementById("FD_Close").checked == true)
	{
			validateClose();	
	}*/
	
	
	document.getElementById('new_entry').style.display = 'block';
	
	if(document.getElementById('mode').value == "Insert")
	{
		document.getElementById('insert').value = "Insert";	
	}
	else if(document.getElementById('mode').value == "Renew")
	{
		var fdoptions = document.getElementsByClassName('fd_options'), i;
		for (i = 0; i < fdoptions.length; i += 1)
		{
			fdoptions[i].style.display = 'none';
		}
		document.getElementById('insert').value = "Renew";	
		document.getElementById('cancel').style.visibility = "visible";	
		document.getElementById('int_table').style.display = 'none';
	}
	else if(document.getElementById('mode').value == "Update")
	{
		document.getElementById('insert').value = "Update";	
		document.getElementById('cancel').style.visibility = "visible";	
		document.getElementById('FD_Close').disabled = false;
		document.getElementById('int_table').style.display = 'none';
	}	
	document.getElementById('form_error').value = 0;	
}

function showStatus(ledger_id,bank_id)
{
	if(ledger_id != "" && ledger_id != 0  && bank_id != "" && bank_id != 0)
	{
		window.location.href = "chequeleafbook.php?bankid=" + bank_id;
	}
	else
	{
		window.location.href = "BankAccountDetails.php";
	}
}

function Renew(lid)
{
		getDetails('renew-' + lid);
	
}

function get_ledger(category_id,status_id)
{
	document.getElementById('msgDiv').innerHTML = '';
	document.getElementById('msgDiv').style.display = 'none';
	showLoader();
	
	$.ajax({
			url : "ajax/ajaxFixedDeposit.php",
			type : "POST",
			data : {"method" : 'getledger',"category_id" : category_id,"status_id" : status_id},
			success : function(data)
			{	
				document.getElementById('ledgerDiv').innerHTML = data;	
				hideLoader();
				
			},
				
			fail: function()
			{
				hideLoader();	
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				hideLoader();
			}
		});		
	
}


 function CompareDates() 
 {

       //Note: 00 is month i.e. January
		var ret = false;
		var str1 = document.getElementById('Deposit_Date').value; 
	    var d1 = str1.substring(0, 2);
		var m1 = str1.substring(3, 5);
		var y1 = str1.substring(6, 10);
		var dateOne = new Date(y1, m1 - 1, d1);//Year, Month, Date
		
		var str2 = document.getElementById('Maturity_Date').value;
		var d2 = str2.substring(0, 2);
		var m2 = str2.substring(3, 5);
		var y2 = str2.substring(6, 10);
		var dateTwo = new Date(y2, m2 - 1, d2);//Year, Month, Date

        if (dateOne > dateTwo || dateOne == dateTwo) 
		{
			ret = true;
			alert("Deposit Date Must Be Smaller Than Maturity Date.");
		}
		else 
		{
			 ret = false;
		}
	return ret;
    }
	
	

function Export()
{
	document.getElementById('society_details').style.display ='table';
	 window.open('data:application/vnd.ms-excel,' + encodeURIComponent( $("#showTable").html()));
	 document.getElementById('society_details').style.display ='none';
	  
}

function PrintPage() 
	{
		var originalContents = document.body.innerHTML;
		document.getElementById('society_details').style.display ='table';
		var printContents = document.getElementById('showTable').innerHTML;
		
		document.body.innerHTML = printContents;
		window.print();
	
		document.body.innerHTML= originalContents;
		document.getElementById('society_details').style.display ='none';
	}