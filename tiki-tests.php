<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-tests.php,v 1.4 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//foo
include_once ('tiki-setup.php');

include_once ('PHPUnit.php');

$h = opendir('tests');

while ($file = readdir($h)) {
	if (strstr($file, '.php')) {
		print ("<b>$file</b>");

		include_once ("tests/$file");
	}
}

closedir ($h);

?>