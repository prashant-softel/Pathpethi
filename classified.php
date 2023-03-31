
<?php //include_once "ses_set_s.php";
include_once("includes/head_s.php"); 
include_once ("classes/dbconst.class.php");
include_once("classes/include/dbop.class.php");
include_once("classes/addclassified.class.php");?>
<?php

$m_dbConnRoot = new dbop(true);
$m_dbConn = new dbop();
$obj_classified = new classified($m_dbConnRoot,$m_dbConn);
$baseDir = dirname( dirname(__FILE__) );
//$fburl=$baseDir.'\beta\uploads\\'.$foldername.'\\'.$url;  
//C:\wamp\www\beta\uploads\\               

?>
<!doctype html>
<html>
<head>
<!--<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
--><!--<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.js"></script>-->
<!--<script type="text/javascript" language="javascript" src="js/jquery.dotdotdot.js"></script>-->
<script type="text/javascript" src="js/addclassified.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/ajax_new.js"></script>
<script type="text/javascript" language="javascript">
			/*$(function() {
				$('#dot1').dotdotdot();

				$('#dot2').dotdotdot();

				$('#dot3').dotdotdot({
					after: 'a.readmore'
				});

				$('#dot4').dotdotdot({
					watch: 'window'
				});
			});*/
				</script>
<!--<link rel="stylesheet" href="css/classified.css">-->
<link rel="stylesheet" type="text/css" href="css/pagination.css">

<title>Untitled Document</title>

<style type="text/css" media="all">
			a, a:link, a:active, a:visited {
				color: #337ab7;
   				 text-decoration: none;
   				 /*font-weight: bold;*/
			}
			a:hover {
				color: blue;
			}

			div.box {
				border: 1px solid #ccc;
				height: 100px;
				padding: 11px 10px 10px 7px;
 				overflow: hidden; 
				width:480px;
    			
			}
			div.resize {
				padding-bottom: 250px;
			}
			div.resize div.box {
				position: absolute;
				width: 50%;
				height: 100px;
			}
			div.resize div.box.before {
				right: 50%;
				margin-right: 10px;
			}
			div.resize div.box.after {
				left: 50%;
				margin-left: 10px;
			}
			div.box.opened
			{
				height: auto;
			}
			div.box .toggle .close,
			div.box.opened .toggle .open
			{
				display: none;
			}
			div.box .toggle .opened,
			div.box.opened .toggle .close
			{
				display: inline;
			}
			div.box.before {
				background-color: #ffeeee;
			}
			div.box.after {
				background-color: rgba(217, 237, 247, 0.28);
			}
			p.before {
				color: #990000;
			}
			p.after {
				color: #006600;
			}
			div.box.pathname {
				height: auto;
			}
			.pathname {
				height: 25px;
			}

</style>
<script>
 function Onsearch(value)
 { 
 //alert(value);
 window.location.href='classified.php?src&cat_id='+value;
 
 }

</script>

</head>

<body>


<br>

<div class="panel panel-info" style="margin-top:0%;margin-left:3.5%; border:none;width:70%">
 
    <div class="panel-heading" style="font-size:20px">
         Classified</div>
         
<br>

<button style="margin-left: 70%;" type="button" class="btn btn-primary" onClick="window.location.href='my_listing_classified.php'">Add / Manage my listing </button>

<br>
<div class="panel-body">
 <div class="table-responsive">
 
 <span style="font-family:sans-serif; font-size:13px;font-size: 15px;float: left;margin-left: 0px;">Search by Category :&nbsp; </span><br/>
<select name="cat_type" id="cat_type" style="font-size:12px;float: left;margin-top: -13px; margin-left: 2px;" onChange="Onsearch(this.value);" >
<?php echo $show_category = $obj_classified->combobox("select `cat_id`,`name` from classified_cate",$_GET['cat_id']);?>
 </select>

<?php
if(isset($_GET['src']))
{
echo "<br>";

$str = $obj_classified->pgnation($_GET['cat_id']);
echo "<br>";
}
else{
	$str = $obj_classified->pgnation(0);
	}
?>
</div></div></div>
<?php include_once "includes/foot.php"; ?>