<?php

// Page access controller library

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("Location: ../index.php");
	die;
}


class TikiAccessLib extends TikiLib {

	function TikiAccessLib() {
		global $dbTiki;
		$this->TikiLib($dbTiki);
	}

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

	function check_feature($features, $feature_name="") {
		global $prefs;
		require_once ('tiki-setup.php');
		if ( ! is_array($features) ) { $features = array($features); }
		foreach ($features as $feature) {
			if ($prefs[$feature] != 'y') {
				if ($feature_name != '') { $feature = $feature_name; }
				$this->display_error('', tra("This feature is disabled").": ". $feature_name, '503' );
			}
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
			}
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

	// you must call ask_ticket('error') before calling this
	function display_error($page, $errortitle="", $errortype="", $enableRedirect = true) {
		global $smarty, $wikilib, $prefs, $tikiroot, $userlib, $user;
		require_once ('tiki-setup.php');
		include_once('lib/wiki/wikilib.php');

		// Don't redirect when calls are made for web services
		if ( $enableRedirect && $prefs['feature_redirect_on_error'] == 'y' && ! $this->is_machine_request() && ! $this->is_xajax_request() && $tikiroot.$prefs['tikiIndex'] != $_SERVER['PHP_SELF'] && $page != $userlib->get_user_default_homepage2($user) ) {
			$this->redirect($prefs['tikiIndex']);
		}

		$detail = array(
			'code' => $errortype,
			'message' => $errortitle,
		);

		if ( !isset($errortitle) ) {
			$detail['message'] = tra('unknown error');
		}

		// Display the template
		$smarty->assign('msg', $errortitle);
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
			$smarty->assign('errortitle', $detail['message']);
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
	function redirect( $url='', $msg='' ) {
		global $prefs;
		if( $url == '' ) $url = $prefs['tikiIndex'];
		if (trim( $msg )) {
			if (strpos( $url, '?' )) {
				$url .= '&msg=' . urlencode( $msg );
			} else {
				$url .= '?msg=' . urlencode( $msg );
			}
		}

		if (headers_sent()) {
			echo "<script>document.location.href='$url';</script>\n";
		} else {
			@ob_end_clean(); // clear output buffer
			header("HTTP/1.0 302 Found");
			header( "Location: $url" );
		}
		exit();
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
		global $tikilib, $userlib, $user, $prefs, $smarty;
		$result=array('msg' => tra("Permission denied you cannot view this section"), 'header' => 'n');

		// allow admin
		print $tiki_p_admin;
		if($userlib->user_has_permission($user,'tiki_p_admin')) {
			return;
		}

		// if current user has appropriate rights, allow.
		foreach($rssrights as $perm) {
			if($userlib->user_has_permission($user,$perm)) {
				return;
			}
		}

		// deny if no basic auth allowed.
		if($prefs['rss_basic_auth'] != 'y') {
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
			foreach ($rssrights as $perm) {
				if ($GLOBALS[$perm] == 'y') {
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

		if (! isset($_SERVER['PHP_AUTH_USER']) ) {
			header('WWW-Authenticate: Basic realm="'.$tikidomain.'"');
			header('HTTP/1.0 401 Unauthorized');
			return false;
		}
		
		$attempt = $_SERVER['PHP_AUTH_USER'] ;
		$pass = $_SERVER['PHP_AUTH_PW'] ;
		list($res,$rest)=$userlib->validate_user_tiki($attempt, $pass, false, false);

		if ($res==USER_VALID) {
			$user = $attempt;
			$perms = $userlib->get_user_permissions($user);
			foreach ($perms as $perm) {
				$GLOBALS[$perm] = 'y';
				$smarty->assign($perm, 'y');
			}

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

	function is_xajax_request() {
		global $prefs;
		return ( $prefs['feature_ajax'] == 'y' && isset($_POST['xajaxargs']) );
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
				echo json_encode( $data );
				return;
			case 'yaml':
				require_once( 'Horde/Yaml.php' );
				require_once( 'Horde/Yaml/Loader.php' );
				require_once( 'Horde/Yaml/Node.php' );
				require_once( 'Horde/Yaml/Exception.php' );

				header( "Content-Type: $full" );
				echo Horde_Yaml::dump($value);
				return;
			}
		}
	}
}

$access = new TikiAccessLib($dbTiki);

?>
