<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// application to display an image from the database with 
// option to resize the image dynamically creating a thumbnail on the fly.
if (!isset($_REQUEST["id"])) {
	die;
}

require_once ('tiki-setup.php');
$access->check_feature('feature_articles');

include_once ('lib/commcenter/commlib.php');
$data = $commlib->get_received_article($_REQUEST["id"]);
$type = $data["image_type"];
$data = $data["image_data"];
header ("Content-type: $type");
echo $data;
