function getAgents(str)
{
	//console.log('Inside the get event');
	var iden=new Array();
	iden=str.split("-");
    //console.log(iden);
	if(iden[0]=="delete")
	{
		var d=confirm("Are you sure , you want to delete it ???");
		//print("hi");
		if(d==1)
		{
			if(iden[2]=="self")
			{
				remoteCall("ajax/ajaxagent_self.php","form=agentForm&method="+iden[0]+"&agent_id="+iden[1],"loadchanges");
			}
			else
			{
				remoteCall("ajax/ajaxagent.php","form=agentForm&method="+iden[0]+"&agent_id="+iden[1],"loadchanges");
			}
		}
	}
	else
	{
			remoteCall("ajax/ajaxagent.php","form=agentForm&method="+iden[0]+"&agent_id="+iden[1],"loadchanges");
	}
}

function loadchanges()
{
	//console.log('Loachanges');
	var a=trim(sResponse);
	//console.log(a);
	var arr1=new Array();
	var arr2=new Array();
	arr1=a.split("@@@");
	//console.log(arr1);
	arr2=arr1[1].split("#");
	//console.log(arr2);
	if(arr1[0] == "edit")
	{
		//console.log('Edit ');
		console.log(JSON.parse(arr2));
		var data = JSON.parse(arr2);
		document.getElementById('agent_id').value=data[0]['agent_id'];
		document.getElementById('agentname').value=data[0]['agent_name'];
		document.getElementById('accounttype').value=data[0]['account_type'];
		document.getElementById('subglcode').value=data[0]['subgl_code'];
		document.getElementById('age').value=data[0]['age'];
		document.getElementById('qualification').value=data[0]['qualification'];
		document.getElementById('resi_add').value=data[0]['address'];
		document.getElementById('city').value=data[0]['city'];
		document.getElementById('state').value=data[0]['state'];
		document.getElementById('pinno').value=data[0]['pin_no'];
		document.getElementById('area').value=data[0]['area'];
		document.getElementById('telno').value=data[0]['tel_no'];
		document.getElementById('mob').value=data[0]['mobile_no'];
		document.getElementById('comm').value=data[0]['commission'];
		document.getElementById('tds').value=data[0]['tds'];
		document.getElementById('datepicker').value=data[0]['joining_date'];
		document.getElementById('refferedby').value=data[0]['ref_by'];
		document.getElementById('sarcharges').value=data[0]['sar_charge'];
		document.getElementById('edu').value=data[0]['edu_ses'];
		document.getElementById('pancard').value=data[0]['pan_card'];
		document.getElementById('savingacno').value=data[0]['saving_account_no'];
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{
		//alert(arr1[1]);
		var pp = arr1[1].split("****");
		//alert(pp[1]);
		if(pp[1]=="self")
		{
			//alert("insideif");
			window.location.href ="agents_view.php";
		}
		else
		{
			//alert("insideelse");
			window.location.href ="agents_view.php";
		}
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

function val()
{
	//alert("call");	
	var accounttype = trim(document.getElementById('accounttype').value);	
	var agentname = trim(document.getElementById('agentname').value);
	var subglcode = trim(document.getElementById('subglcode').value);
	var age = trim(document.getElementById('age').value);
	var qualification = trim(document.getElementById('qualification').value);	
	var mob = trim(document.getElementById('mob').value);
	var comm = trim(document.getElementById('comm').value);
	var tds = trim(document.getElementById('tds').value);
	var refferedby = trim(document.getElementById('refferedby').value);
	var pancard = trim(document.getElementById('pancard').value);
	var savingacno = trim(document.getElementById('savingacno').value);

	if(accounttype=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Select the account type";
		document.getElementById("accounttype").focus();		
		go_error();
		return false;
	}
	if(agentname=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Select the agent name";
		document.getElementById("agentname").focus();		
		go_error();
		return false;
	}
	if(subglcode=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter subglcode code";
		document.getElementById("subglcode").focus();		
		go_error();
		return false;
	}
	if(age=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter the age";
		document.getElementById("age").focus();		
		go_error();
		return false;
	}
	if(qualification=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter the qualification";
		document.getElementById("qualification").focus();		
		go_error();
		return false;
	}
	if(mob=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter the mobile no";
		document.getElementById("mob").focus();		
		go_error();
		return false;
	}else if(isNaN(mob))
    {
		document.getElementById('error').style.display = '';
		document.getElementById("error").innerHTML = "Enter digits only";
		document.getElementById("mob").focus();	
		go_error();
		return false;
    }else if(mob.length!=10)
    {
		document.getElementById('error').style.display = '';
		document.getElementById("error").innerHTML = "Enter the 10 digit mobile no";
		document.getElementById("mob").focus();	
        go_error();
		return false;
        }
	if(comm=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter the percentage of commission";
		document.getElementById("comm").focus();		
		go_error();
		return false;
	}else if(isNaN(comm))
    {
		document.getElementById('error').style.display = '';
		document.getElementById("error").innerHTML = "Enter digits only";
		document.getElementById("comm").focus();	
		go_error();
		return false;
	}
	if(tds=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter the percentage of TDS";
		document.getElementById("tds").focus();		
		go_error();
		return false;
	}else if(isNaN(tds))
    {
		document.getElementById('error').style.display = '';
		document.getElementById("error").innerHTML = "Enter digits only";
		document.getElementById("tds").focus();	
		go_error();
		return false;
	}
	if(refferedby=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter the reference name";
		document.getElementById("refferedby").focus();		
		go_error();
		return false;
	}
	if(pancard=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter the pan card no";
		document.getElementById("pancard").focus();		
		go_error();
		return false;
	}
	if(savingacno=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter the saving account no";
		document.getElementById("savingacno").focus();		
		go_error();
		return false;
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