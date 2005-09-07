<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_banners.php,v 1.10 2005-09-07 21:37:19 damosoft Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');


if ($feature_banners != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_banners");
	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_banners != 'y') {
	$smarty->assign('msg', tra("You do not have permission to edit banners"));
	$smarty->display("error.tpl");
	die;
}


// Display the template
$smarty->assign('mid', 'tiki-edit_banner.tpl');
$smarty->display("tiki.tpl");

?>