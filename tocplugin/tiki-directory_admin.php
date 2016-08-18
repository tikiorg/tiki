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
include_once ('lib/directory/dirlib.php');
$access->check_feature('feature_directory');
$access->check_permission_either(array('tiki_p_admin_directory_sites','tiki_p_admin_directory_cats','tiki_p_validate_links'));
//get_strings tra('Admin Directory')
// This will only display a menu to
// admin_categories
// admin_sites
// validate_sites
// configuration (in tiki admin if admin)
//
// Get number of invalid sites
// Get number of sites
// Get number of categories
// Get number of searches
$stats = $dirlib->dir_stats();
$smarty->assign_by_ref('stats', $stats);
// This page should be displayed with Directory section options
$section = 'directory';
include_once ('tiki-section_options.php');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-directory_admin.tpl');
$smarty->display("tiki.tpl");
