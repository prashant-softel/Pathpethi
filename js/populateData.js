// JavaScript Document
function populateTableList(selobj, url, nameattr)
{
	var aryData = new Array();
	
	$(selobj).empty();
	$.getJSON(url,{},function(data)
	{
		$(selobj).append(
				$('<tr></tr>').append(
				$('<th></th>')
					.html('Distributor List')
				));
				
		var bHasData = false;
		$.each(data, function(i,obj)
		{
			if(obj[nameattr] != null)
			{
				bHasData = true;
				$(selobj).append(
				$('<tr></tr>').append(
				$('<td></td>')
				.val(obj['id'])
				.html(obj[nameattr])));
				
				aryData.push((obj[nameattr]).toUpperCase());
			}
		});
		if(bHasData == false)
		{
			$(selobj).append(
				$('<tr></tr>').append(
				$('<td></td>')
					.html('No Data To Display')
				));
		}
	});
	
	return aryData;
}

function populateDDList(selobj, url, nameattr, bShowAllOption)
{
	if(bShowAllOption == null)
	{
		bShowAllOption = false;
	}
	
	$(selobj).empty();
	$.getJSON(url,{},function(data)
	{
		if(data.length > 0 && bShowAllOption)
		{
			$(selobj).append(
				$('<option></option>')
				.val('0')
				.html('All'));
		}
		
		var bHasData = false;
		$.each(data, function(i,obj)
		{
			if(obj[nameattr] != null)
			{
				bHasData = true;
				$(selobj).append(
				$('<option></option>')
				.val(obj['id'])
				.html(obj[nameattr]));
			}
		});
		if(bHasData == false)
		{
			$(selobj).empty();
			$(selobj).append(
				$('<option></option>')
				.val("-1")
				.html('No Data To Display'));
		}
	});
}

function populateDDListAndTrigger(selobj, url, nameattr, triggerFunc, bShowAllOption, selectedIndex)
{
	if(bShowAllOption == null)
	{
		bShowAllOption = false;
	}
	
	$(selobj).empty();
	$.getJSON(url,{},function(data)
	{
		if(data.length > 0 && bShowAllOption)
		{
			$(selobj).append(
				$('<option></option>')
				.val('0')
				.html('All'));
		}
		
		var bHasData = false;
		$.each(data, function(i,obj)
		{
			if(obj[nameattr] != null)
			{
				bHasData = true;
				$(selobj).append(
				$('<option></option>')
				.val(obj['id'])
				.html(obj[nameattr]));
			}
		});
		if(bHasData == false)
		{
			$(selobj).empty();
			$(selobj).append(
				$('<option></option>')
				.val("0")
				.html('No Data To Display'));
		}
		else
		{
			if(selectedIndex != null)
			{
				$(selobj).val(selectedIndex);
			}
		}
		//eval(triggerFunc +'(' + aryData + ')');
		eval(triggerFunc +'()');
	});
	//return aryData;
}