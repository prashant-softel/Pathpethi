<!DOCTYPE html> 
<html xmlns:fb="https://www.facebook.com/2008/fbml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>New JavaScript SDK & OAuth 2.0 based FBConnect Tutorial | Thinkdiff.net</title>
        <!--
            @author: Mahmud Ahsan (http://mahmud.thinkdiff.net)
        -->
    </head>
    <body>
        <div id="fb-root"></div>
        <script type="text/javascript">
            var button;
            var userInfo;
            
            window.fbAsyncInit = function() {
                FB.init({ appId: '356767461071205', //change the appId to your appId
                    status: true, 
                    cookie: true,
                    xfbml: true,
                    oauth: true});

               //showLoader(true);
               
               function updateButton(response) 
			   {
                    button       =   document.getElementById('fb-auth');
                    userInfo     =   document.getElementById('user-info');
                    
					
                    if (response.authResponse) {alert("111");
                        //user is already logged in and connected
                        FB.api('/me', function(info) {
                            //login(response, info);
                        });
                        
                        button.onclick = function() {
                            FB.logout(function(response) {
                                logout(response);
                            });
                        };
                    } else {alert("222");
                        //user is not connected to your app or logged out
                        button.innerHTML = 'Login';
                        button.onclick = function() {
                            showLoader(true);
                            FB.login(function(response) {
                                if (response.authResponse) {
                                    FB.api('/me', function(info) {
                                        login(response, info);
                                    });	   
                                } else {
                                    //user cancelled login or did not grant authorization
                                    showLoader(false);
                                }
                            }, {scope:'email,user_birthday,status_update,publish_stream,user_about_me'});  	
                        }
                    }
                }
                
                // run once with current status and whenever the status changes
                FB.getLoginStatus(updateButton);
                FB.Event.subscribe('auth.statusChange', updateButton);	
            };
			
            (function() {alert("333");
                var e = document.createElement('script'); e.async = true;
                e.src = document.location.protocol 
                    + '//connect.facebook.net/en_US/all.js';
                document.getElementById('fb-root').appendChild(e);
            }());
            
            
            function login(response, info){alert("444");
                if (response.authResponse) {
                    var accessToken                                 =   response.authResponse.accessToken;
                    
                    userInfo.innerHTML                             = '<img src="https://graph.facebook.com/' + info.id + '/picture"><br>' + 
																	 'Your Name - ' +	info.name + '<br>' + 
																	 'fbid - ' + info.id + '<br>' +
																	 'Your Access Token - ' + accessToken;
																	 
                    button.innerHTML                               = 'Logout';
                    showLoader(false);
                    document.getElementById('other').style.display = "block";
                }
            }
        
            function logout(response){
                userInfo.innerHTML                             =   "";
                document.getElementById('debug').innerHTML     =   "";
                document.getElementById('other').style.display =   "none";
                showLoader(false);
            }

            //stream publish method
            function streamPublish(name, description, hrefTitle, hrefLink, userPrompt)
			{
                showLoader(true);
                FB.ui(
                {
                    method: 'stream.publish',
                    message: '',
                    attachment: {
                        name: name,
                        caption: '',
                        description: (description),
                        href: hrefLink
                    },
                    action_links: [
                        { text: hrefTitle, href: hrefLink }
                    ],
                    user_prompt_message: userPrompt
                },
                function(response) 
				{
                    showLoader(false);
                });
            }
            function showStream()
			{
                FB.api('/me', function(response) 
				{
                    streamPublish(response.name, 'I like the articles of Thinkdiff.net', 'hrefTitle', 'http://thinkdiff.net', "Share thinkdiff.net");
                });
            }


            function share()
			{
                showLoader(true);
                var share = {
                    method: 'stream.share',
                    u: 'http://thinkdiff.net/'
                };

                FB.ui(share, function(response) 
				{ 
                    showLoader(false);
                    console.log(response); 
                });
            }

            function graphStreamPublish()
			{
                showLoader(true);
                
                FB.api('/me/feed', 'post', 
                    { 
                        message     : "I love thinkdiff.net for facebook app development tutorials",
                        link        : 'http://ithinkdiff.net',
                        picture     : 'http://thinkdiff.net/iphone/lucky7_ios.jpg',
                        name        : 'iOS Apps & Games',
                        description : 'Checkout iOS apps and games from iThinkdiff.net. I found some of them are just awesome!'
                        
                    }, 
				function(response) 
				{alert("555");
                    showLoader(false);
                    
                    if (!response || response.error) 
					{
                        alert('Error occured');
                    } else {
                        alert('Post ID: ' + response.id);
                    }
                });
            }
			
            function showLoader(status)
			{
                if(status)
                    document.getElementById('loader').style.display = 'block';
                else
                    document.getElementById('loader').style.display = 'none';
            }
            
        </script>

        <h3>FB Connect in JavaScript</h3>
        
        <button id="fb-auth">Login</button>
        <br>
        
        <div id="loader" style="display:none">
            <img src="ajax-loader.gif" alt="loading" />
        </div>
        
        <br />
        
        <div id="user-info"></div>
        
        <br />
        
        <div id="debug"></div>
        
        <div id="other" style="display:none">
            <a href="#" onclick="showStream(); return false;">Publish Wall Post</a> |
            <a href="#" onclick="share(); return false;">Share With Your Friends</a> |
            <a href="#" onclick="graphStreamPublish(); return false;">Direct Publish on FB</a>
        </div>
    </body>
</html>