<?php include_once "check_default.php";
include_once("classes/dbconst.class.php");
include_once("classes/utility.class.php"); 
include_once("includes/head_s.php");
//include_once("includes/menu.php");
include_once("classes/home_s.class.php");
$obj_AdminPanel = new CAdminPanel($m_dbConn);
$obj_Utility = new utility($m_dbConn);

class CBankAccountDetails
{
	function __construct()
	{
		
	}
}

?>


<!--<div id="page-wrapper" style="margin-top:6%;margin-left:1.5%; border:none;width:100%">
	<div class="row">
    <div class="col-lg-12">-->
		<br />
		<div class="panel panel-info" id="panel" style="display:none">
        <div class="panel-heading" id="pageheader">Bank Accounts</div>
        <!-- /.panel-heading -->
        <div class="panel-body">
            <!--<div class="dataTable_wrapper">
            <div class="dataTables_wrapper form-inline dt-bootstrap no-footer" id="dataTables-example_wrapper">-->
                <!--<table aria-describedby="dataTables-example_info" role="grid" class="table table-striped table-bordered table-hover dataTable no-footer" id="dataTables-example">-->
						<table id="example" class="display" cellspacing="0" width="100%">
                    <thead>
<tr><th >Bank</th><th align="center">Balance</th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
</thead>
                    <tbody>
      
<?php
    $arBankDetails = $obj_AdminPanel->GetBankAccountAndBalance();
   $TotalBalAmount=0;
   foreach($arBankDetails as $arData=>$arvalue)
   {
	   $BankName = $obj_AdminPanel->GetLedgerNameFromID($arvalue["LedgerID"]);
	   $receipts =$arvalue["receipts"];
	   $payments = $arvalue["payments"]; 
	   $BalAmount = $receipts - $payments;
	   $arParentDetails = $obj_Utility->getParentOfLedger($arvalue["LedgerID"]);
	   $CategoryID = $arParentDetails['category'];
	   $TotalBalAmount = $TotalBalAmount + $BalAmount;
	   //print_r($arParentDetails);
	   //echo CASH_ACCOUNT;

	   //$Amount = $arvalue; 
	   $Link = "PaymentDetails.php?bankid=" . $arvalue["LedgerID"]. "&LeafID=-1";
	   $StrPageLinks = "<tr><td>".$BankName."</td><td align=right>". number_format($BalAmount,2) ."</td>";
	   //echo "<script>alert('".$arvalue["LedgerID"]."');<//script>";
	   if($CategoryID != CASH_ACCOUNT)
	   {
	   		$StrPageLinks .= "<td><a href='chequeleafbook.php?bankid=" . $arvalue["LedgerID"]. "'>Issue Cheques</a></td>";
			$StrPageLinks .= "<td><a href='depositgroup.php?bankid=" . $arvalue["LedgerID"]. "'>Generate Deposit Slip</a></td>";
	   }
	   else
	   {
		   $StrPageLinks .= "<td><a href='".$Link."'>Pay Cash</a></td>";
		   $StrPageLinks .= "<td><a href='ChequeDetails.php?depositid=-3&bankid=".$arvalue["LedgerID"]."'>Receive Cash</a></td>";
	   }
	  
	   if($CategoryID != CASH_ACCOUNT)
	   {
	   		$StrPageLinks .= "<td><a href='NeftDetails.php?bankid=" . $arvalue["LedgerID"]. "'>NEFT Transaction</a></td>";
	   		$StrPageLinks .= "<td><a href='bank_statement.php?LedgerID=" .$arvalue["LedgerID"]."'> Statement</a></td>";
	   		$StrPageLinks .= "<td><a href='bank_reconciliation.php?ledgerID=" .$arvalue["LedgerID"]."'> Reconciliation </a></td>";
	   		//$StrPageLinks .= "<td><a href='BankRecoReport.php?ledgerID=" .$arvalue["LedgerID"]."' target='_blank'> Reco_Report </a></td></tr>";
			$StrPageLinks .= "<td><a href='common_period.php?bankreco&lID=" .$arvalue["LedgerID"]."' target='_blank'> Reco_Report </a></td></tr>";
	   }
	   else
	   {
		   $StrPageLinks .= "<td></td>";
	   	   $StrPageLinks .= "<td><a href='bank_statement.php?LedgerID=" .$arvalue["LedgerID"]."'> Statement</a></td>";
	   	   $StrPageLinks .= "<td></td>";
	   	   $StrPageLinks .= "<td></td></tr>";
	   }
	   echo $StrPageLinks;
   }
   ?> 
   <tr><th >Total Balance:</th><th style="text-align:right;" ><?php echo  number_format($TotalBalAmount, 2); ?></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
   </tbody>
   </table>
            <!--</div>
            
        </div>-->
        </div>
        <!-- /.panel-body -->
 
</div>
 
   
   <?php include_once "includes/foot.php"; ?>
 