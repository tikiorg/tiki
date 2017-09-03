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

if (isset($_REQUEST['rebuild'])) {

	$sitemap = new Tiki\Sitemap\Generator();
	$sitemap->generate($base_host);

	Feedback::success(tr('New sitemap created!'), 'session');
	$access->redirect('tiki-admin_sitemap.php');
}

$xml = $base_host . '/temp/public/sitemap.xml';
$url = 'temp/public/sitemap.xml';

$smarty->assign('title', tr('Sitemap'));
$smarty->assign('xml', $xml);
$smarty->assign('url', $url);
$smarty->assign('sitemapAvailable', file_exists($tikipath . 'temp/public/sitemap.xml'));
$smarty->assign('mid', 'tiki-admin_sitemap.tpl');
$smarty->display('tiki.tpl');
