<?php include_once("includes/head_s.php");
include("classes/agent_form.class.php");
include("classes/daily_collection.class.php");
include_once ("classes/dbconst.class.php");
error_reporting(0);
$obj_agent_form = new agent_form($m_dbConn);
$obj_daily_collection = new dailycollection($m_dbConn);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <script type="text/javascript" src="js/jsDailycollection.js"></script>

        <title>Daily Collection</title>
        <script type="text/javascript">
        $(function()
        {
            $.datepicker.setDefaults($.datepicker.regional['']);
            $(".basics").datepicker({ 
            dateFormat: "dd-mm-yy", 
         	showOn: "both", 
            buttonImage: "images/calendar.gif", 
            buttonImageOnly: true, 
			yearRange: '-0:+10', // Range of years to display in drop-down,
        })
	});
</script>
    </head>
    <body>
        <div id="middle">
            <div class="panel panel-info">
                <div class="panel-heading" id="pageheader">
                 Daily Collection               
                 </div>
            	<center>
                        <table>                       
                        	</br></br>
                            <tr>
                            	<td valign="middle"> Agent Name </td>
                                <td valign="middle">&nbsp;&nbsp;:&nbsp;&nbsp;</td>
                                <td valign="middle">
                                	<select id="agent_id" class="agent_id">
                                    <?php echo $obj_agent_form->combobox("select `agent_id`, `agent_name` from `pp_agent`",$_REQUEST['agent_id'],true); ?>
                                    </select>
                                </td>
                                <td></td>
                                <td valign="middle"> Date </td>
                                <td valign="middle">&nbsp;&nbsp;:&nbsp;&nbsp;</td>
		                        <td valign="middle"> <input type="text"  name="datepicker" id="datepicker" class="basics" value="<?=$_REQUEST['date']?>" readonly style="width:80px;"></td>
                                <td valign="middle"> <td><button id="fetch_report_btn" class="fetch_report_btn  btn-primary btn">Fetch Daily Collection</button></td>
                            </tr>
                        </table>
                 
                <div style="height:50px;"></div>
                <div id="error-msgs" class="Print_JOBCARD_MSG text-danger"></div>
                <div id="loader_img"></div>                
                <div id='showTable' style="font-weight:lighter;">                
                </div>
                </center> 
            </div>            
        </div>		
        
<?php include_once "includes/foot.php";?>

<script>
      
function go_error()
{
    setTimeout('hide_error()',3000);	
}
        
function hide_error()
{
    $(".Print_JOBCARD_MSG").html('');
}		    
		
$(document).ready(function()
{       
        
$(document).on('click','#fetch_report_btn', function()
{                
    var agent_id = $('#agent_id').val();
    var date = $('#datepicker').val();
                
    if(agent_id == 0)
    {
        $(".Print_JOBCARD_MSG").html('Please Select agent name');
        go_error();
        return false;
    }else if (date == 0)
    {
        $(".Print_JOBCARD_MSG").html('Please select date');
        go_error();
        return false;
    }               
    $.ajax({
            url:'ajax/ajaxDailyCollection.php',
            type:"POST",
            cache:false,
            data:{'method':'FetchDailyCollectionReport','agent_id':agent_id, 'date':date},
            success:function(data)
            {
                var result = data.split('@@@');
                $('#showTable').html('');
                var CollectionDetails = JSON.parse("["+result[1]+"]");
                var previousCollectionDetailsList = CollectionDetails[0]['default'];
                var ExistingCollectionDetailsList = CollectionDetails[0]['existing'];

                var table = "<br><br><table id='example' style='text-align:left; width:100%; border-collapse: collapse' class='table table-bordered table-hover table-striped table-display' cellpadding='50'>";
                  
                table +="<thead></tr>";                        
                    table +="<th style='border:1px solid #ddd; text-align:center;'>Sr. No</th>";
                    table +="<th style='border:1px solid #ddd; text-align:center;'>Member Name</th>";
                    table +="<th style='border:1px solid #ddd; text-align:center;'>Daily Ledger Name</th>";
                    table +="<th style='border:1px solid #ddd; text-align:center;'>Amount</th>";
                    table +="<th style='border:1px solid #ddd; text-align:center;'>Status</th>";
                    table +="<th style='border:1px solid #ddd; text-align:center;'>Remark</th>";
                    table +="<th style='border:1px solid #ddd; text-align:center;'>Update</th>";
                    table +="<th style='border:1px solid #ddd; text-align:center;'>Actions</th>";
                table +="</tr></thead><tbody>";
                   var srno=0;
                    for(var i = 0; i < previousCollectionDetailsList.length; i++)
                    {
                        srno++;
                        table += "<tr style='border:1px solid #ddd;'>";

                        var member_name =  previousCollectionDetailsList[i].owner_name;
                        var member_id =  previousCollectionDetailsList[i].member_id;
                        var ledger_name =  previousCollectionDetailsList[i].ledger_name;
                        var ledger_id =  previousCollectionDetailsList[i].ledgerid;
                        var amount =  previousCollectionDetailsList[i].amount; 

                        var status = "";
                        var remark = "";
                        var disabled ="";
                        console.log(ExistingCollectionDetailsList,ExistingCollectionDetailsList[member_id], ledger_id);
                        if(ExistingCollectionDetailsList[member_id] != undefined){
                            var currentData = ExistingCollectionDetailsList[member_id][ledger_id];
                        }
                        
                        if(ExistingCollectionDetailsList.length != 0 && previousCollectionDetailsList != 0 && currentData !== undefined){
                            
                            status = (currentData.status) ? "checked":"";                         
                            remark = (currentData.remark);
                            disabled ="disabled";  
                        }                  
            
                        table +="<input type='hidden' id='memberid_"+i+"' value="+member_id+">";
                        table +="<input type='hidden' id='leadgerid_"+i+"' value="+ledger_id+">";                        
                        table +="<td style='border:1px solid #ddd; text-align:center;'>"+ srno +"</td>";
                        table +="<td style='border:1px solid #ddd; text-align:center;' id='membername_"+i+"'>"+ member_name +"</td>";
                        table +="<td style='border:1px solid #ddd; text-align:center;' id='ledgername_"+i+"'>"+ ledger_name +"</td>"; 
                        table +="<td style='border:1px solid #ddd; text-align:center;' id='amount_"+i+"'>"+amount+"</td>";
                        table +="<td style='border:1px solid #ddd; text-align:center;'>"+ "<input type='checkbox' "+disabled+" id='status_"+i+"' "+status+"></td>";
                        table +="<td style='border:1px solid #ddd; text-align:center;'>"+ "<input type='text' id='remark_"+i+"' "+disabled+" value="+remark+"></td>";
                        table +="<td style='border:1px solid #ddd; text-align:center;'>"+ "<input type='button' name='insert' "+disabled+" id='insert_"+i+"' value='Accept' onclick = 'updateRecord("+i+","+agent_id+")'; class='btn btn-primary' style='color:#FFF; width:80px;background-color:#337ab7;'?>"+"</td>";
                        table +="<td style='border:1px solid #ddd; text-align:center;'>"+ "<a onclick='' id='print'><img src='images/print.png' border='0' alt='Print' style='cursor:pointer; width:40%;'>"+"</a>"+"</td>";	                         
                        table +="</tr>";
                    }                	
                        table += "</tbody></table>";
                        $('#showTable').html(table);
                        $('#example').DataTable();
                }
                });	
        });

        <?php if(isset($_REQUEST['date']) && $_REQUEST['agent_id'] <> '')
        { ?>
        $("#fetch_report_btn").click();  
        <?php 

        }?> 
    });
    </script>		
    </body>    
</html>

