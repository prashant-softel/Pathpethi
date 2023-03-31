function getbill_year(str)
{
	var iden	= new Array();
	iden		= str.split("-");

	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{
			$(document).ready(function()
			{
				$("#error").fadeIn("slow");
				document.getElementById("error").innerHTML = "Please Wait...";
			});

			remoteCall("ajax/ajaxbill_year.php","form=bill_year&method="+iden[0]+"&bill_yearId="+iden[1],"loadchanges");
		}
	}
	else
	{
		remoteCall("ajax/ajaxbill_year.php","form=bill_year&method="+iden[0]+"&bill_yearId="+iden[1],"loadchanges");
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
		document.getElementById('freeze_year').checked = (arr2[3] == 1) ? true : false;
		document.getElementById('prev_year_status').value = arr2[3];
		document.getElementById('PrevYearID').value=arr2[2];
		document.getElementById('YearDescription').value=arr2[1];
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
		window.scroll(0, 0);
	}
	else if(arr1[0] == "delete")
	{
		window.location.href ="../bill_year.php?mst&"+arr1[1]+"&mm";
	}
}

function enable()
{
	//alert("enable");
	document.getElementById('freeze_year').disabled = false;	
}