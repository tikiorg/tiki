<?php

// Renders a graph
// Usage
// {WIKIGRAPH(level=>n)}page{WIKIGRAPH}
include_once ('lib/wiki/wikilib.php');

include_once ("lib/graphviz/GraphViz.php");

function wikiplugin_wikigraph($data, $params) {
	global $tikilib;

	global $page;
	global $wikilib;
	extract ($params);

	if (!isset($level))
		$level = 0;

	if ($level > 5)
		$level = 5;

	if (empty($data))
		$data = $page;

	$mapname = md5(uniqid("."));
	$ret = '';

	$ret .= "<div align='center'><img border='0' src='tiki-wiki_graph.php?page=$data&amp;level=$level' alt='graph' usemap='#$mapname' />";
	$mapdata = $wikilib->get_graph_map($page, $level);
	$mapdata = preg_replace("/\n|\r/", '', $mapdata);
	$ret .= "<map name='$mapname'>$mapdata</map></div>";
	return $ret;
}

?>