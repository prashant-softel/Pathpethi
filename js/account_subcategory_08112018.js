//var category = "";
var minStartDate;

function getaccount_subcategory(str)
{
	var iden	= new Array();
	iden		= str.split("-");
	var Groupid;
	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{			
			remoteCall("ajax/account_subcategory.ajax.php","form=account_subcategory&method="+iden[0]+"&account_subcategoryId="+iden[1],"loadchanges");
		}
	}
	else if(iden[0]=="edit")
	{		
		remoteCall("ajax/account_subcategory.ajax.php","form=account_subcategory&method="+iden[0]+"&account_subcategoryId="+iden[1],"loadchanges");
	}
	else if(iden[0]=="view")
	{
		var LedgerID = iden[1];
		//alert("view" + LedgerID);
		$.ajax({
        url: 'ajax/account_subcategory.ajax.php',
        type: 'POST',
        data: {"ledgerid": LedgerID, "method":"FetchGroup"},
        success: function(data){
            fetchdata = data.split("@@@");
			Groupid = fetchdata[1];
			//window.open('<?php echo $paymentDtlUrl; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes');
			popupLedgerWindow = window.open('view_ledger_details.php?lid=' + iden[1] + '&gid=' + Groupid ,'popupLedgerWindow','type=fullWindow,fullscreen,scrollbars=yes');
			//window.open('view_ledger_details.php?lid=' + iden[1] + '&gid=' + Groupid);
		 }
    	});
		
	}
	else if(iden[0]=="print")
	{
		var LedgerID = iden[1];
		//alert("view" + LedgerID);
		$.ajax({
        url: 'ajax/account_subcategory.ajax.php',
        type: 'POST',
        data: {"ledgerid": LedgerID, "method":"FetchGroup"},
        success: function(data){
            fetchdata = data.split("@@@");
			Groupid = fetchdata[1];
			//alert(data);
			window.open('ledger_print.php?lid=' + iden[1] + '&gid=' + Groupid);
			//alert("Groupid: " + Groupid);
        }
    	});
		
	}
}

function get_category(group_id, selectIndex,flag)
{
	console.log("Group ID "+group_id);
	if(selectIndex == null)
	{
		selectIndex = 0;
	}
	
	if(group_id == 0)
	{
		group_id = document.getElementById('groupid').value;
	}
	//alert(group_id);
	if(group_id == 0)
	{
		console.log("IF G ==0 Condition");
		$('select#categoryid').empty();
		$('select#categoryid').append(
			$('<option></option>')
			.val('0')
			.html('None'));
	}
	else
	{
		console.log("Else Condition");
		document.getElementById('error').style.display = '';	
		document.getElementById('error').innerHTML = 'Fetching Category. Please Wait...';	
		populateDDListAndTrigger('select#categoryid', 'ajax/account_category.ajax.php?getcategory&groupid=' + group_id + '&primary=0', 'category', 'hide_error', false, selectIndex);
		if(flag == null)
		{
			setunset_checkbox(group_id);
		}
	}
	if(group_id==1 || group_id==2)
		 {
			// alert(group_id);
			 document.getElementById('opening').style.visibility = "visible";
			 document.getElementById('opening_Balance').style.visibility = "visible";
			//document.getElementById('opening').style.display='table-row'; 
			//document.getElementById('opening_Balance').style.display='table-row'; 
			document.getElementById('GSTIN_Details').style.display='table-row'; 
			document.getElementById('pan_Details').style.display='table-row'; 
			document.getElementById('nature_Details').style.display='table-row';
			document.getElementById('nature_detail_rate').style.display='table-row';
		 }
		 else
		 {
			 document.getElementById('opening').style.visibility = "hidden";
			 document.getElementById('opening_Balance').style.visibility = "hidden";
			 //document.getElementById('opening').style.display='none';
			 //document.getElementById('opening_Balance').style.display='none';
			 document.getElementById('GSTIN_Details').style.display='none';
			 document.getElementById('pan_Details').style.display='none';
			 document.getElementById('nature_Details').style.display='none';
			 document.getElementById('nature_detail_rate').style.display='none';
			
			
		 }
		 
		 if(group_id==3)
		{
		 document.getElementById('opening_type').value='2';
		 document.getElementById('opening_balance').value='0';
		}
		else if(group_id==4)
		{
		 document.getElementById('opening_type').value='1';
		 document.getElementById('opening_balance').value='0';
		}
		else if(group_id==2)
		{
			document.getElementById('nature_Details').style.display='none';
			 document.getElementById('nature_detail_rate').style.display='none';
			
		}
		
}

function loadchanges()
{
	var a		= sResponse.trim();		
	var arr1	= new Array();
	var arr2	= new Array();
	arr1		= a.split("@@@");
	
	arr2		= JSON.parse("["+arr1[1]+"]");		
	console.log(arr2);
	//alert(arr2);
	if(arr1[0] == "edit")
	{		
		document.getElementById('new_entry').style.display = 'block';
		document.getElementById('btnAdd').style.display = 'none';
		document.getElementById("society_id").value=arr2[0].society_id;
		document.getElementById('categoryid').value=arr2[0].categoryid;	
		console.log("Categoty ID "+arr2[0].categoryid);
		//category = arr2[2];		
		document.getElementById('ledger_name').value=arr2[0].ledger_name;		
		document.getElementById("show_in_bill").checked = (arr2[0].show_in_bill == 1) ? true : false;
		document.getElementById("taxable").checked = (arr2[0].taxable == 1)? true : false;
		document.getElementById("sale").checked = (arr2[0].sale == 1)? true : false;
		document.getElementById("purchase").checked = (arr2[0].purchase == 1)? true : false;
		document.getElementById("income").checked = (arr2[0].income == 1)? true : false;
		document.getElementById("expense").checked = (arr2[0].expense == 1)? true : false;
		document.getElementById("payment").checked = (arr2[0].payment == 1)? true : false;
		document.getElementById("receipt").checked = (arr2[0].receipt == 1)? true : false;
		document.getElementById('opening_balance').value=arr2[0].opening_balance;
		document.getElementById('balance_date').value=arr2[0].opening_date;	
		document.getElementById('note').value=arr2[0].note;	
		document.getElementById('opening_type').value=arr2[0].opening_type;	
		document.getElementById('nothreshold').checked = (arr2[0].taxable_no_threshold == 1)? true : false;
		document.getElementById('groupid').value=arr2[0].Group;		
		console.log("Group ID Above "+arr2[0].Group);
		get_category(arr2[0].Group, arr2[0].categoryid,true);
		
		document.getElementById("id").value=arr2[0].id;
		document.getElementById('GSTIN_No').value=arr2[0].GSTIN_No;
		document.getElementById('Pan_no').value=arr2[0].PAN_No;
		document.getElementById('natureOfPayment').value=arr2[0].nature_of_payId;
		document.getElementById('nature_rate').value=arr2[0].nature_deduction_rate;
		//alert(arr2[18]);
		if(isSadmin == false)
		{
			document.getElementById('groupid').style.backgroundColor = 'lightgray';
			document.getElementById('groupid').disabled = true;
				
			document.getElementById('categoryid').style.backgroundColor = 'lightgray';
			document.getElementById('categoryid').disabled = true;
		}
		var date_diff = getDateDiff(parseDate(document.getElementById('balance_date').value), parseDate(minStartDate));
		if(date_diff > 1)
		{
			document.getElementById('opening_balance').readOnly = true;
			document.getElementById('opening_balance').style.backgroundColor = 'lightgray';	
		}
		if(document.getElementById('groupid').value == 1 || document.getElementById('groupid').value == 3 || document.getElementById("show_in_bill").checked == true)
		{
			document.getElementById("show_in_bill").disabled = false;	
		}
		else
		{
			document.getElementById("show_in_bill").disabled = true;	
		}
				
		document.getElementById("insert").value="Update";
		document.getElementById("supplementary_bill").checked = (arr2[0].supplementary_bill == 1) ? true : false;
		
		if(document.getElementById('groupid').value == 1 || document.getElementById('groupid').value == 3 || document.getElementById("supplementary_bill").checked == true)
		{
			document.getElementById("supplementary_bill").disabled = false;	
		}
		else
		{
			document.getElementById("supplementary_bill").disabled = true;	
		}
		window.scroll(0, 0);
		//populateDDListAndTrigger('select#categoryid', 'ajax/account_category.ajax.php?getcategory&groupid=' + arr2[1], 'category', 'set_categoryid', false);			
	}
	else if(arr1[0] == "delete")
	{		
		window.location.href ="../ledger.php?mst&"+arr1[1]+"&mm";
	}
}

/*function set_categoryid()
{	
	document.getElementById('categoryid').value = category;
}*/

function setunset_checkbox(group_id)
{
	//alert('2');
	document.getElementById('sale').checked=false;
	document.getElementById('expense').checked=false;
	document.getElementById('receipt').checked=false;
	document.getElementById('payment').checked=false;
	document.getElementById('purchase').checked=false;
	document.getElementById('income').checked=false;
	document.getElementById('taxable').checked=false;
	//alert(group_id);
	var bUnsetShowInBill = false;
if(group_id==1)
{
	document.getElementById('expense').checked=true;	
	document.getElementById('payment').checked=true;
	document.getElementById('receipt').checked=true;
	document.getElementById('show_in_bill').disabled = false;
}	

if(group_id==2)
{
	document.getElementById('sale').checked=true;	
	document.getElementById('purchase').checked=true;
	document.getElementById('income').checked=true;
	document.getElementById('receipt').checked=true;
	bUnsetShowInBill = true;
}	

if(group_id==3)
{

	document.getElementById('sale').checked=true;	
	document.getElementById('receipt').checked=true;
	document.getElementById('income').checked=true;	
	document.getElementById('show_in_bill').disabled = false;
	document.getElementById('supplementary_bill').disabled=false;
}	


if(group_id==4)
{
	document.getElementById('expense').checked=true;	
	document.getElementById('payment').checked=true;
	bUnsetShowInBill = true;
}	
if(bUnsetShowInBill == true)
{
	document.getElementById('show_in_bill').checked=false;
	document.getElementById('show_in_bill').disabled = true;
	document.getElementById('supplementary_bill').checked=false;
	document.getElementById('supplementary_bill').disabled = true;	
}
	
	
}





function val()
{ 
	
	var categoryid = trim(document.getElementById("categoryid").value);
	var ledger = trim(document.getElementById("ledger_name").value);
	var balance_date = trim(document.getElementById("balance_date").value);
	var opening_type = trim(document.getElementById("opening_type").value);
	
	if(categoryid == "0" || categoryid == "")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Select Category ID";
		go_error();
		return false;
	}
	
	if(ledger == "")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Enter Ledger Name";
		document.getElementById("ledger_name").focus();
		go_error();
		return false;
	}
	
	if(opening_type == "0")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Select Opening Type";
		go_error();
		return false;
	}
	
	/*if(balance_date == "00-00-0000")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Enter Date";
		document.getElementById("balance_date").focus();
		go_error();
		
		return false;
	}*/
	
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
	document.getElementById('groupid').disabled = false;
	document.getElementById('categoryid').disabled = false;
	document.getElementById('opening').style.display = 'table-row';
	document.getElementById('opening_Balance').style.display = 'table-row';
	
}
function getDateDiff(first, second)
{
	return Math.round((second-first)/(1000*60*60*24));
}

function parseDate(str) 
{
	var parts = str.split("-");
   return new Date(parts[2], parts[1] - 1, parts[0]);
}
