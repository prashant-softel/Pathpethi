<html>
<head>
<link rel="stylesheet" href="css/style_gallery.css">
<link rel="stylesheet" type="text/css" href="css/pagination.css">
<style>
#body
{
	background:#fff;
padding:10px;
margin:100px auto;
box-shadow:0 0 10px #75AAC5;
width:710px;

} 
table p
{
	color:#F00;
	font-size:14px; 
	 
	margin-top: 37px; 
	margin-bottom: 37px;
} 
#check
{
	border-radius: none;
    -moz-border-radius: none;
    -webkit-border-radius: none;
    -moz-box-shadow: none;
    -webkit-box-shadow: none;
	 width: 1.5em;
    height: 1.5em;
    /* box-shadow: 1px 1px 4px #666;
}
*/</style>
</head>

<?php 
include_once("classes/dbconst.class.php");
include_once("classes/include/dbop.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/upload_index.class.php");
//include_once("wp-config.php");
//include_once("wp-includes/wp-db.php");
include_once("classes/include/config_normal_wp.php");

 $m_dbConn = new dbop();
$m_dbConnRoot = new dbop(true);

$obj_show=new show_album($m_dbConn,$m_dbConnRoot);

global $wpdb;
?>

<body>
<!--<div class="panel panel-info" id="panel" style="display:block;">
        <div class="panel-heading" id="pageheader">Photo Gallery</div>-->

<form method="post" action="" >
<input type="hidden" name="pg" id="pg" value="4" />
<div id="body">
<?php include "title_bar.php";?>
<center><table>
<tr>
<td><p style="font-weight:bold;">----Maximum Allow Only Nine Images----</p></td></tr></table></center>
<?php
//echo "before";
 $s_00 = "SELECT `thumb_url` FROM `wp_bwg_image` WHERE `gallery_id` =5";
 $r_00 = mysql_query($s_00);
 //var_dump($r_00);
 //$rw_00 = mysql_fetch_array($r_00);
 //var_dump($rw_00);
 //while ($row = mysql_fetch_array($r_00, MYSQL_NUM)) {
    //printf("ID: %s  Name: %s", $row[0], $row[1]);  
//}

$count = 0;
while($row = mysql_fetch_array($r_00, MYSQL_ASSOC))
{
	$data[$count] = $row;
	$count++;
}
//var_dump($data);
 $thumb_url_00 = $rw_00['thumb_url'];
 	
//$result = $wpdb->get_results( "SELECT thumb_url FROM wp_bwg_image " );
//var_dump($result);
//echo "after";
$link=('wp-content\uploads\photo-gallery');
?>

<?php 
foreach ( $data as $res ) 
{ 
//print_r($res);?>

<div id="view_box">
<img src="<?php echo str_replace('/','\\',$link.$res['thumb_url']);?>"  alt="not found" style="width:150px;height:100px;"/>
<input type="checkbox" name="check[]" id="check" value="<?php echo $res['thumb_url'];?>"/>
</div>

<?php }
?>
<div class="clear"></div>
<br><br>
<center><input type="submit" value="Delete" name="delete" id="delete" style="font-size: 15px; background-color: #337ab7; color: #fff;"/></center>
<br>
</form>



<!--</div>-->

</form>
</body>
</html>