<?php
    $fbconfig['appid' ]     = "397348927111503";//"356767461071205";
    $fbconfig['secret']     = "fa9a18a01573a5acf243c08f3f9e24fb";//"f01d2b8c03a4e5aeaf069a9eedf274d4";
	$fbconfig['baseurl']  = "http://way2society.com/newuser.php?c=".$_REQUEST['c'];
	if(isset($_REQUEST['url']))
	{
			$fbconfig['baseurl'] .= '&url='.$_REQUEST['url'];
	}
	 $user = null; //facebook user uid
	
    try
	{
		include_once "facebook.php";
    }
    catch(Exception $o)
	{
		echo $o;
		error_log($o);
    }
    
	// Create our Application instance.
    $facebook = new Facebook(array(
      'appId'  => $fbconfig['appid'],
      'secret' => $fbconfig['secret'],
      'cookie' => true,
    ));

    //Facebook Authentication part
    $user       = $facebook->getUser();
   
    $loginUrl   = $facebook->getLoginUrl(
            array(
				'scope'         => 'email,user_birthday',
                'redirect_uri'  => $fbconfig['baseurl']
            )
    );
    
    $logoutUrl  = $facebook->getLogoutUrl();

    if($user) 
	{
      try 
	  {
        $user_profile = $facebook->api('/me');
      } 
	  catch(FacebookApiException $e) 
	  {
        //d($e);
        $user = null;
      }
    }
	
    //if user is logged in and session is valid.
    if($user)
	{
        //get user basic description
        $userInfo = $facebook->api("/$user");
        
		/*
        if(isset($_GET['publish']))
		{
            try{	
                	$publishStream = $facebook->api("/$user/feed", 'post', array(
                    'message' => "", 
                    'link'    => '',
                    'picture' => '',
                    'name'    => '',
                    'description'=> ''
                    )
                	);
            	}
				catch (FacebookApiException $e) 
				{
                	d($e);
            	}
            	
				$redirectUrl     = $fbconfig['baseurl'] . '/index.php?success=1';
            	header("Location: $redirectUrl");
        }
		*/
		
        //update user's status using graph api
        //http://developers.facebook.com/docs/reference/dialogs/feed/
        if (isset($_POST['tt']))
		{
            try 
			{
                $statusUpdate = $facebook->api("/$user/feed", 'post', array('message'=> $_POST['tt']));
            } 
			catch(FacebookApiException $e) 
			{
                d2($e);
            }
        }

        
		//fql query example using legacy method call and passing parameter
        try
		{
            $fql    =   "select name, hometown_location, sex, pic_square from user where uid=" . $user;
            $param  =   array(
                'method'    => 'fql.query',
                'query'     => $fql,
                'callback'  => ''
            );
            $fqlResult   =   $facebook->api($param);
        }
        catch(Exception $o)
		{
            d2($o);
        }
    }
	else
	{
		//echo 'user not set';	
	}
    
    function d2($d)
	{
        //echo '<pre>';
        //print_r($d);
        //echo '</pre>';
    }
	
	function d1($d)
	{
        echo '<pre>';
        //print_r($d);
        echo '</pre>';
		
		echo "Facebook Id : ".$d['id'];
		echo "<br>";
		
		echo "Name : ".$d['name'];
		echo "<br>";
		
		echo "First Name : ".$d['first_name'];
		echo "<br>";
		
		echo "Last Name : ".$d['last_name'];;
		echo "<br>";
		
		echo "Date of Birth :".$d['birthday'];
		echo "<br>";
		
		echo "Gender : ".$d['gender'];
		echo "<br>";
		
		echo "Email : ".$d['email'];
		echo "<br>";
		
		echo "Location : ".$d['hometown']['name'];
		echo "<br>";
		
    }
	
	function set_sess($d)
	{
		$details = array();
		
		$_SESSION['fbid'] = $d['id'];
		$_SESSION['name'] = $d['name'];	
		$_SESSION['gender'] = $d['gender'];	
		$_SESSION['email'] = $d['email'];	
		
		$b1 = explode('/',$d['birthday']);	
		$dob = $b1[2].'-'.$b1[0].'-'.$b1[1];
		
		$_SESSION['dob'] = $dob;
		
		$details['name'] = $d['name'];
		$details['email'] = $d['email'];
		$details['fbid'] = $d['id'];

		return $details;
	}
?>
