<?php

// $Header: /cvsroot/tikiwiki/tiki/jhot.php,v 1.8 2004-05-05 23:54:11 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Header: /cvsroot/tikiwiki/tiki/jhot.php,v 1.8 2004-05-05 23:54:11 mose Exp $
include_once ('tiki-setup.php');

include_once ('lib/drawings/drawlib.php');

if (isset($_FILES['filepath']) && is_uploaded_file($_FILES['filepath']['tmp_name'])) {
	$size = $_FILES['filepath']['size'];

	$name = $_FILES['filepath']['name'];
	$type = $_FILES['filepath']['type'];

	$pos = strpos($name, 'img/wiki/');
	$name = substr($name, $pos);

	if ($tikidomain) {
		$absolute_name = str_replace("img/wiki/$tikidomain/", '', $name);
	} else {
		$absolute_name = str_replace("img/wiki/", '', $name);
	}
	$absolute_name = str_replace('.gif', '', $absolute_name);
	$absolute_name = str_replace('.pad_xml', '', $absolute_name);

	$now = date("U");

	if (strstr($name, '.gif')) {
		$hash = $absolute_name . md5(uniqid('.')). '.gif';
	}

	if (strstr($name, '.pad_xml')) {
		$hash = $absolute_name . md5(uniqid('.')). '.pad_xml';
	}

	if (strstr($name, '.pad_xml')) {
		$drawlib->update_drawing($absolute_name, $hash, $user);
	} else {
		$drawlib->set_drawing_gif($absolute_name, $hash);
	}

	if ($tikidomain) {
		$hash = "$tikidomain/$hash";
	}
	@$fw = fopen("img/wiki/$hash", "wb");
	@$fw2 = fopen($name, "wb");
	@$fp = fopen($_FILES['filepath']['tmp_name'], "rb");

	while (!feof($fp)) {
		$data = fread($fp, 8192 * 16);

		fwrite($fw, $data);
		fwrite($fw2, $data);
	}

	fclose ($fp);
	fclose ($fw);
	fclose ($fw2);
}

?>
