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
$LedgerDetail=$obj_ledger_details->AllLegerDetail();
$data=$LedgerDetail;

//print_r($data);
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
	
	/*window.onfocus = function() {
		var result = localStorage.getItem('refreshPage');	
		//alert(result);
		if(result != null && result > 0 )
		{	
			localStorage.setItem('refreshPage', "0");
			location.reload();
		}
	};*/
	function ValidateDate()
	{
		var fromDate = document.getElementById('from_date').value;
		var toDate = document.getElementById('to_date').value;		
		var isFromDateValid = jsdateValidator('from_date',fromDate,minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate);
		var isToDateValid = jsdateValidator('to_date',toDate,minGlobalCurrentYearStartDate,maxGlobalCurrentYearEndDate);
		if(isFromDateValid == false || isToDateValid == false)
		{
			return false;	
		}
		return true;
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
	
	window.onfocus = function() {
		var result = localStorage.getItem('refreshPage');	
		//alert(result);
		if(result != null && result > 0 )
		{	
			localStorage.setItem('refreshPage', "0");
			location.reload();
		}
	};

	</script>
    <script> 
	//VoutherNumber.push("<?php// echo $data->sID.'@@@'.$data->sVoucherTypeID; ?>");
	var aryVoucherNumber=[];		
    function SelectAllVoucher(objVoucher)
	{	
		var table = $('#example').DataTable();
	table.page.len( -1 ).draw();

	for(var iVoucher = 0; iVoucher <  aryVoucherNumber.length; iVoucher++)
	{
		//alert(aryUnit[iUnits].unit);
		document.getElementById('check_' + aryVoucherNumber[iVoucher]).checked = objVoucher.checked;
		
		
		}
	}
		
	function CheckVoucher()
{
	var table = $('#example').DataTable();
	table.page.len( -1 ).draw();

	var aryVoucherCheck=[];
	//alert(chkBox.Voucher+ ":" + chkBox.checked);
	for(var iVoucher = 0; iVoucher <  aryVoucherNumber.length; iVoucher++)
	{
		var sKey = 'check_' + aryVoucherNumber[iVoucher];
		//alert(sKey);
		var sVal = document.getElementById(sKey).checked;
		
		//sKey = 'amt_' + sKey;
		//sBtn = 'btn_' + unitID;
		
		if(sVal== true)
		{
			//document.getElementById(sKey).disabled = false;
			aryVoucherCheck.push(aryVoucherNumber[iVoucher]);
			//alert("test");
		}
		

		//alert(aryUnit[iUnits].unit);
		//document.getElementById('check_' + aryVoucherNumber[iVoucher]).checked = objVoucher.checked;
		
		
		}
		
		if(aryVoucherCheck.length > 0)
		{
		//	alert("hi");
			 window.open("all_print_voucher.php?vchno=" + JSON.stringify(aryVoucherCheck));
		}
		else{
					alert("Please select check box...");			
			}
		
}
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
$categoryid=$obj_ledger_details->obj_utility->getParentOfLedger($data[0]['id']);
//print_r($categoryid);
?>
<br><br>
<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">All Ledger Report </div>
        <br>
    
		<div style="padding-left: 15px;padding-bottom: 10px;"><button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;" id="btnBack"><i class="fa  fa-arrow-left"></i></button>
		 <center> 
         <br><br>
           <p>&nbsp;</p>  
        <div align="center"> 
<form name="filter" id="filter" action="ledger_voucher_detail.php" method="post" onSubmit="return ValidateDate();">
	<table style="width:80%; border:1px solid black; background-color:transparent; ">
    <tr> <td colspan="3"><br/> </td></tr>
    	<tr>
        	<td  style="width:20%"> &nbsp; Please Select Group : </td>            
        	<td style="width:28%;">
        		<select name="groupid" id="groupid" >	
               		<!--<option value="all"> All </option>-->	                    
             	 <?php echo $Groups = $obj_ledger_details->combobox("select `id`,`groupname` from `group` where status='Y'", $_REQUEST['groupid']); ?>   
                  
			   </select>
   			</td>
            <td> From :</td>                      
			<td><input type="text" name="from_date" id="from_date"  class="basics" size="10" style="width:80px;" value = "<?php echo getDisplayFormatDate($_SESSION['default_year_start_date'])?>"/></td>
            <td> To :</td>                     
			<td><input type="text" name="to_date" id="to_date"  class="basics" size="10" style="width:80px;" value="<?php echo getDisplayFormatDate($_SESSION['default_year_end_date']);?>"/></td>
            <td></td><td></td>
            <!--<td> Transaction Type :</td>
            <td>
            	<select name="tran_type" id="tran_type" style="width:80px;">
                	<option value="0">All</option>
                    <option value="1">Withdrawals</option>
                    <option value="2">Deposits</option>
                </select>
           	</td>
-->            <td><input type="submit" name="submit" value="Submit"   class="btn btn-primary"/> </td>
     	</tr>        
        <tr> <td colspan="3"><br/> </td></tr>
    </table>
</form>
<br>     
<br>
<br>
<p>&nbsp;</p>
<div> <input type="button"  class="btn btn-primary" value="All Voucher Print" onclick="CheckVoucher(this)"></div>

<?php //echo $data[$k]['VoucherID'];?>
 <div style="width:100%;">
<table id="example" class="display"  width="100%" >
	<thead>
        <tr>
        	<th ><input type="checkbox" name="allcheck" id="allcheck" onclick="SelectAllVoucher(this)"></th>
        	 <th>Print</th>
        	<th >View</th>
            <th  >Edit</th>
            <th >Delete</th>
            <th  >Date</th>
            <th  >Particular</th>
            <th >Voucher Type</th>
            <th >Voucher No</th>
            <th  >Cheque/Bill Number/Description</th>
            <th  class="sum">Amount</th>
            <!--<th  class="sum" >Credit</th>-->
            <th  >Balance</th>
            
          <!-- <th style="display:none;"  class="showwhileprint">Note</th>-->
           <th >Note</th>
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
						if($data[$k]['VoucherTypeID'] <> VOUCHER_SALES)
						{?>
						<script>
						 aryVoucherNumber.push("<?php echo $data[$k]['VoucherNo']?>");
						</script>
						<?php }
					//$categoryid=$obj_utility->getParentOfLedger($data[$k]['id']);
					$categoryid=$obj_ledger_details->obj_utility->getParentOfLedger($data[$k]['id']);
					//print_r ($categoryid);
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
				<tr id="tr_<?php echo $data[$k]['id']; ?>">
                  <?php  if($data[$k]['VoucherTypeID']==VOUCHER_SALES)
			   	{?>
                  <td><input type="checkbox" name="check" id="check_<?php echo $data[$k]['VoucherNo']?>"  value="<?php $data[$k]['VoucherNo']?>" style="display:none;"></td>
                  <?php }
                  else
                  {?>
                  <td><input type="checkbox" name="check" id="check_<?php echo $data[$k]['VoucherNo']?>"  value="<?php $data[$k]['VoucherNo']?>"></td>
                  <?php }?>
                <?php $voucher_details=$obj_ledger_details->get_voucher_details('',$data[$k]['id']);
                 if($data[$k]['VoucherTypeID']==VOUCHER_PAYMENT || $data[$k]['VoucherTypeID']==VOUCHER_RECEIPT || $data[$k]['VoucherTypeID']==VOUCHER_CONTRA || $data[$k]['VoucherTypeID']==VOUCHER_JOURNAL)
                   {?>
                <td align='center' valign='top'><a href="print_voucher.php?&vno=<?php echo base64_encode($data[$k]['VoucherNo']);?>&type=<?php echo base64_encode($data[$k]['VoucherTypeID']);?>" target="_blank" ><img src="images/print.png" border='0' alt='Print' style='cursor:pointer;' width="25" height="10" /></a></td>
               <?php }
			   else if($data[$k]['VoucherTypeID']==VOUCHER_SALES)
			   	{ 
				$BillType=$obj_ledger_details-> BillType($data[$k]['RefNo']);
				//print_r($BillType);
				?>
               	<td align='center' valign='top'><a href="Maintenance_bill.php?UnitID=<?php echo $data[$k]['By'];?>&PeriodID=<?php echo $data[$k]['PeriodID'];?>&BT=<?php echo $BillType[0]['BillType']?>" target="_blank" ><img src="images/print.png" border='0' alt='Print' style='cursor:pointer;' width="25" height="10" /></a></td>
               <?php } ?>
                   <td id="show<?php echo $data[$k]['id'];?>">
					<?php //$show_details  = $obj_ledger_details->details2($_REQUEST['VoucherID'],$_REQUEST['lid']);
					//echo 'opening balance'.$data[$k]['Is_Opening_Balance'];
					?>
                    
					<!--<a href="view_ledger_details.php?lid=<?php //echo $_REQUEST['lid'];?>&gid=<?php  //echo $_REQUEST['gid'];?>&vtype=<?php //echo $data[$k]['VoucherTypeID'];?>&id=<?php //echo $data[$k]['VoucherID'];?>" style="color:#0000FF;"><img src='images/view.jpg' border='0' alt='view' style='cursor:pointer;' width="18" height="15"/></a>-->
					<div onClick="ViewVoucherDetail('<?php echo $data[$k]['By'];?>', '<?php echo $categoryid['group'];?>', '<?php echo $data[$k]['VoucherTypeID'];?>', '<?php echo $data[$k]['id'];?>');" style="color:#0000FF;cursor: pointer;"><img src='images/view.jpg' border='0' alt='View' style='cursor:pointer;' width="18" height="15" /></div>
					
                    </td>
                <td align='center' valign='top'>
					<?php 
                    if($data[$k]['VoucherTypeID']==VOUCHER_JOURNAL)
                    {	
						$Url = $obj_ledger_details->CheckVoucherType($data[$k]['id']);						
						if($Url <> '')
						{							
                        ?>
                        	<a href="" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes'); "> <img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/> </a>
                        <?php } else{
							//echo "test";?>
                        <a  id='edit' href="VoucherEdit.php?Vno=<?php echo $voucher_details[0]['VoucherNo']; ?>&pg=<?php echo $_REQUEST['pg']; ?>"><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a>
                    
                    <?php }
					}
                    else if($data[$k]['VoucherTypeID']==VOUCHER_PAYMENT || $data[$k]['VoucherTypeID']==VOUCHER_RECEIPT || $data[$k]['VoucherTypeID']==VOUCHER_CONTRA)
                    {
						
                       $Url = $obj_ledger_details->generatUrl($data[$k]['id'],$data[$k]['VoucherTypeID']);					   
                        //Url = "PaymentDetails.php?bankid=".$bankID."&LeafID=".$chequeDetails[0]['ChqLeafID']."&CustomLeaf= ". $chequeDetails[0]['CustomLeaf']. "&edt=".$chkDetailID;																	
                     ?>
                     
                     <a href="" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes'); "> <img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/> </a>
                    <?php }
					else if($data[$k]['Is_Opening_Balance'] == 1)
					{
						 if($bIsFdCategory == true)
						 {
							 $Url = "FixedDeposit.php?edt=".$data[$k]['By'];
						 }
						 else
						 {
						 	$Url = "ledger.php?edt=".$data[$k]['By'];
						 }
					?>
					<a href="" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes');"> <img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/> </a>	
					<?php }?>
                </td>
                
                 <td align='center' valign='top'>
                <?php 
				if($data[$k]['VoucherTypeID']==VOUCHER_JOURNAL)
				{
					$Url = $obj_ledger_details->CheckVoucherType($data[$k]['id']);
						if($Url <> '')
						{
                        ?>
                        	<a href="" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes'); "> <img src='images/del.gif' border='0' alt='Delete' style='cursor:pointer;'/> </a>
                        <?php } else{
							//echo "test";?>
                <a  id='<?php echo "delete-".$voucher_details[0]['VoucherNo']."-".$categoryid['group']."-".$data[$k]['By']?>'  onClick='deleteVoucher(this.id);'><img src='images/del.gif' border='0' alt='Edit' style='cursor:pointer;'/></a>
                
				<?php }
				}
				else if($data[$k]['VoucherTypeID']==VOUCHER_PAYMENT || $data[$k]['VoucherTypeID']==VOUCHER_RECEIPT || $data[$k]['VoucherTypeID']==VOUCHER_CONTRA)
				{
					
				   $Url = $obj_ledger_details->generatUrl($data[$k]['id'],$data[$k]['VoucherTypeID'],true);
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
								echo $data[$k]['ledger_name'];
							}
							else
							{
								echo $data[$k]['ledger_name'];
							}
						}
						else if(($categoryid['group']==1 || $categoryid['group']==2) && $data[$k]['Is_Opening_Balance'] ==1)
						{
							echo 'Opening Balance';
						
						}
						else
						{
							echo $data[$k]['ledger_name'];
						}
					?>
                    </td>
					                    
                    <td ><?php $voucher_type =$obj_ledger_details->get_voucher_details($data[$k]['VoucherTypeID']);
								if($voucher_type=='' && $data[$k]['VoucherTypeID'] <> ""){echo '---';}else{echo $voucher_type;}?></td>
                    <td ><?php echo $data[$k]['VoucherNo']; ?></td>
                     <td style="text-align:center;">
                    <?php echo $obj_ledger_details->getChequeNumber($data[$k]['id']);
					//echo $ChequeNumber;?></td>
                    <td style="text-align:right;"><?php if($DebitAmt <> 0){echo number_format($DebitAmt, 2);}else{echo '';} ?></td>
					<!--<td  style="text-align:right; "><?php //if($CreditAmt <> 0){echo number_format($CreditAmt, 2);}else{echo '';} ?></td>-->
                    <td style="text-align:right;"><?php if($IsCreditor==true){echo number_format(abs($BalanceAmt), 2);} else{echo number_format($BalanceAmt, 2);}?></td>
                
                    <?php if($voucher_details[0]['Note']=='')
					{ ?>
                    
						<td  ><?php echo '---'; ?></td>	
					<?php }
					else
					{
                    ?>
                    <td  ><?php echo $voucher_details[0]['Note']; ?></td>
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


