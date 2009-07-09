<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_banners.php,v 1.13.2.1 2007-11-04 22:08:04 nyloth Exp $
require_once ('tiki-setup.php');
if ($prefs['feature_banners'] != 'y') {
	$smarty->assign('msg', tra('This feature is disabled') . ': feature_banners');
	$smarty->display('error.tpl');
	die;
}
if ($tiki_p_admin_banners != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}
// Display the template
$smarty->assign('mid', 'tiki-edit_banner.tpl');
$smarty->display("tiki.tpl");
