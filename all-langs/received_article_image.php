<?php

// $Id: /cvsroot/tikiwiki/tiki/received_article_image.php,v 1.9.2.1 2008-03-01 17:12:54 leyan Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Id: /cvsroot/tikiwiki/tiki/received_article_image.php,v 1.9.2.1 2008-03-01 17:12:54 leyan Exp $
// application to display an image from the database with 
// option to resize the image dynamically creating a thumbnail on the fly.
if (!isset($_REQUEST["id"])) {
	die;
}

require_once ('tiki-setup.php');

if ($prefs['feature_articles'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_articles");

	$smarty->display("error.tpl");
	die;
}

include_once ('lib/commcenter/commlib.php');
$data = $commlib->get_received_article($_REQUEST["id"]);
$type = $data["image_type"];
$data = $data["image_data"];
header ("Content-type: $type");
echo $data;

?>
