<?php

$fp = fopen('tikidump.sql', 'r');
$table = '';

while (!feof($fp)) {
	$l = fgets($fp, 1024);
	if (preg_match('/CREATE TABLE\s+(\S+)\s+/i', $l, $m)) {
		$table = $m[1];
	}

	if (preg_match('/(.*)\s+default\s+NULL/i', $l, $m)) {
		printf("ALTER TABLE %s MODIFY %s NOT NULL;\n", $table, $m[1]);
	}
	if (preg_match('/(.*)\s+text,/i', $l, $m)) {
		printf("ALTER TABLE %s MODIFY %s TEXT NOT NULL;\n", $table, $m[1]);
	}
}

?>
