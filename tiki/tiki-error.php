<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-error.php,v 1.6 2003-12-28 20:12:52 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

ask_ticket('error');

// Display the template
$smarty->assign('msg', strip_tags($_REQUEST["error"]));
$smarty->display("error.tpl");

?>
