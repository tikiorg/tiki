<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-setup_base.php,v 1.142.2.8 2008-03-22 05:12:47 mose Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

require_once 'tiki-filter-base.php';

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
ini_set('magic_quotes_sybase','Off');
ini_set('magic_quotes_runtime',0);
ini_set('allow_call_time_pass_reference','On');
// ---------------------------------------------------------------------
// inclusions of mandatory stuff and setup
require_once("lib/setup/compat.php");
require_once("lib/tikiticketlib.php");
require_once("db/tiki-db.php");
require_once("setup_smarty.php"); 
require_once("lib/tikilib.php");
global $cachelib; require_once("lib/cache/cachelib.php");
global $logslib; require_once("lib/logs/logslib.php");
include_once('lib/init/tra.php');
$tikilib = new TikiLib($dbTiki);

// Get tiki-setup_base needed preferences in one query
$prefs = array();
$needed_prefs = array(
	'session_lifetime' => '0',
	'session_db' => 'n',
	'sessions_silent' => 'disabled',
	'language' => 'en',
	'feature_pear_date' => 'y',
	'lastUpdatePrefs' => -1,
	'error_reporting_level' => 0 // needed by initlib
);

$tikilib->get_preferences($needed_prefs, true, true);
if ( $prefs['lastUpdatePrefs'] == -1 ) {
	$tikilib->query('insert into `tiki_preferences`(`name`,`value`) values(?,?)', array('lastUpdatePrefs', 1));
}

require_once('lib/tikidate.php');
$tikidate = new TikiDate();

// set session lifetime
if ($prefs['session_lifetime'] > 0) {
	ini_set('session.gc_maxlifetime',$prefs['session_lifetime']*60);
}

// is session data  stored in DB or in filesystem?
if ($prefs['session_db'] == 'y') {
	if ($api_tiki == 'adodb') {
		require_once('lib/tikisession-adodb.php');
	} elseif ($api_tiki == 'pdo') {
		require_once('lib/tikisession-pdo.php');
	}
}

// Only accept PHP's session ID in URL when the request comes from the tiki server itself
// This is used by features that need to query the server to retrieve tiki's generated html and images (e.g. pdf export)
if ( isset($_GET['PHPSESSID']) && $_SERVER['REMOTE_ADDR'] == '127.0.0.1' ) {
	$_COOKIE['PHPSESSID'] = $_GET['PHPSESSID'];
	session_id($_GET['PHPSESSID']);
}
if ( $prefs['sessions_silent'] == 'disabled' or !empty($_COOKIE) ) {
	// enabing silent sessions mean a session is only started when a cookie is presented
	session_start();
}

// Check if phpCAS mods is installed 
$phpcas_enabled = is_file('lib/phpcas/source/CAS/CAS.php') ? 'y' : 'n';

// Retrieve all preferences
require_once('lib/setup/prefs.php');

// Handle Smarty Security
if ( $prefs['smarty_security'] == 'y' ) {
	$smarty->security = true;
}

require_once("lib/userslib.php");
$userlib = new UsersLib($dbTiki);
require_once("lib/tikiaccesslib.php");
$access = new TikiAccessLib();
require_once("lib/breadcrumblib.php");

// ------------------------------------------------------
// DEAL WITH XSS-TYPE ATTACKS AND OTHER REQUEST ISSUES

function remove_gpc(&$var) {
	if ( is_array($var) ) {
		foreach ( $var as $key=>$val ) {
			remove_gpc($var[$key]);
		}
	} else {
		$var = stripslashes($var);
	}
}

// mose : simulate strong var type checking for http vars
$patterns['int']   = "/^[0-9]*$/"; // *Id
$patterns['intSign']   = "/^[-+]?[0-9]*$/"; // *offset,
$patterns['char']  = "/^(pref:)?[-,_a-zA-Z0-9]*$/"; // sort_mode 
$patterns['string']  = "/^<\/?(b|strong|small|br *\/?|ul|li|i)>|[^<>\";#]*$/"; // find, and such extended chars
$patterns['stringlist']  = "/^[^<>\"#]*$/"; // to, cc, bcc (for string lists like: user1;user2;user3)
$patterns['vars']  = "/^[-_a-zA-Z0-9]*$/"; // for variable keys
$patterns['dotvars']  = "/^[-_a-zA-Z0-9\.]*$/"; // same pattern as a variable key, but that may contain a dot
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
$vartype['game'] = 'string'; // from games
// galaxia
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
$vartype['parentId'] = 'intSign';
$vartype['bannerId'] = 'int';
$vartype['rssId'] = 'int';
$vartype['page_ref_id'] = 'int';

function varcheck(&$array, $category) {
	global $patterns, $vartype, $prefs;

	$return = array();
	if ( is_array($array) ) {
		foreach ( $array as $rq => $rv ) {

			// check if the variable name is allowed
			if ( ! preg_match($patterns['vars'], $rq) ) {
				//die(tra("Invalid variable name : "). htmlspecialchars($rq));
			} elseif ( isset($vartype["$rq"]) ) {
				$has_sign = false;

				// Variable allowed to be empty?
				if ( '+' == substr($vartype[$rq], 0, 1) ) {
					if ( $rv == "" ) {
						$return[] = tra("Notice: this variable may not be empty:")
							.' <font color="red">$'.$category.'["'.$rq.'"]</font>';
						continue;
					}
					$has_sign = true;
				}

				if ( is_array($rv) ) {
					$tmp = varcheck($array[$rq], $category);
					if ($tmp != "") {	
						$return[] = $tmp;
					}
				} else {
					// Check single parameters
					$pattern_key = $has_sign ? substr($vartype[$rq], 1) : $vartype[$rq];
					if ( ! preg_match($patterns[$pattern_key], $rv) ) {
						$return[] = tra("Notice: invalid variable value:")
							.' $'.$category.'["'.$rq.'"] = <font color="red">'.htmlspecialchars($rv).'</font>';
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
if (isset($_SERVER["REQUEST_URI"])) {
  ini_set('session.cookie_path', str_replace( "\\", "/", dirname($_SERVER["REQUEST_URI"])));
}

if (!isset($_SERVER['QUERY_STRING'])) {
	$_SERVER['QUERY_STRING'] = '';
}
if (empty($_SERVER['REQUEST_URI'])) {
	$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
}
if (empty($_SERVER['SERVER_NAME'])) {
	$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];
}


// in the case of tikis on same domain we have to distinguish the realm
// changed cookie and session variable name by a name made with browsertitle 
$cookie_site = ereg_replace("[^a-zA-Z0-9]", "", $prefs['cookie_name']);
$user_cookie_site = 'tiki-user-'.$cookie_site;

// if remember me is enabled, check for cookie where auth hash is stored
// user gets logged in as the first user in the db with a matching hash
if (($prefs['rememberme'] != 'disabled') 
	and (isset($_COOKIE["$user_cookie_site"]))
	and (!isset($user) and !isset($_SESSION["$user_cookie_site"]))) {
	if ($prefs['feature_intertiki'] == 'y' and !empty($prefs['feature_intertiki_mymaster']) and $prefs['feature_intertiki_sharedcookie'] == 'y') {
		$rpcauth = $userlib->get_remote_user_by_cookie($_COOKIE["$user_cookie_site"]);
		if (is_object($rpcauth)) {
			$response_value = $rpcauth->value();
			if (is_object($response_value)) {
				$user = $response_value->scalarval();
			}
		}
	} else {
		$user = $userlib->get_user_by_cookie($_COOKIE["$user_cookie_site"]);
	}
	if ($user) {
		$_SESSION["$user_cookie_site"] = $user;
	}
}

// if the auth method is 'web site', look for the username in $_SERVER
if (($prefs['auth_method'] == 'ws') and (isset($_SERVER['REMOTE_USER']))) {
	if ($userlib->user_exists($_SERVER['REMOTE_USER'])) {
		$_SESSION["$user_cookie_site"] = $_SERVER['REMOTE_USER'];
	} elseif ($userlib->user_exists(str_replace("\\\\", "\\",$_SERVER['REMOTE_USER']))) {
		// Check for the domain\username with just one backslash
		$_SESSION["$user_cookie_site"] = str_replace("\\\\", "\\",$_SERVER['REMOTE_USER']);
	} elseif ($userlib->user_exists(substr($_SERVER['REMOTE_USER'], strpos($_SERVER['REMOTE_USER'], "\\") + 2))){
		// Check for the username without the domain name
		$_SESSION["$user_cookie_site"] = substr($_SERVER['REMOTE_USER'], strpos($_SERVER['REMOTE_USER'], "\\") + 2);
	}
}

// Check for Shibboleth Login
if ($prefs['auth_method'] == 'shib' and isset($_SERVER['REMOTE_USER'])){
	// Validate the user (if not created create it)
	if($userlib->validate_user($_SERVER['REMOTE_USER'],"","","")){
		$_SESSION["$user_cookie_site"] = $_SERVER['REMOTE_USER'];
	}
}

// if the username is already saved in the session, pull it from there
if (isset($_SESSION["$user_cookie_site"])) {
	$user = $_SESSION["$user_cookie_site"];

	// There could be a case where the session contains a user that doesn't exists in this tiki
	// or that has never used the login step in this tiki.
	// Example : If using the same PHP SESSION cookies for more than one tiki.
	$user_details = $userlib->get_user_details($user);
	if ( ! is_array($user_details) || ! is_array($user_details['info']) || (int)$user_details['info']['lastLogin'] <= 0 ) {
		global $cachelib; require_once("lib/cache/cachelib.php");
		$cachelib->invalidate('user_details_'.$user);
		$user_details = $userlib->get_user_details($user);
		if ( ! is_array($user_details) || ! is_array($user_details['info'])) {
			$user = NULL;
		}
	}
	unset($user_details);

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

// --------------------------------------------------------------

if ( ! $cachelib->isCached("allperms") ) {
	$allperms = $userlib->get_permissions(0, -1, 'permName_desc', '', '');
	$cachelib->cacheItem("allperms",serialize($allperms));
} else {
	$allperms = unserialize($cachelib->getCached("allperms"));
}
$allperms = $allperms["data"];

// Initializes permissions
$admin_perms = array();
foreach ( $allperms as $vperm ) {
	$perm = $vperm["permName"];
	$$perm = 'n';
	$smarty->assign("$perm", 'n');
	if ( $vperm['admin'] == 'y' ) {
		$admin_perms[] = $perm;
	}
}

// Permissions
if ( $user && ( $user == 'admin' || $userlib->user_has_permission($user, 'tiki_p_admin') ) ) {
	// Gives admins all permissions
	foreach ($allperms as $vperm) {
		$perm = $vperm['permName'];
		$$perm = 'y';
		$smarty->assign($perm, 'y');
	}
} else {
	$perms = $userlib->get_user_detailled_permissions($user);
	foreach ($perms as $perm) {
		$smarty->assign($perm['permName'], 'y');
		$$perm['permName'] = 'y';
		if ( in_array($perm['permName'], $admin_perms) ) { // assign all perms of the perm type
			$ps = $userlib->get_permissions(0, -1, 'permName_desc', '', $perm['type']);
			foreach ($ps['data'] as $p) {
				$$p['permName'] = 'y';
				$smarty->assign($p['permName'], 'y');
			}
		}
	}
}

unset($admin_perms);
unset($allperms);

// --------------------------------------------------------------
$magic_quotes_gpc = get_magic_quotes_gpc();
$clean_xss = ( $tiki_p_trust_input != 'y' );

// deal with register_globals
if ( ini_get('register_globals') ) {
	foreach ( array($_ENV, $_GET, $_POST, $_COOKIE, $_SERVER) as $superglob ) {
		foreach ( $superglob as $key=>$val ) {
			if ( isset($GLOBALS[$key]) && $GLOBALS[$key]==$val ) { // if global has been set some other way
				// that is OK (prevents munging of $_SERVER with ?_SERVER=rubbish etc.)
				unset($GLOBALS[$key]);
			}
		}
	}
}

$serverFilter = new DeclFilter;
if( $clean_xss ) {
	$serverFilter->addStaticKeyFilters( array(
		'QUERY_STRING' => 'xss',
		'REQUEST_URI' => 'xss',
		'PHP_SELF' => 'xss',
	) );
}
$jitServer = new JitFilter( $_SERVER );
$_SERVER = $serverFilter->filter( $_SERVER );

if( $magic_quotes_gpc ) {
	remove_gpc($_GET);
	remove_gpc($_POST);
	remove_gpc($_COOKIE);
}

// Rebuild request after gpc fix
// _REQUEST should only contain GET and POST in the app
$_REQUEST = array_merge($_GET, $_POST);

// Preserve unfiltered values accessible through JIT filtering
$jitPost = new JitFilter( $_POST );
$jitGet = new JitFilter( $_GET );
$jitRequest = new JitFilter( $_REQUEST );
$jitCookie = new JitFilter( $_COOKIE );

$jitPost->setDefaultFilter( 'xss' );
$jitGet->setDefaultFilter( 'xss' );
$jitRequest->setDefaultFilter( 'xss' );
$jitCookie->setDefaultFilter( 'xss' );

// Apply configured filters to all other input
if( ! isset( $inputConfiguration ) ) $inputConfiguration = array();
$inputFilter = DeclFilter::fromConfiguration( $inputConfiguration, array('catchAllFilter') );
if( $clean_xss ) {
	$inputFilter->addCatchAllFilter('xss');
}

$_GET = $inputFilter->filter( $_GET );
$_POST = $inputFilter->filter( $_POST );
$_COOKIE = $inputFilter->filter( $_COOKIE );

// Rebuild request with filtered values
$_REQUEST = array_merge($_GET, $_POST);

if ( $tiki_p_trust_input != 'y' ) {
	$varcheck_vars = array('_COOKIE', '_GET', '_POST', '_ENV', '_SERVER');
	$varcheck_errors = '';
	foreach ( $varcheck_vars as $var ) {
		if ( ! isset($$var) ) continue;
		if ( ( $tmp = varcheck($$var, $var) ) != '' ) {
			if ( $varcheck_errors != '' ) $varcheck_errors .= '<br />';
			$varcheck_errors .= $tmp;
		}
	}
	unset($tmp);
}

// deal with old request globals (e.g. used by Smarty)
$GLOBALS['HTTP_GET_VARS'] =& $_GET;
$GLOBALS['HTTP_POST_VARS'] =& $_POST;
$GLOBALS['HTTP_COOKIE_VARS'] =& $_COOKIE;
unset($GLOBALS['HTTP_ENV_VARS']);
unset($GLOBALS['HTTP_SERVER_VARS']);
unset($GLOBALS['HTTP_SESSION_VARS']);
unset($GLOBALS['HTTP_POST_FILES']);


// --------------------------------------------------------------

if (isset($_REQUEST['highlight']) || (isset($prefs['feature_referer_highlight']) && $prefs['feature_referer_highlight'] == 'y') ) {
  $smarty->load_filter('output','highlight');
}

if (function_exists('mb_internal_encoding')) {
	mb_internal_encoding("UTF-8");
}

// --------------------------------------------------------------

// Fix IIS servers not setting what they should set (ay ay IIS, ay ay)
if (!isset($_SERVER['QUERY_STRING']))
    $_SERVER['QUERY_STRING'] = '';

if (!isset($_SERVER['REQUEST_URI']) || empty($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
}

$smarty->assign("tikidomain", $tikidomain);

// Debug console open/close
$smarty->assign('debugconsole_style',
	isset($_COOKIE["debugconsole"]) && ($_COOKIE["debugconsole"] == 'o') ? 'display:block;' : 'display:none;'
);

?>
