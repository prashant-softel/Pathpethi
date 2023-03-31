function  FetchOwnershipTransferHistory(society_id)
{
	showLoader();
	var yearid = document.getElementById('Year').value;
	
	$.ajax({
			url : "ajax/ajaxOwnershipTransferhistory.php",
			type : "POST",
			data : {"method" : 'fetch',"societyID" : society_id,"yearid" : yearid},
			beforeSend: function()
			{
				document.getElementById('showTable').innerHTML = '<center><font color="blue">Fetching Records Please Wait...</font></center>';
			},
			success : function(data)
			{	
				document.getElementById('showTable').innerHTML = data;
				hideLoader();
				
				if(document.getElementById('AllowExport').value  ==  1 && $.trim(data))
				{
					//data not empty
					document.getElementById('btnExport').style.display = 'block';
					document.getElementById('Print').style.display = 'block';
				}
				else
				{
					//data empty
					document.getElementById('btnExport').style.display = 'none';
					document.getElementById('Print').style.display = 'none';
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
}
