<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-forums_rss.php,v 1.12 2003-10-12 12:55:42 ohertel Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');

if ($rss_forums != 'y') {
	$smarty -> assign('msg', tra("This feature is disabled"));
	$smarty -> display("styles/$style_base/error.tpl");
	die; // TODO: output of rss file with message: rss disabled
}

if($tiki_p_admin_forum != 'y' && $tiki_p_forum_read != 'y') {
	$smarty -> assign('msg', tra("Permission denied you can not view this section"));
	$smarty -> display("styles/$style_base/error.tpl");
	die; // TODO: output of rss file with message: permission denied
}

$title = "Tiki RSS feed for forums"; // TODO: make configurable
$desc = "Last topics in forums."; // TODO: make configurable
$now = date("U");
$id = "forumId";
$descId = "data";
$dateId = "commentDate";
$titleId = "title";
$readrepl = "tiki-view_forum_thread.php";
$changes = $tikilib -> list_all_forum_topics(0, $max_rss_forums, $dateId.'_desc', '');

require ("tiki-rss.php");

?>