var RESPONSETYPE_JSON = 1;
var RESPONSETYPE_STRING = 2;

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
		//xmlhttp.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
		xmlHttp.send(sQueryStr);
	}	
}

function remoteCallNew(sURL, nParameters, sResultFunction)
{
	//sURL = generateURL(sURL, nParameters);
	
	//alert(sURL);
	var sQueryStr = generateURL(sURL, nParameters);
	//alert(sQueryStr);
	uri = sURL;
	callingFunc = sResultFunction;
	
	xmlHttp=GetXmlHttpObject();
	
	if (xmlHttp==null)
	{
		alert ("Your browser does not support AJAX!");
		return;
	}
	if (xmlHttp) 
	{
		xmlHttp.onreadystatechange = stateHandler;
		xmlHttp.open("POST", sURL, true);
		xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xmlHttp.send(sQueryStr);
		//xmlHttp.send(nParameters);
	}	
}

function generateURL(sURL, nData)
{
	//alert(Object.keys(nData).length);
	var sQueryStr = "";
	if(Object.keys(nData).length > 0)
	{
		//sQueryStr += "?";
		for(var i = 0; i < Object.keys(nData).length; i++)
		{
			var sData = (Object.keys(nData)[i] + '=' + nData[Object.keys(nData)[i]]);
			sQueryStr += sData + '&';
		}
		sQueryStr += 'test=test';	
	}
	
	//var aes=new pidCrypt.AES.CBC();
	//aes.encryptText(sQueryStr, "secret", {nBits: 128});
	
	return sQueryStr;
}

function stateHandler() 
{
	if(xmlHttp.readyState == 4)
	{
		sResponse = xmlHttp.responseText;
		//alert(sResponse);
		//var sParseResponse = jQuery.parseJSON(sResponse);
		//eval(callingFunc +'("' + sResponse + '")');
		eval(callingFunc +'()');
	}
	return true;
}

function getResponse(eResponseType, bResetResult)
{
	var sData = sResponse;
	
	if(eResponseType == null)
		eResponseType = RESPONSETYPE_JSON;
		
	if(bResetResult	== null)
		bResetResult = true;
		
	//Reset the query result.
	if(bResetResult == true)
	{
		sResponse = "";
	}
			
	switch(eResponseType)
	{
		case RESPONSETYPE_JSON:
			return jQuery.parseJSON(sData);
			break;
		case RESPONSETYPE_STRING:
		default:
			return sData;
	}
}