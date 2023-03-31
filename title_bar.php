<style>
table{
font-family: sans-serif;
}
</style>
<html>

<head>
<link rel="stylesheet" href="css/style_gallery.css">
<script>
function setSelected(id)
{
	alert("btn clicked....");	
}
</script>
</head>
<body>
<div id="title_bar">
<table>
<tr>
<div style="height:20px;" >
<ul class="nav nav-tabs" style="    border-bottom: 0px;">
	<li  class="active" id="btnHome" style="font-size: 13px;font-family: sans-serif;"><a href="#" onClick="tabBtnClicked('upload_index.php','btnHome');"  data-toggle="tab"  >Album</a></li>
	<li align="center"  id="btnCreate" style="font-size: 13px;font-family: sans-serif;" ><a href="#" onClick="tabBtnClicked('create.php','btnCreate');" data-toggle="tab"id="linkCreate" >Create Album</a></li>
	<li align="center" id="btnUpload" style="font-size: 13px;font-family: sans-serif;"><a href="#" onClick="tabBtnClicked('upload.php' ,'btnUpload');" data-toggle="tab">Upload Photo</a></li>
    <li align="center" id="btnHomePage" style="font-size: 13px;font-family: sans-serif;"><a href="#" onClick="tabBtnClicked('homepage_images.php' ,'btnHomePage');" data-toggle="tab">HomePage Photos</a></li>
 </ul>
</div>
</tr>
</table>
</div>

</body>
</html>