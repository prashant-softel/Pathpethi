function getbill_period(str)
{
	var iden	= new Array();
	iden		= str.split("-");

	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{
			remoteCall("ajax/ajaxbill_period.php","form=bill_period&method="+iden[0]+"&bill_periodId="+iden[1],"loadchanges");
		}
	}
	else
	{
		remoteCall("ajax/ajaxbill_period.php","form=bill_period&method="+iden[0]+"&bill_periodId="+iden[1],"loadchanges");
	}
}

function loadchanges()
{
	var a		= sResponse.trim();
	var arr1	= new Array();
	var arr2	= new Array();
	arr1		= a.split("@@@");
	arr2		= arr1[1].split("#");
	//alert(arr2);

	if(arr1[0] == "edit")
	{
		//document.getElementById('Type').value=arr2[1];
		document.getElementById('Cycle').value=arr2[1];
		document.getElementById('YearID').value=arr2[3];
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{
		window.location.href ="bill_period.php?mst&"+arr1[1]+"&mm";
	}
}


