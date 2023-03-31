function getdepositgroup(str)
{
	//alert('test');
	var iden	= new Array();
	iden		= str.split("-");

	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{
			remoteCall("ajax/depositgroup.ajax.php","form=depositgroup&method="+iden[0]+"&depositgroupId="+iden[1],"loadchanges");
		}
	}
	else
	{
		remoteCall("ajax/depositgroup.ajax.php","form=depositgroup&method="+iden[0]+"&depositgroupId="+iden[1],"loadchanges");
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
		document.getElementById('bankid').value=arr2[1];
		document.getElementById('createdby').value=arr2[2];
		document.getElementById('depositedby').value=arr2[3];
		document.getElementById('status').value=arr2[4];
		document.getElementById('desc').value=arr2[5];
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{
		window.location.href ="../depositgroup.php?mst&"+arr1[1]+"&mm";
	}
}
