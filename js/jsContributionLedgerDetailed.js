function  FetchBillRegisterSummary(society_id)
{
	showLoader();
	var ignore_zero = document.getElementById('ignore_zero').checked;
	
	var checks = document.getElementsByClassName('checkBox');
	var UnitArray = [];
    var flag = false;
	checks.forEach(function(val, index, ar) {
		if(ar[index].checked && flag == false) 
		{
			
			if(ar[index].id > 0)
			{
				UnitArray.push(ar[index].id);
			}
			else
			{
				flag = true;
				UnitArray = [];
				checks.forEach(function(val, index, ar) 
				{
					if(ar[index].id > 0)
					{
						UnitArray.push(ar[index].id);
					}
					
					
				});
				
				
			}
			
           
        }
		
    });
	
	
	if(UnitArray.length == 0)
	{
		//this array is empty
		 UnitArray = [];
		checks.forEach(function(val, index, ar) 
		{
			if(ar[index].id > 0)
			{
				UnitArray.push(ar[index].id);
			}
			
			
		});
	}
	
	$.ajax({
			url : "ajax/ajaxContributionLedgerDetailed.php",
			type : "POST",
			data : {"method" : 'fetch',"societyID" : society_id,"unitIDArray" : JSON.stringify(UnitArray),"ignore_zero" : ignore_zero},
			beforeSend: function()
			{
				document.getElementById('showTable').innerHTML = '<center><font color="blue">Fetching Records Please Wait...</font></center>';
			},
			success : function(data)
			{	
				document.getElementById('showTable').innerHTML = data;
				hideLoader();
				if(document.getElementById('AllowExport').value  ==  1)
				{
					document.getElementById('btnExport').style.display = 'block';
					document.getElementById('Print').style.display = 'block';
				}
				
			},
				
			fail: function()
			{
				
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});
		
}

function PrintPage() 
	{
		var originalContents = document.body.innerHTML;
		document.getElementById('societyname').style.display ='block';	
		var printContents = document.getElementById('showTable').innerHTML;
		
		document.body.innerHTML = printContents;
		window.print();
	
		document.body.innerHTML= originalContents;
		 document.getElementById('societyname').style.display ='none';	
	}

