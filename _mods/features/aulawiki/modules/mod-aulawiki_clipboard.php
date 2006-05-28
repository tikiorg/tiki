<?php

/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
include_once ('lib/categories/categlib.php');

if (!isset ($_SESSION["clipboard"])) {
	$_SESSION["clipboard"] = array ();
}
$error = "";
$clipboardEntry = array ();

if (isset ($_REQUEST["pasteIdCateg"]) && isset ($_REQUEST["pasteObjects"])) {
	foreach ($_REQUEST["pasteObjects"] as $key => $objectId) {
		$objectToPaste = $_SESSION["clipboard"][$objectId];
		$idCatObj = $categlib->is_categorized($objectToPaste["type"], $objectToPaste["id"]);
		if (!isset ($idCatObj) || $idCatObj == "") {
			$idCatObj = $categlib->add_categorized_object($objectToPaste["type"], $objectToPaste["id"], $objectToPaste["desc"], $objectToPaste["name"], $objectToPaste["href"]);
		}
		$categlib->categorize($idCatObj, $_REQUEST["pasteIdCateg"]);
	}
}

if (isset ($_REQUEST["ClipboardDeleteAll"])) {
	$_SESSION["clipboard"]=null;
}

if (isset ($_REQUEST["copyIdObj"])) {
	if (isset ($_REQUEST["copyType"])) {
		$clipboardEntry["type"] = $_REQUEST["copyType"];
	} else {
		$error = "type not set";
	}
	if (isset ($_REQUEST["copyIdObj"])) {
		$clipboardEntry["id"] = $_REQUEST["copyIdObj"];
	} else {
		$error = "object id not set";
	}
	if (isset ($_REQUEST["copyName"])) {
		$clipboardEntry["name"] = $_REQUEST["copyName"];
	} else {
		$error = "name not set";
	}
	if (isset ($_REQUEST["copyDesc"])) {
		$clipboardEntry["desc"] = $_REQUEST["copyDesc"];
	}
	if (isset ($_REQUEST["copyHref"])) {
		$clipboardEntry["href"] = $_REQUEST["copyHref"];
	} else {
		$error = "href not set";
	}
	if (isset ($error) && $error != "") {
		$smarty->assign('error', $error);
	} else {
		$_SESSION["clipboard"][$clipboardEntry["id"]] = $clipboardEntry;
	}
	$smarty->assign('error', $error);
}
$smarty->assign('clipboard', $_SESSION["clipboard"]);
$smarty->assign('clipboardCurrentUrl', $tikilib->httpPrefix().$_SERVER["REQUEST_URI"]);
?>