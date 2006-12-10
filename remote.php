<?php
// $Header: /cvsroot/tikiwiki/tiki/remote.php,v 1.4 2006-12-10 12:34:55 ohertel Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

$version = "0.2";

/*
// if you wisely moved that script ..
$_SERVER['TIKI_VIRTUAL'] = 'myvirtual';
chdir('/path/to/tiki');
*/

include_once("lib/init/initlib.php");
include 'tiki-setup_base.php';
require_once("XML/Server.php");

if ($tikilib->get_preference('feature_intertiki') != 'y' ||
    $tikilib->get_preference('feature_intertiki_server') != 'y' ||
    $tikilib->get_preference('feature_intertiki_mymaster')) {

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
	$line = $_SERVER['REMOTE_ADDR']." - $user - ". date('[m/d/Y:H:i:s]')." \"$txt\" $code \"$from\"";
	lograw($file,$line);
}

$known_hosts = unserialize($tikilib->get_preference('known_hosts',serialize(array())));
$intertiki_logfile = $tikilib->get_preference('intertiki_logfile','');
$intertiki_errfile = $tikilib->get_preference('intertiki_errfile','');

define('INTERTIKI_OK',200);
define('INTERTIKI_BADKEY',401);
define('INTERTIKI_BADUSER',404);

$map = array(
	"intertiki.validate" => array("function"=>"validate"),
	"intertiki.version" => array("function"=>"get_version")
);
$s = new XML_RPC_Server($map);

function validate($params) {
	global $tikilib,$userlib,$known_hosts,$intertiki_errfile,$intertiki_logfile,$logslib;

	$key = $params->getParam(0); $key = $key->scalarval(); 
	$login = $params->getParam(1); $login = $login->scalarval(); 
	$pass = $params->getParam(2); $pass = $pass->scalarval(); 
	$slave = $params->getParam(3); $slave = $slave->scalarval();
	
	if (!isset($known_hosts[$key]) or $known_hosts[$key]['ip'] != $_SERVER['REMOTE_ADDR']) {
		$msg = tra('Invalid server key');
		if ($intertiki_errfile) logit($intertiki_errfile,$msg,$key,INTERTIKI_BADKEY,$known_hosts[$key]['name']);
		$logslib->add_log('intertiki',$msg.' from '.$known_hosts[$key]['name'],$login);
		return new XML_RPC_Response(0, 101, $msg);
	}
	
	list($isvalid, $dummy, $error) = $userlib->validate_user($login,$pass,'','');
	if(!$isvalid) {
		$msg = tra('Invalid username or password');
		if ($intertiki_errfile) logit($intertiki_errfile,$msg,$login,INTERTIKI_BADUSER,$known_hosts[$key]['name']);
		$logslib->add_log('intertiki',$msg.' from '.$known_hosts[$key]['name'],$login);
		if (!$userlib->user_exists($login)) {
		    // slave client is supposed to disguise 102 code as 101 not to show
		    // crackers that user does not exists. 102 is required for telling slave
		    // to delete user there
		    return new XML_RPC_Response(0, 102, $msg);
		} else {
		    return new XML_RPC_Response(0, 101, $msg);
		}
	} 
	if ($intertiki_logfile) logit($intertiki_logfile,"logged",$login,INTERTIKI_OK,$known_hosts[$key]['name']);
	
	if ($slave) {
	    $logslib->add_log('intertiki','auth granted from '.$known_hosts[$key]['name'],$login);
	    global $userlib;
	    $user_details = $userlib->get_user_details($login);
	    return new XML_RPC_Response(new XML_RPC_Value(serialize($user_details), "string"));
	} else {
	    $logslib->add_log('intertiki','auth granted from '.$known_hosts[$key]['name'],$login);
	    return new XML_RPC_Response(new XML_RPC_Value(1, "boolean"));
	}
}

function get_version($params) {
	global $version;
	return new XML_RPC_Response(new XML_RPC_Value($version, "int"));
}

?>
