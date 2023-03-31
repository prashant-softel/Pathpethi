<?php 
include_once "ses_set_s.php"; 
include_once("includes/head_s.php"); 
include_once ("classes/include/dbop.class.php");
include_once "classes/dbconst.class.php";
include_once("classes/include/fetch_data.php");
include_once("classes/BalanceSheet_20190328.class.php");
include_once ("classes/utility.class.php");
	$dbConn = new dbop();
	$objFetchData = new FetchData($dbConn);
	$objFetchData->GetSocietyDetails($_SESSION['society_id']);
	$objUtility = new utility($dbConn);	

$objLIABILITY = new BalanceSheet($dbConn);
$objASSET = new BalanceSheet($dbConn);
$objINCOME = new BalanceSheet($dbConn);
$objEXPENSE = new BalanceSheet($dbConn);

$LIABILITYData = $objLIABILITY->CategoryArray(LIABILITY, getDBFormatDate($_SESSION['from_date']), getDBFormatDate($_SESSION['to_date']),$_SESSION['default_income_expenditure_account'],true);
$LIABILITYcredit = $objLIABILITY->CreditTotal;
$LIABILITYdebit = $objLIABILITY->DebitTotal;
$LIABILITYOpeningBalanceCreditTotal = $objLIABILITY->OpeningBalanceCreditTotal; 
$LIABILITYOpeningBalanceDebitTotal = $objLIABILITY->OpeningBalanceDebitTotal; 
$LIABILITYClosingBalanceCreditTotal = $objLIABILITY->ClosingBalanceCreditTotal; 
$LIABILITYClosingBalanceDebitTotal = $objLIABILITY->ClosingBalanceDebitTotal; 

$ASSETData = $objASSET->CategoryArray(ASSET, getDBFormatDate($_SESSION['from_date']), getDBFormatDate($_SESSION['to_date']),0,true);
$ASSETcredit = $objASSET->CreditTotal;
$ASSETdebit = $objASSET->DebitTotal;
$ASSETOpeningBalanceCreditTotal = $objASSET->OpeningBalanceCreditTotal; 
$ASSETOpeningBalanceDebitTotal = $objASSET->OpeningBalanceDebitTotal; 
$ASSETClosingBalanceCreditTotal = $objASSET->ClosingBalanceCreditTotal; 
$ASSETClosingBalanceDebitTotal = $objASSET->ClosingBalanceDebitTotal; 


$INCOMEData = $objINCOME->CategoryArray(INCOME, getDBFormatDate($_SESSION['from_date']), getDBFormatDate($_SESSION['to_date']),0,false);
$INCOMEcredit = $objINCOME->CreditTotal;
$INCOMEdebit = $objINCOME->DebitTotal;
/*$INCOMEOpeningBalanceCreditTotal = $objINCOME->OpeningBalanceCreditTotal; 
$INCOMEOpeningBalanceDebitTotal = $objINCOME->OpeningBalanceDebitTotal; */
$INCOMEOP = $objUtility->getOpeningBalanceOfCategory(INCOME,getDBFormatDate($_SESSION['from_date']),true);
$INCOMEOpeningBalanceCreditTotal = $INCOMEOP['Credit'];
$INCOMEOpeningBalanceDebitTotal =  $INCOMEOP['Debit'];

$INCOMEClosingBalanceCreditTotal = $objINCOME->ClosingBalanceCreditTotal; 
$INCOMEClosingBalanceDebitTotal = $objINCOME->ClosingBalanceDebitTotal; 

$EXPENSEData = $objEXPENSE->CategoryArray(EXPENSE, getDBFormatDate($_SESSION['from_date']), getDBFormatDate($_SESSION['to_date']),0,false);
$EXPENSEcredit = $objEXPENSE->CreditTotal;
$EXPENSEdebit = $objEXPENSE->DebitTotal;
/*$EXPENSEOpeningBalanceCreditTotal = $objEXPENSE->OpeningBalanceCreditTotal; 
$EXPENSEOpeningBalanceDebitTotal = $objEXPENSE->OpeningBalanceDebitTotal;*/

$EXPENSEOP = $objUtility->getOpeningBalanceOfCategory(EXPENSE,getDBFormatDate($_SESSION['from_date']),true);
$EXPENSEOpeningBalanceCreditTotal = $EXPENSEOP['Credit'];
$EXPENSEOpeningBalanceDebitTotal =  $EXPENSEOP['Debit'];
 
$EXPENSEClosingBalanceCreditTotal = $objEXPENSE->ClosingBalanceCreditTotal; 
$EXPENSEClosingBalanceDebitTotal = $objEXPENSE->ClosingBalanceDebitTotal; 

$Creditfinal = $LIABILITYcredit +$ASSETcredit + $INCOMEcredit +$EXPENSEcredit;
$Debitfinal = $LIABILITYdebit + $ASSETdebit +$INCOMEdebit +$EXPENSEdebit;

/*$profitLossOpeningCredit =  abs($INCOMEOpeningBalanceCreditTotal + $EXPENSEOpeningBalanceCreditTotal);
$profitLossOpeningDebit = abs($INCOMEOpeningBalanceDebitTotal + $EXPENSEOpeningBalanceDebitTotal);*/

$profitLossOpeningCredit =  0;
$profitLossOpeningDebit = 0;

if($INCOMEOP['Total'] > 0)
{
	$profitLossOpeningCredit =  $INCOMEOP['Total'];
}
else
{
	$profitLossOpeningDebit = $INCOMEOP['Total'];
}

if($EXPENSEOP['Total'] > 0)
{
	$profitLossOpeningDebit  += $EXPENSEOP['Total'];
}
else
{
	$profitLossOpeningCredit  +=  $EXPENSEOP['Total'];
}

$OpeningBalanceCreditfinal = $LIABILITYOpeningBalanceCreditTotal + $ASSETOpeningBalanceCreditTotal + $INCOMEOpeningBalanceCreditTotal + $EXPENSEOpeningBalanceCreditTotal;
$OpeningBalanceDebitfinal = $LIABILITYOpeningBalanceDebitTotal + $ASSETOpeningBalanceDebitTotal + $INCOMEOpeningBalanceDebitTotal + $EXPENSEOpeningBalanceDebitTotal;

$Creditfinal = $Creditfinal;// - $OpeningBalanceCreditfinal;
$Debitfinal = $Debitfinal ;//- $OpeningBalanceDebitfinal;

$ClosingBalanceCreditfinal = $LIABILITYClosingBalanceCreditTotal + $ASSETClosingBalanceCreditTotal + $INCOMEClosingBalanceCreditTotal + $EXPENSEClosingBalanceCreditTotal +$profitLossOpeningCredit;
$ClosingBalanceDebitfinal = $LIABILITYClosingBalanceDebitTotal + $ASSETClosingBalanceDebitTotal + $INCOMEClosingBalanceDebitTotal + $EXPENSEClosingBalanceDebitTotal +$profitLossOpeningDebit ;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TRIAL BALANCE SHEET</title>
<script  type="text/javascript" src="js/jsBalanceSheet.js"></script>
<script type="text/javascript" language="javascript" src="media/js/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="css/treeview.css" >
<script language="javascript" src="js/bootstrap.min.js"></script>
<style>
.ExportStyle
{
	border-collapse: collapse;padding-left:50px;
}
.rowstyle > th{border:1px dashed gray !important;  }
.Exportdiv{ display:none;}
</style>

</head>

<body>
<br/>
<div class="panel panel-info" id="panel" style="display:none;">
    <div class="panel-heading" id="pageheader">TRIAL BALANCE SHEET</div>
    <br />
   <!-- <div style="padding-left: 15px;"><button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;"><i class="fa  fa-arrow-left"></i></button></div>-->
<center><font style="font-size:14px;"><b>TRIAL BALANCE SHEET FROM <?php echo getDisplayFormatDate($_SESSION['from_date']); ?> TO <?php echo getDisplayFormatDate($_SESSION['to_date']);?></b></font></center>
<br />
<br />
<div style="padding-left: 15px;">
<INPUT TYPE="button" VALUE="Expand All" onClick="ExpandALL();"    class="btn btn-primary"  style="width:10%;box-shadow: none;" >
<INPUT TYPE="button" VALUE="Collapse All" onClick="CollapseALL();"    class="btn btn-primary"  style="width:10%;box-shadow: none;"  >
<?php if($_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] == 1){?>
<input  type="button" id="btnExport1" value=" Export To Excel"  style="width:150px;box-shadow: none;" class="btn btn-primary"   onclick="ExportToExcel(this.id);"/>
<?php } ?>
&nbsp;<label><input type="checkbox" name="checkbox1" id="checkbox1" onclick="reportTypeChange();"  <?php if(isset($_GET['show']) && $_GET['show'] == 0){}else{ echo 'checked';}?>/>&nbsp;Show Zero Value</label>

<div style="float:right;">
<select name = "report_type" id = "report_type" style="width:65%; height:30px;">
    <option value="1" >Summary Report</option>
    <option value="2" >Detailed Report</option>
</select>
<input type="button" name='button' id="btn" value="Go"  class="btn btn-primary"  style="height:30px; width:50px; box-shadow: none;" onclick="reportTypeChange();">
&nbsp;&nbsp;</div>
</div>
<br />
<br />

<center>
<table  align="center" width="98%"  style="border:1px solid black;">
<?php 

if($_GET['q'] == 1)
{
?>	
<script>document.getElementById('report_type').value = '1';</script>
<!--<tr><td style="font-size:14px;"><div style="width:100%;"><div style="width:25%;float:left;"><b>Particulars</b></div><div style="width:20%;float:left; text-align:center;"><b>Credit</b></div><div style="width:20%;float:left; text-align:center;"><b>Debit</b></div><div style="width:20%;float:left;"><b>Closing Balance</b></div></div></td></tr>
<tr><td style="font-size:14px;"><div style="width:100%;"><div style="width:25%;float:left;"><b>Particulars</b></div><div style="width:20%;float:left; text-align:center;"><b>Credit</b></div><div style="width:20%;float:left; text-align:center;"><b>Debit</b></div><div style="width:20%;float:left;"><b>Closing Balance</b></div></div></td></tr>-->
<tr>
	<td style="font-size:14px;"><div style="width:100%;">
		<div style="width:60%;float:left;border:1px solid gray !important;"><b>Particulars</b></div>
		<div style="width:36%;float:left; text-align:center;border:1px solid gray !important;display:none"><b>Transactions(Rs.)</b></div>
        <div style="width:40%;float:left;text-align:center;border:1px solid gray !important;"><b>Closing Balance(Rs.)</b></div></div>
	</td>
</tr>
<tr>
	<td style="font-size:14px;">
		<div style="width:100%;"><div style="width:60%;float:left;border:1px solid gray !important;">&nbsp;</div>
	    <div style="width:18%;float:left; text-align:center;border:1px solid gray !important;display:none"><b>Cr</b></div>
        <div style="width:18%;float:left; text-align:center;border:1px solid gray !important;display:none"><b>Dr</b></div>
        <div style="width:20%;float:left; text-align:center;border:1px solid gray !important;"><b>Cr</b></div>
        <div style="width:20%;float:left; text-align:center;border:1px solid gray !important;"><b>Dr</b></div></div>
     </td>
</tr>        
<tr align="center" ><td style="font-size:16px;" valign="middle"><br /><div style="width:50%;background-color: #337ab7; color: #fff; border-radius:13px;"><b>Liabilities</b></div><br /></td></tr>
<tr><td><?php echo ('<div class="tree well">'.$objLIABILITY->generateTrialBalance($LIABILITYData,0). '</div>');?></td></tr>
<tr align="center"><td style="font-size:16px;" valign="middle"><br /><div style="width:50%;background-color: #337ab7; color: #fff; border-radius:13px;"><b>Assets</b></div><br /></td></tr>
<tr><td><?php echo ('<div class="tree well">'.$objASSET->generateTrialBalance($ASSETData,0). '</div>');?></td></tr>
<tr align="center"><td style="font-size:16px;" valign="middle"><br /><div style="width:50%;background-color: #337ab7; color: #fff; border-radius:13px;"><b>Expenses</b></div><br /></td></tr>
<tr><td><?php echo ('<div class="tree well">'.$objEXPENSE->generateTrialBalance($EXPENSEData,0,true). '</div>');?></td></tr>
<tr align="center"><td style="font-size:16px;" valign="middle"><br /><div style="width:50%;background-color: #337ab7; color: #fff; border-radius:13px;"><b>Income</b></div><br /></td></tr>
<tr><td><?php echo ('<div class="tree well">'.$objINCOME->generateTrialBalance($INCOMEData,0,true). '</div>');?></td></tr>
<!--<tr><td style="font-size:14px;"><div style="width:100%;"><div style="width:25%;float:left;"><b>Grand Total</b></div><div style="width:20%;float:left; text-align:center;"><b><?php echo number_format($Creditfinal,2);?></b></div><div style="width:20%;float:left; text-align:center;"><b><?php echo number_format($Debitfinal,2);?></b></div><div style="width:20%;float:left;"><b><?php echo number_format($ClosingBalancefinal,2);?></b></div></div></td></tr>-->
<tr>
	<td style="font-size:14px;">
        <div style="width:100%;">
            <div style="width:60%;float:left;border:1px solid gray !important;"><b>Profit & Loss A/c</b></div>
            <div style="width:18%;float:left; text-align:right;border:1px solid gray !important;display:none"><b><?php echo number_format(0,2);?></b></div>
            <div style="width:18%;float:left; text-align:right;border:1px solid gray !important;display:none"><b><?php echo number_format(0,2);?></b></div>
            <div style="width:20%;float:left;text-align:right;border:1px solid gray !important;"><b><?php echo number_format($profitLossOpeningCredit,2);?></b></div>
            <div style="width:20%;float:left;text-align:right;border:1px solid gray !important;"><b><?php echo number_format($profitLossOpeningDebit,2);?></b></div>
        </div>
	</td>
</tr>


<tr>
	<td style="font-size:14px;">
        <div style="width:100%;">
            <div style="width:60%;float:left;border:1px solid gray !important;"><b>Grand Total</b></div>
            <div style="width:18%;float:left; text-align:right;border:1px solid gray !important;display:none"><b><?php echo number_format($Creditfinal,2);?></b></div>
            <div style="width:18%;float:left; text-align:right;border:1px solid gray !important;display:none"><b><?php echo number_format($Debitfinal,2);?></b></div>
            <div style="width:20%;float:left;text-align:right;border:1px solid gray !important;"><b><?php echo number_format($ClosingBalanceCreditfinal,2);?></b></div>
            <div style="width:20%;float:left;text-align:right;border:1px solid gray !important;"><b><?php echo number_format($ClosingBalanceDebitfinal,2);?></b></div>
        </div>
	</td>
</tr>
<?php }
else if($_GET['q'] == 2) 
{?>
<script>document.getElementById('report_type').value = '2';</script>
<tr>
	<td style="font-size:14px;">
        <div style="width:100%;">
            <div style="width:25%;float:left;border:1px solid gray !important;"><b>Particulars</b></div>
            <div style="width:25%;float:left; text-align:center;border:1px solid gray !important;"><b>Opening Balance(Rs.)</b></div>
            <div style="width:25%;float:left; text-align:center;border:1px solid gray !important;"><b>Transactions(Rs.)</b></div>
            <div style="width:25%;float:left;text-align:center;border:1px solid gray !important;"><b>Closing Balance(Rs.)</b></div>
        </div>
	</td>
</tr>
<tr>
	<td style="font-size:14px;">
    	<div style="width:100%;">
            <div style="width:25%;float:left;border:1px solid gray !important;">&nbsp;</div>
            <div style="width:12.5%;float:left; text-align:center;border:1px solid gray !important;"><b>Cr</b></div>
            <div style="width:12.5%;float:left; text-align:center;border:1px solid gray !important;"><b>Dr</b></div>
            <div style="width:12.5%;float:left; text-align:center;border:1px solid gray !important;"><b>Cr</b></div>
            <div style="width:12.5%;float:left; text-align:center;border:1px solid gray !important;"><b>Dr</b></div>
            <div style="width:12.5%;float:left; text-align:center;border:1px solid gray !important;"><b>Cr</b></div>
            <div style="width:12.5%;float:right; text-align:center;border:1px solid gray !important;"><b>Dr</b></div>
        </div>
	</td>
</tr>
<tr align="center"><td style="font-size:16px;" valign="middle"><br /><div style="width:50%;background-color: #337ab7; color: #fff; border-radius:13px;"><b>Liabilities</b></div><br /></td></tr>
<tr><td><?php echo ('<div class="tree well">'.$objLIABILITY->generateTrialBalance($LIABILITYData,0). '</div>');?></td></tr>
<tr align="center" ><td style="font-size:16px;" valign="middle"><div style="width:50%;background-color: #337ab7; color: #fff; border-radius:13px;"><b>Assets</b></div><br /></td></tr>
<tr><td><?php echo ('<div class="tree well">'.$objASSET->generateTrialBalance($ASSETData,0). '</div>');?></td></tr>
<tr align="center" ><td style="font-size:16px;" valign="middle"><div style="width:50%;background-color: #337ab7; color: #fff; border-radius:13px;"><b>Expenses</b></div><br /></td></tr>
<tr><td><?php echo ('<div class="tree well">'.$objEXPENSE->generateTrialBalance($EXPENSEData,0,true). '</div>');?></td></tr>
<tr align="center" ><td style="font-size:16px;" valign="middle"><div style="width:50%;background-color: #337ab7; color: #fff; border-radius:13px;"><b>Income</b></div><br /></td></tr>
<tr><td><?php echo ('<div class="tree well">'.$objINCOME->generateTrialBalance($INCOMEData,0,true). '</div>');?></td></tr>
<!--<tr><td style="font-size:14px;"><div style="width:100%;"><div style="width:25%;float:left;"><b>Grand Total</b></div><div style="width:15%;float:left; text-align:right;"><b><?php //echo number_format($OpeningBalancefinal,2);?></b></div><div style="width:17%;float:left; text-align:right;"><b><?php //echo number_format($Creditfinal,2);?></b></div><div style="width:20%;float:left; text-align:right;"><b><?php //echo number_format($Debitfinal,2);?></b></div><div style="width:15%;float:right;"><b><?php //echo number_format($ClosingBalancefinal,2);?></b></div></div></td></tr>-->
<tr>
	<td style="font-size:14px;">
    	<div style="width:100%;">
        	<div style="width:25%;float:left;border:1px solid gray !important;"><b>Profit & Loss A/c</b></div>
			<div style="width:12.5%;float:left; text-align:right;border:1px solid gray !important;"><b><?php echo number_format($profitLossOpeningCredit,2);?></b></div>
			<div style="width:12.5%;float:left; text-align:right;border:1px solid gray !important;"><b><?php echo number_format($profitLossOpeningDebit,2);?></b></div>
			<div style="width:12.5%;float:left; text-align:right;border:1px solid gray !important;"><b><?php echo number_format(0,2);?></b></div>
            <div style="width:12.5%;float:left; text-align:right;border:1px solid gray !important;"><b><?php echo number_format(0,2);?></b></div>
			<div style="width:12.5%;float:left;text-align:right;border:1px solid gray !important;"><b><?php echo number_format($profitLossOpeningCredit,2);?></b></div>
			<div style="width:12.5%;float:right;text-align:right;border:1px solid gray !important;"><b><?php echo number_format($profitLossOpeningDebit,2);?></b></div>
         </div>
    </td>
</tr>

<tr>
	<td style="font-size:14px;">
    	<div style="width:100%;">
        	<div style="width:25%;float:left;border:1px solid gray !important;"><b>Grand Total</b></div>
			<div style="width:12.5%;float:left; text-align:right;border:1px solid gray !important;"><b><?php echo number_format($OpeningBalanceCreditfinal,2);?></b></div>
			<div style="width:12.5%;float:left; text-align:right;border:1px solid gray !important;"><b><?php echo number_format($OpeningBalanceDebitfinal,2);?></b></div>
			<div style="width:12.5%;float:left; text-align:right;border:1px solid gray !important;"><b><?php echo number_format($Creditfinal,2);?></b></div>
            <div style="width:12.5%;float:left; text-align:right;border:1px solid gray !important;"><b><?php echo number_format($Debitfinal,2);?></b></div>
			<div style="width:12.5%;float:left;text-align:right;border:1px solid gray !important;"><b><?php echo number_format($ClosingBalanceCreditfinal,2);?></b></div>
			<div style="width:12.5%;float:right;text-align:right;border:1px solid gray !important;"><b><?php echo number_format($ClosingBalanceDebitfinal,2);?></b></div>
         </div>
    </td>
</tr>
<?php }
else
{?>
<script>document.getElementById('report_type').value = '2';</script>
<tr>
	<td style="font-size:14px;">
    	<div style="width:100%;">
        	<div style="width:25%;float:left;border:1px solid gray !important;"><b>Particulars</b></div>
			<div style="width:25%;float:left; text-align:center;border:1px solid gray !important;"><b>Opening Balance(Rs.)</b></div>
            <div style="width:25%;float:left; text-align:center;border:1px solid gray !important;"><b>Transactions(Rs.)</b></div>
            <div style="width:25%;float:left;text-align:center;border:1px solid gray !important;"><b>Closing Balance(Rs.)</b></div>
        </div>
	</td>
</tr>
<tr>
	<td style="font-size:14px;">
    	<div style="width:100%;">
        	<div style="width:25%;float:left;border:1px solid gray !important;">&nbsp;</div>
            <div style="width:12.5%;float:left; text-align:center;border:1px solid gray !important;"><b>Cr</b></div>
            <div style="width:12.5%;float:left; text-align:center;border:1px solid gray !important;"><b>Dr</b></div>
            <div style="width:12.5%;float:left; text-align:center;border:1px solid gray !important;"><b>Cr</b></div>
            <div style="width:12.5%;float:left; text-align:center;border:1px solid gray !important;"><b>Dr</b></div>
            <div style="width:12.5%;float:left; text-align:center;border:1px solid gray !important;"><b>Cr</b></div>
            <div style="width:12.5%;float:right; text-align:center;border:1px solid gray !important;"><b>Dr</b></div>
        </div>
	</td>
</tr>
<tr align="center"><td style="font-size:16px;" valign="middle"><br /><div style="width:50%;background-color: #337ab7; color: #fff; border-radius:13px;"><b>Liabilities</b></div><br /></td></tr>
<tr><td><?php echo ('<div class="tree well">'.$objLIABILITY->generateTrialBalance($LIABILITYData,0). '</div>');?></td></tr>
<tr align="center"><td style="font-size:16px;" valign="middle"><br /><div style="width:50%;background-color: #337ab7; color: #fff; border-radius:13px;"><b>Assets</b></div><br /></td></tr>
<tr><td><?php echo ('<div class="tree well">'.$objASSET->generateTrialBalance($ASSETData,0). '</div>');?></td></tr>
<tr align="center"><td style="font-size:16px;" valign="middle"><br /><div style="width:50%;background-color: #337ab7; color: #fff; border-radius:13px;"><b>Expenses</b></div><br /></td></tr>
<tr><td><?php echo ('<div class="tree well">'.$objEXPENSE->generateTrialBalance($EXPENSEData,0,true). '</div>');?></td></tr>
<tr align="center"><td style="font-size:16px;" valign="middle"><br /><div style="width:50%;background-color: #337ab7; color: #fff; border-radius:13px;"><b>Income</b></div><br /></td></tr>
<tr><td><?php echo ('<div class="tree well">'.$objINCOME->generateTrialBalance($INCOMEData,0,true). '</div>');?></td></tr>
<!--<tr><td style="font-size:14px;"><div style="width:100%;"><div style="width:25%;float:left;"><b>Grand Total</b></div><div style="width:15%;float:left; text-align:right;"><b><?php //echo number_format($OpeningBalancefinal,2);?></b></div><div style="width:17%;float:left; text-align:right;"><b><?php //echo number_format($Creditfinal,2);?></b></div><div style="width:20%;float:left; text-align:right;"><b><?php //echo number_format($Debitfinal,2);?></b></div><div style="width:15%;float:right;"><b><?php //echo number_format($ClosingBalancefinal,2);?></b></div></div></td></tr>-->
<tr>
	<td style="font-size:14px;">
    	<div style="width:100%;">
        	<div style="width:25%;float:left;border:1px solid gray !important;"><b>Profit & Loss A/c</b></div>
			<div style="width:12.5%;float:left; text-align:right;border:1px solid gray !important;"><b><?php echo number_format($profitLossOpeningCredit,2);?></b></div>
			<div style="width:12.5%;float:left; text-align:right;border:1px solid gray !important;"><b><?php echo number_format($profitLossOpeningDebit,2);?></b></div>
			<div style="width:12.5%;float:left; text-align:right;border:1px solid gray !important;"><b><?php echo number_format(0,2);?></b></div>
            <div style="width:12.5%;float:left; text-align:right;border:1px solid gray !important;"><b><?php echo number_format(0,2);?></b></div>
			<div style="width:12.5%;float:left;text-align:right;border:1px solid gray !important;"><b><?php echo number_format($profitLossOpeningCredit,2);?></b></div>
			<div style="width:12.5%;float:right;text-align:right;border:1px solid gray !important;"><b><?php echo number_format($profitLossOpeningDebit,2);?></b></div>
         </div>
    </td>
</tr>

<tr>
	<td style="font-size:14px;">
    	<div style="width:100%;">
        	<div style="width:25%;float:left;border:1px solid gray !important;"><b>Grand Total</b></div>
             <div style="width:12.5%;float:left; text-align:right;border:1px solid gray !important;"><b><?php echo number_format($OpeningBalanceCreditfinal,2);?></b></div>
             <div style="width:12.5%;float:left; text-align:right;border:1px solid gray !important;"><b><?php echo number_format($OpeningBalanceDebitfinal,2);?></b></div>
             <div style="width:12.5%;float:left; text-align:right;border:1px solid gray !important;"><b><?php echo number_format($Creditfinal,2);?></b></div>
             <div style="width:12.5%;float:left; text-align:right;border:1px solid gray !important;"><b><?php echo number_format($Debitfinal,2);?></b></div>
             <div style="width:12.5%;float:left;text-align:right;border:1px solid gray !important;"><b><?php echo number_format($ClosingBalanceCreditfinal,2);?></b></div>
             <div style="width:12.5%;float:right;text-align:right;border:1px solid gray !important;"><b><?php echo number_format($ClosingBalanceDebitfinal,2);?></b></div>
         </div>
     </td>
</tr>
<?php }?>
	
<script type="text/javascript">
$(function () {
	 $('.tree li:has(ul)').addClass('parent_li').find(' > label ').attr('title', 'Collapse this branch');
    $('.tree li.parent_li > label ').on('click', function (e) {
		 var children = $(this).parent('li.parent_li').find(' > ul > li');
        if (children.is(":visible")) 
		{
            children.hide('fast');
            $(this).attr('title', 'Expand this branch').find(' > div').addClass('icon-plus-sign').removeClass('icon-minus-sign');
        } else 
		{
            children.show('fast');
			$(this).attr('title', 'Collapse this branch').find(' > div').addClass('icon-minus-sign').removeClass('icon-plus-sign');
        }
        e.stopPropagation();
    });
});

</script>

</table>
</center>
<!-- code for export trial balance to excel-->
<div id="Exportdiv" class="Exportdiv">
<center>
<div>
        <div id="bill_header" style="text-align:center;">
            <div id="society_name" style="font-weight:bold; font-size:18px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
            <div id="society_reg" style="font-size:14px;"><?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
				}
				?></div>
            <div id="society_address"; style="font-size:14px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></div>
        </div>
        <div id="bill_subheader" style="text-align:center;">
            <div style="font-weight:bold; font-size:16px;">TRIAL BALANCE SHEET FROM <?php echo getDisplayFormatDate($_SESSION['from_date']); ?> TO <?php echo getDisplayFormatDate($_SESSION['to_date']);?></div>
        </div>
<table  align="center"   width="100%" id="Exportdata" class="ExportStyle" style="border:1px dashed gray !important;" >
    <tr class="rowstyle">
        
		 <?php
			if(!isset($_GET['q']) || (isset($_GET['q']) && $_GET['q'] == 2))
			{
		  ?>
	          <th style="text-align:left;font-size:14px;border:1px dashed gray !important; width:300px;">Particulars</th>
              <th style="text-align:center;font-size:14px;border:1px dashed gray !important;width:200px;" colspan="2">Opening Balance(Rs.)</th>
              <th style="text-align:center;font-size:14px;border:1px dashed gray !important;width:200px;" colspan="2">Trasanctions(Rs.)</th>
              <th style="text-align:center;font-size:14px;border:1px dashed gray !important;width:200px;" colspan="2">Closing Balance(Rs.)</th>
        <?php
			}
			else
			{
			?>
                <th style="text-align:left;font-size:14px;border:1px dashed gray !important; width:600px;">Particulars</th>
                <th style="text-align:center;font-size:14px;border:1px dashed gray !important;width:300px;" colspan="2">Closing Balance(Rs.)</th>
        <?php
			}
			?>
     </tr>
    
    <tr class="rowstyle">
        <th style="text-align:left;font-size:14px;border:1px dashed gray !important; width:300px;"></th>
         <?php
		if(!isset($_GET['q']) || (isset($_GET['q']) && $_GET['q'] == 2))
		{
		?>
            <th style="text-align:right;font-size:14px;border:1px dashed gray !important;width:100px;" >Cr</th>
            <th style="text-align:right;font-size:14px;border:1px dashed gray !important;width:100px;" >Dr</th>
            <th style="text-align:right;font-size:14px;border:1px dashed gray !important;width:100px;" >Cr</th>
            <th style="text-align:right;font-size:14px;border:1px dashed gray !important;width:100px;" >Dr</th>
            <th style="text-align:right;font-size:14px;border:1px dashed gray !important;width:100px;" >Cr</th>
            <th style="text-align:right;font-size:14px;border:1px dashed gray !important;width:100px;" >Dr</th>
        <?php
		}
		else
		{
		?>
            <th style="text-align:right;font-size:14px;border:1px dashed gray !important;width:150px;">Cr</th>
            <th style="text-align:right;font-size:14px;border:1px dashed gray !important;width:150px;">Dr</th>
        <?php
		}
		?>
    </tr>
    <?php if(sizeof($LIABILITYData) > 0)
	{
			if(!isset($_GET['q']) || (isset($_GET['q']) && $_GET['q'] == 2))
			{?>
				 <tr class="rowstyle"><td style="font-size:18px;text-align:center;border-bottom:1px solid black;" colspan="7"><b>LIABILITIES</b></td></tr>
                 
                 <tr class="rowstyle"><td ><?php echo $objLIABILITY->generateTrialBalanceTable($LIABILITYData,0);?></td></tr>
				                 
                 <tr class="rowstyle" style="font-size:12px;border:1px solid black;">
    			 	<td><b>TOTAL LIABILITIES</b></td> 
                 	<td style="text-align:right;width:100px;"><b><?php echo number_format($LIABILITYOpeningBalanceCreditTotal,2);?></b></td>
                    <td style="text-align:right;width:100px;"><b><?php echo number_format($LIABILITYOpeningBalanceDebitTotal,2);?></b></td>
                    <td style="text-align:right;width:100px;"><b><?php echo number_format($LIABILITYcredit,2);?></b></td>
                    <td style="text-align:right;width:100px;"><b><?php echo number_format($LIABILITYdebit,2);?></b></td>
                    <td style="text-align:right;width:100px;"><b><?php echo number_format($LIABILITYClosingBalanceCreditTotal,2);?></b></td>
                    <td style="text-align:right;width:100px;"><b><?php echo number_format($LIABILITYClosingBalanceDebitTotal,2);?></b></td>
                 </tr>
                 <tr class="rowstyle"><td style="border-top:1px solid black;" colspan="7"></td></tr>
	  <?php }
			else
			{ ?>
				   <tr class="rowstyle"><td style="font-size:18px;text-align:center;" colspan="3"><b>LIABILITIES</b></td></tr>
                   
                   <tr class="rowstyle"><td ><?php echo $objLIABILITY->generateTrialBalanceTable($LIABILITYData,0);?></td></tr>
		                          
                   
                   <tr class="rowstyle" style="font-size:12px;border:1px solid black;">
                       <td><b>TOTAL LIABILITIES</b></td> 
                       <td style="text-align:right;width:150px;"><b><?php echo number_format($LIABILITYClosingBalanceCreditTotal,2);?></b></td>
                       <td style="text-align:right;width:150px;"><b><?php echo number_format($LIABILITYClosingBalanceDebitTotal,2);?></b></td>
                   </tr>
                   <tr class="rowstyle"><td style="border-top:1px solid black;" colspan="2"></td></tr>
	  <?php } ?>
      			<tr class="rowstyle"><td><br /></td></tr>
    <?php }?>
    <?php if(sizeof($ASSETData) > 0)
	{
			if(!isset($_GET['q']) || (isset($_GET['q']) && $_GET['q'] == 2))
			{?>
				<tr class="rowstyle"><td style="font-size:18px;text-align:center;border-bottom:1px solid black;"  colspan="7"><b>ASSETS</b></td></tr>
                
                <tr class="rowstyle"><td><?php echo $objASSET->generateTrialBalanceTable($ASSETData,0);?></td></tr>
		        		                
                <tr class="rowstyle" style="font-size:12px;border:1px solid black;">
    			 	<td><b>TOTAL ASSETS</b></td> 
                 	<td style="text-align:right;width:100px;"><b><?php echo number_format($ASSETOpeningBalanceCreditTotal,2);?></b></td>
                    <td style="text-align:right;width:100px;"><b><?php echo number_format($ASSETOpeningBalanceDebitTotal,2);?></b></td>
                    <td style="text-align:right;width:100px;"><b><?php echo number_format($ASSETcredit,2);?></b></td>
                    <td style="text-align:right;width:100px;"><b><?php echo number_format($ASSETdebit,2);?></b></td>
                    <td style="text-align:right;width:100px;"><b><?php echo number_format($ASSETClosingBalanceCreditTotal,2);?></b></td>
                    <td style="text-align:right;width:100px;"><b><?php echo number_format($ASSETClosingBalanceDebitTotal,2);?></b></td>
                 </tr>
                 <tr class="rowstyle"><td style="border-top:1px solid black;" colspan="7"></td></tr>  
	  <?php }
			else
			{ ?>
				   <tr class="rowstyle"><td style="font-size:18px;text-align:center;"  colspan="3"><b>ASSETS</b></td></tr>
                   
                   <tr class="rowstyle"><td><?php echo $objASSET->generateTrialBalanceTable($ASSETData,0);?></td></tr>
		           		           
                    <tr class="rowstyle" style="font-size:12px;border:1px solid black;">
    					<td><b>TOTAL ASSETS</b></td> 
                        <td style="text-align:right;width:150px;"><b><?php echo number_format($ASSETClosingBalanceCreditTotal,2);?></b></td>
                        <td style="text-align:right;width:150px;"><b><?php echo number_format($ASSETClosingBalanceDebitTotal,2);?></b></td>
                    </tr>
                    <tr class="rowstyle"><td style="border-top:1px solid black;" colspan="2"></td></tr>  
	  <?php } ?>
                  <tr class="rowstyle"><td><br /></td></tr>    
		
    <?php }?>
    <?php if(sizeof($EXPENSEData) > 0)
	{
			if(!isset($_GET['q']) || (isset($_GET['q']) && $_GET['q'] == 2))
			{?>
				<tr class="rowstyle"><td style="font-size:18px;text-align:center;border-bottom:1px solid black;" colspan="7"><b>EXPENSES</b></td></tr>
                
                <tr class="rowstyle"><td><?php echo $objEXPENSE->generateTrialBalanceTable($EXPENSEData,0,true);?></td></tr>
								        
                <tr class="rowstyle" style="font-size:12px;border:1px solid black;">
    			 	<td><b>TOTAL EXPENSES</b></td> 
                 	<td style="text-align:right;width:100px;"><b><?php echo number_format($EXPENSEOpeningBalanceCreditTotal,2);?></b></td>
                    <td style="text-align:right;width:100px;"><b><?php echo number_format($EXPENSEOpeningBalanceDebitTotal,2);?></b></td>
                    <td style="text-align:right;width:100px;"><b><?php echo number_format($EXPENSEcredit,2);?></b></td>
                    <td style="text-align:right;width:100px;"><b><?php echo number_format($EXPENSEdebit,2);?></b></td>
                    <td style="text-align:right;width:100px;"><b><?php echo number_format($EXPENSEClosingBalanceCreditTotal,2);?></b></td>
                    <td style="text-align:right;width:100px;"><b><?php echo number_format($EXPENSEClosingBalanceDebitTotal,2);?></b></td>
                   
                 </tr>
                 <tr class="rowstyle"><td style="border-top:1px solid black;" colspan="7"></td></tr>  
	  <?php }
			else
			{ ?>
				   <tr class="rowstyle"><td style="font-size:18px;text-align:center;" colspan="3"><b>EXPENSES</b></td></tr>
                   
                   <tr class="rowstyle"><td><?php echo $objEXPENSE->generateTrialBalanceTable($EXPENSEData,0,true);?></td></tr>
				   		           
        			
                   <tr class="rowstyle" style="font-size:12px;border:1px solid black;">
                       <td><b>TOTAL EXPENSES</b></td>  
                       <td style="text-align:right;width:150px;"><b><?php echo number_format($EXPENSEClosingBalanceCreditTotal,2);?></b></td>
                       <td style="text-align:right;width:150px;"><b><?php echo number_format($EXPENSEClosingBalanceDebitTotal,2);?></b></td>
                   </tr>
                   <tr class="rowstyle"><td style="border-top:1px solid black;" colspan="2"></td></tr>  
	  <?php } ?>
               <tr class="rowstyle"><td><br /></td></tr>   
		
    <?php }?>
    <?php if(sizeof($INCOMEData) > 0)
	{
			if(!isset($_GET['q']) || (isset($_GET['q']) && $_GET['q'] == 2))
			{?>
				<tr class="rowstyle"><td style="font-size:18px;text-align:center;border-bottom:1px solid black;" colspan="7"><b>INCOME</b></td></tr>
                
                <tr class="rowstyle"><td><?php echo $objINCOME->generateTrialBalanceTable($INCOMEData,0,true);?></td></tr>
		        		                
                <tr class="rowstyle" style="font-size:12px;border:1px solid black;">
    			 	<td><b>TOTAL INCOME</b></td> 
                 	<td style="text-align:right;width:100px;"><b><?php echo number_format($INCOMEOpeningBalanceCreditTotal,2);?></b></td>
                    <td style="text-align:right;width:100px;"><b><?php echo number_format($INCOMEOpeningBalanceDebitTotal,2);?></b></td>
                    <td style="text-align:right;width:100px;"><b><?php echo number_format($INCOMEcredit,2);?></b></td>
                    <td style="text-align:right;width:100px;"><b><?php echo number_format($INCOMEdebit,2);?></b></td>
                    <td style="text-align:right;width:100px;"><b><?php echo number_format($INCOMEClosingBalanceCreditTotal,2);?></b></td>
                    <td style="text-align:right;width:100px;"><b><?php echo number_format($INCOMEClosingBalanceDebitTotal,2);?></b></td>
                    
                 </tr>
                 <tr class="rowstyle"><td style="border-top:1px solid black;" colspan="7"></td></tr>  
	  <?php }
			else
			{ ?>
				 <tr class="rowstyle"><td style="font-size:18px;text-align:center;" colspan="3"><b>INCOME</b></td></tr>
                 
                 <tr class="rowstyle"><td><?php echo $objINCOME->generateTrialBalanceTable($INCOMEData,0,true);?></td></tr>
		         		                         
                <tr class="rowstyle" style="font-size:12px;border:1px solid black;">
                    <td><b>TOTAL INCOME</b></td> 
                    <td style="text-align:right;width:150px;"><b><?php echo number_format($INCOMEClosingBalanceCreditTotal,2);?></b></td>
                    <td style="text-align:right;width:150px;"><b><?php echo number_format($INCOMEClosingBalanceDebitTotal,2);?></b></td>
                </tr>
                <tr class="rowstyle"><td style="border-top:1px solid black;" colspan="2"></td></tr>  
	  <?php } ?>
               <tr class="rowstyle"><td><br /></td></tr>        
		
    <?php }?>
    <tr class="rowstyle" style="font-size:18px;border:1px dashed gray !important;">
    	<td><b>Profit & Loss A/c</b></td> 
        <?php
		if(!isset($_GET['q']) || (isset($_GET['q']) && $_GET['q'] == 2))
		{
		?>
            <td style="text-align:right;width:150px;border-left:1px dashed gray !important;"><b><?php echo number_format($profitLossOpeningCredit,2);?></b></td>
            <td style="text-align:right;width:150px;border-left:1px dashed gray !important;"><b><?php echo number_format($profitLossOpeningDebit,2);?></b></td>
            <td style="text-align:right;width:150px;border-left:1px dashed gray !important;"><b><?php echo number_format(0,2);?></b></td>
            <td style="text-align:right;width:150px;border-left:1px dashed gray !important;"><b><?php echo number_format(0,2);?></b></td>
            <td style="text-align:right;width:150px;border-left:1px dashed gray !important;"><b><?php echo number_format($profitLossOpeningCredit,2);?></b></td>
            <td style="text-align:right;width:150px;border-left:1px dashed gray !important;"><b><?php echo number_format($profitLossOpeningDebit,2);?></b></td>
           
        <?php
		}
		else
		{
		?>
            <td style="text-align:right;width:150px;"><b><?php echo number_format($profitLossOpeningCredit,2);?></b></td>
            <td style="text-align:right;width:150px;"><b><?php echo number_format($profitLossOpeningDebit,2);?></b></td></tr>
        <?php
		}
		?>
    </tr>  
   
    <tr class="rowstyle" style="font-size:18px;border:1px dashed gray !important;">
    	<td><b>TOTAL</b></td> 
        <?php
		if(!isset($_GET['q']) || (isset($_GET['q']) && $_GET['q'] == 2))
		{
		?>
            <td style="text-align:right;width:150px;border-left:1px dashed gray !important;"><b><?php echo number_format($OpeningBalanceCreditfinal,2);?></b></td>
            <td style="text-align:right;width:150px;border-left:1px dashed gray !important;"><b><?php echo number_format($OpeningBalanceDebitfinal,2);?></b></td>
            <td style="text-align:right;width:150px;border-left:1px dashed gray !important;"><b><?php echo number_format($Creditfinal,2);?></b></td>
            <td style="text-align:right;width:150px;border-left:1px dashed gray !important;"><b><?php echo number_format($Debitfinal,2);?></b></td>
            <td style="text-align:right;width:150px;border-left:1px dashed gray !important;"><b><?php echo number_format($ClosingBalanceCreditfinal,2);?></b></td>
            <td style="text-align:right;width:150px;border-left:1px dashed gray !important;"><b><?php echo number_format($ClosingBalanceDebitfinal,2);?></b></td>
           
        <?php
		}
		else
		{
		?>
            <td style="text-align:right;width:150px;"><b><?php echo number_format($ClosingBalanceCreditfinal,2);?></b></td>
            <td style="text-align:right;width:150px;"><b><?php echo number_format($ClosingBalanceDebitfinal,2);?></b></td></tr>
        <?php
		}
		?>
    </tr>  
</table>
</div>
</center>
</div>


<br />
<br />
<div style="padding-left: 15px;">
<INPUT TYPE="button" VALUE="Expand All" onClick="ExpandALL();"  class="btn btn-primary"    style="width:10%;box-shadow: none;">
<INPUT TYPE="button" VALUE="Collapse All" onClick="CollapseALL();" class="btn btn-primary"     style="width:10%;box-shadow: none;">
<?php if($_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] == 1){?>
<input  type="button" id="btnExport2" value=" Export To Excel"  class="btn btn-primary"   style="width:150px;box-shadow: none;" onclick="ExportToExcel(this.id);"/>
<?php } ?>
<?php if($ClosingBalanceDebitfinal <> $ClosingBalanceCreditfinal)
{?>
<INPUT TYPE="button" VALUE="Registers Validation" onClick="window.open('RegistersValidation.php','RegistersValidationpopup','type=width=700,height=600,scrollbars=yes');"  class="btn btn-primary"    style="box-shadow: none; float:right;margin-right: 9px;">
<INPUT TYPE="button" VALUE="Bank Validation" onClick="window.open('BankEntriesValidation.php','BankEntriesValidationpopup','type=width=700,height=600,scrollbars=yes');"  class="btn btn-primary"    style="box-shadow: none; float:right;margin-right: 2px;">
<?php } ?>
</div>
<br />
<br />
</div>
<?php include_once "includes/foot.php"; ?>