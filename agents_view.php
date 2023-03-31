<?php include_once("includes/head_s.php");?>
<?php 
//echo "call";
include("classes/agent_form.class.php");
include_once ("classes/dbconst.class.php");

//echo "Test1";
$obj_agent_form = new agent_form($m_dbConn);
$agent_data = $obj_agent_form->getAgentData();
//var_dump($agent_data);
//echo "Test2";
//error_reporting(1);
?>
<html>
<head>
<title> Agent Master Form </title>
</head>

<body>
<div class="panel panel-info" style="margin-top:6%;margin-left:3.5%;width:80%"> 
    <div class="panel-heading" style="font-size:20px;text-align:center;">
        List of Agents
    </div>
    <br />     
          <center><button type="button" class="btn btn-primary" onClick="window.location.href='agentform.php'">Add New Agent</button></center>
    <div class="panel-body">                        
        <div class="table-responsive">
            <table id="example" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:center;">Agent Name</th>
                        <th style="text-align:center;">Account Type</th>
                        <th style="text-align:center;">Age</th>
                        <th style="text-align:center;">Qualification</th>
                        <th style="text-align:center;">Commission</th>
                        <th style="text-align:center;">Reffered By</th>
                        <th style="text-align:center;"> View Daily Report</th>
                        <th >Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                <?php	
            	foreach($agent_data as $k => $v)
           		 {
					?>
					<tr align="center">	                
             		<td><?php echo $agent_data[$k]['agent_name'];?></td>
             		<td><?php echo $agent_data[$k]['Description'];?></td>
                    <td><?php echo $agent_data[$k]['age'];?></td>
                    <td><?php echo $agent_data[$k]['qualification'];?></td>
                    <td><?php echo $agent_data[$k]['commission'];?></td>
                    <td><?php echo $agent_data[$k]['ref_by'];?></td>
                    <td  valign="middle" align="center"> <a href="viewdailycollectreport.php?viewid=<?php echo $agent_data[$k]['agent_id'];?>" style="color:#00F"> <img src="images/view.jpg" width="20"/></a></td>
                    <td  valign="middle" align="center"> <a href="agentform.php?id=<?php echo $agent_data[$k]['agent_id'];?>&edit" style="color:#00F"><img src="images/edit.gif" width="16" /></a></td>
                    <td  valign="middle" align="center"><a href="agentform.php?deleteid=<?php echo $agent_data[$k]['agent_id'];?>&del" style="color:#00F"><img src="images/del.gif" width="16"  /></a></td> 		   
                			
				<?php }?>
                 </tbody>
            </table>
        </div>
            
</div>

</div>

<?php include_once "includes/foot.php"; ?>
