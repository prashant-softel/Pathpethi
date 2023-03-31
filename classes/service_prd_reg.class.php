<?php
include_once("include/display_table.class.php");
include_once ("dbconst.class.php"); 

class service_prd_reg 
{
	public $actionPage = "../service_prd_reg.php?srm";
	public $m_dbConn;
	public $m_dbConnRoot;
	
	function __construct($dbConn, $dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		$this->m_dbConnRoot = $dbConnRoot;
		//dbop::__construct();
	}
	public function startProcess()
	{
		$errorExists=0;
		if($_REQUEST['insert']=='Register' && $errorExists==0)
		{
				if($_FILES['photo']['name'] <> "")
				{ 
					//$exe_photo_main = strtolower(substr($_FILES['photo']['name'],-4));
						
					//if($exe_photo_main=='.jpg' || $exe_photo_main=='.png' || $exe_photo_main=='.jpeg' || $exe_photo_main=='.bmp' || $exe_photo_main=='.gif')
					if (($_FILES["photo"]["type"] == "image/gif") || 
									($_FILES["photo"]["type"] == "image/jpeg") || 
									($_FILES["photo"]["type"] == "image/png") || 
									($_FILES["photo"]["type"] == "image/jpg")) 
							{
								 $exe_photo_main = "";
								//$extension= "";
								//$url="";
								if ($_FILES["photo"]["type"] == "image/jpeg")
								{
									$exe_photo_main =".jpeg" ;
								}
								else if($_FILES["photo"]["type"] == "image/png")
								{
									$exe_photo_main =".png" ;
								}
								else if ($_FILES["photo"]["type"] == "image/gif")
								{
									 $exe_photo_main =".gif" ;
								}
								else if ($_FILES["photo"]["type"] == "image/jpg")
								{
									 $exe_photo_main =".jpg" ;
								}	
								
					$photo_new_path = $this->up_photo($_FILES['photo']['name'],$_FILES["photo"]["tmp_name"],'../upload/main');
					////////////////////////////////////////
					$thumbWidth_index  = 140;
					$thumbHeight_index = 130;
					
					//$pathToThumbs_index = '../upload/thumb/';
					//$pathToThumbs_index = $_SERVER['DOCUMENT_ROOT'].'/upload/thumb/';
					$pathToThumbs_index = '../upload/thumb/';
					$image_name = time().'_thumb_'.str_replace(' ','-',$_FILES['photo']['name']);
					
					$thumb_path = $this->thumb_photo($thumbWidth_index,$thumbHeight_index,$pathToThumbs_index,$photo_new_path,$exe_photo_main,$image_name); 
					////////////////////////////////////////
					//$thumb_path='';
					}
					else 
					{					
						return 'Invalid File Type For Photo';
					}
				}
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						
			$dob = $_POST['dob'];
			$dob1 = explode('-',$dob);
			$dd = $dob1[0];
			$mm = $dob1[1];
			$yy = $dob1[2];		
			$age = $this->age($dd, $mm, $yy);
			
			$insert_query = "insert into service_prd_reg
							(`society_id`,`full_name`,`photo`,`photo_thumb`,`age`,`dob`,`identy_mark`,`cur_resd_add`,`cur_con_1`,`cur_con_2`,`native_add`,
							`native_con_1`,`native_con_2`,`ref_name`,`ref_add`,`ref_con_1`,`ref_con_2`,`since`,`education`,`marry`,`father_name`,
							`father_occ`,`mother_name`,`mother_occ`,`hus_wife_name`,`hus_wife_occ`,`son_dou_name`,`son_dou_occ`,`other_name`,`other_occ`) 
							 
							 values 
							('".$_SESSION['society_id']."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['full_name'])))."','".$photo_new_path."','".$thumb_path."','".$age."','".$_POST['dob']."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['identy_mark'])))."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['cur_resd_add'])))."','".$_POST['cur_con_1']."','".$_POST['cur_con_2']."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['native_add'])))."',
							
							'".$_POST['native_con_1']."','".$_POST['native_con_2']."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['ref_name'])))."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['ref_add'])))."','".$_POST['ref_con_1']."','".$_POST['ref_con_2']."','".getDBFormatDate($_POST['since'])."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['education'])))."','".$_POST['marry']."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['father_name'])))."',
							
							'".$this->m_dbConn->escapeString(ucwords(trim($_POST['father_occ'])))."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['mother_name'])))."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['mother_occ'])))."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['hus_wife_name'])))."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['hus_wife_occ'])))."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['son_dou_name'])))."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['son_dou_occ'])))."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['other_name'])))."','".$this->m_dbConn->escapeString(ucwords(trim($_POST['other_occ'])))."')";
			$data = $this->m_dbConnRoot->insert($insert_query);
						
			if($_POST['cat_id']<>"")
			{
				foreach($_POST['cat_id'] as $k => $v)
				{
					$sql1 = "insert into spr_cat(`service_prd_reg_id`,`cat_id`)values('".$data."','".$v."')";
					$res1 = $this->m_dbConnRoot->insert($sql1);
				}
			}
			
		//	echo "count".$_POST['totaldoc'];			
			//if($_POST['document']<>"")
			//{
				//foreach($_POST['document'] as $k1 => $v1)
				for($i = 0; $i < $_POST['totaldoc']; $i++)
				{
					$fileName = "";						
					if($_POST['document'.$i] <> "")					
					{
						if($_FILES['file'.$i]['name'] <> "")
						{	
							$fileTempName = $_FILES['file'.$i]['tmp_name'];  
							$fileSize = $_FILES['file'.$i]['size'];
							$fileName = time().'_'.basename($_FILES['file'.$i]['name']);
									
							$uploaddir = "../Service_Provider_Documents";			   
							$uploadfile = $uploaddir ."/". $fileName;	
												
							move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile);							
						}									
						$sql2 = "insert into spr_document(`service_prd_reg_id`,`document_id`, `attached_doc`)values('".$data."','".$_POST['document'.$i]."', '".$fileName."')";
						$res2 = $this->m_dbConnRoot->insert($sql2);
					}
				}
			//}
						
			$units = json_decode($_POST['unit1']);						
			
			for($i = 0; $i < sizeof($units); $i++)
			{
				$unit = $units[$i+1];
				$sql = "INSERT INTO `service_prd_units`(`service_prd_id`, `unit_id`, `unit_no`, `society_id`) VALUES ('".$data."','".$units[$i]."', '".$unit."', '".$_SESSION['society_id']."')";	
				$result = $this->m_dbConnRoot->insert($sql);
				$i++;					
			}		
			?>
            <script>//window.location.href = '../service_prd_reg_view.php?srm&add&idd=<?php //echo time();?>';</script>
            <?php
			return "Insert";
		}		
		/*
			}
			else
			{
				return "Already Exist";	
			}
		*/
	}
	public function age( $day,$month,$year)
	{
		(checkdate($month, $day, $year) == 0) ? die("no such date.") : "";
		$y = gmstrftime("%Y");
		$m = gmstrftime("%m");
		$d = gmstrftime("%d");
		$age = $y - $year;
		return (($m <= $month) && ($d <= $day)) ? $age - 1 : $age;
	}
	
	public function startProcess1()
	{
		if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			$up_query = "update service_prd_reg set `full_name`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['full_name'])))."',`photo`='".$_POST['photo']."',`age`='".$_POST['age']."',`dob`='".$_POST['dob']."',`identy_mark`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['identy_mark'])))."',`cur_resd_add`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['cur_resd_add'])))."',`cur_con_1`='".$_POST['cur_con_1']."',`cur_con_2`='".$_POST['cur_con_2']."',`native_add`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['native_add'])))."',`native_con_1`='".$_POST['native_con_1']."',`native_con_2`='".$_POST['native_con_2']."',`ref_name`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['ref_name'])))."',`ref_add`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['ref_add'])))."',`ref_con_1`='".$_POST['ref_con_1']."',`ref_con_2`='".$_POST['ref_con_2']."',`since`='".$_POST['since']."',`education`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['education'])))."',`marry`='".$_POST['marry']."',`father_name`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['father_name'])))."',`father_occ`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['father_occ'])))."',`mother_name`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['mother_name'])))."',`mother_occ`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['mother_occ'])))."',`hus_wife_name`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['hus_wife_name'])))."',`hus_wife_occ`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['hus_wife_occ'])))."',`son_dou_name`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['son_dou_name'])))."',`son_dou_occ`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['son_dou_occ'])))."',`other_name`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['other_name'])))."',`other_occ`='".$this->m_dbConn->escapeString(ucwords(trim($_POST['other_occ'])))."' where service_prd_reg_id='".$_POST['id']."'";
			$data=$this->m_dbConn->update($up_query);
			return "Update";
		
			
			$s  = "delete from hotel_cat where service_prd_reg_id='".$_POST['id']."' and status='Y'"; //echo '<br />';
			$s1 = "delete from spr_document where service_prd_reg_id='".$_POST['id']."' and status='Y'"; //echo '<br />';
			
			$r  = $this->m_dbConn->delete($s);
			$r1 = $this->m_dbConn->delete($s1);
			
			if($_POST['cat_id']<>"")
			{
				foreach($_POST['cat_id'] as $k => $v)
				{
					$sql1 = "insert into spr_cat(`service_prd_reg_id`,`cat_id`)values('".$data."','".$v."')";
					$res1 = $this->m_dbConn->insert($sql1);
				}
			}
			
			if($_POST['document']<>"")
			{
				foreach($_POST['document'] as $k1 => $v1)
				{
					$sql2 = "insert into spr_document(`service_prd_reg_id`,`document_id`)values('".$data."','".$v1."')";
					$res2 = $this->m_dbConn->insert($sql2);
				}
			}
			return "Update";
		}
	}
	
	public function up_photo($name,$tmp_path,$location)
	{
		$photo_name = $name;
		$photo_name1 = str_replace(' ','-',$name);
		$old_path = $tmp_path;
		$new_path = $location.'/'.time().'_'.$photo_name1;
		$image = move_uploaded_file($old_path,$new_path);
		
		return $new_path;
	}
	
	public function thumb_photo($thumbWidth,$thumbHeight,$pathToThumbs,$newpath,$exe,$image_name)
	{
		$kk = 0;
					
	  if($exe=='.jpg' || $exe=='.jpeg')
	  {
		$img = imagecreatefromjpeg($newpath);				  //die();
		if(!$img)
		{
			$kk = 1;
		?>
		<!--	<script> window.location.href = '../service_prd_reg.php?nul=nul'; </script> -->
		<?php	
		}
	  }
	  else if($exe=='.gif')
	  {
		$img = imagecreatefromgif($newpath);				  //die();				  
		if(!$img)
		{
			$kk = 1;
		?>
			<!--<script> window.location.href = '../service_prd_reg.php?nul=nul'; </script> -->
		<?php	
		}
	  }
	  else if($exe=='.png')
	  {
		$img = imagecreatefrompng($newpath);				  //die();
		if(!$img)
		{
			$kk = 1;
		?>
			<!--<script> window.location.href = '../service_prd_reg.php?nul=nul'; </script>-->
		<?php	
		}
	  }
	  else if($exe=='.bmp')
	  {
		$img = imagecreatefromwbmp($newpath);				  //die();
		if(!$img)
		{
			$kk = 1;
		?>
			<!--<script> window.location.href = '../service_prd_reg.php?nul=nul'; </script>-->
		<?php	
		}
	  }
	  else {} 
		  
	  if($kk<>1)
	  {
		  $width  = imagesx($img);
		  $height = imagesy($img);

		  $new_width  = $thumbWidth;
		  $new_height = $thumbHeight;
	
		  $tmp_img = imagecreatetruecolor($new_width,$new_height);
		  imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
		  imagejpeg($tmp_img,"{$pathToThumbs}{$image_name}");
		  
		  $thum_path = $pathToThumbs.$image_name;
		  
		  return $thum_path;
	  }
	}
	
	public function combobox07($query,$id)
	{
	$str.="<option value=''>All</option>";
	$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($id==$v)
						{
							$sel = 'selected';	
						}
						else
						{
							$sel = '';
						}
						
						$str.="<OPTION VALUE=".$v.' '.$sel.">";
					}
					else
					{
						$str.=$v."</OPTION>";
					}
					$i++;
				}
			}
		}
			return $str;
	}
	public function combobox($query)
	{
			$str.="<option value=''>Please Select</option>";
			$data = $this->m_dbConn->select($query);
				if(!is_null($data))
				{
					foreach($data as $key => $value)
					{
						$i=0;
						foreach($value as $k => $v)
						{
							if($i==0)
							{
								$str.="<OPTION VALUE=".$v.">";
							}
							else
							{
								$str.=$v."</OPTION>";
							}
							$i++;
						}
					}
				}
					return $str;
	}
	
	public function combobox11($query,$name,$id)
	{
		$data = $this->m_dbConnRoot->select($query);
		if(!is_null($data))
		{
			$pp = 0;
			foreach($data as $key => $value)
			{
				$i=0;
				
				foreach($value as $k => $v)
				{
					if($i==0)
					{
					?>
					&nbsp;<input type="checkbox" value="<?php echo $v;?>" name="<?php echo $name;?>" id="<?php echo $id;?><?php echo $pp;?>"/>					
					<?php
					}
					else
					{
					echo $v;
					?>
						<br />
					<?php
					}
					$i++;
				}
			$pp++;
			}
			?>
			<input type="hidden" size="2" id="count_<?php echo $id;?>" value="<?php echo $pp;?>" />
			<?php
		}
	}
	public function combobox111($query,$name,$id,$new_id)
	{
		$ww = explode(",",$new_id);
		
		$data = $this->m_dbConnRoot->select($query);
		if(!is_null($data))
		{
			$pp = 0;
			foreach($data as $key => $value)
			{
				$i=0;
				
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if(in_array($v,$ww))
						{
							$s="checked";
						}
						else
						{
							$s="";
						}
					?>
					<input type="checkbox" value="<?php echo $v;?>" name="<?php echo $name;?>" id="<?php echo $id;?><?php echo $pp;?>" <?php echo $s;?>/>					
					<?php
					}
					else
					{
					echo $v;
					?>
						<br />
					<?php
					}
					$i++;
				}
			$pp++;
			}
			?>
			<input type="hidden" size="2" id="count_<?php echo $id;?>" value="<?php echo $pp;?>" />
			<?php
		}
	}
	public function display1($rsas)
	{
			$thheader=array('full_name','photo','age','dob','identy_mark','cur_resd_add','cur_con_1','cur_con_2','native_add','native_con_1','native_con_2','ref_name','ref_add','ref_con_1','ref_con_2','since','education','marry','father_name','father_occ','mother_name','mother_occ','hus_wife_name','hus_wife_occ','son_dou_name','son_dou_occ','other_name','other_occ');
			$this->display_pg->edit="getservice_prd_reg";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="service_prd_reg.php";
			
			//$res=$this->display_reg($rsas);
			$res=$this->display_reg_short($rsas);
			
			return $res;
	}
	public function pgnation()
	{
		$sql1 = "select sp.service_prd_reg_id, sp.full_name, sp.photo, sp.photo_thumb, sp.age, sp.active, s.society_id, s.society_name, sc.spr_cat_id, sc.cat_id , c.cat from service_prd_reg as sp, society as s, spr_cat as sc, cat as c where sp.society_id=s.society_id and sp.service_prd_reg_id=sc.service_prd_reg_id and sc.cat_id=c.cat_id and sp.status='Y' and s.status='Y' and sc.status='Y' and c.status='Y' and sp.society_id = '" . $_SESSION['society_id'] . "'";
		//$sql1 = "select sp.service_prd_reg_id, sp.full_name, sp.photo, sp.photo_thumb, sp.age, s.society_id, s.society_name, sc.spr_cat_id, sc.cat_id , c.cat from service_prd_reg as sp, society as s, spr_cat as sc, cat as c where sp.society_id=s.society_id and sp.service_prd_reg_id=sc.service_prd_reg_id and sc.cat_id=c.cat_id and sp.status='Y' and s.status='Y' and sc.status='Y' and c.status='Y'";
		
		if(isset($_SESSION['admin']))
		{
			$sql1 .= "and s.society_id='".$_SESSION['society_id']."'";	
		}
		
		if($_REQUEST['society_id']<>"")
		{
			$sql1 .= " and s.society_id = '".$_REQUEST['society_id']."'";
		}
		if($_REQUEST['cat_id']<>"")
		{
			foreach($_REQUEST['cat_id'] as $k => $v)
			{
				$cat_id0 .= $v.',';
			}
			$cat_id = substr($cat_id0,0,-1);
			$sql1 .= " and sc.cat_id in (".$cat_id.")";
		}
		if($_REQUEST['key']<>"")
		{
			$sql1 .= " and sp.full_name like '%".$this->m_dbConn->escapeString($_REQUEST['key'])."%'";
		}
		$sql1 .= ' group by sp.service_prd_reg_id order by s.society_id,sp.full_name';
		
		
		
		$cntr = "select count(*) as cnt from service_prd_reg as sp, society as s, spr_cat as sc, cat as c where sp.society_id=s.society_id and sp.service_prd_reg_id=sc.service_prd_reg_id and sc.cat_id=c.cat_id and sp.status='Y' and s.status='Y' and sc.status='Y' and c.status='Y'";
		
		if(isset($_SESSION['admin']))
		{
			$cntr .= "and s.society_id='".$_SESSION['society_id']."'";	
		}
		
		if($_REQUEST['society_id']<>"")
		{
			$cntr .= " and s.society_id = '".$_REQUEST['society_id']."'";
		}
		if($_REQUEST['cat_id']<>"")
		{
			foreach($_REQUEST['cat_id'] as $k1 => $v1)
			{
				$cat_id00 .= $v1.',';
			}
			$cat_id0 = substr($cat_id00,0,-1);
			$cntr .= " and sc.cat_id in (".$cat_id0.")";
		}
		if($_REQUEST['key']<>"")
		{
			$cntr .= " and sp.full_name like '%".$this->m_dbConn->escapeString($_REQUEST['key'])."%'";
		}
		/*$cntr .= ' group by sp.service_prd_reg_id order by s.society_id,sp.full_name';
		
		
		
		$this->display_pg->sql1=$sql1;
		$this->display_pg->cntr1=$cntr;
		$this->display_pg->mainpg="service_prd_reg.php";
		$limit = "30";
		$page = $_REQUEST['page'];
		
		$extra = "&srm";
		
		$res=$this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;*/
			
		$result=$this->m_dbConnRoot->select($sql1);
		$this->display_reg_short($result);
			
	}
	
	
	
	########################################################################################################################################
	########################################################################################################################################
	
	
	public function display_reg_short($res)
	{
		//print_r($res);
		if($res<>"")
		{
			?>
            <table id="example" class="display" cellspacing="0" width="100%">
            <thead>
            <tr  height="30" bgcolor="#CCCCCC">
            	<?php //if(isset($_SESSION['role']) && $_SESSION['role']==ROLE_SUPER_ADMIN){?>
                <!--<th width="180">Society Name</th>-->
                <?php //}?>
                <th ><input type="checkbox" name="allcheck" id="allcheck" onClick="SelectAllPrintIDCard(this)"></th>
            	<th >Photo</th>
                <th >Full Name</th>
                <th >Age(Year)</th>
                <th style="width:80px;" >Category</th>
                <th> Working in Units </th>
                <th >View</th>
                <th >Print</th>
                <!--<th >Print Id Card</th>-->
                <?php //if(isset($_SESSION['role']) && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN) ){?>
            	<th style="width:46px;" >Status</th>
                <th >Edit</th>
                <th >Delete</th>
                <?php //} ?>
            </tr>
            </thead>
            <tbody>
            <?php
			foreach($res as $k => $v)
			{?>
				
			
            <tr height="25" bgcolor="#BDD8F4" align="center" id="tr_<?php echo $res[$k]['service_prd_reg_id'] ?>">
            <?php if($res[$k]['active']==0)
				{?>
			
            <td><input type="checkbox" name="check" id="check_<?php echo $res[$k]['service_prd_reg_id']?>"  value="<?php echo $res[$k]['service_prd_reg_id']?>" style="display:none;"></td>
				<?php }
				else
				{?>
                
            <td><input type="checkbox" name="check" id="check_<?php echo $res[$k]['service_prd_reg_id']?>"  value="<?php echo $res[$k]['service_prd_reg_id']?>"></td>
            			<script>
						 aryServiceRegID.push("<?php echo  $res[$k]['service_prd_reg_id']?>");
						</script>
            	<?php } ?>
                
                <td >
                	<a href="<?php echo substr($res[$k]['photo'],3);?>" target="_blank" class="fancybox"><img src="<?php echo substr($res[$k]['photo_thumb'], 3);?>" height="45" width="45"/></a>
                </td>
                <td align="center">
                	<a href="reg_form_print_new.php?id=<?php echo $res[$k]['service_prd_reg_id']?>&srm" style="color:#00F;"><?php echo $res[$k]['full_name'];?></a>
                </td>
                
                <td align="center"><?php echo $res[$k]['age'];?> </td>
                
                <td align="center">
                    <!--<div style="overflow-y:scroll;overflow-x:hidden;width:200px; height:50px; border:solid #CCCCCC 1px;">-->
                    <?php $get_reg_cat = $this->get_reg_cat($res[$k]['service_prd_reg_id']);?>
                    <!--</div>-->
                </td>
                
                <td align="center">
                	<?php $get_reg_units = $this->get_reg_units($res[$k]['service_prd_reg_id']);?>
                </td>
               <?php if($res[$k]['active']==0)
				{?>
               	<td>
                	<a href="reg_form_print_new.php?id=<?php echo $res[$k]['service_prd_reg_id']?>&srm" style="color:#00F;"><img src="images/view.jpg" width="20" width="20" style="display:none;" /></a>
                </td>
                
                <td>
                	<a href="reg_form_print1.php?id=<?php echo $res[$k]['service_prd_reg_id']?>&srm" target="_blank"><img src="images/print.png" width="35" width="35" style="display:none;"/></a>
                </td>
                <?php }
				else
				{?>
                 	<td>
                	<a href="reg_form_print_new.php?id=<?php echo $res[$k]['service_prd_reg_id']?>&srm" style="color:#00F;"><img src="images/view.jpg" width="20" width="20" style="display:block;" /></a>
                </td>
                
                <td>
                	<a href="reg_form_print1.php?id=<?php echo $res[$k]['service_prd_reg_id']?>&srm" target="_blank"><img src="images/print.png" width="35" width="35" style="display:block;"/></a>
                </td>
                <?php }?>
         <?php if($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN_MEMBER || $_SESSION['role'] == ROLE_ADMIN)
			{
				if($res[$k]['active']==0)
				{?>
               <!-- <td>
                	<a href="printcert.php?id=<?php //echo $res[$k]['service_prd_reg_id']?>&srm" target="_blank"><img src="images/print.png" width="35" width="35"  style="display:none;"/></a>
                </td>-->
            		<td>
          			<span style="color:red; font-size:12px;" id="st_<?php echo $res[$k]['service_prd_reg_id'];?>"  onClick="statusapproved(this.id)" ><b>&nbsp;&nbsp;Pending</b><span style="font-size: 10px;color: black;"><br>( Click here to Aprove )</span></span></td>
			<?php 
				} 
			else
				{?>
                <!-- <td>
                	<a href="printcert.php?id=<?php //echo $res[$k]['service_prd_reg_id']?>&srm" target="_blank"><img src="images/print.png" width="35" width="35"  style="display:block;"/></a>
                </td>-->
            		<td>
            		<p style='color:green;font-size:12px;'><b>Aproved</b></p>
					</td>
     	<?php 
	 			}
			}
		else
			{
			if($res[$k]['active']==0)
			{?>
            	<td>
				<p style='color:red;font-size:12px;'><b>Pending</b></p>
                </td>
                <?php
			}
			else
			{
			?>
			<td>
			<p style='color:green;font-size:12px;'><b>Aproved</b></p>
            </td>
			<?php
			 }
		
			}//}?>
   
                <?php
				if(isset($_SESSION['role']) && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN) )
				{
				?>
                    <td>
                	<a href="javascript:void(0);" onclick="service_prd_reg_edit(<?php echo $res[$k]['service_prd_reg_id']?>)"><img src="images/edit.gif"  /></a>
                  </td>
                                
                <td>					
                    <a href="javascript:void(0);" onclick="getservice_prd_reg('delete-<?php echo $res[$k]['service_prd_reg_id']?>');"><img src="images/del.gif" /></a>                 </td>
      		<?php
				}       
					else if($res[$k]['active']==1)
					{
					?>
                		<td>
                			<a href="javascript:void(0);" onclick="service_prd_reg_edit(<?php echo $res[$k]['service_prd_reg_id']?>)"><img src="images/edit.gif"  style="display:none"/></a>
                     	</td>
                    	<td>					
                    	<a href="javascript:void(0);" onclick="getservice_prd_reg('delete-<?php echo $res[$k]['service_prd_reg_id']?>');"><img src="images/del.gif"  style="display:none;"/></a> 
                		</td>
                <?php 
					}
					else
					{
						?>
                        <td>      
                		<a href="javascript:void(0);" onclick="service_prd_reg_edit(<?php echo $res[$k]['service_prd_reg_id']?>)"><img src="images/edit.gif" /></a>       
                        </td>   
                		<td>					
                    	<a href="javascript:void(0);" onclick="getservice_prd_reg('delete-<?php echo $res[$k]['service_prd_reg_id']?>');"><img src="images/del.gif" /></a>                		</td>
                        
                <?php
				 	}
			
			}
			?>
            </tr>
            </tbody>
            </table>
			<?php
		}
		 else
			{
			?>
            <table align="center" border="0">
            <tr>
            	<td><font color="#FF0000" size="2"><b>No Records Found.</b></font></td>
            </tr>
            </table>
            <?php	
			}
	}	
	
	########################################################################################################################################
	########################################################################################################################################

	public function chk_delete_perm_admin()
	{
		$sql = "select * from del_control_admin where status='Y' and login_id='".$_SESSION['login_id']."'";
		$res = $this->m_dbConn->select($sql);
		return $res[0]['del_control_admin'];
	}
	public function get_reg_cat($service_prd_reg_id)
	{
		$sql = "select c.cat from spr_cat as sc,cat as c where sc.cat_id=c.cat_id and sc.service_prd_reg_id='".$service_prd_reg_id."' and sc.status='Y' and c.status='Y' ";				
		$res = $this->m_dbConnRoot->select($sql);
		
		if($res<>"")
		{
			foreach($res as $k => $v)
			{
				$reg_cat .= $res[$k]['cat'].', ';
			}			
			echo substr($reg_cat,0,-2);
		}
	}
	public function get_reg_doc($service_prd_reg_id)
	{
		$sql = "select d.document from spr_document as sd, document as d where sd.document_id=d.document_id and sd.service_prd_reg_id='".$service_prd_reg_id."' and sd.status='Y' and d.status='Y' ";
		$res = $this->m_dbConnRoot->select($sql);
		
		if($res<>"")
		{
			foreach($res as $k => $v)
			{
				$reg_doc .= $res[$k]['document'].', ';
			}
			echo substr($reg_doc,0,-2);
		}
	}
	
	public function selecting()
	{
		$sql1="select service_prd_reg_id,`full_name`,`photo`,`age`,`dob`,`identy_mark`,`cur_resd_add`,`cur_con_1`,`cur_con_2`,`native_add`,`native_con_1`,`native_con_2`,`ref_name`,`ref_add`,`ref_con_1`,`ref_con_2`,`since`,`education`,`marry`,`father_name`,`father_occ`,`mother_name`,`mother_occ`,`hus_wife_name`,`hus_wife_occ`,`son_dou_name`,`son_dou_occ`,`other_name`,`other_occ` from service_prd_reg where service_prd_reg_id='".$_REQUEST['service_prd_regId']."'";
		$var=$this->m_dbConnRoot->select($sql1);
		return $var;
	}
	public function deleting()
	{
		$sql1 = "update service_prd_reg set status='N' where service_prd_reg_id='".$_REQUEST['service_prd_regId']."'";
		$this->m_dbConnRoot->update($sql1);
		
		$sql = "update spr_cat set status='N' where service_prd_reg_id='".$_REQUEST['service_prd_regId']."'";
		$res = $this->m_dbConnRoot->update($sql);
		
		$sql2 = "update spr_document set status='N' where service_prd_reg_id='".$_REQUEST['service_prd_regId']."'";
		$res2 = $this->m_dbConnRoot->update($sql2);
		
		$deleteQuery = "DELETE FROM `service_prd_units` WHERE `service_prd_id` = '".$_REQUEST['service_prd_regId']."'";
		$this->m_dbConnRoot->delete($deleteQuery);
	}
	
	public function reg_edit()
	{
		$sql = "select * from service_prd_reg where service_prd_reg_id='".$_REQUEST['id']."' and status='Y'";
		$res = $this->m_dbConnRoot->select($sql);	
		return $res;
	}
	
	public function fetchUnits()
	{
		//$sql = 'SELECT `unit_no`, `unit_id` FROM `unit` WHERE `society_id` = "'.$_SESSION['society_id'].'"';	
		$sql = 'SELECT unit.unit_no, unit.unit_id, member_main.owner_name FROM `unit` JOIN `member_main` on unit.unit_id = member_main.unit WHERE unit.society_id = "'.$_SESSION['society_id'].'"';
		$result = $this->m_dbConn->select($sql);
		return $result;
	}
	
	public function fetchDocuments()
	{
		$sql = "select document_id,document from document where status='Y' order by document_id";
		$result = $this->m_dbConnRoot->select($sql);
		return $result;
	}
	
	public function get_reg_units($service_prd_reg_id)
	{
		$sql = "SELECT `unit_no` FROM `service_prd_units` WHERE `service_prd_id` ='".$service_prd_reg_id."'";				
		$res = $this->m_dbConnRoot->select($sql);
		
		if($res<>"")
		{
			foreach($res as $k => $v)
			{				
				$var = explode('[', $res[$k]['unit_no']);				
				$reg_units .= $var[0].', ';
			}			
			echo substr($reg_units,0,-2);
		}
	}
	
		public function get_reg_units_societywise($service_prd_reg_id)
		{
		$resArray = array();
		$sql = "SELECT sp.unit_no,sp.society_id,societytbl.society_name FROM `service_prd_units` as sp JOIN `society` as societytbl on sp.society_id = societytbl.society_id WHERE `service_prd_id`='".$service_prd_reg_id."'";				
		$res = $this->m_dbConnRoot->select($sql);
		
		if($res<>"")
		{
			foreach($res as $k => $v)
			{				
				/*$var = explode('[', $res[$k]['unit_no']);				
				$reg_units .= $var[0].', ';*/
				$var = explode('[', $res[$k]['unit_no']);			
				if (array_key_exists($res[$k]['society_name'],$resArray))
				{
					$resArray[$res[$k]['society_name']] = $resArray[$res[$k]['society_name']].",".$var[0];
				}
				else
				{
					$resArray[$res[$k]['society_name']] = $var[0];
				}
			}			
			//echo substr($reg_units,0,-2);
			return $resArray;
			
			
		}
	}
	
	
	
	
	public function fetchServiceProvider()
	{
		//ftechinf list of service provider from common database
		$sql1 = "select sp.service_prd_reg_id, sp.full_name,sp.father_name,sp.father_occ,sp.mother_name,sp.mother_occ,sp.hus_wife_name,sp.hus_wife_occ,sp.son_dou_name,sp.son_dou_occ,sp.other_name,sp.other_occ,sp.marry as married,sp.cur_con_1,sp.cur_con_2,sp.since,sp.photo, sp.photo_thumb, sp.age,sp.cur_resd_add, s.society_id, s.society_name, sc.spr_cat_id, sc.cat_id , c.cat,sp.identy_mark,sp.education,sp.native_add,sp.native_con_1,sp.native_con_2 from service_prd_reg as sp, society as s, spr_cat as sc, cat as c where sp.society_id=s.society_id and sp.service_prd_reg_id=sc.service_prd_reg_id and sc.cat_id=c.cat_id and sp.status='Y' and s.status='Y' and sc.status='Y' and c.status='Y'";
		
		if(isset($_SESSION['admin']))
		{
			$sql1 .= "and s.society_id='".$_SESSION['society_id']."'";	
		}
		
		if($_REQUEST['society_id']<>"")
		{
			$sql1 .= " and s.society_id = '".$_REQUEST['society_id']."'";
		}
		if($_REQUEST['cat_id']<>"")
		{
			//add condition to fetch service provider category wise
			foreach($_REQUEST['cat_id'] as $k => $v)
			{
				$cat_id0 .= $v.',';
			}
			$cat_id = substr($cat_id0,0,-1);
			$sql1 .= " and sc.cat_id in (".$cat_id.")";
		}
		if($_REQUEST['key']<>"")
		{
			$sql1 .= " and sp.full_name like '%".$this->m_dbConn->escapeString($_REQUEST['key'])."%'";
		}
		$sql1 .= ' group by sp.service_prd_reg_id order by s.society_id,sp.full_name';
		$result=$this->m_dbConnRoot->select($sql1);
		return $result;
	}

}
?>