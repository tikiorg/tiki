<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-wiki_graph.php,v 1.4 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
include_once ("lib/graphviz/GraphViz.php");

include_once ('tiki-setup.php');
include_once ('lib/wiki/wikilib.php');

if (!isset($_REQUEST['level']))
	$_REQUEST['level'] = 0;

$str = $wikilib->wiki_get_link_structure($_REQUEST['page'], $_REQUEST['level']);
$graph = new Image_GraphViz();
$wikilib->wiki_page_graph($str, $graph);
$graph->image();

?>