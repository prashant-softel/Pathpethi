function getBankDetails(str)
{
	var iden	= new Array();
	iden		= str.split("-");

	//$('html, body').animate({ scrollTop: $('#top').offset().top }, 300);

	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{
			
			remoteCall("ajax/ajaxBankDetails.php","form=BankDetails&method="+iden[0]+"&BankDetailsId="+iden[1],"loadchanges");
		}
	}
	else
	{

		remoteCall("ajax/ajaxBankDetails.php","form=BankDetails&method="+iden[0]+"&BankDetailsId="+iden[1],"loadchanges");
	}
}

function loadchanges()
{
	var a		= sResponse.trim();
	var arr1	= new Array();
	var arr2	= new Array();
	arr1		= a.split("@@@");
	arr2		= arr1[1].split("#");

	if(arr1[0] == "edit")
	{
		document.getElementById('LedgerID').innerHTML = '<option>' + arr2[3].trim() + '</option>';
		document.getElementById('LedgerID').disabled = true;
		
		document.getElementById('BankName').value=arr2[1];
		document.getElementById('BranchName').value=arr2[2];
		
		//document.getElementById('LedgerName').value=arr2[3];
		//document.getElementById('LedgerName').disabled = true;
		
		document.getElementById('AcNumber').value=arr2[4];
		document.getElementById('Address').value=arr2[5];
		document.getElementById('IFSC_Code').value=arr2[6];
		document.getElementById('MICR_Code').value=arr2[7];
		document.getElementById('Phone1').value=arr2[8]
		document.getElementById('Phone2').value=arr2[9];
		document.getElementById('Fax').value=arr2[10];
		document.getElementById('Email').value=arr2[11];
		document.getElementById('Website').value=arr2[12];
		document.getElementById('ContactPerson').value=arr2[13];
		document.getElementById('ContactPersonPhone').value=arr2[14];
		document.getElementById('Note').value = arr2[15];
				
		document.getElementById('Balance').value=arr2[16];
		//document.getElementById('Balance').disabled = true;
		
		document.getElementById('Balance_Date').value=arr2[18];
		//document.getElementById('Balance_Date').disabled = true;
				
		document.getElementById("id").value=arr2[0];

		if(arr2[17] == "1")
		{
			document.getElementById('AllowNEFT').checked = true;
		}
		else 
		{
			document.getElementById('AllowNEFT').checked = false;
		}
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{

		window.location.href ="../BankDetails.php?mst&"+arr1[1]+"&mm";
	}
}

function ledgerChange(ledger)
{
	if(ledger.value == 0)
	{
		document.getElementById('Balance').disabled = false;
		document.getElementById('Balance').value = 0;
		document.getElementById('Balance_Date').disabled = false;
		document.getElementById('Balance_Date').value = '';
		document.getElementById('Balance').value = '';
		document.getElementById('BankName').value = '';
		//document.getElementById('LedgerName').value = document.getElementById('BankName').value + ' ' + document.getElementById('BranchName').value;
	}
	else
	{
		//document.getElementById('Balance').disabled = true;
		//document.getElementById('Balance').value = '';
		//document.getElementById('Balance_Date').disabled = true;
		//document.getElementById('Balance_Date').value = '';
		//document.getElementById('LedgerName').disabled = true;
		document.getElementById('BankName').value = document.getElementById('LedgerID').options[document.getElementById('LedgerID').selectedIndex].text;;
		getOpeningBalanceAndDate();
	}
}

function getOpeningBalanceAndDate()
{
	document.getElementById('error').style.display = 'block';
	document.getElementById('error').innerHTML = 'Fetching Opening Balance...';
	var LedgerID = document.getElementById('LedgerID').value;
	
	var sURL = "ajax/ajaxBankDetails.php";
	var obj = {'getbalance':'', 'ledger': LedgerID };
	remoteCallNew(sURL, obj, 'balanceFetched');
}

function balanceFetched()
{
	document.getElementById('error').innerHTML = '';
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	aryResult = sResponse.split('@@@');
	if(aryResult[0] != null)
	{
		document.getElementById('Balance_Date').value = aryResult[0].trim();
	}
	if(aryResult[1] != null)
	{
		document.getElementById('Balance').value = aryResult[1].trim();
	}
}