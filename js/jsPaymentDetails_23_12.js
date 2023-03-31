
var delSetFlag = false;
var newArr	= new Array();		
var isCreatedNewLedger = false;
var ExpenseByArr = new Array();
var aryChqNo = [];
var leafID;
var customleaf = 0;
var i = 1;
var counterArray = [];

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
	var isAnyChequeFill = false;
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
				var arr1	= new Array();
				var arr2	= new Array();
				var arr3	= new Array();
				var arr4	= new Array();
				var arr5 	= new Array();				
				arr1		= data.split("@@@");
				
				arr2 = arr1[1].split('^^');
				
				for(var i = 1 ; i < arr2.length ; i++)
				{					
					arr3 = arr2[i].split('@@');	
																																	
					if(arr3[1].length > 0)							
					{												
						isAnyChequeFill = true;						 
						var iCount = arr3[0];												
						var iData = arr3[1];												
						arr5 = iData.split('_//_');												
						//for(var c = 1; i < arr5.length; i++)
						{							
							arr4 = arr5[1].split('#');						
							
							if(arr2.length == 2 && LeafNo == -1)
							{									
								document.getElementById('VoucherDate').value= arr4[10];	
							}								
							document.getElementById('PaidTo'+iCount).value= arr4[0];
							document.getElementById('PaidTo'+iCount).style.backgroundColor = 'lightgray';
							document.getElementById('PaidTo'+iCount).disabled = true;
							document.getElementById('DE' + iCount).checked=false;
							
							if(arr4[8] != -1)
							{
								document.getElementById('ModeOfPayment' + iCount).value = arr4[8];
								document.getElementById('ModeOfPayment' + iCount).disabled = true;
								document.getElementById('ModeOfPayment' + iCount).style.backgroundColor = 'lightgray';
							}
							if(arr4[1] != 0)
							{									
								document.getElementById('ExpenseTo'+iCount).value=arr4[1];
								document.getElementById('ExpenseTo'+iCount).disabled = true;
								document.getElementById('DE' + iCount).checked=true;
							}
							
							document.getElementById('DE' + iCount).disabled=true;
							document.getElementById('ExpenseTo'+iCount).style.backgroundColor = 'lightgray';
							
							document.getElementById('ChequeDate'+iCount).value=arr4[2];
							document.getElementById('ChequeDate'+iCount).disabled = true;
							document.getElementById('ChequeDate'+iCount).style.backgroundColor = 'lightgray';
							
							document.getElementById('Amount'+iCount).value=arr4[3];
							document.getElementById('Amount'+iCount).disabled = true;
							document.getElementById('Amount'+iCount).style.backgroundColor = 'lightgray';
							
							document.getElementById('Comment'+iCount).value=arr4[7];
							document.getElementById('Comment'+iCount).disabled = true;
							document.getElementById('Comment'+iCount).style.backgroundColor = 'lightgray';
							
							document.getElementById('ChequeNumber'+iCount).value=arr4[6];
							document.getElementById('ChequeNumber'+iCount).disabled = true;
							document.getElementById('ChequeNumber'+iCount).style.backgroundColor = 'lightgray';
							if(arr4[4] !=0)
							{
								document.getElementById('TDSAmount'+iCount).value=arr4[4];																
							}
							if(arr4[12] != 0)
							{
								document.getElementById('InvoiceAmount'+iCount).value = arr4[12];	
							}
							document.getElementById('TDSAmount'+iCount).disabled = true;
							document.getElementById('TDSAmount'+iCount).style.backgroundColor = 'lightgray';	
														
							document.getElementById('InvoiceAmount'+iCount).disabled = true;
							document.getElementById('InvoiceAmount'+iCount).style.backgroundColor = 'lightgray';														
							
							document.getElementById('InvoiceDate'+iCount).value=arr4[5];
							document.getElementById('InvoiceDate'+iCount).disabled = true;
							document.getElementById('InvoiceDate'+iCount).style.backgroundColor = 'lightgray';
														
							document.getElementById('rowid'+iCount).value = arr4[9];
							document.getElementById('rowid'+iCount).disabled = true;
							document.getElementById('rowid'+iCount).style.backgroundColor = 'lightgray';
							
							document.getElementById('reconcileStatus'+iCount).value = arr4[13];
							document.getElementById('reconcileStatus'+iCount).disabled = true;
							document.getElementById('reconcileStatus'+iCount).style.backgroundColor = 'lightgray';
							
							document.getElementById('reconcile'+iCount).value = arr4[14];
							document.getElementById('reconcile'+iCount).disabled = true;
							document.getElementById('reconcile'+iCount).style.backgroundColor = 'lightgray';
							
							document.getElementById('return'+iCount).value = arr4[15];
							document.getElementById('return'+iCount).disabled = true;
							document.getElementById('return'+iCount).style.backgroundColor = 'lightgray';
							
							document.getElementById('reconcileDate'+iCount).value = arr4[16];
							document.getElementById('reconcileDate'+iCount).disabled = true;
							document.getElementById('reconcileDate'+iCount).style.backgroundColor = 'lightgray';
							if(arr4[11] == 1)
							{
								document.getElementById('ME'+iCount).checked = true;
							}
							document.getElementById('ME'+iCount).disabled = true;
							document.getElementById('ME'+iCount).style.backgroundColor = 'lightgray';
															
							if(arr5.length > 2)
							{
								var iChequeAmount = arr4[12] - arr4[4];														
								var counter = document.getElementById('maxrows').value - 1;	
								var isIDExist = document.getElementById('valRow'+iCount);				
    							if (isIDExist == null){	
									var valRow = "<tr id='valRow"+iCount+"'><td colspan='6'><input type='hidden' id='FinalChqAmount" + iCount+"' name='FinalChqAmount" + iCount+"' value='"+iChequeAmount+"' /></td><td id='ChequeAmount"+iCount+"' style='background-color:#F70D1A;color:#FFF;'></td><td colspan='3'></td><td id='TotalAmount"+iCount+"' style='background-color:#F70D1A;color:#FFF;'></td><td id='TotalTDS"+iCount+"' style='background-color:#F70D1A;color:#FFF;'></td><td></td></tr>";
									$(valRow).insertAfter(document.getElementById('row'+iCount));								
								}								
								for(var c = 2; c < arr5.length; c++)
								{										
								counter = counter + 1;																								
								arr4 = arr5[c].split('#');
																
								iChequeAmount = iChequeAmount + (arr4[12] - arr4[4]);	
								//<input type='text'  id='ChequeNumber" + counter+"' name='ChequeNumber" + counter+"' style='width:70px;' value="+arr4[6]+" readonly>							
								var	table = "<tr id='row"+ counter+"'><td align='center' valign='top' id='Edit"+ counter+"' style='visibility:hidden'></td>";
									table += "<td style='visibility:hidden'><select id='ModeOfPayment" + counter+"' style='width:75px;' name='ModeOfPayment" + counter+"'><option value='0'>CHEQUE</option><option value='1'>ECS</option><option value='2'>OTHER</option></select></td>";
									table += "<td style='visibility:hidden'><input type='text'  id='ChequeNumber" + counter+"' name='ChequeNumber" + counter+"' style='width:70px;' value="+arr4[6]+" onchange='updateValues("+iCount+");' readonly></td>";	
									table += "<td style='visibility:hidden'><input type='checkbox' style='width:50px;' name='ME"+counter+"' id='ME"+ counter +"' value='"+ counter +"'></td>";
									table +="<td style='visibility:hidden'><input type='text' id='ChequeDate" + counter+"' name='ChequeDate" + counter+"' style='width:70px;background-color:lightgray;' value='"+arr4[2]+"' onchange='updateValues("+iCount+");' disabled></td>";
			
			 						table += "<td><select id='PaidTo" + counter+"' style='width:200px;background-color:lightgray;display:none;' name='PaidTo" + counter+"' onChange='PaidToChanged(this);updateValues("+iCount+");'>"+newArr+" </select></td>";
			
									table += "<td><input type='text' id='Amount" + counter+"' name='Amount" + counter+"' onBlur='extractNumber(this,2,true);ValidateTotal("+iCount+");' onKeyUp='extractNumber(this,2,true);' onKeyPress='return blockNonNumbers(this, event, true, false)' style='width:90px;background-color:lightgray;display:none;' value='"+arr4[3]+"' disabled></td>";
			
									table += "<td><input type='checkbox' style='width:50px;background-color:lightgray;' name='ExpenseTo"+counter+"' id='DE"+ counter +"' value='"+ counter +"' onChange='ValueChanged(this)' disabled></td>";
									
									table +="<td><input type='text' id='InvoiceDate" + counter+"' name='InvoiceDate" + counter+"' style='width:70px;background-color:lightgray;' value='"+arr4[5]+"' disabled></td>";
									
									table += "<td><select id='ExpenseTo" + counter+"' name='ExpenseTo" + counter+"' disabled style='width:200px;background-color:lightgray;' disabled>'"+ExpenseByArr+"'</select></td>";
									
									table += "<td><input type='text' id='InvoiceAmount" + counter+"' name='InvoiceAmount" + counter+"' onBlur='extractNumber(this,2,true);ValidateTotal("+iCount+")' onKeyUp='extractNumber(this,2,true);' onKeyPress='return blockNonNumbers(this, event, true, false)' style='width:90px;background-color:lightgray;' disabled></td>";
									
									table += "<td><input type='text' id='TDSAmount" + counter+"' name='TDSAmount" + counter+"' onBlur='extractNumber(this,2,true);ValidateTotal("+iCount+");' onKeyUp='extractNumber(this,2,true);' onKeyPress='return blockNonNumbers(this, event, true, false)' style='width:90px;background-color:lightgray;' disabled></td>";						
									
									table += "<td><input type='text' id='Comment" + counter+"' name='Comment" + counter+"' value='"+arr4[7]+"' style='background-color:lightgray;' disabled></td>";
									
									table += "<td><input type='hidden' id='rowid" + counter+"' name='rowid" + counter+"' value='"+arr4[9]+"'> </td>";
			
									table += "<td><input type='hidden' id='edit' name='edit' value=''> </td>";
									
									table += "<td><input type='hidden' id='reconcileStatus" + counter+"' name='reconcileStatus" + counter+"' value='"+arr4[13]+"'> </td>";
									
									table += "<td><input type='hidden' id='reconcileDate" + counter+"' name='reconcileDate" + counter+"' value='"+arr4[16]+"'> </td>";
									
									table += "<td><input type='hidden' id='reconcile" + counter+"' name='reconcile" + counter+"' value='"+arr4[14]+"'> </td>";
									
									table += "<td><input type='hidden' id='return" + counter+"' name='return" + counter+"' value='"+arr4[15]+"'> </td>";
									
									table += "<td><input type='hidden' id='ref" + counter+"' name='ref" + counter+"' value='" +iCount+"'> </td>";
									
									table +="</tr>";										
									//table += "<tr id='RowLabel" + counter+"'><td><p id='label"+ counter +"' name='label"+ counter +"' style='color:#00FF00' readonly></p></td></tr>";
																											
									$(table).insertAfter(document.getElementById('row'+iCount));
									document.getElementById('PaidTo' + counter).value = arr4[0];
									if(arr4[1] != 0)
									{								
										document.getElementById('ExpenseTo' + counter).value = arr4[1];										
										document.getElementById('DE' + counter).checked=true;
										document.getElementById('TDSAmount' + counter).value= arr4[4];
										document.getElementById('InvoiceAmount' + counter).value = arr4[12];
									}									
								}								
								document.getElementById('maxrows').value = counter + 1;	
								iCounter = counter + 1;	
								if(document.getElementById('FinalChqAmount'+iCount))
								{
									document.getElementById('FinalChqAmount'+iCount).value = iChequeAmount;
								}																															
							}
						}
					}
					else
					{
						
						var iCount = arr3[0];	
						//alert(iCount + '||'+ arr3[1].length);
						document.getElementById('Edit' + iCount).style.visibility = "hidden";							
						document.getElementById('ExpenseTo' + iCount).disabled = true;
						document.getElementById('ExpenseTo'+iCount).style.backgroundColor = 'lightgray';
						document.getElementById('InvoiceDate' + iCount).disabled = true;
						document.getElementById('InvoiceDate'+iCount).style.backgroundColor = 'lightgray';
						document.getElementById('TDSAmount' + iCount).disabled = true;
						document.getElementById('TDSAmount' + iCount).style.backgroundColor = 'lightgray';
						document.getElementById('InvoiceAmount' + iCount).disabled = true;
						document.getElementById('InvoiceAmount' + iCount).style.backgroundColor = 'lightgray';
					}					
				}
				
				if(isAnyChequeFill == false)
				{
					document.getElementById("lblEdit").style.visibility = "hidden";		
				}
				
				if(delSetFlag)
				{
					//alert(arr4[9]);
					getPaymentDetails('delete-' + arr4[9]);
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
		delSetFlag = false;
		var conf = confirm("Are you sure , you want to delete it ?");
		if(conf==1)
		{
			/*$(document).ready(function()
			{
				$("#error").fadeIn("slow");*/
				document.getElementById("error").innerHTML = "Please Wait...";
			//});

			remoteCall("ajax/ajaxPaymentDetails.php","form=PaymentDetails&method="+iden[0]+"&PaymentDetailsId="+iden[1],"loadchanges");
			
		}
	}
	else if(iden[0]=="print")
	{
		var pId = iden[1];		
		$.ajax({
			url: 'ajax/ajaxPaymentDetails.php',
        	type: 'POST',
        	data: {"pId": pId, "method":"FetchVoucher"},
        	success: function(data){
            fetchdata = data.split("@@@");
			VoucherData = fetchdata[1].split("#");				
			window.open('print_voucher.php?&vno=' + VoucherData[0] + '&type=' + VoucherData[1]);
			}
		})
	}
	else
	{
		/*$(document).ready(function()
		{
			$("#error").fadeIn("slow");*/
			document.getElementById("error").innerHTML = "Please Wait...";
		//});		
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
		/*$(document).ready(function()
		{
			$("#error").fadeIn("slow");*/
			document.getElementById("error").innerHTML = "Please Wait...";
		//});
var maxRowCounter=document.getElementById('maxrows').value;
		//alert(maxRowCounter);
		//alert(arr2);
	for(var row=2;row < maxRowCounter;row++)
		{
			document.getElementById('PaidTo' + row).style.display = 'none';
			document.getElementById('Amount' + row).style.display = 'none';
			//document.getElementById('ExpenseTo' + row).style.display = 'none';
			//document.getElementById('InvoiceDate' + row).style.display = 'none';
			//document.getElementById('TDSAmount' + row).style.display = 'none';
			//document.getElementById('DE' + row).style.display = 'none';
			document.getElementById('Comment' + row).style.display = 'none';
			
			document.getElementById('CashRow' + row).style.display = 'none';
				document.getElementById('CashRowLabel' + row).style.display = 'none'; 
		}
		
		document.getElementById('VoucherDate1').value = arr2[4];
		document.getElementById('Amount1').value=arr2[5];
		document.getElementById('Amount1').focus();
		document.getElementById('PaidTo1').value=arr2[6];
		//document.getElementById('ExpenseTo1').value=arr2[7];
		document.getElementById('PayerBank').value=arr2[10];
		document.getElementById('Comment1').value=arr2[12];
		//document.getElementById('InvoiceDate1').value=arr2[13];
		//document.getElementById('TDSAmount1').value=arr2[14];
		//if(arr2[13] !='')		
		
		if(arr2[13] != "0000-00-00")
		{
			
			//document.getElementById('DE1').checked=true;
			//document.getElementById('ExpenseTo1').disabled = false;
		}
		else
		{
			//document.getElementById('DE1').checked=false;
			//document.getElementById('ExpenseTo1').disabled = false;
		}
		//alert(arr2[0]);
		document.getElementById('Submit').innerHTML="Update";
		document.getElementById('Submit').onclick=function(){ UpdateCashPaymentDetails(arr2[0],arr2[4],minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate)};
		document.getElementById('SubmitUp').innerHTML="Update";
		document.getElementById('SubmitUp').onclick=function(){ UpdateCashPaymentDetails(arr2[0],arr2[4],minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate)};
		
		if(delSetFlag)
		{
			getPaymentDetails('delete-' + arr2[0]);
		}
	}
	else if(arr1[0] == "delete")
	{
		/*$(document).ready(function()
		{
			$("#error").fadeIn("slow");*/
			document.getElementById("error").innerHTML = "Please Wait...";
		//});
		//alert("test");
		var BankID=document.getElementById('BankID').value;
		var LeafID=document.getElementById('LeafID').value;
		var CustomLeaf=document.getElementById('CustomLeaf').value;
//alert("PaymentDetails.php?bankid=" + BankID + "&LeafID=" + LeafID);
		var out = document.getElementById('edit').value;
					
		if(out != "")
		{
			localStorage.setItem('refreshPage', "1");
			window.close();				
		}
		else
		{
			window.location.href ="PaymentDetails.php?bankid=" + BankID + "&LeafID=" + LeafID + "&CustomLeaf=" + CustomLeaf;	
		}
		
	}
	document.getElementById("error").innerHTML = "";
}


function UpdateCashPaymentDetails(CashDetailsId,VoucherDate,minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate)
{
	
	document.getElementById('Submit').disabled = true;
	document.getElementById('SubmitUp').disabled = true;
	
	var VoucherDate = document.getElementById('VoucherDate1').value;
 	var BankID = document.getElementById('BankID').value;
	var PayerBank = document.getElementById('PayerBank').value;
	var LeafID = document.getElementById('LeafID').value;
	var arRowsToSubmit = [];
	var iSubmittedData = 0;
	var PaidTo = document.getElementById('PaidTo1').value;
	var iChequeNumber = '-1';
	var ChequeDate = -1;
	var iAmount = document.getElementById('Amount1').value;
	var ExpenseBy = 0;
	var chkState = false;
	var VoucherDateFlag = "";
	var InvoiceDate2 = "";
	var iTDSAmount = 0;
	var Comment = document.getElementById('Comment1').value;
	var iCheckState = 0;
	
	if(chkState)
	{
		iCheckState = 1;
	}
	
	if(LeafID == -1)
	{
		if(jsdateValidator('VoucherDate1', VoucherDate , minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate) == true)
		{
			VoucherDateFlag = true;	
		}
		else
		{
			VoucherDateFlag = false;		
		}
		
	}
	
	if(PaidTo == 0 || PaidTo == -1 || iChequeNumber == "" || ChequeDate == "" ||  iAmount == "" || (iCheckState == 1 && ExpenseBy == 0) ||  (iCheckState == 1 && InvoiceDate2 == "") || VoucherDateFlag == false)
	{
		
		document.getElementById('Submit').disabled = false;
		document.getElementById('SubmitUp').disabled = false;
		
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
		if(LeafID == -1 && VoucherDateFlag == false)
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "VoucherDate";
			}
			else
			{
				EmptyField += ",VoucherDate";
			}
			
		}
		
		if(PaidTo == 0 ||  PaidTo == -1)
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "PaidTo";
			}
			else
			{
				EmptyField += ",PaidTo";
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
			//document.getElementById('TDSAmount1').value = "";
			document.getElementById('Comment1').value = "";
			//document.getElementById('DE1').disabled=true;
			//document.getElementById('ExpenseTo1').value = '';
			//document.getElementById('InvoiceDate1').value = '';
		}
	var objData = {'data' : JSON.stringify(arRowsToSubmit), "method" : 'UpdateCashPaymentDetails',"CashDetailsId" : CashDetailsId}; 
	showLoader();	
	$.ajax({
			url : "ajax/ajaxPaymentDetails.php",
			type : "POST",
			data: objData ,
			success : function(data)
			{	
				alert('Data Submitted Successfully');
				if(LeafID == -1)
				{
					var out = document.getElementById('edit').value;
					
					if(out != "")
					{
						localStorage.setItem('refreshPage', "1");
						window.close();				
					}
					else
					{
						location.reload(true);
					}
				}
				
			},
				
			fail: function()
			{
				hideLoader();
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				hideLoader();
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
function SubmitChequeDetails(minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate)
{	
	document.getElementById('Submit').disabled = true;
	document.getElementById('SubmitUp').disabled = true;
	
	var VoucherDate; 
	//alert('test');
	//`ChequeDate`,`ChequeNumber`,`Amount`,`PaidTo`,`PayerBank`,`Comments`,`VoucherDate
	var iRowCount = document.getElementById('maxrows').value;	
	
	//alert(VoucherDate);
	//var BankID = document.getElementById('BankID').value;
	var PayerBank = document.getElementById('PayerBank').value;
	var LeafID = document.getElementById('LeafID').value;
	
	var ExpenseBy = 0;
	//alert(DepositID);
	var InvoiceDate2;
	var iTDSAmount;
	var arRowsToSubmit = [];
	var iSubmittedData = 0; 
	var DateFlag = true;
	var VoucherDateFlag = true;
	var IsInvoiceDateValid = true;
	var isReadyToSubmit = false;
	var iInvoiceAmount;
	for(var iRows = 1 ; iRows < iRowCount; iRows++)
	{
		var str = 'PaidTo'+ iRows;
		//alert(str);
		var iSocietyID = document.getElementById('PaidTo'+ iRows).value;
				
		//if(iSocietyID != 0 && iSocietyID != -1 && LeafID != "")
		if(iSocietyID != 0 && LeafID != "")
		{
			if(LeafID == -1)
			{
				VoucherDate = document.getElementById('VoucherDate'+ iRows).value;
			}
			
			var PaidTo = document.getElementById('PaidTo'+ iRows).value;
			var LeafID = document.getElementById('LeafID').value;
			var rowid = document.getElementById('rowid'+ iRows).value;
			
			//alert(LeafID);
			//alert(iPaidBy);
			if(rowid > 0)
			{
				continue;	
			}
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
			
			var chkState = false;
			var ref = 0;
			var isMEChecked = false;
			if(LeafID != -1)
			{
				chkState = document.getElementById('DE' + iRows).checked;
				ExpenseBy = document.getElementById('ExpenseTo'+ iRows).value;
				InvoiceDate2 = document.getElementById('InvoiceDate'+ iRows).value;
				iTDSAmount = document.getElementById('TDSAmount'+ iRows).value;
				iInvoiceAmount = document.getElementById('InvoiceAmount'+ iRows).value;	
				ref = document.getElementById('ref'+ iRows).value;
				isMEChecked = document.getElementById('ME'+ ref).checked;			
			}
			
			var iModeOfPayment = 0;
			//var iCustomLeaf = document.getElementById('CustomLeaf').value;
			
			var iCheckState = 0;
			if(chkState)
			{
				iCheckState = 1;
			}
			else
			{
				iTDSAmount=0;
				ExpenseBy =0;
				InvoiceDate2="00-00-0000";
				iInvoiceAmount=0;
				
			}
			//alert('chk state');
			//alert(ExpenseBy);
			
			if(LeafID != -1 && ChequeDate != "" &&  PaidTo > 0)
			{
				if(jsdateValidator('ChequeDate'+ iRows , ChequeDate , minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate) == true)
				{
					//return true;
					DateFlag = true;	
				}
				else
				{
					
					DateFlag = false;		
				}
				
			}
			
			if(iCheckState == 1)
			{
				
				if(jsdateValidator('InvoiceDate'+ iRows , InvoiceDate2 , minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate) == true)
				{
					IsInvoiceDateValid = true;			
				}
				else
				{
					IsInvoiceDateValid = false;
				}
			}
			
			if(LeafID == -1)
			{
				if(jsdateValidator('VoucherDate'+ iRows, VoucherDate , minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate) == true)
				{
					//return true;
					VoucherDateFlag = true;	
				}
				else
				{
					VoucherDateFlag = false;		
				}
				
			}
			
			var isInvoiceAmountValid = true;
			if(iCheckState == 1 && isMEChecked == false && iInvoiceAmount != (parseInt(iAmount) + parseInt(iTDSAmount = iTDSAmount ? iTDSAmount : 0)))
			{
				isInvoiceAmountValid = false;
			}
			
			var iChequeAmount = 0;
			if(document.getElementById('FinalChqAmount'+iRows))
			{
				iChequeAmount = document.getElementById('FinalChqAmount'+iRows).value;
			}								
			
			if(PaidTo == 0 || PaidTo == -1 || iChequeNumber == "" || ChequeDate == "" ||  iAmount == "" || (iCheckState == 1 && (ExpenseBy == 0 || ExpenseBy == -1)) ||  (iCheckState == 1 && InvoiceDate2 == "") || IsInvoiceDateValid == false || DateFlag == false || (LeafID == -1 && VoucherDateFlag == false) || (iChequeAmount > 0 && iChequeAmount != iAmount) || (iCheckState == 1 && iInvoiceAmount == "") || (isMEChecked == true && iCheckState != 1) || isInvoiceAmountValid == false)
			{																
				document.getElementById('Submit').disabled = false;
				document.getElementById('SubmitUp').disabled = false;
				
				if(iChequeAmount > 0 && iChequeAmount != iAmount)
				{					
					document.getElementById('label'+ iRows).innerHTML = "Cheque amounts does not match.";
					document.getElementById('label'+ iRows).style.color = '#FF0000';
					setTimeout('timeout(label'+ iRows +')', 5000);
					return;
				}
				
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
				//alert(document.getElementById('ModeOfPayment'+ iRows).value);
				if(( iChequeNumber.length == 0 && LeafID != -1 ) ||  ( iChequeNumber.length == 0 && LeafID != -1 && document.getElementById('ModeOfPayment'+ iRows).value != 2 ))
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
				
				if(( ChequeDate.length == 0 && LeafID != -1 ) || ( DateFlag == false && LeafID != -1 ) )
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
				
				if(( iCheckState == 1 && InvoiceDate2 == "" ) || ( IsInvoiceDateValid == false && LeafID != -1 ) )
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
				
				
				
				
				
				if(LeafID == -1 && VoucherDateFlag == false)
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "VoucherDate";
					}
					else
					{
						EmptyField += ",VoucherDate";
					}
					
				}
				
				if(PaidTo == 0 ||  PaidTo == -1)
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "PaidTo";
					}
					else
					{
						EmptyField += ",PaidTo";
					}
					
				}
				
				if(iCheckState == 1 && iInvoiceAmount == "")
				{
					if(EmptyField.length == 0)
					{
						EmptyField = "Invoice amount";
					}
					else
					{
						EmptyField += ",Invoice amount";
					}
				}
				
				if(EmptyField.length != 0)
				{		
					document.getElementById('label'+ iRows).innerHTML = "Please enter valid value for " + EmptyField +".";
					document.getElementById('label'+ iRows).style.color = '#FF0000';
					//var strLabel = 'label'+ iRows;
					setTimeout('timeout(label'+ iRows +')', 5000);
					return;	
				}
				
				if(isMEChecked == true && iCheckState != 1)
				{
					document.getElementById('label'+ iRows).innerHTML = "Multiple entry must be double entry";
					document.getElementById('label'+ iRows).style.color = '#FF0000';
					setTimeout('timeout(label'+ iRows +')', 5000);
					return;	
				}
				
				if(isInvoiceAmountValid == false)
				{
					document.getElementById('label'+ iRows).innerHTML = "Invalid invoice amount";
					document.getElementById('label'+ iRows).style.color = '#FF0000';
					setTimeout('timeout(label'+ iRows +')', 5000);
					return;
				}
								
				if(document.getElementById('ModeOfPayment'+ iRows).value != 2)
				{												
					continue;
				}
				else if(document.getElementById('ModeOfPayment'+ iRows).value == 2 && ( ChequeDate == "" ||  iAmount == "" || (iCheckState == 1 && ExpenseBy == 0) ||  (iCheckState == 1 && InvoiceDate2 == "")))
				{					
					continue;
				}
			}			
			//alert(PayerBank);
			var Comment = document.getElementById('Comment'+ iRows).value;
			if(LeafID != -1)
			{
			var isMultipleEntry = document.getElementById('ME'+ iRows).checked;
			}
			//alert(PayerChequeBranch);
			
			//alert(ChequeDate);
			var obj = "";
			if(LeafID != -1)
			{
				iModeOfPayment = document.getElementById('ModeOfPayment'+ iRows).value;								
				obj = {"SocietyID" : iSocietyID, "PaidTo" : PaidTo, "ChequeNumber" : iChequeNumber, "ChequeDate" : ChequeDate, "Amount" : iAmount, "PayerBank" : PayerBank, "Comments" : Comment, "VoucherDate" : ChequeDate,"ExpenseBy" : ExpenseBy,"DoubleEntry" : iCheckState, "update" : 'update',
			"LeafID" : LeafID,"InvoiceDate" :InvoiceDate2,"TDSAmount" :iTDSAmount,"ModeOfPayment" : iModeOfPayment, "MultipleEntry":isMultipleEntry, "Ref":ref, "InvoiceAmount":iInvoiceAmount};
			}
			else
			{				
				obj = {'Row' : iRows,"SocietyID" : iSocietyID, "PaidTo" : PaidTo, "ChequeNumber" : iChequeNumber, "ChequeDate" : "0000-00-00", "Amount" : iAmount, "PayerBank" : PayerBank, "Comments" : Comment, "VoucherDate" : VoucherDate,"ExpenseBy" : ExpenseBy,"DoubleEntry" : iCheckState,
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
			
			
			
			
			//document.getElementById(iRows).checked = false;
		}
		
		
	}
	isReadyToSubmit = true;
	
		for(var iRows = 1 ; iRows < iRowCount; iRows++)
		{
			
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
				//document.getElementById('TDSAmount'+ iRows).value = "";
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
				/*if(!document.getElementById('ExpenseTo'+ iRows).disabled)
				{
					document.getElementById('ExpenseTo'+ iRows).value = '';
				}*/
			}
			else
			{
				document.getElementById('ExpenseTo'+ iRows).disabled = true;
				document.getElementById('ExpenseTo'+ iRows).style.backgroundColor = 'lightgray';
				document.getElementById('DE' + iRows).disabled=true;
			}	
		}
	if(isReadyToSubmit)
	{
		var objData = {'data' : JSON.stringify(arRowsToSubmit), "update" : 'update'}; 
		showLoader();
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
					
				},
					
				fail: function()
				{
					hideLoader();
				},
				
				error: function(XMLHttpRequest, textStatus, errorThrown) 
				{
					hideLoader();
				}
			});//remoteCallNew(sURL, objData, 'dbUpdated');	
		//if(LeafID == -1)
		//{
		//location.reload(true);
		//}
		//window.location.href = "PaymentDetails.php?bankid="+PayerBank;
	}
}

function EditChequeDetails(RowCounter,minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate)
{	
	var mainRow = RowCounter;		
	counterArray = [];
	counterArray.push(RowCounter);	
	createCouterArray(RowCounter);		
	document.getElementById('Submit').disabled = true;
	document.getElementById('SubmitUp').disabled = true;	
	document.getElementById('Submit' + RowCounter).disabled = true;	
	 	
	var arRowsToSubmit = [];				
	for(var c = 0; c < counterArray.length; c++)
	{
		RowCounter = counterArray[c];
		var DateFlag = "";
		var IsInvoiceDateValid = true;
		if(LeafID == -1)
		{
			var VoucherDate = document.getElementById('VoucherDate').value;
		}
		var PayerBank = document.getElementById('PayerBank').value;
		var LeafID = document.getElementById('LeafID').value;		
		var iModeOfPayment = 0;
		
		var iChequeNumber = 0;
		if(LeafID != -1)
		{
			iChequeNumber = document.getElementById('ChequeNumber'+ RowCounter).value;
			iModeOfPayment = document.getElementById('ModeOfPayment'+ RowCounter).value;
		}
		else
		{
			iChequeNumber = '-1';
		}
		//alert(iChequeNumber);
		var ChequeDate = 0;
		var isMultipleEntry = 0;
		var ref = 0;
		var isMEChecked = 0;
		if(LeafID != -1)
		{
			ChequeDate = document.getElementById('ChequeDate'+ RowCounter).value;	
			isMultipleEntry = document.getElementById('ME'+ RowCounter).checked;	
			ref = document.getElementById('ref'+ RowCounter).value;	
			isMEChecked = document.getElementById('ME'+ ref).checked;
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
		var rowid = document.getElementById('rowid'+ RowCounter).value;
		var reconcileDate = document.getElementById('reconcileDate'+ RowCounter).value;
		var reconcileStatus = document.getElementById('reconcileStatus'+ RowCounter).value;
		var reconcile = document.getElementById('reconcile'+ RowCounter).value;
		var returnFlag = document.getElementById('return'+ RowCounter).value;
		var iInvoiceAmount = document.getElementById('InvoiceAmount'+ RowCounter).value;	
		
		var iChequeAmount;			
		if(document.getElementById('FinalChqAmount'+mainRow))
		{
			iChequeAmount = document.getElementById('FinalChqAmount'+mainRow).value;	
			iChequeAmount = parseFloat(iChequeAmount);					
			if((iChequeAmount > 0 || document.getElementById('Amount'+ mainRow).value > 0) && (iChequeAmount != document.getElementById('Amount'+ mainRow).value))
			{				
				document.getElementById('label'+ mainRow).innerHTML = "Cheque amount does not match";
				document.getElementById('label'+ mainRow).style.color = '#FF0000';
				setTimeout('timeout(label'+ mainRow +')', 5000);
				document.getElementById('Submit' + mainRow).disabled = false;
				return;
			}
		}
		
		var iCheckState = 0;
		if(chkState)
		{
			iCheckState = 1;
		}
		else
		{
			ExpenseBy = 0;
			iTDSAmount = 0;
			InvoiceDate2="0000-00-00";
			iInvoiceAmount = 0;
		}
		
		if(LeafID != -1)
		{
			if(jsdateValidator('ChequeDate'+ RowCounter , ChequeDate , minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate) == true)
			{
				//return true;
				DateFlag = true;	
			}
			else
			{
				DateFlag = false;		
			}
			
		}
		
		
		if(iCheckState == 1 && InvoiceDate2.length > 0)
		{
			
			if(jsdateValidator('InvoiceDate'+ RowCounter , InvoiceDate2 , minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate) == true)
			{
				IsInvoiceDateValid = true;			
			}
			else
			{
				IsInvoiceDateValid = false;
			}
		}
		
		var isInvoiceAmountValid = true;
		if(iCheckState == 1 && isMEChecked == false && iInvoiceAmount != (parseInt(iAmount) + parseInt(iTDSAmount = iTDSAmount ? iTDSAmount : 0)))
		{
			isInvoiceAmountValid = false;
		}
				
	if(PaidTo == 0 || PaidTo == -1 || (iModeOfPayment != 2 && iChequeNumber == "") || ChequeDate == "" ||  iAmount == "" || (iCheckState == 1 && (ExpenseBy == 0 || ExpenseBy == -1)) ||  (iCheckState == 1 && InvoiceDate2 == "") || IsInvoiceDateValid == false || DateFlag == false || (iCheckState == 1 && iInvoiceAmount == "") || (isMEChecked == true && iCheckState != 1) || isInvoiceAmountValid == false)
	{
		
		document.getElementById('Submit').disabled = false;
		document.getElementById('SubmitUp').disabled = false;
		document.getElementById('Submit' + mainRow).disabled = false;
		
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
		
		if(PaidTo == 0 || PaidTo == -1)
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
		
		
		if(ChequeDate == ""  || DateFlag == false)
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
		
		if((iCheckState == 1 && InvoiceDate2 == "") || IsInvoiceDateValid == false)
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
		
		if(iCheckState == 1 && iInvoiceAmount == "")
		{
			if(EmptyField.length == 0)
			{
				EmptyField = "Invoice amount";
			}
			else
			{
				EmptyField += ",Invoice amount";
			}
		}
		
		document.getElementById('label'+ mainRow).innerHTML = "Please enter valid value for " + EmptyField +".";
		if(isMEChecked == true && iCheckState != 1)
		{
			document.getElementById('label'+ mainRow).innerHTML = "Multiple entry must be double entry";
		}
		if(isInvoiceAmountValid == false)
		{
			document.getElementById('label'+ mainRow).innerHTML = "Invalid invoice amount.";
		}
		document.getElementById('label'+ mainRow).style.color = '#FF0000';
		//var strLabel = 'label'+ iRows;
		setTimeout('timeout(label'+ mainRow +')', 5000);
		return;
	}
	else
	{	
		if(iCheckState != 1)
		{
			InvoiceDate2 = "";
		}
	var obj = ""; 	 
			obj = {"PaidTo" : PaidTo, "ChequeNumber" : iChequeNumber, "ChequeDate" : ChequeDate, "Amount" : iAmount, "PayerBank" : PayerBank, "Comments" : Comment, "VoucherDate" : ChequeDate,"ExpenseBy" : ExpenseBy,"DoubleEntry" : iCheckState,"LeafID" : LeafID,"InvoiceDate" :InvoiceDate2,"TDSAmount" :iTDSAmount, "ModeOfPayment" :iModeOfPayment, "RowID" :rowid, "ReconcileDate" :reconcileDate, "ReconcileStatus" :reconcileStatus, "Reconcile" :reconcile, "ReturnFlag" :returnFlag, "MultipleEntry":isMultipleEntry, "Ref":ref, "InvoiceAmount":iInvoiceAmount };
			arRowsToSubmit.push(obj);
			
			//alert(arRowsToSubmit);
		}
	}
			
	showLoader();		
	var objData = {'data' : JSON.stringify(arRowsToSubmit), "method" : 'EditPaymentDetails'}; 
	$.ajax({
			url : "ajax/ajaxPaymentDetails.php",
			type : "POST",
			data: objData ,
			success : function(data)
			{	
				var arr = Array();
				arr		= data.split("@@@");
							
				if(arr[1]==true || arr[1] == '11')
				{
					alert("Record Updated Successfully...");
					var out = document.getElementById('edit').value;
					
					if(out != "")
					{
						localStorage.setItem('refreshPage', "1");
						window.close();				
					}
				}
				else
				{
					alert("Record Not Updated..");	
				}
 			    
				for(var c = 0; c < counterArray.length; c++)
				{
					RowCounter = counterArray[c];
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
				}
				location.reload(true);				
			
			},
				
			fail: function()
			{
				hideLoader();	
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				hideLoader();	
			}
		});
	
	//}
}


function enableRow(Row)
	{
		//alert(Row);
		//document.getElementById(Row).hidden=true;	
		document.getElementById('Submit').style.display = "none";
		document.getElementById('SubmitUp').style.display = "none";
			
		if(document.getElementById('reconcileStatus' + Row).value == 0)
		{		
			document.getElementById('ChequeNumber'+ Row).disabled = false;
			document.getElementById('ChequeNumber'+ Row).style.backgroundColor = 'white';	
			
			document.getElementById('ChequeDate'+ Row).disabled = false;
			document.getElementById('ChequeDate'+ Row).style.backgroundColor = 'white';
			
			document.getElementById('Amount'+ Row).disabled = false;
			document.getElementById('Amount'+ Row).style.backgroundColor = 'white';	
		}
		
		document.getElementById('PaidTo'+ Row).disabled = false;
		document.getElementById('PaidTo'+ Row).style.backgroundColor = 'white';					
		
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
			document.getElementById("InvoiceAmount" + Row).disabled = false;
			document.getElementById("InvoiceAmount" + Row).style.backgroundColor = 'white';			
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
			
		if(document.getElementById('ME' + Row).checked == false)
		{
			document.getElementById('ModeOfPayment' + Row).disabled = false;
			document.getElementById('ModeOfPayment' + Row).style.backgroundColor = 'white';	
			if(document.getElementById('ModeOfPayment' + Row).value == 0)
			{
				document.getElementById('ME' + Row).disabled = false;
				document.getElementById('ME' + Row).style.backgroundColor = 'white';
			}
		}
		else
		{
			document.getElementById('add' + Row).style.display = 'block';
			document.getElementById('add' + Row).onclick = function () {AddRow(Row);};
		}
		//document.getElementById('Edit' + Row).innerHTML="<button onClick='EditChequeDetails("+ Row +","+ minGlobalCurrentYearStartDate +" , "+ maxGlobalCurrentYearEndDate +" )' id='Submit"+ Row +"'>Submit</button> ";
		document.getElementById('Edit' + Row).innerHTML="<button  id='Submit"+ Row +"'>Submit</button> ";
		document.getElementById('Submit'+ Row).onclick = function () { EditChequeDetails(Row ,minGlobalCurrentYearStartDate ,maxGlobalCurrentYearEndDate); };				
		var row =document.getElementById('row'+Row);		
		var val = $(row).closest('tr').next('tr').attr('id');		
		if(val)
		{
			var id = val.substring(3);		
			if(document.getElementById('ref'+id) != null)
			{
				if(document.getElementById('ref'+id).value == document.getElementById('ref'+Row).value)
				{		
					enableRow(id);
				}
			}
		}		
	}

function timeout(field)
{
	document.getElementById(field.id).innerHTML = "";
}

function setLedArray(flag)
{			
	$.ajax({
			url : "ajax/ajaxPaymentDetails.php",
			type : "POST",
			data: {"method" : "fetchLedgers"} ,
			success : function(data)
			{	
				getArray(data,flag);
			}
		});																			
}

function getArray(data,flag)
{	
	var a1		= data;
	var newArr1	= new Array();																
	newArr1		= a1.split("@@@");	
	newArr = newArr1[1];
	if(flag == true)
	{
		var obj = {"method" : "fetchExpenseBy"};
		remoteCallNew("ajax/ajaxPaymentDetails.php", obj, 'getExpenseByArr');
	}
}

function getExpenseByArr()
{
	var a		= sResponse.trim();
	var newArr	= new Array();																
	newArr		= a.split("@@@");	
	ExpenseByArr = newArr[1];		
	getExistingData(aryChqNo, leafID, customleaf);	
}

function createCouterArray(cnt)
{					
	var row =document.getElementById('row'+cnt);		
	var val = $(row).closest('tr').next('tr').attr('id');
	if(val == null || val == "RowLabel"+cnt)
	{
		val = $(row).closest('tr').next('tr').next('tr').attr('id');
	}
		
	if(val)
	{
		var id = val.substring(3);				
		if(document.getElementById('ref'+id) != null)
		{			
			if(document.getElementById('ref'+id).value == document.getElementById('ref'+cnt).value)
			{			
				counterArray.push(id);
				createCouterArray(id);
			}
		}
	}	
}


function hideEditBtn()
{
	var EditBtnList = document.getElementsByClassName('btnEdit'), i;

	for (var i = 0; i < EditBtnList.length; i ++) {
		EditBtnList[i].style.display = 'none';
	}	
	document.getElementById('lblEdit').style.display = 'none';
}