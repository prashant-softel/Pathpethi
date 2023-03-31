var printMessage = "";
var idToSet = 0;
var prevDivContent = '';
var idToDelete = 0;
var sUrl = window.location.href;

function deleteVoucher(str)
{
	//alert(str);
	var iden	= new Array();
	iden		= str.split("-");

	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{
			idToDelete = iden[4];
			remoteCall("process/createvoucher.process.php","method="+iden[0]+"&Vno="+iden[1]+"&gid="+iden[2]+"&lid="+iden[3],"DeleteRecord");
		}
	}
	
}

function DeleteRecord()
{
	
	alert("Record Deleted Successfully....");
	location.reload(true);	
	//document.getElementById('tr_' + idToDelete).style.display = 'none';
	//idToDelete = 0;
}

function ViewVoucherDetail(ledgerID, groupID, voucherType, voucherID)
{
	idToSet = voucherID;
	
	var sURL = "ajax/view_ledger_details.ajax.php";
	var obj = {"lid" : ledgerID, "vid" : voucherID, "vtype" : voucherType, "getvoucherdetails" : "getvoucherdetails"};
	remoteCallNew(sURL, obj, 'detailsFetched');	
}

function detailsFetched()
{
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	var sMsg = sResponse;
	
	//window.location.href = sUrl + "#openDialogOk";
	
	var sText = '<a href="#close" title="Close" class="close" id="close" onClick="closeDialogBox();">X</a>';
	sText += '<center><font style="font-size:18px;"><b>Voucher Details</b></center></font>' + sMsg + '<br/>';
	sText += '<center><button name="Close" class="closeButton" id="dialogYesNo_yes"  onClick="closeDialogBox();">Close</button></center>';
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

function ShowJV(lid)
{	
	window.open('createvoucher.php?lid='+lid,'popup','type=width=700,height=600,scrollbars=yes');
}

function ShowBankAccountDetails()
{
	window.open('BankAccountDetails.php','popup','type=width=700,height=600,scrollbars=yes');
}



function format(number) 
{
	number = String(number).replace(/,/g,'')
	if(number.length == 0)
	{
		number = 0;	
	}
	var bIsNegative = false;
	if(number < 0)
	{
		bIsNegative = true;
		number = Math.abs(number);
	}
	
    var decimalSeparator = ".";
    var thousandSeparator = ",";

    // make sure we have a string
    var result = String(number);

    // split the number in the integer and decimals, if any
    var parts = result.split(decimalSeparator);

    // if we don't have decimals, add .00
    if (!parts[1]) {
      parts[1] = "00";
    }
  
    // reverse the string (1719 becomes 9171)
    result = parts[0].split("").reverse().join("");

    // add thousand separator each 3 characters, except at the end of the string
    result = result.replace(/(\d{3}(?!$))/g, "$1" + thousandSeparator);

    // reverse back the integer and replace the original integer
    parts[0] = result.split("").reverse().join("");
	
    // recombine integer with decimals
    return  (bIsNegative == true)? ('-' + parts.join(decimalSeparator)) : (parts.join(decimalSeparator));
}

