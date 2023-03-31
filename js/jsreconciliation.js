function filterData()
{
	//alert("Filter Data");
	return "Filter";	
}

function ValidateForm(minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate)
{
	var fromdateValue = document.getElementById('From').value;	
	var todateValue = document.getElementById('To').value;
	
	if(fromdateValue.length > 0 && todateValue.length == 0)
	{
		
		if(jsdateValidator('From',fromdateValue,minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate) == true)
		{
			return true;
		}
		else
		{
			return false;
		}	
		
	}
	
	if(todateValue.length > 0 && fromdateValue.length == 0)
	{
		
		if(jsdateValidator('To',todateValue,minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate) == true)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	if(todateValue.length > 0 && fromdateValue.length > 0)
	{
		
		if(jsdateValidator('From',fromdateValue,minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate) && jsdateValidator('To',todateValue,minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate) == true)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	return true;
}