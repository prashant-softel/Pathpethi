var bTerminate = false;

function val()
{
	//alert("test");
var name=trim(document.getElementById('t_name').value);
var mobile=trim(document.getElementById('contact_1').value);
var email =trim(document.getElementById('email_1').value);
//var member=trim(document.getElementById('member').value);
var startDate=trim(document.getElementById('start_date').value);
var endDate=trim(document.getElementById('end_date').value);
//var upload=trim(document.getElementById('upload').value);
var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
var submit_type = document.getElementById('insert').value;

var CreateLogin = document.getElementById("chkCreateLogin").checked;
var send_commu_emails = document.getElementById("other_send_commu_emails").checked;

if(name=="")
	{	
		document.getElementById('error').innerHTML = "Please Provid Name";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('t_name').focus();
		go_error();
		return false;
	}
	if(startDate=="")
	{	
		document.getElementById('error').innerHTML = "Please Provid Lease Start Date";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('start_date').focus();
		go_error();
		return false;
	}		
		if(endDate=="")
	{	
		document.getElementById('error').innerHTML = "Please Provid Lease End Date";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('start_date').focus();
		go_error();
		return false;
	}
		
	if(mobile=="")
	{	
		document.getElementById('error').innerHTML = "Please Enter Mobile Number ";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('contact_1').focus();
		go_error();
		return false;
	}		
	if(email=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Enter Email Address";
		document.getElementById('email_1').focus();
		
		go_error();
		return false;
	}
	else
	{
		if(reg.test(email) == false) 
		{
			document.getElementById('error').style.display = '';
			document.getElementById('error').innerHTML = 'Invalid Email Address';	
			document.getElementById('email_1').focus();
			
			go_error();
			return false;	
		}		
	}
	if(CreateLogin  == true || send_commu_emails  == true)
	{
		if(document.getElementById('other_email').value == "")
		{
			alert("Please provide Email id");
			return false; 
		}
		else
		{
			var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;  
			if(document.getElementById("other_email").value.match(mailformat))  
			{  
				//alert("Email address valid!");  
				return true;  
			}  
			else  
			{  
				alert("Entered email address is not in valid email format !");  
				document.getElementById("other_email").focus();  
				return false;  
			}
		}
	}
	/*if(member=="")
	{	
		document.getElementById('error').innerHTML = "Please Provid Additional Member Name";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('member').focus();
		go_error();
		return false;
	}*/		
	
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


//function gettenant(str)
//{
//	var iden	= new Array();
//	iden		= str.split("-");
//
//	$('html, body').animate({ scrollTop: $('#top').offset().top }, 300);
//
//	if(iden[0]=="delete")
//	{
//		var conf = confirm("Are you sure , you want to delete it ???");
//		if(conf==1)
//		{
//			$(document).ready(function()
//			{
//				$("#error").fadeIn("slow");
//				document.getElementById("error").innerHTML = "Please Wait...";
//			});
//
//			remoteCall("../ajax/tenant.ajax.php","form=tenant&method="+iden[0]+"&tenantId="+iden[1],"loadchanges");
//		}
//	}
//	else
//	{
//		$(document).ready(function()
//		{
//			$("#error").fadeIn("slow");
//			document.getElementById("error").innerHTML = "Please Wait...";
//		});
//
//		remoteCall("../ajax/tenant.ajax.php","form=tenant&method="+iden[0]+"&tenantId="+iden[1],"loadchanges");
//	}
//}
//
//function loadchanges()
//{
//	var a		= sResponse.trim();
//	var arr1	= new Array();
//	var arr2	= new Array();
//	arr1		= a.split("@@@");
//	arr2		= arr1[1].split("#");
//
//
//	if(arr1[0] == "edit")
//	{
//		$(document).ready(function()
//		{
//			$("#error").fadeIn("slow");
//			document.getElementById("error").innerHTML = "Please Wait...";
//		});
//
//		document.getElementById('t_name').value=arr2[1];
//		//document.getElementById('dob').value=arr2[2];
//		document.getElementById('mob').value=arr2[3];
//		//document.getElementById('alt_mob').value=arr2[4];
//		document.getElementById('email').value=arr2[5];
//		document.getElementById('members').value=arr2[6];
//		document.getElementById('start_date').value=arr2[7];
//		document.getElementById('end_date').value=arr2[8];
//		//document.getElementById('p_varification').value=arr2[9];
//		document.getElementById('upload').value=arr2[10];
//		document.getElementById('note').value=arr2[11];
//		document.getElementById("id").value=arr2[0];
//		document.getElementById("insert").value="Update";
//	}
//	else if(arr1[0] == "delete")
//	{
//		$(document).ready(function()
//		{
//			$("#error").fadeIn("slow");
//			document.getElementById("error").innerHTML = "Please Wait...";
//		});
//
//		window.location.href ="../tenant.php?mst&"+arr1[1]+"&mm";
//	}
//}
//

function getTenant(str)
{ //alert("hi");	
	var iden=new Array();
	iden=str.split("-");		
	//alert(iden[1]);
	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{			
			remoteCall("ajax/tenant.ajax.php","&method="+iden[0]+"&TenantId="+iden[1],"loadchanges");
		}
	}
	else
	{
		remoteCall("ajax/tenant.ajax.php","&method="+iden[0]+"&TenantId="+iden[1],"loadchanges");			
	}
}

function loadchanges()
{	
	var a		= sResponse.trim();			
	var arr1	= new Array();
	var arr2	= new Array();
	arr1		= a.split("@@@");
	arr2		= arr1[1].split("#");		
	
	var tenant_details = new Array();
	tenant_details = JSON.parse(arr1[1]);
	console.log(tenant_details[0]);	
	console.log(tenant_details[0]['tenant_name']);
	//alert('test');		
	if(arr1[0] == "edit")
	{		
	//alert(tenant_details[0]['tenant_id']);
		//document.getElementById("tenant_id").value= tenant_details[0]['tenant_id'];
		document.getElementById("t_name").value= tenant_details[0]['tenant_name'];
		document.getElementById('start_date').value=tenant_details[0]['start_date'];
		//alert(bTerminate);
		
		if(bTerminate == false)
		{
			document.getElementById('end_date').value=tenant_details[0]['end_date'];
		}
		else
		{
			//document.getElementById('end_date').value=todayDate;
			$("#end_date").datepicker().datepicker("setDate", new Date());
		}
		//document.getElementById("dob").value=arr2[1];
		document.getElementById('agent').value=tenant_details[0]['agent_name'];
		document.getElementById('agent_no').value=tenant_details[0]['agent_no'];
		
		//document.getElementById('p_varification').checked = (arr2[7]==1) ? true : false;
		CKEDITOR.instances['note'].setData(tenant_details[0]['note']);
		
		var memberAry = new Array();
		memberAry = tenant_details[0]['members'];
		//alert( memberAry.length);
		for(var iCnt = 1; iCnt <= memberAry.length-1; iCnt++)
		{			
				//alert("add");
				$("#btnAdd").trigger('click');
		}
		
		for(var iCnt = 0; iCnt < memberAry.length; iCnt++)
		{
			document.getElementById('members_'+(iCnt+1)).value = memberAry[iCnt]['mem_name'];
			document.getElementById('relation_'+(iCnt+1)).value = memberAry[iCnt]['relation'];
			document.getElementById('mem_dob_'+(iCnt+1)).value = memberAry[iCnt]['mem_dob'];
			document.getElementById('contact_'+(iCnt+1)).value = memberAry[iCnt]['contact_no'];
			document.getElementById('email_'+(iCnt+1)).value = memberAry[iCnt]['email'];
		}
		if(memberAry[0]['send_act_email']  == "1")
		{
			document.getElementById('chkCreateLogin').checked = true;
		}
		if(memberAry[0]['send_commu_emails']  == "1")
		{
			document.getElementById('other_send_commu_emails').checked = true;
		}
		var DocumentAry = new Array();
		DocumentAry = tenant_details[0]['documents'];
		
		var docTable = "<table>";
		for(var iCnt = 0; iCnt < DocumentAry.length; iCnt++)
		{
			docTable += '<tr>';
			docTable += '<td><a href="Uploaded_Documents/' +  DocumentAry[iCnt]["Document"] + '" target=_blank>' + DocumentAry[iCnt]['Name'] + '</a></td><td><a href="#" onclick="del_Doc('+DocumentAry[iCnt]["doc_id"] + ',' +  tenant_details[0]["tenant_id"] + ') "><img style="width: 15px;margin-left: 10px;" src="images/del.gif" /></a></td>';
			docTable += '</tr>';
		}
		docTable += '<table>';
		document.getElementById('doc').innerHTML = docTable;
		document.getElementById("insert").value = "Update";																		
	}
	else if(arr1[0] == "view")
	{		
		//alert(tenant_details[0]['tenant_id']);
		//document.getElementById("tenant_id").value= tenant_details[0]['tenant_id'];
		//document.getElementById("t_name").value= tenant_details[0]['tenant_name'];
		//document.getElementById("t_name").readOnly = true;
		
		document.getElementById('data_table').style.border = "1px solid #cccccc";
		
		var t_name = tenant_details[0]['tenant_name'];
		document.getElementById('td_1').innerHTML = t_name;
		
		var start_date = tenant_details[0]['start_date'];
		document.getElementById('td_2').innerHTML = start_date;
		//document.getElementById('start_date').value=tenant_details[0]['start_date'];
		//document.getElementById('start_date').readOnly = true;
		//alert(bTerminate);
		
		if(bTerminate == false)
		{
			//document.getElementById('end_date').value=tenant_details[0]['end_date'];
			//document.getElementById('end_date').readOnly = true;
			var end_date = tenant_details[0]['end_date'];
			document.getElementById('td_3').innerHTML = end_date;
		}
		/*else
		{
			//document.getElementById('end_date').value=todayDate;
			$("#end_date").datepicker().datepicker("setDate", new Date());
		}*/
		//document.getElementById("dob").value=arr2[1];
		//document.getElementById('agent').value=tenant_details[0]['agent_name'];
		//document.getElementById('agent').readOnly = true;
		//document.getElementById('agent_no').value=tenant_details[0]['agent_no'];
		//document.getElementById('agent_no').readOnly = true;
		
		var agent_name = tenant_details[0]['agent_name'];
		document.getElementById('td_4').innerHTML = agent_name;
		var agent_no = tenant_details[0]['agent_no'];
		document.getElementById('td_5').innerHTML = agent_no;
		
		document.getElementById('doc_Id').style.display = "none";
		document.getElementById('add_button').style.display = "none";
		
		//document.getElementById('p_varification').checked = (arr2[7]==1) ? true : false;
		//CKEDITOR.instances['note'].setData(tenant_details[0]['note']);
		//CKEDITOR.instances.editor1.readOnly( true );
		//CKEDITOR.instances.note.readOnly( true );
		document.getElementById('textarea').style.display = "none";
		document.getElementById('to_show_note').innerHTML = tenant_details[0]['note'];

		var memberAry = new Array();
		memberAry = tenant_details[0]['members'];
		//alert( memberAry.length);
		for(var iCnt = 1; iCnt <= memberAry.length-1; iCnt++)
		{			
				//alert("add");
				$("#btnAdd").trigger('click');
		}
		
		for(var iCnt = 0; iCnt < memberAry.length; iCnt++)
		{
			document.getElementById('members_td_'+(iCnt+1)).innerHTML = memberAry[iCnt]['mem_name'];
			//document.getElementById('members_td_'+(iCnt+1)).style.border = '1px solid #cccccc';
			//document.getElementById('members_td_'+(iCnt+1)).style.borderColor = 'black';
			//document.getElementById('members_'+(iCnt+1)).readOnly = true;
			document.getElementById('relation_td_'+(iCnt+1)).innerHTML = memberAry[iCnt]['relation'];
			//document.getElementById('relation_td_'+(iCnt+1)).style.border = '1px solid #cccccc';
			//document.getElementById('relation_td_'+(iCnt+1)).style.borderColor = 'black';
			//document.getElementById('relation_'+(iCnt+1)).readOnly = true;
			document.getElementById('mem_dob_td_'+(iCnt+1)).innerHTML = memberAry[iCnt]['mem_dob'];
			//document.getElementById('mem_dob_td_'+(iCnt+1)).style.border = '1px solid #cccccc';
			//document.getElementById('mem_dob_td_'+(iCnt+1)).style.borderColor = 'black';
			//document.getElementById('mem_dob_'+(iCnt+1)).readOnly = true;
			document.getElementById('contact_td_'+(iCnt+1)).innerHTML = memberAry[iCnt]['contact_no'];
			//document.getElementById('contact_td_'+(iCnt+1)).style.border = '1px solid #cccccc';
			//document.getElementById('contact_td_'+(iCnt+1)).style.borderColor = 'black';
			//document.getElementById('contact_'+(iCnt+1)).readOnly = true;
			document.getElementById('email_td_'+(iCnt+1)).innerHTML = memberAry[iCnt]['email'];
			//document.getElementById('email_td_'+(iCnt+1)).style.border = '1px solid #cccccc';
			//document.getElementById('email_td_'+(iCnt+1)).style.borderColor = 'black';
			//document.getElementById('email_'+(iCnt+1)).readOnly = true;			
		}
		document.getElementById('mem_table').style.border = '1px solid #cccccc';
		//document.getElementById('mem_table').style.borderColor = 'black';
		
		document.getElementById('mem_table_tr').style.border = '1px solid #cccccc';
		//document.getElementById('mem_table_tr').style.borderColor = 'black';
		
		document.getElementById('chkCreateLogin').style.display = "none";
		document.getElementById('other_send_commu_emails').style.display = "none";
		
		document.getElementById('create_login').style.display = "none";
		document.getElementById('send_emails').style.display = "none";
		/*if(memberAry[0]['send_act_email']  == "1")
		{
			document.getElementById('chkCreateLogin').checked = true;
		}
		if(memberAry[0]['send_commu_emails']  == "1")
		{
			document.getElementById('other_send_commu_emails').checked = true;
		}*/
		var DocumentAry = new Array();
		DocumentAry = tenant_details[0]['documents'];
		
		var docTable = "<table>";
		for(var iCnt = 0; iCnt < DocumentAry.length; iCnt++)
		{
			docTable += '<tr>';
			docTable += '<td><a href="Uploaded_Documents/' +  DocumentAry[iCnt]["Document"] + '" target=_blank>' + DocumentAry[iCnt]['Name'] + '</a></td><td><a href="#" onclick="del_Doc('+DocumentAry[iCnt]["doc_id"] + ',' +  tenant_details[0]["tenant_id"] + ') "><img style="width: 15px;margin-left: 10px;" src="images/del.gif" /></a></td>';
			docTable += '</tr>';
		}
		docTable += '<table>';
		document.getElementById('doc').innerHTML = docTable;
		//document.getElementById("insert").value = "Update";
		document.getElementById('insert').style.display = "none";	
		document.getElementById('print').style.display = "block";
	}
	else if(arr1[0] == "delete")
	{			
		window.location.href ="show_tenant.php";
	}
}

function del_Doc(id,Tid)
	{
		//alert	(doc +':' + id);
		//alert("id"+id);
		//alert("Tid"+Tid);
		if(id!="")
		{
			var con = confirm("Are you sure,You want to delete the images ?");
			
			if(con==true)
			{	
				//document.getElementById('error_home_page').style.display = '';	
				document.getElementById("error").innerHTML = "Please Wait...";
				
				remoteCall("ajax/tenant.ajax.php","form=tenant&method=del_Doc&id="+id+"&Tid="+Tid,"deleteDocument");	
			}
		}	
	}
function deleteDocument(id,Tid)
{
	//alert("image deleted");
	//window.reload();
	location.reload();
}


///////////  delete Documents File///////////////////////////////////////////
//
//function del_document(doc,qr)
//	{
//		
//		//alert(qr);
//		//alert(doc);
//		if(qr!="")
//		{
//			var con = confirm("Are you sure,You want to delete the images ?");
//			
//			if(con==true)
//			{	
//				//document.getElementById('error_home_page').style.display = '';	
//				document.getElementById("error").innerHTML = "Please Wait...";
//				
//				remoteCall("ajax/tenant.ajax.php","form=tenant&method=del_Doc&qr="+qr+"&doc="+doc,"deletedImage");	
//			}
//		}	
//	}
function deletedImage(doc,qr)
{
	//alert("image deleted");
	//window.reload();
	location.reload();
}


///////////////////////////////////////////////////// fetch  tenant details///////////////////////////////////////////////////////////

function  FetchTenantHistory(society_id)
{
	//alert("test");
	showLoader();
	var TenantList = document.getElementById('TenantList').value;
	
	$.ajax({
			url : "ajax/tenant.ajax.php",
			type : "POST",
			data : {"method" : 'fetch',"societyID" : society_id,"TenantList" : TenantList},
			beforeSend: function()
			{
				document.getElementById('showTable').innerHTML = '<center><font color="blue">Fetching Records Please Wait...</font></center>';
			},
			success : function(data)
			{	
				document.getElementById('showTable').innerHTML = data;
				hideLoader();
				
				if(document.getElementById('AllowExport').value  ==  1 && $.trim(data))
				{
					//data not empty
					document.getElementById('btnExport').style.display = 'block';
					document.getElementById('Print').style.display = 'block';
				}
				else
				{
					//data empty
					document.getElementById('btnExport').style.display = 'none';
					document.getElementById('Print').style.display = 'none';
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

function PrintPage() 
{
	var originalContents = document.body.innerHTML;
	document.getElementById('societyname').style.display ='block';	
	var printContents = document.getElementById('showTable').innerHTML;
	
	document.body.innerHTML = printContents;
	window.print();

	document.body.innerHTML= originalContents;
}

//////////////////////////////////////////////////////////////////////////  user tenant list /////////////////////////////////////////////////////////////////////////////

function  FetchTenantList(society_id,unit_id)
{
	//alert("test");
	showLoader();
	var TenantList = document.getElementById('TenantList').value;
	
	$.ajax({
			url : "ajax/tenant.ajax.php",
			type : "POST",
			data : {"method" : 'fetchList',"societyID" : society_id, "unitID" : unit_id, "TenantList" : TenantList},
			beforeSend: function()
			{
				document.getElementById('showTable').innerHTML = '<center><font color="blue">Fetching Records Please Wait...</font></center>';
			},
			success : function(data)
			{	
				document.getElementById('showTable').innerHTML = data;
				hideLoader();
				
				if(document.getElementById('AllowExport').value  ==  1 && $.trim(data))
				{
					//data not empty
					document.getElementById('btnExport').style.display = 'block';
					document.getElementById('Print').style.display = 'block';
				}
				else
				{
					//data empty
					document.getElementById('btnExport').style.display = 'none';
					document.getElementById('Print').style.display = 'none';
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

function PrintPage() 
{
	var originalContents = document.body.innerHTML;
	document.getElementById('societyname').style.display ='block';	
	var printContents = document.getElementById('showTable').innerHTML;
	
	document.body.innerHTML = printContents;
	window.print();

	document.body.innerHTML= originalContents;
}
