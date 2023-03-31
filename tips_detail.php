<?php  include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");   
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/tips.class.php");
include_once("classes/utility.class.php");
$dbConn = new dbop();
$dbConnRoot = new dbop(true);
$obj_tips = new tips($m_dbConnRoot,$m_dbConn);
$TipsDetails = $obj_tips->RecordsList( $_REQUEST['id']);
//print_r($TipsDetails);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Service Request Details</title>
</head>
<script type="text/javascript" src="lib/jquery-1.10.2.min.js"></script>
		<script language="javascript" type="application/javascript">
	function go_error()
    {
        setTimeout('hide_error()',10000);	
    }
    function hide_error()
    {
        document.getElementById('error').style.display = 'none';	
    }	
	var aryTips =[]; 
	
</script>
<style>
ol, ul
{
	list-style:decimal !important;
}
iframe
{
	width: 60%;
	position: absolute;
	    height: 210px;
}
</style>
<body>
<br><br>
<?php   if($_SESSION['View'] == "ADMIN")
    {
        ?>
        <div class="panel panel-info" id="panel" style="display:none;margin-top:10px;margin-left:3.5%;width:90%">
        <?php
    }
    else
    {
        ?>
        <div class="panel panel-info" id="panel" style="display:none;margin-top:10px;margin-left:3.5%;width:70%">
        <?php
    }
?>
<!--<div class="panel panel-info" id="panel" style="margin-top:6%;margin-left:3.5%; border:none;width:70%;display:none">-->
<div class="panel-heading" id="pageheader" style="font-size:20px">
    Tips Details
    </div>
    <br />
<div width="100%" style="font-size:12px;" id="PrintableDiv"  >

<?php 
for($i=0;$i<sizeof($TipsDetails);$i++)
{
	$tipAry = json_encode(array("id" =>  $TipsDetails[$i]['id'], "title" =>  $TipsDetails[$i]['atr_title'], "desc" =>  $TipsDetails[$i]['desc']));
	?>
     <script>
						 aryTips.push((<?php echo $tipAry;?>));
						 console.log(aryTips[0]);
			</script>
<table width="100%" style="font-size:12px;" id="PrintableTable">

	<tr style="background-color:#bce8f1;font-size:14px;" height="30" >
        <th  style="width:20%; padding-left: 5px; cursor:pointer; padding-top: 10px; color: steelblue;"  id="exp_<?php echo $TipsDetails[$i]['id']; ?>" onClick="expandDetails(this.id);"><span id="change_<?php echo $TipsDetails[$i]['id'];?>" style=" font-size: 1.75em;"></span>&nbsp;&nbsp;&nbsp;<?php echo $TipsDetails[$i]['atr_title'] ?>
       <?php if($TipsDetails[$i]['url']<> '' )
	   {
	   ?>
       <span><img src="images/video.png" width="35" height="35" alt="videos" title="videos" style="margin-top: -5px;float: right;"></span>
       <?php }
	  ?>
        </th>
        
    </tr>
    <tr id="extra_<?php echo $TipsDetails[$i]['id']?>" style="display:none">
    	<td style="text-align: justify;padding-left: 3%;padding-right: 5px; box-sizing:none !important;"><?php echo $TipsDetails[$i]['desc']?><br>
        <?php $Video = $TipsDetails[$i]['url'];
		$VideoMode = false;
		if(isset($Video) && $Video != "")
		{
					$VideoMode = true;
					?>
                    
        <div style="width: 65%; position: relative;padding-bottom:220px"><?php echo $TipsDetails[$i]['url']?></div>
		<?php
        }
		?>
      

        
</td>
          </tr> 
</table>
<?php }?>   
<br>
</div>
</div>
</div>
</body>

<script>
expandDetails('exp_'+aryTips[0]['id']);
function expandDetails(obj)
{
    var id = obj.split('_')[1];
	//var desc=obj['disc'] ;
    //document.getElementById("exp_" + id).innerHTML = "desc";
	for(var i = 0;  i < aryTips.length; i++)
	{
			var iAryID = aryTips[i]['id'];
		
			if(iAryID == id)
			{
				document.getElementById("exp_" + id).onclick = function(){ collapseDetails(obj); } ;
    			document.getElementById("extra_" + id).style.display = "table-row"; 
				document.getElementById("change_" +id).innerHTML="<i class= 'fa fa-minus-circle'></i>";
			}
			else
			{
				document.getElementById("exp_" + iAryID).onclick = function(){ expandDetails(this.id); } ;
    			document.getElementById("extra_" + iAryID).style.display = "none"; 
				document.getElementById("change_" +iAryID).innerHTML="<i class= 'fa fa-plus-circle'></i>";
			}
	}
}
function collapseDetails(obj)
{
    var id = obj.split('_')[1]; 
	//var desc=obj['disc']; 
  	document.getElementById("change_" +id).innerHTML="<i class= 'fa fa-plus-circle'></i>";
    document.getElementById("exp_" + id).onclick = function(){ expandDetails(this.id); } ;
    document.getElementById("extra_" + id).style.display = "none"; 
}
</script>
<?php include_once "includes/foot.php"; ?>
        