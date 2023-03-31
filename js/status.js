// JavaScript Document


function viewMemberStatus(unitID)
{
	var sURL = "ajax/ajaxunit.php";
	var obj = {"unitID" : unitID,"getMemberStatus" : "getMemberStatus"};
	remoteCallNew(sURL, obj, 'detailsFetched');	
}

function detailsFetched()
{
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	var sMsg = sResponse;
	
	//window.location.href = sUrl + "#openDialogOk";
	
	/*var sText = '<a href="" title="Close" class="close" id="close" onClick="closeDialogBox();">X</a>';
	var sText = '<center><font style="font-size:18px;"><b>Ownership Details [' + document.getElementById('unit_no').value + ']</b></center></font>' + sMsg + '<br/><br/>';*/
	var sText = '</font>' + sMsg + '<br/><br/>';
	sText += '<center><button name="Close" class="closeButton" id="dialogYesNo_yes"  onClick="closeDialogBox();">Close</button>';
	sText += '&nbsp;<button name="printButton" id="printButton" class="closeButton"  onClick="printDialogBox();">Print</button></center>';
	document.getElementById('message_ok').innerHTML = sText;
	$( document.body ).css( 'pointer-events', 'none' );
	document.getElementById('openDialogOk').style.opacity = 1;
	$('#openDialogOk').css( 'pointer-events', 'auto' );
		
}

function closeDialogBox()
{
		document.getElementById('openDialogOk').style.opacity = 0;
		$( document.body ).css( 'pointer-events', 'auto' );
		$('#openDialogOk').css( 'pointer-events', 'none' );
				
}

function printDialogBox()
{
		document.getElementById('dialogYesNo_yes').style.visibility = "hidden";
		document.getElementById('printButton').style.visibility = "hidden";
		 var sHeader = "<html><head></head>";
		 sHeader += '<style> table,th,td{border: 1px solid #cccccc;text-align:center;border-collapse:collapse; padding:10px;}</style><body>';	
        var sFooter = "</body></html>";
		document.getElementById("society_header").style.display = "block";
		//document.getElementById("outerDiv").style.border = "1px solid black";
		var printContents = sHeader +  "<center>" + document.getElementById('openDialogOk').innerHTML +  "</center>" + sFooter;
		document.getElementById('dialogYesNo_yes').style.visibility = "visible";
		document.getElementById('printButton').style.visibility = "visible";
		document.getElementById("society_header").style.display = "none";
		memberStatusWindow=window.open('','');
		memberStatusWindow.document.write(printContents);
		memberStatusWindow.focus();
		memberStatusWindow.print(); 
		memberStatusWindow.close(); 
		
}
