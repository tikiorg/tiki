<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-error.php,v 1.10 2004-08-12 22:31:22 teedog Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

ask_ticket('error');

if (!isset($_REQUEST["error"])) $_REQUEST["error"] = tra('unknown error');

// This can be useful for putting custom code inside error page.
// ie: in error.tpl {$referer) will hold "login" if user came from tiki-login.php
// if this gets useful we can integrate with tickets, this was just a hack to show to LarsKl
// during a chat.
if (!empty($_SERVER['HTTP_REFERER']) && preg_match('/tiki-([a-z_]+?)\.php/', $_SERVER['HTTP_REFERER'], $m)) {
    $smarty->assign('referer',$m[1]);
}

// Display the template
$smarty->assign('msg', strip_tags($_REQUEST["error"]));
$smarty->display("error.tpl");
?>
