<?php if(!isset($_SESSION)){ session_start(); }

class Share_Certificate
{
	public $actionPage= "../add_sharecertificate.php";
	public $m_dbConn;	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;								
	}
	
	function getUnitDetails()
	{
		 $sql = "select s.society_name,w.wing,u.unit_no,u.unit_id,m.owner_name,m.owner_gstin_no,u.share_certificate,u.share_certificate_from,u.share_certificate_to,u.nomination,u.nominee_name from unit as u,society as s,wing as w,member_main as m where u.status='Y' and w.status='Y' and s.status='Y' and m.status='Y' and u.wing_id=w.wing_id and u.unit_id=m.unit and s.society_id='".$_SESSION['society_id']."' and m.ownership_status = 1 ORDER BY u.sort_order ASC";
		$result = $this->m_dbConn->select($sql);
		return $result;		
	}
	
	function updateShareCertificateDetails()
	{
		//Print_r($_REQUEST);
		$final = array();
		$errorMsgArray=array();
		$selectQuery = "SELECT unit_id, share_certificate, share_certificate_from, share_certificate_to, nomination,nominee_name FROM unit WHERE society_id='".$_SESSION['society_id']."' ";
		$res = $this->m_dbConn->select($selectQuery);
		for($j = 0; $j < sizeof($res); $j++)
		{			
			$final[$res[$j]['unit_id']] = $res[$j]['share_certificate'];
			$final['from_'.$res[$j]['unit_id']] = $res[$j]['share_certificate_from'];
			$final['to_'.$res[$j]['unit_id']] = $res[$j]['share_certificate_to'];
			$final['nomination'.$res[$j]['unit_id']] = $res[$j]['nomination'];
			$final['nominee_name'.$res[$j]['unit_id']] = $res[$j]['nominee_name'];
		}
		
		for($i = 0; $i < $_POST['count']; $i++)
		{
			
			
			if(!isset($_POST['nomination'.$i]))
			{
				$_POST['nomination'.$i] = 0;
				
			}
			if($_POST['nomination'.$i]==1 && $_POST['nominee_name' .$i] == '')
			{
				$_POST['nomination'.$i] = 0;
				
				//echo "test";
				$errorMsgArray[$i] = '<span style="color:red;">Please enter nominee name *</span>';
				//print_r($errorMsgArray);
				continue;
			}			
			
			if($final[$_POST['unitID'.$i]] <> $_POST['sharecerticateNo'.$i] || $final['from_'.$_POST['unitID'.$i]] <> $_POST['from'.$i] || $final['to_'.$_POST['unitID'.$i]] <> $_POST['to'.$i] || $final['nomination'.$_POST['unitID'.$i]] <> $_POST['nomination'.$i] || $final['nominee_name'.$_POST['unitID'.$i]] <> $_POST['nominee_name'.$i])
			{
				//print_r($_POST['nominee_name' .$i]);
				  $sql = "UPDATE `unit` SET `share_certificate`='".$_POST['sharecerticateNo'.$i]."',`share_certificate_from`= ".$_POST['from'.$i].",`share_certificate_to`= ".$_POST['to'.$i].", `nomination`=" . $_POST['nomination'.$i]. ", `nominee_name`='" .$this->m_dbConn->escapeString(strtoupper( $_POST['nominee_name'.$i])) ."'	WHERE `society_id` = '".$_SESSION['society_id']."' AND `unit_id` = '".$_POST['unitID'.$i]."'";	
											
				$result = $this->m_dbConn->update($sql);
			}
			$sql1="UPDATE `member_main` SET owner_gstin_no='".$_POST['owner_gstin_no'.$i]."' where `society_id` = '".$_SESSION['society_id']."' AND `unit` = '".$_POST['unitID'.$i]."'";
			$result = $this->m_dbConn->update($sql1);
		}
		//print_r($_POST['count']);
		//die();
		//print_r($errorMsgArray);
		return $errorMsgArray;
	}
}
?>