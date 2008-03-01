<?php

// $Header: /cvsroot/tikiwiki/tiki/article_image.php,v 1.18.2.1 2008-03-01 17:12:54 leyan Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// application to display an image from the database with 
// option to resize the image dynamically creating a thumbnail on the fly.

require_once ('tiki-setup.php');

if ($prefs['feature_articles'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_articles");

	$smarty->display("error.tpl");
	die;
}

// Now check permissions to access this page
if($tiki_p_read_article != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view pages"));
  $smarty->display("error.tpl");
  die;  
}

if (!isset($_REQUEST["id"])) {
	die;
}

include_once ('lib/init/initlib.php');
include_once ('tiki-setup_base.php');

$topiccachefile = $prefs['tmpDir'];

if ($tikidomain) { $topiccachefile.= "/$tikidomain"; }
$topiccachefile.= "/article.".$_REQUEST["id"];

if (is_file($topiccachefile) and (!isset($_REQUEST["reload"]))) {
	$size = getimagesize($topiccachefile);
	header ("Content-type: ".$size['mime']); /* do not backport to 1.8 */
	readfile($topiccachefile);
	die();
} else {
	$data = $tikilib->get_article_image($_REQUEST["id"]);
	// if blank then die, otherwise we offer to download strangeness
	// this also catches invalid id's
	if (!$data) {
		die;
	}
	$type = $data["image_type"];
	$data = $data["image_data"];
	if ($data["image_data"]) {
		$fp = fopen($topiccachefile,"wb");
		fputs($fp,$data);
		fclose($fp);
	}
}

header ("Content-type: $type");
if (is_file($topiccachefile)) {
	readfile($topiccachefile);
} else {
	echo $data;
}

?>
