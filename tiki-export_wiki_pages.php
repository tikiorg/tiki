<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');

include_once ("lib/ziplib.php");
include_once ('lib/wiki/exportlib.php');

$access->check_feature('feature_wiki_export');
if (empty($_REQUEST['page'])) {
	$access->check_permission('tiki_p_export_wiki');
}

if (!isset($_REQUEST["page"])) {
	$exportlib->MakeWikiZip();
	$dump = 'dump';
	if (!empty($tikidomain)) $dump .= "/$tikidomain";
	header("location: $dump/export.tar");
} else {
	if (isset($_REQUEST["all"]))
		$all = 0;
	else
		$all = 1;

	$objectperms = Perms::get('wiki page', $_REQUEST['page']);
	$latest = isset($_REQUEST['latest']) && $objectperms->wiki_view_latest;

	if (!$objectperms->view) {
		die('Permission denied.');
	}

	$data = $exportlib->export_wiki_page($_REQUEST["page"], $all, $latest);

	$page = $_REQUEST["page"];
	header("Content-type: application/unknown");
	header("Content-Disposition: inline; filename=$page");
	echo $data;
}
