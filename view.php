
<?php //include_once "ses_set_s.php";
//include_once("includes/head_s.php"); 
include_once("classes/include/dbop.class.php");
include_once("classes/upload_index.class.php");?> 
<?php
$m_dbConn = new dbop();
$m_dbConnRoot = new dbop(true);
$obj_show=new show_album($m_dbConn,$m_dbConnRoot);
$baseDir = dirname( dirname(__FILE__) );
$fburl=$baseDir.'\beta\uploads\\'.$foldername.'\\'.$url;  
//C:\wamp\www\beta\uploads\\               

?>
<!doctype html>
<html>
<head>

<script type="text/javascript" src="js/gallery_upload.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/ajax_new.js"></script>
<link rel="stylesheet" href="css/style_gallery.css">
<link rel="stylesheet" type="text/css" href="css/pagination.css">
<meta charset="utf-8">
<title>Untitled Document</title>
<style>
.radioButton{
	box-shadow:none;
	-webkit-box-shadow:none;
	float:right;
}
.setcover
{
	box-shadow:none;
	-webkit-box-shadow:none;
	float:right;
}
.sendphoto
{
	box-shadow:none;
	-webkit-box-shadow:none;
	float:right;
}
</style>

</head>

<body>
<!--<div class="panel panel-info" id="panel" style="display:block;">
        <div class="panel-heading" id="pageheader">Photo Gallery</div>
-->

<div id="body">
<?php include "title_bar.php";?>
<br>
<?php $attr = 'selected="selected"'; ?>
<center><div id="container"><p style="float:left; margin-left: 120px; margin-top: 4px; font-size: 13px;font-weight: bold;font-family:sans-serif;">Please Select Action:</p><p style="float:left;margin-left: 10px; margin-top: 0px;"><select name="act" id="action" onchange="myFunction()" style="font-size:12px;"/>
<option value="" >Select</option>
<option value="add_photo" <?php echo $_REQUEST['action'] == 'add_photo' ? $attr : ''; ?>>Add Photo</option>
<option value="cover" <?php echo $_REQUEST['action'] == 'cover' ? $attr : ''; ?>>Album Cover</option>
<option value="del_photo" <?php echo $_REQUEST['action'] == 'del_photo' ? $attr : ''; ?>> Delete Photo</option>
<option value="del" <?php echo $_REQUEST['action'] == 'del' ? $attr : ''; ?>>Delete Album</option>
<option value="send" <?php echo $_REQUEST['action'] == 'send' ? $attr : ''; ?>>Send To Homepage</option>

</select></p></div></center>
<div id="container">

<p id="upload"></p>


<?php

 $album_id=$_GET['id'];
 ?>
	<input type="hidden" id="album_id" name="album_id" value="<?php echo $album_id; ?>" /> 
    <?php
 $queryFolder = "SELECT `name`,`folder` FROM `album` WHERE `id`='$album_id'";
  		//echo $queryFolder;
		$resFolder = $m_dbConnRoot->select($queryFolder);
 
		$foldername=$resFolder[0]['folder'];
		$albumname=$resFolder[0]['name'];
		
 $query="SELECT `url`,`id` FROM `photos` WHERE `album_id`='$album_id'";
 $res = $m_dbConnRoot->select($query);
 ?>
 <br>
<div style="font-size:15px; font-weight:bold;margin-top: -30px;color: blue;float: left; margin-left: 230px;">Album Name:&nbsp;&nbsp;&nbsp;<?php echo $albumname?><br></div><br>

<?php
for($i=0;$i<sizeof( $res);$i++)

 //while($run=$res)
 {
	 
	 $photo_id=$res[$i]['id'];
	// echo  $photo_id;
	 $url=$res[$i]['url']; 
	// $res = $this->m_dbConnRoot->select($query);

?>

<!--<input type="hidden" id="show" name="show" value="<?php //echo $url;?>"/>
-->

<div id="view_box" style="width:170px; height:155px;">

<center><img src="uploads/<?php echo $foldername.'/thumb/'.$url ?>" style= "height: 112px; width: auto;"/></center>


<br>


<?php
if(isset($_REQUEST['share']) && $_REQUEST['share'] == 1)
	{ //echo $photo_id;
		?>
<a  class ='share_button' id="share_button" onClick="sharephoto('<?php echo '/uploads/'.$foldername.'/'.$url; ?>') "  data-folder = "<?php echo $foldername; ?>" data-img = "<?php echo $url; ?>"  href="#" >
<img src="img/facebook_share.png" style="height: 20px; width: 70px;float: right;margin-top: -5px;"/></a>
<?php }?>
<!--<a  href="https://www.facebook.com/sharer/sharer.php?id=<?php// echo $fburl; ?>'"><img src="img/facebook_share.png" style="height: 20px; width: 70px;float: right;"/></a>
	-->		
            
            	<!--------------------------Album Cover--------------------------------------->
<?php
	if(isset($_REQUEST['radio']) && $_REQUEST['radio'] == 1)
	{ //echo $photo_id;
		?>
		<input type="radio"  class="setcover" id="cover" value="<?php echo $photo_id;?>" name="cover" style="visibility:visible; float:right; width: 1.5em; height: 1.5em; margin-top: -7px;"/>
        
        <?php
	}
	else
	{
		?>
        	<input type="radio"    class="setcover" id="cover" value="<?php echo $photo_id;?>" name="cover" style="visibility:hidden; float:right; width: 1.5em; height: 1.5em; margin-top: -7px;"/>
            
        <?php
	}
?>



							<!--<-------------------Delete Photo------------------->
<?php
	if(isset($_REQUEST['photo']) && $_REQUEST['photo'] == 1)
	{
		?>
        
		<input type="checkbox"  class="radioButton" id="del<?php echo $id;?>" value="<?php echo $photo_id;?>" name="del" style="visibility:visible; float: right; margin-right: -10px; width: 1.5em; height: 1.5em; margin-top: -1px;"/>
        
        <?php
	}
	else
	{
		?>
        	<input type="checkbox" class="radioButton" id="del" value="<?php echo $photo_id;?>" name="del" style="visibility:hidden;float: right; margin-right: -10px; width: 1.5em; height: 1.5em; margin-top: -1px;"/>
        <?php
	}
?>

			<!------------------------------send to Homepage--------------------------------------->


<?php
	if(isset($_REQUEST['check']) && $_REQUEST['check'] == 1)
	{
		?>
        
		<input type="checkbox"  class="sendphoto" id="send" value="<?php echo $photo_id;?>" name="cover" style="visibility:visible; float:right;float: right; margin-right: -10px; width: 1.5em; height: 1.5em; margin-top: -1px;"/>
        
        <?php
	}
	else
	{
		?>
        	<input type="checkbox" class="sendphoto" id="send" value="<?php echo $photo_id;?>" name="cover" style="visibility:hidden; float:right;float: right; margin-right: -10px; width: 1.5em; height: 1.5em; margin-top: -1px;"/>
        <?php
	}
?>




</div>

<?php }?>
<div class="clear"></div>
<input type="hidden" name="pg" id="pg" value="1" />
<br><br><br>

			<!-----------------------save buttom for Add Album cover-------------------------->
<?php
	if(isset($_REQUEST['radio']) && $_REQUEST['radio'] == 1)
	{
		?>

<center><input type="submit" value="SAVE" id="save" name="save" style="visibility:visible; font-size:15px; background-color: #337ab7; color: #fff;" onClick="SetAlbumCover();"></center>
 <?php
	}
	else
	{
		?>
       <center><input type="submit" value="SAVE" id="save" name="save" style="visibility:hidden; font-size: 15px; background-color: #337ab7; color: #fff;"  onClick="SetAlbumCover();"></center>
		<?php
	}
	
?>

<!------------------------------send button for send to homepage--------------------->
<?php
	if(isset($_REQUEST['check']) && $_REQUEST['check'] == 1)
	{
		?>

<center><input type="submit" value="Send To Homepage" id="send" name="send" style="visibility:visible; font-size: 15px; background-color: #337ab7; color: #fff;" onClick="SetPhotoToHomepage();" ></center>
 <?php
	}
	else
	{
		?>
       <center><input type="submit" value="Send To Homepage" id="send" name="send" style="visibility:hidden; font-size: 15px; background-color: #337ab7; color: #fff;" onClick="SetPhotoToHomepage();"></center>
		<?php
	}
?>




<!-----------------------------delete from album photo-------------------------------->
<?php
	if(isset($_REQUEST['photo']) && $_REQUEST['photo'] == 1)
	{
		?>

<center><input type="submit" value="Delete" id="del" name="del" style="visibility:visible; font-size: 15px; background-color: #337ab7; color: #fff;" onClick="DeletePhoto();"/></center>
 <?php
	}
	else
	{
		?>
       <center><input type="submit" value="Delete" id="del" name="del" style="visibility:hidden; font-size: 15px; background-color: #337ab7; color: #fff;" onClick="DeletePhoto();"></center>
		<?php
	}
?>


<!--</div>-->

</div>
</body>
</html>