function getwing(str)
{
	var iden=new Array();
	iden=str.split("-");

	if(iden[0]=="delete")
	{
		var d=confirm("Are you sure , you want to delete it ???");
		if(d==1)
		{
			remoteCall("ajax/ajaxwing.php","form=wing&method="+iden[0]+"&wingId="+iden[1],"loadchanges");
		}
	}
	else
	{
			remoteCall("ajax/ajaxwing.php","form=wing&method="+iden[0]+"&wingId="+iden[1],"loadchanges");
	}
}


function loadchanges()
{
	var a=trim(sResponse);//alert(a);
	var arr1=new Array();
	var arr2=new Array();
	arr1=a.split("@@@");
	arr2=arr1[1].split("#");
	
	if(arr1[0] == "edit")
	{
		document.getElementById('wing1').value=arr2[1];
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{
		var arr11 = arr1[1].split("###");
		window.location.href = "../wing.php?mst&"+arr11[0]+'&imp&ssid='+arr11[1]+'&s&idd=1209378';
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
	var wing = trim(document.getElementById("wing1").value);
	
	if(wing=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter wing";
		document.getElementById("wing1").focus();
		
		go_error();
		return false;
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
