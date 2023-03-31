<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
// include_once("RightPanel.php");    
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");

?>

<html>

<head>
<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
	<script>
	var type = '';
	function ChkStatus()
	{
		if(type == 'ap')
		{
			document.getElementById('add_forum').onclick=function(){ loadBoardIndexUI()};
			document.getElementById("add_forum").innerHTML = "Go To Forum List";
		}
		if(type == '' || type == 'u')
		{
			document.getElementById('add_forum').onclick=function(){ loadFrame()};
			document.getElementById("add_forum").innerHTML = "Admin Control Panel";
		}	
	}
	function loadFrame()
	{
		//var iFrame = document.getElementById('refreshForm');
		//iFrame.load('forum/adm/index.php?sid=<?php //echo $_SESSION['phpbb_session_id'];?>');	
		//document.getElementById('refreshForm').src = 'forum/adm/index.php?sid=<?php //echo $_SESSION['phpbb_session_id'];?>';
		//document.getElementById('refreshForm').reload(true);
		//iFrame.load('http://www.google.com');
		
		//document.getElementById('refreshForm').style.src = 'forum/adm/index.php?sid=<?php //echo $_SESSION['phpbb_session_id'];?>';
		//document.getElementById('refreshForm').src = 'http://www.google.co.in';
		//alert(document.getElementById('refreshForm').style.src);
		//document.getElementById("refreshForm").src = 'forum/adm/index.php?sid=<?php //echo $_SESSION['phpbb_session_id'];?>';
		//window.location.href = 'forum/adm/index.php?sid=<?php //echo $_SESSION['phpbb_session_id'];?>';
		//window.location.href = 'http://www.google.com';
		
		/*document.getElementById("refreshForm").style.display = 'none';
		
		document.getElementById("refreshForm_admin").style.display = 'block';
		document.getElementById('refreshForm_admin').contentWindow.location.reload();
		*/
		//document.getElementById("refreshForm").src = 'forum/adm/index.php?sid=<?php echo $_SESSION['phpbb_session_id'];?>';
		document.getElementById("refreshForm").src = 'forum/UIChanger.php?type=ap';
		type = 'ap';
		//alert(document.getElementById("refreshForm").src);
		//document.getElementById('refreshForm').contentWindow.location.reload();
		ChkStatus();
		
	}
	
	function loadBoardIndexUI()
	{
		/*document.getElementById("refreshForm").style.display = 'block';
		document.getElementById("refreshForm_admin").style.display = 'none';
		*/
		document.getElementById("refreshForm").src = 'forum/UIChanger.php?type=u';
		type = 'u';
		
		ChkStatus();		
	}
	</script>
	 
</head>
 
<body>
 <div class="panel panel-info" style="margin-top:3.5%;border:none;width:77%">
 
    <div class="panel-heading" style="font-size:20px">
     Forums
    </div>
    <div class="panel-body" style="padding: 0;padding-top: 15px;" > 
    	<?php if($_SESSION['role'] && ($_SESSION['role']==ROLE_ADMIN || $_SESSION['role']==ROLE_SUPER_ADMIN))
    	{?>
    		<button type="button" name = "user"  value="user" onClick="loadFrame()"  class="btn btn-primary" id="add_forum">Admin Control Panel</button>
            <br />
            <br />                  
    	<?php } ?>
        <iframe frameborder="0"  src="forum/UIChanger.php?type=u"  style="overflow:scroll;width:100%; height:750px;" id="refreshForm" name="refreshForm" allowtransparency="true"></iframe>
    </div>
</div>


</body>

<script>ChkStatus();</script>
<?php include_once "includes/foot.php"; ?>