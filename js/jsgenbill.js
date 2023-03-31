var sMode = '';
var bSubmitForm = true;
var currentLocation = window.location.href;

function get_wing(society_id)
{
	if(society_id == 0)
	{
		$('select#wing_id').empty();
		$('select#wing_id').append(
			$('<option></option>')
			.val('0')
			.html('All'));
			
		get_unit(0);
	}
	else
	{
		get_unit(0);
		
		document.getElementById('error').style.display = '';	
		document.getElementById('error').innerHTML = 'Fetching Wings. Please Wait...';	
		populateDDListAndTrigger('select#wing_id', 'ajax/get_wing.php?getwing&society_id=' + society_id, 'wing', 'hide_error', true);
	}
}

function get_unit(wing_id)
{
	if(wing_id == 0)
	{
		$('select#unit_id').empty();
		$('select#unit_id').append(
			$('<option></option>')
			.val('0')
			.html('All'));
	}
	else
	{
		document.getElementById('error').style.display = '';	
		document.getElementById('error').innerHTML = 'Fetching Units. Please Wait...';	
		populateDDListAndTrigger('select#unit_id', 'ajax/get_unit.php?getunit&wing_id=' + wing_id, 'unit', 'hide_error', true);
	}
}

function get_year()
{
	populateDDListAndTrigger('select#year_id', 'ajax/get_unit.php?getyear', 'year', 'get_period', false);
}

function get_period(year_id, period_index)
{
	//alert(period_index);
	
	document.getElementById('error').style.display = '';	
	document.getElementById('error').innerHTML = 'Fetching Period. Please Wait...';	
		
	if(year_id == null || year_id.length == 0)
	{
		populateDDListAndTrigger('select#period_id', 'ajax/ajaxbill_period.php?getperiod&year=' + document.getElementById('year_id').value, 'period', 'periodFetched', false, period_index);
	}
	else
	{
		populateDDListAndTrigger('select#period_id', 'ajax/ajaxbill_period.php?getperiod&year=' + year_id, 'period', 'periodFetched', false, period_index);
	}
}

function get_period_for_reverse_charge(year_id, period_index)
{		
	//alert(year_id);
	//alert(period_index);
	
	if(year_id == null || year_id.length == 0)
	{
		populateDDListAndTrigger('select#period_id', 'ajax/reverse_charges.ajax.php?method=getperiod&year=' + document.getElementById('year_id').value, 'period', 'periodFetched_01', false, period_index);
	}
	else
	{
		populateDDListAndTrigger('select#period_id', 'ajax/reverse_charges.ajax.php?method=getperiod&year=' + year_id, 'period', 'periodFetched_01', false, period_index);
	}
}

function periodFetched_01()
{
	//hide_error();
	//var periodID = document.getElementById('period_id').value;
	//get_date(periodID);
}

function periodFetched()
{
	hide_error();
	var periodID = document.getElementById('period_id').value;
	get_date(periodID);
}

function get_notes(periodid)
{
	document.getElementById('error').style.display = 'block';
	document.getElementById('error').innerHTML = 'Fetching Notes. Please Wait...';
	var societyid = document.getElementById('society_id').value;

	var IsSuppBill = 0;
	
	/*if(document.getElementById('gen_supplementary_bill').checked)
	{
		IsSuppBill = 1;
	}*/
	
	if(document.getElementById('bill_method').value == '1')
	{
		IsSuppBill = 1;
	}
	
	var sURL = "ajax/ajaxgenbill.php";
	var obj = {'getnote':'getnote', 'society':societyid, 'period':periodid, 'supplementary_bill' : IsSuppBill};
	remoteCallNew(sURL, obj, 'notefetched');
	
}

function notefetched()
{
	hide_error();
	
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	
	//document.getElementById('bill_notes').value = sResponse;
	CKEDITOR.instances['bill_notes'].setData(sResponse);
	
	document.getElementById('error').innerHTML = '';
	//var periodID = document.getElementById('period_id').value;
	//get_date(periodID);
}
/////                my functions /////////////////////////
function get_fontSize(periodid)
{
	//alert(periodid);
	document.getElementById('error').style.display = 'block';
	document.getElementById('error').innerHTML = 'Fetching Notes. Please Wait...';
	var societyid = document.getElementById('society_id').value;

	var IsSuppBill = 0;
	
	/*if(document.getElementById('gen_supplementary_bill').checked)
	{
		IsSuppBill = 1;
	}*/
	
	if(document.getElementById('bill_method').value == '1')
	{
		IsSuppBill = 1;
	}
	
	var sURL = "ajax/ajaxgenbill.php";
	var obj = {'getsize':'getsize', 'society':societyid, 'period':periodid, 'supplementary_bill' : IsSuppBill};
	remoteCallNew(sURL, obj, 'FontSizefetched');
	
}

function FontSizefetched()
{
	hide_error();
	
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	document.getElementById('font_size').value = sResponse;
	
	document.getElementById('error').innerHTML = '';
	
	get_notes(document.getElementById('period_id').value);
}
	
function get_date(periodid)
{
	if(periodid > 0)
	{
		document.getElementById('error').style.display = 'block';
		document.getElementById('error').innerHTML = 'Fetching Dates. Please Wait...';
		
		document.getElementById('bill_date').value = '';
		document.getElementById('due_date').value = '';
		
		var societyID = document.getElementById('society_id').value;
		
		var hide_duedate = document.getElementById('hide_duedate').checked ? 1:0;
		var BillType = 0;
		/*if(document.getElementById('gen_supplementary_bill').checked)
		{
			BillType = 1;
		}*/
		
		if(document.getElementById('bill_method').value == '1')
		{
			BillType = 1;
		}
		
		var sURL = "ajax/ajaxgenbill.php";
		var obj = {'getdate':'', 'period':periodid, 'society':societyID, 'supplementary_bill': BillType,'hide_duedate':hide_duedate};
		remoteCallNew(sURL, obj, 'dateFetched');
	}
}

function dateFetched()
{
	hide_error();
	
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	
	var aryDate = sResponse.split("@@@");
		
	if(aryDate[0] != null && aryDate[1] != null)
	{
		document.getElementById('bill_date').value = aryDate[0].trim();
		document.getElementById('due_date').value = aryDate[1].trim();
	}
	
	
	if(document.getElementById('hide_duedate').checked && document.getElementById('bill_method').value == '1')
	{
		//document.getElementById('duedate_tr').style.opacity = '0';
		document.getElementById('duedate_tr').style.visibility = 'collapse';	
	}
	else
	{
			//document.getElementById('duedate_tr').style.opacity = '1';
			document.getElementById('duedate_tr').style.visibility = 'visible';	
	}
	
	
	get_fontSize(document.getElementById('period_id').value);
}

function ExportPDF(periodID)
{
	//Display all records while PDF export since the PDF are exported using iframe.
	var table = $('#example').DataTable();
	table.page.len( -1 ).draw();

	var periodID = document.getElementById('period_id').value;
	//alert(periodID);
	for(var i = 0; i < unitArray.length; i++)
	{
		document.getElementById('exportstatus').innerHTML = 'Exporting PDF for UnitID : ' + unitArray[i];
		
		var unitid = unitArray[i];
		var IsSupplemenataryBill = 0;
		/*if(document.getElementById('gen_supplementary_bill').checked)
		{
			IsSupplemenataryBill = 1;
		}*/
		
		if(document.getElementById('bill_method').value == '1')
		{
			IsSupplemenataryBill = 1;
		}
		
		var downLoadLink = "Maintenance_bill.php?UnitID=" + unitid + "&PeriodID=" + periodID + "&BT="+IsSupplemenataryBill +"&gen=1";
		
		var sTarget = "pdfexport_" + unitArray[i];
		var sStatus = "status_" + unitArray[i];
		
		window.open(downLoadLink, sTarget, "toolbar=no, scrollbars=yes, resizable=no, top=0, left=0, width=1, height=1");
	}
	
	document.getElementById('exportstatus').innerHTML = '';
}

function PDFExported(unitID)
{
	//alert('hi');
	//alert(unitID);
	
	var sTarget = "pdfexport_" + unitID;
	var sStatus = "status_" + unitID;
	document.getElementById(sStatus).innerHTML = 'Exported';
}

function ExportExcel(society_id,wing_id,unit_id,period_id, supplementary_bill)
{
	//alert('Export Excel'); 
	//var downLoadExcelLink = "ajax/ajaxgenbill.php?society_id=" + society_id + "&wing_id=" + wing_id + "&unit_id=" + unit_id + "&period_id=" + period_id + "&Export";
	var downLoadExcelLink = "exportbillreport.php?society_id=" + society_id + "&wing_id=" + wing_id + "&unit_id=" + unit_id + "&period_id=" + period_id + "&Export&supplementary_bill="+ supplementary_bill;
	//alert(downLoadExcelLink);
	//window.open(downLoadExcelLink, "_blank", "toolbar=no, scrollbars=yes, resizable=no, top=0, left=0, width=1, height=1");		
	window.open(downLoadExcelLink, "_blank", "toolbar=no, scrollbars=yes, resizable=yes, top=0, left=0");		
}

function checkType1()
{
	//alert(sMode);
	if(sMode == "Generate")
	{
		bSubmitForm = false;
		var sMsg = "GENERATING BILL FOR\n\nPeriod : " + document.getElementById('period_id').options[document.getElementById('period_id').selectedIndex].text + "\nYear : " + document.getElementById('year_id').options[document.getElementById('year_id').selectedIndex].text + " \n\nAre you sure you want to continue?\n\nClick OK to Generate Bill\nClick CANCEL to skip"; 
		
		if(confirm(sMsg) == false)
		{
			bSubmitForm = false;
		}
		else
		{
			bSubmitForm = true;
		}
	}
	submitForm(bSubmitForm);
}

function checkType()
{
	//alert('Check Type' + sMode);
	if(sMode == "Generate")
	{
		bSubmitForm = false;
		var sBillTypeText = "MAINTENANCE";
		/*if(document.getElementById('gen_supplementary_bill').checked)
		{
			sBillTypeText = "SUPPLEMENTARY";
		}*/
		
		if(document.getElementById('bill_method').value == '1')
		{
			sBillTypeText = "SUPPLEMENTARY";
		}
		var sMsg = "GENERATING " + sBillTypeText + " BILL FOR<br /><br />Period : " + document.getElementById('period_id').options[document.getElementById('period_id').selectedIndex].text + "<br />Year : " + document.getElementById('year_id').options[document.getElementById('year_id').selectedIndex].text + " <br />Wing : " + document.getElementById('wing_id').options[document.getElementById('wing_id').selectedIndex].text + "<br />Unit : " + document.getElementById('unit_id').options[document.getElementById('unit_id').selectedIndex].text + "<br /><br />Are you sure you want to continue?<br /><br />Click YES to Generate Bill<br />Click NO to Skip"; 
		
		window.location.href = "genbill.php#openDialogYesNo";
		var sText = '<a href="#close" title="Close" class="close" id="close">X</a>';
		sText += '<h2>Confirm Operation</h2><p>' + sMsg + '</p><br/><br/>';
		sText += '<a href="#close" title="Close" class="yes" id="dialogYesNo_yes">YES</a>';
		sText += '<a href="#close" title="Close" class="no" id="dialogYesNo_no">NO</a>';
		
		document.getElementById('message_yesno').innerHTML = sText;
		document.getElementById("dialogYesNo_yes").onclick = function () { submitForm(true); };
		document.getElementById("dialogYesNo_no").onclick = function () { submitForm(false); };
	}
	else if(sMode == "Update Notes")
	{
		bSubmitForm = false;
		
		var sMsg = "UPDATING NOTES OF ALL BILLS FOR<br /><br />Period : " + document.getElementById('period_id').options[document.getElementById('period_id').selectedIndex].text + "<br />Year : " + document.getElementById('year_id').options[document.getElementById('year_id').selectedIndex].text + " <br /><br/>[Note : You must perform 'Export PDF' operation again once the Notes are updated.]<br/><br />Are you sure you want to continue?<br /><br />Click YES to Update Notes<br />Click NO to Skip"; 
		
		window.location.href = "genbill.php#openDialogYesNo";
		var sText = '<a href="#close" title="Close" class="close" id="close">X</a>';
		sText += '<h2>Confirm Operation</h2><p>' + sMsg + '</p><br/><br/>';
		sText += '<a href="#close" title="Close" class="yes" id="dialogYesNo_yes">YES</a>';
		sText += '<a href="#close" title="Close" class="no" id="dialogYesNo_no">NO</a>';
		
		document.getElementById('message_yesno').innerHTML = sText;
		document.getElementById("dialogYesNo_yes").onclick = function () { updateNotes(); };
		//document.getElementById("dialogYesNo_no").onclick = function () { submitForm(false); };
	}
	else if(sMode == "Update Font Size")
	{
		bSubmitForm = false;
		
		var sMsg = "UPDATING FONT SIZE  OF ALL BILLS FOR<br /><br />Period : " + document.getElementById('period_id').options[document.getElementById('period_id').selectedIndex].text + "<br />Year : " + document.getElementById('year_id').options[document.getElementById('year_id').selectedIndex].text + " <br /><br/>[Note : You must perform 'Export PDF' operation again once the Font size are updated.]<br/><br />Are you sure you want to continue?<br /><br />Click YES to Update Font Size<br />Click NO to Skip"; 
		
		window.location.href = "genbill.php#openDialogYesNo";
		var sText = '<a href="#close" title="Close" class="close" id="close">X</a>';
		sText += '<h2>Confirm Operation</h2><p>' + sMsg + '</p><br/><br/>';
		sText += '<a href="#close" title="Close" class="yes" id="dialogYesNo_yes">YES</a>';
		sText += '<a href="#close" title="Close" class="no" id="dialogYesNo_no">NO</a>';
		
		document.getElementById('message_yesno').innerHTML = sText;
		document.getElementById("dialogYesNo_yes").onclick = function () { updateFont(); };
		//document.getElementById("dialogYesNo_no").onclick = function () { submitForm(false); };
	}
	submitForm(bSubmitForm);
}

function submitForm(bSubmit)
{
	bSubmitForm = true;
	if(bSubmit == true)
	{		
		document.genbill.submit();
	}
}

function SetMode(sValue)
{
	//alert('Set Mode' + sValue);
	document.getElementById('mode').value = sValue;
	sMode = sValue;	
	checkType();
}

function DownloadBill()
{
	var society_id = document.getElementById('society_id').value;
	var period_id = document.getElementById('period_id').value;
	var IsSupplemenataryBill = 0;
	/*if(document.getElementById('gen_supplementary_bill').checked)
	{
		IsSupplemenataryBill = 1;
	}*/
	
	if(document.getElementById('bill_method').value == '1')
	{
		IsSupplemenataryBill = 1;
	}
	alert("Downloading Bills...");
	var downLoadZipLink = "download_bill.php?society_id=" + society_id + "&period_id=" + period_id + "&BT=" + IsSupplemenataryBill +"&Download";
	//alert(downLoadZipLink);
	//document.getElementById('download_bill').src = downLoadZipLink;
	//window.open(downLoadExcelLink, "download_bill", "toolbar=no, scrollbars=yes, resizable=yes, top=0, left=0");			
	//window.open(downLoadZipLink, "_blank", "toolbar=no, scrollbars=yes, resizable=yes, top=0, left=0");			
	window.open(downLoadZipLink, '_blank');
}

function updateNotes()
{
	var period = document.getElementById('period_id').value;
	var sNote = CKEDITOR.instances['bill_notes'].getData();

	if(period > 0)
	{
		document.getElementById('error').style.display = 'block';
		document.getElementById('error').innerHTML = 'Updating Notes. Please Wait...';
		//var IsSuppBill = document.getElementById('gen_supplementary_bill');
		var IsSuppBill = document.getElementById('bill_method').value;
		if(IsSuppBill == 0)
		{
			//alert(IsSuppBill.value);
			
		}
		
		var sURL = "ajax/ajaxgenbill.php";
		var obj = {'setnote':'setnote', 'period':period, 'note' : sNote, 'supplementary_bill' : IsSuppBill};
		//remoteCallNew(sURL, obj, 'notesUpdated');
		$.ajax({
			url : sURL,
			type : "POST",
			data: obj ,
			success : function(data)
			{
				hide_error();	
				alert(data);
			},		
			fail: function()
			{
				//hideLoader();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				//hideLoader();
			}
		});
	}
	else
	{
		alert("Invalid Period Selected !!!");
	}
}

function notesUpdated()
{
	hide_error();
	
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	alert(sResponse);
}

function updateFont()
{
	var period = document.getElementById('period_id').value;
	var sFont = document.getElementById('font_size').value;
	//var sNote = CKEDITOR.instances['bill_notes'].getData();
	//alert(period);
	//alert(sFont);
	if(period > 0)
	{
		document.getElementById('error').style.display = 'block';
		document.getElementById('error').innerHTML = 'Updating Font Size. Please Wait...';
		//var IsSuppBill = document.getElementById('gen_supplementary_bill');
		var IsSuppBill = document.getElementById('bill_method').value;
		//alert(IsSuppBill)
		if(IsSuppBill == 0)
		{
			//alert(IsSuppBill.value);
			
		}
		
		var sURL = "ajax/ajaxgenbill.php";
		var obj = {'setfont':'setfont', 'period':period, 'font' : sFont, 'supplementary_bill' : IsSuppBill};
		//remoteCallNew(sURL, obj, 'notesUpdated');
		$.ajax({
			url : sURL,
			type : "POST",
			data: obj ,
			success : function(data)
			{
				hide_error();	
				alert(data);
			},		
			fail: function()
			{
				//hideLoader();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) 
			{
				//hideLoader();
			}
		});
	}
	else
	{
		alert("Invalid Period Selected !!!");
	}
}

function notesUpdated()
{
	hide_error();
	
	var sResponse = getResponse(RESPONSETYPE_STRING, true);
	alert(sResponse);
}

function billDelete(iUnitID,iPeriodID)
{
    showLoader();
    
    var sURL = "ajax/ajaxgenbill.php";
    
	var sBillTypeText = "Maintenence";
/*	if(document.getElementById('gen_supplementary_bill').checked)
	{
		sBillTypeText = "Supplemenary";
	}*/
	
	if(document.getElementById('bill_method').value == '1')
	{
		sBillTypeText = "Supplemenary";
	}
	
	var obj = {'iUnitID':iUnitID, 'iPeriodID':iPeriodID,  'BT' : document.getElementById('bill_method').value,'method' : 'bcheckLatestPeriod'};
    
    $.ajax({
        url : sURL,
        type : "POST",
        data: obj ,
        success : function(data)
        {
            var result = removeEmptySpaces(data);
            hideLoader();
            if(result == 'success')
            {
                var str1  = document.getElementById("year_id");
                var str2  = document.getElementById("period_id");
                var strYear = str1.options[str1.selectedIndex].text;
                var strPeriod = str2.options[str2.selectedIndex].text;
                strPeriod = strPeriod.replace("**","");
                var sMsg ="<font style='font-size: 14px;'><b>Deleting " + sBillTypeText + " Bill <br /><br />Year: " + strYear + "<br /> Bill For: " + strPeriod + "<br />";  
                if(iUnitID > 0)
                {
                    var unit_no = document.getElementById('unit_no_'+iUnitID).innerHTML;
                    var owner_name = document.getElementById('owner_name_'+iUnitID).innerHTML;
                    sMsg +="Unit No: " + unit_no + "<br />Owner Name: " + owner_name;
                } 
                sMsg +="<br />Are you sure you want to continue?<br /><br />Click YES to Delete Bill.<br />Click NO to Cancel.";
                sMsg +="</b></font>";
                window.location.href = currentLocation + "#openDialogYesNo";

                var sText = '<a href="#close" title="Close" class="close" id="close">X</a>';
                sText += '<h2>Confirm Operation</h2><p>' + sMsg + '</p><br/><br/>';
                sText += '<a href="#close" title="Close" class="yes" id="dialogYesNo_yes">YES</a>';
                sText += '<a href="#close" title="Close" class="no" id="dialogYesNo_no">NO</a>';

                document.getElementById('message_yesno').innerHTML = sText;
                
                
                changeDialogBackground(false);    
                document.getElementById("dialogYesNo_yes").onclick = function () 
                {    changeDialogBackground(true);   
                    performDelete(iUnitID,iPeriodID);
                };
                document.getElementById("dialogYesNo_no").onclick = function () 
                {
                     changeDialogBackground(true);   
                };
            }
            else
            {
                changeDialogBackground(true);   
                var sMsg = "<font style='font-size: 14px;'><b>You can delete latest generated bill only.</b></font>";  
                window.location.href = currentLocation + "#openDialogYesNo";
                var sText = '<a href="#close" title="Close" class="close" id="close">X</a>';
                sText += '<h2>Confirm Operation</h2><p>' + sMsg + '</p><br/><br/>';
                sText += '<a href="#close" title="Close" class="no" id="dialogYesNo_no">OK</a>';

                document.getElementById('message_yesno').innerHTML = sText;
                document.getElementById("dialogYesNo_no").onclick = function () 
                {
                     changeDialogBackground(true);   
                };
            }
        },		
        fail: function()
        {
            hideLoader();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) 
        {
            hideLoader();
        }
    });
    
    
}

function performDelete(iUnitID,iPeriodID)
{
    showLoader();
    
    var sURL = "ajax/ajaxgenbill.php";
	var IsSupplemenataryBill = 0;
	/*if(document.getElementById('gen_supplementary_bill').checked)
	{
		IsSupplemenataryBill = 1;
	}*/
	
	if(document.getElementById('bill_method').value == '1')
	{
		IsSupplemenataryBill = 1;
	}
    var obj = {'iUnitID':iUnitID, 'iPeriodID':iPeriodID, 'method' : 'performDelete','IsSupplemenataryBill' : IsSupplemenataryBill};
    
    $.ajax({
        url : sURL,
        type : "POST",
        data: obj ,
        success : function(data)
        {
            hideLoader();
            alert("Bill Deleted Successfully.");
            SetMode("View");
        },		
        fail: function()
        {
            hideLoader();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) 
        {
            hideLoader();
        }
    });
    
    
}
function changeDialogBackground(bsetDefault)
{
    if(bsetDefault == false)
    {    
         //alert("red color");
         $(".modalDialog > div").addClass("modalDialogRed");
        //if(browser.mozilla) 
        //{
            //$(".modalDialog > div").css("background","-moz-linear-gradient(#FF0000,  #2e6da4)");
        //} 
        /*if ($.browser.chrome) {
            $(".modalDialog > div").css("background","-webkit-linear-gradient(#FF0000, #2e6da4)");
        } 
        else if (browser.msie) 
        {
            $(".modalDialog > div").css("background","-webkit-linear-gradient(#FF0000, #2e6da4)");
        }*/
    }
    else
    {
        //setting default background
        //alert("default color");
        $(".modalDialog > div").removeClass("modalDialogRed");
        //if(browser.mozilla) 
        //{
            //$(".modalDialog > div").css("background","-moz-linear-gradient(#fff,  #2e6da4)");
        //} 
        /*if (browser.chrome) {
            $(".modalDialog > div").css("background","-webkit-linear-gradient(#fff, #2e6da4)");
        } 
        else if (browser.msie) 
        {
            $(".modalDialog > div").css("background","-webkit-linear-gradient(#fff, #2e6da4)");
        }*/
    }
    
}