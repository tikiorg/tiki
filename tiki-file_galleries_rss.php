<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-file_galleries_rss.php,v 1.16 2003-10-12 12:37:29 ohertel Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');

if ($rss_file_galleries != 'y') {
	$smarty -> assign('msg', tra("This feature is disabled"));
	$smarty -> display("styles/$style_base/error.tpl");
	die; // TODO: output of rss file with message: rss disabled
}

if ($tiki_p_view_file_gallery != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view this section"));
  $smarty->display("styles/$style_base/error.tpl");
  die; // TODO: output of rss file with message: permission denied
}

$title = "Tiki RSS feed for file galleries"; // TODO: make configurable
$desc = "Last files uploaded to the file galleries."; // TODO: make configurable
$now = date("U");
$id = "fileId";
$descId = "description";
$dateId = "created";
$titleId = "filename";
$readrepl = "tiki-download_file.php?$id=";
$changes = $tikilib->list_files(0, $max_rss_file_galleries, $dateId.'_desc', '');

require ("tiki-rss.php");

?>		