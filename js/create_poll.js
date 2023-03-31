function val()
{
var group=trim(document.getElementById('group').value);
var question=trim(document.getElementById('question').value);
var vote_option =trim(document.getElementById('poll_options').value);
var startDate=trim(document.getElementById('start_date').value);
var endDate=trim(document.getElementById('end_date').value);
var submit_type = document.getElementById('insert').value;

if(group=="")
	{	
		document.getElementById('error').innerHTML = "Please Select Group";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('group').focus();
		go_error();
		return false;
	}		
   if(question=="")
	{	//alert('6');		
		document.getElementById('error').innerHTML = "Please Provide Questions";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('question').focus();
		go_error();
		return false;
	}
	if(vote_option=="")
	{	
		document.getElementById('error').innerHTML = "Please Provide Options";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('poll_options').focus();
		go_error();
		return false;
	}	
	else if(vote_option ==data[0])
	{
		document.getElementById('error').innerHTML = "Please Provide Second Options";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('poll_options').focus();
		go_error();
		return false;
	}
	if(startDate=="")
	{	
		document.getElementById('error').innerHTML = "Please Provide Voting Date of Start";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('start_date').focus();
		go_error();
		return false;
	}	
	if(endDate=="")
	{	
		document.getElementById('error').innerHTML = "Please Provide Voting Date of Closing";
		alert(document.getElementById('error').innerHTML);
		document.getElementById('end_date').focus();
		go_error();
		return false;
	}	

	/*if(submit_type == "Submit")
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
	*/
		
	
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



function getcreate_poll(str)
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

			remoteCall("../ajax/create_poll.ajax.php","form=create_poll&method="+iden[0]+"&create_pollId="+iden[1],"loadchanges");
		}
	}
	else
	{
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
			document.getElementById("error").innerHTML = "Please Wait...";
		});

		remoteCall("../ajax/create_poll.ajax.php","form=create_poll&method="+iden[0]+"&create_pollId="+iden[1],"loadchanges");
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

		document.getElementById('qusetion').value=arr2[1];
		document.getElementById('option').value=arr2[2];
		document.getElementById('exp_date').value=arr2[3];
		document.getElementById('status').value=arr2[4];
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

		window.location.href ="../create_poll.php?mst&"+arr1[1]+"&mm";
	}
}

function getPollService(str)
{ //alert("hi");	
	var iden=new Array();
	iden=str.split("-");		
	//alert(iden);
	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{			
			remoteCall("ajax/create_poll.ajax.php","&method="+iden[0]+"&pollId="+iden[1],"loadchanges");
		}
	}
	else
	{
		remoteCall("ajax/create_poll.ajax.php","&method="+iden[0]+"&pollId="+iden[1],"loadchanges");			
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
		document.getElementById("poll_id").value=arr2[0];
		document.getElementById('group').value=arr2[1];
		document.getElementById('question').value=arr2[2];
		document.getElementById('start_date').value=arr2[3];
		document.getElementById('end_date').value=arr2[4];
		document.getElementById('created_by').value=arr2[5];
		document.getElementById('poll_status').value=arr2[6];
		//document.getElementById('revote').value=arr2[7];
		document.getElementById("revote").checked = (arr2[7] == 1) ? true : false;
		CKEDITOR.instances['additional_content'].setData(arr2[8]);
		data=arr2[9].split(",");
		disp();
		//document.getElementById('disp').value=arr2[6];
		//document.getElementById('summery').innerHTML=arr2[7];
		//CKEDITOR.instances['details'].setData(arr2[8]);
		document.getElementById("insert").value = "Update";	
		//CKEDITOR.instances.description.setData(arr2[5]);		
		//window.location.href="poll_list.php";																		
	}
	else if(arr1[0] == "delete")
	{		
		//window.location.href ="../notices.php?mst&"+arr1[1]+"&mm";		
		window.location.href ="poll.php";
	}
}