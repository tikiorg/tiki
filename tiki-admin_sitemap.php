<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');

global $base_host, $prefs, $tikipath;

$access->check_permission('tiki_p_admin');
$access->check_feature('sitemap_enable');

$sitemap = new Tiki\Sitemap\Generator();

if (isset($_REQUEST['rebuild'])) {

	$sitemap->generate($base_host);

	Feedback::success(tr('New sitemap created!'), 'session');
	$access->redirect('tiki-admin_sitemap.php');
}

$smarty->assign('title', tr('Sitemap'));
$smarty->assign('url', $base_host . '/' . $sitemap->getSitemapPath());
$smarty->assign('sitemapAvailable', file_exists($sitemap->getSitemapPath(false)));
$smarty->assign('mid', 'tiki-admin_sitemap.tpl');
$smarty->display('tiki.tpl');
