<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$access->check_feature('feature_mailin');
include_once ('tiki-mailin-code.php');
$smarty->assign('content', $content);
$smarty->assign('mid', 'tiki-mailin.tpl');
$smarty->display("tiki.tpl");
