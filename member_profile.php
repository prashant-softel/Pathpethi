<?php include_once("includes/head_s.php"); 
include_once("classes/list_member.class.php");
include_once("classes/pp_deposits.class.php");
include_once("classes/pp_loan.class.php");
$m_dbConnRoot = new dbop(true);
$member_obj = new list_member($m_dbConn);
$obj_utility = new utility($m_dbConn);
$obj_deposit = new pp_deposits($m_dbConn, $m_dbConnRoot);
$obj_loan    = new pp_loan($m_dbConn, $m_dbConnRoot);

if(isset($_REQUEST['member_id']) && !empty($_REQUEST['member_id'])){
    $memberDetails = $member_obj->getMemberDetails($_REQUEST['member_id']);
    $member_category_Arr = $obj_utility->getMemberCategory();
    extract($memberDetails[0]);
    
    if(empty($memberDetails)){?>
        <script>
            alert('Member Does not exists!!');
            window.location.href = "list_member.php";
        </script>
        <?php     
    }
}

?>
<link rel="stylesheet" type="text/css" href="css/pagination.css">
<link href="css/messagebox.css" rel="stylesheet" type="text/css" />
<style>
.i-padding{
    padding-top: 3px;
}

.personal-details{
    
}
</style>
<body>
<div class="panel panel-info" id="panel" style="display:block;width:80%"> 
<div class="panel-heading" id="pageheader" style="display: block;"> Profile View</div>  
<table class="center"> 
<tr>
    <th colspan="10" class="text-center"><b><u> PERSONAL DETAILS </u></b> <br/><br/></th>
</tr>
<br>

<tr align="left">
	<td width="100"><b>Name</b></td>
    <td width="100">:</td>
    <td width="200" colspan="2"><?=$owner_name?></td>
    <td width="100"><b>Member Category</b></td>
    <td width="100">:</td>
    <td width="200" colspan="2"><?=$member_category_Arr[$member_category]['member_category_name']?></td>
</tr>

<tr align="left">
    <td width="100"><b>Date of Birth</b></td>
    <td width="100">:</td>
    <td width="200" colspan="2"><?=$dob?></td>
    <td width="100"><b>Gender</b></td>
    <td width="100">:</td>
    <td width="200" colspan="2"><?php echo ($gender == GENDER_MALE)? "Male" : "Female";?></td>
</tr>

<tr align="left">
    <td width="100"><b>Mobile No</b></td>
    <td width="100">:</td>
    <td width="200" colspan="2"><?=$mob?></td>
    <td width="100"><b>Email Id </b></td>
    <td width="100">:</td>
    <td width="200" colspan="2"><?=$email?></td>
</tr>
<tr align="left">
	 
    <td width="100"><b>Aadhar Number</b></td>
    <td width="100">:</td>
    <td width="200" colspan="2"><?=$member_aadhar_number?></td>
    <td width="100"><b>PAN Number</b></td>
    <td width="100">:</td>
    <td width="200" colspan="2"><?=$member_pan_number?></td>
</tr>

<tr align="left">
	<td width="100"><b>Address</b></td>
    <td width="100">:</td>
    <td width="200" colspan="2"><?=$alt_address?></td>    
    
</tr>
</div>
<tr height="25" valign="bottom">
    <th colspan="11" class="text-center">
        <br/>
        <b><u> DETAILS OF SAVING ACCOUNT </u></b><br>        
        <a href="/pp_deposits.php?account_category=<?=SAVING_ACCOUNT?>&member_id=<?=$member_id?>" target="_blank"><button type="button" href="#" class="btn btn-primary btn-xs" style="float: right;"><i class="fa fa-plus i-padding"></i> Saving A/C </button></a>
    </th>
</tr>
<tr>
    <td colspan="11">
    <tr height="30" bgcolor="#E8E8E8">
        <th width="100" colspan="2">Start Date</th>
        <th width="100" colspan="3">Ledger Name</th>
        <th width="100" colspan="2">Maturity Calculation</th>
        <th width="100" colspan="2">Balance</th>
        <th width="100">View</th>
        <th width="100">Receipt</th>
    </tr>
    </td>
    <td colspan="11">
    <?php 
    
    $savingDetail = $obj_deposit->getDepositDetail(SAVING_ACCOUNT, $member_id);
    // echo "<pre>";
    // print_r($savingDetail);
    // echo "</pre>";
    foreach ($savingDetail as $data) {?>
        <tr height="25" bgcolor="#BDD8F4">
            <td colspan="2"><?=$data['start_date']?></td>
            <td colspan="3"><?=$data['ledger_name']?></td>
            <td colspan="2"><?=array_keys($MATURITY_CALCULATIONS, $data['maturity_cal'])[0]?></td>
            <td colspan="2"><?=($data['total_balance']) ? $data['total_balance'] : "0.00" ;  ?></td>
            <td><a href="view_ledger_details.php?lid=<?=$data['ledger_id']?>&gid=<?=LIABILITY?>"  target="_blank" class="btn btn-info btn-sm"><i class="fa fa-eye i-padding"></i> view</a></td>
            <td><a href="pp_receipts.php?account_type=<?=SAVING_ACCOUNT?>&member_id=<?=$member_id?>&ledger_id=<?=$data['ledger_id']?>"  target="_blank" class="btn btn-info btn-sm"><i class="fa fa-inr i-padding"></i> Collect</a></td>
        </tr>    
    <?php }?>    
    
</td>
</tr>

<tr height="25" valign="bottom">
<th colspan="11" class="text-center">
        <br/>
        <b><u> DAILY DEPOSIT ACCOUNT </u></b><br>        
        <a href="/pp_deposits.php?account_category=<?=DAILY_DEPOSIT_ACCOUNT?>&member_id=<?=$member_id?>" target="_blank"><button type="button" class="btn btn-primary btn-xs" style="float: right;"><i class="fa fa-plus i-padding"></i> DAILY DEPOSIT A/C </button></a>
    </th>
</tr>
<tr>
    <td colspan="11">
    <tr height="30" bgcolor="#E8E8E8">
        <th width="100">Start Date</th>
        <th width="100">Maturity Date</th>
        <th width="150" colspan="2">Ledger Name</th>
        <th width="100" colspan="2">Maturity Cal</th>
        <th width="100">Inst Amount</th>                  
        <th width="100" colspan="2">Balance</th>
        <th width="100">View</th>
        <th width="100">Receipt</th>
    </tr>
    </td>
    <td colspan="11">
    <?php $dailyDepositDetail = $obj_deposit->getDepositDetail(DAILY_DEPOSIT_ACCOUNT, $member_id);
          foreach ($dailyDepositDetail as $data) { ?>
            <tr height="25" bgcolor="#BDD8F4">
                <td><?=$data['start_date']?></td>
                <td><?=$data['maturity_date']?></td>
                <td colspan="2"><?=$data['ledger_name']?></td>
                <td colspan="2"><?=array_keys($MATURITY_CALCULATIONS, $data['maturity_cal'])[0]?></td>
                <td><?=$data['deposit_amt']?></td>
                <td colspan="2"><?=($data['total_balance']) ? $data['total_balance'] : "0.00" ;?></td>
                <td><a href="view_ledger_details.php?lid=<?=$data['ledger_id']?>&gid=<?=LIABILITY?>"  target="_blank" class="btn btn-info btn-sm"><i class="fa fa-eye i-padding"></i> view</a></td>
                <td><a href="pp_receipts.php?account_type=<?=DAILY_DEPOSIT_ACCOUNT?>&member_id=<?=$member_id?>&ledger_id=<?=$data['ledger_id']?>"  target="_blank" class="btn btn-info btn-sm"><i class="fa fa-inr i-padding"></i> Collect</a></td>
            </tr>
          <?php }
    
    ?>    
    
</td>
</tr>
<tr height="25" valign="bottom">
<th colspan="11" class="text-center">
        <br/>
        <b><u> MONTHLY DEPOSIT ACCOUNT </u></b><br>        
        <a href="/pp_deposits.php?account_category=<?=MONTHLY_DEPOSIT_ACCOUNT?>&member_id=<?=$member_id?>" target="_blank"><button type="button" class="btn btn-primary btn-xs" style="float: right;"><i class="fa fa-plus i-padding"></i> MONTHLY DEPOSIT A/C </button></a>
    </th>
</tr>
<tr>
<td colspan="11">
    <tr height="30" bgcolor="#E8E8E8">
        <th width="100">Start Date</th>
        <th width="100">Maturity Date</th>
        <th width="150" colspan="2">Ledger Name</th>
        <th width="100" colspan="2">Maturity Cal</th>
        <th width="100">Inst Amount</th>                  
        <th width="100" colspan="2">Balance</th>
        <th width="100">View</th>
        <th width="100">Receipt</th>
    </tr>
    </td>
    <td colspan="11">
    <?php $dailyDepositDetail = $obj_deposit->getDepositDetail(MONTHLY_DEPOSIT_ACCOUNT, $member_id);
          foreach ($dailyDepositDetail as $data) { ?>
            <tr height="25" bgcolor="#BDD8F4">
                <td><?=$data['start_date']?></td>
                <td><?=$data['maturity_date']?></td>
                <td colspan="2"><?=$data['ledger_name']?></td>
                <td colspan="2"><?=array_keys($MATURITY_CALCULATIONS, $data['maturity_cal'])[0]?></td>
                <td><?=$data['deposit_amt']?></td>
                <td colspan="2"><?=($data['total_balance']) ? $data['total_balance'] : "0.00" ;?></td>
                <td><a href="view_ledger_details.php?lid=<?=$data['ledger_id']?>&gid=<?=LIABILITY?>"  target="_blank" class="btn btn-info btn-sm"><i class="fa fa-eye i-padding"></i> view</a></td>
                <td><a href="pp_receipts.php?account_type=<?=MONTHLY_DEPOSIT_ACCOUNT?>&member_id=<?=$member_id?>&ledger_id=<?=$data['ledger_id']?>"  target="_blank" class="btn btn-info btn-sm"><i class="fa fa-inr i-padding"></i> Collect</a></td>
            </tr>
          <?php }    
    ?> 
</td>
</tr>

<tr height="25" valign="bottom">
<th colspan="11" class="text-center">
        <br/>
        <b><u> FIXED DEPOSIT </u></b><br>        
        <a href="/pp_deposits.php?account_category=<?=FIXED_DEPOSIT_ACCOUNT?>&member_id=<?=$member_id?>" target="_blank"><button type="button" class="btn btn-primary btn-xs" style="float: right;"> <i class="fa fa-plus i-padding"></i> Fixed Deposit A/C </button></a>
    </th>
</tr>

<tr>
    <td colspan="11">
    <tr height="30" bgcolor="#E8E8E8">
    <th width="100">Start Date</th>
        <th width="100">Maturity Date</th>
        <th width="150" colspan="2">Ledger Name</th>
        <th width="100" colspan="2">Maturity Cal</th>
        <th width="100" colspan="2">Deposit Amount</th>                  
        <th width="100" colspan="2">Balance</th>
        <th width="100">View</th>
    </tr>
    </td>
    <td colspan="11">
    <?php $dailyDepositDetail = $obj_deposit->getDepositDetail(FIXED_DEPOSIT_ACCOUNT, $member_id);
          foreach ($dailyDepositDetail as $data) { ?>
            <tr height="25" bgcolor="#BDD8F4">
                <td><?=$data['start_date']?></td>
                <td><?=$data['maturity_date']?></td>
                <td colspan="2"><?=$data['ledger_name']?></td>
                <td colspan="2"><?=array_keys($MATURITY_CALCULATIONS, $data['maturity_cal'])[0]?></td>
                <td colspan="2"><?=$data['deposit_amt']?></td>
                <td colspan="2"><?=($data['total_balance']) ? $data['total_balance'] : "0.00" ;?></td>
                <td><a href="view_ledger_details.php?lid=<?=$data['ledger_id']?>&gid=<?=LIABILITY?>"  target="_blank" class="btn btn-info btn-sm"><i class="fa fa-eye i-padding"></i> view</a></td>                
            </tr>
          <?php }    
    ?>
</td>
</tr>
<tr height="25" valign="bottom">
<th colspan="11" class="text-center">
        <br/>
        <b><u> Loan ACCOUNT </u></b><br>        
        <a href="/pp_loan.php?id=<?=$member_id?>" target="_blank"><button type="button" class="btn btn-primary btn-xs" style="float: right;"> <i class="fa fa-plus i-padding"></i> Loans </button></a>
    </th>
</tr>

<tr>
    <td colspan="11">
    <tr height="30" bgcolor="#E8E8E8">
        <th width="100">Loan Date</th>
        <th width="100">Maturity Date</th> 
        <th width="100">Loan Type</th>
        <th width="100">Ledger Name</th>
        <th width="100">Linked A/C</th>
        <th width="100">Amount</th>
        <th width="100">Interest Rate</th>
        <th width="100">Installment Amount</th>
        <th width="100">Status</th>
        <th width="100">View</th>
        <th width="100">Receipt</th>
    </tr>
    </td>
    <td colspan="11">
    <?php 
    
    // ToDo Please add pending loan amount and with respect to pending amount show status as close or open column
    $loanDetails = $obj_loan->getLoanForAMember($member_id);
    $ledgerDetail = $obj_loan->getLedger($member_id, LOAN_ACCOUNT, 'id');
    foreach ($loanDetails as $data) {
        $subcategory = $obj_loan->getSubCategory($data['subcategory_id']);
        $accountDetail = $obj_loan->getAccountNumber($data['member_id'], $data['mortgage'], $data['mortgage_account']);
        $accountNumber = $accountDetail[0]['ledger_name'];
        // var_dump(); 
    ?>
        <tr height="25" bgcolor="#BDD8F4">
            <td><?=$data['loan_date']?></td>
            <td><?=$data['maturity_date']?></td>
            <td><?=$subcategory?></td>
            <td><?=$ledgerDetail[$data['ledger_id']]['ledger_name']?></td>
            <td><?=$accountNumber ? $accountNumber : '--'?></td>
            <td><?=$data['amount']?></td>
            <td><?=$data['interest_rate']?></td>
            <td><?=$data['installment_amt']?></td>
            <td><?=$loans[$i]['status'] == '0' ? "Open" : "Closed";?></td>
            <td><a href="view_ledger_details.php?lid=<?=$data['ledger_id']?>&gid=<?=ASSET?>" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-eye i-padding"></i> view</a></td>
            <td><a href="pp_receipts.php?account_type=<?=LOAN_ACCOUNT?>&member_id=<?=$member_id?>&ledger_id=<?=$data['ledger_id']?>"  target="_blank" class="btn btn-info btn-sm"><i class="fa fa-inr i-padding"></i> Collect</a></td>
        </tr>   
    <?php }?>    
    
</td>
</tr>
</table>
<br>
</div>
</body>
<?php include_once "includes/foot.php"; ?>