<?php
//echo "Inside Index";
//error_reporting(E_ALL);
/**
*
* This file is part of the phpBB Forum Software package.
*
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* For full copyright and license information, please see
* the docs/CREDITS.txt file.
*
*/

/**
*/

/**
* @ignore
*/

session_start();
//echo "index.php";
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);

// Start session management
$user->session_kill();
$user->session_begin();
$user->session_kill();

$user->session_begin();
$auth->acl($user->data);


//print_r($auth);
/*echo "SID: ".SID."<br>session_id(): ".session_id();
echo "SID: ".SID."<br>phpbb session : ".$user->data['session_id'];
*/
$sqldetails = "SELECT `member_id`,`password` FROM `login` where `login_id` = '".$_SESSION['login_id']."' ";
//$sqldetails = "SELECT `member_id`,`password` FROM `login` where `login_id` = 1 ";
$details = $db->sql_query($sqldetails);
$fetchrow = $db->sql_fetchrow($details);


if($fetchrow <> "")
{
	/*$username = 'sadmin';
	$password = 'sadmin';*/
	$username = $fetchrow['member_id'];
	$password = $fetchrow['password'];
	
	/*echo "<br>".$username;
	echo "<br>".$password;
	echo "<br>".$user->data['is_registered'];*/
	if(isset($username) && isset($password) )
	{
		//print_r($_SESSION);
		//var_dump($user->data);
		$auth->login($username, $password, true);
	  		$_SESSION['phpbb_session_id'] = $user->data['session_id'];	
		if($user->data['is_registered'] == true)
		{
	  		//$auth->login($username, $password, true);
	  		//$_SESSION['phpbb_session_id'] = $user->data['session_id'];
			if(!$user->data["is_registered"])
			  {
			   //die("User is not logged in");
			  } else {
			   //echo "User is logged in";
			  }
			  //var_dump($auth);
			  if($auth->acl_get('a_'))
			  {
			   		//echo "user is admin";	  
			  }
			  else
			  {
					//echo "not admin";  
			  }
			  
	
		}
		else if($user->data['is_registered'] == false)
		{
			//echo "PLease login";
			// an email address for the user
			//$email_address = 'abc@gmail.com';
			$email_address = $username;
			
			// default is 4 for registered users, or 5 for coppa users.
			$group_id = ($coppa) ? 5 : 4;
			// since group IDs may change, you may want to use a query to make sure you are grabbing the right default group...
			$group_name = ($coppa) ? 'REGISTERED_COPPA' : 'REGISTERED';
			$sql = 'SELECT group_id
					FROM ' . GROUPS_TABLE . "
					WHERE group_name = '" . $db->sql_escape($group_name) . "'
						AND group_type = " . GROUP_SPECIAL;
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$group_id = $row['group_id'];
			
			// timezone of the user... Based on GMT in the format of '-6', '-4', 3, 9 etc...
			$timezone = '-6';
			
			// two digit default language for this use of a language pack that is installed on the board.
			$language = 'en';
			
			// user type, this is USER_INACTIVE, or USER_NORMAL depending on if the user needs to activate himself, or does not.
			// on registration, if the user must click the activation link in their email to activate their account, their account
			// is set to USER_INACTIVE until they are activated. If they are activated instantly, they would be USER_NORMAL
			$user_type = USER_NORMAL;
			
			// here if the user is inactive and needs to activate thier account through an activation link sent in an email
			// we need to set the activation key for the user... (the goal is to get it about 10 chars of randomization)
			// you can use any randomization method you want, for this example, I’ll use the following...
			$user_actkey = md5(rand(0, 100) . time());
			$user_actkey = substr($user_actkey, 0, rand(8, 12));
			
			// IP address of the user stored in the Database.
			$user_ip = $user->ip;
			
			// registration time of the user, timestamp format.
			$registration_time = time();
			
			// inactive reason is the string given in the inactive users list in the ACP.
			// there are four options: INACTIVE_REGISTER, INACTIVE_PROFILE, INACTIVE_MANUAL and INACTIVE_REMIND
			// you do not need this if the user is not going to be inactive
			// more can be read on this in the inactive users section
			$user_inactive_reason = INACTIVE_REGISTER;
			
			// time since the user is inactive. timestamp.
			$user_inactive_time = time();
			
			// these are just examples and some sample (common) data when creating a new user.
			// you can include any information 
			$user_row = array(
				'username'              => $username,
				'user_password'         => phpbb_hash($password),
				'user_email'            => $email_address,
				'group_id'              => (int) $group_id,
				'user_timezone'         => (float) $timezone,
				'user_lang'             => $language,
				'user_type'             => $user_type,
				'user_actkey'           => $user_actkey,
				'user_ip'               => $user_ip,
				'user_regdate'          => $registration_time,
				'user_inactive_reason'  => $user_inactive_reason,
				'user_inactive_time'    => $user_inactive_time,
			);
			
			// Custom Profile fields, this will be covered in another article.
			// for now this is just a stub
			// all the information has been compiled, add the user
			// the user_add() function will automatically add the user to the correct groups
			// and adding the appropriate database entries for this user...
			// tables affected: users table, profile_fields_data table, groups table, and config table.
			$user_id = user_add($user_row);	
			if($user_id <> "")
			{
				$auth->login($username, $password, true);
				$_SESSION['phpbb_session_id'] = $user->data['session_id'];
				// $user->session_create($user->data['user_id']); 	
			}
		}
  
	}
	
}
/* 
	  
		$link1 = "http://localhost/beta/forum/ucp.php?mode=logout&sid=".$_SESSION['phpbb_session_id'];
		if(isset($user->data['session_id']))
		{
			echo "test";
			$link1 = "ucp.php?mode=logout&sid=".$_SESSION['phpbb_session_id'];
			/*$link = str_replace(" ", '%20',$link1);
			echo "<br>LINK : ".$link;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $link);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$response = curl_exec($ch);
			curl_close($ch);
			echo $response;
			
			header($link1);
			//session_destroy();
			//session_unset($_SESSION['phpbb_session_id']);
		}
*/
/*var_dump($auth->acl_get('f_attach'));
if($auth->acl_get('f_announce'))
{
	echo "current user is admin";
}*/

$user->setup('viewforum');

// Mark notifications read
if (($mark_notification = $request->variable('mark_notification', 0)))
{
	if ($user->data['user_id'] == ANONYMOUS)
	{
		if ($request->is_ajax())
		{
			trigger_error('LOGIN_REQUIRED');
		}
		login_box('', $user->lang['LOGIN_REQUIRED']);
	}

	if (check_link_hash($request->variable('hash', ''), 'mark_notification_read'))
	{
		$phpbb_notifications = $phpbb_container->get('notification_manager');

		$notification = $phpbb_notifications->load_notifications(array(
			'notification_id'	=> $mark_notification,
		));

		if (isset($notification['notifications'][$mark_notification]))
		{
			$notification = $notification['notifications'][$mark_notification];

			$notification->mark_read();

			if ($request->is_ajax())
			{
				$json_response = new \phpbb\json_response();
				$json_response->send(array(
					'success'	=> true,
				));
			}

			if (($redirect = $request->variable('redirect', '')))
			{
				redirect(append_sid($phpbb_root_path . $redirect));
			}

			redirect($notification->get_redirect_url());
		}
	}
}

display_forums('', $config['load_moderators']);

$order_legend = ($config['legend_sort_groupname']) ? 'group_name' : 'group_legend';
// Grab group details for legend display
if ($auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel'))
{
	$sql = 'SELECT group_id, group_name, group_colour, group_type, group_legend
		FROM ' . GROUPS_TABLE . '
		WHERE group_legend > 0
		ORDER BY ' . $order_legend . ' ASC';
}
else
{
	$sql = 'SELECT g.group_id, g.group_name, g.group_colour, g.group_type, g.group_legend
		FROM ' . GROUPS_TABLE . ' g
		LEFT JOIN ' . USER_GROUP_TABLE . ' ug
			ON (
				g.group_id = ug.group_id
				AND ug.user_id = ' . $user->data['user_id'] . '
				AND ug.user_pending = 0
			)
		WHERE g.group_legend > 0
			AND (g.group_type <> ' . GROUP_HIDDEN . ' OR ug.user_id = ' . $user->data['user_id'] . ')
		ORDER BY g.' . $order_legend . ' ASC';
}
$result = $db->sql_query($sql);

$legend = array();
while ($row = $db->sql_fetchrow($result))
{
	$colour_text = ($row['group_colour']) ? ' style="color:#' . $row['group_colour'] . '"' : '';
	$group_name = ($row['group_type'] == GROUP_SPECIAL) ? $user->lang['G_' . $row['group_name']] : $row['group_name'];

	if ($row['group_name'] == 'BOTS' || ($user->data['user_id'] != ANONYMOUS && !$auth->acl_get('u_viewprofile')))
	{
		$legend[] = '<span' . $colour_text . '>' . $group_name . '</span>';
	}
	else
	{
		$legend[] = '<a' . $colour_text . ' href="' . append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=group&amp;g=' . $row['group_id']) . '">' . $group_name . '</a>';
	}
}
$db->sql_freeresult($result);

$legend = implode($user->lang['COMMA_SEPARATOR'], $legend);

// Generate birthday list if required ...
$birthdays = $birthday_list = array();
if ($config['load_birthdays'] && $config['allow_birthdays'] && $auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'))
{
	$time = $user->create_datetime();
	$now = phpbb_gmgetdate($time->getTimestamp() + $time->getOffset());

	// Display birthdays of 29th february on 28th february in non-leap-years
	$leap_year_birthdays = '';
	if ($now['mday'] == 28 && $now['mon'] == 2 && !$time->format('L'))
	{
		$leap_year_birthdays = " OR u.user_birthday LIKE '" . $db->sql_escape(sprintf('%2d-%2d-', 29, 2)) . "%'";
	}

	$sql_ary = array(
		'SELECT' => 'u.user_id, u.username, u.user_colour, u.user_birthday',
		'FROM' => array(
			USERS_TABLE => 'u',
		),
		'LEFT_JOIN' => array(
			array(
				'FROM' => array(BANLIST_TABLE => 'b'),
				'ON' => 'u.user_id = b.ban_userid',
			),
		),
		'WHERE' => "(b.ban_id IS NULL OR b.ban_exclude = 1)
			AND (u.user_birthday LIKE '" . $db->sql_escape(sprintf('%2d-%2d-', $now['mday'], $now['mon'])) . "%' $leap_year_birthdays)
			AND u.user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ')',
	);

	/**
	* Event to modify the SQL query to get birthdays data
	*
	* @event core.index_modify_birthdays_sql
	* @var	array	now			The assoc array with the 'now' local timestamp data
	* @var	array	sql_ary		The SQL array to get the birthdays data
	* @var	object	time		The user related Datetime object
	* @since 3.1.7-RC1
	*/
	$vars = array('now', 'sql_ary', 'time');
	extract($phpbb_dispatcher->trigger_event('core.index_modify_birthdays_sql', compact($vars)));

	$sql = $db->sql_build_query('SELECT', $sql_ary);
	$result = $db->sql_query($sql);
	$rows = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);

	foreach ($rows as $row)
	{
		$birthday_username	= get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);
		$birthday_year		= (int) substr($row['user_birthday'], -4);
		$birthday_age		= ($birthday_year) ? max(0, $now['year'] - $birthday_year) : '';

		$birthdays[] = array(
			'USERNAME'	=> $birthday_username,
			'AGE'		=> $birthday_age,
		);

		// For 3.0 compatibility
		$birthday_list[] = $birthday_username . (($birthday_age) ? " ({$birthday_age})" : '');
	}

	/**
	* Event to modify the birthdays list
	*
	* @event core.index_modify_birthdays_list
	* @var	array	birthdays		Array with the users birhtdays data
	* @var	array	rows			Array with the birhtdays SQL query result
	* @since 3.1.7-RC1
	*/
	$vars = array('birthdays', 'rows');
	extract($phpbb_dispatcher->trigger_event('core.index_modify_birthdays_list', compact($vars)));

	$template->assign_block_vars_array('birthdays', $birthdays);
}

// Assign index specific vars
$template->assign_vars(array(
	'TOTAL_POSTS'	=> $user->lang('TOTAL_POSTS_COUNT', (int) $config['num_posts']),
	'TOTAL_TOPICS'	=> $user->lang('TOTAL_TOPICS', (int) $config['num_topics']),
	'TOTAL_USERS'	=> $user->lang('TOTAL_USERS', (int) $config['num_users']),
	'NEWEST_USER'	=> $user->lang('NEWEST_USER', get_username_string('full', $config['newest_user_id'], $config['newest_username'], $config['newest_user_colour'])),

	'LEGEND'		=> $legend,
	'BIRTHDAY_LIST'	=> (empty($birthday_list)) ? '' : implode($user->lang['COMMA_SEPARATOR'], $birthday_list),

	'FORUM_IMG'				=> $user->img('forum_read', 'NO_UNREAD_POSTS'),
	'FORUM_UNREAD_IMG'			=> $user->img('forum_unread', 'UNREAD_POSTS'),
	'FORUM_LOCKED_IMG'		=> $user->img('forum_read_locked', 'NO_UNREAD_POSTS_LOCKED'),
	'FORUM_UNREAD_LOCKED_IMG'	=> $user->img('forum_unread_locked', 'UNREAD_POSTS_LOCKED'),

	'S_LOGIN_ACTION'			=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=login'),
	'U_SEND_PASSWORD'           => ($config['email_enable']) ? append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=sendpassword') : '',
	'S_DISPLAY_BIRTHDAY_LIST'	=> ($config['load_birthdays']) ? true : false,
	'S_INDEX'					=> true,

	'U_MARK_FORUMS'		=> ($user->data['is_registered'] || $config['load_anon_lastread']) ? append_sid("{$phpbb_root_path}index.$phpEx", 'hash=' . generate_link_hash('global') . '&amp;mark=forums&amp;mark_time=' . time()) : '',
	'U_MCP'				=> ($auth->acl_get('m_') || $auth->acl_getf_global('m_')) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=main&amp;mode=front', true, $user->session_id) : '')
);

$page_title = ($config['board_index_text'] !== '') ? $config['board_index_text'] : $user->lang['INDEX'];

/**
* You can use this event to modify the page title and load data for the index
*
* @event core.index_modify_page_title
* @var	string	page_title		Title of the index page
* @since 3.1.0-a1
*/
$vars = array('page_title');
extract($phpbb_dispatcher->trigger_event('core.index_modify_page_title', compact($vars)));

// Output page
page_header($page_title, true);

$template->set_filenames(array(
	'body' => 'index_body.html')
);

page_footer();
