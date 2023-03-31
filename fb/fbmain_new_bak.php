<?php if(!isset($_SESSION)){ session_start(); }
    $fbconfig['appid' ]  = "356767461071205";
    $fbconfig['api'   ]  = "http://attuit.in/"; 
    $fbconfig['secret']  = "f01d2b8c03a4e5aeaf069a9eedf274d4";
 
    try{
        include_once "facebook.php";
    }
    catch(Exception $o){
        echo '<pre>';
        print_r($o);
        echo '</pre>';
    }
    // Create our Application instance.
    $facebook = new Facebook(array(
      'appId'  => $fbconfig['appid'],
      'secret' => $fbconfig['secret'],
      'cookie' => true,
    ));
 
    // We may or may not have this data based on a $_GET or $_COOKIE based session.
    // If we get a session here, it means we found a correctly signed session using
    // the Application Secret only Facebook and the Application know. We dont know
    // if it is still valid until we make an API call using the session. A session
    // can become invalid if it has already expired (should not be getting the
    // session back in this case) or if the user logged out of Facebook.
    $session = $facebook->getUser();
 
    $fbme = null;
    // Session based graph API call.
    if($session) 
	{
      try
	  {//echo 'active';
        $userInfo = $facebook->api("/$session");
        $fbme = $facebook->api('/me');
      } 
	  catch(FacebookApiException $e) 
	  {
          d($e);
      }
    }
 	
	//$params = array('next' => 'http://attuit.in/societies/main/login_m_check.php?log','access_token' => $facebook->getAccessToken() );
    //$logoutUrl  = $facebook->getLogoutUrl();
	
	if($session)
	{
		$show = 'active';	
		return $show;
	}
	else
	{
		$show = 'inactive';	
		return $show;
	}
	
    function d($d)
	{
        echo '<pre>';
       // print_r($d);
        echo '</pre>';
    }
	
	function set_sess($d)
	{
		$_SESSION['fbid'] = $d['id'];
		$_SESSION['name'] = $d['name'];	
		$_SESSION['gender'] = $d['gender'];	
		$_SESSION['email'] = $d['email'];	
		
		$b1 = explode('/',$d['birthday']);	
		$dob = $b1[2].'-'.$b1[0].'-'.$b1[1];
		
		$_SESSION['dob'] = $dob;	
		
		return $_SESSION['fbid'];
	}
?>