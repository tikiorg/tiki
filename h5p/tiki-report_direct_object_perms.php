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

$wikilib = TikiLib::lib('wiki');

$access->check_feature(array('feature_wiki'));
$access->check_permission(array('tiki_p_admin_wiki'));

global $tikilib;

$query = 'select distinct tp.pageName' .
					' from tiki_pages tp, users_objectpermissions perm' .
					" where md5(concat('wiki page', lower(tp.pageName))) = perm.objectId" .
					" and perm.objectType = 'wiki page'" .
					' order by tp.pageName';

$result = $tikilib->query($query);

$pages = array();
while ($row = $result->fetchRow()) {
	$pages[] = $row['pageName'];
}

$smarty->assign('pagesWithDirectPerms', $pages);

$smarty->assign('mid', 'tiki-report_direct_object_perms.tpl');
$smarty->display('tiki.tpl');
