function getsociety_list(group_id)
{ 
	//alert('test');
//	window.location.href="gallery_group.php?group_id=" + group_id;
	remoteCall("ajax/gallery_group.ajax.php","form=group_form&method=fetchsocietylist&group_id="+group_id, "fetchsocietylist");
	//remoteCall("ajax/account_subcategory.ajax.php","form=account_subcategory&method="+iden[0]+"&account_subcategoryId="+iden[1],"loadchanges");
	//alert
	 
}
	
	function fetchsocietylist()
	{
		
		var a = sResponse.trim();
		//alert(a);
		document.getElementById('society_id').innerHTML = a;
		//alert(a);
	}
	
function getgallery_group(str)
{
	var iden	= new Array();
	iden		= str.split("-");

	<!--$('html, body').animate({ scrollTop: $('#top').offset().top }, 300);-->

	if(iden[0]=="delete")
	{alert(iden[0]);
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{			
			remoteCall("ajax/gallery_group.ajax.php","form=group&method="+iden[0]+"&groupId="+iden[1],"loadchanges");
		}
	}
	else
	{		
			//alert(iden[0]);
		remoteCall("ajax/gallery_group.ajax.php","form=group&method="+iden[0]+"&groupId="+iden[1],"loadchanges");
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
		//alert(arr2[1]);
		document.getElementById("create").style.visibility = 'visible';
		document.getElementById("id").value=arr2[0];
		document.getElementById('group').value=arr2[1];
		//alert(arr2[0]);
		getsociety_list(arr2[0]);
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{		
		window.location.href ="gallery_group.php";
	}
}
function getgalleryview(str)
	{
	
	var iden	= new Array();
	iden		= str.split("-");
	//alert(str);
	         window.location.href="group_create.php?group_id=" + iden[1];
   
	
	}