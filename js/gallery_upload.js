function  SetAlbumCover()
{
	var setcover = document.getElementsByClassName('setcover'), i;
	var album_id = document.getElementById('album_id').value;
	//alert(album_id);
	
	var photo_id;
	for (i = 0; i < setcover.length; i += 1)
	{
		//renewfd[i].style.visibility = 'visible';
		
		if(setcover[i].checked == true)
		{//alert("test");
			//alert("val" + setcover[i].value);
			photo_id = setcover[i].value;
		}
	}
	$.ajax({
				
			url : "ajax/ajaxgallery_upload.php",
			type : "POST",
			data : {"method" : 'save',"photoID" : photo_id, "albumID" : album_id},
			success : function(data)
			{	
			//tabBtnClicked('view.php?id=' + album_id + '&photo=1');	
				location.reload(true);	
			},
		});
		
}


function DeletePhoto()
{
	//alert('del photo');
	var radioButton = document.getElementsByClassName('radioButton'), i;
	
	var photo_id;
	var album_id;
	PhotoArray = [];
	
	var album_id = document.getElementById('album_id').value;
	var conf = confirm("Are you sure , you want to delete it ?");
	if(conf == 1)
	{
	
		for (i = 0; i < radioButton.length; i += 1)
		{
			//renewfd[i].style.visibility = 'visible';
			
			if(radioButton[i].checked == true)
			{//alert("test");
				//alert("val" + radioButton[i].value);
				PhotoArray.push ( radioButton[i].value);
			}
		}
		//alert (JSON.stringify(PhotoArray));
			
		$.ajax({
					
				url : "ajax/ajaxgallery_upload.php",
				type : "POST",
				data : {"method" : 'del',"photoID" : photo_id, "PhotoIDArray" : JSON.stringify(PhotoArray),"AlbumID" :album_id},
				success : function(data)
				{	
				tabBtnClicked('view.php?id=' + album_id + '&photo=1');	
					//location.reload(true);	
						//location.href = "gallery_upload.php";
					//window.location.assign("view.php");
			
				}
			});
}
}



function DeleteAlbum(str)
{
	//alert('test');
	var delete_album = document.getElementsByClassName('delete_album'), i;
	
	var album_id;
	AlbumArray = [];
	var conf = confirm("Are you sure , you want to delete it ?");
	if(conf == 1)
	{

		for (i = 0; i < delete_album.length; i += 1)
		{
			//renewfd[i].style.visibility = 'visible';
			
				if(delete_album[i].checked == true)
				{//alert("test");
				//alert("val" + radioButton[i].value);
					AlbumArray.push ( delete_album[i].value);
				}
			
		}
	//alert (JSON.stringify(AlbumArray));
		
	$.ajax({
				
			url : "ajax/ajaxgallery_upload.php",
			type : "POST",
			data : {"method" : 'del_album',"albumID" : album_id, "AlbumIDArray" : JSON.stringify(AlbumArray)},
			success : function(data)
			{	
				location.reload(true);	
					//location.href = "gallery_upload.php";
				//window.location.assign("view.php");
		
			}
		});
	}
}

function SetPhotoToHomepage()
{ 
	var radioButton = document.getElementsByClassName('sendphoto'), i;
	
	var photo_id;
	PhotoArray = [];
	
	var album_id = document.getElementById('album_id').value;
	
	//alert("album_id :" + album_id);
	for (i = 0; i < radioButton.length; i += 1)
	{
		//renewfd[i].style.visibility = 'visible';
		
		if(radioButton[i].checked == true)
		{//alert("test");
			//alert("val" + radioButton[i].value);
			PhotoArray.push ( radioButton[i].value);
		}
	}
	
	$.ajax({
				
			url : "ajax/ajaxgallery_upload.php",
			type : "POST",
			data : {"method" : 'send',"albumID" : album_id,"photoID" : photo_id, "PhotoIDArray" : JSON.stringify(PhotoArray)},
			success : function(data)
			{	
			tabBtnClicked('view.php?id=' +album_id + '&check=1');	
				
			}
		});
		
}

