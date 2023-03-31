<?php include_once("classes/notice.class.php");
	$obj_notice = new notice($m_dbConn);
	$count=$obj_notice->getcount();
	$prevID = "";
	include_once("classes/events_self.class.php");
	$obj_events_self = new events_self($m_dbConn,$m_dbConnRoot);
	$eventscount=$obj_events_self->getcount();
	?>
	
<link href="dist/css/sb-admin-2.css" rel="stylesheet">
<div style="float:right;width:20%;" >
<div class="panel-head" style=" vertical-align:middle;background-color:#F7F7F7;margin-right:2%;margin-top:4%; height:20%;" align="center">
						<div >
                         Quick Links</div>
                         <a href="neft.php"><button type="button" class="btn btn-primary btn-circle" title="Make Payment"><i class="fa  fa-rupee"></i>
                            </button></a>
                            <a href="Complaints.php?cm=0"><button type="button" class="btn btn-danger btn-circle" title="Register a Complaint"><i class="fa  fa-reply"></i>
                            </button></a>
                            <a href="Gallery.php"><button type="button" class="btn btn-warning btn-circle" title="Upload Pictures"><i class="fa  fa- fa-upload  "></i>
                            </button></a>
                           <a href="Ads.php"><button type="button" class="btn btn-success btn-circle" title="Post a Advertisement"><i class="fa  fa-font "></i>
                            </button></a>

                    </div>                    
 <div class="panel panel-info" id="panel_widget" style="margin-top:6%;margin-right:2%;display:none">
 						
                       <!-- <div class="panel-heading">
                         Quick Links
                        </div>
                        <div class="panel-body">
                            <p>
                            <a href="neft.php">Make Payment</a><br>
                            <a href="Complaints.php">Register a Complaint</a><br>
                            <a href="Gallery.php">Upload Pictures</a><br>
                            <a href="Ads.php">Post a Advertisement</a><br></p>
                        </div>-->
                        <div class="panel-heading">
                         <A href="notices.php?in=0">Notices</A>
                        </div>
                        <div class="panel-body">
                            <p>
                        <?php 
						//print_r($count);
						if($count <> "")
						{
							
							foreach($count as $key=>$val)
							{
								//echo "sbfjks";
								//echo $count[$key]['id'];
							$show_notice=$obj_notice->FetchNotices($count[$key]['id']);
							
							if($prevID != $show_notice[0]['id'])
							{
								$prevID = $show_notice[0]['id'];	
                        ?>
                        	
                            <a href="notices.php?in=<?php echo $count[$key]['id'];?>"><?php echo $show_notice[0]['subject'];?></a><br>
                        <?php }
						}
						}?>
                        </p>
                        </div>
                        <div class="panel-heading">
                         <a href="Complaints.php?cm=0">Complaints</a>
                        </div>
                        <div class="panel-body">
                            <p>
                            <a href="Complaints.php?cm=1">Complaint regarding Newspaper</a><br>
                            <a href="Complaints.php?cm=2">Complaint regarding Swimming Pool</a><br>                                                      
                        </div>
                    
                        <div class="panel-heading">
                            <a href="events_view.php">Events</a>
                        </div>
                        <div class="panel-body">
                            <p>
                           <?php 
						//print_r($count);
						if($eventscount <> "")
						{
							
							foreach($eventscount as $key=>$val)
							{
								
							$show_events=$obj_events_self->FetchEvents($eventscount[$key]['events_id']);
							$startDate = date('Y-m-d');
							$days = (strtotime($show_events[0]['events_date']) - strtotime($startDate)) / (60 * 60 * 24);
				
								if($days>=0)
								{
									if($eventscount[$key] != "Event Uploaded")
							{	
                        ?>
                           <!-- <a href="events_view_as_self.php?ev=--><a href="events_view_details.php?id=<?php echo $eventscount[$key]['events_id'];?>"><?php echo $show_events[0]['events_title'];?></a><br>
                        <?php
						}
							else
							{?>							                           
                           <td align="center"><?php echo "<a href='http://way2society.com/Notices/".$eventscount[$key]['Uploaded_file']. "' class='links'>download</a>" ?> </td>
							<?php }
                        		} 
							}
						}?>                                                      
                        </div>
                    </div>                        
                    
</div>