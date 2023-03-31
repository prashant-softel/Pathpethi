function val()
{ 
//alert('val');	
//return false;
var ad_title=trim(document.getElementById('ad_title').value);
//alert('1');	
var cat_type=trim(document.getElementById('cat_type').value);
//alert('2');	
var exp_date=trim(document.getElementById('exp_date').value);
//alert('3');	
var desp=document.getElementById('desp').value;
//alert('4');	
var submit_type = document.getElementById('insert').value;
//alert('5');	
   if(ad_title=="")
	{	//alert('6');		
		document.getElementById('error').innerHTML = "Enter Post Title";
			
		alert(document.getElementById('error').innerHTML);
		//document.getElementById('error').style.color = '#FF0000';
		//setTimeout('timeout(error)', 6000);
		document.getElementById('ad_title').focus();
		go_error();
		//alert("2");
		return false;
	}				
	
	if(cat_type=="")
	{	
		document.getElementById('error').innerHTML = "Please Select Categories";
		alert(document.getElementById('error').innerHTML);
		//document.getElementById('error').style.color = '#FF0000';
		//setTimeout('timeout(error)', 6000);
		document.getElementById('cat_type').focus();
		go_error();
		return false;
	}			
	
	if(exp_date=="")
	{		
		document.getElementById('error').innerHTML = "Enter Expiry Date";
		alert(document.getElementById('error').innerHTML);
		//document.getElementById('error').style.color = '#FF0000';
		//setTimeout('timeout(error)', 6000);
		document.getElementById('exp_date').focus();
		go_error();
		return false;
	}	
	
	if(desp=="")
	{	//alert('7');		
		document.getElementById('error').innerHTML = "Enter Post Description";
		alert(document.getElementById('error').innerHTML);
		//document.getElementById('error').style.color = '#FF0000';
		//setTimeout('timeout(error)', 6000);
		document.getElementById('desp').focus();
		go_error();
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
function getclassified(str)
{
	var iden	= new Array();
	iden		= str.split("-");

	$('html, body').animate({ scrollTop: $('#top').offset().top }, 300);

	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{
			$(document).ready(function()
			{
				$("#error").fadeIn("slow");
				document.getElementById("error").innerHTML = "Please Wait...";
			});

			remoteCall("../ajax/addclassified.ajax.php","form=classified&method="+iden[0]+"&classifiedId="+iden[1],"loadchanges");
		}
	}
	else
	{
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
			document.getElementById("error").innerHTML = "Please Wait...";
		});

		remoteCall("../ajax/addclassified.ajax.php","form=classified&method="+iden[0]+"&classifiedId="+iden[1],"loadchanges");
	}
}

function loadchanges()
{
	var a		= sResponse.trim();
	var arr1	= new Array();
	var arr2	= new Array();
	arr1		= a.split("@@@");
	arr2		= arr1[1].split("#");


	if(arr1[0] == "edit")
	{
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
			document.getElementById("error").innerHTML = "Please Wait...";
		});

		document.getElementById('ad_title').value=arr2[1];
		document.getElementById('desp').value=arr2[2];
		document.getElementById('post_date').value=arr2[3];
		document.getElementById('exp_date').value=arr2[4];
		document.getElementById('img').value=arr2[5];
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
			document.getElementById("error").innerHTML = "Please Wait...";
		});

		window.location.href ="../addclassified.php?mst&"+arr1[1]+"&mm";
	}
}
