<?php

include_once "adduser.class.php";
include_once "initialize.class.php";
include_once "include/dbop.class.php";

class CommitteeNotification{
	
	public $m_dbConn;
	public $m_dbConnRoot;
	public $m_bShowTrace;
	public $obj_addduser;
	public $obj_initialize;
	
	function __construct(){
		
		$this->m_bShowTrace = 1;
		$this->m_dbConn = new dbop();
		$this->m_dbConnRoot = new dbop(true);
		$this->obj_addduser = new adduser($this->m_dbConnRoot,$this->m_dbConn);
		$this->obj_initialize = new initialize($this->m_dbConnRoot);
	}
	
	
	public function sentPaymentVoucherNotificationToCommittee($VoucherNo){
	
	
	$Body = '<center>
				<div style="border: 1px solid #cccccc;width:95%;" id="voucher">
					<div id="bill_header" style="text-align:center;padding: 10px;">
						<div id="society_name"><b>'.$objFetchData->objSocietyDetails->sSocietyName.'</b></div>
						<div id="society_reg" style="font-size:14px;">';
							
							if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
							{
								echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
							}
							
			$Body .= 	'</div>
						<div id="society_address"; style="font-size:14px;">'.$objFetchData->objSocietyDetails->sSocietyAddress.'</div>
						<div id="bill_subheader" style="text-align:center;">
							<div style="font-weight:bold; font-size:17px; padding-top:5px;">"'.$VoucherArray[0]['Type'].'"</div>
						</div>
					</div>';
				
				if($voucherType <> VOUCHER_SALES && ($voucherType == VOUCHER_CONTRA || $voucherType == VOUCHER_PAYMENT))
				{
	$Body .= 	'<table id="table1" border="1">
				<table style="font-size:17px;border: 1px solid #cccccc; border-collapse:collapse;width:100%; border-left:none; border-right:none; ">
					<tr>
							<td style="text-align:left;width:10%; padding-left:10px;" >Bank/Cash Name :</td>
							<td>:</td>
							<td >"'.$VoucherArray[0]['By'].'"</td>
							
						
							<td style="text-align:left; width:13%;" >'.$name.'</td>
						<td>:</td>
						<td style="text-align:left;width:10%;">'.$prefix.'</td>
				   </tr>
					<tr>
						<td style="text-align:left;width:13%;padding-left:10px;">';
						if($VoucherArray[0]['RefTableID'] == TABLE_PAYMENT_DETAILS || $VoucherArray[0]['RefTableID'] == TABLE_CHEQUE_DETAILS){ echo 'Cheque No';}
		$Body .= 		'</td>
						<td>';
						if($VoucherArray[0]['RefTableID'] == TABLE_PAYMENT_DETAILS || $VoucherArray[0]['RefTableID'] == TABLE_CHEQUE_DETAILS){ echo ':'; }
		$Body .=		'</td>
						<td >';
						if($VoucherArray[0]['RefTableID'] == TABLE_PAYMENT_DETAILS || $VoucherArray[0]['RefTableID'] == TABLE_CHEQUE_DETAILS){ if($VoucherArray[0]['ChequeNumber'] == -1){echo 'Cash';}else{echo $VoucherArray[0]['ChequeNumber']; }}
		$Body .=		'</td>
						<td style="text-align:left;" >'.$date.'</td>
						<td>:</td>
						<td style="text-align:left;width:15%;">'.$VoucherArray[0]['Date'].'</td>
					</tr>
					 
					<tr>
							<td style="text-align:left;width:10%; padding-left:10px;" >Paid To</td>
							<td>:</td>
							<td >'.$VoucherArray[1]['To'].'</td>
					</tr> 
								
					<tr><td colspan="6" style="padding:10px;text-align:left;width:13%;" ></td></tr>
				</table>
				<table style="font-size:17px;width:100%; border:none;">
					<tr>
						<th style="text-align:center;border: 1px solid #cccccc;border-left:none;border-top:none;border-collapse:collapse;">Sr. No.</th>
						<th style="text-align:center;border: 1px solid #cccccc;border-left:none;border-top:none;border-collapse:collapse;">Expense Head</th>
						<th style="text-align:center;border: 1px solid #cccccc;border-left:none;border-top:none;border-collapse:collapse;">Invoice Amount</th>
						<td style="text-align:center; border: 1px solid #cccccc;font-weight:bold;border-left:none;border-top:none;border-collapse:collapse;" >TDS Amount</td>
						<td  style="text-align:center; border: 1px solid #cccccc;font-weight:bold; border-right:none;border-top:none;border-collapse:collapse;">Amount(Rs.)</td>
					</tr>';
					for($i = 1;$i <= sizeof($VoucherArray) -1;$i++)
					{	
						if(!empty($VoucherArray[$i]['ExpenseBy']))
						{
							if($VoucherArray[$i]['InvoiceAmount'] > 0) 
							{ 
								$invoiceAmt = $VoucherArray[$i]['InvoiceAmount']; 			
							} 
							else 
							{ 
								$invoiceAmt = $VoucherArray[$i]['Credit']; 
							}		
						
		$Body .=		'<tr>
						<td style="text-align:center;border:1px solid #cccccc;border-left:none;border-collapse:collapse;width:10%;" >'.$i.'</td>
						<td style="border:1px solid #cccccc;border-left:none;border-collapse:collapse;width:30%;" >';
						if($VoucherArray[$i]['ExpenseBy'] <>"" ){ echo $VoucherArray[$i]['ExpenseBy'];}
		$Body .=		'</td>
						<td style="border: 1px solid #cccccc; text-align:right;border-right:none; border-collapse:collapse;width:20%;">'.number_format($invoiceAmt,2).'</td>
						<td style="border: 1px solid #cccccc; text-align:right; border-collapse:collapse;width:20%;">'.number_format($VoucherArray[$i]['TDSAmount'],2).'</td>
						<td  style="border: 1px solid #cccccc; text-align:right;border-right:none; border-left:none; border-collapse:collapse;width:20%;">'.number_format($invoiceAmt-$VoucherArray[$i]['TDSAmount'],2).'</td>        
					</tr>';
					
						}
					}
					if(sizeof($VoucherArray) < 5)
					{
						//adding emty tr to maintain standard size for voucher print
						for($i = 0;$i <= (5 - sizeof($VoucherArray)) ;$i++)
						{	
		$Body .=			'<tr style="empty-cells:hide; border-collapse: separate;">
							<td style="text-align:center;border:1px solid #cccccc;border-left:none;width:10%;" >&nbsp;</td>
							<td style="border:1px solid #cccccc;border-left:none;width:30%;" >&nbsp;</td>
							<td style="border: 1px solid #cccccc; text-align:right;border-right:none; width:20%;">&nbsp;</td>
							<td style="border: 1px solid #cccccc; text-align:right; width:20%;">&nbsp;</td>
							<td  style="border: 1px solid #cccccc; text-align:right;border-right:none; border-left:none; width:20%;">&nbsp;</td>        
						</tr>';
							 	
						}
					}
					
	$Body .=		'<tr>
						<th colspan="2" style="border: 1px solid #cccccc; border-collapse:collapse;text-align:right;">Total Payable (Rs.)</th>        
						<td style="border: 1px solid #cccccc; border-collapse:collapse;text-align:right;">'.number_format($total_invoiceamount,2).'</td>
						<td style="border: 1px solid #cccccc; border-collapse:collapse;text-align:right;">'.number_format($totalTDS,2).'</td>
						<td style="border: 1px solid #cccccc; text-align:right;border-right:none; border-left:none; border-collapse:collapse;">'.number_format($total_payable,2).'</td>
					</tr>
				</table>
				<table style="font-size:17px;width:100%; border:none;">
					<tr>
						<th style="width:23%;">Narration</th>      
						<td colspan="9"> :';
						if($VoucherArray[0]['Note'] <> ''){echo $VoucherArray[0]['Note'] ; }else{echo '-';}
	$Body .=			'</td>
					</tr>
					<tr style=" border:none;"><td><br></td></tr>
					<tr>
						<th style="border: 1px solid #cccccc; ;border-right: 0; border-left:none; border-top:none; border-collapse:collapse; width:23%;">Amount (In Words) </th>        
						<td style="border: 1px solid #cccccc; ;border-left: 0; border-collapse:collapse;border-top:none; border-right:none; " colspan="9"> :';
						if($total_payable <> ''){ echo "Rupees ".  $obj_Utility->convert_number_to_words($total_payable)." Only"; }
	$Body .=			'</td>
					</tr>
					
					<tr style=" border:none;"><td><br></td></tr>
					<tr style=" border:none;"><td><br></td></tr>
					</table>
				
                </table>';
                }
	}
}



?>