<?php
include_once("lib/graphviz/GraphViz.php");
include_once('tiki-setup.php');
include_once('lib/wiki/wikilib.php');

if(!isset($_REQUEST['level'])) $_REQUEST['level']=0;
$str = $wikilib->wiki_get_link_structure($_REQUEST['page'],$_REQUEST['level']);
$graph = new Image_GraphViz();
$wikilib->wiki_page_graph($str,$graph);
$graph->image();
?>