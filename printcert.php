<?php include_once "ses_set_common.php"; ?>
<?php 
include_once("classes/include/dbop.class.php");
include_once("classes/service_prd_reg_edit.class.php");
include_once ("classes/dbconst.class.php");
$m_dbConnRoot = new dbop(true);
$obj_service_prd_reg = new service_prd_reg($m_dbConn, $m_dbConnRoot);
if(isset($_REQUEST['id']))
{
	if($_REQUEST['id']<>"")
	{
		$edit = $obj_service_prd_reg->reg_edit($_REQUEST['id']);
		//print_r($edit);
		$soc_name = $obj_service_prd_reg->soc_name($edit[0]['society_id']);
		$soc_add = $obj_service_prd_reg->soc_add($edit[0]['society_id']);
		//print_r($soc_add);
		
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" type="text/css" href="css/idcard.css"/>
<script language="javascript" type="application/javascript">
function print_ICard()
{
	document.getElementById('printpage').style.display = 'none';	
	//document.getElementById('prt2').style.display = 'none';	
	
	window.print();
}
</script>

<style>
.print_btn{
    color: #fff;
    background-color: #337ab7;
    border-color: #2e6da4;
    display: inline-block;
    padding: 6px 12px;
    margin-bottom: 0;
    font-size: 14px;
    line-height: 1.42857143;
    cursor: pointer;
    -webkit-user-select: none;
    border: 1px solid transparent;
    border-radius: 4px;
	margin-top: 260px;
    margin-left: -205px;
}
</style>
</head>


<body>
<?php
if(strlen($edit[0]['full_name']) > 15)
 {
  $FullName=substr($edit[0]['full_name'],0,15).'...';
 }
 else
 {
	 $FullName=$edit[0]['full_name']; 
 }
$WorkingAs= array();
	$categories = $obj_service_prd_reg->fetchSelectedCategories($_REQUEST['id']);
			for($i = 0; $i < sizeof($categories); $i++)
			{
				array_push($WorkingAs, $categories[$i]['cat']); 
				
			} 		
	
    
    echo'
    <br><br>
    
        <div id="certbody" style=" margin:5px 10px;background-image: url(bgimage2.png); background-repeat:no-repeat; background-position:50% 27%;    border-radius: 10px; ">
            <div class="head">
            <div class="sidea">
            <p><b>'.strtoupper($soc_name).'</b></p>
            <p style="font-size: 8px;">'.strtoupper($soc_add).'</p>
         
            </div>
            <div class="sideb"><img src=' .substr($edit[0]['photo_thumb'], 3).'  width="85%" height="auto" /></div>
            
            </div>
            <div class="bodymain">
            <div class="bodya">
            <ol>
            <li>Full Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp; 
			<div style="width:65%;margin-left: 78px;  color: maroon; margin-top: -12px;font-style:italic; font-family:Helvetica Neue,Helvetica,Arial,sans-serif;"><span style="border-bottom:1px  dotted black;">'.stripslashes($edit[0]['full_name']).'</span></div> 			</li>
			<li>     
			  Date Of Birth &nbsp;: &nbsp;&nbsp; 
			  <span style="border-bottom:1px  dotted black; font-family:Helvetica Neue,Helvetica,Arial,sans-serif; font-style:italic; color:maroon;">'.stripslashes($edit[0]['dob']).'</span>
			</li>
            <li>Contact No.&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;
			<span style="border-bottom:1px  dotted black; font-family:Helvetica Neue,Helvetica,Arial,sans-serif;  font-style:italic; color:maroon;">'.stripslashes($edit[0]['cur_con_1']).' </span>
			</li>
            <li>Working Since:&nbsp;&nbsp;&nbsp;
			<span style="border-bottom:1px  dotted black; font-family:Helvetica Neue,Helvetica,Arial,sans-serif;  font-style:italic; color:maroon;">'.getDisplayFormatDate(stripslashes($edit[0]['since'])).'</span>
			</li>
            <li>Working As &nbsp;&nbsp;&nbsp; :&nbsp;&nbsp;&nbsp;
			<span style="border-bottom:1px  dotted black; font-family:Helvetica Neue,Helvetica,Arial,sans-serif;  font-style:italic; color:maroon;">
			'. implode(',', $WorkingAs) .'
			 </span>
			</li>
            </ol>
            
            </div>
			<br>
            <div class="bodyb">
            <div style="width:30%; float:left;margin-top: 6px; "><p>...............................<br />Signature<br /><small>Managing Committee</small></p></div>
          
             <div style="width:30%; float:right; margin-top: 6px;"><p>..............................<br />Signature <br /><small>('.$FullName.')</small></p></div>
            </div>
			<!--<div style="width: 100%;border-top: 1px solid black;"></div>
			<p style="font-size: 11px;font-weight: bold;font-style: normal;margin-top: 5px; float:left;">Address &nbsp; : &nbsp;
			<span style="font-style: italic;color: maroon;font-size: 10px;">'.stripslashes($edit[0]['cur_resd_add']).'</span>
			</p>-->
            </div>
        
        </div>
    
    </div>
    <div data-role="page" id="pageone">
  <div data-role="header">
 
 </div>
 <div data-role="main" class="ui-content">
    <button onclick="print_ICard();" style="" id="printpage" class="print_btn">Print</button>
	</div>
    ';
?>