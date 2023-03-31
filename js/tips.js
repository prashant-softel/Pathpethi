function val()
{
var subject=trim(document.getElementById('subject').value);
//var dashboard=trim(document.getElementById('type').value);
var desp=CKEDITOR.instances['desc'].getData();	


if(subject=="")
	{	
		document.getElementById('error').innerHTML = "Please Enter Subject";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('subject').focus();
		go_error();
		return false;
	}		
	/*if(dashboard=="")
	{	
		document.getElementById('error').innerHTML = "Please Select Dashboar Type";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('type').focus();
		go_error();
		return false;
	}*/		
if(!(desp.length > 0))
	{	//alert('7');		
		document.getElementById('error').innerHTML = "Please Enter Subject of Description";
		alert(document.getElementById('error').innerHTML);
		//go_error();
		return false;
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
	}
	}

function gettips(str)
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

			remoteCall("../ajax/tips.ajax.php","form=tips&method="+iden[0]+"&tipsId="+iden[1],"loadchanges");
		}
	}
	else
	{
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
			document.getElementById("error").innerHTML = "Please Wait...";
		});

		remoteCall("../ajax/tips.ajax.php","form=tips&method="+iden[0]+"&tipsId="+iden[1],"loadchanges");
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

		document.getElementById('title').value=arr2[1];
		document.getElementById('date').value=arr2[2];
		document.getElementById('end_date').value=arr2[3];
		document.getElementById('massege').value=arr2[4];
		document.getElementById('url').value=arr2[5];
		//document.getElementById("training_type").checked= (arr2[5] == 1) ? true : false;
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

		window.location.href ="../tips.php?mst&"+arr1[1]+"&mm";
	}
}


function getNewTips(str)
{ //alert("hi");	
	var iden=new Array();
	iden=str.split("-");		
	//alert(iden);
	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{			
			remoteCall("ajax/tips.ajax.php","&method="+iden[0]+"&Id="+iden[1],"loadchanges");
		}
	}
	else
	{
		remoteCall("ajax/tips.ajax.php","&method="+iden[0]+"&Id="+iden[1],"loadchanges");			
	}
}

function loadchanges()
{	
	var a		= sResponse.trim();			
	var arr1	= new Array();
	var arr2	= new Array();
	arr1		= a.split("@@@");
	arr2		= arr1[1].split("#");		
	//alert(arr2);		
	if(arr1[0] == "edit")
	{		
		document.getElementById("tips_id").value=arr2[0];
		document.getElementById('subject').value=arr2[1];
		document.getElementById('type').value=arr2[2];
		document.getElementById('date').value=arr2[3];
		CKEDITOR.instances['desc'].setData(arr2[4]);
		document.getElementById('url').value=arr2[5];
		//document.getElementById("training_type").checked= (arr2[5] == 1) ? true : false;
		//CKEDITOR.instances['details'].setData(arr2[8]);
		document.getElementById("insert").value = "Update";	
		//CKEDITOR.instances.description.setData(arr2[5]);		
		//window.location.href="poll_list.php";																		
	}
	else if(arr1[0] == "delete")
	{		
		//window.location.href ="../notices.php?mst&"+arr1[1]+"&mm";		
		window.location.href ="view_tips.php";
	}
}