<?php
include_once("changelog.class.php");
include_once("latestcount.class.php");
include_once("voucher.class.php");
include_once("register.class.php");

class VoucherRegister{

    public $dbConn;
    public $debug_trace;
    public $obj_voucher;
    public $obj_register;
    public $m_objLog;
    private $obj_LatestCount;
     

    public function __construct($dbConn)
    {
        $this->dbConn = $dbConn;
        $this->debug_trace = 0;
        $this->obj_LatestCount = new latestCount($this->dbConn);
        $this->obj_voucher = new voucher($this->dbConn);
        $this->obj_register = new regiser($this->dbConn);
        $this->m_objLog = new changeLog($this->dbConn);
    }

    public function processdata($data){

        try{

            if($this->debug_trace)
            {
                echo "<br>Inside the data";
            }

            $validation = $this->validateData($data);
            
            if($this->debug_trace)
            {
                echo "<pre>";
                print_r($validation);
                echo "</pre>";
            }
            
            if($validation['status'] == true){

                $VoucherNo = $this->obj_LatestCount->getLatestVoucherNo($_SESSION['society_id']);
                $SrNo = 1;
                $EntryAdded = true;
                // var_dump($data);
                foreach($data as $v){

                    if(!empty($v[By]) && empty($v[To]))
                    {
                    //    echo "1"; 
                      $VoucherID = $this->obj_voucher->SetVoucherDetails($v['Date'], $v['RefNo'], $v['RefTable'], $VoucherNo, $SrNo, $v['VoucherType'], $v['By'], TRANSACTION_DEBIT, $v['Debit'], $v['Note']);  
                      if(!empty($VoucherID))
                      {
                        $RegisterID = $this->obj_register->SetRegister($v['Date'], $v['By'], $VoucherID, $v['VoucherType'], TRANSACTION_DEBIT, $v['Debit'], 0);  
                        
                        if(empty($RegisterID))
                        {
                            $EntryAdded = false;    
                        }
                      }
                    else
                    {
                        $EntryAdded = false;
                    }
                    }
                    else if(!empty($v[To]) && empty($v[By]))
                    {
                      	$VoucherID = $this->obj_voucher->SetVoucherDetails($v['Date'], $v['RefNo'], $v['RefTable'], $VoucherNo, $SrNo, $v['VoucherType'], $v['To'], TRANSACTION_CREDIT, $v['Credit'], $v['Note']);  
                        if(!empty(VoucherID))
                        {
                            $RegisterID = $this->obj_register->SetRegister($v['Date'], $v['To'], $VoucherID, $v['VoucherType'], TRANSACTION_CREDIT, $v['Credit'], 0);                        
                            
                            if(empty($RegisterID))
                            {
                                $EntryAdded = false; 
                            }
                        }
                        else
                        {
                            $EntryAdded = false;
                        }
                    }
                    $SrNo++;
                }

                return $validation['status'] = EntryAdded;
            }
            else
            {
                return $validation;    
            }
        }
        catch(Exception  $e){
            return $e->getMessage();
        }
    }

    public function validateData($data){
        
        $DebitTotal = 0;
        $CreditTotal = 0;
        $validate = array();
        $validate['error_msg'] = 'success';
        $validate['status'] = true;
      
        foreach($data as $v)
        {
            if(empty($v['Date']) || $v['Date'] == '0000-00-00')
            {
                $validate['error_msg'] = 'date is not valid';
                $validate['status'] = false;
            }

            if(empty($v['By']) && empty($v['To']))
            {
                $validate['error_msg'] = 'Ledger is missing';
                $validate['status'] = false;
            }
            
            if(!empty($v['Debit']))
            {
                $DebitTotal += $v['Debit'];
            }

            if(!empty($v['Credit']))
            {
                $CreditTotal += $v['Credit'];
            }

        }

        if($DebitTotal <> $CreditTotal)
        {
            $validate['error_msg'] = 'Debit and Credit Amount is matching';
            $validate['status'] = false;
        }
        
        return $validate;
    }

}

?>