<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-setup_base.php,v 1.142.2.4 2007-12-06 16:38:27 nkoth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
	die();
}

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
require_once("lib/tikiticketlib.php");
require_once("db/tiki-db.php");
require_once("setup_smarty.php"); 
require_once("lib/tikilib.php");
require_once("lib/cache/cachelib.php");
require_once("lib/logs/logslib.php");
include_once('lib/init/tra.php');
$tikilib = new TikiLib($dbTiki);

// Get tiki-setup_base needed preferences in one query
$prefs = array();
$needed_prefs = array(
	'session_lifetime' => '0',
	'session_db' => 'n',
	'sessions_silent' => 'disabled',
	'language' => 'en',
	'cookie_name' => 'tikiwiki',
	'rememberme' => 'disabled',
	'auth_method' => 'tiki',
);
$tikilib->get_preferences($needed_prefs, true, true);
extract($prefs);

// set session lifetime
if ($prefs['session_lifetime'] > 0) {
	ini_set('session.gc_maxlifetime',$prefs['session_lifetime']*60);
}

// is session data  stored in DB or in filesystem?
if ($prefs['session_db'] == 'y') {
	include('db/local.php');
	$ADODB_SESSION_DRIVER=$db_tiki;
	$ADODB_SESSION_CONNECT=$host_tiki;
	$ADODB_SESSION_USER=$user_tiki;
	$ADODB_SESSION_PWD=$pass_tiki;
	$ADODB_SESSION_DB=$dbs_tiki;
	unset($db_tiki);
	unset($host_tiki);
	unset($user_tiki);
	unset($pass_tiki);
	unset($dbs_tiki);
	ini_set('session.save_handler','user');
	include('session/adodb-session.php');
}

if ($sessions_silent == 'disabled' or !empty($_COOKIE)) {
	// enabing silent sessions mean a session is only started when a cookie is presented
	session_start();
}

require_once("lib/userslib.php");
$userlib = new UsersLib($dbTiki);
require_once("lib/tikiaccesslib.php");
$access = new TikiAccessLib();
require_once("lib/breadcrumblib.php");
//require_once("lib/tikihelplib.php");

// ------------------------------------------------------
// DEAL WITH XSS-TYPE ATTACKS AND OTHER REQUEST ISSUES

function make_clean(&$var,$gpc=false) {
	if ( is_array($var) ) {
		foreach ( $var as $key=>$val ) {
			make_clean($var[$key],$gpc);
		}
	} else {
		if ($gpc) $var = stripslashes($var);
		$var = preg_replace("~</?\s*(embed|object|applet)[^>]*>~si","",$var);
		if (preg_match("~</?\s*script[^>]*>?~si",$var)) {
			$var = preg_replace("~</?\s*script[^>]*>?~si","",$var);
			make_clean($var);
		}
		$var = preg_replace('~(inhibited_|on)(mouseover|mouseout|load|click|dblclick|focus|mousedown|mouseup|mousemove|mouseenter|mouseleave|blur|change|keydown|keypress|keyup|abort|dragdrop|error|load|move|reset|resize|select|submit|unload)(\s*)?=~si','inhibited_$2$3=',$var);
	}
}

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
make_clean($_GET,get_magic_quotes_gpc());
make_clean($_POST,get_magic_quotes_gpc());
make_clean($_COOKIE,get_magic_quotes_gpc());
make_clean($_SERVER['QUERY_STRING']);
make_clean($_SERVER['REQUEST_URI']);

// deal with old request globals
// Tiki uses them (admin for instance) so compatibility is required
if ( false ) { // if pre-PHP 4.1 compatibility is not required
	unset($GLOBALS['HTTP_GET_VARS']);
	unset($GLOBALS['HTTP_POST_VARS']);
	unset($GLOBALS['HTTP_COOKIE_VARS']);
	unset($GLOBALS['HTTP_ENV_VARS']);
	unset($GLOBALS['HTTP_SERVER_VARS']);
	unset($GLOBALS['HTTP_SESSION_VARS']);
	unset($GLOBALS['HTTP_POST_FILES']);
} else {
	$GLOBALS['HTTP_GET_VARS'] =& $_GET;
	$GLOBALS['HTTP_POST_VARS'] =& $_POST;
	$GLOBALS['HTTP_COOKIE_VARS'] =& $_COOKIE;
}

// mose : simulate strong var type checking for http vars
$patterns['login']   = "/^[-_a-zA-Z0-9@\.]*$/"; // special check for logins, not to be used in varcheck because compat with already created accounts
$patterns['int']   = "/^[0-9]*$/"; // *Id
$patterns['intSign']   = "/^[-+]?[0-9]*$/"; // *offset,
$patterns['char']  = "/^(pref:)?[-,_a-zA-Z0-9]*$/"; // sort_mode 
$patterns['string']  = "/^<\/?(b|strong|small|br *\/?|ul|li|i)>|[^<>\";#]*$/"; // find, and such extended chars
$patterns['stringlist']  = "/^[^<>\"#]*$/"; // to, cc, bcc (for string lists like: user1;user2;user3)
$patterns['vars']  = "/^[-_a-zA-Z0-9]*$/"; // for variable keys
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
$vartype['sort_mode'] = '+char'; // TODO: only allow valid field names and order here!
$vartype['file_sort_mode'] = 'char';
$vartype['file_offset'] = 'int';
$vartype['file_find'] = 'string';
$vartype['file_prev_offset'] = 'intSign';
$vartype['file_next_offset'] = 'intSign';
$vartype['comments_offset'] = 'int';
$vartype['comments_thresold'] = 'int';
$vartype['comments_parentId'] = '+int';
$vartype['thread_sort_mode'] = '+char';  // TODO: only allow valid field names and order here!
$vartype['thread_style'] = '+char';  // TODO: only allow valid field names and order here!
$vartype['comments_per_page'] = '+int';
$vartype['topics_offset'] = 'int';
$vartype['topics_sort_mode'] = '+char';  // TODO: only allow valid field names and order here!
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
$vartype['filter_name'] = '+string';
$vartype['newmajor'] = '+int';
$vartype['newminor'] = '+int';
$vartype['pid'] = '+int';
$vartype['remove_role'] = '+int';
$vartype['rolename'] = 'char';
$vartype['type'] = 'string';
$vartype['userole'] = 'int';
$vartype['focus'] = 'string';

function varcheck($array) {
  global $patterns, $vartype, $prefs;
  if (isset($array) and is_array($array)) {
    foreach ($array as $rq=>$rv) {
	  // check if the variable name is allowed
      if (!preg_match($patterns['vars'],$rq)) {
        //die(tra("Invalid variable name : "). htmlspecialchars($rq));
      } elseif (isset($vartype["$rq"])) {
      	// variable allowed to be empty?
      	if ('+'==substr($vartype[$rq],0,1)) {
      		if ($rv == "") {
						return(tra("Notice: this variable may not be empty:")." <font color='red'>$rq</font>");
      		}
      		// remove + from type
      		$vartype[$rq]=substr($vartype[$rq],1);
      	}
      	// expand arrays:
				// TODO: handle return value
        if (is_array($rv)) {
          varcheck($rv);
        // check single parameters:
        } else {
	        if (
	          (((substr($rq,-2,2) == 'Id' and $rq != 'reqId') or $vartype["$rq"] == 'int') and !preg_match($patterns['int'],$rv))
	          or ($vartype["$rq"] == 'intSign') and !preg_match($patterns['intSign'],$rv)
	          or ($vartype["$rq"] == 'url') and !preg_match($patterns['url'],$rv)
	          or ($vartype["$rq"] == 'char') and !preg_match($patterns['char'],$rv)
	          or ($vartype["$rq"] == 'hash') and !preg_match($patterns['hash'],$rv)
	          or ($vartype["$rq"] == 'string') and !preg_match($patterns['string'],$rv)
	          or ($vartype["$rq"] == 'stringlist') and !preg_match($patterns['stringlist'],$rv))
	        {
						return(tra("Notice: invalid variable value:")." $rq = <font color='red'>".htmlspecialchars($rv)."</font>");
	        }
        }
      }
    } // foreach
  }
} // varcheck($array)

$varcheck_errors=varcheck($_REQUEST);

// rebuild $_REQUEST after sanity check
unset($_REQUEST);
unset($_COOKIE['offset']);
$_REQUEST = array_merge($_COOKIE, $_POST, $_GET, $_ENV, $_SERVER);
if (!empty($_REQUEST['highlight'])) {
	if (is_array($_REQUEST['highlight'])) $_REQUEST['highlight'] = '';
	$_REQUEST['highlight'] = htmlspecialchars($_REQUEST['highlight']);
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
// changed cookie and session variable name by a name made with siteTitle 
$cookie_site = ereg_replace("[^a-zA-Z0-9]", "", $prefs['cookie_name']);
$user_cookie_site = 'tiki-user-'.$cookie_site;

// if remember me is enabled, check for cookie where auth hash is stored
// user gets logged in as the first user in the db with a matching hash
if (($prefs['rememberme'] != 'disabled') 
	and (isset($_COOKIE["$user_cookie_site"])) 
	and (!isset($user) and !isset($_SESSION["$user_cookie_site"]))) {
	$user = $userlib->get_user_by_cookie($_COOKIE["$user_cookie_site"]);
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

// check if phpCAS mods is installed 
if (is_file('lib/phpcas/source/CAS/CAS.php')) {
	$phpcas_enabled = 'y';
} else {
	$phpcas_enabled = 'n';
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
		global $cachelib;
		require_once("lib/cache/cachelib.php");
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

if (isset($_REQUEST['highlight']) || (isset($prefs['feature_referer_highlight']) && $prefs['feature_referer_highlight'] == 'y') ) {
  $smarty->load_filter('output','highlight');
}


/* \brief  substr with a utf8 string - works only with $start and $length positive or nuls
 * This function is the same as substr but works with multibyte
 * In a multybyte sequence, the first byte of a multibyte sequence that represents a non-ASCII character is always in the range 0xC0 to 0xFD
 * and it indicates how many bytes follow for this character.
 * All further bytes in a multibyte sequence are in the range 0x80 to 0xBF.
 */
if (function_exists('mb_substr')) {
    mb_internal_encoding("UTF-8");
}
else {
    function mb_substr($str, $start, $len = '', $encoding="UTF-8"){
        $limit = strlen($str);
        for ($s = 0; $start > 0;--$start) {// found the real start
            if ($s >= $limit)
                break;
            if ($str[$s] <= "\x7F")
                ++$s;
            else {
                ++$s; // skip length
                while ($str[$s] >= "\x80" && $str[$s] <= "\xBF")
                    ++$s;
            }
        }
        if ($len == '')
            return substr($str, $s);
        else {
            for ($e = $s; $len > 0; --$len) {//found the real end
                if ($e >= $limit)
                    break;
                if ($str[$e] <= "\x7F")
                    ++$e;
                else {
                    ++$e;//skip length
                    while ($str[$e] >= "\x80" && $str[$e] <= "\xBF" && $e < $limit)
                        ++$e;
                       }
            }
        return substr($str, $s, $e - $s);
		}
    }
}


// We might need to cache this on a per-user basis
// Cache cache
// function user_has_permission($user,$perm) 
if(!$cachelib->isCached("allperms")) {
	$allperms = $userlib->get_permissions(0, -1, 'permName_desc', '', '');
	$cachelib->cacheItem("allperms",serialize($allperms));
} else {
	$allperms = unserialize($cachelib->getCached("allperms"));
}
$allperms = $allperms["data"];

//Initializes permissions

foreach ($allperms as $vperm) {
	$perm = $vperm["permName"];
	$$perm = 'n';

	$smarty->assign("$perm", 'n');
}

// Permissions
if ($user == 'admin' || ($user && $userlib->user_has_permission($user, 'tiki_p_admin'))) {//Gives admins all permissions
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
		if ($perm['admin'] == 'y') { // assign all perms of the perm type
			$ps = $userlib->get_permissions(0, -1, 'permName_desc', '', $perm['type']);
			foreach ($ps['data'] as $perm) {
				$$perm['permName'] = 'y';
				$smarty->assign($perm['permName'], 'y');
			}
		}
	}
}

unset($allperms);

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
