<?php include_once "fb/fbmain_new.php"; ?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    </head>    
<body>
<div id="fb-root"></div>
<script src="http://connect.facebook.net/en_US/all.js"></script>
<script type="text/javascript">
        window.onload = function () 
		{
         
            FB.init({ apiKey: <?php echo $fbconfig['appid'];?> });
            FB.getLoginStatus(handleSessionResponse);
        }
        function handleSessionResponse(response) 
		{
			//if we dont have a session (which means the user has been logged out, redirect the user)
			if (!response.session) {
				window.location = "http://attuit.in/societies/main/login_m_check.php?log";
				return;
			}
		
			//if we do have a non-null response.session, call FB.logout(),
			//the JS method will log the user out of Facebook and remove any authorization cookies
			FB.logout(handleSessionResponse);
		}
</script>

<script type="text/javascript">
function logout()
{
	window.location = "http://attuit.in/societies/main/login_m_check.php?log";
}
</script>

</body>
</html>