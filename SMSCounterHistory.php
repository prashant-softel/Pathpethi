<?php include_once("includes/head_s.php");
	include_once("classes/include/display_table.class.php");
	$display_pg=new display_table($m_dbConn);
	include_once("classes/utility.class.php");
?>	
			
	<div class="panel panel-info" id="panel" style="display:none;">
    	<div class="panel-heading" id="pageheader">SMS Counter History Report</div>  
			<?php 
                $sqlQuery = "SELECT BillType,DATE_FORMAT(`SentBillSMSDate`, '%Y-%m-%d') AS BillSMSDate, DATE_FORMAT(`SentSMSReminderDate`, '%Y-%m-%d') AS ReminderSMSDate, DATE_FORMAT(`SentBillEmailDate`, '%Y-%m-%d') AS BillEmailDate, COUNT(*) AS tCount FROM `notification` GROUP BY DATE_FORMAT(`SentBillSMSDate`, '%Y-%m-%d') ASC,DATE_FORMAT(`SentSMSReminderDate`, '%Y-%m-%d'),DATE_FORMAT(`SentBillEmailDate`, '%Y-%m-%d'),BillType  ORDER BY DATE_FORMAT(`SentBillSMSDate`, '%Y-%m-%d') ASC,DATE_FORMAT(`SentSMSReminderDate`, '%Y-%m-%d') ASC,DATE_FORMAT(`SentBillEmailDate`, '%Y-%m-%d') ASC";       
                $result1 = $m_dbConn->select($sqlQuery);	
				
                $sqlQuery2 = "SELECT DATE_FORMAT(`SentGeneralSMSDate`, '%Y-%m-%d') AS GeneralSMSDate, COUNT(*) AS tCount FROM `generalsms_log` GROUP BY DATE_FORMAT(`SentGeneralSMSDate`, '%Y-%m-%d') ORDER BY DATE_FORMAT(`SentGeneralSMSDate`, '%Y-%m-%d') ASC ";
                $result2 = $m_dbConn->select($sqlQuery2);
                $result = array();
                if(is_array($result1) & !empty($result1))
                {
                    $result = array_merge($result,$result1);
                }
                if(is_array($result2) & !empty($result2))
                {
                    $result = array_merge($result,$result2);
                }

                $counterArr = array();
                for($i = 0; $i < sizeof($result); $i++)
                {
					
					if(isset($result[$i]['BillType']) && $result[$i]['BillType'] == 0)
				   {
					  
						 $counterArr[$result[$i]['BillSMSDate']]['BillSMSDate'] = $result[$i]['tCount'];
						 $counterArr[$result[$i]['ReminderSMSDate']]['ReminderSMSDate'] = $result[$i]['tCount'];
						 $counterArr[$result[$i]['BillEmailDate']]['BillEmailDate'] = $result[$i]['tCount'];
				   }
				   else if(isset($result[$i]['BillType']) && $result[$i]['BillType'] == 1)
				   {
						$counterArr[$result[$i]['BillSMSDate']]['BillSMSDate_Supp'] = $result[$i]['tCount'];
						$counterArr[$result[$i]['ReminderSMSDate']]['ReminderSMSDate_Supp'] = $result[$i]['tCount'];
						$counterArr[$result[$i]['BillEmailDate']]['BillEmailDate_Supp'] = $result[$i]['tCount'];
				   }
				  $counterArr[$result[$i]['GeneralSMSDate']]['GeneralSMSDate'] = $result[$i]['tCount'];
                }
				krsort($counterArr);
			?> 
            <br/><br />
            <center>
                <table style="width:95%; background-color:transparent;" class="table table-bordered table-hover table-striped" cellpadding="50">
                	<thead>
                        <tr align="center">
                            <th style="text-align:center;">Date</th>
                            <th style="text-align:center;">General SMS Counter</th>
                            <th colspan="2"  width="auto" style="text-align:center;">Bill SMS Counter</th>
                            <th colspan="2" width="auto" style="text-align:center;">Reminder SMS Counter</th>
                            <th colspan="2" width="auto" style="text-align:center;">Bill Email Counter</th>
                         </tr>
                	</thead>
                    
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th style="text-align:center;">Regular</th>
                            <th style="text-align:center;">Supplementry</th>
                            <th style="text-align:center;">Regular</th>
                            <th style="text-align:center;">Supplementry</th>
                            <th style="text-align:center;">Regular</th>
                            <th style="text-align:center;">Supplementry</th>
                        </tr>
                	</thead>
                    <tbody>
                    <?php 
						foreach($counterArr  as $key=>$value)
						{	
							if($key == 0)
							{
								continue;
							}
						?>
                    <tr>
                    	<td><?php echo getDisplayFormatDate($key); ?></td>
                        <td><?php echo $counterArr[$key]['GeneralSMSDate']; ?></td>
                        <td><?php echo $counterArr[$key]['BillSMSDate']; ?></td>
                         <td><?php echo $counterArr[$key]['BillSMSDate_Supp']; ?></td>
                        <td><?php echo $counterArr[$key]['ReminderSMSDate']; ?></td>
                        <td><?php echo $counterArr[$key]['ReminderSMSDate_Supp']; ?></td>
						 <td><?php echo $counterArr[$key]['BillEmailDate']; ?></td>
                         <td><?php echo $counterArr[$key]['BillEmailDate_Supp']; ?></td>
                    </tr>
                    <?php
						}						
					?>
                    </tbody>                        
                </table>   
          	</center>        
<?php include_once "includes/foot.php"; ?>             