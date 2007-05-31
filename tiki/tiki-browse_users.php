<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-browse_users.php,v 1.2 2007-05-31 09:42:56 nyloth Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//
// $Header: /cvsroot/tikiwiki/tiki/tiki-browse_users.php,v 1.2 2007-05-31 09:42:56 nyloth Exp $
//

// Initialization
require_once ('tiki-setup.php');

if (!isset($_REQUEST["type"])) $type = 'wiki';
else $type = $_REQUEST["type"];

$smarty->assign('type', $type);
$smarty->assign('view_user', $_REQUEST['view_user']);

$section = 'community';
include_once ('tiki-section_options.php');
ask_ticket('browse-users');

// Display the template
$smarty->assign('mid', 'tiki-browse_users.tpl');
$smarty->display("tiki.tpl");

?>
