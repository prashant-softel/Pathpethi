
//alert('test');
function val()
{
	//alert('hi');
	var albumName=document.getElementById('album').value;
var oFile = document.getElementById('img').files[0];
if(albumName=="")
{
 document.getElementById('ErrorDiv').style.display='block';
  document.getElementById('ErrorDiv').innerHTML ="Select Album Name";
  return false; 	
}
 // <input type="file" id="fileUpload" accept=".jpg,.png,.gif,.jpeg"/> 
 if (oFile.size >1024) // 2 mb for bytes. 
 { 
  alert("File size must under 1 mb!"); 
  document.getElementById('ErrorDiv').style.display='block';
  document.getElementById('ErrorDiv').innerHTML ="File size must under 1 mb!";
  return false; 
				
 }
 
}