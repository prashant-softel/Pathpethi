<?php include_once("includes/head_s.php");
 include_once("classes/dbconst.class.php");
 include_once "classes/utility.class.php";
include_once("classes/FixedDeposit.class.php");
 ?>
<?php
include_once("classes/view_ledger_details.class.php");

$obj_ledger_details = new view_ledger_details($m_dbConn);
$m_objUtility = new utility($m_dbConn);
$sHeader = $m_objUtility->getSocietyDetails();

$obj_FixedDeposit = new FixedDeposit($m_dbConn);
$fdAccountArray = $obj_FixedDeposit->FetchFdCategories();
$bIsFdCategory = false;
$ledgerName = $m_objUtility->getLedgerName($_REQUEST['lid']);

if(isset($_SESSION['from_date']) && isset($_SESSION['to_date']) && isset($_REQUEST['dt']))
{
	
	$get_details = $obj_ledger_details->details($_REQUEST["gid"],$_REQUEST['lid'], getDBFormatDate($_SESSION['from_date']) , getDBFormatDate($_SESSION['to_date']));
	
}
else
{	
	$get_details = $obj_ledger_details->details($_REQUEST["gid"],$_REQUEST['lid'], "", "");

}

if($_REQUEST["gid"] == 1 || $_REQUEST["gid"] == 2)
{
	$date = ""; 
	$arLedgerParentDetails = $m_objUtility->getParentOfLedger($_REQUEST['lid']);
	if(!(empty($arLedgerParentDetails)))
	{
		$LedgerGroupID = $arLedgerParentDetails['group'];
		$LedgerCategoryID = $arLedgerParentDetails['category'];
	}
	if(isset($fdAccountArray))
	{
		if (in_array($arLedgerParentDetails['category'], $fdAccountArray) && $arLedgerParentDetails['group'] == ASSET)
		{
			$bIsFdCategory = true;
		}
	}
	if(isset($_SESSION['from_date']) && isset($_SESSION['to_date']) && isset($_REQUEST['dt']))
	{	
		$date = $_SESSION['from_date'];
	}
	else
	{
		$date = $m_objUtility->getCurrentYearBeginingDate($_SESSION['default_year']);		
	}
	
	if($date <> "")
	{
		$res = $m_objUtility->getOpeningBalance($_REQUEST['lid'],$date);
		
		if($res <> "")
		{
			if($LedgerCategoryID == BANK_ACCOUNT || $LedgerCategoryID == CASH_ACCOUNT)
			{
				$data[0] = array("id" => $_REQUEST['lid'] , "Date" => $res['OpeningDate'] , "Particular" => $res['LedgerName'] , "Debit" => ($res['OpeningType'] == TRANSACTION_CREDIT) ? $res['Total'] : 0 , "Credit" => ($res['OpeningType'] == TRANSACTION_DEBIT) ? $res['Total'] : 0  , "VoucherID" => 0 , "VoucherTypeID" => 0 , "Is_Opening_Balance" => 1);
			}
			else
			{
				$data[0] = array("id" => $_REQUEST['lid'] , "Date" => $res['OpeningDate'] , "Particular" => $res['LedgerName'] , "Debit" => ($res['OpeningType'] == TRANSACTION_DEBIT) ? $res['Total'] : 0 , "Credit" => ($res['OpeningType'] == TRANSACTION_CREDIT) ? $res['Total'] : 0  , "VoucherID" => 0 , "VoucherTypeID" => 0 , "Is_Opening_Balance" => 1);		
			}
			if($get_details <> "")
			{
				for($i = 0 ; $i < sizeof($get_details); $i++)
				{
					$data[$i + 1] = $get_details[$i];
				}
			}
		}
		else
		{
			$data = $get_details;	
		}
	}
}
else
{
	$data = $get_details;		
}
$IsCreditor=$obj_ledger_details->IsCreditor($_REQUEST['lid']);
?>

<?php

 if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
else
{
	include_once("includes/head_s.php");
}

if(isset($_GET['ssid'])){if($_GET['ssid']<>$_SESSION['society_id']){?><script>window.location.href = "logout.php";</script><?php }}
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<link href="css/messagebox.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/ajax.js"></script>      
    <script type="text/javascript" src="js/jsViewLedgerDetails.js"></script>      
	<script type="text/javascript" src="js/ajax_new.js"></script> 
	<script language="javascript" type="application/javascript">
	function go_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
		});
        setTimeout('hide_error()',8000);	
    }
    function hide_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});
    }
	//show();
	function show()
	{
	//alert("test 1");
	
	//window.location.search = jQuery.query.set("gid",vid);
	<?php if(isset($_GET['id'])){
	
		?> //=document.write(vid);
	//alert("test 2");
document.getElementById("show<?php echo $_GET['id'];?>").innerHTML="<?php echo $obj_ledger_details->details2($_REQUEST['lid'],$_GET['id'],$_GET['vtype']);?>";

	<?php }
	?>		
	}
	
	window.onfocus = function() {
		var result = localStorage.getItem('refreshPage');	
		//alert(result);
		if(result != null && result > 0 )
		{	
			localStorage.setItem('refreshPage', "0");
			location.reload();
		}
	};
	
	
	
	/*function displayNote()
	{
		var table = $('#example').DataTable();
		table.page.len( -1 ).draw();
		var Chkstatus = document.getElementById('showNote');
			
		if(Chkstatus.checked) 
		{
			$('.showwhileprint').show();		
		}
		else
		{
			$('.showwhileprint').hide();	
		}
			
	}*/

	
	</script>
    <script>
		/*$(document).ready(function() {
	
	 $('#example').dataTable({
            "bDestroy": true
        }).fnDestroy();

  
	 $('#example').dataTable({
                dom: 'Bfrtip',
        buttons: [
            {
                extend: 'colvis',
                postfixButtons: [ 'colvisRestore' ]
            }
        ],
        columnDefs: [
            {
                targets: -1,
                visible: false
            }
        ]
		 });

} );*/
	</script>
 <style>
 @media print {
  /* style sheet for print goes here */
  .PrintClass
  {
		display:block;
   }
}

 </style>   
</head>
<body>
<center>
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

<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Ledger Report </div>
        <br>
		<div style="padding-left: 15px;padding-bottom: 10px;"><button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;" id="btnBack"><i class="fa  fa-arrow-left"></i></button>
		 <center>  <?php
	  	if($catid['category']!=BANK_ACCOUNT && $catid['category']!= CASH_ACCOUNT )
		{?>	 <button type="button" class="btn btn-primary" onClick="ShowJV('<?php echo $_REQUEST['lid']; ?>');" style="">Create JV</button> <?php } ?> 
        <button type="button" class="btn btn-primary" onClick="ShowBankAccountDetails();" style="">Payment/Receipt </button> </center></div>
        <center><font style="font-size:14px;"><b><a href="ledger.php?edt=<?php echo $_REQUEST['lid']?>" ><?php echo  $ledgerName;/*$data[0]['Particular']; if($data[1]['owner_name'] <> ""){echo ' - ' .$data[1]['owner_name'] ;}*/?></a></b></font></center>
       <?php
		if(isset($_SESSION['from_date']) && isset($_SESSION['to_date']) && isset($_REQUEST['dt']))
		{ ?>
		<h5 align="center">FROM <?php echo getDisplayFormatDate($_SESSION['from_date']); ?> TO <?php echo getDisplayFormatDate($_SESSION['to_date']);?></h5>
		<?php 
		} 
		
		$catid=$obj_ledger_details->obj_utility->getParentOfLedger($_REQUEST['lid']);		
		?>
<br>     


<?php echo $data[$k]['VoucherID'];?>
<div style="width:100%;">
<table id="example" class="display" cellspacing="0" width="100%" >
	<thead>
        <tr>
        	 <th>Print</th>
        	<th>View</th>
            <th >Edit</th>
            <th >Delete</th>
            <th >Date</th>
            <th >Particular</th>
            <th>Voucher Type</th>
            <th >Voucher No</th>
            <th >Cheque/Bill Number</th>
            <th  class="sum">Debit</th>
            <th  class="sum">Credit</th>
            <th >Balance</th>
            
          <!-- <th style="display:none;"  class="showwhileprint">Note</th>-->
           <th>Note</th>
        </tr>
    </thead>
    <tfoot class="footerClass">
            <tr style="font-size:12px; ">
                <th style="text-align:center;" colspan="9"> Grand Total </th>
                <th style="text-align:right;padding-right: 10px;"></th>
                <th style="text-align:right;padding-right: 10px;"></th>
                <th style="text-align:right;padding-right: 10px;" id="BalanceAmount"></th>
              </tr>
        </tfoot>
   	<tbody>  
        <?php
		$BalanceAmt=0;
		if($data<>"")
		{			
				foreach($data as $k => $v)
				{
					if(isset($data[$k]['id'])){
					//$categoryid=$obj_utility->getParentOfLedger($data[$k]['id']);
					$categoryid=$obj_ledger_details->obj_utility->getParentOfLedger($data[$k]['id']);
					
					$Is_Opening_Balance=$data[$k]['Is_Opening_Balance'];
					//echo 'CATEGORYID'.$categoryid['category'];
					if($categoryid['category']==BANK_ACCOUNT || $categoryid['category']== CASH_ACCOUNT )
					{ 
							$CreditAmt = $data[$k]['Debit'];
							$DebitAmt = $data[$k]['Credit'];	
							
							if($Is_Opening_Balance==1 && $CreditAmt <> 0)
							{
								$BalanceAmt -= $CreditAmt;
							}
							else if($Is_Opening_Balance==1 && $DebitAmt <> 0)
							{
								$BalanceAmt += $DebitAmt;
							}
							else
							{
								if($DebitAmt <> 0 )
								{
									$BalanceAmt += $DebitAmt;
								}
								
								if($CreditAmt <> 0)
								{
									$BalanceAmt -= $CreditAmt;
								}																	
							}
					
					}
					else
					{
						$DebitAmt = $data[$k]['Debit'];
						$CreditAmt = $data[$k]['Credit'];
						//$BalanceAmt = $BalanceAmt + $CreditAmt - $DebitAmt;
						$BalanceAmt = $BalanceAmt + $DebitAmt - $CreditAmt;
					}
				?>
				<tr id="tr_<?php echo $data[$k]['VoucherID']; ?>">
                <?php $voucher_details=$obj_ledger_details->get_voucher_details('',$data[$k]['VoucherID']);
		//var_dump($voucher_details);
                 if($data[$k]['VoucherTypeID']==VOUCHER_PAYMENT || $data[$k]['VoucherTypeID']==VOUCHER_RECEIPT || $data[$k]['VoucherTypeID']==VOUCHER_CONTRA || $data[$k]['VoucherTypeID'] == VOUCHER_JOURNAL || $data[$k]['VoucherTypeID'] == VOUCHER_CREDIT_NOTE || $data[$k]['VoucherTypeID'] == VOUCHER_DEBIT_NOTE)
                 {
					 if($data[$k]['VoucherTypeID'] == VOUCHER_CREDIT_NOTE || $data[$k]['VoucherTypeID'] == VOUCHER_DEBIT_NOTE || $voucher_details[0]['RefTableID'] == TABLE_SALESINVOICE) 
					 {
						$DebitOrCreditOrInvoiceDetails = $obj_ledger_details->getDebitCreditDetails($voucher_details[0]['RefNo'],$voucher_details[0]['RefTableID']);
						
						if($voucher_details[0]['RefTableID'] == TABLE_SALESINVOICE)
						{
							$InvoicepageUrl = "Invoice.php?UnitID=".$DebitOrCreditOrInvoiceDetails[0]['UnitID']."&inv_number=".$DebitOrCreditOrInvoiceDetails[0]['Inv_Number'];
						}
						else
						{
							$InvoicepageUrl = "Invoice.php?debitcredit_id=".$voucher_details[0]['RefNo']."&UnitID=".$DebitOrCreditOrInvoiceDetails[0]['UnitID']."&NoteType=".$DebitOrCreditOrInvoiceDetails[0]['Note_Type'];
						}
							 
					?>
                    <td align='center' valign='top'><a href="<?php echo $InvoicepageUrl ;?>" target="_blank" ><img src="images/print.png" border='0' alt='Print' style='cursor:pointer;' width="25" height="10" /></a></td>      
                     <?php }else
					 {?>
                     
                <td align='center' valign='top'><a href="print_voucher.php?&vno=<?php echo base64_encode($voucher_details[0]['VoucherNo']);?>&type=<?php echo base64_encode($data[$k]['VoucherTypeID']);?>" target="_blank" ><img src="images/print.png" border='0' alt='Print' style='cursor:pointer;' width="25" height="10" /></a></td>
               <?php }
			   }
			   else{ ?>
               	<td align='center' valign='top'></td>
               <?php } ?>
                   <td id="show<?php echo $data[$k]['VoucherID'];?>">
					<?php //$show_details  = $obj_ledger_details->details2($_REQUEST['VoucherID'],$_REQUEST['lid']);
					//echo 'opening balance'.$data[$k]['Is_Opening_Balance'];
					if(($_REQUEST['gid']==1 || $_REQUEST['gid']==2) && $data[$k]['Is_Opening_Balance'] ==1)
					{
						//echo 'Opening Balance';
					
					}
					else 
					{
					?>
                    
					<!--<a href="view_ledger_details.php?lid=<?php //echo $_REQUEST['lid'];?>&gid=<?php  //echo $_REQUEST['gid'];?>&vtype=<?php //echo $data[$k]['VoucherTypeID'];?>&id=<?php //echo $data[$k]['VoucherID'];?>" style="color:#0000FF;"><img src='images/view.jpg' border='0' alt='view' style='cursor:pointer;' width="18" height="15"/></a>-->
					<div onClick="ViewVoucherDetail('<?php echo $_REQUEST['lid'];?>', '<?php echo $_REQUEST['gid'];?>', '<?php echo $data[$k]['VoucherTypeID'];?>', '<?php echo $data[$k]['VoucherID'];?>');" style="color:#0000FF;cursor: pointer;"><img src='images/view.jpg' border='0' alt='View' style='cursor:pointer;' width="18" height="15" /></div>
					<?php }?>
                    </td>
                
				
				<td align='center' valign='top'>
					<?php
					if($data[$k]['VoucherTypeID'] == VOUCHER_CREDIT_NOTE || $data[$k]['VoucherTypeID'] == VOUCHER_DEBIT_NOTE || $voucher_details[0]['RefTableID'] == TABLE_SALESINVOICE)
					{
						//echo '<br>URL : '.$InvoicepageUrl;
						$Url = $InvoicepageUrl."&edt";
					?>
						
                        <a href="<?php echo $Url ;?>" target="_blank" > <img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/> </a>							
					
					<?php }
					
					 
                    elseif($data[$k]['VoucherTypeID']==VOUCHER_JOURNAL)
                    {	
						$Url = $obj_ledger_details->CheckVoucherType($data[$k]['VoucherID']);						
						if($Url <> '')
						{							
                        ?>
                        	<a href="" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes'); "> <img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/> </a>
                        <?php } else{
							//echo "test";?>
                        <a  id='edit' href="" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes'); "><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a>
                    
                    <?php }
					}
                    else if($data[$k]['VoucherTypeID']==VOUCHER_PAYMENT || $data[$k]['VoucherTypeID']==VOUCHER_RECEIPT || $data[$k]['VoucherTypeID']==VOUCHER_CONTRA)
                    {
						
                       $Url = $obj_ledger_details->generatUrl($data[$k]['VoucherID'],$data[$k]['VoucherTypeID']);					   
                        //Url = "PaymentDetails.php?bankid=".$bankID."&LeafID=".$chequeDetails[0]['ChqLeafID']."&CustomLeaf= ". $chequeDetails[0]['CustomLeaf']. "&edt=".$chkDetailID;		
						//echo "url: ".$Url;
						if($Url == "")
						{
						}
						else
						{
                     ?>
                     
                     <a href="" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes'); "> <img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/> </a>
                    <?php }
					 }
					else if($data[$k]['Is_Opening_Balance'] == 1)
					{
						 if($bIsFdCategory == true)
						 {
							 $Url = "FixedDeposit.php?edt=".$_REQUEST['lid'];
						 }
						 else
						 {
						 	$Url = "ledger.php?edt=".$_REQUEST['lid'];
						 }
					?>
					<a href="" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes');"> <img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/> </a>	
					<?php }
					?>
                </td>
                
                 <td align='center' valign='top'>
                <?php
				if($data[$k]['VoucherTypeID'] == VOUCHER_CREDIT_NOTE || $data[$k]['VoucherTypeID'] == VOUCHER_DEBIT_NOTE || $voucher_details[0]['RefTableID'] == TABLE_SALESINVOICE)
				{ 
					if($voucher_details[0]['RefTableID'] == TABLE_SALESINVOICE)
					{?>
						<img src='images/del.gif' border='0' alt='Edit' onClick="deleteInvoice('<?php echo $voucher_details[0]['RefNo'];?>','<?php echo $DebitOrCreditOrInvoiceDetails[0]['UnitID'];?>','<?php echo $DebitOrCreditOrInvoiceDetails[0]['Inv_Date'];?>')" style='cursor:pointer;'/>
					<?php }else
					{
				?>
					 <img src='images/del.gif' border='0' alt='Edit' onClick="deleteDebitorCredit('<?php echo $voucher_details[0]['RefNo'];?>','<?php echo $DebitOrCreditOrInvoiceDetails[0]['Note_Type'];?>')" style='cursor:pointer;'/>
				<?php }
				}
				else if($data[$k]['VoucherTypeID']==VOUCHER_JOURNAL)
				{ 
				
				 $ExistVoucher = $obj_ledger_details->InvoiceStatus($voucher_details[0]['VoucherNo']);
				$Url = $obj_ledger_details->CheckVoucherType($data[$k]['VoucherID']);
					
						if(($Url <> '') && ($ExistVoucher[0]['InvoiceClearedVoucherNo'] > 0 || $ExistVoucher[0]['TDSVoucherNo'] > 0  ))
						{
                        ?>
                        	<a href="" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes'); "> <img src='images/del.gif' border='0' alt='Delete' style='cursor:pointer;'/> </a>
                        <?php } else {
							    ?>
                <a  id='<?php echo "delete-".$voucher_details[0]['VoucherNo']."-".$_REQUEST["gid"]."-".$_REQUEST['lid']?>'  onClick='deleteVoucher(this.id);'><img src='images/del.gif' border='0' alt='Edit' style='cursor:pointer;'/></a>
                
				<?php }
				}
				else if($data[$k]['VoucherTypeID']==VOUCHER_PAYMENT || $data[$k]['VoucherTypeID']==VOUCHER_RECEIPT || $data[$k]['VoucherTypeID']==VOUCHER_CONTRA)
				{
					
				   $Url = $obj_ledger_details->generatUrl($data[$k]['VoucherID'],$data[$k]['VoucherTypeID'],true);
				 ?>
				 
				 <a href="" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes'); "> <img src='images/del.gif' border='0' alt='Delete' style='cursor:pointer;'/> </a>
				<?php }?>
                </td>
					<td ><?php echo getDisplayFormatDate($data[$k]['Date']);?></td>
					<td>
					<?php
						if($data[$k]['VoucherTypeID']==VOUCHER_JOURNAL || $data[$k]['VoucherTypeID']==VOUCHER_SALES)
						{ 
							if($DebitAmt <> 0)
							{
								$finalData = $obj_ledger_details->get_voucher_details($data[$k]['VoucherTypeID'],$data[$k]['VoucherID'],$_REQUEST['lid'],$_REQUEST["gid"], "To");
								echo $finalData[0]['ledger_name'];
							}
							else
							{
								$finalData =  $obj_ledger_details->get_voucher_details($data[$k]['VoucherTypeID'],$data[$k]['VoucherID'],$_REQUEST['lid'],$_REQUEST["gid"], "By");
								echo $finalData[0]['ledger_name'];
							}
						}
						else if(($_REQUEST['gid']==1 || $_REQUEST['gid']==2) && $data[$k]['Is_Opening_Balance'] ==1)
						{
							echo 'Opening Balance';
						
						}
						else
						{
							$finalData = $obj_ledger_details->get_voucher_details($data[$k]['VoucherTypeID'],$data[$k]['VoucherID'],$_REQUEST['lid'],$_REQUEST["gid"]);
							echo $finalData[0]['ledger_name'];
						}
					?>
                    </td>
					                    
                    <td ><?php $voucher_type =$obj_ledger_details->get_voucher_details($data[$k]['VoucherTypeID']);
								if($voucher_type=='' && $data[$k]['VoucherTypeID'] <> ""){echo '---';}else{echo $voucher_type;}?></td>
                    <td ><?php echo $voucher_details[0]['VoucherNo']; ?></td>
                     <td style="text-align:center;">
                    <?php echo $obj_ledger_details->getChequeNumber($data[$k]['VoucherID']);
					//echo $ChequeNumber;?></td>
                    <td style="text-align:right;"><?php if($DebitAmt <> 0){echo number_format($DebitAmt, 2);}else{echo '';} ?></td>
					<td  style="text-align:right;"><?php if($CreditAmt <> 0){echo number_format($CreditAmt, 2);}else{echo '';} ?></td>
                    <td style="text-align:right;"><?php if($IsCreditor==true){echo number_format(abs($BalanceAmt), 2);} else{echo number_format($BalanceAmt, 2);}?></td>
                
                    <?php if($voucher_details[0]['Note']=='')
					{ ?>
                    
						<td ><?php echo '---'; ?></td>	
					<?php }
					else
					{
                    ?>
                    <td ><?php echo $voucher_details[0]['Note']; ?></td>
					<?php } ?>
                </tr>
				 <?php
				}
				}
				?>
<script>document.getElementById('BalanceAmount').innerHTML = format(<?php echo $BalanceAmt?>,2)</script> 	
 <?php                
		}
		
		else
		{
			?>
            <!--</tbody><tr height="25"><td colspan="9" align="center"><font color="#FF0000"><b>Records Not Found....</b></font></td></tr>-->
            <?php	
		}
		?>
        
</tbody>
</table>
</div>
</center>
<script>
show();</script>	

</div>	
<?php include_once "includes/foot.php"; ?>

<div id="openDialogOk" class="modalDialog" >
	<div style="margin:2% auto; ">
		<div id="message_ok">
		</div>
	</div>
</div>

<script>
if (window.opener) {
   // alert('inside a pop-up window or target=_blank window');
	document.getElementById('btnBack').style.visibility = 'hidden';
} 
else {
    document.getElementById('btnBack').style.visibility = 'visible';
}

</script>
<script src="js/jsCommon_20190326.js"></script> 
<script>


$(document).keyup(function(e) {    
    if (e.keyCode == 27) 
	{ 
		//escape key
		var sHeaders = document.getElementsByClassName('PrintClass'), i;
	
		for (i = 0; i < sHeaders.length; i += 1)
		{
			sHeaders[i].style.display = 'none';
		}
    }
});

$(document).ready(function() {
	
printMessage = '<?php echo $sHeader?> ';
printMessage += '<center><font style="font-size:14px;" id="ledger_name" class="PrintClass"><b><?php echo $data[0]['Particular']; if($data[1]['owner_name'] <> ""){echo ' - ' .$data[1]['owner_name'] ;}?></b></font></center>';
 $('#example').dataTable(
 {
	"bDestroy": true
}).fnDestroy();

if(localStorage.getItem("client_id") != "" && localStorage.getItem("client_id") != 1)
{
		$('#example').dataTable( {
						dom: 'T<"clear">Blfrtip',
						"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
						buttons: 
						[
							{
								extend: 'colvis',
								width:'inherit'/*,
								collectionLayout: 'fixed three-column'*/
							}
						],
					 columnDefs: 
					 [
						{
							targets: 12,
							visible: false
						}
					],
						"oTableTools": 
						{
							"aButtons": 
							[
								{ "sExtends": "copy", "mColumns": "visible" },
								{ "sExtends": "csv", "mColumns": "visible" },
								{ "sExtends": "xls", "mColumns": "visible" },
								{ "sExtends": "pdf", "mColumns": "visible"},
								{ "sExtends": "print", "mColumns": "visible","sMessage": printMessage + " "}
							],
						 "sRowSelect": "multi"
					},
					aaSorting : [],
						
					fnInitComplete: function ( oSettings, json ) {
						//var otb = $(".DTTT_container")
						//alert("fnInitComplete");
						$(".DTTT_container").append($(".dt-button"));
						
						//get sum of amount in column at footer by class name sum
						this.api().columns('.sum').every(function(){
						var column = this;
						var total = 0;
						var sum = column
							.data()
							.reduce(function (a, b) {
								if(a.length == 0)
								{
									a = '0.00';
								} 
								if(b.length == 0)
								{
									b = '0.00';
								}
								var val1 = parseFloat( String(a).replace(/,/g,'') ).toFixed(2);
								var val2 = parseFloat(String(b).replace(/,/g,'') ).toFixed(2);
								total = parseFloat(parseFloat(val1)+parseFloat(val2));
								return  total;
							});
					$(column.footer()).html(format(sum,2));
					});
					
					}
					
				} );	
	}
	else
	{
			$('#example').dataTable( {
						/*dom: 'T<"clear">lfrtip',*/
						dom: 'T<"clear">Blfrtip',
						"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
						buttons: 
						[
							{
								extend: 'colvis',
								width:'inherit'/*,
								collectionLayout: 'fixed three-column'*/
							}
						],
					 columnDefs: 
					 [
						{
							targets: 12,
							visible: false
						}
					],
						"oTableTools": 
						{
							"aButtons": 
							[
								{ "sExtends": "copy", "mColumns": "visible" },
								{ "sExtends": "csv", "mColumns": "visible" },
								{ "sExtends": "xls", "mColumns": "visible" },
								{ "sExtends": "pdf", "mColumns": "visible"},
								{ "sExtends": "print", "mColumns": "visible","sMessage": printMessage + " "}
							],
						 "sRowSelect": "multi"
					},
					aaSorting : [],
						
					fnInitComplete: function ( oSettings, json ) {
						//var otb = $(".DTTT_container")
						//alert("fnInitComplete");
						$(".DTTT_container").append($(".dt-button"));
						
						//get sum of amount in column at footer by class name sum
						this.api().columns('.sum').every(function(){
						var column = this;
						var total = 0;
						var sum = column
							.data()
							.reduce(function (a, b) {
								if(a.length == 0)
								{
									a = '0.00';
								} 
								if(b.length == 0)
								{
									b = '0.00';
								}
								var val1 = parseFloat( String(a).replace(/,/g,'') ).toFixed(2);
								var val2 = parseFloat(String(b).replace(/,/g,'') ).toFixed(2);
								total = parseFloat(parseFloat(val1)+parseFloat(val2));
								return  total;
							});
					$(column.footer()).html(format(sum,2));
					});
					
					}
					
				} );	
			
	}

} );		
</script>


