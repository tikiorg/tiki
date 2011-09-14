<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$version = "0.2";

include 'tiki-setup.php';
require_once("XML/Server.php");

if ($prefs['feature_intertiki'] != 'y' || $prefs['feature_intertiki_server'] != 'y' || $prefs['feature_intertiki_mymaster']) {

	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<methodResponse><fault><value><struct><member><name>faultCode</name><value><int>403</int></value></member>";
	echo "<member><name>faultString</name><value><string>Server is not configured</string></value></member></struct></value></fault></methodResponse>";
	exit;
}

function lograw($file,$line) {
	$fp = fopen($file,'a+');
	fputs($fp,"$line\n");
	fclose($fp);
}

function logit($file,$txt,$user,$code,$from) {
	global $tikilib;
	$line = $tikilib->get_ip_address()." - $user - ". date('[m/d/Y:H:i:s]')." \"$txt\" $code \"$from\"";
	lograw($file,$line);
}

define('INTERTIKI_OK',200);
define('INTERTIKI_BADKEY',401);
define('INTERTIKI_BADUSER',404);

$map = array(
	"intertiki.validate" => array("function"=>"validate"),
	'intertiki.setUserInfo' => array('function' => 'set_user_info'),
	"intertiki.logout" => array("function"=>"logout"),
	"intertiki.cookiecheck" => array("function"=>"cookie_check"),
	"intertiki.version" => array("function"=>"get_version"),
	'intertiki.getUserInfo' => array('function' => 'get_user_info'),
	'intertiki.getRegistrationPrefs' => array('function' => 'get_registration_prefs'),
	'intertiki.registerUser' => array('function' => 'register_user')
);
$s = new XML_RPC_Server($map);

function validate($params) {
	global $tikilib,$userlib,$prefs,$logslib;

	$key = $params->getParam(0); $key = $key->scalarval(); 
	$login = $params->getParam(1); $login = $login->scalarval(); 
	$pass = $params->getParam(2); $pass = $pass->scalarval(); 
	$slave = $params->getParam(3); $slave = $slave->scalarval();
	$hashkey = $params->getParam(4); $hashkey = $hashkey->scalarval();
	
	if (!isset($prefs['known_hosts'][$key]) or $prefs['known_hosts'][$key]['ip'] != $tikilib->get_ip_address()) {
		$msg = tra('Invalid server key');
		if (!empty($prefs['intertiki_errfile']))
			logit($prefs['intertiki_errfile'],$msg,$key,INTERTIKI_BADKEY,$prefs['known_hosts'][$key]['name']);
		$logslib->add_log('intertiki',$msg.' from '.$prefs['known_hosts'][$key]['name'],$login);
		return new XML_RPC_Response(0, 101, $msg);
	}
	
	list($isvalid, $dummy, $error) = $userlib->validate_user($login,$pass,'','');
	if(!$isvalid) {
		$msg = tra('Invalid username or password');
		if ($prefs['intertiki_errfile']) logit($prefs['intertiki_errfile'],$msg,$login,INTERTIKI_BADUSER,$prefs['known_hosts'][$key]['name']);
		$logslib->add_log('intertiki',$msg.' from '.$prefs['known_hosts'][$key]['name'],$login);
		if (!$userlib->user_exists($login)) {
		    // slave client is supposed to disguise 102 code as 101 not to show
		    // crackers that user does not exists. 102 is required for telling slave
		    // to delete user there
		    return new XML_RPC_Response(0, 102, $msg);
		} else {
		    return new XML_RPC_Response(0, 101, $msg);
		}
	} 
	if ($prefs['intertiki_logfile']) logit($prefs['intertiki_logfile'],"logged",$login,INTERTIKI_OK,$prefs['known_hosts'][$key]['name']);
	$userInfo = $userlib->get_user_info($login);
	$userlib->create_user_cookie($userInfo['userId'], $hashkey);

	if ($slave) {
	    $logslib->add_log('intertiki','auth granted from '.$prefs['known_hosts'][$key]['name'],$login);
	    global $userlib;
		$user_details = $userlib->get_user_details($login);
		$user_info = $userlib->get_user_info($login);
		$ret['avatarData'] = new XML_RPC_Value($user_info['avatarData'], "base64");
		$ret['user_details'] = new XML_RPC_Value(serialize($user_details), "string");
		//$fp=fopen('temp/toto', 'w+');fwrite($fp, var_export($ret, true));fclose($fp);
		return new XML_RPC_Response(new XML_RPC_Value($ret, "struct"));
	} else {
	    $logslib->add_log('intertiki','auth granted from '.$prefs['known_hosts'][$key]['name'],$login);
	    return new XML_RPC_Response(new XML_RPC_Value(1, "boolean"));
	}
}

function set_user_info($params) {
	global $tikilib, $userlib, $prefs;
	if ($prefs['feature_userPreferences'] != 'y') {
		return new XML_RPC_Response(new XML_RPC_Value(1, 'boolean'));
	}
	$key = $params->getParam(0); $key = $key->scalarval(); 
	if (!isset($prefs['known_hosts'][$key]) or $prefs['known_hosts'][$key]['ip'] != $tikilib->get_ip_address()) {
		$msg = tra('Invalid server key');
		if ($prefs['intertiki_errfile']) logit($prefs['intertiki_errfile'],$msg,$key,INTERTIKI_BADKEY,$prefs['known_hosts'][$key]['name']);
		$logslib->add_log('intertiki',$msg.' from '.$prefs['known_hosts'][$key]['name'],$login);
		return new XML_RPC_Response(0, 101, $msg);
	}
	$login = $params->getParam(1); $login = $login->scalarval(); 
	$userlib->interSetUserInfo($login, $params->getParam(2));
	return new XML_RPC_Response(new XML_RPC_Value(1, 'boolean'));
}

function logout($params) {
	global $tikilib, $userlib,$logslib,$prefs;
	$key = $params->getParam(0); $key = $key->scalarval();
	$login = $params->getParam(1); $login = $login->scalarval();
	if (!isset($prefs['known_hosts'][$key]) or $prefs['known_hosts'][$key]['ip'] != $tikilib->get_ip_address()) {
		$msg = tra('Invalid server key');
		if ($prefs['intertiki_errfile']) logit($prefs['intertiki_errfile'],$msg,$key,INTERTIKI_BADKEY,$prefs['known_hosts'][$key]['name']);
		$logslib->add_log('intertiki',$msg.' from '.$prefs['known_hosts'][$key]['name'],$login);
		return new XML_RPC_Response(0, 101, $msg);
	}
	$userlib->user_logout($login, true);
	$userInfo = $this->get_user_info($login);
	$userlib->delete_user_cookie($userInfo['userId']);
	if ($prefs['intertiki_logfile']) logit($prefs['intertiki_logfile'],"logout",$login,INTERTIKI_OK,$prefs['known_hosts'][$key]['name']);
	$logslib->add_log('intertiki','auth revoked from '.$prefs['known_hosts'][$key]['name'],$login);
	return new XML_RPC_Response(new XML_RPC_Value(1, "boolean"));
}

function cookie_check($params) {
	global $tikilib, $userlib,$prefs;
	$key = $params->getParam(0); $key = $key->scalarval();
	$hash = $params->getParam(1); $hash = $hash->scalarval();
	if (!isset($prefs['known_hosts'][$key]) or $prefs['known_hosts'][$key]['ip'] != $tikilib->get_ip_address()) {
		$msg = tra('Invalid server key');
		if ($prefs['intertiki_errfile']) logit($prefs['intertiki_errfile'],$msg,$key,INTERTIKI_BADKEY,$prefs['known_hosts'][$key]['name']);
		$logslib->add_log('intertiki',$msg.' from '.$prefs['known_hosts'][$key]['name'],$login);
		return new XML_RPC_Response(0, 101, $msg);
	}
	$result = $userlib->get_user_by_cookie($hash);
	// $fp=fopen('temp/interlogtest','a+');fputs($fp,"main      -- ".$hash."\n");fclose($fp);
	if ($result) {
		return new XML_RPC_Response(new XML_RPC_Value($result, "string"));
	}
	$msg = tra('Cookie not found');
	return new XML_RPC_Response(0, 101, $msg);
}

function get_version($params) {
	global $version;
	return new XML_RPC_Response(new XML_RPC_Value($version, "int"));
}

function get_user_info($params) {
	global $tikilib, $prefs, $userlib;
	$key = $params->getParam(0); $key = $key->scalarval(); 
	if (!isset($prefs['known_hosts'][$key]) or $prefs['known_hosts'][$key]['ip'] != $tikilib->get_ip_address()) {
		$msg = tra('Invalid server key');
		if ($prefs['intertiki_errfile']) logit($prefs['intertiki_errfile'],$msg,$key,INTERTIKI_BADKEY,$prefs['known_hosts'][$key]['name']);
		$logslib->add_log('intertiki',$msg.' from '.$prefs['known_hosts'][$key]['name'],$login);
		return new XML_RPC_Response(0, 101, $msg);
	}
	$login = $params->getParam(1); $login = $login->scalarval(); 
	$email = $params->getParam(2); $email = $email->scalarval();
	if (empty($login)) {
		$login = empty($email)?'': $userlib->get_user_by_email($email);
	}
	if (empty($login)) {
		$msg = "Invalid username";
		return new XML_RPC_Response(0, 102, $msg);
	}
	if (empty($email)) {
		$email = $userlib->get_user_email($login);
	}
	$ret['login'] = new XML_RPC_Value($login, "string");
	$ret['email'] = new XML_RPC_Value($email, "string");
	return new XML_RPC_Response(new XML_RPC_Value($ret, "struct"));
}

function get_registration_prefs($params) {
	global $tikilib, $prefs, $registrationlib, $logslib;

	$key = $params->getParam(0); $key = $key->scalarval();
	if (!isset($prefs['known_hosts'][$key]) or $prefs['known_hosts'][$key]['ip'] != $tikilib->get_ip_address()) {
		$msg = tra('Invalid server key');
		if ($prefs['intertiki_errfile']) logit($prefs['intertiki_errfile'],$msg,$key,INTERTIKI_BADKEY,$prefs['known_hosts'][$key]['name']);
		$logslib->add_log('intertiki',$msg.' from '.$prefs['known_hosts'][$key]['name'],$login);
		return new XML_RPC_Response(0, 101, $msg);
	}

	if (!isset($prefs['known_hosts'][$key]['allowusersregister']) || ($prefs['known_hosts'][$key]['allowusersregister'] != 'y'))
		return new XML_RPC_Response(0, 101, "Users are not allowed to register via intertiki on this master.");

	require_once 'lib/registration/registrationlib.php';

	return new XML_RPC_Response(XML_RPC_encode($registrationlib->merged_prefs));
}

function register_user($params) {
	global $tikilib, $prefs, $registrationlib, $logslib;

	$key = $params->getParam(0); $key = $key->scalarval(); 
	if (!isset($prefs['known_hosts'][$key]) or $prefs['known_hosts'][$key]['ip'] != $tikilib->get_ip_address()) {
		$msg = tra('Invalid server key');
		if ($prefs['intertiki_errfile']) logit($prefs['intertiki_errfile'],$msg,$key,INTERTIKI_BADKEY,$prefs['known_hosts'][$key]['name']);
		$logslib->add_log('intertiki',$msg.' from '.$prefs['known_hosts'][$key]['name'],$login);
		return new XML_RPC_Response(0, 101, $msg);
	}

	if (!isset($prefs['known_hosts'][$key]['allowusersregister']) || ($prefs['known_hosts'][$key]['allowusersregister'] != 'y'))
		return new XML_RPC_Response(0, 101, "Users are not allowed to register via intertiki on this master.");

	require_once 'lib/registration/registrationlib.php';

	$result=$registrationlib->register_new_user_from_intertiki(XML_RPC_decode($params->getParam(1)));

	return new XML_RPC_Response(XML_RPC_encode($result));
}

