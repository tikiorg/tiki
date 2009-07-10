<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-mailin.php,v 1.10 2007-10-12 07:55:29 nyloth Exp $
require_once ('tiki-setup.php');
//check if feature is on
if ($prefs['feature_mailin'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled") . ": feature_mailin");
	$smarty->display("error.tpl");
	die;
}
include_once ('tiki-mailin-code.php');
$smarty->assign('content', $content);
$smarty->assign('mid', 'tiki-mailin.tpl');
$smarty->display("tiki.tpl");
