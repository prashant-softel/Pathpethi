<?php
include_once("include/display_table.class.php");


class mDirectory
{
	public $m_dbConn;
	public $display_pg;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg = new display_table($this->m_dbConn);
	
	}
	
	public function MemberDirectory()
	{
		//$sql = "SELECT unit.unit_id,CONCAT(CONCAT('<div style=text-align:left;width:250px;>',''), owner_name,'</div>') as owner_name,
			//		unit.unit_no, intercom_no,
				//	CONCAT(CONCAT('<div style=text-align:center;float:center;>',''), IF(publish_contact = '1',mob,'<div style=\"text-align:center;\"><i class=\'fa  fa-lock\'  style=\'font-size:10px;font-size:1.75vw;color:#C0C0C0; \' title=\'No information has been shared by member\'></i></div>'),'</div>') as mobile,
					//CONCAT(CONCAT('<div style=text-align:center;float:center;>',''), IF(publish_contact = '1',other_email,'<div style=\"text-align:center;\"><i class=\'fa  fa-lock\'  style=\'font-size:10px;font-size:1.75vw;color:#C0C0C0; \' title=\'No information has been shared by member\'></i></div>'),'</div>')  as email 
//					FROM `unit` JOIN `member_main` ON unit.unit_id = member_main.unit where  member_main.ownership_status=1 ORDER BY unit.sort_order ASC";
					$sql = "SELECT unit.unit_id,CONCAT(CONCAT('<div style=text-align:left;width:250px;>',''), other_name,'</div>') as owner_name, unit.unit_no, intercom_no, CONCAT(CONCAT('<div style=text-align:center;float:center;>',''), IF(other_family.other_publish_contact = '1',other_mobile,'<div style=\"text-align:center;\"><i class=\'fa fa-lock\' style=\'font-size:10px;font-size:1.75vw;color:#C0C0C0; \' title=\'No information has been shared by member\'></i></div>'),'</div>') as mobile, CONCAT(CONCAT('<div style=text-align:center;float:center;>',''), IF(other_family.other_publish_contact = '1',other_email,'<div style=\"text-align:center;\"><i class=\'fa fa-lock\' style=\'font-size:10px;font-size:1.75vw;color:#C0C0C0; \' title=\'No information has been shared by member\'></i></div>'),'</div>') as email FROM `unit` JOIN `member_main` ON unit.unit_id = member_main.unit JOIN `mem_other_family` as other_family ON other_family.member_id= member_main.member_id where member_main.ownership_status=1 ORDER BY unit.sort_order ASC;";
		$result = $this->m_dbConn->select($sql);
		$thheader = array('Member Name','Unit No','Intercom No','Mobile','Email');		
		$data = $this->displayDatatable($result,$thheader);
		return $data;
	}
	
	
	public function ProfessionalListings()
	{
		$finalArray = array();
		$sql = "SELECT member_main.member_id,CONCAT(CONCAT('<div style=text-align:left;>',''), owner_name,'</div>') as owner_name,
					unit.unit_no,dsg.desg,profile,owner_name as rel,
					CONCAT(CONCAT('<div style=text-align:center;float:center;>',''), IF(publish_contact = '1',mob,'<div style=\"text-align:center;\"><i class=\'fa  fa-lock\'  style=\'font-size:10px;font-size:1.75vw;color:#3100FF4D; \' title=\'No information has been shared by member\'></i></div>'),'</div>') as mobile,
					CONCAT(CONCAT('<div style=text-align:center;float:center;>',''), IF(publish_contact = '1',email,'<div style=\"text-align:center;\"><i class=\'fa  fa-lock\'  style=\'font-size:10px;font-size:1.75vw;color:#3100FF4D; \' title=\'No information has been shared by member\'></i></div>'),'</div>')  as email ,publish_profile
					FROM `unit` JOIN `member_main` ON unit.unit_id = member_main.unit JOIN desg as dsg on  member_main.desg=dsg.desg_id where member_main.ownership_status=1 ORDER BY unit.sort_order ASC";
		$result = $this->m_dbConn->select($sql);
		
		
		//array_unshift($resultotherFamilyMembers,array("unit_id" => $result[$i]['unit_id'] ));
		//$resultotherFamilyMembers = array("unit_id" => $result[$i]['unit_id'] ) + $resultotherFamilyMembers;
		for($i = 0; $i <= sizeof($result) -1; $i++)
		{
			/*if($result[$i]["publish_profile"] == 1)
			{
				$result[$i]["owner_name"] = '<div style="text-align:left;padding-left: 15px;">'.$result[$i]['owner_name'] .'</div>';
				$result[$i]["mobile"] = '<div style="text-align:center;padding-left: 15px;float: left;">'.$result[$i]['mobile'] .'</div>';
				$result[$i]["email"] = '<div style="text-align:center;padding-left: 15px;float: left;">'.$result[$i]['email'] .'</div>';
				$result[$i]["profile"] = '<div style="width: 100px;white-space: nowrap; overflow: hidden;text-overflow: ellipsis;">'.$result[$i]['profile'] .'</div>';
				$result[$i]["rel"] = '<div style="text-align:left;padding-left: 15px;">Self</div>';
				unset($result[$i]["publish_profile"]);
				array_push($finalArray,$result[$i]);
			}*/
			$sqlotherFamilyMembers = "SELECT mm.other_name,desg,other_profile,mm.relation  as rel,
															CONCAT(CONCAT('<div style=text-align:center;float:center;>',''), IF(other_publish_contact = '1',mm.other_mobile,'<div style=\"text-align:center;\"><i class=\'fa  fa-lock\'  style=\'font-size:10px;font-size:1.75vw;color:#C0C0C0; \' title=\'No information has been shared by member\'></i></div>'),'</div>') as mobile,
															CONCAT(CONCAT('<div style=text-align:center;float:center;>',''), IF(other_publish_contact = '1',mm.other_email,'<div style=\"text-align:center;\"><i class=\'fa  fa-lock\'  style=\'font-size:10px;font-size:1.75vw;color:#C0C0C0; \' title=\'No information has been shared by member\'></i></div>'),'</div>')  as email 
															FROM mem_other_family as mm  JOIN desg as dsg on  mm.other_desg=dsg.desg_id
															where mm.status='Y' and  member_id = '".$result[$i]['member_id']."' and  other_publish_profile = 1";
			$resultotherFamilyMembers = $this->m_dbConn->select($sqlotherFamilyMembers);
			if(sizeof($resultotherFamilyMembers) > 0)
			{
				for($other = 0; $other <= sizeof($resultotherFamilyMembers) -1; $other++)
				{
					$temp = array();
					$temp[0]["member_id"] = $result[$i]['member_id'] ;
					$temp[0]["owner_name"] = '<div style="text-align:left;padding-left: 15px;">'.$resultotherFamilyMembers[$other]['other_name'] .'</div>';
					$temp[0]["unit_no"] = $result[$i]['unit_no'] ;
					$temp[0]["desg"] = $resultotherFamilyMembers[$other]['desg'];
					$temp[0]["profile"] = '<div style="width: 100px;white-space: nowrap; overflow: hidden;text-overflow: ellipsis;">'.$resultotherFamilyMembers[$other]['other_profile'].'</div>';
					$temp[0]["rel"] = $resultotherFamilyMembers[$other]['rel'];
					$temp[0]["mobile"] = '<div style="text-align:center;padding-left: 15px;float: center;">'.$resultotherFamilyMembers[$other]['mobile'].'</div>' ;
					$temp[0]["email"] =  '<div style="text-align:center;padding-left: 15px;float: center;">'.$resultotherFamilyMembers[$other]['email'].'</div>'  ;
					//var_dump($temp);
					array_push($finalArray,$temp[0]);
				}
			}
			
		}
		$thheader = array('Name','Unit No','Designation','Profile','Relation With Owner','Mobile','Email');	
		$data = $this->displayDatatable($finalArray,$thheader);
		return $data;
	}
	
	
	public function BloodGroupListings()
	{
		$finalArray = array();
		//$sql = "SELECT mm.member_id,mm.owner_name,unit_no,bgg.bg,mm.owner_name as rel,
					//IF(publish_contact = '1',mm.mob,'<div style=\"text-align:center;\"><i class=\'fa  fa-lock\'  style=\'font-size:10px;font-size:1.75vw;color:#3100FF4D; \' title=\'No information has been shared by member\'></i></div>') as mobile,
					//IF(publish_contact = '1',mm.email,'<div style=\"text-align:center;\"><i class=\'fa  fa-lock\'  style=\'font-size:10px;font-size:1.75vw;color:#3100FF4D; \' title=\'No information has been shared by member\'></i></div>') as email
					//FROM member_main as mm,bg as bgg,unit as u,wing as w,desg as dsg 
				  	//where mm.blood_group=bgg.bg_id and mm.unit=u.unit_id and u.wing_id=w.wing_id and mm.desg=dsg.desg_id and 
				 //mm.status='Y' and bgg.status='Y' and u.status='Y' and w.status='Y' and dsg.status='Y' and  mm.ownership_status=1 ORDER BY u.sort_order ASC";

		$sql = "SELECT mm.member_id, mm.owner_name, unit_no FROM member_main as mm,unit as u, wing as w where mm.unit=u.unit_id and u.wing_id=w.wing_id and mm.status='Y' and u.status='Y' and w.status='Y' and mm.ownership_status=1 ORDER BY u.sort_order ASC";

		$result = $this->m_dbConn->select($sql);
		for($i = 0; $i <= sizeof($result) -1; $i++)
		{
			/*if($result[$i]["bg_id"] <> 9)
			{
				$result[$i]["owner_name"] = '<div style="text-align:left;padding-left: 15px;">'.$result[$i]['owner_name'] .'</div>';
				$result[$i]["mobile"] = '<div style="text-align:left;padding-left: 15px;float: left;">'.$result[$i]['mobile'] .'</div>';
				$result[$i]["email"] = '<div style="text-align:left;padding-left: 15px;float: left;">'.$result[$i]['email'] .'</div>';
				$result[$i]["rel"] = '<div style="text-align:left;padding-left: 15px;">Self</div>';
				array_push($finalArray,$result[$i]);
			}*/
			//echo "<br>".$finalArray[$i]['member_id'];
			//unset($finalArray[$i]['member_id']);
			
			$sqlotherFamilyMembers = "SELECT mm.other_name,bgg.bg as bg,mm.relation  as rel,
														CONCAT(CONCAT('<div style=text-align:center;float:center;>',''), IF(other_publish_contact = '1',mm.other_mobile,'<div style=\"text-align:center;\"><i class=\'fa  fa-lock\'  style=\'font-size:10px;font-size:1.75vw;color:#C0C0C0; \' title=\'No information has been shared by member\'></i></div>'),'</div>') as mobile,
															CONCAT(CONCAT('<div style=text-align:center;float:center;>',''), IF(other_publish_contact = '1',mm.other_email,'<div style=\"text-align:center;\"><i class=\'fa  fa-lock\'  style=\'font-size:10px;font-size:1.75vw;color:#C0C0C0; \' title=\'No information has been shared by member\'></i></div>'),'</div>')  as email FROM mem_other_family as mm,bg as bgg 
														where mm.child_bg =bgg.bg_id  and  mm.status='Y' 
														and bgg.status='Y' and bgg.bg_id != 9  and  member_id = '".$result[$i]['member_id']."' ";
			$resultotherFamilyMembers = $this->m_dbConn->select($sqlotherFamilyMembers);
			//array_unshift($resultotherFamilyMembers,array("unit_id" => $result[$i]['unit_id'] ));
			//$resultotherFamilyMembers = array("unit_id" => $result[$i]['unit_id'] ) + $resultotherFamilyMembers;
			if(sizeof($resultotherFamilyMembers) > 0)
			{
				for($other = 0; $other <= sizeof($resultotherFamilyMembers) -1; $other++)
				{
					$temp = array();
					$temp[0]["member_id"] = $result[$i]['member_id'] ;
					$temp[0]["owner_name"] = '<div style="text-align:left;padding-left: 15px;">'.$resultotherFamilyMembers[$other]['other_name'] .'</div>';
					$temp[0]["unit_no"] = $result[$i]['unit_no'] ;
					$temp[0]["bg"] = $resultotherFamilyMembers[$other]['bg'] ;
					$temp[0]["rel"] = '<div style="text-align:left;padding-left: 15px;">'.$resultotherFamilyMembers[$other]['rel'].'</div>';
					$temp[0]["mobile"] = '<div style="text-align:center;padding-left: 15px;float: center;">'.$resultotherFamilyMembers[$other]['mobile'].'</div>' ;
					$temp[0]["email"] =  '<div style="text-align:center;padding-left: 15px;float: center;">'.$resultotherFamilyMembers[$other]['email'].'</div>'  ;
					array_push($finalArray,$temp[0]);
				}
			}
		}
		
		$thheader = array('Name','Unit No','Blood Group','Relation With Owner','Mobile','Email');			
		$data = $this->displayDatatable($finalArray,$thheader);
		return $data;
	}
	
	public function displayDatatable($rsas,$thheader,$map)
	{
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= $map;
		
		$res = $this->display_pg->display_datatable($rsas, false, false);
		return $res;
	}
}
?>