<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-error.php,v 1.3 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

// Display the template
$smarty->assign('msg', $_REQUEST["error"]);
$smarty->display("styles/$style_base/error.tpl");

?>