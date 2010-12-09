<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require 'tiki-setup.php';

$access->check_feature('feature_userPreferences');

global $userprefslib, $tikidomain;
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
if (empty($content) && isset($_REQUEST['always'])) {
	$content = file_get_contents('pics/noavatar.png');
}
header("Content-type: $type");
echo $content;

if( $prefs['users_serve_avatar_static'] == 'y' ) {
	require 'lib/mime/mimeextensions.php';
	$ext = $mimeextensions[$type];
	$image = "temp/public/$tikidomain/avatar_{$_REQUEST['user']}.$ext";

	file_put_contents( $image, $info['avatarData'] );
	chmod($image, 0644);
}
