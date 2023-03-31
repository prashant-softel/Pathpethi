<?php
include_once("dbconst.class.php");
class income_report extends dbop
{
	public $m_dbConn;
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;

	}


public function getIncomeDetailsNew($from, $to)
{
	$ledgername_array=array();
	
//$sql="SELECT incometbl.Credit,incometbl.Debit,incometbl.Date,incometbl.LedgerID,vouchertbl.VoucherNo,vouchertbl.Note,incometbl.VoucherID from `incomeregister` as incometbl  JOIN `voucher` as vouchertbl on vouchertbl.id=incometbl.VoucherID where incometbl.Date between '".$from_date."' and '".$to_date."' GROUP BY  incometbl.LedgerID,incometbl.Date";
//$sql="SELECT DATE_FORMAT(v.`Date`,'%M %Y') AS MonthYear, Sum(i.TDSAmount) as TotalTDS, Sum(i.IGST_Amount) as TotalIGST, Sum(i.CGST_Amount) as TotalCGST,Sum(i.SGST_Amount) as TotalSGST, Sum(i.CESS_Amount) as TotalCESS FROM `invoicestatus` as i JOIN `voucher`as v ON i.InvoiceRaisedVoucherNo =v.VoucherNo where v.id in (Select min(id) from voucher group by VoucherNo) and (v.Date between '".getDBFormatDate($from)."' and '".getDBFormatDate($to)."') group by YEAR(Date),MONTH(Date) Order by Date";
$sql="SELECT SUM(incometbl.Credit) as Credit,SUM(incometbl.Debit) as Debit,incometbl.Date,incometbl.LedgerID from `incomeregister` as incometbl WHERE incometbl.Date between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."' GROUP BY incometbl.Date,incometbl.LedgerID  ORDER BY incometbl.LedgerID, incometbl.Date";
$result=$this->m_dbConn->select($sql);

$get_ledger_name="select id,ledger_name from `ledger`";
$result02=$this->m_dbConn->select($get_ledger_name);

//print_r($result02);
for($i = 0; $i < sizeof($result02); $i++)
{
$ledgername_array[$result02[$i]['id']]=$result02[$i]['ledger_name'];

}

for($i = 0; $i < sizeof($result); $i++)
{
	//$result[$i]['BY'] = $ledgername_array[$result[$i]['LedgerID']];
	$result[$i]['TO'] = $ledgername_array[$result[$i]['LedgerID']];
}
//print_r($result);
return $result;	
}

/*-----------------GST DETAILS------------------*/


public function get_InvoiceGSTDetails($from, $to)
{
	//echo "Begining of function";
	//echo "IGST_SERVICE_TAX 2<" .SGST_SERVICE_TAX . ">"; 
	$ledgername_array=array();
	
	$sql="SELECT DATE_FORMAT(v.`Date`,'%M %Y') AS MonthYear, Sum(i.TDSAmount) as TotalTDS, Sum(i.IGST_Amount) as TotalIGST, Sum(i.CGST_Amount) as TotalCGST,Sum(i.SGST_Amount) as TotalSGST, Sum(i.CESS_Amount) as TotalCESS FROM `invoicestatus` as i JOIN `voucher`as v ON i.InvoiceRaisedVoucherNo =v.VoucherNo where v.id in (Select min(id) from voucher group by VoucherNo) and (v.Date between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."') group by YEAR(Date),MONTH(Date) Order by Date";
	
$result=$this->m_dbConn->select($sql);
//print_r($result);
$get_ledger_name="select id,ledger_name from `ledger`";
$result02=$this->m_dbConn->select($get_ledger_name);

//print_r($result02);
for($i = 0; $i < sizeof($result02); $i++)
{
$ledgername_array[$result02[$i]['id']]=$result02[$i]['ledger_name'];

}

for($i = 0; $i < sizeof($result); $i++)
{
	//$result[$i]['BY'] = $ledgername_array[$result[$i]['LedgerID']];
	$result[$i]['TO'] = $ledgername_array[$result[$i]['LedgerID']];
}
//print_r($result);
return $result;	
}


public function getGSTDetails($from, $to)
{
	$ledgername_array=array();
	$sql="SELECT DATE_FORMAT(`Date`,'%M %Y') AS MonthYear,`LedgerID` , SUM(CASE WHEN (`LedgerID`='".IGST_SERVICE_TAX."') THEN Credit ELSE 0 END) AS 'IGST' , SUM(CASE WHEN (`LedgerID`='".CGST_SERVICE_TAX."') THEN Credit ELSE 0 END) AS 'CGST' , SUM(CASE WHEN (`LedgerID`='".SGST_SERVICE_TAX."') THEN Credit ELSE 0 END) AS 'SGST' , SUM(CASE WHEN (`LedgerID`='".CESS_SERVICE_TAX."') THEN Credit ELSE 0 END) AS 'CESS' FROM `incomeregister` WHERE Date between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."' and LedgerID IN ('".IGST_SERVICE_TAX."','".CGST_SERVICE_TAX."','".SGST_SERVICE_TAX."','".CESS_SERVICE_TAX."') group by YEAR(Date), MONTH(Date) Order by LedgerID , Date";
	
//$sql="SELECT incometbl.Credit,incometbl.Debit,incometbl.Date,incometbl.LedgerID,vouchertbl.VoucherNo,vouchertbl.Note,incometbl.VoucherID from `incomeregister` as incometbl  JOIN `voucher` as vouchertbl on vouchertbl.id=incometbl.VoucherID where incometbl.Date between '".$from_date."' and '".$to_date."' GROUP BY  incometbl.LedgerID,incometbl.Date";
//$sql="SELECT SUM(incometbl.Credit) as Credit,SUM(incometbl.Debit) as Debit,incometbl.Date,incometbl.LedgerID from `incomeregister` as incometbl WHERE incometbl.Date between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."' and incometbl.LedgerID IN ('".IGST_SERVICE_TAX."','".CGST_SERVICE_TAX."','".SGST_SERVICE_TAX."','".CESS_SERVICE_TAX."') GROUP BY incometbl.Date,incometbl.LedgerID  ORDER BY incometbl.LedgerID, incometbl.Date";
$result=$this->m_dbConn->select($sql);

$get_ledger_name="select id,ledger_name from `ledger`";
$result02=$this->m_dbConn->select($get_ledger_name);

//print_r($result02);
for($i = 0; $i < sizeof($result02); $i++)
{
$ledgername_array[$result02[$i]['id']]=$result02[$i]['ledger_name'];

}

for($i = 0; $i < sizeof($result); $i++)
{
	//$result[$i]['BY'] = $ledgername_array[$result[$i]['LedgerID']];
	$result[$i]['TO'] = $ledgername_array[$result[$i]['LedgerID']];
}
//print_r($result);
return $result;	
}


public function getIncomeDetails($from_date,$to_date)
{
	$ledgername_array=array();
	
$sql="SELECT incometbl.Credit,incometbl.Debit,incometbl.Date,incometbl.LedgerID,vouchertbl.VoucherNo,vouchertbl.Note,incometbl.VoucherID from `incomeregister` as incometbl  JOIN `voucher` as vouchertbl on vouchertbl.id=incometbl.VoucherID where incometbl.Date between '".getDBFormatDate($from_date)."' and '".getDBFormatDate($to_date)."' ";
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
	//$result[$i]['BY'] = $ledgername_array[$result[$i]['LedgerID']];
	$result[$i]['TO'] = $ledgername_array[$result[$i]['LedgerID']];
}
//print_r($result);
return $result;	
}


public function show_particulars($lid,$vid)
	{
		
	$sql2="select RefNo,RefTableID,VoucherNo from `voucher` where id=".$vid." ";
	//echo $sql2;
		$data2=$this->m_dbConn->select($sql2);
		$RefNo=$data2[0]['RefNo'];
		$RefTableID=$data2[0]['RefTableID'];
		$VoucherNo=$data2[0]['VoucherNo'];
		
		
		$sql3="select `ledger_name` from `voucher` as vouchertbl JOIN `ledger` as ledgertbl on vouchertbl.By=ledgertbl.id where vouchertbl.RefNo='".$RefNo."' and vouchertbl.RefTableID='".$RefTableID."' and vouchertbl.VoucherNo='".$VoucherNo."'";
	//echo $sql3;
		$data3=$this->m_dbConn->select($sql3);	
		return $data3[0]['ledger_name'];
	}

public function paid_InvoiceGSTDetails($from, $to)
{
	//echo "Begining of function";
	//echo "IGST_SERVICE_TAX 2<" .SGST_SERVICE_TAX . ">"; 
	$ledgername_array=array();
	//echo $sql="SELECT v.`Date` AS MonthYear, i.TDSAmount as TotalTDS, i.IGST_Amount as TotalIGST,i.CGST_Amount as TotalCGST,i.SGST_Amount as TotalSGST, i.CESS_Amount as TotalCESS,i.AmountReceived as TotalInvoiceAmount, i.TDSAmount as TDSAmounts, i.InvoiceRaisedVoucherNo,v.To,l.ledger_name as LegerName FROM `invoicestatus` as i JOIN `voucher`as v ON i.InvoiceRaisedVoucherNo =v.VoucherNo join ledger as l on v.To=l.id where v.To!='' and (v.Date between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."') order by l.ledger_name";
	 $sql="SELECT v.`Date` AS MonthYear, i.TDSAmount as TotalTDS, i.IGST_Amount as TotalIGST,i.CGST_Amount as TotalCGST,i.SGST_Amount as TotalSGST, i.CESS_Amount as TotalCESS,i.AmountReceived as TotalInvoiceAmount, i.TDSAmount as TDSAmounts, i.InvoiceRaisedVoucherNo,v.To,l.ledger_name as LegerName,ld.GSTIN_No,ld.PAN_No FROM `invoicestatus` as i JOIN `voucher`as v ON i.InvoiceRaisedVoucherNo =v.VoucherNo join ledger as l on v.To=l.id left join ledger_details as ld on l.id=ld.LedgerID where v.To!='' and (v.Date between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."') order by l.ledger_name";
	//echo  $sql="SELECT DATE_FORMAT(v.`Date`,'%M %Y') AS MonthYear, sum(i.TDSAmount) as TotalTDS, sum(i.IGST_Amount) as TotalIGST,sum(i.CGST_Amount) as TotalCGST,sum(i.SGST_Amount) as TotalSGST, sum(i.CESS_Amount) as TotalCESS,sum(i.AmountReceived) as TotalInvoiceAmount, sum(i.TDSAmount) as TDSAmounts,v.To,l.ledger_name as LegerName FROM `invoicestatus` as i JOIN `voucher`as v ON i.InvoiceRaisedVoucherNo =v.VoucherNo join ledger as l on v.To=l.id where v.To!='' and (v.Date between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."') group by v.To,YEAR(Date),MONTH(Date) Order by Date";
	//echo $sql="SELECT DATE_FORMAT(v.`Date`,'%M %Y') AS MonthYear, sum(i.TDSAmount) as TotalTDS, sum(i.IGST_Amount) as TotalIGST,sum(i.CGST_Amount) as TotalCGST,sum(i.SGST_Amount) as TotalSGST, sum(i.CESS_Amount) as TotalCESS,v.To FROM `invoicestatus` as i JOIN `voucher`as v ON i.InvoiceRaisedVoucherNo =v.VoucherNo where v.To!='' and (v.Date between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."')  group by v.To,YEAR(Date),MONTH(Date) Order by Date ";
$result=$this->m_dbConn->select($sql);
//print_r($result);
return $result;	
}
///------------------------------------------New GST Incoming report-------------------------------///
public function getGSTIncomingDetails($from, $to,$UnitID)
{
	
	$ledgername_array=array();
	
	 $sql="select SUM(`IGST`) as 'TotalIGST',SUM(`CGST`) as 'TotalCGST',SUM(`SGST`) as 'TotalSGST',SUM(`CESS`) as 'TotalCESS',UnitID,billregister.BillDate from billdetails join billregister on billdetails.BillRegisterID=billregister.ID where UnitID=".$UnitID." and (billregister.BillDate between '".getDBFormatDate($from)."' AND '".getDBFormatDate($to)."') order by UnitID";
$result=$this->m_dbConn->select($sql);

$get_ledger_name="select id,ledger_name from `ledger`";
$result02=$this->m_dbConn->select($get_ledger_name);

//print_r($result02);
for($i = 0; $i < sizeof($result02); $i++)
{
$ledgername_array[$result02[$i]['id']]=$result02[$i]['ledger_name'];

}

for($i = 0; $i < sizeof($result); $i++)
{
	//$result[$i]['BY'] = $ledgername_array[$result[$i]['LedgerID']];
	$result[$i]['TO'] = $ledgername_array[$result[$i]['LedgerID']];
}
//print_r($result);
return $result;	
}

}

?>