// JavaScript Document
var bIsSendEmail = false;
function jsBillUpdate(HeaderAndAmount,NewRowCounter)
{
	var obj = [];
	var CurrentValue =0;
	var PreviousValue =0;
	var arDataToSubmit = [];
	var HeaderID="";
	for(var i=0; i < HeaderAndAmount.length;i++)
	{
		arr23 = HeaderAndAmount[i].split("@@@");
		HeaderID = arr23[1].toString();
		if(document.getElementById("txtInterestOnArrears").value != HeaderID)
		{ 
			VoucherID = arr23[0].toString();
			Taxable = arr23[2].toString();
			//alert(arr23[0]);
			CurrentValue = document.getElementById(arr23[0]).value;
			PreviousValue = document.getElementById(arr23[0]).defaultValue;
			obj = {'Head' : HeaderID , 'Amt' : CurrentValue , 'HeadOldValue' : PreviousValue, 'VoucherID' : VoucherID, 'Taxable' : Taxable};
			arDataToSubmit.push(obj);
		}
	}
	
	if(NewRowCounter >0)
	{
		for(var m=1;m <= NewRowCounter; m++)
		{
			HeaderID = document.getElementById('particular'+m).value;
			CurrentValue = document.getElementById('HeaderAmount'+m).value;
			PreviousValue=0;
			VoucherID=0
			if(HeaderID !=0 && CurrentValue !=0)
			{
				obj = {'Head' : HeaderID ,'Amt' : CurrentValue ,'HeadOldValue' : PreviousValue,'VoucherID' : VoucherID};
				arDataToSubmit.push(obj);
				//print obj array 
				/*for (var key in obj) 
				{
					alert(obj[key]);
				}*/
			}
		}
	}
	var IsSuppBill = document.getElementById('IsSupplementaryBill').value;
	//if(IsSuppBill == 0)
	{
		var InterestOnPrincipleDue = document.getElementById('InterestOnPrincipleDue').value;
		var IntrestOnPreviousarrears = document.getElementById('IntrestOnPreviousarrears').value;
		var PrinciplePreviousArrears = document.getElementById('PrinciplePreviousArrears').value;
		var AdjustmentCredit = document.getElementById('AdjustmentCredit').value;
		var UnitID = document.getElementById('UnitID').value;
		var PeriodID = document.getElementById('PeriodID').value;
		var objData = {'data' : JSON.stringify(arDataToSubmit), "method" : 'BillEdit',"UnitID" : UnitID,"PeriodID" : PeriodID,"InterestOnPrincipleDue" : InterestOnPrincipleDue,"IntrestOnPreviousarrears" : IntrestOnPreviousarrears,"PrinciplePreviousArrears" : PrinciplePreviousArrears,"AdjustmentCredit":AdjustmentCredit,"SupplementaryBill" : IsSuppBill}; 
		$.ajax({
				url : "ajax/ajaxgenbill.php",
				type : "POST",
				data: objData ,
				success : function(data)
				{	
					alert('Bill Updated Successfully');
					//location.reload(true);
					window.location.href = "Maintenance_bill.php?UnitID="+ UnitID + '&PeriodID='+ PeriodID+ '&BT='+IsSuppBill;
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




function SubmitBillRows()
{
	alert("inside SubmitBillRows");
}


function sendEmail(unitID,periodID,bGeneratePdf,sEmail,billType)
{
	showLoader();

	document.getElementById('send_email').disabled = true;
	document.getElementById('status').style.visibility = 'visible';
	document.getElementById('status').innerHTML = 'Sending ...';

	if(billType == null)
	{
		console.log("No bill type as been set.");
		billType = 0;
	}
	
	//generating pdf for maintenance_bill
	if(bGeneratePdf == true)
	{
		bIsSendEmail = true;
		ViewPDF(unitID,periodID);
	}
	else
	{
		bIsSendEmail = false;
		SentEmailManually = 1;
		$.ajax(
		{
				url : "classes/email.class.php",
				type : "POST",
				data: { "unit":unitID, "period":periodID, "SentEmailManually":SentEmailManually, "BT" : billType} ,
				success : function(data)
				{	
					var unitsAry = JSON.parse(data);
					for(var objUnit in unitsAry)
					{
						if(unitsAry[objUnit].trim() == "Success")
						{
							data = "Email sent successfully to :" + " " +sEmail;	
						}
						else
						{
							data = "" + unitsAry[objUnit];	
						}
						
					}
					document.getElementById('status').innerHTML = data;
					document.getElementById('send_email').disabled = false;
					hideLoader();
				},
					
				fail: function()
				{
					document.getElementById('send_email').disabled = false;
					hideLoader();
				},
				
				error: function(XMLHttpRequest, textStatus, errorThrown) 
				{
					document.getElementById('send_email').disabled = false;
					hideLoader();
				}
		}
		);
	}
}