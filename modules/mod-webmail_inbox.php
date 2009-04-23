<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (!$user) {
	$smarty->assign('tpl_module_title', tra('Webmail error'));
	$smarty->assign('error', 'You are not logged in');
	return;	// modules cannot "exit", they must "return" to keep tiki alive
}

if ($prefs['feature_webmail'] != 'y') {
	$smarty->assign('tpl_module_title', tra('Webmail error'));
	$smarty->assign('error', 'This feature is disabled');
	return;
}
global $tiki_p_use_webmail;
if ($tiki_p_use_webmail != 'y') {
	$smarty->assign('tpl_module_title', tra('Webmail error'));
	$smarty->assign('error', 'Permission denied to use this feature');
	return;
}

if ($prefs['feature_ajax'] == "y") {
	// this includes what we need for ajax
	require_once ("tiki-webmail_ajax.php");
}

global $webmaillib, $dbTiki, $headerlib, $user, $headerlib, $webmail_reload;
if (!isset($webmaillib)) { include_once ('lib/webmail/webmaillib.php'); }
require_once ("lib/webmail/net_pop3.php");
include_once ("lib/webmail/tikimaillib.php");

// get autoRefresh val from account so it can go into the page JS
$current = $webmaillib->get_current_webmail_account($user);
if ($current && $current['autoRefresh'] > 0) {
	$headerlib->add_js('var autoRefresh = '.($current['autoRefresh'] * 1000).';');
}
$webmail_reload = (isset($module_params["reload"]) && $module_params['reload'] == 'y'); 

//$no_contact_instance = true;  //This prevents the lib from setting $contactlib
//include_once ('lib/webmail/contactlib.php');
//$contactlib = new ContactLib($dbTiki);

global $webmail_list;
$webmail_list = array();

function webmail_refresh() {	// called in ajax mode
	global $webmaillib, $headerlib, $user, $smarty, $webmail_list, $current, $webmail_reload;
	
	if (!$current) {
		$current = $webmaillib->get_current_webmail_account($user);
	}
	if (!$current) {
		$smarty->assign('tpl_module_title', tra('Webmail error'));
		$smarty->assign('error', 'No accounts set up');
		return;
	}
	
	$smarty->assign('current', $current);
	
	$timeout = -1;
	if ($current['autoRefresh'] > 0) {
		$timeout = time() - $current['autoRefresh'];
	}
	$smarty->assign('flagsPublic',$current['flagsPublic']);
	
	$serialized_params = $current['accountId'].':'.$current['user'].':'.$current['account'];
	if (!$webmail_reload && isset($_SESSION['webmailbox'][$serialized_params]) && isset($_SESSION['webmailbox'][$serialized_params]['timestamp']) && $_SESSION['webmailbox'][$serialized_params]['timestamp'] >  $timeout) {
		$webmail_list = $_SESSION['webmailbox'][$serialized_params]['webmail_list'];
	
	} else {	// no cached list of timed out

		// start getting mail
		$pop3 = new Net_POP3();
		
		$r1 = $pop3->connect($current["pop"]);
		$r2 = $pop3->login($current["username"], $current["pass"]);
	
		if ($r1 !== true || $r2 !== true) {
			$msg = "";
			if ($r1 !== true){
				$msg .= tra('The connection failed, so check the server names.');
			} else {
				$msg .=  tra('The connection was OK.');
				if (get_class($r2) == 'PEAR_Error'){
					$msg .= tra(' But the login failed, so check the Username and Password.');
				};
			};
			$smarty->assign('error', $msg);
			$smarty->assign('tpl_module_title', tra('Webmail error'));
			return;
		}
	
		$mailsum = $pop3->numMsg();
	
		$numshow = $current["msgs"];
	
		if (isset($_REQUEST["start"]) && $_REQUEST["start"] > $mailsum)
			$_REQUEST["start"] = $mailsum;
		if (!isset($_REQUEST["start"]))
			$_REQUEST["start"] = $mailsum;
	
		$upperlimit = $_REQUEST["start"];
		$smarty->assign('start', $_REQUEST["start"]);
	
		for ($i = $upperlimit; $i > 0 && count($webmail_list) < $numshow; $i--) {
			if (isset($_REQUEST["filter"])) {
				$aux = $filtered[$i];
			} else {
				$aux = $pop3->getParsedHeaders($i);
				$mail = preg_split('/[<>]/', $aux["From"], -1,PREG_SPLIT_NO_EMPTY);
				$aux["sender"]["name"] = $mail[0];
				$aux["sender"]["email"] = $mail[1];
				if (empty($aux["sender"]["email"])) {
					$aux["sender"]["email"] = $aux["sender"]["name"];
				} else if (!strstr($aux["sender"]["email"], '@')) {
					$e = $aux["sender"]["name"];
					$aux["sender"]["name"] = $aux["sender"]["email"];
					$aux["sender"]["email"] =  $aux["sender"]["name"];
				}
				$aux["subject"] = decode_subject_utf8($aux["Subject"] );
				$aux["timestamp"] = strtotime($aux['Date']);
				$l = $pop3->_cmdList($i);
				$aux["size"] = $l["size"];
				$aux["realmsgid"] = ereg_replace("[<>]","",$aux["Message-ID"]);
				$webmaillib->replace_webmail_message($current["accountId"], $user, $aux["realmsgid"]);
				list($aux["isRead"], $aux["isFlagged"], $aux["isReplied"]) = $webmaillib->get_mail_flags($current["accountId"], $user, $aux["realmsgid"]);
	
				$aux["sender"]["name"] = htmlspecialchars($aux["sender"]["name"]);
	
				if (empty($aux["subject"])) {
					$aux["subject"] = '[' . tra('No subject'). ']';
				}
	
				$aux["subject"] = htmlspecialchars($aux["subject"]);
			}
			$aux["msgid"] = $i;
			$webmail_list[] = $aux;
		}
		$lowerlimit = $i;
	
		if ($lowerlimit < 0)
			$lowerlimit = 0;
	
		$showstart = $mailsum - $upperlimit + 1;
		$showend = $mailsum - $lowerlimit;
		$smarty->assign('showstart', $showstart);
		$smarty->assign('showend', $showend);
		$smarty->assign('total', $mailsum);
	
		if ($lowerlimit > 0) {
			$smarty->assign('nextstart', $lowerlimit);
		} else {
			$smarty->assign('nextstart', '');
		}
	
		if ($upperlimit <> $mailsum) {
			$prevstart = $upperlimit + $numshow;
	
			if ($prevstart > $mailsum)
				$prevstart = $mailsum;
	
			$smarty->assign('prevstart', $prevstart);
		} else {
			$smarty->assign('prevstart', '');
		}
	
		if ($_REQUEST["start"] <> $mailsum) {
			$smarty->assign('first', $mailsum);
		} else {
			$smarty->assign('first', '');
		}
	
		// Now calculate the last message block
		$last = $mailsum % $numshow;
	
		if ($_REQUEST["start"] <> $last) {
			$smarty->assign('last', $last);
		} else {
			$smarty->assign('last', '');
		}
	
		$pop3->disconnect();
		
		$_SESSION['webmailbox'][$serialized_params]['webmail_list'] = $webmail_list;
		$_SESSION['webmailbox'][$serialized_params]['timestamp'] = time();

	}		// endif no cached list of timed out
}

if (isset($_REQUEST['refresh_mail']) || (isset($_REQUEST['xjxfun']) && $_REQUEST['xjxfun'] == 'refreshWebmail')) {	// YUK!
	webmail_refresh();
}

$module_params["autoloaddelay"] = isset($module_params["autoloaddelay"]) ? isset($module_params["autoloaddelay"]) : 1;
if ($module_params["autoloaddelay"] > -1) {
	$headerlib->add_js('setTimeout("doRefreshWebmail()", '.($module_params["autoloaddelay"] * 1000).');');
}

$smarty->assign('list', $webmail_list);

$smarty->assign('maxlen', isset($module_params["maxlen"]) ? $module_params["maxlen"] : 26);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
$smarty->assign('request_uri', strpos($_SERVER['REQUEST_URI'], '?') === false ? $_SERVER['REQUEST_URI'].'?' : $_SERVER['REQUEST_URI'].'&');
$module_rows = count($webmail_list);
$smarty->assign('module_rows', $module_rows);
if (isset($module_params['title'])) {
	$smarty->assign('tpl_module_title', $module_params['title']);
}
