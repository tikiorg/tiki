<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-view_minical_topic.php,v 1.7.2.1 2008-03-01 16:07:36 lphuberdeau Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/minical/minicallib.php');

if ($prefs['feature_minical'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_minical");

	$smarty->display("error.tpl");
	die;
}

if ($prefs['tiki_p_minical'] != 'y') {
	$smarty->assign('msg', tra("Permission denied"));

	$smarty->display("error.tpl");
	die;
}


if (!$user)
	die;

if (!isset($_REQUEST["topicId"])) {
	die;
}

$info = $minicallib->minical_get_topic($user, $_REQUEST["topicId"]);
$type = &$info["filetype"];
$file = &$info["filename"];
$content = &$info["data"];
header ("Content-type: $type");
header ("Content-Disposition: inline; filename=$file");
echo "$content";

?>
