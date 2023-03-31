<?php
include_once "ses_set_s.php"; 
include_once("includes/head_s.php");  
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include_once "classes/dbconst.class.php";
include_once("classes/include/fetch_data.php");
include_once("classes/genbill.class.php");
include_once("classes/utility.class.php");
$obj_Utility =  new utility($dbConn);
$obj_genbill = new genbill($dbConn);
$objFetchData = new FetchData($dbConn);
	$objFetchData->GetSocietyDetails($_SESSION['society_id']);
$memberIDS = $obj_Utility->getMemberIDs($_SESSION['default_year_end_date']);	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>I - Register</title>

<style>
select.dropdown {
    position: relative;
    width: 100px;
    margin: 0 auto;
    padding: 10px 10px 10px 30px;
	appearance:button;
		

    /* Styles */
    background: #fff;
    border: 1px solid silver;
   /* cursor: pointer;*/
    outline: none;
	
}

@media print
	{    
		.no-print, .no-print *
		{
			display: none !important;
		}
		
		 div.tr, div.td , div.th 
		 {
			page-break-inside: avoid;
		}
</style>
<script type="text/javascript" src="js/jsContributionLedgerDetailed.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/ajax_new.js"></script>
    
    <script>
	jQuery.expr[':'].Contains = function(a, i, m) {
    return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
};

function ShowSearchElement()
{
		document.getElementById('msgDiv').style.display = 'none';
   		var w =  $('#searchbox').val();
        if (w)
		 {
				if($('#unit_no li:Contains('+w+')').length == 0)
				{
					$('#unit_no li').hide();
					document.getElementById('msgDiv').style.display = 'block';
					document.getElementById('msgDiv').innerHTML = '<font style="color:#F00;"><b>No Match Found...</b></font> ';
				}
				else
				{
					$('#unit_no li').hide();
					$('#unit_no li:Contains('+w+')').show();	
				}
		} 
		else 
		{
			 $('#unit_no li').show();                  
        }
}

function uncheckDefaultCheckBox(id)
{
	if(document.getElementById(id).checked  == true)
	{
		document.getElementsByClassName('chekAll')[0].checked = false;
	}
	else
	{
		var count = 0;
		var checks = document.getElementsByClassName('checkBox');
		checks.forEach(function(val, index, ar) {
			if(ar[index].checked) 
			{
				count++;
			}
		});
		if(count == 0)
		{
			document.getElementsByClassName('chekAll')[0].checked = true;			
		}
		else
		{
			document.getElementsByClassName('chekAll')[0].checked = false;
		}
	}
	
}

function uncheckothers(id)
{
	var checks = document.getElementsByClassName('checkBox');
	checks.forEach(function(val, index, ar) {
		ar[index].checked = false;
	});
}
	</script>
<script>
function Expoort()
{
	//$("#btnExport").click(function(e) {
		document.getElementById('societyname').style.display ='block';	
	  window.open('data:application/vnd.ms-excel,' + encodeURIComponent( $("#showTable").html()));
	  //e.preventDefault();
	  document.getElementById('societyname').style.display ='none';	
			 
	//});  
}
</script>
</head>

<body>
<br/>
<div class="panel panel-info" id="panel" style="display:none;">
    <div class="panel-heading" id="pageheader">I - Register</div>
<br />
<br />
	<form name="accounting_report" id="accounting_report" method="post" action="i_register.php" target="_blank">
        <center>
        <table> <!--style="border:1px solid; border-color:#000"-->
        	<tr>
            	<th style="font-size:14px; border:1px solid; border-color:#000;"><center>Flat No.</center></th>
                <th style="font-size:14px; border:1px solid; border-color:#000; border-left:none;"><center>Owner Name</center></th>
            </tr>
            <?php 
			$sql01 = "select unit.unit_no, unit.unit_id, member_main.owner_name from `unit` JOIN `member_main` on unit.unit_id = member_main.unit where unit.society_id = '" . $_SESSION['society_id'] . "' and  member_main.member_id IN ($memberIDS) ORDER BY unit.sort_order ASC";
			$sql11 = $m_dbConn->select($sql01);
			
			for($z=0;$z<sizeof($sql11);$z++)
			{
				?>
                <tr>
                	<td style="font-size:12px; border:1px solid; border-color:#000; border-top:none"><center><?php echo $sql11[$z]['unit_no']; ?></center></td>
                    <td style="font-size:12px; border:1px solid; border-color:#000; border-left:none; border-top:none"><a href="i_register.php?i-reg-checkbox=<?php echo $sql11[$z]['unit_id']; ?>" target="_blank"><?php echo $sql11[$z]['owner_name']; ?></a></td>
                </tr>
                <?php
			}
			?>
            
<!--            <tr> <td colspan="3"> <br /> </td> </tr>
            <tr align="left">
                <td valign="middle"></td>
                <td><b>Unit No</b></td>
                <td>&nbsp; : &nbsp;</td>
                
                <td>
                 <div class="input-group input-group-unstyled" style="width:355px; ">
    				<input type="text" class="form-control" style="width:355px; height:30px;"  id="searchbox" placeholder="Search Unit No Or Member Name"   onChange="ShowSearchElement();"  onKeyUp="ShowSearchElement();" />
    			</div>
            	<div style="overflow-y:scroll;overflow-x:hidden;width:355px; height:150px; border:solid #CCCCCC 2px;" name="unit_no[]" id="unit_no" >
                	<p id="msgDiv" style="display:none;"></p>
                	<?php //echo $combo_unit = $obj_genbill->comboboxForLedgerReport("select unit.unit_id, CONCAT_WS(' - ',unit.unit_no,member_main.owner_name) as name from `unit` JOIN `member_main` on unit.unit_id = member_main.unit where unit.society_id = '" . $_SESSION['society_id'] . "'    and  member_main.member_id IN (SELECT `member_id` FROM (select  `member_id` from `member_main` where ownership_date <= '" .$_SESSION['default_year_end_date']. "'  ORDER BY ownership_date desc) as member_id Group BY unit) Group BY unit.unit_id ORDER BY unit.sort_order ASC",0,'All','0');
					//echo $sql = "select unit.unit_id, CONCAT_WS(' - ',unit.unit_no,member_main.owner_name) as name from `unit` JOIN `member_main` on unit.unit_id = member_main.unit where unit.society_id = '" . $_SESSION['society_id'] . "' and  member_main.member_id IN ($memberIDS) ORDER BY unit.sort_order ASC";
					//echo $combo_unit = $obj_genbill->comboboxForIRegister($sql,0,'All','0');?>
				</div>
            </td>
                <td  align="center">                               	                         
                    &nbsp;&nbsp;
                    <input type="submit" name="Fetch" id="Fetch" value="Fetch"  class="btn btn-primary"  /> 
                 </td>
              
           </tr>
           <tr><td colspan="6"><br /></td></tr>
-->        </table>
		</center>
                <input type="text" style="visibility:hidden" name="AllowExport" id="AllowExport" value="<?php echo $_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE]; ?>" />

     </form>


<div id='showTable' style="font-weight:lighter;">
</div>



</div>
<?php include_once "includes/foot.php"; ?>