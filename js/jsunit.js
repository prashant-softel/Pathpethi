var currentLocation = window.location.href;
function getunit(str)
{
	var iden=new Array();
	iden=str.split("-");

	if(iden[0]=="delete")
	{
		var d=confirm("Are you sure , you want to delete it ???");
		if(d==1)
		{
			remoteCall("ajax/ajaxunit.php","form=unit&method="+iden[0]+"&unitId="+iden[1],"loadchanges");
		}
	}
	else
	{
			remoteCall("ajax/ajaxunit.php","form=unit&method="+iden[0]+"&unitId="+iden[1],"loadchanges");
	}
}

function loadchanges()
{
	var a=trim(sResponse);
	var arr1=new Array();
	var arr2=new Array();
	arr1=a.split("@@@");
	arr2=arr1[1].split("#");
	if(arr1[0] == "edit")
	{
		document.getElementById('pageheader').innerHTML = "<font style='font-family: Times New Roman, Georgia, Serif;' > [ " + arr2[3] + " ] - " + arr2[4] + "</font>";
		document.getElementById('unit_presentation').value = arr2[15];
		document.getElementById("id").value = arr2[0];
		document.getElementById("member_id").value = arr2[1];
		document.getElementById('wing_id').value = arr2[2];
		document.getElementById('unit_no').value = arr2[3];
		document.getElementById('owner_name').value = arr2[4];
		document.getElementById('ownership_date').value = arr2[5];
		document.getElementById("floor_no").value=arr2[6];
		document.getElementById("unit_type").value=arr2[7];
		document.getElementById("composition").value=arr2[8];
		document.getElementById("area").value=arr2[9];
		document.getElementById("carpet").value=arr2[10];
		document.getElementById("commercial").value=arr2[11];
		document.getElementById("residential").value=arr2[12];
		document.getElementById("terrace").value=arr2[13];
		document.getElementById("intercom_no").value=arr2[14];
		
		document.getElementById("permanant_add").value=arr2[16];
		document.getElementById("resident_no").value=arr2[17];
		document.getElementById('Year').value = arr2[20];
		jsGetperiods(arr2[20], arr2[21]);
		
		document.getElementById('principle_balance').value = arr2[22];
		document.getElementById('interest_balance').value = arr2[23];
		document.getElementById('bill_subtotal').value = arr2[24];
		document.getElementById('bill_interest').value = arr2[25];
		
		document.getElementById('supp_principle_balance').value = arr2[26];
		document.getElementById('supp_interest_balance').value = arr2[27];
		document.getElementById('supp_bill_subtotal').value = arr2[28];
		document.getElementById('supp_bill_interest').value = arr2[29];
		
		if(arr2[18] == "1")
		{
			document.getElementById('Blocked').checked = true;
			document.getElementById('reason').value = arr2[19];
		}
		else 
		{
			document.getElementById('Blocked').checked = false;
			document.getElementById('reason').value = arr2[19];
		}
		
		document.getElementById('Year').disabled = true;
		document.getElementById('Period').disabled = true;

		document.getElementById('owner_name').readOnly = true;
		
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{
		var arr11 = arr1[1].split("###");
		window.location.href ="../unit.php?mst&"+arr11[0]+'&imp&ssid='+arr11[1]+'&ws&wwid='+arr11[2]+'&idd=1209378';
	}
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


function val()
{
	var unit_no = trim(document.getElementById("unit_no").value);
	var floor_no = trim(document.getElementById("floor_no").value);
	//var unit_type = trim(document.getElementById("unit_type").value);
	var composition= trim(document.getElementById("composition").value);
	var residential= trim(document.getElementById("residential").value);
	var commercial= trim(document.getElementById("commercial").value);
	var terrace= trim(document.getElementById("terrace").value);
	var carpet= trim(document.getElementById("carpet").value);
	var area= trim(document.getElementById("area").value);
	
	if(unit_no==""){
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter unit no.";
		document.getElementById("unit_no").focus();
		
		go_error();
		return false;
	}
	
	
	if(floor_no=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter floor no.";
		document.getElementById("floor_no").focus();
		
		go_error();
		return false;
	}
	
	/*if(unit_type=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter unit type";
		document.getElementById("unit_type").focus();
		
		go_error();
		return false;
	}*/
	
	if(composition=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter composition";
		document.getElementById("composition").focus();
		
		go_error();
		return false;
	}
	
	if(area=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter area";
		document.getElementById("area").focus();
		
		go_error();
		return false;
	}
	
	if(carpet=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter carpet";
		document.getElementById("carpet").focus();
		
		go_error();
		return false;
	}
	
	
	
	if(commercial=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter commercial area";
		document.getElementById("commercial").focus();
		
		go_error();
		return false;
	}
	/*
	if(residential=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter residential area";
		document.getElementById("resdential").focus();
		
		go_error();
		return false;
	}
	
	*/
  /*	
	else if(terrace=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter terrace area";
		document.getElementById("terrace").focus();
		
		go_error();
		return false;
	}
	
	*/
	
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


function enableFields()
{
	if(validateOwnershipTransfer() == true)
	{
		document.getElementById('Year').disabled = false;
		document.getElementById('Period').disabled = false;
		document.getElementById('wing_id').disabled = false;
		document.getElementById('unit_presentation').disabled = false;
		return true;
	}
	else
	{
		return false;	
	}
	
}

function showOrHideOwnershipFields(bIsFlag)
{
	var  iOwnershipFields = document.getElementsByClassName('ownership'), m;

	for (m = 0; m < iOwnershipFields.length; m += 1)
	{
		if(iOwnershipFields[m].style.display == 'none' && bIsFlag != true)
		{
			showHideUnitFields(false);
			iOwnershipFields[m].style.display = 'table-row';
			document.getElementById('new_owner_name').focus();
			document.getElementById('transferOwnership').innerHTML = "Cancel Ownership Transfer";
		}
		else if(iOwnershipFields[m].style.display == 'none' && bIsFlag == true && (document.getElementById('new_owner_name').value != "" ||  document.getElementById('new_ownership_date').value != ""))
		{
			showHideUnitFields(false);
			iOwnershipFields[m].style.display = 'table-row';
			document.getElementById('transferOwnership').innerHTML = "Cancel Ownership Transfer";
		}
		else if(bIsFlag != true && iOwnershipFields[m].style.display == 'table-row')
		{
			showHideUnitFields(true);
			document.getElementById('new_owner_name').value = "";
			document.getElementById('new_ownership_date').value = "";
			document.getElementById('email').value = "";
			document.getElementById('mob').value = "";
			iOwnershipFields[m].style.display = 'none';
			document.getElementById('transferOwnership').innerHTML = "<i class='fa  fa-exchange'>&nbsp;Trasnfer Ownership</i>";
		}
	}	
}


function validateOwnershipTransfer()
{
	
	var new_owner_name = trim(document.getElementById('new_owner_name').value);
	var ownership_date = trim(document.getElementById('new_ownership_date').value);
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	var email = trim(document.getElementById('email').value);
	var mob = trim(document.getElementById('mob').value);
	
	if(new_owner_name != "" && ownership_date != ""  && email != "" && reg.test(email) == false)
	{
		document.getElementById('error').style.display = '';
		document.getElementById('error').innerHTML = 'Invalid email id';	
		document.getElementById('email').focus();
		
		go_error();
		return false;	
	}
	else if(new_owner_name != "" && ownership_date == "")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter New Ownership Date";
		document.getElementById('new_ownership_date').focus();
		
		go_error();
		return false;
	}
	else if(new_owner_name == "" && ownership_date != "")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter New Owner Name";
		document.getElementById('new_owner_name').focus();
		
		go_error();
		return false;
	}
	else if(new_owner_name != "" && ownership_date != "")
	{
			var sMsg = "Transfering Unit Ownership<br /><br />Previous Owner Name: " + document.getElementById('owner_name').value + "<br />Previous Ownership Date: " + document.getElementById('ownership_date').value + "<br /> New Owner Name: " + document.getElementById('new_owner_name').value + " <br />New Ownership Date: " + document.getElementById('new_ownership_date').value+ " <br />" ; 
			if(email != "" && reg.test(email) == true)
			{
				sMsg  +="Email ID: " +  email + "<br />"; 
			}
			if(mob != "")
			{
				sMsg  +="Mobile Number: " + mob + "<br />"; 
			}
			sMsg  += "<br /><br />Are you sure you want to continue?<br /><br />Click YES to Transfer Ownership<br />Click NO to Cancel"; 
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
		submitForm(true);	
	}
	
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

function submitForm(status)
{
	if(status == true)
	{	
		document.getElementById('mode').value =  document.getElementById('insert').value;	
		document.getElementById('Year').disabled = false;
		document.getElementById('Period').disabled = false;
		document.getElementById('wing_id').disabled = false;
		document.getElementById('unit_presentation').disabled = false;
		document.unitForm.submit();
	}		
	
}

function showHideUnitFields(bIsShowFields)
{
		var  iUnitFields = document.getElementsByClassName('UnitFields'), i;
		if(bIsShowFields == true)
		{
			for (i = 0; i < iUnitFields.length; i += 1)
			{
				iUnitFields[i].style.display = 'table-row';
			}
			document.getElementById('wing_id').disabled = false;
			document.getElementById('unit_presentation').disabled = false;
			document.getElementById('unit_no').readOnly = false;
			document.getElementById('owner_name').readOnly = false;	
		}
		else
		{
			for (i = 0; i < iUnitFields.length; i += 1)
			{
				iUnitFields[i].style.display = 'none';
			}
			document.getElementById('wing_id').disabled = true;
			document.getElementById('unit_presentation').disabled = true;
			document.getElementById('unit_no').readOnly = true;
			document.getElementById('owner_name').readOnly = true;	
		}
}


