<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	die('This script may only be included.');
}
//smarty is not there - we need setup
require_once('tiki-setup.php');  

$wikilib = TikiLib::lib('wiki');
$plugins = $wikilib->list_plugins(true);

$smarty->assign_by_ref('plugins', $plugins);
