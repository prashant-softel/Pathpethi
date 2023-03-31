var category = "";
function getaccount_category(str)
{
	var iden	= new Array();
	iden		= str.split("-");
    
	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{
			remoteCall("ajax/account_category.ajax.php","form=account_category&method="+iden[0]+"&account_categoryId="+iden[1],"loadchanges");
		}
	}
	else
	{		
		remoteCall("ajax/account_category.ajax.php","form=account_category&method="+iden[0]+"&account_categoryId="+iden[1],"loadchanges");
	}
}

function get_category(group_id)
{
	if(group_id == 0)
	{
		$('select#parentcategory_id').empty();
		$('select#parentcategory_id').append(
			$('<option></option>')
			.val('0')
			.html('All'));
	}
	else
	{
		document.getElementById('error').style.display = '';	
		document.getElementById('error').innerHTML = 'Fetching Categories. Please Wait...';	
		populateDDListAndTrigger('select#parentcategory_id', 'ajax/account_category.ajax.php?getcategory&groupid=' + group_id, 'category', 'hide_error', false);
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
		document.getElementById('group_id').value=arr2[2];
		category = arr2[3];		
		//document.getElementById('parentcategory_id').value=arr2[3];
		document.getElementById('category_name').value=arr2[4];
		document.getElementById('description').value=arr2[5];
		document.getElementById("is_fd_category").checked = (arr2[6] == 1) ? true : false;
		//document.getElementById('opening_type').value=arr2[6];
		//document.getElementById('is_bill_item').value=arr2[7];
		//document.getElementById('opening_balance').value=arr2[7];
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
		populateDDListAndTrigger('select#parentcategory_id', 'ajax/account_category.ajax.php?getcategory&groupid=' + arr2[2], 'category', 'set_parentcategoryid', false);	
		window.scroll(0, 0);	
	}
	else if(arr1[0] == "delete")
	{	//alert(arr2[0]);	
		window.location.href ="account_category.php";
	}
}

function set_parentcategoryid()
{		
	document.getElementById('parentcategory_id').value = category;
}