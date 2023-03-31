function SubmitForm()
{

var ChangedBy = $("#ChangedBy").val();

var ChangeTSFrom=document.getElementById('ChangeTSFrom').options[document.getElementById('ChangeTSFrom').selectedIndex].text.trim();
var ChangeTSTo=document.getElementById('ChangeTSTo').options[document.getElementById('ChangeTSTo').selectedIndex].text.trim();
var ChangeTableName=document.getElementById('ChangedTableName').options[document.getElementById('ChangedTableName').selectedIndex].text.trim();
//var ChangeTSFrom = document.getElementById('ChangeTSFrom').selectedIndex.html;
//var ChangeTSTo = $("#ChangeTSTo").html();
//alert("ChangeTableName:" +ChangeTableName);
//var PostString = 'ChangedBy='+ ChangedBy + '&ChangeTSFrom='+ ChangeTSFrom + '&ChangeTSTo='+ ChangeTSTo;
//var objData = {'data' : PostString, "method" : 'applyFilter'}; 
	//alert("objData:"+ objData );
	$.ajax({
	type: "POST",
	url: "ajax/ajaxChangeLog.php",
	data: {"ChangedBy" : ChangedBy,"ChangeTSFrom" : ChangeTSFrom , "ChangeTSTo" : ChangeTSTo,"ChangeTableName" : ChangeTableName,"method" : 'applyFilter'},
	success: function(result)
		{
			//alert(result);
			document.getElementById('FilterData').innerHTML=result;
			$('#example').dataTable().fnDestroy();
			$('#example').DataTable( {
				dom: 'T<"clear">lfrtip',
				"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
				aaSorting : [],
				  "aoColumns": [
				{ "width": "12%","sClass" : "desc"},
				{ "width": "10%","sClass" : "desc"},
				{ "width": "20%","sClass" : "desc"},
				{ "width": "10%","sClass" : "desc" },
				{ "width": "10%" }
			  ]
				 
			} );
			
			document.getElementById('example').style.width='90%';
			alert("Data Fetched Succesfully..");
		}
	});

	
	
}