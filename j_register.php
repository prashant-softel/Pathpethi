<?php 
include_once "classes/include/dbop.class.php";
$dbConn = new dbop();
include "classes/include/fetch_data.php";
$objFetchData = new FetchData($dbConn);
$objFetchData->GetSocietyDetails($_SESSION['society_id']);
include_once "classes/dbconst.class.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>J Register</title>
<style>
	table {
    	border-collapse: collapse;
	}
	table, th, td {
   		border: 1px solid #cccccc;
		text-align:left;
	}	
</style>


</head>

<body>
<div id="mainDiv">


<?php include_once( "report_template.php" ); // get the contents, and echo it out.?>

<?php 
$arrayUnit = array();
$sql01 = "select unit_id from unit";
$sql11 = $dbConn->select($sql01);
for($z=0;$z<sizeof($sql11);$z++)
{
	$arrayUnit[$z] = $sql11[$z]['unit_id'];
}
/*echo "<pre>";
print_r($arrayUnit);
echo "</pre>";
die();*/

?>

<div style="border: 1px solid #cccccc;">

        <div id="bill_header" style="text-align:center;">
            <div id="society_name" style="font-weight:bold; font-size:18px;"><?php echo $objFetchData->objSocietyDetails->sSocietyName; ?></div>
            <!--<div id="society_type" style="font-weight:bold; font-size:20px;">PREMISES CO-OPERATIVE SOCIETY LTD.</div>-->
            <div id="society_reg" style="font-size:14px;"><?php if($objFetchData->objSocietyDetails->sSocietyRegNo <> "")
				{
					echo "Registration No. ".$objFetchData->objSocietyDetails->sSocietyRegNo; 
				}
				?>
            </div>
            <div id="society_address"; style="font-size:14px;"><?php echo $objFetchData->objSocietyDetails->sSocietyAddress; ?></div>
        </div>
        <div id="bill_subheader" style="text-align:center;">
            <div style="font-weight:bold; font-size:22px;">LIST OF MEMBERS</div>
            <div style="font-weight:bold; font-size:20px;">Form "J"</div>
            <div style="font-weight:bold; font-size:16px;">(See Rule 33)</div>
           	         
        </div>        
		
        <?php
		
		?>
        <table style="width:100%" border="1">
        	<tr>
            	<th style="width:5%"><center>Serial No.</center></th>
                <th style="width:40%"><center>Full Name of the Member</center></th>
                <th style="width:40%"><center>Address</center></th>
                <th style="width:15%"><center>Class of Member</center></th>
            </tr>
            <?php
			$z = 1; 
			for($i=0; $i<sizeof($arrayUnit); $i++)
				  {
					  $toDisplayArray = $objFetchData->getDataForJ_register($arrayUnit[$i]);
					  
			?>
            <tr>
            	<td><center><?php echo $z; ?></center></td>
                
                <td style="max-width:100"><?php echo trim($toDisplayArray[0]['owner_name']); ?></td>
                
                <td><?php echo trim($toDisplayArray[0]['alt_address']); ?></td>
                
                <td></td>
             </tr>        
  
  <?php 
$z++;
}
?>

</table>
</div>
  </div>
</body>
</html>