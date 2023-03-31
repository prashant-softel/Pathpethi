
<?php 
if(!isset($_SESSION)){ session_start(); }
set_time_limit(0);
include_once("classes/include/check_session.php");
?>
<script type="text/javascript" src="js/jsReportOptions.js"></script>
<script type="text/javascript" language="javascript" src="media/js/jquery.js"></script>

<style>	
	@media print
	{    
		.no-print, .no-print *
		{
			display: none !important;
		}
	}
</style>

<?php if($_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] == 1){?>

<div align="center" style="alignment-adjust:middle; left:80px;">
<input  type="button" id="Print" onClick="PrintPage()" name="Print!" value="Print" style="width:100px; height:30px;" class="no-print  btn-report"/>	
<input  type="button" id="btnExport" value=" Export To Excel"   onClick="exportToExcel()" style="width:150px; height:30px;" class="no-print  btn-report" />
<input  type="button" id="btnExportPdf" value=" Export To Pdf"   onClick="ViewPDF()" style="width:150px; height:30px;"  class="no-print  btn-report"/> 
</div>
<br/>
<form  action="common_pdf.php" method="post" id="myForm" class="no-print">
	 <input type="hidden" name="data"  id="data"/>
     <input type="hidden" id="landscape" name="landscape" value="0">
</form>
<iframe name="my-iframe" height="0"  width="0" style="visibility:hidden;"  class="no-print"></iframe>
<?php } ?>