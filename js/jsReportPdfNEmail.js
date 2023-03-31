var filepath='';

function ViewPDF(SocietyCode,UnitNo,isEmail)
{
	var sData = document.getElementById('Exportdiv').innerHTML;

	var sHeader = '<html><head>';
	sHeader += '<style>	 a{text-decoration:none;}  table{border-collapse:collapse;}</style>';	
	sHeader +=	'</head><body>';
	
	var sFooter =  '</body></html>';
	
	sData = sHeader + sData + sFooter;
	var sFileName = "MemberLedgerReport-" + SocietyCode + "-" + UnitNo;
	if(CurrentUnit == 0)
	{
		sFileName = "MemberLedgerReport-" + SocietyCode + "-" + "All";
		UnitNo = "All";
	}
	
	document.getElementById('status').style.color="red";     
	document.getElementById('status').innerHTML="Exporting To PDF Please Wait......";

	var obj = {"data":sData, "file":sFileName};
	$.ajax({
		url : "viewreportpdf.php",
		type : "POST",
		data: { "data":sData, 
				"filename":sFileName, 
				"society":SocietyCode} ,
		success : function(data)
		{
			if(isEmail != true)
			{
				window.open('Reports/' + SocietyCode + '/' + sFileName + '.pdf');
				alert("PDF For Unit : [" + UnitNo + "] Generated Successfully..");
			}
			else
			{
				document.getElementById('EmailTable').style.visibility='visible';
				document.getElementById('EmailID').value = document.getElementById('EmailID').defaultValue;	
					
				filepath = 'Reports/' + SocietyCode + '/' + sFileName + '.pdf';
				$('#EmailFile').attr('href', filepath);
			}
			document.getElementById('status').innerHTML="";
		},
			
		fail: function()
		{
		},
		
		error: function(XMLHttpRequest, textStatus, errorThrown) 
		{
		}
	});
}
	
function ViewAllPDF(SocietyCode, UnitNo, isEmail)
{
	var iCounter = 0;
	var iResponse = 0;
	
	//alert(jUnitIDNoArray.length);
	
	var bDelete = 1;
	
	for(var i = 0; i < jUnitIDNoArray.length; i++)
	{
		var temp = jUnitIDNoArray[i].split("_");
		UnitNo = temp[2];
		//alert(UnitNo);
		var sData = document.getElementById(jUnitIDNoArray[i]).innerHTML;

		var sHeader = '<html><head>';
		sHeader += '<style>	 a{text-decoration:none;}  table{border-collapse:collapse;}</style>';	
		sHeader +=	'</head><body>';
		
		var sFooter =  '</body></html>';
		
		sData = sHeader + sData + sFooter;
		var sFileName = "MemberLedgerReport-" + SocietyCode + "-" + UnitNo;
		
		//alert(sFileName);
		/*if(CurrentUnit == 0)
		{
			sFileName = "MemberLedgerReport-" + SocietyCode + "-" + "All";
			UnitNo = "All";
		}*/
		
		document.getElementById('status').style.color="red";     
		document.getElementById('status').innerHTML="Exporting To PDF Please Wait......";

		iCounter++;	
		var obj = {"data":sData, "file":sFileName};
		$.ajax({
			url : "viewreportpdf.php",
			type : "POST",
			data: { "data":sData, 
					"filename":sFileName, 
					"society":SocietyCode,
					"merge" : 0} ,
			success : function(data)
			{
				/*if(isEmail != true)
				{
					window.open('Reports/' + SocietyCode + '/' + sFileName + '.pdf');
					alert("PDF For Unit : [" + UnitNo + "] Generated Successfully..");
				}
				else
				{
					filepath = 'Reports/' + SocietyCode + '/' + sFileName + '.pdf';
					$('#EmailFile').attr('href', filepath);
				}*/
				iResponse++;
				if(iCounter == iResponse)
				{
					//alert("All units exported");
					document.getElementById('status').innerHTML="Combining PDF. Please wait...";
					MergeReports(SocietyCode, isEmail);
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
}

function MergeReports(SocietyCode, isEmail)
{
	sFileName = "MemberLedgerReport-" + SocietyCode + "-" + "All";
	
	//alert("merge file");
	
	//var obj = {"data":sData, "file":sFileName};
		$.ajax({
			url : "viewreportpdf.php",
			type : "POST",
			data: { "filename":sFileName, 
					"society":SocietyCode,
					"merge" : 1} ,
			success : function(data)
			{
				document.getElementById('status').innerHTML="";
				//alert(data);
				if(isEmail != true)
				{
					window.open('Reports/' + SocietyCode + '/' + sFileName + '.pdf');
					alert("PDF Generated Successfully..");
				}
				else
				{
					document.getElementById('EmailTable').style.visibility='visible';
					document.getElementById('EmailID').value = document.getElementById('EmailID').defaultValue;	
					document.getElementById('SubjectHead').value = "Member Ledger Report for All Units";
					document.getElementById('Message').innerHTML = "Attached Member Ledger Report For All Units";
					filepath = 'Reports/' + SocietyCode + '/' + sFileName + '.pdf';
					$('#EmailFile').attr('href', filepath);
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

function sendEmail()
{
	//document.getElementById('status_' + unitID).innerHTML = 'Sending ...';
	//ViewPDF(SocietyCode,UnitNo)
	//var periodID = document.getElementById('period_id').value;
	var unitID = document.getElementById('UnitID').value;
	var emailID = document.getElementById('EmailID').value;
	var emailMessage = document.getElementById('Message').value;
	var emailSubjectHead = document.getElementById('SubjectHead').value;
	var emailCC = document.getElementById('CC').value;
	
	if(CurrentUnit == 0)
	{
		unitID = 0;
	}
	//alert("UnitID:" + unitID);
	//alert("emailID:" + emailID);
	//alert("emailMessage:" + emailMessage);
	//alert("emailSubjectHead:" + emailSubjectHead);
	document.getElementById('status').style.color="red";     
	document.getElementById('status').innerHTML="Sending Email Please Wait......";
	$.ajax({
			url : "ajax/ajaxUnitReport.php",
			type : "POST",
			data: { "unitID":unitID,"emailID":emailID,"emailMessage":emailMessage,"emailSubjectHead":emailSubjectHead,"method" : 'email', "CC":emailCC} ,
			success : function(data)
			{	
			document.getElementById('status').innerHTML="";
				//document.getElementById('status_' + unitID).innerHTML = data;
				alert("Thank You Email Sent To: <" + emailID + "> Successfully.");
				//alert(data);
				document.getElementById('EmailTable').style.visibility='hidden';
				//document.getElementById('status').innerHTML="";	
			},
				
			fail: function()
			{
				alert("Sorry Failed");
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				alert("Error Occured");
			}
		});
		
		return false;
		
}


function ShowTable()
{
	var SocietyCode = document.getElementById('SocietyCode').value;
	var UnitNo = document.getElementById('UnitNoForPdf').value;
	if(CurrentUnit == 0)
	{
		ViewAllPDF(SocietyCode,UnitNo,true);
	}
	else
	{
		ViewPDF(SocietyCode,UnitNo,true);
	}
	//document.getElementById('EmailTable').style.visibility='visible';
	//document.getElementById('EmailID').value = document.getElementById('EmailID').defaultValue;	
	window.scrollTo(0,document.body.scrollHeight);
}

var EnableSubmit = false;
function ValidateEmail(inputText)  
{  
	//alert(inputText.value);
	var result = inputText.value.replace(" ", "");
	//alert(result);
	document.getElementById('CC').innerHTML = result;

	var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
	//alert(inputText.value);
	var EMailIDs = result.split(";");  

	var iEMmailIDCount = EMailIDs.length;
	//alert(iEMmailIDCount);
	for(var iCounter =0; iCounter <= iEMmailIDCount; iCounter++)
	{
		//alert(EMailIDs[iCounter]);
		if(EMailIDs[iCounter].length > 0)
		{
			if(EMailIDs[iCounter].match(mailformat))  
			{  
				//document.getElementById(inputText.id).focus(); 
				document.getElementById(inputText.id).style.color="black";     
				//return true;  
			}  
			else  
			{  
				alert("You have entered an invalid email address <" + EMailIDs[iCounter] +">.");  
				document.getElementById(inputText.id).focus();
				document.getElementById(inputText.id).style.color="red";    
				//return false;
				EnableSubmit = false;  
				break;
				//alert(EMailIDs[iCounter].value);
			}
		}
	}
	if(EnableSubmit)
	{
		document.getElementById("SendEmail").disabled = true;
	}
	else
	{
		document.getElementById("SendEmail").disabled = false;
	}
}  




	