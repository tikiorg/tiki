<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-view_blog_post_image.php,v 1.8.2.1 2008-03-01 16:07:36 lphuberdeau Exp $
require_once ('tiki-setup.php');
if ($prefs['feature_blogs'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled") . ": feature_blogs");
	$smarty->display("error.tpl");
	die;
}
include_once ('lib/blogs/bloglib.php');
if (!isset($_REQUEST["imgId"])) {
	die;
}
$info = $bloglib->get_post_image($_REQUEST["imgId"]);
$type = & $info["filetype"];
$file = & $info["filename"];
$content = & $info["data"];
header("Content-type: $type");
header("Content-Disposition: inline; filename=$file");
echo "$content";
