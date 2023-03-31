function validate()
{	
	var raisedDate = trim(document.getElementById('complaint_date').value);
	var email = trim(document.getElementById('email').value);
	var priority = trim(document.getElementById('priority').value);
	var category = trim(document.getElementById('category').value);
	var summery = trim(document.getElementById('summery').value);
	//var details = trim(document.getElementById('details').value);
	var details = CKEDITOR.instances['details'].getData();
	
	if(raisedDate == "")
	{
		document.getElementById('error').style.display = "";
		document.getElementById('error').innerHTML = "Please Enter Date";
		document.getElementById('complaint_date').focus();
		go_error();
		return false;	
	}
	
	if(email == "")
	{
		document.getElementById('error').style.display = "";
		document.getElementById('error').innerHTML = "Please Enter Valid Email ID";	
		document.getElementById('email').focus();
		go_error();
		return false;
	}
	
	if(priority == 0)
	{
		document.getElementById('error').style.display = "";
		document.getElementById('error').innerHTML = "Please Select Priority";
		document.getElementById('priority').focus();
		go_error();
		return false;
	}
	
	if(category == 0)
	{
		document.getElementById('error').style.display = "";
		document.getElementById('error').innerHTML = "Please Select Category";
		document.getElementById('category').focus();
		go_error();
		return false;
	}
	
	if(summery == "")
	{
		document.getElementById('error').style.display = "";
		document.getElementById('error').innerHTML = "Please Enter Summery";	
		document.getElementById('summery').focus();
		go_error();
		return false;
	}
	
	alert(details);
	if(details == "")
	{		
		document.getElementById('error').style.display = "";
		document.getElementById('error').innerHTML = "Please Enter Request Details";
		document.getElementById('details').focus();
		go_error();
		return false;	
	}
	
	///////////////////////////////////////////////////////////////////////////	
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
	///////////////////////////////////////////////////////////////////////////	
}

function val()
{
	var status = document.getElementById('status').value;
	var comments = document.getElementById('comments').value;
	
	if(status == 0)
	{
		document.getElementById('error').style.display = "";
		document.getElementById('error').innerHTML = "Please Select Status";
		document.getElementById('status').focus();
		go_error();
		return false;	
	}
	
	if(comments == "")
	{
		document.getElementById('error').style.display = "";
		document.getElementById('error').innerHTML = "Please Enter Comments";
		document.getElementById('comments').focus();
		go_error();
		return false;	
	}
}

function blockNonNumbers(obj, e, allowDecimal, allowNegative)
{
	var key;
	var isCtrl = false;
	var keychar;
	var reg;
		
	if(window.event) {
		key = e.keyCode;
		isCtrl = window.event.ctrlKey
	}
	else if(e.which) {
		key = e.which;
		isCtrl = e.ctrlKey;
	}
	
	if (isNaN(key)) return true;
	
	keychar = String.fromCharCode(key);
	
	// check for backspace or delete, or if Ctrl was pressed
	if (key == 8 || isCtrl)
	{
		return true;
	}

	reg = /\d/;
	var isFirstN = allowNegative ? keychar == '-' && obj.value.indexOf('-') == -1 : false;
	var isFirstD = allowDecimal ? keychar == '.' && obj.value.indexOf('.') == -1 : false;
	
	return isFirstN || isFirstD || reg.test(keychar);
}
