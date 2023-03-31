<?php
class income_details extends dbop
{
	public $m_dbConn;
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;

	}
	
	
	public function ledgername($lid)
	{
	$sql="select ledger_name from `ledger` where id=".$lid." ";
	$res=$this->m_dbConn->select($sql);	
	return $res[0]['ledger_name'];
		
		
	}
	
	
	public function show_income_details($lid)
	{
		//echo '1';
		if(isset($_POST['selectdate']) && $_POST['selectdate'] <> '')
		{
		$sql ="select `Date`,`Debit`,`Credit`,VoucherID,VoucherTypeID from `incomeregister` where LedgerID=".$lid." and `Date`='".getDBFormatDate($_POST['selectdate'])."' ";	//echo $sql;
		//echo '2';	
		}
		else
		{
			//echo '3';
		$sql ="select `Date`,`Debit`,`Credit`,VoucherID,VoucherTypeID from `incomeregister` where LedgerID=".$lid."  ";
		}
		//echo $sql;
		$res = $this->m_dbConn->select($sql);
		
		return $res;
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
	
	
	
	public function combobox($query, $id, $bShowAll = false)
	{
		if($bShowAll == true)
		{
			$str.="<option value=''>All</option>";
		}
		else
		{
			$str.="<option value=''>Please Select</option>";
		}
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($id==getDisplayFormatDate($v))
						{
							$sel = 'selected';	
						}
						else
						{
							$sel = '';
						}
						
						$str.="<OPTION VALUE='".getDisplayFormatDate($v)."'".' '.$sel.">";
					}
					else
					{
						$str.=getDisplayFormatDate($v)."</OPTION>";
					}
					$i++;
				}
			}
		}
			return $str;
	}

	
	
}

?>