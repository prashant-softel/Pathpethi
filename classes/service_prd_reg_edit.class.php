<?php
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once("dbconst.class.php");

class service_prd_reg 
{
	public $actionPage = "../reg_form_print_new.php";
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
		if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			if($_FILES['photo']['name']<>"")
			{
				//$exe_photo_main = strtolower(substr($_FILES['photo']['name'],-4));
						
				//if($exe_photo_main=='.jpg' || $exe_photo_main=='.png' || $exe_photo_main=='.jpeg' || $exe_photo_main=='.bmp' || $exe_photo_main=='.gif')
				//{
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
				//$photo_new_path = $this->up_photo($_FILES['photo']['name'],$_FILES["photo"]["tmp_name"],$_SERVER['DOCUMENT_ROOT'].'/upload/main');
				////////////////////////////////////////
				$thumbWidth_index  = 140;
				$thumbHeight_index = 130;
				//$pathToThumbs_index = $_SERVER['DOCUMENT_ROOT'].'/upload/thumb/';
				$pathToThumbs_index ='../upload/thumb/';
				$image_name = time().'_thumb_'.str_replace(' ','-',$_FILES['photo']['name']);
				
				$thumb_path = $this->thumb_photo($thumbWidth_index,$thumbHeight_index,$pathToThumbs_index,$photo_new_path,$exe_photo_main,$image_name); 
				////////////////////////////////////////
				//$thumb_path='';
				unlink($_POST['photo_old']);
				unlink($_POST['photo_thumb_old']);
				}
				else 
				{
					return 'Invalid File Type';
				}
			}
			else
			{
				$photo_new_path = $_POST['photo_old'];
				$thumb_path		= $_POST['photo_thumb_old']; 		
			}
			
			$dob = $_POST['dob'];
			$dob1 = explode('-',$dob);
			$dd = $dob1[0];
			$mm = $dob1[1];
			$yy = $dob1[2];		
			$age = $this->age($dd,$mm,$yy);
			
		 	$up_query = "update service_prd_reg set `full_name`='".addslashes(ucwords(trim($_POST['full_name'])))."',`photo`='".$photo_new_path."',`photo_thumb`='".$thumb_path."',`age`='".$age."',`dob`='".$_POST['dob']."',`identy_mark`='".addslashes(ucwords(trim($_POST['identy_mark'])))."',`cur_resd_add`='".addslashes(ucwords(trim($_POST['cur_resd_add'])))."',`cur_con_1`='".$_POST['cur_con_1']."',`cur_con_2`='".$_POST['cur_con_2']."',`native_add`='".addslashes(ucwords(trim($_POST['native_add'])))."',`native_con_1`='".$_POST['native_con_1']."',`native_con_2`='".$_POST['native_con_2']."',`ref_name`='".addslashes(ucwords(trim($_POST['ref_name'])))."',`ref_add`='".addslashes(ucwords(trim($_POST['ref_add'])))."',`ref_con_1`='".$_POST['ref_con_1']."',`ref_con_2`='".$_POST['ref_con_2']."',`since`='".getDBFormatDate($_POST['since'])."',`education`='".addslashes(ucwords(trim($_POST['education'])))."',`marry`='".$_POST['marry']."',`father_name`='".addslashes(ucwords(trim($_POST['father_name'])))."',`father_occ`='".addslashes(ucwords(trim($_POST['father_occ'])))."',`mother_name`='".addslashes(ucwords(trim($_POST['mother_name'])))."',`mother_occ`='".addslashes(ucwords(trim($_POST['mother_occ'])))."',`hus_wife_name`='".addslashes(ucwords(trim($_POST['hus_wife_name'])))."',`hus_wife_occ`='".addslashes(ucwords(trim($_POST['hus_wife_occ'])))."',`son_dou_name`='".addslashes(ucwords(trim($_POST['son_dou_name'])))."',`son_dou_occ`='".addslashes(ucwords(trim($_POST['son_dou_occ'])))."',`other_name`='".addslashes(ucwords(trim($_POST['other_name'])))."',`other_occ`='".addslashes(ucwords(trim($_POST['other_occ'])))."' where service_prd_reg_id='".$_POST['id']."'";
			
			$data=$this->m_dbConnRoot->update($up_query);
			
			
			$s  = "delete from spr_cat where service_prd_reg_id='".$_POST['id']."' and status='Y'";
			$s1 = "delete from spr_document where service_prd_reg_id='".$_POST['id']."' and status='Y'";
			
			$r  = $this->m_dbConnRoot->delete($s);
			$r1 = $this->m_dbConnRoot->delete($s1);
			
			
			if($_POST['cat_id']<>"")
			{
				foreach($_POST['cat_id'] as $k => $v)
				{
					$sql1 = "insert into spr_cat(`service_prd_reg_id`,`cat_id`)values('".$_POST['id']."','".$v."')";
					$res1 = $this->m_dbConnRoot->insert($sql1);
				}
			}
			
			/*if($_POST['document']<>"")
			{
				foreach($_POST['document'] as $k1 => $v1)
				{
					$sql2 = "insert into spr_document(`service_prd_reg_id`,`document_id`)values('".$_POST['id']."','".$v1."')";
					$res2 = $this->m_dbConnRoot->insert($sql2);
				}
			}*/
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
											
						move_uploaded_file($_FILES['file'.$i]['tmp_name'], $uploadfile);							
					}	
					else
					{
					$fileName = $_POST['file'.$i];
				//$thumb_path		= $_POST['photo_thumb_old']; 		
				}								
					 $sql2 = "insert into spr_document(`service_prd_reg_id`,`document_id`, `attached_doc`)values('".$_POST['id']."','".$_POST['document'.$i]."', '".$fileName."')";
				//}
					$res2 = $this->m_dbConnRoot->insert($sql2);
				}
			}
			
			$deleteQuery = "DELETE FROM `service_prd_units` WHERE `service_prd_id` = '".$_POST['id']."' AND `society_id` = '".$_SESSION['society_id']."'";
			//echo $deleteQuery;
			$result = $this->m_dbConnRoot->delete($deleteQuery);
			
			$units = json_decode($_POST['unit1']);
			//print_r($units);			
			
			for($i = 0; $i < sizeof($units); $i++)
			{
				$unit = $units[$i+1];
				$sql = "INSERT INTO `service_prd_units`(`service_prd_id`, `unit_id`, `unit_no`, `society_id`) VALUES ('".$_POST['id']."','".$units[$i]."', '".$unit."', '".$_SESSION['society_id']."')";	
				//echo $sql;
				$result = $this->m_dbConnRoot->insert($sql);
				$i++;					
			}
					
			return "Update";
		}
	}
	public function age($day,$month,$year)
	{
		(checkdate($month, $day, $year) == 0) ? die("no such date.") : "";
		$y = gmstrftime("%Y");
		$m = gmstrftime("%m");
		$d = gmstrftime("%d");
		$age = $y - $year;
		return (($m <= $month) && ($d <= $day)) ? $age - 1 : $age;
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
			<script> window.location.href = 'service_prd_reg.php?nul=nul'; </script>
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
			<script> window.location.href = 'service_prd_reg.php?nul=nul'; </script>
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
			<script> window.location.href = 'service_prd_reg.php?nul=nul'; </script>
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
			<script> window.location.href = 'service_prd_reg.php?nul=nul'; </script>
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
	
	public function combobox_units($prd_id)
	{	
		$query = 'SELECT * FROM `service_prd_units` WHERE `service_prd_id` = "'.$prd_id.'" ORDER BY `society_id`';						
		$data = $this->m_dbConnRoot->select($query);
		if(!is_null($data))
		{
			$prevSocietyId = "";
			$str = "";
			for($i=0; $i < sizeof($data); $i++)
			{
				if($prevSocietyId != $data[$i]['society_id'])
				{	
					$prevSocietyId = $data[$i]['society_id'];
					$societyname = $this->soc_name($data[$i]['society_id']);	
					$str.="<OPTION><b>";		
					$str.=$societyname."</b></OPTION>";											
					$str.="<OPTION> &nbsp; &nbsp; &nbsp; &nbsp;";		
					$str.=$data[$i]['unit_no']."</OPTION>";		
				}
				else
				{
					$str.="<OPTION> &nbsp; &nbsp; &nbsp; &nbsp;";		
					$str.=$data[$i]['unit_no']."</OPTION>";	
				}
			}
		}
		return $str;
	}
	
	public function combobox11($query,$name,$id)
	{
		$data = $this->m_dbConn->select($query);
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
					<input type="checkbox" value="<?php echo $v;?>" name="<?php echo $name;?>" id="<?php echo $id;?><?php echo $pp;?>"/>					
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
	public function combobox111($query,$name,$id,$sql)
	{
		$s = $sql;
		$r = $this->m_dbConnRoot->select($s);
		
		if($r<>"")
		{
			foreach($r as $t =>$z)
			{
				foreach($z as $m =>$g)
				{
					if($i==0)
					{
					$ee.= $g.",";
					}
				}
			}
		}
		$active = substr($ee,0,-1);
		$ww = explode(",",$active);
		
		
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

						//$str.="<OPTION VALUE=".$v." ".$s.">";
					?>
					&nbsp;<input type="checkbox" value="<?php echo $v;?>" name="<?php echo $name;?>" id="<?php echo $id;?><?php echo $pp;?>" <?php echo $s;?>/>					
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
	
	public function combobox1111($query,$name,$id,$sql)
	{
		$s = $sql;
		$r = $this->m_dbConnRoot->select($s);
		
		if($r<>"")
		{
			foreach($r as $t =>$z)
			{
				foreach($z as $m =>$g)
				{
					if($i==0)
					{
					$ee.= $g.",";
					}
				}
			}
		}
		$active = substr($ee,0,-1);
		$ww = explode(",",$active);
		
		
		$data = $this->m_dbConnRoot->select($query);
		if(!is_null($data))
		{
			$pp = 0;
			?>
            <table align="center" border="0">
            <tr>
            <?php
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
						if($pp%2==0)
						{
							echo '</tr><tr>';
							//$pp = 0;
						}
					?>
				<td width="20" align="center"><input type="checkbox" value="<?php echo $v;?>" name="<?php echo $name;?>" id="<?php echo $id;?><?php echo $pp;?>" <?php echo $s;?> disabled="disabled"/></td>					
					<?php
					}
					else
					{
						
					echo '<td width=170>'.$v.'<td>';
					}
					$i++;
				}
			$pp++;
			}
			?>
            </tr>
            </table>
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
			$res=$this->display_reg($rsas);
			return $res;
	}
	public function pgnation()
	{
		$sql1 = "select service_prd_reg_id,`full_name`,`photo`,`photo_thumb`,`age`,`dob`,`identy_mark`,`cur_resd_add`,`cur_con_1`,`cur_con_2`,`native_add`,`native_con_1`,`native_con_2`,`ref_name`,`ref_add`,`ref_con_1`,`ref_con_2`,`since`,`education`,`marry`,`father_name`,`father_occ`,`mother_name`,`mother_occ`,`hus_wife_name`,`hus_wife_occ`,`son_dou_name`,`son_dou_occ`,`other_name`,`other_occ` from service_prd_reg where status='Y'";
		
		$cntr = "select count(*) as cnt from service_prd_reg where status='Y'";
		
		$this->display_pg->sql1=$sql1;
		$this->display_pg->cntr1=$cntr;
		$this->display_pg->mainpg="service_prd_reg.php";
		$limit="5";
		$page=$_REQUEST['page'];
		$extra="";
		$res=$this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;
	}
	
	public function display_reg($res)
	{
		if($res<>"")
		{
			?>
            <table align="center" border="1">
            <tr>
            	<th>Edit</th>
                <th>Delete</th>
                
         		<th>Full Name</th>
                <th>Photo</th>
                <th>Category</th>
                <th>Age</th>
                <th>Date of Birth</th>
                <th>Indetification Marks</th>
                <th>Current Residence Address<br> & <br>Contact No.1<br>Contact No.2</th>
                <th>Permanent / Native Address<br> & <br>Contact No.1<br>Contact No.2</th>
                <th>Reference Name</th>
                <th>Reference Address<br> & <br>Contact No.1<br>Contact No.2</th>
                <th>Wroking in <br>Acme Since</th>
                <th>Education</th>
                <th>Married</th>
                
                <th>Father Name<br> & <br>Occupation</th>
                <th>Mother Name<br> & <br>Occupation</th>
                <th>Husband/Wife Name<br> & <br>Occupation</th>
                <th>Son/Daughter Name<br> & <br>Occupation</th>
                <th>Other 1 Name<br> & <br>Occupation</th>
                
                <th>Attached<br>Document</th>
            </tr>
            <?php
			foreach($res as $k => $v)
			{
			?>
            <tr align="center">
            	<td><a href="javascript:void(0);" onclick="service_prd_reg_edit(<?php echo $res[$k]['service_prd_reg_id']?>)"><img src="images/edit.gif" /></a></td>
                <td><a href="javascript:void(0);" onclick="getservice_prd_reg('delete-<?php echo $res[$k]['service_prd_reg_id']?>');"><img src="images/del.gif" /></a></td>
            	
            	<td><div style="width:160px;"><?php echo $res[$k]['full_name'];?></div></td>
                <td><a href="<?php echo $res[$k]['photo'];?>" target="_blank"><img src="<?php echo $res[$k]['photo_thumb'];?>" height="60" width="60"/></td>
                
                <td>
                <div style="overflow-y:scroll;overflow-x:hidden;width:180px; height:100px; border:solid #CCCCCC 1px;">
				<?php $get_reg_cat = $this->get_reg_cat($res[$k]['service_prd_reg_id']);?>
                </div>
                </td>
                
                <td><div style="width:70px;"><?php echo $res[$k]['age'];?> Year</div></td>
                
                <td><div style="width:100px;"><?php echo $res[$k]['dob'];?></div></td>
                <td><div style="overflow-y:scroll;overflow-x:hidden;width:160px; height:100px; border:solid #CCCCCC 1px;"><?php echo $res[$k]['identy_mark'];?></div></td>
                
                <td><div style="overflow-y:scroll;overflow-x:hidden;width:180px; height:100px; border:solid #CCCCCC 1px;"><?php echo $res[$k]['cur_resd_add'].'<br><br>'.$res[$k]['cur_con_1'].'<br>'.$res[$k]['cur_con_2'];?></div></td>
                
                <td><div style="overflow-y:scroll;overflow-x:hidden;width:180px; height:100px; border:solid #CCCCCC 1px;"><?php echo $res[$k]['native_add'].'<br><br>'.$res[$k]['native_con_1'].'<br>'.$res[$k]['native_con_2'];?></div></td>
                
                <td><div style="width:120px;"><?php echo $res[$k]['ref_name'];?></div></td>
                
                <td><div style="overflow-y:scroll;overflow-x:hidden;width:180px; height:100px; border:solid #CCCCCC 1px;"><?php echo $res[$k]['ref_add'].'<br><br>'.$res[$k]['ref_con_1'].'<br>'.$res[$k]['ref_con_2'];?></div></td>
                
                <td><div style="width:100px;"><?php echo $res[$k]['since'];?></div></td>
                <td><div style="width:100px;"><?php echo $res[$k]['education'];?></div></td>
                <td><div style="width:80px;"><?php echo $res[$k]['marry'];?></div></td>
                
                <td><div style="width:170px;"><?php echo $res[$k]['father_name'].'<br><br>'.$res[$k]['father_occ']?></div></td>
                <td><div style="width:170px;"><?php echo $res[$k]['mother_name'].'<br><br>'.$res[$k]['mother_occ']?></div></td>
                <td><div style="width:170px;"><?php echo $res[$k]['hus_wife_name'].'<br><br>'.$res[$k]['hus_wife_occ']?></div></td>
                <td><div style="width:170px;"><?php echo $res[$k]['son_dou_name'].'<br><br>'.$res[$k]['sun_dou_occ']?></div></td>
                <td><div style="width:170px;"><?php echo $res[$k]['other_name'].'<br><br>'.$res[$k]['other_occ']?></div></td>
                
                
                <td>
                <div style="overflow-y:scroll;overflow-x:hidden;width:180px; height:100px; border:solid #CCCCCC 1px;">
				<?php $get_reg_doc = $this->get_reg_doc($res[$k]['service_prd_reg_id']);?>
                </div>
                </td>
            </tr>
            <?php
			}
			?>
            </table>
			<?php
		}
		else
		{
			?>
            <table align="center" border="0">
            <tr>
            	<td align="center"><font color="#FF0000" size="2"><b>No Records Found.</b></font></td>
            </tr>
            </table>
            <?php	
		}
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
		$sql1 = "select service_prd_reg_id,`full_name`,`photo`,`age`,`dob`,`identy_mark`,`cur_resd_add`,`cur_con_1`,`cur_con_2`,`native_add`,`native_con_1`,`native_con_2`,`ref_name`,`ref_add`,`ref_con_1`,`ref_con_2`,`since`,`education`,`marry`,`father_name`,`father_occ`,`mother_name`,`mother_occ`,`hus_wife_name`,`hus_wife_occ`,`son_dou_name`,`son_dou_occ`,`other_name`,`other_occ` from service_prd_reg where service_prd_reg_id='".$_REQUEST['service_prd_regId']."'";
		$var=$this->m_dbConnRoot->select($sql1);
		return $var;
	}
	public function deleting()
	{
		$sql1 = "update service_prd_reg set status='N' where service_prd_reg_id='".$_REQUEST['service_prd_regId']."'";
		$this->m_dbConnRoot->update($sql1);
	}
	
	public function reg_edit()
	{
		$sql = "select * from service_prd_reg where service_prd_reg_id='".$_REQUEST['id']."' and status='Y'";	
		//echo $sql;			
		$res = $this->m_dbConnRoot->select($sql);		
		return $res;
	}
	
	public function soc_name($sid)
	{
		$sql = "select * from society where society_id='".$sid."' and status='Y'";
		$res = $this->m_dbConn->select($sql);	
		return $res[0]['society_name'];
		
	}
	public function soc_add($sid)
	{
		$sql = "select * from society where society_id='".$sid."' and status='Y'";
		$res1 = $this->m_dbConn->select($sql);	
		return $res1[0]['society_add'];
		
	}
	public function fetchUnits()
	{
		//$sql = 'SELECT `unit_no`, `unit_id` FROM `unit` WHERE `society_id` = "'.$_SESSION['society_id'].'"';
		$sql = 'SELECT unit.unit_no, unit.unit_id, member_main.owner_name FROM `unit` JOIN `member_main` on unit.unit_id = member_main.unit WHERE unit.society_id = "'.$_SESSION['society_id'].'"';	
		$result = $this->m_dbConn->select($sql);
		return $result;
	}
	
	public function fetchSelectedUnits($prd_id)
	{
		$sql = 'SELECT * FROM `service_prd_units` WHERE `service_prd_id` = "'.$prd_id.'" AND `society_id` = "'.$_SESSION['society_id'].'"';
		$result = $this->m_dbConnRoot->select($sql);
		return $result;	
	}
	
	public function fetchDocuments()
	{
		$sql = "select document_id,document from document where status='Y' order by document_id";
		$result = $this->m_dbConnRoot->select($sql);
		return $result;
	}
	
	public function fetchSelectedDocs($prd_id)
	{
		//$sql = 'select document_id from spr_document where service_prd_reg_id = '.$prd_id;
	$sql = 'SELECT spr_document.document_id, spr_document.attached_doc,spr_document.spr_document_id , document.document FROM `spr_document` JOIN `document` ON spr_document.document_id = document.document_id WHERE spr_document.service_prd_reg_id = '.$prd_id;		
		$result = $this->m_dbConnRoot->select($sql);
		return $result;	
	}
	
	public function fetchSelectedCategories($prd_id)
	{
		$sql = 'select spr_cat.cat_id, cat.cat from `spr_cat` JOIN `cat` ON spr_cat.cat_id = cat.cat_id where spr_cat.service_prd_reg_id = '.$prd_id;
		$result = $this->m_dbConnRoot->select($sql);
		return $result;
	}
	
	public function AllPrintCard($societyID,$ServiceID)
	{
		$serviceReqiestID=json_decode($ServiceID);
		
		 $sql = "select * from service_prd_reg where society_id='".$societyID."' and `service_prd_reg_id` IN (".implode(',', $serviceReqiestID).") and active='1' and status='Y'";	
		//echo $sql;			
		$res = $this->m_dbConnRoot->select($sql);		
		return $res;
	}
}
?>