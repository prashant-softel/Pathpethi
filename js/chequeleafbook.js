function getchequeleafbook(str)
{
	//alert(str);
	var iden	= new Array();
	iden		= str.split("-");

	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{
			remoteCall("ajax/chequeleafbook.ajax.php","form=chequeleafbook&method="+iden[0]+"&chequeleafbookId="+iden[1],"loadchanges");
		}
	}
	else
	{
		remoteCall("ajax/chequeleafbook.ajax.php","form=chequeleafbook&method="+iden[0]+"&chequeleafbookId="+iden[1],"loadchanges");
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
		var lTable = document.getElementById("AddNewSlip");
		lTable.style.display =  "table";
			
		var lTable2 = document.getElementById("SelectLeaf");
		lTable2.style.display =  "none" ;
		
		document.getElementById('LeafName').value=arr2[1];
		if(arr2[2] == "1")
		{		
		document.getElementById('CustomLeaf').checked = true;
		document.getElementById('StartCheque').style.visibility = "hidden";
		document.getElementById('EndCheque').style.visibility = "hidden";
		CustomLeafChanged();
		}
		else
		{
			document.getElementById('StartCheque').value=arr2[3];
			document.getElementById('EndCheque').value=arr2[4];
		}
		document.getElementById('BankID').value=arr2[5];
		document.getElementById('Comment').value=arr2[6];
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{
		window.location.href ="../chequeleafbook.php?mst&"+arr1[1]+"&mm";
	}
}
