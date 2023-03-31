function getgroup(str)
{
	var iden	= new Array();
	iden		= str.split("-");

	<!--$('html, body').animate({ scrollTop: $('#top').offset().top }, 300);-->

	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{			
			remoteCall("ajax/group.ajax.php","form=group&method="+iden[0]+"&groupId="+iden[1],"loadchanges");
		}
	}
	else
	{		
		remoteCall("ajax/group.ajax.php","form=group&method="+iden[0]+"&groupId="+iden[1],"loadchanges");
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
		document.getElementById('srno').value=arr2[1];
		document.getElementById('groupname').value=arr2[2];
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{		
		window.location.href ="../group.php?mst&"+arr1[1]+"&mm";
	}
}
