<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-error.php,v 1.4 2003-10-22 22:54:04 gmuslera Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

// Display the template
$smarty->assign('msg', strip_tags($_REQUEST["error"]));
$smarty->display("styles/$style_base/error.tpl");

?>
