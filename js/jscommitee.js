function getCommiteeDetails()
{
	//alert("getdetails");		
	remoteCall("ajax/ajaxcommitee.ajax.php","&method=edit","loadchanges");		
	return false;		
}

function deleteCommiteeMember(str)
{
	var iden=new Array();
	iden=str.split("-");		
	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{			
			remoteCall("ajax/ajaxcommitee.ajax.php","&method="+iden[0]+"&commiteememberId="+iden[1],"loadchanges");
		}
	}	
}

function loadchanges()
{	
	var a		= sResponse.trim();			
	var arr1	= new Array();
	var arr2	= new Array();
	var arr3 	= new Array();
	arr1		= a.split("@@@");	
	arr2		= arr1[1].split("Array");
	
	if(arr1[0] == "edit")
	{	
		if(arr2.length > 1)
		{
			arr3 = arr2[1].split("#");						
			document.getElementById("secretory").value = arr3[3];
			
			arr3 = arr2[2].split("#");
			document.getElementById("join_secretory").value = arr3[3];
			
			arr3 = arr2[3].split("#");
			document.getElementById("treasurer").value = arr3[3];
			
			arr3 = arr2[4].split("#");
			document.getElementById("chairman").value = arr3[3];
			
			var j = 1;
			for(var i = 5; i < arr2.length; i++)
			{	
				arr3 = arr2[i].split("#");
				document.getElementById("commitee_member"+ j).value = arr3[3];																		
				j++;
			}	
			
			document.getElementById("insert").value = "Update";
		}
	}
	else if(arr1[0] == "delete")
	{					
		window.location.href ="commitee.php";
	}
}

