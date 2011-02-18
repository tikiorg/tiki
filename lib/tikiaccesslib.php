<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("Location: ../index.php");
	die;
}


class TikiAccessLib extends TikiLib
{

	// check that the user is admin or has admin permissions
	function check_admin($user,$feature_name="") {
		global $tiki_p_admin, $prefs;
		require_once ('tiki-setup.php');
		// first check that user is logged in
		$this->check_user($user);
		if (($user != 'admin') && ($tiki_p_admin != 'y')) {
			$msg = tra("You do not have permission to use this feature");
			if ($feature_name) {
				$msg = $msg . ": " . $feature_name;
			}
			$this->display_error( '', $msg, '403' );
		}
	}

	function check_user($user) {
		global $prefs;
		require_once ('tiki-setup.php');
		if (!$user) {
			$title = tra("You are not logged in");
			$this->display_error( '', $title, '403' );
		}
	}

	function check_page($user='y', $features=array(), $permissions=array(), $permission_name='') {
		require_once ('tiki-setup.php');
		if( $features ) {
			$this->check_feature($features);
		}
		$this->check_user($user);
		if( $permissions ) {
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
	 */
	function check_feature($features, $feature_name='', $relevant_admin_panel='features', $either = false) {
		global $prefs;
		require_once ('tiki-setup.php');

		$perms = Perms::get();
		if( $perms->admin && isset($_REQUEST['check_feature']) && isset($_REQUEST['lm_preference']) ) {
			global $prefslib; require_once 'lib/prefslib.php';
			
			$prefslib->applyChanges( (array) $_REQUEST['lm_preference'], $_REQUEST );
		}

		if ( ! is_array($features) ) { $features = array($features); }
		
		if ( $either ) {
			// if anyone will do, start assuming no go and test for feature
			$allowed = false;
		} else {
			// if all is needed, start assuming it's a go and test for feature not on
			$allowed = true;
		}
		
		foreach ($features as $feature) {
			if ($prefs[$feature] != 'y') {
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
			global $smarty;
		
			if( $perms->admin ) {
				$smarty->assign('required_preferences', $features);
			}
				
			$msg = tr('Required features: <b>%0</b>. If you do not have the privileges to activate these features, ask the site administrator.', implode( ', ', $features ) );

			$this->display_error('', $msg, 'no_redirect_login' );
		}		
	}

	function check_permission($permissions, $permission_name='') {
		require_once ('tiki-setup.php');
		if ( ! is_array($permissions) ) { $permissions = array($permissions); }
		foreach ($permissions as $permission) {
			global $$permission;
			if ($$permission != 'y') {
				if ($permission_name) { $permission = $permission_name; }
				$this->display_error('', tra("You do not have permission to use this feature").": ". $permission, '403', false);
				if (!$user) $_SESSION['loginfrom'] = $_SERVER['REQUEST_URI'];
			}
		}
	}

	// check for any one of the permission will be enough
	// NOTE that you do NOT have to use this to include admin perms, as admin perms automatically inherit the perms they are admin of 
	function check_permission_either($permissions, $permission_name='') {
		require_once ('tiki-setup.php');
		$allowed = false;
		if ( ! is_array($permissions) ) { $permissions = array($permissions); }
		foreach ($permissions as $permission) {
			global $$permission;
			if ($$permission == 'y') {
				$allowed = true;
			}
		}
		if ( !$allowed ) {
			if ($permission_name) { $permission = $permission_name; } else $permission = implode(', ', $permissions);
			$this->display_error('', tra("You do not have permission to use this feature").": ". $permission, '403', false);
		}
	}
	
	// check permission, where the permission is normally unset
	function check_permission_unset($permissions, $permission_name) {
		require_once ('tiki-setup.php');
		foreach ($permissions as $permission) {
			global $$permission;
			if ((isset($$permission) && $$permission == 'n')) {
				if ($permission_name) { $permission = $permission_name; }
				$this->display_error('', tra("You do not have permission to use this feature").": ". $permission, '403', false);
			}
		}
	}

	// check page exists
	function check_page_exists($page) {
		require_once ('tiki-setup.php');
		if (!$this->page_exists($page)) {
			$this->display_error($page, tra("Page cannot be found"), '404');
		}
	}

	/**
	 *  Check whether script was called directly or included
	 *  err and die if called directly
	 *  Typical usage: $access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));
	 * 
	 *  if feature_redirect_on_error is active, then just goto the Tiki HomePage as configured
	 *  in Admin->General. -- Damian
	 * 
	 */
	function check_script($scriptname, $page) {
		global $prefs;
		if (basename($scriptname) == $page) {
			if( !isset($prefs['feature_usability']) || $prefs['feature_usability'] == 'n' ) {
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
	 *  This uses random tokens. The first call brings to a request confirmation screen with a new token in the form. The second call, in the second request, verifies the submitted token matches.
	 *  Typical usage: $access->check_authenticity();
	 *  Warning: this mechanism does not allow passing uploaded files ($_FILES). For that, see check_ticket().

	 * @param string $confirmation_text Text on the confirmation screen. Default: 'Click here to confirm your action'
	 * @access public
	 * @return void
	 */
	function check_authenticity($confirmation_text = '') {
		global $prefs;
		if ($prefs['feature_ticketlib2'] == 'y') {
			if (isset($_REQUEST['daconfirm'])) {
				key_check();
			} else {
				key_get(null, $confirmation_text);
			}
		}
	}
	
	function check_ticket() {
		global $smarty, $prefs, $user;
		if ($prefs['feature_ticketlib2'] == 'y') {
			if (empty($user) || (isset($_REQUEST['ticket']) && isset($_SESSION['ticket']) && $_SESSION['ticket'] == $_REQUEST['ticket'])) {
				return true;
			}
			$smarty->assign('msg',tra('Sea Surfing (CSRF) detected. Operation blocked.')); // TODO: Improve feedback and allow proceeding by confirming the request. $_REQUEST needs to be saved and restored.
			$smarty->display("error.tpl");
			exit();
		}
	}

	// you must call ask_ticket('error') before calling this
	function display_error($page, $errortitle="", $errortype="", $enableRedirect = true, $message='') {
		global $smarty, $wikilib, $prefs, $tikiroot, $userlib, $user;
		require_once ('tiki-setup.php');
		include_once('lib/wiki/wikilib.php');

		// Don't redirect when calls are made for web services
		if ( $enableRedirect && $prefs['feature_redirect_on_error'] == 'y' && ! $this->is_machine_request() && ! $this->is_xajax_request() && $tikiroot.$prefs['tikiIndex'] != $_SERVER['PHP_SELF'] && $page != $userlib->get_user_default_homepage2($user) ) {
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
		switch( $errortype ) {
		case '404':
			header ("HTTP/1.0 404 Not Found");
			$detail['page'] = $page;
			$detail['message'] .= ' (404)';
			break;
		case '403':
			if( $this->is_machine_request() )
				header ("HTTP/1.0 403 Forbidden");
			break;
		case '503':
			if( $this->is_machine_request() )
				header ("HTTP/1.0 503 Service Unavailable");
			break;
		}

		if( $this->is_serializable_request() ) {
			$this->output_serialized( $detail );
		} else {
			if (($errortype == 401 || $errortype == 403) && empty($user) && ($prefs['permission_denied_login_box'] == 'y' || !empty($prefs['permission_denied_url']))) {
				$_SESSION['loginfrom'] = $_SERVER['REQUEST_URI'];
			}
			$smarty->assign('errortitle', $detail['errortitle']);
			$smarty->assign('msg', $detail['message']);
			$smarty->assign('errortype', $detail['code']);
			if( isset( $detail['page'] ) )
				$smarty->assign('page', $page);
			$smarty->display("error.tpl");
		}
		die;
	}

	function get_home_page($page='') {
		global $prefs, $tikilib, $use_best_language;

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
			if(!$tikilib->page_exists($prefs['wikiHomePage'])) {
				$tikilib->create_page($prefs['wikiHomePage'],0,'',$this->now,'Tiki initialization');
			}
			if ($prefs['feature_best_language'] == 'y') {
				$use_best_language = true;
			}
		}
		return $page;
	}

	/**
	 * Utility function redirect the browser location to another url
	 *
	 * @param string The target web address
	 * @param string an optional message to display
	 */
	function redirect( $url='', $msg='', $code = 302 ) {
		global $prefs;
		if( $url == '' ) $url = $prefs['tikiIndex'];
		if (trim( $msg )) {
			$session = session_id();
			if( empty( $session ) ) {
				if (strpos( $url, '?' )) {
					$url .= '&msg=' . urlencode( $msg );
				} else {
					$url .= '?msg=' . urlencode( $msg );
				}
			} else {
				$_SESSION['msg'] = $msg;
			}
		}

		if (headers_sent()) {
			echo "<script>document.location.href='$url';</script>\n";
		} else {
			@ob_end_clean(); // clear output buffer
			if ( $prefs['feature_obzip'] == 'y' ) {
				@ob_start('ob_gzhandler');
			}
			header("HTTP/1.0 $code Found");
			header( "Location: $url" );
		}
		exit();
	}

	function flash( $message ) {
		$this->redirect( $_SERVER['REQUEST_URI'], $message );
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

	function authorize_rss($rssrights) {
		global $tikilib, $userlib, $user, $smarty, $prefs;
		$perms = Perms::get();
		$result=array('msg' => tra("You do not have permission to view this section"), 'header' => 'n');

		// if current user has appropriate rights, allow.
		foreach($rssrights as $perm) {
			if($perms->$perm) {
				return;
			}
		}

		// deny if no basic auth allowed.
		if($prefs['feed_basic_auth'] != 'y') {
			return $result;
		}

		//login is needed to access the contents
		$https_mode = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on';

		//refuse to authenticate in plaintext if https_login_required.
		if ($prefs['https_login_required'] == 'y' && !$https_mode) {
			$result['msg']=tra("For the security of your password direct access to the feed is only available via https");
			return $result;
		}

		if( $this->http_auth() ) {
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

	function http_auth()
	{
		global $tikidomain, $userlib, $user, $smarty;

		if( ! $tikidomain ) {
			$tikidomain = "Default";
		}
		if (empty($_SERVER['PHP_AUTH_USER']) && !empty($_REQUEST['user']) && !empty($_REQUEST['pass'])) {
			$_SERVER['PHP_AUTH_USER'] = $_REQUEST['user'];
			$_SERVER['PHP_AUTH_PW'] = $_REQUEST['pass'];
		}

		if (! isset($_SERVER['PHP_AUTH_USER']) ) {
			header('WWW-Authenticate: Basic realm="'.$tikidomain.'"');
			header('HTTP/1.0 401 Unauthorized');
			exit;
		}
		
		$attempt = $_SERVER['PHP_AUTH_USER'] ;
		$pass = $_SERVER['PHP_AUTH_PW'] ;
		list($res,$rest)=$userlib->validate_user_tiki($attempt, $pass, false, false);

		if ($res==USER_VALID) {
			global $permissionList;
			$user = $attempt;
			$groups = $userlib->get_user_groups( $user );
			$perms = Perms::getInstance();
			$perms->setGroups( $groups );

			$perms = Perms::get();
			$perms->globalize( $permissionList, $smarty );

			return true;
		} else {
			header('WWW-Authenticate: Basic realm="'.$tikidomain.'"');
			header('HTTP/1.0 401 Unauthorized');
			return false;
		}
	}

	function get_accept_types() {
		$accept = explode( ',', $_SERVER['HTTP_ACCEPT'] );

		if( isset( $_REQUEST['httpaccept'] ) ) {
			$accept = array_merge( explode( ',', $_REQUEST['httpaccept'] ), $accept );
		}

		$types = array();

		foreach( $accept as $type ) {
			$known = null;

			if( strpos( $t = 'application/json', $type ) !== false )
				$known = 'json';
			elseif( strpos( $t = 'text/javascript', $type ) !== false )
				$known = 'json';
			elseif( strpos( $t = 'text/x-yaml', $type ) !== false )
				$known = 'yaml';

			if( $known && ! isset( $types[$known] ) )
				$types[$known] = $t;
		}

		if( empty( $types ) )
			$types['html'] = 'text/html';

		return $types;
	}

	function is_machine_request() {
		foreach( $this->get_accept_types() as $name => $full ) {
			switch( $name ) {
			case 'html':
				return false;
			case 'json':
			case 'yaml':
				return true;
			}
		}

		return false;
	}

	function is_serializable_request() {
		foreach( $this->get_accept_types() as $name => $full ) {
			switch( $name ) {
			case 'json':
			case 'yaml':
				return true;
			}
		}

		return false;
	}

	function output_serialized( $data ) {
		foreach( $this->get_accept_types() as $name => $full ) {
			switch( $name ) {
			case 'json':
				header( "Content-Type: $full" );
				$data = json_encode( $data );
				if (isset($_REQUEST['callback'])) {
					$data = $_REQUEST['callback'] . '(' . $data . ')';
				}
				echo $data;
				return;
			case 'yaml':
				require_once( 'Horde/Yaml.php' );
				require_once( 'Horde/Yaml/Loader.php' );
				require_once( 'Horde/Yaml/Node.php' );
				require_once( 'Horde/Yaml/Exception.php' );

				header( "Content-Type: $full" );
				echo Horde_Yaml::dump($data);
				return;
			}
		}
	}
}
$access = new TikiAccessLib;
