<?php

// $Header: /cvsroot/tikiwiki/tiki/commxmlrpc.php,v 1.12 2004-01-28 02:44:16 musus Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Header: /cvsroot/tikiwiki/tiki/commxmlrpc.php,v 1.12 2004-01-28 02:44:16 musus Exp $
include_once("lib/init/initlib.php");
include_once ("db/tiki-db.php");
include_once ("lib/tikilib.php");
include_once ("lib/userslib.php");
include_once ("lib/xmlrpc.inc");
include_once ("lib/xmlrpcs.inc");
include_once ("lib/commcenter/commlib.php");

$tikilib = new Tikilib($dbTiki);
$userlib = new Userslib($dbTiki);

if ($tikilib->get_preference("feature_comm", 'n') != 'y') {
	die;
}

$map = array(
	"sendPage" => array("function" => "sendPage"),
	"sendArticle" => array("function" => "sendArticle")
);

$s = new xmlrpc_server($map);

/* Validates the user and returns user information */
function sendPage($params) {
	// Get the page and store it in received_pages
	global $tikilib, $userlib, $commlib;

	$pp = $params->getParam(0);
	$site = $pp->scalarval();
	$pp = $params->getParam(1);
	$username = $pp->scalarval();
	$pp = $params->getParam(2);
	$password = $pp->scalarval();
	$pp = $params->getParam(3);
	$pageName = $pp->scalarval();
	$pp = $params->getParam(4);
	$data = $pp->scalarval();
	$pp = $params->getParam(5);
	$comment = $pp->scalarval();
	$pp = $params->getParam(6);
	$description = $pp->scalarval();
	// 
	if (!$userlib->validate_user($username, $password, '', '')) {
		return new xmlrpcresp(0, 101, "Invalid username or password");
	}

	// Verify if the user has tiki_p_sendme_pages
	if (!$userlib->user_has_permission($username, 'tiki_p_sendme_pages')) {
		return new xmlrpcresp(0, 101, "Permissions denied user $username cannot send pages to this site");
	}

	// Store the page in the tiki_received_pages_table
	$data = base64_decode($data);
	$commlib->receive_page($pageName, $data, $comment, $site, $username, $description);
	return new xmlrpcresp(new xmlrpcval(1, "boolean"));
}

function sendArticle($params) {
	// Get the page and store it in received_pages
	global $tikilib, $userlib, $commlib;

	$pp = $params->getParam(0);
	$site = $pp->scalarval();
	$pp = $params->getParam(1);
	$username = $pp->scalarval();
	$pp = $params->getParam(2);
	$password = $pp->scalarval();
	$pp = $params->getParam(3);
	$title = $pp->scalarval();
	$pp = $params->getParam(4);
	$authorName = $pp->scalarval();
	$pp = $params->getParam(5);
	$size = $pp->scalarval();
	$pp = $params->getParam(6);
	$use_image = $pp->scalarval();
	$pp = $params->getParam(7);
	$image_name = $pp->scalarval();
	$pp = $params->getParam(8);
	$image_type = $pp->scalarval();
	$pp = $params->getParam(9);
	$image_size = $pp->scalarval();
	$pp = $params->getParam(10);
	$image_x = $pp->scalarval();
	$pp = $params->getParam(11);
	$image_y = $pp->scalarval();
	$pp = $params->getParam(12);
	$image_data = $pp->scalarval();
	$pp = $params->getParam(13);
	$publishDate = $pp->scalarval();
	$pp = $params->getParam(14);
	$expireDate = $pp->scalarval();
	$pp = $params->getParam(15);
	$created = $pp->scalarval();
	$pp = $params->getParam(16);
	$heading = $pp->scalarval();
	$pp = $params->getParam(17);
	$body = $pp->scalarval();
	$pp = $params->getParam(18);
	$hash = $pp->scalarval();
	$pp = $params->getParam(19);
	$author = $pp->scalarval();
	$pp = $params->getParam(20);
	$type = $pp->scalarval();
	$pp = $params->getParam(21);
	$rating = $pp->scalarval();

	// 
	if (!$userlib->validate_user($username, $password, '', '')) {
		return new xmlrpcresp(0, 101, "Invalid username or password");
	}

	// Verify if the user has tiki_p_sendme_pages
	if (!$userlib->user_has_permission($username, 'tiki_p_sendme_articles')) {
		return new xmlrpcresp(0, 101, "Permissions denied user $username cannot send articles to this site");
	}

	// Store the page in the tiki_received_pages_table
	$title = base64_decode($title);
	$authorName = base64_decode($authorName);
	$image_data = base64_decode($image_data);
	$heading = base64_decode($heading);
	$body = base64_decode($body);

	$commlib->receive_article($site, $username, $title, $authorName, $size, $use_image, $image_name, $image_type, $image_size,
		$image_x, $image_y, $image_data, $publishDate, $expireDate, $created, $heading, $body, $hash, $author, $type, $rating);

	return new xmlrpcresp(new xmlrpcval(1, "boolean"));
}

?>
