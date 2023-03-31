<?php

include_once("dbconst.class.php");
class payment_report
{
	public $m_dbConn;
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;

	}

	public function getPaymentDetails($from_date,$to_date, $ledgerID)
	{
		$ledgername_array=array();
		if($ledgerID == "")
		{
			$sql="SELECT paymenttbl.id,paymenttbl.PaidTo,paymenttbl.ChequeDate,paymenttbl.ChequeNumber,paymenttbl.VoucherDate,vouchertbl.VoucherNo,vouchertbl.By,vouchertbl.To,vouchertbl.Debit,vouchertbl.Credit,paymenttbl.Comments FROM `paymentdetails`  as paymenttbl JOIN `voucher` as vouchertbl on vouchertbl.RefNo=paymenttbl.id  and vouchertbl.RefTableID='".TABLE_PAYMENT_DETAILS."' where vouchertbl.VoucherTypeID = '" . VOUCHER_PAYMENT . "' and `VoucherDate` between '".getDBFormatDate($from_date)."' and '".getDBFormatDate($to_date)."' ORDER BY paymenttbl.ChequeDate";
		}
		else
		{
			$sql="SELECT paymenttbl.id,paymenttbl.PaidTo,paymenttbl.ChequeDate,paymenttbl.ChequeNumber,paymenttbl.VoucherDate,vouchertbl.VoucherNo,vouchertbl.By,vouchertbl.To,vouchertbl.Debit,vouchertbl.Credit,paymenttbl.Comments FROM `paymentdetails`  as paymenttbl JOIN `voucher` as vouchertbl on vouchertbl.RefNo=paymenttbl.id  and vouchertbl.RefTableID='".TABLE_PAYMENT_DETAILS."' where vouchertbl.VoucherTypeID = '" . VOUCHER_PAYMENT . "' and `VoucherDate` between '".getDBFormatDate($from_date)."' and '".getDBFormatDate($to_date)."' and vouchertbl.By = '".$ledgerID."' ORDER BY paymenttbl.ChequeDate";
		}
		//echo $sql;
		$result=$this->m_dbConn->select($sql);	
		
		//echo $sql;
		$get_ledger_name="select id,ledger_name from `ledger`";
		$result02=$this->m_dbConn->select($get_ledger_name);
		
		//print_r($result02);
		for($i = 0; $i < sizeof($result02); $i++)
		{
		$ledgername_array[$result02[$i]['id']]=$result02[$i]['ledger_name'];
		
		}
		
		for($i = 0; $i < sizeof($result); $i++)
		{
			$result[$i]['BY'] = $ledgername_array[$result[$i]['By']];
			$result[$i]['TO'] = $ledgername_array[$result[$i]['PaidTo']];
		}
		//print_r($result);
		return $result;	
	}




}



?>