<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-error.php,v 1.7 2004-03-23 22:39:25 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

ask_ticket('error');

if (!isset($_REQUEST["error"])) $_REQUEST["error"] = tra('unknown error');

// Display the template
$smarty->assign('msg', strip_tags($_REQUEST["error"]));
$smarty->display("error.tpl");
?>
