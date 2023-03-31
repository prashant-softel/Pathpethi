// JavaScript Document
var iSocietyID = 0;
var iLoginID = 0;

function fetchSocietyList(clientID)
{
	document.getElementById('main_list').style.display = 'block';
	document.getElementById('user_list').style.display = 'none';
	document.getElementById('user_details').style.display = 'none';	
	
	document.getElementById('list_society').innerHTML = 'Fetching list. Please wait ...';
	var obj = {"method" : "fetchlist", "client" : clientID};
	remoteCallNew("ajax/client.ajax.php?client="+iClientID, obj, 'loadSocietyList');
}

function loadSocietyList()
{
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	document.getElementById('list_society').innerHTML = sResponse;
	
	var oTable = $('#example').dataTable();
	oTable.fnDestroy();
	
	$('#example').DataTable( {
		dom: 'T<"clear">lfrtip',
		"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
		aaSorting : [],
		 "iDisplayLength" : 50
	} );
	
}

function fetchUserDetails(societyID)
{
	iSocietyID = societyID;
	
	document.getElementById('society_name').innerHTML = "";
	
	document.getElementById('user_list').style.display = 'block';
	document.getElementById('main_list').style.display = 'none';
	document.getElementById('user_details').style.display = 'none';	
	
	var obj = {"method" : "fetchsociety", "society" : societyID};
	remoteCallNew("ajax/client.ajax.php?client="+iClientID, obj, 'setSocietyName');		
}

function fetchLoginDetails(userID)
{		
	iLoginID = userID;
	document.getElementById('unit').innerHTML = '';				
	document.getElementById('user_list').style.display = 'none';
	document.getElementById('main_list').style.display = 'none';
	document.getElementById('user_details').style.display = 'block';
	document.getElementById('login_list').style.display = 'none';
	document.getElementById('assing_new_society').style.display = 'none';
	document.getElementById('member').innerHTML = 'Fetching list. Please wait ...';		
	
	var obj = {"method" : "fetchlogindetails", "userID" : userID};
	remoteCallNew("ajax/client.ajax.php?client="+iClientID, obj, 'setLoginDetails');		
}

function setSocietyName()
{
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	document.getElementById('society_name').innerHTML = sResponse;
	
	document.getElementById('user_super_admin').innerHTML = 'Fetching list. Please wait ...';
	document.getElementById('user_admin').innerHTML = 'Fetching list. Please wait ...';
	document.getElementById('user_admin_member').innerHTML = 'Fetching list. Please wait ...';
	document.getElementById('user_member').innerHTML = 'Fetching list. Please wait ...';
		
	var obj = {"method" : "fetchuserlist", "society" : iSocietyID, "usertype" : "Super Admin"};
	remoteCallNew("ajax/client.ajax.php?client="+iClientID, obj, 'loadSAUserList');	
}

function setLoginDetails()
{	
	document.getElementById('member').style.display = 'block';
	document.getElementById('assing_new_society').style.display = 'none';
	var sResponse = getResponse(RESPONSETYPE_STRING, true);	
	sResponse = sResponse.replace('id="example"', 'id="member1"');		
	document.getElementById('member').innerHTML = sResponse;	
	
	$('#member1').DataTable( {
		dom: 'T<"clear">lfrtip',
		"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
		aaSorting : []
	});	
	document.getElementById('unit').innerHTML = document.getElementById('loginName').value;	
}

function loadSAUserList()
{
	loadUserList("user_super_admin");
	
	var obj = {"method" : "fetchuserlist", "society" : iSocietyID, "usertype" : "Admin"};
	remoteCallNew("ajax/client.ajax.php?client="+iClientID, obj, 'loadAUserList');
}

function loadAUserList()
{
	loadUserList("user_admin");

	var obj = {"method" : "fetchuserlist", "society" : iSocietyID, "usertype" : "Admin Member"};
	remoteCallNew("ajax/client.ajax.php?client="+iClientID, obj, 'loadAMUserList');
}

function loadAMUserList()
{
	loadUserList("user_admin_member");

	var obj = {"method" : "fetchuserlist", "society" : iSocietyID, "usertype" : "Member"};
	remoteCallNew("ajax/client.ajax.php?client="+iClientID, obj, 'loadMUserList');
}

function loadMUserList()
{
	loadUserList("user_member");
}

function loadUserList(CtrlID)
{
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	
	sResponse = sResponse.replace('id="example"', 'id="' + CtrlID +  '_table"');
	
	document.getElementById(CtrlID).innerHTML = sResponse;
	
	$('#' + CtrlID + "_table").DataTable( {
		dom: 'T<"clear">lfrtip',
		"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
		aaSorting : []
	});
}

function backToSocietyList()
{
	document.getElementById('main_list').style.display = 'block';
	document.getElementById('user_list').style.display = 'none';
	document.getElementById('user_super_admin').innerHTML = '';
	document.getElementById('user_admin').innerHTML = '';
	document.getElementById('user_admin_member').innerHTML = '';
	document.getElementById('user_member').innerHTML = '';	
	document.getElementById('user_details').style.display = 'none';	
}

function backToMembersList()
{
	document.getElementById('main_list').style.display = 'none';
	document.getElementById('user_list').style.display = 'block';	
	document.getElementById('user_details').style.display = 'none';	
}

function viewAssienedSocieties()
{	
	if(iLoginID > 0)
	{
		var obj = {"method" : "fetchAssignedSocieties", "LoginID" : iLoginID};
		remoteCallNew("ajax/client.ajax.php?client="+iClientID, obj, 'setAssignedSocieties');	
	}
	else
	{		
		document.getElementById('assigned_societies').style.display = 'none';
		document.getElementById('login_list').style.display = 'block';
		document.getElementById('member_list').style.display = 'none';
		document.getElementById('member').style.display = 'block';
		document.getElementById('assing_new_society').style.display = 'block';	
		document.getElementById('member').innerHTML = 'Still There is no society assigned.';
	}
}

function setAssignedSocieties()
{	
	document.getElementById('assigned_societies').style.display = 'none';
	document.getElementById('login_list').style.display = 'block';
	document.getElementById('member_list').style.display = 'none';
	document.getElementById('member').style.display = 'block';
	document.getElementById('assing_new_society').style.display = 'block';
	document.getElementById('loginDetails').innerHTML = 'Assigned Societies For';	
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	
	sResponse = sResponse.replace('id="example"', 'id="member2"');
	
	document.getElementById('member').innerHTML = sResponse;
	
	$('#member2').DataTable( {
		dom: 'T<"clear">lfrtip',
		"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
		aaSorting : []
	});	
}

function backToLoginList()
{
	document.getElementById('assigned_societies').style.display = 'block';	
	document.getElementById('member_list').style.display = 'block';
	fetchLoginDetails(iLoginID);
}

function AddNewSociety()
{				
	var strLink = "assignNewSociety.php?loginID=" + document.getElementById('encryptedLoginID').value + "&ClID="+iEncrClientID;
	document.getElementById("addnewsociety").setAttribute("href",strLink);
}

function myFunction(clientID, mID) 
{
	var emailID = prompt("Please enter Email ID", "abc@way2society.com");
	
	if (emailID != null)
	{				
		$.ajax({
				url : "classes/emailToUser.class.php",
				type : "POST",
				data: { "email":emailID, "clientID":clientID, "mID":mID} ,
				success : function(data)
				{	
					alert('Mail sent successfully');
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
