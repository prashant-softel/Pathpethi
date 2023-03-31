
function ExpandALL()
{
	
	$('.tree li:has(ul)').addClass('parent_li').find(' > label ').attr('title', 'Collapse this branch');
    var children = $('.tree li.parent_li > label ').parent('li.parent_li').find(' > ul > li');
	if (children.is(":visible")) {
		//children.hide('fast');
		//$(this).attr('title', 'Expand this branch').find(' > span').addClass('icon-plus-sign').removeClass('icon-minus-sign');
	} else {
		
		children.show('fast');
		
		$(this).attr('title', 'Collapse this branch').find(' > div').addClass('icon-minus-sign').removeClass('icon-plus-sign');
	}
        
   
}



function CollapseALL()
{
	$('.tree li:has(ul)').addClass('parent_li').find(' > label ').attr('title', 'Expand this branch');
    var children = $('.tree li.parent_li > label ').parent('li.parent_li').find(' > ul > li');
	if (children.is(":visible")) {
		children.hide('fast');
		$(this).attr('title', 'Expand this branch').find(' > div').addClass('icon-plus-sign').removeClass('icon-minus-sign');
	} else {
		//children.show('fast');
		
		//$(this).attr('title', 'Collapse this branch').find(' > span').addClass('icon-minus-sign').removeClass('icon-plus-sign');
	}
        
}

function reportTypeChange()
{
		var repprtType = $('#report_type').val();
		var url = "TrialBalance.php?q=" + repprtType;
		if (document.getElementById('checkbox1').checked) {
            url = url + '&show=1';
        } else {
            url = url + '&show=0';
        }
	   //window.location.href ="TrialBalance.php?q=" + repprtType;
	   window.location.href = url;
}



function ExportToExcel()
{
	var originalDiv = document.getElementById("Exportdiv").innerHTML;	
	document.getElementById('Exportdiv').style.display = 'block';
	$("td").find('a').contents().unwrap();
	window.open('data:application/vnd.ms-excel,' + encodeURIComponent( '<html><head><style>.ExportStyle{border-collapse: collapse;}.rowstyle > td,th{ border-collapse: collapse;border:1px solid black;  }</style></head><body>' + $("#Exportdiv").html() + '</body></html>'));
    document.getElementById('Exportdiv').style.display = 'none';
   document.getElementById("Exportdiv").innerHTML = originalDiv;
  	location.reload(true);
	e.preventDefault();	
	
}