<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-map_rss.php,v 1.12 2006-09-19 16:33:17 ohertel Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');
require_once ('lib/rss/rsslib.php');

if ($rss_mapfiles != 'y') {
        $errmsg=tra("rss feed disabled");
        require_once ('tiki-rss_error.php');
}

if($tiki_p_map_view != 'y') {
        $errmsg=tra("Permission denied you cannot view this section");
        require_once ('tiki-rss_error.php');
}

$feed = "map";
$uniqueid = $feed;
$output = $rsslib->get_from_cache($uniqueid);

if ($output["data"]=="EMPTY") {
	$title = (!empty($title_rss_mapfiles)) ? $title_rss_mapfiles : tra("Tiki RSS feed for maps");
	$desc =  (!empty($desc_rss_mapfiles)) ? $desc_rss_mapfiles : tra("List of maps available.");
	$now = date("U");
	$id = "name";
	$titleId = "name";
	$descId = "description";
	$dateId = "lastModif";
	$authorId = "";
	$readrepl = "tiki-map.phtml?mapfile=";
	
	$tmp = $tikilib->get_preference('title_rss_'.$feed, '');
	if ($tmp<>'') $title = $tmp;
	$tmp = $tikilib->get_preference('desc_rss_'.$feed, '');
	if ($desc<>'') $desc = $tmp;
	
	  // Get mapfiles from the mapfiles directory
	  $tmp = array();
	  $h = @opendir($map_path);
	
	  while (($file = @readdir($h)) !== false)
	  {
	  	if (preg_match('/\.map$/i', $file))
	  	{
	  		$filetlist[$file] = filemtime ($map_path."/".$file);
	  	}
	  }
	  @arsort($filetlist, SORT_NUMERIC);
	
	  $aux = array();
	  $i=0;
	  if (is_array($filetlist))
	  while (list ($key, $val) = each ($filetlist))
	  {
	    if ($i >= $max_rss_mapfiles) break;
	    $i++;
	  	$aux["name"] = $key;
	  	$aux["lastModif"] = $val;
	  	$aux["description"] = "";
	  	$tmp[] = $aux;
	  }
	
	  @closedir ($h);
	  $changes = array();
	  $changes["data"] = $tmp;
	
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
}
header("Content-type: ".$output["content-type"]);
print $output["data"];

?>
