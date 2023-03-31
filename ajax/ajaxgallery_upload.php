

<?php 

include_once("../classes/include/dbop.class.php");
include_once("../classes/dbconst.class.php");
include_once("../classes/upload_index.class.php");

include_once("../classes/include/config_normal_wp.php");
	  $m_dbConn = new dbop();
$m_dbConnRoot = new dbop(true);

$obj_show=new show_album($m_dbConn,$m_dbConnRoot);



if($_REQUEST["method"] == 'save')
{
	// echo "update `photos` set cover='1' where `id`='".$_REQUEST['photoID']."'";
	 $resetAll = "update `photos` set `cover`='0' where `album_id`='".$_REQUEST['albumID']."'";
	$reset = $m_dbConnRoot->update($resetAll);
	
	$save = "update `photos` set `cover`='1' where `id`='".$_REQUEST['photoID']."'";
	//echo $save;
		$res = $m_dbConnRoot->update($save);

		//$obj_show->startProcess($save);
	
}

					/*----------Photo deleted from folder---------*/
//echo "<br>after setcover method";	
				
if($_REQUEST["method"]=='del')
{	
//print_r($_REQUEST);
//echo "inside del method";
$baseDir = dirname( dirname(__FILE__) );
		$PhotoIDArray = json_decode(str_replace('\\', '', $_REQUEST['PhotoIDArray']), true);
		for($i=0;$i<count($PhotoIDArray);$i++){
		
		$del_id = $PhotoIDArray[$i];
		 $sql2 = "select `url` FROM `photos` WHERE id='$del_id'";

		$res2 = $m_dbConnRoot->select($sql2);
		
		$album_id=$_REQUEST["AlbumID"];
		 $sql3="select `id`, `name`,`folder` FROM `album` WHERE id='$album_id'";
		$res3 = $m_dbConnRoot->select($sql3);
	print_r($res3);
		//$result=
	
		//print_r($res2);
		//echo $baseDir.'/uploads/'.$res3[0]['name'].'/'.$res2[0]['url'];
		if (file_exists($baseDir.'/uploads/'.$res3[0]['folder'].'/'.$res2[0]['url'])) {
    		unlink($baseDir.'/uploads/'.$res3[0]['folder'].'/'.$res2[0]['url']);
			unlink($baseDir.'/uploads/'.$res3[0]['folder'].'/thumb/'.$res2[0]['url']);
			echo "file deleted";
		}
		else
		{
			echo "not deleted file";
			}
		$del_id = $PhotoIDArray[$i];
		$sql = "DELETE FROM `photos` WHERE id='$del_id'";

		$res = $m_dbConnRoot->update($sql);
		//$obj_show->startProcess('view.php');
	
	}		
}

//echo "<br>after del method";
if($_REQUEST["method"]=='del_album')
{
		$AlbumIDArray = json_decode(str_replace('\\', '', $_REQUEST['AlbumIDArray']), true);
		
		for($i=0;$i<count($AlbumIDArray);$i++){
			
			$del_id = $AlbumIDArray[$i];
		$sql2 = "select `id`, `name`,`folder` FROM `album` WHERE id='$del_id'";

		$res2 = $m_dbConnRoot->select($sql2);
		
		
		//$path = "uploads/";
		$baseDir = dirname( dirname(__FILE__) );
		 if(!$dh=opendir($baseDir.'/uploads/'.$res2[0]['folder']))
    {
        return false;
    }
     
    while($file=readdir($dh))
    {
        if($file == "." || $file == "..")
        {
            continue;
        }
         
       if(is_dir($baseDir.'/uploads/'.$res2[0]['folder']."/".$file))
        {
			
			
            unlink($baseDir.'/uploads/'.$res2[0]['folder']."/");
			//deleteDirectory($baseDir.'/uploads/'.$res2[0]['folder']."/thumb/".$file);
			
        }
         
        if(is_file($baseDir.'/uploads/'.$res2[0]['folder']."/".$file))
        {
            unlink($baseDir.'/uploads/'.$res2[0]['folder']."/".$file);
			unlink($baseDir.'/uploads/'.$res2[0]['folder']."/thumb/".$file);
        }
    }
     
    	$del_album = $AlbumIDArray[$i];
			$sql = "DELETE FROM `album` WHERE id='$del_album'";

			$res = $m_dbConnRoot->delete($sql);
			
			$sqlDeletePhoto = "DELETE FROM `photos` WHERE album_id='$del_album'";

			$res = $m_dbConnRoot->delete($sqlDeletePhoto);
			closedir($dh);
     
	 rmdir($baseDir.'/uploads/'.$res2[0]['folder'].'/thumb');
    rmdir($baseDir.'/uploads/'.$res2[0]['folder']);
//	rmdir($baseDir.'/uploads/'.$res2[0]['folder'].'/thumb');
		}
}

//echo "<br>after albul_del  method";
if($_REQUEST["method"]=='send')
		{
			//print_r($_REQUEST);
			//echo "inside send method";
			//echo 'album id:'.$_REQUEST['albumID'];
		$baseDir = dirname( dirname(__FILE__) );
		$baseDir2 = dirname( dirname(dirname(__FILE__)) );
		$PhotoIDArray = json_decode(str_replace('\\', '', $_REQUEST['PhotoIDArray']), true);
		$sqlAlbum = "select `id`, `name`,`folder` FROM `album` WHERE id='".$_REQUEST['albumID']."'";

		$resAlbum = $m_dbConnRoot->select($sqlAlbum);
		//print_r($resAlbum);
		for($i=0;$i<count($PhotoIDArray);$i++){
		
		$set_id = $PhotoIDArray[$i];
		$sql2 = "select `url` FROM `photos` WHERE id='$set_id'";

		$res2 = $m_dbConnRoot->select($sql2);
		
		
		
		
	//$wpdb="INSERT INTO `photos`(`album_id`, `url`) VALUES ('$album_id','$random_name.$fileExt')";	 
	
/*	$result=$wpdb->get_results( 
	"
	SELECT id 
	FROM $wpdb->wp_bwg_gallery
	WHERE published = '1' 
	"
);
print_r($result);
*/

	/*$file= $_FILES['pic']['name'];
	$file_tmp=$_FILES['pic']['tmp_name'];
	print_r($_FILES);*/
	echo 'first: '.$baseDir2.'/html/wp-content/uploads/photo-gallery/'.$res2[0]['url'];
	echo 'second:'.$baseDir.'/uploads/'.$resAlbum[0]['folder'].'/'.$res2[0]['url'];
	
//move_uploaded_file($file_tmp, $baseDir.'\uploads\\'.$res2[0]['url']);
//copy($baseDir2.'\wordpress\wp-content\uploads\photo-gallery\\'.$res2[0]['url'], $baseDir.'\uploads\\'.$resAlbum[0]['name'].'\\'.$res2[0]['url']);
copy($baseDir.'/uploads/'.$resAlbum[0]['folder'].'/'.$res2[0]['url'],$baseDir2.'/html/wp-content/uploads/photo-gallery/'.$res2[0]['url']);
//copy($baseDir.'\uploads\\'.$resAlbum[0]['name'].'\\'.$res2[0]['url'],$baseDir2.'\wordpress\wp-content\uploads\photo-gallery\.original\\'.$res2[0]['url']);
copy($baseDir.'/uploads/'.$resAlbum[0]['folder'].'/'.$res2[0]['url'],$baseDir2.'/html/wp-content/uploads/photo-gallery/thumb/'.$res2[0]['url']);
$thumb_url = '/thumb/'.$res2[0]['url'];
$main_url = '/'.$res2[0]['url'];

$sqq0 = "insert into wp_bwg_image(`image_url`,`filename`,`thumb_url`,`alt`,`slug`,`author`,`published`,`gallery_id`)values('".$main_url."','".$res2[0]['url']."','".$thumb_url."','way2society','way2society','1','1','5')";
		$ree0 = mysql_query($sqq0);
		//echo $sqq0;


/*$wpdb->insert( '
	'wp_bwg_image', 
	array( 
		
		'image_url' =>'/'.$res2[0]['url'],
		'filename' =>$res2[0]['url'],
		'thumb_url' =>'/thumb/'.$res2[0]['url'],
		'alt' => 'way2society',
		'slug' => 'way2society',
		'author' => 1,
		'published'=> 1,
		'gallery_id' =>5,
	       	),
	array(
		'%s','%d', ) 
	
	); */
	} 
	}
	
?>
