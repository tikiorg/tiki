<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-map_rss.php,v 1.2 2003-10-12 20:47:29 ohertel Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');

if($tiki_p_map_view != 'y') {
	$smarty -> assign('msg', tra("Permission denied you cannot view this section"));
	$smarty -> display("styles/$style_base/error.tpl");
	die; // TODO: output of rss file with message: permission denied
}

$title = "Tiki RSS feed for maps"; // TODO: make configurable
$desc = "List of maps available."; // TODO: make configurable
$now = date("U");
$id = "name";
$titleId = "name";
$descId = "description";
$dateId = "lastModif";
$readrepl = "tiki-map.phtml?mapfile=";

// Get mapfiles from the mapfiles directory
$tmp = array();
$h = opendir($map_path);
while (($file = readdir($h)) !== false)
{
	if (preg_match('/\.map$/i', $file))
	{
		$aux = array();
		$aux["name"] = $file;
		$aux["lastModif"] = filectime($map_path."/".$file);
		$aux["description"] = "";
		$tmp[] = $aux;
	}
}
closedir ($h);
sort ($tmp);
$changes = array();
$changes["data"] = $tmp;

require ("tiki-rss.php");

?>
