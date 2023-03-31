<?php
include_once("include/display_table.class.php");


class Parking
{
	public $m_dbConn;
	public $display_pg;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg = new display_table($this->m_dbConn);
	
	}
	

	
	public function MemberParkingListings($bBillWise = false)
	{
		$finalArray = array();

			//print_r($new_parking);
			//die();
		
		if(!$bBillWise)
		{
			//$sqlCars = "SELECT mem.member_id, unit.unit_no, car.car_owner as vehicle_owner,car.parking_slot as parking_slot, car.parking_sticker as sticker, car.car_reg_no as reg_no,car.car_make as make,car.car_model as model,car.car_color as color,mem.owner_name as type,CONCAT(unit.unit_no, '-',mem.owner_name) as owner_name FROM `mem_car_parking` as car Join `member_main`as mem on mem.member_id = car.member_id Join `unit` on mem.unit = unit.unit_id where mem.ownership_status=1 order by unit.sort_order asc";
			$sqlCars ="SELECT mem.member_id, unit.unit_no, car.car_owner as vehicle_owner,car.parking_slot as parking_slot, car.parking_sticker as sticker, car.car_reg_no as reg_no,car.car_make as make,car.car_model as model,car.car_color as color,mem.owner_name as type FROM `mem_car_parking` as car Join `member_main`as mem on mem.member_id = car.member_id Join `unit` on mem.unit = unit.unit_id where mem.ownership_status=1 and car.status='Y' order by unit.sort_order asc";
			$resultCars = $this->m_dbConn->select($sqlCars);
			
			
		//	$sqlBike =  "SELECT mem.member_id, unit.unit_no, bk.bike_owner as vehicle_owner,bk.parking_slot as parking_slot, bk.parking_sticker as sticker, bk.bike_reg_no as reg_no,bk.bike_make as make,bk.bike_model as model,bk.bike_color as color,mem.owner_name as type,CONCAT(unit.unit_no, '-',mem.owner_name) as owner_name FROM `mem_bike_parking` as bk Join `member_main`as mem on mem.member_id = bk.member_id Join `unit` on mem.unit = unit.unit_id where mem.ownership_status=1 order by unit.sort_order asc";
				$sqlBike =  "SELECT mem.member_id, unit.unit_no, bk.bike_owner as vehicle_owner,bk.parking_slot as parking_slot, bk.parking_sticker as sticker, bk.bike_reg_no as reg_no,bk.bike_make as make,bk.bike_model as model,bk.bike_color as color,mem.owner_name as type FROM `mem_bike_parking` as bk Join `member_main`as mem on mem.member_id = bk.member_id Join `unit` on mem.unit = unit.unit_id where mem.ownership_status=1 and bk.status='Y' order by unit.sort_order asc";
			$resultBike = $this->m_dbConn->select($sqlBike);
			
			if(sizeof($resultCars) > 0)
			{
				for($cars = 0;$cars <= sizeof($resultCars)-1;$cars ++ )
				{
					$resultCars[$cars]["unit_no"] ="<a href='view_member_profile.php?id=".$resultCars[$cars]['member_id']."'>".$resultCars[$cars]['unit_no']."</a>";
					$resultCars[$cars]["type"] = 'Car';//'<i class="fa fa-car fa-lg" aria-hidden="true"></i>';
					array_push($finalArray,$resultCars[$cars]);
				}
			}
			if(sizeof($resultBike) > 0)
			{
				for($bikes = 0;$bikes <= sizeof($resultBike)-1;$bikes ++ )
				{
					$resultBike[$bikes]["unit_no"]="<a href='view_member_profile.php?id=".$resultBike[$bikes]['member_id']."'>".$resultBike[$bikes]['unit_no']."</a>";
					$resultBike[$bikes]["type"] = 'Bike';//<i class="fa fa-bicycle fa-lg" aria-hidden="true"></i>';
					array_push($finalArray,$resultBike[$bikes]);
				}
			}
			$thheader = array("Unit No", "Vehicle Owner Name", "Slot Number", "Sticker Number", "Registration Number", "Make", "Model", "Color", "Vehicle Type");
		}
		else
		{
			$sqlBike = "SELECT count(*) as bikes,bike.member_id,unit.unit_no,mm.primary_owner_name FROM `mem_bike_parking` as bike join `member_main` as mm on mm.member_id = bike.member_id join unit on unit.unit_id = mm.unit group by bike.member_id limit 0,1000";

			$sqlCars = "SELECT count(*) as cars,car.member_id,unit.unit_no,mm.primary_owner_name FROM `mem_car_parking` as car join `member_main` as mm on mm.member_id = car.member_id join unit on unit.unit_id = mm.unit where car.status = 'Y' group by car.member_id limit 0,1000";
			//echo $sqlBike; 
			$resultBike = $this->m_dbConn->select($sqlBike);
			$resultCars = $this->m_dbConn->select($sqlCars);
			
			/*echo "Bikes:";
			echo "<pre>";
			print_r($resultBike);
			echo "</pre>";
			
			echo "Cars:";
			echo "<pre>";
			print_r($resultCars);
			echo "</pre>";*/
			
			$new_parking = array_replace_recursive($resultBike, $resultCars);
			/*echo "New Parking";
			echo "<pre>";
			print_r($new_parking);
			echo "</pre>";*/
			
			/*foreach ($resultBike as $key => $value) 
			{
				$mem_id = $value["member_id"];
				//echo "<br>trace:".$mem_id;
				//if(in_array($resultCars, $mem_id))
				$b_Found = 0;
				foreach ($resultCars as $keyCar => $valueCar) 
				{
					//echo "car:".$valueCar["member_id"];
					if($valueCar["member_id"] == $mem_id)
					{
						//echo "<br>found".$mem_id ;
						$valueCar["bikes"] = $value["bikes"];
						$b_Found = 1;
					}
				}
				if($b_Found == 0)
				{
					array_push($valueCar, $value);
				}
			}*/
			
			for($i = 0; $i < sizeof($resultBike); $i++)
			{
				$mem_id = $resultBike[$i]["member_id"];
				$b_Found = 0;
				for($j = 0; $j < sizeof($resultCars); $j++)
				{
					if($resultCars[$j]["member_id"] == $mem_id)
					{
						$resultCars[$j]["bikes"] = $resultBike[$i]["bikes"];
						$b_Found = 1;
					}
				}
				if($b_Found == 0)
				{
					array_push($resultCars,$resultBike[$i]);
				}
			}
			
			for($i = 0; $i < sizeof($resultCars); $i++)
			{
				if(!isset($resultCars[$i]["cars"]))
				{
					$resultCars[$i]["cars"] = "0";
					//array_push($resultCars[$i],$resultCars[$i]["cars"]);
				}
			}
			
			/*echo "<pre>";
			print_r($resultCars);
			echo "</pre>";*/
			
			/*for($iBKCount  = 0; $iBKCount < sizeof($resultBike) ; $iBKCount++) 
			{
				$mem_id = $resultBike[$iBKCount]["member_id"];
				//echo "<br>trace:".$mem_id;
				//if(in_array($resultCars, $mem_id))
				for($iCarCount  = 0; $iCarCount < sizeof($resultCars) ; $iCarCount++) 
				{
				//foreach ($resultCars as $keyCar => $valueCar) 
				//{
					//echo "car:".$valueCar["member_id"];
					if($resultCars[$iCarCount]["member_id"] == $mem_id)
					{
						//echo "<br>found".$mem_id ;
						$resultCars[$iCarCount]["bikes"] = $resultBike[$iBKCount]["bikes"];
						break;
					}

				}
			}
			for($iCarCount  = 0; $iCarCount < sizeof($resultCars) ; $iCarCount++) 
			{
				$mem_id = $resultCars[$iBKCount]["member_id"];
				//echo "<br>trace:".$mem_id;
				$bFound = 0;
				$sBikesCount = 0;	
				for($iBKCount  = 0; $iBKCount < sizeof($resultBike) ; $iBKCount++) 
				{
					
					if($resultBike[$iBKCount]["member_id"] != $mem_id)
					{
						$bFound = 1;
						$sBikesCount = $resultBike[$iBKCount]["bikes"];
					}
					
				}
				if($bFound == 0)
				{
					//echo "<br>found".$mem_id ;
					$resultCars[$iCarCount]["bikes"] = $sBikesCount;
				}
				
			}*/
			/*echo "<pre>";
			print_r($resultCars);
			echo "</pre>";*/
			
			//$new_parking = array_merge($resultBike, $resultCars);
			//echo json_encode($new_parking);
			$new_vehicles = array();
			for($new = 0; $new < sizeof($resultCars); $new++) 
			{
				//echo "<br>cnt:". $new . " unit: " .$new_parking[$new]["unit_no"];
				$UnitNo = "-";
				$CarOwner = "-";
				$Bikes = "0";
				$Cars = "0";
				$bike_owner = "-";
				$owner_name = "-";
				if(isset($resultCars[$new]["unit_no"]))
				{
					$UnitNo = $resultCars[$new]["unit_no"];
				}
				if(isset($resultCars[$new]["car_owner"]))
				{
					$CarOwner = $resultCars[$new]["car_owner"];
				}
				if(isset($resultCars[$new]["bikes"]))
				{
					$Bikes = $resultCars[$new]["bikes"];
				}
				if(isset($resultCars[$new]["cars"]))
				{
					$Cars = $resultCars[$new]["cars"];
				}
				if(isset($resultCars[$new]["primary_owner_name"]))
				{
					$owner_name = $resultCars[$new]["primary_owner_name"];
				}
				if(isset($resultCars[$new]["bike_owner"]))
				{
					$bike_owner = $resultCars[$new]["bike_owner"];
				}
				$new_vehicles[$new]["member_id"] = $resultCars[$new]["member_id"];
				$new_vehicles[$new]["unit_no"] = $UnitNo; 
				$new_vehicles[$new]["primary_owner_name"] = '<a href="view_member_profile.php?scm&id='.$resultCars[$new]["member_id"].'&tik_id='.time().'&m&view" >'.$owner_name.'</a>'; 
				//$new_vehicles[$new]["bike_owner"] = $bike_owner; 
				$new_vehicles[$new]["bikes"] = $Bikes; 
				//$new_vehicles[$new]["car_owner"] = $CarOwner;
				$new_vehicles[$new]["cars"] = $Cars;  
				//$finalArray[""] = $new_parking[$new]["owner_name"];
				//print_r($new_vehiles_collection[$new]);
				array_push($finalArray, $new_vehicles[$new]);
			}
			$thheader = array("Unit","Owner Name","No. of Bikes","No. of Cars");
		}	
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