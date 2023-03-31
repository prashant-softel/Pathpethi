<?php if(!isset($_SESSION)){ session_start(); } ?>
<?php
include_once("includes/head_s.php");
include_once("classes/import_report.class.php");
$obj_import_report=new import_report($m_dbConn);

?>


<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Import Report</title>
<center><h3>Society Import Report</h3></center>
<center><font color="#FF0000"><h4>Click on Go to set defaults</h4></font></center>

<link rel="stylesheet" type="text/css" href="css/pagination.css" >
<script type="text/javascript" src="javascript/jquery-1.2.6.pack.js"></script>
</head>

<body>
<center>
<table align="center" width="100%">
<tr>
<td>
<?php

echo "<br>";
$str1 = $obj_import_report->pgnation();
echo "<br>";
echo $str = $obj_import_report->display1($str1);
echo "<br>";
$str1 = $obj_import_report->pgnation();
echo "<br>";
?>
</td>
</tr>
</table>
</center>
</body>

</html>
<?php include_once "includes/foot.php"; ?>