// JavaScript Document

function get_category(group_id, selectIndex)
{
	if(selectIndex == null)
	{
		selectIndex = 1;
	}
	
	if(group_id == 0)
	{
		$('select#category_id').empty();
		$('select#category_id').append(
			$('<option></option>')
			.val('0')
			.html('All'));
	}
	
	else
	{
		populateDDListAndTrigger('select#category_id', 'ajax/ledger_details.ajax.php?getcategory&groupid=' + group_id, 'category', 'hide_error', false, selectIndex);
	}
}


function getReserveBill(str)
{
	// alert(str);
	var iden	= new Array();
	iden		= str.split("-");
	// alert(iden[1])
	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ?");
		if(conf==1)
		{
			remoteCall("ajax/reverse_charges.ajax.php","form=reverse_charges&method="+iden[0]+"&reverse_chargesId="+iden[1],"loadchanges");
			window.location.reload();
		}
	}
	else
	{
		remoteCall("ajax/reverse_charges.ajax.php","form=reverse_charges&method="+iden[0]+"&reverse_chargesId="+iden[1],"loadchanges");
	}
}
function loadchanges()
{
	var a		= sResponse.trim();
	var arr1	= new Array();
	var arr2	= new Array();
	arr1		= a.split("@@@");
	arr2		= arr1[1].split("#");
	
 var year_id = document.getElementById('year').value;
	if(arr1[0] == "edit")
	{
		
		var date = arr2[1];
		var billtype = arr2[7];

		
		if(date)
		{
			$.ajax(
			{
				url: "ajax/reverse_charges.ajax.php",
				type: "POST",
				data: "date="+date+"&year_id="+year_id+"&billtype="+billtype,
				success: function(result)
				{
					
					var res = result.split("@@@");
					if(res[1])
					{
						var res2 = res[1].split("#");

					}
					
					if(res2)
					{
						document.getElementById('bill_id').value=arr2[0];
						document.getElementById('bill_method').value=arr2[7];
						document.getElementById('ledger').value=arr2[5];
						$("#period_id").empty().append('<option selected="selected" value='+res2[0]+'>'+res2[1]+'**</option>');
						
							document.getElementById("comments").value= arr2[6];
							document.getElementById("insert").value="Update";
							
							if(arr2[8] == 1)
							{
								var amount = arr2[4];
								document.getElementById("amount").value = Math.abs(amount);
								document.getElementById("rev_charge").checked = true;
							}
							else if(arr2[8] == 2)
							{
								document.getElementById("amount").value = arr2[4];
								document.getElementById("fine").checked = true;

							}
							else
							{
								document.getElementById("amount").value = Math.abs(arr2[4]);
								document.getElementById("rev_charge").checked = true;
							}
										
					}
					else
					{
						alert("Please change the financial year to update this record.");
						document.getElementById("amount").value = 0;
						document.getElementById("fine").checked = false;
						document.getElementById("rev_charge").checked = false;
						document.getElementById("comments").value= '';
						document.getElementById("insert").value="Submit";
						document.getElementById('ledger').value=0;
						return;
					}
					  
					 
				}
			
			
			});
		}
		
		
		// document.getElementById('year_id').value=arr2[];
	
		
 	}
	else if(arr1[0] == "delete")
	{
		var id = arr2[0];
		$.ajax(
		{
			url: "ajax/reverse_charges.ajax.php",
			type: "POST",
			data: "id="+id,
			success: function(result)
			{
				var res = result.split("@@@");
				
				if(res[1] > 0)
				{
					alert("Record deleted successfully");
					location.reload();
				}
			}
		});
		
	}
}
