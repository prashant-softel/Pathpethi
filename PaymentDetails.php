<?php 
include_once("includes/head_s.php");
include_once ("check_default.php");
error_reporting(1);
include_once("classes/PaymentDetails.class.php");
$obj_PaymentDetails = new PaymentDetails($m_dbConn);
include_once("classes/ChequeDetails.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/home_s.class.php");
$obj_AdminPanel = new CAdminPanel($m_dbConn);
include_once("classes/utility.class.php");
$objUtility = new utility($m_dbConn);

$CustomLeaf = $_REQUEST["CustomLeaf"];
$VoucherDate = '';
$status = $objUtility->getIsDateInRange( date("Y-m-d") , getDBFormatDate($_SESSION['default_year_start_date']) , getDBFormatDate($_SESSION['default_year_end_date'])); 

$voucherType = "";

/*if($_REQUEST['LeafID'] <> -1)
{
	//We Check society using same counter for all banks or not
	$IsSameCntApply = $objUtility->IsSameCounterApply();
	if($IsSameCntApply == 1)
	{
		$Counter = $objUtility->GetCounter(VOUCHER_PAYMENT,0);	
	}
	else
	{
		$Counter = $objUtility->GetCounter(VOUCHER_PAYMENT, $_REQUEST['bankid']);		
	}
	$voucherType = VOUCHER_PAYMENT;	
}
else
{
	$Counter = $objUtility->GetCounter(VOUCHER_CASHPAY,$_REQUEST['bankid']);
	$voucherType = VOUCHER_CASHPAY;
}*/

$IsSameCntApply = $objUtility->IsSameCounterApply();
if($IsSameCntApply == 1)
{
	$Counter = $objUtility->GetCounter(VOUCHER_PAYMENT,0);	
}
else
{
	$Counter = $objUtility->GetCounter(VOUCHER_PAYMENT, $_REQUEST['bankid']);		
}
$voucherType = VOUCHER_PAYMENT;

if($status)
{
	$VoucherDate = 	date("d-m-Y");	
}
else
{
	$VoucherDate = 	getDisplayFormatDate($_SESSION['default_year_end_date']);		
}

$returncheque = $obj_PaymentDetails->m_dbConn->select("SELECT `IsReturnChequeLeaf` FROM `chequeleafbook` WHERE `id` = '".$_REQUEST['LeafID']."'");
$isreturncheque = $returncheque[0]['IsReturnChequeLeaf'];
if(!isset($_REQUEST["CustomLeaf"]))
{
	$_REQUEST["CustomLeaf"] = -1;	
}

?>
 

<html>
<head>
<style>

</style>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
    <script type="text/javascript" src="js/ajax_new.js"></script>
	<script type="text/javascript" src="js/jsPaymentDetails_20190706.js"></script>
    <script type="text/javascript" src="js/jsCommon_20190326.js"></script>
    <script type="text/javascript" src="js/validate.js"></script>
    
    <script>SetAry_ExitingExCounter(<?php echo json_encode($Counter[0]['ExitingCounter']);?>)</script>  
    
<script>
//expandDetails('exp_'+aryTips[0]['id']);
function expandDetails(obj,counter)
{
	console.log("Inside the expand"+obj.id);
	
	if(obj.id == "ShowMLData"+counter)
	{
			document.getElementById("ShowMLData" +counter).innerHTML="<i class= 'fa fa-minus-circle' style='font-size: 20px;color: #286090'></i>";
			document.getElementById("ShowMyMLdata" +counter).style.display = "table-row"; 
			document.getElementById("ShowMLData" + counter).onclick = function(){ collapseDetails(obj,counter); };
					
	}
	else
	{
			document.getElementById("ShowInvoiceData" +counter).innerHTML="<i class= 'fa fa-minus-circle' style='font-size: 20px;color: #286090'></i>";
			document.getElementById("ShowMydata" +counter).style.display = "table-row"; 
			document.getElementById("ShowInvoiceData" + counter).onclick = function(){ collapseDetails(obj,counter); };
					
	}

   
}
function collapseDetails(obj,counter)
{
	if(obj.id == "ShowMLData"+counter)
	{
			document.getElementById("ShowMLData" +counter).innerHTML="<i class= 'fa fa-plus-circle' style='font-size: 20px;color: #286090'></i>";
			document.getElementById("ShowMLData" +counter).onclick = function(){ expandDetails(obj,counter); } ;
    		document.getElementById("ShowMyMLdata" +counter).style.display = "none";			
	}
	else
	{
		  	document.getElementById("ShowInvoiceData" +counter).innerHTML="<i class= 'fa fa-plus-circle' style='font-size: 20px;color: #286090'></i>";
			document.getElementById("ShowInvoiceData" +counter).onclick = function(){ expandDetails(obj,counter); } ;
    		document.getElementById("ShowMydata" +counter).style.display = "none";		
	}

}
</script>

	
    
	<script language="javascript" type="application/javascript">
	setLedArray(false);
	aryChqNo = [];
  	leafID = <?php echo $_REQUEST['LeafID']; ?>;	
	customleaf = <?php echo $_REQUEST["CustomLeaf"]; ?>;
	
	var iCounter = 1;
	var iTableCounter = 1;
	var iChequeNumber= 0;
	var iEndChequeNumber = 0;
	
	function go_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
		});
        setTimeout('hide_error()',8000);	
    }
	
	
	function ModeOfPaymentChanged(cbMode)
	{
		var id = cbMode.id;
		var count = id.substring(13);		
		if(cbMode.value == 1 || cbMode.value == 2)
		{	
			document.getElementById('ME'+count).checked = false;		
			document.getElementById('ME'+count).disabled = true;	
		}
		else
		{			
			if(document.getElementById('reconcileStatus' + count).value == 0)
			{
				document.getElementById('ME'+count).disabled = false;
			}
		}
	}
	function PaidToChanged(cbPaidTo)
	{
		if(cbPaidTo == -1)
		{
			isCreatedNewLedger = true;
			window.open('ledger.php?type=1','CreateLedgerPopup','type=fullWindow,fullscreen,scrollbars=yes');
		}
	}
	function LanchPopup(ChequeNo,ChequeDate,PaidTo,Amount,LeafID,CustomLeaf,Comment,recStatus,reDate,GroupID,ClearVNo,reconcile,ModeOFPayment,iCanShowValues,ExistPaymentVoucherNo,RowID)
	{
			var IsInvoiceEdit = document.getElementById('IsInvoiceEdit').value;
			OpenWin = this.open('addPayment.php?bankid=<?php echo $_REQUEST['bankid']?>&LeafID='+LeafID+'&chkNo='+ChequeNo+'&chkdt='+ChequeDate+'&paidto='+PaidTo+'&amt='+Amount+'&clrVNO='+ClearVNo+'&MofPay='+ModeOFPayment+'&note='+Comment+'&recStatus='+recStatus+'&reDate='+reDate+'&reconcile='+reconcile+'&grpID='+GroupID+'&extPayVocher='+ExistPaymentVoucherNo+'&IsInvoiceEdit='+IsInvoiceEdit+'&rowID='+RowID,"CtrlWindow","top=80,left=100,screenX=100,screenY=80,width=1320,height=570,toolbar=no,menubar=no,location=no, scrollbars=yes");
	
	<?php $headerList = $obj_PaymentDetails->m_dbConn->select("select ledgertbl.id,ledgertbl.ledger_name,categorytbl.group_id, ledgertbl.categoryid from ledger As ledgertbl JOIN account_category As categorytbl ON ledgertbl.categoryid = categorytbl.category_id where ledgertbl.payment=1 and categorytbl.group_id=1 and ledgertbl.society_id=".$_SESSION['society_id']." ORDER BY ledgertbl.ledger_name ASC"); 
		
		
		for($iHeaderCount = 0; $iHeaderCount < sizeof($headerList); $iHeaderCount++)
		{
			//echo "<script>alert(".$headerList[$iHeaderCount]['id'].")<//script>";
		?>
			var obj = '<?php echo $headerList[$iHeaderCount]['id']; ?> ';
			//alert(obj);
			if(parseInt(obj) == parseInt(PaidTo))
			{
				iCanShowValues = 1;
			}
			//aryAccHead.push(obj);
		<?php
		}?>
		if(iCanShowValues == 0)
		{
			<?php
			$arBankDetails = $obj_AdminPanel->GetBankAccountAndBalance();
		   //print_r($arBankDetails);
		   
		   foreach($arBankDetails as $arData=>$arvalue)
		   {
			   $BankName = $arvalue["LedgerID"];
			   
			   ?>
				//$PaidTo = "<script>document.getElementByID('PaidTo1').value<//script>"; 
				//$LedgerIDDetails = $obj_PaymentDetails->m_objUtility->getParentOfLedger($PaidTo);?>
				var BankName = '<?php echo $BankName; ?>';
				//var BankAccountID = '<?php //echo $_SESSION['default_bank_account']; ?>';
				//alert(BankName);
				//alert(iPaidTo);
				if(parseInt(BankName) == parseInt(PaidTo))
				{
					iCanShowValues = 1;
					//alert('bingo');
				}
				<?php
		   }
		   ?>
		}
		//alert(ichkID);
		if(iCanShowValues == 1)
		{
			//document.getElementById("ExpenseTo" + ichkID).innerHTML = "<?php //echo $PaidTo = $obj_PaymentDetails->comboboxEx("select id,ledger_name from ledger where expense='1' and society_id=".$_SESSION['society_id']);?>";
			document.getElementById("ExpenseTo" + ichkID).disabled = false;
			
		}
		else
		{
			//document.getElementById("ExpenseTo" + ichkID).innerHTML = "<?php //echo "<option value='0'></option>"?>";
			document.getElementById("ExpenseTo" + ichkID).disabled = true;
		}
		//alert(iPaidTo);
		if(chkbox.checked==true)
		{						
			document.getElementById("ExpenseTo" + ichkID).disabled = false;
			document.getElementById("ExpenseTo" + ichkID).style.backgroundColor = 'white';
			document.getElementById("InvoiceDate" + ichkID).disabled = false;
			document.getElementById("InvoiceDate" + ichkID).style.backgroundColor = 'white';
			document.getElementById("TDSAmount" + ichkID).disabled = false;
			document.getElementById("TDSAmount" + ichkID).style.backgroundColor = 'white';
			document.getElementById('InvoiceAmount' + ichkID).disabled = false;
			document.getElementById('InvoiceAmount' + ichkID).style.backgroundColor = 'white';			
		}
		else
		{
			document.getElementById("ExpenseTo" + ichkID).disabled = true;
			document.getElementById("ExpenseTo" + ichkID).style.backgroundColor = 'lightgray';
			document.getElementById("InvoiceDate" + ichkID).disabled = true;
			document.getElementById("InvoiceDate" + ichkID).style.backgroundColor = 'lightgray';
			document.getElementById("TDSAmount" + ichkID).disabled = true;
			document.getElementById("TDSAmount" + ichkID).style.backgroundColor = 'lightgray';	
			document.getElementById('InvoiceAmount' + ichkID).disabled = true;
			document.getElementById('InvoiceAmount' + ichkID).style.backgroundColor = 'lightgray';					
		}	
	}

	function ValueChanged(chkbox,ichkID)
	{
		
			var iCanShowValues= 0;
			var ModeOFPayment=0;
		
			var ChequeNo = -1;
			var ChequeDate= '0000-00-00';
			var GroupID =0;
			var InviceDatabtn=0;
			var ExistPaymentVoucherNo=0;
			var LeafID = document.getElementById("LeafID").value;
			
			if(LeafID != -1)
			{
			 ChequeNo=document.getElementById("ChequeNumber" + ichkID).value;
			 ChequeDate=document.getElementById("ChequeDate" + ichkID).value;
			 GroupID = document.getElementById('GroupID' + ichkID).value;
			 InviceDatabtn = document.getElementById('ShowInvoiceData' + ichkID).value;
			 ExistPaymentVoucherNo = document.getElementById('PaymentVoucherNo' + ichkID).value;
			}
			else
			{
				 ChequeDate=document.getElementById("VoucherDate" + ichkID).value;
				 GroupID = document.getElementById('GroupID' + ichkID).value;
				 ExistPaymentVoucherNo = document.getElementById('PaymentVoucherNo' + ichkID).value;
			}
			
			console.log("Group ID"+GroupID);
			// ChequeDate=document.getElementById("VoucherDate" + ichkID).value;
			var RowID = document.getElementById("rowid" + ichkID).value;
			if(RowID == null || RowID == "")
			{
				RowID = 0;
			}
			var PaidTo=document.getElementById("PaidTo" + ichkID).value;
			var Amount=document.getElementById("Amount" + ichkID).value;
			
			console.log("Paid To Value "+PaidTo);
			
			var CustomLeaf = document.getElementById("CustomLeaf").value;
			var Comment = document.getElementById("Comment" + ichkID).value;
			var recStatus=document.getElementById('reconcileStatus' + ichkID).value;
			var reDate=document.getElementById('reconcileDate' + ichkID).value;
			var reconcile=document.getElementById('reconcile' + ichkID).value;
			
			
			//var ExistPaymentVoucherNo = document.getElementById('PaymentVoucherNo' + ichkID).value;
			
			//alert(GroupID);
			var bShowDialog = 1;
			if(CustomLeaf==1)
				{
			 	ModeOFPayment = document.getElementById("ModeOfPayment"+ ichkID).value;
				}
			
				var ClearVNo=-1;
			
				if(document.getElementById("ClearVoucherNo"+ichkID) != null)
				{
					if(LeafID != -1)
					{
					ClearVNo=document.getElementById("PaymentVoucherNo"+ichkID).value;
					
					}
					else
					{
						ClearVNo=document.getElementById("PaymentVoucherNo"+ichkID).value;
						
					}
					
				}
				
				if(ChequeDate=='')
				{ 
					alert("Please Select Cheque Date !");
					return false;
				}
				if(PaidTo=='')
				{
					alert("Please Select Paid To !");
					return false;
				}
				
			/*	if(Amount=='')
				{
					//Amount= 0;
					alert("Please Enter Amount !");
					return false;
				}*/
				//else
				//{
			
				else if(GroupID == '0')
				{	 
				$(function()
				{
   				 $.ajax({
     		 		type: "POST",
     		 		url: "ajax/ajaxPaymentDetails.php",
     			 	data: ({'PaidTo' :PaidTo ,"method" : 'FetchGroupID'}),
      				success: function(data) 
					{
						console.log("Data"+data);
				 		var arr = Array();
						arr	= data.split("@@@");
            	 		var NewGroupID=arr[1];
							
				 		if(NewGroupID == 4)
				 		{
				  			alert ("Warning : You are paying to expense ledger directly. In order to link expense invoice to the payment, you need to pay to liability ledger.");
				 			return false;
			 			}
						else
						{
							LanchPopup(ChequeNo,ChequeDate,PaidTo,Amount,LeafID,CustomLeaf,Comment,recStatus,reDate,GroupID,ClearVNo,reconcile,ModeOFPayment,iCanShowValues, ExistPaymentVoucherNo,RowID);
						}
			
            		}
          		});
       		});
		}
			//alert(GroupID);
			//alert(InviceDatabtn);
			
			
			
			else if(GroupID == 4 && InviceDatabtn == "Dontshow") 
			{
				console.log("GroupID "+GroupID);
				console.log("Bingo");;
				alert ("Warning : You are paying to expense ledger directly. In order to link expense invoice to the payment, you need to pay to liability ledger.");
				
             
			}
			
			else
			{	
				LanchPopup(ChequeNo,ChequeDate,PaidTo,Amount,LeafID,CustomLeaf,Comment,recStatus,reDate,GroupID,ClearVNo,reconcile,ModeOFPayment,iCanShowValues,ExistPaymentVoucherNo,RowID);
			//LanchPopup(ChequeNo,ChequeDate,PaidTo,Amount,LeafID,CustomLeaf,Comment,recStatus,reDate,GroupID,ClearVNo,reconcile);
			//OpenWin = this.open('addPayment.php?bankid=<?php //echo $_REQUEST['bankid']?>&LeafID='+LeafID+'&chkNo='+ChequeNo+'&chkdt='+ChequeDate+'&paidto='+PaidTo+'&amt='+Amount+'&clrVNO='+ClearVNo+'&MofPay='+ModeOFPayment+'&note='+Comment+'&recStatus='+recStatus+'&reDate='+reDate+'&reconcile='+reconcile+'&grpID='+GroupID,"CtrlWindow","top=80,left=100,screenX=100,screenY=80,width=1320,height=570,toolbar=no,menubar=no,location=no, scrollbars=yes");
		//}
		
		//alert(iPaidTo);
		
	}	
	}
	function AddNewRow(addMultipleEntry, cnter)
	{
		var show_counter = document.getElementById('show_counter').value;
		
		if(show_counter != 1)
		{
			document.getElementById('VoucherHead').style.visibility = 'hidden';			
		}
		
		if(iCounter == 1)
		{
			iChequeNumber = document.getElementById("StartChequeNo").value;
			iEndChequeNumber = document.getElementById("EndChequeNo").value;
		}
		
		if(addMultipleEntry == 0)
		{
			cnter = iCounter;	
		}				
		var strCusotmLeaf = "";
		var iLimit = 5;
		var iCnt = 1;
		var LeafID = document.getElementById("LeafID").value;
		
		var CustomLeaf = document.getElementById("CustomLeaf").value;
		if(CustomLeaf == 1)
		{
			
			if(iCounter == 1)
			{
				iChequeNumber = iChequeNumber.split(",");
				strCusotmLeaf = iChequeNumber;
		
				iEndChequeNumber = 5;
			}
		}
		if(iEndChequeNumber != '')
		{
			iLimit = iEndChequeNumber;
			iCnt = iChequeNumber;
		}
		aryChqNo = [];
		aryCounter = [];
		if(CustomLeaf == 1) 
		{
			if(iCounter == 1)
			{
				//iCnt = 0;
				<?php if(isset($_REQUEST['edt']) && $_REQUEST['edt'] <> ""){?>
				iCnt = strCusotmLeaf.length;
				<?php }else{?>
				iCnt = 0;
				<?php }?>
				iLimit = strCusotmLeaf.length;
				//alert(iLimit);
			}
			else
			{
				iCnt= 1;
				iLimit = 5;
			}
		}
		if(addMultipleEntry == 1)
		{
			iCnt = iLimit = 1;	
		}

		var currentCounter =  <?php echo $Counter[0]['CurrentCounter']?>;
		var VoucherCounter = '';
		var tmp = 0;
		for(;iCnt <= iLimit;iCnt++)
		{
			if(tmp != 0)
			{
				VoucherRowCount = tmp+1;	
			}
			else
			{
				VoucherRowCount = parseInt(currentCounter);	
			}
						
			VoucherCounter = getExCounter(VoucherRowCount);
			tmp = VoucherCounter;
			
			if((iEndChequeNumber != '' && LeafID!= -1))
			{
			
			var varRow = "<tr id='row"+ iCounter+"'>";
			
			<?php //if($isreturncheque != 1) {?>
			
			varRow += "<td align='center' valign='top' id='Edit"+ iCounter+"'  class='btnEdit'><a onClick='enableRow("+ iCounter+");'><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a></td>";
			
			<?php //} ?>

			if(CustomLeaf == 1)
			{
			    varRow += "<td><select id='ModeOfPayment" + iCounter+"' style='width:75px;' name='ModeOfPayment" + iCounter+"' onChange='ModeOfPaymentChanged(this)'><option value='0'>CHEQUE</option><option value='1'>ECS</option><option value='2'>OTHER</option></select></td>";
			   	varRow += "<td><input type='text' name='VoucherCounter" + iCounter+"' id='VoucherCounter" + iCounter+"'  style='width:80px;background:lightgray;' disabled /></td>";
			    varRow += "<td><input type='text'  id='ChequeNumber" + iCounter+"' name='ChequeNumber" + iCounter+"'  style='width:70px;' onchange='updateValues("+cnter+");'></td>";
			}
			else
			{
				varRow += "<td style='visibility:hidden'><select id='ModeOfPayment" + iCounter+"' style='width:5px;' name='ModeOfPayment" + iCounter+"' onChange='ModeOfPaymentChanged(this)'><option value='0'>CHEQUE</option><option value='1'>ECS</option><option value='2'>OTHER</option></select></td>";
				varRow += "<td><input type='text' name='VoucherCounter" + iCounter+"' id='VoucherCounter" + iCounter+"' style='width:80px;background:lightgray;' disabled /></td>";
			    varRow += "<td><input type='text'  id='ChequeNumber" + iCounter+"' name='ChequeNumber" + iCounter+"' onBlur='extractNumber(this,0,false);' onKeyUp='extractNumber(this,2,false);' onKeyPress='return blockNonNumbers(this, event, false, false)' style='width:70px;' value="+iChequeNumber+" readonly></td>";
			}
			
			varRow +="<td><input type='text' id='ChequeDate" + iCounter+"' name='ChequeDate" + iCounter+"' style='width:70px' onchange='updateValues("+cnter+");'></td>";
			
			varRow += "<td><select id='PaidTo" + iCounter+"' style='width:200px;' name='PaidTo" + iCounter+"' onChange='PaidToChanged(this.value);updateValues("+cnter+");'  onClick='fetchLedgers(this.id)'> <?php echo $PaidTo = $obj_PaymentDetails->comboboxEx("select id,concat_ws(' - ', ledgertable.ledger_name,categorytbl.category_name) from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where ledgertable.payment='1' and ledgertable.society_id=".$_SESSION['society_id']. " ORDER BY ledgertable.ledger_name ASC");?>'</select></td>";
			
			varRow +="<td><input type='button' value='M.L.' id='ML"+ iCounter +"' name='ML"+iCounter+"' style='width:40px' onClick='Redirect_Multiple_LedgerPage("+iCounter+")'></td>";
			
			varRow += "<td><input type='text' id='Amount" + iCounter+"' name='Amount" + iCounter+"' onBlur='extractNumber(this,2,true);ValidateTotal("+cnter+");' onKeyUp='extractNumber(this,2,true);' onKeyPress='return blockNonNumbers(this, event, true, false)' style='width:90px;'></td>";
			
			varRow +="<td align='left'> <input type='text' id='Comment" + iCounter+"' name='Comment" + iCounter+"' value='' style='width: 320px;'><input type='hidden' id='GroupID" + iCounter+"' name='GroupID" + iCounter+"' value='0'> </td>";
			
			//varRow +="<td><input type='button' value='Invoices' id='ClearVoucherNo"+ iCounter +"' name='ExpenseTo"+iCounter+"' style='width:50px' onClick='ValueChanged(this, "+iCounter+")'></td>";					
			varRow +="<td><input type='button' value='Invoices' id='DE"+ iCounter +"' name='ExpenseTo"+iCounter+"' style='width:50px' onClick='ValueChanged(this, "+iCounter+")'></td>";	
			//varRow += "<td colspan='8'><div id='ShowMydata" + iCounter+"'></div></td>";
			varRow +="<td id='ShowInvoiceData" + iCounter+"' onClick='expandDetails(this,"+iCounter+")' style='display:none;'><i class= 'fa fa-plus-circle' style='font-size: 20px;color: #286090;'></i></td>";
			
			varRow +="<td id='ShowMLData" + iCounter+"' onClick='expandDetails(this,"+iCounter+")' style='display:none;'><i class= 'fa fa-plus-circle' style='font-size: 20px;color: #286090;'></i></td>";
			
			varRow += "<td><input type='hidden' id='rowid" + iCounter+"' name='rowid" + iCounter+"' value='0'> </td>";
			
			varRow += "<td><input type='hidden' id='edit' name='edit' value='<?php echo $_REQUEST['edt']; ?>'> </td>";
			
			varRow += "<td><input type='hidden' id='bankid' name='bankid' value='<?php echo $_REQUEST['bankid']; ?>'> </td>";
			
			varRow += "<td><input type='hidden' name='Current_Counter" + iCounter+"' id='Current_Counter" + iCounter+"' value="+currentCounter+" style='width:80px;' /></td>";
			
			//varRow += "<td><input type='hidden' name='Current_Counter" + iCounter+"' id='Current_Counter" + iCounter+"' value="+VoucherCounter+" style='width:80px;' /></td>";
			
			varRow += "<td><input type='hidden' name='OnPageLoadTimeVoucherNumber" + iCounter+"' id='OnPageLoadTimeVoucherNumber" + iCounter+"' style='width:80px;' /></td>";
			
			varRow += "<td><input type='hidden' id='reconcileStatus" + iCounter+"' name='reconcileStatus" + iCounter+"' value='0'> </td>";
			
			varRow += "<td><input type='hidden' id='reconcileDate" + iCounter+"' name='reconcileDate" + iCounter+"' value='0'> </td>";
			
			varRow += "<td><input type='hidden' id='reconcile" + iCounter+"' name='reconcile" + iCounter+"' value='0'> </td>";
			varRow += "<td><input type='hidden' id='PaymentVoucherNo" + iCounter+"' name='PaymentVoucherNo" + iCounter+"' value='0'> </td>";
			
			varRow += "<td><input type='hidden' id='show_in_jvformat" + iCounter+"' name='show_in_jvformat" + iCounter+"' value='0'> </td>";
			
			varRow += "<td><input type='hidden' id='return" + iCounter+"' name='return" + iCounter+"' value='0'> </td>";
			
			varRow += "<td><input type='hidden' id='ref" + iCounter+"' name='ref" + iCounter+"' value='" +cnter+"'> </td>";
			
			
			
			
			//varRow +="</tr><tr><td><br></td></tr>";
			
			//varRow +="<td id='showData" + iCounter+"' onClick='expandDetails(this,"+iCounter+")'><i class= 'fa fa-plus-circle' style='font-size: 20px;color: #286090;'></i></td></tr>";
			varRow += "<tr><td>&nbsp;</td><td colspan='8'><div id='ShowMesssgedata" + iCounter+"' style='display:block;'></div></td></tr>";
			varRow += "<tr id='showinvoicetable" +iCounter+"' style='display:none;'><td>&nbsp;</td><td colspan='8'><div id='ShowMydata" + iCounter+"' style='display:none;'></div></td></tr>";
			varRow += "<tr id='showMLtable" +iCounter+"' style='display:none;'><td colspan='8'><div id='ShowMyMLdata" + iCounter+"' style='display:none;'></div></td></tr>";
			
			varRow +="<tr id='RowLabel" + iCounter+"' style='display:block;'><td></td><td colspan='8' align='left'><p id='label"+ iCounter +"' name='label"+ iCounter +"' style='color:#00FF00' readonly></p></td></tr>";
			
			}
			else
			{		 	
			varRow = "<tr id='CashRow" + iCounter+"'>";
			varRow += "<td><input type='text' name='VoucherCounter" + iCounter+"' id='VoucherCounter" + iCounter+"' value="+VoucherCounter+" style='width:80px;' /></td>";
			
			varRow += "<td><input type='text' name='VoucherDate" + iCounter+"' id='VoucherDate" + iCounter+"' value=<?php echo $VoucherDate; ?> style='width:80px;' /></td>";
			
			varRow += "<td><select id='PaidTo" + iCounter+"' style='width:200px;' name='PaidTo" + iCounter+"' onChange='PaidToChanged(this.value)'   onClick='fetchLedgers(this.id)'> <?php echo $PaidTo = $obj_PaymentDetails->comboboxEx("select id,concat_ws(' - ', ledgertable.ledger_name,categorytbl.category_name)  from `ledger` as ledgertable Join `account_category` as categorytbl on  categorytbl.category_id=ledgertable.categoryid where ledgertable.payment='1' and ledgertable.society_id=".$_SESSION['society_id']." ORDER BY ledgertable.ledger_name ASC");?>'</select></td>";
			
			varRow +="<td><input type='button' value='M.L.' id='ML"+ iCounter +"' name='ML"+iCounter+"' style='width:40px' onClick='Redirect_Multiple_LedgerPage("+iCounter+")'></td>";
		
			varRow += "<td><input type='text' id='Amount" + iCounter+"' name='Amount" + iCounter+"' onBlur='extractNumber(this,2,true);' onKeyUp='extractNumber(this,2,true);' onKeyPress='return blockNonNumbers(this, event, true, false)' style='width:90px;'></td>";
			
			varRow +="<td> <input type='text' id='Comment" + iCounter+"' name='Comment" + iCounter+"' value='' style='width: 320px;'><input type='hidden' id='GroupID" + iCounter+"' name='GroupID" + iCounter+"' value='0'> </td>";
			
			//varRow +="<td><input type='button' value='Invoices' id='ClearVoucherNo"+ iCounter +"' style='width:50px' onClick='ValueChanged(this, "+iCounter+")'></td>";	
			varRow +="<td><input type='button' value='Invoices' id='DE"+ iCounter +"' name='ExpenseTo"+iCounter+"' style='width:50px' onClick='ValueChanged(this, "+iCounter+")'></td>";	
			
			varRow +="<td id='ShowInvoiceData" + iCounter+"' onClick='expandDetails(this,"+iCounter+")' style='display:none;'><i class= 'fa fa-plus-circle' style='font-size: 20px;color: #286090;'></i></td>";
			
			varRow +="<td id='ShowMLData" + iCounter+"' onClick='expandDetails(this,"+iCounter+")' style='display:none;'><i class= 'fa fa-plus-circle' style='font-size: 20px;color: #286090;'></i></td>";
		
			varRow += "<td><input type='hidden' id='rowid" + iCounter+"' name='rowid" + iCounter+"' value='0'> </td>";
			
			varRow += "<td><input type='hidden' id='edit' name='edit' value='<?php echo $_REQUEST['edt']; ?>'> </td>";
			
			varRow += "<td><input type='hidden' name='Current_Counter" + iCounter+"' id='Current_Counter" + iCounter+"' value="+VoucherCounter+" style='width:80px;' /></td>";
			
			varRow += "<td><input type='hidden' name='OnPageLoadTimeVoucherNumber" + iCounter+"' id='OnPageLoadTimeVoucherNumber" + iCounter+"' value="+VoucherCounter+" style='width:80px;' /></td>";
			
			varRow += "<td><input type='hidden' id='reconcileStatus" + iCounter+"' name='reconcileStatus" + iCounter+"' value='0'> </td>";
			
			varRow += "<td><input type='hidden' id='reconcileDate" + iCounter+"' name='reconcileDate" + iCounter+"' value='0'> </td>";
			
			varRow += "<td><input type='hidden' id='reconcile" + iCounter+"' name='reconcile" + iCounter+"' value='0'> </td>";
			varRow += "<td><input type='hidden' id='PaymentVoucherNo" + iCounter+"' name='PaymentVoucherNo" + iCounter+"' value='0'> </td>";
			
			varRow += "<td><input type='hidden' id='show_in_jvformat" + iCounter+"' name='show_in_jvformat" + iCounter+"' value='0'> </td>";
			
			varRow += "<tr><td colspan='8'><div id='ShowMesssgedata" + iCounter+"' style='display:block;'></div></td></tr>";
			
			varRow += "<tr id='showinvoicetable" +iCounter+"' style='display:none;'><td colspan='8'><div id='ShowMydata" + iCounter+"' style='display:none;'></div></td></tr>";
			
			varRow += "<tr id='showMLtable" +iCounter+"' style='display:none;'><td colspan='8'><div id='ShowMyMLdata" + iCounter+"' style='display:none;'></div></td></tr>";
				
			varRow +="</tr><tr id='CashRowLabel" + iCounter+"'><td></td><td colspan='8' align='left'><p id='label"+ iCounter +"' name='label"+ iCounter +"' style='color:#00FF00' readonly></p></td></tr>";
			
			}
					
			if(addMultipleEntry == 1)
			{				
				var row =document.getElementById('row'+cnter);	
				var val = $(row).closest('tr').next('tr').attr('id');				
				if(val == 'RowLabel'+cnter)
				{				
					$(varRow).insertAfter(document.getElementById('RowLabel'+cnter));
				}
				else
				{
					$(varRow).insertAfter(document.getElementById('row'+cnter));
				}
			}
			else
			{
				$("#table_details > tbody").append(varRow);
			}
			
			if(show_counter != 1)
			{
				document.getElementById('VoucherCounter' + iCounter).style.visibility = 'hidden';
				document.getElementById('VoucherCounter' + iCounter).style.visibility = 'hidden';
				document.getElementById('VoucherCounter' + iCounter).style.visibility = 'hidden';
			}
			setDatePicker('ChequeDate' + iCounter);
			setDatePicker('InvoiceDate' + iCounter);
			setDatePicker('VoucherDate' + iCounter);
			var iChq = iChequeNumber;
			console.log("ChequeNumber"+iEndChequeNumber);
			//console.log("iEndChequeNumber"+iEndChequeNumber);
			if(CustomLeaf == 1)
			{
				iChq = strCusotmLeaf[iCounter - 1];
			}
			if(iEndChequeNumber != '')
			{
				obj = {'no' : iCounter, 'cheque' : iChq};
				aryChqNo.push(obj);
			}

			iCounter = iCounter + 1;
			if(addMultipleEntry == 0)
			{
				cnter = iCounter;	
			}
			
			//}
			if(CustomLeaf != 1)
			{
				iChequeNumber++;
				if(iEndChequeNumber == '' && iChequeNumber == iEndChequeNumber)
				{
					alert("All cheque Leafs are used for leaf : "+ document.getElementById("LeafName").value  +". Please add new Cheque Leaf.");
					break;
				}
			}
			else
			{
				iChequeNumber = strCusotmLeaf[iCounter];
			}
		}
		
		if(LeafID != -1 && addMultipleEntry != 1)
		{
			//getExistingData(aryChqNo, LeafID, CustomLeaf);
			setLedArray(true);
		}
		iTableCounter = iTableCounter + 1;
		document.getElementById('maxrows').value = iCounter;
	}
	
	
	
	
    function hide_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});
    }
	
	function setDatePicker(fieldName)
	{
		//alert(fieldName);
		$(function() {
			$('#' + fieldName).datepicker({
				dateFormat: "dd-mm-yy",
				minDate: minGlobalCurrentYearStartDate,
				maxDate: maxGlobalCurrentYearEndDate
				});
			});
	}
	
     $(function()
        {
			
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
            showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true,
			minDate: minGlobalCurrentYearStartDate,
			maxDate: maxGlobalCurrentYearEndDate
        })});
 	
	$(function()
	{
		<?php if($_SESSION['role'] <> ROLE_SUPER_ADMIN)
		{ ?>
			for(var i = 1 ; i <= 5 ; i++)
			{
				document.getElementById('VoucherCounter'+i).disabled = true;
				document.getElementById('VoucherCounter'+i).style.background = "#d1d1d1";	
			}
			
		<?php } ?>
	});
	
	
	
 	function leftPad(number)
	{
   		var output = number + '';
    	while (output.length < 6) 
		{
        	output = '0' + output;
    	}
    	return output;
	}
	
	
	function fetchLedgers(id)
	{
		
		if(isCreatedNewLedger == true)
		{
			tmpArr = newArr.split("</option>");
			if(document.getElementById(id).length < (tmpArr.length- 1))
			{ 	
				document.getElementById(id).innerHTML = newArr; 	
			}
		}
		
	}
	
	window.onfocus = function() {
		
		if(isCreatedNewLedger == true)
		{
			setLedArray(false);
		}
		
	};
	

	</script>
</head>
<body>
<br>

<div class="panel panel-info" id="panel" style="display:none;">
	<?php
		if($_REQUEST["LeafID"] == -1)
		{
			$pageHead = "Cash Payments";
		}
		else
		{
			$pageHead = "Payment Details";	
		}
	?>
    <div class="panel-heading" id="pageheader"><?php echo $pageHead; ?></div>
<div id="panel-inner-div">    
<?php
		$sqlBankName = "select BankName from bank_master where BankID = '" . $_REQUEST['bankid'] . "'";
		$sqlResult = $m_dbConn->select($sqlBankName);
		echo '<center><h3>'.$sqlResult[0]['BankName'].'</h3></center>';
	?>
 </font></h2>
<?php
$star = "<font color='#FF0000'>*</font>";
if(isset($_REQUEST['msg']))
{
	$msg = "Sorry !!! You can't delete it. ( Dependency )";
}
else if(isset($_REQUEST['msg1']))
{
	$msg = "Deleted Successfully.";
}
else{}
?>
<center>
<?php $arValues = $m_dbConn->select("select StartCheque,LastIssuedCheque,EndCheque,LeafName,CustomLeaf from chequeleafbook where id=".$_REQUEST["LeafID"]);  
//var_dump($arValues);
if(isset($arValues))
{
	$StartChequeNo = $arValues[0]['StartCheque'];
	$LastIssuedChequeNo = $arValues[0]['LastIssuedCheque'];
	if($LastIssuedChequeNo == 0)
	{
		$LastIssuedChequeNo = $StartChequeNo;
	}
	else
	{
		$LastIssuedChequeNo = $LastIssuedChequeNo+1;
	}
	$EndChequeNo = $arValues[0]['EndCheque'];
	$LeafName = $arValues[0]['LeafName'];
	if($arValues[0]['CustomLeaf'] == "1")
	{
		if(isset($_REQUEST['edt']))
		{ 			
			$arLeafDetails = $m_dbConn->select("select id from paymentdetails where ChqLeafID=".$_REQUEST["LeafID"]. " And `id` = ".$_REQUEST['edt']);			
		}
		else
		{
			$arLeafDetails1 = $m_dbConn->select("select id,Reference from paymentdetails where ChqLeafID=".$_REQUEST["LeafID"]);			
			$prevRef = 0;
			$arLeafDetails = array();			
			for($c = 0; $c < sizeof($arLeafDetails1); $c++)
			{
				if($arLeafDetails1[$c]['Reference'] <>0 && $prevRef == $arLeafDetails1[$c]['Reference'])
				{					
				}
				else
				{
					$prevRef = $arLeafDetails1[$c]['Reference'];					
					array_push($arLeafDetails,array('id'=>$arLeafDetails1[$c]['id']));
				}								
			}			
		}
		//var_dump($arLeafDetails);
		$NumberOfLeafs = sizeof($arLeafDetails);
		for($iLeafCounter = 0; $iLeafCounter < $NumberOfLeafs;$iLeafCounter++)
		{
			$strVals = ","; 
			if($strOutput == "")
			{
				$strOutput =  (string)implode($strVals, $arLeafDetails[0]);
			}
			else
			{
				$strVal = (string)implode($strVals, $arLeafDetails[$iLeafCounter]);
				$strOutput .= "," .  $strVal;				
			}		
		}
		
		$StartChequeNo = $strOutput;
		$EndChequeNo = "25";
	}
	else if($arValues[0]['CustomLeaf'] == "0" && isset($_REQUEST['edt']) )
	{
		$chequeNumber = $m_dbConn->select("select `id`, `ChequeNumber` from paymentdetails where ChqLeafID=".$_REQUEST["LeafID"]. " And `id` = ".$_REQUEST['edt']);
		$StartChequeNo = $EndChequeNo = $chequeNumber[0]['ChequeNumber'];
	}
}
$CounterEnable = 0;
if($_SESSION['role'] == ROLE_SUPER_ADMIN)
{
	$CounterEnable = 1;
}

echo '<input type="hidden" id="StartChequeNo" value="'.$StartChequeNo.'"/>';
echo '<input type="hidden" id="EndChequeNo" value="'. $EndChequeNo .'"/>';
echo '<input type="hidden" id="LeafName" value="'. $LeafName .'"/>';
echo '<input type="hidden" id="LeafID" value="'.$_REQUEST["LeafID"] .'"/>';
echo '<input type="hidden" id="BankID" value="'.$_REQUEST["bankid"] .'"/>';
echo '<input type="hidden" id="VoucherType" value="'.$voucherType.'"/>';
echo '<input type="hidden" id="CustomLeaf" value="'.$_REQUEST["CustomLeaf"] .'"/>';
echo '<input type="hidden" id="CounterEnable" value="'.$CounterEnable.'"/>';
echo '<input type="hidden" id="show_counter" value="'.$_SESSION['show_counter'].'"/>';
echo '<input type="hidden" value="0" id="IsInvoiceEdit">';
if($_REQUEST["LeafID"] == -1)
{
?>

<!--<table>
    <tr>
        <td><?php echo $star;?></td>
        <td>Voucher Date<input type="text" name="VoucherDate" id="VoucherDate" class="basics" size="10" value=<?php echo $VoucherDate; ?> style="width:80px;" /></td>
    </tr>
</table>-->
<?php
} ?>
<input type="hidden" name="maxrows" id="maxrows" />
</table>
	<!--<div class="scrollit">    -->
    <div>
	<center>
		<table id="table_details" style="text-align:center;">
			<tbody>            
            <?php 
			
			if($_REQUEST["LeafID"] != -1)
			{
				/*if(!isset($_REQUEST['edt']))
				{*/
					echo '<tr><center><td colspan=9  style="padding-left: 0px;"><button onClick="SubmitChequeDetails(minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate)"  id="SubmitUp"  class="btn btn-primary" >Submit</button> </td></tr><tr><td><br></td></tr>';
				/*}
				else
				{
					//echo '<tr><td><br></td></tr>';	
				}*/
				//echo "<tr><th>Paid To</th><th style='width:50px;'>Double Entry?</th><th>Expense To</th><th>Cheque Number</th><th>Cheque Date<br>(DD-MM-YYYY)</th><th>Amount</th><th>Comments</th></tr>";
				if($CustomLeaf == "1")
				{
					if($isreturncheque != 1)
					{
						echo "<tr><th id='lblEdit'>Edit &nbsp;</th><th style='text-align: center;'>Mode Of Payment</th><th style='text-align: center;'>Cheque /Transaction Number</th><th id ='VoucherHead' style='text-align: center;'>Voucher Number</th><!--<th>Multiple Entries?</th>--><th style='text-align: center;'>Cheque /Transaction Date<br>(DD-MM-YYYY)</th><th style='text-align: center;'>Paid To</th><th style='text-align: center;'>Multiple Ledgers</th><th style='text-align: center;'>Cheque /Transaction Amount</th><th style='text-align: center;'>Comments</th><th style='width:50pxtext-align: center;' >Double Entry?</th><th style='text-align: center;'>Show/Hide <br> Invoice Details</th><th style='text-align: center;'>Show/Hide ML</th><!--<th>Invoice date<br>(DD-MM-YYYY)</th><th style='width:125px;'>Expense To</th><th>Invoice Amount</th><th>TDS Amount</th>--></tr>";
					}
					else
					{
						echo "<tr><th></th><th id ='VoucherHead' style='text-align: center;'>Voucher Number</th><th style='text-align: center;'>Mode Of Payment</th><th style='text-align: center;'>Cheque /Transaction Number</th><!--<th>Multiple Entries?</th>--><th style='text-align: center;'>Cheque /Transaction Date<br>(DD-MM-YYYY)</th><th style='text-align: center;'>Paid To</th><th style='text-align: center;'>Multiple Ledgers</th><th style='text-align: center;'>Cheque /Transaction Amount</th><th style='text-align: center;'>Comments</th><th style='width:50px;text-align: center;'>Double Entry?</th><th style='text-align: center;'>Show/Hide <br> Invoice Details</th><th style='text-align: center;'>Show/Hide ML</th><!--<th>Invoice date<br>(DD-MM-YYYY)</th><th style='width:125px;'>Expense To</th><th>Invoice Amount</th><th>TDS Amount</th><th>Comments</th>--></tr>";
					}
				}
				else
				{
					echo "<tr><th id='lblEdit'>Edit &nbsp;</th><th></th><th id ='VoucherHead' style='text-align: center;'>Voucher Number</th><th style='text-align: center;'>Cheque Number</th><!--<th>Multiple Entries?</th>--><th style='text-align: center;'>Cheque Date<br>(DD-MM-YYYY)</th><th style='text-align: center;'>Paid To</th><th style='text-align: center;'>Multiple Ledgers</th><th style='text-align: center;'>Cheque Amount</th><th style='text-align: center;'>Comments</th><th style='width:50px;text-align: center;''>Double Entry?</th><th style='text-align: center;'>Show/Hide <br>Invoice Details</th><th style='text-align: center;'>Show/Hide ML</th><!--<th>Invoice date<br>(DD-MM-YYYY)</th><th style='width:125px;'>Expense To</th><th>Invoice Amount</th><th>TDS Amount</th><th>Comments</th>--></tr>";
				}
			}
			else
			{
				echo '<tr><center><td colspan=8  style="padding-left: 7px;"><button onClick="SubmitChequeDetails(minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate)"  id="SubmitUp"  class="btn btn-primary" >Submit</button> </td></tr>';
				echo "<tr><th id ='VoucherHead' style='text-align: center;'>Voucher Number</th><th style='text-align: center;'>Voucher Date</th><th style='text-align: center;'>Paid To</th><th style='text-align: center;'>Multiple Ledgers</th><th style='text-align: center;'>Amount</th><th style='text-align: center;'>Comments</th></tr>";
			}
				?>
    		</tbody>
    		</table>
			</center>
    </div>
<table><tr>
<?php 
if(($_REQUEST["LeafID"] == -1) || ($_REQUEST["CustomLeaf"] == 1))
{
	
?>

<td>
<input type="button" value="Add 5 Rows"  class="btn btn-primary" onClick="AddNewRow(0,0)">
</td>
<?php 
}
?>
<!--<td>&nbsp;&nbsp;&nbsp;</td>
<td>
<input type="button" value="Add Multiple Legder"  class="btn btn-primary" onClick="Redirect_Multiple_LedgerPage()">
</td>-->
<td>&nbsp;&nbsp;&nbsp;</td>
<td>
<button onClick="SubmitChequeDetails(minGlobalCurrentYearStartDate , maxGlobalCurrentYearEndDate)"  id="Submit"  class="btn btn-primary">Submit</button>   </td>
<td>&nbsp;&nbsp;&nbsp;</td>
<td>
<button id="Cancel"  class="btn btn-primary" onClick="CancleButton();">Cancel</button>   </td>
</tr>
</table>
<script>
function CancleButton()
{
 window.location.reload(true);
}
</script>
<form name="PaymentDetails" id="PaymentDetails" method="post" action="process/PaymentDetails.process.php">
<table align='center'>
<?php
if(isset($msg))
{
	if(isset($_POST['ShowData']))
	{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
	}
	else
	{
	?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $msg; ?></b></font></td></tr>
	<?php
	}
}
else
{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
}
?>

		<tr>
			
			<td>
            <select  id="PayerBank" name="PayerBank" style="width:200px;display:none;">
            
			
			<?php	echo "<option value=".$_REQUEST["bankid"]. ">" .$_REQUEST["bankid"]."</option>";
			
			?>
	        
            </select>
            </td>
		</tr>
</table>
</form>
</center>
</div>
<?php

if(isset($_REQUEST['edt']) && $_REQUEST["LeafID"] == -1 && isset($_REQUEST['delete']))
{ 
?>

<script>
	getPaymentDetails('delete-' + <?php echo  $_REQUEST['edt'] ?>); 
</script>

<?php    
}
else if(isset($_REQUEST['edt']) && $_REQUEST["LeafID"] == -1)
{
?>

<script>
	getPaymentDetails('edit-' + <?php echo  $_REQUEST['edt'] ?> + '-' + <?php echo  $_REQUEST['bankid'] ?>); 
</script>

<?php    
}
else if(isset($_REQUEST['delete']))
{
?>

<script>
	delSetFlag = true;
</script>

<?php    
}
else if(isset($_REQUEST['edt']))
{
?>

<script>
	edtSetFlag = true;	
</script>
<?php    
} ?>

<script>
var disSetFlag = false;</script>
<?php 
if(isset($_REQUEST['disable']))
{
?>

<script>
	disSetFlag = true;	
</script>
<?php    
}
?>
<script>

AddNewRow(0,0);</script>

<script>
	function ShowAddButton(val)
	{
		document.getElementById('add'+val).style.display = 'block';
		document.getElementById('add'+val).onclick = function() { AddRow(val); };
	}
	
	function AddRow(val)
	{
		if(document.getElementById('ME'+val).checked == true)
		{
			if(document.getElementById('ChequeNumber'+val).value == '' || document.getElementById('ChequeDate'+val).value == '')
			{	
				alert("Please enter cheque date and cheque number");						
			}
			else
			{
				AddNewRow(1,val);
				var newRowID = document.getElementById('maxrows').value;
				newRowID--;		
				ValueChanged(document.getElementById('DE'+newRowID));	
				document.getElementById('ChequeNumber'+newRowID).value = document.getElementById('ChequeNumber'+val).value;
				document.getElementById('ModeOfPayment'+newRowID).value = document.getElementById('ModeOfPayment'+val).value;
				document.getElementById('ChequeDate'+newRowID).value = document.getElementById('ChequeDate'+val).value;
				document.getElementById('ModeOfPayment'+newRowID).style.visibility = 'hidden';
				document.getElementById('ChequeNumber'+newRowID).style.visibility = 'hidden';
				document.getElementById('ME'+newRowID).style.display = 'none';
				document.getElementById('ChequeDate'+newRowID).style.display = 'none';	
				document.getElementById('Edit'+newRowID).style.visibility = 'hidden';	
				document.getElementById('PaidTo'+newRowID).value = document.getElementById('PaidTo'+val).value;
				document.getElementById('PaidTo'+newRowID).style.visibility = 'hidden';
				document.getElementById('Amount'+newRowID).value = 00;
				document.getElementById('Amount'+newRowID).style.visibility = 'hidden';
				var isIDExist = document.getElementById('valRow'+val);				
    			if (isIDExist == null){						
					var multEntry = "<tr id='valRow"+val+"'><input type='hidden' id='FinalChqAmount" + val+"' name='FinalChqAmount" + val+"' value='0' /><td colspan='6'></td><td id='ChequeAmount"+val+"' style='background-color:#F70D1A;color:#FFF;'>0.00</td><td colspan='3'></td><td id='TotalAmount"+val+"' style='background-color:#F70D1A;color:#FFF;'>0.00</td><td id='TotalTDS"+val+"' style='background-color:#F70D1A;color:#FFF;'>0.00</td><td></td></tr>";
					var row =document.getElementById('row'+newRowID);	
					var nextRow = $(row).closest('tr').next('tr').attr('id');				
					if(nextRow == 'RowLabel'+newRowID)
					{				
						$(multEntry).insertAfter(document.getElementById('RowLabel'+newRowID));
					}
					else
					{
						$(multEntry).insertAfter(document.getElementById('row'+newRowID));
					}					
					ValidateTotal(val);
				}
			}
		}		
	}
	
	function ValidateTotal(counter)
	{		
		//if(document.getElementById('ME'+counter).checked == false)
		//{
			//return;	
		//}
		var iTotal = 0;	
		var iTotalTDS = 0;
		var row =document.getElementById('row'+counter);
		var iAmount = document.getElementById('InvoiceAmount'+counter).value;
		iTotal = parseFloat(iAmount = iAmount ? iAmount : 0);
		var iTDS = document.getElementById('TDSAmount'+counter).value;
		iTotalTDS = parseFloat(iTDS = iTDS ? iTDS : 0);	
		var val = $(row).closest('tr').next('tr').attr('id');			
			
		if(val == 'RowLabel'+counter)
		{
			val = $(row).closest('tr').next('tr').next('tr').attr('id');			
			if(val == null)
			{
				return;	
			}
		}
		while(val != 'valRow'+counter)
		{								
			var id = val.substring(3);		
			if(document.getElementById('ref'+id) != null)
			{
				if(document.getElementById('ref'+id).value == document.getElementById('ref'+counter).value)
				{	
					iAmount = document.getElementById('InvoiceAmount'+id).value;	
					iTotal = parseFloat(iTotal) + parseFloat(iAmount = iAmount ? iAmount : 0);
					iTDS = document.getElementById('TDSAmount'+id).value;
					iTotalTDS = parseFloat(iTotalTDS) + parseFloat(iTDS = iTDS ? iTDS : 0);
				}
				else
				{
					break;	
				}
			}
			row =document.getElementById('row'+id);
			val = $(row).closest('tr').next('tr').attr('id');				
			if(val == 'RowLabel'+id)
			{
				val = $(row).closest('tr').next('tr').next('tr').attr('id');
			}
		}
		document.getElementById("TotalAmount"+counter).innerHTML='<b>Total Amount : ' + iTotal + '</b>';		
		document.getElementById('TotalTDS'+counter).innerHTML='<b>TDS Amount :  ' + iTotalTDS + '</b>';		
		var iChequeAmount = parseFloat(iTotal) - parseFloat(iTotalTDS);
		if(document.getElementById('FinalChqAmount'+counter))
		{
			document.getElementById('FinalChqAmount'+counter).value =	iChequeAmount;
		}
		document.getElementById('ChequeAmount'+counter).innerHTML='<b>Cheque Amount : ' + iChequeAmount + '</b>';
		if(document.getElementById('Amount'+counter).value != '' && 	document.getElementById('Amount'+counter).value == iChequeAmount)
		{
			document.getElementById("ChequeAmount"+counter).style.backgroundColor = '#7FE817';
			document.getElementById("TotalAmount"+counter).style.backgroundColor = '#7FE817';
			document.getElementById("TotalTDS"+counter).style.backgroundColor = '#7FE817';
		}
		else
		{
			document.getElementById("ChequeAmount"+counter).style.backgroundColor = '#F70D1A';
			document.getElementById("TotalAmount"+counter).style.backgroundColor = '#F70D1A';
			document.getElementById("TotalTDS"+counter).style.backgroundColor = '#F70D1A';
		}
	}
	
	function updateValues(count)
	{				
		return;
		///if(document.getElementById('ME'+count).checked == false)
		//{
			//return;	
		//}
		var row =document.getElementById('row'+count);
		var val = $(row).closest('tr').next('tr').attr('id');	
		if( val == 'RowLabel'+count)
		{
			val = $(row).closest('tr').next('tr').next('tr').attr('id');			
			if(val == null)
			{
				return;	
			}
		}
		while(val != 'valRow'+count)
		{
			//alert(val);					
			var id = val.substring(3);					
			if(document.getElementById('ref'+id) != null)
			{				
				if(document.getElementById('ref'+id).value == document.getElementById('ref'+count).value)
				{	
					document.getElementById('ChequeNumber'+id).value = document.getElementById('ChequeNumber'+count).value;	
					document.getElementById('ChequeDate'+id).value = document.getElementById('ChequeDate'+count).value;	
					document.getElementById('PaidTo'+id).value = document.getElementById('PaidTo'+count).value;					
				}
				else
				{
					break;	
				}
			}
			row =document.getElementById('row'+id);
			val = $(row).closest('tr').next('tr').attr('id');
		
			if(val == null || val.substring(0,8) == 'RowLabel')
			{
				val = $(row).closest('tr').next('tr').next('tr').attr('id');
			}			
		}
	}
	
	
</script>
<?php 
if(isset($_REQUEST['report']))
{ ?>
	<script>getPaymentDetails('edit-'+<?php echo $_REQUEST['report'] ;?>)</script>;
<?php } ?> 
<center>
<table align="center">
<tr>
<td>
<?php
echo "<br>";
$str1 = $obj_PaymentDetails->pgnation(IsReadonlyPage());

?>
</td>
</tr>
</table>

</center>


<?php include_once "includes/foot.php"; ?>



<?php 
if(isset($_REQUEST['edt']) && $_REQUEST["LeafID"] != -1)
{
	?>
    <script> document.getElementById('Submit').style.display = 'none'; </script>
<?php }
if(IsReadonlyPage() == true)
{?>
<script>
	$("#panel-inner-div").css( 'pointer-events', 'none' );
	document.getElementById('Submit').style.display = 'none';
	document.getElementById('SubmitUp').style.display = 'none';
</script>
	<?php if($_REQUEST["LeafID"] != -1)
    {?>
		<script>
            hideEditBtn();
        </script>
    <?php }
}
if(IsReadonlyPage() == true && $_REQUEST["LeafID"] == -1){?>
<script>
	$("#panel-inner-div").css( 'display', 'none' );
</script>
    
<?php }?>

