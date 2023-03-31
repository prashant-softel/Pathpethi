function getmem_other_family(str)
{
	var iden=new Array();
	iden=str.split("-");

	if(iden[0]=="delete")
	{
		var d=confirm("Are you sure , you want to delete it ???");
		if(d==1)
		{
			remoteCall("ajax/ajaxmem_other_family.php","form=mem_other_family&method="+iden[0]+"&mem_other_familyId="+iden[1],"loadchanges");
		}
	}
	else
	{
			remoteCall("ajax/ajaxmem_other_family.php","form=mem_other_family&method="+iden[0]+"&mem_other_familyId="+iden[1],"loadchanges");
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
		document.getElementById('other_name').value=arr2[2];
		document.getElementById('relation').value=arr2[3];
		document.getElementById('other_dob').value=arr2[4];
		document.getElementById('other_desg').value=arr2[5];
		document.getElementById('ssc').value=arr2[6];
		document.getElementById('child_bg').value=arr2[7];
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{
		window.location.href ="../mem_other_family.php";
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
	//return false;
	var other_name = trim(document.getElementById("other_name").value);
	var relation = trim(document.getElementById("relation").value);
	var other_desg = trim(document.getElementById("other_desg").value);
	var child_bg = trim(document.getElementById("child_bg").value);
	var CreateLogin = document.getElementById("chkCreateLogin").checked;
	var send_commu_emails = document.getElementById("other_send_commu_emails").checked;
	var BloodGroup = document.getElementById("child_bg").value;
	//////////////////////////////////////////////////////////////////////////////////////////
	
	
	//////////////////////////////////////////////////////////////////////////////////////////
	if(other_name=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter name";
		document.getElementById("other_name").focus();
		
		go_error();
		return false;
	}
	//alert(CreateLogin);
	if(CreateLogin  == true || send_commu_emails  == true)
	{
		if(document.getElementById('other_email').value == "")
		{
			alert("Please provide Email id");
			return false; 
		}
		else
		{
			var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;  
			if(document.getElementById("other_email").value.match(mailformat))  
			{  
				//alert("Email address valid!");  
				return true;  
			}  
			else  
			{  
				alert("Entered email address is not in valid email format !");  
				document.getElementById("other_email").focus();  
				return false;  
			}
		}
	}
	/*if(BloodGroup == "9")
	{
		alert("Please select valid Blood Group");
		return false;
	}*/
	/*if(relation=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter relation with flat owner";
		document.getElementById("relation").focus();
		
		go_error();
		return false;
	}
	if(other_desg=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please select occupation";
		document.getElementById("other_desg").focus();
		
		go_error();
		return false;
	}
	if(child_bg=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please select blood group";
		document.getElementById("child_bg").focus();
		
		go_error();
		return false;
	}*/
	
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