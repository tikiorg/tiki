<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_banners.php,v 1.11 2005-09-07 22:15:26 michael_davey Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

// $access->check_admin();
$access->check_feature('feature_banners');
$access->check_permission('tiki_p_admin_banners');

// Display the template
$smarty->assign('mid', 'tiki-edit_banner.tpl');
$smarty->display("tiki.tpl");

?>
