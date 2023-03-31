// JavaScript Document


	function PrintPage() 
	{
		var originalContents = document.body.innerHTML;
		$('#mainDiv').find('.btn-report').remove();
		var printContents = document.getElementById('mainDiv').innerHTML;
		
		document.body.innerHTML = printContents;
	
		$("td").find('a').contents().unwrap();
		window.print();
	
		document.body.innerHTML= originalContents;
	}
	
	
	function exportToExcel()
	{
		var originalContents = document.getElementById('mainDiv').innerHTML;
		$('#mainDiv').find('.btn-report').remove();
		$("td").find('a').contents().unwrap();
		$('#mainDiv').find('.no-print').remove();
	
		 window.open('data:application/vnd.ms-excel,' + encodeURIComponent( $("#mainDiv").html()));
		 document.getElementById('mainDiv').innerHTML = originalContents;
	}
	
	
function ViewPDF()
{
	var originalContents = document.body.innerHTML;
	$('#mainDiv').find('.btn-report').remove();
	
	$('#mainDiv').find('.no-print').remove();
	$("td").find('a').contents().unwrap();
	var sData = document.getElementById('mainDiv').innerHTML;

	var sHeader = '<html><head>';
	sHeader += '<style>	 a{text-decoration:none;}  table{border-collapse:collapse;}</style>';	
	sHeader +=	'</head><body><center>   ';
	
	var sFooter =  '</center></body></html>';
	
	sData = sHeader + sData + sFooter;
	document.body.innerHTML= originalContents;
	document.getElementById("data").value =sData; 
	document.getElementById("myForm").submit();
	
}

