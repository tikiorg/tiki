<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-install.php,v 1.74 2005-08-26 01:05:43 luciash Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

if (!file_exists("installer/tiki-installer.php")) {
	echo "Tiki installer is disabled.";
} else {
	include_once("installer/tiki-installer.php");
}
?>
