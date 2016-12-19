<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$file = $_SERVER['argv'][1];
$tokens = token_get_all(file_get_contents($file));

$out = array();

foreach ($tokens as $token) {
	if (! is_array($token) || $token[0] !== T_COMMENT) {
		$out[] = $token;
	}
}

ob_start();

foreach ($out as $o) {
	if (is_array($o)) {
		echo $o[1];
	} else {
		echo $o;
	}
}

file_put_contents($file, ob_get_clean());

