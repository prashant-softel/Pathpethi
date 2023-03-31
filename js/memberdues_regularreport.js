function sendNotice()
{
	
	var unitAry = document.getElementById('unit_ary').value.split('#');
	
	var UnitArray = [];
	for(var iCnt = 0 ; iCnt < unitAry.length - 1; iCnt++)
	{
		if(document.getElementById('chk_' + unitAry[iCnt]).checked == true)
		{
			//alert("checked is: "+unitAry[iCnt]);
			UnitArray.push(unitAry[iCnt]);
			
		}
		
	}
	
	if (confirm("Are you sure , you want to send it ?")) 
	{
		 
	//alert(UnitArray);
	$.ajax({
					
				url : "ajax/memberdues_regularreport.ajax.php",
				type : "POST",
				data : {"method" : 'sendDuesNotice',"UnitArray" :JSON.stringify(UnitArray)},
	
		success : function(data)
				{	
					
					alert("Notice send successfully");
				
				},
			fail: function()
			{
				alert("Notice not send");
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				alert('Notice not send');
				//alert(textStatus);
				//alert(errorThrown);
			}
			});
}
else
{
}
}
