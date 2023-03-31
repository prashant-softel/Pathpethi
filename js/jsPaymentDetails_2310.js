function GetChequeDetails(ChequeNumber, iCounter, LeafID)
{
	$.ajax({
			url : "ajax/ajaxPaymentDetails.php",
			type : "POST",
			data: { "ChequeNumber":ChequeNumber, "LeafID":LeafID, "mode":"Fill"} ,
			success : function(data)
			{	
				//alert(data[0]['Amount']);
				var a		= data.trim();
				var arr1	= new Array();
				var arr2	= new Array();
				arr1		= a.split("@@@");
				if(arr1[1].length > 0)
				{
					arr2		= arr1[1].split("#");
					document.getElementById('PaidTo'+iCounter).value=arr2[0];
					document.getElementById('PaidTo'+iCounter).style.backgroundColor = 'lightgray';
					document.getElementById('PaidTo'+iCounter).disabled = true;
					document.getElementById('DE' + iCounter).checked=false;
					if(arr2[1] != 0)
					{
						document.getElementById('ExpenseTo'+iCounter).disabled = false;
						//document.getElementById('ExpenseTo'+iCounter).color = '#FF0000';
						document.getElementById('ExpenseTo'+iCounter).value=arr2[1];
						document.getElementById('ExpenseTo'+iCounter).disabled = true;
						document.getElementById('DE' + iCounter).checked=true;
					}
					
					document.getElementById('DE' + iCounter).disabled=true;
					document.getElementById('ExpenseTo'+iCounter).style.backgroundColor = 'lightgray';
					
					document.getElementById('ChequeDate'+iCounter).value=arr2[2];
					document.getElementById('ChequeDate'+iCounter).style.backgroundColor = 'lightgray';
					
					document.getElementById('Amount'+iCounter).value=arr2[3];
					document.getElementById('Amount'+iCounter).style.backgroundColor = 'lightgray';
					
					document.getElementById('Comment'+iCounter).value=arr2[4];
					document.getElementById('Comment'+iCounter).style.backgroundColor = 'lightgray';
					
					document.getElementById('ChequeNumber'+iCounter).value=ChequeNumber;
					document.getElementById('ChequeNumber'+iCounter).style.backgroundColor = 'lightgray';
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

function getExistingData(chqAry, LeafNo, bIsCustomLeaf)
{
	if(bIsCustomLeaf == null)
	{
		bIsCustomLeaf = "0";
	}
	$.ajax({
			url : "ajax/ajaxPaymentDetails.php",
			type : "POST",
			data: { "Cheque": JSON.stringify(chqAry), "LeafID":LeafNo, "mode":"Fill","CustomLeaf": bIsCustomLeaf} ,
			success : function(data)
			{	
				//alert(LeafNo);
				//alert(data);
				var arr1	= new Array();
				var arr2	= new Array();
				var arr3	= new Array();
				var arr4	= new Array();
				arr1		= data.split("@@@");
				
				arr2 = arr1[1].split('^^');
				
				
				for(var i = 1 ; i < arr2.length ; i++)
				{
					//alert(arr2[i]);
					arr3 = arr2[i].split('@@');
					//alert(arr3);
					//for(var j = 0 ; j < (arr4.length - 1) ; j++)
					{
						//alert(iCounter + ":" + arr4[j]);
						
						if(arr3[1].length > 0)
							
						{
							var iCounter = arr3[0];
					var iData = arr3[1];
					//alert(iCounter);
					//alert(iData);
					arr4 = iData.split('#');
					
					
							//arr2		= arr1[1].split("#");
							document.getElementById('PaidTo'+iCounter).value= arr4[0];
							document.getElementById('PaidTo'+iCounter).style.backgroundColor = 'lightgray';
							document.getElementById('PaidTo'+iCounter).disabled = true;
							document.getElementById('DE' + iCounter).checked=false;
							if(arr4[8] == 1)
							{
								document.getElementById('ModeOfPayment' + iCounter).value = 1;
							}
							if(arr4[1] != 0)
							{
								//document.getElementById('ExpenseTo'+iCounter).disabled = false;
								//document.getElementById('ExpenseTo'+iCounter).color = '#FF0000';
								document.getElementById('ExpenseTo'+iCounter).value=arr4[1];
								document.getElementById('ExpenseTo'+iCounter).disabled = true;
								document.getElementById('DE' + iCounter).checked=true;
							}
							
							document.getElementById('DE' + iCounter).disabled=true;
							document.getElementById('ExpenseTo'+iCounter).style.backgroundColor = 'lightgray';
							
							document.getElementById('ChequeDate'+iCounter).value=arr4[2];
							document.getElementById('ChequeDate'+iCounter).disabled = true;
							document.getElementById('ChequeDate'+iCounter).style.backgroundColor = 'lightgray';
							
							document.getElementById('Amount'+iCounter).value=arr4[3];
							document.getElementById('Amount'+iCounter).disabled = true;
							document.getElementById('Amount'+iCounter).style.backgroundColor = 'lightgray';
							
							document.getElementById('Comment'+iCounter).value=arr4[7];
							document.getElementById('Comment'+iCounter).disabled = true;
							document.getElementById('Comment'+iCounter).style.backgroundColor = 'lightgray';
							
							document.getElementById('ChequeNumber'+iCounter).value=arr4[6];
							document.getElementById('ChequeNumber'+iCounter).disabled = true;
							document.getElementById('ChequeNumber'+iCounter).style.backgroundColor = 'lightgray';
							if(arr4[4] !=0)
							{
							document.getElementById('TDSAmount'+iCounter).value=arr4[4];
							}
							document.getElementById('TDSAmount'+iCounter).disabled = true;
							document.getElementById('TDSAmount'+iCounter).style.backgroundColor = 'lightgray';
							
							//alert(arr4[5]);
							
							document.getElementById('InvoiceDate'+iCounter).value=arr4[5];
							document.getElementById('InvoiceDate'+iCounter).disabled = true;
							document.getElementById('InvoiceDate'+iCounter).style.backgroundColor = 'lightgray';
							
							
							
							}
							else
							{
								
								var iCounter = arr3[0];
								//document.getElementById("ExpenseTo" + ichkID).innerHTML = "<?php //echo "<option value='0'></option>"?>";
								document.getElementById('ExpenseTo' + iCounter).disabled = true;
								document.getElementById('ExpenseTo'+iCounter).style.backgroundColor = 'lightgray';
								document.getElementById('InvoiceDate' + iCounter).disabled = true;
								document.getElementById('InvoiceDate'+iCounter).style.backgroundColor = 'lightgray';
								document.getElementById('TDSAmount' + iCounter).disabled = true;
								document.getElementById('TDSAmount' + iCounter).style.backgroundColor = 'lightgray';
							}
						}
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

function getPaymentDetails(str)
{
	var iden	= new Array();
	iden		= str.split("-");

	//$('html, body').animate({ scrollTop: $('#top').offset().top }, 300);

	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{
			$(document).ready(function()
			{
				$("#error").fadeIn("slow");
				document.getElementById("error").innerHTML = "Please Wait...";
			});

			remoteCall("ajax/ajaxPaymentDetails.php","form=PaymentDetails&method="+iden[0]+"&PaymentDetailsId="+iden[1],"loadchanges");
			
		}
	}
	else
	{
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
			document.getElementById("error").innerHTML = "Please Wait...";
		});

		remoteCall("ajax/ajaxPaymentDetails.php","form=PaymentDetails&method="+iden[0]+"&PaymentDetailsId="+iden[1],"loadchanges");
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
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
			document.getElementById("error").innerHTML = "Please Wait...";
		});
var maxRowCounter=document.getElementById('maxrows').value;
		//alert(maxRowCounter);
		//alert(arr2);
	for(var row=2;row < maxRowCounter;row++)
		{
			document.getElementById('PaidTo' + row).style.display = 'none';
			document.getElementById('Amount' + row).style.display = 'none';
			document.getElementById('ExpenseTo' + row).style.display = 'none';
			document.getElementById('InvoiceDate' + row).style.display = 'none';
			document.getElementById('TDSAmount' + row).style.display = 'none';
			document.getElementById('DE' + row).style.display = 'none';
			document.getElementById('Comment' + row).style.display = 'none';
			
			document.getElementById('CashRow' + row).style.display = 'none';
				document.getElementById('CashRowLabel' + row).style.display = 'none'; 
		}
		
		
		document.getElementById('Amount1').value=arr2[5];
		document.getElementById('Amount1').focus();
		document.getElementById('PaidTo1').value=arr2[6];
		document.getElementById('ExpenseTo1').value=arr2[7];
		document.getElementById('PayerBank').value=arr2[10];
		document.getElementById('Comment1').value=arr2[12];
		document.getElementById('InvoiceDate1').value=arr2[13];
		document.getElementById('TDSAmount1').value=arr2[14];
		if(arr2[13] !='')
		{
			
			document.getElementById('DE1').checked=true;
			document.getElementById('ExpenseTo1').disabled = false;
		}
		document.getElementById('Submit').innerHTML="Update";
		document.getElementById('Submit').onclick=function(){ UpdateCashPaymentDetails(arr2[0],arr2[4])};
		document.getElementById('Submit2').innerHTML="Update";
		document.getElementById('Submit2').onclick=function(){ UpdateCashPaymentDetails(arr2[0],arr2[4])};
	}
	else if(arr1[0] == "delete")
	{
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
			document.getElementById("error").innerHTML = "Please Wait...";
		});
		//alert("test");
		var BankID=document.getElementById('BankID').value;
		var LeafID=document.getElementById('LeafID').value;
		var CustomLeaf=document.getElementById('CustomLeaf').value;
//alert("PaymentDetails.php?bankid=" + BankID + "&LeafID=" + LeafID);
		window.location.href ="PaymentDetails.php?bankid=" + BankID + "&LeafID=" + LeafID + "&CustomLeaf=" + CustomLeaf;
	}
	document.getElementById("error").innerHTML = "";
}


function UpdateCashPaymentDetails(CashDetailsId,VoucherDate)
{
 	var BankID = document.getElementById('BankID').value;
	var PayerBank = document.getElementById('PayerBank').value;
	var LeafID = document.getElementById('LeafID').value;
	var arRowsToSubmit = [];
	var iSubmittedData = 0;
	var PaidTo = document.getElementById('PaidTo1').value;
	var iChequeNumber = '-1';
	var ChequeDate = -1;
	var iAmount = document.getElementById('Amount1').value;
	var ExpenseBy = document.getElementById('ExpenseTo1').value;
	var chkState = document.getElementById('DE1').checked;
	var InvoiceDate2 = document.getElementById('InvoiceDate1').value;
	var iTDSAmount = document.getElementById('TDSAmount1').value;
	var Comment = document.getElementById('Comment1').value;
	var iCheckState = 0;
	if(chkState)
	{
		iCheckState = 1;
	}
	if(iChequeNumber == "" || ChequeDate == "" ||  iAmount == "" || (iCheckState == 1 && ExpenseBy == 0) ||  (iCheckState == 1 && InvoiceDate2 == ""))
	{
		var EmptyField = "";
		if(iCheckState == 1 && ExpenseBy == 0)
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "ExpenseTo";
			}
			else
			{
				EmptyField += ", ExpenseTo";
			}
		}//alert(ChequeDate.length);
		
		
		if(iAmount.length == 0)
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "Amount";
			}
			else
			{
				EmptyField += ", Amount";
			}
		}
		
		if(iCheckState == 1 && InvoiceDate2 == "")
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "InvoiceDate";
			}
			else
			{
				EmptyField += ", InvoiceDate";
			}
			
		}
		document.getElementById('label1').innerHTML = "Please enter value for " + EmptyField +".";
		document.getElementById('label1').style.color = '#FF0000';
		//var strLabel = 'label'+ iRows;
		setTimeout('timeout(label1' +')', 5000);
	}
	else
	{		
		var obj = "";
		if(LeafID == -1)
		{
		obj = {"PaidTo" : PaidTo, "ChequeNumber" : iChequeNumber, "ChequeDate" : "0000-00-00", "Amount" : iAmount, "PayerBank" : PayerBank, "Comments" : Comment, "VoucherDate" : VoucherDate,"ExpenseBy" : ExpenseBy,"DoubleEntry" : iCheckState,
		"LeafID" : LeafID,"InvoiceDate" :InvoiceDate2,"TDSAmount" :iTDSAmount};
		}
		//alert("obj"+ obj);
		arRowsToSubmit.push(obj);
		if(LeafID == -1)
		{
			document.getElementById('PaidTo1').value ='';
			document.getElementById('Amount1').value = "";
			document.getElementById('TDSAmount1').value = "";
			document.getElementById('Comment1').value = "";
			document.getElementById('DE1').disabled=true;
			document.getElementById('ExpenseTo1').value = '';
			document.getElementById('InvoiceDate1').value = '';
		}
	var objData = {'data' : JSON.stringify(arRowsToSubmit), "method" : 'UpdateCashPaymentDetails',"CashDetailsId" : CashDetailsId}; 
	$.ajax({
			url : "ajax/ajaxPaymentDetails.php",
			type : "POST",
			data: objData ,
			success : function(data)
			{	
				alert('Data Submitted Successfully');
				if(LeafID == -1)
				{
					location.reload(true);
				}
				
			},
				
			fail: function()
			{
				//hideLoader();
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				//hideLoader();
			}
		});
		
		
	}
}


function dbUpdated()
{
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	if(sResponse == "Cheque Already issued")
	{
		//alert(sResponse);	
	}
	//alert("Data Updated Successfully");	
	//window.location.href = "PaymentDetails.php?bankid="+PayerBank;
}
function SubmitChequeDetails()
{
	//alert('test');
	//`ChequeDate`,`ChequeNumber`,`Amount`,`PaidTo`,`PayerBank`,`Comments`,`VoucherDate
	var iRowCount = document.getElementById('maxrows').value;
	//alert(iRowCount);
	var VoucherDate = document.getElementById('VoucherDate').value;
	//alert(VoucherDate);
	//var BankID = document.getElementById('BankID').value;
	var PayerBank = document.getElementById('PayerBank').value;
	var LeafID = document.getElementById('LeafID').value;
	
	//alert(DepositID);
	var arRowsToSubmit = [];
	var iSubmittedData = 0;
	for(var iRows = 1 ; iRows < iRowCount; iRows++)
	{
		var str = 'PaidTo'+ iRows;
		//alert(str);
		var iSocietyID = document.getElementById(str).value;
		
		//alert(iSocietyID);
		
		if(iSocietyID != "" && LeafID != "")
		{
			var PaidTo = document.getElementById('PaidTo'+ iRows).value;
			var LeafID = document.getElementById('LeafID').value;
			
			
			//alert(LeafID);
			//alert(iPaidBy);
			var iChequeNumber = 0;
			if(LeafID != -1)
			{
				iChequeNumber = document.getElementById('ChequeNumber'+ iRows).value;
			}
			else
			{
				iChequeNumber = '-1';
			}
			//alert(iChequeNumber);
			var ChequeDate = 0;
			if(LeafID != -1)
			{
				ChequeDate = document.getElementById('ChequeDate'+ iRows).value;
			}
			else
			{
				ChequeDate = -1;
			}
			//alert(ChequeDate);
			var iAmount = document.getElementById('Amount'+ iRows).value;
			var ExpenseBy = document.getElementById('ExpenseTo'+ iRows).value;
			var chkState = document.getElementById('DE' + iRows).checked;
			var InvoiceDate2 = document.getElementById('InvoiceDate'+ iRows).value;
			var iTDSAmount = document.getElementById('TDSAmount'+ iRows).value;
			var iModeOfPayment = 0;
			//var iCustomLeaf = document.getElementById('CustomLeaf').value;
			
			var iCheckState = 0;
			if(chkState)
			{
				iCheckState = 1;
			}
			//alert('chk state');
			//alert(ExpenseBy);
			
			if(iChequeNumber == "" || ChequeDate == "" ||  iAmount == "" || (iCheckState == 1 && ExpenseBy == 0) ||  (iCheckState == 1 && InvoiceDate2 == ""))
			{
				var EmptyField = "";
				if(iCheckState == 1 && ExpenseBy == 0)
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "ExpenseTo";
					}
					else
					{
						EmptyField += ", ExpenseTo";
					}
				}//alert(ChequeDate.length);
				if((iChequeNumber.length == 0 && LeafID == -1) || (document.getElementById('ModeOfPayment'+ iRows).value != 2))
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "ChequeNumber";
					}
					else
					{
						
						EmptyField += ", ChequeNumber";
					}
				}
				if(ChequeDate.length == 0 && LeafID == -1)
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "ChequeDate";
					}
					else
					{
						
						EmptyField += ", ChequeDate";
					}
				}
				if(iAmount.length == 0)
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "Amount";
					}
					else
					{
						EmptyField += ", Amount";
					}
				}
				
				if(iCheckState == 1 && InvoiceDate2 == "")
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "InvoiceDate";
					}
					else
					{
						EmptyField += ", InvoiceDate";
					}
					
				}
				
				
				
				document.getElementById('label'+ iRows).innerHTML = "Please enter value for " + EmptyField +".";
				document.getElementById('label'+ iRows).style.color = '#FF0000';
				//var strLabel = 'label'+ iRows;
				setTimeout('timeout(label'+ iRows +')', 5000);					
				if(document.getElementById('ModeOfPayment'+ iRows).value != 2 && iChequeNumber == "")
				{							
					continue;
				}
			}			
			//alert(PayerBank);
			var Comment = document.getElementById('Comment'+ iRows).value;
			//alert(PayerChequeBranch);
			
			//alert(ChequeDate);
			var obj = "";
			if(LeafID != -1)
			{
				iModeOfPayment = document.getElementById('ModeOfPayment'+ iRows).value;
				obj = {"SocietyID" : iSocietyID, "PaidTo" : PaidTo, "ChequeNumber" : iChequeNumber, "ChequeDate" : ChequeDate, "Amount" : iAmount, "PayerBank" : PayerBank, "Comments" : Comment, "VoucherDate" : ChequeDate,"ExpenseBy" : ExpenseBy,"DoubleEntry" : iCheckState, "update" : 'update',
			"LeafID" : LeafID,"InvoiceDate" :InvoiceDate2,"TDSAmount" :iTDSAmount,"ModeOfPayment" : iModeOfPayment};
			}
			else
			{
				
				obj = {'Row' : iRows,"SocietyID" : iSocietyID, "PaidTo" : PaidTo, "ChequeNumber" : iChequeNumber, "ChequeDate" : "0000-00-00", "Amount" : iAmount, "PayerBank" : PayerBank, "Comments" : Comment, "VoucherDate" : ChequeDate,"ExpenseBy" : ExpenseBy,"DoubleEntry" : iCheckState,
			"LeafID" : LeafID,"InvoiceDate" :InvoiceDate2,"TDSAmount" :iTDSAmount,"ModeOfPayment" : "-1"};
			}
			arRowsToSubmit.push(obj);
			iSubmittedData++;
	
			//document.getElementById('label'+ iRows).innerHTML = "Record Added";
			//setTimeout(timeout('label'+ iRows),8000);
			//document.getElementById(str).value = "";
			//if(iSocietyID != 0)
			//{
			//alert(iSocietyID);
			//
			
			if(LeafID == -1)
			{
				document.getElementById('PaidTo'+ iRows).value ='';
			}
			else
			{
				document.getElementById('PaidTo'+ iRows).disabled = true;
				document.getElementById('PaidTo'+ iRows).style.backgroundColor = 'lightgray';
			}
			//alert(iPaidBy);
			if(LeafID == -1)
			{
				//document.getElementById('ChequeNumber'+ iRows).value = "";
			}
			else
			{
				document.getElementById('ChequeNumber'+ iRows).disabled = true;
				document.getElementById('ChequeNumber'+ iRows).style.backgroundColor = 'lightgray';
			}
			//alert(iChequeNumber);
			if(LeafID == -1)
			{
				//document.getElementById('ChequeDate'+ iRows).value = "";
			}
			else
			{
				document.getElementById('ChequeDate'+ iRows).disabled = true;
				document.getElementById('ChequeDate'+ iRows).style.backgroundColor = 'lightgray';
			}
			//alert(ChequeDate);
			
			if(LeafID == -1)
			{
				//document.getElementById('ChequeDate'+ iRows).value = "";
			}
			else
			{
				document.getElementById('InvoiceDate'+ iRows).disabled = true;
				document.getElementById('InvoiceDate'+ iRows).style.backgroundColor = 'lightgray';
			}
			
			if(LeafID == -1)
			{
				document.getElementById('Amount'+ iRows).value = "";
			}
			else
			{
				document.getElementById('Amount'+ iRows).disabled = true;
				document.getElementById('Amount'+ iRows).style.backgroundColor = 'lightgray';
			}
			
			if(LeafID == -1)
			{
				document.getElementById('TDSAmount'+ iRows).value = "";
			}
			else
			{
				document.getElementById('TDSAmount'+ iRows).disabled = true;
				document.getElementById('TDSAmount'+ iRows).style.backgroundColor = 'lightgray';
			}
			//alert(iAmount);
			//document.getElementById('ExpenseBy'+ iRows).value = "";
			//alert(PayerBank);
			if(LeafID == -1)
			{
				document.getElementById('Comment'+ iRows).value = "";
			}
			else
			{
				document.getElementById('Comment'+ iRows).disabled = true;
				document.getElementById('Comment'+ iRows).style.backgroundColor = 'lightgray';
			}
			if(LeafID == -1)
			{
				if(!document.getElementById('ExpenseTo'+ iRows).disabled)
				{
					document.getElementById('ExpenseTo'+ iRows).value = '';
				}
			}
			else
			{
				document.getElementById('ExpenseTo'+ iRows).disabled = true;
				document.getElementById('ExpenseTo'+ iRows).style.backgroundColor = 'lightgray';
			}
			
			document.getElementById('DE' + iRows).disabled=true;
			//document.getElementById(iRows).checked = false;
		}
	}
	
	var objData = {'data' : JSON.stringify(arRowsToSubmit), "update" : 'update'}; 
	//showLoader();
	$.ajax({
			url : "ajax/ajaxPaymentDetails.php",
			type : "POST",
			data: objData ,
			success : function(data)
			{	
				alert('Data Submitted Successfully');
 				if(LeafID == -1 && iSubmittedData > 0)
				{
					location.reload(true);
				}
				location.reload(true);
				//hideLoader();
			},
				
			fail: function()
			{
				//hideLoader();
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				//hideLoader();
			}
		});//remoteCallNew(sURL, objData, 'dbUpdated');	
	//if(LeafID == -1)
	//{
	//location.reload(true);
	//}
	//window.location.href = "PaymentDetails.php?bankid="+PayerBank;
}

function EditChequeDetails(RowCounter)
{

	//alert("EditChequeDetails");
	//alert(RowCounter);
	var VoucherDate = document.getElementById('VoucherDate').value;
	var PayerBank = document.getElementById('PayerBank').value;
	var LeafID = document.getElementById('LeafID').value;
	var arRowsToSubmit = [];
	
	var iChequeNumber = 0;
	if(LeafID != -1)
	{
		iChequeNumber = document.getElementById('ChequeNumber'+ RowCounter).value;
	}
	else
	{
		iChequeNumber = '-1';
	}
	//alert(iChequeNumber);
	var ChequeDate = 0;
	if(LeafID != -1)
	{
		ChequeDate = document.getElementById('ChequeDate'+ RowCounter).value;
	}
	else
	{
		ChequeDate = -1;
	}
	
	var chkState = document.getElementById('DE' + RowCounter).checked;
	var InvoiceDate2 = document.getElementById('InvoiceDate'+ RowCounter).value;
	var PaidTo = document.getElementById('PaidTo'+ RowCounter).value;
	//alert(PaidTo);
	var iAmount = document.getElementById('Amount'+ RowCounter).value;
	var ExpenseBy = document.getElementById('ExpenseTo'+ RowCounter).value;
	var iTDSAmount = document.getElementById('TDSAmount'+ RowCounter).value;
	var Comment = document.getElementById('Comment'+ RowCounter).value;
	
	var iCheckState = 0;
	if(chkState)
	{
		iCheckState = 1;
	}
	
		
	if(PaidTo == "" || iChequeNumber == "" || ChequeDate == "" ||  iAmount == "" || (iCheckState == 1 && ExpenseBy == 0) ||  (iCheckState == 1 && InvoiceDate2 == ""))
	{
		var EmptyField = "";
		if(iCheckState == 1 && ExpenseBy == 0)
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "ExpenseTo";
			}
			else
			{
				EmptyField += ", ExpenseTo";
			}
		}//alert(ChequeDate.length);
		
		if(PaidTo == 0)
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "PaidTo";
			}
			else
			{
				EmptyField += ", PaidTo";
			}
		}
		
		if(iChequeNumber == " ")
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "ChequeNumber";
			}
			else
			{
				EmptyField += ", ChequeNumber";
			}
		}
		
		
		if(ChequeDate == "" )
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "ChequeDate";
			}
			else
			{
				EmptyField += ", ChequeDate";
			}
		}
		
		
		if(iAmount.length == 0)
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "Amount";
			}
			else
			{
				EmptyField += ", Amount";
			}
		}
		
		if(iCheckState == 1 && InvoiceDate2 == "")
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "InvoiceDate";
			}
			else
			{
				EmptyField += ", InvoiceDate";
			}
			
		}
		
		
		
		document.getElementById('label'+ RowCounter).innerHTML = "Please enter value for " + EmptyField +".";
		document.getElementById('label'+ RowCounter).style.color = '#FF0000';
		//var strLabel = 'label'+ iRows;
		setTimeout('timeout(label'+ RowCounter +')', 5000);
		
	}
	else
	{
	var obj = "";
	obj = {"PaidTo" : PaidTo, "ChequeNumber" : iChequeNumber, "ChequeDate" : ChequeDate, "Amount" : iAmount, "PayerBank" : PayerBank, "Comments" : Comment, "VoucherDate" : ChequeDate,"ExpenseBy" : ExpenseBy,"DoubleEntry" : iCheckState,"LeafID" : LeafID,"InvoiceDate" :InvoiceDate2,"TDSAmount" :iTDSAmount};
	arRowsToSubmit.push(obj);
	
	//alert(arRowsToSubmit);
	
			
	var objData = {'data' : JSON.stringify(arRowsToSubmit), "method" : 'EditPaymentDetails'}; 
	$.ajax({
			url : "ajax/ajaxPaymentDetails.php",
			type : "POST",
			data: objData ,
			success : function(data)
			{	//alert("success function");
 			    
				document.getElementById('PaidTo'+ RowCounter).disabled = true;
				document.getElementById('PaidTo'+ RowCounter).style.backgroundColor = 'lightgray';
							
				document.getElementById('ChequeNumber'+ RowCounter).disabled = true;
				document.getElementById('ChequeNumber'+ RowCounter).style.backgroundColor = 'lightgray';
				
				document.getElementById('ChequeDate'+ RowCounter).disabled = true;
				document.getElementById('ChequeDate'+ RowCounter).style.backgroundColor = 'lightgray';
				
				document.getElementById('InvoiceDate'+ RowCounter).disabled = true;
				document.getElementById('InvoiceDate'+ RowCounter).style.backgroundColor = 'lightgray';
				
				document.getElementById('Amount'+ RowCounter).disabled = true;
				document.getElementById('Amount'+ RowCounter).style.backgroundColor = 'lightgray';
				
				document.getElementById('TDSAmount'+ RowCounter).disabled = true;
				document.getElementById('TDSAmount'+ RowCounter).style.backgroundColor = 'lightgray';
				
				document.getElementById('Comment'+ RowCounter).disabled = true;
				document.getElementById('Comment'+ RowCounter).style.backgroundColor = 'lightgray';
				
				document.getElementById('ExpenseTo'+ RowCounter).disabled = true;
				document.getElementById('ExpenseTo'+ RowCounter).style.backgroundColor = 'lightgray';
				
				document.getElementById('DE' + RowCounter).disabled=true;
				document.getElementById('Edit' + RowCounter).innerHTML="<a onClick='enableRow("+ RowCounter+");'><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a> ";    
				location.reload(true);
			
			},
				
			fail: function()
			{
				
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});
	
	}
}


function enableRow(Row)
	{
		//alert(Row);
		//document.getElementById(Row).hidden=true;
		//alert(document.getElementById('ChequeNumber'+ Row).value);
		//alert("enableRow");
		document.getElementById('ChequeNumber'+ Row).disabled = false;
		document.getElementById('ChequeNumber'+ Row).style.backgroundColor = 'white';	
		
		document.getElementById('ChequeDate'+ Row).disabled = false;
		document.getElementById('ChequeDate'+ Row).style.backgroundColor = 'white';
		
		document.getElementById('PaidTo'+ Row).disabled = false;
		document.getElementById('PaidTo'+ Row).style.backgroundColor = 'white';	
		
		document.getElementById('Amount'+ Row).disabled = false;
		document.getElementById('Amount'+ Row).style.backgroundColor = 'white';	
		
		document.getElementById('Comment'+ Row).disabled = false;
		document.getElementById('Comment'+ Row).style.backgroundColor = 'white';	
		
		if(document.getElementById('DE'+ Row).checked == true)
		{
			document.getElementById("DE" + Row).disabled = false;
			document.getElementById("DE" + Row).style.backgroundColor = 'white';
			document.getElementById("ExpenseTo" + Row).disabled = false;
			document.getElementById("ExpenseTo" + Row).style.backgroundColor = 'white';
			document.getElementById("InvoiceDate" + Row).disabled = false;
			document.getElementById("InvoiceDate" + Row).style.backgroundColor = 'white';
			document.getElementById("TDSAmount" + Row).disabled = false;
			document.getElementById("TDSAmount" + Row).style.backgroundColor = 'white';
		}
		else
		{
			document.getElementById("DE" + Row).disabled = false;
			document.getElementById("DE" + Row).style.backgroundColor = 'white';
			document.getElementById("ExpenseTo" + Row).disabled = true;
			document.getElementById("ExpenseTo" + Row).style.backgroundColor = 'lightgray';
			document.getElementById("InvoiceDate" + Row).disabled = true;
			document.getElementById("InvoiceDate" + Row).style.backgroundColor = 'lightgray';
			document.getElementById("TDSAmount" + Row).disabled = true;
			document.getElementById("TDSAmount" + Row).style.backgroundColor = 'lightgray';
		}
		
		document.getElementById('Edit' + Row).innerHTML="<button onClick='EditChequeDetails("+Row+")' id='Submit"+ Row +"'>Submit</button> ";
	
	}

function timeout(field)
{
	document.getElementById(field.id).innerHTML = "";
}