<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

include_once("tiki-setup.php");
include_once ("lib/pear/XML/Server.php");
include_once ('lib/commcenter/commlib.php');

if ($tikilib->get_preference("feature_comm", 'n') != 'y') {
	die;
}

$map = array(
	"sendPage" => array("function" => "sendPage"),
	"sendStructurePage" => array("function" => "sendStructurePage"),
	"sendArticle" => array("function" => "sendArticle")
);

$s = new XML_RPC_Server($map);

function sendStructurePage($params) {
	global $tikilib, $userlib, $commlib, $prefs;
	include_once ('lib/structures/structlib.php');
	$site = $params->getParam(0); $site = $site->scalarval();
	$user = $params->getParam(1); $user = $user->scalarval();
	$pass = $params->getParam(2); $pass = $pass->scalarval();
	$sName = $params->getParam(3); $sName = $sName->scalarval();
	$pName = $params->getParam(4); $pName = $pName->scalarval();
	$name = $params->getParam(5); $name = $name->scalarval();
	$data = $params->getParam(6); $data = $data->scalarval();
	$comm = $params->getParam(7); $comm = $comm->scalarval();
	$desc = $params->getParam(8); $desc = $desc->scalarval();
	$pos = $params->getParam(9); $pos = $pos->scalarval();
	$alias = $params->getParam(10); $alias = $alias->scalarval();

	if ($user != 'admin' && $prefs['feature_intertiki'] == 'y' && !empty($prefs['feature_intertiki_mymaster'])) {
		$ok = $userlib->intervalidate($prefs['interlist'][$prefs['feature_intertiki_mymaster']], $user, $pass, false);
	} else {
		list($ok, $user, $error) = $userlib->validate_user($user, $pass, '', '');
	}
	if (!$ok) {
		return new XML_RPC_Response(0, 101, "Invalid username or password");
	}

	// Verify if the user has tiki_p_sendme_pages
	if (!$userlib->user_has_permission($user, 'tiki_p_sendme_pages')) {
		return new XML_RPC_Response(0, 101, "Permissions denied user $user cannot send pages to this site");
	}

	// Store the page in the tiki_received_pages_table
	$data = base64_decode($data);
	$commlib->receive_structure_page($name, $data, $comm, $site, $user, $desc, $sName, $pName, $pos, $alias);

	return new XML_RPC_Response(new XML_RPC_Value(1, "boolean"));
}

/* Validates the user and returns user information */
function sendPage($params) {
	// Get the page and store it in received_pages
	global $tikilib, $userlib, $commlib, $prefs;

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

	if ($username != 'admin' && $prefs['feature_intertiki'] == 'y' && !empty($prefs['feature_intertiki_mymaster'])) {
		$ok = $userlib->intervalidate($prefs['interlist'][$prefs['feature_intertiki_mymaster']], $username, $password, false);
	} else {
		list($ok, $username, $error) = $userlib->validate_user($username, $password, '', '');
	}
	if (!$ok) {
		return new XML_RPC_Response(0, 101, "Invalid username or password");
	}

	// Verify if the user has tiki_p_sendme_pages
	if (!$userlib->user_has_permission($username, 'tiki_p_sendme_pages')) {
		return new XML_RPC_Response(0, 101, "Permissions denied user $username cannot send pages to this site");
	}

	// Store the page in the tiki_received_pages_table
	$data = base64_decode($data);
	$commlib->receive_page($pageName, $data, $comment, $site, $username, $description);
	return new XML_RPC_Response(new XML_RPC_Value(1, "boolean"));
}

function sendArticle($params) {
	// Get the page and store it in received_pages
	global $tikilib, $userlib, $commlib, $prefs;

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

	if ($username != 'admin' && $prefs['feature_intertiki'] == 'y' && !empty($prefs['feature_intertiki_mymaster'])) {
		$ok = $userlib->intervalidate($prefs['interlist'][$prefs['feature_intertiki_mymaster']], $username, $password, false);
	} else {
		list($ok, $username, $error) = $userlib->validate_user($username, $password, '', '');
	}
	if (!$ok) {
		return new XML_RPC_Response(0, 101, "Invalid username or password");
	}

	// Verify if the user has tiki_p_sendme_pages
	if (!$userlib->user_has_permission($username, 'tiki_p_sendme_articles')) {
		return new XML_RPC_Response(0, 101, "Permissions denied user $username cannot send articles to this site");
	}

	// Store the page in the tiki_received_pages_table
	$title = base64_decode($title);
	$authorName = base64_decode($authorName);
	$image_data = base64_decode($image_data);
	$heading = base64_decode($heading);
	$body = base64_decode($body);

	$commlib->receive_article($site, $username, $title, $authorName, $size, $use_image, $image_name, $image_type, $image_size,
		$image_x, $image_y, $image_data, $publishDate, $expireDate, $created, $heading, $body, $hash, $author, $type, $rating);

	return new XML_RPC_Response(new XML_RPC_Value(1, "boolean"));
}
