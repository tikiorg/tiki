<?php 

// $HEADER$

include_once("lib/init/initlib.php");
require_once('db/tiki-db.php');
require_once('lib/tikilib.php');
require_once('lib/userslib.php');
require_once("lib/xmlrpc.inc");
require_once("lib/xmlrpcs.inc");
require_once("lib/wiki/wikilib.php");


$map = array ("getSubGraph" => array( "function" => "getSubGraph" ) );

$server = new xmlrpc_server( $map );

function getSubGraph($params) {
    global $wikilib, $dbTiki;

    $nodeName = $params->getParam(0); $nodeName = $nodeName->scalarVal();
    $depth = $params->getParam(1); $depth = $depth->scalarVal();

    $nodes = array();

    $passed = array($nodeName => true);
    $queue = array($nodeName);
    $i = 0;

    $tikilib = new TikiLib($dbTiki);
    $existing_color = $tikilib->get_preference("wiki_3d_existing_page_color", '#00BB88');
    $missing_color = $tikilib->get_preference("wiki_3d_missing_page_color", '#FF6666');

    while ($i <= $depth && sizeof($queue) > 0) {
	$nextQueue = array();
	foreach ($queue as $nodeName) {

	    $neighbours = $wikilib->wiki_get_neighbours($nodeName);
	    
	    for ($j = 0; $j < sizeof($neighbours); $j++) {
		if (!isset($passed[$neighbours[$j]])) {
		    $nextQueue[] = $neighbours[$j];
		    $passed[$neighbours[$j]] = true;
		}
		$neighbours[$j] = new XmlRpcVal($neighbours[$j]);
	    }

	    $node = array();

	    $base_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	    $base_url = preg_replace('/\/tiki-wiki3d_xmlrpc.php.*$/','',$base_url);

	    if ($wikilib->page_exists($nodeName)) {
		$color = $existing_color;
		$actionUrl = "${base_url}/tiki-index.php?page=${nodeName}";
	    } else {
		$color = $missing_color;
		$actionUrl = "${base_url}/tiki-editpage.php?page=${nodeName}";
	    }

	    $node['neighbours'] = new XmlRpcVal($neighbours, "array");
	    if (!empty($color)) {
		$node['color'] = new XmlRpcVal($color, "string");
	    }
	    $node['actionUrl'] = new XmlRpcVal($actionUrl, "string");

	    $nodes[$nodeName] = new XmlRpcVal($node, "struct");

	}
	$i++;
	$queue = $nextQueue;
    }

    $response = array("graph" => new XmlRpcVal($nodes, "struct"));
    
    return new XmlRpcResp(new XmlRpcVal($response, "struct"));
}

?>