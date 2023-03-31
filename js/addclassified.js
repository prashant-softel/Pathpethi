function val()
{ 
//alert('val');	
//return false;
var cat_type=trim(document.getElementById('cat_id').value);
//alert('cat');
var ad_title=trim(document.getElementById('ad_title').value);
//alert('title');
var location=trim(document.getElementById('location').value);
//alert('loca');
var phone=trim(document.getElementById('phone').value);
//alert('phone');
var email=trim(document.getElementById('email').value);
//alert('email');
var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;  
	
var exp_date=trim(document.getElementById('exp_date').value);
//alert('exp');

var desp=CKEDITOR.instances['desp'].getData();	
	
var submit_type = document.getElementById('insert').value;

if(cat_type=="")
	{	
		document.getElementById('error').innerHTML = "Please Select Categories";
		alert(document.getElementById('error').innerHTML);
		//document.getElementById('error').style.color = '#FF0000';
		//setTimeout('timeout(error)', 6000);
		document.getElementById('cat_id').focus();
		go_error();
		return false;
	}		
   if(ad_title=="")
	{	//alert('6');		
		document.getElementById('error').innerHTML = "Please Provide Classified Title";
			
		alert(document.getElementById('error').innerHTML);
		//document.getElementById('error').style.color = '#FF0000';
		//setTimeout('timeout(error)', 6000);
		document.getElementById('ad_title').focus();
		go_error();
		//alert("2");
		return false;
	}				
	
	if(location=="")
	{	
		document.getElementById('error').innerHTML = "Please Provide Location";
		alert(document.getElementById('error').innerHTML);
		//document.getElementById('error').style.color = '#FF0000';
		//setTimeout('timeout(error)', 6000);
		document.getElementById('location').focus();
		go_error();
		return false;
	}		
	if(phone=="")
	{	
		document.getElementById('error').innerHTML = "Please Provide Contact Number";
		alert(document.getElementById('error').innerHTML);
		//document.getElementById('error').style.color = '#FF0000';
		//setTimeout('timeout(error)', 6000);
		document.getElementById('phone').focus();
		go_error();
		return false;
	}				
	if(email=="")
	{	
		document.getElementById('error').innerHTML = "Please Provide Email Address";
		alert(document.getElementById('error').innerHTML);
		//document.getElementById('error').style.color = '#FF0000';
		//setTimeout('timeout(error)', 6000);
		document.getElementById('email').focus();
		go_error();
		return false;
	}			
	else if(!document.getElementById("email").value.match(mailformat))  
			{  
				document.getElementById('error').innerHTML = "Please Provide Valid Email Address";
		alert(document.getElementById('error').innerHTML);
		//document.getElementById('error').style.color = '#FF0000';
		//setTimeout('timeout(error)', 6000);
		document.getElementById('email').focus();
		go_error();
		return false;
			}  
	if(exp_date=="")
	{		
		document.getElementById('error').innerHTML = "Please Provide Expiry Date";
		alert(document.getElementById('error').innerHTML);
		//document.getElementById('error').style.color = '#FF0000';
		//setTimeout('timeout(error)', 6000);
		document.getElementById('exp_date').focus();
		go_error();
		return false;
	}	
	
	if(!(desp.length > 0))
	{	//alert('7');		
		document.getElementById('error').innerHTML = "Please Provide Description";
		alert(document.getElementById('error').innerHTML);
		//document.getElementById('error').style.color = '#FF0000';
		//setTimeout('timeout(error)', 6000);
		//document.getElementById('desp').focus();
		//go_error();
		return false;
	}						
	
	
	
	if(submit_type == "Submit")
	{				
		if(creation_type == 2 && uploaded_fileName == "")
		{		
			document.getElementById('error').innerHTML = "Please select file to upload, by clicking on browse button";
			alert(document.getElementById('error').innerHTML);
			//document.getElementById('error').style.color = '#FF0000';
			//setTimeout('timeout(error)', 6000);
			//document.getElementById('userfile').focus();
			return false;
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

function statusaproved(id)
{ //alert ("aproved");
//alert (id);
	var status= id.split("_");
	alert ("Are you sure , you want to Aprove it ?");

$.ajax({
				
			url : "ajax/ajaxclassified.php",
			type : "POST",
			data : {"method" : 'aprove',"id" :status[1]},
			success : function(data)
			{	
			//tabBtnClicked('view.php?id=' + album_id + '&photo=1');	
				location.reload(true);	
			},
		});
		
}
//////////////////////////////////////////////////////////

function getclassified(str)
{
	//alert(str);
	var iden=new Array();
	iden=str.split("-");  
//alert(iden);
	if(iden[0]=="delete")
	{
		
		var d=confirm("Are you sure , you want to delete it ???");
		if(d==1)
		{
			remoteCall("ajax/ajaxclassified.php","form=classified&method="+iden[0]+"&id="+iden[1],"loadchanges");
		}
	}
	
	else (iden[0]=="edit")
	{
		//alert(str);
		// $("id").append('')
		window.location.href='addclassified.php?edt&id='+iden[1];
		
		//remoteCall("ajax/ajaxclassified.php","form=classified&method="+iden[0]+"&id="+iden[1],"loadchanges");
	}
}

function loadchanges()
{
	//alert(sResponse.trim());
	var a		= sResponse.trim();	
	var arr1	= new Array();
	var arr2	= new Array();	
	arr1		= a.split("@@@");
	arr2		= arr1[1].split("#");
	//alert(arr2);

	if(arr1[0] == "edit")
	{		
		//alert(arr2[1]);
		//document.getElementById('post_by').style.display ='none';
		document.getElementById("id").value=arr2[0];
		document.getElementById('cat_id').value=arr2[1];
		document.getElementById('ad_title').value=arr2[2];
		document.getElementById('location').value=arr2[3];
		document.getElementById('phone').value=arr2[4];
		document.getElementById('email').value=arr2[5];
		document.getElementById('act_date').value=arr2[6];
		document.getElementById('exp_date').value=arr2[7];
		//document.getElementById('desp').value=arr2[9];
		CKEDITOR.instances['desp'].setData(arr2[8]);
		document.getElementById('post_by').innerHTML=arr2[9];
		//alert(arr2[9]);
		//CKEDITOR.instances.desp.setData(arr2[9]);
		
		//getsociety_list(arr2[0]);
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{		
		window.location.href ="my_listing_classified.php";
	}
}

function geteditclassified(str)
{
	//alert(str);
	var iden=new Array();
	iden=str.split("-");

	remoteCall("ajax/ajaxclassified.php","form=classified&method="+iden[0]+"&id="+iden[1],"loadchanges1");

}

function loadchanges1()
{
	//alert(sResponse.trim());
	var a		= sResponse.trim();	
	var arr1	= new Array();
	var arr2	= new Array();	
	arr1		= a.split("@@@");
	arr2		= arr1[1].split("####");
	//alert(arr2);

	if(arr1[0] == "edit")
	{		
		//alert(arr2[1]);
		//document.getElementById('post_by').style.display ='none';
		document.getElementById("id").value=arr2[0];
		document.getElementById('cat_id').value=arr2[1];
		document.getElementById('ad_title').value=arr2[2];
		document.getElementById('location').value=arr2[3];
		document.getElementById('phone').value=arr2[4];
		document.getElementById('email').value=arr2[5];
		document.getElementById('act_date').value=arr2[6];
		document.getElementById('exp_date').value=arr2[7];
		//document.getElementById('desp').value=arr2[9];
		CKEDITOR.instances['desp'].setData(arr2[8]);
		document.getElementById("varified").checked= (arr2[11] == 'Y') ? true : false;
		document.getElementById('post_by').innerHTML=arr2[10];
		
		//alert(arr2[11]);
		//CKEDITOR.instances.desp.setData(arr2[9]);
		
		//getsociety_list(arr2[0]);
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{		
		window.location.href ="my_listing_classified.php";
	}
} 


function del_photo(img,id)
	{
		
		//alert(id);
		//alert(img);
		if(id!="")
		{
			var con = confirm("Are you sure,You want to delete the images ?");
			
			if(con==true)
			{	
				//document.getElementById('error_home_page').style.display = '';	
				document.getElementById("error").innerHTML = "Please Wait...";
				
				remoteCall("ajax/ajaxclassified.php","form=classified&method=del_photo&id="+id+"&img="+img,"deletedImage");	
			}
		}	
	}
function deletedImage(img,id)
{
	//alert("image deleted");
	//window.reload();
	location.reload();
}
function blockNonNumbers(obj, e, allowDecimal, allowNegative)
{
	var key;
	var isCtrl = false;
	var keychar;
	var reg;
		
	if(window.event) {
		key = e.keyCode;
		isCtrl = window.event.ctrlKey
	}
	else if(e.which) {
		key = e.which;
		isCtrl = e.ctrlKey;
	}
	
	if (isNaN(key)) return true;
	
	keychar = String.fromCharCode(key);
	
	// check for backspace or delete, or if Ctrl was pressed
	if (key == 8 || isCtrl)
	{
		return true;
	}

	reg = /\d/;
	var isFirstN = allowNegative ? keychar == '-' && obj.value.indexOf('-') == -1 : false;
	var isFirstD = allowDecimal ? keychar == '.' && obj.value.indexOf('.') == -1 : false;
	
	return isFirstN || isFirstD || reg.test(keychar);
}
