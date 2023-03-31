<?php 
    if(!session_id()) {
        session_start();
    }

    define('FB_APP_ID', '397348927111503');
    define('FB_APP_SECRET', 'fa9a18a01573a5acf243c08f3f9e24fb');
    define('FB_BASE_APP_URL', 'http://'.$_SERVER['SERVER_NAME'].'/');

    require_once 'Facebook/autoload.php';
  
    $fb = new Facebook\Facebook([
        'app_id' => FB_APP_ID, // Replace {app-id} with your app id
        'app_secret' => FB_APP_SECRET,
        'default_graph_version' => 'v2.9',
        'persistent_data_handler'=>'session'
    ]);
  
    $helper = $fb->getRedirectLoginHelper();

    $permissions = array('email'); // Optional permissions

    //$protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https';
    $protocol = 'https';
    $host     = $_SERVER['HTTP_HOST'];
    $script   = $_SERVER['SCRIPT_NAME'];
    $params   = $_SERVER['QUERY_STRING'];
    $referer  =  $_SERVER['HTTP_REFERER'];
  
    //Set the current URL as the default call back URL.
    $currentUrl = $protocol . '://' . $host . $script . '?' . $params;

    $loginUrl = $helper->getLoginUrl($currentUrl, $permissions);

    //Overight the default call back URL. 
    function setLoginURL($sURL)
    {
      if($sURL <> '')
      {
          global $helper;
          global $permissions;
          global $loginUrl;
          if($loginUrl <> "")
          {
            $loginUrl = $helper->getLoginUrl($sURL, $permissions);
          }
      }
    }

    $UserDetails = array();

    if(isset($_GET['state']) && isset($_GET['code']))
    { 
        //echo '<h3>State and Code Reveived</h3>';
        $_SESSION['FBRLH_state']=$_GET['state'];
        try 
        {
            $accessToken = $helper->getAccessToken();
            $response = $fb->get('/me?fields=id,name,email,birthday,picture', $accessToken);
        } 
        catch(Facebook\Exceptions\FacebookResponseException $e) 
        {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } 
        catch(Facebook\Exceptions\FacebookSDKException $e) 
        {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (! isset($accessToken)) 
        {
          if ($helper->getError()) 
          {
              header('HTTP/1.0 401 Unauthorized');
              echo "Error: " . $helper->getError() . "\n";
              echo "Error Code: " . $helper->getErrorCode() . "\n";
              echo "Error Reason: " . $helper->getErrorReason() . "\n";
              echo "Error Description: " . $helper->getErrorDescription() . "\n";
          } 
          else 
          {
              header('HTTP/1.0 400 Bad Request');
              echo 'Bad request';
          }
          exit;
       }

        //Logged in
        //echo '<h3>Access Token</h3>';
        //var_dump($accessToken->getValue());

        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();

        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        //echo '<h3>Metadata</h3>';
        //var_dump($tokenMetadata);

        //echo '<h3>USERID</h3>';
        //echo $tokenMetadata->getUserId();

        //echo '<h3>User Details</h3>';
        $user = $response->getGraphUser();

        if($user)
        {
            $UserDetails['fbid'] = $user->getId();
            $UserDetails['email'] = $user->getEmail();
            $UserDetails['name'] = $user->getName();
            $UserDetails['picture'] = $user->getPicture()->getUrl();
        }

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId(FB_APP_ID); // Replace {app-id} with your app id
        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();

        if (! $accessToken->isLongLived()) 
        {
            // Exchanges a short-lived access token for a long-lived one
            try 
            {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } 
            catch (Facebook\Exceptions\FacebookSDKException $e) 
            {
                echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
                exit;
            }

            //echo '<h3>Long-lived</h3>';
            //var_dump($accessToken->getValue());
        } 

        $_SESSION['fb_access_token'] = (string) $accessToken;

        // User is logged in with a long-lived access token.
        // You can redirect them to a members-only page.
        //header('Location: https://example.com/members.php');
    }

?>