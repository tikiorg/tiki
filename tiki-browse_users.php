<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-browse_users.php,v 1.1 2005-12-14 17:40:31 lfagundes Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//
// $Header: /cvsroot/tikiwiki/tiki/tiki-browse_users.php,v 1.1 2005-12-14 17:40:31 lfagundes Exp $
//

// Initialization
require_once ('tiki-setup.php');

if (!isset($_REQUEST["type"])) {
	$type = 'wiki';
} else {
	$type = $_REQUEST["type"];
}

$smarty->assign('type', $type);

$smarty->assign('view_user', $_REQUEST['view_user']);

$base_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$base_url = preg_replace('/\/tiki-browse_users.php.+$/','',$base_url);
$smarty->assign('base_url',$base_url);

$section = 'community';
include_once ('tiki-section_options.php');
ask_ticket('browse-users');

// Display the template
$smarty->assign('mid', 'tiki-browse_users.tpl');
$smarty->display("tiki.tpl");

?>
