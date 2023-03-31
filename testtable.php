<?php include_once "ses_set_s.php"; ?>
<?php
	include_once("classes/home_s.class.php");
	include_once("classes/dbconst.class.php");
	$obj_AdminPanel = new CAdminPanel($m_dbConn);
?>

<?php
	$ary = array("a"=>"a1", "b"=>"b1");
	
	echo implode(',', $ary);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style>
	.main_block{
			width:33%;
			border:0px solid #000;
			text-align:center;
			vertical-align:top;
			border-radius:15px;
			height:175px;
		}
	.main_div{
			background-color:#0099CC;
			border-radius:15px;
			width:80%;
			border:2px solid #333;
			margin:auto;
			min-height:100%;
			height:175px;
		}
	.main_head{
			border-top-left-radius:15px;
			border-top-right-radius:15px;
			color:#FFF;
			font-size:20px;
			font-weight:bold;
			padding:3px;
			padding-right:10px;
			text-align:right;
			height:10px;
			text-decoration:underline;
		}
	.main_data{
			background:none;
			color:#000;
			font-size:14px;
			text-align:center;
			height:81px;
		}
	.main_footer{
			background:#DFDFDF;
			border-bottom-left-radius:12px;
			border-bottom-right-radius:12px;
			color:#00F;
			font-size:18px;
			font-weight:bolder;
			text-align:center;
			height:30px;
			display:table;
			width:100%;
		}
		table, td{
			width:33%;
		}
</style>
</head>
<body>
    <table style="width:100%;">
	<tr>
    	<td>
        	<div class="main_div">
            	<div class="main_head"><img src="images/bank_cash.png" style="width:50px;height:50px;float:left;"/>Bank & Cash</div>
                <br><br>
                <div class="main_data">
				<?php
  						$arBankDetails = $obj_AdminPanel->GetBankAccountAndBalance(3);
   						if($arBankDetails <> '')
					   	{
						   	?>
						   <table style="width:100%;">
                           <?php
						   foreach($arBankDetails as $arData=>$arvalue)
						   {
							   $len = strlen($obj_AdminPanel->GetLedgerNameFromID($arvalue["LedgerID"]));
							   
							   $BankName =  ($len > 15) ? (substr($obj_AdminPanel->GetLedgerNameFromID($arvalue["LedgerID"]), 0, 15) . '...') : $obj_AdminPanel->GetLedgerNameFromID($arvalue["LedgerID"]);
							   
							   $receipts =$arvalue["receipts"];
							   $payments = $arvalue["payments"]; 
							   $BalAmount = $receipts - $payments;
							   ?>
                               <tr><td style="width:60%;text-align:left;"><?php  echo $BankName ?></td><td style="width:5%;">:</td><td style="font-weight:bold; width:35%; text-align:right;"><?php  echo number_format($BalAmount,2); ?></td></tr>
                               <?php
							   //echo $BankName." : <b>" .number_format($BalAmount,2) . '</b><br>';
							}
							?>
                            <!--<tr><td colspan="3" style="text-align:center;"><br><b><a href='BankAccountDetails.php'>Details</a></b></td></tr>-->
                            </table>
                            <?php
					   }
                	?>
                </div>
                <div class="main_footer">
                	<span style="display:table-cell;vertical-align: middle;"><a href='BankAccountDetails.php'>View Details</a></span>
                </div>
            </div>
        </td>
        <td>
        	<div class="main_div">
            	<div class="main_head"><img src="images/bank_cash.png" style="width:50px;height:50px;float:left;"/>Income</div>
                <br><br>
                <div class="main_data">
                <?php
                   $arBankDetails = $obj_AdminPanel->GetTotalIncome();
				   if($arBankDetails <> '')
				   {
					   	?>
                        <table style="width:100%;">
                        <?php
						$iCounter = 1;
						foreach($arBankDetails as $arData=>$arvalue)
						{
							$month = $arvalue["date"];
							$receipts =$arvalue["receipts"];
							$payments = $arvalue["payments"]; 
							$BalAmount = $receipts - $payments;
							//echo "<tr align=right><td>".$month.":</td><td align=right><b>".number_format($BalAmount,2);"</b></td></tr>";
							?>
                               <tr><td style="width:60%;text-align:left;"><?php  echo $month ?></td><td style="width:5%;">:</td><td style="font-weight:bold; width:35%; text-align:right;"><?php  echo number_format($BalAmount,2); ?></td></tr>
                            <?php
							
							if($iCounter >= 3)
							{
								break;
							}
							$iCounter++;
						}
						   
                           //echo "<tr><td colspan='3' style='text-align:center;'><br><b><a href='IncomeDetails.php'>Details</a></b></td></tr>";
                            echo "</table>";
				   }
                  ?>	
                </div>
                <div class="main_footer">
                	<span style="display:table-cell;vertical-align: middle;"><a href='IncomeDetails.php'>View Details</a></span>
                </div>
            </div>
        </td><td>
        	<div class="main_div">
            	<div class="main_head"><img src="images/bank_cash.png" style="width:50px;height:50px;float:left;"/>Expense</div>
                <br><br>
                <div class="main_data">
                	
                </div>
                <div class="main_footer">
                	<span style="display:table-cell;vertical-align: middle;">View Details</span>
                </div>
            </div>
        </td>
        </tr>
        </table>
</body>
</html>