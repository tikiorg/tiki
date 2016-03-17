<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}
$logslib = TikiLib::lib('logs');


/**
 * This class bundles several social networks functions (twitter, facebook ...)
 * @author cdrwhite
 * @since 6.0
 */
class SocialNetworksLib extends LogsLib
{

	/**
	 * @var	array	options for Twitter Zend functions
	 */
	public $options = array(
			'callbackUrl'    => '',
			'siteUrl'        => 'http://twitter.com/oauth',
			'consumerKey'    => '',
			'consumerSecret' => '',
	);

	/**
	 * retrieves the URL for the current page
	 *
	 * @return string	URL for the current page
	 */
	function getURL()
	{
		$url = 'http';
		$port = '';
		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			$url .= 's';
			if ($_SERVER['SERVER_PORT'] != 443 and strpos($_SERVER['HTTP_HOST'], ':') == 0) {
				$port = ':' . $_SERVER['SERVER_PORT'];
			}
		} else {
			if ($_SERVER['SERVER_PORT'] != 80 and strpos($_SERVER['HTTP_HOST'], ':') == 0) {
				$port = ':' . $_SERVER['SERVER_PORT'];
			}
		}
		$url .= '://' . $_SERVER['HTTP_HOST'] . $port . $_SERVER['REQUEST_URI'];
		return $url;
	}

	/**
	 * Checks if the site is registered with twitter (consumer key and secret are set)
	 *
	 * @return bool	true, if this site is registered with twitter as an application
	 */
	function twitterRegistered()
	{
		global $prefs;
		return ($prefs['socialnetworks_twitter_consumer_key'] != '' and $prefs['socialnetworks_twitter_consumer_secret'] != '');
	}

	/**
	 * If this site is registered with twitter, it redirects to twitter to ask for a request token
	 */
	function getTwitterRequestToken()
	{
		global $prefs;

		if (!$this->twitterRegistered()) {
			return false;
		}

		$this->options['callbackUrl'] = $this->getURL();
		$this->options['siteUrl'] = 'https://api.twitter.com/oauth';
		$this->options['consumerKey'] = $prefs['socialnetworks_twitter_consumer_key'];
		$this->options['consumerSecret'] = $prefs['socialnetworks_twitter_consumer_secret'];

		try {
			$consumer = new ZendOAuth\Consumer($this->options);
			$token = $consumer->getRequestToken();
			$_SESSION['TWITTER_REQUEST_TOKEN'] = serialize($token);
			$consumer->redirect();
		} catch (ZendOAuth\Exception\ExceptionInterface $e) {
			return false;
		}
	}

	/**
	 * When the user confirms the request token, twitter redirects back to our site providing us with a request token.
	 * This function receives a permanent access token for the given user and stores it in his preferences
	 *
	 * @param string $user  user Id of the user to store the access token for
	 *
	 * @return bool 		true on success
	 */
	function getTwitterAccessToken($user)
	{
		global $prefs;

		if ($prefs['socialnetworks_twitter_consumer_key'] == ''
				or $prefs['socialnetworks_twitter_consumer_secret'] == ''
				or !isset($_SESSION['TWITTER_REQUEST_TOKEN'])
		) {
			return false;
		}

		$this->options['callbackUrl'] = $this->getURL();
		$this->options['consumerKey'] = $prefs['socialnetworks_twitter_consumer_key'];
		$this->options['consumerSecret'] = $prefs['socialnetworks_twitter_consumer_secret'];

		$consumer = new ZendOAuth\Consumer($this->options);
		$token = $consumer->getAccessToken($_GET, unserialize($_SESSION['TWITTER_REQUEST_TOKEN']));
		unset($_SESSION['TWITTER_REQUEST_TOKEN']);
		$this->set_user_preference($user, 'twitter_token', serialize($token));
		return true;
	}

	/**
	 * Checks if the site is registered with facebook (application id , api key and secret are set)
	 *
	 * @return bool	true, if this site is registered with facebook as an application
	 */
	function facebookRegistered()
	{
		global $prefs;
		return ($prefs['socialnetworks_facebook_application_id'] != '' and $prefs['socialnetworks_facebook_application_secr'] != '');
	}

	/**
	 * if this site is registered with facebook, it redirects to facebook to ask for a request token
	 */
	function getFacebookRequestToken()
	{
		global $prefs;
		if (!$this->facebookRegistered()) {
			return false;
		}
		$scopes = array();
		if ($prefs['socialnetworks_facebook_offline_access'] == 'y') {
			$scopes[] = 'offline_access';
		}
		if ($prefs['socialnetworks_facebook_publish_stream'] == 'y') {
			$scopes[] = 'publish_stream';
		}
		if ($prefs['socialnetworks_facebook_manage_events'] == 'y') {
			$scopes[] = 'create_event';
			$scopes[] = 'rsvp_event';
		}
		if ($prefs['socialnetworks_facebook_sms'] == 'y') {
			$scopes[] = 'sms';
		}
		if ($prefs['socialnetworks_facebook_manage_pages'] == 'y') {
			$scopes[] = 'manage_pages';
		}
		if ($prefs['socialnetworks_facebook_email'] === 'y') {
			$scopes[] = 'email';
		}
		$scope = implode(',', $scopes);
		$url = $this->getURL();
		if (strpos($url, '?') != 0) {
			$url = preg_replace('/\?.*/', '', $url);
		}
		$url = urlencode($url.'?request_facebook');
		$url = 'https://www.facebook.com/v2.0/dialog/oauth?client_id=' . $prefs['socialnetworks_facebook_application_id'] .
			'&scope=' . $scope . '&redirect_uri='.$url;
		header("Location: $url");
		die();
	}

	/**
	 * When the user confirms the request token, facebook redirects back to our site providing us with a request token.
	 * This function receives a permanent access token for the given user and stores it in his preferences
	 *
	 * @param string $user  user Id of the user to store the access token for
	 *
	 * @return bool 		true on success
	 */
	function getFacebookAccessToken()
	{
		global $prefs, $user;
		$userlib = TikiLib::lib('user');
		if ($prefs['socialnetworks_facebook_application_id'] == '' or $prefs['socialnetworks_facebook_application_secr'] == '') {
			return false;
		}

		$url = '/v2.0/oauth/access_token?client_id=' . $prefs['socialnetworks_facebook_application_id'] .
			'&redirect_uri=' . $this->getURL() .'&client_secret=' . $prefs['socialnetworks_facebook_application_secr']; // code is already in the url


		$request = "GET $url HTTP/1.1\r\n".
			"Host: graph.facebook.com\r\n".
			"Accept: */*\r\n".
			"Expect: 100-continue\r\n".
			"Connection: close\r\n\r\n";

		$fp = fsockopen('ssl://graph.facebook.com', 443);
		if ($fp === false) {
			$this->add_log('getFacebookAccessToken', "can't connect");
			return false;
		} else {
			fputs($fp, $request);
			$ret = '';
			while (!feof($fp)) {
				$ret .= fgets($fp, 128);
			}
			fclose($fp);
		}
		$ret = preg_split('/(\r\n\r\n|\r\r|\n\n)/', $ret, 2);
		$ret = $ret[1];

		if (substr($ret, 0, 13) == 'access_token=') {
			$access_token = substr($ret, 13);
			if ($endoftoken = strpos($access_token, '&')) {
				// Returned string may have other var like expiry
				$access_token = substr($access_token, 0, $endoftoken);
			}
			$fields = array('id', 'name', 'first_name', 'last_name');

			if ($prefs['socialnetworks_facebook_email'] == 'y') {
				$fields[] = 'email';
			}

			$fb_profile = json_decode($this->facebookGraph('', 'me', array('fields' => implode(',', $fields),'access_token' => $access_token), false, 'GET'));
			if (empty($fb_profile->id)) {
				return false;
			}
			if (!$user) {
				if ($prefs['socialnetworks_facebook_login'] != 'y') {
					return false;
				}
				$local_user = $this->getOne("select `user` from `tiki_user_preferences` where `prefName` = 'facebook_id' and `value` = ?", array($fb_profile->id));
				if ($local_user) {
					$user = $local_user;
				} elseif ($prefs['socialnetworks_facebook_autocreateuser'] == 'y') {
					$randompass = $userlib->genPass();
					$email = $prefs['socialnetworks_facebook_email'] === 'y' ? $fb_profile->email : '';
					if ($prefs['login_is_email'] == 'y' && $email) {
						$user = $email;
					} else if ($prefs['login_autogenerate'] == 'y') {
						$user = '';
					} else {
						$user = 'fb_' . $fb_profile->id;
					}
					$user = $userlib->add_user($user, $randompass, $email);

					if (!$user) {
						$smarty = TikiLib::lib('smarty');
						$smarty->assign('errortype', 'login');
						$smarty->assign('msg', tra('We were unable to log you in using your Facebook account. Please contact the administrator.'));
						$smarty->display('error.tpl');
						die;
					}

					$ret = $userlib->get_usertrackerid("Registered");
					$userTracker = $ret['usersTrackerId'];
					$userField = $ret['usersFieldId'];
					if ($userTracker && $userField) {
						$definition = Tracker_Definition::get($userTracker);
						$utilities = new Services_Tracker_Utilities();
						$fields = array('ins_'.$userField => $user);
						if (!empty($prefs['socialnetworks_facebook_names'])) {
							$names = array_map('trim', explode(',', $prefs['socialnetworks_facebook_names']));
							$fields['ins_'.$names[0]] = $fb_profile->first_name;
							$fields['ins_'.$names[1]] = $fb_profile->last_name;
						}
						$utilities->insertItem($definition,
							array(
								'status' => '',
								'fields' => $fields,
							)
						);
					}

					$this->set_user_preference($user, 'realName', $fb_profile->name);
					if ($prefs['socialnetworks_facebook_firstloginpopup'] == 'y') {
						$this->set_user_preference($user, 'socialnetworks_user_firstlogin', 'y');
					}
					if ($prefs['feature_userPreferences'] == 'y') {
						$fb_avatar = json_decode($this->facebookGraph('', 'me/picture', array('type' => 'square', 'width' => '480', 'redirect' => '0','access_token' => $access_token), false, 'GET'));
						$avatarlib = TikiLib::lib('avatar');
						$avatarlib->set_avatar_from_url($fb_avatar->data->url, $user);
					}
				} else {
					$smarty = TikiLib::lib('smarty');
					$smarty->assign('errortype', 'login');
					$smarty->assign('msg', tra('You need to link your local account to Facebook before you can login using it'));
					$smarty->display('error.tpl');
					die;
				}
				global $user_cookie_site;
				$_SESSION[$user_cookie_site] = $user;
				$userlib->update_expired_groups();
				$this->set_user_preference($user, 'facebook_id', $fb_profile->id);
				$this->set_user_preference($user, 'facebook_token', $access_token);
				$userlib->update_lastlogin($user);
				header('Location: tiki-index.php');
				die;
			} else {
				$this->set_user_preference($user, 'facebook_id', $fb_profile->id);
				$this->set_user_preference($user, 'facebook_token', $access_token);
			}
			return true;
		} else {
			$smarty = TikiLib::lib('smarty');
			$smarty->assign('errortype', 'login');
			$smarty->assign('msg', tra('We were unable to log you in using your Facebook account. Please contact the administrator.'));
			$smarty->display('error.tpl');
			die;
		}
	}

	/**
	 * Checks if the site is registered with linkedIn (client id  and secret are set)
	 *
	 * @return bool	true, if this site is registered with linkedIn as an application
	 */
	function linkedInRegistered(){
		global $prefs;
		return ($prefs['socialnetworks_linkedin_client_id'] != '' and $prefs['socialnetworks_linkedin_client_secr'] != '');
	}

	function getLinkedInRequestToken()
	{
		global $prefs;
		if (!$this->linkedInRegistered()) {
			return false;
		}
		$scopes = array();
		$scopes[] = 'r_basicprofile';
		if ($prefs['socialnetworks_linkedin_email'] == 'y') {
			$scopes[] = 'r_emailaddress';
		}
		$scope = implode(' ', $scopes);

		//generate a random state token to pass to linked in to verify on response to protect against CSRF
		$state = md5((string) rand());
		$_SESSION['LINKEDIN_REQ_STATE'] = $state;

		$url = $this->getURL();
		if (strpos($url, '?') != 0) {
			$url = preg_replace('/\?.*/', '', $url);
		}
		$_SESSION['LINKEDIN_CALLBACK_URL'] = $url;
		$url = 'https://www.linkedin.com/uas/oauth2/authorization?response_type=code&client_id=' . $prefs['socialnetworks_linkedin_client_id'] .
			'&state=' . $state . '&scope=' . $scope . '&redirect_uri=' . $url;
		header("Location: $url");
		die();
	}

	function getLinkedInAccessToken() {
		global $prefs;
		if (!$this->linkedInRegistered()) {
			return false;
		}

		$curl_request = curl_init();
		curl_setopt_array($curl_request, array(
			CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => 'https://www.linkedin.com/uas/oauth2/accessToken',
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => http_build_query (array(
				client_secret => $prefs['socialnetworks_linkedin_client_secr'],
				client_id => $prefs['socialnetworks_linkedin_client_id'],
				client_secret => $prefs['socialnetworks_linkedin_client_secr'],
				grant_type => "authorization_code",
				redirect_uri => $_SESSION['LINKEDIN_CALLBACK_URL'],
				code => $_SESSION['LINKEDIN_AUTH_CODE'],
			), '', '&'),
		));

		$curl_result = curl_exec($curl_request);
		$ret = json_decode($curl_result);

		if (empty($curl_result)) {
			$smarty = TikiLib::lib('smarty');
			$smarty->assign('errortype', 'login');
			$smarty->assign('msg', tra('We were unable to connect to your LinkedIn account. Please contact the administrator.'));
                        $smarty->display('error.tpl');
			die;
		}

		$_SESSION['LINKEDIN_ACCESS_TOKEN'] = $ret->access_token;
		$_SESSION['LINKEDIN_ACCESS_TOKEN_EXPIRY'] = time() + $ret->expires_in;

		$this->linkedInLogin();
		return true;

	}

	function linkedInLogin() {
		global $user, $prefs;
		$userlib = TikiLib::lib('user');
		$curl = curl_init();

		$data = array(
			"oauth2_access_token"=>$_SESSION['LINKEDIN_ACCESS_TOKEN'],
			"format"=>"json"
		);
		$profile_fields = array(
			'id',
			'first-name',
			'last-name',
			'formatted-name',
			'picture-url',
			'picture-urls::(original)',
		);

		if ($prefs['socialnetworks_linkedin_email'] == 'y') {
			$profile_fields[] = 'email-address';
		}

		$url = "https://api.linkedin.com/v1/people/~:(". implode(",",$profile_fields) .")";
		$url = sprintf("%s?%s", $url, http_build_query($data, '', '&'));
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($curl);
		curl_close($curl);

		$linkedin_info = json_decode($result);
		if (isset($linkedin_info->errorCode)) {
			$smarty = TikiLib::lib('smarty');
			$smarty->assign('errortype', 'login');
			$smarty->assign('msg', tra('We were unable to log you in using your LinkedIn account. Please contact the administrator.'));
			$smarty->display('error.tpl');
			die;
		}
		if (!$user) {
			if ($prefs['socialnetworks_linkedin_login'] != 'y') {
				return false;
			}
			$local_user = $this->getOne("select `user` from `tiki_user_preferences` where `prefName` = 'linkedin_id' and `value` = ?", array($linkedin_info->id));
			if ($local_user) {
				$user = $local_user;
			} elseif ($prefs['socialnetworks_linkedin_autocreateuser'] == 'y') {
				$randompass = $userlib->genPass();
				$email = $prefs['socialnetworks_linkedin_email'] === 'y' ? $linkedin_info->emailAddress : '';
				if ($prefs['login_is_email'] == 'y' && $email) {
					$user = $email;
				} else if ($prefs['login_autogenerate'] == 'y') {
						$user = '';
				} else {
					$user = 'li_' . $linkedin_info->id;
				}
				$user = $userlib->add_user($user, $randompass, $email);

				if (!$user) {
					$smarty = TikiLib::lib('smarty');
					$smarty->assign('errortype', 'login');
					$smarty->assign('msg', tra('We were unable to log you in using your LinkedIn account. Please contact the administrator.'));
					$smarty->display('error.tpl');
					die;
				}
				//Checks if user tracker is used and if it is, then set the names as per the info
				$ret = $userlib->get_usertrackerid("Registered");
				$userTracker = $ret['usersTrackerId'];
				$userField = $ret['usersFieldId'];
				if ($userTracker && $userField) {
					$definition = Tracker_Definition::get($userTracker);
					$utilities = new Services_Tracker_Utilities();
					$fields = array('ins_'.$userField => $user);
					if (!empty($prefs['socialnetworks_linkedin_names'])) {
						$names = array_map('trim', explode(',', $prefs['socialnetworks_linkedin_names']));
						$fields['ins_'.$names[0]] = $linkedin_info->firstName;
						$fields['ins_'.$names[1]] = $linkedin_info->lastName;
					}
					$utilities->insertItem($definition,
						array(
							'status' => '',
							'fields' => $fields,
						)
					);
				}

				$this->set_user_preference($user, 'realName', $linkedin_info->formattedName);
				if ($prefs['feature_userPreferences'] == 'y') {
					$avatarlib = TikiLib::lib('avatar');
					$avatarlib->set_avatar_from_url($linkedin_info->pictureUrls->values[0], $user);
				}
			} else {
				$_SESSION['loginfrom'] = str_replace('tiki-socialnetworks_linkedin.php', 'tiki-socialnetworks.php', $_SERVER['REQUEST_URI']);
				$smarty = TikiLib::lib('smarty');
				$smarty->assign('errortype', 'login');
				$smarty->assign('msg', tra('You need to link your local account to LinkedIn before you can login using it'));
				$smarty->display('error.tpl');
				die;
			}
			global $user_cookie_site;
			$_SESSION[$user_cookie_site] = $user;
			$userlib->update_expired_groups();
			$this->set_user_preference($user, 'linkedin_id', $linkedin_info->id);
			$this->set_user_preference($user, 'linkedin_token', $_SESSION['LINKEDIN_ACCESS_TOKEN']);
			$userlib->update_lastlogin($user);
			header('Location: tiki-index.php');
			die;
		} else {
			$this->set_user_preference($user, 'linkedin_id', $linkedin_info->id);
			$this->set_user_preference($user, 'linkedin_token', $_SESSION['LINKEDIN_ACCESS_TOKEN']);
		}
		return true;
	}

	/**
	 * Sends a tweet via Twitter
	 *
	 * @param string	$message	Message to send
	 * @param string	$user		UserId of the user to send the message for
	 * @param bool		$cutMessage	Should the message be cut if it is longer than 140 characters,
	 *								if set to false, an error will be returned if the message is longer than 140 characters
	 *
	 * @return int	-1 if the user did not authorize the site with twitter,
	 *							-2, if the message is longer than 140 characters,
	 *							a negative number corresponding to the HTTP response codes from twitter
	 *							(http://dev.twitter.com/pages/streaming_api_response_codes)
	 *  							or a positive tweet id of the message
	 */
	function tweet($message, $user, $cutMessage = false)
	{
		global $prefs;
		$token = $this->get_user_preference($user, 'twitter_token', '');
		if ($token == '') {
			$this->add_log('tweet', 'user not registered with twitter');
			return -1;
		}
		if ($cutMessage) {
			$message = substr($message, 0, 140);
		} else {
			if (strlen($message) > 140) {
				$this->add_log('tweet', 'message too long');
				return -2;
			}
		}
		$token = unserialize($token);

		$this->options['callbackUrl'] = $this->getURL();
		$this->options['consumerKey'] = $prefs['socialnetworks_twitter_consumer_key'];
		$this->options['consumerSecret'] = $prefs['socialnetworks_twitter_consumer_secret'];
		$twitter = new ZendService\Twitter\Twitter(
			array(
				'oauthOptions' => array(
					'consumerKey' => $prefs['socialnetworks_twitter_consumer_key'],
					'consumerSecret' => $prefs['socialnetworks_twitter_consumer_secret'],
				),
				'accessToken' => $token
			)
		);

		try {
			$response = $twitter->statuses->update($message);
		} catch (ZendService\Twitter\Exception\ExceptionInterface $e) {
			$this->add_log('tweet', 'twitter error ' . $e->getMessage());
			return -($e->getCode());
		}

		if (!$response->isSuccess()) {
			$errors = $response->getErrors();
			$this->add_log('tweet', 'twitter response: ' . $errors[0]->message . ' - Code: ' . $errors[0]->code);
			return -$errors['code'];
		} else {
			$id = $response->toValue();
			return $id->id_str;
		}
	}

	/**
	 * Deletes a tweet with the given tweet id
	 *
	 * @param int		$id		Id of the tweet to delete
	 * @param string	$user		UserId of the user who sent the tweet
	 *
	 * @return bool					true on success
	 */
	function destroyTweet($id, $user)
	{
		global $prefs;
		$token = $this->get_user_preference($user, 'twitter_token', '');
		if ($token == '') {
			return false;
		}
		$token = unserialize($token);
		$this->options['callbackUrl'] = $this->getURL();
		$this->options['consumerKey'] = $prefs['socialnetworks_twitter_consumer_key'];
		$this->options['consumerSecret'] = $prefs['socialnetworks_twitter_consumer_secret'];
		$twitter = new ZendService\Twitter\Twitter(
			array(
				'oauthOptions' => array(
					'consumerKey' => $prefs['socialnetworks_twitter_consumer_key'],
					'consumerSecret' => $prefs['socialnetworks_twitter_consumer_secret'],
				),
				'accessToken' => $token
			)
		);
		try {
			$response = $twitter->statuses->destroy($id);
		} catch(ZendService\Twitter\Exception\ExceptionInterface $e)	{
			return false;
		}
		return true;
	}

	/**
	 * Talking to Facebook via the graph api at "https://graph.facebook.com/" using fsockopen
	 *
	 * @param	string	$user		userId of the user to send the request for
	 * @param	string	$action		directory/file part of the graph api URL
	 * @param	array	$params		parameters for the api call, each entry is one element submitted in the request
	 * @param	bool	$addtoken	should the access token be added to the parameters if the calling function did not pass this parameter
	 *
	 * @return	string				body of the response page (json encoded object)
	 */
	function facebookGraph($user, $action, $params, $addtoken = true, $method = 'POST')
	{
		global $prefs;
		if (!$this->facebookRegistered()) {
			$this->add_log('facebookGraph', 'application not set up');
			return false;
		}
		if ($addtoken) {
			$token = $this->get_user_preference($user, 'facebook_token', '');
			if ($token == '') {
				$this->add_log('facebookGraph', 'user not registered with facebook');
				return false;
			}

			if (!isset($params['access_token'])) {
				$params['access_token'] = $token;
			}
		}

		$data = http_build_query($params, '', '&');
		if ($method == 'GET') {
			$action .= "?$data";
		}

		$request = "$method /v2.0/$action HTTP/1.1\r\n".
			"Host: graph.facebook.com\r\n".
			"Accept: */*\r\n".
			"Expect: 100-continue\r\n".
			"Connection: close\r\n";
		if ($method == 'POST') {
			$request .= "Content-type: application/x-www-form-urlencoded\r\n".
				"Content-length: ". strlen($data) ."\r\n";
		}
		$request .= "\r\n";
		if ($method == 'POST') {
			$request .= $data;
		}

		$fp = fsockopen('ssl://graph.facebook.com', 443);
		if ($fp === false) {
			$this->add_log('facebookGraph', "can't connect");
			return false;
		} else {
			fputs($fp, $request);
			$ret = '';
			while (!feof($fp)) {
				$ret .= fgets($fp, 128);
			}
			fclose($fp);
		}
		$ret = preg_split('/(\r\n\r\n|\r\r|\n\n)/', $ret, 2);
		return $ret[1];
	}

	/**
	 *
	 * publish a message (status or link with more options) on facebook
	 *
	 * @param string	$user		userId of the user to send for
	 * @param string	$message	message/main text to send
	 * @param string	$url		optional URL to pass along
	 * @param string	$text		optional text to show for the URL
	 * @param string	$caption	optional caption of the message accompanying the url
	 * @param string	$privacy	currently unused as I did not find the docu on how to use the privacy settings
	 *
	 * @return	string|bool			false on error, object Id of the message on success
	 */
	function facebookWallPublish($user, $message, $url = '', $text = '', $caption = '', $privacy = '')
	{
		$params = array();
		if ($url != '') {
			$params['link'] = $url;
			if ($text != '') {
				$params['name'] = $text;
			}
			if ($caption != '') {
				$params['caption'] = $caption;
			}
			$params['description'] = $message;
		} else {
			$params['message'] = substr($message, 0, 400);
		}
		$ret = $this->facebookGraph($user, 'me/feed/', $params);
		$result = json_decode($ret);
		if (isset($result->id)) {
			return $result->id;
		} else {
			return false;
		}
	}

	/**
	 * like an object on facebook
	 *
	 * @param string	$user		userId of the user to send for
	 * @param string	$facebookId	id of the object to like
	 *
	 * @return	string|bool			false on error, object Id of the message on success
	 */
	function facebookLike($user, $id)
	{
		$params = array();
		$ret = $this->facebookGraph($user, "$id/likes/", $params);
		return json_decode($ret);
	}

	/**
	 * Talking to bit.ly api at "http://api.bit.ly/" using Zend
	 *
	 * @param	string	$user		userId of the user to send the request for
	 * @param	string	$action		directory/file part of the api URL
	 * @param	array	$params		parameters for the api call, each entry is one element submitted in the request
	 *
	 * @return	string				body of the response page (json encoded object)
	 */
	function bitlyApi($user, $action, $params)
	{
		global $prefs;

		if ($prefs['socialnetworks_bitly_sitewide'] != 'y') {
			$login = $this->get_user_preference($user, 'bitly_login', '');
		}
		if ($login == '') {
			$login = $prefs['socialnetworks_bitly_login'];

			if ($login == '') {
				return false;
			}

			$key = $prefs['socialnetworks_bitly_key'];
		} else {
			$key = $this->get_user_preference($user, 'bitly_key', '');
		}
		if ($key == '') {
			return false;
		}

		$httpclient = TikiLib::lib('tiki')->get_http_client();
		$httpclient->setUri("http://api.bit.ly/$action");

		$params['login'] = $login;
		$params['apiKey'] = $key;
		$httpclient->setParameterGet($params);

		$response = $httpclient->send();
		if (!$response->isSuccess() ) {
			return false;
		}
		return $response->getBody();
	}

	/**
	 * Asks bit.ly to shorten an url for us
	 *
	 * @param $user
	 * @param $url
	 */
	function bitlyShorten($user, $url)
	{
		$query = 'SELECT * FROM `tiki_url_shortener` WHERE `longurl_hash`=MD5(?)';

		$result = $this->query($query, array($url));

		while ($data = $result->fetchRow()) {
			if ($url == $data['longurl']) {
				return $data['shorturl'];
			}
		}

		$params = array(
				'version' => '2.0.1',
				'longUrl' => $url,
				'history' => '1',
		);

		$ret = $this->bitlyApi($user, 'shorten', $params);
		if ($ret == false) {
			return false;
		}

		$ret = json_decode($ret);
		if ($ret->errorCode != 0) {
			return false;
		}

		$shorturl = $ret->{'results'}->{$url}->{'shortUrl'};
		$query = 'INSERT INTO `tiki_url_shortener` SET `user`=?, `longurl`=?, `longurl_hash`=MD5(?), `service`=?, `shorturl`=?';
		$this->query($query, array($user, $url, $url, 'bit.ly', $shorturl));

		return $shorturl;
	}

	/**
	 * Get Timeline off Twitter
	 * @param  string	$user		Tiki username to get timeline for
	 * @param  string	$timelineType	Timeline to get: public/friends/search - Default: public
	 * @param  string	$search		Search string
	 * @return string|int			-1 if the user did not authorize the site with twitter, a negative number corresponding to the HTTP response codes from twitter (https://dev.twitter.com/docs/streaming-api/response-codes) or the requested timeline (json encoded object)
	 */
	function getTwitterTimeline($user, $timelineType = 'public', $search = 'tikiwiki' )
	{
		global $prefs;
		$token=$this->get_user_preference($user, 'twitter_token', '');
		if ($token=='') {
			$this->add_log('tweet', 'user not registered with twitter');
			return -1;
		}

		$token = unserialize($token);

		$twitter = new ZendService\Twitter\Twitter(
			array(
				'oauthOptions' => array(
					'consumerKey' => $prefs['socialnetworks_twitter_consumer_key'],
					'consumerSecret' => $prefs['socialnetworks_twitter_consumer_secret'],
				),
				'accessToken' => $token
			)
		);

		if ($timelineType=='friends') {
			$response = $twitter->statuses->homeTimeline();
		} elseif ($timelineType == 'search') {
			$response = $twitter->search->tweets($search, array('include_entities' => true));
		} else {
			$response = $twitter->statuses->userTimeline();
		}

		if (!$response->isSuccess()) {
			$errors = $response->getErrors();
			$this->add_log('tweet', 'twitter response: ' . $errors[0]->message . ' - Code: ' . $errors[0]->code);
			return -$errors['code'];
		} else {
			return $response->toValue();
		}
	}

	/**
	 *
	 * get the public Facebook timeline of a user
	 *
	 * @param string	$user		Tiki username to get facebook wall for
	 * @param bool		$addtoken	should the access token be added to the parameters if the calling function did not pass this parameter
	 *
	 * @return		string|bool	false on error, JSON encoded Facebook response on success
	 */
	function facebookGetWall($user, $addtoken=true)
	{
		global $prefs;
		if (!$this->facebookRegistered()) {
			$this->add_log('facebookGraph', 'application not set up');
			return false;
		}
		if ($addtoken) {
			$token=$this->get_user_preference($user, 'facebook_token', '');
			// expires will make the token fail
			$token = preg_replace('/&expires=(\d)*/', '', $token);
			$token=urlencode($token);
			$getdata='?'.urlencode('access_token').'='.$token;
			if ($token=='') {
				$this->add_log('facebookGraph', 'user not registered with facebook');
				return -1;
			}

		}

			$request="GET /v2.0/me/feed".$getdata." HTTP/1.1\r\n".
			 "Host: graph.facebook.com\r\n".
			 "Accept:*/*\r\n".
			 "Accept-Charset:ISO-8859-1,utf-8;q=0.7,*;q=0.3\r\n".
			 "Expect: 100-continue\r\n".
			 "Connection: close\r\n\r\n";

  		$fp = fsockopen("ssl://graph.facebook.com", 443);
  		if ($fp===false) {
			$this->add_log('facebookGraph', "can't connect");
			return false;
  		} else {
			//stream_set_timeout($fp, 0, 5000);
	  		fputs($fp, $request);
	  		$ret='';
			while (!feof($fp)) {
				$ret .= fgets($fp, 128);
	  		}
			fclose($fp);
  		}
		$ret=preg_split('/(\r\n\r\n|\r\r|\n\n)/', $ret, 2);
		$result=json_decode($ret[1]);
		foreach ($result->data as $key=>$value) {
			if (isset($result->data[$key]->message)) {
				$feed[$key]["message"]=$result->data[$key]->message;
				$feed[$key]["type"]="message";
			} else {
				$feed[$key]["message"]=$result->data[$key]->story;
				$feed[$key]["type"]="story";
			}
			$feed[$key]["fromName"]=$result->data[$key]->from->name;
			$feed[$key]["fromId"]=$result->data[$key]->from->id;
			$feed[$key]["created_time"]=$result->data[$key]->created_time;
			$id=$result->data[$key]->id;
			$id=str_replace("_", "/posts/", $id);
			$feed[$key]["link"]="https://www.facebook.com/".$id;
		}
		return $feed;
	}
}

global $socialnetworkslib;

$socialnetworkslib = new socialNetworksLib;
