function getmem_bike_parking(str)
{
	var iden=new Array();
	iden=str.split("-");

	if(iden[0]=="delete")
	{
		var d=confirm("Are you sure , you want to delete it ???");
		if(d==1)
		{
			remoteCall("ajax/ajaxmem_bike_parking.php","form=mem_bike_parking&method="+iden[0]+"&mem_bike_parkingId="+iden[1],"loadchanges");
		}
	}
	else
	{
			remoteCall("ajax/ajaxmem_bike_parking.php","form=mem_bike_parking&method="+iden[0]+"&mem_bike_parkingId="+iden[1],"loadchanges");
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
		document.getElementById('member_id').value=arr2[1];
		document.getElementById('parking_slot').value=arr2[2];
		document.getElementById('bike_reg_no').value=arr2[3];
		document.getElementById('bike_owner').value=arr2[4];
		document.getElementById('bike_model').value=arr2[5];
		document.getElementById('bike_make').value=arr2[6];
		document.getElementById('bike_color').value=arr2[7];
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{
		window.location.href ="../mem_bike_parking.php";
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
	//////////////////////////////////////////////////////////////////////////////////////////
	var parking_slot = trim(document.getElementById("parking_slot").value);
	var bike_reg_no = trim(document.getElementById("bike_reg_no").value);
	var bike_owner = trim(document.getElementById("bike_owner").value);
	
	var bike_make = trim(document.getElementById("bike_make").value);
	var bike_color = trim(document.getElementById("bike_color").value);
	//////////////////////////////////////////////////////////////////////////////////////////
	
	
	//////////////////////////////////////////////////////////////////////////////////////////
	if(parking_slot=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter parking slot";
		document.getElementById("parking_slot").focus();
		
		go_error();
		return false;
	}
	if(bike_reg_no=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter bike registration No";
		document.getElementById("bike_reg_no").focus();
		
		go_error();
		return false;
	}
	if(bike_owner=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter bike owner name";
		document.getElementById("bike_owner").focus();
		
		go_error();
		return false;
	}
	if(bike_make=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter bike maker";
		document.getElementById("bike_make").focus();
		
		go_error();
		return false;
	}
	if(bike_color=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter bike color";
		document.getElementById("bike_color").focus();
		
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
}