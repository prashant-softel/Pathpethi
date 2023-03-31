<?php 
include_once "ses_set_s.php"; 
include_once("includes/head_s.php"); 
include_once ("classes/include/dbop.class.php");
	$dbConn = new dbop();
include_once "classes/dbconst.class.php";
include "classes/BalanceSheet_20190328.class.php";

$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
$objINCOME = new BalanceSheet($dbConn);
$objEXPENSE = new BalanceSheet($dbConn);

$INCOMEData = $objINCOME->CategoryArray(INCOME, getDBFormatDate($_SESSION['from_date']), getDBFormatDate($_SESSION['to_date']));
$EXPENSEData = $objEXPENSE->CategoryArray(EXPENSE, getDBFormatDate($_SESSION['from_date']), getDBFormatDate($_SESSION['to_date']));

$IncomeCreditPrev = $objINCOME->CreditTotalPrev;
$IncomeDebitPrev = $objINCOME->DebitTotalPrev;
$IncomeCredit = $objINCOME->CreditTotal;
$IncomeDebit = $objINCOME->DebitTotal;


$incomeTotalPrev = abs($IncomeCreditPrev - $IncomeDebitPrev);
$incomeTotalFinalPrev = $incomeTotalPrev;

$incomeTotal = abs($IncomeCredit - $IncomeDebit);
$incomeTotalFinal = $incomeTotal;

$ExpenseCreditPrev = $objEXPENSE->CreditTotalPrev;
$ExpenseDebitPrev = $objEXPENSE->DebitTotalPrev;
$ExpenseCredit = $objEXPENSE->CreditTotal;
$ExpenseDebit = $objEXPENSE->DebitTotal;


$expenseTotalPrev = abs($ExpenseDebitPrev - $ExpenseCreditPrev);
$expenseTotalFinalPrev = $expenseTotalPrev;

$expenseTotal = abs($ExpenseDebit - $ExpenseCredit);
$expenseTotalFinal = $expenseTotal;

$PrevYearTotal = $incomeTotalFinalPrev - $expenseTotalFinalPrev;
$CurrYearTotal = $incomeTotalFinal - $expenseTotalFinal;

$excessIncomePrevYear = 0;
$excessIncomeCurrYear = 0;
$excessExpensePrevYear = 0;
$excessExpenseCurrYear = 0;

if($PrevYearTotal > 0)
{
	$excessIncomePrevYear = $PrevYearTotal;	
}
else
{
	$excessExpensePrevYear = abs($PrevYearTotal);	
}

if($CurrYearTotal > 0)
{
	$excessIncomeCurrYear = $CurrYearTotal;	
}
else
{
	$excessExpenseCurrYear = abs($CurrYearTotal);	
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>INCOME AND EXPENDITURE STATEMENT</title>
<script  type="text/javascript" src="js/jsBalanceSheet.js"></script>
<script type="text/javascript" language="javascript" src="media/js/jquery.js"></script>
<script src="js/jquery.treetable.js"></script>
<link rel="stylesheet" href="css/jquery.treetable.css" />
<link rel="stylesheet" href="css/jquery.treetable.theme.default.css" />
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
<br>
<div class="panel panel-info" id="panel" style="display:none;min-width: 100%;">
    <div class="panel-heading" id="pageheader">INCOME AND EXPENDITURE STATEMENT</div>
    <br />
   
   <!-- <div style="padding-left: 15px;"><button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;"><i class="fa  fa-arrow-left"></i></button></div>-->
<center><font style="font-size:14px;"><b>INCOME AND EXPENDITURE STATEMENT FROM <?php echo getDisplayFormatDate($_SESSION['from_date']); ?> TO <?php echo getDisplayFormatDate($_SESSION['to_date']);?></b></font></center>
<br />
<br />
<div style="padding-left: 15px;">
<INPUT TYPE="button" VALUE="Expand All" onClick="jQuery('#example-basic-expandable2').treetable('expandAll'); jQuery('#example-basic-expandable3').treetable('expandAll');return false;"   class="btn btn-primary"   style="width:10vw;box-shadow: none;font-size:1.1vw;">
<INPUT TYPE="button" VALUE="Collapse All" onClick="jQuery('#example-basic-expandable2').treetable('collapseAll');jQuery('#example-basic-expandable3').treetable('collapseAll'); return false;"    class="btn btn-primary"  style="width:10vw;box-shadow: none;font-size:1.1vw;">
<?php //if($_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] == 1){?>
<input  type="button" id="btnExport1" value=" Export To Excel"  style="width:10vw;box-shadow: none;font-size:1.1vw;" class="btn btn-primary"   onclick="ExportToExcel(this.id);">
<?php //}?>
<label>&nbsp;Show Previous Amount&nbsp;&nbsp;<input type="checkbox" name="checkbox1" id="checkbox1" onclick="togglePrevAmt(this.id);" checked="checked"/></label>
<br />
<br />
<center>
<table  align="center" width="100%"  style="max-width:100%;border:1px solid black;font-size:1.1vw;table-layout: fixed;" >
<tr>
	<td style="width: 50%;">
     <table  id="example-basic-expandable2"   style="width:100%; float:left;min-width:100%; max-width:100%;">
    	 <thead>
          <tr>
          <th  style="width: 10px;" class = "icon-class"></th>
            <th   style="border:1px solid gray !important; width:25%;text-align:center; " class = "Previous">Previous &nbsp;(Rs.)</th>
            <th style="border:1px solid gray !important; width:50%;text-align:center;">Particulars &nbsp;(Expense)</th>
            <th style="border:1px solid gray !important; width:25%;text-align:center; " class = "Current">Current &nbsp;(Rs.)</th>
          </tr>
        </thead>
        <tbody   style="width:100%; float:left; height:400px; overflow-y:auto;min-width:80%;">
         <?php 
      	echo $objEXPENSE->generateIncomeStatement(EXPENSE,$EXPENSEData,0);
        ?>
        </tbody>
         <tfoot>
          <tr>
           <th  style="width: 10px;"  class = "icon-class">&nbsp;</th>
            <th   style="border:1px solid gray !important; width:25%;text-align:center; " class = "Previous"><?php echo number_format($expenseTotalFinalPrev, 2);?></th>
            <th style="border:1px solid gray !important; width:50%;text-align:center;">Total &nbsp;(Rs.)</th>
            <th style="border:1px solid gray !important; width:25%;text-align:center; " class = "Current"><?php echo number_format($expenseTotalFinal, 2);?></th>
          </tr>
          
           <tr>
             <th  style="width: 10px;"   class = "icon-class">&nbsp;</th>
            <th   style="border:1px solid gray !important; width:25%;text-align:center; " class = "Previous"><?php echo number_format($excessIncomePrevYear, 2);?></th>
            <th style="border:1px solid gray !important; width:50%;text-align:center;">Excess of Income Over Expenditure</th>
            <th style="border:1px solid gray !important; width:25%;text-align:center; " class = "Current"><?php echo number_format($excessIncomeCurrYear, 2);?></th>
          </tr>
        </tfoot>
        </table>
     </td>
     <td  style="width: 50%;">
        <table  id="example-basic-expandable3" style="width:100%; float:right;min-width:100%; max-width:100%;">
           <thead>
          <tr>
            <th  style="width: 10px;"   class = "icon-class">&nbsp;</th>
            <th   style="border:1px solid gray !important; width:25%;text-align:center; " class = "Previous">Previous &nbsp;(Rs.)</th>
            <th style="border:1px solid gray !important; width:50%;text-align:center;">Particulars &nbsp;(Income)</th>
            <th style="border:1px solid gray !important; width:25%;text-align:center; " class = "Current">Current &nbsp;(Rs.)</th>
          </tr>
        </thead>
        <tbody   style="width:100%; float:right; height:400px; overflow-y:auto;min-width:80%;">
        <?php 
         echo $objINCOME->generateIncomeStatement(INCOME,$INCOMEData,0);
        ?>
        </tbody>
         <tfoot>
          <tr>
             <th  style="width:10px;"   class = "icon-class">&nbsp;</th>
            <th   style="border:1px solid gray !important; width:25%;text-align:center; " class = "Previous"><?php echo number_format($incomeTotalFinalPrev, 2);?></th>
            <th style="border:1px solid gray !important; width:50%;text-align:center;">Total &nbsp;(Rs.)</th>
            <th style="border:1px solid gray !important; width:25%;text-align:center; " class = "Current"><?php echo number_format($incomeTotalFinal, 2);?></th>
          </tr>
          
           <tr>
           <th  style="width: 10px;"   class = "icon-class">&nbsp;</th>
            <th   style="border:1px solid gray !important; width:25%;text-align:center; " class = "Previous"><?php echo number_format($excessExpensePrevYear, 2);?></th>
            <th style="border:1px solid gray !important; width:50%;text-align:center;">Excess of Expenditure Over Income </th>
            <th style="border:1px solid gray !important; width:25%;text-align:center; " class = "Current"><?php echo number_format($excessExpenseCurrYear, 2);?></th>
          </tr>
        </tfoot>
        </table>
         
     </td>
<tr>

<script type="text/javascript">
$(function () 
{
	 $('.tree li:has(ul)').addClass('parent_li').find(' > label ').attr('title', 'Collapse this branch');
     $('.tree li.parent_li > label ').on('click', function (e) {
		var children = $(this).parent('li.parent_li').find(' > ul > li');
        
		if (children.is(":visible")) 
		{
            children.hide('fast');
            $(this).attr('title', 'Expand this branch').find(' > span').addClass('icon-plus-sign').removeClass('icon-minus-sign');
        } 
		else 
		{
            children.show('fast');
			$(this).attr('title', 'Collapse this branch').find(' > span').addClass('icon-minus-sign').removeClass('icon-plus-sign');
        }
		
        e.stopPropagation();
    });
});



$("#example-basic-expandable2").treetable({ expandable: true });
$("#example-basic-expandable3").treetable({ expandable: true });

</script>
</table>

</center>

<br />
<br />
<div style="padding-left: 15px;">
<INPUT TYPE="button" VALUE="Expand All" onClick="jQuery('#example-basic-expandable2').treetable('expandAll'); jQuery('#example-basic-expandable3').treetable('expandAll');return false;"    class="btn btn-primary"   style="width:10vw;box-shadow: none;font-size:1.1vw;">
<INPUT TYPE="button" VALUE="Collapse All" onClick="jQuery('#example-basic-expandable2').treetable('collapseAll');jQuery('#example-basic-expandable3').treetable('collapseAll'); return false;"    class="btn btn-primary"   style="width:10vw;box-shadow: none;font-size:1.1vw;">
<?php //if($_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] == 1){?>
<input  type="button" id="btnExport1" value=" Export To Excel"   style="width:10vw;box-shadow: none;font-size:1.1vw;" class="btn btn-primary"   onclick="ExportToExcel(this.id);"/>
<?php //} ?>
</div>
<br />
<br />
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
            <div style="font-weight:bold; font-size:16px;">INCOME AND EXPENDITURE STATEMENT FROM <?php echo getDisplayFormatDate($_SESSION['from_date']); ?> TO <?php echo getDisplayFormatDate($_SESSION['to_date']);?></div>
        </div>
<table  align="center"   width="100%" id="Exportdata" class="ExportStyle" style="border:1px dashed gray !important;">
    <tr class="rowstyle">
      <th style="text-align:center;font-size:14px;border:1px dashed gray !important;"  colspan="2" class = "Previous">Previous (Rs.)</th>
      <th style="text-align:left;font-size:14px;border:1px dashed gray !important; " >Particulars(Expense)</th>
      <th style="text-align:center;font-size:14px;border:1px dashed gray !important;"  colspan="2">Current (Rs.)</th>
      <th style="text-align:center;font-size:14px;border:1px dashed gray !important;"  colspan="2" class = "Previous">Previous (Rs.)</th>
      <th style="text-align:left;font-size:14px;border:1px dashed gray !important;">Particulars(Income)</th>
      <th style="text-align:center;font-size:14px;border:1px dashed gray !important;" colspan="2">Current (Rs.)</th>
    </tr>
    <tr class="rowstyle">
    	<td colspan="5" class ="colchange">
    		<table>
            	<?php echo $objEXPENSE->generateIncomeStatementTable(EXPENSE,$EXPENSEData,0);?>
             </table>
        </td>
       <td colspan="5" class ="colchange">
        	<table>
				<?php echo $objEXPENSE->generateIncomeStatementTable(INCOME,$INCOMEData,0);?>
			 </table>
        </td>
    </tr>
     <tr class="rowstyle">
    	 <td style="text-align:right;font-size:14px;border:1px dashed gray !important;" class = "Previous"></td>
    	<td style="text-align:right;font-size:14px;border:1px dashed gray !important;" class = "Previous"><b><?php echo number_format($expenseTotalFinalPrev, 2);?></b></td>
         <td style="font-size:14px;border:1px dashed gray !important;"><b>TOTAL(Rs.)</b></td>
          <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"><b><?php echo number_format($expenseTotalFinal, 2);?></b></td>
         <td style="text-align:right;font-size:14px;border:1px dashed gray !important;" class = "Previous"></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;" class = "Previous"><b><?php echo number_format($incomeTotalFinalPrev, 2);?></b></td>
        <td style="font-size:14px;border:1px dashed gray !important;"><b>TOTAL(Rs.)</b></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"><b><?php echo number_format($incomeTotalFinal, 2);?></b></td>
     </tr>
    
    <tr class="rowstyle">
    	 <td style="text-align:right;font-size:14px;border:1px dashed gray !important;" class = "Previous"></td>
    	<td style="text-align:right;font-size:14px;border:1px dashed gray !important;" class = "Previous"><b><?php echo number_format($excessIncomePrevYear, 2);?></b></td>
         <td style="font-size:14px;border:1px dashed gray !important;"><b>Excess of Income Over Expenditure</b></td>
          <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"><b><?php echo number_format($excessIncomeCurrYear, 2);?></b></td>
         <td style="text-align:right;font-size:14px;border:1px dashed gray !important;" class = "Previous"></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;" class = "Previous"><b><?php echo number_format($excessExpensePrevYear, 2);?></b></td>
        <td style="font-size:14px;border:1px dashed gray !important;"><b>Excess of Expenditure over Income</b></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"><b><?php echo number_format($excessExpenseCurrYear, 2);?></b></td>
     </tr>
</table>
</div>
</center>
</div>

<br />
<br />
<script>togglePrevAmt("checkbox1");</script>
</div>

<?php include_once "includes/foot.php"; ?>