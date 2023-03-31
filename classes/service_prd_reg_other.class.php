<?php if(!isset($_SESSION)){ session_start(); }
//include_once("include/dbop.class.php");
include_once("include/display_table.class.php");

class service_prd_reg_other extends dbop
{
	public $actionPage = "../service_prd_reg_other.php";
	public $m_dbConn;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
$this->display_pg=new display_table($this->m_dbConn);
		//dbop::__construct();
	}
	public function startProcess()
	{
		$errorExists=0;
		if($_REQUEST['insert']=='Register here' && $errorExists==0)
		{
			/*
			$sql = "select count(*)as cnt from service_prd_reg where full_name='".addslashes(trim(ucwords($_POST['full_name'])))."' and status='Y'";
			$res = $this->m_dbConn->select($sql);
			if($res[0]['cnt']==0)
			{
			*/	
				$exe_photo_main = strtolower(substr($_FILES['photo']['name'],-4));
					
				if($exe_photo_main=='.jpg' || $exe_photo_main=='.png' || $exe_photo_main=='.jpeg' || $exe_photo_main=='.bmp' || $exe_photo_main=='.gif')
				{
				$photo_new_path = $this->up_photo($_FILES['photo']['name'],$_FILES["photo"]["tmp_name"],'../upload/main');
				
				////////////////////////////////////////
				$thumbWidth_index  = 100;
				$thumbHeight_index = 100;
				
				$pathToThumbs_index = '../upload/thumb/';
				$image_name = time().'_thumb_'.str_replace(' ','-',$_FILES['photo']['name']);
				
				$thumb_path = $this->thumb_photo($thumbWidth_index,$thumbHeight_index,$pathToThumbs_index,$photo_new_path,$exe_photo_main,$image_name); 
				////////////////////////////////////////
				
				}
				else 
				{
					return 'Invalid File Type';
				}
				
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					
			$insert_query = "insert into service_prd_reg
							(`society_id`,`full_name`,`photo`,`photo_thumb`,`age`,`dob`,`identy_mark`,`cur_resd_add`,`cur_con_1`,`cur_con_2`,`native_add`,
							`native_con_1`,`native_con_2`,`ref_name`,`ref_add`,`ref_con_1`,`ref_con_2`,`since`,`education`,`marry`,`father_name`,
							`father_occ`,`mother_name`,`mother_occ`,`hus_wife_name`,`hus_wife_occ`,`son_dou_name`,`son_dou_occ`,`other_name`,`other_occ`) 
							 
							 values 
							('".$_SESSION['society_id']."','".addslashes(ucwords(trim($_POST['full_name'])))."','".$photo_new_path."','".$thumb_path."','".$_POST['age']."','".$_POST['dob']."','".addslashes(ucwords(trim($_POST['identy_mark'])))."','".addslashes(ucwords(trim($_POST['cur_resd_add'])))."','".$_POST['cur_con_1']."','".$_POST['cur_con_2']."','".addslashes(ucwords(trim($_POST['native_add'])))."',
							
							'".$_POST['native_con_1']."','".$_POST['native_con_2']."','".addslashes(ucwords(trim($_POST['ref_name'])))."','".addslashes(ucwords(trim($_POST['ref_add'])))."','".$_POST['ref_con_1']."','".$_POST['ref_con_2']."','".$_POST['since']."','".addslashes(ucwords(trim($_POST['education'])))."','".$_POST['marry']."','".addslashes(ucwords(trim($_POST['father_name'])))."',
							
							'".addslashes(ucwords(trim($_POST['father_occ'])))."','".addslashes(ucwords(trim($_POST['mother_name'])))."','".addslashes(ucwords(trim($_POST['mother_occ'])))."','".addslashes(ucwords(trim($_POST['hus_wife_name'])))."','".addslashes(ucwords(trim($_POST['hus_wife_occ'])))."','".addslashes(ucwords(trim($_POST['son_dou_name'])))."','".addslashes(ucwords(trim($_POST['son_dou_occ'])))."','".addslashes(ucwords(trim($_POST['other_name'])))."','".addslashes(ucwords(trim($_POST['other_occ'])))."')";
			$data = $this->m_dbConn->insert($insert_query);
			
			
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
			?>
            <script>window.location.href = '../service_prd_reg_view.php?srm&add&idd=<?php echo time();?>';</script>
            <?php
			//return "Insert";
		}		
		/*
			}
			else
			{
				return "Already Exist";	
			}
		*/
	}
	
	public function startProcess1()
	{
		if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			$up_query = "update service_prd_reg set `full_name`='".addslashes(ucwords(trim($_POST['full_name'])))."',`photo`='".$_POST['photo']."',`age`='".$_POST['age']."',`dob`='".$_POST['dob']."',`identy_mark`='".addslashes(ucwords(trim($_POST['identy_mark'])))."',`cur_resd_add`='".addslashes(ucwords(trim($_POST['cur_resd_add'])))."',`cur_con_1`='".$_POST['cur_con_1']."',`cur_con_2`='".$_POST['cur_con_2']."',`native_add`='".addslashes(ucwords(trim($_POST['native_add'])))."',`native_con_1`='".$_POST['native_con_1']."',`native_con_2`='".$_POST['native_con_2']."',`ref_name`='".addslashes(ucwords(trim($_POST['ref_name'])))."',`ref_add`='".addslashes(ucwords(trim($_POST['ref_add'])))."',`ref_con_1`='".$_POST['ref_con_1']."',`ref_con_2`='".$_POST['ref_con_2']."',`since`='".$_POST['since']."',`education`='".addslashes(ucwords(trim($_POST['education'])))."',`marry`='".$_POST['marry']."',`father_name`='".addslashes(ucwords(trim($_POST['father_name'])))."',`father_occ`='".addslashes(ucwords(trim($_POST['father_occ'])))."',`mother_name`='".addslashes(ucwords(trim($_POST['mother_name'])))."',`mother_occ`='".addslashes(ucwords(trim($_POST['mother_occ'])))."',`hus_wife_name`='".addslashes(ucwords(trim($_POST['hus_wife_name'])))."',`hus_wife_occ`='".addslashes(ucwords(trim($_POST['hus_wife_occ'])))."',`son_dou_name`='".addslashes(ucwords(trim($_POST['son_dou_name'])))."',`son_dou_occ`='".addslashes(ucwords(trim($_POST['son_dou_occ'])))."',`other_name`='".addslashes(ucwords(trim($_POST['other_name'])))."',`other_occ`='".addslashes(ucwords(trim($_POST['other_occ'])))."' where service_prd_reg_id='".$_POST['id']."'";
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
			<script> window.location.href = '../service_prd_reg.php?nul=nul'; </script>
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
			<script> window.location.href = '../service_prd_reg.php?nul=nul'; </script>
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
			<script> window.location.href = '../service_prd_reg.php?nul=nul'; </script>
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
			<script> window.location.href = '../service_prd_reg.php?nul=nul'; </script>
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
		$sql1 = "select sp.service_prd_reg_id, sp.full_name, sp.photo, sp.photo_thumb, sp.age, s.society_id, s.society_name, sc.spr_cat_id, sc.cat_id , c.cat from service_prd_reg as sp, society as s, spr_cat as sc, cat as c where sp.society_id=s.society_id and sp.service_prd_reg_id=sc.service_prd_reg_id and sc.cat_id=c.cat_id and sp.status='Y' and s.status='Y' and sc.status='Y' and c.status='Y' and s.society_id!='".$_SESSION['society_id']."'";
		
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
			$sql1 .= " and sp.full_name like '%".addslashes($_REQUEST['key'])."%'";
		}
		$sql1 .= ' group by sp.service_prd_reg_id order by s.society_id,sp.full_name';
		
		
		
		$cntr = "select count(*) as cnt from service_prd_reg as sp, society as s, spr_cat as sc, cat as c where sp.society_id=s.society_id and sp.service_prd_reg_id=sc.service_prd_reg_id and sc.cat_id=c.cat_id and sp.status='Y' and s.status='Y' and sc.status='Y' and c.status='Y' and s.society_id!='".$_SESSION['society_id']."'";
		
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
			$cntr .= " and sp.full_name like '%".addslashes($_REQUEST['key'])."%'";
		}
		$cntr .= ' group by sp.service_prd_reg_id order by s.society_id,sp.full_name';
		
		
		
		$this->display_pg->sql1=$sql1;
		$this->display_pg->cntr1=$cntr;
		$this->display_pg->mainpg="service_prd_reg.php";
		$limit = "30";
		$page = $_REQUEST['page'];
		
		$extra = "&srm";
		
		$res=$this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;
	}
	
	
	########################################################################################################################################
	########################################################################################################################################

	public function display_reg_short($res)
	{
		if($res<>"")
		{
			?>
            <table align="center" border="0">
            <tr height="30" bgcolor="#CCCCCC">
            	
                <th width="180">Other Society Name</th>
                
                
            	<th width="150">Photo</th>
                <th width="150">Full Name</th>
                <th width="60">Age</th>
                <th>Category</th>
                <th width="60">View</th>
                <th width="60">Print</th>
                
                <!--
            	<th width="60">Edit</th>
                <th width="70">Delete</th>
                -->
            </tr>
            <?php
			foreach($res as $k => $v)
			{
			?>
            <tr height="25" bgcolor="#BDD8F4" align="center">
            	
            	<td align="center"><?php echo $res[$k]['society_name'];?></td>
            	
                <td>
                    <a href="<?php echo $res[$k]['photo'];?>" class="fancybox">
                    <img src="<?php echo $res[$k]['photo_thumb'];?>"/>
                    </a>
                </td>
                <td>
                	<a href="reg_form_print_new.php?id=<?php echo $res[$k]['service_prd_reg_id']?>&other&srm" style="color:#00F;"><?php echo $res[$k]['full_name'];?></a>
                </td>
                
                <td><?php echo $res[$k]['age'];?> Year</td>
                
                <td>
                    <div style="overflow-y:scroll;overflow-x:hidden;width:180px; height:140px; border:solid #CCCCCC 1px;">
                    <?php $get_reg_cat = $this->get_reg_cat($res[$k]['service_prd_reg_id']);?>
                    </div>
                </td>
                
               	<td>
                	<a href="reg_form_print_new.php?id=<?php echo $res[$k]['service_prd_reg_id']?>&other&srm" style="color:#00F;"><img src="../images/view.jpg" width="20" width="20" /></a>
                </td>
                
                <td>
                	<a href="reg_form_print1.php?id=<?php echo $res[$k]['service_prd_reg_id']?>&srm" target="_blank"><img src="../images/print.png" width="35" width="35" /></a>
                </td>
                
                <!--
                <td>
                	<a href="javascript:void(0);" onclick="service_prd_reg_edit(<?php echo $res[$k]['service_prd_reg_id']?>)"><img src="../images/edit.gif" /></a>
                </td>
                
                <td>
					<?php if($this->chk_delete_perm_admin()==1){?>
                    <a href="javascript:void(0);" onclick="getservice_prd_reg('delete-<?php echo $res[$k]['service_prd_reg_id']?>');"><img src="../images/del.gif" /></a>
                    <?php }else{?>
                    <a href="del_control_admin.php?prm" target="_blank" style="text-decoration:none;"><font color=#FF0000 style='font-size:10px;'><b>Not Allowed</b></font></a>
                    <?php }?>
                </td>
                -->
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
		$res = $this->m_dbConn->select($sql);
		
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
		$res = $this->m_dbConn->select($sql);
		
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
		$var=$this->m_dbConn->select($sql1);
		return $var;
	}
	public function deleting()
	{
		$sql1 = "update service_prd_reg set status='N' where service_prd_reg_id='".$_REQUEST['service_prd_regId']."'";
		$this->m_dbConn->update($sql1);
		
		$sql = "update spr_cat set status='N' where service_prd_reg_id='".$_REQUEST['service_prd_regId']."'";
		$res = $this->m_dbConn->update($sql);
		
		$sql2 = "update spr_document set status='N' where service_prd_reg_id='".$_REQUEST['service_prd_regId']."'";
		$res2 = $this->m_dbConn->update($sql2);
	}
	
	public function reg_edit()
	{
		$sql = "select * from service_prd_reg where service_prd_reg_id='".$_REQUEST['id']."' and status='Y'";
		$res = $this->m_dbConn->select($sql);	
		return $res;
	}
}
?>