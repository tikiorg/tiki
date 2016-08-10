<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

if (!isset($_SERVER['QUERY_STRING'])) {
	$_SERVER['QUERY_STRING'] = '';
}
if (empty($_SERVER['REQUEST_URI'])) {
	$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
}
if (empty($_SERVER['SERVER_NAME'])) {
	$_SERVER['SERVER_NAME'] = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']: '';
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
ini_set('magic_quotes_sybase', 'Off');
ini_set('magic_quotes_runtime', 0);
ini_set('allow_call_time_pass_reference', 'On');

$memory_limiter = new Tiki_MemoryLimit('128M'); // Keep in variable to hold scope

// ---------------------------------------------------------------------
// inclusions of mandatory stuff and setup
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
	'feature_fullscreen' => 'n',
	'error_reporting_level' => 0,
	'memcache_enabled' => 'n',
	'memcache_expiration' => 3600,
	'memcache_prefix' => 'tiki_',
	'memcache_compress' => 'y',
	'memcache_servers' => false,
	'min_pass_length' => 5,
	'pass_chr_special' => 'n',
	'cookie_consent_feature' => 'n',
	'cookie_consent_disable' => 'n',
	'cookie_consent_name' => 'tiki_cookies_accepted',

);

// check that tiki_preferences is there
if ($tikilib->query("SHOW TABLES LIKE 'tiki_preferences'")->numRows() == 0) {
	// smarty not initialised at this point to do a polite message, sadly
	header('location: tiki-install.php');
	exit;
}
$tikilib->get_preferences($needed_prefs, true, true);
global $systemConfiguration;
$prefs = $systemConfiguration->preference->toArray() + $prefs;

// mose : simulate strong var type checking for http vars
$patterns['int'] = "/^[0-9]*$/"; // *Id
$patterns['intSign'] = "/^[-+]?[0-9]*$/"; // *offset,
$patterns['char'] = "/^(pref:)?[-,_a-zA-Z0-9]*$/"; // sort_mode
$patterns['string'] = "/^<\/?(b|strong|small|br *\/?|ul|li|i)>|[^<>\";#]*$/"; // find, and such extended chars
$patterns['stringlist'] = "/^[^<>\"#]*$/"; // to, cc, bcc (for string lists like: user1;user2;user3)
$patterns['vars'] = "/^[-_a-zA-Z0-9]*$/"; // for variable keys
$patterns['dotvars'] = "/^[-_a-zA-Z0-9\.]*$/"; // same pattern as a variable key, but that may contain a dot
$patterns['hash'] = "/^[a-z0-9]*$/"; // for hash reqId in live support
$patterns['url'] = "/^(https?:\/\/)?[^<>\"]*$/";

// IIS always sets the $_SERVER['HTTPS'] value (on|off)
$noSSLActive = !isset($_SERVER['HTTPS']) || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'off');
if (isset($prefs['session_protected']) && $prefs['session_protected'] == 'y' && $noSSLActive && php_sapi_name() != 'cli') {
	header("Location: https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
	exit;
}

$cachelib = TikiLib::lib('cache');
$logslib = TikiLib::lib('logs');
include_once ('lib/init/tra.php');
$tikidate = TikiLib::lib('tikidate');
// set session lifetime
if (isset($prefs['session_lifetime']) && $prefs['session_lifetime'] > 0) {
	ini_set('session.gc_maxlifetime', $prefs['session_lifetime'] * 60);
}
// is session data  stored in DB or in filesystem?
if (isset($prefs['session_storage']) && $prefs['session_storage'] == 'db') {
	$db = TikiDb::get();
	if ($db instanceof TikiDb_MasterSlaveDispatch) {
		$db->getReal();
	}

	if ($db instanceof TikiDb_AdoDb) {
		require_once ('lib/tikisession-adodb.php');
	} elseif ($db instanceof TikiDb_Pdo) {
		require_once ('lib/tikisession-pdo.php');
	}
} elseif ( isset($prefs['session_storage']) && $prefs['session_storage'] == 'memcache' && TikiLib::lib("memcache")->isEnabled() ) {
	require_once ('lib/tikisession-memcache.php');
}

if ( ! isset( $prefs['session_cookie_name'] ) || empty( $prefs['session_cookie_name'] ) ) {
	$prefs['session_cookie_name'] = session_name();
}

session_name($prefs['session_cookie_name']);

// Only accept PHP's session ID in URL when the request comes from the tiki server itself
// This is used by features that need to query the server to retrieve tiki's generated html and images (e.g. pdf export)
// It could be , that the server initiates his request with its own ip, so we check also if server == remote
// Note: this is an incomplete implemenation - the session handling does not really work this way. Session data is lost and not regenerated.
// Maybe better to use tokens: see i.e. the example in lib/pdflib.php
if (isset($_GET[session_name()]) && (($tikilib->get_ip_address() == '127.0.0.1') || ($_SERVER["SERVER_ADDR"] == $_SERVER["REMOTE_ADDR"]))) {
	$_COOKIE[session_name()] = $_GET[session_name()];
	session_id($_GET[session_name()]);		
}

//Set tikiroot and tikidomain to blank string if not set.
if (empty($tikiroot)) {
	$tikiroot = "";
}
if (empty($tikidomain)) {
	$tikidomain = "";
}

if ($prefs['cookie_consent_feature'] === 'y' && empty($_COOKIE[$prefs['cookie_consent_name']]) && $prefs['cookie_consent_disable'] !== 'y' ) {
	// No consent yet
	$feature_no_cookie = true;
} else {
	// Cookie consent not implemented or consent given or consent forced with preference cookie_consent_disable
	$feature_no_cookie = false;
}

$start_session = true;
$extra_cookie_name = session_name() . 'CV';
if ( $prefs['session_silent'] == 'y' && empty($_COOKIE[session_name()]) && empty($_COOKIE[$extra_cookie_name]) ) {
	$start_session = false;
}

// If called from the CDN, refuse to execute anything
$cdn_pref = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? $prefs['tiki_cdn_ssl'] : isset($prefs['tiki_cdn']) ? $prefs['tiki_cdn'] : '' ;
if ( $cdn_pref ) {
	$host = parse_url($cdn_pref, PHP_URL_HOST);
	if (isset($_SERVER['HTTP_HOST']) && $host == $_SERVER['HTTP_HOST'] ) {
		header("HTTP/1.0 410 Gone");
		echo "This is a Content Delivery Network (CDN) to speed up delivery of images, CSS, and javascript files. However, PHP code is not executed.";
		exit;
	}
}
if (isset($_SERVER["REQUEST_URI"])) {
	ini_set('session.cookie_path', str_replace("\\", "/", $tikiroot));
	if ( $start_session ) {
		// enabing silent sessions mean a session is only started when a cookie is presented
		$session_params = session_get_cookie_params();
		session_set_cookie_params($session_params['lifetime'], $tikiroot);
		unset($session_params);

		try {
						
			Zend\Session\Container::getDefaultManager()->start();

			/* This portion may seem strange, but it is an extra validation against session
			 * collisions. An extra cookie is set with an additional random value. When loading
			 * the session, it makes sure the extra cookie matches the one in the session. Otherwise
			 * it destroys the session and reloads the page for the user.
			 *
			 * Effectively, in the occurence of a collision, both users are kicked out.
			 * This is an extremely rare occurence that is hard to reproduce by nature.
			 */ 
			if (isset($_SESSION['extra_validation'])) {
				$cookie = isset($_COOKIE[$extra_cookie_name]) ? $_COOKIE[$extra_cookie_name] : null;

				if ($cookie !== $_SESSION['extra_validation']) {

					TikiLib::lib('logs')->add_log('system', 'session cookie validation failed');

					Zend\Session\Container::getDefaultManager()->destroy();
					header('Location: ' . $_SERVER['REQUEST_URI']);
					exit;
				}
			} else {
				$sequence = $tikilib->generate_unique_sequence(16);
				$_SESSION['extra_validation'] = $sequence;
				setcookie($extra_cookie_name, $sequence, time() + 365*24*3600, ini_get('session.cookie_path'));
				unset($sequence);
			}
		} catch( Zend\Session\Exception\ExceptionInterface $e ) {
			// Ignore
		} catch( Zend\Stdlib\Exception\InvalidArgumentException $e ) {
			// Ignore
		}
	}
}

// Moved here from tiki-setup.php because smarty use a copy of session
if (isset($prefs['feature_fullscreen']) && $prefs['feature_fullscreen'] == 'y') {
	require_once ('lib/setup/fullscreen.php');
}

// Retrieve Tiki addons
TikiAddons::refresh();

// Retrieve all preferences
require_once ('lib/setup/prefs.php');

$access = TikiLib::lib('access');

require_once ('lib/setup/absolute_urls.php');
// Smarty needs session since 2.6.25
global $smarty;
$smarty = TikiLib::lib('smarty');

// Define the special maxRecords global variable
$maxRecords = $prefs['maxRecords'];
$smarty->assignByRef('maxRecords', $maxRecords);

global $userlib;
$userlib = TikiLib::lib('user');
require_once ('lib/breadcrumblib.php');
// ------------------------------------------------------
// DEAL WITH XSS-TYPE ATTACKS AND OTHER REQUEST ISSUES
/**
 * @param $var
 */
function remove_gpc(&$var)
{
	if (is_array($var)) {
		foreach ($var as $key => $val) {
			remove_gpc($var[$key]);
		}
	} else {
		$var = stripslashes($var);
	}
}
// parameter type definitions. prepend a + if variable may not be empty, e.g. '+int'
$vartype['id'] = '+int';
$vartype['forumId'] = '+int';
$vartype['offset'] = 'intSign';
$vartype['prev_offset'] = 'intSign';
$vartype['next_offset'] = 'intSign';
$vartype['threshold'] = 'int';
$vartype['sort_mode'] = '+char';
$vartype['file_sort_mode'] = 'char';
$vartype['file_offset'] = 'int';
$vartype['file_find'] = 'string';
$vartype['file_prev_offset'] = 'intSign';
$vartype['file_next_offset'] = 'intSign';
$vartype['comments_offset'] = 'int';
$vartype['comments_threshold'] = 'int';
$vartype['comments_parentId'] = '+int';
$vartype['thread_sort_mode'] = '+char';
$vartype['thread_style'] = '+char';
$vartype['comments_per_page'] = '+int';
$vartype['topics_offset'] = 'int';
$vartype['topics_sort_mode'] = '+char';
$vartype['theme'] = 'string';
$vartype['flag'] = 'char';
$vartype['lang'] = 'char';
$vartype['language'] = 'char';
$vartype['page'] = 'string';
$vartype['edit_mode'] = 'char';
$vartype['find'] = 'string';
$vartype['topic_find'] = 'string';
$vartype['initial'] = 'string';
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
$vartype['filesyntax'] = 'string';
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
/**
 * @param $array
 * @param $category
 * @return string
 */
function varcheck(&$array, $category)
{
	global $patterns, $vartype, $prefs;
	$return = array();
	if (is_array($array)) {
		foreach ($array as $rq => $rv) {
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
	if (is_array($_REQUEST['highlight'])) {
		$_REQUEST['highlight'] = '';
	}
	$_REQUEST['highlight'] = htmlspecialchars($_REQUEST['highlight']);
	// Convert back sanitization tags into real tags to avoid them to be displayed
	$_REQUEST['highlight'] = str_replace('&lt;x&gt;', '<x>', $_REQUEST['highlight']);
}
// ---------------------------------------------------------------------

/*
 * Clean variables past in _GET & _POST & _COOKIE
 */
$magic_quotes_gpc = get_magic_quotes_gpc();
if ($magic_quotes_gpc) {
	remove_gpc($_GET);
	remove_gpc($_POST);
	remove_gpc($_COOKIE);
}

global $base_uri;
if (!empty($base_uri) && is_object($smarty)) {
	$smarty->assign('base_uri', $base_uri);
}

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
		if ($prefs['cookie_refresh_rememberme'] === 'y') {
			if (empty($userId)) {    // for intertiki
				$userId = $userlib->get_user_id($user);
			}
			$secret = $userlib->create_user_cookie($userId);
			setcookie($user_cookie_site, $secret . '.' . $userId, $tikilib->now + $prefs['remembertime'], $prefs['cookie_path'], $prefs['cookie_domain']);
			$logslib->add_log('login', 'refreshed a cookie for ' . $prefs['remembertime'] . ' seconds');
		}
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
		if ($userlib->add_user($_SERVER['REMOTE_USER'], '', '')) {
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
	if (!is_array($user_details) || !is_array($user_details['info']) || (int) $user_details['info']['lastLogin'] <= 0) {
		$cachelib = TikiLib::lib('cache');
		$cachelib->invalidate('user_details_' . $user);
		$user_details = $userlib->get_user_details($user);
		if (!is_array($user_details) || !is_array($user_details['info'])) {
			$user = null;
		}
	}
	unset($user_details);

	// Generate anti-CSRF ticket
	if ($prefs['feature_ticketlib2'] == 'y' && !isset($_SESSION['ticket'])) {
		$_SESSION['ticket'] = md5(uniqid(rand()));
	}
} else {
	$user = null;

	if ( isset($prefs['login_http_basic']) && $prefs['login_http_basic'] === 'always' ||
		(isset($prefs['login_http_basic']) && $prefs['login_http_basic'] === 'ssl' && isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')) {
		// Authenticate if the credentials are present, do nothing otherwise
		if (! empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
			$_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
		}
		if (! empty($_SERVER['HTTP_AUTHORIZATION'])) {
			$ha = base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6));
			$ha = explode(':', $ha, 2);
			
			if (count($ha) == 2) {
				list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = $ha;
			}
		}
		if (isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
			$validate = $userlib->validate_user($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
			if ($validate[0]) {
				$user = $validate[1];
				$userlib->confirm_user($user);
			} else {
				header('WWW-Authenticate: Basic realm="'.$tikidomain.'"');
				header('HTTP/1.0 401 Unauthorized');
				exit;
			}
		}
	}
}

$smarty->assign('CSRFTicket', isset( $_SESSION['ticket'] ) ? $_SESSION['ticket'] : null);

require_once ('lib/setup/perms.php');
// --------------------------------------------------------------
// deal with register_globals
if (ini_get('register_globals')) {
	foreach (array($_ENV, $_GET, $_POST, $_COOKIE, $_SERVER) as $superglob) {
		foreach ($superglob as $key => $val) {
			if (isset($GLOBALS[$key]) && $GLOBALS[$key] == $val) {
				// if global has been set some other way
				// that is OK (prevents munging of $_SERVER with ?_SERVER=rubbish etc.)
				unset($GLOBALS[$key]);
			}
		}
	}
}
$serverFilter = new DeclFilter;
if ( ( isset($prefs['tiki_allow_trust_input']) && $prefs['tiki_allow_trust_input'] ) !== 'y' || $tiki_p_trust_input != 'y') {
	$serverFilter->addStaticKeyFilters(array('QUERY_STRING' => 'xss', 'REQUEST_URI' => 'url', 'PHP_SELF' => 'url',));
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
if (!isset($inputConfiguration)) {
	$inputConfiguration = array();
}

array_unshift(
	$inputConfiguration, array(
		'staticKeyFilters' => array(
			'menu' => 'striptags',
			'cat_categorize' => 'alpha',
			'tabs' => 'striptags',
			'javascript_enabled' => 'alpha',
			$prefs['cookie_consent_name'] => 'alpha',
			'mobile_mode' => 'alpha',
			'categ' => 'striptags',
			'local_tz' => 'text',
			'preview' => 'text',
			'rbox' => 'text',
		),
		'staticKeyFiltersForArrays' => array(
			'cat_managed' => 'digits',
			'cat_categories' => 'digits',
		),
	)
);

$inputFilter = DeclFilter::fromConfiguration($inputConfiguration, array('catchAllFilter'));
if ( ( isset($prefs['tiki_allow_trust_input']) && $prefs['tiki_allow_trust_input'] !== 'y' ) || $tiki_p_trust_input != 'y') {
	$inputFilter->addCatchAllFilter('xss');
}
$cookieFilter = DeclFilter::fromConfiguration($inputConfiguration, array('catchAllFilter'));
$cookieFilter->addCatchAllFilter('striptags');

$_GET = $inputFilter->filter($_GET);
$_POST = $inputFilter->filter($_POST);
$_COOKIE = $cookieFilter->filter($_COOKIE);
// Rebuild request with filtered values
$_REQUEST = array_merge($_GET, $_POST);
if ( ( isset($prefs['tiki_allow_trust_input']) && $prefs['tiki_allow_trust_input'] !== 'y' ) || $tiki_p_trust_input != 'y') {
	$varcheck_vars = array('_COOKIE', '_GET', '_POST', '_ENV', '_SERVER');
	$varcheck_errors = '';
	foreach ($varcheck_vars as $var) {
		if (!isset($$var)) {
			continue;
		}
		if (($tmp = varcheck($$var, $var)) != '') {
			if ($varcheck_errors != '') {
				$varcheck_errors.= '<br />';
			}
			$varcheck_errors.= $tmp;
		}
	}
	unset($tmp);
}

if (count($_FILES)) {
	$mimelib = TikiLib::lib('mime');

	foreach ($_FILES as $key => & $upload_file_info) {
		if (is_array($upload_file_info['tmp_name'])) {
			foreach ($upload_file_info['tmp_name'] as $k => $tmp_name) {
				if ($tmp_name) {
					$type = $mimelib->from_path($upload_file_info['name'][$k], $tmp_name);
					$upload_file_info['type'][$k] = $type;
				}
			}
		} elseif ($upload_file_info['tmp_name']) {
			$type = $mimelib->from_path($upload_file_info['name'], $upload_file_info['tmp_name']);
			$upload_file_info['type'] = $type;
		}
	}
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
	$smarty->loadFilter('output', 'highlight');
}
if (function_exists('mb_internal_encoding')) {
	mb_internal_encoding("UTF-8");
}
// --------------------------------------------------------------
// Fix IIS servers not setting what they should set (ay ay IIS, ay ay)
if (!isset($_SERVER['QUERY_STRING'])) {
	$_SERVER['QUERY_STRING'] = '';
}



$smarty->assign("tikidomain", $tikidomain);
