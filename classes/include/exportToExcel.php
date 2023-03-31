<?php
	function cleanData(&$str)
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
	}


?>