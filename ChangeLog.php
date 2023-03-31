<?php
include_once("includes/head_s.php");
include_once ("check_default.php");
include_once("classes/changelog.class.php");
$obj_changeLog = new changeLog($m_dbConn);


?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
	<script type="text/javascript" src="js/ajax.js"></script>
    <script type="text/javascript" src="js/jsChangeLog.js"></script>
    <script type="text/javascript" src="js/populateData.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }
	
	</script>
    
<style>
.desc{ text-align:left;}
</style>    
</head>

<body>
<br>
<div id="middle">
<div class="panel panel-default">
<div class="panel-heading" id="pageheader">Change Log History</div>
<center><br>
<form name="ChengeLog" id="ChengeLog" method="post" action="">
<input type="hidden" name="ssid" value="<?php echo $_GET['ssid'];?>">
<table width="80%" style="border:1px solid black; background-color:transparent;border-collapse:collapse; padding:30px;">
<tr> <td colspan="4"><br/> </td></tr>
<tr>
	<td style="text-align:left;width:10%;">&nbsp; Changed By: &nbsp;<select name="ChangedBy" id="ChangedBy" style="width:150px;" ><?php echo $ChangedBY = $obj_changeLog->comboboxEx("SELECT DISTINCT logintbl.login_id,logintbl.name FROM `change_log` as chnglogtbl JOIN `login` as logintbl on chnglogtbl.ChangedBy=logintbl.login_id",0);?></select>
    </td>
    <td style="text-align:left;">
    Changed Table:<select name="ChangedTableName" id="ChangedTableName" style="width:150px;" ><?php echo $ChangedTableName = $obj_changeLog->comboboxEx("SELECT ChangeLogID,ChangedTable FROM `change_log` group by ChangedTable",0);//echo $ChangedTableName = $obj_changeLog->comboboxEx("SELECT DISTINCT ChangedTable,ChangedTable as Table1 FROM `change_log`",0);?></select>
    </td>
</tr>
<tr> <td colspan="4"><br/> </td></tr>
<tr>
	<td style=" text-align:left;width:40%;"> &nbsp; TimeStamp: &nbsp; 
    	 <select name="ChangeTSFrom" id="ChangeTSFrom" style="width:150px;" ><?php echo $ChangeTSFrom = $obj_changeLog->comboboxEx("SELECT ChangeLogID,ChangeTS FROM `change_log`",0);?></select>
       &nbsp;&nbsp;To &nbsp;&nbsp;<select name="ChangeTSTo" id="ChangeTSTo" style="width:150px;" ><?php echo $ChangeTSTo = $obj_changeLog->comboboxEx("SELECT ChangeLogID,ChangeTS FROM `change_log`",0);?></select>
    </td>
    <td style=" text-align:left;width:40%;"><input type="button" value="Apply Filter"  class="btn btn-primary" id="submit" name="submit" onClick="SubmitForm();"></td>
    
</tr>
<tr> <td colspan="4"><br/> </td></tr>
<!--<tr align="center"><td><input type="button" value="Apply Filter" id="submit" name="submit" onClick="SubmitForm();" style=" width:100px; height:30px; background-color:#BDD8F4;"></td></tr>-->

</table>    
    
</form>
<br>
<div id="FilterData">   
<?php $res = $obj_changeLog->pgnation();?>
</div> 
<script>document.getElementById('example').style.width='90%';</script>    
</center>
</div>
</div>
<?php include_once "includes/foot.php"; ?>
<script>
$(document).ready(function() {
    $('#example').dataTable().fnDestroy();
			$('#example').DataTable( {
				dom: 'T<"clear">lfrtip',
				"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
				aaSorting : [],
				 "aoColumns": [
				{ "width": "12%","sClass" : "desc"},
				{ "width": "10%","sClass" : "desc"},
				{ "width": "20%","sClass" : "desc"},
				{ "width": "10%","sClass" : "desc" },
				{ "width": "10%"}
			  ]
				 
			} );
	
    } );
</script>
