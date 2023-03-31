// JavaScript Document
var iSocietyID = 0;
var iLoginID = 0;

function fetchSocietyList(clientID)
{
	document.getElementById('main_list').style.display = 'block';
	document.getElementById('user_list').style.display = 'none';
	document.getElementById('user_details').style.display = 'none';
	document.getElementById('addSociety').style.display = 'none';
	
	document.getElementById('list_society').innerHTML = 'Fetching list. Please wait ...';
	var obj = {"method" : "fetchlist", "client" : clientID};
	remoteCallNew("ajax/client.ajax.php", obj, 'loadSocietyList');
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
		aaSorting : []
	} );
}

function fetchUserDetails(societyID)
{
	iSocietyID = societyID;
	
	document.getElementById('society_name').innerHTML = "";
	
	document.getElementById('user_list').style.display = 'block';
	document.getElementById('main_list').style.display = 'none';
	document.getElementById('user_details').style.display = 'none';
	document.getElementById('addSociety').style.display = 'none';
	
	var obj = {"method" : "fetchsociety", "society" : societyID};
	remoteCallNew("ajax/client.ajax.php", obj, 'setSocietyName');		
}

function fetchLoginDetails(userID)
{
	iLoginID = userID;
					
	document.getElementById('user_list').style.display = 'none';
	document.getElementById('main_list').style.display = 'none';
	document.getElementById('user_details').style.display = 'block';
	document.getElementById('login_list').style.display = 'none';
	document.getElementById('addSociety').style.display = 'none';
	document.getElementById('member').innerHTML = 'Fetching list. Please wait ...';		
	
	var obj = {"method" : "fetchlogindetails", "userID" : userID};
	remoteCallNew("ajax/client.ajax.php", obj, 'setLoginDetails');		
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
	remoteCallNew("ajax/client.ajax.php", obj, 'loadSAUserList');	
}

function setLoginDetails()
{
	document.getElementById('member').style.display = 'block';
	document.getElementById('assing_new_society').style.display = 'block';
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	
	sResponse = sResponse.replace('id="example"', 'id="member1"');
	document.getElementById('unit').innerHTML = iLoginID;
	document.getElementById('member').innerHTML = sResponse;
	
	$('#member1').DataTable( {
		dom: 'T<"clear">lfrtip',
		"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
		aaSorting : []
	});	
	
	populateSocietyList();
}

function loadSAUserList()
{
	loadUserList("user_super_admin");
	
	var obj = {"method" : "fetchuserlist", "society" : iSocietyID, "usertype" : "Admin"};
	remoteCallNew("ajax/client.ajax.php", obj, 'loadAUserList');
}

function loadAUserList()
{
	loadUserList("user_admin");

	var obj = {"method" : "fetchuserlist", "society" : iSocietyID, "usertype" : "Admin Member"};
	remoteCallNew("ajax/client.ajax.php", obj, 'loadAMUserList');
}

function loadAMUserList()
{
	loadUserList("user_admin_member");

	var obj = {"method" : "fetchuserlist", "society" : iSocietyID, "usertype" : "Member"};
	remoteCallNew("ajax/client.ajax.php", obj, 'loadMUserList');
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
	document.getElementById('addSociety').style.display = 'none';
}

function backToMembersList()
{
	document.getElementById('main_list').style.display = 'none';
	document.getElementById('user_list').style.display = 'block';	
	document.getElementById('user_details').style.display = 'none';
	document.getElementById('addSociety').style.display = 'none';
}

function viewAssienedSocieties()
{
	var obj = {"method" : "fetchAssignedSocieties", "LoginID" : iLoginID};
	remoteCallNew("ajax/client.ajax.php", obj, 'setAssignedSocieties');	
}

function setAssignedSocieties()
{
	document.getElementById('addSociety').style.display = 'none';
	document.getElementById('assigned_societies').style.display = 'none';
	document.getElementById('login_list').style.display = 'block';
	document.getElementById('member').style.display = 'block';	
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
	document.getElementById('loginDetails').style.display = 'block';
	fetchLoginDetails(iLoginID);
}

function populateSocietyList()
{
	populateDDListAndTrigger('select#societies', 'ajax/client.ajax.php?getSocieties&LoginID=' + iLoginID, 'society_name', '', false);	
}

function getUnits(id)
{	
	populateDDListAndTrigger('select#unitID', 'ajax/client.ajax.php?getUnits&SocietyID=' + id.value, 'unit', '', false);		
}

function addSociety()
{
	document.getElementById('addSociety').style.display = 'block';	
	document.getElementById('main_list').style.display = 'none';
	document.getElementById('user_list').style.display = 'none';
	document.getElementById('user_details').style.display = 'block';
	document.getElementById('member').style.display = 'none';
	document.getElementById('loginDetails').style.display = 'none';
	document.getElementById('unit').style.display = 'none';	
	document.getElementById('assing_new_society').style.display = 'none';
	document.getElementById('login_list').style.display = 'block';
}

function addUser()
{
	var userRole = document.getElementById('role').value;
	var iSocietyID = document.getElementById('societies').value;
	var iUnitID = document.getElementById('unitID').value;		
	if(iSocietyID == 0)
	{
		alert('Please select Society first.');
		return;
	}
	else if(userRole == 'Member' && iUnitID == 0)	
	{
		alert('Please select Unit for member.');
		return;
	}
	else 
	{
		var obj = {"method" : "adduser", "societyID" : iSocietyID, "userRole" : userRole, "unitID" : iUnitID, "LoginID" : iLoginID};
		remoteCallNew("ajax/client.ajax.php", obj, 'fetchLoginDetails(' + iLoginID + ')');
	}
	backToLoginList();
}