<?php
	
	require_once 'Facebook/autoload.php';
	
	$fb = new Facebook\Facebook([
	  	'app_id' => '1847189808877092', // Replace {app-id} with your app id
	  	'app_secret' => '94d12382d62e964f6d788bb37d10b3c6',
	  	'default_graph_version' => 'v2.9',
    	'persistent_data_handler'=>'session'
  	]);
	
	$helper = $fb->getRedirectLoginHelper();

	$permissions = array('email'); // Optional permissions
	$loginUrl = $helper->getLoginUrl('http://localhost/Facebook/callback.php', $permissions);

	echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';
?>