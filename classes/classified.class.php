
<?php
include_once ("dbconst.class.php");
include_once("include/dbop.class.php");
include_once("include/display_table.class.php");

class classified 
{
	public $actionPage = "../classified.php";
	//public $m_dbConn;
	public $m_dbConnRoot;
	public $table='<table>';
	public $table1='<table>';
	public $errormsg;
	public $errormsg1;
	function __construct($dbConnRoot)
	{
		$this->display_pg=new display_table();
		//$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;

		
		//dbop::__construct();
	}

	public function startProcess()
	{
		$errorExists = 0;
		$return_value="";
print_r($_REQUEST);
		if($_POST['insert']=='Submit' && $errorExists==0)
		{   //echo "hello";
			$file_type=$_FILES['img']['type'];
			$file_size=$_FILES['img']['size'];
			$file_tmp=$_FILES['img']['tmp_name'];
			list($txt, $ext) = explode(".", $file);
			$randon_name = $file.".".$ext;
			$kaboom = explode(".", $_FILES['img']['name']); // Split file name into an array using the dot
			$fileExt = end($kaboom);
			$random_name= rand();
			//print_r($_FILES);
			if($_FILES["img"]<>'')
			{
			if ($_FILES["img"]["size"] > 10240*1024) 
			{
				 $error="Sorry, your file is too large.";
				 $this->table .= "Sorry, your file is too large.";
			}
			else if (($_FILES["img"]["type"] == "image/gif") || 
					($_FILES["img"]["type"] == "image/jpeg") || 
					($_FILES["img"]["type"] == "image/png") || 
					($_FILES["img"]["type"] == "image/pjpeg")) 
			{
				//echo "2";
		
				if ($_FILES["img"]["type"] == "image/jpeg")
				{ //echo"jpeg type";
					$url =$random_name.'.'.$fileExt;
				}
				else if($_FILES["img"]["type"] == "image/png")
				{//echo"png type";
					$url =$random_name.'.'.$fileExt;
				}
				else if ($_FILES["img"]["type"] == "image/gif")
				{
					$url =$random_name.'.'.$fileExt;
				}
		//echo $url = '../ads/'.$url;
			
			move_uploaded_file($file_tmp, '../ads/'.$random_name.'.'.$fileExt);
			
			}
		
			$insert_query="insert into classified (`ad_title`,`desp`,`post_date`,`act_date`,`exp_date`,`cat_type`,`img`) values ('".$_POST['ad_title']."','".$_POST['desp']."','".date('Y-m-d')."','".getDBFormatDate($_POST['act_date'])."','".getDBFormatDate($_POST['exp_date'])."','".$_POST['cat_type']."','$random_name.$fileExt')";
			echo $insert_query;		
			$data = $this->m_dbConnRoot->insert($insert_query);
			$return_value= "Insert";
		}
		else
		{
			//$error="Uploaded image should be jpg or gif or png";
			//return $errString;
		}
		
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			$up_query="update classified set `ad_title`='".$_POST['ad_title']."',`desp`='".$_POST['desp']."',`exp_date`='".$_POST['exp_date']."',`img`='".$_POST['img']."' where id='".$_POST['id']."'";
			$data = $this->m_dbConnRoot->insert($up_query);
			$return_value="Update";
		}
		
	$this->table .='</table>';
	$this->table .='</table>';
	$this->errormsg = $this->table;
	$this->errormsg1 = $this->table1;
	
	if($error <> '')
	{
		
	return $error;	
	}
		
	return $return_value;
	
	}				
			
	
	public function combobox($query)
	{
	}
	public function display1($rsas)
	{
		$thheader = array('ad_title','desp','post_date','exp_date','img');
		$this->display_pg->edit		= "getclassified";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "classified.php";

		$res = $this->display_pg->display_new($rsas);
		return $res;
	}
	public function pgnation()
	{
		$sql1 = "select id,`ad_title`,`desp`,`post_date`,`exp_date`,`img` from classified where status='Y'";
		$cntr = "select count(status) as cnt from classified where status='Y'";

		//$this->display_pg->sql1		= $sql1;
		//$this->display_pg->cntr1	= $cntr;
		$this->display_pg->mainpg	= "classified.php";

		$limit	= "50";
		$page	= $_REQUEST['page'];
		$extra	= "";

		$res	= $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;
	}
	public function selecting()
	{
		$sql = "select id,`ad_title`,`desp`,`post_date`,`exp_date`,`img` from classified where id='".$_REQUEST['classifiedId']."'";
		$res = $this->select($sql);
		return $res;
	}
	public function deleting()
	{
		$sql = "update classified set status='N' where id='".$_REQUEST['classifiedId']."'";
		$res = $this->update($sql);
	}
}


////    image 
?>