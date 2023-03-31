function getsociety_list(group_id)
{ 
	//alert('test');
	//window.location.href="group_create.php?group_id=" + group_id;
	remoteCall("ajax/group_create.ajax.php","form=group_create&method=fetchsocietylist&group_id="+group_id, "fetchsocietylist");
	//remoteCall("ajax/account_subcategory.ajax.php","form=account_subcategory&method="+iden[0]+"&account_subcategoryId="+iden[1],"loadchanges");
	//alert
	 
}
	
	function fetchsocietylist()
	{
		var a = sResponse.trim();
		document.getElementById('society_id').innerHTML = a;
		//alert(a);
	}
	
	
	function getsociety_create(str,society_id, selectIndex)
{ 
	if(selectIndex == null)
	{
		selectIndex = 1;
	}
	
	if(society_id == 0)
	{
		$('select#society_id').empty();
		$('select#society_id').append(
			$('<option></option>')
			.val('0')
			.html('All'));
	}
	
	else
	{
		//document.getElementById('error').style.display = '';	
		//document.getElementById('error').innerHTML = 'Fetching Categories. Please Wait...';	
		populateDDListAndTrigger('select#society_id', 'ajax/../ajax/group_create.ajax.php&society_id=' + society_id, 'society', 'hide_error', false, selectIndex);
	}
	var iden	= new Array();
	iden		= str.split("-");

	$('html, body').animate({ scrollTop: $('#top').offset().top }, 300);

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

			remoteCall("../ajax/group_create.ajax.php","form=group_create&method="+iden[0]+"&group_createId="+iden[1],"loadchanges");
		}
	}
	else
	{
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
			document.getElementById("error").innerHTML = "Please Wait...";
		});

		remoteCall("../ajax/group_create.ajax.php","form=group_create&method="+iden[0]+"&group_createId="+iden[1],"loadchanges");
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
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
			document.getElementById("error").innerHTML = "Please Wait...";
		});

		document.getElementById('group_name').value=arr2[1];
		document.getElementById('society_name').value=arr2[2];
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
			document.getElementById("error").innerHTML = "Please Wait...";
		});

		window.location.href ="../group_create.php?mst&"+arr1[1]+"&mm";
	}
}


