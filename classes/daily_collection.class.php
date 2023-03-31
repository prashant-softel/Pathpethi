<?php
include_once("dbconst.class.php");
include_once("include/display_table.class.php");
include_once("utility.class.php");
include_once("VoucherRegister.class.php");
class dailycollection
{
	public $actionPage = "../daily_collection.php";
    public $obj_utility;
    public $obj_voucher_register;
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
        $this->obj_utility = new utility($this->m_dbConn);
        $this->obj_voucher_register = new VoucherRegister($this->m_dbConn);
		$this->display_pg=new display_table($this->m_dbConn);
	}
    public function startProcess()
	{

    }
    public function updatedailycollection($agent_id)
	{
        if($_REQUEST['method']=='update' && $errorExists==0)
		{		
			if($_POST['agentid'] <>'')
			{   
                $insert_query="insert into daily_collection(`agent_id`,`date`,`member_id`,`daily_ledgerid`,`amount`,`status`,`remark`) values('".$_POST['agentid']."','".getDBFormatDate($_POST['date'])."','".$_POST['memberid']."','".$_POST['ledgerid']."','".$_POST['amount']."','".$_POST['status']."','".$_POST['remark']."')";
                $this->m_dbConn->insert($insert_query);    
                return "update";            								
            }								
        }   
	}
    public function FetchDailyCollectionReport($agent_id,$date)
    {
        $agent_data = array();
        $existingdataWithReIndexing = array();
        
        $getDailyRecordpre="select d.id, mm.member_id, mm.owner_name, l.id as ledgerid,l.ledger_name, d.amount from member_main as mm join pp_deposits as d on mm.member_id=d.member_id join ledger as l on l.id=d.ledger_id JOIN account_category as ac ON l.categoryid = ac.category_id WHERE ac.parentcategory_id = '".DAILY_DEPOSIT_ACCOUNT."' AND d.agent_id = '$agent_id' AND '".getDBFormatDate($date)."' BETWEEN d.deposit_date AND d.maturity_date";
        $previous = $this->m_dbConn->select($getDailyRecordpre); 
        
        $getDailyRecordexisting = "select agent_id , date, member_id, daily_ledgerid, amount, status, remark from daily_collection where date ='".getDBFormatDate($date)."' and agent_id = '".$agent_id."'";
        $existing = $this->m_dbConn->select($getDailyRecordexisting); 
        
        foreach ($existing as $data) {
            $existingdataWithReIndexing[$data['member_id']][$data['daily_ledgerid']] = $data;
        }
    	
        $agent_data['default'] = $previous;
        $agent_data['existing'] = $existingdataWithReIndexing;
        
        return $agent_data;     
    } 

    // Function to make entry of agent daily collection
    public function agentDailyCollection($params){
        
        try {
            $this->m_dbConn->begin_transaction();
            extract($params);
            $LedgerDetails = $this->obj_utility->getCategoryLedgerID($_SESSION['default_cash_account']);
            if(empty($LedgerDetails)){
                throw new Exception("Cash Ledger Not Found");
            }
            $cashLedger = $LedgerDetails[0]['id'];
            $date = getDBFormatDate($date);

            // Insert into cheque entry table
            $query = "INSERT INTO `chequeentrydetails`(`VoucherDate`, `ChequeDate`, `ChequeNumber`, `Amount`, `PaidBy`, `BankID`, `EnteredBy`, `DepositID`, `Comments`) VALUES ('$date','$date',".CASH_CHEQUE_NO.", '$amount', '$agent_id', '$cashLedger', '".$_SESSION['login_id']."', ".DEPOSIT_CASH.", '$comment')";
            $insert_id = $this->m_dbConn->insert($query);

            if($insert_id){

                $VoucherData = array();
                $ByArray = array("Date"=>$date,"RefNo"=>$insert_id,"RefTable"=>TABLE_CHEQUE_DETAILS , "VoucherType"=>VOUCHER_RECEIPT, "By"=>"", "To"=>$cashLedger,"Debit"=>"","Credit"=>$amount,"Note"=>$comment);
				$ToArray = array("Date"=>$date,"RefNo"=>$insert_id,"RefTable"=>TABLE_CHEQUE_DETAILS , "VoucherType"=>VOUCHER_RECEIPT, "By"=>$agent_id, "To"=>"", "Debit"=>$amount, "Credit"=>"","Note"=>$comment);
			
                array_push($VoucherData,$ByArray);
                array_push($VoucherData,$ToArray);
                
                $VoucherRegisterResult = $this->obj_voucher_register->processdata($VoucherData);
                if($VoucherRegisterResult['status'] == true)
                {
                    $this->m_dbConn->commit();
                    return array('status'=>'success', 'message'=>'transaction completed!!');
                }
                else{
                    throw new Exception("Failed to complete transaction");
                }
            }        
        } catch (Exception $e) {
            $this->m_dbConn->rollback();
            return array('status'=>'failed', 'message'=>$e->getMessage());
        }
        
        
    }

}   


 
    
      
