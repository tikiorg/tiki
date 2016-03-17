<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

require_once ('tiki-setup.php');

$access->check_feature('feature_wiki');

$wikilib = TikiLib::lib('wiki');
$plugins = $wikilib->list_plugins(true);
$smarty->assign_by_ref('plugins', $plugins);
$smarty->display("tiki-edit_help.tpl");

