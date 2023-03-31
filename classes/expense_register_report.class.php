<?php
include_once("dbconst.class.php");
class expense_register_report extends dbop
{
	public $m_dbConn;
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;

	}


public function getExpenseDetails($from_date,$to_date)
{
	$ledgername_array=array();
	
$sql="SELECT expensetbl.id,expensetbl.LedgerID,expensetbl.ExpenseHead,expensetbl.Debit,vouchertbl.Date as VoucherDate,vouchertbl.Note,vouchertbl.VoucherNo,paymenttbl.ChequeDate,paymenttbl.ChequeNumber, paymenttbl.PayerBank FROM `expenseregister` as expensetbl JOIN `voucher` as vouchertbl on vouchertbl.id=expensetbl.VoucherID  JOIN  `paymentdetails` as paymenttbl on paymenttbl.id=vouchertbl.RefNo where expensetbl.Date between '".getDBFormatDate($from_date)."' and '".getDBFormatDate($to_date)."' ";
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
	$result[$i]['BY'] = $ledgername_array[$result[$i]['LedgerID']];
	$result[$i]['To'] = $ledgername_array[$result[$i]['PayerBank']];
}
//print_r($result);
return $result;	
}




}



?>