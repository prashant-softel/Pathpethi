// JavaScript Document

function submitTableData()
{
	var units = [];
	var oTable = document.getElementById('unit_sort');

    //gets rows of table
    var rowLength = oTable.rows.length;
	
	//loops through rows    
    for (var i = 1; i < rowLength; i++){

      //gets cells of current row  
       var oCells = oTable.rows.item(i).cells;

       //gets amount of cells of current row
       var cellLength = oCells.length;

       		var unit_no = oCells.item(2).innerHTML;
			var unit_id = oCells.item(5).innerHTML;
			var wing_id = document.getElementById('wing_' + unit_id).value;
			var sort_order = parseInt(document.getElementById('sort_order' + i).value.trim());
			var obj = "";
					 
			 obj = {"UnitNO" : unit_no, "SortOrderID" : sort_order,"UnitID" : unit_id,"WingID" : wing_id};
           units.push(obj);
	}
	
	//sort units array 
	units.sort(function (a, b) {
    if (a.SortOrderID > b.SortOrderID) {
       return 1;   //return any +ve values
    } else if (a.SortOrderID < b.SortOrderID) {
       return -1;  // return any -ve values
    } else {
       return 0;  // return zero for equal values
    }
 });

var objData = {'data' : JSON.stringify(units), "update" : 'update'}; 
	$.ajax({
			url : "ajax/ajaxUnitSorting.php",
			type : "POST",
			data: objData ,
			success : function(data)
			{	
				alert("Unit Sorted According To Your Preference...");
				window.location.href ="unit_sorting.php";
			},
				
			fail: function()
			{
				
			},
			
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
			}
		});
	
}




function selectOnlyThis(id) {
	var oTable = document.getElementById('unit_sort');
	var newID="";
    //gets rows of table
    var rowLength = oTable.rows.length;
    for (var i = 1;i <rowLength; i++)
    {
		newID="checkbox" + i;
		if(newID != id)
		{
        document.getElementById("checkbox" + i).checked = false;
		}
    }
    document.getElementById(id).checked = true;
}



function SelectAllUnit(objUnit)
{
	showLoader();	
	var oTable = document.getElementById('unit_sort');

    var rowLength = oTable.rows.length;
	 for (var i = 1; i < rowLength; i++)
	{
		//gets cells of current row  
       var oCells = oTable.rows.item(i).cells;

       //gets amount of cells of current row
       var cellLength = oCells.length;

      	var unit_id = oCells.item(5).innerHTML;
		document.getElementById('chk_' + unit_id).checked = objUnit.checked;
	  }
	  hideLoader();
	
}



function ApplyWing()
{
	showLoader();	
	var wing_comman = document.getElementById('header_combo').value;
	
	var bUnitsFound = false;
	
	var oTable = document.getElementById('unit_sort');

    var rowLength = oTable.rows.length;
	if(wing_comman > 0 )
	{
		 for (var i = 1; i < rowLength; i++)
		{
			   //gets cells of current row  
			   var oCells = oTable.rows.item(i).cells;
		
			   //gets amount of cells of current row
			   var cellLength = oCells.length;
		
				var unit_id = oCells.item(5).innerHTML;
				if(document.getElementById('chk_' + unit_id).checked)
				{
					bUnitsFound = true;
					document.getElementById('wing_' + unit_id).value = wing_comman;
					//document.getElementById('wing_' + unit_id).selectedIndex = wing_comman;
					//document.getElementById('wing_' + unit_id).setAttribute("selected","selected");
				}
		}
		
		if(bUnitsFound == false)
		{
			alert("Please select Units to Apply the wing");
		}
	}
	else
	{
		alert("Please select  wing to Apply to selected units");	
	}
	
	  hideLoader();
}