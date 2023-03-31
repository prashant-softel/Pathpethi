<?php 
include_once "ses_set_s.php"; 
include_once("includes/head_s.php");
include_once ("classes/include/dbop.class.php");
$dbConn = new dbop();
include_once "classes/dbconst.class.php";
include "classes/BalanceSheet_20190328.class.php";

$parent=0;

$objLIABILITY = new BalanceSheet($dbConn);
$objASSET = new BalanceSheet($dbConn);
$objINCOME = new BalanceSheet($dbConn);
$objEXPENSE = new BalanceSheet($dbConn);
$objFetchData = new FetchData($dbConn);

$OpeningBalance = $objLIABILITY->OpeningBalanceCalculate();
$LIABILITYData = $objLIABILITY->CategoryArray(LIABILITY, getDBFormatDate($_SESSION['from_date']), getDBFormatDate($_SESSION['to_date']));
$ASSETData = $objASSET->CategoryArray(ASSET, getDBFormatDate($_SESSION['from_date']), getDBFormatDate($_SESSION['to_date']));




$ASSETData = $objLIABILITY->ArrayShifting(LIABILITY,$LIABILITYData,$ASSETData);
$LIABILITYData = $objLIABILITY->UnsetArray($LIABILITYData);

$LIABILITYData = $objASSET->ArrayShifting(ASSET,$ASSETData,$LIABILITYData);
$ASSETData = $objASSET->UnsetArray($ASSETData);

$LIABILITYData = $objLIABILITY->CalculateTotal($LIABILITYData);
$ASSETData = $objASSET->CalculateTotal($ASSETData);

$LIABILITYTotal = $objLIABILITY->getTotal($LIABILITYData);
$ASSETTotal = $objASSET->getTotal($ASSETData);


$INCOMEData = $objINCOME->CategoryArray(INCOME, getDBFormatDate($_SESSION['from_date']), getDBFormatDate($_SESSION['to_date']));
$EXPENSEData = $objEXPENSE->CategoryArray(EXPENSE, getDBFormatDate($_SESSION['from_date']), getDBFormatDate($_SESSION['to_date']));

$INCOMEcredit = $objINCOME->CreditTotal;
$INCOMEdebit = $objINCOME->DebitTotal;
$INCOMEOpeningcredit = $objINCOME->OpeningBalanceCreditTotal;
$INCOMEOpeningdebit = $objINCOME->OpeningBalanceDebitTotal;
$INCOMEOpeningFinal = $INCOMEOpeningcredit - $INCOMEOpeningdebit;
$incomeTotal = abs(($INCOMEcredit + $INCOMEOpeningcredit)- ($INCOMEdebit + $INCOMEOpeningdebit ));

$EXPENSEcredit = $objEXPENSE->CreditTotal;
$EXPENSEdebit = $objEXPENSE->DebitTotal;
$EXPENSEOpeningcredit = $objEXPENSE->OpeningBalanceCreditTotal;
$EXPENSEOpeningdebit = $objEXPENSE->OpeningBalanceDebitTotal;
$EXPENSEOpeningFinal = $EXPENSEOpeningdebit - $EXPENSEOpeningcredit;

$expenseTotal = abs(($EXPENSEdebit + $EXPENSEOpeningdebit) - ($EXPENSEcredit + $EXPENSEOpeningcredit));

$LastYearBalance =  $INCOMEOpeningFinal - $EXPENSEOpeningFinal;
$CurrentYearBalance =  abs(($INCOMEcredit- $INCOMEdebit)- ($EXPENSEdebit - $EXPENSEcredit));
if($incomeTotal > $expenseTotal)
{
	
	$netIncome = $incomeTotal - $expenseTotal;
	$ProfitnLoss = $netIncome;
	$LIABILITYTotal = $LIABILITYTotal +  $ProfitnLoss;
	
}
else
{
	$netLoss = $expenseTotal - $incomeTotal;
	$ProfitnLoss = $netLoss;
	$LIABILITYTotal = $LIABILITYTotal  - $ProfitnLoss;
}
$objFetchData->GetSocietyDetails($_SESSION['society_id']);

?>

<html>
<head>
<title>BALANCE SHEET</title>
<script  type="text/javascript" src="js/jsBalanceSheet.js"></script>
<script type="text/javascript" language="javascript" src="media/js/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="css/treeview.css" >
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
<div class="panel panel-info" id="panel" style="display:none;">
    <div class="panel-heading" id="pageheader">BALANCE SHEET</div>
   <br />
   <!--<div style="padding-left: 15px;"><button type="button" class="btn btn-primary btn-circle" onClick="history.go(-1);" style="float:left;"><i class="fa  fa-arrow-left"></i></button></div>-->
<center><font style="font-size:14px;"><b>BALANCE SHEET FROM <?php echo getDisplayFormatDate($_SESSION['from_date']); ?> TO <?php echo getDisplayFormatDate($_SESSION['to_date']);?></b></font></center>
<br />
<br />
<div style="padding-left: 15px;">
<INPUT TYPE="button" VALUE="Expand All" onClick="ExpandALL();"    class="btn btn-primary"   style="width:10%;box-shadow: none;" />
<INPUT TYPE="button" VALUE="Collapse All" onClick="CollapseALL();"    class="btn btn-primary"   style="width:10%;box-shadow: none;" />
<?php //if($_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] == 1){?>
<input  type="button" id="btnExport1" value=" Export To Excel"  style="width:150px;box-shadow: none;" class="btn btn-primary"   onclick="ExportToExcel(this.id);"/>
<?php //}?> 
</div>
<br />
<br />
<center>
<table  align="center" width="90%"  style="max-width:90%;border:1px solid black;">
<tr>
	<td style="font-size:14px;">
    	<div style="width:100%;">
        	<div style="border:1px solid gray !important; width:50%;float:left;text-align:center;"><b>Particulars</b>&nbsp;(Liabilities)</div><?php //echo str_repeat("&nbsp;",70);?>
            <div style="border:1px solid gray !important; width:50%;float:left;text-align:center; "> <b>Closing Balance</b>&nbsp;(Rs.)</div>
        </div>
     </td>
     <td style="font-size:14px;">
     <div style="width:100%;">
     	<div style="border:1px solid gray !important;width:50%;float:left;text-align:center; "><b>Particulars</b>&nbsp;(Assets)</div><?php //echo str_repeat("&nbsp;",70);?>
        <div style="border:1px solid gray !important; width:50%;float:left;text-align:center; "> <b>Closing Balance</b>&nbsp;(Rs.)</div>
     </div>
     </td>
<tr>
    <td valign="top" style="border-right:1px solid black;">
    <?php  
        //$first_key = reset($LIABILITYData);
		//var_dump($LIABILITYData);
       echo ('<div class="tree well">'.$objLIABILITY->generateBalanceSheet($LIABILITYData,0,1));
        echo '<ul><li><label><span style="border:none;width:270px;">Profit & Loss A/c</span><span style="border:none;width:50px;text-align:right;"><a href = "IncomeStmt.php" target = "_blank">'.number_format($ProfitnLoss,2).'</a></span></label><ul ><li style="display:none;"><label><span style="border:none;width:270px;font-weight:normal;" class="icon-minus-sign">Opening Balance</span><span style="border:none;width:50px;text-align:right;font-weight:normal;" class="icon-minus-sign">'.number_format($LastYearBalance,2).'</span></label></li></ul><ul ><li style="display:none;"><label><span style="border:none;width:270px;font-weight:normal;">Current Year</span><span style="border:none;width:50px;text-align:right;font-weight:normal;" class="icon-minus-sign">'.number_format($CurrentYearBalance,2).'</span></label></li></ul></li></ul>';
		echo '</div>';
    
    ?>
    
    </td>
    <td valign="top">
        <?php 
        foreach($ASSETData as $key)
        {
            $parent = $ASSETData[$key]['parent_id'];
            break;	
        }
        $first_key = reset($ASSETData);
        echo ('<div class="tree well">'.$objASSET->generateBalanceSheet($ASSETData,0,2));
        echo '</div>';
        ?>
    
    </td>
</tr>
<tr>
	<td style="font-size:14px;">
    	<div style="width:100%;">
        	<div style="border:1px solid gray !important; width:50%;float:left;text-align:center;"><b>Total</b>&nbsp;(Rs.)</div><?php //echo str_repeat("&nbsp;",70);?>
            <div style="border:1px solid gray !important; width:50%;float:left;text-align:center; "> <b><?php echo number_format($LIABILITYTotal, 2);?></b></div>
        </div>
    </td>
    <td style="font-size:14px;">
    	<div style="width:100%;">
        	<div style="border:1px solid gray !important; width:50%;float:left;text-align:center;"><b>Total</b>&nbsp;(Rs.)</div><?php //echo str_repeat("&nbsp;",70);?>
            <div style="border:1px solid gray !important; width:50%;float:left;text-align:center; "> <b><?php echo number_format($ASSETTotal, 2);?></b></div>
        </div>
    </td>
</tr>
<script type="text/javascript">
$(function () {
	$('.tree li:has(ul)').addClass('parent_li').find(' > label ').attr('title', 'Collapse this branch');
    $('.tree li.parent_li > label ').on('click', function (e) {
		 var children = $(this).parent('li.parent_li').find(' > ul > li');
        if (children.is(":visible")) {
            children.hide('fast');
            $(this).attr('title', 'Expand this branch').find(' > span').addClass('icon-plus-sign').removeClass('icon-minus-sign');
        } else {
            children.show('fast');
			
            $(this).attr('title', 'Collapse this branch').find(' > span').addClass('icon-minus-sign').removeClass('icon-plus-sign');
        }
        e.stopPropagation();
    });
});
</script>
</table>
</center>

<br />
<br />
<div style="padding-left: 15px;">
<INPUT TYPE="button" VALUE="Expand All" onClick="ExpandALL();"    class="btn btn-primary"   style="width:10%;box-shadow: none;" />
<INPUT TYPE="button" VALUE="Collapse All" onClick="CollapseALL();"    class="btn btn-primary"   style="width:10%;box-shadow: none;" />
<?php if($_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] == 1){?>
<input  type="button" id="btnExport1" value=" Export To Excel"  style="width:150px;box-shadow: none;" class="btn btn-primary"   onclick="ExportToExcel(this.id);"/>
<?php }?>
<?php if($LIABILITYTotal <> $ASSETTotal)
{?>
<INPUT TYPE="button" VALUE="Registers Validation" onClick="window.open('RegistersValidation.php','RegistersValidationpopup','type=width=700,height=600,scrollbars=yes');"  class="btn btn-primary"    style="box-shadow: none; float:right;margin-right: 9px;">
<INPUT TYPE="button" VALUE="Bank Validation" onClick="window.open('BankEntriesValidation.php','BankEntriesValidationpopup','type=width=700,height=600,scrollbars=yes');"  class="btn btn-primary"    style="box-shadow: none; float:right;margin-right: 2px;">
<?php } ?>
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
            <div style="font-weight:bold; font-size:16px;">BALANCE SHEET FROM <?php echo getDisplayFormatDate($_SESSION['from_date']); ?> TO <?php echo getDisplayFormatDate($_SESSION['to_date']);?></div>
        </div>
<table  align="center"   width="100%" id="Exportdata" class="ExportStyle" style="border:1px dashed gray !important;">
    <tr class="rowstyle">
      <th style="text-align:left;font-size:14px;border:1px dashed gray !important; width:300px;" >Particulars(Liabilities)</th>
      <th style="text-align:center;font-size:14px;border:1px dashed gray !important;width:200px;"  colspan="2">Closing Balance(Rs.)</th>
      <th style="text-align:left;font-size:14px;border:1px dashed gray !important;width:300px;">Particulars(Assets)</th>
      <th style="text-align:center;font-size:14px;border:1px dashed gray !important;width:200px;" colspan="2">Closing Balance(Rs.)</th>
    </tr>
    <tr class="rowstyle">
    	<td colspan="3">
    		<table>
            	
				<?php 
					echo $objLIABILITY->generateBalanceSheetTable($LIABILITYData,0,1);
				?>
            	<tr>
                	<td style="border:1px solid black;"><b>Profit & Loss A/c (Rs.)</b></td>
                    <td style="border:1px solid black;"><b><?php echo number_format($ProfitnLoss,2);?></b></td>
                    <td style="border:1px solid black;"></td>
                </tr>
             </table>
        </td>
       <td colspan="3">
        	<table><?php echo $objASSET->generateBalanceSheetTable($ASSETData,0,2);?></table>
        </td>
    </tr>
    <!--<tr class="rowstyle"><td style="font-size:14px;border:1px dashed gray !important;"><b>Profit & Loss A/c (Rs.)</b></td><td style="text-align:right;font-size:14px;border:1px dashed gray !important;"><b><?php //echo number_format($ProfitnLoss,2);?></b></td><td style="text-align:right;font-size:14px;border:1px dashed gray !important;"></td><td style="font-size:14px;border:1px dashed gray !important;"></td><td style="text-align:right;font-size:14px;border:1px dashed gray !important;"></td><td style="text-align:right;font-size:14px;border:1px dashed gray !important;"></td></tr>-->
    <tr class="rowstyle">
    	<td style="font-size:14px;border:1px dashed gray !important;"><b>TOTAL(Rs.)</b></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"><b><?php echo number_format($LIABILITYTotal, 2);?></b></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"></td>
        <td style="font-size:14px;border:1px dashed gray !important;"><b>TOTAL(Rs.)</b></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"><b><?php echo number_format($ASSETTotal, 2);?></b></td>
        <td style="text-align:right;font-size:14px;border:1px dashed gray !important;"></td>
    </tr>
</table>
</div>
</center>
</div>

<br />
<br />

</div>

<?php include_once "includes/foot.php"; ?>