<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*

Please note that this script is experimental. Run it on backup copies of tiki to see if it fixes the messed-up encoding.

*/
die('REMOVE THIS LINE TO USE THE SCRIPT.');

require_once 'tiki-setup.php';
require 'db/local.php';

if ($client_charset !== 'utf8') {
	die('Please. Client charset to utf8.');
}

if ('' === trim(`which enca`)) {
	die('enca must be installed.');
}

$db = TikiDb::get();

// All text fields with an encoding except those char(1) and varchar(1)
$text_fields = $db->fetchAll("select distinct table_name, column_name, column_type, character_set_name from information_schema.columns WHERE table_schema = '$dbs_tiki' and (character_set_name IS NOT NULL AND column_type <> 'char(1)' AND column_type <> 'varchar(1)')");

$pairs = array();

foreach ($text_fields as $field) {
	extract($field);

	$values = $db->fetchAll("select `$column_name` value from `$table_name`");

	foreach ($values as $value) {
		if (ctype_alpha($value['value']) || empty($value['value'])) {
			continue;
		}

		file_put_contents('/tmp/data', $value['value']);

		$output = trim(`enca -L none /tmp/data`);

		if (0 === strpos($output, 'Universal transformation format 8 bits; UTF-8')) {
			$db->query("UPDATE `$table_name` SET `$column_name`=CONVERT(CONVERT(CONVERT(CONVERT(`$column_name` USING binary) USING utf8) USING latin1) USING binary) WHERE `$column_name` = ?", array($value['value']));
		}
	}
}

