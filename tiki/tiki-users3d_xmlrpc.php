<?php 

// $HEADER$

include_once("lib/init/initlib.php");
require_once('db/tiki-db.php');
require_once('lib/tikilib.php');
require_once('lib/userslib.php');
require_once("XML/Server.php");


$map = array ("getSubGraph" => array( "function" => "getSubGraph" ) );

$server = new XML_RPC_Server( $map );

function getSubGraph($params) {
    global $dbTiki;

    $userlib = new UsersLib($dbTiki);

    $nodeName = $params->getParam(0); $nodeName = $nodeName->scalarVal();
    $depth = $params->getParam(1); $depth = $depth->scalarVal();

    $nodes = array();

    $passed = array($nodeName => true);
    $queue = array($nodeName);
    $i = 0;
    $neighbours = array();

    while ($i <= $depth && sizeof($queue) > 0) {
	$nextQueue = array();
	foreach ($queue as $nodeName) {

	    $similar = $userlib->related_users($nodeName,5);
	    if (isset($neighbours[$nodeName])) {
		$myNeighbours = $neighbours[$nodeName];
	    } else {
		$myNeighbours = array();
	    }
	    foreach ($similar as $user) {
		$myNeighbours[] = $user['login'];
		$neighbours[$user['login']][] = $nodeName;
	    }
	    
	    $temp_max = sizeof($myNeighbours);
	    for ($j = 0; $j < $temp_max; $j++) {
		if (!isset($passed[$myNeighbours[$j]])) {
		    $nextQueue[] = $myNeighbours[$j];
		    $passed[$myNeighbours[$j]] = true;
		}
		$myNeighbours[$j] = new XML_RPC_Value($myNeighbours[$j]);
	    }

	    $node = array();

	    $base_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	    $base_url = preg_replace('/\/tiki-users3d_xmlrpc.php.*$/','',$base_url);

	    $actionUrl = "javascript:listObjects('$nodeName');";
	    $color = '#0000FF';

	    $node['neighbours'] = new XML_RPC_Value($myNeighbours, "array");
	    if (!empty($color)) {
		$node['color'] = new XML_RPC_Value($color, "string");
	    }
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
