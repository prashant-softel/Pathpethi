function validate()
{	
	var category = trim(document.getElementById('category').value);
	var email = trim(document.getElementById('email').value);	
	var member = trim(document.getElementById('member').value);	
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;					
	
	if(category == "")
	{
		document.getElementById('error').style.display = "";
		document.getElementById('error').innerHTML = "Please Enter Category";
		document.getElementById('category').focus();
		go_error();
		return false;
	}
	
	if(member == "")
	{
		document.getElementById('error').style.display = "";
		document.getElementById('error').innerHTML = "Please Select Member";	
		document.getElementById('member').focus();
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
	else if(email != "" && reg.test(email) == false)
	{
		document.getElementById('error').style.display = '';
		document.getElementById('error').innerHTML = 'Invalid email id';	
		document.getElementById('email').focus();
			
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


function getcategory(str)
{
	var iden	= new Array();
	iden		= str.split("-");	
	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure, you want to delete it ?");
		if(conf==1)
		{			
			remoteCall("ajax/servicerequest_cat.ajax.php","form=service_category&method="+iden[0]+"&categoryId="+iden[1],"loadchanges");
		}
	}
	else
	{		
		remoteCall("ajax/servicerequest_cat.ajax.php","form=service_category&method="+iden[0]+"&categoryId="+iden[1],"loadchanges");
	}
}

function loadchanges()
{
	var a		= sResponse.trim();		
	var arr1	= new Array();
	var arr2	= new Array();	
	arr1		= a.split("@@@");
	arr2		= arr1[1].split("#");	
	if(arr1[0] == "edit")
	{		
		document.getElementById('category').value=arr2[1];
		document.getElementById('member').value=arr2[5];
		document.getElementById('co-member').value=arr2[6];
		document.getElementById('email').value = arr2[3];
		document.getElementById('email_cc').value = arr2[4];
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
		if(arr2[7] == 1)
		{
			document.getElementById('check').checked = true;
		}
		else
		{
			document.getElementById('check').checked = false;
		}
	}
	else if(arr1[0] == "delete")
	{		
		window.location.href ="servicerequest_master.php?mst&"+arr1[1]+"&mm";
	}
}

function getEmailID(member_id)
{
	remoteCall("ajax/servicerequest_cat.ajax.php","form=service_category&method=getEmail&member_id="+member_id,"setEmail");
}

function setEmail()
{
	var a = sResponse.trim();
	var arr1	= new Array();	
	arr1		= a.split("@@@");
	if(arr1[1]!='')
	{	
	document.getElementById('email').value = arr1[1];
	document.getElementById('email').readOnly = true;
	document.getElementById('email').style.backgroundColor = 'lightgray';
	}
	else
	{
		document.getElementById('email').value =''
		document.getElementById('email').readOnly = false;
		document.getElementById('email').style.backgroundColor = 'white';
	}
}