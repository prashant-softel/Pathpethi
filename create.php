<?php
//include_once("includes/head_s.php");
include_once("classes/include/dbop.class.php");
?>
 
<?php
//echo "1";
include_once("classes/upload_index.class.php");

$obj_create_album = new show_album($m_dbConn,$m_dbConnRoot);

?>

<?php
 
 include_once ("classes/dbconst.class.php");
//print_r($_SESSION);
include_once( "classes/include/fetch_data.php");
$objFetchData = new FetchData($m_dbConn,$m_dbConnRoot);
$m_dbConnRoot = new dbop(true);
$obj_create=new show_album($m_dbConn,$m_dbConnRoot);

 
 
 ?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
<link rel="stylesheet" href="css/style_gallery.css">
<link rel="stylesheet" href="css/pagination.css">

<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/upload_index.js"></script>
    <script type="text/javascript" src="js/create.js"></script>
   
    
</head>

<body><br>
<!--<div class="panel panel-info" id="panel" style="display:block;">
        <div class="panel-heading" id="pageheader">Photo Gallery</div>-->
<div id="body">
<?php  include 'title_bar.php';?>
<div id="container">
<center><h3>Create Album</h3></center>
<center>

<form method="post" name="create_form"  action="process/upload_index.process.php" onSubmit="return valGroup();">
<br><br>
<div id="ErrorDiv3" style="display:block;font-weight:bold; color:#F00;font-size: 12px;"><?php echo $_REQUEST['error3'] ?></div><br>

<span style="font-family:sans-serif; font-size:13px;">Select Group </span><br/>
<select name="group" id="group" style="font-size:12px;">
<?php //echo $group_create = $obj_create->combobox("select group_id,group_name from `group` where status='Y' and (society_id = 0 OR society_id = " . $_SESSION['society_id'] . ") order by group_id desc",$_REQUEST['group_id']); ?>
<?php echo $group_create = $obj_create->combobox("select g.`group_id`, g.`group_name` from `group` as g JOIN `soc_group` as s ON g.`group_id` = s.`group_id` Join `society` as c on s.`society_id` = c.`society_id`  where s.`status`='Y' and g.`status`='Y' and  c.`client_id` = '" .$_SESSION['society_client_id'] . "' and c.society_id='".$_SESSION['society_id']."'", $_REQUEST['group_id']); ?>

</select>
<?php 
if(isset($_REQUEST['name']))
 	{
	 $name=$_REQUEST['name'];
	 
	 if(empty($name))
		 {
		echo "Please Enter the album Name<br><br>";	 
		} 
		else
		{ //echo"insert into album('id','name') values('$name')";
			//mysqli_query("INSERT INTO `album`(`id`, `name`) VALUES ('','$name')");
			//$data=$this->m_dbConnRoot->insert($sqldata);
			echo "Album create successfully <br><br>";
			}
	}

	
?>
<br><br>
<span style="font-family:sans-serif; font-size:13px;">Album Name</span> <br> <input type="text" id="name" name="name"/ style="font-size:12px;"><br><br><br>
<input type="submit" value="Create" name="create"  id="create" style="font-size: 15px; background-color: #337ab7; color: #fff;" />
</form>
</center>
<!--</div>-->
</div>
</div>

</body>
</html>
