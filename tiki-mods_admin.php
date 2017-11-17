<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');
include_once('lib/mods/modslib.php');
$access->check_permission('tiki_p_admin');

if (isset($_REQUEST['save'])) {
	if (isset($_REQUEST['feature_mods_provider']) and $_REQUEST['feature_mods_provider'] == 'on') {
		$tikilib->set_preference('feature_mods_provider', 'y');
	} else {
		$tikilib->set_preference('feature_mods_provider', 'n');
	}
	if (isset($_REQUEST['mods_dir'])) {
		if (is_dir($_REQUEST['mods_dir'])) {
			$tikilib->set_preference('mods_dir', $_REQUEST['mods_dir']);
		} else {
			Feedback::warning(tr('Directory %0 not found.', $_REQUEST['mods_dir']));
		}
	} else {
		$tikilib->set_preference('mods_dir', 'mods');
	}
	if (isset($_REQUEST['mods_server'])) {
		$tikilib->set_preference('mods_server', $_REQUEST['mods_server']);
	} else {
		$tikilib->set_preference('mods_server', 'http://mods.tiki.org');
	}
}
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-mods_admin.tpl');
$smarty->display("tiki.tpl");
