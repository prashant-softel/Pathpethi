<?php include_once("includes/head_s.php");
include("classes/agent_form.class.php");
include_once ("classes/dbconst.class.php");
$obj_agent_form = new agent_form($m_dbConn);
$agent_id = $_REQUEST['viewid'];
$details = $obj_agent_form->GetDetailsReport($agent_id);
$detailsreport = $obj_agent_form->GetDetailsDailyReport($agent_id);
$detailsExistingReport = $obj_agent_form->GetDetailsDailyExistingReport($agent_id);
?>
<html>
<head>
<title>Daily Collection Report</title>
</head>
<body>
<div class="panel panel-info" id="panel" style="display:none;width:70%;margin-left:3.5%; width:80%;">
<div class="panel-heading" id="pageheader">Daily Collection Report</div>
<br>
<center>
	<button type="button" class="btn btn-primary" onclick="window.location.href='agents_view.php'">Back to Agent list</button><br/><br/>
</center>
<center>
<table width="100%" style=" border:1px solid #e7e7e7;" align="center">
<tr><td><br></td></tr>
<tr style="background-color:#bce8f1; height:22px">
<th style="text-align: center;line-height: 22px;" >Agent Name  </th>
<th colspan="2" style="text-align: center;line-height: 22px;">Account Type </th>
<th style="text-align: center;line-height: 22px;">Qualification </th>
<th  style="text-align: center;line-height: 22px;">Age  </th>
</tr>
<tr>
<td align="center"><b><?php echo $details[0]['agent_name'];?></b></td>
<td colspan="2" align="center"><b><?php echo $details[0]['Description'];?></b></td>
<td align="center"><b><?php echo $details[0]['qualification'];?></b></td> 
<td align="center"><b><?php echo $details[0]['age'];?></b></td>
</tr>
<tr><td><br></td></tr>
<tr style="background-color:#bce8f1; height:22px">
<th style="text-align: center;line-height: 22px;">Mobille No </th>
<th colspan="2" style="text-align: center;line-height: 22px;" > Residential Address  </th>
<th style="text-align: center;line-height: 22px;">Commission </th>
<th style="text-align: center;line-height: 22px;">T.D.S </th>
</tr>
<tr>
<td align="center"><b><?php echo $details[0]['mobile_no'];?></b></td>
<td colspan="2" align="center"><b><?php echo $details[0]['address'];?></b></td>
<td align="center"><b><?php echo $details[0]['commission'];?></b></td>
<td align="center"><b><?php echo $details[0]['tds'];?></b></td>
</tr>
<tr><td><br></td></tr>
</table> 
</center>
<br/><br/>
<table id="example" class="display" cellspacing="0" width="100%" style=" border:1px solid #e7e7e7;" align="center">
    <thead>
		<tr><th colspan="6" style="color: #43729F;font-weight: bold;font-size: 18px;text-align: center;background-color: #d9edf7;border-color: #bce8f1; padding: 5px 5px;"> Agent Daily Collection </th></tr>
        <tr>
        <th style="text-align:center;">Sr. No</th>
        <th style="text-align:center;">Date</th>
        <th style="text-align:center;">Collected Amount</th>
        <th style="text-align:center;">Status</th>
        <th style="text-align:center;">Remark</th>
        <th style="text-align:center;">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php	
		foreach($detailsreport as $k => $data)
        {
            $disabled = $checked = $comment = '';
            $btn = 'btn-primary';
            if(!empty($detailsExistingReport[$data['date']]['Amount']) == $data['amount']){
                $disabled = 'disabled';
                $checked  = 'checked';
                $comment  = $detailsExistingReport[$data['date']]['comments'];
                $btn      = 'btn-default';
            } 
        ?>
			<tr align="center">	                
            <td><?php echo $k+1;?></td>
            <td id="date_<?=$k?>"><?php echo getDisplayFormatDate($data['date']);?></td>
            <td><a href="daily_collection.php?date=<?php echo $data['date'];?>&&agent_id=<?php echo $data['agent_id'];?>" id="amount_<?=$k?>"><?php echo $data['amount'];?></a></td>
			<td style="width:50px;"> <input type="checkbox" name="status" id="status_<?=$k?>" <?=$checked?> <?=$disabled?>> </td>            
            <td><input type="text" name="comment_<?=$k?>" id="comment_<?=$k?>" <?=$disabled?> value="<?=$comment?>"></td>
			<td><input type="button" name="update" id="update_<?=$k?>" class="update <?=$btn?>" data-row="<?=$k?>" value="Accept" class="btn btn-primary" <?=$disabled?> >
            </td>
		<?php 
	}?>
    <input type="hidden" name="agent_id" id="agent_id" value="<?=$agent_id?>">
    </tbody>            
</table>
</div>
</body>
<script src="js/jsDailycollection.js"></script>
<?php include_once "includes/foot.php"; ?>