function getservice_prd_reg(str)
{
	var iden=new Array();
	iden=str.split("-");

	if(iden[0]=="delete")
	{
		var d=confirm("Are you sure , you want to delete it ???");
		if(d==1)
		{
			remoteCall("ajax/ajaxservice_prd_reg.php","form=service_prd_reg&method="+iden[0]+"&service_prd_regId="+iden[1],"loadchanges");
		}
	}
	else
	{
			remoteCall("ajax/ajaxservice_prd_reg.php","form=service_prd_reg&method="+iden[0]+"&service_prd_regId="+iden[1],"loadchanges");
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
		document.getElementById('full_name').value=arr2[1];
		document.getElementById('photo').value=arr2[2];
		document.getElementById('cat_id').value=arr2[3];
		document.getElementById('age').value=arr2[4];
		document.getElementById('dob').value=arr2[5];
		document.getElementById('identy_mark').value=arr2[6];
		document.getElementById('cur_resd_add').value=arr2[7];
		document.getElementById('cur_con_1').value=arr2[8];
		document.getElementById('cur_con_2').value=arr2[9];
		document.getElementById('native_add').value=arr2[10];
		document.getElementById('native_con_1').value=arr2[11];
		document.getElementById('native_con_2').value=arr2[12];
		
		document.getElementById('ref_name').value=arr2[13];
		document.getElementById('ref_add').value=arr2[14];
		document.getElementById('ref_con_1').value=arr2[15];
		document.getElementById('ref_con_2').value=arr2[16];
		
		document.getElementById('since').value=arr2[17];
		document.getElementById('education').value=arr2[18];
		document.getElementById('marry').value=arr2[19];
		document.getElementById('father_name').value=arr2[20];
		document.getElementById('father_occ').value=arr2[21];
		document.getElementById('mother_name').value=arr2[22];
		document.getElementById('mother_occ').value=arr2[23];
		document.getElementById('hus_wife_name').value=arr2[24];
		document.getElementById('hus_wife_occ').value=arr2[25];
		document.getElementById('son_dou_name').value=arr2[26];
		document.getElementById('son_dou_occ').value=arr2[27];
		document.getElementById('other_name').value=arr2[28];
		document.getElementById('other_occ').value=arr2[29];
		document.getElementById('document').value=arr2[30];
		document.getElementById("id").value=arr2[0];
		
		document.getElementById('ref_name').value=arr2[31];
		document.getElementById('ref_add').value=arr2[32];
		document.getElementById('ref_con_1').value=arr2[33];
		document.getElementById('ref_con_2').value=arr2[34];
		
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{
		window.location.href ="service_prd_reg_view.php?del&id=14748&konid=22&srm";
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

function service_prd_reg_edit(str)
{
	window.location.href = "service_prd_reg_edit.php?id="+str+"&tokufj=475958&srm";
}

function val()
{
	//////////////////////////////////////////////////////////////////////////////////////////
	var full_name = trim(document.getElementById("full_name").value);	
	var photo = trim(document.getElementById("photo").value);	
	
	var cnt_cat_id = document.getElementById("count_cat_id").value;
	
	//var age = trim(document.getElementById("age").value);	
	var dob = trim(document.getElementById("dob").value);	
	var identy_mark = trim(document.getElementById("identy_mark").value);
		
	var cur_resd_add = trim(document.getElementById("cur_resd_add").value);	
	var cur_con_1 = trim(document.getElementById("cur_con_1").value);	
	var cur_con_2 = trim(document.getElementById("cur_con_2").value);	
	
	//var native_add = trim(document.getElementById("native_add").value);	
	//var native_con_1 = trim(document.getElementById("native_con_1").value);	
	//var native_con_2 = trim(document.getElementById("native_con_2").value);	
	
	var ref_name = trim(document.getElementById("ref_name").value);	
	var ref_add = trim(document.getElementById("ref_add").value);	
	var ref_con_1 = trim(document.getElementById("ref_con_1").value);	
	//var ref_con_2 = trim(document.getElementById("ref_con_2").value);	
	
	var since = trim(document.getElementById("since").value);	
	var education = trim(document.getElementById("education").value);	
	
	//var cnt_document = document.getElementById("count_document").value;	
	//var units = document.getElementById("selectedUnits").length;
	//alert(units);
	//////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	
		
	//////////////////////////////////////////////////////////////////////////////////////////
	if(full_name=="")
	{
		//alert("full_name");
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Enter Full Name";
		document.getElementById("full_name").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(photo=="")
	{
		//alert("photo");
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Upload photo";
		
		go_error();
		return false;
	}*/	
	//////////////////////////////////////////////////////////////////////////////////////////
	for(var i1=0;i1<=cnt_cat_id-1;i1++)
	{
		if(document.getElementById('cat_id'+i1).checked==false)
		{
			var ttt1 = "ppp";
		}
		else
		{
			var kkk1 = 1;
		}
	}
	
	if(kkk1!=1)
	{
		document.getElementById('error').style.display = '';
		document.getElementById('error').innerHTML = "Please Select Atleast One Category";
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	/*
	if(age=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Enter Age";
		document.getElementById("age").focus();
		
		go_error();
		return false;
	}
	*/
	//////////////////////////////////////////////////////////////////////////////////////////
	if(dob=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Enter Date of Birth";
		document.getElementById("dob").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(identy_mark=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Enter Identification Marks";
		document.getElementById("identy_mark").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(cur_resd_add=="")
	{
		window.location.href = '#';
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Enter Current Address";
		document.getElementById("cur_resd_add").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(cur_con_1=="")
	{
		window.location.href = '#';
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Enter Contact No.1";
		document.getElementById("cur_con_1").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(cur_con_2=="")
	{
		window.location.href = '#';
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Enter Contact No.2";
		document.getElementById("cur_con_2").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(native_add=="")
	{
		window.location.href = '#';
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Enter Permanent/Native Address";
		document.getElementById("native_add").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(native_con_1=="")
	{
		window.location.href = '#';
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Enter Permanent/Native Contact No.1";
		document.getElementById("native_con_1").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(native_con_2=="")
	{
		window.location.href = '#';
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter permanent / native Contact No.2";
		document.getElementById("native_con_2").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	if(ref_name=="")
	{
		window.location.href = '#';
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Enter Refrence Name";
		document.getElementById("ref_name").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(ref_add=="")
	{
		window.location.href = '#';
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Enter Reference Address";
		document.getElementById("ref_add").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(ref_con_1=="")
	{
		window.location.href = '#';
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Enter Reference Contact No.1 ";
		document.getElementById("ref_con_1").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(ref_con_2=="")
	{
		window.location.href = '#';
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter reference contact No.2 ";
		document.getElementById("ref_con_2").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	if(since=="")
	{
		window.location.href = '#';
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Working In Society Since field should not be blank";
		document.getElementById("since").focus();
		
		go_error();
		return false;
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(education=="")
	{
		window.location.href = '#';
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter your education";
		document.getElementById("education").focus();
		
		go_error();
		return false;
	}	*/
	//////////////////////////////////////////////////////////////////////////////////////////
	/*for(var i2=0;i2<=cnt_document-1;i2++)
	{
		if(document.getElementById('document'+i2).checked==false)
		{
			var ttt2 = "ppp";
		}
		else
		{
			var kkk2 = 1;
		}
	}
	
	if(kkk2!=1)
	{
		document.getElementById('error').style.display = '';
		document.getElementById('error').innerHTML = "Please Click Atleast one Document attached";
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	//////////////////////////////////////////////////////////////////////////////////////////
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
	//////////////////////////////////////////////////////////////////////////////////////////
}



/*
function val_edit()
{
	//////////////////////////////////////////////////////////////////////////////////////////
	var full_name = trim(document.getElementById("full_name").value);	
	
	var cnt_cat_id = document.getElementById("count_cat_id").value;
	
	var dob = trim(document.getElementById("dob").value);	
	var identy_mark = trim(document.getElementById("identy_mark").value);
		
	var cur_resd_add = trim(document.getElementById("cur_resd_add").value);	
	var cur_con_1 = trim(document.getElementById("cur_con_1").value);	
	var cur_con_2 = trim(document.getElementById("cur_con_2").value);	
	
	var native_add = trim(document.getElementById("native_add").value);	
	var native_con_1 = trim(document.getElementById("native_con_1").value);	
	var native_con_2 = trim(document.getElementById("native_con_2").value);	
	
	var ref_name = trim(document.getElementById("ref_name").value);	
	var ref_add = trim(document.getElementById("ref_add").value);	
	var ref_con_1 = trim(document.getElementById("ref_con_1").value);	
	var ref_con_2 = trim(document.getElementById("ref_con_2").value);	
	
	var since = trim(document.getElementById("since").value);	
	var education = trim(document.getElementById("education").value);	
	
	var cnt_document = document.getElementById("count_document").value;
	//////////////////////////////////////////////////////////////////////////////////////////
	
	
	
	
		
	//////////////////////////////////////////////////////////////////////////////////////////
	if(full_name=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Enter Full Name";
		document.getElementById("full_name").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	for(var i1=0;i1<=cnt_cat_id-1;i1++)
	{
		if(document.getElementById('cat_id'+i1).checked==false)
		{
			var ttt1 = "ppp";
		}
		else
		{
			var kkk1 = 1;
		}
	}
	
	if(kkk1!=1)
	{
		document.getElementById('error').style.display = '';
		document.getElementById('error').innerHTML = "Please Click Atleast one Category";
		return false;
	}

	if(dob=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Enter Date of Birth";
		document.getElementById("dob").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(identy_mark=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Enter Identification Marks";
		document.getElementById("identy_mark").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(cur_resd_add=="")
	{
		window.location.href = '#';
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Enter Current Residence Address";
		document.getElementById("cur_resd_add").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(cur_con_1=="")
	{
		window.location.href = '#';
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Enter Contact No.1";
		document.getElementById("cur_con_1").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////////////////////////////////////
	if(native_add=="")
	{
		window.location.href = '#';
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter permanent / native address";
		document.getElementById("native_add").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(native_con_1=="")
	{
		window.location.href = '#';
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter permanent / native Contact No.1";
		document.getElementById("native_con_1").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	
	//////////////////////////////////////////////////////////////////////////////////////////
	if(ref_name=="")
	{
		window.location.href = '#';
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter refrence name";
		document.getElementById("ref_name").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(ref_add=="")
	{
		window.location.href = '#';
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter reference address";
		document.getElementById("ref_add").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	if(ref_con_1=="")
	{
		window.location.href = '#';
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter reference contact No.1 ";
		document.getElementById("ref_con_1").focus();
		
		go_error();
		return false;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	
	//////////////////////////////////////////////////////////////////////////////////////////
	if(since=="")
	{
		window.location.href = '#';
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Working in Acme Since field should not be blank";
		document.getElementById("since").focus();
		
		go_error();
		return false;
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////
	if(education=="")
	{
		window.location.href = '#';
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please enter your education";
		document.getElementById("education").focus();
		
		go_error();
		return false;
	}	
	//////////////////////////////////////////////////////////////////////////////////////////
	for(var i2=0;i2<=cnt_document-1;i2++)
	{
		if(document.getElementById('document'+i2).checked==false)
		{
			var ttt2 = "ppp";
		}
		else
		{
			var kkk2 = 1;
		}
	}
	
	if(kkk2!=1)
	{
		document.getElementById('error').style.display = '';
		document.getElementById('error').innerHTML = "Please Click Atleast one Document attached";
		return false;
	}
	
}*/

function statusaproved(id)
{
	var txt;
    if (confirm("Are You sure to Aprove") == true) {
      
   
	var status= id.split("_");
	//alert (status);
	alert ("Are you sure , you want to Aprove it ?");

$.ajax({
				
			url : "ajax/ajaxservice_prd_reg.php",
			type : "POST",
			data : {"method" : 'aprove',"id" :status[1]},
			success : function(data)
			{	
			//tabBtnClicked('view.php?id=' + album_id + '&photo=1');	
				location.reload(true);	
			},
		});
	} 
	else {
      
		return false;
    	}
		
}
function del_Doc(Doc,sprId)
	{
		
//alert(id);
		//alert(Doc);
		//alert(sprId);
		if(sprId!="")
		{
			var con = confirm("Are you sure,You want to delete the images ?");
			
			if(con==true)
			{	
				//document.getElementById('error_home_page').style.display = '';	
				document.getElementById("error").innerHTML = "Please Wait...";
				
				remoteCall("ajax/ajaxservice_prd_reg.php","form=form=service_prd_reg&method=del_Doc&sprId="+sprId+"&Doc="+Doc,"deletedDocument");	
			}
		}	
	}
function deletedDocument(Doc,sprId)
{
//alert("image deleted");
//window.reload();
location.reload();
}