<?php

/*
1. Select member 
case 1. if start date is 10th then no interest will calculate on amount
case 2. if   
*/
//Interest calculation pending
include_once('../classes/dbconst.class.php');
include_once('../classes/VoucherRegister.class.php');
include_once('../classes/include/dbop.class.php');

$dbRootConn = new dbop(true);
error_reporting(1);
ini_set('display_error',E_ALL);
try {

    $getPPQuery = "SELECT * FROM `society` WHERE status = 'Y'";
    $PPDetails  = $dbRootConn->select($getPPQuery);
    $EnteredBy = -2; // -2 will be cron job

    if(empty($PPDetails)){
        throw new Exception('Not found any Patpedhi');
    }

    foreach ($PPDetails as $detail) {
        extract($detail);
        $dbConn = new dbop(false, $dbname);
        
        if($dbConn->mMysqli->connect_errno){
            echo "ERROR: Unable to connect ".$dbname." database";
            continue;
        }
        
        if($dbConn){
            
            $dbConn->begin_transaction();
            $voucherRegister = new VoucherRegister($dbConn);

            $getCashLedger = "SELECT l.id as cash_ledger from ledger as l JOIN account_category as ac ON l.categoryid = ac.category_id JOIN appdefault as app ON ac.category_id = app.APP_DEFAULT_CASH_ACCOUNT Where app.APP_DEFAULT_CASH_ACCOUNT IS NOT NULL LIMIT 1";
            $cashLedgerDetail = $dbConn->select($getCashLedger);
            
            if(empty($cashLedgerDetail)){
                throw new Exception("Cash Ledger Not found");                
            }

            $query = "SELECT sum(amount) AS total, daily_ledgerid, member_id FROM `daily_collection` where Date <= CURDATE()  group by member_id, daily_ledgerid";
            $dailyCollections = $dbConn->select($query);
            
            if(empty($dailyCollections)){
                throw new Exception("No Record Found to transfer");                
            }
            
            $Receipt_Date = date('Y-m-d');
            $Receipt_deposit_slip = DEPOSIT_CASH;
            $Receipt_Cheque_No = CASH_CHEQUE_NO;
            $Receipt_Bank_id = $cashLedgerDetail[0]['cash_ledger'];
         
            foreach ($dailyCollections as $collectionData) {

                $daily_collection_id = $collectionData['dailyCollectionID'];
                $Receipt_Amount =  $collectionData['total'];
                $PaidBy = $collectionData['daily_ledgerid'];
                $member_id = $collectionData['member_id'];

                $Receipt_comments = 'Transferred Daily collection amount '.$Receipt_Amount.' for ledger id '.$PaidBy.' in cash account through cron job';

                $insert_query="insert into chequeentrydetails (`VoucherDate`,`ChequeDate`,`ChequeNumber`,`Amount`,`PaidBy`,`BankID`,`DepositID`,`EnteredBy`,`Comments`) values ('".getDBFormatDate($Receipt_Date)."','".getDBFormatDate($Receipt_Date)."','".$Receipt_Cheque_No."','".$Receipt_Amount."','".$PaidBy."','".$Receipt_Bank_id."','".$Receipt_deposit_slip."','".$EnteredBy."','".$Receipt_comments."')";
                $RefNo = $dbConn->insert($insert_query);
                
                $VoucherData[] = array("Date"=>$Receipt_Date,"RefNo"=>$RefNo,"RefTable"=>TABLE_CHEQUE_DETAILS , "VoucherType"=>VOUCHER_RECEIPT, "By"=>"", "To"=>$Receipt_Bank_id,"Debit"=>"","Credit"=>$Receipt_Amount,"Note"=>$Receipt_comments);
                $VoucherData[] = array("Date"=>$Receipt_Date,"RefNo"=>$RefNo,"RefTable"=>TABLE_CHEQUE_DETAILS , "VoucherType"=>VOUCHER_RECEIPT, "By"=>$PaidBy, "To"=>"", "Debit"=>$Receipt_Amount, "Credit"=>"","Note"=>$Receipt_comments);
                
                $VoucherRegisterResult = $voucherRegister->processdata($VoucherData);
                
                if($VoucherRegisterResult['status'] == true)
                {
                    $dailyCollectionUpdateQuery = "UPDATE daily_collection SET transfer_status = 1 WHERE Date <= CURDATE() AND transfer_status = '0' AND daily_ledgerid = '$PaidBy' AND member_id = '$member_id' ";
                    $dbConn->update($dailyCollectionUpdateQuery);
                    $dbConn->commit();
                    echo "<br> transfer successful for member id ".$member_id." AND ledger id ".$PaidBy;                    
                }     
            }
        }
    }     
} catch (Exception $e) {
    if($dbConn){
        $dbConn->rollback();
    }
    echo "<br><br>Error : ".$e->getMessage();
}

?>