<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-custom_home.php,v 1.17 2007-10-12 07:55:25 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

if ($prefs['feature_custom_home'] != 'y') {
	$smarty->assign('msg', tra("This feature has been disabled"));

	$smarty->display("error.tpl");
	die;
}

// Display the template
$smarty->assign('mid', 'tiki-custom_home.tpl');
$smarty->display("tiki.tpl");
