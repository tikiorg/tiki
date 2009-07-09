<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-show_user_avatar.php,v 1.10 2007-03-06 19:29:52 sylvieg Exp $
require 'tiki-setup.php';
if ($prefs['feature_userPreferences'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled") . ": feature_userPreferences");
	$smarty->display("error.tpl");
	die;
}
global $userprefslib;
include_once ('lib/userprefs/userprefslib.php');
// application to display an image from the database with
// option to resize the image dynamically creating a thumbnail on the fly.
// you have to check if the user has permission to see this gallery
if (!isset($_REQUEST["user"])) {
	die;
}
$info = $userprefslib->get_user_avatar_img($_REQUEST["user"]);
$type = $info["avatarFileType"];
$content = $info["avatarData"];
header("Content-type: $type");
echo "$content";
