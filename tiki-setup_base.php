<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
require_once ('tiki-filter-base.php');
// ---------------------------------------------------------------------
// basic php conf adjustment
// xhtml compliance
ini_set('arg_separator.output', '&amp;');
// URL session handling is not safe or pretty
// better avoid using trans_sid for security reasons
ini_set('session.use_only_cookies', 1);
// true, but you cannot change the url_rewriter.tags in safe mode ...
// its usually safe to leave it as is.
//ini_set('url_rewriter.tags', '');
// use shared memory for sessions (useful in shared space)
// ini_set('session.save_handler', 'mm');
// ... or if you use turck mmcache
// ini_set('session.save_handler', 'mmcache');
// ... or if you just cant to store sessions in file
// ini_set('session.save_handler', 'files');
// Smarty workaround - if this would be 'On' in php.ini Smarty fails to parse tags
ini_set('magic_quotes_sybase', 'Off');
ini_set('magic_quotes_runtime', 0);
ini_set('allow_call_time_pass_reference', 'On');
// ---------------------------------------------------------------------
// inclusions of mandatory stuff and setup
require_once ('lib/setup/compat.php');
require_once ('lib/tikiticketlib.php');
require_once ('db/tiki-db.php');
require_once ('lib/tikilib.php');
$tikilib = new TikiLib;
// Get tiki-setup_base needed preferences in one query
$prefs = array();
$needed_prefs = array(
	'session_lifetime' => '0',
	'session_storage' => 'default',
	'session_silent' => 'n',
	'session_cookie_name' => session_name(),
	'session_protected' => 'n',
	'tiki_cdn' => '',
	'tiki_cdn_ssl' => '',
	'language' => 'en',
	'lang_use_db' => 'n',
	'feature_pear_date' => 'y',
	'lastUpdatePrefs' => - 1,
	'feature_fullscreen' => 'n',
	'error_reporting_level' => 0,
	'smarty_notice_reporting' => 'n',
	'memcache_enabled' => 'n',
	'memcache_expiration' => 3600,
	'memcache_prefix' => 'tiki_',
	'memcache_compress' => 'y',
	'memcache_servers' => false,
);
$tikilib->get_preferences($needed_prefs, true, true);
if (!isset($prefs['lastUpdatePrefs']) || $prefs['lastUpdatePrefs'] == - 1) {
	$tikilib->query('delete from `tiki_preferences` where `name`=?', array('lastUpdatePrefs'));
	$tikilib->query('insert into `tiki_preferences`(`name`,`value`) values(?,?)', array('lastUpdatePrefs', 1));
}

if ($prefs['session_protected'] == 'y' && ! isset($_SERVER['HTTPS'])) {
	header("Location: https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
	exit;
}

global $cachelib;
require_once ('lib/cache/cachelib.php');
global $logslib;
require_once ('lib/logs/logslib.php');
include_once ('lib/init/tra.php');

if( $prefs['memcache_enabled'] == 'y' ) {
	require_once('lib/cache/memcachelib.php');
	if( is_array( $prefs['memcache_servers'] ) ) {
		$servers = $prefs['memcache_servers'];
	} else {
		$servers = unserialize( $prefs['memcache_servers'] );
	}

	global $memcachelib;
	$memcachelib = new MemcacheLib( $servers, array(
		'enabled' => true,
		'expiration' => (int) $prefs['memcache_expiration'],
		'key_prefix' => $prefs['memcache_prefix'],
		'compress' => $prefs['memcache_compress'],
	) );
}

require_once ('lib/tikidate.php');
$tikidate = new TikiDate();
// set session lifetime
if ($prefs['session_lifetime'] > 0) {
	ini_set('session.gc_maxlifetime', $prefs['session_lifetime'] * 60);
}
// is session data  stored in DB or in filesystem?
if ($prefs['session_storage'] == 'db') {
	if ($api_tiki == 'adodb') {
		require_once ('lib/tikisession-adodb.php');
	} elseif ($api_tiki == 'pdo') {
		require_once ('lib/tikisession-pdo.php');
	}
} elseif( $prefs['session_storage'] == 'memcache' && isset( $memcachelib ) && $memcachelib->isEnabled() ) {
	require_once ('lib/tikisession-memcache.php');
}

if( ! isset( $prefs['session_cookie_name'] ) || empty( $prefs['session_cookie_name'] ) ) {
	$prefs['session_cookie_name'] = session_name();
}

session_name( $prefs['session_cookie_name'] );

// Only accept PHP's session ID in URL when the request comes from the tiki server itself
// This is used by features that need to query the server to retrieve tiki's generated html and images (e.g. pdf export)
if (isset($_GET[session_name()]) && $tikilib->get_ip_address() == '127.0.0.1') {
	$_COOKIE[session_name()] = $_GET[session_name()];
	session_id($_GET[session_name()]);
}

$start_session = $prefs['session_silent'] != 'y' or isset( $_COOKIE[session_name()] );

// If called from the CDN, refuse to execute anything
$cdn_pref = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? $prefs['tiki_cdn_ssl'] : $prefs['tiki_cdn'];
if( $cdn_pref ) {
	$host = parse_url( $cdn_pref, PHP_URL_HOST );
	if( $host == $_SERVER['HTTP_HOST'] ) {
		header("HTTP/1.0 404 Not Found");
		echo "File not found.";
		exit;
	}
}
$cookie_path = '';
if (isset($_SERVER["REQUEST_URI"])) {
	$cookie_path = str_replace("\\", "/", dirname($_SERVER["REQUEST_URI"]));
	if ($cookie_path != '/') {
		$cookie_path .= '/';
	}
	ini_set('session.cookie_path', str_replace("\\", "/", $cookie_path));
	if ( $start_session ) {
		// enabing silent sessions mean a session is only started when a cookie is presented
		$session_params = session_get_cookie_params();
		session_set_cookie_params($session_params['lifetime'], $cookie_path);
		unset($session_params);
	
		try {
			require_once "Zend/Session.php";
			Zend_Session::start();
		} catch( Zend_Session_Exception $e ) {
			// Ignore
		}
	}
}

// Moved here from tiki-setup.php because smarty use a copy of session
if ($prefs['feature_fullscreen'] == 'y') {
	require_once ('lib/setup/fullscreen.php');
}
// Retrieve all preferences
require_once ('lib/setup/prefs.php');
// Smarty needs session since 2.6.25
require_once ('lib/init/smarty.php');
require_once ('lib/userslib.php'); global $userlib;
$userlib = new UsersLib;
require_once ('lib/tikiaccesslib.php');
$access = new TikiAccessLib;
require_once ('lib/breadcrumblib.php');
// ------------------------------------------------------
// DEAL WITH XSS-TYPE ATTACKS AND OTHER REQUEST ISSUES
function remove_gpc(&$var) {
	if (is_array($var)) {
		foreach($var as $key => $val) {
			remove_gpc($var[$key]);
		}
	} else {
		$var = stripslashes($var);
	}
}
// mose : simulate strong var type checking for http vars
$patterns['int'] = "/^[0-9]*$/"; // *Id
$patterns['intSign'] = "/^[-+]?[0-9]*$/"; // *offset,
$patterns['char'] = "/^(pref:)?[-,_a-zA-Z0-9]*$/"; // sort_mode
$patterns['string'] = "/^<\/?(b|strong|small|br *\/?|ul|li|i)>|[^<>\";#]*$/"; // find, and such extended chars
$patterns['stringlist'] = "/^[^<>\"#]*$/"; // to, cc, bcc (for string lists like: user1;user2;user3)
$patterns['vars'] = "/^[-_a-zA-Z0-9]*$/"; // for variable keys
$patterns['dotvars'] = "/^[-_a-zA-Z0-9\.]*$/"; // same pattern as a variable key, but that may contain a dot
$patterns['hash'] = "/^[a-z0-9]*$/"; // for hash reqId in live support
// needed for the htmlpage inclusion in tiki-editpage
$patterns['url'] = "/^(https?:\/\/)?[^<>\"']*$/"; // needed for the htmlpage inclusion in tiki-editpage
// parameter type definitions. prepend a + if variable may not be empty, e.g. '+int'
$vartype['id'] = '+int';
$vartype['forumId'] = '+int';
$vartype['offset'] = 'intSign';
$vartype['prev_offset'] = 'intSign';
$vartype['next_offset'] = 'intSign';
$vartype['thresold'] = 'int';
$vartype['sort_mode'] = '+char';
$vartype['file_sort_mode'] = 'char';
$vartype['file_offset'] = 'int';
$vartype['file_find'] = 'string';
$vartype['file_prev_offset'] = 'intSign';
$vartype['file_next_offset'] = 'intSign';
$vartype['comments_offset'] = 'int';
$vartype['comments_thresold'] = 'int';
$vartype['comments_parentId'] = '+int';
$vartype['thread_sort_mode'] = '+char';
$vartype['thread_style'] = '+char';
$vartype['comments_per_page'] = '+int';
$vartype['topics_offset'] = 'int';
$vartype['topics_sort_mode'] = '+char';
$vartype['priority'] = 'int';
$vartype['theme'] = 'string';
$vartype['flag'] = 'char';
$vartype['lang'] = 'char';
$vartype['language'] = 'char';
$vartype['page'] = 'string';
$vartype['edit_mode'] = 'char';
$vartype['find'] = 'string';
$vartype['topic_find'] = 'string';
$vartype['initial'] = 'char';
$vartype['username'] = '+string';
$vartype['realName'] = 'string';
$vartype['homePage'] = 'string';
$vartype['to'] = 'stringlist';
$vartype['cc'] = 'stringlist';
$vartype['bcc'] = 'stringlist';
$vartype['subject'] = 'string';
$vartype['name'] = 'string';
$vartype['reqId'] = '+hash';
$vartype['days'] = '+int';
$vartype['max'] = '+int';
$vartype['maxRecords'] = '+int';
$vartype['numrows'] = '+int';
$vartype['rows'] = '+int';
$vartype['cols'] = '+int';
$vartype['topicname'] = '+string';
$vartype['error'] = 'string';
$vartype['editmode'] = 'char'; // from calendar
$vartype['actpass'] = '+string'; // remind password page
$vartype['user'] = '+string'; // remind password page
$vartype['remind'] = 'string'; // remind password page
$vartype['url'] = 'url';

$vartype['aid'] = '+int';
$vartype['description'] = 'string';
$vartype['filter_active'] = 'char';
$vartype['filter_name'] = 'string';
$vartype['newmajor'] = '+int';
$vartype['newminor'] = '+int';
$vartype['pid'] = '+int';
$vartype['remove_role'] = '+int';
$vartype['rolename'] = 'char';
$vartype['type'] = 'string';
$vartype['userole'] = 'int';
$vartype['focus'] = 'string';
$vartype['filegals_manager'] = 'vars';
$vartype['ver'] = 'dotvars'; // filename hash for drawlib + rss type for rsslib
$vartype['trackerId'] = 'int';
$vartype['articleId'] = 'int';
$vartype['galleryId'] = 'int';
$vartype['blogId'] = 'int';
$vartype['postId'] = 'int';
$vartype['calendarId'] = 'int';
$vartype['faqId'] = 'int';
$vartype['quizId'] = 'int';
$vartype['sheetId'] = 'int';
$vartype['surveyId'] = 'int';
$vartype['nlId'] = 'int';
$vartype['chartId'] = 'int';
$vartype['categoryId'] = 'int';
$vartype['parentId'] = 'int';
$vartype['bannerId'] = 'int';
$vartype['rssId'] = 'int';
$vartype['page_ref_id'] = 'int';
function varcheck(&$array, $category) {
	global $patterns, $vartype, $prefs;
	$return = array();
	if (is_array($array)) {
		foreach($array as $rq => $rv) {
			// check if the variable name is allowed
			if (!preg_match($patterns['vars'], $rq)) {
				//die(tra("Invalid variable name : "). htmlspecialchars($rq));
				
			} elseif (isset($vartype["$rq"])) {
				$has_sign = false;
				// Variable allowed to be empty?
				if ('+' == substr($vartype[$rq], 0, 1)) {
					if ($rv == "") {
						$return[] = tra("Notice: this variable may not be empty:") . ' <font color="red">$' . $category . '["' . $rq . '"]</font>';
						continue;
					}
					$has_sign = true;
				}
				if (is_array($rv)) {
					$tmp = varcheck($array[$rq], $category);
					if ($tmp != "") {
						$return[] = $tmp;
					}
				} else {
					// Check single parameters
					$pattern_key = $has_sign ? substr($vartype[$rq], 1) : $vartype[$rq];
					if (!preg_match($patterns[$pattern_key], $rv)) {
						$return[] = tra("Notice: invalid variable value:") . ' $' . $category . '["' . $rq . '"] = <font color="red">' . htmlspecialchars($rv) . '</font>';
						$array[$rq] = ''; // Clear content
						
					}
				}
			}
		}
	}
	return implode('<br />', $return);
}
unset($_COOKIE['offset']);
if (!empty($_REQUEST['highlight'])) {
	if (is_array($_REQUEST['highlight'])) $_REQUEST['highlight'] = '';
	$_REQUEST['highlight'] = htmlspecialchars($_REQUEST['highlight']);
	// Convert back sanitization tags into real tags to avoid them to be displayed
	$_REQUEST['highlight'] = str_replace('&lt;x&gt;', '<x>', $_REQUEST['highlight']);
}
// ---------------------------------------------------------------------
if (!isset($_SERVER['QUERY_STRING'])) {
	$_SERVER['QUERY_STRING'] = '';
}
if (empty($_SERVER['REQUEST_URI'])) {
	$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
}
if (empty($_SERVER['SERVER_NAME'])) {
	$_SERVER['SERVER_NAME'] = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']: '';
}

/*
 * Clean variables past in _GET & _POST & _COOKIE
 */
$magic_quotes_gpc = get_magic_quotes_gpc();
if ($magic_quotes_gpc) {
	remove_gpc($_GET);
	remove_gpc($_POST);
	remove_gpc($_COOKIE);
}

require_once ('lib/setup/absolute_urls.php');

// in the case of tikis on same domain we have to distinguish the realm
// changed cookie and session variable name by a name made with browsertitle
$cookie_site = preg_replace("/[^a-zA-Z0-9]/", "", $prefs['cookie_name']);
$user_cookie_site = 'tiki-user-' . $cookie_site;
// if remember me is enabled, check for cookie where auth hash is stored
// user gets logged in as the first user in the db with a matching hash
if (($prefs['rememberme'] != 'disabled') and (isset($_COOKIE["$user_cookie_site"])) and (!isset($user) and !isset($_SESSION["$user_cookie_site"]))) {
	if ($prefs['feature_intertiki'] == 'y' and !empty($prefs['feature_intertiki_mymaster']) and $prefs['feature_intertiki_sharedcookie'] == 'y') {
		$rpcauth = $userlib->get_remote_user_by_cookie($_COOKIE["$user_cookie_site"]);
		if (is_object($rpcauth)) {
			$response_value = $rpcauth->value();
			if (is_object($response_value)) {
				$user = $response_value->scalarval();
			}
		}
	} else {
		if ($userId = $userlib->get_user_by_cookie($_COOKIE["$user_cookie_site"])) {
			$userInfo = $userlib->get_userid_info($userId);
			$user = $userInfo['login'];
		}
	}
	if (isset($user) && $user) {
		$_SESSION["$user_cookie_site"] = $user;
	}
}
// if the auth method is 'web site', look for the username in $_SERVER
if (($prefs['auth_method'] == 'ws') and (isset($_SERVER['REMOTE_USER']))) {
	if ($userlib->user_exists($_SERVER['REMOTE_USER'])) {
		$user = $_SERVER['REMOTE_USER'];
		$_SESSION["$user_cookie_site"] = $user;
	} elseif ($userlib->user_exists(str_replace("\\\\", "\\", $_SERVER['REMOTE_USER']))) {
		// Check for the domain\username with just one backslash
		$user = str_replace("\\\\", "\\", $_SERVER['REMOTE_USER']);
		$_SESSION["$user_cookie_site"] = $user;
	} elseif ($userlib->user_exists(substr($_SERVER['REMOTE_USER'], strpos($_SERVER['REMOTE_USER'], "\\") + 2))) {
		// Check for the username without the domain name
		$user = substr($_SERVER['REMOTE_USER'], strpos($_SERVER['REMOTE_USER'], "\\") + 2);
		$_SESSION["$user_cookie_site"] = $user;
	} elseif ($prefs['auth_ws_create_tiki'] == 'y') {
		$user = $_SERVER['REMOTE_USER'];
		if ($userlib->add_user($_SERVER['REMOTE_USER'],'', '')) {
			$user = $_SERVER['REMOTE_USER'];
			$_SESSION["$user_cookie_site"] = $user;
		}
	}
	if (!empty($_SESSION["$user_cookie_site"])) {
		$userlib->update_lastlogin($user);
	}
}
// Check for Shibboleth Login
if ($prefs['auth_method'] == 'shib' and isset($_SERVER['REMOTE_USER'])) {
	// Validate the user (if not created create it)
	if ($userlib->validate_user($_SERVER['REMOTE_USER'], "", "", "")) {
		$_SESSION["$user_cookie_site"] = $_SERVER['REMOTE_USER'];
	}
}

$userlib->check_cas_authentication($user_cookie_site);

// if the username is already saved in the session, pull it from there
if (isset($_SESSION["$user_cookie_site"])) {
	$user = $_SESSION["$user_cookie_site"];
	// There could be a case where the session contains a user that doesn't exists in this tiki
	// or that has never used the login step in this tiki.
	// Example : If using the same PHP SESSION cookies for more than one tiki.
	$user_details = $userlib->get_user_details($user);
	if (!is_array($user_details) || !is_array($user_details['info']) || (int)$user_details['info']['lastLogin'] <= 0) {
		global $cachelib;
		require_once ('lib/cache/cachelib.php');
		$cachelib->invalidate('user_details_' . $user);
		$user_details = $userlib->get_user_details($user);
		if (!is_array($user_details) || !is_array($user_details['info'])) {
			$user = NULL;
		}
	}
	unset($user_details);
	
	// Generate anti-CSRF ticket
	if ($prefs['feature_ticketlib2'] == 'y' && !isset($_SESSION['ticket'])) {
		$_SESSION['ticket'] = md5(uniqid(rand()));
	}
} else {
	$user = NULL;
	// if everything failed, check for user+pass params in the URL
	// this is needed for access to things like RSS feeds that are configured to be
	// be visible to registered users and/or certain groups
	// #####################################################################################
	// Note: if you uncomment the following section, people are allowed to log in using
	// GET (username and password in URL). That is some kind of insecure, because
	// password and username are not encrypted and visible and browser caches etc, besides
	// that someone could try to break in with brute force attacks. So uncomment this only
	// if you are in a trusted environment (maybe intranet) and want to ignore the risks.
	// #####################################################################################
	// 	$isvalid = false;
	// 	if (isset($_REQUEST["user"]) && isset($_REQUEST["pass"])) {
	// 		$isvalid = $userlib->validate_user($_REQUEST["user"], $_REQUEST["pass"], '', '');
	// 		if ($isvalid) {
	// 			$_SESSION["$user_cookie_site"] = $_REQUEST["user"];
	// 			$user = $_REQUEST["user"];
	// 			$smarty->assign_by_ref('user', $user);
	// 			// Now since the user is valid we put the user provpassword as the password
	// 			$userlib->confirm_user($user);
	// 		}
	// }
	
}
$smarty->assign( 'CSRFTicket', isset( $_SESSION['ticket'] ) ? $_SESSION['ticket'] : null);
require_once ('lib/setup/perms.php');
// --------------------------------------------------------------
// deal with register_globals
if (ini_get('register_globals')) {
	foreach(array($_ENV, $_GET, $_POST, $_COOKIE, $_SERVER) as $superglob) {
		foreach($superglob as $key => $val) {
			if (isset($GLOBALS[$key]) && $GLOBALS[$key] == $val) { // if global has been set some other way
				// that is OK (prevents munging of $_SERVER with ?_SERVER=rubbish etc.)
				unset($GLOBALS[$key]);
			}
		}
	}
}
$serverFilter = new DeclFilter;
if ( $tiki_p_trust_input != 'y' ) {
	$serverFilter->addStaticKeyFilters(array('QUERY_STRING' => 'url', 'REQUEST_URI' => 'url', 'PHP_SELF' => 'url',));
}
$jitServer = new JitFilter($_SERVER);
$_SERVER = $serverFilter->filter($_SERVER);
// Rebuild request after gpc fix
// _REQUEST should only contain GET and POST in the app

$prepareInput = new TikiFilter_PrepareInput('~');
$_GET = $prepareInput->prepare($_GET);
$_POST = $prepareInput->prepare($_POST);

$_REQUEST = array_merge($_GET, $_POST);
// Preserve unfiltered values accessible through JIT filtering
$jitPost = new JitFilter($_POST);
$jitGet = new JitFilter($_GET);
$jitRequest = new JitFilter($_REQUEST);
$jitCookie = new JitFilter($_COOKIE);
$jitPost->setDefaultFilter('xss');
$jitGet->setDefaultFilter('xss');
$jitRequest->setDefaultFilter('xss');
$jitCookie->setDefaultFilter('xss');
// Apply configured filters to all other input
if (!isset($inputConfiguration)) $inputConfiguration = array();

array_unshift( $inputConfiguration, array(
	'staticKeyFilters' => array(
		'menu' => 'striptags',
		'cat_categorize' => 'alpha',
		'cat_clearall' => 'alpha',
		'tab' => 'digits',
		'javascript_enabled' => 'alpha',
		'XDEBUG_PROFILE' => 'int',
	),
	'staticKeyFiltersForArrays' => array(
		'cat_managed' => 'digits',
		'cat_categories' => 'digits',
	),
) );

$inputFilter = DeclFilter::fromConfiguration($inputConfiguration, array('catchAllFilter'));
if ( $tiki_p_trust_input != 'y' ) {
	$inputFilter->addCatchAllFilter('xss');
}
$cookieFilter = DeclFilter::fromConfiguration($inputConfiguration, array('catchAllFilter'));
$cookieFilter->addCatchAllFilter('striptags');

$_GET = $inputFilter->filter($_GET);
$_POST = $inputFilter->filter($_POST);
$_COOKIE = $cookieFilter->filter($_COOKIE);
// Rebuild request with filtered values
$_REQUEST = array_merge($_GET, $_POST);
if ($tiki_p_trust_input != 'y') {
	$varcheck_vars = array('_COOKIE', '_GET', '_POST', '_ENV', '_SERVER');
	$varcheck_errors = '';
	foreach($varcheck_vars as $var) {
		if (!isset($$var)) continue;
		if (($tmp = varcheck($$var, $var)) != '') {
			if ($varcheck_errors != '') $varcheck_errors.= '<br />';
			$varcheck_errors.= $tmp;
		}
	}
	unset($tmp);
}
// deal with old request globals (e.g. used by Smarty)
$GLOBALS['HTTP_GET_VARS'] = & $_GET;
$GLOBALS['HTTP_POST_VARS'] = & $_POST;
$GLOBALS['HTTP_COOKIE_VARS'] = & $_COOKIE;
unset($GLOBALS['HTTP_ENV_VARS']);
unset($GLOBALS['HTTP_SERVER_VARS']);
unset($GLOBALS['HTTP_SESSION_VARS']);
unset($GLOBALS['HTTP_POST_FILES']);
// --------------------------------------------------------------
if (isset($_REQUEST['highlight']) || (isset($prefs['feature_referer_highlight']) && $prefs['feature_referer_highlight'] == 'y')) {
	$smarty->load_filter('output', 'highlight');
}
if (function_exists('mb_internal_encoding')) {
	mb_internal_encoding("UTF-8");
}
// --------------------------------------------------------------
// Fix IIS servers not setting what they should set (ay ay IIS, ay ay)
if (!isset($_SERVER['QUERY_STRING'])) $_SERVER['QUERY_STRING'] = '';
if (!isset($_SERVER['REQUEST_URI']) || empty($_SERVER['REQUEST_URI'])) {
	$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
}
$smarty->assign("tikidomain", $tikidomain);
