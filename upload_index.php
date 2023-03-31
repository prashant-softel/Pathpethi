<?php //include_once("includes/head_s.php"); ?>
<?php include_once("classes/include/dbop.class.php");?>

<?php
include_once("classes/upload_index.class.php");
$dbConn = new dbop();
$m_dbConnRoot = new dbop(true);
$obj_show_album = new show_album($m_dbConn,$m_dbConnRoot);
?>
<html>
<head>
<link rel="stylesheet" href="css/style_gallery.css"/>
<link rel="stylesheet" type="text/css" href="css/pagination.css">
	<script type="text/javascript" src="js/gallery_upload.js"></script>
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/upload_index.js"></script>
    <script type="text/javascript" src="js/ajax_new.js"></script>
<style>    
    .delete_album{
	box-shadow:none;
	-webkit-box-shadow:none;
}
</style>

    
</head>
<body>


<form method="post" enctype="multipart/form-data" action="process/upload_index.process.php" onSubmit="return val();">
 <br>
<!--<div class="panel panel-info" id="panel" style="display:block;">
        <div class="panel-heading" id="pageheader">Photo Gallery </div>-->
        <div id="ErrorDiv3" style="display:block;font-weight:bold; color:#F00;font-size: 12px;"><?php echo $_REQUEST['error'] ?></div><br>

 <?php
	if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_ADMIN_MEMBER || $_SESSION['role']==ROLE_SUPER_ADMIN))
	      {?>

<div id="body">
	<?php include 'title_bar.php';?>
	<div id="container">
    <?php
	//echo "SELECT `id`,`name` FROM `album`"; 
     
 	//$queryFolder="SELECT `id`,`name` FROM `album`";
	$queryFolder = "SELECT a.`id`, a.`name`,a.`folder` FROM `album` as a JOIN `soc_group` as g ON a.group_id = g.group_id where g.society_id = '" . $_SESSION['society_id'] . "' ";
	if(isset($_REQUEST['grp']))
	{
		$queryFolder .= "and a.group_id = '".$_REQUEST['grp']."'";
	}
	//echo $queryFolder;
	 $res = $m_dbConnRoot->select($queryFolder);
	 //print_r($res);
	for($i=0;$i<sizeof ($res);$i++)
	{
		 $albumsql ="SELECT count(*) as cnt FROM `photos` WHERE `album_id`='".$res[$i]['id']."' ";
			$resCount = $m_dbConnRoot->select($albumsql);

		 $album_id=$res[$i]['id'];
		 $album_name=$res[$i]['name'];
		 $folder_name=$res[$i]['folder'];
		 
		 
		 	$query1="SELECT `url` FROM `photos` WHERE `album_id`='".$res[$i]['id']."'  order by `cover` desc";
			$res1 = $m_dbConnRoot->select($query1);
			
			//$run1= mysqli_fetch_array($res);
			$pic=$res1[0]['url'];
			//$foldername=$resFolder[0]['name'];
			
		/*if($album_id=='id')	
		{
$sqldata="delete from `album` where album_id='".$_REQUEST['id']."'";
				$data=$m_dbConnRoot->delete($sqldata);
		}
	*/
	
	?>
    
    	
    <a href="#" onClick="tabImgClicked('view.php?id=<?php echo $album_id?>&share=1')">
    <div id="view_box" style="width:170px; height:170px;">
    <?php if($pic<>'')
	{?>
    <center><img src="uploads/<?php echo $folder_name.'/thumb/'.$pic;?>"  style= "height: 115px; width: auto "></center>
    
	<?php }
	else
	{?>
    <center><img src="uploads/no-photo-grey_1x.png" style="width:auto; height:auto;"></center>
   <?php  }?>
   <br>
   <div style="float:left;font-size: 12px;width: 115px; text-align: left;"> <b><?php echo substr($album_name,0,20);?></b></div><div style="float:right;font-size: 12px;">
   <b>
                <div style="float:right;margin-right: 12px;margin-top:-3px">
                <div  style="font-size:10px;font-size:1.75vw;width: 20px;height: 21px;">
                            <i class="fa  fa-camera " style="font-size:20px;"></i>
                            </div>
                            </div>
                            <div style="float: right;margin-top: -12px;margin-right: -34px;"><img src="images/icon.png" style="float: right;width: 25px;height: 25px;margin-top: -1px;"><b style="float: right;font-size: 10px;margin-top: 8%;margin-right: -50%;;color:#fff;">
   <?php echo $resCount[0]['cnt'];?></b></div> </div>   
<br><br>
<!--<a  href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($url); ?>"><img src="img/facebook_share.png" style="height: 20px; width: 70px;float: right;/"></a>
-->			
    <div></a>
    <?php
	if(isset($_REQUEST['album_del']) && $_REQUEST['album_del'] == 1)
	{
		?>
		<input type="checkbox"  class="delete_album" id="delete'<?php echo $album_id;?>'" value="<?php echo $album_id;?>" name="delete" style="visibility:visible; float:right; width: 1.5em; height: 1.5em;margin-top: -12px;margin-right: 12px;"/>
        
        <?php
	}
	else
	{
		?>
        	<input type="checkbox"  class="delete_album" id="delete" value="<?php echo $album_id;?>" name="delete" style="visibility:hidden; float:right; width: 1.5em; height: 1.5em; margin-top: -12px;margin-right: 12px;"/>
        <?php
	}
?>
    </div>

    </div>
    
   

    <?php }?>
<div class="clear"></div>
<?php }?>
<br><br><br>
 <?php
	if(isset($_REQUEST['album_del']) && $_REQUEST['album_del'] == 1)
	{
		?>

<center><input type="button" value="Delete Album" id="del" name="del" style="visibility:visible; font-size: 15px;  background-color: #337ab7; color: #fff;" onClick="DeleteAlbum();"/></center>
 <?php
	}
	else
	{
		?>
       <center><input type="button" value="Delete Album" id="del" name="del" style="visibility:hidden; font-size: 15px;  background-color: #337ab7; color: #fff;" onClick=" DeleteAlbum();"></center>
		<?php
	}
?>

<!--</div>-->
</div>
</div></form>
<body>
</html>
<?php /*?><?php include_once "../includes/foot.php"; ?><?php */?>