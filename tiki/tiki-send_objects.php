<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-send_objects.php,v 1.8 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ("lib/xmlrpc.inc");
include_once ("lib/xmlrpcs.inc");

if ($feature_comm != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($tiki_p_send_pages != 'y' && $tiki_p_send_articles != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$smarty->display("styles/$style_base/error.tpl");
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
	// Create XMLRPC object
	$client = new xmlrpc_client($_REQUEST["path"], $_REQUEST["site"], 80);

	$client->setDebug(0);

	foreach ($sendpages as $page) {
		$page_info = $tikilib->get_page_info($page);

		if ($page_info) {
			$searchMsg = new xmlrpcmsg('sendPage', array(
				new xmlrpcval($_SERVER["SERVER_NAME"], "string"),
				new xmlrpcval($_REQUEST["username"], "string"),
				new xmlrpcval($_REQUEST["password"], "string"),
				new xmlrpcval($page, "string"),
				new xmlrpcval(base64_encode($page_info["data"]), "string"),
				new xmlrpcval($page_info["comment"], "string"),
				new xmlrpcval($page_info["description"], "string")
			));

			$result = $client->send($searchMsg);

			if (!$result) {
				$errorMsg = 'Cannot login to server maybe the server is down';
			} else {
				if (!$result->faultCode()) {
					// We have a response
					$res = xmlrpc_decode($result->value());

					if ($res) {
						$msg .= tra('page'). ': ' . $page . tra(' successfully sent'). "<br/>";
					}
				} else {
					$errorMsg = $result->faultstring();

					$msg .= tra('page'). ': ' . $page . tra(' not sent'). $errorMsg . "<br/>";
				}
			}
		}
	}

	foreach ($sendarticles as $article) {
		$page_info = $tikilib->get_article($article);

		if ($page_info) {
			$searchMsg = new xmlrpcmsg('sendArticle', array(
				new xmlrpcval($_SERVER["SERVER_NAME"], "string"),
				new xmlrpcval($_REQUEST["username"], "string"),
				new xmlrpcval($_REQUEST["password"], "string"),
				new xmlrpcval(base64_encode($page_info["title"]), "string"),
				new xmlrpcval(base64_encode($page_info["authorName"]), "string"),
				new xmlrpcval($page_info["size"], "int"),
				new xmlrpcval($page_info["useImage"], "string"),
				new xmlrpcval($page_info["image_name"], "string"),
				new xmlrpcval($page_info["image_type"], "string"),
				new xmlrpcval($page_info["image_size"], "int"),
				new xmlrpcval($page_info["image_x"], "int"),
				new xmlrpcval($page_info["image_x"], "int"),
				new xmlrpcval(base64_encode($page_info["image_data"]), "string"),
				new xmlrpcval($page_info["publishDate"], "int"),
				new xmlrpcval($page_info["created"], "int"),
				new xmlrpcval(base64_encode($page_info["heading"]), "string"),
				new xmlrpcval(base64_encode($page_info["body"]), "string"),
				new xmlrpcval($page_info["hash"], "string"),
				new xmlrpcval($page_info["author"], "string"),
				new xmlrpcval($page_info["type"], "string"),
				new xmlrpcval($page_info["rating"], "string")
			));

			$result = $client->send($searchMsg);

			if (!$result) {
				$errorMsg = 'Cannot login to server maybe the server is down';
			} else {
				if (!$result->faultCode()) {
					// We have a response
					$res = xmlrpc_decode($result->value());

					if ($res) {
						$msg .= tra('article'). ': ' . $article . tra(' successfully sent'). "<br/>";
					}
				} else {
					$errorMsg = $result->faultstring();

					$msg .= tra('page'). ': ' . $article . tra(' not sent'). $errorMsg . "<br/>";
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

$pages = $tikilib->list_pages(0, -1, 'pageName_asc', $find);
$articles = $tikilib->list_articles(0, -1, 'publishDate_desc', $find, date("U"), $user);
$smarty->assign_by_ref('pages', $pages["data"]);
$smarty->assign_by_ref('articles', $articles["data"]);

// Display the template
$smarty->assign('mid', 'tiki-send_objects.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>