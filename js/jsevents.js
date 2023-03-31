function getevents(str)
{
	var iden=new Array();
	iden=str.split("-");

	if(iden[0]=="delete")
	{
		var d=confirm("Are you sure , you want to delete it ???");
		if(d==1)
		{
			if(iden[2]=="self")
			{
				remoteCall("ajax/ajaxevents_self.php","form=events&method="+iden[0]+"&eventsId="+iden[1],"loadchanges");
			}
			else
			{
				remoteCall("ajax/ajaxevents.php","form=events&method="+iden[0]+"&eventsId="+iden[1],"loadchanges");
			}
		}
	}
	else
	{
			remoteCall("ajax/ajaxevents.php","form=events&method="+iden[0]+"&eventsId="+iden[1],"loadchanges");
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
		document.getElementById('events_date').value=arr2[1];
		document.getElementById('events_desc').value=arr2[2];
		document.getElementById('events_title').value=arr2[3];
		document.getElementById("id").value=arr2[0];
		document.getElementById("insert").value="Update";
	}
	else if(arr1[0] == "delete")
	{alert(arr1[1]);
		var pp = arr1[1].split("****");alert(pp[1]);
		if(pp[1]=="self")
		{
			window.location.href ="../events_view_as_self.php?mst&del&"+pp[0]+"&ev";
		}
		else
		{
			window.location.href ="../events_view_as.php?mst&del&"+pp[0]+"&ev";
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
	///////////////////////////////////////////////////////////////////////////	
	var events_date = trim(document.getElementById('events_date').value);	
	var edit = trim(document.getElementById('event_id').value);
	var hr = trim(document.getElementById('hr').value);	
	var mn = trim(document.getElementById('mn').value);	
	
	var events_title = trim(document.getElementById('events_title').value);	
	//var events_desc = trim(document.getElementById('events_desc').value);	
	var event_creation = document.getElementById('event_creation_type').value;	
	var uploaded_filename = trim(document.getElementById('userfile').value);

	///////////////////////////////////////////////////////////////////////////	
	
	
	///////////////////////////////////////////////////////////////////////////	
	if(events_date=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter event date";
		document.getElementById("events_date").focus();
		
		go_error();
		return false;
	}
	if(hr=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please set proper event time";
		document.getElementById("hr").focus();
		
		go_error();
		return false;
	}
	if(mn=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please set proper event time";
		document.getElementById("mn").focus();
		
		go_error();
		return false;
	}
	
	if(events_title=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter event title";
		document.getElementById("events_title").focus();
		
		go_error();
		return false;
	}
	
	if(event_creation == 0)
	{
		document.getElementById('error').style.display = '';
		document.getElementById("error").innerHTML = "Please select event creation type";
		document.getElementById('event_creation_type').focus();
		
		go_error();
		return false;
	}
	
	/*if(event_creation == 2 && uploaded_filename == "")
	{
		if(edit != '')
		{
			return true;
		}
		else
		{
		document.getElementById('error').style.display = '';
		document.getElementById("error").innerHTML = "Please select file to upload, by clicking on browse button";
		document.getElementById('userfile').focus();
		
		go_error();
		return false;
		}
	}
*/	
	/*if(events_desc=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter event description";
		document.getElementById("events_desc").focus();
		
		go_error();
		return false;
	}
	*/
	
/*	t1 = document.getElementById('cur_date').value
	t2 = events_date ;
	
	var one_day = 1000 * 60 * 60 * 24; 

	var x = t1.split("-");     
	var y = t2.split("-");

	var date1 = new Date(x[0],(x[1]-1),x[2]);
	var date2 = new Date(y[0],(y[1]-1),y[2])
	
	var month1 = x[1]-1;
	var month2 = y[1]-1;
		   
	var diff = Math.ceil((date2.getTime()-date1.getTime())/(one_day)); 	

	if(diff<0)
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Invalid date.Please select current or future date";
		//document.getElementById('events_date').value = '';

		go_error();
		return false;	
	}
	*/
	///////////////////////////////////////////////////////////////////////////	
	
	
	///////////////////////////////////////////////////////////////////////////	
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
	//return true;
	///////////////////////////////////////////////////////////////////////////	
}


function getEvents(str)
{
	
	var iden=new Array();
	iden=str.split("-");		
	if(iden[0]=="delete")
	{
		var conf = confirm("Are you sure , you want to delete it ???");
		if(conf==1)
		{			
			remoteCall("ajax/ajaxevents.php","&method="+iden[0]+"&eventId="+iden[1],"loadchanges");
		}
	}
	else
	{
		remoteCall("ajax/ajaxevents.php","&method="+iden[0]+"&eventId="+iden[1],"loadchanges");			
	}
}

function loadchanges()
{	
	var a		= sResponse.trim();			
	var arr1	= new Array();
	var arr2	= new Array();
	arr1		= a.split("@@@");
	arr2		= arr1[1].split("^");	
				
	if(arr1[0] == "edit")
	{	//alert(arr2);
		document.getElementById("event_id").value = arr2[0];	
		document.getElementById("society_id").value = arr2[1];
		document.getElementById("issueby").value = arr2[2];	
		document.getElementById("issueby").readOnly=true;
		document.getElementById("events_date").value = arr2[3];
		document.getElementById("end_date").value = arr2[4];
		
		document.getElementById("events_title").value = arr2[5];
		
		//alert(sample);
		/*var sample;
		sample = arr2[12].replace("\r\n"," ");
		alert(sample);*/
		CKEDITOR.instances.events_desc.setData(arr2[12]);
		
		arr3=arr2[6].split(" ");
		arr4=arr3[0].split(":");
		
		document.getElementById("hr").value = arr4[0];
		document.getElementById("mn").value = arr4[1];
		document.getElementById("ampm").value = arr3[1];
		
		
		document.getElementById("event_type").value = arr2[8];		
		document.getElementById("event_charges").value = arr2[9];	
		//document.getElementById("event_creation_type").value = arr2[11];
		document.getElementById("event_creation_type").value = "2";
		EnableEventType(arr2[11]);	
			
		document.getElementById("insert").value = "Update";	
																				
	}
	else if(arr1[0] == "delete")
	{		
		//window.location.href ="../notices.php?mst&"+arr1[1]+"&mm";		
		window.location.href ="events_view.php";
	}
}

/*function del_file(upload,id)
	{
		
		alert(id);
		alert(upload);
		if(id!="")
		{
			var con = confirm("Are you sure,You want to delete the images ?");
			
			if(con==true)
			{	
				//document.getElementById('error_home_page').style.display = '';	
				document.getElementById("error").innerHTML = "Please Wait...";
				
				remoteCall("ajax/ajaxevents.php","form=event&method=del_file&id="+id+"&upload="+upload,"deletedFile");	
			}
		}	
	}
	function deletedFile(upload,id)
{
	//alert("image deleted");
	//window.reload();
	location.reload();
}*/