<?php

// $Id: /cvsroot/tikiwiki/tiki/index.php,v 1.9 2007-10-12 07:55:23 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');
if ( ! headers_sent() ) {
	header ('location: '.$prefs['tikiIndex']);
}
die("header already sent");

?>
