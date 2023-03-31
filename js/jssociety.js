function getsociety(str)
{
	var iden=new Array();
	iden=str.split("-");

	if(iden[0]=="delete")
	{
		var d=confirm("Are you sure , you want to delete it ???");
		if(d==1)
		{
			remoteCall("ajax/ajaxsociety.php","form=society&method="+iden[0]+"&societyId="+iden[1],"loadchanges");
		}
	}
	
	else if(iden[0]=="edit")
	{
		//alert("edit");
		
			remoteCall("ajax/ajaxsociety.php","form=society&method="+iden[0]+"&societyId="+iden[1],"loadchanges");
	}
	else if(iden[0]=="show")
	{
		//alert("show");
		
			remoteCall("ajax/ajaxsociety.php","form=society&method="+iden[0]+"&societyId="+iden[1],"loadchanges");
	}
}

function society_edit(str)
{
	window.location.href = "society.php?id="+str+"&tokufj=475958&edt&imp";	
}

function loadchanges()
{
	
	//alert('loadchanges');
	var a=trim(sResponse);
	var arr1=new Array();
	var arr2=new Array();
	arr1=a.split("@@@");


	arr2=arr1[1].split("#");

	// console.log(arr2);

	if(arr1[0] == "edit")
	{
		document.getElementById("society_code").value=arr2[1];
		document.getElementById("society_code").readOnly='readOnly';	
		document.getElementById("society_name").value=arr2[2];
	    document.getElementById("circle").value=arr2[3];
		document.getElementById("registration_date").value=arr2[4];
		document.getElementById("registration_no").value=arr2[5];
		document.getElementById("society_add").value=arr2[6];
		document.getElementById("city").value=arr2[7];
		document.getElementById("landmark").value=arr2[8];
		document.getElementById("state_id").value=arr2[9];
		document.getElementById("region").value=arr2[10];
		document.getElementById("postal_code").value=arr2[11];	
		document.getElementById("phone").value=arr2[13];
		document.getElementById("phone2").value=arr2[14];
		document.getElementById("fax_number").value=arr2[15];
		document.getElementById("pan_no").value=arr2[16];
		document.getElementById("tan_no").value=arr2[17];
		document.getElementById("service_tax_no").value=arr2[18];
		document.getElementById("email").value=arr2[19];
		document.getElementById("url").value=arr2[20];
		document.getElementById("member_since").value=arr2[21];
		document.getElementById("bill_cycle").value=arr2[22];
		document.getElementById("int_rate").value=arr2[23];
		document.getElementById("int_tri_amt").value=arr2[24];
		document.getElementById("int_method").value=arr2[25];
		document.getElementById("rebate_method").value=arr2[26];
		document.getElementById("rebate").value=arr2[27];
		document.getElementById("chq_bounce_charge").value=arr2[28];
		document.getElementById("bill_method").value=arr2[29];
		document.getElementById("wing").checked= (arr2[30] == 1) ? true : false;
		document.getElementById("parking").checked= (arr2[31] == 1) ? true : false;
		document.getElementById("area").checked=(arr2[32] == 1) ? true : false;
		document.getElementById("calc_int").checked=(arr2[33] == 1) ? true : false;
		document.getElementById("property_tax_no").value=arr2[34];
		document.getElementById("water_tax_no").value=arr2[35];
		document.getElementById("share").checked=(arr2[36] == 1) ? true : false;
		document.getElementById('bill_footer').value = arr2[37];
		document.getElementById('sms_start_text').value = arr2[38];
		document.getElementById('sms_end_text').value = arr2[39];
		document.getElementById('send_reminder').checked = (arr2[40] == 1) ? true : false;
		document.getElementById('bill_due_date').checked = (arr2[41] == 1) ? true : false;
		document.getElementById('show_floor').checked = (arr2[42] == 1) ? true : false;
		document.getElementById("unit_presentation").value=arr2[43];
		document.getElementById("unit_presentation_previous_value").value=arr2[43];
		document.getElementById('cc_email').value = arr2[44];
		document.getElementById('bill_as_link').checked = (arr2[45] == 1) ? true : false;
		document.getElementById('email_contactno').value = arr2[46];
		document.getElementById('neft_notify_by_email').checked = (arr2[47] == 1) ? true : false;
		document.getElementById('show_address_in_email').checked = (arr2[48] == 1) ? true : false;
		document.getElementById('apply_service_tax').checked = (arr2[49] == 1) ? true : false;
		document.getElementById('service_tax_threshold').value = arr2[50];
		document.getElementById('igst_tax_rate').value = arr2[51];
		document.getElementById('cgst_tax_rate').value = arr2[52];
		document.getElementById('sgst_tax_rate').value = arr2[53];
		document.getElementById('cess_tax_rate').value = arr2[54];
		document.getElementById('gstin_no').value = arr2[55];
		document.getElementById('apply_GST_On_Interest').checked = (arr2[56] == 1) ? true : false;
		document.getElementById('apply_GST_above_Threshold').checked = (arr2[57] == 1) ? true : false;
		document.getElementById('gst_start_date').value=arr2[58];
		document.getElementById("insert").value="Update";
		document.getElementById('bill_footer_view').style.display='none';
		document.getElementById('bill_footer_edit').style.display='block';
	}
	else if(arr1[0] == "show")	
	{
		var option = '';
		for (var i = 0; i < arr2.length - 1; i++){
   			option += '<option value="'+ arr2[i] + '">' + arr2[i] + '</option>';
		}
		$('#referred_1').append('<option value="please_select">PLEASE SELECT</option>' + option);
		$('#referred_2').append('<option value="please_select">PLEASE SELECT</option>' +option);



		document.getElementById("wing").checked= (arr2[30] == 1) ? true : false;
		document.getElementById("parking").checked= (arr2[31] == 1) ? true : false;
		document.getElementById("area").checked=(arr2[32] == 1) ? true : false;
		document.getElementById("calc_int").checked=(arr2[33] == 1) ? true : false;
		document.getElementById("property_tax_no").value=arr2[34];
		document.getElementById("water_tax_no").value=arr2[35];
		document.getElementById("share").checked=(arr2[36] == 1) ? true : false;
		document.getElementById('bill_footer').value = arr2[37];
		document.getElementById('bill_footer_view').innerHTML=arr2[37];
		document.getElementById('sms_start_text').value = arr2[38];
		document.getElementById('sms_end_text').value = arr2[39];
		document.getElementById('send_reminder').checked = (arr2[40] == 1) ? true : false;
		document.getElementById('bill_due_date').checked = (arr2[41] == 1) ? true : false;
		document.getElementById('show_floor').checked = (arr2[42] == 1) ? true : false;
		document.getElementById("unit_presentation").value=arr2[43];
		document.getElementById("unit_presentation_previous_value").value=arr2[43];
		document.getElementById('cc_email').value = arr2[44];
		document.getElementById('bill_as_link').checked = (arr2[45] == 1) ? true : false;
		document.getElementById('email_contactno').value = arr2[46];
		document.getElementById('neft_notify_by_email').checked = (arr2[47] == 1) ? true : false;
		document.getElementById('show_address_in_email').checked = (arr2[48] == 1) ? true : false;
		document.getElementById('apply_service_tax').checked = (arr2[49] == 1) ? true : false;
		document.getElementById('service_tax_threshold').value = arr2[50];
		//document.getElementById('service_tax_rate').value = arr2[51];
		document.getElementById('igst_tax_rate').value = arr2[51];
		document.getElementById('cgst_tax_rate').value = arr2[52];
		document.getElementById('sgst_tax_rate').value = arr2[53];
		document.getElementById('cess_tax_rate').value = arr2[54];
		document.getElementById('gstin_no').value = arr2[55];
		document.getElementById('apply_GST_On_Interest').checked = (arr2[56] == 1) ? true : false;
		document.getElementById('apply_GST_above_Threshold').checked = (arr2[57] == 1) ? true : false;
		document.getElementById("gst_start_date").value=arr2[58];
		document.getElementById("insert").value="Edit";
		
		var f = document.forms['society'];
		for(var i=0,fLen=f.length;i<fLen;i++)
		{
			$('#state_id').attr('disabled', true);
			$('#bill_cycle').attr('disabled', true);
			$('#int_method').attr('disabled', true);
			$('#rebate_method').attr('disabled', true);
			$('#bill_method').attr('disabled', true);
			$('#wing').attr('disabled', true);
			$('#area').attr('disabled', true);
			$('#parking').attr('disabled', true);
			$('#calc_int').attr('disabled', true);
			$('#share').attr('disabled', true);
			$('#send_reminder').attr('disabled', true);
			$('#bill_due_date').attr('disabled', true);
			$('#show_floor').attr('disabled', true);
			$('#unit_presentation').attr('disabled', true);
			$('#bill_as_link').attr('disabled', true);
			$('#neft_notify_by_email').attr('disabled', true);
			$('#show_address_in_email').attr('disabled', true);
			$('#apply_service_tax').attr('disabled', true);
			$('#apply_GST_On_Interest').attr('disabled', true);
			$('#apply_GST_above_Threshold').attr('disabled', true);
		  f.elements[i].readOnly = true;//As @oldergod noted, the "O" must be upper case
		}
		//alert("apply field");
	
	$('.field_input').replaceWith(function(){
                    return '<span class='+this.className+'>'+this.value+'</span>'
                });
                $('.field_select').replaceWith(function(){
                    return '<span class='+this.className+'>' + this.options[this.selectedIndex].text + '</span>'
                });
                $('.field_date').replaceWith(function(){
                    return '<span class="">'+this.value+'</span>'
                });
				 $('.field_text').replaceWith(function(){
                    return '<span class='+this.className+'>'+this.value+'</span>'
                });
			//document.getElementById('bill_footer_view').style.display='block';
			document.getElementById('bill_footer_edit').style.display='none';
	
	}
	else if(arr1[0] == "delete")
	{
		window.location.href ="../society.php?mst&"+arr1[1]+'&mm';
	}
	else
	{
		
	document.getElementById("society_code").readOnly='readOnly';	
	
	
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
	
	
	if(document.getElementById("insert").value == "Edit")
	{
		return true;	
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	//var wing_id = trim(document.getElementById("wing_id").value);
	//var society_id=trim(document.getElementById("society_id").value);
	var society_code=trim(document.getElementById("society_code").value);
	var society_name = trim(document.getElementById("society_name").value);
	var circle = trim(document.getElementById("circle").value);
	var registration_date = trim(document.getElementById("registration_date").value);
	var registration_no = trim(document.getElementById("registration_no").value);
	var society_add = trim(document.getElementById("society_add").value);
	var landmark = trim(document.getElementById("landmark").value);
	var state = trim(document.getElementById("state_id").value);
	var city = trim(document.getElementById("city").value);
	var region = trim(document.getElementById("region").value);
	var postal_code = trim(document.getElementById("postal_code").value);
	var country = trim(document.getElementById("country").value);	
	var phone = trim(document.getElementById("phone").value);
	var fax = trim(document.getElementById('fax_number').value);
	var pan_no = trim(document.getElementById("pan_no").value);
	var tan_no = trim(document.getElementById("tan_no").value);
	var service_tax_no = trim(document.getElementById("service_tax_no").value);
	var email = trim(document.getElementById("email").value);
	var member_since = trim(document.getElementById("member_since").value);
	//var key = trim(document.getElementById("key").value);
	//var admin_user = trim(document.getElementById("admin_user").value);
	//var admin_pass = trim(document.getElementById("admin_pass").value);
	var billing_cycle = document.getElementById('bill_cycle').value;	
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	document.getElementById("error").innerHTML = "Enter society address";
	var unit_presentation = document.getElementById('unit_presentation').value;
	var cc_email = trim(document.getElementById('cc_email').value);
	
		
	if(society_code=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter society code";
		document.getElementById("society_code").focus();
	//document.write(society_code);	
		go_error();
		return false;
	}
	

	//////////////////////////////////////////////////////////////////////////////////////////
	if(society_name=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter society name";
		document.getElementById("society_name").focus();
		
		go_error();
		return false;
	}
/////////////////////////////////////////////////////////////////////////////////////////////	
	/*if(circle=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter circle";
		document.getElementById("circle").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(society_add=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter society address";
		document.getElementById("society_add").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(landmark=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter landmark";
		document.getElementById("landmark").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(state=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Select state";
		document.getElementById("state_id").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(city=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter city";
		document.getElementById("city").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(region=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter region";
		document.getElementById("region").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(postal_code=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter postal code";
		document.getElementById("postal_code").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(country=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter country";
		document.getElementById("country").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(phone=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter phone";
		document.getElementById("phone").focus();
		
		go_error();
		return false;
	}*/
	//////////////////////////////////////////////////////////////////////////////////////////
	if(email=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Enter email id";
		document.getElementById("email").focus();
		
		go_error();
		return false;
	}
	else
	{
		if(reg.test(email) == false) 
		{
			document.getElementById('error').style.display = '';
			document.getElementById('error').innerHTML = 'Invalid email id';	
			document.getElementById('email').focus();
			
			go_error();
			return false;	
		}		
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	/*
	if(member_since=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Member since field should not be blank";
		document.getElementById("member_since").focus();
		
		go_error();
		return false;
	}
	*/
	//////////////////////////////////////////////////////////////////////////////////////////
	/*if(key=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Security No. should not be blank";
		document.getElementById("key").focus();
		
		go_error();
		return false;
	}*/
	/*if(admin_user=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Username should not be blank";
		document.getElementById("admin_user").focus();
		
		go_error();
		return false;
	}
	if(admin_pass=="")
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Password should not be blank";
		document.getElementById("admin_pass").focus();
		
		go_error();
		return false;
	}*/


	if(billing_cycle == "" || billing_cycle == 0 )
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Select Billing Cycle";
		go_error();
		return false;
	}	
	
	if(unit_presentation == "" || unit_presentation == 0 )
	{
		document.getElementById('error').style.display = '';	
		document.getElementById("error").innerHTML = "Please Select Unit Presentation";
		go_error();
		return false;
	}	
	
	if(cc_email != "")
	{
		if(reg.test(cc_email) == false) 
		{
			document.getElementById('error').style.display = '';
			document.getElementById('error').innerHTML = 'Invalid CC - email id';	
			document.getElementById('cc_email').focus();
			
			go_error();
			return false;	
		}		
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////
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
	//////////////////////////////////////////////////////////////////////////////////////////
	
}

$(function() {
	$("#load_amt").focus();
	$("#loan_amt").focusout(function() {
		var loan_amt = parseInt($(this).val());
		
		$("#loan_period").focus();

		var days = parseInt($("#loan_period").val());
		var installment_amt = 0;
		var months = 0;

		if (!isNaN(days)) {
			months = Math.floor(days / 30);
			installment_amt = Math.round(loan_amt / months);
			$("#installment_amt").val(installment_amt).attr('disabled', true);
			
		}	
	})

	$('#loan_type').change(function(){
        var id = $(this).prop("id");
        var select_val = $(this).val();

        if (select_val == 'Gold Loan') {
        	alert('gold')
        }
    });
});