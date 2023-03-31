function getsociety_group(str)
{
	var iden=new Array();
	iden=str.split("-");

	if(iden[0]=="delete")
	{
		var d=confirm("Are you sure , you want to delete it ???");
		if(d==1)
		{
			remoteCall("ajax/ajaxsociety_group.php","form=society_group&method="+iden[0]+"&society_groupId="+iden[1],"loadchanges");
		}
	}
	else
	{
			remoteCall("ajax/ajaxsociety_group.php","form=society_group&method="+iden[0]+"&society_groupId="+iden[1],"loadchanges");
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
		document.getElementById('grp_name').value=arr2[1];
		document.getElementById('society_id').value=arr2[2];
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{
		window.location.href ="../society_group.php";
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
	var grp_name = trim(document.getElementById("grp_name").value);
	var grp_name_old = document.getElementById("grp_name_old").value;
	var cnt1 = document.getElementById("count_society_id").value;
	//////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	//////////////////////////////////////////////////////////////////////////////////////////
	if(grp_name=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Group name should no be blank";
		
		document.getElementById("grp_name").value = grp_name_old;
		
		go_error();
		return false;
	}
	for(var i1=0;i1<=cnt1-1;i1++)
	{
		if(document.getElementById('society_id'+i1).checked==false)
		{
			var ttt1 = "ppp";
		}
		else
		{
			var kkk1 = 1;
		}
	}
	
	if(kkk1!=1)
	{
		document.getElementById('error').style.display = '';
		document.getElementById('error').innerHTML = "Please click atleast one society";
		
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