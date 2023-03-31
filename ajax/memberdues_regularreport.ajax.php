<?php
include_once("../classes/include/dbop.class.php");
include_once("../classes/dbconst.class.php");
include_once("../classes/member_due_regular.class.php");
include_once("../classes/include/fetch_data.php");
include_once("../classes/document_maker.class.php");
include_once("../classes/utility.class.php");
include_once("../classes/notice.class.php");	


$m_dbConn = new dbop();
$m_dbConnRoot = new dbop(true);
$obj_memberDuesRegular= new memberDuesRegular($m_dbConn,$m_dbConnRoot);
$obj_templates = new doc_templates($m_dbConn,$m_dbConnRoot);
$objFetchData = new FetchData($m_dbConn);
$obj_Utility = new utility($m_dbConn,$m_dbConnRoot);
$obj_notice = new notice($m_dbConn, $dbConnRoot);


//echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="sendDuesNotice")
	{
		$Units   = array();
		$societyID = $_SESSION['society_id'];
		$DBName = $_SESSION['dbname'];
		$unitAry=$_REQUEST['UnitArray'];
		$Units = json_decode($unitAry);	
		
		for($i=0;$i<count($Units);$i++)
		{
			$toPass=array();
			$toPass['unit_id']=$Units[$i];
			$toPass['template_id']=27;
			$society_id = $_SESSION['society_id'];
			//echo $society_id;
			$login_id = $_SESSION['login_id'];
			$dbname = $_SESSION['dbname']; 
			$IssuedBy = $_SESSION['name']; 
			$Subject = 'OVERDUE PAYMENT NOTICE';
			$noticeType = 5;
			$Document=$obj_templates ->fetch_data($toPass);
			$noticeTypeID = 1;
			$note='';
			$Result= $obj_notice->AddNotice();
			$noticeCreationType = 1;
			$sub="OVERDUE PAYMENT NOTICE";
			$PostDate= date('d-m-Y');
			$datetime = new DateTime($PostDate);
			$datetime->modify('+1 day');
			$Exp_date=$datetime->format('d-m-Y');
			$notify = 1;
			
			$arUnit = array();
			array_push($arUnit, $toPass['unit_id']);
			echo $obj_notice->AddNotice($society_id, $login_id, $dbname, $IssuedBy,$sub, $Document,$note, $PostDate, $Exp_date, $noticeType,$noticeCreationType, $arUnit, $noticeTypeID, $notify, 0, "");
			     
		
		}
	
	}
?>