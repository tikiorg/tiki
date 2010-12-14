<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');

$access->check_feature('feature_webmail');
$access->check_permission('tiki_p_use_webmail');

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
$names = explode(';', $part->headers["content-disposition"]);
$names = explode('=', $names[1]);
$file = $names[1];

header ("Content-type: $type");
//header( "Content-Disposition: attachment; filename=$file" );
header ("Content-Disposition: inline; filename=$file");
echo "$content";
