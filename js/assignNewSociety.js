function getUnits(id)
{	
	populateDDListAndTrigger('select#unitID', 'ajax/client.ajax.php?getUnits&SocietyID=' + id.value, 'unit', '', false);		
}

function addUser()
{
	var userRole = document.getElementById('role').value;
	var iSocietyID = document.getElementById('societies').value;
	var iUnitID = document.getElementById('unitID').value;
	var iLoginID = document.getElementById('loginID').value; 		
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
		remoteCallNew("ajax/client.ajax.php", obj, 'backToLoginDetails');
	}	
}

function backToLoginDetails()
{
	//var strLink = "client_details.php?client=1";
	//document.getElementById("addUser").setAttribute("href",strLink);	
	window.location.href="client_details.php?client="+iEncryptedClientID;
}