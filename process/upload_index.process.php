<?php include_once("../classes/upload_index.class.php");
	include_once("../classes/include/dbop.class.php");
	  	$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
	  $obj_create_album = new show_album($dbConn,$dbConnRoot);
	  //print_r($_POST);
	  $validator = $obj_create_album->startProcess();
?>

<?php /*?><?php
// Function for resizing jpg, gif, or png image files
function ak_img_resize($target, $newcopy, $w, $h, $ext) {
    list($w_orig, $h_orig) = getimagesize($target);
    $scale_ratio = $w_orig / $h_orig;
    if (($w / $h) > $scale_ratio) {
           $w = $h * $scale_ratio;
    } else {
           $h = $w / $scale_ratio;
    }
    $img = "";
    $ext = strtolower($ext);
    if ($ext == "gif"){ 
      $img = imagecreatefromgif($target);
    } else if($ext =="png"){ 
      $img = imagecreatefrompng($target);
    } else { 
      $img = imagecreatefromjpeg($target);
    }
    $tci = imagecreatetruecolor($w, $h);
    // imagecopyresampled(dst_img, src_img, dst_x, dst_y, src_x, src_y, dst_w, dst_h, src_w, src_h)
    imagecopyresampled($tci, $img, 0, 0, 0, 0, $w, $h, $w_orig, $h_orig);
    imagejpeg($tci, $newcopy, 80);
}
?>
<?php */?><html>
<body>
<font color="#FF0000" size="+2">Please Wait...</font>

<form name="Goback" method="post" action="<?php echo $obj_create_album->actionPage; ?>">
	<?php
//echo $obj_create_album->actionPage;
	if($validator=="Insert")
	{
		$ShowData = "Record Added Successfully";
	}
	else if($validator=="Update")
	{
		$ShowData = "Record Updated Successfully";
	}
	else if($validator=="Delete")
	{
		$ShowData = "Record Deleted Successfully";
	}
	else
	{
		
		$ShowData = $validator;
	}
	foreach($_POST as $key=>$value)
	{
		echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
	}
	if($obj_create_album->errormsg <> '')
	{
		echo "<input type=\"hidden\" name=\"error\" value=\"$obj_create_album->errormsg\" />";
	}
	if($obj_create_album->errormsg1 <> '')
	{
		echo "<input type=\"hidden\" name=\"error1\" value=\"$obj_create_album->errormsg1\" />";
	}
	//echo $obj_create_album->table;
	?>

<input type="hidden" name="ShowData" value="<?php echo $ShowData; ?>">
</form>

<script>
	document.Goback.submit();
</script>

</body>
</html>
