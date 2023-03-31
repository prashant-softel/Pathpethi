<?php
	include_once("classes/genbill.class.php");
	include_once("classes/include/dbop.class.php");
	include_once ("classes/include/exportToExcel.php");
	include_once ("classes/utility.class.php");

	$dbConn = new dbop();
	$obj_genbill = new genbill($dbConn);
	$get_period="";
	
	if(isset($_REQUEST['period_id']))
	{
		$get_period = $obj_genbill->getPeriod($_REQUEST['period_id']);
			//print_r($get_period);
			//echo $get_period;
	}
	
	if(isset($_REQUEST['getnote']))
	{
		$get_notes = $obj_genbill->getNotes($_REQUEST['society'], $_REQUEST['period']);
	}
	else if(isset($_REQUEST['getdate']))
	{
		$get_dates = $obj_genbill->getBillAndDueDate($_REQUEST['period'], $_REQUEST['society']);
		echo $get_dates['BillDate'] . "@@@" . $get_dates['DueDate'];
	}
	else if(isset($_REQUEST['Export']))
	{
		$details = $obj_genbill->getCollectionOfDataToDisplay_optimize($_REQUEST['society_id'], $_REQUEST['wing_id'], $_REQUEST['unit_id'], $_REQUEST['period_id'], false, 
		$_REQUEST["supplementary_bill"]);			
		displayResults($details,$get_period, $_REQUEST["supplementary_bill"], $dbConn);
	}
	
	function displayResults($details,$period, $SuppBill, $dbConn)
	{
		
		//print_r($details[0]);
		$flag = false;
		$dataCount = 0;
		
		$objUtility =  new utility($dbConn);
		$societyInfo = $objUtility->GetSocietyInformation($_SESSION['society_id']);
		//print_r($societyInfo);
		$SocietyName = $societyInfo['society_name'];
			
		if($SuppBill)
		{
			echo "<div id='Exportdiv'><center><b>
			<h1><font>" . $SocietyName . "</font></h1>
			<h2><font> Supplimentary Bill Register For " .$period. "</font></h2>
			</center><br>";	
		}
		else
		{
			echo "<div id='Exportdiv'><center><b>
			<h1><font>" . $SocietyName . "</font></h1>
			<h2><font> Maintenence Bill Register For " .$period. "</font></h2>
			</center><br>";	
		}
		echo '<br><table style="border:1px solid black;text-align:center;">';
		foreach ($details as $row)
		{
			//echo " inif: ";
			//print_r($row);
			
			if(!$flag) 
			{
				//echo " inif: ";
				//print_r($row);
				//echo "\n"; 
				//echo '<thead>';
				echo '<tr style="border:1px solid black;">';
				echo implode('<td style="border:1px solid black;">', array_keys($row)) . "\n";
				$flag = true;
				echo '</tr>';
				//echo '</thead>';
				//echo '<tbody>';
				echo '<tr style="border:1px solid black;">';
				echo implode('<td style="border:1px solid black;">', array_values($row)) . "\n";
				echo '</tr>';
				
			}
			//array_walk($row, 'cleanData');
			else
			{
				echo '<tr style="border:1px solid black;">';
			echo implode('<td style="border:1px solid black;">', array_values($row)) . "\n";
			echo '</tr>';
			}
		}
		echo '</table></div>';
	}
	
	/*function cleanData(&$str)
	{		
		$str = preg_replace("/\t/", "\\t", $str); $str = preg_replace("/\r?\n/", "\\n", $str);
	}
	
	function exportExcel($result)
	{		
		# file name for download
		$filename = "RES.xls";
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");
		//header("Content-Type: text/plain");
		$flag = false;
		$dataCount = 0;
		foreach ($result as $row)
		{
		if(!$flag) 
		{
		echo implode("\t", array_keys($row)) . "\n";
			$flag = true;
		}
		array_walk($row, 'cleanData');
		echo implode("\t", array_values($row)) . "\n";
		}
		exit;
	}*/
?>
<script type="text/javascript" language="javascript" src="media/js/jquery.js"></script>
<script>
window.open('data:application/vnd.ms-excel,' + encodeURIComponent( $("#Exportdiv").html()));
    e.preventDefault();
</script>