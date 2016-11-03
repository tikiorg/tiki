<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("Location: ../index.php");
	die;
}


/**
 * TikiAccessLib
 *
 * @uses TikiLib
 *
 */
class TikiAccessLib extends TikiLib
{
	private $noRedirect = false;

	function preventRedirect($prevent)
	{
		$this->noRedirect = (bool) $prevent;
	}

	/**
	 * check that the user is admin or has admin permissions
	 *
	 */
	function check_admin($user, $feature_name = '')
	{
		global $tiki_p_admin, $prefs;
		require_once ('tiki-setup.php');
		// first check that user is logged in
		$this->check_user($user);

		if (($user != 'admin') && ($tiki_p_admin != 'y')) {
			$msg = tra("You do not have permission to use this feature");
			if ($feature_name) {
				$msg = $msg . ": " . $feature_name;
			}
			$this->display_error('', $msg, '403');
		}
	}

    /**
     * @param $user
     */
    function check_user($user)
	{
		global $prefs;
		require_once ('tiki-setup.php');

		if (!$user) {
			$title = tra("You are not logged in");
			$this->display_error('', $title, '403');
		}
	}

    /**
     * @param string $user
     * @param array $features
     * @param array $permissions
     * @param string $permission_name
     */
    function check_page($user = 'y', $features = array(), $permissions = array(), $permission_name = '')
	{
		require_once ('tiki-setup.php');

		if ( $features ) {
			$this->check_feature($features);
		}
		$this->check_user($user);

		if ( $permissions ) {
			$this->check_permission($permissions, $permission_name);
		}
	}

	/**
	 * check_feature: Checks if a feature or a list of features are activated
	 *
	 * @param string or array $features If just a string, this method will only test that one. If an array, all features will be tested
	 * @param string $feature_name Name that will be printed on the error screen
	 * @param string $relevant_admin_panel Admin panel where the feature can be set to 'Y'. This link is provided on the error screen
	 * @access public
	 * @return void
	 *
	 */
	function check_feature($features, $feature_name = '', $relevant_admin_panel = 'features', $either = false)
	{
		global $prefs;
		require_once ('tiki-setup.php');

		$perms = Perms::get();

		if ( $perms->admin && isset($_REQUEST['check_feature']) && isset($_REQUEST['lm_preference']) ) {
			$prefslib = TikiLib::lib('prefs');
			$prefslib->applyChanges((array) $_REQUEST['lm_preference'], $_REQUEST);
		}

		if ( ! is_array($features) ) {
			$features = array($features);
		}

		if ( $either ) {
			// if anyone will do, start assuming no go and test for feature
			$allowed = false;
		} else {
			// if all is needed, start assuming it's a go and test for feature not on
			$allowed = true;
		}

		foreach ($features as $feature) {
			if (!$either && $prefs[$feature] != 'y') {
				if ($feature_name != '') {
					$feature = $feature_name;
				}
				$allowed = false;
				break;
			} elseif ($either && $prefs[$feature] == 'y') {
				// test for feature in "anyone will do" case
				$allowed = true;
				break;
			}
		}

		if ( !$allowed ) {
			$smarty = TikiLib::lib('smarty');

			if ( $perms->admin ) {
				$smarty->assign('required_preferences', $features);
			}

			$msg = tr(
				'Required features: <b>%0</b>. If you do not have permission to activate these features, ask the site administrator.',
				implode(', ', $features)
			);

			$this->display_error('', $msg, 'no_redirect_login');
		}
	}

	/**
	 * Check permissions for current user and display an error if not granted
	 * Multiple perms can be checked at once using an array and all those perms need to be granted to continue
	 *
	 * @param string|array $permissions		permission name or names (can be old style e.g. 'tiki_p_view' or just 'view')
	 * @param string $permission_name		text used in warning if perm not granted
	 * @param bool|string $objectType		optional object type (e.g. 'wiki page')
	 * @param bool|string $objectId			optional object id (e.g. 'HomePage' or '42' depending on object type)
	 */
    function check_permission($permissions, $permission_name = '', $objectType = false, $objectId = false)
	{
		require_once ('tiki-setup.php');

		if ( ! is_array($permissions) ) {
			$permissions = array($permissions);
		}

		foreach ($permissions as $permission) {
			if (false !== $objectType) {
				$applicable = Perms::getCombined($objectType, $objectId);
			} else {
				$applicable = Perms::get();
			}

			if ($applicable->$permission) {
				continue;
			}

			if ($permission_name) {
				$permission = $permission_name;
			}
			$this->display_error('', tra("You do not have permission to use this feature:")." ". $permission, '403', false);
			if (empty($GLOBALS['user'])) {
				$_SESSION['loginfrom'] = $_SERVER['REQUEST_URI'];
			}
		}
	}

	/**
	 * Check permissions for current user and display an error if not granted
	 * Multiple perms can be checked at once using an array and ANY ONE OF those perms only needs to be granted to continue
	 *
	 * NOTE that you do NOT have to use this to include admin perms, as admin perms automatically inherit the perms they are admin of
	 *
	 * @param string|array $permissions		permission name or names (can be old style e.g. 'tiki_p_view' or just 'view')
	 * @param string $permission_name		text used in warning if perm not granted
	 * @param bool|string $objectType		optional object type (e.g. 'wiki page')
	 * @param bool|string $objectId			optional object id (e.g. 'HomePage' or '42' depending on object type)
	 */
	function check_permission_either($permissions, $permission_name = '', $objectType = false, $objectId = false)
	{
		require_once ('tiki-setup.php');
		$allowed = false;

		if ( ! is_array($permissions) ) {
			$permissions = array($permissions);
		}

		foreach ($permissions as $permission) {
			if (false !== $objectType) {
				$applicable = Perms::getCombined($objectType, $objectId);
			} else {
				$applicable = Perms::get();
			}

			if ($applicable->$permission) {
				$allowed = true;
				break;
			}
		}

		if ( !$allowed ) {
			if ($permission_name) {
				$permission = $permission_name;
			} else
				$permission = implode(', ', $permissions);

			$this->display_error('', tra("You do not have permission to use this feature").": ". $permission, '403', false);
		}
	}

	/**
	 * check permission, where the permission is normally unset
	 *
	 */
	function check_permission_unset($permissions, $permission_name)
	{
		require_once ('tiki-setup.php');

		foreach ($permissions as $permission) {
			global $$permission;
			if ((isset($$permission) && $$permission == 'n')) {
				if ($permission_name) {
					$permission = $permission_name;
				}
				$this->display_error('', tra("You do not have permission to use this feature").": ". $permission, '403', false);
			}
		}
	}

	/**
	 * check page exists
	 *
	 */
	function check_page_exists($page)
	{
		require_once ('tiki-setup.php');
		if (!$this->page_exists($page)) {
			$this->display_error($page, tra("Page cannot be found"), '404');
		}
	}

	/**
	 *  Check whether script was called directly or included
	 *  err and die if called directly
	 *  Typical usage: $access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));
	 *
	 *  if feature_redirect_on_error is active, then just goto the Tiki HomePage as configured
	 *  in Admin->General. -- Damian
	 *
	 */
	function check_script($scriptname, $page)
	{
		global $prefs;
		if (basename($scriptname) == $page) {
			if ( !isset($prefs['feature_usability']) || $prefs['feature_usability'] == 'n' ) {
				$msg = tra("This script cannot be called directly");
				$this->display_error($page, $msg);
			} else {
				$msg = tr("Page '%0' cannot be found", $page);
				$this->display_error($page, $msg, "404");
			}
		}
	}

	/**
	 *  Checks whether the request was willingly submitted by the user, instead of being triggered by Cross-Site Request Forgery.
	 *  This uses random tokens. The first call brings to a request confirmation screen with
	 *   a new token in the form. The second call, in the second request, verifies the submitted token matches.
	 *  Typical usage: $access->check_authenticity();
	 *  Warning: this mechanism does not allow passing uploaded files ($_FILES). For that, see check_ticket().

	 * @param string $confirmation_text Text on the confirmation screen. Default: 'Click here to confirm your action'
	 * @access public
	 * @return array
	 */
	function check_authenticity($confirmation_text = '', $returnHtml = true)
	{
		global $prefs, $jitRequest;
		if (isset($_REQUEST['daconfirm'])) {
			$daconfirm = $_REQUEST['daconfirm'];
		} elseif (isset($jitRequest['daconfirm'])) {
			$daconfirm = $jitRequest->daconfirm->alpha();
		}
		if ($prefs['feature_ticketlib2'] == 'y' || $returnHtml === false) {
			if (isset($daconfirm)) {
				if ($returnHtml) {
					key_check();
				} else {
					$ret = key_check(null, false);
				}
			} else {
				if ($returnHtml) {
					key_get(null, $confirmation_text);
				} else {
					$ret = key_get(null, null, null, false);
				}
			}
			if (!$returnHtml) {
				return $ret;
			}
		}
	}

    /**
     * @return bool
     */
    function check_ticket()
	{
		global $prefs, $user;
		$smarty = TikiLib::lib('smarty');

		if ($prefs['feature_ticketlib2'] == 'y') {
			if (empty($user) || (isset($_REQUEST['ticket']) && isset($_SESSION['ticket']) && $_SESSION['ticket'] == $_REQUEST['ticket'])) {
				return true;
			}
			// TODO: Improve feedback and allow proceeding by confirming the request. $_REQUEST needs to be saved and restored.
			$smarty->assign('msg', tra('Possible cross-site request forgery (CSRF, or \"sea surfing\") detected. Operation blocked.'));
			$smarty->display("error.tpl");
			exit();
		}
	}

    /**
     * @param $page
     * @param string $errortitle
     * @param string $errortype
     * @param bool $enableRedirect
     * @param string $message
     */
    function display_error($page, $errortitle = "", $errortype = "", $enableRedirect = true, $message = '')
	{
		global $prefs, $tikiroot, $user;
		require_once ('tiki-setup.php');
		$userlib = TikiLib::lib('user');
		$smarty = TikiLib::lib('smarty');

		// Don't redirect when calls are made for web services
		if ( $enableRedirect && $prefs['feature_redirect_on_error'] == 'y' && ! $this->is_machine_request()
				&& $tikiroot.$prefs['tikiIndex'] != $_SERVER['PHP_SELF'] && $page != $userlib->get_user_default_homepage2($user) ) {
			$this->redirect($prefs['tikiIndex']);
		}

		$detail = array(
				'code' => $errortype,
				'errortitle' => $errortitle,
				'message' => $message,
		);

		if ( !isset($errortitle) ) {
			$detail['errortitle'] = tra('unknown error');
		}

		if ( empty($message)) {
			$detail['message'] = $detail['errortitle'];
		}

		// Display the template
		switch ( $errortype ) {
			case '404':
				header("HTTP/1.0 404 Not Found");
				$detail['page'] = $page;
				$detail['message'] .= ' (404)';
				break;

			case '403':
				header("HTTP/1.0 403 Forbidden");
				break;

			case '503':
				header("HTTP/1.0 503 Service Unavailable");
				break;

			default:
				$errortype = (int) $errortype;
				$title = strip_tags($detail['errortitle']);

				if (! $errortype) {
					$errortype = 403;
					$title = 'Forbidden';
				}
				header("HTTP/1.0 $errortype $title");
				break;
		}

		if ( $this->is_serializable_request() ) {
			$errorreport = TikiLib::lib('errorreport');
			$errorreport->report($errortitle);
			$errorreport->send_headers();

			$this->output_serialized($detail);
		} elseif ($this->is_xml_http_request()) {
			$smarty->assign('detail', $detail);
			$smarty->display('error-ajax.tpl');
		} else {
			if (($errortype == 401 || $errortype == 403) &&
						empty($user) &&
						($prefs['permission_denied_login_box'] == 'y' || !empty($prefs['permission_denied_url']))
			) {
				$_SESSION['loginfrom'] = $_SERVER['REQUEST_URI'];
				if ($prefs['login_autologin'] == 'y' && $prefs['login_autologin_redirectlogin'] == 'y' && !empty($prefs['login_autologin_redirectlogin_url'])) {
					$this->redirect($prefs['login_autologin_redirectlogin_url']);
				}
			}

			$smarty->assign('errortitle', $detail['errortitle']);
			$smarty->assign('msg', $detail['message']);
			$smarty->assign('errortype', $detail['code']);
			$check = key_get(null, null, null, false);
			$smarty->assign('ticket', $check['ticket']);
			if ( isset( $detail['page'] ) ) {
				$smarty->assign('page', $page);
			}
			$smarty->display("error.tpl");
		}
		die;
	}

    /**
     * @param string $page
     * @return string
     */
    function get_home_page($page = '')
	{
		global $prefs, $use_best_language, $user;
		$userlib = TikiLib::lib('user');
		$tikilib = TikiLib::lib('tiki');

		if (!isset($page) || $page == '') {
			if ($prefs['useGroupHome'] == 'y') {
				$groupHome = $userlib->get_user_default_homepage($user);
				if ($groupHome) {
					$page = $groupHome;
				} else {
					$page = $prefs['wikiHomePage'];
				}
			} else {
				$page = $prefs['wikiHomePage'];
			}
			if (!$tikilib->page_exists($prefs['wikiHomePage'])) {
				$tikilib->create_page($prefs['wikiHomePage'], 0, '', $this->now, 'Tiki initialization');
			}
			if ($prefs['feature_best_language'] == 'y') {
				$use_best_language = true;
			}
		}
		return $page;
	}

	/**
	 * Utility function redirect the browser location to another url

	 * @param string $url       The target web address
	 * @param string $msg       An optional message to display
	 * @param int $code         HTTP code
	 * @param string $msgtype   Type of message which determines styling (e.g., success, error, warning, etc.)
	 */
	function redirect( $url = '', $msg = '', $code = 302, $msgtype = '')
	{
		global $prefs;

		if ($this->noRedirect) {
			return;
		}

		if ( $url == '' )
			$url = $prefs['tikiIndex'];

		if (trim($msg)) {
			$session = session_id();
			if ( empty($session) ) {
				$start = strpos($url, '?') ? '&' : '?';
				$url = $start . 'msg=' . urlencode($msg) . '&msgtype=' . urlencode($msgtype);
			} else {
				$_SESSION['msg'] = $msg;
				$_SESSION['msgtype'] = $msgtype;
			}
		}

		TikiLib::events()->trigger('tiki.process.redirect');

		session_write_close();
		if (headers_sent()) {
			echo "<script>document.location.href='$url';</script>\n";
		} else {
			@ob_end_clean(); // clear output buffer
			if ( $prefs['feature_obzip'] == 'y' ) {
				@ob_start('ob_gzhandler');
			}
			header("HTTP/1.0 $code Found");
			header("Location: $url");
		}
		exit();
	}

    /**
     * @param $message
     */
    function flash( $message )
	{
		$this->redirect($_SERVER['REQUEST_URI'], $message);
	}

	/**
	 * Authorizes access to Tiki RSS feeds via user/password embedded in a URL
	 * e.g. https://joe:secret@localhost/tiki/tiki-calendars_rss.php?ver=2
	 *              ~~~~~~~~~~
	 *
	 * @param array the permissions that needs to be checked against (e.g. tiki_p_view)
	 *
	 * @return null if authorized, otherwise an array(msg,header)
	 *              where msg can be displayed, and header decides whether to
	 *              send 401 Unauthorized headers.
	 */

	function authorize_rss($rssrights)
	{
		global $user, $prefs;
		$userlib = TikiLib::lib('user');
		$tikilib = TikiLib::lib('tiki');
		$smarty = TikiLib::lib('smarty');
		$perms = Perms::get();
		$result = array('msg' => tra("You do not have permission to view this section"), 'header' => 'n');

		// if current user has appropriate rights, allow.
		foreach ($rssrights as $perm) {
			if ($perms->$perm) {
				return;
			}
		}

		// deny if no basic auth allowed.
		if ($prefs['feed_basic_auth'] != 'y') {
			return $result;
		}

		//login is needed to access the contents
		$https_mode = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on';

		//refuse to authenticate in plaintext if https_login_required.
		if ($prefs['https_login_required'] == 'y' && !$https_mode) {
			$result['msg']=tra("For the security of your password, direct access to the feed is only available via HTTPS");
			return $result;
		}

		if ( $this->http_auth() ) {
			$perms = Perms::get();
			foreach ($rssrights as $perm) {
				if ($perms->$perm) {
					// if user/password and the appropriate rights are correct, allow.
					return;
				}
			}
		}

		return $result;
	}

    /**
     * @return bool
     */
    function http_auth()
	{
		global $tikidomain, $user;
		$userlib = TikiLib::lib('user');
		$smarty = TikiLib::lib('smarty');

		if ( ! $tikidomain ) {
			$tikidomain = "Default";
		}

		if (! isset($_SERVER['PHP_AUTH_USER']) ) {
			header('WWW-Authenticate: Basic realm="'.$tikidomain.'"');
			header('HTTP/1.0 401 Unauthorized');
			exit;
		}

		$attempt = $_SERVER['PHP_AUTH_USER'] ;
		$pass = $_SERVER['PHP_AUTH_PW'] ;
		list($res, $rest) = $userlib->validate_user_tiki($attempt, $pass, false, false);

		if ($res == USER_VALID) {
			global $_permissionContext;

			$_permissionContext = new Perms_Context($attempt, false);
			$_permissionContext->activate(true);

			return true;
		} else {
			header('WWW-Authenticate: Basic realm="' . $tikidomain . '"');
			header('HTTP/1.0 401 Unauthorized');
			return false;
		}
	}

    /**
     * @param bool $acceptFeed
     * @return array
     */
    function get_accept_types($acceptFeed = false)
	{
		$accept = explode(',', $_SERVER['HTTP_ACCEPT']);

		if ( isset($_REQUEST['httpaccept']) ) {
			$accept = array_merge(explode(',', $_REQUEST['httpaccept']), $accept);
		}

		$types = array();

		foreach ( $accept as $type ) {
			$known = null;

			if ( strpos($t = 'application/json', $type) !== false)
				$known = 'json';
			elseif ( strpos($t = 'text/javascript', $type) !== false )
				$known = 'json';
			elseif ( strpos($t = 'text/x-yaml', $type) !== false )
				$known = 'yaml';
			elseif ( strpos($t = 'application/rss+xml', $type) !== false )
				$known = 'rss';
			elseif ( strpos($t = 'application/atom+xml', $type) !== false )
				$known = 'atom';

			if ( $known && ! isset($types[$known]) )
				$types[$known] = $t;
		}

		if ( empty($types) )
			$types['html'] = 'text/html';

		return $types;
	}

    /**
     * @return bool
     */
    function is_machine_request()
	{
		foreach ( $this->get_accept_types() as $name => $full ) {
			switch ( $name ) {
				case 'html':
					return false;
				case 'json':
				case 'yaml':
					return true;
			}
		}

		return false;
	}

    /**
     * @param bool $acceptFeed
     * @return bool
     */
    function is_serializable_request($acceptFeed = false)
	{
		foreach ( $this->get_accept_types($acceptFeed) as $name => $full ) {
			switch ( $name ) {
				case 'json':
				case 'yaml':
					return true;
				case 'rss':
				case 'atom':
					if ($acceptFeed) {
						return true;
					}
			}
		}

		return false;
	}

    /**
     * @return bool
     */
    function is_xml_http_request()
	{
		return ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}

	/**
	 * Will process the output by serializing in the best way possible based on the request's accept headers.
	 * To output as an RSS/Atom feed, a descriptor may be provided to map the array data to the feed's properties
	 * and to supply additional information. The descriptor must contain the following keys:
	 * [feedTitle] Feed's title, static value
	 * [feedDescription] Feed's description, static value
	 * [entryTitleKey] Key to lookup for each entry to find the title
	 * [entryUrlKey] Key to lookup to find the URL of each entry
	 * [entryModificationKey] Key to lookup to find the modification date
	 * [entryObjectDescriptors] Optional. Array containing two key names, object key and object type to lookup missing information (url and title)
	 */
	function output_serialized( $data, $feed_descriptor = null )
	{
		foreach ( $this->get_accept_types(! is_null($feed_descriptor)) as $name => $full ) {
			switch ( $name ) {
				case 'json':
					header("Content-Type: $full");
					$data = json_encode($data);
					if ($data === false) {
						$error = '';
						switch (json_last_error()) {
							case JSON_ERROR_NONE:
								$error = 'json_encode - No errors';
								break;
							case JSON_ERROR_DEPTH:
								$error = 'json_encode - Maximum stack depth exceeded';
								break;
							case JSON_ERROR_STATE_MISMATCH:
								$error = 'json_encode - Underflow or the modes mismatch';
								break;
							case JSON_ERROR_CTRL_CHAR:
								$error = 'json_encode - Unexpected control character found';
								break;
							case JSON_ERROR_SYNTAX:
								$error = 'json_encode - Syntax error, malformed JSON';
								break;
							case JSON_ERROR_UTF8:
								$error = 'json_encode - Malformed UTF-8 characters, possibly incorrectly encoded';
								break;
							default:
								$error = 'json_encode - Unknown error';
								break;
						}
						throw new Exception ($error);
					}
					if (isset($_REQUEST['callback'])) {
						$data = $_REQUEST['callback'] . '(' . $data . ')';
					}
					echo $data;
					return;

				case 'yaml':
					header("Content-Type: $full");
					echo Horde_Yaml::dump($data);
					return;

				case 'rss':
					$rsslib = TikiLib::lib('rss');
					$writer = $rsslib->generate_feed_from_data($data, $feed_descriptor);
					$writer->setFeedLink($this->tikiUrl($_SERVER['REQUEST_URI']), 'rss');

					header('Content-Type: application/rss+xml');
					echo $writer->export('rss');
					return;

				case 'atom':
					$rsslib = TikiLib::lib('rss');
					$writer = $rsslib->generate_feed_from_data($data, $feed_descriptor);
					$writer->setFeedLink($this->tikiUrl($_SERVER['REQUEST_URI']), 'atom');

					header('Content-Type: application/atom+xml');
					echo $writer->export('atom');
					return;

				case 'html':
					header("Content-Type: $full");
					echo $data;
					return;
			}
		}
	}
}
