<?php 
include_once("includes/head_s.php");
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/tenant.class.php");
include_once("classes/mem_other_family.class.php");
include_once("classes/include/fetch_data.php");
include_once("classes/utility.class.php");

$obj_Utility =  new utility($m_dbConn);
$objFetchData = new FetchData($m_dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);

$obj_tenant = new tenant($m_dbConn);
//$obj_mem_other_family = new mem_other_family($m_dbConn);

$details = $obj_tenant->MemberList( $_REQUEST['u_id']);
//$unit_details = $obj_mem_other_family->unit_details($_REQUEST['mem_id']);
//print_r($details);
?>
<style>
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
<script type="text/javascript" src="js/tenant.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/ajax_new.js"></script>
<script>
function Expoort()
{
	document.getElementById('societyname').style.display ='block';	
	window.open('data:application/vnd.ms-excel,' + encodeURIComponent( $("#showTable").html()));
	document.getElementById('societyname').style.display ='none';	
}
</script>
<body>
<br/><br/>
<?php
    if($_SESSION['role'] == ROLE_MEMBER || $_SESSION['role'] == ROLE_ADMIN_MEMBER)
    {
        ?>
<!--<div class="panel panel-info" id="panel" style="display:none;"> -->
  <div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">
        <?php
    }
    else
    {
        ?>
        <div class="panel panel-info" id="panel" style="display:none;"> 
        <!-- <div class="panel panel-info" id="panel" style="display:none;margin-top:10px;margin-left:3.5%;width:75%">-->
        <?php
    }
?>
 <div class="panel-heading" id="pageheader">Lease List</div>
   <!-- <br />
   <button type="button" class="btn btn-primary" onClick="window.location.href='tenant.php'">Add New Tenant</button>
	<br />-->
  <div class="panel-body">                        
        <div class="table-responsive">
                    <!-- Nav tabs -->
      <br/>
      <center><div><b> Unit No. : <?php echo $details[0]['unit_no']?></b></div>
</center>      <br/><br/>
           <br />
<br />
<center>
	<form name="show_tenant" id="show_tenant" method="post">
        <table style="border:1px solid #ddd; border-radius:10px;padding:10px;">
            <tr> <td colspan="3"> <br /> </td> </tr>
            <tr align="left">
                <td valign="middle"></td>
                <td valign="middle"><b>Select</b></td>
                <td valign="middle">&nbsp; : &nbsp;</td>
                <td valign="middle"><select id="TenantList" name="TenantList" style="width:8vw;">
                <option value="0">All</option>
                <option value="1">Lease Active </option>
                 <option value="2">Lease Expired </option>
             	 <?php //echo $combo_state = $obj_bill_period->combobox("select YearID,YearDescription from year where status='Y' ORDER BY YearID DESC", DEFAULT_YEAR); ?>   
                  </select>  
                </td>
               
                <td  align="center">                               	                         
                    &nbsp;&nbsp;
                    <input type="button" name="Fetch" id="Fetch" value="Fetch"  class="btn btn-primary"  onclick="FetchTenantList(<?php echo $_SESSION['society_id']?>,<?php echo $details[0]['unit_id'] ?>);" /> 
                 </td>
                <td>
                	
                	<?php 
					
					if($_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] == 1)
					{?>
                    <input  type="button" id="btnExport" value="Export To Excel"   class="btn btn-primary" onclick="Expoort()"  style="display:none;"/>
                	 <?php 
					 }
					 else
					{?>
							<input  type="button" id="btnExport" value="Export To Excel"   class="btn btn-primary" onclick="Expoort()" style="display:none;visibility:hidden;"/>
					<?php 
					}
					?>	
                </td>
                <td>
                <?php if($_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE] == 1){?>
                 <input  type="button" id="Print" onClick="PrintPage()" name="Print!" value="Print/Export To Pdf" class="btn btn-primary" style="display:none;"/>
                  <?php }
					else
					{?>
						  <input  type="button" id="Print" onClick="PrintPage()" name="Print!" value="Print/Export To Pdf" class="btn btn-primary" style="display:none;visibility:hidden;"/>
					<?php }?>	
                </td>
              
           </tr>
           <tr><td colspan="6"><br /></td></tr>
          </table>
           <input type="text" style="visibility:hidden" name="AllowExport" id="AllowExport" value="<?php echo $_SESSION['feature'][CLIENT_FEATURE_EXPORT_MODULE]; ?>" />
       </form>

</center>
<div id='showTable' style="font-weight:lighter;">
</div>
</div>
</div>
</div>
<?php include_once "includes/foot.php"; ?>