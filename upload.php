
<?php include_once "ses_set_s.php";
//include_once("includes/head_s.php"); 
include_once("classes/include/dbop.class.php");
include_once("classes/upload_index.class.php");?> 
<?php
$m_dbConnRoot = new dbop(true);
$obj_upload=new show_album($m_dbConn,$m_dbConnRoot);

?>
<!doctype html>
<html>
<head>
<script tytpe="text/javascript" src="js/upload.js"></script>

<meta charset="utf-8">
<title>Untitled Document</title>
<link rel="stylesheet" href="css/style_gallery.css">
</head>

<body>
<br>
<!--<div class="panel panel-info" id="panel" style="display:block;">
        <div class="panel-heading" id="pageheader">Photo Gallery </div>-->

<div id="body">
<?php include 'title_bar.php';?>
<div id="container">
<center><h3>Upload Photos</h3></center>
<center>

<br>
<form enctype="multipart/form-data" method="post" action="process/upload_index.process.php" onSubmit="return valTest();" >
<table>
<?php
if(isset($_REQUEST["ShowData"]))
			{
		?>
				<tr height="30"><td colspan="4" align="center"><font color="red" style="font-size:12px;"><b id="error"><?php echo $_REQUEST["ShowData"]; ?></b></font></td></tr>
		<?php }?>
</table>            

<input type="hidden" name="pg" id="pg" value="2" />
<br><br>
<div id="ErrorDiv2" style="display:block;font-weight:bold; color:#F00;font-size: 12px;"><?php echo $_REQUEST['error2'] ?></div><br>
<span style="font-family:sans-serif; font-size:13px;">Select Album</span><br>
<select name="album" id="album" style="font-size:12px;">
<!--select `id`,`name` from `album` where status='Y' order by id desc ", $_REQUEST['id'] -->
<?php echo $album_select = $obj_upload->combobox1 ("SELECT a.`id`, a.`name` FROM `album` as a JOIN `soc_group` as g ON a.group_id = g.group_id where g.society_id = '" . $_SESSION['society_id'] . "'", $_REQUEST['id']); ?>
</select>
<br><br>
<span style="font-family:sans-serif; font-size:13px;">Select Photo </span><br>
<input type="file" name="pic[]" multiple id="img" style="font-size:12px;"/ >
<!--<br><br><p style="font-size:12px; font-family:sans-serif; color:#F00;">Maximum Upload File Size 1MB.</p> -->
<br>
<input type="submit" name="upload" value="Upload" style="font-size: 15px; background-color: #337ab7; color: #fff;" />
<br><br>
<!--<div class="message">
                    	<?php
                    		/*if($_REQUEST['upload']){
                        		if ($error) {*/
                            		?>
                            		<label class="error"><?php //echo $error; ?></label>
                        <?php/*
                            		}
                        	}*/
                    	?>
                	</div>-->
		
<!--<div id="ErrorDiv" style="display:block;font-weight:bold;color:#F00;"><?php// echo $_REQUEST['error'] ?></div>
<div id="ErrorDiv1" style="display:block;font-weight:bold; color:#81b441;"><?php //echo $_REQUEST['error1'] ?></div>
<div id="ErrorDiv2" style="display:block;font-weight:bold; color:#81b441;"><?php //echo $_REQUEST['error2'] ?></div>
-->
<input type="hidden" id="album_id" name="album_id" value="<?php echo $folder; ?>" />
<script> alert("in form"); </script>
</form>
</center>
</div>
<!--</div>-->
</body>
</html>