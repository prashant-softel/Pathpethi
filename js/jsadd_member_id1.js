function getadd_member_id(str)
{
	var iden=new Array();
	iden=str.split("-");

	if(iden[0]=="delete")
	{
		var d=confirm("Are you sure , you want to delete it ???");
		if(d==1)
		{
			remoteCall("ajax/ajaxadd_member_id.php","form=add_member_id&method="+iden[0]+"&add_member_idId="+iden[1],"loadchanges");
		}
	}
	else
	{
			remoteCall("ajax/ajaxadd_member_id.php","form=add_member_id&method="+iden[0]+"&add_member_idId="+iden[1],"loadchanges");
	}
}

function loadchanges()
{
	var a=trim(sResponse);
	var arr1=new Array();
	var arr2=new Array();
	arr1=a.split("@@@");
	arr2=arr1[1].split("#");
	if(arr1[0] == "edit")
	{
		document.getElementById('com_id').value=arr2[1];
		document.getElementById('member_id').value=arr2[2];
		document.getElementById('password').value=arr2[3];
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{
		window.location.href ="../add_member_id.php";
	}
	function LTrim( value )
	{
	var re = /\s*((\S+\s*)*)/;
	return value.replace(re, "$1");
	}
	function RTrim( value )
	{
	var re = /((\s*\S+)*)\s*/;
	return value.replace(re, "$1");
	}
	function trim( value )
	{
	return LTrim(RTrim(value));
	}
	
}


function emailPromtWindow(sEmailId,sName,sCode) 
{
	//alert(sCode);
	if (sEmailId == "")
	{
			sEmailId = "abc@way2society.com";
	}
	var emailID = prompt("Please enter Email ID",  sEmailId);
	
	if (emailID != null)
	{				
		/*var sURL = "ajax/ajaxmanage_users.php";
		var obj = {"method" : "fetchEmail","email" : sEmailId,"name":sName , "code":sCode};
		remoteCallNew(sURL, obj, 'emailPreview()');	*/
		
		
		$.ajax({
			url : "ajax/ajaxmanage_users.php",
			type : "POST",
			data : {"method" : "fetchEmail","email" : emailID,"name":sName , "code":sCode},
			success : function(data)
			{	
				//hideLoader();	
				emailPreview(data,emailID,sName ,sCode);
				
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

function ToggleCheckAll()
{
	//alert('test');
	var UnitsCheckboxes = document.getElementsByClassName('chckbxUnits'), i;
	var bCheckStatus = document.getElementById("checkall").checked;
	//alert(bCheckStatus);
	var iUnitsToggled = 0;
	for (i = 0; i < UnitsCheckboxes.length; i += 1)
	{
		var sEmailID = UnitsCheckboxes[i].id;
		var sName = UnitsCheckboxes[i].name;
		//alert(UnitsCheckboxes[i].checked);
		if(sEmailID.length > 0 )
		{
			//if(UnitsCheckboxes[i].checked)
			//{
				//UnitsCheckboxes[i].checked = false;
				//document.getElementById(UnitsCheckboxes[i].id).checked = bCheckStatus;
				if(bCheckStatus)
				{
					//alert('checked');
					$(UnitsCheckboxes[i]).closest('tr').find('input:checkbox:first').attr('checked', true);
				}
				else
				{
					$(UnitsCheckboxes[i]).closest('tr').find('input:checkbox:first').attr('checked', false);
				}
			iUnitsToggled++; 
		}
		else
		{
			$(UnitsCheckboxes[i]).closest('tr').find('div').text('Email ID is not set');
			$(UnitsCheckboxes[i]).closest('tr').find('div'). css("color", "black");
			
			
			//document.getElementById('lbl').innerHTML  = "Email ID is not set";
			
			//$(':div').closest('tr').find('div').text('Email ID is not set');
			//alert(UnitsCheckboxes[i]);
			//var demo = $(UnitsCheckboxes[i]).closest('tr').find('p')[0].innerHTML + 'test';
			//$(UnitsCheckboxes[i]).closest('tr').find('div').text(demo);
			
			//$('lbl').hide().html('Email ID is not set').fadeIn('slow').delay(5000).hide();
		}
	}
	if(bCheckStatus)
	{
		alert( iUnitsToggled  + ' unit(s) selected.');
		document.getElementById("SendAllTop").style.display = 'block';
		
		document.getElementById("SendAll").style.display = 'block';
	}
	
}

function SendAll()
{
	//alert('start');
	var UnitsCheckboxes = document.getElementsByClassName('chckbxUnits'), i;
	//alert(UnitsCheckboxes.length);
	for (i = 0; i < UnitsCheckboxes.length; i += 1)
	{
		var sEmailID = UnitsCheckboxes[i].id;
		var sName = UnitsCheckboxes[i].name;
		var sResponse = "";
		//alert(UnitsCheckboxes[i].checked);
		if(sEmailID.length > 0 && UnitsCheckboxes[i].checked )
		{
			//sendAccountActivationEmail(sEmailID ,sName , 0);
			//alert(sEmailID);
			var sCode = $(UnitsCheckboxes[i]).closest('tr').find('p')[0].innerHTML;
			//alert($(UnitsCheckboxes[i]).closest('tr').find('p').text());
			document.getElementById('lbl'+sEmailID).innerHTML = 'Sending ...';
			$.ajax({
			url : "ajax/ajaxmanage_users1.php",
			type : "POST",
			data : {"method" : "sendEmail","email" : sEmailID,"name":sName , "code":sCode},
			success : function(data)
			{	
				//hideLoader();	
				data = data.replace(/\s+/, "") ;
				var iIndex = data.lastIndexOf(":");
				if(iIndex >= 0)
				{
					var sEmail = data.substr(0, iIndex);
					var sResult = data.substr(iIndex + 1);
					//sResponse = data;
					//alert('email:'+sEmail);
					//alert('result:'+sResult);
					document.getElementById('lbl'+sEmail).innerHTML = sResult;
					if(sResult == 'Success')
					{
						document.getElementById('lbl'+sEmail).style.color = "green";
					}
					else
					{
						document.getElementById('lbl'+sEmail).style.color = "red";
					}
				}
				//$(UnitsCheckboxes[i]).closest('tr').find('label').text('data);
				
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
			//alert(sEmailID);
			
		}
	}
}

function emailPreview(sResponse,sEmailId,sName ,sCode) 
{
	
	//var sResponse = getResponse(RESPONSETYPE_STRING, true);
	var sMsg = sResponse;
	var sText = '<a href="#close" title="Close" class="close" id="close" onClick="closeDialogBox();">X</a>';
	sText += '<center><font style="font-size:18px;"><b>Preview Email</b></center></font>' + sMsg + '<br/>';
	var sButton = '<br/><center><button name="Send" class="closeButton" id="dialogYesNo_yes"  onClick="sendAccountActivationEmail(\''+sEmailId +'\',\''+sName +'\',\''+sCode+'\');">Send Activation Email To User</button>';
	sButton += '<button name="Close" class="closeButton" id="dialogYesNo_no"  onClick="closeDialogBox();">Close</button></center>';
	
	document.getElementById('message_ok').innerHTML = sText;
	document.getElementById('message2').innerHTML = sButton;
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

function sendAccountActivationEmail(sEmailId ,sName , sCode)
{

	showLoader();
	$.ajax({
			url : "ajax/ajaxmanage_users1.php",
			type : "POST",
			data : {"method" : "sendEmail","email" : sEmailId,"name":sName , "code":sCode},
			success : function(data)
			{	
				hideLoader();	
				//alert(data);
				data = data.replace(/\s+/, "") ;
				var iIndex = data.lastIndexOf(":");
				if(iIndex >= 0)
				{
					var sEmail = data.substr(0, iIndex);
					var sResult = data.substr(iIndex + 1);
					//sResponse = data;
					//alert('email:'+sEmail);
					alert('Email Sent status : '+sResult);
					//document.getElementById('lbl'+sEmail).innerHTML = sResult;
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