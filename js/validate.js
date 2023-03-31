var MONTHS_MONTHLY = ['April','May','June','July','August','September','October','November','December','January','February','March'];
var MONTHS_BIMONTHLY = ['April-May','June-July','August-September','October-November','December-January','February-March'];
var MONTHS_QUATERLY = ['April-May-June','July-August-September','October-November-December','January-February-March'];
var MONTHS_QUADRUPLE = ['April-May-June-July','August-September-October-November','December-January-February-March'];
//var MONTHS_HALFYEARLY = ['April-May-June-July-August-September', 'October-November-December-January-February-March'];
var MONTHS_HALFYEARLY = ['April-September', 'October-March'];
//var MONTHS_YEARLY = ['April-May-June-July-August-September-October-November-December-January-February-March'];
var MONTHS_YEARLY = ['April'];
	
function extractNumber(obj, decimalPlaces, allowNegative)
{
	var temp = obj.value;
	// avoid changing things if already formatted correctly
	var reg0Str = '[0-9]*';
	if (decimalPlaces > 0) {
		reg0Str += '\\.?[0-9]{0,' + decimalPlaces + '}';
	} else if (decimalPlaces < 0) {
		reg0Str += '\\.?[0-9]*';
	}
	reg0Str = allowNegative ? '^-?' + reg0Str : '^' + reg0Str;
	reg0Str = reg0Str + '$';
	var reg0 = new RegExp(reg0Str);
	if (reg0.test(temp)) return true;

	// first replace all non numbers
	var reg1Str = '[^0-9' + (decimalPlaces != 0 ? '.' : '') + (allowNegative ? '-' : '') + ']';
	var reg1 = new RegExp(reg1Str, 'g');
	temp = temp.replace(reg1, '');

	if (allowNegative) {
		// replace extra negative
		var hasNegative = temp.length > 0 && temp.charAt(0) == '-';
		var reg2 = /-/g;
		temp = temp.replace(reg2, '');
		if (hasNegative) temp = '-' + temp;
	}
	
	if (decimalPlaces != 0) {
		var reg3 = /\./g;
		var reg3Array = reg3.exec(temp);
		if (reg3Array != null) {
			// keep only first occurrence of .
			//  and the number of places specified by decimalPlaces or the entire string if decimalPlaces < 0
			var reg3Right = temp.substring(reg3Array.index + reg3Array[0].length);
			reg3Right = reg3Right.replace(reg3, '');
			reg3Right = decimalPlaces > 0 ? reg3Right.substring(0, decimalPlaces) : reg3Right;
			temp = temp.substring(0,reg3Array.index) + '.' + reg3Right;
		}
	}
	
	obj.value = temp;
}
function blockNonNumbers(obj, e, allowDecimal, allowNegative)
{
	var key;
	var isCtrl = false;
	var keychar;
	var reg;
		
	if(window.event) {
		key = e.keyCode;
		isCtrl = window.event.ctrlKey
	}
	else if(e.which) {
		key = e.which;
		isCtrl = e.ctrlKey;
	}
	
	if (isNaN(key)) return true;
	
	keychar = String.fromCharCode(key);
	
	// check for backspace or delete, or if Ctrl was pressed
	if (key == 8 || isCtrl)
	{
		return true;
	}

	reg = /\d/;
	var isFirstN = allowNegative ? keychar == '-' && obj.value.indexOf('-') == -1 : false;
	var isFirstD = allowDecimal ? keychar == '.' && obj.value.indexOf('.') == -1 : false;
	
	return isFirstN || isFirstD || reg.test(keychar);
}

function getMonths(cycleID)
{
	var Months = [];
	switch(cycleID)
	{
		case 1:
			Months = MONTHS_YEARLY;
			break;
		case 2:
			Months = MONTHS_HALFYEARLY;
			break;
		case 3:
			Months = MONTHS_QUADRUPLE;
			break;
		case 4:
			Months = MONTHS_QUATERLY;
			break;
		case 5:
			Months = MONTHS_BIMONTHLY;
			break;
		default:
			Months = MONTHS_MONTHLY;
			break;
	}
	
	return Months;
}


function isValidDate(id){
	var str = document.getElementById(id).value;
	//alert(str); 
	var ret = true; //RETURN VALUE	
	if(str=="" || str==null){return false;}								
	
	// m[1] is year 'YYYY' * m[2] is month 'MM' * m[3] is day 'DD'					
	//var m = str.match(/(\d{4})-(\d{2})-(\d{2})/);
	var m = str.match(/(\d{2})-(\d{2})-(\d{4})/);
	// STR IS NOT FIT m IS NOT OBJECT
	//alert(m);
	if( m === null || typeof m !== 'object'){ret=false; alert("Date Format Not Valid(dd-mm-yyyy)..");}				
	// CHECK m TYPE
	if (typeof m !== 'object' && m !== null && m.size!==3){ret=false;alert("Date Format Not Valid(dd-mm-yyyy)..");}
	//var thisYear = new Date().getFullYear(); //YEAR NOW
	var minYear = 1999; //MIN YEAR
	var date = str.substring(0, 2);
	var month = str.substring(3, 5);
	var year = str.substring(6, 10);

	var myDate = new Date(year, month - 1, date);
	var today = new Date();
	if (myDate > today) {
		alert("Entered date is greater than today's date ");
	}
	//alert("test2");
	// YEAR CHECK
	//if( (m[3].length < 4) || m[3] < minYear || m[3] > thisYear){ret = false;}
	if( (m[3].length < 4) || m[3] < minYear){ret = false; alert("Please Enter Valid Year..");}
	// MONTH CHECK	
	//alert("test3");		
	if( (m[2].length < 2) || m[2] < 1 || m[2] > 12){ret = false;alert("Please Enter Valid Month..");}
	// DAY CHECK
	//alert(m[1]);
	if( (m[1].length < 2) || m[1] < 1 || m[1] > 31){ret = false;alert("Please Enter Valid Date..");}
	document.getElementById(id).focus();
			
}


function jsdateValidator(EnteredDateID,EnteredDate,CurrentYearMinDate,CurrentYearMaxDate)
{
	var m = EnteredDate.match(/(\d{2})-(\d{2})-(\d{4})/);
	if( m === null || typeof m !== 'object')
	{
		alert("Entered Date Format Not Valid(dd-mm-yy)..");
		document.getElementById(EnteredDateID).focus();
		return false;
	}
	else
	{
		if(ConvertDateToYMD(EnteredDate) < ConvertDateToYMD(CurrentYearMinDate))
		{
			alert('Entered date is smaller than ' + CurrentYearMinDate);
			document.getElementById(EnteredDateID).focus();
			return false;
		}
		else if(ConvertDateToYMD(EnteredDate) > ConvertDateToYMD(CurrentYearMaxDate))	
		{
			alert('Entered date is greater than ' + CurrentYearMaxDate);
			document.getElementById(EnteredDateID).focus();
			return false;
		}
	}
	return true;	
}

function ConvertDateToYMD(date)
{
	var parts = date.split("-");
	return new Date(parts[2], parts[1] - 1, parts[0]);
}



function blockSpecialChars(e) 
{
    var keynum
    var keychar
    var numcheck
    
	// For Internet Explorer
    if (window.event) {
        keynum = e.keyCode;
    }
	
    // For Netscape/Firefox/Opera
    else if (e.which) {
        keynum = e.which;
    }
    keychar = String.fromCharCode(keynum);
	//var sChars = ["'" , "`" , "!"  , "@"  , "#" , "$" , "%" , "^" , "&" , "*" , "(" , ")" , "-" , "_" , "+" , "=" , "~" , "<" , ">" , "," , ";" , ":" , "|" , "?" , "{" , "}" , "[" , "]" , "¬" , "£" ,"\\"];
	var sChars = ["'" , "`" , "!"  , "@"  , "#" , "$" , "%" , "^" , "&" , "*" , "+" , "=" , "<" , ">" , "," , ";" , ":" , "|" , "?" , "{" , "}" , "[" , "]" , "¬" , "£" ,"\\"];
	 //List of special characters you want to restrict
   // if (keychar == "'" || keychar == "`" || keychar =="!" || keychar =="@" || keychar =="#" || keychar =="$" || keychar =="%" || keychar =="^" || keychar =="&" || keychar =="*" || keychar =="(" || keychar ==")" || keychar =="-" || keychar =="_" || keychar =="+" || keychar =="=" || keychar =="/" || keychar =="~" || keychar =="<" || keychar ==">" || keychar =="," || keychar ==";" || keychar ==":" || keychar =="|" || keychar =="?" || keychar =="{" || keychar =="}" || keychar =="[" || keychar =="]" || keychar =="¬" || keychar =="£" || keychar =='"' || keychar =="\\") {
     if(sChars.includes(keychar))
	 {
		//alert("Special Characters Not Allowed");
	    return false;
    } else {
        return true;
    }
}



function removeEmptySpaces(value)
{
	var re = /((\s*\S+)*)\s*/;
	value.replace(re, "$1");
	var re = /\s*((\S+\s*)*)/;
	value.replace(re, "$1");
	return value;

	
}


 function checkAlphaNumeric(e) 
 {
	
	 var key;
	 var isCtrl = false;
	 
	 if(window.event) 
	 {
		key = e.keyCode;
		isCtrl = window.event.ctrlKey;
	 }
	  else if(e.which) 
	 {
		key = e.which;
		isCtrl = e.ctrlKey;
	 }
	 
	 // check for arrow keys was pressed
	 if (isNaN(key)) return true;
	 
	 // check for backspace or delete, or if Ctrl was pressed
	if (key == 8 || isCtrl)
	{
		return true;
	}
	 if ((key >= 48 && key <= 57) || (key >= 65 && key <= 90) || (key >= 97 && key <= 122))
	 {
		return true;
	 }
	return false;
 }
