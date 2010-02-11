<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

include_once ('tiki-setup.php');
if ($prefs['feature_xmlrpc'] != 'y' || $prefs['wiki_feature_3d'] != 'y') {
	die;
}
require_once ("XML/Server.php");
$map = array("getSubGraph" => array("function" => "getSubGraph"));
$server = new XML_RPC_Server($map);
function getSubGraph($params) {
	global $dbTiki, $base_url;
	$userlib = new UsersLib;
	$nodeName = $params->getParam(0);
	$nodeName = $nodeName->scalarVal();
	$depth = $params->getParam(1);
	$depth = $depth->scalarVal();
	$nodes = array();
	$passed = array($nodeName => true);
	$queue = array($nodeName);
	$i = 0;
	$neighbours = array();
	while ($i <= $depth && count($queue) > 0) {
		$nextQueue = array();
		foreach($queue as $nodeName) {
			$similar = $userlib->related_users($nodeName, 5);
			if (isset($neighbours[$nodeName])) {
				$myNeighbours = $neighbours[$nodeName];
			} else {
				$myNeighbours = array();
			}
			foreach($similar as $user) {
				$myNeighbours[] = $user['login'];
				$neighbours[$user['login']][] = $nodeName;
			}
			$temp_max = count($myNeighbours);
			for ($j = 0; $j < $temp_max; $j++) {
				if (!isset($passed[$myNeighbours[$j]])) {
					$nextQueue[] = $myNeighbours[$j];
					$passed[$myNeighbours[$j]] = true;
				}
				$myNeighbours[$j] = new XML_RPC_Value($myNeighbours[$j]);
			}
			$node = array();
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
