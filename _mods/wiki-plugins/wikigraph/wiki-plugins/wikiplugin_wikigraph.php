<?php

// Renders a graph
// Usage
// {WIKIGRAPH(level=>n)}page{WIKIGRAPH}
include_once ('lib/wiki/wikilib.php');

include_once ('lib/graphviz/GraphViz.php');
// the functions in wikigraphlilb.php have been in wikilib.php, 
// check if wikilib.php is needed at all
include_once ('lib/graphviz/wikigraphlib.php');

function wikiplugin_wikigraph_help() {
	$back = tra("Renders a graph, with linked pages navigation visually figured.\n");
	$back.= tra("^Parameters: key=>value,...\n");
	$back.= "||\n";
	$back.= tra("__key__ | __default__ | __comments__\n");
	$back.= "level | 0 | " . tra("the number of hops the graph follows\n");
	$back.= "title | wikigraph | " . tra("the title of the map\n");
	$back.= "nodesep | .1 | " . tra("the space between nodes\n");
	$back.= "rankdir | LR | " . tra("Left to Right, the direction of graph\n");
	$back.= "bgcolor | transparent | " . tra("the background color, use #rrvvbb color types.\n");
	$back.= "size | | " . tra("nothing there, unlimited size. use 5,3 type sizes in inches\n");
	$back.= "fontsize | 9 | " . tra("the font size in pts presumably\n");
	$back.= "fontname | Helvetica | " . tra("the name of the font used for labels\n");
	$back.= "shap | box | " . tra("the shape of a node. can be ");
	$back.= tra("plaintext ellipse circle egg triangle box diamond trapezium parallelogram house hexagon octagon\n");
	$back.= "nodestyle | filled | " . tra("style for drawing nodes.\n");
	$back.= "nodecolor | #aeaeae | " . tra("color of the border\n");
	$back.= "nodefillcolor | #FFFFFF | " . tra("background color of the node\n");
	$back.= "nodewidth | .1 | " . tra("sortof relative width ??\n");
	$back.= "nodeheight | .1 | " . tra("same mystery as above\n");
	$back.= "edgecolor | #999999 | " . tra("color for links (called edges here)\n");
	$back.= "edgestyle | solid | " . tra("shape of the arrow that come with the link\n");
	$back.= "||^";
	return $back;
}

function wikiplugin_wikigraph($data, $params) {
	global $tikilib;
	global $page;
	global $wikilib;
	global $dbTiki;
	$wikigraphlib = new WikiGraphLib($dbTiki);
	$add = "";
	extract ($params, EXTR_SKIP);
  if(!isset($level)) $level = 0;
	if(!isset($title)) $title = "wikigraph";
	if(isset($nodesep)) $add.="&amp;nodesep=$nodesep";
	if(isset($rankdir)) $add.="&amp;rankdir=$rankdir";
	if(isset($size)) $add.="&amp;size=$size";
	if(isset($bgcolor)) $add.="&amp;bgcolor=$bgcolor";
	if(isset($fontsize)) $add.="&amp;fontsize=$fontsize";
	if(isset($fontname)) $add.="&amp;fontname=$fontname";
	if(isset($shape)) $add.="&amp;shape=$shape";
	if(isset($nodestyle)) $add.="&amp;nodestyle=$nodestyle";
	if(isset($nodecolor)) $add.="&amp;nodecolor=$nodecolor";
	if(isset($nodefillcolor)) $add.="&amp;nodefillcolor=$nodefillcolor";
	if(isset($nodewidth)) $add.="&amp;nodewidth=$nodewidth";
	if(isset($nodeheight)) $add.="&amp;nodeheight=$nodeheight";
	if(isset($edgecolor)) $add.="&amp;edgecolor=$edgecolor";
	if(isset($edgestyle)) $add.="&amp;edgestyle=$edgestyle";
  if(empty($data)) $data=$page;
  $mapname=md5(uniqid("."));
  $ret='';

$garg = array(
  'att' => array(
    'level' => $level,
    'nodesep' => isset($nodesep) ? $nodesep : ".1",
    'rankdir' => isset($rankdir) ? $rankdir : "LR",
    'bgcolor' => isset($bgcolor) ? $bgcolor : "transparent",
    'size' => isset($size) ? $size : ""
	),
	'node' => array(
    'fontsize' => isset($fontsize) ? $fontsize : "9",
    'fontname' => isset($fontname) ? $fontname : "Helvetica",
    'shape' => isset($shape) ? $shape : "box",
    'style' => isset($nodestyle) ? $nodestyle : "filled",
    'color' => isset($nodecolor) ? $nodecolor : "#aeaeae",
    'fillcolor' => isset($nodefillcolor) ? $nodefillcolor : "#FFFFFF",
    'width' => isset($nodewidth) ? $nodewidth : ".1",
    'height' => isset($nodeheight) ? $nodeheight : ".1"
  ),
  'edge' => array(
    'color' => isset($edgecolor) ? $edgecolor : "#999999",
    'style' => isset($edgestyle) ? $edgestyle : "solid"
));

	$ret .= "<div align='center'><img border='0' src='tiki-wiki_graph.php?page=".urlencode($data)."&amp;level=$level$add' alt='graph' usemap='#$mapname' />";
	$mapdata = $wikigraphlib->get_graph_map($data, $level, $garg);
	//$mapdata = $wikilib->get_graph_map($data, $level, $garg);
	$mapdata = preg_replace("/\n|\r/", '', $mapdata);
        $mapdata = preg_replace("/\&#45;/", '-', $mapdata);
	$ret .= "<map name='$mapname'>$mapdata</map></div>";
	return $ret;
}

?>
