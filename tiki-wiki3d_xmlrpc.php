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
    global $wikilib;

    $nodeName = $params->getParam(0); $nodeName = $nodeName->scalarVal();
    $depth = $params->getParam(1); $depth = $depth->scalarVal();

    $nodes = array();

    $passed = array($nodeName => true);
    $queue = array($nodeName);
    $i = 0;

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

	    $nodes[$nodeName] = new XmlRpcVal($neighbours, "array");

	}
	$i++;
	$queue = $nextQueue;
    }

    @$wikilib->db->query($depth);

    $response = array("nodes" => new XmlRpcVal($nodes, "struct"));

    
    return new XmlRpcResp(new XmlRpcVal($response, "struct"));
}

?>