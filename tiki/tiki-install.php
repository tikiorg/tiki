<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-install.php,v 1.86.2.1 2007-11-04 22:08:04 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

if (!file_exists("installer/tiki-installer.php")) {
	header ("Status: 410 Gone");
	header ("HTTP/1.0 410 Gone"); 
	header ('location: index.php');
	die('Tikiwiki installer has been disabled.');
} else {
	include_once("installer/tiki-installer.php");
}

?>
