<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-webmail_download_attachment.php,v 1.9.2.1 2008-03-01 16:07:36 lphuberdeau Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

if ($prefs['feature_webmail'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_use_webmail != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied to use this feature"));

	$smarty->display("error.tpl");
	die;
}

require_once ('lib/webmail/webmaillib.php');

require ("lib/webmail/mimeDecode.php");
//require ("lib/webmail/pop3.php");
require ("lib/webmail/net_pop3.php");

$current = $webmaillib->get_current_webmail_account($user);
//$pop3 = new POP3($current["pop"], $current["username"], $current["pass"]);
//$pop3->Open();
$pop3->connect($current["pop"]);
$pop3->login($current["username"], $current["pass"]);
$full = $pop3->getMsg($_REQUEST["msgid"]);
$smarty->assign('msgid', $_REQUEST["msgid"]);
$pop3->disconnect();
$params = array(
	'input' => $full,
	'crlf' => "\r\n",
	'include_bodies' => TRUE,
	'decode_headers' => TRUE,
	'decode_bodies' => TRUE
);

$output = Mail_mimeDecode::decode($params);
$part = $output->parts[$_REQUEST["getpart"]];
$type = $part->headers["content-type"];
$content = $part->body;
$names = split(';', $part->headers["content-disposition"]);
$names = split('=', $names[1]);
$file = $names[1];

header ("Content-type: $type");
//header( "Content-Disposition: attachment; filename=$file" );
header ("Content-Disposition: inline; filename=$file");
echo "$content";

?>
