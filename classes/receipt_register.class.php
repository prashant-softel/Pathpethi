<?php
include_once("dbconst.class.php");
include_once("utility.class.php");
class receipt_report extends dbop
{
	public $m_dbConn;
	public $m_objUtility;
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->m_objUtility = new utility($this->m_dbConn);
	}


public function getReceiptDetails($from_date,$to_date)
{
	$ledgername_array=array();
	$member_array=array();
	
$sql="SELECT vouchertbl.id,vouchertbl.Date,vouchertbl.VoucherNo,vouchertbl.By,vouchertbl.Debit as Amount,chequeentrytbl.Comments,chequeentrytbl.ChequeNumber,chequeentrytbl.ChequeDate,chequeentrytbl.PayerBank from `voucher` as vouchertbl JOIN `chequeentrydetails` as chequeentrytbl on  chequeentrytbl.id=vouchertbl.RefNo where vouchertbl.VoucherTypeID=".VOUCHER_RECEIPT." and vouchertbl.Date between '".getDBFormatDate($from_date)."' and '".getDBFormatDate($to_date)."' ORDER BY vouchertbl.Date";
$result=$this->m_dbConn->select($sql);

$get_ledger_name="select id,ledger_name from `ledger`";
$result02=$this->m_dbConn->select($get_ledger_name);

for($i = 0; $i < sizeof($result02); $i++)
{
$ledgername_array[$result02[$i]['id']]=$result02[$i]['ledger_name'];

}

 $get_member_name="select unit,owner_name,ownership_date from `member_main` order by ownership_date desc";
$result03=$this->m_dbConn->select($get_member_name);


for($i = 0; $i < sizeof($result03); $i++)
{
	$member_array[$result03[$i]['unit']][$i]['owner_name']=$result03[$i]['owner_name'];
	$member_array[$result03[$i]['unit']][$i]['ownership_date']=$result03[$i]['ownership_date'];


}

for($i = 0; $i < sizeof($result); $i++)
{
	$owner_name ='';
	$arrayTest = $member_array[$result[$i]['By']];
	foreach($arrayTest as $k => $v)
	{
		$date = $v['ownership_date'];
		$datediff = $this->m_objUtility->getDateDiff($date, $result[$i]['Date']);
		if($datediff <= 0)
		{
			$owner_name = $v['owner_name'];	
			break;
		}
	}
	$result[$i]['BY'] = $ledgername_array[$result[$i]['By']];
	$result[$i]['owner_name'] = $owner_name;
	
}
return $result;	
}







}



?>