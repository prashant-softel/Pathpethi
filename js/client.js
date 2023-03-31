function getClient(str)
{	
	var iden	= new Array();
	iden		= str.split("-");	
	if(iden[0]=="edit")
	{		
		remoteCall("ajax/client.ajax.php","form=newclient&method="+iden[0]+"&cID="+iden[1],"loadchanges");
	}
}

function getNeftDetails(neftID)
{
	var iden=new Array();
	iden=neftID.split("-");
	//alert(iden[1]);
	
	if(iden[0]=="delete")
	{
		var d=confirm("Are you sure , you want to delete it ???");
		if(d==1)
		{
			var obj = {"form" : "neft", "method" : iden[0], "neftId" : iden[1]};
			remoteCallNew("ajax/ajaxneft.php", obj, 'loadchanges');
		}
	}
	else
	{
		var obj = {"form" : "neft", "method" : iden[0], "neftId" : iden[1]};
		remoteCallNew("ajax/ajaxneft.php", obj, 'loadchanges');
	}
}

function loadchanges()
{
	var a		= sResponse.trim();			
	var arr1	= new Array();
	var arr2	= new Array();
	arr1		= a.split("@@@");
	arr2		= arr1[1].split("#");			
	if(sResponse != null)
	{
		document.getElementById('client_name').value = arr2[1];
		document.getElementById('mobile').value = arr2[2];
		document.getElementById('landline').value = arr2[3];
		document.getElementById('email').value = arr2[4];
		document.getElementById('address').value = arr2[5];
		CKEDITOR.instances.email_header.setData(arr2[6]);
		CKEDITOR.instances.email_footer.setData(arr2[7]);
		document.getElementById('sms_userid').value = arr2[8];	
		document.getElementById('sms_key').value = arr2[9];	
		document.getElementById('sms_domain').value = arr2[10]; 
		document.getElementById('sms_senderid').value = arr2[11]; 
		document.getElementById('details').value = arr2[12];	
		CKEDITOR.instances.bill_footer.setData(arr2[13]);
		document.getElementById('id').value = arr2[0];	
		document.getElementById('new_entry').style.display = 'block';
		if(document.getElementById('btnAdd'))
		{
			document.getElementById('btnAdd').style.display = 'none';				
		}
		
		document.getElementById("insert").value="Update";
		window.scroll(0,0);
	}
}

function approve(id)
{
	var obj = {"form" : "neft", "method" : "edit", "neftId" : id};
	remoteCallNew("ajax/ajaxneft.php", obj, 'setConfirm');
}

function setConfirm()
{
	var sResponse = getResponse(RESPONSETYPE_JSON, true);
	if(sResponse != null)
	{
		document.getElementById('PaidBy').value = sResponse[0]['paid_by'];
		//document.getElementById('PaidBy').disabled = true;
		document.getElementById('PaidTo').value = sResponse[0]['paid_to'];
		//document.getElementById('PaidTo').disabled = true;
		document.getElementById('BankName').value = sResponse[0]['payer_bank'];
		//document.getElementById('BankName').disabled = true;
		document.getElementById('BranchName').value = sResponse[0]['payer_branch'];
		//document.getElementById('BranchName').disabled = true;
		document.getElementById('Amount').value = sResponse[0]['amount'];
		//document.getElementById('Amount').disabled = true;
		document.getElementById('Date').value = sResponse[0]['date'];
		//document.getElementById('Date').disabled = true;
		document.getElementById('AcNumber').value = sResponse[0]['acc_no'];
		//document.getElementById('AcNumber').disabled = true;
		document.getElementById('TransationNo').value = sResponse[0]['transaction_no'];
		//document.getElementById('TransationNo').disabled = true;
		document.getElementById('Comments').value = sResponse[0]['comments'];
		
		document.getElementById("id").value = sResponse[0]['ID'];
		document.getElementById("insert").value="Approve";
		
		scroll(0, 0);
	}
}