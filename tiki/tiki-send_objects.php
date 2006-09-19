<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-send_objects.php,v 1.21 2006-09-19 16:33:18 ohertel Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ("XML/Server.php");

if ($feature_comm != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_comm");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_send_pages != 'y' && $tiki_p_send_articles != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["username"])) {
	$_REQUEST["username"] = $user;
}

if (!isset($_REQUEST["path"])) {
	$_REQUEST["path"] = '/tiki/commxmlrpc.php';
}

if (!isset($_REQUEST["site"])) {
	$_REQUEST["site"] = '';
}

if (!isset($_REQUEST["password"])) {
	$_REQUEST["password"] = '';
}

if (!isset($_REQUEST["sendpages"])) {
	$sendpages = array();
} else {
	$sendpages = unserialize(urldecode($_REQUEST['sendpages']));
}

if (!isset($_REQUEST["sendarticles"])) {
	$sendarticles = array();
} else {
	$sendarticles = unserialize(urldecode($_REQUEST['sendarticles']));
}

$smarty->assign('username', $_REQUEST["username"]);
$smarty->assign('site', $_REQUEST["site"]);
$smarty->assign('path', $_REQUEST["path"]);
$smarty->assign('password', $_REQUEST["password"]);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

if (isset($_REQUEST["addpage"])) {
	if (!in_array($_REQUEST["pageName"], $sendpages)) {
		$sendpages[] = $_REQUEST["pageName"];
	}
}

if (isset($_REQUEST["clearpages"])) {
	$sendpages = array();
}

if (isset($_REQUEST["addarticle"])) {
	if (!in_array($_REQUEST["articleId"], $sendarticles)) {
		$sendarticles[] = $_REQUEST["articleId"];
	}
}

if (isset($_REQUEST["cleararticles"])) {
	$sendarticles = array();
}

$msg = '';

if (isset($_REQUEST["send"])) {
	check_ticket('send-objects');
	// Create XMLRPC object
	$client = new XML_RPC_Client($_REQUEST["path"], $_REQUEST["site"], 80);

	$client->setDebug(0);

	foreach ($sendpages as $page) {
		$page_info = $tikilib->get_page_info($page);

		if ($page_info) {
			$searchMsg = new XML_RPC_Message('sendPage', array(
				new XML_RPC_Value($_SERVER["SERVER_NAME"], "string"),
				new XML_RPC_Value($_REQUEST["username"], "string"),
				new XML_RPC_Value($_REQUEST["password"], "string"),
				new XML_RPC_Value($page, "string"),
				new XML_RPC_Value(base64_encode($page_info["data"]), "string"),
				new XML_RPC_Value($page_info["comment"], "string"),
				new XML_RPC_Value($page_info["description"], "string")
			));

			$result = $client->send($searchMsg);
					
			if (!$result) {
				$errorMsg = 'Cannot login to server maybe the server is down';
				$msg .= tra($errorMsg);
			} else {
				if (!$result->faultCode()) {
				    $msg .= tra('Page'). ': ' . $page . tra(' successfully sent'). "<br />";
				} else {
				    $errorMsg = $result->faultString();
				    $msg .= tra('Page'). ': ' . $page . tra(' not sent') . '!' . "<br />";
				    $msg .= tra('Error: ') . $result->faultCode() . '-' . tra($errorMsg) . "<br />";
				}
			}
		}
	}

	foreach ($sendarticles as $article) {
		$page_info = $tikilib->get_article($article);

		if ($page_info) {
			$searchMsg = new XML_RPC_Message('sendArticle', array(
				new XML_RPC_Value($_SERVER["SERVER_NAME"], "string"),
				new XML_RPC_Value($_REQUEST["username"], "string"),
				new XML_RPC_Value($_REQUEST["password"], "string"),
				new XML_RPC_Value(base64_encode($page_info["title"]), "string"),
				new XML_RPC_Value(base64_encode($page_info["authorName"]), "string"),
				new XML_RPC_Value($page_info["size"], "int"),
				new XML_RPC_Value($page_info["useImage"], "string"),
				new XML_RPC_Value($page_info["image_name"], "string"),
				new XML_RPC_Value($page_info["image_type"], "string"),
				new XML_RPC_Value($page_info["image_size"], "int"),
				new XML_RPC_Value($page_info["image_x"], "int"),
				new XML_RPC_Value($page_info["image_x"], "int"),
				new XML_RPC_Value(base64_encode($page_info["image_data"]), "string"),
				new XML_RPC_Value($page_info["publishDate"], "int"),
				new XML_RPC_Value($page_info["created"], "int"),
				new XML_RPC_Value(base64_encode($page_info["heading"]), "string"),
				new XML_RPC_Value(base64_encode($page_info["body"]), "string"),
				new XML_RPC_Value($page_info["hash"], "string"),
				new XML_RPC_Value($page_info["author"], "string"),
				new XML_RPC_Value($page_info["type"], "string"),
				new XML_RPC_Value($page_info["rating"], "string")
			));

			$result = $client->send($searchMsg);
			
			if (!$result) {
				$errorMsg = 'Cannot login to server maybe the server is down';
				$msg .= tra($errorMsg);
			} else {
				if (!$result->faultCode()) {
				    $msg .= tra('page'). ': ' . $page . tra(' successfully sent'). "<br />";
				} else {
				    $errorMsg = $result->faultString();
				    $msg .= tra('page'). ': ' . $page . tra(' not sent') . '!' . "<br />";
				    $msg .= tra('Error: ') . $result->faultCode() . '-' . tra($errorMsg) . "<br />";
				}
			}
		}
	}
}

$smarty->assign('msg', $msg);

$smarty->assign('sendpages', $sendpages);
$smarty->assign('sendarticles', $sendarticles);
$form_sendpages = urlencode(serialize($sendpages));
$form_sendarticles = urlencode(serialize($sendarticles));
$smarty->assign('form_sendarticles', $form_sendarticles);
$smarty->assign('form_sendpages', $form_sendpages);

$pages = $tikilib->list_pageNames(0, -1, 'pageName_asc', $find);
$articles = $tikilib->list_articles(0, -1, 'publishDate_desc', $find, date("U"), $user);
$smarty->assign_by_ref('pages', $pages["data"]);
$smarty->assign_by_ref('articles', $articles["data"]);

ask_ticket('send-objects');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-send_objects.tpl');
$smarty->display("tiki.tpl");

?>
