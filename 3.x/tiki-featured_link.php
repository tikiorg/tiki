<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-featured_link.php,v 1.15 2007-10-12 07:55:27 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/featured_links/flinkslib.php');

if ($prefs['feature_featuredLinks'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_featuredLinks");

	$smarty->display("error.tpl");
	die;
}

$flinkslib->add_featured_link_hit($_REQUEST["url"]);
// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["url"])) {
	$smarty->assign('msg', tra("No page indicated"));

	$smarty->display("error.tpl");
	die;
}

$section = 'featured_links';
include_once ('tiki-section_options.php');

$smarty->assign_by_ref('url', $_REQUEST["url"]);
$smarty->assign('mid', 'tiki-featured_link.tpl');
$smarty->display("tiki.tpl");

?>
