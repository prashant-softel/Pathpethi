function getmem_spouse_details(str)
{
	var iden=new Array();
	iden=str.split("-");

	if(iden[0]=="delete")
	{
		var d=confirm("Are you sure , you want to delete it ???");
		if(d==1)
		{
			remoteCall("ajax/ajaxmem_spouse_details.php","form=mem_spouse_details&method="+iden[0]+"&mem_spouse_detailsId="+iden[1],"loadchanges");
		}
	}
	else
	{
			remoteCall("ajax/ajaxmem_spouse_details.php","form=mem_spouse_details&method="+iden[0]+"&mem_spouse_detailsId="+iden[1],"loadchanges");
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
		document.getElementById('spouse_name').value=arr2[2];
		document.getElementById('spouse_desg').value=arr2[3];
		document.getElementById('spouse_dob').value=arr2[4];
		document.getElementById('spouse_off_add').value=arr2[5];
		document.getElementById('spouse_off_no').value=arr2[6];
		document.getElementById('spouse_bg').value=arr2[7];
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{
		window.location.href ="../mem_spouse_details.php";
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
	var spouse_name = trim(document.getElementById("spouse_name").value);
	var spouse_desg = trim(document.getElementById("spouse_desg").value);
	var spouse_bg = trim(document.getElementById("spouse_bg").value);
	//////////////////////////////////////////////////////////////////////////////////////////
	
	
	//////////////////////////////////////////////////////////////////////////////////////////
	if(spouse_name=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter spouse name";
		document.getElementById("spouse_name").focus();
		
		go_error();
		return false;
	}
	if(spouse_desg=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please select occupation";
		document.getElementById("spouse_desg").focus();
		
		go_error();
		return false;
	}
	if(spouse_bg=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please select blood group";
		document.getElementById("spouse_bg").focus();
		
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