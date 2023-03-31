// JavaScript Document
function val()
{
//alert('val');	
var issueby=trim(document.getElementById('issueby').value);
var subject=trim(document.getElementById('subject').value);
var description=trim(document.getElementById('description').value);
var post_date=trim(document.getElementById('post_date').value);
var exp_date=trim(document.getElementById('exp_date').value);
var creation_type=document.getElementById('notice_creation_type').value;
var uploaded_fileName = document.getElementById('userfile').value;
var submit_type = document.getElementById('insert').value;
//alert( creation_type + uploaded_fileName );
   if(issueby=="")
	{		
		document.getElementById('error').innerHTML = "Enter Issue By";
		alert(document.getElementById('error').innerHTML);
		//document.getElementById('error').style.color = '#FF0000';
		//setTimeout('timeout(error)', 6000);
		//document.getElementById('issueby').focus();
		//alert("2");
		return false;
	}				
	
	if(subject=="")
	{		
		document.getElementById('error').innerHTML = "Enter Notice Subject";
		alert(document.getElementById('error').innerHTML);
		//document.getElementById('error').style.color = '#FF0000';
		//setTimeout('timeout(error)', 6000);
		//document.getElementById('subject').focus();
		return false;
	}			
	
	/*if(description=="")
	{
		//alert('description');
		document.getElementById('error').innerHTML = "Enter Description";
		document.getElementById('error').style.color = '#FF0000';
		setTimeout('timeout(error)', 6000);
		document.getElementById('description').focus();
		return false;
	}	*/
	
	if(post_date=="")
	{		
		document.getElementById('error').innerHTML = "Enter Post Date";
		alert(document.getElementById('error').innerHTML);
		//document.getElementById('error').style.color = '#FF0000';
		//setTimeout('timeout(error)', 6000);
		//document.getElementById('post_date').focus();
		return false;
	}	
	
	/*if(exp_date=="" && exp_date!="00-00-0000")
	{		
		document.getElementById('error').innerHTML = "Enter Expiry Date";
		alert(document.getElementById('error').innerHTML);
		//document.getElementById('error').style.color = '#FF0000';
		//setTimeout('timeout(error)', 6000);
		//document.getElementById('exp_date').focus();
		return false;
	}*/						
	
	if(creation_type == 0)
	{		
		document.getElementById('error').innerHTML = "Please select Notice Creation Type";
		alert(document.getElementById('error').innerHTML);	
		//document.getElementById('error').style.color = '#FF0000';
		//setTimeout('timeout(error)', 6000);
		//document.getElementById('notice_creation_type').focus();
		return false;
	}
	
	if(submit_type == "Submit")
	{				
		/*if(creation_type == 2 && uploaded_fileName == "")
		{		
			document.getElementById('error').innerHTML = "Please select file to upload, by clicking on browse button";
			alert(document.getElementById('error').innerHTML);
			//document.getElementById('error').style.color = '#FF0000';
			//setTimeout('timeout(error)', 6000);
			//document.getElementById('userfile').focus();
			return false;
		}*/
	}
	$('input[type=submit]').click(function(){
    $(this).attr('disabled', 'disabled');
});
	
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
	//return value.trim();
	}	
}

function timeout(field)
{
	document.getElementById('error').innerHTML = "";
}

function getNotice(str)
{	
	var iden=new Array();
	iden=str.split("-");		
	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{			
			remoteCall("ajax/ajaxnotice.ajax.php","&method="+iden[0]+"&noticeId="+iden[1],"loadchanges");
		}
	}
	else
	{
		remoteCall("ajax/ajaxnotice.ajax.php","&method="+iden[0]+"&noticeId="+iden[1],"loadchanges");			
	}
}

function loadchanges()
{	
	var arResponse		=  sResponse.trim();
	
	var arr1	= new Array();
	//var arr2	= new Array();
	arr1		= arResponse.split("@@@");
	//arr2		= arr1[1].split("#");				
	if(arr1[0] ==  "edit")
	{	
		//alert(arr1);
		var arResponse		=  JSON.parse(arr1[1].trim());	
		document.getElementById("issueby").value = arResponse["issuedby"];
		document.getElementById("subject").value = arResponse["subject"];	
		//alert(arResponse["note"]);
		if(arResponse["note"].length)
		{					
			setTimeout(function() { pausecomp(2, arResponse["description"] ); }, (2 * 1000));	
			document.getElementById("notice_creation_type").value = 2;			
			//EnableNoticeType(0);
			document.getElementById("noticename").style.visibility = 'visible';
			//alert(arResponse["attachment_gdrive_id"]);
			if(arResponse["attachment_gdrive_id"] == "" || arResponse["attachment_gdrive_id"] == "-")
			{
				document.getElementById("noticename").href = "http://way2society.com/Notices/" +arResponse["note"];			
			}
			else
			{
				document.getElementById("noticename").href =  "https://drive.google.com/file/d/"+ arResponse["attachment_gdrive_id"] + "/view";;
			}
		}
		else
		{
			setTimeout(function() { pausecomp(1, arResponse["description"]); }, (2 * 1000));
			document.getElementById("notice_creation_type").value = 2;		//changed from 1 for gdrive
			//EnableNoticeType(1);
		}									
		//CKEDITOR.instances.description.setData(arr2[5]);	
		//alert(arResponse["doc_id"]);			
		
		//alert(arResponse["exp_date"]);
		document.getElementById("post_date").value = arResponse["post_date"];
		if(arResponse["exp_date"] == "00-00-0000")
		{
			document.getElementById("exp_date").value = "";	
		}
		else
		{
			document.getElementById("exp_date").value = arResponse["exp_date"];
		}
		if(arResponse["doc_id"] != 0)
		{
			document.getElementById("notice_type").value = arResponse["doc_id"];
		}	
		else
		{
			document.getElementById("notice_type").value =	4;
		}
		
		if(arResponse["doc_template_id"] != 0)
		{
			document.getElementById("notice_template").value = arResponse["doc_template_id"];
		}
		else
		{
			document.getElementById("notice_template").selectedIndex =	0;
		}
		
		var units = Array();
		units = arResponse["unit"].split(",");					
		var s = document.getElementById("post_noticeto");			
		s.options[0].selected = false;
		for ( var k = 0; k < s.options.length; k++ )
		{																												
			if(units.indexOf(s.options[k].value) >= 0)
			{																												
				s.options[k].selected = true;																							
			}										
		}
		document.getElementById("notify").checked = (arResponse["isNotify"] == 1)? true : false;
		document.getElementById("insert").value = "Update";	
		//CKEDITOR.instances.description.setData(arr2[5]);																				
	}
	else if(arr1[1] == "Data Deleted Successfully")
	{		
		//window.location.href ="../notices.php?mst&"+arr1[1]+"&mm";		
		window.location.href ="notices.php";
	}
}

function pausecomp(val, text)
{		
  	//EnableNoticeType(val);
	CKEDITOR.instances.description.setData(text);
}