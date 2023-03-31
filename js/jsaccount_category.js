function getaccount_category(str)
{
	var iden	= new Array();
	iden		= str.split("-");

	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{
			remoteCall("ajax/ajaxaccount_category.php","form=account_category&method="+iden[0]+"&account_categoryId="+iden[1],"loadchanges");
		}
	}
	else
	{
		remoteCall("ajax/ajaxaccount_category.php","form=account_category&method="+iden[0]+"&account_categoryId="+iden[1],"loadchanges");
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
		document.getElementById('AccountCategory').value=arr2[1];
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{
		window.location.href ="../account_category.php?mst&"+arr1[1]+"&mm";
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
