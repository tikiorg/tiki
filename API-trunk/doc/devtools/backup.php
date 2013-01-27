<?php
/**
 * script for backing up Tiki.
 * 
 * requires Tiki Administrator permissions (tiki_p_admin)
 *
 * @package   Tiki
 * @subpackage  DevTools
 * @copyright (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project. All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * @license   LGPL. See license.txt for more details
 */
// $Id$

require_once('tiki-setup.php');
$access->check_permission('tiki_p_admin');
$backup = "";
foreach (TikiLib::fetchAll('SHOW TABLES') as $table) {
	$table = end($table);
	$result = TikiLib::fetchAll('SELECT * FROM '. $table);
	$num_fields = count($result);

	$backup.= 'DROP TABLE '.$table.';';
	$createTable = TikiLib::fetchAll('SHOW CREATE TABLE '.$table);
	$backup.= "\n\n".$createTable[0]['Create Table'].";\n\n";

	foreach ($result as $row) {
		$fields = array();

		foreach ($row as $field) {
			$field = addslashes($field);
			$field = preg_replace("\n", "\\n", $field);
			$fields[] = (isset($field) ? '"'.$field.'"' : '""');
		}

		$backup.= 'INSERT INTO '.$table.' VALUES('.implode(",", $fields).');' . "\n";
	}

	$backup.="\n\n\n";
}
//save file
$handle = fopen('temp/db-backup-'.time().'-'.(md5(implode(',', $tables))).'.sql', 'w+');
fwrite($handle, $backup);
fclose($handle);
