<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
	Script to extract images from a Tiki database and create them as files
	Damian Parker
	SF: damosoft
	tikigod.org

	This script is MySQL only!

	v0.1

	you can run this from cmd line with: php extract-img-db-to-fs.php

*/

// Database settings

$db_tiki = 'tikiwiki';
$db_user = 'root';
$db_pass = '';
$db_host = 'localhost';

// Extract images to folder, make sure PHP can write here
// remember the trailing /
$extract_to = '/home/damian/public_html/tikiimages/dump/';

$db = mysql_connect($db_host, $db_user, $db_pass);
mysql_select_db($db_tiki);

$query = "select * from tiki_images_data where type = 'o'";
$results = mysql_query($query);

while ($r = mysql_fetch_array($results)) {
	extract($r, EXTR_PREFIX_ALL, 'r');

	echo "$r_filename ";
	if (file_exists($extract_to . $r_filename)) {
		$r_filename .= '001';
		echo "exists going to: $r_filename";
	}

	$img = fopen($extract_to . $r_filename, 'w');
	fwrite($img, $r_data);
	fclose($img);
	echo "Done!\n";
}
