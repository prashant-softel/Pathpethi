var aryUnit = [];
var aryAccHead = [];
var aryArea = [];

var bMsgDisplayed = false;
var bUpdateCounter = 0;

var bSupplementaryBill = 0;
	
function get_society()
{
	document.getElementById('error').style.display = '';
	document.getElementById('error').innerHTML = 'Fetching Societies. Please Wait...';
	populateDDListAndTrigger('select#society_id', 'ajax/get_society.php?getsociety', 'society', 'get_wing', false);
}

function get_wing(society_id)
{
	var iSocietyID = 0;
	if(society_id == null)
	{
		iSocietyID = document.getElementById('society_id').value;
	}
	else
	{
		iSocietyID = society_id;
	}
	
	document.getElementById('error').style.display = '';
	document.getElementById('error').innerHTML = 'Fetching Wings. Please Wait...';
	populateDDListAndTrigger('select#wing_id', 'ajax/get_wing.php?getwing&society_id=' + iSocietyID, 'wing', 'hide_error', true);
}

function get_AccountHeader(billtype)
{
	aryAccHead = [];
	document.getElementById('error').innerHTML = 'Fetching Account Headers. Please Wait...';
	
	var sURL = "ajax/ajaxbillmaster.php";
	var obj = {"acchead" : '', "billtype" : billtype};
	remoteCallNew(sURL, obj, 'headerFetched');
}

function headerFetched()
{
	document.getElementById('error').innerHTML = '';
	var sResponse = getResponse(RESPONSETYPE_JSON, true);
	if(sResponse[0].success == 0)
	{
		if(sResponse.length > 1)
		{
			for(var iHeadCount = 1; iHeadCount < sResponse.length; iHeadCount++)
			{
				aryAccHead.push(sResponse[iHeadCount]);
			}
			setAccHeadDD();
		}
	}
	else
	{
		document.getElementById('error').innerHTML = 'No Account Headers Fetched.<br/>Cannot Proceed with setting Bill Master.';
	}
}

function setAccHeadDD()
{
	$('#header_combo').empty();
	if(aryAccHead.length > 0)
	{
		for(var i = 0; i < aryAccHead.length; i++)
		{
			$('#header_combo').append(
			$('<option></option>')
			.val(aryAccHead[i]['id'])
			.html(aryAccHead[i]['head']));
		}
	}
	
	document.getElementById('insert').disabled = false;
}

function get_unit()
{
	showLoader();

	document.getElementById('error').style.display = '';
	document.getElementById('error').innerHTML = 'Fetching Units. Please Wait...';
	document.getElementById('unit_info').innerHTML = '';
	document.getElementById('set_common').style.display = 'none';
	
	aryUnit = [];
	
	var iSocietyID = document.getElementById('society_id').value;
	var iWingID = document.getElementById('wing_id').value;
	
	var sURL = "ajax/get_unit.php";
	var obj = {"getunit" : '', "wing_id": iWingID, "society_id" : iSocietyID};
	remoteCallNew(sURL, obj, 'unitsFetched');

	return false;
}

function unitsFetched()
{
	//alert("Units fetched");
	//document.getElementById('error').innerHTML = '';
	var sResponse = getResponse(RESPONSETYPE_JSON, true);

	if(sResponse[0].success == 0)
	{
		if(sResponse.length > 1)
		{
			for(var iUnitCount = 1; iUnitCount < sResponse.length; iUnitCount++)
			{
				aryUnit.push(sResponse[iUnitCount]);
			}
			
			fetchExistingData();
			document.getElementById('set_common').style.display = 'block';
		}
	}
	else
	{
		document.getElementById('error').innerHTML = 'No Units to display';
		go_error();
	}
}

function fetchExistingData()
{
	var aryTemp = [];
	
	for(var iUnits = 0; iUnits < aryUnit.length; iUnits++)
	{
		aryTemp.push(parseInt(aryUnit[iUnits].id));
	}
	
	var tariffPeriod = document.getElementById('period_id').value;
	
	var sURL = "ajax/ajaxbillmaster.php";
	var obj = {"getdata" : 'getdata', "unit": JSON.stringify(aryTemp), "period" : tariffPeriod,"bill_type" : bSupplementaryBill};
	remoteCallNew(sURL, obj, 'dataFetched');
}

function dataFetched()
{
	document.getElementById('error').innerHTML = '';
	var sResponse = getResponse(RESPONSETYPE_JSON, true);
	var aryData = [];
	
	if(sResponse[0].success == 0)
	{
		if(sResponse.length > 1)
		{
			for(var iDataCount = 1; iDataCount < sResponse.length; iDataCount++)
			{
				//alert(sResponse[iDataCount]['id'] + ':' + sResponse[iDataCount]['head']);
				var dataKey = sResponse[iDataCount]['id'] + '_' + sResponse[iDataCount]['head']
				//aryData.push(sResponse[iDataCount]);
				//alert(dataKey + ':' + sResponse[iDataCount]['amt']);
				
				aryData[dataKey] = sResponse[iDataCount]['amt'];
			}
		}
	}
	
	displayUnits(aryData);
}

function displayUnits(aryData)
{
	aryArea = [];
	var iColumnCount = 1;
	
	/*var iTable = '';
	//iTable += '<table style="width:100%; border:1px solid black; border-collapse: collapse;">';
	iTable += '<table width="100%" style="text-align:center;" class="table table-bordered table-hover table-striped"><thead>';
	iTable += '<tr><th style="width:100%; border:1px solid black;"><input type="checkbox" id="all_unit" onclick="SelectAllUnit(this);">&nbsp;&nbsp;Unit</th>';
	iTable += '<th style="width:100%; border:1px solid black;">Unit Area</th>';
	for(var iHeader = 0; iHeader < aryAccHead.length; iHeader++)
	{
		iColumnCount = iColumnCount + 1;
		//iTable += '<th colspan="2" style="width:100%; border:1px solid black;">' + aryAccHead[iHeader].head + '</th>';
		iTable += '<th style="width:100%; border:1px solid black;">' + aryAccHead[iHeader].head + '</th>';
	}
	iTable += '<th style="width:100%; border:1px solid black;">Bill Total</th>';
	iTable += '</tr></thead><tbody>';*/

	var iTable = '<div style="text-align:left;font-weight:bold;color:blue;">&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="all_unit" onclick="SelectAllUnit(this);">&nbsp;&nbsp;Select All Units</div><br/>';	
	for(var iUnits = 0; iUnits < aryUnit.length; iUnits++)
	{
		//iTable += '<table style="width:100%; border:1px solid black; border-collapse: collapse;">';
		iTable += '<table width="100%" style="text-align:center;" class="table table-bordered table-hover table-striped"><thead>';
		iTable += '<tr><th style="width:100%; border:1px solid black;"></th>';
		iTable += '<th style="width:100%; border:1px solid black;">Unit Area</th>';
		for(var iHeader = 0; iHeader < aryAccHead.length; iHeader++)
		{
			iColumnCount = iColumnCount + 1;
			iTable += '<th style="width:100%; border:1px solid black;">' + aryAccHead[iHeader].head + '</th>';
		}
		iTable += '<th style="width:100%; border:1px solid black;">Wing : ' + aryUnit[iUnits].wing + '<br/>Unit : ' + aryUnit[iUnits].unit + '<br/><br/>Bill Total</th>';
		iTable += '</tr></thead><tbody>';
		iTable += '<tr>';
		iTable += '<td style="border:1px solid black;"><input type="checkbox" id="chk_' + aryUnit[iUnits].id + '" onclick="checkBoxClicked(this);"/><br/>' + aryUnit[iUnits].wing + ' ' + aryUnit[iUnits].unit + '<br/><div style="color:#0000FF;"  onclick="window.open(\'unit_tariff_details.php?&uid=' + aryUnit[iUnits].id + '&bill=' + bSupplementaryBill + '\');"><u>Details</u></div></td>';
		iTable += '<td style="border:1px solid black;">' + aryUnit[iUnits].area + '</td>';
		
		aryArea.push(parseInt(aryUnit[iUnits].area));
		
		var iBillTotal = 0;
		for(var iHeader = 0; iHeader < aryAccHead.length; iHeader++)
		{
			var sValue = 0;
			var dataKey = aryUnit[iUnits].id + "_" + aryAccHead[iHeader].id;
			if(aryData[dataKey] != null)
			{
				sValue = aryData[dataKey];
				iBillTotal += parseInt(sValue);
			}
			
			iTable += '<td style="border:1px solid black; vertical-align:center;"><input type="text" style="width:50px;background:#C0C0C0;" value="' + sValue + '" placeholder="Amount" id="amt_' + aryUnit[iUnits].id + '_' + aryAccHead[iHeader].id + '" disabled /><br/><br/><div style="color:#0000FF;" id="detail_' + aryUnit[iUnits].id + '_' + aryAccHead[iHeader].id + '" onclick="showDetails(this);"><u>Details</u></div></td>';
		}
		
		iTable += '<td style="border:1px solid black; vertical-align:center;"><input type="text" readonly disabled value="' + iBillTotal + '" id="total_' + aryUnit[iUnits].id + '" style="background-color:#CCC;width:80px;"/></td>';
				
		iTable += '<td style="border:1px solid black; vertical-align:center;"><input type="button" value="Update" id="btn_' + aryUnit[iUnits].id + '" onclick="UpdateData(' + aryUnit[iUnits].id + ', false);" disabled /></td>';
		iTable += '</tr>';		
		
		iTable += '</tbody></table>';
	
	}
	iTable += '<br/><div id="update_status" style="color:#FF0000;font-weight:bold;"></div>';
	iTable += '<br/><input type="button" value="Update All" onClick="UpdateAllData(); " style="width:80px;height:30px;">';
	
	document.getElementById('unit_info').innerHTML = iTable;
	
	hideLoader();
}

function showDetails(detailID)
{
	showLoader();
	
	var details = new Array();
	details = detailID.id.split("_");
	
	var UnitID = details[1];
	var HeaderID = details[2];
	
	var sURL = "ajax/ajaxbillmaster.php";
	var obj = {"unit" : UnitID, "head" : HeaderID, "details" : "details", "bill_type" : bSupplementaryBill};
	remoteCallNew(sURL, obj, 'detailsFetched');
}

function detailsFetched()
{
	window.location.href = "billmaster.php#detailDialog";
	
	//var sMsg = '<br/><br/>Coming Soon .....';
	var sMsg = getResponse(RESPONSETYPE_STRING, true);
	
	var sBillType = "(Regular Bill)";
	if(bSupplementaryBill == 1)
	{
		sBillType = "(Supplementary Bill)";
	}
	var sText = '<a href="#close" title="Close" class="close" id="close">X</a>';
	sText += '<h3>Tariff History ' + sBillType + '</h3><p>' + sMsg + '</p><br/><br/>';
	//sText += '<a href="#close" title="Close" class="yes" id="dialogYesNo_yes">YES</a>';
	//sText += '<a href="#close" title="Close" class="no" id="dialogYesNo_no">NO</a>';
	sText += '<a href="#close" title="Close" class="no" id="dialogYesNo_no">OK</a>';
	
	document.getElementById('message_ok').innerHTML = sText;
	//document.getElementById("dialogYesNo_yes").onclick = function () { submitForm(true); };
	//document.getElementById("dialogYesNo_no").onclick = function () { submitForm(false); };
	hideLoader();
}

function UpdateAllData()
{
	bUpdateCounter = 0;
	
	if(checkIsRangeValid() == true)
	{
		var bUnitsFound = false;
	
		for(var iUnits = 0; iUnits < aryUnit.length; iUnits++)
		{
			if(document.getElementById('chk_' + aryUnit[iUnits].id).checked)
			{
				document.getElementById('update_status').innerHTML = 'Updating Data. Please Wait...';

				bUnitsFound = true;
				UpdateData(aryUnit[iUnits].id, true);
			}
		}
		
		if(bUnitsFound == false)
		{
			alert("Please select units to update tariff");
		}
	}
	
	//alert('Before : ' + bUpdateCounter);
}

function UpdateData(unitID, bAllUnits)
{
	var bProceed = true;
	if(bAllUnits == false)
	{
		bUpdateCounter = 0;
		if(checkIsRangeValid() == false)
		{
			bProceed = false;
		}
	}
	
	if(bProceed == true)
	{
		var aryHead = [];
		var aryAmt = [];
		
		var billTotal = 0;
		
		for(var iHeader = 0; iHeader < aryAccHead.length; iHeader++)
		{
			var sKey = unitID + '_' + aryAccHead[iHeader].id;
			aryHead.push(parseInt(aryAccHead[iHeader].id));
			sKey = 'amt_' + sKey;
			if(document.getElementById(sKey).value.trim().length == 0)
			{
				aryAmt.push(parseFloat('0'));
			}
			else
			{
				aryAmt.push(parseFloat(document.getElementById(sKey).value.trim()));
				billTotal += parseFloat(document.getElementById(sKey).value.trim());
			}
		}
		
		
		document.getElementById('total_' + unitID).value = billTotal;
		
		bUpdateCounter++;
		UpdateDB(unitID, aryHead, aryAmt);
	}
}

function UpdateDB(unitID, aryHead, aryAmt)
{
	showLoader();
	//alert(aryHead + ":" + aryAmt);
	var period = document.getElementById('period_id').value;
	
	/*var start_period = document.getElementById('period_id_start').value;
	var end_period = document.getElementById('period_id_end').value;
	
	if(end_period == '')
	{
		end_period = 0;
	}*/
	
	var start_period = period;
	var end_period = period;
	
	if(document.getElementById('tariff_range_lifetime').checked)
	{
		end_period = 0;
	}
	
	document.getElementById('update_status').innerHTML = 'Updating Data. Please Wait...';
	
	var sURL = "ajax/ajaxbillmaster.php";
	var obj = {"head" : JSON.stringify(aryHead), "amt" : JSON.stringify(aryAmt), "unit" : unitID, "update" : 'update', "period" : period, "start_period" : start_period, "end_period" : end_period, "bill_type" : bSupplementaryBill};
	remoteCallNew(sURL, obj, 'dbUpdated');
}

function dbUpdated()
{
	document.getElementById('update_status').innerHTML = '';
	bUpdateCounter--;
	//alert(bUpdateCounter);
	//if(bUpdateCounter == 0)
	//{
	//	alert(bUpdateCounter);
		alert(getResponse(RESPONSETYPE_STRING, true));
	//}
	
	hideLoader();
}

function SelectAllUnit(objUnit)
{
	for(var iUnits = 0; iUnits < aryUnit.length; iUnits++)
	{
		//alert(aryUnit[iUnits].unit);
		document.getElementById('chk_' + aryUnit[iUnits].id).checked = objUnit.checked;
		
		for(var iHeader = 0; iHeader < aryAccHead.length; iHeader++)
		{
			var sKeyText = aryUnit[iUnits].id + '_' + aryAccHead[iHeader].id;
			sKeyText = 'amt_' + sKeyText;
			sBtn = 'btn_' + aryUnit[iUnits].id;
			
			if(objUnit.checked == true)
			{
				document.getElementById(sKeyText).disabled = false;
				document.getElementById(sKeyText).style.backgroundColor = "#FFFF00";
				document.getElementById(sBtn).disabled = false;
			}
			else
			{
				document.getElementById(sKeyText).disabled = true;
				document.getElementById(sKeyText).style.backgroundColor = "#C0C0C0";
				document.getElementById(sBtn).disabled = true;
			}
		}
	}
}

function SelectAllHeader(objHeader)
{
	for(var iUnits = 0; iUnits < aryUnit.length; iUnits++)
	{
		var sKey = aryUnit[iUnits].id + '_' + objHeader.id;
		document.getElementById(sKey).checked = objHeader.checked;
	}
}

function ApplyAmt()
{
	var iHeadID = document.getElementById('header_combo').value;
	var iType = document.getElementById('amt_type').value;
	var iAmount = document.getElementById('common_amt').value.trim();
	var bUnitsFound = false;
	
	var iTotalArea = 0;
	for(var iArea = 0; iArea < aryArea.length; iArea++)
	{
		iTotalArea = iTotalArea + aryArea[iArea];
	}
	
	if(iHeadID.length > 0 && iAmount.length > 0)
	{
		for(var iUnits = 0; iUnits < aryUnit.length; iUnits++)
		{
			if(document.getElementById('chk_' + aryUnit[iUnits].id).checked)
			{
				bUnitsFound = true;
				
				sKey = 'amt_' + aryUnit[iUnits].id + '_' + iHeadID;
				
				if(iType == 1)
				{
					document.getElementById(sKey).value = iAmount;
				}
				else if(iType == 2)
				{
					document.getElementById(sKey).value = Math.round(parseFloat((iAmount / iTotalArea) * aryArea[iUnits]));
				}
				else if(iType == 3)
				{
					document.getElementById(sKey).value = Math.round(parseFloat(iAmount * aryArea[iUnits]));
				}
			}
		}
		
		if(bUnitsFound == false)
		{
			alert("Please select Units to Apply the amounts");
		}
	}
	else
	{
		alert("Please enter amount to Apply to selected units");	
	}
	
}

function get_period(year_id, period_index, period_element_id)
{
	document.getElementById('error').style.display = '';	
	document.getElementById('error').innerHTML = 'Fetching Period. Please Wait...';	
		
	if(year_id == null || year_id.length == 0)
	{
		populateDDListAndTrigger('select#' + period_element_id, 'ajax/ajaxbill_period.php?getperiod&year=' + document.getElementById('year_id').value, 'period', 'periodFetched', false, period_index);
	}
	else
	{
		populateDDListAndTrigger('select#' + period_element_id, 'ajax/ajaxbill_period.php?getperiod&year=' + year_id, 'period', 'periodFetched', false, period_index);
	}
}

function periodFetched()
{
	hide_error();
	var periodID = document.getElementById('period_id').value;
}

function checkIsRangeValid()
{
	var bValid = true;
	
	/*var period = document.getElementById('period_id').value;
	var start_period = document.getElementById('period_id_start').value;
	var end_period = document.getElementById('period_id_end').value;
	var end_year = document.getElementById('year_id_end').value;
	
	if(start_period == '' || start_period == 0)
	{
		bValid = false;
		alert("Please select a valid 'From' Period");
	}
	else if(end_year != 0 && (end_period == '' || end_period == 0))
	{
		bValid = false;
		alert("Please select a valid 'To' Period");
	}
	else
	{
		if(end_period == '')
		{
			end_period = 0;
		}
	
		if(end_period > 0 && end_period < start_period)
		{
			bValid = false;
			alert("Please select a valid 'To' Period.\n\n'To' Period should be greater than or equal to 'From' period.");
		}
	}*/
	
	return bValid;
}

function checkBoxClicked(chkBox)
{
	//alert(chkBox.id + ":" + chkBox.checked);
	var ary	= new Array();
	ary	= chkBox.id.split("_");
	
	var unitID = ary[1];
	
	for(var iHeader = 0; iHeader < aryAccHead.length; iHeader++)
	{
		var sKey = unitID + '_' + aryAccHead[iHeader].id;
		sKey = 'amt_' + sKey;
		sBtn = 'btn_' + unitID;
		
		if(chkBox.checked == true)
		{
			document.getElementById(sKey).disabled = false;
			document.getElementById(sKey).style.backgroundColor = "#FFFF00";
			document.getElementById(sBtn).disabled = false;
		}
		else
		{
			document.getElementById(sKey).disabled = true;
			document.getElementById(sKey).style.backgroundColor = "#C0C0C0";
			document.getElementById(sBtn).disabled = true;
		}
	}
}

function billTypeChange()
{
	document.getElementById('insert').disabled = true;
	document.getElementById('set_common').style.display = 'none';
	document.getElementById('unit_info').innerHTML = '';
	/*
	if(document.getElementById('supplementary_bill').checked)
	{
		bSupplementaryBill = 1;
		get_AccountHeader(1);
	}
	else
	{
		bSupplementaryBill = 0;
		get_AccountHeader(0);
	}*/
	
	if(document.getElementById('bill_method').value == "1")
	{
		bSupplementaryBill = 1;
		get_AccountHeader(1);
	}
	else
	{
		bSupplementaryBill = 0;
		get_AccountHeader(0);
	}
}