function getmember_main(str)
{
	var iden=new Array();
	iden=str.split("-");

	if(iden[0]=="delete")
	{
		var d=confirm("Are you sure , you want to delete it ???");
		if(d==1)
		{
			remoteCall("ajax/ajaxmember_main.php","form=member_main&method="+iden[0]+"&member_mainId="+iden[1],"loadchanges");
		}
	}
	else
	{
			remoteCall("ajax/ajaxmember_main.php","form=member_main&method="+iden[0]+"&member_mainId="+iden[1],"loadchanges");
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
		document.getElementById('unit').value=arr2[1];
		document.getElementById('owner_name').value=arr2[2];
		document.getElementById('resd_no').value=arr2[3];
		document.getElementById('mob').value=arr2[4];
		document.getElementById('alt_mob').value=arr2[5];
		document.getElementById('off_no').value=arr2[6];
		document.getElementById('off_add').value=arr2[7];
		document.getElementById('desg').value=arr2[8];
		document.getElementById('email').value=arr2[9];
		document.getElementById('alt_email').value=arr2[10];
		document.getElementById('dob').value=arr2[11];
		document.getElementById('wed_any').value=arr2[12];
		document.getElementById('bg').value=arr2[13];
		
		document.getElementById('eme_rel_name').value=arr2[14];
		document.getElementById('eme_contact_1').value=arr2[15];
		document.getElementById('eme_contact_2').value=arr2[16];
		
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{
		window.location.href ="../member_main.php?mst&"+arr1[1]+'&mm';
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
	var wing_id = trim(document.getElementById("wing_id").value);
	var unitt = trim(document.getElementById("unit").value);
	
	var owner_name = trim(document.getElementById("owner_name").value);
	var resd_no = trim(document.getElementById("resd_no").value);
	var mob = trim(document.getElementById("mob").value);
	
	var off_no = trim(document.getElementById("off_no").value);
	var off_add = trim(document.getElementById("off_add").value);
	
	var desg = trim(document.getElementById("desg").value);
	var email = trim(document.getElementById("email").value);
	var alt_email = trim(document.getElementById("alt_email").value);
	
	var dob = trim(document.getElementById("dob").value);
	var bg = trim(document.getElementById("bg").value);
	
	var mem_user = trim(document.getElementById("mem_user").value);
	var mem_pass = trim(document.getElementById("mem_pass").value);
	
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	//////////////////////////////////////////////////////////////////////////////////////////
	
	
	//////////////////////////////////////////////////////////////////////////////////////////
	if(wing_id=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please select wing";
		document.getElementById("wing_id").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(unitt=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please select unit";
		document.getElementById("unit").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(owner_name=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter owner name";
		document.getElementById("owner_name").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(resd_no=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter residence phone number";
		document.getElementById("resd_no").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(mob=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter mobile number";
		document.getElementById("mob").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(off_no=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter office number";
		document.getElementById("off_no").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(off_add=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter office address";
		document.getElementById("off_add").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(desg=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter designation";
		document.getElementById("desg").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(email=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter email id";
		document.getElementById("email").focus();
		
		go_error();
		return false;
	}
	else
	{
		if(reg.test(email) == false) 
		{
			document.getElementById('error').style.display = '';
			document.getElementById('error').innerHTML = 'Invalid email id';	
			document.getElementById('email').focus();
			
			go_error();
			return false;	
		}	
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(alt_email!="")
	{
		if(reg.test(alt_email) == false) 
		{
			document.getElementById('error').style.display = '';
			document.getElementById('error').innerHTML = 'Invalid alternate email id';	
			document.getElementById('alt_email').focus();
			
			go_error();
			return false;	
		}	
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(dob=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter date of birth";
		document.getElementById("dob").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(bg=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please select blood group";
		document.getElementById("bg").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(mem_user=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please set username";
		document.getElementById("mem_user").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(mem_pass=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please set password";
		document.getElementById("mem_pass").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	
	
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