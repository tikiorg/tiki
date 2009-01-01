<?php 

// $HEADER$

include_once('tiki-setup.php');
if($prefs['feature_xmlrpc'] != 'y' || $prefs['wiki_feature_3d'] != 'y') {
  die;  
}

require_once("XML/Server.php");

$map = array ("getSubGraph" => array( "function" => "getSubGraph" ) );
$server = new XML_RPC_Server( $map );

function getSubGraph($params) {
    global $wikilib, $dbTiki, $base_url, $prefs;

    $nodeName = $params->getParam(0); $nodeName = $nodeName->scalarVal();
    $depth = $params->getParam(1); $depth = $depth->scalarVal();

    $nodes = array();

    $passed = array($nodeName => true);
    $queue = array($nodeName);
    $i = 0;

    $tikilib = new TikiLib($dbTiki);
    $existing_color = $prefs['wiki_3d_existing_page_color'];
    $missing_color = $prefs['wiki_3d_missing_page_color'];

    while ($i <= $depth && sizeof($queue) > 0) {
	$nextQueue = array();
	foreach ($queue as $nodeName) {

	    $neighbours = $wikilib->wiki_get_neighbours($nodeName);
	    
	    $temp_max = sizeof($neighbours);
	    for ($j = 0; $j < $temp_max; $j++) {
		if (!isset($passed[$neighbours[$j]])) {
		    $nextQueue[] = $neighbours[$j];
		    $passed[$neighbours[$j]] = true;
		}
		$neighbours[$j] = new XML_RPC_Value($neighbours[$j]);
	    }

	    $node = array();

	    if ( $wikilib->page_exists($nodeName) ) {
		$color = $existing_color;
		$actionUrl = $base_url.'tiki-index.php?page='.$nodeName;
	    } else {
		$color = $missing_color;
		$actionUrl = $base_url.'tiki-editpage.php?page='.$nodeName;
	    }

	    $node['neighbours'] = new XML_RPC_Value($neighbours, "array");
	    if ( ! empty($color) ) $node['color'] = new XML_RPC_Value($color, "string");

	    $node['actionUrl'] = new XML_RPC_Value($actionUrl, "string");
	    $nodes[$nodeName] = new XML_RPC_Value($node, "struct");
	}
	$i++;
	$queue = $nextQueue;
    }

    $response = array("graph" => new XML_RPC_Value($nodes, "struct"));
    
    return new XML_RPC_Response(new XML_RPC_Value($response, "struct"));
}

?>
