<?php
    include_once "fbmain1.php";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>Facebook Wallpost</title>
    
        <script type="text/javascript">
            function streamPublish(name, description, hrefTitle, hrefLink, userPrompt)
			{        
                FB.ui({ method : 'feed', 
                        message: userPrompt,
                        link   : hrefLink,
                        caption: hrefTitle,
						description : description,
                        picture: 'http://career-courses.in/acme/images/welcome.jpg'
               });
               //http://developers.facebook.com/docs/reference/dialogs/feed/
   
            }
			
            function publishStream()
			{
                streamPublish('Stream Publish', 
							  'Participate now to win a trip to Washington DC, the home of NCIS!',
							  'Society Software', 
							  'http://career-courses.in/acme/main/login_m.php?mlog', 
							  'Demo Facebook Application Tutorial');
            }
            
            function newInvite(){
                 var receiverUserIds = FB.ui({ 
                        method : 'apprequests',
                        message: 'Come on man checkout my applications. visit http://ithinkdiff.net',
                 },
                 function(receiverUserIds) {
                          console.log("IDS : " + receiverUserIds.request_ids);
                        }
                 );
                 //http://developers.facebook.com/docs/reference/dialogs/requests/
            }
        </script>
    </head>
<body>
    
<style type="text/css">
    .box{
        margin: 5px;
        border: 1px solid #60729b;
        padding: 5px;
        width: 500px;
        height: 200px;
        overflow:auto;
        background-color: #e6ebf8;
    }
</style>

<div id="fb-root"></div>
    <script type="text/javascript" src="http://connect.facebook.net/en_US/all.js"></script>
     <script type="text/javascript">
       FB.init({
         appId  : '<?php echo $fbconfig['appid']?>',
         status : true, // check login status
         cookie : true, // enable cookies to allow the server to access the session
         xfbml  : true  // parse XFBML
       });
       
     </script>

    
    
    <?php if (!$user) { ?>
        <a href="<?php echo $loginUrl?>"><img src="fb_connect.jpg" alt="" /></a>
        <br>
        <a href="#" onclick="publishStream(); return false;">Wall Post  - Javascript</a>
        <br>
        <a href="<?php echo $fbconfig['baseurl']?>?publish=1">Wall Post - PHP </a>
        
        
    <?php } else { ?>
        <a href="<?php echo $logoutUrl?>">Facebook Logout</a>
        <a href="<?php echo $fbconfig['baseurl']?>?publish=1">Publish Post using PHP </a>
    <?php } ?>

    <!-- all time check if user session is valid or not -->
    <?php if ($user){ ?>
    <table border="0" cellspacing="3" cellpadding="3">
        <tr>
            <td>
                <!-- Data retrived from user profile are shown here -->
                <div class="box">
                    <b>User Information using Graph API</b>
                    <?php d($userInfo); ?>
                </div>
            </td>
            <td>           
                <div class="box">
                    <b>User likes these movies | using graph api</b>
                     <?php d($movies); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="box">
                    <b>Wall Post & Invite Friends</b>
                    <br /><br />
                    <a href="<?php echo $fbconfig['baseurl']?>?publish=1">Publish Post using PHP </a>
                    <br />
                    You can also publish wall post to user's wall via Ajax.
                    <?php if (isset($_GET['success'])) { ?>
                        <br />
                        <strong style="color: red">Wall post published in your wall successfully!</strong>
                    <?php } ?>
                        
                    <br /><br />
                    <a href="#" onclick="publishStream(); return false;">Publish Wall post using Facebook Javascript</a>
                    
                    <br /><br />
                    <a href="#" onclick="newInvite(); return false;">Invite Friends</a>
                </div>
            </td>
            <td>
                <div class="box">
                    <b>FQL Query Example by calling Legacy API method "fql.query"</b>
                    <?php d($fqlResult); ?>
                </div>
            </td>
        </tr>
    </table>
    <div class="box">
        <form name="" action="<?php echo $fbconfig['baseurl']?>" method="post">
            <label for="tt">Status update using Graph API</label>
            <br />
            <textarea id="tt" name="tt" cols="50" rows="5">Write your status here and click 'Update My Status'</textarea>
            <br />
            <input type="submit" value="Update My Status" />
        </form>
        <?php if (isset($statusUpdate)) { ?>
            <br />
            <strong style="color: red">Status Updated Successfully! Status id is <?php echo $statusUpdate['id']?></strong>
         <?php } ?>
    </div>
    <?php } ?>
    
    <?php
	if(isset($userInfo))
	{
		d1($userInfo);
	}
	?>
	
    </body>
</html>