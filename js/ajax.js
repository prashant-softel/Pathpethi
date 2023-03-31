var xmlHttp;
var uri = "";
var callingFunc = "";
var sResponse = "";

function GetXmlHttpObject()
{
	xmlHttp=null;
	try
	{
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	}
	catch (e)
	{
	  // Internet Explorer
		try
		{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}


function remoteCall(sUrl, sQueryStr, sCalledBy)
{
	uri = sUrl;
	callingFunc = sCalledBy;
	
	xmlHttp=GetXmlHttpObject();
	
	if (xmlHttp==null)
	{
		alert ("Your browser does not support AJAX!");
		return;
	}
	if (xmlHttp) 
	{
		xmlHttp.onreadystatechange = stateHandler;
		xmlHttp.open("POST", sUrl, true);
		xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xmlHttp.send(sQueryStr);
	}	
}

function stateHandler() 
{
	if(xmlHttp.readyState == 4)
	{
		sResponse = xmlHttp.responseText;
		eval(callingFunc+'()');
	}
	return true;
}